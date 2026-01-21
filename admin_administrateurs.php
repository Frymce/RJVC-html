<?php
// Démarrer la session
session_start();

// Inclure la configuration de la base de données
require_once 'dbconfig.php';

// Vérifier si l'utilisateur est connecté et est super_admin
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_role'] !== 'super_admin') {
    header('Location: admin.php');
    exit;
}

// Fonction pour nettoyer les entrées
function clean_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'add':
            $username = clean_input($_POST['username'] ?? '');
            $email = clean_input($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $role = $_POST['role'] ?? 'admin';
            
            // Validation
            $errors = [];
            if (empty($username)) $errors[] = "Le nom d'utilisateur est requis";
            if (empty($email)) $errors[] = "L'email est requis";
            if (empty($password)) $errors[] = "Le mot de passe est requis";
            if (!in_array($role, ['super_admin', 'admin', 'moderator'])) $errors[] = "Le rôle est invalide";
            
            if (empty($errors)) {
                try {
                    // Vérifier si l'utilisateur existe déjà
                    $stmt = $pdo->prepare("SELECT id FROM administrateurs WHERE username = ? OR email = ?");
                    $stmt->execute([$username, $email]);
                    
                    if ($stmt->fetch()) {
                        $_SESSION['error'] = "Un administrateur avec ce nom d'utilisateur ou cet email existe déjà.";
                    } else {
                        // Créer l'administrateur
                        $password_hash = password_hash($password, PASSWORD_DEFAULT);
                        $stmt = $pdo->prepare("INSERT INTO administrateurs (username, email, password_hash, role) VALUES (?, ?, ?, ?)");
                        $stmt->execute([$username, $email, $password_hash, $role]);
                        $_SESSION['success'] = "Administrateur créé avec succès.";
                    }
                } catch (Exception $e) {
                    $_SESSION['error'] = "Erreur lors de la création de l'administrateur: " . $e->getMessage();
                }
            } else {
                $_SESSION['error'] = implode("<br>", $errors);
            }
            header('Location: admin_administrateurs.php');
            exit;
            
        case 'edit':
            $id = intval($_POST['id'] ?? 0);
            $username = clean_input($_POST['username'] ?? '');
            $email = clean_input($_POST['email'] ?? '');
            $role = $_POST['role'] ?? 'admin';
            
            // Validation
            $errors = [];
            if (empty($username)) $errors[] = "Le nom d'utilisateur est requis";
            if (empty($email)) $errors[] = "L'email est requis";
            if (!in_array($role, ['super_admin', 'admin', 'moderator'])) $errors[] = "Le rôle est invalide";
            
            if (empty($errors)) {
                try {
                    // Vérifier si l'utilisateur existe déjà (sauf lui-même)
                    $stmt = $pdo->prepare("SELECT id FROM administrateurs WHERE (username = ? OR email = ?) AND id != ?");
                    $stmt->execute([$username, $email, $id]);
                    
                    if ($stmt->fetch()) {
                        $_SESSION['error'] = "Un administrateur avec ce nom d'utilisateur ou cet email existe déjà.";
                    } else {
                        // Mettre à jour l'administrateur
                        $stmt = $pdo->prepare("UPDATE administrateurs SET username = ?, email = ?, role = ? WHERE id = ?");
                        $stmt->execute([$username, $email, $role, $id]);
                        $_SESSION['success'] = "Administrateur mis à jour avec succès.";
                    }
                } catch (Exception $e) {
                    $_SESSION['error'] = "Erreur lors de la mise à jour de l'administrateur: " . $e->getMessage();
                }
            } else {
                $_SESSION['error'] = implode("<br>", $errors);
            }
            header('Location: admin_administrateurs.php');
            exit;
            
        case 'delete':
            $id = intval($_POST['id'] ?? 0);
            
            try {
                // Empêcher la suppression du super_admin connecté
                if ($id == $_SESSION['admin_id']) {
                    $_SESSION['error'] = "Vous ne pouvez pas supprimer votre propre compte.";
                } else {
                    $stmt = $pdo->prepare("DELETE FROM administrateurs WHERE id = ?");
                    $stmt->execute([$id]);
                    $_SESSION['success'] = "Administrateur supprimé avec succès.";
                }
            } catch (Exception $e) {
                $_SESSION['error'] = "Erreur lors de la suppression de l'administrateur: " . $e->getMessage();
            }
            header('Location: admin_administrateurs.php');
            exit;
            
        case 'reset_password':
            $id = intval($_POST['id'] ?? 0);
            $new_password = $_POST['new_password'] ?? '';
            
            if (empty($new_password)) {
                $_SESSION['error'] = "Le nouveau mot de passe est requis.";
            } else {
                try {
                    $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("UPDATE administrateurs SET password_hash = ? WHERE id = ?");
                    $stmt->execute([$password_hash, $id]);
                    $_SESSION['success'] = "Mot de passe réinitialisé avec succès.";
                } catch (Exception $e) {
                    $_SESSION['error'] = "Erreur lors de la réinitialisation du mot de passe: " . $e->getMessage();
                }
            }
            header('Location: admin_administrateurs.php');
            exit;
    }
}

// Récupérer la liste des administrateurs
try {
    $stmt = $pdo->prepare("SELECT id, username, email, role, created_at, last_login, statut FROM administrateurs ORDER BY created_at DESC");
    $stmt->execute();
    $administrateurs = $stmt->fetchAll();
} catch (Exception $e) {
    $administrateurs = [];
    $_SESSION['error'] = "Erreur lors de la récupération des administrateurs: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Administrateurs - RJVC</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-gradient-to-r from-blue-600 to-blue-800 text-white shadow-lg sticky top-0 z-40">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-4">
                    <div class="flex items-center">
                        <i class="fas fa-user-shield mr-3 text-yellow-400"></i>
                        <h1 class="text-xl font-bold">Gestion des Administrateurs</h1>
                    </div>
                    
                    <!-- Menu hamburger pour mobile -->
                    <div class="flex items-center">
                        <!-- Infos utilisateur (desktop) -->
                        <div class="hidden sm:flex items-center mr-4">
                            <div class="text-right mr-3">
                                <p class="text-sm font-medium text-white">
                                    <?php echo htmlspecialchars($_SESSION['admin_username']); ?>
                                </p>
                                <p class="text-xs text-blue-200">Super Admin</p>
                            </div>
                            <div class="w-10 h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                                <i class="fas fa-user text-white"></i>
                            </div>
                        </div>
                        
                        <!-- Navigation desktop (cachée sur mobile) -->
                        <nav class="hidden sm:flex items-center space-x-4">
                            <a href="admin_evenements.php" class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white px-3 py-2 rounded-lg transition-all duration-200 flex items-center">
                                <i class="fas fa-calendar mr-2"></i>
                                Événements
                            </a>
                            <a href="admin_inscriptions.php" class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white px-3 py-2 rounded-lg transition-all duration-200 flex items-center">
                                <i class="fas fa-users mr-2"></i>
                                Inscriptions
                            </a>
                            <a href="index.html" class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white px-3 py-2 rounded-lg transition-all duration-200 flex items-center">
                                <i class="fas fa-home mr-2"></i>
                                Retour au site
                            </a>
                            <a href="logout.php" class="bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded-lg transition-all duration-200 flex items-center">
                                <i class="fas fa-sign-out-alt mr-2"></i>
                                Déconnexion
                            </a>
                        </nav>
                        
                        <!-- Bouton hamburger pour mobile -->
                        <button onclick="toggleMobileMenu()" class="sm:hidden p-2 rounded-lg hover:bg-white hover:bg-opacity-20 transition-colors">
                            <i id="hamburgerIcon" class="fas fa-bars text-xl"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Menu mobile (caché par défaut) -->
                <div id="mobileMenu" class="hidden sm:hidden pb-4">
                    <!-- Infos utilisateur mobile -->
                    <div class="flex items-center justify-between mb-4 pb-4 border-b border-white border-opacity-20">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-white bg-opacity-20 rounded-full flex items-center justify-center mr-3">
                                <i class="fas fa-user text-white text-sm"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-white">
                                    <?php echo htmlspecialchars($_SESSION['admin_username']); ?>
                                </p>
                                <p class="text-xs text-blue-200">Super Admin</p>
                            </div>
                        </div>
                    </div>
                    
                    <nav class="flex flex-col space-y-2">
                        <a href="admin_evenements.php" onclick="toggleMobileMenu()" class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white px-3 py-2 rounded-lg transition-all duration-200 flex items-center">
                            <i class="fas fa-calendar mr-3"></i>
                            Événements
                        </a>
                        <a href="admin_inscriptions.php" onclick="toggleMobileMenu()" class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white px-3 py-2 rounded-lg transition-all duration-200 flex items-center">
                            <i class="fas fa-users mr-3"></i>
                            Inscriptions
                        </a>
                        <a href="index.html" onclick="toggleMobileMenu()" class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white px-3 py-2 rounded-lg transition-all duration-200 flex items-center">
                            <i class="fas fa-home mr-3"></i>
                            Retour au site
                        </a>
                        <a href="logout.php" onclick="toggleMobileMenu()" class="bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded-lg transition-all duration-200 flex items-center">
                            <i class="fas fa-sign-out-alt mr-3"></i>
                            Déconnexion
                        </a>
                    </nav>
                </div>
            </div>
        </header>

        <!-- Messages -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <!-- Contenu principal -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Bouton d'ajout -->
            <div class="mb-6 flex justify-between items-center">
                <h2 class="text-2xl font-bold text-gray-900">Liste des Administrateurs</h2>
                <button onclick="showAddForm()" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg flex items-center">
                    <i class="fas fa-plus mr-2"></i>
                    Ajouter un Admin
                </button>
            </div>

            <!-- Liste des administrateurs -->
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <div class="min-w-full">
                        <table class="w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Nom d'utilisateur</th>
                                    <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Email</th>
                                    <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Rôle</th>
                                    <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Statut</th>
                                    <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Date de création</th>
                                    <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Dernière connexion</th>
                                    <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($administrateurs as $admin): ?>
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($admin['username']); ?></div>
                                        </td>
                                        <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-500">
                                                <a href="mailto:<?php echo htmlspecialchars($admin['email']); ?>" class="text-blue-600 hover:text-blue-900">
                                                    <?php echo htmlspecialchars($admin['email']); ?>
                                                </a>
                                            </div>
                                        </td>
                                        <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                <?php 
                                                echo match($admin['role']) {
                                                    'super_admin' => 'bg-purple-100 text-purple-800',
                                                    'admin' => 'bg-blue-100 text-blue-800',
                                                    'moderator' => 'bg-green-100 text-green-800',
                                                    default => 'bg-gray-100 text-gray-800'
                                                };
                                                ?>">
                                                <?php 
                                                echo match($admin['role']) {
                                                    'super_admin' => 'Super Admin',
                                                    'admin' => 'Administrateur',
                                                    'moderator' => 'Modérateur',
                                                    default => $admin['role']
                                                };
                                                ?>
                                            </span>
                                        </td>
                                        <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                <?php 
                                                echo match($admin['statut']) {
                                                    'actif' => 'bg-green-100 text-green-800',
                                                    'inactif' => 'bg-red-100 text-red-800',
                                                    default => 'bg-gray-100 text-gray-800'
                                                };
                                                ?>">
                                                <?php echo ucfirst($admin['statut']); ?>
                                            </span>
                                        </td>
                                        <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-500">
                                                <?php echo date('d/m/Y H:i', strtotime($admin['created_at'])); ?>
                                            </div>
                                        </td>
                                        <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-500">
                                                <?php echo $admin['last_login'] ? date('d/m/Y H:i', strtotime($admin['last_login'])) : 'Jamais'; ?>
                                            </div>
                                        </td>
                                        <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <button onclick="editAdmin(<?php echo $admin['id']; ?>, '<?php echo htmlspecialchars($admin['username']); ?>', '<?php echo htmlspecialchars($admin['email']); ?>', '<?php echo $admin['role']; ?>')" 
                                                    class="text-indigo-600 hover:text-indigo-900 mr-2 p-1 hover:bg-indigo-50 rounded" title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button onclick="showPasswordForm(<?php echo $admin['id']; ?>, '<?php echo htmlspecialchars($admin['username']); ?>')" 
                                                    class="text-yellow-600 hover:text-yellow-900 mr-2 p-1 hover:bg-yellow-50 rounded" title="Réinitialiser mot de passe">
                                                <i class="fas fa-key"></i>
                                            </button>
                                            <?php if ($admin['id'] != $_SESSION['admin_id']): ?>
                                                <button onclick="deleteAdmin(<?php echo $admin['id']; ?>, '<?php echo htmlspecialchars($admin['username']); ?>')" 
                                                        class="text-red-600 hover:text-red-900 p-1 hover:bg-red-50 rounded" title="Supprimer">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>

        <!-- Modal pour ajouter/modifier un admin -->
        <div id="adminModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <h3 id="formTitle" class="text-lg font-medium text-gray-900">Ajouter un administrateur</h3>
                    <form id="adminForm" method="POST" class="mt-4 space-y-4">
                        <input type="hidden" name="action" id="formAction" value="add">
                        <input type="hidden" name="id" id="adminId">
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nom d'utilisateur</label>
                            <input type="text" name="username" required class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" name="email" required class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2">
                        </div>
                        
                        <div id="passwordField">
                            <label class="block text-sm font-medium text-gray-700">Mot de passe</label>
                            <input type="password" name="password" required class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Rôle</label>
                            <select name="role" required class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2">
                                <option value="super_admin">Super Admin</option>
                                <option value="admin">Administrateur</option>
                                <option value="moderator">Modérateur</option>
                            </select>
                        </div>
                        
                        <div class="flex justify-end space-x-2">
                            <button type="button" onclick="hideForm()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-md">
                                Annuler
                            </button>
                            <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-md">
                                Enregistrer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal pour réinitialiser le mot de passe -->
        <div id="passwordModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <h3 class="text-lg font-medium text-gray-900">Réinitialiser le mot de passe</h3>
                    <p class="text-sm text-gray-600 mt-2">Pour <span id="resetUsername" class="font-semibold"></span></p>
                    <form id="passwordForm" method="POST" class="mt-4 space-y-4">
                        <input type="hidden" name="action" value="reset_password">
                        <input type="hidden" name="id" id="resetAdminId">
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nouveau mot de passe</label>
                            <input type="password" name="new_password" required class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2">
                        </div>
                        
                        <div class="flex justify-end space-x-2">
                            <button type="button" onclick="hidePasswordForm()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-md">
                                Annuler
                            </button>
                            <button type="submit" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-md">
                                Réinitialiser
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleMobileMenu() {
            const mobileMenu = document.getElementById('mobileMenu');
            const hamburgerIcon = document.getElementById('hamburgerIcon');
            
            mobileMenu.classList.toggle('hidden');
            
            if (mobileMenu.classList.contains('hidden')) {
                hamburgerIcon.classList.remove('fa-times');
                hamburgerIcon.classList.add('fa-bars');
            } else {
                hamburgerIcon.classList.remove('fa-bars');
                hamburgerIcon.classList.add('fa-times');
            }
        }

        function showAddForm() {
            document.getElementById('formTitle').textContent = 'Ajouter un administrateur';
            document.getElementById('formAction').value = 'add';
            document.getElementById('adminId').value = '';
            document.getElementById('adminForm').reset();
            document.getElementById('passwordField').style.display = 'block';
            document.getElementById('adminModal').classList.remove('hidden');
        }

        function editAdmin(id, username, email, role) {
            document.getElementById('formTitle').textContent = 'Modifier un administrateur';
            document.getElementById('formAction').value = 'edit';
            document.getElementById('adminId').value = id;
            
            const form = document.getElementById('adminForm');
            form.querySelector('input[name="username"]').value = username;
            form.querySelector('input[name="email"]').value = email;
            form.querySelector('select[name="role"]').value = role;
            
            document.getElementById('passwordField').style.display = 'none';
            document.getElementById('adminModal').classList.remove('hidden');
        }

        function showPasswordForm(id, username) {
            document.getElementById('resetUsername').textContent = username;
            document.getElementById('resetAdminId').value = id;
            document.getElementById('passwordModal').classList.remove('hidden');
        }

        function hideForm() {
            document.getElementById('adminModal').classList.add('hidden');
            document.getElementById('adminForm').reset();
        }

        function hidePasswordForm() {
            document.getElementById('passwordModal').classList.add('hidden');
            document.getElementById('passwordForm').reset();
        }

        function deleteAdmin(id, username) {
            if (confirm(`Êtes-vous sûr de vouloir supprimer l'administrateur "${username}" ?`)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="${id}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</body>
</html>
