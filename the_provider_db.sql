-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Värd: 127.0.0.1
-- Tid vid skapande: 14 okt 2022 kl 11:29
-- Serverversion: 10.4.24-MariaDB
-- PHP-version: 8.1.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Databas: `the_provider_db`
--

-- --------------------------------------------------------

--
-- Tabellstruktur `blog_post`
--

CREATE TABLE `blog_post` (
  `ID` int(11) NOT NULL,
  `serviceID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_swedish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_swedish_ci;

-- --------------------------------------------------------

--
-- Tabellstruktur `calendar_event`
--

CREATE TABLE `calendar_event` (
  `ID` int(11) NOT NULL,
  `serviceID` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_swedish_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_swedish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_swedish_ci;

-- --------------------------------------------------------

--
-- Tabellstruktur `calendar_invite`
--

CREATE TABLE `calendar_invite` (
  `ID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `eventID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_swedish_ci;

-- --------------------------------------------------------

--
-- Tabellstruktur `content`
--

CREATE TABLE `content` (
  `ID` int(11) NOT NULL,
  `pageID` int(11) NOT NULL,
  `postID` int(11) NOT NULL,
  `versionID` int(11) NOT NULL,
  `HTML_element` varchar(255) COLLATE utf8mb4_swedish_ci NOT NULL,
  `contents` text COLLATE utf8mb4_swedish_ci NOT NULL,
  `imgurl` varchar(255) COLLATE utf8mb4_swedish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_swedish_ci;

-- --------------------------------------------------------

--
-- Tabellstruktur `end_user`
--

CREATE TABLE `end_user` (
  `ID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `serviceID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_swedish_ci;

-- --------------------------------------------------------

--
-- Tabellstruktur `service`
--

CREATE TABLE `service` (
  `ID` int(11) NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_swedish_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_swedish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_swedish_ci;

-- --------------------------------------------------------

--
-- Tabellstruktur `user`
--

CREATE TABLE `user` (
  `ID` int(11) NOT NULL,
  `admin` tinyint(1) NOT NULL,
  `ban` tinyint(1) NOT NULL,
  `displayname` varchar(255) COLLATE utf8mb4_swedish_ci NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_swedish_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_swedish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_swedish_ci;

-- --------------------------------------------------------

--
-- Tabellstruktur `wiki_page`
--

CREATE TABLE `wiki_page` (
  `ID` int(11) NOT NULL,
  `serviceID` int(11) NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_swedish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_swedish_ci;

-- --------------------------------------------------------

--
-- Tabellstruktur `wiki_page_version`
--

CREATE TABLE `wiki_page_version` (
  `ID` int(11) NOT NULL,
  `pageID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_swedish_ci;

--
-- Index för dumpade tabeller
--

--
-- Index för tabell `blog_post`
--
ALTER TABLE `blog_post`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `serviceID` (`serviceID`,`userID`);

--
-- Index för tabell `calendar_event`
--
ALTER TABLE `calendar_event`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `serviceID` (`serviceID`);

--
-- Index för tabell `calendar_invite`
--
ALTER TABLE `calendar_invite`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `userID` (`userID`,`eventID`);

--
-- Index för tabell `content`
--
ALTER TABLE `content`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `pageID` (`pageID`,`postID`,`versionID`);

--
-- Index för tabell `end_user`
--
ALTER TABLE `end_user`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `userID` (`userID`,`serviceID`);

--
-- Index för tabell `service`
--
ALTER TABLE `service`
  ADD PRIMARY KEY (`ID`);

--
-- Index för tabell `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`ID`);

--
-- Index för tabell `wiki_page`
--
ALTER TABLE `wiki_page`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `serviceID` (`serviceID`);

--
-- Index för tabell `wiki_page_version`
--
ALTER TABLE `wiki_page_version`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `pageID` (`pageID`,`userID`);

--
-- AUTO_INCREMENT för dumpade tabeller
--

--
-- AUTO_INCREMENT för tabell `blog_post`
--
ALTER TABLE `blog_post`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT för tabell `calendar_event`
--
ALTER TABLE `calendar_event`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT för tabell `calendar_invite`
--
ALTER TABLE `calendar_invite`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT för tabell `content`
--
ALTER TABLE `content`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT för tabell `end_user`
--
ALTER TABLE `end_user`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT för tabell `service`
--
ALTER TABLE `service`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT för tabell `user`
--
ALTER TABLE `user`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT för tabell `wiki_page`
--
ALTER TABLE `wiki_page`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT för tabell `wiki_page_version`
--
ALTER TABLE `wiki_page_version`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
