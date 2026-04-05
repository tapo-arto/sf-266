<?php
/**
 * Updates Page
 *
 * Lists published changelog entries newest-first.
 * Content is shown in the user's active UI language with fallback to English then Finnish.
 */

declare(strict_types=1);

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../app/includes/auth.php';
require_once __DIR__ . '/../../assets/lib/Database.php';
require_once __DIR__ . '/../../assets/lib/sf_terms.php';

sf_require_login();

Database::setConfig($config['db'] ?? []);

$user    = sf_current_user();
$uiLang  = $_SESSION['ui_lang'] ?? 'fi';
$base    = rtrim($config['base_url'] ?? '', '/');

// Load published changelog entries newest first
$db = Database::getInstance();
$stmt = $db->prepare(
    "SELECT c.*, u.first_name, u.last_name
     FROM sf_changelog c
     LEFT JOIN sf_users u ON c.created_by = u.id
     WHERE c.is_published = 1
     ORDER BY c.created_at DESC"
);
$stmt->execute();
$entries = $stmt->fetchAll();

/**
 * Resolve translated title/content for the given language with fallback.
 *
 * @param array  $translations  Decoded JSON array
 * @param string $lang          Desired language code
 * @param string $field         'title' or 'content'
 * @return string
 */
function resolveTranslation(array $translations, string $lang, string $field): string
{
    if (!empty($translations[$lang][$field])) {
        return $translations[$lang][$field];
    }
    // Fallback chain: en → fi → first available
    foreach (['en', 'fi'] as $fallback) {
        if (!empty($translations[$fallback][$field])) {
            return $translations[$fallback][$field];
        }
    }
    foreach ($translations as $t) {
        if (!empty($t[$field])) {
            return $t[$field];
        }
    }
    return '';
}
?>

<div class="sf-page-container">
    <div class="sf-page-header">
        <h1 class="sf-page-title">
            <?= htmlspecialchars(sf_term('updates_title', $uiLang), ENT_QUOTES, 'UTF-8') ?>
        </h1>
    </div>

    <?php if (empty($entries)): ?>
        <div class="sf-updates-empty">
            <p><?= htmlspecialchars(sf_term('updates_empty', $uiLang), ENT_QUOTES, 'UTF-8') ?></p>
        </div>
    <?php else: ?>
        <div class="sf-updates-timeline">
            <?php foreach ($entries as $entry): ?>
                <?php
                $translations = [];
                if (!empty($entry['translations'])) {
                    $decoded = json_decode($entry['translations'], true);
                    if (is_array($decoded)) {
                        $translations = $decoded;
                    }
                }
                $title   = resolveTranslation($translations, $uiLang, 'title');
                $content = resolveTranslation($translations, $uiLang, 'content');
                $authorName = trim(($entry['first_name'] ?? '') . ' ' . ($entry['last_name'] ?? ''));
                if ($authorName === '') {
                    $authorName = 'Admin';
                }
                $dateStr = date('d.m.Y', strtotime($entry['created_at']));
                ?>
                <div class="sf-updates-item sf-card-appear">
                    <div class="sf-updates-item-date"><?= htmlspecialchars($dateStr, ENT_QUOTES, 'UTF-8') ?></div>
                    <div class="sf-updates-item-body">
                        <?php if ($title !== ''): ?>
                            <h2 class="sf-updates-item-title"><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></h2>
                        <?php endif; ?>
                        <?php if ($content !== ''): ?>
                            <div class="sf-updates-item-content">
                                <?= nl2br(htmlspecialchars($content, ENT_QUOTES, 'UTF-8')) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<style>
.sf-updates-empty {
    padding: 48px 24px;
    text-align: center;
    color: var(--sf-muted);
    font-size: 1rem;
}

.sf-updates-timeline {
    display: flex;
    flex-direction: column;
    gap: 0;
    max-width: 760px;
    margin: 0 auto;
    padding: 24px 0;
    position: relative;
}

.sf-updates-timeline::before {
    content: '';
    position: absolute;
    left: 96px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: var(--sf-border);
}

.sf-updates-item {
    display: flex;
    gap: 24px;
    padding: 0 0 32px 0;
    position: relative;
}

.sf-updates-item::before {
    content: '';
    position: absolute;
    left: 89px;
    top: 6px;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    background: var(--sf-yellow, #FEE000);
    border: 3px solid var(--sf-surface, #fff);
    box-shadow: 0 0 0 2px var(--sf-border);
    z-index: 1;
}

.sf-updates-item-date {
    width: 80px;
    flex-shrink: 0;
    font-size: 0.78rem;
    color: var(--sf-muted);
    padding-top: 4px;
    text-align: right;
}

.sf-updates-item-body {
    flex: 1;
    background: var(--sf-surface, #fff);
    border: 1px solid var(--sf-border);
    border-radius: var(--sf-radius, 14px);
    padding: 18px 22px;
    box-shadow: 0 1px 4px rgba(0,0,0,0.05);
    margin-left: 12px;
}

.sf-updates-item-title {
    font-size: 1.05rem;
    font-weight: 600;
    margin: 0 0 10px;
    color: var(--sf-text, #111827);
}

.sf-updates-item-content {
    font-size: 0.93rem;
    color: var(--sf-text, #111827);
    line-height: 1.6;
    white-space: pre-wrap;
}

@media (max-width: 600px) {
    .sf-updates-timeline::before {
        left: 0;
    }
    .sf-updates-item {
        flex-direction: column;
        gap: 6px;
        padding-left: 16px;
    }
    .sf-updates-item::before {
        left: -7px;
        top: 4px;
    }
    .sf-updates-item-date {
        width: auto;
        text-align: left;
    }
    .sf-updates-item-body {
        margin-left: 0;
    }
}
</style>
