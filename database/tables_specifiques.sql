-- Tables spécifiques pour chaque type d'inscription RJVC
USE `rjvc`;

-- Table pour les inscriptions aux formations (RJVC Academy)
CREATE TABLE IF NOT EXISTS `inscriptions_formations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `inscription_id` int(11) NOT NULL,
  `formation_choisie` enum('developpement','design','photo','video','leadership','theologie') NOT NULL,
  `niveau_actuel` varchar(50) DEFAULT NULL,
  `objectifs` text DEFAULT NULL,
  `experience_prealable` text DEFAULT NULL,
  `statut` enum('en_attente','accepte','refuse','en_cours') DEFAULT 'en_attente',
  `date_inscription` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `inscription_id` (`inscription_id`),
  CONSTRAINT `fk_formation_inscription_rjvc` FOREIGN KEY (`inscription_id`) REFERENCES `inscriptions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table pour les organisations d'événements
CREATE TABLE IF NOT EXISTS `organisations_evenements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `inscription_id` int(11) NOT NULL,
  `type_evenement` enum('mariage','bapteme','anniversaire','conference','retraite','concert') NOT NULL,
  `date_prevue` date DEFAULT NULL,
  `nombre_participants_estime` int(11) DEFAULT NULL,
  `lieu_prevu` varchar(255) DEFAULT NULL,
  `budget_estime` decimal(10,2) DEFAULT NULL,
  `besoins_specifiques` text DEFAULT NULL,
  `statut` enum('en_attente','en_discussion','confirme','annule') DEFAULT 'en_attente',
  `date_demande` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `inscription_id` (`inscription_id`),
  CONSTRAINT `fk_organisation_inscription_rjvc` FOREIGN KEY (`inscription_id`) REFERENCES `inscriptions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table pour la participation aux activités
CREATE TABLE IF NOT EXISTS `participations_activites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `inscription_id` int(11) NOT NULL,
  `interets` json DEFAULT NULL,
  `disponibilites` varchar(255) DEFAULT NULL,
  `competences` text DEFAULT NULL,
  `souhaits_benevolat` tinyint(1) DEFAULT 0,
  `zones_interet` text DEFAULT NULL,
  `statut` enum('en_attente','actif','inactif') DEFAULT 'en_attente',
  `date_inscription` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `inscription_id` (`inscription_id`),
  CONSTRAINT `fk_participation_inscription_rjvc` FOREIGN KEY (`inscription_id`) REFERENCES `inscriptions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table pour le mouvement annuel RJVC
CREATE TABLE IF NOT EXISTS `inscriptions_mouvement` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `inscription_id` int(11) NOT NULL,
  `annee_mouvement` year NOT NULL,
  `engagement_mensuel` decimal(8,2) DEFAULT NULL,
  `mode_participation` enum('membre_actif','supporteur','prieur','benevole') DEFAULT 'membre_actif',
  `talents_offerts` text DEFAULT NULL,
  `disponibilites_mensuelles` varchar(255) DEFAULT NULL,
  `objectifs_personnels` text DEFAULT NULL,
  `statut` enum('en_attente','membre','suspendu','radié') DEFAULT 'en_attente',
  `date_inscription` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `inscription_id` (`inscription_id`),
  CONSTRAINT `fk_mouvement_inscription_rjvc` FOREIGN KEY (`inscription_id`) REFERENCES `inscriptions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table pour les bénévoles
CREATE TABLE IF NOT EXISTS `inscriptions_benevolat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `inscription_id` int(11) NOT NULL,
  `domaines_benevolat` json DEFAULT NULL,
  `disponibilites_hebdomadaires` varchar(255) DEFAULT NULL,
  `experience_benevolat` text DEFAULT NULL,
  `competences_specifiques` text DEFAULT NULL,
  `engagement_duree` enum('ponctuel','3_mois','6_mois','1_an','indetermine') DEFAULT 'ponctuel',
  `motivations` text DEFAULT NULL,
  `statut` enum('en_attente','approuve','actif','inactif') DEFAULT 'en_attente',
  `date_candidature` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `inscription_id` (`inscription_id`),
  CONSTRAINT `fk_benevolat_inscription_rjvc` FOREIGN KEY (`inscription_id`) REFERENCES `inscriptions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Ajout du champ type_inscription dans la table principale
SET @dbname = DATABASE();
SET @tablename = 'inscriptions';
SET @columnname = 'type_inscription';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_schema = @dbname)
      AND (table_name = @tablename)
      AND (column_name = @columnname)
  ) > 0,
  'SELECT 1',
  CONCAT('ALTER TABLE `', @tablename, '` ADD COLUMN `', @columnname, '` enum(\'formation\',\'evenement\',\'participer\',\'mouvement\',\'benevolat\') NOT NULL AFTER `commentaires`;')
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;
