-- =====================================================================
--  Gestion-don — Orphelinat du Centre YƐtƐn
--  Schéma de la base de données (à importer dans phpMyAdmin)
--
--  * Sans danger sur une base existante : chaque table n'est créée que
--    si elle n'existe pas déjà (CREATE TABLE IF NOT EXISTS).
--  * Les noms de tables / colonnes sont ceux utilisés par le code PHP
--    d'origine (Donateur, Dons, Besoins, repartition, encouragement).
-- =====================================================================

SET NAMES utf8mb4;
CREATE DATABASE IF NOT EXISTS gestion_dons CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE gestion_dons;

-- Donateurs : identifiés par leur numéro de téléphone (chiffres uniquement)
CREATE TABLE IF NOT EXISTS Donateur (
    id       INT AUTO_INCREMENT PRIMARY KEY,
    nom      VARCHAR(80)  NOT NULL,
    prenom   VARCHAR(80)  NOT NULL,
    sexe     VARCHAR(10)  NOT NULL,
    numero   VARCHAR(20)  NOT NULL,
    UNIQUE KEY uq_donateur_numero (numero)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dons : montant = 0 pour les dons en nature (alimentaire, vestimentaire, médical)
CREATE TABLE IF NOT EXISTS Dons (
    id_don      INT AUTO_INCREMENT PRIMARY KEY,
    montant     DECIMAL(12,2) NOT NULL DEFAULT 0,
    type_don    VARCHAR(20)   NOT NULL,
    date_don    DATE          NOT NULL,
    id_donateur INT           NOT NULL,
    detail_don  TEXT          NULL,
    KEY idx_dons_donateur (id_donateur),
    KEY idx_dons_date (date_don),
    CONSTRAINT fk_dons_donateur FOREIGN KEY (id_donateur) REFERENCES Donateur(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Besoins publiés par l'orphelinat
CREATE TABLE IF NOT EXISTS Besoins (
    id_besoin         INT AUTO_INCREMENT PRIMARY KEY,
    type_besoin       VARCHAR(30)  NOT NULL,
    description       TEXT         NOT NULL,
    priorite          VARCHAR(10)  NOT NULL DEFAULT 'moyenne',
    date_requete      DATE         NOT NULL,
    montant_estimatif DECIMAL(12,2) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Répartition : à quoi a servi chaque don (colonne USA = « usage », nom conservé)
CREATE TABLE IF NOT EXISTS repartition (
    id_repartition   INT AUTO_INCREMENT PRIMARY KEY,
    id_don           INT           NOT NULL,
    USA              VARCHAR(255)  NOT NULL,
    montant_utilise  DECIMAL(12,2) NOT NULL DEFAULT 0,
    date_utilisation DATE          NOT NULL,
    KEY idx_rep_don (id_don),
    CONSTRAINT fk_rep_don FOREIGN KEY (id_don) REFERENCES Dons(id_don)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Messages d'encouragement envoyés depuis la page Contact
CREATE TABLE IF NOT EXISTS encouragement (
    id_encouragement INT AUTO_INCREMENT PRIMARY KEY,
    message          TEXT        NOT NULL,
    frequence        VARCHAR(20) NOT NULL DEFAULT 'unique',
    date_envoi       DATETIME    NOT NULL,
    id_donateur      INT         NULL,
    KEY idx_enc_donateur (id_donateur),
    CONSTRAINT fk_enc_donateur FOREIGN KEY (id_donateur) REFERENCES Donateur(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
