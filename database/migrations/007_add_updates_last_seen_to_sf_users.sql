-- Migration: 007_add_updates_last_seen_to_sf_users
-- Adds a per-user timestamp that records when each user last visited the Updates page.
-- This enables accurate, user-specific "unread updates" notification bubbles.
-- Run once against the production database before deploying the updated PHP files.

ALTER TABLE `sf_users`
  ADD COLUMN `updates_last_seen_at` datetime DEFAULT NULL
    COMMENT 'Timestamp of when the user last visited the Updates page; used for per-user unread badge';
