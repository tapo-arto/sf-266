<?php
/**
 * assets/partials/body_map_modal.php
 * Kehokarttamodaali — loukkaantuneiden ruumiinosien valinta (Ensitiedote)
 */
?>
<div class="sf-modal hidden" id="sfBodyMapModal" role="dialog" aria-modal="true" aria-labelledby="sfBodyMapModalTitle">
    <div class="sf-modal-content sf-body-map-modal-content">

        <div class="sf-modal-header">
            <h2 id="sfBodyMapModalTitle">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                     class="sf-modal-icon" aria-hidden="true">
                    <path d="M12 2a3 3 0 1 0 0 6 3 3 0 0 0 0-6z"/>
                    <path d="M6.5 8a2 2 0 0 0-2 2v2a6 6 0 0 0 2 4.47V21a1 1 0 0 0 2 0v-3h3v3a1 1 0 0 0 2 0v-4.53A6 6 0 0 0 15.5 12v-2a2 2 0 0 0-2-2h-7z"/>
                </svg>
                Merkitse loukkaantuneet ruumiinosat
            </h2>
            <button type="button" class="sf-modal-close" data-modal-close aria-label="Sulje">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path d="M18 6L6 18M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div class="sf-modal-body sf-body-map-modal-body">

            <!-- Dropdown valinta -->
            <div class="sf-body-map-select-row">
                <label for="sfBodyPartSelect" class="sf-label">Valitse ruumiinosat listasta</label>
                <select id="sfBodyPartSelect" multiple class="sf-body-part-select" size="6">
                    <optgroup label="Pää ja niska">
                        <option value="bp-head">Pää</option>
                        <option value="bp-eyes">Silmä / Silmät</option>
                        <option value="bp-ear">Korva / Kuulo</option>
                        <option value="bp-neck">Kaula / Niska</option>
                    </optgroup>
                    <optgroup label="Keskivartalo">
                        <option value="bp-chest">Rintakehä</option>
                        <option value="bp-abdomen">Vatsa</option>
                        <option value="bp-pelvis">Lantioseutu</option>
                        <option value="bp-upper-back">Yläselkä</option>
                        <option value="bp-lower-back">Alaselkä</option>
                    </optgroup>
                    <optgroup label="Yläraajat">
                        <option value="bp-shoulder-left">Vasen olkapää</option>
                        <option value="bp-shoulder-right">Oikea olkapää</option>
                        <option value="bp-arm-left">Vasen käsivarsi</option>
                        <option value="bp-arm-right">Oikea käsivarsi</option>
                        <option value="bp-hand-left">Vasen kämmen</option>
                        <option value="bp-hand-right">Oikea kämmen</option>
                    </optgroup>
                    <optgroup label="Alaraajat">
                        <option value="bp-thigh-left">Vasen reisi</option>
                        <option value="bp-thigh-right">Oikea reisi</option>
                        <option value="bp-knee-left">Vasen polvi</option>
                        <option value="bp-knee-right">Oikea polvi</option>
                        <option value="bp-calf-left">Vasen pohje</option>
                        <option value="bp-calf-right">Oikea pohje</option>
                        <option value="bp-foot-left">Vasen jalkaterä</option>
                        <option value="bp-foot-right">Oikea jalkaterä</option>
                    </optgroup>
                </select>
                <p class="sf-help-text">Pidä Ctrl/⌘ pohjassa valitaksesi useita.</p>
            </div>

            <!-- SVG kehokuva -->
            <div class="sf-body-map-svg-row">
                <p class="sf-body-map-svg-hint">Tai klikkaa kehokuvasta</p>
                <div class="sf-body-map-figures">

                    <!-- Etupuoli -->
                    <figure class="sf-body-figure">
                        <figcaption>Etupuoli</figcaption>
                        <svg class="sf-body-svg" viewBox="0 0 120 300" xmlns="http://www.w3.org/2000/svg"
                             aria-label="Ihmiskeho etupuoli" role="img">

                            <!-- Pää -->
                            <ellipse id="bp-head" class="sf-bp" cx="60" cy="22" rx="16" ry="18"
                                     data-label="Pää"/>

                            <!-- Kaula / Niska -->
                            <rect id="bp-neck" class="sf-bp" x="53" y="38" width="14" height="12" rx="3"
                                  data-label="Kaula / Niska"/>

                            <!-- Silmä / Silmät (etupuoli, symbolinen piste päässä) -->
                            <g id="bp-eyes" class="sf-bp" data-label="Silmä / Silmät">
                                <circle cx="55" cy="20" r="3"/>
                                <circle cx="65" cy="20" r="3"/>
                            </g>

                            <!-- Korva / Kuulo (symboliset pisteet) -->
                            <g id="bp-ear" class="sf-bp" data-label="Korva / Kuulo">
                                <ellipse cx="44" cy="22" rx="4" ry="5"/>
                                <ellipse cx="76" cy="22" rx="4" ry="5"/>
                            </g>

                            <!-- Vasen olkapää (anatomisesti oikealla SVG:ssä) -->
                            <ellipse id="bp-shoulder-left" class="sf-bp" cx="34" cy="54" rx="11" ry="10"
                                     data-label="Vasen olkapää"/>

                            <!-- Oikea olkapää -->
                            <ellipse id="bp-shoulder-right" class="sf-bp" cx="86" cy="54" rx="11" ry="10"
                                     data-label="Oikea olkapää"/>

                            <!-- Rintakehä -->
                            <rect id="bp-chest" class="sf-bp" x="42" y="50" width="36" height="40" rx="5"
                                  data-label="Rintakehä"/>

                            <!-- Vatsa -->
                            <rect id="bp-abdomen" class="sf-bp" x="44" y="92" width="32" height="28" rx="4"
                                  data-label="Vatsa"/>

                            <!-- Lantioseutu -->
                            <rect id="bp-pelvis" class="sf-bp" x="40" y="122" width="40" height="22" rx="5"
                                  data-label="Lantioseutu"/>

                            <!-- Vasen käsivarsi -->
                            <rect id="bp-arm-left" class="sf-bp" x="18" y="64" width="14" height="60" rx="6"
                                  data-label="Vasen käsivarsi"/>

                            <!-- Oikea käsivarsi -->
                            <rect id="bp-arm-right" class="sf-bp" x="88" y="64" width="14" height="60" rx="6"
                                  data-label="Oikea käsivarsi"/>

                            <!-- Vasen kämmen -->
                            <rect id="bp-hand-left" class="sf-bp" x="16" y="126" width="16" height="20" rx="5"
                                  data-label="Vasen kämmen"/>

                            <!-- Oikea kämmen -->
                            <rect id="bp-hand-right" class="sf-bp" x="88" y="126" width="16" height="20" rx="5"
                                  data-label="Oikea kämmen"/>

                            <!-- Vasen reisi -->
                            <rect id="bp-thigh-left" class="sf-bp" x="42" y="146" width="16" height="50" rx="6"
                                  data-label="Vasen reisi"/>

                            <!-- Oikea reisi -->
                            <rect id="bp-thigh-right" class="sf-bp" x="62" y="146" width="16" height="50" rx="6"
                                  data-label="Oikea reisi"/>

                            <!-- Vasen polvi -->
                            <ellipse id="bp-knee-left" class="sf-bp" cx="50" cy="204" rx="10" ry="10"
                                     data-label="Vasen polvi"/>

                            <!-- Oikea polvi -->
                            <ellipse id="bp-knee-right" class="sf-bp" cx="70" cy="204" rx="10" ry="10"
                                     data-label="Oikea polvi"/>

                            <!-- Vasen pohje -->
                            <rect id="bp-calf-left" class="sf-bp" x="42" y="216" width="14" height="48" rx="6"
                                  data-label="Vasen pohje"/>

                            <!-- Oikea pohje -->
                            <rect id="bp-calf-right" class="sf-bp" x="64" y="216" width="14" height="48" rx="6"
                                  data-label="Oikea pohje"/>

                            <!-- Vasen jalkaterä -->
                            <rect id="bp-foot-left" class="sf-bp" x="38" y="265" width="20" height="14" rx="4"
                                  data-label="Vasen jalkaterä"/>

                            <!-- Oikea jalkaterä -->
                            <rect id="bp-foot-right" class="sf-bp" x="62" y="265" width="20" height="14" rx="4"
                                  data-label="Oikea jalkaterä"/>

                        </svg>
                    </figure>

                    <!-- Takapuoli -->
                    <figure class="sf-body-figure">
                        <figcaption>Takapuoli</figcaption>
                        <svg class="sf-body-svg sf-body-svg-back" viewBox="0 0 120 300"
                             xmlns="http://www.w3.org/2000/svg"
                             aria-label="Ihmiskeho takapuoli" role="img">

                            <!-- Pää (takapuoli — linkittyy samaan bp-head:iin) -->
                            <ellipse class="sf-bp sf-bp-back-ref" data-bp-ref="bp-head"
                                     cx="60" cy="22" rx="16" ry="18" data-label="Pää"/>

                            <!-- Kaula (takapuoli ref) -->
                            <rect class="sf-bp sf-bp-back-ref" data-bp-ref="bp-neck"
                                  x="53" y="38" width="14" height="12" rx="3" data-label="Kaula / Niska"/>

                            <!-- Yläselkä -->
                            <rect id="bp-upper-back" class="sf-bp" x="42" y="50" width="36" height="36" rx="5"
                                  data-label="Yläselkä"/>

                            <!-- Alaselkä -->
                            <rect id="bp-lower-back" class="sf-bp" x="42" y="88" width="36" height="30" rx="5"
                                  data-label="Alaselkä"/>

                            <!-- Lantio takapuoli ref -->
                            <rect class="sf-bp sf-bp-back-ref" data-bp-ref="bp-pelvis"
                                  x="40" y="120" width="40" height="22" rx="5" data-label="Lantioseutu"/>

                            <!-- Vasen käsivarsi (takapuoli ref) -->
                            <rect class="sf-bp sf-bp-back-ref" data-bp-ref="bp-arm-left"
                                  x="18" y="64" width="14" height="60" rx="6" data-label="Vasen käsivarsi"/>

                            <!-- Oikea käsivarsi (takapuoli ref) -->
                            <rect class="sf-bp sf-bp-back-ref" data-bp-ref="bp-arm-right"
                                  x="88" y="64" width="14" height="60" rx="6" data-label="Oikea käsivarsi"/>

                            <!-- Vasen kämmen (takapuoli ref) -->
                            <rect class="sf-bp sf-bp-back-ref" data-bp-ref="bp-hand-left"
                                  x="16" y="126" width="16" height="20" rx="5" data-label="Vasen kämmen"/>

                            <!-- Oikea kämmen (takapuoli ref) -->
                            <rect class="sf-bp sf-bp-back-ref" data-bp-ref="bp-hand-right"
                                  x="88" y="126" width="16" height="20" rx="5" data-label="Oikea kämmen"/>

                            <!-- Vasen reisi (takapuoli ref) -->
                            <rect class="sf-bp sf-bp-back-ref" data-bp-ref="bp-thigh-left"
                                  x="42" y="146" width="16" height="50" rx="6" data-label="Vasen reisi"/>

                            <!-- Oikea reisi (takapuoli ref) -->
                            <rect class="sf-bp sf-bp-back-ref" data-bp-ref="bp-thigh-right"
                                  x="62" y="146" width="16" height="50" rx="6" data-label="Oikea reisi"/>

                            <!-- Vasen polvi (takapuoli ref) -->
                            <ellipse class="sf-bp sf-bp-back-ref" data-bp-ref="bp-knee-left"
                                     cx="50" cy="204" rx="10" ry="10" data-label="Vasen polvi"/>

                            <!-- Oikea polvi (takapuoli ref) -->
                            <ellipse class="sf-bp sf-bp-back-ref" data-bp-ref="bp-knee-right"
                                     cx="70" cy="204" rx="10" ry="10" data-label="Oikea polvi"/>

                            <!-- Vasen pohje (takapuoli ref) -->
                            <rect class="sf-bp sf-bp-back-ref" data-bp-ref="bp-calf-left"
                                  x="42" y="216" width="14" height="48" rx="6" data-label="Vasen pohje"/>

                            <!-- Oikea pohje (takapuoli ref) -->
                            <rect class="sf-bp sf-bp-back-ref" data-bp-ref="bp-calf-right"
                                  x="64" y="216" width="14" height="48" rx="6" data-label="Oikea pohje"/>

                            <!-- Vasen jalkaterä (takapuoli ref) -->
                            <rect class="sf-bp sf-bp-back-ref" data-bp-ref="bp-foot-left"
                                  x="38" y="265" width="20" height="14" rx="4" data-label="Vasen jalkaterä"/>

                            <!-- Oikea jalkaterä (takapuoli ref) -->
                            <rect class="sf-bp sf-bp-back-ref" data-bp-ref="bp-foot-right"
                                  x="62" y="265" width="20" height="14" rx="4" data-label="Oikea jalkaterä"/>

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
                    Peruuta
                </button>
                <button type="button" class="sf-btn sf-btn-primary" id="sfBodyMapSaveBtn">
                    Tallenna valinnat
                </button>
            </div>
        </div>

    </div><!-- /.sf-modal-content -->
</div><!-- /#sfBodyMapModal -->
