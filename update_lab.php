<?php
$file = 'data/lab_content.json';
$data = json_decode(file_get_contents($file), true);

$new_areas = [
    [
        "icon" => "fa-brain",
        "title" => "Cybersecurity for AI",
        "description" => "Securing AI models against adversarial attacks and data poisoning."
    ],
    [
        "icon" => "fa-robot",
        "title" => "AI for Cybersecurity",
        "description" => "Leveraging artificial intelligence to detect and mitigate cyber threats."
    ]
];

$areas = $data['research_areas'];
// Remove Network Sec
foreach ($areas as $k => $v) {
    if ($v['title'] === 'Network Sec') {
        unset($areas[$k]);
    }
}

$data['research_areas'] = array_merge($new_areas, array_values($areas));

file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
echo "Done\n";
