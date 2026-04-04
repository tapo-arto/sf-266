<?php
/**
 * assets/partials/body_map_modal.php
 * Kehokarttamodaali — loukkaantuneiden ruumiinosien valinta (Ensitiedote)
 *
 * Variables available from form.php: $base, $uiLang
 */

/**
 * Reads a body-part SVG file and returns an inline <svg> snippet containing
 * a <g id="$id" class="sf-bp"> wrapper with the file's path content.
 * The inner group is positioned within the combined SVG at ($x, $y, $w, $h).
 */
function buildBodyPartSvg(string $id, string $svgFile, int $x, int $y, int $w, int $h): string
{
    if (!file_exists($svgFile)) {
        return '';
    }
    $raw = @file_get_contents($svgFile);
    if ($raw === false || $raw === '') {
        return '';
    }

    // Extract viewBox from the file
    if (!preg_match('/viewBox="([^"]+)"/', $raw, $vm)) {
        return '';
    }
    $viewBox = $vm[1];

    // Extract everything between the root <svg> tags
    if (!preg_match('/<svg[^>]*>(.*?)<\/svg>/s', $raw, $cm)) {
        return '';
    }
    $inner = trim($cm[1]);

    // Remove any residual XML/DOCTYPE declarations
    $inner = preg_replace('/<\?xml[^>]*\?>\s*/', '', $inner);

    // Remove hardcoded fill/stroke so CSS (.sf-bp) can control appearance
    $inner = preg_replace('/\s+fill="[^"]*"/', '', $inner);
    $inner = preg_replace('/\s+stroke="[^"]*"/', '', $inner);
    $inner = preg_replace('/\s+stroke-width="[^"]*"/', '', $inner);

    // Remove id attributes from inner elements to avoid duplicate IDs in the page
    $inner = preg_replace('/\s+id="[^"]*"/', '', $inner);

    $eId      = htmlspecialchars($id,      ENT_QUOTES, 'UTF-8');
    $eViewBox = htmlspecialchars($viewBox, ENT_QUOTES, 'UTF-8');

    return '<svg x="' . $x . '" y="' . $y . '" width="' . $w . '" height="' . $h . '"'
         . ' viewBox="' . $eViewBox . '" overflow="visible">'
         . '<g id="' . $eId . '" class="sf-bp">' . $inner . '</g>'
         . '</svg>';
}

// Directory containing the individual body-part SVG files
$bpDir = __DIR__ . '/../../assets/img/body-map/';

// -----------------------------------------------------------------------
// Layout: position of each body part in the combined SVG (viewBox 0 0 120 320)
// Each entry: [id, x, y, w, h] within the 120 × 320 coordinate space.
// -----------------------------------------------------------------------
$frontLayout = [
    ['bp-head',            21,   2,  38,  50],
    ['bp-eyes',            33,  17,  34,   8],
    ['bp-ear',             19,  14,  42,  14],
    ['bp-neck',            47,  48,  26,  13],
    ['bp-shoulder-left',   -2,  56,  38,  24],
    ['bp-shoulder-right',  84,  56,  38,  24],
    ['bp-chest',           38,  56,  44,  40],
    ['bp-abdomen',         40,  95,  40,  28],
    ['bp-pelvis',          26, 122,  68,  35],
    ['bp-arm-left',         2,  74,  32,  88],
    ['bp-arm-right',       86,  74,  32,  88],
    ['bp-hand-left',        0, 158,  28,  40],
    ['bp-hand-right',      92, 158,  28,  40],
    ['bp-thigh-left',      36, 157,  24,  74],
    ['bp-thigh-right',     60, 157,  24,  74],
    ['bp-knee-left',       36, 231,  22,  22],
    ['bp-knee-right',      62, 231,  22,  22],
    ['bp-calf-left',       36, 253,  22,  50],
    ['bp-calf-right',      62, 253,  22,  50],
    ['bp-ankle-left',      36, 303,  12,  11],
    ['bp-ankle-right',     72, 303,  12,  11],
    ['bp-foot-left',       24, 307,  28,  13],
    ['bp-foot-right',      68, 307,  28,  13],
];

$backLayout = [
    ['bp-head-back',            25,   2,  34,  44],
    ['bp-ear-back',             19,  14,  42,  14],
    ['bp-neck-back',            47,  44,  26,  14],
    ['bp-upper-back',           38,  56,  44,  40],
    ['bp-lower-back',           40,  95,  40,  28],
    ['bp-pelvis-back',          26, 122,  68,  35],
    ['bp-shoulder-left-back',   -2,  56,  38,  24],
    ['bp-shoulder-right-back',  84,  56,  38,  24],
    ['bp-arm-left-back',         2,  74,  32,  88],
    ['bp-arm-right-back',       86,  74,  32,  88],
    ['bp-hand-left-back',        0, 158,  28,  40],
    ['bp-hand-right-back',      92, 158,  28,  40],
    ['bp-thigh-left-back',      36, 157,  24,  74],
    ['bp-thigh-right-back',     60, 157,  24,  74],
    ['bp-knee-left-back',       36, 231,  22,  22],
    ['bp-knee-right-back',      62, 231,  22,  22],
    ['bp-calf-left-back',       36, 253,  22,  50],
    ['bp-calf-right-back',      62, 253,  22,  50],
    ['bp-ankle-left-back',      36, 303,  12,  11],
    ['bp-ankle-right-back',     72, 303,  12,  11],
    ['bp-foot-left-back',       25, 307,  18,  13],
    ['bp-foot-right-back',      77, 307,  18,  13],
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
                        <svg class="sf-body-svg" viewBox="0 0 120 320" xmlns="http://www.w3.org/2000/svg"
                             aria-label="<?= htmlspecialchars(sf_term('body_map_front_label', $uiLang), ENT_QUOTES, 'UTF-8') ?>" role="img">
<?php foreach ($frontLayout as [$bpId, $bpX, $bpY, $bpW, $bpH]):
    echo buildBodyPartSvg($bpId, $bpDir . $bpId . '.svg', $bpX, $bpY, $bpW, $bpH) . "\n";
endforeach; ?>

                        </svg>
                    </figure>

                    <!-- Takapuoli -->
                    <figure class="sf-body-figure">
                        <figcaption><?= htmlspecialchars(sf_term('body_map_back_label', $uiLang), ENT_QUOTES, 'UTF-8') ?></figcaption>
                        <svg class="sf-body-svg sf-body-svg-back" viewBox="0 0 120 320"
                             xmlns="http://www.w3.org/2000/svg"
                             aria-label="<?= htmlspecialchars(sf_term('body_map_back_label', $uiLang), ENT_QUOTES, 'UTF-8') ?>" role="img">
<?php foreach ($backLayout as [$bpId, $bpX, $bpY, $bpW, $bpH]):
    echo buildBodyPartSvg($bpId, $bpDir . $bpId . '.svg', $bpX, $bpY, $bpW, $bpH) . "\n";
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


