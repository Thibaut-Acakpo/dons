-- =====================================================================
--  DONNÉES DE DÉMONSTRATION (facultatif — à ne PAS importer en production)
--  Toutes les personnes et tous les numéros ci-dessous sont fictifs.
--  À importer APRÈS database.sql, sur une base vide.
-- =====================================================================
SET NAMES utf8mb4;
USE gestion_dons;

INSERT INTO Donateur (nom, prenom, sexe, numero) VALUES
 ('Démo', 'Awa',    'Femme',  '0100000001'),
 ('Démo', 'Koffi',  'Homme',  '0100000002'),
 ('Démo', 'Sènami', 'Femme',  '0100000003');

INSERT INTO Dons (montant, type_don, date_don, id_donateur, detail_don) VALUES
 (50000, 'Financier',     DATE_SUB(CURDATE(), INTERVAL 40 DAY), 1, ''),
 (25000, 'Financier',     DATE_SUB(CURDATE(), INTERVAL 20 DAY), 2, ''),
 (0,     'Alimentaire',   DATE_SUB(CURDATE(), INTERVAL 15 DAY), 2, '50 kg de riz et 10 cartons de lait en poudre'),
 (0,     'Vestimentaire', DATE_SUB(CURDATE(), INTERVAL 8 DAY),  3, 'Vêtements et chaussures pour enfants de 6 à 12 ans'),
 (10000, 'Financier',     DATE_SUB(CURDATE(), INTERVAL 2 DAY),  3, '');

INSERT INTO repartition (id_don, USA, montant_utilise, date_utilisation) VALUES
 (1, 'Achat de fournitures scolaires pour la rentrée', 30000, DATE_SUB(CURDATE(), INTERVAL 30 DAY)),
 (1, 'Frais de consultation médicale',                 12000, DATE_SUB(CURDATE(), INTERVAL 25 DAY)),
 (3, 'Repas de la semaine',                                0, DATE_SUB(CURDATE(), INTERVAL 10 DAY));

INSERT INTO Besoins (type_besoin, description, priorite, date_requete, montant_estimatif) VALUES
 ('Alimentaire',   'Sacs de riz, huile et haricots pour le trimestre', 'haute',   CURDATE(), 150000),
 ('Médical',       'Moustiquaires imprégnées et trousse de premiers soins', 'haute', CURDATE(), 60000),
 ('Vestimentaire', 'Uniformes scolaires pour 15 enfants',               'moyenne', CURDATE(), 90000),
 ('Financier',     'Réparation de la toiture du dortoir',               'basse',   CURDATE(), 400000);

INSERT INTO encouragement (message, frequence, date_envoi, id_donateur) VALUES
 ('Courage à toute l''équipe, vous faites un travail magnifique !', 'unique', NOW(), 1);
