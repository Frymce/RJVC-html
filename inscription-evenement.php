<!DOCTYPE html>
<html class="dark" lang="fr">
  <head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Organiser un événement - RJVC</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com" rel="preconnect" />
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect" />
    <link
      href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;700;800&display=swap"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
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
    <div class="relative flex min-h-screen w-full flex-col items-center overflow-x-hidden">
      <header class="w-full max-w-6xl px-4 py-5">
        <div class="flex items-center justify-between whitespace-nowrap border-b border-solid border-white/10 px-6 sm:px-10 py-3">
          <div class="flex items-center gap-4 text-white">
            <div class="w-[70px] h-[70px] bg-primary rounded-full flex items-center justify-center">
              <span class="text-2xl font-bold text-black">RJVC</span>
            </div>
            <span class="text-xl font-semibold">Organisation d'événements</span>
          </div>
          <a href="index.html" class="text-white hover:text-primary transition-colors">
            <i class="fas fa-home mr-2"></i>Accueil
          </a>
        </div>
      </header>

      <main class="flex flex-col items-center gap-8 px-4 py-8 w-full max-w-4xl">
        <div class="text-center">
          <h1 class="text-4xl font-bold text-white mb-4">Organiser votre événement</h1>
          <p class="text-lg text-white/80">Faites de RJVC votre partenaire pour vos événements spéciaux</p>
        </div>

        <?php if (isset($_SESSION['form_errors'])): ?>
          <div class="w-full bg-red-500/20 border border-red-500 rounded-lg p-4">
            <div class="text-red-200">
              <?php foreach ($_SESSION['form_errors'] as $error): ?>
                <p class="mb-2">• <?php echo htmlspecialchars($error); ?></p>
              <?php endforeach; ?>
            </div>
          </div>
          <?php unset($_SESSION['form_errors']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
          <div class="w-full bg-red-500/20 border border-red-500 rounded-lg p-4">
            <p class="text-red-200"><?php echo htmlspecialchars($_SESSION['error']); ?></p>
          </div>
          <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <form id="formulaire-evenement" method="POST" action="process_inscription_evenement.php" class="w-full bg-white/5 backdrop-blur-sm rounded-2xl p-8 border border-white/10">
          <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? bin2hex(random_bytes(32)); ?>">
          
          <!-- Informations personnelles -->
          <div class="mb-8">
            <h2 class="text-2xl font-semibold text-white mb-6">Informations personnelles</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div class="flex flex-col">
                <label class="pb-2 text-base font-medium text-white">Nom *</label>
                <input type="text" name="nom" required 
                  class="form-input flex h-14 w-full rounded-md border border-white/20 bg-white/5 p-[15px] text-white placeholder:text-white/40 focus:border-primary focus:outline-0 focus:ring-2 focus:ring-primary/40"
                  value="<?php echo htmlspecialchars($_SESSION['form_data']['nom'] ?? ''); ?>">
              </div>
              <div class="flex flex-col">
                <label class="pb-2 text-base font-medium text-white">Prénom *</label>
                <input type="text" name="prenom" required
                  class="form-input flex h-14 w-full rounded-md border border-white/20 bg-white/5 p-[15px] text-white placeholder:text-white/40 focus:border-primary focus:outline-0 focus:ring-2 focus:ring-primary/40"
                  value="<?php echo htmlspecialchars($_SESSION['form_data']['prenom'] ?? ''); ?>">
              </div>
              <div class="flex flex-col">
                <label class="pb-2 text-base font-medium text-white">Email *</label>
                <input type="email" name="email" required
                  class="form-input flex h-14 w-full rounded-md border border-white/20 bg-white/5 p-[15px] text-white placeholder:text-white/40 focus:border-primary focus:outline-0 focus:ring-2 focus:ring-primary/40"
                  value="<?php echo htmlspecialchars($_SESSION['form_data']['email'] ?? ''); ?>">
              </div>
              <div class="flex flex-col">
                <label class="pb-2 text-base font-medium text-white">Téléphone *</label>
                <input type="tel" name="telephone" required
                  class="form-input flex h-14 w-full rounded-md border border-white/20 bg-white/5 p-[15px] text-white placeholder:text-white/40 focus:border-primary focus:outline-0 focus:ring-2 focus:ring-primary/40"
                  value="<?php echo htmlspecialchars($_SESSION['form_data']['telephone'] ?? ''); ?>">
              </div>
            </div>
          </div>

          <!-- Détails de l'événement -->
          <div class="mb-8">
            <h2 class="text-2xl font-semibold text-white mb-6">Détails de l'événement</h2>
            <div class="space-y-6">
              <div class="flex flex-col">
                <label class="pb-2 text-base font-medium text-white">Type d'événement *</label>
                <select name="type_evenement" required
                  class="form-select flex h-14 w-full rounded-md border border-white/20 bg-white/5 p-[15px] text-white focus:border-primary focus:outline-0 focus:ring-2 focus:ring-primary/40 appearance-none">
                  <option value="">Choisir le type d'événement</option>
                  <option value="mariage" <?php echo (($_SESSION['form_data']['type_evenement'] ?? '') === 'mariage') ? 'selected' : ''; ?>>Mariage</option>
                  <option value="bapteme" <?php echo (($_SESSION['form_data']['type_evenement'] ?? '') === 'bapteme') ? 'selected' : ''; ?>>Baptême</option>
                  <option value="anniversaire" <?php echo (($_SESSION['form_data']['type_evenement'] ?? '') === 'anniversaire') ? 'selected' : ''; ?>>Anniversaire</option>
                  <option value="conference" <?php echo (($_SESSION['form_data']['type_evenement'] ?? '') === 'conference') ? 'selected' : ''; ?>>Conférence/Séminaire</option>
                  <option value="retraite" <?php echo (($_SESSION['form_data']['type_evenement'] ?? '') === 'retraite') ? 'selected' : ''; ?>>Retraite Spirituelle</option>
                  <option value="concert" <?php echo (($_SESSION['form_data']['type_evenement'] ?? '') === 'concert') ? 'selected' : ''; ?>>Concert/Louange</option>
                </select>
              </div>
              <div class="flex flex-col">
                <label class="pb-2 text-base font-medium text-white">Date prévue de l'événement</label>
                <input type="date" name="date_prevue"
                  class="form-input flex h-14 w-full rounded-md border border-white/20 bg-white/5 p-[15px] text-white placeholder:text-white/40 focus:border-primary focus:outline-0 focus:ring-2 focus:ring-primary/40"
                  value="<?php echo htmlspecialchars($_SESSION['form_data']['date_prevue'] ?? ''); ?>">
              </div>
              <div class="flex flex-col">
                <label class="pb-2 text-base font-medium text-white">Lieu prévu</label>
                <input type="text" name="lieu_prevu" placeholder="Adresse ou nom du lieu"
                  class="form-input flex h-14 w-full rounded-md border border-white/20 bg-white/5 p-[15px] text-white placeholder:text-white/40 focus:border-primary focus:outline-0 focus:ring-2 focus:ring-primary/40"
                  value="<?php echo htmlspecialchars($_SESSION['form_data']['lieu_prevu'] ?? ''); ?>">
              </div>
              <div class="flex flex-col">
                <label class="pb-2 text-base font-medium text-white">Nombre de participants estimé</label>
                <input type="number" name="nombre_participants_estime" min="1" placeholder="Ex: 50"
                  class="form-input flex h-14 w-full rounded-md border border-white/20 bg-white/5 p-[15px] text-white placeholder:text-white/40 focus:border-primary focus:outline-0 focus:ring-2 focus:ring-primary/40"
                  value="<?php echo htmlspecialchars($_SESSION['form_data']['nombre_participants_estime'] ?? ''); ?>">
              </div>
              <div class="flex flex-col">
                <label class="pb-2 text-base font-medium text-white">Budget estimé (FCFA)</label>
                <input type="number" name="budget_estime" min="0" step="0.01" placeholder="Ex: 100000"
                  class="form-input flex h-14 w-full rounded-md border border-white/20 bg-white/5 p-[15px] text-white placeholder:text-white/40 focus:border-primary focus:outline-0 focus:ring-2 focus:ring-primary/40"
                  value="<?php echo htmlspecialchars($_SESSION['form_data']['budget_estime'] ?? ''); ?>">
              </div>
              <div class="flex flex-col">
                <label class="pb-2 text-base font-medium text-white">Besoins spécifiques</label>
                <textarea name="besoins_specifiques" rows="4" placeholder="Décrivez vos besoins spécifiques (décoration, sonorisation, catering, etc.)"
                  class="form-input flex w-full rounded-md border border-white/20 bg-white/5 p-[15px] text-white placeholder:text-white/40 focus:border-primary focus:outline-0 focus:ring-2 focus:ring-primary/40"><?php echo htmlspecialchars($_SESSION['form_data']['besoins_specifiques'] ?? ''); ?></textarea>
              </div>
            </div>
          </div>

          <!-- Adresse -->
          <div class="mb-8">
            <h2 class="text-2xl font-semibold text-white mb-6">Adresse</h2>
            <div class="space-y-6">
              <div class="flex flex-col">
                <label class="pb-2 text-base font-medium text-white">Adresse</label>
                <input type="text" name="adresse"
                  class="form-input flex h-14 w-full rounded-md border border-white/20 bg-white/5 p-[15px] text-white placeholder:text-white/40 focus:border-primary focus:outline-0 focus:ring-2 focus:ring-primary/40"
                  value="<?php echo htmlspecialchars($_SESSION['form_data']['adresse'] ?? ''); ?>">
              </div>
              <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="flex flex-col">
                  <label class="pb-2 text-base font-medium text-white">Code postal</label>
                  <input type="text" name="code_postal"
                    class="form-input flex h-14 w-full rounded-md border border-white/20 bg-white/5 p-[15px] text-white placeholder:text-white/40 focus:border-primary focus:outline-0 focus:ring-2 focus:ring-primary/40"
                    value="<?php echo htmlspecialchars($_SESSION['form_data']['code_postal'] ?? ''); ?>">
                </div>
                <div class="flex flex-col">
                  <label class="pb-2 text-base font-medium text-white">Ville</label>
                  <input type="text" name="ville"
                    class="form-input flex h-14 w-full rounded-md border border-white/20 bg-white/5 p-[15px] text-white placeholder:text-white/40 focus:border-primary focus:outline-0 focus:ring-2 focus:ring-primary/40"
                    value="<?php echo htmlspecialchars($_SESSION['form_data']['ville'] ?? ''); ?>">
                </div>
                <div class="flex flex-col">
                  <label class="pb-2 text-base font-medium text-white">Pays</label>
                  <input type="text" name="pays" value="Côte d'Ivoire"
                    class="form-input flex h-14 w-full rounded-md border border-white/20 bg-white/5 p-[15px] text-white placeholder:text-white/40 focus:border-primary focus:outline-0 focus:ring-2 focus:ring-primary/40">
                </div>
              </div>
            </div>
          </div>

          <button type="submit" 
            class="w-full bg-primary text-black font-semibold py-4 px-8 rounded-lg hover:bg-yellow-400 transition-colors duration-200 transform hover:scale-[1.02]">
            <i class="fas fa-calendar-alt mr-2"></i>
            Soumettre la demande d'organisation
          </button>
        </form>

        <div class="text-center">
          <a href="rejoindre.php" class="text-white/80 hover:text-primary transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>Retour aux choix d'inscriptions
          </a>
        </div>
      </main>
    </div>
  </body>
</html>
<?php unset($_SESSION['form_data']); ?>
