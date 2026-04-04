/**
 * assets/js/body-map.js
 * Loukkaantuneiden ruumiinosien valinta — SVG ↔ dropdown-synkronointi
 */
(function () {
    'use strict';

    // Nimikartta: svg_id -> näytettävä nimi (fallback, jos käännetty versio puuttuu)
    var PART_LABELS_FALLBACK = {
        'bp-head':           'Pää',
        'bp-eyes':           'Silmä / Silmät',
        'bp-ear':            'Korva / Kuulo',
        'bp-neck':           'Kaula / Niska',
        'bp-chest':          'Rintakehä',
        'bp-abdomen':        'Vatsa',
        'bp-pelvis':         'Lantioseutu',
        'bp-upper-back':     'Yläselkä',
        'bp-lower-back':     'Alaselkä',
        'bp-shoulder-left':  'Vasen olkapää',
        'bp-shoulder-right': 'Oikea olkapää',
        'bp-arm-left':       'Vasen käsivarsi',
        'bp-arm-right':      'Oikea käsivarsi',
        'bp-hand-left':      'Vasen kämmen',
        'bp-hand-right':     'Oikea kämmen',
        'bp-thigh-left':     'Vasen reisi',
        'bp-thigh-right':    'Oikea reisi',
        'bp-knee-left':      'Vasen polvi',
        'bp-knee-right':     'Oikea polvi',
        'bp-calf-left':      'Vasen pohje',
        'bp-calf-right':     'Oikea pohje',
        'bp-ankle-left':     'Vasen nilkka',
        'bp-ankle-right':    'Oikea nilkka',
        'bp-foot-left':      'Vasen jalkaterä',
        'bp-foot-right':     'Oikea jalkaterä',
    };

    // Käytä PHP:ltä annettuja käännettyjä termejä, tai fallback suomeen
    var PART_LABELS = (typeof window.BODY_MAP_LABELS === 'object' && window.BODY_MAP_LABELS)
        ? window.BODY_MAP_LABELS
        : PART_LABELS_FALLBACK;

    // i18n-merkkijonot laskurille ja poistonapille
    var I18N = {
        countSingle: '1 ruumiinosa valittu',
        countPlural:  '{n} ruumiinosaa valittu',
        removeLabel:  'Poista',
    };
    if (typeof window.BODY_MAP_I18N === 'object' && window.BODY_MAP_I18N) {
        if (window.BODY_MAP_I18N.countSingle) { I18N.countSingle = window.BODY_MAP_I18N.countSingle; }
        if (window.BODY_MAP_I18N.countPlural)  { I18N.countPlural  = window.BODY_MAP_I18N.countPlural; }
        if (window.BODY_MAP_I18N.removeLabel)  { I18N.removeLabel  = window.BODY_MAP_I18N.removeLabel; }
    }

    // Käytössä olevat valinnat (svg_id-joukko)
    const selected = new Set();

    // DOM-viitteet (alustetaan myöhemmin)
    let modal, select, saveBtn, countEl, tagsContainer, hiddenSelect;

    function init() {
        modal          = document.getElementById('sfBodyMapModal');
        select         = document.getElementById('sfBodyPartSelect');
        saveBtn        = document.getElementById('sfBodyMapSaveBtn');
        countEl        = document.getElementById('sfBodyMapSelectionCount');
        tagsContainer  = document.getElementById('sfInjuryTags');
        hiddenSelect   = document.getElementById('sfInjuredPartsHidden');

        if (!modal) return;

        // Lataa olemassa olevat valinnat (editointitila)
        loadFromHiddenSelect();

        // Renderöi tagit heti sivun latautuessa (editointitila)
        renderTags();

        // SVG-klikkaukset — etupuoli (id:lliset osat)
        modal.querySelectorAll('.sf-bp[id]').forEach(function (part) {
            part.addEventListener('click', function () {
                togglePart(this.id);
            });
        });

        // SVG-klikkaukset — takapuoli (viittaavat id:llisiin osiin)
        modal.querySelectorAll('.sf-bp-back-ref').forEach(function (part) {
            part.addEventListener('click', function () {
                var ref = this.dataset.bpRef;
                if (ref) togglePart(ref);
            });
        });

        // Dropdown-muutos → SVG-synk
        if (select) {
            select.addEventListener('change', function () {
                syncFromSelect();
            });
        }

        // Tallenna-nappi
        if (saveBtn) {
            saveBtn.addEventListener('click', function () {
                applySelections();
                closeSelf();
            });
        }

        // Avattaessa: päivitä tila
        var openBtn = document.getElementById('sfBodyMapOpenBtn');
        if (openBtn) {
            openBtn.addEventListener('click', function () {
                refreshModalState();
            });
        }
    }

    /** Lataa valinnat piilotetusta selectistä (editointitila) */
    function loadFromHiddenSelect() {
        if (!hiddenSelect) return;
        selected.clear();
        Array.from(hiddenSelect.options).forEach(function (opt) {
            if (opt.selected) selected.add(opt.value);
        });
        refreshAllSvg();
        refreshDropdown();
        updateCount();
    }

    /** Toggle yksittäinen ruumiinosa */
    function togglePart(svgId) {
        if (selected.has(svgId)) {
            selected.delete(svgId);
        } else {
            selected.add(svgId);
        }
        refreshAllSvg();
        refreshDropdown();
        updateCount();
    }

    /** Synkronoi SVG-elementtien .selected-luokka */
    function refreshAllSvg() {
        // Etupuolen id:lliset osat
        modal.querySelectorAll('.sf-bp[id]').forEach(function (el) {
            el.classList.toggle('selected', selected.has(el.id));
        });
        // Takapuolen viittaukset
        modal.querySelectorAll('.sf-bp-back-ref').forEach(function (el) {
            var ref = el.dataset.bpRef;
            if (ref) el.classList.toggle('selected', selected.has(ref));
        });
    }

    /** Synkronoi dropdown valituista osista */
    function refreshDropdown() {
        if (!select) return;
        Array.from(select.options).forEach(function (opt) {
            opt.selected = selected.has(opt.value);
        });
    }

    /** Synkronoi valitut osat dropdownista SVG:hen */
    function syncFromSelect() {
        selected.clear();
        Array.from(select.options).forEach(function (opt) {
            if (opt.selected) selected.add(opt.value);
        });
        refreshAllSvg();
        updateCount();
    }

    /** Päivitä valittujen lukumäärä */
    function updateCount() {
        if (!countEl) return;
        var n = selected.size;
        if (n === 0) {
            countEl.textContent = '';
        } else if (n === 1) {
            countEl.textContent = I18N.countSingle;
        } else {
            countEl.textContent = I18N.countPlural.replace('{n}', n);
        }
    }

    /** Hae näyttönimi svg_id:lle */
    function getLabel(svgId) {
        return PART_LABELS[svgId] || svgId;
    }

    /** Tallenna valinnat piilotettuun selectiin ja renderöi tagit */
    function applySelections() {
        updateHiddenSelect();
        renderTags();
    }

    /** Kirjoita valinnat piilotettuun selectiin */
    function updateHiddenSelect() {
        if (!hiddenSelect) return;
        // Tyhjennä ensin
        hiddenSelect.innerHTML = '';
        selected.forEach(function (svgId) {
            var opt = document.createElement('option');
            opt.value = svgId;
            opt.textContent = getLabel(svgId);
            opt.selected = true;
            hiddenSelect.appendChild(opt);
        });
    }

    /** Renderöi tagit Step 3:n alla */
    function renderTags() {
        if (!tagsContainer) return;
        tagsContainer.innerHTML = '';

        if (selected.size === 0) return;

        selected.forEach(function (svgId) {
            var tag = document.createElement('span');
            tag.className = 'sf-injury-tag';
            tag.dataset.svgId = svgId;

            var label = document.createTextNode(getLabel(svgId));
            tag.appendChild(label);

            var removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'sf-injury-tag-remove';
            removeBtn.setAttribute('aria-label', I18N.removeLabel + ' ' + getLabel(svgId));
            removeBtn.innerHTML = '\u00D7'; // ×
            removeBtn.addEventListener('click', function () {
                selected.delete(svgId);
                refreshAllSvg();
                refreshDropdown();
                updateHiddenSelect();
                renderTags();
                updateCount();
            });

            tag.appendChild(removeBtn);
            tagsContainer.appendChild(tag);
        });
    }

    /** Päivitä modalin sisäinen tila ennen avaamista */
    function refreshModalState() {
        loadFromHiddenSelect();
    }

    /** Sulje modaali (hyödyntää globaalia modals.js) */
    function closeSelf() {
        if (modal) modal.classList.add('hidden');
        if (document.querySelectorAll('.sf-modal:not(.hidden), .sf-library-modal:not(.hidden)').length === 0) {
            document.body.classList.remove('sf-modal-open');
        }
    }

    // Alusta kun DOM on valmis
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // Julkinen rajapinta
    window.BodyMap = { init: init, refresh: refreshModalState };
})();
