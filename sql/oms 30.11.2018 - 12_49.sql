-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 30. Nov 2018 um 12:48
-- Server-Version: 10.1.36-MariaDB
-- PHP-Version: 7.2.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `oms`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `changelog`
--

CREATE TABLE `changelog` (
  `id` int(11) NOT NULL,
  `version` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `text` varchar(2000) COLLATE utf8_unicode_ci NOT NULL,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `new` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Daten für Tabelle `changelog`
--

INSERT INTO `changelog` (`id`, `version`, `text`, `date`, `new`) VALUES
(1, '0.1.0', 'Erste Version des Objekt Management Systems%%', '2018-11-30 12:16:07', 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `fields`
--

CREATE TABLE `fields` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `maxLength` int(11) NOT NULL,
  `minLength` int(11) DEFAULT NULL,
  `type` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `refId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Daten für Tabelle `fields`
--

INSERT INTO `fields` (`id`, `name`, `maxLength`, `minLength`, `type`, `refId`) VALUES
(1, 'Titel', 100, 4, 'text', 1),
(2, 'Bericht', 2000, 10, 'textarea', 1),
(3, 'Name', 50, 4, 'text', 2),
(5, 'Rolle', 30, 4, 'text', 2),
(9, 'Ort', 50, 0, 'text', 1),
(10, 'Datum', 50, 0, 'text', 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `fieldvalues`
--

CREATE TABLE `fieldvalues` (
  `id` int(11) NOT NULL,
  `refField` int(11) NOT NULL,
  `refObj` int(11) NOT NULL,
  `value` varchar(2048) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Daten für Tabelle `fieldvalues`
--

INSERT INTO `fieldvalues` (`id`, `refField`, `refObj`, `value`) VALUES
(1, 1, 1, 'Data'),
(2, 2, 1, 'Data2'),
(4, 9, 1, 'Data3'),
(5, 1, 2, 'Thalmässinger Music Adventure 2018'),
(6, 2, 2, 'Auch dieses Jahr war die ELJ Nennslingen am Thalmässinger Music Adventure vertreten.\nAm Mittwoch vor dem TMA haben wir uns auf den Weg gemacht und die Helfer des Fests mit selbstgemachten Waffeln versorgt. Dies kam sehr gut an und wir haben als kleines Dankeschön ein Brotzeitbreddl bekommen. Hier nochmal ein herzliches DANKESCHÖN nach Thalmässing!\n\nAm Freitag ging es dann los. Zu den Sounds von „Gestört aber Geil“ und „Mike Candys“ wurde richtig gefeiert. Man merkte am Freitag schon, dass das ganze Fest top organisiert war und es keinerlei Zwischenfälle gab. Auch Samstag haben wir uns natürlich den Spaß nicht entgehen lassen und sind nach einem kurzen Treffpunkt in der Landjugend mit dem Shuttlebus nach Thalmässing gefahren.\nNach der obligatorischen, mittlerweile traditionellen Runde Flunkyball vor der Bühne (in diesem Sinne ein sorry an den DJ der jedes Jahr miterleben muss wie wir in diesen 15 min. die Aufmerksam bekommen die er verdient hätte) haben wir mit „DJ EL MAR“ und dem darauf folgenden Haupt Act „Mashup Germany“ so richtig Vollgas gegeben. Um halb 3 sind wir dann komplett ausgepowert mit dem Shuttlebus nach Nennslingen gefahren (zumindest die Meisten ;) ).\n\nIn diesem Sinne möchte sich die ELJ Nennslingen auch noch einmal bei dem ganzen freiwilligen Helfern des „Thalmässinger Music Adventures“ bedanken, was Ihr jedes Jahr aufs Neue auf die Beine stellt ist der Oberhammer, wir freuen uns schon auf nächstes Jahr!!!'),
(7, 9, 2, 'Thalmässing'),
(10, 10, 2, '27.10.2018'),
(11, 1, 12, 'Gänsbauchfestla'),
(12, 2, 12, 'Wenn das heuer mal kein Pfund war!\nZum ersten Mal haben wir das Gänsbauchfestla veranstaltet und wir waren überwältigt wie gut es gleich im ersten Jahr bei allen Besuchern angekommen ist!\nNach langwierigen Vorbereitungen, Organisationsrunden und Teambesprechungen, konnten wir endlich mit dem Aufbau starten. Und was für ein Aufbau das war: Jeder war irgendwie dabei und konnte sich austoben. Egal ob beim Budenaufstellen oder Schildermalen, Teamgeist und Spaß waren immer dabei. An dieser Stelle ergeht auch ein großer Dank an unsere Nennslinger Unternehmer und die Gemeinde, ohne deren Hilfe (egal ob finanzieller oder materieller Natur) das Ganze nicht möglich gewesen wäre.\nDann war es endlich soweit und das Gänsbauchfestla 2018 konnte durchstarten. Zu Beginn noch gemütlich mit Biergarten und Blasmusik von unserer Nennslinger Blaskapelle, sorgten im Anschluss ALC&Band sowie DJ Hias 2000 für ausgelassene Stimmung. Partykracher wie \"Name eines Mädchens\" oder \"Meine Omy\" durften dabei natürlich nicht fehlen.\nDie ausgelassene Stimmung hielt bis in die Morgenstunden an, besonders erfreulich war dabei, dass es zu keinen Notfällen oder Problemen gekommen ist.\nBeim Abbau griffen uns dann noch ein paar Eltern unter die Arme, denen an dieser Stelle auch nochmals ein großes Danke aussprechen wollen! Insgesamt ein rundum gelungenes Event, das wir nächstes Jahr gerne wiederholen wollen!'),
(13, 9, 12, 'ELJ Heim Nennslingen'),
(14, 10, 12, '04.08.2018'),
(15, 3, 9, 'Jonas Buckel'),
(16, 5, 9, 'Schriftführer');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `images`
--

CREATE TABLE `images` (
  `id` int(11) NOT NULL,
  `public` tinyint(1) NOT NULL DEFAULT '1',
  `internal` tinyint(1) NOT NULL DEFAULT '1',
  `isCoverImage` tinyint(1) NOT NULL DEFAULT '0',
  `path` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `refId` int(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Daten für Tabelle `images`
--

INSERT INTO `images` (`id`, `public`, `internal`, `isCoverImage`, `path`, `refId`) VALUES
(4, 1, 1, 0, '../objects/Vorstand/210060/1.jpg', 10),
(5, 1, 1, 0, '../objects/Vorstand/210060/0.jpg', 10),
(25, 1, 0, 0, '../objects/Vorstand/12/image_0.jpg', 9);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `objectlist`
--

CREATE TABLE `objectlist` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `refId` int(11) NOT NULL,
  `refName` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `path` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `hasImages` tinyint(1) NOT NULL DEFAULT '0',
  `hasFields` tinyint(1) NOT NULL DEFAULT '0',
  `public` tinyint(1) NOT NULL DEFAULT '0',
  `views` int(11) NOT NULL DEFAULT '0',
  `userCreated` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `dateCreated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `userChanged` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `dateChanged` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Daten für Tabelle `objectlist`
--

INSERT INTO `objectlist` (`id`, `name`, `refId`, `refName`, `path`, `hasImages`, `hasFields`, `public`, `views`, `userCreated`, `dateCreated`, `userChanged`, `dateChanged`) VALUES
(2, 'Thalmässinger Music Adventure', 1, 'Artikel', '../objects/Artikel/530480/', 1, 1, 0, 0, 'admin', '2018-11-29 11:03:35', 'admin', '2018-11-29 11:03:35'),
(9, 'Jonas Buckel', 2, 'Vorstand', '../objects/Vorstand/12/', 1, 1, 1, 0, 'admin', '2018-11-29 12:19:55', 'admin', '2018-11-29 12:19:55'),
(10, 'Test', 2, 'Vorstand', '../objects/Vorstand/210060/', 1, 1, 0, 0, 'admin', '2018-11-29 13:57:54', 'admin', '2018-11-29 13:57:54'),
(11, '1. Mai 2018', 1, 'Artikel', '../objects/Artikel/995983/', 1, 1, 1, 0, 'admin', '2018-11-30 11:22:30', 'admin', '2018-11-30 11:22:30'),
(12, 'Gänsbauchfestla 2018', 1, 'Artikel', '../objects/Artikel/364297/', 1, 1, 1, 0, 'admin', '2018-11-30 11:23:04', 'admin', '2018-11-30 11:23:04');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `objects`
--

CREATE TABLE `objects` (
  `id` int(11) NOT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `hasImages` tinyint(1) NOT NULL DEFAULT '0',
  `hasFields` tinyint(1) NOT NULL DEFAULT '0',
  `views` int(11) NOT NULL,
  `dateCreated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `userCreated` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `dateChanged` datetime NOT NULL,
  `userChanged` varchar(100) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Daten für Tabelle `objects`
--

INSERT INTO `objects` (`id`, `name`, `hasImages`, `hasFields`, `views`, `dateCreated`, `userCreated`, `dateChanged`, `userChanged`) VALUES
(1, 'Artikel', 1, 1, 0, '2018-02-10 12:34:15', 'admin', '2018-02-10 12:34:15', 'admin'),
(2, 'Vorstand', 1, 1, 0, '2018-11-20 09:38:37', 'admin', '2018-11-20 09:38:37', 'admin');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `username` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `surname` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `rights` int(5) NOT NULL DEFAULT '0',
  `isAdminAllowed` tinyint(1) NOT NULL DEFAULT '0',
  `editable` tinyint(1) NOT NULL DEFAULT '1',
  `dateCreated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `userCreated` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `dateChanged` datetime NOT NULL,
  `userChanged` varchar(128) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Daten für Tabelle `user`
--

INSERT INTO `user` (`id`, `username`, `password`, `surname`, `name`, `rights`, `isAdminAllowed`, `editable`, `dateCreated`, `userCreated`, `dateChanged`, `userChanged`) VALUES
(1, 'admin', '$2y$10$UWTdPCKtLPN2ecOerw28ku3IMu7g7/vru1DVkRFoCYMI8ABKjgk66', 'Administrator', 'Administrator', 10, 1, 0, '2018-02-10 00:00:00', 'admin', '2018-11-08 12:05:51', 'admin');

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `changelog`
--
ALTER TABLE `changelog`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `version` (`version`);

--
-- Indizes für die Tabelle `fields`
--
ALTER TABLE `fields`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `fieldvalues`
--
ALTER TABLE `fieldvalues`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `images`
--
ALTER TABLE `images`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `objectlist`
--
ALTER TABLE `objectlist`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `objects`
--
ALTER TABLE `objects`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indizes für die Tabelle `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `changelog`
--
ALTER TABLE `changelog`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT für Tabelle `fields`
--
ALTER TABLE `fields`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT für Tabelle `fieldvalues`
--
ALTER TABLE `fieldvalues`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT für Tabelle `images`
--
ALTER TABLE `images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT für Tabelle `objectlist`
--
ALTER TABLE `objectlist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT für Tabelle `objects`
--
ALTER TABLE `objects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT für Tabelle `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
