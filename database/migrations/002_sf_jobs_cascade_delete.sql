-- Migration: 002_sf_jobs_cascade_delete
-- Adds a foreign key with ON DELETE CASCADE so that sf_jobs rows are
-- automatically removed by the database when the parent sf_flashes row is
-- deleted.  This acts as a safety net in addition to the explicit DELETE
-- statements added to the PHP delete endpoints.
--
-- Run once against the production database before (or alongside) deploying
-- the updated PHP files.

ALTER TABLE `sf_jobs`
  ADD CONSTRAINT `fk_sf_jobs_flash_id`
    FOREIGN KEY (`flash_id`) REFERENCES `sf_flashes` (`id`)
    ON DELETE CASCADE;
