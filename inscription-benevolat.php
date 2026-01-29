<!DOCTYPE html>
<html class="dark" lang="fr">
  <head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Bénévolat RJVC</title>
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
            <span class="text-xl font-semibold">Devenir Bénévole</span>
          </div>
          <a href="index.html" class="text-white hover:text-primary transition-colors">
            <i class="fas fa-home mr-2"></i>Accueil
          </a>
        </div>
      </header>

      <main class="flex flex-col items-center gap-8 px-4 py-8 w-full max-w-4xl">
        <div class="text-center">
          <h1 class="text-4xl font-bold text-white mb-4">Devenez Bénévole RJVC</h1>
          <p class="text-lg text-white/80">Mettez vos talents au service de la communauté</p>
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

        <form id="formulaire-benevolat" method="POST" action="process_inscription_benevolat.php" class="w-full bg-white/5 backdrop-blur-sm rounded-2xl p-8 border border-white/10">
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
              <div class="flex flex-col">
                <label class="pb-2 text-base font-medium text-white">Date de naissance *</label>
                <input type="date" name="date_naissance" required
                  class="form-input flex h-14 w-full rounded-md border border-white/20 bg-white/5 p-[15px] text-white placeholder:text-white/40 focus:border-primary focus:outline-0 focus:ring-2 focus:ring-primary/40"
                  value="<?php echo htmlspecialchars($_SESSION['form_data']['date_naissance'] ?? ''); ?>">
              </div>
              <div class="flex flex-col">
                <label class="pb-2 text-base font-medium text-white">Genre *</label>
                <select name="genre" required
                  class="form-select flex h-14 w-full rounded-md border border-white/20 bg-white/5 p-[15px] text-white focus:border-primary focus:outline-0 focus:ring-2 focus:ring-primary/40 appearance-none">
                  <option value="">Sélectionner</option>
                  <option value="M" <?php echo (($_SESSION['form_data']['genre'] ?? '') === 'M') ? 'selected' : ''; ?>>Masculin</option>
                  <option value="F" <?php echo (($_SESSION['form_data']['genre'] ?? '') === 'F') ? 'selected' : ''; ?>>Féminin</option>
                  <option value="Autre" <?php echo (($_SESSION['form_data']['genre'] ?? '') === 'Autre') ? 'selected' : ''; ?>>Autre</option>
                </select>
              </div>
            </div>
          </div>

          <!-- Détails du bénévolat -->
          <div class="mb-8">
            <h2 class="text-2xl font-semibold text-white mb-6">Détails du bénévolat</h2>
            <div class="space-y-6">
              <div class="flex flex-col">
                <label class="pb-2 text-base font-medium text-white">Domaines de bénévolat *</label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                  <?php 
                  $domaines_options = [
                    'administration' => 'Administration',
                    'evenementiel' => 'Événementiel',
                    'communication' => 'Communication',
                    'enseignement' => 'Enseignement',
                    'musique' => 'Musique & Louange',
                    'technique' => 'Support Technique',
                    'accueil' => 'Accueil & Hôpitalité',
                    'jeunesse' => 'Ministère Jeunesse',
                    'social' => 'Actions Sociales',
                    'mission' => 'Missions & Évangélisation'
                  ];
                  $saved_domaines = $_SESSION['form_data']['domaines_benevolat'] ?? [];
                  foreach ($domaines_options as $value => $label): ?>
                  <label class="flex items-center gap-3 p-3 rounded-md border border-white/20 bg-white/5 hover:bg-white/10 transition-colors cursor-pointer">
                    <input type="checkbox" name="domaines_benevolat[]" value="<?php echo $value; ?>"
                      class="form-checkbox h-5 w-5 rounded border-white/30 bg-white/10 text-primary focus:ring-primary/50"
                      <?php echo (is_array($saved_domaines) && in_array($value, $saved_domaines)) ? 'checked' : ''; ?>>
                    <span class="text-sm text-white/80"><?php echo $label; ?></span>
                  </label>
                  <?php endforeach; ?>
                </div>
              </div>
              <div class="flex flex-col">
                <label class="pb-2 text-base font-medium text-white">Disponibilités hebdomadaires</label>
                <input type="text" name="disponibilites_hebdomadaires" placeholder="Ex: Samedi après-midi, 2 soirs par semaine..."
                  class="form-input flex h-14 w-full rounded-md border border-white/20 bg-white/5 p-[15px] text-white placeholder:text-white/40 focus:border-primary focus:outline-0 focus:ring-2 focus:ring-primary/40"
                  value="<?php echo htmlspecialchars($_SESSION['form_data']['disponibilites_hebdomadaires'] ?? ''); ?>">
              </div>
              <div class="flex flex-col">
                <label class="pb-2 text-base font-medium text-white">Expérience en bénévolat</label>
                <textarea name="experience_benevolat" rows="4" placeholder="Décrivez votre expérience précédente en bénévolat"
                  class="form-input flex w-full rounded-md border border-white/20 bg-white/5 p-[15px] text-white placeholder:text-white/40 focus:border-primary focus:outline-0 focus:ring-2 focus:ring-primary/40"><?php echo htmlspecialchars($_SESSION['form_data']['experience_benevolat'] ?? ''); ?></textarea>
              </div>
              <div class="flex flex-col">
                <label class="pb-2 text-base font-medium text-white">Compétences spécifiques</label>
                <textarea name="competences_specifiques" rows="4" placeholder="Compétences particulières que vous pouvez mettre au service (informatique, langues, artisanat, etc.)"
                  class="form-input flex w-full rounded-md border border-white/20 bg-white/5 p-[15px] text-white placeholder:text-white/40 focus:border-primary focus:outline-0 focus:ring-2 focus:ring-primary/40"><?php echo htmlspecialchars($_SESSION['form_data']['competences_specifiques'] ?? ''); ?></textarea>
              </div>
              <div class="flex flex-col">
                <label class="pb-2 text-base font-medium text-white">Durée d'engagement souhaitée</label>
                <select name="engagement_duree"
                  class="form-select flex h-14 w-full rounded-md border border-white/20 bg-white/5 p-[15px] text-white focus:border-primary focus:outline-0 focus:ring-2 focus:ring-primary/40 appearance-none">
                  <option value="ponctuel" <?php echo (($_SESSION['form_data']['engagement_duree'] ?? '') === 'ponctuel') ? 'selected' : ''; ?>>Ponctuel</option>
                  <option value="3_mois" <?php echo (($_SESSION['form_data']['engagement_duree'] ?? '') === '3_mois') ? 'selected' : ''; ?>>3 mois</option>
                  <option value="6_mois" <?php echo (($_SESSION['form_data']['engagement_duree'] ?? '') === '6_mois') ? 'selected' : ''; ?>>6 mois</option>
                  <option value="1_an" <?php echo (($_SESSION['form_data']['engagement_duree'] ?? '') === '1_an') ? 'selected' : ''; ?>>1 an</option>
                  <option value="indetermine" <?php echo (($_SESSION['form_data']['engagement_duree'] ?? '') === 'indetermine') ? 'selected' : ''; ?>>Indéterminé</option>
                </select>
              </div>
              <div class="flex flex-col">
                <label class="pb-2 text-base font-medium text-white">Motivations</label>
                <textarea name="motivations" rows="4" placeholder="Pourquoi voulez-vous devenir bénévole au RJVC ?"
                  class="form-input flex w-full rounded-md border border-white/20 bg-white/5 p-[15px] text-white placeholder:text-white/40 focus:border-primary focus:outline-0 focus:ring-2 focus:ring-primary/40"><?php echo htmlspecialchars($_SESSION['form_data']['motivations'] ?? ''); ?></textarea>
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
            <i class="fas fa-hands-helping mr-2"></i>
            Soumettre la candidature
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
