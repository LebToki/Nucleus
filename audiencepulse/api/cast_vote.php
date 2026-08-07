<?php
require_once __DIR__ . '/../includes/config.php';

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$pollId = (int)($input['poll_id'] ?? 0);
$optionId = (int)($input['option_id'] ?? 0);
$phone = $input['phone'] ?? '';

if (!$pollId || !$optionId || !$phone) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

$result = castVote($pollId, $optionId, $phone, 'simulation');
echo json_encode($result);
