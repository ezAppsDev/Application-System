-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 13, 2019 at 06:57 PM
-- Server version: 10.4.6-MariaDB
-- PHP Version: 7.3.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `app_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `applicants`
--

CREATE TABLE `applicants` (
  `id` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `app` int(11) NOT NULL,
  `status` enum('PENDING','DENIED','ACCEPTED') NOT NULL DEFAULT 'PENDING',
  `denial_reason` text DEFAULT NULL,
  `accepted_by` text DEFAULT NULL,
  `created` text NOT NULL,
  `format` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `applicant_comments`
--

CREATE TABLE `applicant_comments` (
  `id` int(11) NOT NULL,
  `aid` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `created` text NOT NULL,
  `msg` text NOT NULL,
  `hidden` enum('false','true') NOT NULL DEFAULT 'false'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `applications`
--

CREATE TABLE `applications` (
  `id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `status` enum('OPEN','CLOSED','ON-HOLD') NOT NULL DEFAULT 'OPEN',
  `created` text NOT NULL,
  `format` text NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `logs`
--

CREATE TABLE `logs` (
  `id` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `datetime` text NOT NULL,
  `ip` text NOT NULL,
  `action` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL DEFAULT 'Application System',
  `discord_webhook` text DEFAULT NULL,
  `wh_app_declined` enum('true','false') NOT NULL DEFAULT 'true',
  `wh_app_accepted` enum('true','false') NOT NULL DEFAULT 'true',
  `wh_app_created` enum('true','false') NOT NULL DEFAULT 'true',
  `app_accept_message` varchar(355) NOT NULL DEFAULT '''Your application has been accepted!''',
  `theme` varchar(64) NOT NULL DEFAULT 'default'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `name`, `discord_webhook`, `wh_app_declined`, `wh_app_accepted`, `wh_app_created`, `app_accept_message`, `theme`) VALUES
(1, 'ezApps', NULL, 'true', 'true', 'true', 'Your application has been accepted!', 'dark');

-- --------------------------------------------------------

--
-- Table structure for table `usergroups`
--

CREATE TABLE `usergroups` (
  `id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `access` enum('true','false') NOT NULL DEFAULT 'true',
  `super_admin` enum('true','false') NOT NULL DEFAULT 'false',
  `view_apps` enum('true','false') NOT NULL DEFAULT 'false',
  `review_apps` enum('true','false') NOT NULL DEFAULT 'false',
  `view_users` enum('true','false') NOT NULL DEFAULT 'false',
  `view_usergroups` enum('true','false') NOT NULL DEFAULT 'false',
  `edit_users` enum('true','false') NOT NULL DEFAULT 'false',
  `edit_usergroups` enum('true','false') NOT NULL DEFAULT 'false'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `usergroups`
--

INSERT INTO `usergroups` (`id`, `name`, `access`, `super_admin`, `view_apps`, `review_apps`, `view_users`, `view_usergroups`, `edit_users`, `edit_usergroups`) VALUES
(0, 'Banned', 'false', 'false', 'false', 'false', 'false', 'false', 'false', 'false'),
(1, 'User', 'true', 'false', 'false', 'false', 'false', 'false', 'false', 'false'),
(2, 'Admin', 'true', 'true', 'false', 'false', 'false', 'true', 'false', 'false');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `display_name` varchar(64) NOT NULL,
  `password` text NOT NULL,
  `joined` varchar(64) NOT NULL,
  `usergroup` int(11) NOT NULL DEFAULT 1,
  `discord_id` text DEFAULT NULL,
  `avatar` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `applicants`
--
ALTER TABLE `applicants`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `applicant_comments`
--
ALTER TABLE `applicant_comments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `applications`
--
ALTER TABLE `applications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `usergroups`
--
ALTER TABLE `usergroups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `applicants`
--
ALTER TABLE `applicants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `applicant_comments`
--
ALTER TABLE `applicant_comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `applications`
--
ALTER TABLE `applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `usergroups`
--
ALTER TABLE `usergroups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
