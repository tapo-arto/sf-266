<?php
/**
 * API Endpoint: Save Report Settings
 *
 * Saves editable per-flash settings:
 *   - original_type: The original Safetyflash category before any type change
 *
 * POST params:
 *   flash_id      (int, required)
 *   original_type (string: 'red'|'yellow'|'green'|'', required)
 *   csrf_token    (string, required)
 *
 * Auth: owner, admin (role 1), or safety team (role 3).
 */

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../../assets/lib/Database.php';

global $config;
Database::setConfig($config['db'] ?? []);

// Authentication
$user = sf_current_user();
if (!$user) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'error' => 'Unauthorized'], JSON_UNESCAPED_UNICODE);
    exit;
}

// Method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Method not allowed'], JSON_UNESCAPED_UNICODE);
    exit;
}

// CSRF
if (!sf_csrf_validate()) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'error' => 'CSRF validation failed'], JSON_UNESCAPED_UNICODE);
    exit;
}

$userId  = (int)$user['id'];
$roleId  = (int)($user['role_id'] ?? 0);
$isAdmin  = ($roleId === 1);
$isSafety = ($roleId === 3);

try {
    // Validate flash_id
    $flashId = (int)($_POST['flash_id'] ?? 0);
    if ($flashId <= 0) {
        http_response_code(400);
        echo json_encode(['ok' => false, 'error' => 'Invalid flash ID'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $pdo = Database::getInstance();

    // Load flash
    $stmt = $pdo->prepare("SELECT id, created_by, is_archived, state FROM sf_flashes WHERE id = ? LIMIT 1");
    $stmt->execute([$flashId]);
    $flash = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$flash) {
        http_response_code(404);
        echo json_encode(['ok' => false, 'error' => 'Flash not found'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Authorization
    $isOwner = ($userId > 0 && (int)$flash['created_by'] === $userId);
    if (!$isAdmin && !$isSafety && !$isOwner) {
        http_response_code(403);
        echo json_encode(['ok' => false, 'error' => 'Forbidden'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Archived check
    if (!empty($flash['is_archived'])) {
        http_response_code(403);
        echo json_encode(['ok' => false, 'error' => 'Cannot edit archived reports'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Published check — settings are locked once the report is published
    if (($flash['state'] ?? '') === 'published') {
        http_response_code(403);
        echo json_encode(['ok' => false, 'error' => 'Cannot change settings of published reports'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Validate original_type
    $allowedTypes = ['red', 'yellow', 'green', ''];
    $originalType = trim((string)($_POST['original_type'] ?? ''));
    if (!in_array($originalType, $allowedTypes, true)) {
        http_response_code(400);
        echo json_encode(['ok' => false, 'error' => 'Invalid original_type value'], JSON_UNESCAPED_UNICODE);
        exit;
    }
    $originalTypeValue = $originalType === '' ? null : $originalType;

    // Persist
    $upd = $pdo->prepare("UPDATE sf_flashes SET original_type = :original_type WHERE id = :id");
    $upd->execute([':original_type' => $originalTypeValue, ':id' => $flashId]);

    echo json_encode(['ok' => true, 'original_type' => $originalTypeValue], JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {
    error_log('save_report_settings.php: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Server error'], JSON_UNESCAPED_UNICODE);
    exit;
}
