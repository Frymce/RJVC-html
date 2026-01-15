-- Mise à jour de la table inscriptions pour gérer les différents types d'inscription
USE `rjvc`;

-- Ajouter les colonnes pour les différents types d'inscription
ALTER TABLE `inscriptions` 
ADD COLUMN `type_inscription` enum('formation','evenement','participer','mouvement','benevolat') NOT NULL DEFAULT 'formation' AFTER `commentaires`,
ADD COLUMN `formation_souhaitee` varchar(100) DEFAULT NULL AFTER `type_inscription`,
ADD COLUMN `niveau_formation` varchar(50) DEFAULT NULL AFTER `formation_souhaitee`,
ADD COLUMN `type_evenement` varchar(50) DEFAULT NULL AFTER `niveau_formation`,
ADD COLUMN `date_evenement` date DEFAULT NULL AFTER `type_evenement`,
ADD COLUMN `nb_participants_estime` int(11) DEFAULT NULL AFTER `date_evenement`,
ADD COLUMN `interets_principaux` text DEFAULT NULL AFTER `nb_participants_estime`,
ADD COLUMN `disponibilites` text DEFAULT NULL AFTER `interets_principaux`,
ADD COLUMN `competences` text DEFAULT NULL AFTER `disponibilites`;

-- Créer des tables séparées pour les détails spécifiques si nécessaire
-- Table pour les détails de formations
CREATE TABLE IF NOT EXISTS `inscription_formations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `inscription_id` int(11) NOT NULL,
  `formation_souhaitee` varchar(100) NOT NULL,
  `niveau_actuel` varchar(50) DEFAULT NULL,
  `objectifs` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_inscription_formation` (`inscription_id`),
  CONSTRAINT `fk_inscription_formation` FOREIGN KEY (`inscription_id`) REFERENCES `inscriptions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table pour les détails d'événements
CREATE TABLE IF NOT EXISTS `inscription_evenements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `inscription_id` int(11) NOT NULL,
  `type_evenement` varchar(50) NOT NULL,
  `date_prevue` date DEFAULT NULL,
  `nb_participants_estime` int(11) DEFAULT NULL,
  `description_evenement` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_inscription_evenement` (`inscription_id`),
  CONSTRAINT `fk_inscription_evenement` FOREIGN KEY (`inscription_id`) REFERENCES `inscriptions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table pour les détails de participation
CREATE TABLE IF NOT EXISTS `inscription_participations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `inscription_id` int(11) NOT NULL,
  `interets_principaux` text DEFAULT NULL,
  `disponibilites` text DEFAULT NULL,
  `preferences` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_inscription_participation` (`inscription_id`),
  CONSTRAINT `fk_inscription_participation` FOREIGN KEY (`inscription_id`) REFERENCES `inscriptions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table pour les détails de bénévolat
CREATE TABLE IF NOT EXISTS `inscription_benevolats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `inscription_id` int(11) NOT NULL,
  `competences` text DEFAULT NULL,
  `disponibilites` text DEFAULT NULL,
  `domaines_interet` text DEFAULT NULL,
  `experience_precedente` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_inscription_benevolat` (`inscription_id`),
  CONSTRAINT `fk_inscription_benevolat` FOREIGN KEY (`inscription_id`) REFERENCES `inscriptions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
