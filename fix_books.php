<?php
$file = 'data/publications.json';
$data = json_decode(file_get_contents($file), true);

foreach ($data['books'] as &$c) {
    if ($c['title'] === '' && strpos($c['author'], 'S. Dehuri and S. Tripathy, "An Extended Bayesian/HAPSO Intelligent Method in Intrusion Detection System "') !== false) {
        $c['author'] = "S. Dehuri and S. Tripathy";
        $c['title'] = "An Extended Bayesian/HAPSO Intelligent Method in Intrusion Detection System";
    }
    
    if ($c['published_at'] === '' && strpos($c['author'], 'Subho Shankar Basu, Somanath Tripathy, Security and Fault Tolerance in Internet of Things.') !== false) {
        $c['author'] = "Subho Shankar Basu, Somanath Tripathy";
        $c['published_at'] = "Security and Fault Tolerance in Internet of Things. Page Springer, Cham, 2019.";
    }
}
unset($c);

file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
echo "Fixed publications.json\n";
