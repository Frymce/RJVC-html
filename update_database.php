<?php
// Script pour mettre à jour la structure de la base de données
require_once 'dbconfig.php';

echo "Mise à jour de la base de données RJVC...\n";

try {
    // Vérifier d'abord si les colonnes existent avant de les ajouter
    $checkColumn = $pdo->query("SHOW COLUMNS FROM inscriptions LIKE 'type_inscription'");
    if ($checkColumn->rowCount() == 0) {
        // Ajouter les colonnes pour les différents types d'inscription
        $sql = "ALTER TABLE `inscriptions` 
                ADD COLUMN `type_inscription` enum('formation','evenement','participer','mouvement','benevolat') NOT NULL DEFAULT 'formation' AFTER `commentaires`";
        $pdo->exec($sql);
        echo "Colonne type_inscription ajoutée!\n";
    } else {
        echo "Colonne type_inscription existe déjà!\n";
    }
    
    // Ajouter les autres colonnes une par une
    $columns = [
        'formation_souhaitee' => "varchar(100) DEFAULT NULL AFTER `type_inscription`",
        'niveau_formation' => "varchar(50) DEFAULT NULL AFTER `formation_souhaitee`",
        'type_evenement' => "varchar(50) DEFAULT NULL AFTER `niveau_formation`",
        'date_evenement' => "date DEFAULT NULL AFTER `type_evenement`",
        'nb_participants_estime' => "int(11) DEFAULT NULL AFTER `date_evenement`",
        'interets_principaux' => "text DEFAULT NULL AFTER `nb_participants_estime`",
        'disponibilites' => "text DEFAULT NULL AFTER `interets_principaux`",
        'competences' => "text DEFAULT NULL AFTER `disponibilites`"
    ];
    
    foreach ($columns as $columnName => $columnDef) {
        $checkColumn = $pdo->query("SHOW COLUMNS FROM inscriptions LIKE '$columnName'");
        if ($checkColumn->rowCount() == 0) {
            $sql = "ALTER TABLE `inscriptions` ADD COLUMN `$columnName` $columnDef";
            $pdo->exec($sql);
            echo "Colonne $columnName ajoutée!\n";
        } else {
            echo "Colonne $columnName existe déjà!\n";
        }
    }
    
    // Créer les tables supplémentaires si elles n'existent pas
    $tables = [
        "CREATE TABLE IF NOT EXISTS `inscription_formations` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `inscription_id` int(11) NOT NULL,
          `formation_souhaitee` varchar(100) NOT NULL,
          `niveau_actuel` varchar(50) DEFAULT NULL,
          `objectifs` text DEFAULT NULL,
          `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
          PRIMARY KEY (`id`),
          KEY `fk_inscription_formation` (`inscription_id`),
          CONSTRAINT `fk_inscription_formation` FOREIGN KEY (`inscription_id`) REFERENCES `inscriptions` (`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        
        "CREATE TABLE IF NOT EXISTS `inscription_evenements` (
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        
        "CREATE TABLE IF NOT EXISTS `inscription_participations` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `inscription_id` int(11) NOT NULL,
          `interets_principaux` text DEFAULT NULL,
          `disponibilites` text DEFAULT NULL,
          `preferences` text DEFAULT NULL,
          `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
          PRIMARY KEY (`id`),
          KEY `fk_inscription_participation` (`inscription_id`),
          CONSTRAINT `fk_inscription_participation` FOREIGN KEY (`inscription_id`) REFERENCES `inscriptions` (`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        
        "CREATE TABLE IF NOT EXISTS `inscription_benevolats` (
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
    ];
    
    foreach ($tables as $tableSql) {
        $pdo->exec($tableSql);
    }
    
    echo "Tables créées avec succès!\n";
    echo "Mise à jour terminée avec succès!\n";
    
} catch (PDOException $e) {
    echo "Erreur lors de la mise à jour : " . $e->getMessage() . "\n";
}
?>
