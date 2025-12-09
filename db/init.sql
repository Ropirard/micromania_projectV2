-- Active: 1765183046017@@127.0.0.1@3360@admin
-- ============================================
-- MICROMANIA PROJECT - INIT DATABASE
-- ============================================
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;
-- ============================================
-- TABLE: users
-- ============================================
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `email` VARCHAR(150) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `firstname` VARCHAR(100) NOT NULL,
    `lastname` VARCHAR(100) NOT NULL,
    `created_at` DATETIME DEFAULT NULL,
    `is_active` TINYINT(1) DEFAULT 1,
    `role` VARCHAR(50) DEFAULT 'user',
    PRIMARY KEY (`id`),
    INDEX `idx_email` (`email`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
-- ============================================
-- TABLE: genres
-- ============================================
DROP TABLE IF EXISTS `genres`;
CREATE TABLE `genres` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
-- ============================================
-- TABLE: plateforms
-- ============================================
DROP TABLE IF EXISTS `plateforms`;
CREATE TABLE `plateforms` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
-- ============================================
-- TABLE: games
-- ============================================
DROP TABLE IF EXISTS `games`;
CREATE TABLE `games` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(100) NOT NULL,
    `description` TEXT NOT NULL,
    `price` DECIMAL(10, 2) NOT NULL,
    `stock` INT(11) NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
-- ============================================
-- TABLE: charts (panier)
-- ============================================
DROP TABLE IF EXISTS `charts`;
CREATE TABLE `charts` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `user_id` INT(11) NOT NULL,
    `status` VARCHAR(100) NOT NULL DEFAULT 'active',
    `delivery_status` VARCHAR(100) DEFAULT 'En cours de préparation',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `validated_at` DATETIME DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `fk_charts_user` (`user_id`),
    CONSTRAINT `fk_charts_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
-- ============================================
-- TABLE: media
-- ============================================
DROP TABLE IF EXISTS `media`;
CREATE TABLE `media` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `filename` VARCHAR(255) NOT NULL,
    `original_filename` VARCHAR(255) NOT NULL,
    `mime_type` VARCHAR(100) DEFAULT NULL,
    `size` INT(11) DEFAULT NULL,
    `path` VARCHAR(255) NOT NULL,
    `type` VARCHAR(50) DEFAULT NULL,
    `created_at` DATETIME DEFAULT NULL,
    `updated_at` DATETIME DEFAULT NULL,
    `game_id` INT(11) DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `fk_media_game` (`game_id`),
    CONSTRAINT `fk_media_game` FOREIGN KEY (`game_id`) REFERENCES `games` (`id`) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
-- ============================================
-- TABLE DE JOINTURE: games_genres (ManyToMany)
-- ============================================
DROP TABLE IF EXISTS `games_genres`;
CREATE TABLE `games_genres` (
    `game_id` INT(11) NOT NULL,
    `genre_id` INT(11) NOT NULL,
    PRIMARY KEY (`game_id`, `genre_id`),
    KEY `fk_games_genres_game` (`game_id`),
    KEY `fk_games_genres_genre` (`genre_id`),
    CONSTRAINT `fk_games_genres_game` FOREIGN KEY (`game_id`) REFERENCES `games` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_games_genres_genre` FOREIGN KEY (`genre_id`) REFERENCES `genres` (`id`) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
-- ============================================
-- TABLE DE JOINTURE: games_plateforms (ManyToMany)
-- ============================================
DROP TABLE IF EXISTS `games_plateforms`;
CREATE TABLE `games_plateforms` (
    `game_id` INT(11) NOT NULL,
    `plateform_id` INT(11) NOT NULL,
    PRIMARY KEY (`game_id`, `plateform_id`),
    KEY `fk_games_plateforms_game` (`game_id`),
    KEY `fk_games_plateforms_plateform` (`plateform_id`),
    CONSTRAINT `fk_games_plateforms_game` FOREIGN KEY (`game_id`) REFERENCES `games` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_games_plateforms_plateform` FOREIGN KEY (`plateform_id`) REFERENCES `plateforms` (`id`) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
-- ============================================
-- TABLE DE JOINTURE: charts_games (ManyToMany)
-- ============================================
DROP TABLE IF EXISTS `charts_games`;
CREATE TABLE `charts_games` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `chart_id` INT(11) NOT NULL,
    `game_id` INT(11) NOT NULL,
    PRIMARY KEY (`id`),
    KEY `fk_charts_games_chart` (`chart_id`),
    KEY `fk_charts_games_game` (`game_id`),
    CONSTRAINT `fk_charts_games_chart` FOREIGN KEY (`chart_id`) REFERENCES `charts` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_charts_games_game` FOREIGN KEY (`game_id`) REFERENCES `games` (`id`) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
-- ============================================
-- DONNÉES DE TEST
-- ============================================
-- Insertion de genres
INSERT INTO `genres` (`name`)
VALUES ('Action'),
    ('Aventure'),
    ('RPG'),
    ('Sport'),
    ('Course'),
    ('Simulation');
-- Insertion de plateformes
INSERT INTO `plateforms` (`name`)
VALUES ('PlayStation 5'),
    ('Xbox Series X'),
    ('Nintendo Switch'),
    ('PC'),
    ('PlayStation 4'),
    ('Xbox One');
-- Insertion de jeux exemples
INSERT INTO `games` (`title`, `description`, `price`, `stock`)
VALUES (
        'The Legend of Zelda: Tears of the Kingdom',
        'Explorez les vastes terres et cieux d\'Hyrule dans cette suite épique.',
        69.99,
        50
    ),
    (
        'Elden Ring',
        'Un RPG d\'action épique dans un vaste monde ouvert créé par FromSoftware et George R.R. Martin.',
        59.99,
        30
    ),
    (
        'FIFA 24',
        'Le jeu de football le plus réaliste avec des graphismes next-gen.',
        69.99,
        100
    ),
    (
        'Gran Turismo 7',
        'Simulateur de course automobile ultime pour les passionnés.',
        59.99,
        40
    );
-- Association jeux-genres
INSERT INTO `games_genres` (`game_id`, `genre_id`)
VALUES (1, 2),
    -- Zelda = Aventure
    (2, 1),
    -- Elden Ring = Action
    (2, 3),
    -- Elden Ring = RPG
    (3, 4),
    -- FIFA = Sport
    (4, 5);
-- Gran Turismo = Course
-- Association jeux-plateformes
INSERT INTO `games_plateforms` (`game_id`, `plateform_id`)
VALUES (1, 3),
    -- Zelda = Nintendo Switch
    (2, 1),
    -- Elden Ring = PS5
    (2, 2),
    -- Elden Ring = Xbox Series X
    (2, 4),
    -- Elden Ring = PC
    (3, 1),
    -- FIFA = PS5
    (3, 2),
    -- FIFA = Xbox Series X
    (3, 3),
    -- FIFA = Nintendo Switch
    (3, 4),
    -- FIFA = PC
    (4, 1),
    -- Gran Turismo = PS5
    (4, 5);
-- Gran Turismo = PS4
SET FOREIGN_KEY_CHECKS = 1;