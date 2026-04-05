-- Migration: 005_create_sf_changelog
-- Creates the sf_changelog table for the Updates / Changelog feature.
-- Run once against the production database before deploying the updated PHP files.

CREATE TABLE IF NOT EXISTS `sf_changelog` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `feedback_id` int(10) unsigned DEFAULT NULL COMMENT 'Optional link to originating feedback',
  `translations` json NOT NULL COMMENT 'Multilingual content, e.g. {"fi":{"title":"...","content":"..."},"en":{...}}',
  `is_published` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 = draft, 1 = published',
  `created_by` int(10) unsigned NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_is_published` (`is_published`),
  KEY `idx_created_at` (`created_at`),
  KEY `idx_feedback_id` (`feedback_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
