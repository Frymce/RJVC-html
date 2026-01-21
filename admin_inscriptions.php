<?php
// Démarrer la session
session_start();

// Inclure la configuration de la base de données
require_once 'dbconfig.php';

// Vérifier si l'utilisateur est connecté
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

// Récupérer toutes les inscriptions
function getAllInscriptions($pdo) {
    $stmt = $pdo->query("
        SELECT * FROM inscriptions 
        ORDER BY date_inscription DESC
    ");
    return $stmt->fetchAll();
}

// Récupérer une inscription par son ID
function getInscriptionById($pdo, $id) {
    $stmt = $pdo->prepare("SELECT * FROM inscriptions WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

// Valider une inscription
function validerInscription($pdo, $id) {
    $stmt = $pdo->prepare("UPDATE inscriptions SET statut = 'validee' WHERE id = ?");
    return $stmt->execute([$id]);
}

// Refuser une inscription
function refuserInscription($pdo, $id, $motif = '') {
    $stmt = $pdo->prepare("UPDATE inscriptions SET statut = 'refusee', commentaires = CONCAT(IFNULL(commentaires, ''), '\n\nMotif de refus: ', ?) WHERE id = ?");
    return $stmt->execute([$motif, $id]);
}

// Supprimer une inscription
function deleteInscription($pdo, $id) {
    $stmt = $pdo->prepare("DELETE FROM inscriptions WHERE id = ?");
    return $stmt->execute([$id]);
}

// Statistiques des inscriptions
function getStatistiques($pdo) {
    $stats = [];
    
    // Total des inscriptions
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM inscriptions");
    $stats['total'] = $stmt->fetch()['total'];
    
    // Inscriptions en attente
    $stmt = $pdo->query("SELECT COUNT(*) as en_attente FROM inscriptions WHERE statut = 'en_attente'");
    $stats['en_attente'] = $stmt->fetch()['en_attente'];
    
    // Inscriptions validées
    $stmt = $pdo->query("SELECT COUNT(*) as validee FROM inscriptions WHERE statut = 'validee'");
    $stats['validee'] = $stmt->fetch()['validee'];
    
    // Inscriptions refusées
    $stmt = $pdo->query("SELECT COUNT(*) as refusee FROM inscriptions WHERE statut = 'refusee'");
    $stats['refusee'] = $stmt->fetch()['refusee'];
    
    // Répartition par genre
    $stmt = $pdo->query("SELECT genre, COUNT(*) as count FROM inscriptions GROUP BY genre");
    $stats['genre'] = $stmt->fetchAll();
    
    return $stats;
}

// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'valider':
            $id = intval($_POST['id'] ?? 0);
            if (validerInscription($pdo, $id)) {
                $_SESSION['success'] = "Inscription validée avec succès.";
            } else {
                $_SESSION['error'] = "Erreur lors de la validation de l'inscription.";
            }
            header('Location: admin_inscriptions.php');
            exit;
            
        case 'refuser':
            $id = intval($_POST['id'] ?? 0);
            $motif = clean_input($_POST['motif'] ?? '');
            if (refuserInscription($pdo, $id, $motif)) {
                $_SESSION['success'] = "Inscription refusée avec succès.";
            } else {
                $_SESSION['error'] = "Erreur lors du refus de l'inscription.";
            }
            header('Location: admin_inscriptions.php');
            exit;
            
        case 'delete':
            $id = intval($_POST['id'] ?? 0);
            if (deleteInscription($pdo, $id)) {
                $_SESSION['success'] = "Inscription supprimée avec succès.";
            } else {
                $_SESSION['error'] = "Erreur lors de la suppression de l'inscription.";
            }
            header('Location: admin_inscriptions.php');
            exit;
    }
}

// Récupérer les inscriptions pour l'affichage
$inscriptions = getAllInscriptions($pdo);
$statistiques = getStatistiques($pdo);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration des Inscriptions - RJVC</title>
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
                        <i class="fas fa-users mr-3 text-yellow-400"></i>
                        <h1 class="text-xl font-bold">Administration des Inscriptions</h1>
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
                            <a href="admin_evenements.php" class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white px-3 py-2 rounded-lg transition-all duration-200 flex items-center">
                                <i class="fas fa-calendar mr-2"></i>
                                Événements
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
                        <a href="admin_evenements.php" onclick="toggleMobileMenu()" class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white px-3 py-2 rounded-lg transition-all duration-200 flex items-center">
                            <i class="fas fa-calendar mr-3"></i>
                            Événements
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
            <!-- Statistiques -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-blue-100 rounded-full">
                            <i class="fas fa-users text-blue-600"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-500">Total</p>
                            <p class="text-2xl font-bold text-gray-900"><?php echo $statistiques['total']; ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-yellow-100 rounded-full">
                            <i class="fas fa-clock text-yellow-600"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-500">En attente</p>
                            <p class="text-2xl font-bold text-yellow-600"><?php echo $statistiques['en_attente']; ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-green-100 rounded-full">
                            <i class="fas fa-check text-green-600"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-500">Validées</p>
                            <p class="text-2xl font-bold text-green-600"><?php echo $statistiques['validee']; ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-red-100 rounded-full">
                            <i class="fas fa-times text-red-600"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-500">Refusées</p>
                            <p class="text-2xl font-bold text-red-600"><?php echo $statistiques['refusee']; ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filtres -->
            <div class="bg-white shadow rounded-lg p-4 mb-6">
                <div class="flex flex-wrap gap-4 items-center">
                    <div class="flex items-center space-x-2">
                        <label class="text-sm font-medium text-gray-700">Filtrer par statut:</label>
                        <select id="statutFilter" onchange="filterInscriptions()" class="border border-gray-300 rounded px-3 py-1 text-sm">
                            <option value="">Tous</option>
                            <option value="en_attente">En attente</option>
                            <option value="validee">Validées</option>
                            <option value="refusee">Refusées</option>
                        </select>
                    </div>
                    <div class="flex items-center space-x-2">
                        <input type="text" id="searchInput" placeholder="Rechercher par nom ou email..." 
                               onkeyup="filterInscriptions()" 
                               class="border border-gray-300 rounded px-3 py-1 text-sm">
                    </div>
                </div>
            </div>

            <!-- Liste des inscriptions -->
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <div class="min-w-full">
                        <table class="w-full divide-y divide-gray-200" id="inscriptionsTable">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Nom</th>
                                    <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Email</th>
                                    <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Téléphone</th>
                                    <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Date de naissance</th>
                                    <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Genre</th>
                                    <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Date d'inscription</th>
                                    <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Statut</th>
                                    <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($inscriptions as $inscription): ?>
                                    <tr class="inscription-row hover:bg-gray-50 transition-colors" data-statut="<?php echo $inscription['statut']; ?>" 
                                        data-nom="<?php echo strtolower($inscription['nom'] . ' ' . $inscription['prenom']); ?>" 
                                        data-email="<?php echo strtolower($inscription['email']); ?>">
                                        <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900 max-w-xs truncate">
                                                <?php echo htmlspecialchars($inscription['prenom'] . ' ' . $inscription['nom']); ?>
                                            </div>
                                        </td>
                                        <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-500">
                                                <a href="mailto:<?php echo htmlspecialchars($inscription['email']); ?>" class="text-blue-600 hover:text-blue-900">
                                                    <?php echo htmlspecialchars($inscription['email']); ?>
                                                </a>
                                            </div>
                                        </td>
                                        <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-500"><?php echo htmlspecialchars($inscription['telephone']); ?></div>
                                        </td>
                                        <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-500">
                                                <?php echo date('d/m/Y', strtotime($inscription['date_naissance'])); ?>
                                            </div>
                                        </td>
                                        <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                                            <span class="text-sm text-gray-500">
                                                <?php 
                                                echo match($inscription['genre']) {
                                                    'M' => 'Homme',
                                                    'F' => 'Femme',
                                                    'Autre' => 'Autre',
                                                    default => $inscription['genre']
                                                };
                                                ?>
                                            </span>
                                        </td>
                                        <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-500">
                                                <?php echo date('d/m/Y H:i', strtotime($inscription['date_inscription'])); ?>
                                            </div>
                                        </td>
                                        <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                <?php 
                                                echo match($inscription['statut']) {
                                                    'en_attente' => 'bg-yellow-100 text-yellow-800',
                                                    'validee' => 'bg-green-100 text-green-800',
                                                    'refusee' => 'bg-red-100 text-red-800',
                                                    default => 'bg-gray-100 text-gray-800'
                                                };
                                                ?>">
                                                <?php 
                                                echo match($inscription['statut']) {
                                                    'en_attente' => 'En attente',
                                                    'validee' => 'Validée',
                                                    'refusee' => 'Refusée',
                                                    default => $inscription['statut']
                                                };
                                                ?>
                                            </span>
                                        </td>
                                        <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <?php if ($inscription['statut'] === 'en_attente'): ?>
                                                <button onclick="validerInscription(<?php echo $inscription['id']; ?>)" 
                                                        class="text-green-600 hover:text-green-900 mr-1 p-1 hover:bg-green-50 rounded" title="Valider">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <button onclick="showRefusForm(<?php echo $inscription['id']; ?>)" 
                                                        class="text-red-600 hover:text-red-900 mr-1 p-1 hover:bg-red-50 rounded" title="Refuser">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            <?php endif; ?>
                                            <button onclick="viewDetails(<?php echo $inscription['id']; ?>)" 
                                                    class="text-blue-600 hover:text-blue-900 mr-1 p-1 hover:bg-blue-50 rounded" title="Voir détails">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button onclick="deleteInscription(<?php echo $inscription['id']; ?>)" 
                                                    class="text-red-600 hover:text-red-900 p-1 hover:bg-red-50 rounded" title="Supprimer">
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

    <!-- Modal pour refuser une inscription -->
    <div id="refusModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900">Refuser l'inscription</h3>
                <form id="refusForm" method="POST" class="mt-4 space-y-4">
                    <input type="hidden" name="action" value="refuser">
                    <input type="hidden" name="id" id="refusId">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Motif du refus</label>
                        <textarea name="motif" rows="3" required 
                                  class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2"
                                  placeholder="Expliquez pourquoi vous refusez cette inscription..."></textarea>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="hideRefusForm()" 
                                class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400">
                            Annuler
                        </button>
                        <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700">
                            Refuser
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal pour voir les détails -->
    <div id="detailsModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-10 mx-auto p-5 border w-2/3 max-w-4xl shadow-lg rounded-md bg-white max-h-[80vh] overflow-y-auto">
            <div class="mt-3">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Détails de l'inscription</h3>
                    <button onclick="hideDetailsModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div id="detailsContent"></div>
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

        function filterInscriptions() {
            const statutFilter = document.getElementById('statutFilter').value.toLowerCase();
            const searchInput = document.getElementById('searchInput').value.toLowerCase();
            const rows = document.querySelectorAll('.inscription-row');
            
            rows.forEach(row => {
                const statut = row.dataset.statut.toLowerCase();
                const nom = row.dataset.nom;
                const email = row.dataset.email;
                
                const matchesStatut = !statutFilter || statut === statutFilter;
                const matchesSearch = !searchInput || nom.includes(searchInput) || email.includes(searchInput);
                
                row.style.display = matchesStatut && matchesSearch ? '' : 'none';
            });
        }

        function validerInscription(id) {
            if (confirm('Êtes-vous sûr de vouloir valider cette inscription ?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="valider">
                    <input type="hidden" name="id" value="${id}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        function showRefusForm(id) {
            document.getElementById('refusId').value = id;
            document.getElementById('refusModal').classList.remove('hidden');
        }

        function hideRefusForm() {
            document.getElementById('refusModal').classList.add('hidden');
            document.getElementById('refusForm').reset();
        }

        function deleteInscription(id) {
            if (confirm('Êtes-vous sûr de vouloir supprimer cette inscription ? Cette action est irréversible.')) {
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

        function viewDetails(id) {
            // Récupérer les détails via une requête AJAX ou afficher les données déjà disponibles
            const rows = document.querySelectorAll('.inscription-row');
            let inscriptionData = null;
            
            rows.forEach(row => {
                const button = row.querySelector(`button[onclick*="${id}"]`);
                if (button) {
                    inscriptionData = row;
                }
            });
            
            if (inscriptionData) {
                const cells = inscriptionData.querySelectorAll('td');
                const detailsHTML = `
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <h4 class="font-semibold text-gray-700 mb-2">Informations personnelles</h4>
                            <p><strong>Nom complet:</strong> ${cells[0].textContent.trim()}</p>
                            <p><strong>Email:</strong> ${cells[1].textContent.trim()}</p>
                            <p><strong>Téléphone:</strong> ${cells[2].textContent.trim()}</p>
                            <p><strong>Date de naissance:</strong> ${cells[3].textContent.trim()}</p>
                            <p><strong>Genre:</strong> ${cells[4].textContent.trim()}</p>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-700 mb-2">Inscription</h4>
                            <p><strong>Date d'inscription:</strong> ${cells[5].textContent.trim()}</p>
                            <p><strong>Statut:</strong> ${cells[6].textContent.trim()}</p>
                        </div>
                    </div>
                `;
                document.getElementById('detailsContent').innerHTML = detailsHTML;
                document.getElementById('detailsModal').classList.remove('hidden');
            }
        }

        function hideDetailsModal() {
            document.getElementById('detailsModal').classList.add('hidden');
        }
    </script>
</body>
</html>
