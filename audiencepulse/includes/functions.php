<?php
/**
 * AudiencePulse — Helper Functions
 */

// Get all settings as key-value array
function getSettings(): array {
    $rows = dbAll('SELECT setting_key, setting_value FROM settings');
    $settings = [];
    foreach ($rows as $row) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
    return $settings;
}

// Get a single setting value
function getSetting(string $key, string $default = ''): string {
    global $settings;
    return $settings[$key] ?? $default;
}

// Get all shows
function getShows(): array {
    return dbAll('SELECT * FROM shows ORDER BY created_at DESC');
}

// Get a single show
function getShow(int $id): ?array {
    return dbOne('SELECT * FROM shows WHERE id = ?', [$id]);
}

// Get polls for a show
function getPolls(int $showId): array {
    return dbAll('SELECT * FROM polls WHERE show_id = ? ORDER BY created_at DESC', [$showId]);
}

// Get a single poll with options
function getPoll(int $id): ?array {
    $poll = dbOne('SELECT * FROM polls WHERE id = ?', [$id]);
    if ($poll) {
        $poll['options'] = dbAll('SELECT * FROM poll_options WHERE poll_id = ? ORDER BY sort_order', [$id]);
        $poll['total_votes'] = (int) dbOne('SELECT COUNT(*) as cnt FROM votes WHERE poll_id = ?', [$id])['cnt'];
    }
    return $poll;
}

// Get vote counts per option for a poll
function getPollResults(int $pollId): array {
    return dbAll(
        'SELECT po.id, po.option_text, po.option_text_ar, COUNT(v.id) as votes
         FROM poll_options po
         LEFT JOIN votes v ON v.option_id = po.id AND v.poll_id = ?
         WHERE po.poll_id = ?
         GROUP BY po.id
         ORDER BY po.sort_order',
        [$pollId, $pollId]
    );
}

// Get messages for moderation
function getMessages(int $showId, string $status = ''): array {
    $sql = 'SELECT m.*, v.name as voter_name, v.phone as voter_phone, v.avatar as voter_avatar
            FROM messages m
            JOIN voters v ON v.id = m.voter_id
            WHERE m.show_id = ?';
    $params = [$showId];
    if ($status !== '') {
        $sql .= ' AND m.status = ?';
        $params[] = $status;
    }
    $sql .= ' ORDER BY m.created_at DESC';
    return dbAll($sql, $params);
}

// Get all voters
function getVoters(): array {
    return dbAll('SELECT * FROM voters ORDER BY created_at DESC');
}

// Get voter by phone
function getVoterByPhone(string $phone): ?array {
    return dbOne('SELECT * FROM voters WHERE phone = ?', [$phone]);
}

// Create or get voter
function findOrCreateVoter(string $phone, string $name = ''): int {
    $voter = getVoterByPhone($phone);
    if ($voter) {
        return (int) $voter['id'];
    }
    return dbInsert('INSERT INTO voters (phone, name, country) VALUES (?, ?, ?)', [$phone, $name, 'EG']);
}

// Cast a vote (deduplicated)
function castVote(int $pollId, int $optionId, string $phone, string $source = 'whatsapp'): array {
    $voterId = findOrCreateVoter($phone);
    $existing = dbOne('SELECT id FROM votes WHERE poll_id = ? AND voter_id = ?', [$pollId, $voterId]);
    if ($existing) {
        return ['success' => false, 'message' => 'duplicate_vote'];
    }
    dbInsert('INSERT INTO votes (poll_id, option_id, voter_id, source) VALUES (?, ?, ?, ?)', [$pollId, $optionId, $voterId, $source]);
    return ['success' => true, 'message' => 'vote_cast'];
}

// Add a message to moderation queue
function addMessage(int $showId, string $phone, string $content, string $contentAr = ''): int {
    $voterId = findOrCreateVoter($phone);
    return dbInsert('INSERT INTO messages (show_id, voter_id, content, content_ar, status) VALUES (?, ?, ?, ?, ?)', [$showId, $voterId, $content, $contentAr, 'pending']);
}

// Update message status
function updateMessageStatus(int $messageId, string $status, string $notes = ''): void {
    dbExec('UPDATE messages SET status = ?, moderator_notes = ? WHERE id = ?', [$status, $notes, $messageId]);
}

// Get dashboard stats
function getDashboardStats(): array {
    return [
        'total_shows' => (int) dbOne('SELECT COUNT(*) as cnt FROM shows')['cnt'],
        'live_shows' => (int) dbOne("SELECT COUNT(*) as cnt FROM shows WHERE status = 'live'")['cnt'],
        'total_polls' => (int) dbOne('SELECT COUNT(*) as cnt FROM polls')['cnt'],
        'open_polls' => (int) dbOne("SELECT COUNT(*) as cnt FROM polls WHERE status = 'open'")['cnt'],
        'total_votes' => (int) dbOne('SELECT COUNT(*) as cnt FROM votes')['cnt'],
        'total_voters' => (int) dbOne('SELECT COUNT(*) as cnt FROM voters')['cnt'],
        'pending_messages' => (int) dbOne("SELECT COUNT(*) as cnt FROM messages WHERE status = 'pending'")['cnt'],
        'approved_messages' => (int) dbOne("SELECT COUNT(*) as cnt FROM messages WHERE status = 'approved'")['cnt'],
    ];
}

// Get recent votes for live feed
function getRecentVotes(int $limit = 20): array {
    return dbAll(
        'SELECT v.*, po.option_text, po.option_text_ar, p.question, p.question_ar, vv.name as voter_name
         FROM votes v
         JOIN poll_options po ON po.id = v.option_id
         JOIN polls p ON p.id = v.poll_id
         JOIN voters vv ON vv.id = v.voter_id
         ORDER BY v.created_at DESC
         LIMIT ?',
        [$limit]
    );
}

// Get viewer avatar path (falls back to default.png)
function viewerAvatar(array $voter): string {
    $avatar = $voter['avatar'] ?? '';
    $path = 'assets/images/viewers/';
    if ($avatar !== '' && file_exists(APP_ROOT . '/admin/' . $path . $avatar)) {
        return $path . $avatar;
    }
    return $path . 'default.png';
}

// Get country flag path (falls back to a neutral flag)
function countryFlag(string $country = ''): string {
    $country = strtoupper(trim($country));
    $path = 'assets/images/flags/';
    if ($country !== '' && file_exists(APP_ROOT . '/admin/' . $path . $country . '.png')) {
        return $path . $country . '.png';
    }
    return $path . 'flag-tag.png';
}

// Get TV show image path (falls back to default.png)
function tvShowImage(array $show): string {
    $image = $show['image'] ?? '';
    $path = 'assets/images/tvshows/';
    if ($image !== '' && file_exists(APP_ROOT . '/admin/' . $path . $image)) {
        return $path . $image;
    }
    return $path . 'default.png';
}

// Escape output
function e(?string $value): string {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

// Format number
function fmt(int $number): string {
    return number_format($number);
}

// Time ago
function timeAgo(string $datetime): string {
    $time = strtotime($datetime);
    $diff = time() - $time;
    if ($diff < 60) return t('time.just_now');
    if ($diff < 3600) return sprintf(t('time.minutes_ago'), floor($diff / 60));
    if ($diff < 86400) return sprintf(t('time.hours_ago'), floor($diff / 3600));
    return sprintf(t('time.days_ago'), floor($diff / 86400));
}

// Status badge class
function statusBadge(string $status): string {
    $map = [
        'live' => 'success',
        'draft' => 'secondary',
        'ended' => 'dark',
        'open' => 'success',
        'closed' => 'dark',
        'pending' => 'warning',
        'approved' => 'success',
        'rejected' => 'danger',
        'held' => 'info',
    ];
    return $map[$status] ?? 'secondary';
}

// Status label
function statusLabel(string $status): string {
    $map = [
        'live' => t('status.live'),
        'draft' => t('status.draft'),
        'ended' => t('status.ended'),
        'open' => t('status.open'),
        'closed' => t('status.closed'),
        'pending' => t('status.pending'),
        'approved' => t('status.approved'),
        'rejected' => t('status.rejected'),
        'held' => t('status.held'),
    ];
    return $map[$status] ?? $status;
}
