<?php
// Démarrer la session
session_start();

// Inclure la configuration de la base de données
require_once 'dbconfig.php';

// Fonction pour nettoyer les entrées
function clean_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

// Vérification du jeton CSRF
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
        header('Location: contact.html?error=Token de sécurité invalide.');
        exit;
    }

    // Récupération et nettoyage des données
    $full_name = clean_input($_POST['full-name'] ?? '');
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $reason = clean_input($_POST['reason'] ?? '');
    $message = clean_input($_POST['message'] ?? '');

    // Validation des données
    $errors = [];

    if (empty($full_name)) $errors[] = "Le nom complet est obligatoire.";
    if (empty($email)) $errors[] = "L'adresse email est obligatoire.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "L'adresse email n'est pas valide.";
    if (empty($reason)) $errors[] = "La raison du contact est obligatoire.";
    if (empty($message)) $errors[] = "Le message est obligatoire.";
    if (strlen($message) < 10) $errors[] = "Le message doit contenir au moins 10 caractères.";
    if (strlen($message) > 2000) $errors[] = "Le message ne peut pas dépasser 2000 caractères.";

    // Si pas d'erreurs, on procède à l'insertion
    if (empty($errors)) {
        try {
            // Insertion dans la base de données
            $stmt = $pdo->prepare("
                INSERT INTO contacts (
                    full_name, email, reason, message
                ) VALUES (?, ?, ?, ?)
            ");
            
            $stmt->execute([$full_name, $email, $reason, $message]);

            // Envoyer un email de notification (à implémenter)
            // $this->sendContactNotification($full_name, $email, $reason, $message);

            // Redirection avec message de succès
            $_SESSION['contact_success'] = true;
            header('Location: contact.html?success=1');
            exit;
        } catch (PDOException $e) {
            error_log("Erreur lors de l'insertion en base de données : " . $e->getMessage());
            $errors[] = "Une erreur est survenue lors de l'envoi du message. Veuillez réessayer.";
        }
    }

    // Si on arrive ici, il y a eu des erreurs
    $_SESSION['form_errors'] = $errors;
    $_SESSION['form_data'] = $_POST;
    header('Location: contact.html#contact-form');
    exit;
} else {
    // Si la méthode n'est pas POST, on redirige
    header('Location: contact.html');
    exit;
}
?>
