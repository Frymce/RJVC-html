<?php
// Script de test pour diagnostiquer les problèmes d'inscription
session_start();

echo "<h1>Test de diagnostic d'inscription RJVC</h1>";

// 1. Test de connexion à la base de données
echo "<h2>1. Test de connexion à la base de données</h2>";
try {
    require_once 'dbconfig.php';
    echo "<p style='color: green;'>✅ Connexion à la base de données réussie</p>";
    
    // Vérifier si la table inscriptions existe
    $stmt = $pdo->query("SHOW TABLES LIKE 'inscriptions'");
    if ($stmt->rowCount() > 0) {
        echo "<p style='color: green;'>✅ Table 'inscriptions' trouvée</p>";
        
        // Compter les enregistrements
        $countStmt = $pdo->query("SELECT COUNT(*) as count FROM inscriptions");
        $count = $countStmt->fetch()['count'];
        echo "<p>📊 Nombre d'inscriptions dans la base : " . $count . "</p>";
        
        // Afficher les 3 dernières inscriptions si elles existent
        if ($count > 0) {
            echo "<h3>Dernières inscriptions :</h3>";
            $lastStmt = $pdo->query("SELECT nom, prenom, email, date_inscription FROM inscriptions ORDER BY date_inscription DESC LIMIT 3");
            echo "<table border='1'><tr><th>Nom</th><th>Prénom</th><th>Email</th><th>Date</th></tr>";
            while ($row = $lastStmt->fetch()) {
                echo "<tr><td>" . htmlspecialchars($row['nom']) . "</td><td>" . htmlspecialchars($row['prenom']) . "</td><td>" . htmlspecialchars($row['email']) . "</td><td>" . $row['date_inscription'] . "</td></tr>";
            }
            echo "</table>";
        }
    } else {
        echo "<p style='color: red;'>❌ Table 'inscriptions' NON trouvée</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erreur de connexion : " . $e->getMessage() . "</p>";
}

// 2. Test des données POST
echo "<h2>2. Test des données POST</h2>";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<p>📥 Méthode POST détectée</p>";
    echo "<h3>Données reçues :</h3>";
    echo "<pre>" . print_r($_POST, true) . "</pre>";
    
    // Vérifier le jeton CSRF
    if (isset($_POST['csrf_token'])) {
        echo "<p>🔐 Jeton CSRF reçu : " . substr($_POST['csrf_token'], 0, 10) . "...</p>";
        if (isset($_SESSION['csrf_token'])) {
            echo "<p>🔐 Jeton CSRF en session : " . substr($_SESSION['csrf_token'], 0, 10) . "...</p>";
            if ($_POST['csrf_token'] === $_SESSION['csrf_token']) {
                echo "<p style='color: green;'>✅ Jetons CSRF identiques</p>";
            } else {
                echo "<p style='color: red;'>❌ Jetons CSRF différents</p>";
            }
        } else {
            echo "<p style='color: red;'>❌ Aucun jeton CSRF en session</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ Aucun jeton CSRF reçu</p>";
    }
} else {
    echo "<p>📤 Aucune donnée POST (méthode : " . $_SERVER['REQUEST_METHOD'] . ")</p>";
}

// 3. Test d'insertion manuel
echo "<h2>3. Test d'insertion manuel</h2>";
try {
    $testData = [
        'nom' => 'TEST',
        'prenom' => 'Diagnostic',
        'email' => 'test' . time() . '@rjvc.org',
        'telephone' => '0123456789',
        'date_naissance' => '2000-01-01',
        'genre' => 'Autre',
        'adresse' => 'Test address',
        'code_postal' => '75000',
        'ville' => 'Paris',
        'pays' => 'France',
        'niveau_etude' => 'Test',
        'ecole_entreprise' => 'Test',
        'commentaires' => 'Test insertion automatique',
        'type_inscription' => 'formation',
        'formation_souhaitee' => 'developpement',
        'niveau_formation' => 'Intermédiaire',
        'type_evenement' => 'conference',
        'date_evenement' => '2024-12-25',
        'nb_participants_estime' => 50,
        'interets_principaux' => 'Musique, Louange',
        'disponibilites' => 'Week-end, Soir',
        'competences' => 'Musique, Organisation'
    ];
    
    $stmt = $pdo->prepare("
        INSERT INTO inscriptions (
            nom, prenom, email, telephone, date_naissance, genre,
            adresse, code_postal, ville, pays, niveau_etude, ecole_entreprise, commentaires,
            type_inscription, formation_souhaitee, niveau_formation, type_evenement, 
            date_evenement, nb_participants_estime, interets_principaux, disponibilites, competences
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    $result = $stmt->execute([
        $testData['nom'], $testData['prenom'], $testData['email'], $testData['telephone'], 
        $testData['date_naissance'], $testData['genre'], $testData['adresse'], $testData['code_postal'], 
        $testData['ville'], $testData['pays'], $testData['niveau_etude'], $testData['ecole_entreprise'], 
        $testData['commentaires'], $testData['type_inscription'], $testData['formation_souhaitee'], 
        $testData['niveau_formation'], $testData['type_evenement'], $testData['date_evenement'], 
        $testData['nb_participants_estime'], $testData['interets_principaux'], $testData['disponibilites'], 
        $testData['competences']
    ]);
    
    if ($result) {
        echo "<p style='color: green;'>✅ Insertion test réussie avec l'email : " . $testData['email'] . "</p>";
        echo "<p>📝 Type d'inscription : " . $testData['type_inscription'] . "</p>";
        echo "<p>📝 Formation souhaitée : " . $testData['formation_souhaitee'] . "</p>";
    } else {
        echo "<p style='color: red;'>❌ Échec de l'insertion test</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erreur lors du test d'insertion : " . $e->getMessage() . "</p>";
    echo "<p><strong>Détails de l'erreur :</strong> " . $e->getTraceAsString() . "</p>";
}

// 4. Informations de configuration
echo "<h2>4. Informations de configuration</h2>";
echo "<p>📁 Chemin du script : " . __FILE__ . "</p>";
echo "<p>📁 Répertoire de travail : " . getcwd() . "</p>";
echo "<p>🔧 Version PHP : " . PHP_VERSION . "</p>";
echo "<p>🔧 Extensions PDO : ";
echo implode(', ', PDO::getAvailableDrivers());
echo "</p>";

echo "<hr>";
echo "<p><a href='rejoindre.php'>← Retour au formulaire d'inscription</a></p>";
?>
