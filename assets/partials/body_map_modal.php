<?php
/**
 * assets/partials/body_map_modal.php
 * Kehokarttamodaali — loukkaantuneiden ruumiinosien valinta (Ensitiedote)
 *
 * Variables available from form.php: $base, $uiLang
 */

// Build translated body-part label map to pass to JS
$bodyMapLabels = [
    'bp-head'           => sf_term('bp_head',           $uiLang),
    'bp-eyes'           => sf_term('bp_eyes',           $uiLang),
    'bp-ear'            => sf_term('bp_ear',            $uiLang),
    'bp-neck'           => sf_term('bp_neck',           $uiLang),
    'bp-chest'          => sf_term('bp_chest',          $uiLang),
    'bp-abdomen'        => sf_term('bp_abdomen',        $uiLang),
    'bp-pelvis'         => sf_term('bp_pelvis',         $uiLang),
    'bp-upper-back'     => sf_term('bp_upper_back',     $uiLang),
    'bp-lower-back'     => sf_term('bp_lower_back',     $uiLang),
    'bp-shoulder-left'  => sf_term('bp_shoulder_left',  $uiLang),
    'bp-shoulder-right' => sf_term('bp_shoulder_right', $uiLang),
    'bp-arm-left'       => sf_term('bp_arm_left',       $uiLang),
    'bp-arm-right'      => sf_term('bp_arm_right',      $uiLang),
    'bp-hand-left'      => sf_term('bp_hand_left',      $uiLang),
    'bp-hand-right'     => sf_term('bp_hand_right',     $uiLang),
    'bp-thigh-left'     => sf_term('bp_thigh_left',     $uiLang),
    'bp-thigh-right'    => sf_term('bp_thigh_right',    $uiLang),
    'bp-knee-left'      => sf_term('bp_knee_left',      $uiLang),
    'bp-knee-right'     => sf_term('bp_knee_right',     $uiLang),
    'bp-calf-left'      => sf_term('bp_calf_left',      $uiLang),
    'bp-calf-right'     => sf_term('bp_calf_right',     $uiLang),
    'bp-ankle-left'     => sf_term('bp_ankle_left',     $uiLang),
    'bp-ankle-right'    => sf_term('bp_ankle_right',    $uiLang),
    'bp-foot-left'      => sf_term('bp_foot_left',      $uiLang),
    'bp-foot-right'     => sf_term('bp_foot_right',     $uiLang),
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
                        <option value="bp-head"><?= htmlspecialchars(sf_term('bp_head',  $uiLang), ENT_QUOTES, 'UTF-8') ?></option>
                        <option value="bp-eyes"><?= htmlspecialchars(sf_term('bp_eyes',  $uiLang), ENT_QUOTES, 'UTF-8') ?></option>
                        <option value="bp-ear"> <?= htmlspecialchars(sf_term('bp_ear',   $uiLang), ENT_QUOTES, 'UTF-8') ?></option>
                        <option value="bp-neck"><?= htmlspecialchars(sf_term('bp_neck',  $uiLang), ENT_QUOTES, 'UTF-8') ?></option>
                    </optgroup>
                    <optgroup label="<?= htmlspecialchars(sf_term('bp_cat_torso', $uiLang), ENT_QUOTES, 'UTF-8') ?>">
                        <option value="bp-chest">     <?= htmlspecialchars(sf_term('bp_chest',      $uiLang), ENT_QUOTES, 'UTF-8') ?></option>
                        <option value="bp-abdomen">   <?= htmlspecialchars(sf_term('bp_abdomen',    $uiLang), ENT_QUOTES, 'UTF-8') ?></option>
                        <option value="bp-pelvis">    <?= htmlspecialchars(sf_term('bp_pelvis',     $uiLang), ENT_QUOTES, 'UTF-8') ?></option>
                        <option value="bp-upper-back"><?= htmlspecialchars(sf_term('bp_upper_back', $uiLang), ENT_QUOTES, 'UTF-8') ?></option>
                        <option value="bp-lower-back"><?= htmlspecialchars(sf_term('bp_lower_back', $uiLang), ENT_QUOTES, 'UTF-8') ?></option>
                    </optgroup>
                    <optgroup label="<?= htmlspecialchars(sf_term('bp_cat_upper_limbs', $uiLang), ENT_QUOTES, 'UTF-8') ?>">
                        <option value="bp-shoulder-left"> <?= htmlspecialchars(sf_term('bp_shoulder_left',  $uiLang), ENT_QUOTES, 'UTF-8') ?></option>
                        <option value="bp-shoulder-right"><?= htmlspecialchars(sf_term('bp_shoulder_right', $uiLang), ENT_QUOTES, 'UTF-8') ?></option>
                        <option value="bp-arm-left">  <?= htmlspecialchars(sf_term('bp_arm_left',   $uiLang), ENT_QUOTES, 'UTF-8') ?></option>
                        <option value="bp-arm-right"> <?= htmlspecialchars(sf_term('bp_arm_right',  $uiLang), ENT_QUOTES, 'UTF-8') ?></option>
                        <option value="bp-hand-left"> <?= htmlspecialchars(sf_term('bp_hand_left',  $uiLang), ENT_QUOTES, 'UTF-8') ?></option>
                        <option value="bp-hand-right"><?= htmlspecialchars(sf_term('bp_hand_right', $uiLang), ENT_QUOTES, 'UTF-8') ?></option>
                    </optgroup>
                    <optgroup label="<?= htmlspecialchars(sf_term('bp_cat_lower_limbs', $uiLang), ENT_QUOTES, 'UTF-8') ?>">
                        <option value="bp-thigh-left"> <?= htmlspecialchars(sf_term('bp_thigh_left',  $uiLang), ENT_QUOTES, 'UTF-8') ?></option>
                        <option value="bp-thigh-right"><?= htmlspecialchars(sf_term('bp_thigh_right', $uiLang), ENT_QUOTES, 'UTF-8') ?></option>
                        <option value="bp-knee-left">  <?= htmlspecialchars(sf_term('bp_knee_left',   $uiLang), ENT_QUOTES, 'UTF-8') ?></option>
                        <option value="bp-knee-right"> <?= htmlspecialchars(sf_term('bp_knee_right',  $uiLang), ENT_QUOTES, 'UTF-8') ?></option>
                        <option value="bp-calf-left">  <?= htmlspecialchars(sf_term('bp_calf_left',   $uiLang), ENT_QUOTES, 'UTF-8') ?></option>
                        <option value="bp-calf-right"> <?= htmlspecialchars(sf_term('bp_calf_right',  $uiLang), ENT_QUOTES, 'UTF-8') ?></option>
                        <option value="bp-ankle-left"> <?= htmlspecialchars(sf_term('bp_ankle_left',  $uiLang), ENT_QUOTES, 'UTF-8') ?></option>
                        <option value="bp-ankle-right"><?= htmlspecialchars(sf_term('bp_ankle_right', $uiLang), ENT_QUOTES, 'UTF-8') ?></option>
                        <option value="bp-foot-left">  <?= htmlspecialchars(sf_term('bp_foot_left',   $uiLang), ENT_QUOTES, 'UTF-8') ?></option>
                        <option value="bp-foot-right"> <?= htmlspecialchars(sf_term('bp_foot_right',  $uiLang), ENT_QUOTES, 'UTF-8') ?></option>
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

                            <!-- == Decorative silhouette background == -->
                            <g class="sf-body-bg" aria-hidden="true">
                                <!-- Head bg -->
                                <ellipse cx="60" cy="26" rx="20" ry="22"/>
                                <!-- Neck bg -->
                                <path d="M54,47 Q60,51 66,47 L67,59 Q60,63 53,59 Z"/>
                                <!-- Torso bg (chest + abdomen + pelvis) -->
                                <path d="M38,60 C34,62 32,68 32,76 L32,94 C30,100 32,112 36,116
                                         L38,130 C34,136 32,144 34,152 C36,158 40,162 38,166
                                         L82,166 C80,162 84,158 86,152 C88,144 86,136 82,130
                                         L84,116 C88,112 90,100 88,94 L88,76 C88,68 86,62 82,60
                                         Q60,56 38,60 Z"/>
                                <!-- Left arm bg -->
                                <path d="M18,80 C14,86 10,98 10,116 C10,134 14,150 18,158
                                         C22,162 32,162 36,158 C40,150 42,134 42,116
                                         C42,98 40,86 36,80 C32,76 22,76 18,80 Z"/>
                                <!-- Right arm bg -->
                                <path d="M102,80 C106,86 110,98 110,116 C110,134 106,150 102,158
                                         C98,162 88,162 84,158 C80,150 78,134 78,116
                                         C78,98 80,86 84,80 C88,76 98,76 102,80 Z"/>
                                <!-- Left hand bg -->
                                <ellipse cx="24" cy="172" rx="14" ry="12"/>
                                <!-- Right hand bg -->
                                <ellipse cx="96" cy="172" rx="14" ry="12"/>
                                <!-- Left leg bg -->
                                <path d="M32,166 C30,170 28,176 28,186 L28,288 C28,296 34,302 40,302
                                         C48,302 54,296 54,288 L54,186 C54,176 56,170 58,166 Z"/>
                                <!-- Right leg bg -->
                                <path d="M88,166 C90,170 92,176 92,186 L92,288 C92,296 86,302 80,302
                                         C72,302 66,296 66,288 L66,186 C66,176 64,170 62,166 Z"/>
                            </g>
                            <!-- == End silhouette background == -->

                            <!-- Pää -->
                            <ellipse id="bp-head" class="sf-bp" cx="60" cy="26" rx="19" ry="21"
                                     data-label="<?= htmlspecialchars($bodyMapLabels['bp-head'], ENT_QUOTES, 'UTF-8') ?>"/>

                            <!-- Silmä / Silmät -->
                            <g id="bp-eyes" class="sf-bp"
                               data-label="<?= htmlspecialchars($bodyMapLabels['bp-eyes'], ENT_QUOTES, 'UTF-8') ?>">
                                <ellipse cx="52" cy="22" rx="4" ry="3"/>
                                <ellipse cx="68" cy="22" rx="4" ry="3"/>
                            </g>

                            <!-- Korva / Kuulo -->
                            <g id="bp-ear" class="sf-bp"
                               data-label="<?= htmlspecialchars($bodyMapLabels['bp-ear'], ENT_QUOTES, 'UTF-8') ?>">
                                <ellipse cx="39" cy="26" rx="4" ry="6"/>
                                <ellipse cx="81" cy="26" rx="4" ry="6"/>
                            </g>

                            <!-- Kaula / Niska -->
                            <path id="bp-neck" class="sf-bp"
                                  d="M54,47 Q60,51 66,47 L67,59 Q60,63 53,59 Z"
                                  data-label="<?= htmlspecialchars($bodyMapLabels['bp-neck'], ENT_QUOTES, 'UTF-8') ?>"/>

                            <!-- Vasen olkapää -->
                            <path id="bp-shoulder-left" class="sf-bp"
                                  d="M34,60 C28,60 20,64 14,72 C11,76 12,82 16,84 L38,84
                                     C40,80 40,72 38,66 Z"
                                  data-label="<?= htmlspecialchars($bodyMapLabels['bp-shoulder-left'], ENT_QUOTES, 'UTF-8') ?>"/>

                            <!-- Oikea olkapää -->
                            <path id="bp-shoulder-right" class="sf-bp"
                                  d="M86,60 C92,60 100,64 106,72 C109,76 108,82 104,84 L82,84
                                     C80,80 80,72 82,66 Z"
                                  data-label="<?= htmlspecialchars($bodyMapLabels['bp-shoulder-right'], ENT_QUOTES, 'UTF-8') ?>"/>

                            <!-- Rintakehä -->
                            <path id="bp-chest" class="sf-bp"
                                  d="M40,60 Q60,56 80,60 L82,102 Q60,106 38,102 Z"
                                  data-label="<?= htmlspecialchars($bodyMapLabels['bp-chest'], ENT_QUOTES, 'UTF-8') ?>"/>

                            <!-- Vatsa -->
                            <path id="bp-abdomen" class="sf-bp"
                                  d="M38,102 Q60,106 82,102 L80,132 Q60,136 40,132 Z"
                                  data-label="<?= htmlspecialchars($bodyMapLabels['bp-abdomen'], ENT_QUOTES, 'UTF-8') ?>"/>

                            <!-- Lantioseutu -->
                            <path id="bp-pelvis" class="sf-bp"
                                  d="M40,132 Q60,136 80,132 L82,164 Q60,168 38,164 Z"
                                  data-label="<?= htmlspecialchars($bodyMapLabels['bp-pelvis'], ENT_QUOTES, 'UTF-8') ?>"/>

                            <!-- Vasen käsivarsi -->
                            <path id="bp-arm-left" class="sf-bp"
                                  d="M12,86 C10,92 8,104 8,120 C8,138 10,152 12,158
                                     C16,164 28,164 32,160 C36,154 38,140 38,122
                                     C38,104 38,92 36,86 C32,82 16,82 12,86 Z"
                                  data-label="<?= htmlspecialchars($bodyMapLabels['bp-arm-left'], ENT_QUOTES, 'UTF-8') ?>"/>

                            <!-- Oikea käsivarsi -->
                            <path id="bp-arm-right" class="sf-bp"
                                  d="M108,86 C110,92 112,104 112,120 C112,138 110,152 108,158
                                     C104,164 92,164 88,160 C84,154 82,140 82,122
                                     C82,104 82,92 84,86 C88,82 104,82 108,86 Z"
                                  data-label="<?= htmlspecialchars($bodyMapLabels['bp-arm-right'], ENT_QUOTES, 'UTF-8') ?>"/>

                            <!-- Vasen kämmen -->
                            <path id="bp-hand-left" class="sf-bp"
                                  d="M8,160 C6,164 6,176 10,180 C14,184 22,186 28,184
                                     C34,182 38,176 38,170 L36,162 C32,158 24,158 18,160 Z"
                                  data-label="<?= htmlspecialchars($bodyMapLabels['bp-hand-left'], ENT_QUOTES, 'UTF-8') ?>"/>

                            <!-- Oikea kämmen -->
                            <path id="bp-hand-right" class="sf-bp"
                                  d="M112,160 C114,164 114,176 110,180 C106,184 98,186 92,184
                                     C86,182 82,176 82,170 L84,162 C88,158 96,158 102,160 Z"
                                  data-label="<?= htmlspecialchars($bodyMapLabels['bp-hand-right'], ENT_QUOTES, 'UTF-8') ?>"/>

                            <!-- Vasen reisi -->
                            <path id="bp-thigh-left" class="sf-bp"
                                  d="M38,166 C36,170 34,178 34,188 L34,228
                                     C34,234 38,238 44,240 C50,242 56,238 58,234
                                     C60,230 60,222 60,188 C60,178 58,170 56,166 Z"
                                  data-label="<?= htmlspecialchars($bodyMapLabels['bp-thigh-left'], ENT_QUOTES, 'UTF-8') ?>"/>

                            <!-- Oikea reisi -->
                            <path id="bp-thigh-right" class="sf-bp"
                                  d="M82,166 C84,170 86,178 86,188 L86,228
                                     C86,234 82,238 76,240 C70,242 64,238 62,234
                                     C60,230 60,222 60,188 C60,178 62,170 64,166 Z"
                                  data-label="<?= htmlspecialchars($bodyMapLabels['bp-thigh-right'], ENT_QUOTES, 'UTF-8') ?>"/>

                            <!-- Vasen polvi -->
                            <ellipse id="bp-knee-left" class="sf-bp"
                                     cx="47" cy="246" rx="13" ry="10"
                                     data-label="<?= htmlspecialchars($bodyMapLabels['bp-knee-left'], ENT_QUOTES, 'UTF-8') ?>"/>

                            <!-- Oikea polvi -->
                            <ellipse id="bp-knee-right" class="sf-bp"
                                     cx="73" cy="246" rx="13" ry="10"
                                     data-label="<?= htmlspecialchars($bodyMapLabels['bp-knee-right'], ENT_QUOTES, 'UTF-8') ?>"/>

                            <!-- Vasen pohje -->
                            <path id="bp-calf-left" class="sf-bp"
                                  d="M34,256 C32,262 30,272 30,280 C30,286 32,290 34,292 L60,292
                                     C62,288 64,284 64,280 C64,272 62,262 60,256 Z"
                                  data-label="<?= htmlspecialchars($bodyMapLabels['bp-calf-left'], ENT_QUOTES, 'UTF-8') ?>"/>

                            <!-- Oikea pohje -->
                            <path id="bp-calf-right" class="sf-bp"
                                  d="M86,256 C88,262 90,272 90,280 C90,286 88,290 86,292 L60,292
                                     C58,288 56,284 56,280 C56,272 58,262 60,256 Z"
                                  data-label="<?= htmlspecialchars($bodyMapLabels['bp-calf-right'], ENT_QUOTES, 'UTF-8') ?>"/>

                            <!-- Vasen nilkka (uusi) -->
                            <path id="bp-ankle-left" class="sf-bp"
                                  d="M30,293 C28,296 28,303 32,305 L58,305
                                     C62,303 62,296 60,293 Z"
                                  data-label="<?= htmlspecialchars($bodyMapLabels['bp-ankle-left'], ENT_QUOTES, 'UTF-8') ?>"/>

                            <!-- Oikea nilkka (uusi) -->
                            <path id="bp-ankle-right" class="sf-bp"
                                  d="M90,293 C92,296 92,303 88,305 L62,305
                                     C58,303 58,296 60,293 Z"
                                  data-label="<?= htmlspecialchars($bodyMapLabels['bp-ankle-right'], ENT_QUOTES, 'UTF-8') ?>"/>

                            <!-- Vasen jalkaterä -->
                            <path id="bp-foot-left" class="sf-bp"
                                  d="M26,307 Q44,302 60,307 L58,318 Q44,324 28,318 Z"
                                  data-label="<?= htmlspecialchars($bodyMapLabels['bp-foot-left'], ENT_QUOTES, 'UTF-8') ?>"/>

                            <!-- Oikea jalkaterä -->
                            <path id="bp-foot-right" class="sf-bp"
                                  d="M94,307 Q76,302 60,307 L62,318 Q76,324 92,318 Z"
                                  data-label="<?= htmlspecialchars($bodyMapLabels['bp-foot-right'], ENT_QUOTES, 'UTF-8') ?>"/>

                        </svg>
                    </figure>

                    <!-- Takapuoli -->
                    <figure class="sf-body-figure">
                        <figcaption><?= htmlspecialchars(sf_term('body_map_back_label', $uiLang), ENT_QUOTES, 'UTF-8') ?></figcaption>
                        <svg class="sf-body-svg sf-body-svg-back" viewBox="0 0 120 320"
                             xmlns="http://www.w3.org/2000/svg"
                             aria-label="<?= htmlspecialchars(sf_term('body_map_back_label', $uiLang), ENT_QUOTES, 'UTF-8') ?>" role="img">

                            <!-- == Decorative silhouette background (mirrored) == -->
                            <g class="sf-body-bg" aria-hidden="true">
                                <ellipse cx="60" cy="26" rx="20" ry="22"/>
                                <path d="M54,47 Q60,51 66,47 L67,59 Q60,63 53,59 Z"/>
                                <path d="M38,60 C34,62 32,68 32,76 L32,94 C30,100 32,112 36,116
                                         L38,130 C34,136 32,144 34,152 C36,158 40,162 38,166
                                         L82,166 C80,162 84,158 86,152 C88,144 86,136 82,130
                                         L84,116 C88,112 90,100 88,94 L88,76 C88,68 86,62 82,60
                                         Q60,56 38,60 Z"/>
                                <path d="M18,80 C14,86 10,98 10,116 C10,134 14,150 18,158
                                         C22,162 32,162 36,158 C40,150 42,134 42,116
                                         C42,98 40,86 36,80 C32,76 22,76 18,80 Z"/>
                                <path d="M102,80 C106,86 110,98 110,116 C110,134 106,150 102,158
                                         C98,162 88,162 84,158 C80,150 78,134 78,116
                                         C78,98 80,86 84,80 C88,76 98,76 102,80 Z"/>
                                <ellipse cx="24" cy="172" rx="14" ry="12"/>
                                <ellipse cx="96" cy="172" rx="14" ry="12"/>
                                <path d="M32,166 C30,170 28,176 28,186 L28,288 C28,296 34,302 40,302
                                         C48,302 54,296 54,288 L54,186 C54,176 56,170 58,166 Z"/>
                                <path d="M88,166 C90,170 92,176 92,186 L92,288 C92,296 86,302 80,302
                                         C72,302 66,296 66,288 L66,186 C66,176 64,170 62,166 Z"/>
                            </g>
                            <!-- == End silhouette background == -->

                            <!-- Pää (takapuoli — ref to bp-head) -->
                            <ellipse class="sf-bp sf-bp-back-ref" data-bp-ref="bp-head"
                                     cx="60" cy="26" rx="19" ry="21"
                                     data-label="<?= htmlspecialchars($bodyMapLabels['bp-head'], ENT_QUOTES, 'UTF-8') ?>"/>

                            <!-- Kaula (takapuoli ref) -->
                            <path class="sf-bp sf-bp-back-ref" data-bp-ref="bp-neck"
                                  d="M54,47 Q60,51 66,47 L67,59 Q60,63 53,59 Z"
                                  data-label="<?= htmlspecialchars($bodyMapLabels['bp-neck'], ENT_QUOTES, 'UTF-8') ?>"/>

                            <!-- Yläselkä -->
                            <path id="bp-upper-back" class="sf-bp"
                                  d="M40,60 Q60,56 80,60 L82,102 Q60,106 38,102 Z"
                                  data-label="<?= htmlspecialchars($bodyMapLabels['bp-upper-back'], ENT_QUOTES, 'UTF-8') ?>"/>

                            <!-- Alaselkä -->
                            <path id="bp-lower-back" class="sf-bp"
                                  d="M38,102 Q60,106 82,102 L80,132 Q60,136 40,132 Z"
                                  data-label="<?= htmlspecialchars($bodyMapLabels['bp-lower-back'], ENT_QUOTES, 'UTF-8') ?>"/>

                            <!-- Lantio (takapuoli ref) -->
                            <path class="sf-bp sf-bp-back-ref" data-bp-ref="bp-pelvis"
                                  d="M40,132 Q60,136 80,132 L82,164 Q60,168 38,164 Z"
                                  data-label="<?= htmlspecialchars($bodyMapLabels['bp-pelvis'], ENT_QUOTES, 'UTF-8') ?>"/>

                            <!-- Vasen olkapää (takapuoli ref) -->
                            <path class="sf-bp sf-bp-back-ref" data-bp-ref="bp-shoulder-left"
                                  d="M34,60 C28,60 20,64 14,72 C11,76 12,82 16,84 L38,84
                                     C40,80 40,72 38,66 Z"
                                  data-label="<?= htmlspecialchars($bodyMapLabels['bp-shoulder-left'], ENT_QUOTES, 'UTF-8') ?>"/>

                            <!-- Oikea olkapää (takapuoli ref) -->
                            <path class="sf-bp sf-bp-back-ref" data-bp-ref="bp-shoulder-right"
                                  d="M86,60 C92,60 100,64 106,72 C109,76 108,82 104,84 L82,84
                                     C80,80 80,72 82,66 Z"
                                  data-label="<?= htmlspecialchars($bodyMapLabels['bp-shoulder-right'], ENT_QUOTES, 'UTF-8') ?>"/>

                            <!-- Vasen käsivarsi (takapuoli ref) -->
                            <path class="sf-bp sf-bp-back-ref" data-bp-ref="bp-arm-left"
                                  d="M12,86 C10,92 8,104 8,120 C8,138 10,152 12,158
                                     C16,164 28,164 32,160 C36,154 38,140 38,122
                                     C38,104 38,92 36,86 C32,82 16,82 12,86 Z"
                                  data-label="<?= htmlspecialchars($bodyMapLabels['bp-arm-left'], ENT_QUOTES, 'UTF-8') ?>"/>

                            <!-- Oikea käsivarsi (takapuoli ref) -->
                            <path class="sf-bp sf-bp-back-ref" data-bp-ref="bp-arm-right"
                                  d="M108,86 C110,92 112,104 112,120 C112,138 110,152 108,158
                                     C104,164 92,164 88,160 C84,154 82,140 82,122
                                     C82,104 82,92 84,86 C88,82 104,82 108,86 Z"
                                  data-label="<?= htmlspecialchars($bodyMapLabels['bp-arm-right'], ENT_QUOTES, 'UTF-8') ?>"/>

                            <!-- Vasen kämmen (takapuoli ref) -->
                            <path class="sf-bp sf-bp-back-ref" data-bp-ref="bp-hand-left"
                                  d="M8,160 C6,164 6,176 10,180 C14,184 22,186 28,184
                                     C34,182 38,176 38,170 L36,162 C32,158 24,158 18,160 Z"
                                  data-label="<?= htmlspecialchars($bodyMapLabels['bp-hand-left'], ENT_QUOTES, 'UTF-8') ?>"/>

                            <!-- Oikea kämmen (takapuoli ref) -->
                            <path class="sf-bp sf-bp-back-ref" data-bp-ref="bp-hand-right"
                                  d="M112,160 C114,164 114,176 110,180 C106,184 98,186 92,184
                                     C86,182 82,176 82,170 L84,162 C88,158 96,158 102,160 Z"
                                  data-label="<?= htmlspecialchars($bodyMapLabels['bp-hand-right'], ENT_QUOTES, 'UTF-8') ?>"/>

                            <!-- Vasen reisi (takapuoli ref) -->
                            <path class="sf-bp sf-bp-back-ref" data-bp-ref="bp-thigh-left"
                                  d="M38,166 C36,170 34,178 34,188 L34,228
                                     C34,234 38,238 44,240 C50,242 56,238 58,234
                                     C60,230 60,222 60,188 C60,178 58,170 56,166 Z"
                                  data-label="<?= htmlspecialchars($bodyMapLabels['bp-thigh-left'], ENT_QUOTES, 'UTF-8') ?>"/>

                            <!-- Oikea reisi (takapuoli ref) -->
                            <path class="sf-bp sf-bp-back-ref" data-bp-ref="bp-thigh-right"
                                  d="M82,166 C84,170 86,178 86,188 L86,228
                                     C86,234 82,238 76,240 C70,242 64,238 62,234
                                     C60,230 60,222 60,188 C60,178 62,170 64,166 Z"
                                  data-label="<?= htmlspecialchars($bodyMapLabels['bp-thigh-right'], ENT_QUOTES, 'UTF-8') ?>"/>

                            <!-- Vasen polvi (takapuoli ref) -->
                            <ellipse class="sf-bp sf-bp-back-ref" data-bp-ref="bp-knee-left"
                                     cx="47" cy="246" rx="13" ry="10"
                                     data-label="<?= htmlspecialchars($bodyMapLabels['bp-knee-left'], ENT_QUOTES, 'UTF-8') ?>"/>

                            <!-- Oikea polvi (takapuoli ref) -->
                            <ellipse class="sf-bp sf-bp-back-ref" data-bp-ref="bp-knee-right"
                                     cx="73" cy="246" rx="13" ry="10"
                                     data-label="<?= htmlspecialchars($bodyMapLabels['bp-knee-right'], ENT_QUOTES, 'UTF-8') ?>"/>

                            <!-- Vasen pohje (takapuoli ref) -->
                            <path class="sf-bp sf-bp-back-ref" data-bp-ref="bp-calf-left"
                                  d="M34,256 C32,262 30,272 30,280 C30,286 32,290 34,292 L60,292
                                     C62,288 64,284 64,280 C64,272 62,262 60,256 Z"
                                  data-label="<?= htmlspecialchars($bodyMapLabels['bp-calf-left'], ENT_QUOTES, 'UTF-8') ?>"/>

                            <!-- Oikea pohje (takapuoli ref) -->
                            <path class="sf-bp sf-bp-back-ref" data-bp-ref="bp-calf-right"
                                  d="M86,256 C88,262 90,272 90,280 C90,286 88,290 86,292 L60,292
                                     C58,288 56,284 56,280 C56,272 58,262 60,256 Z"
                                  data-label="<?= htmlspecialchars($bodyMapLabels['bp-calf-right'], ENT_QUOTES, 'UTF-8') ?>"/>

                            <!-- Vasen nilkka (takapuoli ref) -->
                            <path class="sf-bp sf-bp-back-ref" data-bp-ref="bp-ankle-left"
                                  d="M30,293 C28,296 28,303 32,305 L58,305
                                     C62,303 62,296 60,293 Z"
                                  data-label="<?= htmlspecialchars($bodyMapLabels['bp-ankle-left'], ENT_QUOTES, 'UTF-8') ?>"/>

                            <!-- Oikea nilkka (takapuoli ref) -->
                            <path class="sf-bp sf-bp-back-ref" data-bp-ref="bp-ankle-right"
                                  d="M90,293 C92,296 92,303 88,305 L62,305
                                     C58,303 58,296 60,293 Z"
                                  data-label="<?= htmlspecialchars($bodyMapLabels['bp-ankle-right'], ENT_QUOTES, 'UTF-8') ?>"/>

                            <!-- Vasen jalkaterä (takapuoli ref) -->
                            <path class="sf-bp sf-bp-back-ref" data-bp-ref="bp-foot-left"
                                  d="M26,307 Q44,302 60,307 L58,318 Q44,324 28,318 Z"
                                  data-label="<?= htmlspecialchars($bodyMapLabels['bp-foot-left'], ENT_QUOTES, 'UTF-8') ?>"/>

                            <!-- Oikea jalkaterä (takapuoli ref) -->
                            <path class="sf-bp sf-bp-back-ref" data-bp-ref="bp-foot-right"
                                  d="M94,307 Q76,302 60,307 L62,318 Q76,324 92,318 Z"
                                  data-label="<?= htmlspecialchars($bodyMapLabels['bp-foot-right'], ENT_QUOTES, 'UTF-8') ?>"/>

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
/* Translated body-part labels for body-map.js (generated server-side) */
window.BODY_MAP_LABELS = <?= json_encode($bodyMapLabels, JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) ?>;
window.BODY_MAP_I18N = {
    countSingle: <?= json_encode(sf_term('body_map_count_single', $uiLang), JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) ?>,
    countPlural: <?= json_encode(sf_term('body_map_count_plural', $uiLang), JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) ?>,
    removeLabel: <?= json_encode(sf_term('body_map_remove_label', $uiLang), JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) ?>,
};
</script>

