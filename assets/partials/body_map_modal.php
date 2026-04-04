<?php
/**
 * assets/partials/body_map_modal.php
 * Kehokarttamodaali — loukkaantuneiden ruumiinosien valinta (Ensitiedote)
 *
 * Variables available from form.php: $base, $uiLang
 */

/**
 * Reads a body-part SVG file and returns a <g> element wrapping the part's
 * content. The new SVG files are pre-positioned on a shared 595.28×841.89
 * canvas, so no transform is needed — each file's paths already carry the
 * correct absolute coordinates.
 *
 * @param string $id       Element id, e.g. "bp-head"
 * @param string $svgFile  Absolute path to the .svg file
 */
function buildBodyPartSvg(string $id, string $svgFile): string
{
    // Validate that the resolved path stays within the body-map asset directory
    $expectedDir = realpath(__DIR__ . '/../../assets/img/body-map');
    $realFile    = realpath($svgFile);
    if (
        $realFile === false || $expectedDir === false
        || strncmp($realFile, $expectedDir . DIRECTORY_SEPARATOR, strlen($expectedDir) + 1) !== 0
    ) {
        return '';
    }

    $raw = file_get_contents($realFile);
    if ($raw === false || $raw === '') {
        return '';
    }

    // Extract everything between the root <svg> tags
    if (!preg_match('/<svg[^>]*>(.*?)<\/svg>/s', $raw, $cm)) {
        return '';
    }
    $inner = trim($cm[1]);

    // Remove any residual XML/DOCTYPE declarations
    $inner = preg_replace('/<\?xml[^>]*\?>\s*/', '', $inner);

    // Strip potentially dangerous SVG elements and attributes
    $inner = preg_replace('/<script[^>]*>.*?<\/script>/si', '', $inner);
    $inner = preg_replace('/\s+on\w+="[^"]*"/i', '', $inner);
    $inner = preg_replace('/\s+on\w+=\'[^\']*\'/i', '', $inner);
    $inner = preg_replace('/href\s*=\s*["\']?\s*javascript:[^"\'>\s]*/i', '', $inner);

    // Remove hardcoded fill/stroke so CSS (.sf-bp) can control appearance
    $inner = preg_replace('/\s+fill="[^"]*"/', '', $inner);
    $inner = preg_replace('/\s+stroke="[^"]*"/', '', $inner);
    $inner = preg_replace('/\s+stroke-width="[^"]*"/', '', $inner);

    // Remove id attributes from inner elements to avoid duplicate IDs in the page
    $inner = preg_replace('/\s+id="[^"]*"/', '', $inner);

    $eId = htmlspecialchars($id, ENT_QUOTES, 'UTF-8');

    return '<g id="' . $eId . '" class="sf-bp">' . $inner . '</g>';
}

// Directory containing the individual body-part SVG files
$bpDir = __DIR__ . '/../../assets/img/body-map/';

// -----------------------------------------------------------------------
// Layout: each entry maps a medical/JS element ID to the SVG file to load.
// The new SVG files share a common 595.28×841.89 canvas so no positioning
// transform is needed — the paths carry the correct absolute coordinates.
//
// Front side: ID and file name match directly.
// Back side:  left/right limb pairs are cross-wired because the user named
//             the files by their visual position on screen (mirrored view),
//             while the IDs follow the medical convention (patient's own
//             left/right). Loading the opposite file corrects the mirror.
// -----------------------------------------------------------------------
$frontLayout = [
    // Head & face — bp-head_1.svg is the face-forward head; bp-head.svg is unused
    ['id' => 'bp-head',           'file' => 'bp-head_1.svg'],
    ['id' => 'bp-eyes',           'file' => 'bp-eyes.svg'],
    ['id' => 'bp-ear',            'file' => 'bp-ear.svg'],
    ['id' => 'bp-neck',           'file' => 'bp-neck.svg'],
    // Torso
    ['id' => 'bp-shoulder-left',  'file' => 'bp-shoulder-left.svg'],
    ['id' => 'bp-shoulder-right', 'file' => 'bp-shoulder-right.svg'],
    ['id' => 'bp-chest',          'file' => 'bp-chest.svg'],
    ['id' => 'bp-abdomen',        'file' => 'bp-abdomen.svg'],
    ['id' => 'bp-pelvis',         'file' => 'bp-pelvis.svg'],
    // Arms & hands
    ['id' => 'bp-arm-left',       'file' => 'bp-arm-left.svg'],
    ['id' => 'bp-arm-right',      'file' => 'bp-arm-right.svg'],
    ['id' => 'bp-hand-left',      'file' => 'bp-hand-left.svg'],
    ['id' => 'bp-hand-right',     'file' => 'bp-hand-right.svg'],
    // Legs & feet
    ['id' => 'bp-thigh-left',     'file' => 'bp-thigh-left.svg'],
    ['id' => 'bp-thigh-right',    'file' => 'bp-thigh-right.svg'],
    ['id' => 'bp-knee-left',      'file' => 'bp-knee-left.svg'],
    ['id' => 'bp-knee-right',     'file' => 'bp-knee-right.svg'],
    ['id' => 'bp-calf-left',      'file' => 'bp-calf-left.svg'],
    ['id' => 'bp-calf-right',     'file' => 'bp-calf-right.svg'],
    ['id' => 'bp-ankle-left',     'file' => 'bp-ankle-left.svg'],
    ['id' => 'bp-ankle-right',    'file' => 'bp-ankle-right.svg'],
    ['id' => 'bp-foot-left',      'file' => 'bp-foot-left.svg'],
    ['id' => 'bp-foot-right',     'file' => 'bp-foot-right.svg'],
];

$backLayout = [
    // Head & neck (centre parts: ID and file match)
    ['id' => 'bp-head-back',           'file' => 'bp-head-back.svg'],
    ['id' => 'bp-ear-back',            'file' => 'bp-ear-back.svg'],
    ['id' => 'bp-neck-back',           'file' => 'bp-neck-back.svg'],
    // Torso centre parts (ID and file match)
    ['id' => 'bp-upper-back',          'file' => 'bp-upper-back.svg'],
    ['id' => 'bp-lower-back',          'file' => 'bp-lower-back.svg'],
    ['id' => 'bp-pelvis-back',         'file' => 'bp-pelvis-back.svg'],
    // Shoulders — cross-wired: medical left → visual right file, and vice versa
    ['id' => 'bp-shoulder-left-back',  'file' => 'bp-shoulder-right-back.svg'],
    ['id' => 'bp-shoulder-right-back', 'file' => 'bp-shoulder-left-back.svg'],
    // Arms — cross-wired
    ['id' => 'bp-arm-left-back',       'file' => 'bp-arm-right-back.svg'],
    ['id' => 'bp-arm-right-back',      'file' => 'bp-arm-left-back.svg'],
    // Hands — cross-wired
    ['id' => 'bp-hand-left-back',      'file' => 'bp-hand-right-back.svg'],
    ['id' => 'bp-hand-right-back',     'file' => 'bp-hand-left-back.svg'],
    // Thighs — cross-wired
    ['id' => 'bp-thigh-left-back',     'file' => 'bp-thigh-right-back.svg'],
    ['id' => 'bp-thigh-right-back',    'file' => 'bp-thigh-left-back.svg'],
    // Knees — cross-wired
    ['id' => 'bp-knee-left-back',      'file' => 'bp-knee-right-back.svg'],
    ['id' => 'bp-knee-right-back',     'file' => 'bp-knee-left-back.svg'],
    // Calves — cross-wired
    ['id' => 'bp-calf-left-back',      'file' => 'bp-calf-right-back.svg'],
    ['id' => 'bp-calf-right-back',     'file' => 'bp-calf-left-back.svg'],
    // Ankles — cross-wired
    ['id' => 'bp-ankle-left-back',     'file' => 'bp-ankle-right-back.svg'],
    ['id' => 'bp-ankle-right-back',    'file' => 'bp-ankle-left-back.svg'],
    // Feet — cross-wired
    ['id' => 'bp-foot-left-back',      'file' => 'bp-foot-right-back.svg'],
    ['id' => 'bp-foot-right-back',     'file' => 'bp-foot-left-back.svg'],
];
?>
<div class="sf-modal hidden" id="sfBodyMapModal" role="dialog" aria-modal="true" aria-labelledby="sfBodyMapModalTitle">
    <div class="sf-modal-content sf-body-map-modal-content">

        <div class="sf-modal-header">
            <h2 id="sfBodyMapModalTitle">
                <img src="<?= htmlspecialchars($base, ENT_QUOTES, 'UTF-8') ?>/assets/img/icons/injury_icon.svg"
                     width="22" height="22" alt="" aria-hidden="true" class="sf-modal-icon sf-modal-icon-img">
                <?= htmlspecialchars(sf_term('body_map_modal_title', $uiLang), ENT_QUOTES, 'UTF-8') ?>
            </h2>
            <button type="button" class="sf-modal-close" data-modal-close
                    aria-label="<?= htmlspecialchars(sf_term('body_map_close_label', $uiLang), ENT_QUOTES, 'UTF-8') ?>">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path d="M18 6L6 18M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div class="sf-modal-body sf-body-map-modal-body">

            <!-- Dropdown valinta -->
            <div class="sf-body-map-select-row">
                <label for="sfBodyPartSelect" class="sf-label">
                    <?= htmlspecialchars(sf_term('body_map_select_label', $uiLang), ENT_QUOTES, 'UTF-8') ?>
                </label>
                <select id="sfBodyPartSelect" multiple class="sf-body-part-select" size="8">
                    <optgroup label="<?= htmlspecialchars(sf_term('bp_cat_head_neck', $uiLang), ENT_QUOTES, 'UTF-8') ?>">
                        <option value="bp-head"><?= htmlspecialchars(sf_term('bp_head', $uiLang), ENT_QUOTES, 'UTF-8') ?></option>
                        <option value="bp-eyes"><?= htmlspecialchars(sf_term('bp_eyes', $uiLang), ENT_QUOTES, 'UTF-8') ?></option>
                        <option value="bp-ear"><?= htmlspecialchars(sf_term('bp_ear',   $uiLang), ENT_QUOTES, 'UTF-8') ?></option>
                        <option value="bp-neck"><?= htmlspecialchars(sf_term('bp_neck', $uiLang), ENT_QUOTES, 'UTF-8') ?></option>
                    </optgroup>
                    <optgroup label="<?= htmlspecialchars(sf_term('bp_cat_torso', $uiLang), ENT_QUOTES, 'UTF-8') ?>">
                        <option value="bp-chest"><?= htmlspecialchars(sf_term('bp_chest',      $uiLang), ENT_QUOTES, 'UTF-8') ?></option>
                        <option value="bp-abdomen"><?= htmlspecialchars(sf_term('bp_abdomen',  $uiLang), ENT_QUOTES, 'UTF-8') ?></option>
                        <option value="bp-pelvis"><?= htmlspecialchars(sf_term('bp_pelvis',    $uiLang), ENT_QUOTES, 'UTF-8') ?></option>
                        <option value="bp-upper-back"><?= htmlspecialchars(sf_term('bp_upper_back', $uiLang), ENT_QUOTES, 'UTF-8') ?></option>
                        <option value="bp-lower-back"><?= htmlspecialchars(sf_term('bp_lower_back', $uiLang), ENT_QUOTES, 'UTF-8') ?></option>
                    </optgroup>
                    <optgroup label="<?= htmlspecialchars(sf_term('bp_cat_upper_limbs', $uiLang), ENT_QUOTES, 'UTF-8') ?>">
                        <option value="bp-shoulder-left"><?= htmlspecialchars(sf_term('bp_shoulder_left',  $uiLang), ENT_QUOTES, 'UTF-8') ?></option>
                        <option value="bp-shoulder-right"><?= htmlspecialchars(sf_term('bp_shoulder_right', $uiLang), ENT_QUOTES, 'UTF-8') ?></option>
                        <option value="bp-arm-left"><?= htmlspecialchars(sf_term('bp_arm_left',   $uiLang), ENT_QUOTES, 'UTF-8') ?></option>
                        <option value="bp-arm-right"><?= htmlspecialchars(sf_term('bp_arm_right', $uiLang), ENT_QUOTES, 'UTF-8') ?></option>
                        <option value="bp-hand-left"><?= htmlspecialchars(sf_term('bp_hand_left', $uiLang), ENT_QUOTES, 'UTF-8') ?></option>
                        <option value="bp-hand-right"><?= htmlspecialchars(sf_term('bp_hand_right', $uiLang), ENT_QUOTES, 'UTF-8') ?></option>
                    </optgroup>
                    <optgroup label="<?= htmlspecialchars(sf_term('bp_cat_lower_limbs', $uiLang), ENT_QUOTES, 'UTF-8') ?>">
                        <option value="bp-thigh-left"><?= htmlspecialchars(sf_term('bp_thigh_left',  $uiLang), ENT_QUOTES, 'UTF-8') ?></option>
                        <option value="bp-thigh-right"><?= htmlspecialchars(sf_term('bp_thigh_right', $uiLang), ENT_QUOTES, 'UTF-8') ?></option>
                        <option value="bp-knee-left"><?= htmlspecialchars(sf_term('bp_knee_left',   $uiLang), ENT_QUOTES, 'UTF-8') ?></option>
                        <option value="bp-knee-right"><?= htmlspecialchars(sf_term('bp_knee_right', $uiLang), ENT_QUOTES, 'UTF-8') ?></option>
                        <option value="bp-calf-left"><?= htmlspecialchars(sf_term('bp_calf_left',   $uiLang), ENT_QUOTES, 'UTF-8') ?></option>
                        <option value="bp-calf-right"><?= htmlspecialchars(sf_term('bp_calf_right', $uiLang), ENT_QUOTES, 'UTF-8') ?></option>
                        <option value="bp-ankle-left"><?= htmlspecialchars(sf_term('bp_ankle_left', $uiLang), ENT_QUOTES, 'UTF-8') ?></option>
                        <option value="bp-ankle-right"><?= htmlspecialchars(sf_term('bp_ankle_right', $uiLang), ENT_QUOTES, 'UTF-8') ?></option>
                        <option value="bp-foot-left"><?= htmlspecialchars(sf_term('bp_foot_left',   $uiLang), ENT_QUOTES, 'UTF-8') ?></option>
                        <option value="bp-foot-right"><?= htmlspecialchars(sf_term('bp_foot_right', $uiLang), ENT_QUOTES, 'UTF-8') ?></option>
                    </optgroup>
                </select>
                <p class="sf-help-text">
                    <?= htmlspecialchars(sf_term('body_map_select_hint', $uiLang), ENT_QUOTES, 'UTF-8') ?>
                </p>
            </div>

            <!-- SVG kehokuva -->
            <div class="sf-body-map-svg-row">
                <p class="sf-body-map-svg-hint">
                    <?= htmlspecialchars(sf_term('body_map_svg_hint', $uiLang), ENT_QUOTES, 'UTF-8') ?>
                </p>
                <div class="sf-body-map-figures">

                    <!-- Etupuoli -->
                    <figure class="sf-body-figure">
                        <figcaption><?= htmlspecialchars(sf_term('body_map_front_label', $uiLang), ENT_QUOTES, 'UTF-8') ?></figcaption>
                         <svg class="sf-body-svg" viewBox="0 0 595.28 841.89" xmlns="http://www.w3.org/2000/svg"
                              aria-label="<?= htmlspecialchars(sf_term('body_map_front_label', $uiLang), ENT_QUOTES, 'UTF-8') ?>" role="img">
<?php foreach ($frontLayout as $bp):
    echo buildBodyPartSvg($bp['id'], $bpDir . $bp['file']) . "\n";
endforeach; ?>

                        </svg>
                    </figure>

                    <!-- Takapuoli -->
                    <figure class="sf-body-figure">
                        <figcaption><?= htmlspecialchars(sf_term('body_map_back_label', $uiLang), ENT_QUOTES, 'UTF-8') ?></figcaption>
                        <svg class="sf-body-svg sf-body-svg-back" viewBox="0 0 595.28 841.89"
                             xmlns="http://www.w3.org/2000/svg"
                             aria-label="<?= htmlspecialchars(sf_term('body_map_back_label', $uiLang), ENT_QUOTES, 'UTF-8') ?>" role="img">
<?php foreach ($backLayout as $bp):
    echo buildBodyPartSvg($bp['id'], $bpDir . $bp['file']) . "\n";
endforeach; ?>
                        </svg>
                    </figure>

                </div><!-- /.sf-body-map-figures -->
            </div><!-- /.sf-body-map-svg-row -->

        </div><!-- /.sf-modal-body -->

        <div class="sf-modal-footer">
            <div class="sf-body-map-selection-summary" id="sfBodyMapSelectionSummary">
                <span id="sfBodyMapSelectionCount" class="sf-body-map-count"></span>
            </div>
            <div class="sf-modal-actions">
                <button type="button" class="sf-btn sf-btn-secondary" data-modal-close>
                    <?= htmlspecialchars(sf_term('body_map_cancel_btn', $uiLang), ENT_QUOTES, 'UTF-8') ?>
                </button>
                <button type="button" class="sf-btn sf-btn-primary" id="sfBodyMapSaveBtn">
                    <?= htmlspecialchars(sf_term('body_map_save_btn', $uiLang), ENT_QUOTES, 'UTF-8') ?>
                </button>
            </div>
        </div>

    </div><!-- /.sf-modal-content -->
</div><!-- /#sfBodyMapModal -->

<script>
window.BODY_MAP_I18N = {
    countSingle: <?= json_encode(sf_term('body_map_count_single', $uiLang), JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) ?>,
    countPlural: <?= json_encode(sf_term('body_map_count_plural', $uiLang), JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) ?>,
    removeLabel: <?= json_encode(sf_term('body_map_remove_label', $uiLang), JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) ?>,
};
</script>


