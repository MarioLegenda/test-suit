-- phpMyAdmin SQL Dump
-- version 4.2.11
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: May 01, 2015 at 02:42 PM
-- Server version: 5.6.21
-- PHP Version: 5.6.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `suit`
--

-- --------------------------------------------------------

--
-- Table structure for table `test_control`
--

CREATE TABLE IF NOT EXISTS `test_control` (
`test_control_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `test_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `identifier` char(32) COLLATE utf8_unicode_ci NOT NULL,
  `visibility` char(10) COLLATE utf8_unicode_ci NOT NULL,
  `isFinished` smallint(6) NOT NULL,
  `remarks` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created` datetime NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `test_control`
--

INSERT INTO `test_control` (`test_control_id`, `user_id`, `test_name`, `identifier`, `visibility`, `isFinished`, `remarks`, `created`) VALUES
(2, 1, 'javascript', 'otB/4QZoO8gjZBgMdBOv0uQI2U5OVSa7', 'restricted', 0, 'no remarks', '2015-04-28 16:03:34'),
(3, 1, 'php', '5hlLZndhrBWJg6mwbOehcNxEH8SKTxxY', 'public', 0, 'no remarks', '2015-04-28 16:05:46'),
(4, 1, 'go', 'sVBKTBlIgO4fdopLXv01Kbidi0GTb6UJ', 'public', 0, 'it is public', '2015-04-28 18:01:00');
