-- phpMyAdmin SQL Dump
-- version 3.2.0.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 28, 2012 at 10:12 PM
-- Server version: 5.1.36
-- PHP Version: 5.3.0

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `schema`
--

-- --------------------------------------------------------

--
-- Table structure for table `example`
--

CREATE TABLE IF NOT EXISTS `example` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `signed_integer_no_default` int(11) NOT NULL,
  `signed_integer_default` int(11) NOT NULL DEFAULT '1',
  `unsigned_integer_no_default` int(11) unsigned NOT NULL,
  `unsigned_integer_default` int(11) unsigned NOT NULL DEFAULT '1',
  `signed_tinyint_no_default` tinyint(4) NOT NULL,
  `signed_smallint_no_default` smallint(6) NOT NULL,
  `signed_mediumint_no_default` mediumint(9) NOT NULL,
  `signed_bigint_no_default` bigint(20) NOT NULL,
  `char_length1_no_default` char(1) NOT NULL,
  `char_length1_default` char(1) NOT NULL DEFAULT 'A',
  `char_length5_no_default` char(5) NOT NULL,
  `char_length5_default` char(5) NOT NULL DEFAULT 'abcde',
  `varchar_length20_no_default` varchar(20) NOT NULL,
  `varchar_length20_default` varchar(20) NOT NULL DEFAULT 'one two three',
  `text_no_default` text NOT NULL,
  `longtext_no_default` longtext NOT NULL,
  `float_no_default` float NOT NULL,
  `float_default` float NOT NULL DEFAULT '5',
  `double_no_default` double NOT NULL,
  `double_default` double NOT NULL DEFAULT '5',
  `datetime_no_default` datetime NOT NULL,
  `datetime_default` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `timestamp_default` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `example`
--

