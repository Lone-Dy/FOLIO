CREATE DATABASE Folio;
Use Folio;

DROP TABLE IF EXISTS `media`, `comment`, `projet`, `folio`, `user`;

-- 1. Table USER
CREATE TABLE `user` (
   `id_user` INT AUTO_INCREMENT PRIMARY KEY,
   `nom` VARCHAR(50) NOT NULL,
   `prenom` VARCHAR(50) NOT NULL,
   `email` VARCHAR(100) UNIQUE NOT NULL,
   `age` INT NOT NULL,
   `mot_de_passe` VARCHAR(255) NOT NULL,
   `biographie` TEXT NULL,
   `photo_profile` VARCHAR(255) DEFAULT 'default-avatar.png',
   `statut_compte` VARCHAR(50) DEFAULT 'actif',
   `role` ENUM('admin', 'user') NOT NULL DEFAULT 'user'
) ENGINE=InnoDB;

-- 2. Table FOLIO
CREATE TABLE `folio` (
  `id_folio` INT AUTO_INCREMENT PRIMARY KEY,
  `titre` VARCHAR(150) NOT NULL,
  `description` TEXT,
  `categorie_folio` VARCHAR(50) DEFAULT 'Non classé',
  `is_published` TINYINT(1) DEFAULT 0,
  `id_user` INT NOT NULL,
  `date_creation` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_folio_user` FOREIGN KEY (`id_user`) REFERENCES `user`(`id_user`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 3. Table PROJET
CREATE TABLE `projet` (
  `id_projet` INT AUTO_INCREMENT PRIMARY KEY,
  `id_folio` INT NOT NULL, 
  `type` VARCHAR(100) NOT NULL,
  `contenu` TEXT,
  `ordre_affichage` INT DEFAULT 0,
  CONSTRAINT `fk_projet_folio` FOREIGN KEY (`id_folio`) REFERENCES `folio`(`id_folio`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 4. Table MEDIA
CREATE TABLE `media` (
  `id_media` INT AUTO_INCREMENT PRIMARY KEY,
  `id_projet` INT NOT NULL,
  `cheminFichier` VARCHAR(255) NOT NULL,
  `mediaType` ENUM('image', 'video') NOT NULL,
  `ordreAffichage` INT NOT NULL DEFAULT 0,
  `poidsFichier` INT NOT NULL,
  CONSTRAINT `fk_media_projet` FOREIGN KEY (`id_projet`) REFERENCES `projet`(`id_projet`) ON DELETE CASCADE
) ENGINE=InnoDB;
