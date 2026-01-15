<?php
session_start();

// Inclure la configuration de la base de données
require_once 'dbconfig.php';

// Traitement du formulaire de connexion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = clean_input($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = "Veuillez remplir tous les champs.";
    } else {
        try {
            // Vérifier les identifiants dans la base
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

                header('Location: admin_evenements.php');
                exit;
            } else {
                $error = "Identifiants incorrects.";
            }
        } catch (PDOException $e) {
            error_log("Erreur lors de la connexion : " . $e->getMessage());
            $error = "Une erreur est survenue. Veuillez réessayer.";
        }
    }
}

// Fonction pour nettoyer les entrées
function clean_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

// Si déjà connecté, rediriger vers l'admin
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: admin_evenements.php');
    exit;
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
<body class="bg-background-dark min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full space-y-8 p-8">
        <!-- Header -->
        <div class="text-center">
            <div class="flex justify-center items-center mb-6">
                <div class="text-primary text-4xl mr-3">
                    <i class="fas fa-cross"></i>
                </div>
                <h1 class="text-3xl font-bold text-white">RJVC</h1>
            </div>
            <h2 class="text-xl text-white/80">Administration</h2>
            <p class="text-white/60 mt-2">Connectez-vous pour gérer les événements</p>
        </div>

        <!-- Message d'erreur -->
        <?php if (isset($error)): ?>
            <div class="bg-red-500/20 border border-red-500 text-red-400 px-4 py-3 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Formulaire de connexion -->
        <form method="POST" class="space-y-6">
            <div>
                <label for="username" class="block text-sm font-medium text-white mb-2">
                    Nom d'utilisateur
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-user text-white/40"></i>
                    </div>
                    <input
                        id="username"
                        name="username"
                        type="text"
                        required
                        class="bg-white/10 border border-white/20 text-white pl-10 pr-3 py-3 rounded-lg w-full focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                        placeholder="Entrez votre nom d'utilisateur"
                        autocomplete="username"
                    >
                </div>
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-white mb-2">
                    Mot de passe
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-lock text-white/40"></i>
                    </div>
                    <input
                        id="password"
                        name="password"
                        type="password"
                        required
                        class="bg-white/10 border border-white/20 text-white pl-10 pr-3 py-3 rounded-lg w-full focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                        placeholder="Entrez votre mot de passe"
                        autocomplete="current-password"
                    >
                </div>
            </div>

            <div>
                <button
                    type="submit"
                    class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-background-dark bg-primary hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors"
                >
                    <i class="fas fa-sign-in-alt mr-2"></i>
                    Se connecter
                </button>
            </div>
        </form>

        <!-- Informations -->
        <div class="text-center text-white/40 text-sm mt-6">
            <p class="mb-2">
                <i class="fas fa-info-circle mr-1"></i>
                Identifiants par défaut pour la démo :
            </p>
            <div class="bg-white/5 rounded-lg p-3 text-xs">
                <p><strong>Utilisateur:</strong> admin</p>
                <p><strong>Mot de passe:</strong> admin123</p>
            </div>
        </div>

        <!-- Lien de retour -->
        <div class="text-center">
            <a href="index.html" class="text-primary hover:text-yellow-400 text-sm">
                <i class="fas fa-arrow-left mr-1"></i>
                Retour au site
            </a>
        </div>
    </div>
</body>
</html>
