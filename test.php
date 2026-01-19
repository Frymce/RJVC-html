<?php
// Test simple pour voir si PHP fonctionne
echo "<h1>Test PHP</h1>";
echo "<p>Si vous voyez ceci, PHP fonctionne.</p>";

// Test de connexion BDD
try {
    $host = 'localhost';
    $dbname = 'rjvc';
    $username = 'root';
    $password = '';
    
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    echo "<p style='color: green;'>✅ Connexion BDD réussie</p>";
    
    // Test de la table administrateurs
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM administrateurs");
    $result = $stmt->fetch();
    echo "<p>Nombre d'admins: " . $result['count'] . "</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erreur: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<a href='login.php'>Retour à login</a>";
?>
