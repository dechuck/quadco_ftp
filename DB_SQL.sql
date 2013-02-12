-- phpMyAdmin SQL Dump
-- version 3.5.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Base de données
--

-- --------------------------------------------------------

--
-- Structure de la table `T_FTP`
--

CREATE TABLE IF NOT EXISTS `T_FTP` (
  `FTP_id` int(11) NOT NULL AUTO_INCREMENT,
  `FTP_host` varchar(100) NOT NULL,
  `FTP_port` smallint(6) NOT NULL DEFAULT '21',
  `FTP_user` varchar(75) NOT NULL,
  `FTP_pass` varchar(100) NOT NULL,
  PRIMARY KEY (`FTP_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Structure de la table `T_Users`
--

CREATE TABLE IF NOT EXISTS `T_Users` (
  `Usr_id` int(11) NOT NULL AUTO_INCREMENT,
  `Usr_name` varchar(75) NOT NULL,
  `Usr_hashpass` varchar(150) NOT NULL,
  `Usr_secret` varchar(10) NOT NULL,
  `Usr_class` smallint(6) NOT NULL DEFAULT '6' COMMENT '1 => Admin, 6=> Minimum privilege',
  PRIMARY KEY (`Usr_id`),
  UNIQUE KEY `Usr_name` (`Usr_name`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Contenu de la table `T_Users`
--

INSERT INTO `T_Users` (`Usr_id`, `Usr_name`, `Usr_hashpass`, `Usr_secret`, `Usr_class`) VALUES
(1, 'Admin', '0bfa1d965e9393e72e5dac9c87919831', 'NmGhY3j2', 1), -- User Admin, mot de pass admin
