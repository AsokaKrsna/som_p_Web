<?php
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

// Anti-spam Honeypot Check
if (!empty($_POST['bot_field'])) {
    // Bot detected: fake success response
    echo json_encode(['success' => true, 'message' => 'Your message has been sent successfully.']);
    exit;
}

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$degree = trim($_POST['degree'] ?? '');
$position = trim($_POST['position'] ?? '');
$message = trim($_POST['message'] ?? '');

// Validation
if (empty($name) || empty($email) || empty($message)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Please fill in all required fields (Name, Email, Message).']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Please provide a valid email address.']);
    exit;
}

$entry = [
    'timestamp' => date('Y-m-d H:i:s'),
    'name' => $name,
    'email' => $email,
    'degree' => $degree,
    'position' => $position,
    'message' => $message,
    'ip' => $_SERVER['REMOTE_ADDR'] ?? 'Unknown'
];

// Save to data/inquiries.json as a persistent backup
$inquiriesFile = __DIR__ . '/data/inquiries.json';
$inquiries = [];
if (file_exists($inquiriesFile)) {
    $existing = json_decode(@file_get_contents($inquiriesFile), true);
    if (is_array($existing)) {
        $inquiries = $existing;
    }
}
$inquiries[] = $entry;
@file_put_contents($inquiriesFile, json_encode($inquiries, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

// Dispatch Email to target address
$to = "durjoymajumdar02@gmail.com";
$subject = "[Lab Inquiry] " . (!empty($position) ? $position : "General Inquiry") . " from " . $name;

$emailBody = "
<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8'>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background: #f8fafc; color: #1e293b; padding: 20px; }
        .card { background: #ffffff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 28px; max-width: 600px; margin: 0 auto; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .header { border-bottom: 2px solid #0891b2; padding-bottom: 12px; margin-bottom: 20px; }
        .header h2 { margin: 0; color: #0891b2; font-size: 20px; }
        .field { margin-bottom: 14px; }
        .label { font-weight: bold; color: #64748b; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
        .value { font-size: 15px; color: #0f172a; }
        .message-box { background: #f1f5f9; padding: 14px; border-radius: 8px; border-left: 4px solid #0891b2; font-size: 14px; white-space: pre-wrap; margin-top: 6px; }
        .footer { font-size: 12px; color: #94a3b8; margin-top: 24px; text-align: center; }
    </style>
</head>
<body>
    <div class='card'>
        <div class='header'>
            <h2>New Inquiry - Cybersecurity Lab</h2>
        </div>
        <div class='field'>
            <div class='label'>Applicant Name</div>
            <div class='value'>" . htmlspecialchars($name) . "</div>
        </div>
        <div class='field'>
            <div class='label'>Email Address</div>
            <div class='value'><a href='mailto:" . htmlspecialchars($email) . "'>" . htmlspecialchars($email) . "</a></div>
        </div>
        " . (!empty($degree) ? "
        <div class='field'>
            <div class='label'>Current / Last Degree</div>
            <div class='value'>" . htmlspecialchars($degree) . "</div>
        </div>" : "") . "
        " . (!empty($position) ? "
        <div class='field'>
            <div class='label'>Position of Interest</div>
            <div class='value'>" . htmlspecialchars($position) . "</div>
        </div>" : "") . "
        <div class='field'>
            <div class='label'>Message / Statement</div>
            <div class='message-box'>" . htmlspecialchars($message) . "</div>
        </div>
        <div class='footer'>
            Sent via Cybersecurity Lab Portal &bull; " . date('d M Y, H:i:s T') . "
        </div>
    </div>
</body>
</html>
";

$headers = [];
$headers[] = 'MIME-Version: 1.0';
$headers[] = 'Content-type: text/html; charset=utf-8';
$headers[] = 'From: Cybersecurity Lab Portal <no-reply@iitp.ac.in>';
$headers[] = 'Reply-To: ' . $email;
$headers[] = 'X-Mailer: PHP/' . phpversion();

// Send email
@mail($to, $subject, $emailBody, implode("\r\n", $headers));

echo json_encode([
    'success' => true,
    'message' => 'Thank you! Your message has been sent successfully. We will get in touch with you soon.'
]);
