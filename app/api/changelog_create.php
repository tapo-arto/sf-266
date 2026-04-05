<?php
/**
 * API Endpoint: Create Changelog Entry
 *
 * Creates a new sf_changelog record. Admin only.
 */

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../../assets/lib/Database.php';

global $config;
Database::setConfig($config['db'] ?? []);

$user = sf_current_user();
if (!$user) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'error' => 'Unauthorized'], JSON_UNESCAPED_UNICODE);
    exit;
}

if ((int)($user['role_id'] ?? 0) !== 1) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'error' => 'Admin access required'], JSON_UNESCAPED_UNICODE);
    exit;
}

if (!sf_csrf_validate()) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'error' => 'CSRF validation failed'], JSON_UNESCAPED_UNICODE);
    exit;
}

$userId      = (int)$user['id'];
$rawJson     = trim($_POST['translations'] ?? '');
$isPublished = (int)($_POST['is_published'] ?? 0) === 1 ? 1 : 0;
$feedbackId  = (int)($_POST['feedback_id'] ?? 0);

// Optional publish date; validate format YYYY-MM-DD
$publishDateRaw = trim($_POST['publish_date'] ?? '');
$publishDate    = null;
if ($publishDateRaw !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $publishDateRaw)) {
    $publishDate = $publishDateRaw;
}

// Validate translations JSON
$translations = json_decode($rawJson, true);
if (!is_array($translations)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Invalid translations JSON'], JSON_UNESCAPED_UNICODE);
    exit;
}

// Ensure at least one language has a title
$hasContent = false;
foreach ($translations as $lang => $t) {
    if (!empty($t['title']) || !empty($t['content'])) {
        $hasContent = true;
        break;
    }
}
if (!$hasContent) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'At least one language must have content'], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $db = Database::getInstance();
    $stmt = $db->prepare(
        "INSERT INTO sf_changelog (feedback_id, translations, is_published, publish_date, created_by, created_at, updated_at)
         VALUES (:feedback_id, :translations, :is_published, :publish_date, :created_by, NOW(), NOW())"
    );
    $stmt->execute([
        ':feedback_id'  => $feedbackId > 0 ? $feedbackId : null,
        ':translations' => json_encode($translations, JSON_UNESCAPED_UNICODE),
        ':is_published' => $isPublished,
        ':publish_date' => $publishDate,
        ':created_by'   => $userId,
    ]);

    echo json_encode(['ok' => true, 'id' => (int)$db->lastInsertId()], JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    error_log('changelog_create error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Database error'], JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    error_log('changelog_create error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Server error'], JSON_UNESCAPED_UNICODE);
}
