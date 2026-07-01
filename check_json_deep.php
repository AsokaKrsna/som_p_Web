<?php
$dir = 'data';
$files = glob($dir . '/*.json');

foreach ($files as $file) {
    echo "\n=== Checking $file ===\n";
    $content = file_get_contents($file);
    $data = json_decode($content, true);
    
    if ($data === null) {
        echo "ERROR: Invalid JSON in $file\n";
        continue;
    }
    
    // Check if it's a list or a dict of lists
    $is_dict = false;
    foreach ($data as $key => $val) {
        if (!is_numeric($key)) {
            $is_dict = true;
            break;
        }
    }
    
    $check_array = function($arr, $path) {
        if (!is_array($arr)) {
            echo "Path $path is not an array.\n";
            return;
        }
        
        $schemas = [];
        foreach ($arr as $i => $item) {
            if (is_array($item)) {
                $keys = array_keys($item);
                sort($keys);
                
                $type_schema = [];
                foreach ($keys as $k) {
                    $type = gettype($item[$k]);
                    if ($type === 'string' && empty($item[$k])) {
                        $type = 'empty_string';
                    }
                    $type_schema[] = "$k: $type";
                }
                
                $schema_hash = implode(', ', $type_schema);
                if (!isset($schemas[$schema_hash])) {
                    $schemas[$schema_hash] = 0;
                }
                $schemas[$schema_hash]++;
            } else {
                $schema_hash = "primitive: " . gettype($item);
                if (!isset($schemas[$schema_hash])) {
                    $schemas[$schema_hash] = 0;
                }
                $schemas[$schema_hash]++;
            }
        }
        
        if (count($schemas) > 1) {
            echo "Inconsistency in $path:\n";
            foreach ($schemas as $schema => $count) {
                echo "  - $count items have schema: [$schema]\n";
            }
        } else {
             //echo "All good in $path\n";
        }
    };
    
    if ($is_dict) {
        foreach ($data as $key => $val) {
            $check_array($val, $key);
        }
    } else {
        $check_array($data, 'root');
    }
}
echo "\nDone checking all files.\n";
