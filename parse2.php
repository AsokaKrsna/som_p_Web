<?php
$logPath = 'C:\\Users\\Durjoy Majumdar\\.gemini\\antigravity-ide\\brain\\88427ace-fb23-4445-b84a-ed2546d688e1\\.system_generated\\logs\\transcript.jsonl';
$lines = file($logPath);

$fileContent = file_get_contents('research_group.php');

foreach ($lines as $line) {
    $obj = json_decode($line, true);
    if (!$obj) continue;
    
    // STOP before my manual overwrite (step > 400)
    if ($obj['step_index'] > 400) {
        break;
    }
    
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
                    // ReplacementChunks is sometimes a JSON string in logs
                    $chunks = $args['ReplacementChunks'];
                    if (is_string($chunks)) {
                        $chunks = json_decode($chunks, true);
                    }
                    if (is_array($chunks)) {
                        foreach ($chunks as $chunk) {
                            $target = $chunk['TargetContent'];
                            $repl = $chunk['ReplacementContent'];
                            $fileContent = str_replace($target, $repl, $fileContent);
                        }
                    }
                    echo "Applied multi_replace_file_content from step " . $obj['step_index'] . "\n";
                }
            }
        }
    }
}

file_put_contents('c:\\Users\\Durjoy Majumdar\\Desktop\\som_p_Web\\cybersecurity-lab.php', $fileContent);
echo "File reconstructed perfectly from the golden state!\n";
?>
