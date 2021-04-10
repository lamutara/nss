-- phpMyAdmin SQL Dump
-- version 4.8.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 10, 2021 at 10:42 PM
-- Server version: 5.5.62-log
-- PHP Version: 5.6.40

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `nss`
--

-- --------------------------------------------------------

--
-- Table structure for table `Contents`
--

CREATE TABLE `Contents` (
  `Seq` int(11) NOT NULL,
  `RootSeq` int(11) NOT NULL,
  `ParentSeq` int(11) NOT NULL,
  `PostDate` datetime NOT NULL,
  `Text` varchar(8191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `Del` int(11) NOT NULL DEFAULT '0',
  `Like` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `CtTag`
--

CREATE TABLE `CtTag` (
  `Seq` int(11) NOT NULL,
  `Tag` varchar(63) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `Log`
--

CREATE TABLE `Log` (
  `LogSeq` int(11) NOT NULL,
  `LogDt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Uid` varchar(16) NOT NULL DEFAULT '',
  `Pid` varchar(16) NOT NULL DEFAULT '',
  `Seq` int(11) NOT NULL DEFAULT '0',
  `REMOTE_ADDR` varchar(64) NOT NULL DEFAULT '',
  `REMOTE_HOST` varchar(64) NOT NULL DEFAULT '',
  `HTTP_REFERER` varchar(255) NOT NULL DEFAULT '',
  `HTTP_USER_AGENT` varchar(255) NOT NULL DEFAULT '',
  `Memo` varchar(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Contents`
--
ALTER TABLE `Contents`
  ADD PRIMARY KEY (`Seq`),
  ADD KEY `IDX_1` (`PostDate`),
  ADD KEY `IDX_2` (`RootSeq`);

--
-- Indexes for table `CtTag`
--
ALTER TABLE `CtTag`
  ADD PRIMARY KEY (`Seq`,`Tag`);

--
-- Indexes for table `Log`
--
ALTER TABLE `Log`
  ADD PRIMARY KEY (`LogSeq`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `Contents`
--
ALTER TABLE `Contents`
  MODIFY `Seq` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5201;

--
-- AUTO_INCREMENT for table `Log`
--
ALTER TABLE `Log`
  MODIFY `LogSeq` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32106;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
