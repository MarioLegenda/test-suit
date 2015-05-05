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
-- Table structure for table `restricted_tests`
--

CREATE TABLE IF NOT EXISTS `restricted_tests` (
`restricted_tests_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `test_control_id` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `restricted_tests`
--

INSERT INTO `restricted_tests` (`restricted_tests_id`, `user_id`, `test_control_id`) VALUES
(1, 1, 2),
(2, 2, 2);