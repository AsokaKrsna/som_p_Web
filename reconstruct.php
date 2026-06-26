<?php
$logPath = 'C:\\Users\\Durjoy Majumdar\\.gemini\\antigravity-ide\\brain\\88427ace-fb23-4445-b84a-ed2546d688e1\\.system_generated\\logs\\transcript.jsonl';
$lines = file($logPath);

$fileContent = "";

foreach ($lines as $line) {
    $obj = json_decode($line, true);
    if (!$obj) continue;
    
    if (isset($obj['tool_calls'])) {
        foreach ($obj['tool_calls'] as $call) {
            $args = $call['args'] ?? [];
            if (isset($args['TargetFile']) && strpos($args['TargetFile'], 'cybersecurity-lab.php') !== false) {
                if ($call['name'] === 'write_to_file') {
                    $fileContent = $args['CodeContent'];
                    echo "Applied write_to_file from step " . $obj['step_index'] . "\n";
                } elseif ($call['name'] === 'replace_file_content') {
                    $target = $args['TargetContent'];
                    $repl = $args['ReplacementContent'];
                    $fileContent = str_replace($target, $repl, $fileContent);
                    echo "Applied replace_file_content from step " . $obj['step_index'] . "\n";
                } elseif ($call['name'] === 'multi_replace_file_content') {
                    foreach ($args['ReplacementChunks'] as $chunk) {
                        $target = $chunk['TargetContent'];
                        $repl = $chunk['ReplacementContent'];
                        $fileContent = str_replace($target, $repl, $fileContent);
                    }
                    echo "Applied multi_replace_file_content from step " . $obj['step_index'] . "\n";
                }
            }
        }
    }
}

file_put_contents('c:\\Users\\Durjoy Majumdar\\Desktop\\som_p_Web\\cybersecurity-lab.php', $fileContent);
echo "File reconstructed perfectly!\n";
?>
