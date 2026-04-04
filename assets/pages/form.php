<?php if ($isTranslationChild): ?>
    <!-- Translation child mode footer -->
    <div class="sf-step6-footer">
      <?php 
      $showSendToReview = ! $editing 
          || $state_val === 'draft' 
          || $state_val === 'request_info'
          || $state_val === '';  
      $actionUrl = $base . '/app/api/save_flash.php';
      ?>
      <?php if (! ($editing && ! $showSendToReview) && $isInBundle): ?>
        <!-- Bundle info bar above the button row -->
        <div class="sf-bundle-info-bar">
          <span class="sf-bundle-info-label">
            <?= htmlspecialchars(sf_term('bundle_members_label', $uiLang) ?? 'Nipussa:', ENT_QUOTES, 'UTF-8') ?>
            <strong><?= htmlspecialchars($bundleMemberLabel) ?></strong>
          </span>
        </div>
      <?php endif; ?>
      <div class="sf-step6-footer-actions">
        <button type="button" class="sf-btn sf-btn-secondary sf-prev-btn">
          <?= htmlspecialchars(sf_term('btn_prev', $uiLang), ENT_QUOTES, 'UTF-8'); ?>
        </button>
        <div class="sf-step6-footer-right">
          <?php if ($editing && ! $showSendToReview): ?>
            <!-- Muokkaus tilassa joka EI ole draft/request_info - vain tallenna -->
            <button
              type="button"
              id="sfSaveInline"
              class="sf-btn sf-btn-primary"
              data-action-url="<?= htmlspecialchars($actionUrl, ENT_QUOTES, 'UTF-8') ?>"
              data-flash-id="<?= (int)$editId ?>"
            >
              <?= htmlspecialchars(sf_term('btn_save', $uiLang) ?? 'Tallenna', ENT_QUOTES, 'UTF-8') ?>
            </button>
          <?php else: ?>
            <?php if ($isInBundle): ?>
              <!-- Bundle mode: save draft + add language + send-all -->
              <button
                type="submit"
                name="submission_type"
                value="draft"
                id="sfSaveDraft"
                class="sf-btn sf-btn-secondary"
              >
                <?= htmlspecialchars(sf_term('btn_save_draft', $uiLang), ENT_QUOTES, 'UTF-8') ?>
              </button>
              <button
                type="button"
                id="sfAddLanguageVersion"
                class="sf-btn sf-btn-outline"
                title="<?= htmlspecialchars(sf_term('btn_add_language_version_title', $uiLang) ?? 'Tallenna ensin luonnoksena, luo sitten uusi kieliversio', ENT_QUOTES, 'UTF-8') ?>"
              >
                ➕ <?= htmlspecialchars(sf_term('btn_add_language_version', $uiLang) ?? 'Lisää kieliversio', $uiLang) ?>
              </button>
              <button
                type="submit"
                name="submission_type"
                value="review"
                id="sfSubmitReview"
                class="sf-btn sf-btn-primary"
              >
                <?php
                $sendAllLabel = sprintf(
                    sf_term('btn_send_bundle_review', $uiLang) ?? 'Lähetä kaikki (%d) tarkistettavaksi',
                    $bundleCount
                );
                echo htmlspecialchars($sendAllLabel, ENT_QUOTES, 'UTF-8');
                ?>
              </button>
            <?php else: ?>
              <!-- Single translation child (not in bundle): save as draft + add language + send for review -->
              <button
                type="submit"
                name="submission_type"
                value="draft"
                id="sfSaveDraft"
                class="sf-btn sf-btn-secondary"
              >
                <?= htmlspecialchars(sf_term('btn_save_draft', $uiLang) ?? 'Tallenna', ENT_QUOTES, 'UTF-8') ?>
              </button>
              <button
                type="button"
                id="sfAddLanguageVersion"
                class="sf-btn sf-btn-outline"
                title="<?= htmlspecialchars(sf_term('btn_add_language_version_title', $uiLang) ?? 'Tallenna ensin luonnoksena, luo sitten uusi kieliversio', ENT_QUOTES, 'UTF-8') ?>"
              >
                ➕ <?= htmlspecialchars(sf_term('btn_add_language_version', $uiLang) ?? 'Lisää kieliversio', $uiLang) ?>
              </button>
              <button
                type="submit"
                name="submission_type"
                value="review"
                id="sfSubmitReview"
                class="sf-btn sf-btn-primary"
              >
                <?= htmlspecialchars(sf_term('btn_send_review', $uiLang) ?? 'Lähetä tarkistettavaksi', ENT_QUOTES, 'UTF-8') ?>
              </button>
            <?php endif; ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
  <?php endif; ?>