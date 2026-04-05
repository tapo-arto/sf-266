<?php
// assets/pages/settings/tab_updates.php
declare(strict_types=1);

// $currentUiLang and $baseUrl come from settings.php
// Database is already configured via config.php

$db = Database::getInstance();

// Fetch all changelog entries (published and drafts) newest first
$stmt = $db->prepare(
    "SELECT c.*, u.first_name, u.last_name
     FROM sf_changelog c
     LEFT JOIN sf_users u ON c.created_by = u.id
     ORDER BY c.created_at DESC"
);
$stmt->execute();
$entries = $stmt->fetchAll();

// Supported languages from terms config
$termsConfig = sf_get_terms_config();
$supportedLangs = $termsConfig['languages'] ?? ['fi', 'sv', 'en', 'it', 'el'];
$langLabels = [
    'fi' => 'Suomi (FI)',
    'sv' => 'Svenska (SV)',
    'en' => 'English (EN)',
    'it' => 'Italiano (IT)',
    'el' => 'Ελληνικά (EL)',
];

// Current admin user id
$adminUser = sf_current_user();
$adminUserId = (int)($adminUser['id'] ?? 0);

$csrfField = sf_csrf_field();
$csrfToken = sf_csrf_token();
?>

<div class="sf-settings-section">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
        <h3 style="margin:0;padding:0;border:none;">
            <?= htmlspecialchars(sf_term('admin_updates_heading', $currentUiLang), ENT_QUOTES, 'UTF-8') ?>
        </h3>
        <button type="button" class="sf-btn sf-btn-primary" id="btnNewUpdate">
            <img src="<?= $baseUrl ?>/assets/img/icons/changelog_icon.svg" alt="" class="sf-btn-icon" aria-hidden="true">
            <?= htmlspecialchars(sf_term('admin_updates_new', $currentUiLang), ENT_QUOTES, 'UTF-8') ?>
        </button>
    </div>

    <?php if (empty($entries)): ?>
        <p style="color:var(--sf-muted);">
            <?= htmlspecialchars(sf_term('updates_empty', $currentUiLang), ENT_QUOTES, 'UTF-8') ?>
        </p>
    <?php else: ?>
        <div class="sf-updates-admin-list">
            <?php foreach ($entries as $entry): ?>
                <?php
                $translations = [];
                if (!empty($entry['translations'])) {
                    $decoded = json_decode($entry['translations'], true);
                    if (is_array($decoded)) {
                        $translations = $decoded;
                    }
                }
                // Show Finnish title if available, otherwise first available
                $displayTitle = '';
                foreach (['fi', 'en', 'sv'] as $tryLang) {
                    if (!empty($translations[$tryLang]['title'])) {
                        $displayTitle = $translations[$tryLang]['title'];
                        break;
                    }
                }
                if ($displayTitle === '') {
                    foreach ($translations as $t) {
                        if (!empty($t['title'])) {
                            $displayTitle = $t['title'];
                            break;
                        }
                    }
                }
                $isPublished = (int)$entry['is_published'];
                $authorName = trim(($entry['first_name'] ?? '') . ' ' . ($entry['last_name'] ?? ''));
                if ($authorName === '') $authorName = 'Admin';
                $dateStr = date('d.m.Y H:i', strtotime($entry['created_at']));
                ?>
                <div class="sf-updates-admin-item" id="update-row-<?= (int)$entry['id'] ?>">
                    <div class="sf-updates-admin-item-info">
                        <span class="sf-updates-admin-status <?= $isPublished ? 'published' : 'draft' ?>">
                            <?= $isPublished
                                ? htmlspecialchars(sf_term('admin_updates_publish', $currentUiLang), ENT_QUOTES, 'UTF-8')
                                : htmlspecialchars(sf_term('admin_updates_draft', $currentUiLang), ENT_QUOTES, 'UTF-8') ?>
                        </span>
                        <strong class="sf-updates-admin-title">
                            <?= htmlspecialchars($displayTitle ?: '—', ENT_QUOTES, 'UTF-8') ?>
                        </strong>
                        <span class="sf-updates-admin-meta"><?= htmlspecialchars($dateStr, ENT_QUOTES, 'UTF-8') ?></span>
                        <?php if (!empty($entry['feedback_id'])): ?>
                            <span class="sf-updates-admin-meta">
                                <?= htmlspecialchars(sf_term('updates_linked_feedback', $currentUiLang), ENT_QUOTES, 'UTF-8') ?>
                                #<?= (int)$entry['feedback_id'] ?>
                            </span>
                        <?php endif; ?>
                    </div>
                    <div class="sf-updates-admin-item-actions">
                        <button type="button"
                                class="sf-btn sf-btn-small sf-btn-secondary btn-edit-update"
                                data-id="<?= (int)$entry['id'] ?>"
                                data-translations="<?= htmlspecialchars(json_encode($translations), ENT_QUOTES, 'UTF-8') ?>"
                                data-is-published="<?= $isPublished ?>"
                                data-feedback-id="<?= (int)($entry['feedback_id'] ?? 0) ?>">
                            <?= htmlspecialchars(sf_term('admin_updates_edit', $currentUiLang), ENT_QUOTES, 'UTF-8') ?>
                        </button>
                        <button type="button"
                                class="sf-btn sf-btn-small sf-btn-danger btn-delete-update"
                                data-id="<?= (int)$entry['id'] ?>">
                            <?= htmlspecialchars(sf_term('feedback_delete', $currentUiLang), ENT_QUOTES, 'UTF-8') ?>
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Create / Edit Update Modal -->
<div id="modalUpdateEdit" class="sf-modal hidden" aria-hidden="true" role="dialog" aria-modal="true">
    <div class="sf-modal-content" style="max-width:680px;">
        <div class="sf-modal-header">
            <h3 id="modalUpdateEditTitle">
                <?= htmlspecialchars(sf_term('admin_updates_new', $currentUiLang), ENT_QUOTES, 'UTF-8') ?>
            </h3>
            <button type="button" class="sf-modal-close-btn" data-modal-close aria-label="Close">×</button>
        </div>

        <form id="formUpdateEdit" class="sf-modal-body">
            <?= $csrfField ?>
            <input type="hidden" id="updateEditId" name="update_id" value="0">
            <input type="hidden" id="updateEditFeedbackId" name="feedback_id" value="0">

            <!-- Language tabs -->
            <div class="sf-lang-tabs" style="display:flex;gap:4px;margin-bottom:16px;flex-wrap:wrap;">
                <?php foreach ($supportedLangs as $idx => $lang): ?>
                    <button type="button"
                            class="sf-btn sf-btn-small <?= $idx === 0 ? 'sf-btn-primary' : 'sf-btn-secondary' ?> sf-update-lang-tab"
                            data-lang="<?= htmlspecialchars($lang) ?>">
                        <?= htmlspecialchars($langLabels[$lang] ?? strtoupper($lang)) ?>
                    </button>
                <?php endforeach; ?>
            </div>

            <!-- Per-language fields -->
            <?php foreach ($supportedLangs as $idx => $lang): ?>
                <div class="sf-update-lang-panel" data-lang="<?= htmlspecialchars($lang) ?>"
                     <?= $idx !== 0 ? 'style="display:none;"' : '' ?>>
                    <div class="sf-form-group">
                        <label for="updateTitle_<?= $lang ?>">
                            <?= htmlspecialchars(sf_term('updates_field_title', $currentUiLang), ENT_QUOTES, 'UTF-8') ?>
                            (<?= htmlspecialchars(strtoupper($lang)) ?>)
                        </label>
                        <input type="text"
                               id="updateTitle_<?= $lang ?>"
                               name="title_<?= $lang ?>"
                               class="sf-form-input sf-update-title-field"
                               data-lang="<?= htmlspecialchars($lang) ?>">
                    </div>
                    <div class="sf-form-group">
                        <label for="updateContent_<?= $lang ?>">
                            <?= htmlspecialchars(sf_term('updates_field_content', $currentUiLang), ENT_QUOTES, 'UTF-8') ?>
                            (<?= htmlspecialchars(strtoupper($lang)) ?>)
                        </label>
                        <div id="updateContentEditor_<?= $lang ?>"
                             class="sf-update-quill-editor"
                             data-lang="<?= htmlspecialchars($lang) ?>"
                             style="min-height:140px;background:#fff;"></div>
                        <input type="hidden"
                               id="updateContent_<?= $lang ?>"
                               name="content_<?= $lang ?>"
                               class="sf-update-content-field"
                               data-lang="<?= htmlspecialchars($lang) ?>">
                    </div>
                </div>
            <?php endforeach; ?>

            <div class="sf-form-group" style="margin-top:12px;">
                <label class="sf-checkbox-label" style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                    <input type="checkbox" id="updateIsPublished" name="is_published" value="1"
                           style="width:18px;height:18px;cursor:pointer;">
                    <span><?= htmlspecialchars(sf_term('updates_field_is_published', $currentUiLang), ENT_QUOTES, 'UTF-8') ?></span>
                </label>
            </div>
        </form>

        <div class="sf-modal-actions">
            <button type="button" class="sf-btn sf-btn-secondary" data-modal-close>
                <?= htmlspecialchars(sf_term('feedback_cancel', $currentUiLang), ENT_QUOTES, 'UTF-8') ?>
            </button>
            <button type="button" class="sf-btn sf-btn-primary" id="btnSaveUpdate">
                <?= htmlspecialchars(sf_term('feedback_save', $currentUiLang), ENT_QUOTES, 'UTF-8') ?>
            </button>
        </div>
    </div>
</div>

<style>
.sf-update-quill-editor .ql-toolbar {
    border-radius: var(--sf-radius, 14px) var(--sf-radius, 14px) 0 0;
    border-color: var(--sf-border);
}
.sf-update-quill-editor .ql-container {
    border-radius: 0 0 var(--sf-radius, 14px) var(--sf-radius, 14px);
    border-color: var(--sf-border);
    font-size: 0.9rem;
    font-family: inherit;
}
.sf-updates-admin-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.sf-updates-admin-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    padding: 14px 18px;
    background: var(--sf-surface, #fff);
    border: 1px solid var(--sf-border);
    border-radius: var(--sf-radius, 14px);
    flex-wrap: wrap;
}

.sf-updates-admin-item-info {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
    flex: 1;
    min-width: 0;
}

.sf-updates-admin-title {
    font-size: 0.95rem;
    color: var(--sf-text, #111827);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 280px;
}

.sf-updates-admin-meta {
    font-size: 0.78rem;
    color: var(--sf-muted);
}

.sf-updates-admin-status {
    font-size: 0.72rem;
    font-weight: 600;
    padding: 2px 8px;
    border-radius: 20px;
    white-space: nowrap;
}

.sf-updates-admin-status.published {
    background: #dcfce7;
    color: #166534;
}

.sf-updates-admin-status.draft {
    background: #f3f4f6;
    color: #6b7280;
}

.sf-updates-admin-item-actions {
    display: flex;
    gap: 8px;
    flex-shrink: 0;
}
</style>

<script src="<?= $baseUrl ?>/assets/js/vendor/quill.min.js"></script>
<script src="<?= $baseUrl ?>/assets/js/vendor/purify.min.js"></script>
<script>
(function () {
    const BASE_URL = <?= json_encode($baseUrl, JSON_UNESCAPED_SLASHES) ?>;
    const CSRF_TOKEN = <?= json_encode($csrfToken) ?>;
    const SUPPORTED_LANGS = <?= json_encode($supportedLangs) ?>;

    const i18n = {
        deleteConfirm: <?= json_encode(sf_term('admin_updates_delete_confirm', $currentUiLang)) ?>,
        saved:         <?= json_encode(sf_term('admin_updates_saved', $currentUiLang)) ?>,
        deleted:       <?= json_encode(sf_term('admin_updates_deleted', $currentUiLang)) ?>,
        errorSave:     <?= json_encode(sf_term('updates_error_save', $currentUiLang)) ?>,
        errorDelete:   <?= json_encode(sf_term('updates_error_delete', $currentUiLang)) ?>,
        titleNew:      <?= json_encode(sf_term('admin_updates_new', $currentUiLang)) ?>,
        titleEdit:     <?= json_encode(sf_term('admin_updates_edit', $currentUiLang)) ?>,
    };

    // Quill instances keyed by language code
    const quillEditors = {};
    const SAFE_TAGS = ['P', 'BR', 'STRONG', 'EM', 'U', 'OL', 'UL', 'LI', 'SPAN'];
    const QUILL_EMPTY_HTML = '<p><br></p>';

    function sanitizeHtml(html) {
        if (typeof DOMPurify !== 'undefined') {
            return DOMPurify.sanitize(html, { ALLOWED_TAGS: SAFE_TAGS, ALLOWED_ATTR: [] });
        }
        return html;
    }

    function getQuillEditor(lang) {
        if (quillEditors[lang]) { return quillEditors[lang]; }
        if (typeof Quill === 'undefined') { return null; }
        const editorEl = document.getElementById('updateContentEditor_' + lang);
        if (!editorEl) { return null; }
        quillEditors[lang] = new Quill('#updateContentEditor_' + lang, {
            theme: 'snow',
            modules: {
                toolbar: [
                    ['bold', 'italic', 'underline'],
                    [{ list: 'ordered' }, { list: 'bullet' }],
                    ['clean']
                ]
            }
        });
        return quillEditors[lang];
    }

    // Do NOT initialize Quill editors up front — the modal is hidden (display:none)
    // and Quill cannot measure/render inside an invisible container.
    // Editors are initialized lazily when the modal is first opened.

    function openModal(id) {
        const m = document.getElementById(id);
        if (m) { m.classList.remove('hidden'); m.setAttribute('aria-hidden', 'false'); document.body.style.overflow = 'hidden'; }
    }
    function closeModal(id) {
        const m = document.getElementById(id);
        if (m) { m.classList.add('hidden'); m.setAttribute('aria-hidden', 'true'); document.body.style.overflow = ''; }
    }

    document.querySelectorAll('[data-modal-close]').forEach(btn => {
        btn.addEventListener('click', function () {
            const modal = this.closest('.sf-modal');
            if (modal) closeModal(modal.id);
        });
    });

    // Language tab switching
    document.querySelectorAll('.sf-update-lang-tab').forEach(tab => {
        tab.addEventListener('click', function () {
            const lang = this.dataset.lang;
            document.querySelectorAll('.sf-update-lang-tab').forEach(t => {
                t.classList.remove('sf-btn-primary');
                t.classList.add('sf-btn-secondary');
            });
            this.classList.add('sf-btn-primary');
            this.classList.remove('sf-btn-secondary');
            document.querySelectorAll('.sf-update-lang-panel').forEach(p => {
                p.style.display = p.dataset.lang === lang ? '' : 'none';
            });
        });
    });

    function resetForm() {
        document.getElementById('updateEditId').value = '0';
        document.getElementById('updateEditFeedbackId').value = '0';
        document.getElementById('updateIsPublished').checked = false;
        SUPPORTED_LANGS.forEach(lang => {
            const titleEl = document.getElementById('updateTitle_' + lang);
            if (titleEl) titleEl.value = '';
            const q = quillEditors[lang];
            if (q) { q.setContents([]); }
        });
        // Activate first language tab
        const firstTab = document.querySelector('.sf-update-lang-tab');
        if (firstTab) firstTab.click();
    }

    // New update
    document.getElementById('btnNewUpdate')?.addEventListener('click', function () {
        document.getElementById('modalUpdateEditTitle').textContent = i18n.titleNew;
        // Open modal first so editors are visible (not display:none) before Quill initializes
        openModal('modalUpdateEdit');
        resetForm();
    });

    // Edit update
    document.querySelectorAll('.btn-edit-update').forEach(btn => {
        btn.addEventListener('click', function () {
            document.getElementById('modalUpdateEditTitle').textContent = i18n.titleEdit;
            const id = this.dataset.id;
            const translations = JSON.parse(this.dataset.translations || '{}');
            const isPublished = parseInt(this.dataset.isPublished, 10) === 1;
            const feedbackId = parseInt(this.dataset.feedbackId, 10) || 0;

            document.getElementById('updateEditId').value = id;
            document.getElementById('updateEditFeedbackId').value = feedbackId;
            document.getElementById('updateIsPublished').checked = isPublished;

            // Open modal FIRST so editors become visible before Quill initializes
            openModal('modalUpdateEdit');
            // Reset (clears existing editor content if already initialized)
            resetForm();

            SUPPORTED_LANGS.forEach(lang => {
                const titleEl = document.getElementById('updateTitle_' + lang);
                if (titleEl) titleEl.value = (translations[lang] && translations[lang].title) || '';
                // Use getQuillEditor (lazy init) so editor is created now that modal is visible
                const q = getQuillEditor(lang);
                if (q) {
                    const rawContent = (translations[lang] && translations[lang].content) || '';
                    if (rawContent) {
                        q.clipboard.dangerouslyPasteHTML(sanitizeHtml(rawContent));
                    } else {
                        q.setContents([]);
                    }
                }
            });
        });
    });

    // Save update
    document.getElementById('btnSaveUpdate')?.addEventListener('click', async function () {
        const updateId = parseInt(document.getElementById('updateEditId').value, 10) || 0;
        const feedbackId = parseInt(document.getElementById('updateEditFeedbackId').value, 10) || 0;
        const isPublished = document.getElementById('updateIsPublished').checked ? 1 : 0;

        // Build translations object using Quill editor content
        const translations = {};
        SUPPORTED_LANGS.forEach(lang => {
            const title = (document.getElementById('updateTitle_' + lang)?.value || '').trim();
            const q = quillEditors[lang];
            const content = q ? sanitizeHtml(q.root.innerHTML).trim() : '';
            // Quill empty state produces QUILL_EMPTY_HTML — treat as empty
            const isEmpty = !content || content === QUILL_EMPTY_HTML;
            if (title || !isEmpty) {
                translations[lang] = { title, content: isEmpty ? '' : content };
            }
        });

        const formData = new FormData();
        formData.append('csrf_token', CSRF_TOKEN);
        formData.append('update_id', updateId);
        formData.append('feedback_id', feedbackId);
        formData.append('is_published', isPublished);
        formData.append('translations', JSON.stringify(translations));

        const endpoint = updateId > 0
            ? BASE_URL + '/app/api/changelog_update.php'
            : BASE_URL + '/app/api/changelog_create.php';

        try {
            const response = await fetch(endpoint, {
                method: 'POST',
                headers: { 'X-CSRF-Token': CSRF_TOKEN },
                body: formData
            });
            const data = await response.json();
            if (data.ok) {
                if (typeof window.sfToast === 'function') window.sfToast('success', i18n.saved);
                closeModal('modalUpdateEdit');
                setTimeout(() => window.location.reload(), 400);
            } else {
                if (typeof window.sfToast === 'function') window.sfToast('danger', data.error || i18n.errorSave);
                else alert(data.error || i18n.errorSave);
            }
        } catch (e) {
            console.error(e);
            if (typeof window.sfToast === 'function') window.sfToast('danger', i18n.errorSave);
            else alert(i18n.errorSave);
        }
    });

    // Delete update
    document.querySelectorAll('.btn-delete-update').forEach(btn => {
        btn.addEventListener('click', async function () {
            if (!confirm(i18n.deleteConfirm)) return;
            const id = this.dataset.id;
            const formData = new FormData();
            formData.append('csrf_token', CSRF_TOKEN);
            formData.append('update_id', id);
            try {
                const response = await fetch(BASE_URL + '/app/api/changelog_delete.php', {
                    method: 'POST',
                    headers: { 'X-CSRF-Token': CSRF_TOKEN },
                    body: formData
                });
                const data = await response.json();
                if (data.ok) {
                    if (typeof window.sfToast === 'function') window.sfToast('success', i18n.deleted);
                    const row = document.getElementById('update-row-' + id);
                    if (row) row.remove();
                } else {
                    if (typeof window.sfToast === 'function') window.sfToast('danger', data.error || i18n.errorDelete);
                    else alert(data.error || i18n.errorDelete);
                }
            } catch (e) {
                console.error(e);
                if (typeof window.sfToast === 'function') window.sfToast('danger', i18n.errorDelete);
                else alert(i18n.errorDelete);
            }
        });
    });
})();
</script>
