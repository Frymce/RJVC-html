<?php
// Démarrer la session
session_start();

// Inclure la configuration de la base de données
require_once 'dbconfig.php';

// Vérifier si l'utilisateur est connecté (simple authentification pour la démo)
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

// Fonction pour gérer l'upload d'images
function handleImageUpload($file) {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }
    
    // Vérifier le type de fichier
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mimeType, $allowedTypes)) {
        return null;
    }
    
    // Créer le dossier uploads s'il n'existe pas
    $uploadDir = 'uploads/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    // Générer un nom de fichier unique
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $fileName = 'event_' . uniqid() . '.' . $extension;
    $filePath = $uploadDir . $fileName;
    
    // Déplacer le fichier
    if (move_uploaded_file($file['tmp_name'], $filePath)) {
        return $filePath;
    }
    
    return null;
}

// Fonction pour nettoyer les entrées
function clean_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

// Récupérer tous les événements
function getAllEvenements($pdo) {
    $stmt = $pdo->query("
        SELECT * FROM evenements 
        ORDER BY date_debut ASC
    ");
    return $stmt->fetchAll();
}

// Récupérer un événement par son ID
function getEvenementById($pdo, $id) {
    $stmt = $pdo->prepare("SELECT * FROM evenements WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

// Ajouter un événement
function addEvenement($pdo, $data) {
    $stmt = $pdo->prepare("
        INSERT INTO evenements (
            titre, description, date_debut, date_fin, lieu, 
            categorie, capacite, image_url, statut
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    return $stmt->execute([
        $data['titre'],
        $data['description'],
        $data['date_debut'],
        $data['date_fin'],
        $data['lieu'],
        $data['categorie'],
        $data['capacite'],
        $data['image_url'],
        $data['statut'] ?? 'planifie'
    ]);
}

// Mettre à jour un événement
function updateEvenement($pdo, $id, $data) {
    $stmt = $pdo->prepare("
        UPDATE evenements SET 
            titre = ?, description = ?, date_debut = ?, date_fin = ?, 
            lieu = ?, categorie = ?, capacite = ?, image_url = ?, 
            statut = ?, updated_at = CURRENT_TIMESTAMP
        WHERE id = ?
    ");
    
    return $stmt->execute([
        $data['titre'],
        $data['description'],
        $data['date_debut'],
        $data['date_fin'],
        $data['lieu'],
        $data['categorie'],
        $data['capacite'],
        $data['image_url'],
        $data['statut'],
        $id
    ]);
}

// Supprimer un événement
function deleteEvenement($pdo, $id) {
    $stmt = $pdo->prepare("DELETE FROM evenements WHERE id = ?");
    return $stmt->execute([$id]);
}

// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'add':
            $data = [
                'titre' => clean_input($_POST['titre'] ?? ''),
                'description' => clean_input($_POST['description'] ?? ''),
                'date_debut' => $_POST['date_debut'] ?? '',
                'date_fin' => $_POST['date_fin'] ?? '',
                'lieu' => clean_input($_POST['lieu'] ?? ''),
                'categorie' => $_POST['categorie'] ?? 'evenement',
                'capacite' => intval($_POST['capacite'] ?? 0),
                'image_url' => '',
                'statut' => $_POST['statut'] ?? 'planifie'
            ];
            
            // Gérer l'upload d'image
            if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
                $uploadedImage = handleImageUpload($_FILES['image_file']);
                if ($uploadedImage) {
                    $data['image_url'] = $uploadedImage;
                }
            }
            
            if (addEvenement($pdo, $data)) {
                $_SESSION['success'] = "Événement ajouté avec succès.";
            } else {
                $_SESSION['error'] = "Erreur lors de l'ajout de l'événement.";
            }
            header('Location: admin_evenements.php');
            exit;
            
        case 'edit':
            $id = intval($_POST['id'] ?? 0);
            $data = [
                'titre' => clean_input($_POST['titre'] ?? ''),
                'description' => clean_input($_POST['description'] ?? ''),
                'date_debut' => $_POST['date_debut'] ?? '',
                'date_fin' => $_POST['date_fin'] ?? '',
                'lieu' => clean_input($_POST['lieu'] ?? ''),
                'categorie' => $_POST['categorie'] ?? 'evenement',
                'capacite' => intval($_POST['capacite'] ?? 0),
                'image_url' => clean_input($_POST['image_url'] ?? ''),
                'statut' => $_POST['statut'] ?? 'planifie'
            ];
            
            // Gérer l'upload d'image
            if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
                $uploadedImage = handleImageUpload($_FILES['image_file']);
                if ($uploadedImage) {
                    $data['image_url'] = $uploadedImage;
                }
            }
            
            if (updateEvenement($pdo, $id, $data)) {
                $_SESSION['success'] = "Événement mis à jour avec succès.";
            } else {
                $_SESSION['error'] = "Erreur lors de la mise à jour de l'événement.";
            }
            header('Location: admin_evenements.php');
            exit;
            
        case 'delete':
            $id = intval($_POST['id'] ?? 0);
            if (deleteEvenement($pdo, $id)) {
                $_SESSION['success'] = "Événement supprimé avec succès.";
            } else {
                $_SESSION['error'] = "Erreur lors de la suppression de l'événement.";
            }
            header('Location: admin_evenements.php');
            exit;
    }
}

// Récupérer les événements pour l'affichage
$evenements = getAllEvenements($pdo);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration des Événements - RJVC</title>
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
                        <i class="fas fa-calendar-alt mr-3 text-yellow-400"></i>
                        <h1 class="text-xl font-bold">Administration des Événements</h1>
                    </div>
                    
                    <!-- Menu hamburger pour mobile -->
                    <div class="flex items-center">
                        <!-- Infos utilisateur (desktop) -->
                        <div class="hidden sm:flex items-center mr-4">
                            <div class="text-right mr-3">
                                <p class="text-sm font-medium text-white">
                                    <?php echo htmlspecialchars($_SESSION['admin_username']); ?>
                                </p>
                                <p class="text-xs text-blue-200">
                                    <?php 
                                    $roleLabels = [
                                        'super_admin' => 'Super Admin',
                                        'admin' => 'Administrateur', 
                                        'moderator' => 'Modérateur'
                                    ];
                                    echo $roleLabels[$_SESSION['admin_role']] ?? $_SESSION['admin_role'];
                                    ?>
                                </p>
                            </div>
                            <div class="w-10 h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                                <i class="fas fa-user text-white"></i>
                            </div>
                        </div>
                        
                        <!-- Navigation desktop (cachée sur mobile) -->
                        <nav class="hidden sm:flex items-center space-x-4">
                            <?php if ($_SESSION['admin_role'] === 'super_admin'): ?>
                                <a href="admin_administrateurs.php" class="bg-purple-500 hover:bg-purple-600 text-white px-3 py-2 rounded-lg transition-all duration-200 flex items-center">
                                    <i class="fas fa-user-shield mr-2"></i>
                                    Gérer Admins
                                </a>
                            <?php endif; ?>
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
                                <p class="text-xs text-blue-200">
                                    <?php 
                                    $roleLabels = [
                                        'super_admin' => 'Super Admin',
                                        'admin' => 'Administrateur', 
                                        'moderator' => 'Modérateur'
                                    ];
                                    echo $roleLabels[$_SESSION['admin_role']] ?? $_SESSION['admin_role'];
                                    ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <nav class="flex flex-col space-y-2">
                        <?php if ($_SESSION['admin_role'] === 'super_admin'): ?>
                            <a href="admin_administrateurs.php" onclick="toggleMobileMenu()" class="bg-purple-500 hover:bg-purple-600 text-white px-3 py-2 rounded-lg transition-all duration-200 flex items-center">
                                <i class="fas fa-user-shield mr-3"></i>
                                Gérer Admins
                            </a>
                        <?php endif; ?>
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

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <!-- Bouton d'ajout -->
            <div class="mb-6">
                <button onclick="showAddForm()" class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-yellow-600 transition-colors">
                    <i class="fas fa-plus mr-2"></i>Ajouter un événement
                </button>
            </div>

            <!-- Liste des événements -->
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <div class="min-w-full">
                        <table class="w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Titre</th>
                                    <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Date</th>
                                    <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Lieu</th>
                                    <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Catégorie</th>
                                    <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Statut</th>
                                    <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($evenements as $evenement): ?>
                                    <tr class="hover:bg-gray-50 transition-colors" data-event-id="<?php echo $evenement['id']; ?>"
                                        data-titre="<?php echo htmlspecialchars($evenement['titre']); ?>"
                                        data-description="<?php echo htmlspecialchars($evenement['description']); ?>"
                                        data-date-debut="<?php echo htmlspecialchars($evenement['date_debut']); ?>"
                                        data-date-fin="<?php echo htmlspecialchars($evenement['date_fin']); ?>"
                                        data-lieu="<?php echo htmlspecialchars($evenement['lieu'] ?? ''); ?>"
                                        data-categorie="<?php echo htmlspecialchars($evenement['categorie']); ?>"
                                        data-capacite="<?php echo htmlspecialchars($evenement['capacite'] ?? ''); ?>"
                                        data-image-url="<?php echo htmlspecialchars($evenement['image_url'] ?? ''); ?>"
                                        data-statut="<?php echo htmlspecialchars($evenement['statut']); ?>">
                                        <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900 max-w-xs truncate"><?php echo htmlspecialchars($evenement['titre']); ?></div>
                                        </td>
                                        <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-500" title="<?php echo htmlspecialchars($evenement['date_debut']); ?>">
                                                <?php echo date('d/m/Y H:i', strtotime($evenement['date_debut'])); ?>
                                            </div>
                                        </td>
                                        <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-500 max-w-xs truncate"><?php echo htmlspecialchars($evenement['lieu'] ?? 'Non spécifié'); ?></div>
                                        </td>
                                        <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                <?php echo htmlspecialchars($evenement['categorie']); ?>
                                            </span>
                                        </td>
                                        <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                <?php 
                                                echo match($evenement['statut']) {
                                                    'planifie' => 'bg-yellow-100 text-yellow-800',
                                                    'en_cours' => 'bg-green-100 text-green-800',
                                                    'termine' => 'bg-gray-100 text-gray-800',
                                                    'annule' => 'bg-red-100 text-red-800',
                                                    default => 'bg-gray-100 text-gray-800'
                                                };
                                                ?>">
                                                <?php echo htmlspecialchars($evenement['statut']); ?>
                                            </span>
                                        </td>
                                        <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <button onclick="editEvenement(<?php echo $evenement['id']; ?>)" class="text-indigo-600 hover:text-indigo-900 mr-2 p-1 hover:bg-indigo-50 rounded" title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button onclick="deleteEvenement(<?php echo $evenement['id']; ?>)" class="text-red-600 hover:text-red-900 p-1 hover:bg-red-50 rounded" title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Formulaire d'ajout/modification (caché par défaut) -->
    <div id="eventForm" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900" id="formTitle">Ajouter un événement</h3>
                    <button onclick="hideForm()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <form id="eventFormElement" method="POST" class="mt-4 space-y-4" enctype="multipart/form-data">
                    <input type="hidden" name="action" id="formAction" value="add">
                    <input type="hidden" name="id" id="eventId" value="">
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Titre</label>
                        <input type="text" name="titre" required class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea name="description" required rows="3" class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2"></textarea>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Date début</label>
                            <input type="datetime-local" name="date_debut" required class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Date fin</label>
                            <input type="datetime-local" name="date_fin" required class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2">
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Lieu</label>
                        <input type="text" name="lieu" class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2">
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Catégorie</label>
                            <select name="categorie" class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2">
                                <option value="formation">Formation</option>
                                <option value="evenement">Événement</option>
                                <option value="service">Service</option>
                                <option value="social">Social</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Capacité</label>
                            <input type="number" name="capacite" min="0" class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2">
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Statut</label>
                        <select name="statut" class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2">
                            <option value="planifie">Planifié</option>
                            <option value="en_cours">En cours</option>
                            <option value="termine">Terminé</option>
                            <option value="annule">Annulé</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Image de l'événement</label>
                        <input type="file" name="image_file" accept="image/*" onchange="previewImage(this)" class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2">
                        <p class="text-xs text-gray-500 mt-1">Sélectionnez une image depuis votre galerie (JPG, PNG, GIF)</p>
                        <div id="imagePreview" class="mt-2 hidden">
                            <img id="previewImg" src="" alt="Aperçu" class="h-20 w-20 object-cover rounded">
                            <p class="text-xs text-gray-600 mt-1">Aperçu de l'image</p>
                        </div>
                        <input type="hidden" name="image_url" id="image_url" value="">
                    </div>
                    
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="hideForm()" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400">
                            Annuler
                        </button>
                        <button type="submit" class="bg-primary text-white px-4 py-2 rounded-md hover:bg-yellow-600">
                            Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function toggleMobileMenu() {
            const mobileMenu = document.getElementById('mobileMenu');
            const hamburgerIcon = document.getElementById('hamburgerIcon');
            
            mobileMenu.classList.toggle('hidden');
            
            // Changer l'icône entre hamburger et X
            if (mobileMenu.classList.contains('hidden')) {
                hamburgerIcon.classList.remove('fa-times');
                hamburgerIcon.classList.add('fa-bars');
            } else {
                hamburgerIcon.classList.remove('fa-bars');
                hamburgerIcon.classList.add('fa-times');
            }
        }

        function showAddForm() {
            document.getElementById('formTitle').textContent = 'Ajouter un événement';
            document.getElementById('formAction').value = 'add';
            document.getElementById('eventId').value = '';
            document.getElementById('eventFormElement').reset();
            document.getElementById('eventForm').classList.remove('hidden');
        }

        function editEvenement(id) {
            // Récupérer la ligne de l'événement avec les attributs data
            const row = document.querySelector(`tr[data-event-id="${id}"]`);
            
            if (row) {
                // Extraire toutes les données depuis les attributs data
                const eventData = {
                    id: row.dataset.eventId,
                    titre: row.dataset.titre,
                    description: row.dataset.description,
                    date_debut: row.dataset.dateDebut,
                    date_fin: row.dataset.dateFin,
                    lieu: row.dataset.lieu,
                    categorie: row.dataset.categorie,
                    capacite: row.dataset.capacite,
                    image_url: row.dataset.imageUrl,
                    statut: row.dataset.statut
                };
                
                // Remplir le formulaire avec toutes les données
                document.getElementById('formTitle').textContent = 'Modifier un événement';
                document.getElementById('formAction').value = 'edit';
                document.getElementById('eventId').value = eventData.id;
                
                const form = document.getElementById('eventFormElement');
                form.querySelector('input[name="titre"]').value = eventData.titre;
                form.querySelector('textarea[name="description"]').value = eventData.description;
                form.querySelector('input[name="date_debut"]').value = eventData.date_debut.replace(' ', 'T');
                form.querySelector('input[name="date_fin"]').value = eventData.date_fin.replace(' ', 'T');
                form.querySelector('input[name="lieu"]').value = eventData.lieu;
                form.querySelector('select[name="categorie"]').value = eventData.categorie;
                form.querySelector('input[name="capacite"]').value = eventData.capacite;
                form.querySelector('input[name="image_url"]').value = eventData.image_url;
                form.querySelector('select[name="statut"]').value = eventData.statut;
                
                // Pour le champ file, on ne peut pas pré-remplir, mais on peut afficher l'image actuelle
                const fileInput = form.querySelector('input[name="image_file"]');
                if (eventData.image_url) {
                    // Ajouter une indication qu'une image existe déjà
                    const fileLabel = fileInput.previousElementSibling;
                    fileLabel.innerHTML = 'Image de l\'événement <span class="text-xs text-green-600">(Image existante: ' + eventData.image_url + ')</span>';
                }
                
                document.getElementById('eventForm').classList.remove('hidden');
            } else {
                alert('Événement non trouvé');
            }
        }

        function deleteEvenement(id) {
            if (confirm('Êtes-vous sûr de vouloir supprimer cet événement ?')) {
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

        function hideForm() {
            document.getElementById('eventForm').classList.add('hidden');
            document.getElementById('eventFormElement').reset();
            // Cacher l'aperçu de l'image
            document.getElementById('imagePreview').classList.add('hidden');
            // Réinitialiser le label
            const fileLabel = document.querySelector('input[name="image_file"]').previousElementSibling;
            fileLabel.innerHTML = 'Image de l\'événement';
        }

        function previewImage(input) {
            const preview = document.getElementById('imagePreview');
            const previewImg = document.getElementById('previewImg');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    preview.classList.remove('hidden');
                };
                
                reader.readAsDataURL(input.files[0]);
            } else {
                preview.classList.add('hidden');
            }
        }
    </script>
</body>
</html>
