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
-- Table structure for table `user_info`
--

CREATE TABLE IF NOT EXISTS `user_info` (
`user_info_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `fields` varchar(1024) COLLATE utf8_unicode_ci DEFAULT NULL,
  `programming_languages` varchar(1024) COLLATE utf8_unicode_ci DEFAULT NULL,
  `tools` varchar(1024) COLLATE utf8_unicode_ci DEFAULT NULL,
  `years_of_experience` smallint(6) DEFAULT NULL,
  `future_plans` varchar(1024) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` varchar(1024) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `user_info`
--

INSERT INTO `user_info` (`user_info_id`, `user_id`, `fields`, `programming_languages`, `tools`, `years_of_experience`, `future_plans`, `description`) VALUES
(1, 1, NULL, NULL, NULL, NULL, NULL, NULL),
(2, 2, 'web development', 'php, javascript', 'git', 2, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut ultricies tellus quis lorem blandit pretium. Proin eu consectetur tellus, et dictum mi. Integer posuere a urna eget vulputate. Nulla at volutpat nibh. Donec dapibus placerat augue et tincidunt. Aliquam molestie vel sapien vitae bibendum. Proin ac faucibus purus, dapibus tempor lacus. Quisque vehicula pulvinar fringilla.', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut ultricies tellus quis lorem blandit pretium. Proin eu consectetur tellus, et dictum mi. Integer posuere a urna eget vulputate. Nulla at volutpat nibh. Donec dapibus placerat augue et tincidunt. Aliquam molestie vel sapien vitae bibendum. Proin ac faucibus purus, dapibus tempor lacus. Quisque vehicula pulvinar fringilla.'),
(3, 3, 'web development', 'php, javascript', 'git', 2, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut ultricies tellus quis lorem blandit pretium. Proin eu consectetur tellus, et dictum mi. Integer posuere a urna eget vulputate. Nulla at volutpat nibh. Donec dapibus placerat augue et tincidunt. Aliquam molestie vel sapien vitae bibendum. Proin ac faucibus purus, dapibus tempor lacus. Quisque vehicula pulvinar fringilla.', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut ultricies tellus quis lorem blandit pretium. Proin eu consectetur tellus, et dictum mi. Integer posuere a urna eget vulputate. Nulla at volutpat nibh. Donec dapibus placerat augue et tincidunt. Aliquam molestie vel sapien vitae bibendum. Proin ac faucibus purus, dapibus tempor lacus. Quisque vehicula pulvinar fringilla.');