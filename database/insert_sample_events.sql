-- Insertion d'événements d'exemple pour la table evenements
-- Exécuter ce script dans votre base de données RJVC

INSERT INTO evenements (titre, description, date_debut, date_fin, lieu, categorie) VALUES
('Festival de la Joie 2024', 'Trois jours de louange, de musique et de partage. Un moment inoubliable pour célébrer notre foi ensemble.', '2024-08-24 09:00:00', '2024-08-26 21:00:00', 'Parc des Expositions, Paris', 'evenement'),

('Week-end Leadership', 'Une formation intensive pour les jeunes leaders et ceux qui aspirent à servir avec excellence.', '2024-09-15 10:00:00', '2024-09-15 18:00:00', 'Centre RJVC, Lyon', 'formation'),

('Convention Annuelle', 'Le point culminant de notre année. Un rassemblement national pour tous les membres du mouvement RJVC.', '2024-10-18 09:00:00', '2024-10-20 20:00:00', 'Palais des Congrès, Marseille', 'evenement'),

('Camp d\'été Jeunesse', 'Une semaine de retrouvailles, d\'activités et de croissance spirituelle au cœur de la nature.', '2024-07-15 08:00:00', '2024-07-20 22:00:00', 'Campagne du Beaujolais', 'evenement'),

('Mariage de Grâce & David', 'Une célébration d\'amour et de foi au sein de notre communauté.', '2024-06-15 15:00:00', '2024-06-15 23:00:00', 'Église Saint-Jean, Paris', 'evenement'),

('Baptême du petit Léo', 'Un moment de joie et de bénédiction pour notre communauté.', '2024-05-20 11:00:00', '2024-05-20 15:00:00', 'Église Notre-Dame, Lyon', 'evenement'),

('Gala de charité 2023', 'Une soirée mémorable pour une bonne cause.', '2023-12-10 19:00:00', '2023-12-10 23:00:00', 'Salle des Fêtes, Paris', 'social'),

('Fête de la communauté', 'Partage et fraternité au cœur de notre mission.', '2023-11-25 14:00:00', '2023-11-25 20:00:00', 'Centre RJVC, Bordeaux', 'social'),

('Journée de prière', 'Une journée consacrée à la prière et à la méditation.', '2024-09-01 09:00:00', '2024-09-01 17:00:00', 'Centre RJVC, Paris', 'service'),

('Atelier théâtre chrétien', 'Découvrez comment utiliser le théâtre pour partager votre foi.', '2024-08-10 14:00:00', '2024-08-10 18:00:00', 'Centre Culturel, Lille', 'formation'),

('Concert de Noël', 'Célébrons la naissance de Jésus avec musique et chants.', '2024-12-20 20:00:00', '2024-12-20 23:00:00', 'Église Centrale, Paris', 'evenement'),

('Session de formation biblique', 'Approfondissez votre connaissance des Écritures Saintes.', '2024-09-20 09:00:00', '2024-09-20 16:00:00', 'Centre RJVC, Marseille', 'formation');

-- Vérification des événements insérés
SELECT * FROM evenements ORDER BY date_debut DESC;
