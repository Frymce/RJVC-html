-- Création de la base de données RJVC
CREATE DATABASE IF NOT EXISTS `rjvc` 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE `rjvc`;

-- Table pour les inscriptions
CREATE TABLE IF NOT EXISTS `inscriptions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `telephone` varchar(20) NOT NULL,
  `date_naissance` date NOT NULL,
  `genre` enum('M','F','Autre') NOT NULL,
  `adresse` text,
  `code_postal` varchar(10) DEFAULT NULL,
  `ville` varchar(100) DEFAULT NULL,
  `pays` varchar(100) DEFAULT 'France',
  `niveau_etude` varchar(100) DEFAULT NULL,
  `ecole_entreprise` varchar(255) DEFAULT NULL,
  `commentaires` text,
  `statut` enum('en_attente','validee','refusee') DEFAULT 'en_attente',
  `date_inscription` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table pour les messages de contact
CREATE TABLE IF NOT EXISTS `contacts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `reason` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `statut` enum('nouveau','en_cours','traite') DEFAULT 'nouveau',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table pour les événements
CREATE TABLE IF NOT EXISTS `evenements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titre` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `date_debut` datetime NOT NULL,
  `date_fin` datetime NOT NULL,
  `lieu` varchar(255) DEFAULT NULL,
  `categorie` enum('formation','evenement','service','social') DEFAULT 'evenement',
  `capacite` int(11) DEFAULT NULL,
  `image_url` varchar(500) DEFAULT NULL,
  `statut` enum('planifie','en_cours','termine','annule') DEFAULT 'planifie',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_date_debut` (`date_debut`),
  KEY `idx_categorie` (`categorie`),
  KEY `idx_statut` (`statut`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table pour les inscriptions aux événements
CREATE TABLE IF NOT EXISTS `participations_evenements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `evenement_id` int(11) NOT NULL,
  `inscription_id` int(11) NOT NULL,
  `date_inscription` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `statut` enum('inscrit','confirme','annule') DEFAULT 'inscrit',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_participation` (`evenement_id`, `inscription_id`),
  KEY `fk_evenement` (`evenement_id`),
  KEY `fk_inscription` (`inscription_id`),
  CONSTRAINT `fk_participation_evenement` FOREIGN KEY (`evenement_id`) REFERENCES `evenements` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_participation_inscription` FOREIGN KEY (`inscription_id`) REFERENCES `inscriptions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table pour les abonnés à la newsletter
CREATE TABLE IF NOT EXISTS `newsletter_subscribers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `subscribed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `statut` enum('actif','desinscrit') DEFAULT 'actif',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table pour les administrateurs
CREATE TABLE IF NOT EXISTS `administrateurs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('super_admin','admin','moderator') DEFAULT 'admin',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_login` timestamp NULL DEFAULT NULL,
  `statut` enum('actif','inactif') DEFAULT 'actif',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertion d'un administrateur par défaut (mot de passe: admin123)
INSERT INTO `administrateurs` (`username`, `email`, `password_hash`, `role`) 
VALUES ('admin', 'admin@rjvc.org', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'super_admin')
ON DUPLICATE KEY UPDATE `id` = `id`;

-- Insertion de quelques événements d'exemple
INSERT INTO `evenements` (`titre`, `description`, `date_debut`, `date_fin`, `lieu`, `categorie`, `capacite`, `statut`) VALUES
('Festival de la Joie 2024', 'Trois jours de louange, de musique et de partage. Un moment inoubliable pour célébrer notre foi ensemble.', '2024-08-24 09:00:00', '2024-08-26 18:00:00', 'Centre RJVC', 'evenement', 500, 'planifie'),
('Week-end Leadership', 'Une formation intensive pour les jeunes leaders et ceux qui aspirent à servir avec excellence.', '2024-09-15 10:00:00', '2024-09-15 17:00:00', 'Salle de formation RJVC', 'formation', 50, 'planifie'),
('Convention Annuelle', 'Le point culminant de notre année. Un rassemblement national pour tous les membres du mouvement RJVC.', '2024-10-18 14:00:00', '2024-10-20 20:00:00', 'Palais des Congrès', 'evenement', 1000, 'planifie')
ON DUPLICATE KEY UPDATE `updated_at` = CURRENT_TIMESTAMP;
