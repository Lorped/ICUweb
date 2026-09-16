-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: 89.46.68.60:3306
-- Creato il: Set 16, 2026 alle 12:46
-- Versione del server: 5.0.96-community-log
-- Versione PHP: 8.0.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `Sql153576_5`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `clan`
--

CREATE TABLE `clan` (
  `IDclan` int(11) NOT NULL,
  `Nomeclan` char(50) COLLATE utf8_unicode_ci NOT NULL,
  `clanimg` char(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Disc1` int(11) NOT NULL,
  `Disc2` int(11) NOT NULL,
  `Disc3` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `cond_oggetti`
--

CREATE TABLE `cond_oggetti` (
  `IDcondizione` int(11) NOT NULL,
  `IDoggetto` int(11) NOT NULL,
  `tipocond` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  `tabcond` int(11) NOT NULL,
  `valcond` int(11) NOT NULL,
  `descrX` varchar(1024) COLLATE utf8_unicode_ci DEFAULT NULL,
  `risp` varchar(2) COLLATE utf8_unicode_ci DEFAULT NULL,
  `subskill` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `discipline`
--

CREATE TABLE `discipline` (
  `IDdisciplina` int(11) NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL,
  `livello` smallint(6) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `discipline_main`
--

CREATE TABLE `discipline_main` (
  `IDdisciplina` int(11) NOT NULL,
  `nomedisciplina` varchar(35) CHARACTER SET utf8 NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `dominio`
--

CREATE TABLE `dominio` (
  `IDdominio` int(11) NOT NULL,
  `nomedominio` varchar(50) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `logscanfull`
--

CREATE TABLE `logscanfull` (
  `IDscan` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `IDoggetto` int(11) NOT NULL,
  `motivo` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `descrizione` varchar(1024) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `logscanogg`
--

CREATE TABLE `logscanogg` (
  `IDoggetto` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `datascan` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `logscanpaired`
--

CREATE TABLE `logscanpaired` (
  `IDoggetto` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `datascan` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `messaggi`
--

CREATE TABLE `messaggi` (
  `ID` bigint(20) NOT NULL,
  `idutente` int(11) NOT NULL,
  `nomepg` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `Ora` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `Testo` varchar(2000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Destinatario` int(11) NOT NULL DEFAULT '-1',
  `clan` int(11) NOT NULL DEFAULT '-1'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci PACK_KEYS=0;

-- --------------------------------------------------------

--
-- Struttura della tabella `oggetti`
--

CREATE TABLE `oggetti` (
  `IDoggetto` int(11) NOT NULL,
  `barcode` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `nomeoggetto` varchar(1024) COLLATE utf8_unicode_ci NOT NULL,
  `descrizione` text COLLATE utf8_unicode_ci NOT NULL,
  `fissomobile` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  `ifdomanda` smallint(6) NOT NULL DEFAULT '0',
  `domanda` varchar(1024) COLLATE utf8_unicode_ci DEFAULT NULL,
  `r1` varchar(1024) COLLATE utf8_unicode_ci DEFAULT NULL,
  `r2` varchar(1024) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `paired`
--

CREATE TABLE `paired` (
  `IDoggetto1` int(11) NOT NULL,
  `IDoggetto2` int(11) NOT NULL,
  `Paired` varchar(1024) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `personaggio`
--

CREATE TABLE `personaggio` (
  `user_id` int(11) NOT NULL,
  `nomeutente` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `nomeplayer` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `nomepg` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `IDclan` int(11) NOT NULL,
  `IDsocieta` int(11) NOT NULL,
  `IDdominio` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `push_subscriptions`
--

CREATE TABLE `push_subscriptions` (
  `user_id` int(20) NOT NULL,
  `token` varchar(512) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `skill`
--

CREATE TABLE `skill` (
  `IDskill` int(11) NOT NULL,
  `livello` smallint(6) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `skill_main`
--

CREATE TABLE `skill_main` (
  `IDskill` int(11) NOT NULL,
  `nomeskill` varchar(30) CHARACTER SET utf8 NOT NULL,
  `subskill` int(11) NOT NULL,
  `tipologia` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `societa`
--

CREATE TABLE `societa` (
  `IDsocieta` int(11) NOT NULL,
  `nomesocieta` varchar(50) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `clan`
--
ALTER TABLE `clan`
  ADD PRIMARY KEY (`IDclan`);

--
-- Indici per le tabelle `cond_oggetti`
--
ALTER TABLE `cond_oggetti`
  ADD PRIMARY KEY (`IDcondizione`),
  ADD KEY `idoggetto` (`IDoggetto`);

--
-- Indici per le tabelle `discipline`
--
ALTER TABLE `discipline`
  ADD UNIQUE KEY `IDdisciplina` (`IDdisciplina`,`user_id`);

--
-- Indici per le tabelle `discipline_main`
--
ALTER TABLE `discipline_main`
  ADD PRIMARY KEY (`IDdisciplina`);

--
-- Indici per le tabelle `dominio`
--
ALTER TABLE `dominio`
  ADD PRIMARY KEY (`IDdominio`);

--
-- Indici per le tabelle `logscanfull`
--
ALTER TABLE `logscanfull`
  ADD PRIMARY KEY (`IDscan`),
  ADD UNIQUE KEY `user_id` (`user_id`,`IDoggetto`,`motivo`);

--
-- Indici per le tabelle `logscanogg`
--
ALTER TABLE `logscanogg`
  ADD UNIQUE KEY `scanuserogg` (`IDoggetto`,`user_id`);

--
-- Indici per le tabelle `logscanpaired`
--
ALTER TABLE `logscanpaired`
  ADD PRIMARY KEY (`IDoggetto`,`user_id`);

--
-- Indici per le tabelle `messaggi`
--
ALTER TABLE `messaggi`
  ADD PRIMARY KEY (`ID`);

--
-- Indici per le tabelle `oggetti`
--
ALTER TABLE `oggetti`
  ADD PRIMARY KEY (`IDoggetto`),
  ADD UNIQUE KEY `barcode` (`barcode`);

--
-- Indici per le tabelle `paired`
--
ALTER TABLE `paired`
  ADD UNIQUE KEY `IDoggetto1` (`IDoggetto1`,`IDoggetto2`);

--
-- Indici per le tabelle `personaggio`
--
ALTER TABLE `personaggio`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `nome` (`nomepg`);

--
-- Indici per le tabelle `push_subscriptions`
--
ALTER TABLE `push_subscriptions`
  ADD PRIMARY KEY (`user_id`);

--
-- Indici per le tabelle `skill`
--
ALTER TABLE `skill`
  ADD UNIQUE KEY `IDSkill` (`IDskill`,`user_id`);

--
-- Indici per le tabelle `skill_main`
--
ALTER TABLE `skill_main`
  ADD PRIMARY KEY (`IDskill`);

--
-- Indici per le tabelle `societa`
--
ALTER TABLE `societa`
  ADD PRIMARY KEY (`IDsocieta`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `clan`
--
ALTER TABLE `clan`
  MODIFY `IDclan` int(11) NOT NULL auto_increment;

--
-- AUTO_INCREMENT per la tabella `cond_oggetti`
--
ALTER TABLE `cond_oggetti`
  MODIFY `IDcondizione` int(11) NOT NULL auto_increment;

--
-- AUTO_INCREMENT per la tabella `dominio`
--
ALTER TABLE `dominio`
  MODIFY `IDdominio` int(11) NOT NULL auto_increment;

--
-- AUTO_INCREMENT per la tabella `logscanfull`
--
ALTER TABLE `logscanfull`
  MODIFY `IDscan` int(11) NOT NULL auto_increment;

--
-- AUTO_INCREMENT per la tabella `messaggi`
--
ALTER TABLE `messaggi`
  MODIFY `ID` bigint(20) NOT NULL auto_increment;

--
-- AUTO_INCREMENT per la tabella `oggetti`
--
ALTER TABLE `oggetti`
  MODIFY `IDoggetto` int(11) NOT NULL auto_increment;

--
-- AUTO_INCREMENT per la tabella `personaggio`
--
ALTER TABLE `personaggio`
  MODIFY `user_id` int(11) NOT NULL auto_increment;

--
-- AUTO_INCREMENT per la tabella `skill_main`
--
ALTER TABLE `skill_main`
  MODIFY `IDskill` int(11) NOT NULL auto_increment;

--
-- AUTO_INCREMENT per la tabella `societa`
--
ALTER TABLE `societa`
  MODIFY `IDsocieta` int(11) NOT NULL auto_increment;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
