<?php
/**
 * API Endpoint: Update Changelog Entry
 *
 * Updates an existing sf_changelog record. Admin only.
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

$updateId    = (int)($_POST['update_id'] ?? 0);
$rawJson     = trim($_POST['translations'] ?? '');
$isPublished = (int)($_POST['is_published'] ?? 0) === 1 ? 1 : 0;
$feedbackId  = (int)($_POST['feedback_id'] ?? 0);

// Optional publish date override; validate format YYYY-MM-DD
$publishDateRaw = trim($_POST['publish_date'] ?? '');
$publishDate    = null;
if ($publishDateRaw !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $publishDateRaw)) {
    $publishDate = $publishDateRaw;
}

if ($updateId <= 0) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Invalid update ID'], JSON_UNESCAPED_UNICODE);
    exit;
}

$translations = json_decode($rawJson, true);
if (!is_array($translations)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Invalid translations JSON'], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $db = Database::getInstance();

    // Verify the record exists
    $check = $db->prepare("SELECT id FROM sf_changelog WHERE id = :id");
    $check->execute([':id' => $updateId]);
    if (!$check->fetch()) {
        http_response_code(404);
        echo json_encode(['ok' => false, 'error' => 'Update not found'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $stmt = $db->prepare(
        "UPDATE sf_changelog
         SET translations = :translations,
             is_published = :is_published,
             publish_date = :publish_date,
             feedback_id  = :feedback_id,
             updated_at   = NOW()
         WHERE id = :id"
    );
    $stmt->execute([
        ':translations' => json_encode($translations, JSON_UNESCAPED_UNICODE),
        ':is_published' => $isPublished,
        ':publish_date' => $publishDate,
        ':feedback_id'  => $feedbackId > 0 ? $feedbackId : null,
        ':id'           => $updateId,
    ]);

    echo json_encode(['ok' => true], JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    error_log('changelog_update error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Database error'], JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    error_log('changelog_update error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Server error'], JSON_UNESCAPED_UNICODE);
}
