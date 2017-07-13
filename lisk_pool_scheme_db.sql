-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 08, 2017 at 07:23 PM
-- Server version: 10.0.29-MariaDB-0ubuntu0.16.04.1
-- PHP Version: 7.0.15-0ubuntu0.16.04.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `lisk_template`
--

-- --------------------------------------------------------

--
-- Table structure for table `blocks`
--

CREATE TABLE `blocks` (
  `id` int(11) NOT NULL,
  `blockid` int(19) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `liskstats`
--

CREATE TABLE `liskstats` (
  `id` int(11) NOT NULL,
  `object` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `miners`
--

CREATE TABLE `miners` (
  `id` int(11) NOT NULL,
  `address` varchar(64) NOT NULL,
  `balance` bigint(19) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `miner_balance`
--

CREATE TABLE `miner_balance` (
  `id` int(11) NOT NULL,
  `miner` varchar(64) NOT NULL,
  `value` varchar(64) NOT NULL,
  `var_timestamp` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `payout_history`
--

CREATE TABLE `payout_history` (
  `id` int(11) NOT NULL,
  `address` varchar(64) NOT NULL,
  `balance` varchar(64) NOT NULL,
  `time` varchar(32) NOT NULL,
  `txid` varchar(128) NOT NULL,
  `fee` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `pool_balance`
--

CREATE TABLE `pool_balance` (
  `id` int(11) NOT NULL,
  `value` varchar(64) NOT NULL,
  `var_timestamp` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `pool_rank`
--

CREATE TABLE `pool_rank` (
  `id` int(11) NOT NULL,
  `value` varchar(64) NOT NULL,
  `var_timestamp` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `pool_votepower`
--

CREATE TABLE `pool_votepower` (
  `id` int(11) NOT NULL,
  `votepower` varchar(64) NOT NULL,
  `val_timestamp` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `pool_voters`
--

CREATE TABLE `pool_voters` (
  `id` int(11) NOT NULL,
  `value` varchar(64) NOT NULL,
  `var_timestamp` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `stats`
--

CREATE TABLE `stats` (
  `id` bigint(11) NOT NULL,
  `user` varchar(64) NOT NULL,
  `userid` varchar(16) NOT NULL,
  `hashrate` varchar(64) NOT NULL,
  `val_timestamp` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `blocks`
--
ALTER TABLE `blocks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `liskstats`
--
ALTER TABLE `liskstats`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `miners`
--
ALTER TABLE `miners`
  ADD PRIMARY KEY (`id`),
  ADD KEY `address` (`address`),
  ADD KEY `balance` (`balance`);

--
-- Indexes for table `miner_balance`
--
ALTER TABLE `miner_balance`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `miner` (`miner`);

--
-- Indexes for table `payout_history`
--
ALTER TABLE `payout_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `address` (`address`);

--
-- Indexes for table `pool_balance`
--
ALTER TABLE `pool_balance`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pool_rank`
--
ALTER TABLE `pool_rank`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pool_votepower`
--
ALTER TABLE `pool_votepower`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pool_voters`
--
ALTER TABLE `pool_voters`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stats`
--
ALTER TABLE `stats`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user` (`user`),
  ADD KEY `id` (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `blocks`
--
ALTER TABLE `blocks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `liskstats`
--
ALTER TABLE `liskstats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `miners`
--
ALTER TABLE `miners`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `miner_balance`
--

ALTER TABLE `miner_balance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `payout_history`
--
ALTER TABLE `payout_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `pool_balance`
--
ALTER TABLE `pool_balance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `pool_rank`
--
ALTER TABLE `pool_rank`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `pool_votepower`
--
ALTER TABLE `pool_votepower`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `pool_voters`
--
ALTER TABLE `pool_voters`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `stats`
--
ALTER TABLE `stats`
  MODIFY `id` bigint(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
