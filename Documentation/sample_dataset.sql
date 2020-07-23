-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jul 21, 2020 at 01:44 PM
-- Server version: 10.2.10-MariaDB
-- PHP Version: 7.2.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `races`
--

-- --------------------------------------------------------

--
-- Table structure for table `event`
--

CREATE TABLE `event` (
  `id` smallint(6) NOT NULL,
  `name` varchar(25) NOT NULL,
  `date` date NOT NULL,
  `pot` decimal(6,2) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Boolean, 1 means completed.',
  `create_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `update_time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `champion_id` smallint(6) DEFAULT NULL,
  `champion_purse` decimal(6,2) DEFAULT NULL,
  `champion_photo` varchar(128) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `event`
--

INSERT INTO `event` (`id`, `name`, `date`, `pot`, `status`, `create_time`, `update_time`, `champion_id`, `champion_purse`, `champion_photo`) VALUES
(1, 'Keeneland 2020', '2020-07-21', '420.00', 1, '2020-06-18 22:08:45', '2020-07-21 13:37:07', 2, '58.60', '/uploads/1-champion.jpg'),
(2, 'Keeneland 2021', '2021-07-21', '421.00', 1, '2020-06-18 22:08:55', '2020-07-21 13:37:21', 3, '65.63', '/uploads/2-champion.jpg'),
(3, 'Keeneland 2022', '2022-07-21', '422.00', 0, '2020-06-17 19:14:46', '2020-07-21 13:37:33', NULL, NULL, '');

-- --------------------------------------------------------

--
-- Table structure for table `event_standings`
--

CREATE TABLE `event_standings` (
  `event_id` smallint(6) NOT NULL,
  `user_id` smallint(6) NOT NULL,
  `earnings` decimal(6,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `event_standings`
--

INSERT INTO `event_standings` (`event_id`, `user_id`, `earnings`) VALUES
(1, 1, '26.60'),
(1, 2, '58.60'),
(1, 3, '1.20'),
(1, 4, '5.40'),
(1, 5, '45.60'),
(1, 6, '0.00'),
(1, 7, '12.40'),
(1, 8, '8.40'),
(1, 9, '22.40'),
(1, 10, '1.20'),
(1, 11, '12.60'),
(2, 1, '9.42'),
(2, 2, '45.63'),
(2, 3, '65.63'),
(2, 4, '17.63'),
(2, 5, '28.42'),
(2, 6, '5.42'),
(2, 7, '38.63'),
(2, 8, '13.63'),
(2, 9, '26.84'),
(2, 10, '11.42'),
(2, 11, '15.42');

-- --------------------------------------------------------

--
-- Table structure for table `horse`
--

CREATE TABLE `horse` (
  `id` int(11) NOT NULL,
  `race_event_id` smallint(6) NOT NULL,
  `race_race_number` tinyint(4) NOT NULL,
  `horse_number` char(3) DEFAULT NULL,
  `finish` enum('win','place','show') DEFAULT NULL,
  `win_purse` decimal(6,2) DEFAULT NULL,
  `place_purse` decimal(6,2) DEFAULT NULL,
  `show_purse` decimal(6,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `horse`
--

INSERT INTO `horse` (`id`, `race_event_id`, `race_race_number`, `horse_number`, `finish`, `win_purse`, `place_purse`, `show_purse`) VALUES
(1, 1, 1, '201', 'win', '27.20', '11.20', '7.20'),
(2, 1, 1, '202', 'place', NULL, '4.20', '2.20'),
(3, 1, 1, '203', 'show', NULL, NULL, '1.20'),
(4, 1, 1, '204', NULL, NULL, NULL, NULL),
(5, 1, 1, '205', NULL, NULL, NULL, NULL),
(6, 1, 1, '206', NULL, NULL, NULL, NULL),
(7, 1, 1, '207', NULL, NULL, NULL, NULL),
(8, 1, 1, '208', NULL, NULL, NULL, NULL),
(9, 1, 1, '209', NULL, NULL, NULL, NULL),
(10, 1, 2, '201', NULL, NULL, NULL, NULL),
(11, 1, 2, '202', NULL, NULL, NULL, NULL),
(12, 1, 2, '203', NULL, NULL, NULL, NULL),
(13, 1, 2, '204', 'win', '27.20', '11.20', '7.20'),
(14, 1, 2, '205', 'place', NULL, '4.20', '2.20'),
(15, 1, 2, '206', 'show', NULL, NULL, '1.20'),
(16, 1, 2, '207', NULL, NULL, NULL, NULL),
(17, 1, 2, '208', NULL, NULL, NULL, NULL),
(18, 1, 2, '209', NULL, NULL, NULL, NULL),
(19, 1, 3, '201', NULL, NULL, NULL, NULL),
(20, 1, 3, '202', NULL, NULL, NULL, NULL),
(21, 1, 3, '203', NULL, NULL, NULL, NULL),
(22, 1, 3, '204', NULL, NULL, NULL, NULL),
(23, 1, 3, '205', NULL, NULL, NULL, NULL),
(24, 1, 3, '206', NULL, NULL, NULL, NULL),
(25, 1, 3, '207', 'win', '27.20', '11.20', '7.20'),
(26, 1, 3, '208', 'place', NULL, '4.20', '2.20'),
(27, 1, 3, '209', 'show', NULL, NULL, '1.20'),
(28, 2, 1, '211', 'win', '27.21', '11.21', '7.21'),
(29, 2, 1, '212', 'place', NULL, '4.21', '2.21'),
(30, 2, 1, '213', 'show', NULL, NULL, '1.21'),
(31, 2, 1, '214', NULL, NULL, NULL, NULL),
(32, 2, 1, '215', NULL, NULL, NULL, NULL),
(33, 2, 1, '216', NULL, NULL, NULL, NULL),
(34, 2, 1, '217', NULL, NULL, NULL, NULL),
(35, 2, 1, '218', NULL, NULL, NULL, NULL),
(36, 2, 1, '219', NULL, NULL, NULL, NULL),
(37, 2, 2, '211', NULL, NULL, NULL, NULL),
(38, 2, 2, '212', NULL, NULL, NULL, NULL),
(39, 2, 2, '213', NULL, NULL, NULL, NULL),
(40, 2, 2, '214', 'win', '27.21', '11.21', '7.21'),
(41, 2, 2, '215', 'place', NULL, '4.21', '2.21'),
(42, 2, 2, '216', 'show', NULL, NULL, '1.21'),
(43, 2, 2, '217', NULL, NULL, NULL, NULL),
(44, 2, 2, '218', NULL, NULL, NULL, NULL),
(45, 2, 2, '219', NULL, NULL, NULL, NULL),
(46, 2, 3, '211', NULL, NULL, NULL, NULL),
(47, 2, 3, '212', NULL, NULL, NULL, NULL),
(48, 2, 3, '213', NULL, NULL, NULL, NULL),
(49, 2, 3, '214', NULL, NULL, NULL, NULL),
(50, 2, 3, '215', NULL, NULL, NULL, NULL),
(51, 2, 3, '216', NULL, NULL, NULL, NULL),
(52, 2, 3, '217', 'win', '27.21', '11.21', '7.21'),
(53, 2, 3, '218', 'place', NULL, '4.21', '2.21'),
(54, 2, 3, '219', 'show', NULL, NULL, '1.21'),
(55, 3, 1, '221', 'win', '27.22', '11.22', '7.22'),
(56, 3, 1, '222', 'place', NULL, '4.22', '2.22'),
(57, 3, 1, '223', 'show', NULL, NULL, '1.22'),
(58, 3, 1, '224', NULL, NULL, NULL, NULL),
(59, 3, 1, '225', NULL, NULL, NULL, NULL),
(60, 3, 1, '226', NULL, NULL, NULL, NULL),
(61, 3, 1, '227', NULL, NULL, NULL, NULL),
(62, 3, 1, '228', NULL, NULL, NULL, NULL),
(63, 3, 1, '229', NULL, NULL, NULL, NULL),
(64, 3, 2, '221', NULL, NULL, NULL, NULL),
(65, 3, 2, '222', NULL, NULL, NULL, NULL),
(66, 3, 2, '223', NULL, NULL, NULL, NULL),
(67, 3, 2, '224', NULL, NULL, NULL, NULL),
(68, 3, 2, '225', NULL, NULL, NULL, NULL),
(69, 3, 2, '226', NULL, NULL, NULL, NULL),
(70, 3, 2, '227', NULL, NULL, NULL, NULL),
(71, 3, 2, '228', NULL, NULL, NULL, NULL),
(72, 3, 2, '229', NULL, NULL, NULL, NULL),
(73, 3, 3, '221', NULL, NULL, NULL, NULL),
(74, 3, 3, '222', NULL, NULL, NULL, NULL),
(75, 3, 3, '223', NULL, NULL, NULL, NULL),
(76, 3, 3, '224', NULL, NULL, NULL, NULL),
(77, 3, 3, '225', NULL, NULL, NULL, NULL),
(78, 3, 3, '226', NULL, NULL, NULL, NULL),
(79, 3, 3, '227', NULL, NULL, NULL, NULL),
(80, 3, 3, '228', NULL, NULL, NULL, NULL),
(81, 3, 3, '229', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `pick`
--

CREATE TABLE `pick` (
  `user_id` smallint(6) NOT NULL,
  `race_event_id` smallint(6) NOT NULL,
  `race_race_number` tinyint(4) NOT NULL,
  `horse_number` char(3) NOT NULL,
  `finish` enum('win','place','show') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `pick`
--

INSERT INTO `pick` (`user_id`, `race_event_id`, `race_race_number`, `horse_number`, `finish`) VALUES
(1, 1, 1, '201', 'place'),
(1, 1, 2, '205', 'place'),
(1, 1, 3, '208', 'place'),
(1, 2, 1, '211', 'show'),
(1, 2, 2, '215', 'show'),
(1, 2, 3, '216', 'win'),
(1, 3, 1, '224', 'win'),
(2, 1, 1, '201', 'win'),
(2, 1, 2, '205', 'place'),
(2, 1, 3, '207', 'win'),
(2, 2, 1, '211', 'win'),
(2, 2, 2, '214', 'show'),
(2, 2, 3, '217', 'place'),
(2, 3, 1, '223', 'win'),
(3, 1, 1, '204', 'place'),
(3, 1, 2, '206', 'show'),
(3, 1, 3, '201', 'win'),
(3, 2, 1, '211', 'place'),
(3, 2, 2, '214', 'win'),
(3, 2, 3, '217', 'win'),
(3, 3, 1, '221', 'show'),
(4, 1, 1, '205', 'win'),
(4, 1, 2, '205', 'place'),
(4, 1, 3, '209', 'show'),
(4, 2, 1, '211', 'place'),
(4, 2, 2, '215', 'place'),
(4, 2, 3, '218', 'show'),
(4, 3, 1, '222', 'place'),
(5, 1, 1, '201', 'show'),
(5, 1, 2, '204', 'win'),
(5, 1, 3, '207', 'place'),
(5, 2, 1, '211', 'win'),
(5, 2, 2, '216', 'show'),
(5, 2, 3, '211', 'place'),
(5, 3, 1, '223', 'win'),
(6, 1, 1, '206', 'show'),
(6, 1, 2, '209', 'win'),
(6, 1, 3, '202', 'win'),
(6, 2, 1, '213', 'show'),
(6, 2, 2, '218', 'win'),
(6, 2, 3, '218', 'place'),
(6, 3, 1, '221', 'win'),
(7, 1, 1, '201', 'place'),
(7, 1, 2, '207', 'place'),
(7, 1, 3, '209', 'show'),
(7, 2, 1, '221', 'place'),
(7, 2, 2, '214', 'win'),
(7, 2, 3, '217', 'show'),
(7, 3, 1, '221', 'place'),
(8, 1, 1, '203', 'place'),
(8, 1, 2, '206', 'place'),
(8, 1, 3, '207', 'show'),
(8, 2, 1, '211', 'show'),
(8, 2, 2, '215', 'place'),
(8, 2, 3, '218', 'show'),
(8, 3, 1, '229', 'show'),
(9, 1, 1, '201', 'place'),
(9, 1, 2, '208', 'win'),
(9, 1, 3, '207', 'place'),
(9, 2, 1, '212', 'show'),
(9, 2, 2, '214', 'place'),
(9, 2, 3, '219', 'win'),
(9, 3, 1, '228', 'place'),
(10, 1, 1, '209', 'show'),
(10, 1, 2, '201', 'win'),
(10, 1, 3, '209', 'show'),
(10, 2, 1, '217', 'show'),
(10, 2, 2, '215', 'place'),
(10, 2, 3, '217', 'show'),
(10, 3, 1, '227', 'show'),
(11, 1, 1, '203', 'show'),
(11, 1, 2, '205', 'place'),
(11, 1, 3, '207', 'show'),
(11, 2, 1, '212', 'place'),
(11, 2, 2, '212', 'place'),
(11, 2, 3, '217', 'place'),
(11, 3, 1, '223', 'place');

-- --------------------------------------------------------

--
-- Table structure for table `race`
--

CREATE TABLE `race` (
  `event_id` smallint(6) NOT NULL,
  `race_number` tinyint(4) NOT NULL,
  `window_closed` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `race`
--

INSERT INTO `race` (`event_id`, `race_number`, `window_closed`) VALUES
(1, 1, 1),
(1, 2, 1),
(1, 3, 1),
(2, 1, 1),
(2, 2, 1),
(2, 3, 1),
(3, 1, 1),
(3, 2, 1),
(3, 3, 0);

-- --------------------------------------------------------

--
-- Table structure for table `race_standings`
--

CREATE TABLE `race_standings` (
  `race_event_id` smallint(6) NOT NULL,
  `race_race_number` tinyint(4) NOT NULL,
  `user_id` smallint(6) NOT NULL,
  `earnings` decimal(6,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `race_standings`
--

INSERT INTO `race_standings` (`race_event_id`, `race_race_number`, `user_id`, `earnings`) VALUES
(1, 1, 1, '11.20'),
(1, 1, 2, '27.20'),
(1, 1, 3, '0.00'),
(1, 1, 4, '0.00'),
(1, 1, 5, '7.20'),
(1, 1, 6, '0.00'),
(1, 1, 7, '11.20'),
(1, 1, 8, '0.00'),
(1, 1, 9, '11.20'),
(1, 1, 10, '0.00'),
(1, 1, 11, '1.20'),
(1, 2, 1, '11.20'),
(1, 2, 2, '4.20'),
(1, 2, 3, '1.20'),
(1, 2, 4, '4.20'),
(1, 2, 5, '27.20'),
(1, 2, 6, '0.00'),
(1, 2, 7, '0.00'),
(1, 2, 8, '1.20'),
(1, 2, 9, '0.00'),
(1, 2, 10, '0.00'),
(1, 2, 11, '4.20'),
(1, 3, 1, '4.20'),
(1, 3, 2, '27.20'),
(1, 3, 3, '0.00'),
(1, 3, 4, '1.20'),
(1, 3, 5, '11.20'),
(1, 3, 6, '0.00'),
(1, 3, 7, '1.20'),
(1, 3, 8, '7.20'),
(1, 3, 9, '11.20'),
(1, 3, 10, '1.20'),
(1, 3, 11, '7.20'),
(2, 1, 1, '7.21'),
(2, 1, 2, '27.21'),
(2, 1, 3, '11.21'),
(2, 1, 4, '11.21'),
(2, 1, 5, '27.21'),
(2, 1, 6, '1.21'),
(2, 1, 7, '4.21'),
(2, 1, 8, '7.21'),
(2, 1, 9, '2.21'),
(2, 1, 10, '0.00'),
(2, 1, 11, '4.21'),
(2, 2, 1, '2.21'),
(2, 2, 2, '7.21'),
(2, 2, 3, '27.21'),
(2, 2, 4, '4.21'),
(2, 2, 5, '1.21'),
(2, 2, 6, '0.00'),
(2, 2, 7, '27.21'),
(2, 2, 8, '4.21'),
(2, 2, 9, '11.21'),
(2, 2, 10, '4.21'),
(2, 2, 11, '0.00'),
(2, 3, 1, '0.00'),
(2, 3, 2, '11.21'),
(2, 3, 3, '27.21'),
(2, 3, 4, '2.21'),
(2, 3, 5, '0.00'),
(2, 3, 6, '4.21'),
(2, 3, 7, '7.21'),
(2, 3, 8, '2.21'),
(2, 3, 9, '0.00'),
(2, 3, 10, '7.21'),
(2, 3, 11, '11.21'),
(3, 1, 1, '0.00'),
(3, 1, 2, '0.00'),
(3, 1, 3, '7.22'),
(3, 1, 4, '4.22'),
(3, 1, 5, '0.00'),
(3, 1, 6, '27.22'),
(3, 1, 7, '11.22'),
(3, 1, 8, '0.00'),
(3, 1, 9, '0.00'),
(3, 1, 10, '0.00'),
(3, 1, 11, '1.22');

-- --------------------------------------------------------

--
-- Table structure for table `site_settings`
--

CREATE TABLE `site_settings` (
  `id` tinyint(4) NOT NULL,
  `name` varchar(45) NOT NULL,
  `sound_fx` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Boolean, 1 means enabled.',
  `voiceovers` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Boolean, 1 means enabled.',
  `terms_enable` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Boolean, 1 means enabled.',
  `terms_text` longtext DEFAULT NULL,
  `default_horse_count` tinyint(4) NOT NULL DEFAULT 20,
  `memorial_race_enable` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Boolean, 1 means enabled.',
  `memorial_race_number` tinyint(4) NOT NULL DEFAULT 9,
  `memorial_race_name` varchar(45) NOT NULL,
  `welcome_video_url` varchar(128) NOT NULL,
  `invite_email_subject` varchar(64) NOT NULL,
  `invite_email_body` text NOT NULL,
  `email_server` varchar(64) NOT NULL,
  `email_server_port` varchar(5) NOT NULL,
  `email_server_account` varchar(64) NOT NULL,
  `email_server_password` varchar(64) NOT NULL,
  `email_from_name` varchar(64) NOT NULL,
  `email_from_address` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `site_settings`
--

INSERT INTO `site_settings` (`id`, `name`, `sound_fx`, `voiceovers`, `terms_enable`, `terms_text`, `default_horse_count`, `memorial_race_enable`, `memorial_race_number`, `memorial_race_name`, `welcome_video_url`, `invite_email_subject`, `invite_email_body`, `email_server`, `email_server_port`, `email_server_account`, `email_server_password`, `email_from_name`, `email_from_address`) VALUES
(1, 'Family Races', 1, 1, 1, 'Maecenas sed diam eget risus varius blandit sit amet non magna. Maecenas sed diam eget risus varius blandit sit amet non magna. Duis mollis, est non commodo luctus, nisi erat porttitor ligula, eget lacinia odio sem nec elit. Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit.\r\n\r\nEtiam porta sem malesuada magna mollis euismod. Cras justo odio, dapibus ac facilisis in, egestas eget quam. Nullam quis risus eget urna mollis ornare vel eu leo. Aenean eu leo quam. Pellentesque ornare sem lacinia quam venenatis vestibulum. Donec ullamcorper nulla non metus auctor fringilla. Curabitur blandit tempus porttitor.\r\n\r\nNullam id dolor id nibh ultricies vehicula ut id elit. Aenean lacinia bibendum nulla sed consectetur. Cras mattis consectetur purus sit amet fermentum. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Cras mattis consectetur purus sit amet fermentum. Vestibulum id ligula porta felis euismod semper.\r\n\r\nMaecenas faucibus mollis interdum. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Maecenas sed diam eget risus varius blandit sit amet non magna. Vestibulum id ligula porta felis euismod semper. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum id ligula porta felis euismod semper.', 9, 1, 3, 'Denny Jones Memorial', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'You\'re Invited!', 'Here\'s your invite link to get started using Family Races!', 'smtp.gmail.com', '587', 'cscdevemail@gmail.com', 'nku1234!', 'Family Races', 'cscdevemail@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` smallint(6) NOT NULL,
  `first_name` varchar(45) DEFAULT NULL,
  `last_name` varchar(45) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `create_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `update_time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `city` varchar(45) DEFAULT NULL,
  `state` char(2) DEFAULT NULL,
  `motto` varchar(255) DEFAULT NULL,
  `photo` varchar(128) DEFAULT '/images/no-user-image.jpg',
  `sound_fx` tinyint(1) DEFAULT 1 COMMENT 'Boolean, 1 means enabled.',
  `voiceovers` tinyint(1) DEFAULT 1 COMMENT 'Boolean, 1 means enabled.',
  `pw_reset_code` char(8) DEFAULT NULL,
  `invite_code` char(8) DEFAULT NULL,
  `admin` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `first_name`, `last_name`, `email`, `password`, `create_time`, `update_time`, `city`, `state`, `motto`, `photo`, `sound_fx`, `voiceovers`, `pw_reset_code`, `invite_code`, `admin`) VALUES
(1, 'Boss', 'O&#039;Dasyte', 'admin@mysite.com', '$2y$10$3oagwP1YGgYpbyr6uCrTXOCB7pyaFEnfmV8pATvSnHo4W6h6aIbR6', '2020-06-17 15:48:06', '2020-07-14 21:50:53', 'Florence', 'KY', 'Whatever, man!', '/uploads/1.jpg', 1, 1, NULL, NULL, 1),
(2, 'Joe', 'Blow', 'joe@blow.com', '$2y$10$pw2q39Sm3QgsLKQVsULmOeG/.8jNfnqcS/WLrbnrAlf8rI0X5hz/.', '2020-06-17 15:48:06', '2020-07-14 21:59:04', 'Fort Thomas', 'KY', 'Everything in moderation, including moderation.', '/uploads/2.jpg', 1, 1, NULL, NULL, 0),
(3, 'Amy', 'Adams', 'amy123@gmaily.com', '$2y$10$..QfModlnxZJpu6j3GIghOqQ3aFqC6taGpXaZIy6OAaYfDtjsdQ4q', '2020-06-17 15:51:01', '2020-07-14 21:51:13', 'Boston', 'MA', 'We may encounter many defeats but we must not be defeated.', '/uploads/3.jpg', 1, 1, NULL, NULL, 0),
(4, 'Habernathy', 'Olathe', 'hab@habbieshobbies.com', '$2y$10$pAvFnyXIfQNuzZlPrV7ngu8I33LErjK3CyRmjk2E9MRqiJkXiiwBm', '2020-06-17 15:51:01', '2020-07-14 21:51:22', 'Barstow', 'CA', 'Draco Dormiens Nunquam Titillandus', '/uploads/4.jpg', 1, 1, NULL, NULL, 0),
(5, 'Cornelius', 'Frank', 'cfrank@enkayewe.edu', '$2y$10$FqQp9Qc9Dou1xHsNpucvMujl1PU0UvonPMdUdU/czlEndZr4f4qx.', '2020-06-17 15:54:39', '2020-07-14 21:51:28', 'Zyzzx', 'CA', 'The gull sees farthest who flies highest', '/uploads/5.jpg', 1, 1, NULL, NULL, 0),
(6, 'Beth', 'Wilson', 'beth@wilson.com', '$2y$10$D0XWQKDM.gvRjD6/lyiG3u3kJEHbeyUmHatDhymEluaFO.ywOvL7u', '2020-06-17 15:54:39', '2020-07-14 21:51:34', 'Bethel', 'AK', 'Be sweet and carry a sharp knife.', '/uploads/6.jpg', 1, 1, NULL, NULL, 0),
(7, 'Lemuel', 'Cricketbritches', 'cricketbritches@lemmy.com', '$2y$10$Hofalv7IJPRj07D1FQBp1ebF7zu0X7UbdrpTaORHJIq5ECdfOGI9O', '2020-06-17 15:57:12', '2020-07-14 21:51:55', 'St John\'s Wood', 'UK', 'If you\'ve already dug yourself a hole too deep to climb out of, you may as well keep digging.', '/uploads/7.jpg', 1, 1, NULL, NULL, 0),
(8, 'John', 'Smith', 'jsmith@gmailzzz.com', '$2y$10$2cV.S5TLZ9QW9XHdXSq77e9ubKnCzv9Yh8clbsBcTJyNoj78sSjqO', '2020-06-17 15:57:12', '2020-07-10 01:38:10', 'Booger Hole', 'WV', 'All hope abandon, ye who enter here.', '/images/no-user-image.jpg', 1, 1, NULL, NULL, 0),
(9, 'Jane', 'Doe', 'jane@doe.com', '$2y$10$NjspBSMZXfTsFtnYu.totO38diKzigfuEakbanIyqiOpzzr2r0Bhu', '2020-06-17 15:59:34', '2020-07-14 21:52:03', 'Monkey\'s Eyebrow', 'KY', 'Eight hours work, eight hours sleep, and ten hours recreation; Now that\'s a good day!', '/uploads/9.jpg', 1, 1, NULL, NULL, 0),
(10, 'Larry', 'Noble', 'larrythenoble@primordialooze.org', '$2y$10$1XiQQ55K7yTgO4NEZz9IxuWuLsIjejtQVqclmuhB9.4KZfXKvcEGK', '2020-06-17 16:02:16', '2020-07-14 21:52:08', 'Scratch Ankle', 'AL', 'Envy is honor\'s foe.', '/uploads/10.jpg', 1, 1, NULL, NULL, 0),
(11, 'Babbette', 'Lloyd', 'babs@glitterfusion.xyz', '$2y$10$5M.osBVngZBufDbjJvNu/OcgNwbqUQgsEb/b2MnJZupucbt9vf8LS', '2020-06-17 16:06:58', '2020-07-14 21:52:20', 'Truth or Consequences', 'NM', 'Truth and virtue conquer.', '/uploads/11.jpg', 1, 1, NULL, NULL, 0),
(12, NULL, NULL, 'ashoemaker@gmail.com', '', '2020-06-17 16:06:58', '2020-06-19 15:32:11', NULL, NULL, NULL, '/images/no-user-image.jpg', 1, 1, NULL, 'b696aa76', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `event`
--
ALTER TABLE `event`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_event_user1_idx` (`champion_id`);

--
-- Indexes for table `event_standings`
--
ALTER TABLE `event_standings`
  ADD PRIMARY KEY (`event_id`,`user_id`),
  ADD KEY `fk_event_standings_user1_idx` (`user_id`),
  ADD KEY `fk_event_standings_event1_idx` (`event_id`);

--
-- Indexes for table `horse`
--
ALTER TABLE `horse`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_horse_race1_idx` (`race_event_id`,`race_race_number`);

--
-- Indexes for table `pick`
--
ALTER TABLE `pick`
  ADD PRIMARY KEY (`user_id`,`race_event_id`,`race_race_number`),
  ADD KEY `fk_pick_user1_idx` (`user_id`),
  ADD KEY `fk_pick_race1_idx` (`race_event_id`,`race_race_number`);

--
-- Indexes for table `race`
--
ALTER TABLE `race`
  ADD PRIMARY KEY (`event_id`,`race_number`),
  ADD KEY `fk_race_event1_idx` (`event_id`);

--
-- Indexes for table `race_standings`
--
ALTER TABLE `race_standings`
  ADD PRIMARY KEY (`race_event_id`,`race_race_number`,`user_id`),
  ADD KEY `fk_race_standings_race1_idx` (`race_event_id`,`race_race_number`),
  ADD KEY `fk_race_standings_user1` (`user_id`);

--
-- Indexes for table `site_settings`
--
ALTER TABLE `site_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `event`
--
ALTER TABLE `event`
  MODIFY `id` smallint(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `horse`
--
ALTER TABLE `horse`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;

--
-- AUTO_INCREMENT for table `site_settings`
--
ALTER TABLE `site_settings`
  MODIFY `id` tinyint(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` smallint(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `event`
--
ALTER TABLE `event`
  ADD CONSTRAINT `fk_event_user1` FOREIGN KEY (`champion_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `event_standings`
--
ALTER TABLE `event_standings`
  ADD CONSTRAINT `fk_event_standings_event1` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_event_standings_user1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `horse`
--
ALTER TABLE `horse`
  ADD CONSTRAINT `fk_horse_race1` FOREIGN KEY (`race_event_id`,`race_race_number`) REFERENCES `race` (`event_id`, `race_number`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `pick`
--
ALTER TABLE `pick`
  ADD CONSTRAINT `fk_pick_race1` FOREIGN KEY (`race_event_id`,`race_race_number`) REFERENCES `race` (`event_id`, `race_number`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_pick_user1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `race`
--
ALTER TABLE `race`
  ADD CONSTRAINT `fk_race_event1` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `race_standings`
--
ALTER TABLE `race_standings`
  ADD CONSTRAINT `fk_race_standings_race1` FOREIGN KEY (`race_event_id`,`race_race_number`) REFERENCES `race` (`event_id`, `race_number`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_race_standings_user1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
