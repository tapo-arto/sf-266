-- Migration: 006_add_publish_date_to_sf_changelog
-- Adds an optional publish_date column to sf_changelog so admins can set
-- the displayed publication date independently of created_at.
-- Run once against the production database before deploying the updated PHP files.

ALTER TABLE `sf_changelog`
  ADD COLUMN `publish_date` date DEFAULT NULL
    COMMENT 'Optional override for the displayed publication date; falls back to created_at when NULL'
    AFTER `is_published`;

ALTER TABLE `sf_changelog`
  ADD KEY `idx_publish_date` (`publish_date`);
