-- Migration: 001_create_sf_jobs
-- Replaces file-based .jobdata queue in uploads/processes/ with a database table.
-- Run once against the production database before deploying the updated PHP files.

CREATE TABLE IF NOT EXISTS `sf_jobs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `flash_id` int(10) unsigned NOT NULL,
  `job_data` json DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'pending' COMMENT 'pending, in_progress, completed, failed',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_flash_id` (`flash_id`),
  KEY `idx_status` (`status`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
