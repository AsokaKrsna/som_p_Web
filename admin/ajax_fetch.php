<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$file = basename($_GET['file'] ?? '');
$allowed_files = [
    'publications.json', 'projects.json', 'patents.json',
    'teaching.json', 'seminars.json', 'memberships.json',
    'editorships.json', 'awards.json', 'research_group.json',
    'lab_content.json', 'announcements.json', 'profile_content.json',
    'achievements.json'
];

if (!in_array($file, $allowed_files)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid file']);
    exit;
}

$path = __DIR__ . '/../data/' . $file;

if (!file_exists($path)) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'File not found']);
    exit;
}

$content = file_get_contents($path);
echo json_encode(['success' => true, 'content' => $content]);
