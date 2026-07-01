<?php
$d = json_decode(file_get_contents('data/publications.json'), true);
foreach($d['conferences'] as $c) {
    if(!isset($c['title'])) {
        echo json_encode($c) . "\n";
    }
}
