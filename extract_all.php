<?php
$logPath = 'C:\\Users\\Durjoy Majumdar\\.gemini\\antigravity-ide\\brain\\88427ace-fb23-4445-b84a-ed2546d688e1\\.system_generated\\logs\\transcript.jsonl';
$lines = file($logPath);

foreach ($lines as $line) {
    $obj = json_decode($line, true);
    if (!$obj) continue;
    
    if (isset($obj['tool_calls'])) {
        foreach ($obj['tool_calls'] as $call) {
            $args = $call['args'] ?? [];
            if (isset($args['TargetFile']) && (strpos($args['TargetFile'], 'cybersecurity-lab.php') !== false || strpos($args['TargetFile'], 'research_group.php') !== false)) {
                $cleanName = trim(basename($args['TargetFile']), '"');
                echo "Step " . $obj['step_index'] . ": " . $call['name'] . " on " . $cleanName . "\n";
                if ($call['name'] === 'write_to_file') {
                    file_put_contents('step_' . $obj['step_index'] . '_' . $cleanName, trim($args['CodeContent'], '"'));
                }
            }
        }
    }
}
?>
