-- Migration: 004_add_original_type
-- Adds `original_type` column to `sf_flashes` to track the original Safetyflash
-- category when a report type changes over its lifecycle (e.g. Ensitiedote → Tutkintatiedote).
-- Also enables the Report Settings modal to let users manually set the original type.
--
-- Run once against the production database before deploying the updated PHP files.
-- The column may already exist if it was added via a prior hotfix; the
-- ALTER TABLE … IF NOT EXISTS syntax is not supported by all MySQL versions,
-- so check first:
--   SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
--   WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'sf_flashes' AND COLUMN_NAME = 'original_type';

ALTER TABLE `sf_flashes`
    ADD COLUMN IF NOT EXISTS `original_type` VARCHAR(255) NULL DEFAULT NULL
        COMMENT 'Original Safetyflash type before any lifecycle type change (red/yellow/green)';
