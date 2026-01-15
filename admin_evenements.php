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
                'image_url' => clean_input($_POST['image_url'] ?? ''),
                'statut' => $_POST['statut'] ?? 'planifie'
            ];
            
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
        <header class="bg-primary text-white shadow-lg">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-4">
                    <div class="flex items-center">
                        <i class="fas fa-calendar-alt mr-3"></i>
                        <h1 class="text-xl font-bold">Administration des Événements</h1>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="index.html" class="text-white hover:text-gray-200">
                            <i class="fas fa-home"></i> Retour au site
                        </a>
                        <a href="logout.php" class="text-white hover:text-gray-200">
                            <i class="fas fa-sign-out-alt"></i> Déconnexion
                        </a>
                    </div>
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
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Titre</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lieu</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catégorie</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($evenements as $evenement): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($evenement['titre']); ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-500">
                                        <?php echo date('d/m/Y H:i', strtotime($evenement['date_debut'])); ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-500"><?php echo htmlspecialchars($evenement['lieu'] ?? 'Non spécifié'); ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        <?php echo htmlspecialchars($evenement['categorie']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
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
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button onclick="editEvenement(<?php echo $evenement['id']; ?>)" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button onclick="deleteEvenement(<?php echo $evenement['id']; ?>)" class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
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
                <form id="eventFormElement" method="POST" class="mt-4 space-y-4">
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
        function showAddForm() {
            document.getElementById('formTitle').textContent = 'Ajouter un événement';
            document.getElementById('formAction').value = 'add';
            document.getElementById('eventId').value = '';
            document.getElementById('eventFormElement').reset();
            document.getElementById('eventForm').classList.remove('hidden');
        }

        function editEvenement(id) {
            // Charger les données de l'événement (requiert AJAX ou rechargement)
            // Pour simplifier, on va faire une requête séparée
            window.location.href = 'edit_evenement.php?id=' + id;
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
        }
    </script>
</body>
</html>
