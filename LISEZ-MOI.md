# Gestion-don — Orphelinat du Centre YƐtƐn

Site de collecte et de suivi de dons (PHP / MySQL, sans framework).

## Installation sous WAMP (étape par étape)

1. Clone le dépôt ou copie le dossier du projet dans `C:\wamp64\www\`.
2. Copie `config.example.php` vers `config.php`, puis renseigne la base, le courriel administrateur et les informations du site.
3. Dans le dossier du projet, installe les dépendances PHP avec Composer : `composer install`.
4. **Démarre WAMP** : l'icône doit être **verte**.
5. Ouvre **phpMyAdmin** (`http://localhost/phpmyadmin`), puis onglet **Importer** ; choisis `sql/database.sql` et clique sur **Exécuter**.
   La base `gestion_don` et ses 5 tables sont créées.
6. *(Facultatif)* Importe aussi `sql/donnees_demo.sql` pour tester avec des données fictives. **Ne pas l'importer en production.**
7. Ouvre **http://localhost/gestion_dons/**
8. Espace admin : **http://localhost/gestion_dons/admin_login.php**

Si ton MySQL n'est pas en `root` sans mot de passe, modifie `DB_USER` / `DB_PASS` dans `config.php`.
Compatible PHP 7.2 et plus (testé en PHP 8.3).

## Compte administrateur

- E-mail : valeur de `ADMIN_EMAIL` dans `config.php`.
- Mot de passe : celui défini lors de la configuration; seul son hash est stocké.

Pour une nouvelle installation, définis `ADMIN_EMAIL`, puis génère un hash avec `php generer_hash.php "NouveauMotDePasse"` et colle-le dans `ADMIN_PASSWORD_HASH` de `config.php`.

Pour changer le mot de passe (PowerShell, dans le dossier du projet) :

    php generer_hash.php "NouveauMotDePasse"

puis colle le résultat dans `ADMIN_PASSWORD_HASH` (`config.php`).

## Ce qui a été fait

**Site public** : accueil animé avec statistiques réelles, parcours de don (nouveau / ancien donateur), choix du type de don en cartes,
page de remerciement + **reçu PDF téléchargeable et imprimable**, suivi des dons par numéro de téléphone (historique + utilisation), besoins filtrables
avec bouton « Contribuer », contact, activités (galerie + visionneuse + vidéos), politique de confidentialité.

**Administration** : tableau de bord (indicateurs, graphique 6 mois, dons par type), liste des dons avec recherche / filtres /
pagination / **export CSV**, donateurs, messages reçus, gestion complète des besoins et des répartitions.

**Règles métier** : une répartition ne peut pas dépasser le solde restant du don ; un don déjà réparti ne peut pas être supprimé ;
le montant n'est demandé que pour les dons financiers.

**Corrections de l'existant** : les tables et colonnes sont conservées (compatibles avec ta base actuelle) ; `database.sql` crée
ce qui manque sans rien écraser. Bugs corrigés : requêtes `delete_repartition` (mauvaises colonnes), liens vers pages inexistantes,
pages vides (`connexion`, `inscription`, `politique`…), statistiques de l'accueil jamais affichées, images `.avif` nommées `.jpg`
(converties en vrais JPEG), vidéo `.mp3` (c'était un MP4), chemins d'images cassés (`ativite…`), mot de passe admin en clair.

**Sécurité** : jeton CSRF sur tous les formulaires, suppressions en POST uniquement, protection contre les essais répétés
(connexion admin, recherche de numéro), session admin expirant après 2 h d'inactivité, échappement de toutes les sorties,
export CSV protégé contre l'injection de formules, dossiers `includes/` et `sql/` bloqués par `.htaccess`.

## À faire avant la mise en ligne

- Dans `config.php` : garder `APP_DEBUG` à `false`, créer un utilisateur MySQL dédié (pas `root`), et renseigner `SITE_PAYMENT_INFO`
  (numéro Mobile Money affiché après un don financier). **Le site enregistre les dons mais ne traite aucun paiement en ligne.**
- Faire valider `politique.php` par l'orphelinat (texte modèle).
- Servir le site en HTTPS.
- Retirer `sql/donnees_demo.sql` du serveur.

## Structure

    config.php            réglages (base, contacts, admin)
    includes/             fonctions, gabarits, formulaires partagés
    assets/css, assets/js style et animations (aucune librairie externe)
    assets/img, media     images converties + vidéos
    sql/                  database.sql, donnees_demo.sql
    *.php                 pages (public : index, faire_don, merci, besoins…  |  admin : admin_*, *_besoin, *_repartition)
