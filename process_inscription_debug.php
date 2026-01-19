<?php
// Démarrer la session
session_start();

// Activer l'affichage des erreurs
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Début du traitement...<br>";

try {
    // Inclure la configuration de la base de données
    require_once 'dbconfig.php';
    echo "Connexion BDD OK<br>";
    
    // Vérifier la méthode
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        echo "Méthode POST détectée<br>";
        
        // Vérifier le jeton CSRF
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
            echo "Erreur CSRF<br>";
            header('Location: rejoindre.php?error=Token de sécurité invalide.');
            exit;
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
        $commentaires = trim($_POST['commentaires'] ?? '');
        
        // Récupérer les champs optionnels
        $type_inscription = $_POST['type-inscription'] ?? '';
        $choix_formation = $_POST['choix-formation'] ?? '';
        $type_evenement = $_POST['type-evenement'] ?? '';
        $date_evenement = $_POST['date-evenement'] ?? '';
        $nb_participants = $_POST['nb-participants'] ?? '';
        
        // Récupérer les intérêts (checkboxes)
        $interets = $_POST['interets'] ?? [];
        $interets_str = is_array($interets) ? implode(', ', $interets) : $interets;
        
        echo "Données récupérées :<br>";
        echo "Nom: $nom<br>";
        echo "Prénom: $prenom<br>";
        echo "Email: $email<br>";
        echo "Téléphone: $telephone<br>";
        echo "Date naissance: $date_naissance<br>";
        echo "Genre: $genre<br>";
        echo "Type inscription: $type_inscription<br>";
        echo "Formation: $choix_formation<br>";
        echo "Type événement: $type_evenement<br>";
        echo "Date événement: $date_evenement<br>";
        echo "Nb participants: $nb_participants<br>";
        echo "Intérêts: $interets_str<br>";
        
        // Validation simple
        $errors = [];
        if (empty($nom)) $errors[] = "Le nom est obligatoire.";
        if (empty($prenom)) $errors[] = "Le prénom est obligatoire.";
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "L'email n'est pas valide.";
        
        if (empty($errors)) {
            echo "Validation OK<br>";
            
            // Vérifier si l'email existe déjà
            $stmt = $pdo->prepare("SELECT id FROM inscriptions WHERE email = ?");
            $stmt->execute([$email]);
            
            if ($stmt->fetch()) {
                echo "Email déjà existant<br>";
                $errors[] = "Cette adresse email est déjà utilisée.";
            } else {
                echo "Email unique, insertion...<br>";
                
                // Insertion simple avec les colonnes de base
                $sql = "INSERT INTO inscriptions (nom, prenom, email, telephone, date_naissance, genre, adresse, commentaires, statut) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'en_attente')";
                
                $stmt = $pdo->prepare($sql);
                $result = $stmt->execute([$nom, $prenom, $email, $telephone, $date_naissance, $genre, $adresse, $commentaires]);
                
                if ($result) {
                    echo "Insertion réussie !<br>";
                    header('Location: confirmation-inscription.html');
                    exit;
                } else {
                    echo "Échec de l'insertion<br>";
                    print_r($stmt->errorInfo());
                }
            }
        } else {
            echo "Erreurs de validation : " . implode(', ', $errors) . "<br>";
        }
    } else {
        echo "Méthode non POST<br>";
    }
    
} catch (PDOException $e) {
    echo "Erreur PDO : " . $e->getMessage() . "<br>";
    echo "Code d'erreur : " . $e->getCode() . "<br>";
} catch (Exception $e) {
    echo "Erreur générale : " . $e->getMessage() . "<br>";
}

echo "Fin du script";
?>
