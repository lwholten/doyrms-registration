-- Note that this file is an SQL dump used to create the database necessary for the application

-- phpMyAdmin SQL Dump
-- version 5.1.1deb5ubuntu1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Feb 04, 2023 at 09:40 PM
-- Server version: 8.0.32-0ubuntu0.22.04.2
-- PHP Version: 8.1.2-1ubuntu2.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dregDB`
--

-- --------------------------------------------------------

--
-- Table structure for table `AwayUsers`
--

CREATE TABLE `AwayUsers` (
  `AwayLogID` int NOT NULL,
  `UserID` int NOT NULL,
  `DateTimeAway` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `DateTimeReturn` datetime DEFAULT NULL,
  `Reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Events`
--

CREATE TABLE `Events` (
  `EventID` int NOT NULL,
  `Event` char(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `LocationID` int DEFAULT NULL,
  `StartTime` time NOT NULL,
  `EndTime` time NOT NULL,
  `Deviation` int NOT NULL DEFAULT '0',
  `Days` varchar(7) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Alerts` tinyint(1) NOT NULL DEFAULT '0',
  `SignInEvent` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Locations`
--

CREATE TABLE `Locations` (
  `LocationID` int NOT NULL,
  `LocationName` char(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `KeyWords` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `Description` char(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Log`
--

CREATE TABLE `Log` (
  `LogID` int NOT NULL,
  `UserID` int NOT NULL,
  `LocationID` int DEFAULT NULL,
  `LogTime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `EventID` int DEFAULT NULL,
  `MinutesLate` int DEFAULT NULL,
  `Auto` tinyint(1) NOT NULL DEFAULT '0',
  `StaffAction` tinyint(1) NOT NULL DEFAULT '0',
  `StaffMessage` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `RestrictedUsers`
--

CREATE TABLE `RestrictedUsers` (
  `RestrictedLogID` int NOT NULL,
  `UserID` int NOT NULL,
  `DateTimeRestricted` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `DateTimeUnrestricted` datetime DEFAULT NULL,
  `Reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Staff`
--

CREATE TABLE `Staff` (
  `StaffID` int NOT NULL,
  `Username` char(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Forename` char(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Surname` char(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Email` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `Salt` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Hash` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `AccessLevel` int NOT NULL DEFAULT '1',
  `LastChangedPassword` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Users`
--

CREATE TABLE `Users` (
  `UserID` int NOT NULL,
  `Forename` char(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Surname` char(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Email` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `Gender` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `RoomNumber` int DEFAULT NULL,
  `Initials` char(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `LocationID` int DEFAULT NULL,
  `LastActive` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `AwayUsers`
--
ALTER TABLE `AwayUsers`
  ADD PRIMARY KEY (`AwayLogID`),
  ADD UNIQUE KEY `AwayLogID` (`AwayLogID`);

--
-- Indexes for table `Events`
--
ALTER TABLE `Events`
  ADD PRIMARY KEY (`EventID`),
  ADD UNIQUE KEY `EventID` (`EventID`);

--
-- Indexes for table `Locations`
--
ALTER TABLE `Locations`
  ADD PRIMARY KEY (`LocationID`),
  ADD UNIQUE KEY `LocationID` (`LocationID`);

--
-- Indexes for table `Log`
--
ALTER TABLE `Log`
  ADD PRIMARY KEY (`LogID`),
  ADD UNIQUE KEY `LogID` (`LogID`);

--
-- Indexes for table `RestrictedUsers`
--
ALTER TABLE `RestrictedUsers`
  ADD PRIMARY KEY (`RestrictedLogID`),
  ADD UNIQUE KEY `RestrictedLogID` (`RestrictedLogID`);

--
-- Indexes for table `Staff`
--
ALTER TABLE `Staff`
  ADD PRIMARY KEY (`StaffID`),
  ADD UNIQUE KEY `StaffID` (`StaffID`),
  ADD UNIQUE KEY `StaffID_2` (`StaffID`,`Username`);

--
-- Indexes for table `Users`
--
ALTER TABLE `Users`
  ADD PRIMARY KEY (`UserID`),
  ADD UNIQUE KEY `UserID` (`UserID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `AwayUsers`
--
ALTER TABLE `AwayUsers`
  MODIFY `AwayLogID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Events`
--
ALTER TABLE `Events`
  MODIFY `EventID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Locations`
--
ALTER TABLE `Locations`
  MODIFY `LocationID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Log`
--
ALTER TABLE `Log`
  MODIFY `LogID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `RestrictedUsers`
--
ALTER TABLE `RestrictedUsers`
  MODIFY `RestrictedLogID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Staff`
--
ALTER TABLE `Staff`
  MODIFY `StaffID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Users`
--
ALTER TABLE `Users`
  MODIFY `UserID` int NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
