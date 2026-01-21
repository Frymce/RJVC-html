# RJVC - Site Web de l'Association

Site web officiel du RJVC (Rassemblement de la Jeunesse Vivante Chrétienne) avec système d'administration complet.

## 🌐 Fonctionnalités

### Pour les visiteurs
- **Page d'accueil** : Présentation de l'association et des événements à venir
- **Inscription en ligne** : Formulaire complet pour rejoindre l'association
- **Contact** : Formulaire de contact et informations
- **À propos** : Présentation détaillée de l'association

### Pour les administrateurs
- **Gestion des événements** : Création, modification, suppression d'événements
- **Gestion des inscriptions** : Validation et suivi des inscriptions
- **Gestion des administrateurs** : Création et gestion des comptes admin (Super Admin uniquement)
- **Upload d'images** : Importation d'images depuis la galerie pour les événements

## 🚀 Installation

### Prérequis
- PHP 7.4 ou supérieur
- MySQL 5.7 ou supérieur
- Apache/Nginx
- Extension PHP GD pour le traitement des images

### Configuration

1. **Cloner le dépôt**
   ```bash
   git clone https://github.com/Frymce/RJVC-html.git
   cd RJVC-html
   ```

2. **Base de données**
   - Importer le fichier `database/rjvc_database.sql` dans votre base de données MySQL
   - Configurer les accès dans `dbconfig.php`

3. **Permissions**
   ```bash
   chmod 755 uploads/
   chmod 644 uploads/*
   ```

4. **Configuration du serveur**
   - Assurez-vous que le module `mod_rewrite` est activé (Apache)
   - Configurez le `DocumentRoot` vers le répertoire du projet

## 🔐 Accès Administration

### Identifiants par défaut
- **URL** : `https://votre-site.com/admin.php`
- **Nom d'utilisateur** : `admin`
- **Mot de passe** : `admin123`

### Rôles et permissions

#### Super Admin
- Accès complet à toutes les fonctionnalités
- Gestion des administrateurs
- Création, modification, suppression d'événements
- Validation des inscriptions

#### Administrateur
- Gestion des événements
- Validation des inscriptions
- Pas d'accès à la gestion des administrateurs

#### Modérateur
- Validation des inscriptions uniquement
- Accès limité aux fonctionnalités de modération

## 📁 Structure du projet

```
RJVC-html/
├── admin.php                    # Point d'entrée administration
├── admin_evenements.php          # Gestion des événements
├── admin_inscriptions.php       # Gestion des inscriptions
├── admin_administrateurs.php    # Gestion des admins (Super Admin)
├── login.php                   # Connexion administration
├── logout.php                  # Déconnexion
├── index.html                  # Page d'accueil
├── about.html                  # Page à propos
├── contact.html                # Page contact
├── process_inscription.php      # Traitement formulaire inscription
├── dbconfig.php                # Configuration base de données
├── database/
│   └── rjvc_database.sql      # Structure de la base de données
├── uploads/                    # Images uploadées
└── assets/                     # Assets statiques
```

## 🎨 Technologies utilisées

- **Frontend** : HTML5, Tailwind CSS, Font Awesome
- **Backend** : PHP 7.4+
- **Base de données** : MySQL
- **Design** : Responsive design, Mobile-first

## 📱 Caractéristiques techniques

- **Responsive design** : Interface adaptative mobile/desktop
- **Menu hamburger** : Navigation optimisée pour mobile
- **Tableaux scrollables** : Affichage responsive des données
- **Upload d'images** : Gestion des fichiers avec validation
- **Sécurité** : Protection XSS, validation des entrées
- **Sessions** : Gestion sécurisée des connexions

## 🔧 Maintenance

### Mise à jour du mot de passe admin
Utiliser le script `fix_admin_password.php` pour réinitialiser le mot de passe administrateur.

### Sauvegarde
- Sauvegarder régulièrement la base de données
- Sauvegarder le dossier `uploads/` contenant les images

### Logs
Les erreurs PHP sont configurées pour s'afficher en développement. En production, ajuster `error_reporting` et `display_errors`.

## 🤝 Contribuer

1. Fork le projet
2. Créer une branche (`git checkout -b feature/nouvelle-fonctionnalite`)
3. Commiter les changements (`git commit -am 'Ajout nouvelle fonctionnalité'`)
4. Pusher la branche (`git push origin feature/nouvelle-fonctionnalite`)
5. Créer une Pull Request

## 📄 Licence

Ce projet est sous licence MIT. Voir le fichier `LICENSE` pour plus de détails.

## 📞 Support

Pour toute question ou problème :
- Contacter l'administrateur système
- Créer une issue sur GitHub
- Envoyer un email à l'adresse de contact

---

**RJVC** - Rassemblement de la Jeunesse Vivante Chrétienne  
*Développé avec ❤️ pour la communauté* 
