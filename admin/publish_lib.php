<?php
/**
 * Publish Center — Git integration library
 * ---------------------------------------------------------------
 * Pure functions used by admin/publish.php to talk to git.
 *
 * Design notes:
 * - All git invocations use exec() with an ARGUMENT ARRAY and a
 *   controlled environment. No user input is ever passed through a
 *   shell string, so shell injection is not possible.
 * - Home directory is captured at load time (before any PHP function
 *   may alter the environment) so git can always find
 *   ~/.git-credentials / ~/.gitconfig / ~/.ssh of the web user.
 * - Functions never echo anything; they return arrays for the UI.
 */

if (!function_exists('pub_home_dir')) {
    /** Web user's home directory, captured at load time. */
    function pub_home_dir(): string
    {
        static $home = null;
        if ($home === null) {
            $home = getenv('HOME') ?: (function_exists('posix_getpwuid')
                ? (posix_getpwuid(posix_geteuid())['dir'] ?? sys_get_temp_dir())
                : sys_get_temp_dir());
        }
        return $home;
    }
}

if (!function_exists('pub_env')) {
    /** Environment for every git call: HOME + configured extras. */
    function pub_env(array $publish): array
    {
        return array_merge(
            ['HOME' => pub_home_dir()],
            $publish['env'] ?? []
        );
    }
}

if (!function_exists('pub_exec')) {
    /**
     * Run a git command safely.
     *
     * @param array $args    Arguments AFTER the git binary, e.g. ['status','--porcelain']
     * @param array $publish Publish config array
     * @param int|null $timeout  Optional per-call override (seconds)
     * @return array{ok:bool, code:int, out:string, err:string, timeout:bool}
     */
    function pub_exec(array $args, array $publish, ?int $timeout = null): array
    {
        $bin      = $publish['git_bin'] ?? 'git';
        $repoPath = $publish['repo_path'] ?? dirname(__DIR__);
        $limit    = $timeout ?? ($publish['command_timeout'] ?? 120);

        // 'git -C <repo> <args...>' — always scope to the repository.
        array_unshift($args, '-C', $repoPath);

        $pipes  = [];
        $result = ['ok' => false, 'code' => -1, 'out' => '', 'err' => '', 'timeout' => false];

        $proc = proc_open(array_merge([$bin], $args), [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ], $pipes, $repoPath, pub_env($publish));

        if (!is_resource($proc)) {
            $result['err'] = "Could not start the git binary ({$bin}).";
            return $result;
        }

        fclose($pipes[0]); // no stdin needed

        // Read both streams without deadlocking (select loop).
        stream_set_blocking($pipes[1], false);
        stream_set_blocking($pipes[2], false);
        $started = time();
        while (true) {
            $read = [$pipes[1], $pipes[2]];
            $write = null; $except = null;
            $n = @stream_select($read, $write, $except, 0, 200000);
            if ($n === false) break;
            if ($n > 0) {
                foreach ($read as $stream) {
                    $chunk = fread($stream, 65536);
                    if ($chunk === false || $chunk === '') continue;
                    if ($stream === $pipes[1]) $result['out'] .= $chunk;
                    else $result['err'] .= $chunk;
                }
            }
            $status = proc_get_status($proc);
            if (!$status['running']) {
                // Drain whatever remains.
                while (($chunk = fread($pipes[1], 65536)) !== false && $chunk !== '') $result['out'] .= $chunk;
                while (($chunk = fread($pipes[2], 65536)) !== false && $chunk !== '') $result['err'] .= $chunk;
                $result['code'] = $status['exitcode'];
                break;
            }
            if ((time() - $started) >= $limit) {
                $result['timeout'] = true;
                proc_terminate($proc);
                $result['err'] .= "\nCommand exceeded {$limit}s and was terminated.";
                break;
            }
        }

        fclose($pipes[1]);
        fclose($pipes[2]);
        proc_close($proc);

        $result['ok'] = ($result['code'] === 0 && !$result['timeout']);
        return $result;
    }
}

if (!function_exists('pub_git_version')) {
    /** git version string, or null if git is unusable. */
    function pub_git_version(array $publish): ?string
    {
        $r = pub_exec(['--version'], $publish, 10);
        if (!$r['ok']) return null;
        return trim(str_replace('git version', '', $r['out'])) ?: trim($r['out']);
    }
}

if (!function_exists('pub_repo_path')) {
    /** Resolved repo path or null if it is not a git work tree. */
    function pub_repo_path(array $publish): ?string
    {
        $r = pub_exec(['rev-parse', '--show-toplevel'], $publish, 15);
        if (!$r['ok']) return null;
        return trim($r['out']) ?: null;
    }
}

if (!function_exists('pub_branch')) {
    /** Current checked-out branch (e.g. 'main'). */
    function pub_branch(array $publish): ?string
    {
        $r = pub_exec(['rev-parse', '--abbrev-ref', 'HEAD'], $publish, 15);
        if (!$r['ok']) return null;
        $b = trim($r['out']);
        return ($b === '' || $b === 'HEAD') ? null : $b;
    }
}

if (!function_exists('pub_remote_url')) {
    /** URL of the configured remote ('origin' by default). */
    function pub_remote_url(array $publish): ?string
    {
        $remote = $publish['remote'] ?? 'origin';
        $r = pub_exec(['remote', 'get-url', $remote], $publish, 15);
        if (!$r['ok']) return null;
        $url = trim($r['out']);
        if ($url === '') return null;
        // Never leak stored credentials in the UI.
        return preg_replace('#https://([^/@]+):[^/@]+@#i', 'https://$1:****@', $url);
    }
}

if (!function_exists('pub_status')) {
    /**
     * Working tree status.
     * @return array{ok:bool, clean:bool, branch:?string, entries:array<int,array{x:string,y:string,file:string,orig:?string}>, error:?string}
     */
    function pub_status(array $publish): array
    {
        $out = [
            'ok' => false, 'clean' => false, 'branch' => null,
            'entries' => [], 'error' => null,
        ];

        $branch = pub_branch($publish);
        $out['branch'] = $branch;

        $r = pub_exec(['status', '--porcelain=v1'], $publish, 30);
        if (!$r['ok']) {
            $out['error'] = trim($r['err']) ?: 'git status failed.';
            return $out;
        }

        $out['ok'] = true;
        $lines = preg_split('/\r?\n/', trim($r['out'])) ?: [];
        foreach ($lines as $line) {
            if ($line === '' || strlen($line) < 4) continue;
            $x = $line[0];            // index (staged) status
            $y = $line[1];            // worktree status
            $rest = substr($line, 3);
            $orig = null;
            if (strpos($rest, '->') !== false) {
                [$from, $to] = array_map('trim', explode('->', $rest, 2));
                $orig = $from;
                $rest = $to;
            }
            $out['entries'][] = ['x' => $x, 'y' => $y, 'file' => trim($rest), 'orig' => $orig];
        }
        $out['clean'] = ($out['entries'] === []);
        return $out;
    }
}

if (!function_exists('pub_describe_status')) {
    /** Human label for a porcelain status code pair. */
    function pub_describe_status(string $x, string $y): string
    {
        if ($x === '?' && $y === '?') return 'New (untracked)';
        if ($x === 'A' || $x === '?') return 'New file';
        if ($x === 'M' || $y === 'M') return 'Modified';
        if ($x === 'D' || $y === 'D') return 'Deleted';
        if ($x === 'R') return 'Renamed';
        if ($x === 'C') return 'Copied';
        if ($x === 'U' || $y === 'U') return 'Merge conflict';
        return 'Changed';
    }
}

if (!function_exists('pub_add_all')) {
    /** Stage every change (new/modified/deleted) in the whole work tree. */
    function pub_add_all(array $publish): array
    {
        return pub_exec(['add', '-A', '--'], $publish, 120);
    }
}

if (!function_exists('pub_commit')) {
    /** Commit the staged changes with the given message. */
    function pub_commit(array $publish, string $message): array
    {
        // '-c key=value' directives must come BEFORE the subcommand:
        // git -C <repo> -c user.name=... commit -m ...
        $args = [];
        if (!empty($publish['commit_user_name'])) {
            $args[] = '-c';
            $args[] = 'user.name=' . $publish['commit_user_name'];
        }
        if (!empty($publish['commit_user_email'])) {
            $args[] = '-c';
            $args[] = 'user.email=' . $publish['commit_user_email'];
        }
        $args[] = 'commit';
        $args[] = '-m';
        $args[] = $message;
        return pub_exec($args, $publish, 120);
    }
}

if (!function_exists('pub_push')) {
    /**
     * Push the given branch to the configured remote.
     * Handles the "no upstream yet" case by setting it automatically.
     * @return array Results of each attempt, keyed 'push' and optionally 'retry'.
     */
    function pub_push(array $publish, string $branch): array
    {
        $remote = $publish['remote'] ?? 'origin';
        $res = ['push' => pub_exec(['push', $remote, $branch], $publish)];

        if (!$res['push']['ok']) {
            $haystack = $res['push']['err'] . $res['push']['out'];
            if (stripos($haystack, 'upstream') !== false || stripos($haystack, 'set-upstream') !== false) {
                $res['retry'] = pub_exec(['push', '--set-upstream', $remote, $branch], $publish);
                $res['push'] = $res['retry'];
                unset($res['retry']);
            }
        }
        $res['push']['remote'] = $remote;
        $res['push']['branch'] = $branch;
        return $res;
    }
}

if (!function_exists('pub_fetch')) {
    /** Update remote-tracking refs (used by the "Check Remote" button). */
    function pub_fetch(array $publish): array
    {
        return pub_exec(['fetch', $publish['remote'] ?? 'origin', '--quiet'], $publish, 60);
    }
}

if (!function_exists('pub_ahead_behind')) {
    /**
     * How many commits the local branch is ahead of / behind its
     * remote-tracking counterpart. Nulls when there is no upstream yet.
     * @return array{ahead:?int, behind:?int, upstream:?string}
     */
    function pub_ahead_behind(array $publish, string $branch): array
    {
        $remote = $publish['remote'] ?? 'origin';
        $upstream = $remote . '/' . $branch;
        $res = ['ahead' => null, 'behind' => null, 'upstream' => $upstream];

        $r = pub_exec(['rev-parse', '--verify', '--quiet', $upstream], $publish, 15);
        if (!$r['ok']) return $res; // no upstream ref yet

        $r = pub_exec(['rev-list', '--left-right', '--count', $upstream . '...' . $branch], $publish, 30);
        if ($r['ok'] && preg_match('/^\s*(\d+)\s+(\d+)\s*$/', trim($r['out']), $m)) {
            $res['behind'] = (int)$m[1];
            $res['ahead']  = (int)$m[2];
        }
        return $res;
    }
}

if (!function_exists('pub_log')) {
    /** Most recent local commits: [['hash'=>..,'date'=>..,'subject'=>..], ...] */
    function pub_log(array $publish, int $limit = 8): array
    {
        // Args are passed to proc_open as an array (no shell), so the
        // format strings must NOT be shell-escaped. Note: git uses
        // strftime-style % directives, not PHP date() formats.
        $r = pub_exec([
            'log', '-n', (string)$limit,
            '--date=format:%d %b %Y, %H:%M',
            '--pretty=format:%h%x09%ad%x09%s',
        ], $publish, 30);
        if (!$r['ok']) return [];
        $rows = [];
        foreach (preg_split('/\r?\n/', trim($r['out'])) ?: [] as $line) {
            if ($line === '') continue;
            $parts = explode("\t", $line, 3);
            $rows[] = ['hash' => $parts[0] ?? '', 'date' => $parts[1] ?? '', 'subject' => $parts[2] ?? ''];
        }
        return $rows;
    }
}

if (!function_exists('pub_is_locked')) {
    /**
     * Prevent two publishes from racing (e.g. two browser tabs).
     * Uses a lock file inside the system temp directory.
     * @return resource|null Lock handle to release with pub_unlock(), null if busy.
     */
    function pub_is_locked()
    {
        $lockFile = sys_get_temp_dir() . '/som_p_Web_publish.lock';
        $fh = @fopen($lockFile, 'c');
        if (!$fh) return null; // cannot lock at all; allow anyway
        if (!flock($fh, LOCK_EX | LOCK_NB)) {
            fclose($fh);
            return null;
        }
        return $fh;
    }
}

if (!function_exists('pub_unlock')) {
    /** Release a lock obtained from pub_is_locked(). */
    function pub_unlock($fh): void
    {
        if (is_resource($fh)) {
            flock($fh, LOCK_UN);
            fclose($fh);
        }
    }
}
