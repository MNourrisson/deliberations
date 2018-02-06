-- phpMyAdmin SQL Dump
-- version 4.5.2
-- http://www.phpmyadmin.net
--
-- Client :  127.0.0.1
-- Généré le :  Mar 06 Février 2018 à 11:53
-- Version du serveur :  5.7.9
-- Version de PHP :  5.6.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `deliberations`
--

-- --------------------------------------------------------

--
-- Structure de la table `axe`
--

DROP TABLE IF EXISTS `axe`;
CREATE TABLE IF NOT EXISTS `axe` (
  `id_axe` int(11) NOT NULL AUTO_INCREMENT,
  `libelle` varchar(255) NOT NULL,
  `id_charte` int(11) NOT NULL,
  `parent` int(11) NOT NULL,
  `niveau` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_axe`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `charte`
--

DROP TABLE IF EXISTS `charte`;
CREATE TABLE IF NOT EXISTS `charte` (
  `id_charte` int(11) NOT NULL AUTO_INCREMENT,
  `libelle` varchar(255) NOT NULL,
  `defaut` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_charte`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `deliberation`
--

DROP TABLE IF EXISTS `deliberation`;
CREATE TABLE IF NOT EXISTS `deliberation` (
  `id_deliberation` int(11) NOT NULL AUTO_INCREMENT,
  `id_reunion` int(11) NOT NULL,
  `libelle` varchar(255) NOT NULL,
  `num` varchar(10) NOT NULL,
  `num_delib` varchar(10) NOT NULL,
  `id_axe` int(11) NOT NULL,
  `folio` varchar(40) NOT NULL,
  `id_fichier` int(11) NOT NULL,
  `id_budget` int(11) NOT NULL,
  `id_emargement` int(11) NOT NULL,
  PRIMARY KEY (`id_deliberation`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `fichier`
--

DROP TABLE IF EXISTS `fichier`;
CREATE TABLE IF NOT EXISTS `fichier` (
  `id_fichier` int(11) NOT NULL AUTO_INCREMENT,
  `nom_affichage` varchar(255) NOT NULL,
  `nom_reel` varchar(255) NOT NULL,
  PRIMARY KEY (`id_fichier`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `reunion`
--

DROP TABLE IF EXISTS `reunion`;
CREATE TABLE IF NOT EXISTS `reunion` (
  `id_reunion` int(11) NOT NULL AUTO_INCREMENT,
  `id_type_reunion` int(11) NOT NULL,
  `date` date NOT NULL,
  `id_charte` int(11) NOT NULL,
  PRIMARY KEY (`id_reunion`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `type_reunion`
--

DROP TABLE IF EXISTS `type_reunion`;
CREATE TABLE IF NOT EXISTS `type_reunion` (
  `id_type_reunion` int(11) NOT NULL AUTO_INCREMENT,
  `libelle` varchar(50) NOT NULL,
  `parent` int(11) NOT NULL,
  PRIMARY KEY (`id_type_reunion`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
--

DROP TABLE IF EXISTS `utilisateur`;
CREATE TABLE IF NOT EXISTS `utilisateur` (
  `id_utilisateur` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(50) NOT NULL,
  `mdp` varchar(255) NOT NULL,
  PRIMARY KEY (`id_utilisateur`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
