<?php
// Démarrer la session
session_start();

// Activer l'affichage des erreurs
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Début du script process_inscription.php...<br>";

try {
    // Inclure la configuration de la base de données
    require_once 'dbconfig.php';
    echo "Connexion BDD OK<br>";
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        echo "Méthode POST détectée<br>";
        
        // Vérifier le jeton CSRF
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
            echo "Erreur CSRF<br>";
            die("Token CSRF invalide");
        }
        echo "Token CSRF OK<br>";
        
        // Récupérer les données de base
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
        $niveau_etude = trim($_POST['niveau_etude'] ?? '');
        $ecole_entreprise = trim($_POST['ecole_entreprise'] ?? '');
        $commentaires = trim($_POST['commentaires'] ?? '');
        
        echo "Données de base récupérées<br>";
        
        // Validation simple
        $errors = [];
        if (empty($nom)) $errors[] = "Le nom est obligatoire.";
        if (empty($prenom)) $errors[] = "Le prénom est obligatoire.";
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "L'email n'est pas valide.";
        if (empty($telephone)) $errors[] = "Le téléphone est obligatoire.";
        if (empty($date_naissance)) $errors[] = "La date de naissance est obligatoire.";
        if (empty($genre)) $errors[] = "Le genre est obligatoire.";
        
        if (!empty($errors)) {
            echo "Erreurs de validation : " . implode(', ', $errors) . "<br>";
            $_SESSION['form_errors'] = $errors;
            $_SESSION['form_data'] = $_POST;
            header('Location: rejoindre.php#formulaire-inscription');
            exit;
        }
        
        echo "Validation OK<br>";
        
        // Vérifier si l'email existe déjà
        $stmt = $pdo->prepare("SELECT id FROM inscriptions WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->fetch()) {
            echo "Email déjà existant<br>";
            $errors[] = "Cette adresse email est déjà utilisée.";
            $_SESSION['form_errors'] = $errors;
            $_SESSION['form_data'] = $_POST;
            header('Location: rejoindre.php#formulaire-inscription');
            exit;
        }
        
        echo "Email unique, tentative d'insertion...<br>";
        
        // Insertion avec seulement les colonnes de base qui existent sûrement
        $sql = "INSERT INTO inscriptions (nom, prenom, email, telephone, date_naissance, genre, adresse, code_postal, ville, pays, niveau_etude, ecole_entreprise, commentaires, statut) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'en_attente')";
        
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([
            $nom, $prenom, $email, $telephone, $date_naissance, $genre,
            $adresse, $code_postal, $ville, $pays, $niveau_etude, $ecole_entreprise, $commentaires
        ]);
        
        if ($result) {
            echo "Insertion réussie !<br>";
            $_SESSION['inscription_success'] = true;
            header('Location: confirmation-inscription.html');
            exit;
        } else {
            echo "Échec de l'insertion<br>";
            print_r($stmt->errorInfo());
        }
        
    } else {
        echo "Méthode non POST : " . $_SERVER['REQUEST_METHOD'] . "<br>";
    }
    
} catch (PDOException $e) {
    echo "Erreur PDO : " . $e->getMessage() . "<br>";
    echo "Code d'erreur : " . $e->getCode() . "<br>";
} catch (Exception $e) {
    echo "Erreur générale : " . $e->getMessage() . "<br>";
}

echo "Fin du script";
?>
