<?php
/**
 * assets/partials/body_map_modal.php
 * Kehokarttamodaali — loukkaantuneiden ruumiinosien valinta (Ensitiedote)
 *
 * Variables available from form.php: $base, $uiLang
 */

/**
 * Reads a body-part SVG file and returns a <g> element that positions and
 * scales the part's content within the combined figure SVG.
 *
 * Each individual body-part SVG has its own bounding-box viewBox (0 0 w h).
 * A transform="translate(x,y) scale(sx,sy)" maps from that local coordinate
 * space into the combined SVG's coordinate space (viewBox 0 0 300 700).
 *
 * @param string $id       Element id, e.g. "bp-head"
 * @param string $svgFile  Absolute path to the .svg file
 * @param float  $x        Left edge in the combined figure's coordinate space
 * @param float  $y        Top edge in the combined figure's coordinate space
 * @param float  $w        Desired width  in the combined figure's coordinate space
 * @param float  $h        Desired height in the combined figure's coordinate space
 */
function buildBodyPartSvg(string $id, string $svgFile, float $x, float $y, float $w, float $h): string
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

    // Extract viewBox dimensions from the file's own coordinate space
    if (!preg_match('/viewBox="([^"]+)"/', $raw, $vm)) {
        return '';
    }
    $vbParts = preg_split('/[\s,]+/', trim($vm[1]));
    if (count($vbParts) !== 4) {
        return '';
    }
    $vbW = (float) $vbParts[2];
    $vbH = (float) $vbParts[3];
    if ($vbW <= 0.0 || $vbH <= 0.0 || $w <= 0.0 || $h <= 0.0) {
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

    // Scale factors: map from the SVG's own viewBox to the desired display size.
    // All divisors are validated non-zero above.
    $scaleX = round($w / $vbW, 6);
    $scaleY = round($h / $vbH, 6);
    // Cast coordinates to float and verify they are finite before embedding
    $tx = (float) $x;
    $ty = (float) $y;
    if (!is_finite($tx) || !is_finite($ty) || !is_finite($scaleX) || !is_finite($scaleY)) {
        return '';
    }
    $transform = 'translate(' . $tx . ',' . $ty . ') scale(' . $scaleX . ',' . $scaleY . ')';
    $eTransform = htmlspecialchars($transform, ENT_QUOTES, 'UTF-8');

    return '<g id="' . $eId . '" class="sf-bp" transform="' . $eTransform . '">'
         . $inner . '</g>';
}

// Directory containing the individual body-part SVG files
$bpDir = __DIR__ . '/../../assets/img/body-map/';

// -----------------------------------------------------------------------
// Layout: position of each body part in the combined SVG (viewBox 0 0 300 700).
// Each entry: [id, x, y, w, h] — top-left position and display size in the
// 300 × 700 coordinate space. Since each body-part SVG has a bounding-box
// viewBox, a translate+scale transform maps it to the correct position.
// -----------------------------------------------------------------------
$frontLayout = [
    // Head & face
    ['bp-head',            111, 10,  78, 101],
    ['bp-eyes',            123, 66,  54,  13],
    ['bp-ear',             117, 40,  67,  22],
    ['bp-neck',            129,111,  42,  21],
    // Torso
    ['bp-shoulder-left',    64,132,  34,  60],
    ['bp-shoulder-right',  202,132,  34,  60],
    ['bp-chest',            98,132, 104,  94],
    ['bp-abdomen',         103,226,  95,  65],
    ['bp-pelvis',           97,291, 106,  56],
    // Arms & hands
    ['bp-arm-left',         57,192,  49, 136],
    ['bp-arm-right',       194,192,  49, 136],
    ['bp-hand-left',        60,328,  43,  61],
    ['bp-hand-right',      198,328,  43,  61],
    // Legs & feet
    ['bp-thigh-left',       95,347,  53, 120],
    ['bp-thigh-right',     152,347,  53, 120],
    ['bp-knee-left',       104,467,  35,  35],
    ['bp-knee-right',      161,467,  35,  35],
    ['bp-calf-left',       104,502,  36, 104],
    ['bp-calf-right',      161,502,  36, 104],
    ['bp-ankle-left',      113,606,  18,  21],
    ['bp-ankle-right',     170,606,  18,  21],
    ['bp-foot-left',       101,627,  42,  43],
    ['bp-foot-right',      158,627,  42,  43],
];

$backLayout = [
    // Head & neck
    ['bp-head-back',           123, 10,  54,  65],
    ['bp-ear-back',            117, 40,  67,  22],
    ['bp-neck-back',           123, 75,  54,  20],
    // Torso
    ['bp-shoulder-left-back',   64, 95,  34,  60],
    ['bp-shoulder-right-back', 201, 95,  34,  60],
    ['bp-upper-back',           98, 95, 103,  97],
    ['bp-lower-back',          103,192,  95,  65],
    ['bp-pelvis-back',          97,257, 106,  56],
    // Arms & hands
    ['bp-arm-left-back',        57,155,  49, 136],
    ['bp-arm-right-back',      194,155,  49, 136],
    ['bp-hand-left-back',       60,291,  43,  61],
    ['bp-hand-right-back',     198,291,  43,  61],
    // Legs & feet
    ['bp-thigh-left-back',      95,313,  53, 120],
    ['bp-thigh-right-back',    152,313,  53, 120],
    ['bp-knee-left-back',      104,433,  35,  35],
    ['bp-knee-right-back',     161,433,  35,  35],
    ['bp-calf-left-back',      104,468,  36, 104],
    ['bp-calf-right-back',     161,468,  36, 104],
    ['bp-ankle-left-back',     113,572,  18,  21],
    ['bp-ankle-right-back',    170,572,  18,  21],
    ['bp-foot-left-back',      108,593,  27,  20],
    ['bp-foot-right-back',     165,593,  27,  20],
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
                        <svg class="sf-body-svg" viewBox="0 0 300 700" xmlns="http://www.w3.org/2000/svg"
                             aria-label="<?= htmlspecialchars(sf_term('body_map_front_label', $uiLang), ENT_QUOTES, 'UTF-8') ?>" role="img">
<?php foreach ($frontLayout as [$bpId, $bpX, $bpY, $bpW, $bpH]):
    echo buildBodyPartSvg($bpId, $bpDir . $bpId . '.svg', $bpX, $bpY, $bpW, $bpH) . "\n";
endforeach; ?>

                        </svg>
                    </figure>

                    <!-- Takapuoli -->
                    <figure class="sf-body-figure">
                        <figcaption><?= htmlspecialchars(sf_term('body_map_back_label', $uiLang), ENT_QUOTES, 'UTF-8') ?></figcaption>
                        <svg class="sf-body-svg sf-body-svg-back" viewBox="0 0 300 700"
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


