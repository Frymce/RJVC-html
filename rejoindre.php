<!DOCTYPE html>
<html class="dark" lang="fr">
  <head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Inscription Événements RJVC</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com" rel="preconnect" />
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect" />
    <link
      href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;700;800&amp;display=swap"
      rel="stylesheet"
    />
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
    </style>
    <script id="tailwind-config">
      tailwind.config = {
        darkMode: "class",
        theme: {
          extend: {
            colors: {
              primary: "#f9d406",
              "background-light": "#f8f8f5",
              "background-dark": "#23200f",
            },
            fontFamily: {
              display: ["Plus Jakarta Sans", "sans-serif"],
            },
            borderRadius: {
              DEFAULT: "1rem",
              lg: "2rem",
              xl: "3rem",
              full: "9999px",
            },
          },
        },
      };
    </script>
  </head>
  <body class="font-display bg-background-light dark:bg-background-dark">
    <div
      class="relative flex min-h-screen w-full flex-col items-center overflow-x-hidden"
    >
      <header class="w-full max-w-6xl px-4 py-5">
        <div
          class="flex items-center justify-between whitespace-nowrap border-b border-solid border-white/10 px-6 sm:px-10 py-3"
        >
          <div class="flex items-center gap-4 text-white">
            <div class="h-6 w-6 text-primary">
              <svg
                fill="currentColor"
                viewbox="0 0 48 48"
                xmlns="http://www.w3.org/2000/svg"
              >
                <path
                  d="M4 4H17.3334V17.3334H30.6666V30.6666H44V44H4V4Z"
                ></path>
              </svg>
            </div>
            <h2 class="text-white text-lg font-bold tracking-[-0.015em]">
              RJVC
            </h2>
          </div>
          <div class="flex items-center gap-9">
            <a
              class="text-white text-sm font-medium transition-colors hover:text-primary"
              href="./index.html"
              >Retour à l'accueil</a
            >
          </div>
        </div>
      </header>
      <main
        class="flex w-full max-w-6xl flex-1 flex-col items-center justify-center px-4 py-10 sm:py-20"
      >
        <div class="flex w-full max-w-xl flex-col items-center gap-8">
          <div class="flex flex-col gap-3 text-center">
            <h1
              class="text-white text-4xl font-black leading-tight tracking-[-0.033em] md:text-5xl"
            >
              Inscription aux Activités RJVC
            </h1>
            <p class="text-white/70 text-base font-normal leading-normal">
              Rejoignez-nous pour participer à nos événements, formations et
              activités communautaires.
            </p>
          </div>
          <?php
          // Démarrer la session pour le jeton CSRF
          session_start();
          
          // Générer un jeton CSRF s'il n'existe pas
          if (empty($_SESSION['csrf_token'])) {
              $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
          }
          $csrf_token = $_SESSION['csrf_token'];
          
          // Récupérer les données du formulaire en cas d'erreur
          $form_data = $_SESSION['form_data'] ?? [];
          $form_errors = $_SESSION['form_errors'] ?? [];
          
          // Effacer les messages d'erreur après les avoir affichés
          unset($_SESSION['form_errors'], $_SESSION['form_data']);
          ?>
          
          <?php if (!empty($form_errors)): ?>
            <div class="bg-red-900/50 border border-red-700 text-red-100 px-6 py-4 rounded-md mb-6">
              <p class="font-bold mb-2">Veuillez corriger les erreurs suivantes :</p>
              <ul class="list-disc pl-5 space-y-1">
                <?php foreach ($form_errors as $error): ?>
                  <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
              </ul>
            </div>
          <?php endif; ?>
          
          <form action="process_inscription_simple.php" method="POST" class="flex w-full flex-col gap-6" id="inscription-form">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
              <label class="flex flex-col">
                <p class="pb-2 text-base font-medium leading-normal text-white">
                  Prénom *
                </p>
                <input
                  name="prenom"
                  class="form-input flex h-14 w-full min-w-0 flex-1 resize-none overflow-hidden rounded-md border border-white/20 bg-white/5 p-[15px] text-base font-normal leading-normal text-white placeholder:text-white/40 focus:border-primary focus:outline-0 focus:ring-2 focus:ring-primary/40"
                  placeholder="Entrez votre prénom"
                  type="text"
                  required
                  value="<?php echo htmlspecialchars($form_data['prenom'] ?? ''); ?>"
                />
              </label>
              <label class="flex flex-col">
                <p class="pb-2 text-base font-medium leading-normal text-white">
                  Nom *
                </p>
                <input
                  name="nom"
                  class="form-input flex h-14 w-full min-w-0 flex-1 resize-none overflow-hidden rounded-md border border-white/20 bg-white/5 p-[15px] text-base font-normal leading-normal text-white placeholder:text-white/40 focus:border-primary focus:outline-0 focus:ring-2 focus:ring-primary/40"
                  placeholder="Entrez votre nom"
                  type="text"
                  required
                  value="<?php echo htmlspecialchars($form_data['nom'] ?? ''); ?>"
                />
              </label>
            </div>
            <label class="flex flex-col">
              <p class="pb-2 text-base font-medium leading-normal text-white">
                Adresse e-mail *
              </p>
              <input
                name="email"
                class="form-input flex h-14 w-full min-w-0 flex-1 resize-none overflow-hidden rounded-md border border-white/20 bg-white/5 p-[15px] text-base font-normal leading-normal text-white placeholder:text-white/40 focus:border-primary focus:outline-0 focus:ring-2 focus:ring-primary/40"
                placeholder="votre.email@exemple.com"
                type="email"
                required
                value="<?php echo htmlspecialchars($form_data['email'] ?? ''); ?>"
              />
            </label>
            <label class="flex flex-col">
              <p class="pb-2 text-base font-medium leading-normal text-white">
                Numéro de téléphone *
              </p>
              <input
                name="telephone"
                class="form-input flex h-14 w-full min-w-0 flex-1 resize-none overflow-hidden rounded-md border border-white/20 bg-white/5 p-[15px] text-base font-normal leading-normal text-white placeholder:text-white/40 focus:border-primary focus:outline-0 focus:ring-2 focus:ring-primary/40"
                placeholder="+225 01 23 45 67 89"
                type="tel"
                required
                value="<?php echo htmlspecialchars($form_data['telephone'] ?? ''); ?>"
              />
            </label>

            <!-- Sélecteur de type d'inscription -->
            <!-- Date de naissance -->
            <label class="flex flex-col">
              <p class="pb-2 text-base font-medium leading-normal text-white">
                Date de naissance *
              </p>
              <input
                name="date_naissance"
                type="date"
                class="form-input flex h-14 w-full min-w-0 flex-1 resize-none overflow-hidden rounded-md border border-white/20 bg-white/5 p-[15px] text-base font-normal leading-normal text-white placeholder:text-white/40 focus:border-primary focus:outline-0 focus:ring-2 focus:ring-primary/40"
                required
                value="<?php echo htmlspecialchars($form_data['date_naissance'] ?? ''); ?>"
              />
            </label>
            
            <!-- Genre -->
            <label class="flex flex-col">
              <p class="pb-2 text-base font-medium leading-normal text-white">
                Genre *
              </p>
              <div class="flex space-x-6">
                <label class="inline-flex items-center">
                  <input type="radio" name="genre" value="M" class="form-radio h-5 w-5 text-primary" required 
                    <?php echo (isset($form_data['genre']) && $form_data['genre'] === 'M') ? 'checked' : ''; ?>>
                  <span class="ml-2 text-white">Homme</span>
                </label>
                <label class="inline-flex items-center">
                  <input type="radio" name="genre" value="F" class="form-radio h-5 w-5 text-primary"
                    <?php echo (isset($form_data['genre']) && $form_data['genre'] === 'F') ? 'checked' : ''; ?>>
                  <span class="ml-2 text-white">Femme</span>
                </label>
                <label class="inline-flex items-center">
                  <input type="radio" name="genre" value="Autre" class="form-radio h-5 w-5 text-primary"
                    <?php echo (isset($form_data['genre']) && $form_data['genre'] === 'Autre') ? 'checked' : ''; ?>>
                  <span class="ml-2 text-white">Autre</span>
                </label>
              </div>
            </label>
            
            <!-- Adresse -->
            <label class="flex flex-col">
              <p class="pb-2 text-base font-medium leading-normal text-white">
                Adresse
              </p>
              <input
                name="adresse"
                class="form-input flex h-14 w-full min-w-0 flex-1 resize-none overflow-hidden rounded-md border border-white/20 bg-white/5 p-[15px] text-base font-normal leading-normal text-white placeholder:text-white/40 focus:border-primary focus:outline-0 focus:ring-2 focus:ring-primary/40"
                placeholder="Votre adresse"
                type="text"
                value="<?php echo htmlspecialchars($form_data['adresse'] ?? ''); ?>"
              />
            </label>
            
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
              <label class="flex flex-col">
                <p class="pb-2 text-base font-medium leading-normal text-white">
                  Code postal
                </p>
                <input
                  name="code_postal"
                  class="form-input flex h-14 w-full min-w-0 flex-1 resize-none overflow-hidden rounded-md border border-white/20 bg-white/5 p-[15px] text-base font-normal leading-normal text-white placeholder:text-white/40 focus:border-primary focus:outline-0 focus:ring-2 focus:ring-primary/40"
                  placeholder="Code postal"
                  type="text"
                  value="<?php echo htmlspecialchars($form_data['code_postal'] ?? ''); ?>"
                />
              </label>
              <label class="flex flex-col">
                <p class="pb-2 text-base font-medium leading-normal text-white">
                  Ville
                </p>
                <input
                  name="ville"
                  class="form-input flex h-14 w-full min-w-0 flex-1 resize-none overflow-hidden rounded-md border border-white/20 bg-white/5 p-[15px] text-base font-normal leading-normal text-white placeholder:text-white/40 focus:border-primary focus:outline-0 focus:ring-2 focus:ring-primary/40"
                  placeholder="Ville"
                  type="text"
                  value="<?php echo htmlspecialchars($form_data['ville'] ?? ''); ?>"
                />
              </label>
              <label class="flex flex-col">
                <p class="pb-2 text-base font-medium leading-normal text-white">
                  Pays
                </p>
                <input
                  name="pays"
                  class="form-input flex h-14 w-full min-w-0 flex-1 resize-none overflow-hidden rounded-md border border-white/20 bg-white/5 p-[15px] text-base font-normal leading-normal text-white placeholder:text-white/40 focus:border-primary focus:outline-0 focus:ring-2 focus:ring-primary/40"
                  placeholder="Pays"
                  type="text"
                  value="<?php echo htmlspecialchars($form_data['pays'] ?? 'Côte d\'Ivoire'); ?>"
                />
              </label>
            </div>
            
            <!-- Niveau d'études et école/entreprise -->
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
              <label class="flex flex-col">
                <p class="pb-2 text-base font-medium leading-normal text-white">
                  Niveau d'études
                </p>
                <select
                  name="niveau_etude"
                  class="form-select flex h-14 w-full min-w-0 flex-1 resize-none overflow-hidden rounded-md border border-white/20 bg-white/5 p-[15px] pr-12 text-base font-normal leading-normal text-white focus:border-primary focus:outline-0 focus:ring-2 focus:ring-primary/40 appearance-none"
                >
                  <option value="" disabled selected>Sélectionnez un niveau</option>
                  <option value="Secondaire" <?php echo (isset($form_data['niveau_etude']) && $form_data['niveau_etude'] === 'Secondaire') ? 'selected' : ''; ?>>Secondaire</option>
                  <option value="Bac" <?php echo (isset($form_data['niveau_etude']) && $form_data['niveau_etude'] === 'Bac') ? 'selected' : ''; ?>>Bac</option>
                  <option value="Bac+2" <?php echo (isset($form_data['niveau_etude']) && $form_data['niveau_etude'] === 'Bac+2') ? 'selected' : ''; ?>>Bac+2</option>
                  <option value="Licence" <?php echo (isset($form_data['niveau_etude']) && $form_data['niveau_etude'] === 'Licence') ? 'selected' : ''; ?>>Licence</option>
                  <option value="Master" <?php echo (isset($form_data['niveau_etude']) && $form_data['niveau_etude'] === 'Master') ? 'selected' : ''; ?>>Master</option>
                  <option value="Doctorat" <?php echo (isset($form_data['niveau_etude']) && $form_data['niveau_etude'] === 'Doctorat') ? 'selected' : ''; ?>>Doctorat</option>
                  <option value="Autre" <?php echo (isset($form_data['niveau_etude']) && $form_data['niveau_etude'] === 'Autre') ? 'selected' : ''; ?>>Autre</option>
                </select>
              </label>
              <label class="flex flex-col">
                <p class="pb-2 text-base font-medium leading-normal text-white">
                  École/Entreprise
                </p>
                <input
                  name="ecole_entreprise"
                  class="form-input flex h-14 w-full min-w-0 flex-1 resize-none overflow-hidden rounded-md border border-white/20 bg-white/5 p-[15px] text-base font-normal leading-normal text-white placeholder:text-white/40 focus:border-primary focus:outline-0 focus:ring-2 focus:ring-primary/40"
                  placeholder="Nom de l'école ou entreprise"
                  type="text"
                  value="<?php echo htmlspecialchars($form_data['ecole_entreprise'] ?? ''); ?>"
                />
              </label>
            </div>
            
            <!-- Type d'inscription -->
            <label class="flex flex-col">
              <p class="pb-2 text-base font-medium leading-normal text-white">
                Type d'inscription *
              </p>
              <div class="relative">
                <select
                  class="form-select flex h-14 w-full min-w-0 flex-1 resize-none overflow-hidden rounded-md border border-white/20 bg-white/5 p-[15px] pr-12 text-base font-normal leading-normal text-white focus:border-primary focus:outline-0 focus:ring-2 focus:ring-primary/40 appearance-none"
                  id="type-inscription"
                  name="type-inscription"
                  required
                >
                  <option value="" disabled selected>
                    Sélectionnez votre type d'inscription
                  </option>
                  <option value="formation">
                    Suivre une formation (RJVC Academy)
                  </option>
                  <option value="evenement">
                    Organiser un événement avec RJVC
                  </option>
                  <option value="participer">
                    Participer aux activités RJVC
                  </option>
                  <option value="mouvement">
                    Rejoindre le Mouvement Annuel RJVC
                  </option>
                  <option value="benevolat">Devenir bénévole RJVC</option>
                </select>
                <div
                  class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-white/60"
                >
                  <i class="fas fa-chevron-down text-xl"></i>
                </div>
              </div>
            </label>

            <!-- Section conditionnelle pour les formations -->
            <div id="formation-details" class="hidden flex-col gap-4">
              <label class="flex flex-col">
                <p class="pb-2 text-base font-medium leading-normal text-white">
                  Formation souhaitée *
                </p>
                <div class="relative">
                  <select
                    class="form-select flex h-14 w-full min-w-0 flex-1 resize-none overflow-hidden rounded-md border border-white/20 bg-white/5 p-[15px] pr-12 text-base font-normal leading-normal text-white focus:border-primary focus:outline-0 focus:ring-2 focus:ring-primary/40 appearance-none"
                    id="choix-formation"
                    name="choix-formation"
                  >
                    <option value="" disabled selected>
                      Choisissez une formation
                    </option>
                    <option value="developpement">Développement IT</option>
                    <option value="design">Design Graphique</option>
                    <option value="photo">Photographie</option>
                    <option value="video">Vidéographie</option>
                    <option value="leadership">Leadership Chrétien</option>
                    <option value="theologie">Théologie Pratique</option>
                  </select>
                  <div
                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-white/60"
                  >
                    <i class="fas fa-chevron-down text-xl"></i>
                  </div>
                </div>
              </label>
              <label class="flex flex-col">
                <p class="pb-2 text-base font-medium leading-normal text-white">
                  Niveau actuel
                </p>
                <input
                  class="form-input flex h-14 w-full min-w-0 flex-1 resize-none overflow-hidden rounded-md border border-white/20 bg-white/5 p-[15px] text-base font-normal leading-normal text-white placeholder:text-white/40 focus:border-primary focus:outline-0 focus:ring-2 focus:ring-primary/40"
                  placeholder="Débutant, Intermédiaire, Avancé"
                  type="text"
                />
              </label>
            </div>

            <!-- Section conditionnelle pour les événements -->
            <div id="evenement-details" class="hidden flex-col gap-4">
              <label class="flex flex-col">
                <p class="pb-2 text-base font-medium leading-normal text-white">
                  Type d'événement *
                </p>
                <div class="relative">
                  <select
                    class="form-select flex h-14 w-full min-w-0 flex-1 resize-none overflow-hidden rounded-md border border-white/20 bg-white/5 p-[15px] pr-12 text-base font-normal leading-normal text-white focus:border-primary focus:outline-0 focus:ring-2 focus:ring-primary/40 appearance-none"
                    id="type-evenement"
                    name="type-evenement"
                  >
                    <option value="" disabled selected>
                      Choisissez le type d'événement
                    </option>
                    <option value="mariage">Mariage</option>
                    <option value="bapteme">Baptême</option>
                    <option value="anniversaire">Anniversaire</option>
                    <option value="conference">Conférence/Séminaire</option>
                    <option value="retraite">Retraite Spirituelle</option>
                    <option value="concert">Concert/Louange</option>
                  </select>
                  <div
                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-white/60"
                  >
                    <i class="fas fa-chevron-down text-xl"></i>
                  </div>
                </div>
              </label>
              <label class="flex flex-col">
                <p class="pb-2 text-base font-medium leading-normal text-white">
                  Date prévue de l'événement
                </p>
                <input
                  class="form-input flex h-14 w-full min-w-0 flex-1 resize-none overflow-hidden rounded-md border border-white/20 bg-white/5 p-[15px] text-base font-normal leading-normal text-white placeholder:text-white/40 focus:border-primary focus:outline-0 focus:ring-2 focus:ring-primary/40"
                  placeholder="JJ/MM/AAAA"
                  type="date"
                  name="date-evenement"
                />
              </label>
              <label class="flex flex-col">
                <p class="pb-2 text-base font-medium leading-normal text-white">
                  Nombre de participants estimé
                </p>
                <input
                  class="form-input flex h-14 w-full min-w-0 flex-1 resize-none overflow-hidden rounded-md border border-white/20 bg-white/5 p-[15px] text-base font-normal leading-normal text-white placeholder:text-white/40 focus:border-primary focus:outline-0 focus:ring-2 focus:ring-primary/40"
                  placeholder="Ex: 50 personnes"
                  type="number"
                  min="1"
                  name="nb-participants"
                />
              </label>
            </div>

            <!-- Section conditionnelle pour la participation -->
            <div id="participation-details" class="hidden flex-col gap-4">
              <label class="flex flex-col">
                <p class="pb-2 text-base font-medium leading-normal text-white">
                  Intérêts principaux *
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                  <label
                    class="flex items-center gap-3 p-3 rounded-md border border-white/20 bg-white/5 hover:bg-white/10 transition-colors cursor-pointer"
                  >
                    <input
                      type="checkbox"
                      class="form-checkbox h-5 w-5 rounded border-white/30 bg-white/10 text-primary focus:ring-primary/50"
                      value="worship"
                      name="interets[]"
                    />
                    <span class="text-sm text-white/80"
                      >Louange & Adoration</span
                    >
                  </label>
                  <label
                    class="flex items-center gap-3 p-3 rounded-md border border-white/20 bg-white/5 hover:bg-white/10 transition-colors cursor-pointer"
                  >
                    <input
                      type="checkbox"
                      class="form-checkbox h-5 w-5 rounded border-white/30 bg-white/10 text-primary focus:ring-primary/50"
                      value="service"
                      name="interets[]"
                    />
                    <span class="text-sm text-white/80"
                      >Service Communautaire</span
                    >
                  </label>
                  <label
                    class="flex items-center gap-3 p-3 rounded-md border border-white/20 bg-white/5 hover:bg-white/10 transition-colors cursor-pointer"
                  >
                    <input
                      type="checkbox"
                      class="form-checkbox h-5 w-5 rounded border-white/30 bg-white/10 text-primary focus:ring-primary/50"
                      value="study"
                      name="interets[]"
                    />
                    <span class="text-sm text-white/80">Étude Biblique</span>
                  </label>
                  <label
                    class="flex items-center gap-3 p-3 rounded-md border border-white/20 bg-white/5 hover:bg-white/10 transition-colors cursor-pointer"
                  >
                    <input
                      type="checkbox"
                      class="form-checkbox h-5 w-5 rounded border-white/30 bg-white/10 text-primary focus:ring-primary/50"
                      value="social"
                      name="interets[]"
                    />
                    <span class="text-sm text-white/80"
                      >Activités Sociales</span
                    >
                  </label>
                  <label
                    class="flex items-center gap-3 p-3 rounded-md border border-white/20 bg-white/5 hover:bg-white/10 transition-colors cursor-pointer"
                  >
                    <input
                      type="checkbox"
                      class="form-checkbox h-5 w-5 rounded border-white/30 bg-white/10 text-primary focus:ring-primary/50"
                      value="missions"
                      name="interets[]"
                    />
                    <span class="text-sm text-white/80"
                      >Missions & Évangélisation</span
                    >
                  </label>
                  <label
                    class="flex items-center gap-3 p-3 rounded-md border border-white/20 bg-white/5 hover:bg-white/10 transition-colors cursor-pointer"
                  >
                    <input
                      type="checkbox"
                      class="form-checkbox h-5 w-5 rounded border-white/30 bg-white/10 text-primary focus:ring-primary/50"
                      value="youth"
                      name="interets[]"
                    />
                    <span class="text-sm text-white/80">Jeunesse & Sports</span>
                  </label>
                </div>
              </label>
              <label class="flex flex-col">
                <p class="pb-2 text-base font-medium leading-normal text-white">
                  Disponibilités
                </p>
                <input
                  class="form-input flex h-14 w-full min-w-0 flex-1 resize-none overflow-hidden rounded-md border border-white/20 bg-white/5 p-[15px] text-base font-normal leading-normal text-white placeholder:text-white/40 focus:border-primary focus:outline-0 focus:ring-2 focus:ring-primary/40"
                  placeholder="Ex: Weekends, Soirées en semaine..."
                  type="text"
                />
              </label>
            </div>

            <!-- Commentaires -->
            <label class="flex flex-col">
              <p class="pb-2 text-base font-medium leading-normal text-white">
                Commentaires ou questions
              </p>
              <textarea
                name="commentaires"
                class="form-textarea flex min-h-[120px] w-full min-w-0 flex-1 resize-none overflow-hidden rounded-md border border-white/20 bg-white/5 p-[15px] text-base font-normal leading-normal text-white placeholder:text-white/40 focus:border-primary focus:outline-0 focus:ring-2 focus:ring-primary/40"
                placeholder="Avez-vous des questions ou des commentaires ?"
              ><?php echo htmlspecialchars($form_data['commentaires'] ?? ''); ?></textarea>
            </label>

            <div class="flex flex-col gap-4 pt-2">
              <label class="flex cursor-pointer items-center gap-3">
                <input
                  class="form-checkbox h-5 w-5 rounded border-white/30 bg-white/10 text-primary focus:ring-primary/50 focus:ring-offset-background-dark"
                  type="checkbox"
                />
                <span class="text-sm text-white/80"
                  >Je souhaite m'inscrire à la newsletter pour recevoir les
                  actualités.</span
                >
              </label>
              <label class="flex cursor-pointer items-center gap-3">
                <input
                  class="form-checkbox h-5 w-5 rounded border-white/30 bg-white/10 text-primary focus:ring-primary/50 focus:ring-offset-background-dark"
                  required
                  type="checkbox"
                />
                <span class="text-sm text-white/80"
                  >J'accepte les
                  <a class="font-semibold text-primary underline" href="#"
                    >Termes et Conditions</a
                  >.</span
                >
              </label>
            </div>

            <div class="relative flex items-center py-2">
              <div class="flex-grow border-t border-white/20"></div>
              <!-- <span class="mx-4 flex-shrink text-sm text-white/60">OU</span> -->
              <div class="flex-grow border-t border-white/20"></div>
            </div>

            <button
              class="flex w-full items-center justify-center gap-2 rounded-md bg-primary px-6 py-4 text-base font-semibold leading-normal text-background-dark transition-colors hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-primary/40 focus:ring-offset-2 focus:ring-offset-background-dark"
              type="submit"
              name="submit_inscription"
            >
              <span>Envoyer ma demande d'inscription</span>
              <i class="fas fa-arrow-right text-xl"></i>
            </button>
          </form>
        </div>
      </main>
    </div>

    <script>
      // Fonction pour formater automatiquement le numéro de téléphone
      function formatPhoneNumber(input) {
        // Supprimer tous les caractères non numériques
        let value = input.value.replace(/\D/g, '');
        
        // Limiter à 10 chiffres (pour un numéro français)
        if (value.length > 10) {
          value = value.substring(0, 10);
        }
        
        // Ajouter des espaces pour la lisibilité
        if (value.length > 2) {
          value = value.substring(0, 2) + ' ' + value.substring(2);
        }
        if (value.length > 5) {
          value = value.substring(0, 5) + ' ' + value.substring(5);
        }
        if (value.length > 8) {
          value = value.substring(0, 8) + ' ' + value.substring(8);
        }
        
        // Mettre à jour la valeur du champ
        input.value = value.trim();
      }
      
      // Fonction pour formater automatiquement le code postal
      function formatPostalCode(input) {
        // Supprimer tous les caractères non numériques
        let value = input.value.replace(/\D/g, '');
        
        // Limiter à 5 chiffres (pour un code postal français)
        if (value.length > 5) {
          value = value.substring(0, 5);
        }
        
        // Mettre à jour la valeur du champ
        input.value = value;
      }
      
      // Ajouter les écouteurs d'événements
      document.addEventListener("DOMContentLoaded", function () {
        // Formater le numéro de téléphone
        const phoneInput = document.querySelector('input[name="telephone"]');
        if (phoneInput) {
          phoneInput.addEventListener('input', function() {
            formatPhoneNumber(this);
          });
        }
        
        // Formater le code postal
        const postalCodeInput = document.querySelector('input[name="code_postal"]');
        if (postalCodeInput) {
          postalCodeInput.addEventListener('input', function() {
            formatPostalCode(this);
          });
        }
        
        // Gestion des sections conditionnelles
        const typeInscription = document.getElementById("type-inscription");
        const formationDetails = document.getElementById("formation-details");
        const evenementDetails = document.getElementById("evenement-details");
        const participationDetails = document.getElementById(
          "participation-details"
        );
        const inscriptionForm = document.getElementById("inscription-form");

        // Fonction pour masquer toutes les sections conditionnelles
        function hideAllSections() {
          formationDetails.classList.add("hidden");
          evenementDetails.classList.add("hidden");
          participationDetails.classList.add("hidden");
        }

        // Afficher la section correspondante au choix
        typeInscription.addEventListener("change", function () {
          hideAllSections();

          switch (this.value) {
            case "formation":
              formationDetails.classList.remove("hidden");
              break;
            case "evenement":
              evenementDetails.classList.remove("hidden");
              break;
            case "participer":
            case "mouvement":
            case "benevolat":
              participationDetails.classList.remove("hidden");
              break;
          }
        });

        // Gestion de la soumission du formulaire
        inscriptionForm.addEventListener("submit", function (e) {
          // Ne pas empêcher la soumission normale pour l'instant
          // e.preventDefault();
          
          // Valider le formulaire avant envoi
          const formData = new FormData(inscriptionForm);
          
          // Afficher les données pour debug
          console.log("Données du formulaire:", Object.fromEntries(formData));
          
          // Le formulaire sera soumis normalement au serveur
          // Le PHP s'occupera du traitement
        });

        // Améliorer l'expérience des sélecteurs
        const selects = document.querySelectorAll(".form-select");
        selects.forEach((select) => {
          select.addEventListener("focus", function () {
            this.parentElement.classList.add("ring-2", "ring-primary/40");
          });

          select.addEventListener("blur", function () {
            this.parentElement.classList.remove("ring-2", "ring-primary/40");
          });
        });
      });
    </script>
    
  </body>
</html>
