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
-- Table structure for table `tests`
--

CREATE TABLE IF NOT EXISTS `tests` (
`test_id` int(11) NOT NULL,
  `test_control_id` int(11) NOT NULL,
  `test_serialized` text COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `tests`
--

INSERT INTO `tests` (`test_id`, `test_control_id`, `test_serialized`) VALUES
(1, 2, '[{"dataType":"text","blockType":"question","element":"textarea","blockId":0,"placeholder":null,"directiveType":"code-block","data":{"type":"code-block","data":"var foo = function foo() {\\r\\n    console.log(foo === foo);  \\r\\n};\\r\\nfoo();"}},{"dataType":"text","blockType":"question","element":"textarea","blockId":1,"placeholder":"Type your question here...","directiveType":"plain-text-block","data":{"type":"plain-text-block","data":"What is printed in the console?"}},{"dataType":"object","blockType":"answer","element":"textarea","blockId":2,"placeholder":"Text for one radio button goes here...","directiveType":"radio-block","data":{"type":"radio-block","data":["True","False","RefferenceError"],"selected":null}}]'),
(2, 2, '[{"dataType":"text","blockType":"question","element":"textarea","blockId":0,"placeholder":null,"directiveType":"code-block","data":{"type":"code-block","data":"function aaa() {\\r\\n    return\\r\\n    {\\r\\n        test: 1\\r\\n    };\\r\\n}\\r\\nalert(typeof aaa());"}},{"dataType":"text","blockType":"question","element":"textarea","blockId":1,"placeholder":"Type your question here...","directiveType":"plain-text-block","data":{"type":"plain-text-block","data":"What does the above alert?"}},{"dataType":"object","blockType":"answer","element":"textarea","blockId":2,"placeholder":"Text for one radio button goes here...","directiveType":"radio-block","data":{"type":"radio-block","data":["function","number","object","undefined"],"selected":null}}]'),
(3, 2, '[{"dataType":"text","blockType":"question","element":"textarea","blockId":0,"placeholder":null,"directiveType":"code-block","data":{"type":"code-block","data":"Number(\\"1\\") - 1 == 0;"}},{"dataType":"text","blockType":"question","element":"textarea","blockId":1,"placeholder":"Type your question here...","directiveType":"plain-text-block","data":{"type":"plain-text-block","data":"What is the result?"}},{"dataType":"object","blockType":"answer","element":"textarea","blockId":2,"placeholder":"Text for one radio button goes here...","directiveType":"radio-block","data":{"type":"radio-block","data":["True","False","TypeError"],"selected":null}}]'),
(4, 2, '[{"dataType":"text","blockType":"question","element":"textarea","blockId":0,"placeholder":null,"directiveType":"code-block","data":{"type":"code-block","data":"(true + false) > 2 + true;"}},{"dataType":"text","blockType":"question","element":"textarea","blockId":1,"placeholder":"Type your question here...","directiveType":"plain-text-block","data":{"type":"plain-text-block","data":"What is the result?"}},{"dataType":"object","blockType":"answer","element":"textarea","blockId":2,"placeholder":"Text for one radio button goes here...","directiveType":"radio-block","data":{"type":"radio-block","data":["True","False","TypeError","NaN"],"selected":null}}]'),
(5, 2, '[{"dataType":"text","blockType":"question","element":"textarea","blockId":0,"placeholder":null,"directiveType":"code-block","data":{"type":"code-block","data":"function bar() {\\r\\n    return foo;\\r\\n    foo = 10;\\r\\n    function foo() {}\\r\\n    var foo = ''11'';\\r\\n}\\r\\nalert(typeof bar());"}},{"dataType":"text","blockType":"question","element":"textarea","blockId":1,"placeholder":"Type your question here...","directiveType":"plain-text-block","data":{"type":"plain-text-block","data":"What is alerted?"}},{"dataType":"object","blockType":"answer","element":"textarea","blockId":2,"placeholder":"Text for one radio button goes here...","directiveType":"radio-block","data":{"type":"radio-block","data":["number","function","undefined","string","Error"],"selected":null}}]');

