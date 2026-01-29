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
        
            <!-- Choix du type d'inscription -->
            <div class="text-center mb-8">
              <h2 class="text-2xl font-semibold text-white mb-6">Choisissez votre type d'inscription</h2>
              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <a href="inscription-formation.php" 
                   class="bg-white/10 hover:bg-white/20 backdrop-blur-sm rounded-xl p-6 border border-white/20 transition-all duration-300 hover:scale-105 hover:shadow-xl">
                  <div class="text-center">
                    <i class="fas fa-graduation-cap text-4xl text-primary mb-4"></i>
                    <h3 class="text-xl font-semibold text-white mb-2">RJVC Academy</h3>
                    <p class="text-white/80 text-sm">Suivre une formation professionnelle</p>
                  </div>
                </a>
                
                <a href="inscription-evenement.php" 
                   class="bg-white/10 hover:bg-white/20 backdrop-blur-sm rounded-xl p-6 border border-white/20 transition-all duration-300 hover:scale-105 hover:shadow-xl">
                  <div class="text-center">
                    <i class="fas fa-calendar-alt text-4xl text-primary mb-4"></i>
                    <h3 class="text-xl font-semibold text-white mb-2">Organiser un événement</h3>
                    <p class="text-white/80 text-sm">Faites appel à RJVC pour vos événements</p>
                  </div>
                </a>
                
                <a href="inscription-participation.php" 
                   class="bg-white/10 hover:bg-white/20 backdrop-blur-sm rounded-xl p-6 border border-white/20 transition-all duration-300 hover:scale-105 hover:shadow-xl">
                  <div class="text-center">
                    <i class="fas fa-users text-4xl text-primary mb-4"></i>
                    <h3 class="text-xl font-semibold text-white mb-2">Participer aux activités</h3>
                    <p class="text-white/80 text-sm">Rejoignez nos activités communautaires</p>
                  </div>
                </a>
                
                <a href="inscription-mouvement.php" 
                   class="bg-white/10 hover:bg-white/20 backdrop-blur-sm rounded-xl p-6 border border-white/20 transition-all duration-300 hover:scale-105 hover:shadow-xl">
                  <div class="text-center">
                    <i class="fas fa-flag text-4xl text-primary mb-4"></i>
                    <h3 class="text-xl font-semibold text-white mb-2">Mouvement Annuel</h3>
                    <p class="text-white/80 text-sm">Engagez-vous pour une année complète</p>
                  </div>
                </a>
                
                <a href="inscription-benevolat.php" 
                   class="bg-white/10 hover:bg-white/20 backdrop-blur-sm rounded-xl p-6 border border-white/20 transition-all duration-300 hover:scale-105 hover:shadow-xl">
                  <div class="text-center">
                    <i class="fas fa-hands-helping text-4xl text-primary mb-4"></i>
                    <h3 class="text-xl font-semibold text-white mb-2">Devenir bénévole</h3>
                    <p class="text-white/80 text-sm">Mettez vos talents au service</p>
                  </div>
                </a>
              </div>
            </div>

        </div>
      </main>
    </div>

    <script>
      // Animation au chargement de la page
      document.addEventListener('DOMContentLoaded', function() {
        const cards = document.querySelectorAll('a[href*="inscription-"]');
        cards.forEach((card, index) => {
          card.style.opacity = '0';
          card.style.transform = 'translateY(20px)';
          setTimeout(() => {
            card.style.transition = 'all 0.6s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
          }, index * 100);
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
