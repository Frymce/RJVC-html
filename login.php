<?php
// Démarrer la session
session_start();

// Inclure la configuration de la base de données
require_once 'dbconfig.php';

// Traitement du formulaire de connexion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    try {
        // Récupérer l'administrateur depuis la base de données
        $stmt = $pdo->prepare("SELECT * FROM administrateurs WHERE username = ? AND statut = 'actif'");
        $stmt->execute([$username]);
        $admin = $stmt->fetch();
        
        if ($admin && password_verify($password, $admin['password_hash'])) {
            // Connexion réussie
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['admin_role'] = $admin['role'];
            
            // Mettre à jour la date de dernière connexion
            $updateStmt = $pdo->prepare("UPDATE administrateurs SET last_login = CURRENT_TIMESTAMP WHERE id = ?");
            $updateStmt->execute([$admin['id']]);
            
            // Rediriger vers l'interface d'administration
            header('Location: admin_evenements.php');
            exit;
        } else {
            $error = "Nom d'utilisateur ou mot de passe incorrect";
        }
    } catch (Exception $e) {
        $error = "Erreur de connexion: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Administration - RJVC</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full space-y-8">
        <div class="bg-white shadow-xl rounded-lg p-8">
            <!-- Header -->
            <div class="text-center">
                <div class="mx-auto h-16 w-16 bg-yellow-500 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-shield-alt text-white text-2xl"></i>
                </div>
                <h2 class="text-3xl font-bold text-gray-900">Administration RJVC</h2>
                <p class="mt-2 text-sm text-gray-600">Connectez-vous pour accéder au panneau d'administration</p>
            </div>

            <!-- Messages d'erreur -->
            <?php if (isset($error)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Formulaire de connexion -->
            <form class="mt-8 space-y-6" method="POST">
                <div class="space-y-4">
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700">
                            <i class="fas fa-user mr-1"></i> Nom d'utilisateur
                        </label>
                        <input 
                            id="username" 
                            name="username" 
                            type="text" 
                            required 
                            class="mt-1 block w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
                            placeholder="admin"
                            value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
                        >
                    </div>
                    
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">
                            <i class="fas fa-lock mr-1"></i> Mot de passe
                        </label>
                        <input 
                            id="password" 
                            name="password" 
                            type="password" 
                            required 
                            class="mt-1 block w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
                            placeholder="••••••••"
                        >
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input 
                            id="remember" 
                            name="remember" 
                            type="checkbox" 
                            class="h-4 w-4 text-yellow-500 focus:ring-yellow-500 border-gray-300 rounded"
                        >
                        <label for="remember" class="ml-2 block text-sm text-gray-700">
                            Se souvenir de moi
                        </label>
                    </div>
                </div>

                <div>
                    <button 
                        type="submit" 
                        class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-yellow-500 hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition-colors"
                    >
                        <i class="fas fa-sign-in-alt mr-2"></i>
                        Se connecter
                    </button>
                </div>
            </form>

            <!-- Informations de connexion -->
            <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                <h3 class="text-sm font-semibold text-blue-800 mb-2">
                    <i class="fas fa-info-circle mr-1"></i> Informations de connexion
                </h3>
                <div class="text-xs text-blue-700 space-y-1">
                    <p><strong>Nom d'utilisateur:</strong> admin</p>
                    <p><strong>Mot de passe:</strong> admin123</p>
                    <p class="mt-2 text-blue-600">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        Changez ces identifiants après votre première connexion
                    </p>
                </div>
            </div>

            <!-- Liens utiles -->
            <div class="mt-6 text-center space-y-2">
                <a href="index.html" class="block text-sm text-gray-600 hover:text-gray-900">
                    <i class="fas fa-arrow-left mr-1"></i> Retour au site
                </a>
                <a href="fix_admin_password.php" class="block text-sm text-blue-600 hover:text-blue-900">
                    <i class="fas fa-key mr-1"></i> Réinitialiser le mot de passe admin
                </a>
            </div>
        </div>
    </div>

    <script>
        // Auto-focus sur le premier champ vide
        document.addEventListener('DOMContentLoaded', function() {
            const username = document.getElementById('username');
            const password = document.getElementById('password');
            
            if (!username.value) {
                username.focus();
            } else {
                password.focus();
            }
        });
    </script>
</body>
</html>