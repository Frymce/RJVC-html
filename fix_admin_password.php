<?php
// Script pour générer un hash de mot de passe correct et mettre à jour l'admin
require_once 'dbconfig.php';

// Mot de passe souhaité
$password = 'admin123';
$username = 'admin';

// Générer le hash correct
$correct_hash = password_hash($password, PASSWORD_DEFAULT);

echo "<h1>🔐 Mise à jour du mot de passe admin</h1>";
echo "<p><strong>Mot de passe:</strong> " . htmlspecialchars($password) . "</p>";
echo "<p><strong>Nouveau hash:</strong> " . htmlspecialchars($correct_hash) . "</p>";

// Vérifier le hash
echo "<p><strong>Vérification:</strong> " . (password_verify($password, $correct_hash) ? '✅ Valide' : '❌ Invalide') . "</p>";

try {
    // Mettre à jour la base de données
    $stmt = $pdo->prepare("UPDATE administrateurs SET password_hash = ? WHERE username = ?");
    $result = $stmt->execute([$correct_hash, $username]);
    
    if ($result) {
        echo "<p style='color: green;'><strong>✅ Mot de passe mis à jour avec succès dans la base de données!</strong></p>";
        
        // Vérifier la mise à jour
        $checkStmt = $pdo->prepare("SELECT username, password_hash FROM administrateurs WHERE username = ?");
        $checkStmt->execute([$username]);
        $admin = $checkStmt->fetch();
        
        if ($admin) {
            echo "<h2>📋 Vérification en base:</h2>";
            echo "<p><strong>Username:</strong> " . htmlspecialchars($admin['username']) . "</p>";
            echo "<p><strong>Hash en base:</strong> " . htmlspecialchars($admin['password_hash']) . "</p>";
            echo "<p><strong>Test de connexion:</strong> " . (password_verify($password, $admin['password_hash']) ? '✅ Le mot de passe fonctionne!' : '❌ Problème') . "</p>";
        }
    } else {
        echo "<p style='color: red;'><strong>❌ Erreur lors de la mise à jour</strong></p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'><strong>❌ Erreur:</strong> " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<h2>🔗 Liens utiles:</h2>";
echo "<p><a href='login.php'>📝 Page de connexion admin</a></p>";
echo "<p><a href='index.html'>🏠 Retour à l'accueil</a></p>";
?>
