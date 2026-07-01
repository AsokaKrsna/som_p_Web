<?php
$dir = 'data/';
$files = glob($dir . '*.json');
$report = [];

function cleanString($str) {
    // Replace multiple spaces with a single space
    $str = preg_replace('/\s+/', ' ', $str);
    // Trim leading and trailing whitespace
    return trim($str);
}

function recursiveClean(&$array) {
    if (is_array($array)) {
        foreach ($array as $key => &$value) {
            if (is_string($value)) {
                $value = cleanString($value);
            } else if (is_array($value)) {
                recursiveClean($value);
            }
        }
    }
}

foreach ($files as $file) {
    $content = file_get_contents($file);
    $data = json_decode($content, true);
    
    if ($data === null) continue;
    
    // Analyze schema for arrays of objects
    $schemaAnalysis = [];
    foreach ($data as $key => $value) {
        if (is_array($value) && count($value) > 0) {
            // Check if it's an array of objects
            $isAssocArray = count(array_filter(array_keys($value), 'is_string')) > 0;
            if (!$isAssocArray) { // indexed array
                $allObjects = true;
                foreach ($value as $item) {
                    if (!is_array($item)) {
                        $allObjects = false;
                        break;
                    }
                }
                
                if ($allObjects) {
                    $keysFound = [];
                    foreach ($value as $item) {
                        $keys = array_keys($item);
                        sort($keys);
                        $keysStr = implode(',', $keys);
                        if (!isset($keysFound[$keysStr])) {
                            $keysFound[$keysStr] = 0;
                        }
                        $keysFound[$keysStr]++;
                    }
                    if (count($keysFound) > 1) {
                        $report[] = "Inconsistency found in $file (key: $key):";
                        foreach ($keysFound as $k => $count) {
                            $report[] = "  - $count items have keys: $k";
                        }
                    }
                }
            }
        }
    }
    
    recursiveClean($data);
    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
}

echo implode("\n", $report);
echo "\nDone cleaning.\n";
