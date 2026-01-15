<?php
// Test des tables spécialisées
session_start();

echo "<h1>🔧 Test des tables spécialisées d'inscription</h1>";

// Connexion à la base
try {
    require_once 'dbconfig.php';
    echo "<p style='color: green;'>✅ Connexion BDD réussie</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erreur connexion : " . $e->getMessage() . "</p>";
    exit;
}

// 1. Vérifier que les tables spécialisées existent
echo "<h2>1. Vérification des tables spécialisées</h2>";
$tables = ['inscription_formations', 'inscription_evenements', 'inscription_participations', 'inscription_benevolats'];

foreach ($tables as $table) {
    try {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            echo "<p style='color: green;'>✅ Table '$table' existe</p>";
            
            // Compter les enregistrements
            $countStmt = $pdo->query("SELECT COUNT(*) as count FROM $table");
            $count = $countStmt->fetch()['count'];
            echo "<p>📊 Enregistrements dans '$table' : $count</p>";
        } else {
            echo "<p style='color: red;'>❌ Table '$table' n'existe pas</p>";
        }
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Erreur vérification table '$table' : " . $e->getMessage() . "</p>";
    }
}

// 2. Test d'insertion complète avec tables spécialisées
echo "<h2>2. Test d'insertion dans toutes les tables spécialisées</h2>";

$test_inscriptions = [
    [
        'type' => 'formation',
        'nom' => 'TEST FORMATION',
        'prenom' => 'Test',
        'email' => 'test_formation_' . time() . '@rjvc.org',
        'formation_souhaitee' => 'developpement',
        'niveau_formation' => 'Intermédiaire',
        'commentaires' => 'Test inscription formation'
    ],
    [
        'type' => 'evenement',
        'nom' => 'TEST EVENEMENT',
        'prenom' => 'Test',
        'email' => 'test_evenement_' . time() . '@rjvc.org',
        'type_evenement' => 'mariage',
        'date_evenement' => '2024-12-25',
        'nb_participants_estime' => 100,
        'commentaires' => 'Test organisation événement'
    ],
    [
        'type' => 'participer',
        'nom' => 'TEST PARTICIPATION',
        'prenom' => 'Test',
        'email' => 'test_participation_' . time() . '@rjvc.org',
        'interets_principaux' => 'Musique, Louange, Sport',
        'disponibilites' => 'Week-end, Soir',
        'commentaires' => 'Test participation activités'
    ],
    [
        'type' => 'benevolat',
        'nom' => 'TEST BENEVOLAT',
        'prenom' => 'Test',
        'email' => 'test_benevolat_' . time() . '@rjvc.org',
        'competences' => 'Musique, Organisation, Communication',
        'disponibilites' => 'Week-end, Jours fériés',
        'interets_principaux' => 'Musique, Événements',
        'commentaires' => 'Test bénévolat'
    ],
    [
        'type' => 'mouvement',
        'nom' => 'TEST MOUVEMENT',
        'prenom' => 'Test',
        'email' => 'test_mouvement_' . time() . '@rjvc.org',
        'commentaires' => 'Test mouvement annuel'
    ]
];

foreach ($test_inscriptions as $test) {
    echo "<h3>Test d'inscription : " . strtoupper($test['type']) . "</h3>";
    
    try {
        // Données communes
        $nom = $test['nom'];
        $prenom = $test['prenom'];
        $email = $test['email'];
        $telephone = '0123456789';
        $date_naissance = '2000-01-01';
        $genre = 'Autre';
        $type_inscription = $test['type'];
        $commentaires = $test['commentaires'];
        
        // Insertion dans la table principale
        $stmt = $pdo->prepare("
            INSERT INTO inscriptions (
                nom, prenom, email, telephone, date_naissance, genre,
                type_inscription, commentaires
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $result = $stmt->execute([
            $nom, $prenom, $email, $telephone, $date_naissance, $genre,
            $type_inscription, $commentaires
        ]);
        
        if ($result) {
            $inscription_id = $pdo->lastInsertId();
            echo "<p style='color: green;'>✅ Insertion principale réussie - ID : $inscription_id</p>";
            
            // Insertion dans la table spécialisée selon le type
            switch ($type_inscription) {
                case 'formation':
                    $stmtFormation = $pdo->prepare("
                        INSERT INTO inscription_formations (
                            inscription_id, formation_souhaitee, niveau_actuel, objectifs
                        ) VALUES (?, ?, ?, ?)
                    ");
                    $resultFormation = $stmtFormation->execute([
                        $inscription_id, 
                        $test['formation_souhaitee'], 
                        $test['niveau_formation'], 
                        $commentaires
                    ]);
                    echo $resultFormation ? 
                        "<p style='color: green;'>✅ Insertion inscription_formations réussie</p>" : 
                        "<p style='color: red;'>❌ Insertion inscription_formations échouée</p>";
                    break;
                    
                case 'evenement':
                    $stmtEvenement = $pdo->prepare("
                        INSERT INTO inscription_evenements (
                            inscription_id, type_evenement, date_prevue, nb_participants_estime, description_evenement
                        ) VALUES (?, ?, ?, ?, ?)
                    ");
                    $resultEvenement = $stmtEvenement->execute([
                        $inscription_id, 
                        $test['type_evenement'], 
                        $test['date_evenement'], 
                        $test['nb_participants_estime'], 
                        $commentaires
                    ]);
                    echo $resultEvenement ? 
                        "<p style='color: green;'>✅ Insertion inscription_evenements réussie</p>" : 
                        "<p style='color: red;'>❌ Insertion inscription_evenements échouée</p>";
                    break;
                    
                case 'participer':
                    $stmtParticipation = $pdo->prepare("
                        INSERT INTO inscription_participations (
                            inscription_id, interets_principaux, disponibilites, preferences
                        ) VALUES (?, ?, ?, ?)
                    ");
                    $resultParticipation = $stmtParticipation->execute([
                        $inscription_id, 
                        $test['interets_principaux'], 
                        $test['disponibilites'], 
                        $commentaires
                    ]);
                    echo $resultParticipation ? 
                        "<p style='color: green;'>✅ Insertion inscription_participations réussie</p>" : 
                        "<p style='color: red;'>❌ Insertion inscription_participations échouée</p>";
                    break;
                    
                case 'benevolat':
                    $stmtBenevolat = $pdo->prepare("
                        INSERT INTO inscription_benevolats (
                            inscription_id, competences, disponibilites, domaines_interet, experience_precedente
                        ) VALUES (?, ?, ?, ?, ?)
                    ");
                    $resultBenevolat = $stmtBenevolat->execute([
                        $inscription_id, 
                        $test['competences'], 
                        $test['disponibilites'], 
                        $test['interets_principaux'], 
                        $commentaires
                    ]);
                    echo $resultBenevolat ? 
                        "<p style='color: green;'>✅ Insertion inscription_benevolats réussie</p>" : 
                        "<p style='color: red;'>❌ Insertion inscription_benevolats échouée</p>";
                    break;
                    
                case 'mouvement':
                    echo "<p style='color: blue;'>ℹ️ Mouvement annuel (pas de table spécialisée)</p>";
                    break;
            }
        } else {
            echo "<p style='color: red;'>❌ Insertion principale échouée pour " . $test['type'] . "</p>";
        }
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Exception pour " . $test['type'] . " : " . $e->getMessage() . "</p>";
    }
    
    echo "<hr>";
}

// 3. Vérifier les relations et afficher toutes les données
echo "<h2>3. Vérification complète des relations</h2>";

try {
    // Afficher les inscriptions avec tous les détails joints
    $stmt = $pdo->query("
        SELECT 
            i.id, i.nom, i.prenom, i.email, i.type_inscription, i.date_inscription,
            f.formation_souhaitee, f.niveau_actuel as niveau_formation,
            e.type_evenement, e.date_prevue, e.nb_participants_estime,
            p.interets_principaux, p.disponibilites as disponibilites_participation,
            b.competences, b.disponibilites as disponibilites_benevolat, b.domaines_interet
        FROM inscriptions i 
        LEFT JOIN inscription_formations f ON i.id = f.inscription_id 
        LEFT JOIN inscription_evenements e ON i.id = e.inscription_id 
        LEFT JOIN inscription_participations p ON i.id = p.inscription_id 
        LEFT JOIN inscription_benevolats b ON i.id = b.inscription_id 
        WHERE i.email LIKE '%test_%' 
        ORDER BY i.date_inscription DESC 
        LIMIT 10
    ");
    
    echo "<h3>Toutes les inscriptions de test avec détails spécialisés :</h3>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr style='background: #f0f0f0;'>";
    echo "<th>ID</th><th>Nom</th><th>Email</th><th>Type</th><th>Détails spécifiques</th><th>Date</th>";
    echo "</tr>";
    
    while ($row = $stmt->fetch()) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . htmlspecialchars($row['nom'] . ' ' . $row['prenom']) . "</td>";
        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
        echo "<td><strong>" . htmlspecialchars($row['type_inscription']) . "</strong></td>";
        
        // Afficher les détails spécifiques selon le type
        $details = '';
        switch ($row['type_inscription']) {
            case 'formation':
                $details = "Formation: " . htmlspecialchars($row['formation_souhaitee'] ?? 'N/A') . 
                          "<br>Niveau: " . htmlspecialchars($row['niveau_formation'] ?? 'N/A');
                break;
            case 'evenement':
                $details = "Type: " . htmlspecialchars($row['type_evenement'] ?? 'N/A') . 
                          "<br>Date: " . htmlspecialchars($row['date_prevue'] ?? 'N/A') . 
                          "<br>Participants: " . htmlspecialchars($row['nb_participants_estime'] ?? 'N/A');
                break;
            case 'participer':
                $details = "Intérêts: " . htmlspecialchars($row['interets_principaux'] ?? 'N/A') . 
                          "<br>Disponibilités: " . htmlspecialchars($row['disponibilites_participation'] ?? 'N/A');
                break;
            case 'benevolat':
                $details = "Compétences: " . htmlspecialchars($row['competences'] ?? 'N/A') . 
                          "<br>Disponibilités: " . htmlspecialchars($row['disponibilites_benevolat'] ?? 'N/A') . 
                          "<br>Domaines: " . htmlspecialchars($row['domaines_interet'] ?? 'N/A');
                break;
            case 'mouvement':
                $details = "Mouvement annuel RJVC";
                break;
        }
        echo "<td>" . $details . "</td>";
        echo "<td>" . $row['date_inscription'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erreur vérification relations : " . $e->getMessage() . "</p>";
}

// 4. État détaillé de chaque table
echo "<h2>4. État détaillé de chaque table spécialisée</h2>";

$tables_details = [
    'inscription_formations' => [
        'columns' => ['id', 'inscription_id', 'formation_souhaitee', 'niveau_actuel', 'objectifs'],
        'title' => 'Inscriptions aux Formations'
    ],
    'inscription_evenements' => [
        'columns' => ['id', 'inscription_id', 'type_evenement', 'date_prevue', 'nb_participants_estime', 'description_evenement'],
        'title' => 'Organisations d\'Événements'
    ],
    'inscription_participations' => [
        'columns' => ['id', 'inscription_id', 'interets_principaux', 'disponibilites', 'preferences'],
        'title' => 'Participations aux Activités'
    ],
    'inscription_benevolats' => [
        'columns' => ['id', 'inscription_id', 'competences', 'disponibilites', 'domaines_interet', 'experience_precedente'],
        'title' => 'Inscriptions en Bénévolat'
    ]
];

foreach ($tables_details as $table => $info) {
    echo "<h3>" . $info['title'] . "</h3>";
    
    try {
        $countStmt = $pdo->query("SELECT COUNT(*) as count FROM $table");
        $count = $countStmt->fetch()['count'];
        echo "<p>📊 Total : $count enregistrements</p>";
        
        if ($count > 0) {
            $dataStmt = $pdo->query("SELECT * FROM $table ORDER BY created_at DESC LIMIT 3");
            echo "<table border='1' style='border-collapse: collapse; width: 100%; margin-bottom: 20px;'>";
            echo "<tr style='background: #f0f0f0;'>";
            foreach ($info['columns'] as $col) {
                echo "<th>" . htmlspecialchars($col) . "</th>";
            }
            echo "</tr>";
            
            while ($row = $dataStmt->fetch()) {
                echo "<tr>";
                foreach ($info['columns'] as $col) {
                    $value = $row[$col] ?? 'N/A';
                    if (strlen($value) > 50) {
                        $value = substr($value, 0, 50) . '...';
                    }
                    echo "<td>" . htmlspecialchars($value) . "</td>";
                }
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p style='color: orange;'>⚠️ Aucun enregistrement dans cette table</p>";
        }
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Erreur lecture table '$table' : " . $e->getMessage() . "</p>";
    }
    
    echo "<hr>";
}

// 4. État final des tables
echo "<h2>4. État final des tables</h2>";
foreach ($tables as $table) {
    try {
        $countStmt = $pdo->query("SELECT COUNT(*) as count FROM $table");
        $count = $countStmt->fetch()['count'];
        echo "<p>📊 '$table' : $count enregistrements</p>";
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Erreur lecture '$table' : " . $e->getMessage() . "</p>";
    }
}

echo "<hr>";
echo "<p><a href='rejoindre.php'>← Retour au formulaire</a></p>";
echo "<p><a href='test_simple_insertion.php'>← Test simple</a></p>";
?>
