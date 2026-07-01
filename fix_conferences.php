<?php
$file = 'data/publications.json';
$data = json_decode(file_get_contents($file), true);

foreach ($data['conferences'] as &$c) {
    if (!isset($c['title'])) {
        $c['title'] = '';
        $c['link'] = '';
        
        $pub = $c['published_at'] ?? '';
        
        if (strpos($pub, "SCTP-Sec: Secure Transmission Control Protocol,") === 0) {
            $c['title'] = "SCTP-Sec: Secure Transmission Control Protocol";
            $c['published_at'] = "3rd Hackers' Workshop on Computer and Internet Security, Hackin-2009, pp 44-49, IIT Kanpur, March 17-19, 2009.";
            $c['author'] = "Rahul Choudhari and Somanath Tripathy";
        } elseif (strpos($pub, "Sukumar Nandi and Abhijit Mitra, 'A Secured Response Algorithm") === 0) {
            $c['title'] = "A Secured Response Algorithm with Query Authentication for Wireless Sensor Networks";
            $c['author'] = "Somanath Tripathy, Sukumar Nandi and Abhijit Mitra";
            $c['published_at'] = "5th Asian International Mobile Computing Conference, AMOC-2007, Tata Mcgrawhill Publication, pp.220-225, 2007, India.";
        } elseif (strpos($pub, "Security Enhanced used Identification and Distribution Scheme preserving Anonymity,") === 0) {
            $c['title'] = "Security Enhanced used Identification and Distribution Scheme preserving Anonymity";
            $c['published_at'] = "in Proc. of Intl. Conf. on Emerging Applications of IT (EAIT 2006), Elsevier, pp.201-204, 2006, (India).";
        } elseif (strpos($pub, "CASE: Cellular Automata based Symmetric Encryption,") === 0) {
            $c['title'] = "CASE: Cellular Automata based Symmetric Encryption";
            $c['published_at'] = "Third International Conference on Innovative Applications of Information Technology for Developing World, AACC 2005, 2005, (Nepal).";
        } elseif (strpos($pub, "Broadcast on Demand: A heuristic for real-time multiple data broadcast strategy") === 0) {
            $c['title'] = "Broadcast on Demand: A heuristic for real-time multiple data broadcast strategy in mobile environments";
            $c['published_at'] = "Asia Pacific Conference on Parallel and Distributed Computing Technologies (ObComAPC-2004), (India)";
        }
    }
}
unset($c); // break reference

// Re-order keys to match schema: title, link, impact_factor, author, published_at, doi, show_personal, show_lab
$ordered_conferences = [];
foreach ($data['conferences'] as $c) {
    $ordered = [
        'link' => $c['link'] ?? '',
        'title' => $c['title'] ?? '',
        'impact_factor' => $c['impact_factor'] ?? '',
        'author' => $c['author'] ?? '',
        'published_at' => $c['published_at'] ?? '',
        'doi' => $c['doi'] ?? '',
        'show_personal' => $c['show_personal'] ?? true,
        'show_lab' => $c['show_lab'] ?? true
    ];
    $ordered_conferences[] = $ordered;
}
$data['conferences'] = $ordered_conferences;

file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
echo "Done\n";
