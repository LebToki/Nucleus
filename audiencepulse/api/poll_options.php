<?php
require_once __DIR__ . '/../includes/config.php';

header('Content-Type: application/json');

$pollId = (int)($_GET['poll_id'] ?? 0);
if (!$pollId) {
    echo json_encode([]);
    exit;
}

$options = dbAll('SELECT id, option_text, option_text_ar FROM poll_options WHERE poll_id = ? ORDER BY sort_order', [$pollId]);
echo json_encode($options);
