<?php
session_start();
header('Content-Type: application/json');

// Only allow logged-in admins
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$file = $_POST['file'] ?? '';
$content = $_POST['content'] ?? '';

// Validate and sanitize filename
$file = basename($file);
$allowed_files = [
    'publications.json', 'projects.json', 'patents.json',
    'teaching.json', 'seminars.json', 'memberships.json',
    'editorships.json', 'awards.json', 'research_group.json'
];

if (!in_array($file, $allowed_files)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid file']);
    exit;
}

$path = __DIR__ . '/../data/' . $file;

// Validate JSON
$decoded = json_decode($content);
if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid JSON: ' . json_last_error_msg()]);
    exit;
}

// Save with pretty print
$pretty = json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
if (file_put_contents($path, $pretty)) {
    echo json_encode(['success' => true, 'message' => 'Changes saved successfully!']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to write file. Check permissions.']);
}
