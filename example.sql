-- phpMyAdmin SQL Dump
-- version 3.2.0.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 25, 2012 at 10:43 PM
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
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `example`
--

