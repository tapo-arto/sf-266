-- Migration: 003_create_body_parts
-- Creates body_parts lookup table and incident_body_part pivot table
-- for tracking injured body parts in Ensitiedote (type='red') incident reports.
-- Run once against the production database before deploying the updated PHP files.

CREATE TABLE IF NOT EXISTS `body_parts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `category` varchar(100) NOT NULL,
  `svg_id` varchar(100) NOT NULL COMMENT 'Matches the SVG path id in the body map',
  `sort_order` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_svg_id` (`svg_id`),
  KEY `idx_category` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed: body parts grouped by category
INSERT IGNORE INTO `body_parts` (`name`, `category`, `svg_id`, `sort_order`) VALUES
  -- Pää ja niska
  ('Pää',           'Pää ja niska',  'bp-head',           10),
  ('Silmä / Silmät','Pää ja niska',  'bp-eyes',           20),
  ('Korva / Kuulo', 'Pää ja niska',  'bp-ear',            30),
  ('Kaula / Niska', 'Pää ja niska',  'bp-neck',           40),
  -- Keskivartalo
  ('Rintakehä',     'Keskivartalo',  'bp-chest',          50),
  ('Vatsa',         'Keskivartalo',  'bp-abdomen',        60),
  ('Lantioseutu',   'Keskivartalo',  'bp-pelvis',         70),
  ('Yläselkä',      'Keskivartalo',  'bp-upper-back',     80),
  ('Alaselkä',      'Keskivartalo',  'bp-lower-back',     90),
  -- Yläraajat
  ('Vasen olkapää', 'Yläraajat',     'bp-shoulder-left',  100),
  ('Oikea olkapää', 'Yläraajat',     'bp-shoulder-right', 110),
  ('Vasen käsivarsi','Yläraajat',    'bp-arm-left',       120),
  ('Oikea käsivarsi','Yläraajat',    'bp-arm-right',      130),
  ('Vasen kämmen',  'Yläraajat',     'bp-hand-left',      140),
  ('Oikea kämmen',  'Yläraajat',     'bp-hand-right',     150),
  -- Alaraajat
  ('Vasen reisi',   'Alaraajat',     'bp-thigh-left',     160),
  ('Oikea reisi',   'Alaraajat',     'bp-thigh-right',    170),
  ('Vasen polvi',   'Alaraajat',     'bp-knee-left',      180),
  ('Oikea polvi',   'Alaraajat',     'bp-knee-right',     190),
  ('Vasen pohje',   'Alaraajat',     'bp-calf-left',      200),
  ('Oikea pohje',   'Alaraajat',     'bp-calf-right',     210),
  ('Vasen nilkka',  'Alaraajat',     'bp-ankle-left',     215),
  ('Oikea nilkka',  'Alaraajat',     'bp-ankle-right',    217),
  ('Vasen jalkaterä','Alaraajat',    'bp-foot-left',      220),
  ('Oikea jalkaterä','Alaraajat',    'bp-foot-right',     230);

CREATE TABLE IF NOT EXISTS `incident_body_part` (
  `incident_id` int(10) unsigned NOT NULL,
  `body_part_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`incident_id`, `body_part_id`),
  KEY `idx_body_part_id` (`body_part_id`),
  CONSTRAINT `fk_ibp_incident` FOREIGN KEY (`incident_id`) REFERENCES `sf_flashes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_ibp_body_part` FOREIGN KEY (`body_part_id`) REFERENCES `body_parts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
