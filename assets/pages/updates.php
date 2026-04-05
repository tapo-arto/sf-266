<?php
/**
 * Updates Page
 *
 * Lists published changelog entries newest-first.
 * Content is shown in the user's active UI language with fallback to English then Finnish.
 * Clicking a title or the "Read more" button opens a modal with the full entry.
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
    "SELECT *
     FROM sf_changelog
     WHERE is_published = 1
     ORDER BY COALESCE(publish_date, DATE(created_at)) DESC, created_at DESC"
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

/**
 * Sanitize changelog HTML content.
 * Allows safe formatting tags and removes all attributes.
 * Identical logic to sf_sanitize_ai_html() used on the view page.
 * Falls back to nl2br for plain-text (no HTML tags) content.
 */
function sf_updates_sanitize_html(string $html): string
{
    // Plain-text content: convert newlines to <br> tags
    if (strip_tags($html) === $html) {
        return nl2br(htmlspecialchars($html, ENT_QUOTES, 'UTF-8'));
    }
    // HTML content: strip disallowed tags and remove all attributes
    $allowed = '<p><br><strong><em><u><ol><ul><li><span>';
    $html = strip_tags($html, $allowed);
    $html = preg_replace('/<(\w+)(?:\s[^>]*)?(\/?)>/', '<$1$2>', $html);
    return $html;
}
?>

<div class="sf-page-container">
    <div class="sf-page-header">
        <h1 class="sf-page-title">
            <?= htmlspecialchars(sf_term('updates_title', $uiLang), ENT_QUOTES, 'UTF-8') ?>
        </h1>
    </div>
    <p class="sf-updates-description">
        <?= htmlspecialchars(sf_term('updates_description', $uiLang), ENT_QUOTES, 'UTF-8') ?>
    </p>

    <?php if (empty($entries)): ?>
        <div class="sf-updates-empty">
            <p><?= htmlspecialchars(sf_term('updates_empty', $uiLang), ENT_QUOTES, 'UTF-8') ?></p>
        </div>
    <?php else: ?>
        <?php
        // Build sorted unique month list for filter buttons
        $months = [];
        foreach ($entries as $e) {
            $rawDate = !empty($e['publish_date']) ? $e['publish_date'] : $e['created_at'];
            $ts = strtotime($rawDate);
            if ($ts === false) { continue; }
            $key = date('Y-m', $ts);
            if (!isset($months[$key])) {
                // Localise month label: use IntlDateFormatter when available, otherwise a manual map
                if (class_exists('IntlDateFormatter')) {
                    $localeMap = ['fi' => 'fi_FI', 'sv' => 'sv_SE', 'en' => 'en_US', 'it' => 'it_IT', 'el' => 'el_GR'];
                    $locale = $localeMap[$uiLang] ?? 'en_US';
                    $fmt = new IntlDateFormatter(
                        $locale,
                        IntlDateFormatter::NONE,
                        IntlDateFormatter::NONE,
                        null,
                        null,
                        'MMMM yyyy'
                    );
                    $label = ucfirst($fmt->format($ts));
                } else {
                    $label = date('m/Y', $ts);
                }
                $months[$key] = $label;
            }
        }
        ?>
        <?php if (!empty($months)): ?>
        <div class="sf-updates-filter" role="group" aria-label="<?= htmlspecialchars(sf_term('updates_filter_label', $uiLang), ENT_QUOTES, 'UTF-8') ?>">
            <span class="sf-updates-filter-label">
                <?= htmlspecialchars(sf_term('updates_filter_label', $uiLang), ENT_QUOTES, 'UTF-8') ?>
            </span>
            <div class="sf-updates-filter-buttons">
                <button type="button"
                        class="sf-btn sf-btn-small sf-btn-primary sf-updates-filter-btn"
                        data-month="all">
                    <?= htmlspecialchars(sf_term('updates_filter_all', $uiLang), ENT_QUOTES, 'UTF-8') ?>
                </button>
                <?php foreach ($months as $key => $label): ?>
                    <button type="button"
                            class="sf-btn sf-btn-small sf-btn-secondary sf-updates-filter-btn"
                            data-month="<?= htmlspecialchars($key, ENT_QUOTES, 'UTF-8') ?>">
                        <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        <div class="sf-updates-timeline" id="sfUpdatesTimeline">
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
                // Use publish_date when set, otherwise fall back to created_at
                $rawDate = !empty($entry['publish_date']) ? $entry['publish_date'] : $entry['created_at'];
                $displayTimestamp = strtotime($rawDate);
                if ($displayTimestamp === false) { $displayTimestamp = time(); }
                $dateStr  = date('d.m.Y', $displayTimestamp);
                $monthKey = date('Y-m', $displayTimestamp);
                $entryId  = (int)$entry['id'];
                // Sanitize content for safe HTML rendering
                $sanitizedContent = sf_updates_sanitize_html($content);
                ?>
                <div class="sf-updates-item sf-card-appear" data-month="<?= htmlspecialchars($monthKey, ENT_QUOTES, 'UTF-8') ?>">
                    <div class="sf-updates-item-date"><?= htmlspecialchars($dateStr, ENT_QUOTES, 'UTF-8') ?></div>
                    <div class="sf-updates-item-body">
                        <?php if ($title !== ''): ?>
                            <h2 class="sf-updates-item-title">
                                <button type="button"
                                        class="sf-updates-title-btn"
                                        data-entry-id="<?= $entryId ?>"
                                        aria-haspopup="dialog">
                                    <?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>
                                </button>
                            </h2>
                        <?php endif; ?>
                        <?php if ($content !== ''): ?>
                            <button type="button"
                                    class="sf-btn sf-btn-small sf-btn-secondary sf-updates-read-more"
                                    data-entry-id="<?= $entryId ?>"
                                    aria-haspopup="dialog">
                                <?= htmlspecialchars(sf_term('updates_read_more', $uiLang), ENT_QUOTES, 'UTF-8') ?>
                            </button>
                            <!-- Hidden content rendered server-side for safe injection into modal -->
                            <div id="sf-update-content-<?= $entryId ?>"
                                 class="sf-updates-hidden-content"
                                 aria-hidden="true">
                                <?= $sanitizedContent ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Detail Modal -->
<div id="sfUpdateDetailModal" class="sf-modal hidden" role="dialog" aria-modal="true" aria-labelledby="sfUpdateDetailModalTitle">
    <div class="sf-modal-content sf-updates-modal-content">
        <div class="sf-modal-header">
            <h3 id="sfUpdateDetailModalTitle" class="sf-updates-modal-title"></h3>
            <button type="button" class="sf-modal-close-btn" data-modal-close aria-label="<?= htmlspecialchars(sf_term('updates_close', $uiLang), ENT_QUOTES, 'UTF-8') ?>">×</button>
        </div>
        <div class="sf-modal-body sf-updates-modal-body" id="sfUpdateDetailModalBody"></div>
        <div class="sf-modal-actions">
            <button type="button" class="sf-btn sf-btn-secondary" data-modal-close>
                <?= htmlspecialchars(sf_term('updates_close', $uiLang), ENT_QUOTES, 'UTF-8') ?>
            </button>
        </div>
    </div>
</div>

<style>
.sf-updates-description {
    margin: 1.25rem 0 2rem;
    color: rgba(255, 255, 255, 0.8);
    font-size: 1rem;
    line-height: 1.5;
}

.sf-updates-empty {
    padding: 48px 24px;
    text-align: center;
    color: var(--sf-muted);
    font-size: 1rem;
}

.sf-updates-filter {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    flex-wrap: wrap;
    margin-bottom: 20px;
}

.sf-updates-filter-label {
    font-size: 0.85rem;
    color: rgba(255, 255, 255, 0.7);
    white-space: nowrap;
    padding-top: 5px;
    flex-shrink: 0;
}

.sf-updates-filter-buttons {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
}

.sf-updates-timeline {
    display: flex;
    flex-direction: column;
    gap: 0;
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
    text-align: left;
}

.sf-updates-item-title {
    font-size: 1.05rem;
    font-weight: 600;
    margin: 0 0 12px;
    color: var(--sf-text, #111827);
    text-align: left;
}

.sf-updates-title-btn {
    background: none;
    border: none;
    padding: 0;
    margin: 0;
    font-size: inherit;
    font-weight: inherit;
    color: inherit;
    cursor: pointer;
    text-align: left;
    text-decoration: underline;
    text-decoration-color: transparent;
    transition: text-decoration-color 0.15s;
    font-family: inherit;
    line-height: inherit;
}

.sf-updates-title-btn:hover,
.sf-updates-title-btn:focus-visible {
    text-decoration-color: currentColor;
    outline: none;
}

.sf-updates-hidden-content {
    display: none;
}

/* Modal content formatting */
.sf-updates-modal-content {
    max-width: 640px;
    width: 100%;
}

.sf-updates-modal-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--sf-text, #111827);
    margin: 0;
    text-align: left;
}

.sf-updates-modal-body {
    font-size: 0.93rem;
    color: var(--sf-text, #111827);
    line-height: 1.7;
    text-align: left;
}

.sf-updates-modal-body p {
    margin: 0 0 0.75em;
}

.sf-updates-modal-body p:last-child {
    margin-bottom: 0;
}

.sf-updates-modal-body ul,
.sf-updates-modal-body ol {
    margin: 0 0 0.75em;
    padding-left: 1.5em;
}

.sf-updates-modal-body li {
    margin-bottom: 0.25em;
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

<script>
(function () {
    var modal = document.getElementById('sfUpdateDetailModal');
    var modalTitle = document.getElementById('sfUpdateDetailModalTitle');
    var modalBody = document.getElementById('sfUpdateDetailModalBody');

    function openUpdateModal(entryId) {
        var titleBtn = document.querySelector('.sf-updates-title-btn[data-entry-id="' + entryId + '"]');
        var contentEl = document.getElementById('sf-update-content-' + entryId);

        if (modalTitle) {
            modalTitle.textContent = titleBtn ? titleBtn.textContent.trim() : '';
        }
        if (modalBody && contentEl) {
            modalBody.innerHTML = contentEl.innerHTML;
        }

        if (modal) {
            modal.classList.remove('hidden');
            document.body.classList.add('sf-modal-open');
            var closeBtn = modal.querySelector('.sf-modal-close-btn');
            if (closeBtn) closeBtn.focus({ preventScroll: true });
        }
    }

    document.addEventListener('click', function (e) {
        var trigger = e.target.closest('.sf-updates-title-btn, .sf-updates-read-more');
        if (trigger) {
            e.preventDefault();
            var entryId = parseInt(trigger.dataset.entryId, 10);
            if (entryId > 0) {
                openUpdateModal(entryId);
            }
        }
    });

    // Month filter
    var filterBtns = document.querySelectorAll('.sf-updates-filter-btn');
    if (filterBtns.length) {
        filterBtns.forEach(function (btn) {
            btn.addEventListener('click', function () {
                var month = this.dataset.month;
                // Toggle active state on buttons
                filterBtns.forEach(function (b) {
                    b.classList.remove('sf-btn-primary');
                    b.classList.add('sf-btn-secondary');
                });
                this.classList.add('sf-btn-primary');
                this.classList.remove('sf-btn-secondary');
                // Show/hide timeline items
                var items = document.querySelectorAll('#sfUpdatesTimeline .sf-updates-item');
                items.forEach(function (item) {
                    if (month === 'all' || item.dataset.month === month) {
                        item.style.display = '';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        });
    }
})();
</script>
