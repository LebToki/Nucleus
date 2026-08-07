<?php
require_once __DIR__ . '/../includes/config.php';

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$showId = (int)($input['show_id'] ?? 0);
$phone = $input['phone'] ?? '';
$name = $input['name'] ?? '';
$content = $input['content'] ?? '';

if (!$showId || !$phone || !$content) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

$messageId = addMessage($showId, $phone, $content);
echo json_encode(['success' => true, 'message_id' => $messageId]);
