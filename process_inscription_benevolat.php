<?php
// Démarrer la session
session_start();

// Inclure la configuration de la base de données
require_once 'dbconfig.php';

// Traitement du formulaire de bénévolat
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Vérifier le jeton CSRF
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
        $_SESSION['error'] = "Token de sécurité invalide.";
        header('Location: inscription-benevolat.php');
        exit;
    }
    
    // Récupérer et nettoyer les données principales
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $telephone = trim($_POST['telephone'] ?? '');
    $date_naissance = $_POST['date_naissance'] ?? '';
    $genre = $_POST['genre'] ?? '';
    $adresse = trim($_POST['adresse'] ?? '');
    $code_postal = trim($_POST['code_postal'] ?? '');
    $ville = trim($_POST['ville'] ?? '');
    $pays = trim($_POST['pays'] ?? 'Côte d\'Ivoire');
    
    // Récupérer les données spécifiques au bénévolat
    $domaines_benevolat = $_POST['domaines_benevolat'] ?? [];
    $disponibilites_hebdomadaires = trim($_POST['disponibilites_hebdomadaires'] ?? '');
    $experience_benevolat = trim($_POST['experience_benevolat'] ?? '');
    $competences_specifiques = trim($_POST['competences_specifiques'] ?? '');
    $engagement_duree = $_POST['engagement_duree'] ?? 'ponctuel';
    $motivations = trim($_POST['motivations'] ?? '');
    
    // Validation des données
    $errors = [];
    if (empty($nom)) $errors[] = "Le nom est obligatoire.";
    if (empty($prenom)) $errors[] = "Le prénom est obligatoire.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "L'email n'est pas valide.";
    if (empty($telephone)) $errors[] = "Le téléphone est obligatoire.";
    if (empty($date_naissance)) $errors[] = "La date de naissance est obligatoire.";
    if (empty($genre)) $errors[] = "Le genre est obligatoire.";
    if (empty($domaines_benevolat)) $errors[] = "Veuillez sélectionner au moins un domaine de bénévolat.";
    
    if (!empty($errors)) {
        $_SESSION['form_errors'] = $errors;
        $_SESSION['form_data'] = $_POST;
        header('Location: inscription-benevolat.php');
        exit;
    }
    
    try {
        // Vérifier si l'email existe déjà
        $stmt = $pdo->prepare("SELECT id FROM inscriptions WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->fetch()) {
            $_SESSION['error'] = "Cette adresse email est déjà utilisée.";
            $_SESSION['form_data'] = $_POST;
            header('Location: inscription-benevolat.php');
            exit;
        }
        
        // Démarrer une transaction
        $pdo->beginTransaction();
        
        // Inscription dans la table principale
        $sql = "INSERT INTO inscriptions (nom, prenom, email, telephone, date_naissance, genre, adresse, code_postal, ville, pays, type_inscription, statut) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'benevolat', 'en_attente')";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $nom, $prenom, $email, $telephone, $date_naissance, $genre,
            $adresse, $code_postal, $ville, $pays
        ]);
        
        // Récupérer l'ID de l'inscription principale
        $inscription_id = $pdo->lastInsertId();
        
        // Préparer les domaines de bénévolat pour le JSON
        $domaines_json = json_encode($domaines_benevolat);
        
        // Inscription dans la table spécifique du bénévolat
        $sql_benevolat = "INSERT INTO inscription_benevolat 
                          (inscription_id, domaines_benevolat, disponibilites_hebdomadaires, experience_benevolat, competences_specifiques, engagement_duree, motivations) 
                          VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmt_benevolat = $pdo->prepare($sql_benevolat);
        $stmt_benevolat->execute([
            $inscription_id, $domaines_json, $disponibilites_hebdomadaires, 
            $experience_benevolat, $competences_specifiques, $engagement_duree, $motivations
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
        header('Location: inscription-benevolat.php');
        exit;
    }
    
} else {
    // Redirection si accès direct au script
    header('Location: inscription-benevolat.php');
    exit;
}
?>
