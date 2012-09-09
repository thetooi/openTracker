-- phpMyAdmin SQL Dump
-- version 3.4.5deb1
-- http://www.phpmyadmin.net
--
-- Värd: localhost
-- Skapad: 27 aug 2012 kl 05:44
-- Serverversion: 5.1.62
-- PHP-version: 5.3.6-13ubuntu3.8

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Databas: `tracker2`
--

-- --------------------------------------------------------

--
-- Tabellstruktur `tracker_polls`
--

CREATE TABLE IF NOT EXISTS `tracker_polls` (
  `poll_id` int(11) NOT NULL AUTO_INCREMENT,
  `poll_title` varchar(255) NOT NULL,
  `poll_description` text NOT NULL,
  `poll_added` int(11) NOT NULL,
  `poll_status` int(11) NOT NULL,
  PRIMARY KEY (`poll_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Tabellstruktur `tracker_polls_options`
--

CREATE TABLE IF NOT EXISTS `tracker_polls_options` (
  `option_id` int(11) NOT NULL AUTO_INCREMENT,
  `option_poll` int(11) NOT NULL,
  `option_title` varchar(255) NOT NULL,
  PRIMARY KEY (`option_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

--
-- Tabellstruktur `tracker_polls_votes`
--

CREATE TABLE IF NOT EXISTS `tracker_polls_votes` (
  `vote_id` int(11) NOT NULL AUTO_INCREMENT,
  `vote_poll` int(11) NOT NULL,
  `vote_option` int(11) NOT NULL,
  `vote_userid` varchar(255) NOT NULL,
  `vote_added` int(11) NOT NULL,
  PRIMARY KEY (`vote_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
