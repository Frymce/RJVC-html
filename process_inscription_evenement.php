<?php
// Démarrer la session
session_start();

// Inclure la configuration de la base de données
require_once 'dbconfig.php';

// Traitement du formulaire d'organisation d'événement
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Vérifier le jeton CSRF
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
        $_SESSION['error'] = "Token de sécurité invalide.";
        header('Location: inscription-evenement.php');
        exit;
    }
    
    // Récupérer et nettoyer les données principales
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $telephone = trim($_POST['telephone'] ?? '');
    $adresse = trim($_POST['adresse'] ?? '');
    $code_postal = trim($_POST['code_postal'] ?? '');
    $ville = trim($_POST['ville'] ?? '');
    $pays = trim($_POST['pays'] ?? 'Côte d\'Ivoire');
    
    // Récupérer les données spécifiques à l'événement
    $type_evenement = $_POST['type_evenement'] ?? '';
    $date_prevue = $_POST['date_prevue'] ?? '';
    $lieu_prevu = trim($_POST['lieu_prevu'] ?? '');
    $nombre_participants_estime = intval($_POST['nombre_participants_estime'] ?? 0);
    $budget_estime = floatval($_POST['budget_estime'] ?? 0);
    $besoins_specifiques = trim($_POST['besoins_specifiques'] ?? '');
    
    // Validation des données
    $errors = [];
    if (empty($nom)) $errors[] = "Le nom est obligatoire.";
    if (empty($prenom)) $errors[] = "Le prénom est obligatoire.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "L'email n'est pas valide.";
    if (empty($telephone)) $errors[] = "Le téléphone est obligatoire.";
    if (empty($type_evenement)) $errors[] = "Le type d'événement est obligatoire.";
    
    if (!empty($errors)) {
        $_SESSION['form_errors'] = $errors;
        $_SESSION['form_data'] = $_POST;
        header('Location: inscription-evenement.php');
        exit;
    }
    
    try {
        // Vérifier si l'email existe déjà
        $stmt = $pdo->prepare("SELECT id FROM inscriptions WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->fetch()) {
            $_SESSION['error'] = "Cette adresse email est déjà utilisée.";
            $_SESSION['form_data'] = $_POST;
            header('Location: inscription-evenement.php');
            exit;
        }
        
        // Démarrer une transaction
        $pdo->beginTransaction();
        
        // Inscription dans la table principale
        $sql = "INSERT INTO inscriptions (nom, prenom, email, telephone, adresse, code_postal, ville, pays, type_inscription, statut) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'evenement', 'en_attente')";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $nom, $prenom, $email, $telephone, $adresse, $code_postal, $ville, $pays
        ]);
        
        // Récupérer l'ID de l'inscription principale
        $inscription_id = $pdo->lastInsertId();
        
        // Inscription dans la table spécifique des organisations d'événements
        $sql_evenement = "INSERT INTO organisations_evenements 
                         (inscription_id, type_evenement, date_prevue, lieu_prevu, nombre_participants_estime, budget_estime, besoins_specifiques) 
                         VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmt_evenement = $pdo->prepare($sql_evenement);
        $stmt_evenement->execute([
            $inscription_id, $type_evenement, 
            !empty($date_prevue) ? $date_prevue : null,
            $lieu_prevu, 
            $nombre_participants_estime > 0 ? $nombre_participants_estime : null,
            $budget_estime > 0 ? $budget_estime : null,
            $besoins_specifiques
        ]);
        
        // Valider la transaction
        $pdo->commit();
        
        $_SESSION['inscription_success'] = true;
        header('Location: confirmation-inscription.html');
        exit;
        
    } catch (PDOException $e) {
        // Annuler la transaction en cas d'erreur
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        $_SESSION['error'] = "Erreur de base de données. Veuillez contacter l'administrateur.";
        header('Location: inscription-evenement.php');
        exit;
    }
    
} else {
    // Redirection si accès direct au script
    header('Location: inscription-evenement.php');
    exit;
}
?>
