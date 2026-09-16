-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:3306
-- Généré le : lun. 14 sep. 2026 à 20:12
-- Version du serveur : 11.4.13-MariaDB
-- Version de PHP : 8.4.24

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `diso2055_gestion_hr`
--

-- --------------------------------------------------------

--
-- Structure de la table `activity_log`
--

CREATE TABLE `activity_log` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `log_name` varchar(255) DEFAULT NULL,
  `description` text NOT NULL,
  `subject_type` varchar(255) DEFAULT NULL,
  `event` varchar(255) DEFAULT NULL,
  `subject_id` bigint(20) UNSIGNED DEFAULT NULL,
  `causer_type` varchar(255) DEFAULT NULL,
  `causer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `properties` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`properties`)),
  `batch_uuid` char(36) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('gestion-hr-cache-356a192b7913b04c54574d18c28d46e6395428ab', 'i:1;', 1789330282),
('gestion-hr-cache-356a192b7913b04c54574d18c28d46e6395428ab:timer', 'i:1789330282;', 1789330282),
('gestion-hr-cache-a72b20062ec2c47ab2ceb97ac1bee818f8b6c6cb', 'i:1;', 1789349492),
('gestion-hr-cache-a72b20062ec2c47ab2ceb97ac1bee818f8b6c6cb:timer', 'i:1789349492;', 1789349492),
('gestion-hr-cache-af3e133428b9e25c55bc59fe534248e6a0c0f17b', 'i:1;', 1789182289),
('gestion-hr-cache-af3e133428b9e25c55bc59fe534248e6a0c0f17b:timer', 'i:1789182289;', 1789182289),
('gestion-hr-cache-b4c96d80854dd27e76d8cc9e21960eebda52e962', 'i:1;', 1789207944),
('gestion-hr-cache-b4c96d80854dd27e76d8cc9e21960eebda52e962:timer', 'i:1789207944;', 1789207944),
('gestion-hr-cache-livewire-rate-limiter:965a43fa7337ce0d1fc664eb662e3e161ddca656', 'i:1;', 1789348806),
('gestion-hr-cache-livewire-rate-limiter:965a43fa7337ce0d1fc664eb662e3e161ddca656:timer', 'i:1789348806;', 1789348806),
('gestion-hr-cache-livewire-rate-limiter:bcecf399c2bdf175e33c744d01d8e5fef692b1fc', 'i:1;', 1789343401),
('gestion-hr-cache-livewire-rate-limiter:bcecf399c2bdf175e33c744d01d8e5fef692b1fc:timer', 'i:1789343401;', 1789343401),
('gestion-hr-cache-spatie.permission.cache', 'a:3:{s:5:\"alias\";a:4:{s:1:\"a\";s:2:\"id\";s:1:\"b\";s:4:\"name\";s:1:\"c\";s:10:\"guard_name\";s:1:\"r\";s:5:\"roles\";}s:11:\"permissions\";a:194:{i:0;a:4:{s:1:\"a\";i:1;s:1:\"b\";s:16:\"ViewAny:Activity\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:1;a:4:{s:1:\"a\";i:2;s:1:\"b\";s:13:\"View:Activity\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:2;a:4:{s:1:\"a\";i:3;s:1:\"b\";s:15:\"Create:Activity\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:3;a:4:{s:1:\"a\";i:4;s:1:\"b\";s:15:\"Update:Activity\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:4;a:4:{s:1:\"a\";i:5;s:1:\"b\";s:15:\"Delete:Activity\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:5;a:4:{s:1:\"a\";i:6;s:1:\"b\";s:18:\"DeleteAny:Activity\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:6;a:4:{s:1:\"a\";i:7;s:1:\"b\";s:16:\"Restore:Activity\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:7;a:4:{s:1:\"a\";i:8;s:1:\"b\";s:20:\"ForceDelete:Activity\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:8;a:4:{s:1:\"a\";i:9;s:1:\"b\";s:23:\"ForceDeleteAny:Activity\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:9;a:4:{s:1:\"a\";i:10;s:1:\"b\";s:19:\"RestoreAny:Activity\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:10;a:4:{s:1:\"a\";i:11;s:1:\"b\";s:18:\"Replicate:Activity\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:11;a:4:{s:1:\"a\";i:12;s:1:\"b\";s:16:\"Reorder:Activity\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:12;a:4:{s:1:\"a\";i:13;s:1:\"b\";s:23:\"ViewAny:DocumentRequest\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:9:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:6;i:5;i:7;i:6;i:8;i:7;i:9;i:8;i:11;}}i:13;a:4:{s:1:\"a\";i:14;s:1:\"b\";s:20:\"View:DocumentRequest\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:9:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:6;i:5;i:7;i:6;i:8;i:7;i:9;i:8;i:11;}}i:14;a:4:{s:1:\"a\";i:15;s:1:\"b\";s:22:\"Create:DocumentRequest\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:9:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:6;i:5;i:7;i:6;i:8;i:7;i:9;i:8;i:11;}}i:15;a:4:{s:1:\"a\";i:16;s:1:\"b\";s:22:\"Update:DocumentRequest\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;}}i:16;a:4:{s:1:\"a\";i:17;s:1:\"b\";s:22:\"Delete:DocumentRequest\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:17;a:4:{s:1:\"a\";i:18;s:1:\"b\";s:25:\"DeleteAny:DocumentRequest\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:18;a:4:{s:1:\"a\";i:19;s:1:\"b\";s:23:\"Restore:DocumentRequest\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:19;a:4:{s:1:\"a\";i:20;s:1:\"b\";s:27:\"ForceDelete:DocumentRequest\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:20;a:4:{s:1:\"a\";i:21;s:1:\"b\";s:30:\"ForceDeleteAny:DocumentRequest\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:21;a:4:{s:1:\"a\";i:22;s:1:\"b\";s:26:\"RestoreAny:DocumentRequest\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:22;a:4:{s:1:\"a\";i:23;s:1:\"b\";s:25:\"Replicate:DocumentRequest\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:23;a:4:{s:1:\"a\";i:24;s:1:\"b\";s:23:\"Reorder:DocumentRequest\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:24;a:4:{s:1:\"a\";i:25;s:1:\"b\";s:27:\"ViewAny:CommunicationMethod\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:25;a:4:{s:1:\"a\";i:26;s:1:\"b\";s:24:\"View:CommunicationMethod\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:26;a:4:{s:1:\"a\";i:27;s:1:\"b\";s:26:\"Create:CommunicationMethod\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:27;a:4:{s:1:\"a\";i:28;s:1:\"b\";s:26:\"Update:CommunicationMethod\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:28;a:4:{s:1:\"a\";i:29;s:1:\"b\";s:26:\"Delete:CommunicationMethod\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:29;a:4:{s:1:\"a\";i:30;s:1:\"b\";s:29:\"DeleteAny:CommunicationMethod\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:30;a:4:{s:1:\"a\";i:31;s:1:\"b\";s:27:\"Restore:CommunicationMethod\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:31;a:4:{s:1:\"a\";i:32;s:1:\"b\";s:31:\"ForceDelete:CommunicationMethod\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:32;a:4:{s:1:\"a\";i:33;s:1:\"b\";s:34:\"ForceDeleteAny:CommunicationMethod\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:33;a:4:{s:1:\"a\";i:34;s:1:\"b\";s:30:\"RestoreAny:CommunicationMethod\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:34;a:4:{s:1:\"a\";i:35;s:1:\"b\";s:29:\"Replicate:CommunicationMethod\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:35;a:4:{s:1:\"a\";i:36;s:1:\"b\";s:27:\"Reorder:CommunicationMethod\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:36;a:4:{s:1:\"a\";i:37;s:1:\"b\";s:16:\"ViewAny:Employee\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;}}i:37;a:4:{s:1:\"a\";i:38;s:1:\"b\";s:13:\"View:Employee\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:9:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:6;i:5;i:7;i:6;i:8;i:7;i:9;i:8;i:11;}}i:38;a:4:{s:1:\"a\";i:39;s:1:\"b\";s:15:\"Create:Employee\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;}}i:39;a:4:{s:1:\"a\";i:40;s:1:\"b\";s:15:\"Update:Employee\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:9:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:6;i:5;i:7;i:6;i:8;i:7;i:9;i:8;i:11;}}i:40;a:4:{s:1:\"a\";i:41;s:1:\"b\";s:15:\"Delete:Employee\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:41;a:4:{s:1:\"a\";i:42;s:1:\"b\";s:18:\"DeleteAny:Employee\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:42;a:4:{s:1:\"a\";i:43;s:1:\"b\";s:16:\"Restore:Employee\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:43;a:4:{s:1:\"a\";i:44;s:1:\"b\";s:20:\"ForceDelete:Employee\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:44;a:4:{s:1:\"a\";i:45;s:1:\"b\";s:23:\"ForceDeleteAny:Employee\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:45;a:4:{s:1:\"a\";i:46;s:1:\"b\";s:19:\"RestoreAny:Employee\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:46;a:4:{s:1:\"a\";i:47;s:1:\"b\";s:18:\"Replicate:Employee\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:47;a:4:{s:1:\"a\";i:48;s:1:\"b\";s:16:\"Reorder:Employee\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:48;a:4:{s:1:\"a\";i:49;s:1:\"b\";s:14:\"ViewAny:Groupe\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:49;a:4:{s:1:\"a\";i:50;s:1:\"b\";s:11:\"View:Groupe\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:50;a:4:{s:1:\"a\";i:51;s:1:\"b\";s:13:\"Create:Groupe\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:51;a:4:{s:1:\"a\";i:52;s:1:\"b\";s:13:\"Update:Groupe\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:52;a:4:{s:1:\"a\";i:53;s:1:\"b\";s:13:\"Delete:Groupe\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:53;a:4:{s:1:\"a\";i:54;s:1:\"b\";s:16:\"DeleteAny:Groupe\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:54;a:4:{s:1:\"a\";i:55;s:1:\"b\";s:14:\"Restore:Groupe\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:55;a:4:{s:1:\"a\";i:56;s:1:\"b\";s:18:\"ForceDelete:Groupe\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:56;a:4:{s:1:\"a\";i:57;s:1:\"b\";s:21:\"ForceDeleteAny:Groupe\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:57;a:4:{s:1:\"a\";i:58;s:1:\"b\";s:17:\"RestoreAny:Groupe\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:58;a:4:{s:1:\"a\";i:59;s:1:\"b\";s:16:\"Replicate:Groupe\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:59;a:4:{s:1:\"a\";i:60;s:1:\"b\";s:14:\"Reorder:Groupe\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:60;a:4:{s:1:\"a\";i:61;s:1:\"b\";s:13:\"ViewAny:Leave\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:9:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:6;i:5;i:7;i:6;i:8;i:7;i:9;i:8;i:11;}}i:61;a:4:{s:1:\"a\";i:62;s:1:\"b\";s:10:\"View:Leave\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:9:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:6;i:5;i:7;i:6;i:8;i:7;i:9;i:8;i:11;}}i:62;a:4:{s:1:\"a\";i:63;s:1:\"b\";s:12:\"Create:Leave\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:9:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:6;i:5;i:7;i:6;i:8;i:7;i:9;i:8;i:11;}}i:63;a:4:{s:1:\"a\";i:64;s:1:\"b\";s:12:\"Update:Leave\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;}}i:64;a:4:{s:1:\"a\";i:65;s:1:\"b\";s:12:\"Delete:Leave\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:65;a:4:{s:1:\"a\";i:66;s:1:\"b\";s:15:\"DeleteAny:Leave\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:66;a:4:{s:1:\"a\";i:67;s:1:\"b\";s:13:\"Restore:Leave\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:67;a:4:{s:1:\"a\";i:68;s:1:\"b\";s:17:\"ForceDelete:Leave\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:68;a:4:{s:1:\"a\";i:69;s:1:\"b\";s:20:\"ForceDeleteAny:Leave\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:69;a:4:{s:1:\"a\";i:70;s:1:\"b\";s:16:\"RestoreAny:Leave\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:70;a:4:{s:1:\"a\";i:71;s:1:\"b\";s:15:\"Replicate:Leave\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:71;a:4:{s:1:\"a\";i:72;s:1:\"b\";s:13:\"Reorder:Leave\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:72;a:4:{s:1:\"a\";i:73;s:1:\"b\";s:17:\"ViewAny:LeaveType\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:73;a:4:{s:1:\"a\";i:74;s:1:\"b\";s:14:\"View:LeaveType\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:74;a:4:{s:1:\"a\";i:75;s:1:\"b\";s:16:\"Create:LeaveType\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:75;a:4:{s:1:\"a\";i:76;s:1:\"b\";s:16:\"Update:LeaveType\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:76;a:4:{s:1:\"a\";i:77;s:1:\"b\";s:16:\"Delete:LeaveType\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:77;a:4:{s:1:\"a\";i:78;s:1:\"b\";s:19:\"DeleteAny:LeaveType\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:78;a:4:{s:1:\"a\";i:79;s:1:\"b\";s:17:\"Restore:LeaveType\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:79;a:4:{s:1:\"a\";i:80;s:1:\"b\";s:21:\"ForceDelete:LeaveType\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:80;a:4:{s:1:\"a\";i:81;s:1:\"b\";s:24:\"ForceDeleteAny:LeaveType\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:81;a:4:{s:1:\"a\";i:82;s:1:\"b\";s:20:\"RestoreAny:LeaveType\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:82;a:4:{s:1:\"a\";i:83;s:1:\"b\";s:19:\"Replicate:LeaveType\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:83;a:4:{s:1:\"a\";i:84;s:1:\"b\";s:17:\"Reorder:LeaveType\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:84;a:4:{s:1:\"a\";i:85;s:1:\"b\";s:22:\"ViewAny:NatureDocument\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:85;a:4:{s:1:\"a\";i:86;s:1:\"b\";s:19:\"View:NatureDocument\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:86;a:4:{s:1:\"a\";i:87;s:1:\"b\";s:21:\"Create:NatureDocument\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:87;a:4:{s:1:\"a\";i:88;s:1:\"b\";s:21:\"Update:NatureDocument\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:88;a:4:{s:1:\"a\";i:89;s:1:\"b\";s:21:\"Delete:NatureDocument\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:89;a:4:{s:1:\"a\";i:90;s:1:\"b\";s:24:\"DeleteAny:NatureDocument\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:90;a:4:{s:1:\"a\";i:91;s:1:\"b\";s:22:\"Restore:NatureDocument\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:91;a:4:{s:1:\"a\";i:92;s:1:\"b\";s:26:\"ForceDelete:NatureDocument\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:92;a:4:{s:1:\"a\";i:93;s:1:\"b\";s:29:\"ForceDeleteAny:NatureDocument\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:93;a:4:{s:1:\"a\";i:94;s:1:\"b\";s:25:\"RestoreAny:NatureDocument\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:94;a:4:{s:1:\"a\";i:95;s:1:\"b\";s:24:\"Replicate:NatureDocument\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:95;a:4:{s:1:\"a\";i:96;s:1:\"b\";s:22:\"Reorder:NatureDocument\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:96;a:4:{s:1:\"a\";i:97;s:1:\"b\";s:22:\"ViewAny:NiveauScolaire\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:97;a:4:{s:1:\"a\";i:98;s:1:\"b\";s:19:\"View:NiveauScolaire\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:98;a:4:{s:1:\"a\";i:99;s:1:\"b\";s:21:\"Create:NiveauScolaire\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:99;a:4:{s:1:\"a\";i:100;s:1:\"b\";s:21:\"Update:NiveauScolaire\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:100;a:4:{s:1:\"a\";i:101;s:1:\"b\";s:21:\"Delete:NiveauScolaire\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:101;a:4:{s:1:\"a\";i:102;s:1:\"b\";s:24:\"DeleteAny:NiveauScolaire\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:102;a:4:{s:1:\"a\";i:103;s:1:\"b\";s:22:\"Restore:NiveauScolaire\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:103;a:4:{s:1:\"a\";i:104;s:1:\"b\";s:26:\"ForceDelete:NiveauScolaire\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:104;a:4:{s:1:\"a\";i:105;s:1:\"b\";s:29:\"ForceDeleteAny:NiveauScolaire\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:105;a:4:{s:1:\"a\";i:106;s:1:\"b\";s:25:\"RestoreAny:NiveauScolaire\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:106;a:4:{s:1:\"a\";i:107;s:1:\"b\";s:24:\"Replicate:NiveauScolaire\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:107;a:4:{s:1:\"a\";i:108;s:1:\"b\";s:22:\"Reorder:NiveauScolaire\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:108;a:4:{s:1:\"a\";i:109;s:1:\"b\";s:18:\"ViewAny:Profession\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:109;a:4:{s:1:\"a\";i:110;s:1:\"b\";s:15:\"View:Profession\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:110;a:4:{s:1:\"a\";i:111;s:1:\"b\";s:17:\"Create:Profession\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:111;a:4:{s:1:\"a\";i:112;s:1:\"b\";s:17:\"Update:Profession\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:112;a:4:{s:1:\"a\";i:113;s:1:\"b\";s:17:\"Delete:Profession\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:113;a:4:{s:1:\"a\";i:114;s:1:\"b\";s:20:\"DeleteAny:Profession\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:114;a:4:{s:1:\"a\";i:115;s:1:\"b\";s:18:\"Restore:Profession\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:115;a:4:{s:1:\"a\";i:116;s:1:\"b\";s:22:\"ForceDelete:Profession\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:116;a:4:{s:1:\"a\";i:117;s:1:\"b\";s:25:\"ForceDeleteAny:Profession\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:117;a:4:{s:1:\"a\";i:118;s:1:\"b\";s:21:\"RestoreAny:Profession\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:118;a:4:{s:1:\"a\";i:119;s:1:\"b\";s:20:\"Replicate:Profession\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:119;a:4:{s:1:\"a\";i:120;s:1:\"b\";s:18:\"Reorder:Profession\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:120;a:4:{s:1:\"a\";i:121;s:1:\"b\";s:17:\"ViewAny:Transport\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:121;a:4:{s:1:\"a\";i:122;s:1:\"b\";s:14:\"View:Transport\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:122;a:4:{s:1:\"a\";i:123;s:1:\"b\";s:16:\"Create:Transport\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:123;a:4:{s:1:\"a\";i:124;s:1:\"b\";s:16:\"Update:Transport\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:124;a:4:{s:1:\"a\";i:125;s:1:\"b\";s:16:\"Delete:Transport\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:125;a:4:{s:1:\"a\";i:126;s:1:\"b\";s:19:\"DeleteAny:Transport\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:126;a:4:{s:1:\"a\";i:127;s:1:\"b\";s:17:\"Restore:Transport\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:127;a:4:{s:1:\"a\";i:128;s:1:\"b\";s:21:\"ForceDelete:Transport\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:128;a:4:{s:1:\"a\";i:129;s:1:\"b\";s:24:\"ForceDeleteAny:Transport\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:129;a:4:{s:1:\"a\";i:130;s:1:\"b\";s:20:\"RestoreAny:Transport\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:130;a:4:{s:1:\"a\";i:131;s:1:\"b\";s:19:\"Replicate:Transport\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:131;a:4:{s:1:\"a\";i:132;s:1:\"b\";s:17:\"Reorder:Transport\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:132;a:4:{s:1:\"a\";i:133;s:1:\"b\";s:12:\"ViewAny:User\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;}}i:133;a:4:{s:1:\"a\";i:134;s:1:\"b\";s:9:\"View:User\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;}}i:134;a:4:{s:1:\"a\";i:135;s:1:\"b\";s:11:\"Create:User\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;}}i:135;a:4:{s:1:\"a\";i:136;s:1:\"b\";s:11:\"Update:User\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;}}i:136;a:4:{s:1:\"a\";i:137;s:1:\"b\";s:11:\"Delete:User\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:137;a:4:{s:1:\"a\";i:138;s:1:\"b\";s:14:\"DeleteAny:User\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:138;a:4:{s:1:\"a\";i:139;s:1:\"b\";s:12:\"Restore:User\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:139;a:4:{s:1:\"a\";i:140;s:1:\"b\";s:16:\"ForceDelete:User\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:140;a:4:{s:1:\"a\";i:141;s:1:\"b\";s:19:\"ForceDeleteAny:User\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:141;a:4:{s:1:\"a\";i:142;s:1:\"b\";s:15:\"RestoreAny:User\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:142;a:4:{s:1:\"a\";i:143;s:1:\"b\";s:14:\"Replicate:User\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:143;a:4:{s:1:\"a\";i:144;s:1:\"b\";s:12:\"Reorder:User\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:144;a:4:{s:1:\"a\";i:145;s:1:\"b\";s:12:\"ViewAny:Role\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:145;a:4:{s:1:\"a\";i:146;s:1:\"b\";s:9:\"View:Role\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:146;a:4:{s:1:\"a\";i:147;s:1:\"b\";s:11:\"Create:Role\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:147;a:4:{s:1:\"a\";i:148;s:1:\"b\";s:11:\"Update:Role\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:148;a:4:{s:1:\"a\";i:149;s:1:\"b\";s:11:\"Delete:Role\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:149;a:4:{s:1:\"a\";i:150;s:1:\"b\";s:14:\"DeleteAny:Role\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:150;a:4:{s:1:\"a\";i:151;s:1:\"b\";s:12:\"Restore:Role\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:151;a:4:{s:1:\"a\";i:152;s:1:\"b\";s:16:\"ForceDelete:Role\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:152;a:4:{s:1:\"a\";i:153;s:1:\"b\";s:19:\"ForceDeleteAny:Role\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:153;a:4:{s:1:\"a\";i:154;s:1:\"b\";s:15:\"RestoreAny:Role\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:154;a:4:{s:1:\"a\";i:155;s:1:\"b\";s:14:\"Replicate:Role\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:155;a:4:{s:1:\"a\";i:156;s:1:\"b\";s:12:\"Reorder:Role\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:156;a:4:{s:1:\"a\";i:157;s:1:\"b\";s:14:\"View:Dashboard\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:9:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:6;i:5;i:7;i:6;i:8;i:7;i:9;i:8;i:11;}}i:157;a:4:{s:1:\"a\";i:158;s:1:\"b\";s:18:\"View:EcoleSettings\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:158;a:4:{s:1:\"a\";i:159;s:1:\"b\";s:18:\"View:AccountWidget\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:159;a:3:{s:1:\"a\";i:160;s:1:\"b\";s:24:\"View:EmployeeStatsWidget\";s:1:\"c\";s:3:\"web\";}i:160;a:4:{s:1:\"a\";i:161;s:1:\"b\";s:20:\"View:HrStatsOverview\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:161;a:4:{s:1:\"a\";i:162;s:1:\"b\";s:17:\"View:LeavesWidget\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:162;a:4:{s:1:\"a\";i:163;s:1:\"b\";s:12:\"ApproveLeave\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;}}i:163;a:4:{s:1:\"a\";i:164;s:1:\"b\";s:15:\"ValidatePayroll\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:164;a:4:{s:1:\"a\";i:165;s:1:\"b\";s:15:\"MarkPayrollPaid\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:165;a:4:{s:1:\"a\";i:166;s:1:\"b\";s:16:\"ViewAny:AuditLog\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:166;a:4:{s:1:\"a\";i:167;s:1:\"b\";s:13:\"View:AuditLog\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:167;a:4:{s:1:\"a\";i:168;s:1:\"b\";s:14:\"View:MonEspace\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:8:{i:0;i:2;i:1;i:3;i:2;i:4;i:3;i:6;i:4;i:7;i:5;i:8;i:6;i:9;i:7;i:11;}}i:168;a:4:{s:1:\"a\";i:169;s:1:\"b\";s:34:\"View:DocumentsAdministratifsWidget\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:169;a:4:{s:1:\"a\";i:170;s:1:\"b\";s:24:\"View:AutreDemandesWidget\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:170;a:4:{s:1:\"a\";i:171;s:1:\"b\";s:20:\"ViewAny:DocumentType\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:171;a:4:{s:1:\"a\";i:172;s:1:\"b\";s:17:\"View:DocumentType\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:172;a:4:{s:1:\"a\";i:173;s:1:\"b\";s:19:\"Create:DocumentType\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:173;a:4:{s:1:\"a\";i:174;s:1:\"b\";s:19:\"Update:DocumentType\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:174;a:4:{s:1:\"a\";i:175;s:1:\"b\";s:19:\"Delete:DocumentType\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:175;a:4:{s:1:\"a\";i:176;s:1:\"b\";s:22:\"DeleteAny:DocumentType\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:176;a:4:{s:1:\"a\";i:177;s:1:\"b\";s:20:\"Restore:DocumentType\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:177;a:4:{s:1:\"a\";i:178;s:1:\"b\";s:24:\"ForceDelete:DocumentType\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:178;a:4:{s:1:\"a\";i:179;s:1:\"b\";s:27:\"ForceDeleteAny:DocumentType\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:179;a:4:{s:1:\"a\";i:180;s:1:\"b\";s:23:\"RestoreAny:DocumentType\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:180;a:4:{s:1:\"a\";i:181;s:1:\"b\";s:22:\"Replicate:DocumentType\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:181;a:4:{s:1:\"a\";i:182;s:1:\"b\";s:20:\"Reorder:DocumentType\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:182;a:4:{s:1:\"a\";i:183;s:1:\"b\";s:28:\"ViewAny:EmployeeDocumentType\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:183;a:4:{s:1:\"a\";i:184;s:1:\"b\";s:25:\"View:EmployeeDocumentType\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:184;a:4:{s:1:\"a\";i:185;s:1:\"b\";s:27:\"Create:EmployeeDocumentType\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:185;a:4:{s:1:\"a\";i:186;s:1:\"b\";s:27:\"Update:EmployeeDocumentType\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:186;a:4:{s:1:\"a\";i:187;s:1:\"b\";s:27:\"Delete:EmployeeDocumentType\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:187;a:4:{s:1:\"a\";i:188;s:1:\"b\";s:30:\"DeleteAny:EmployeeDocumentType\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:188;a:4:{s:1:\"a\";i:189;s:1:\"b\";s:28:\"Restore:EmployeeDocumentType\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:189;a:4:{s:1:\"a\";i:190;s:1:\"b\";s:32:\"ForceDelete:EmployeeDocumentType\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:190;a:4:{s:1:\"a\";i:191;s:1:\"b\";s:35:\"ForceDeleteAny:EmployeeDocumentType\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:191;a:4:{s:1:\"a\";i:192;s:1:\"b\";s:31:\"RestoreAny:EmployeeDocumentType\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:192;a:4:{s:1:\"a\";i:193;s:1:\"b\";s:30:\"Replicate:EmployeeDocumentType\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:193;a:4:{s:1:\"a\";i:194;s:1:\"b\";s:28:\"Reorder:EmployeeDocumentType\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}}s:5:\"roles\";a:9:{i:0;a:3:{s:1:\"a\";i:1;s:1:\"b\";s:11:\"super-admin\";s:1:\"c\";s:3:\"web\";}i:1;a:3:{s:1:\"a\";i:2;s:1:\"b\";s:9:\"directeur\";s:1:\"c\";s:3:\"web\";}i:2;a:3:{s:1:\"a\";i:3;s:1:\"b\";s:10:\"secretaire\";s:1:\"c\";s:3:\"web\";}i:3;a:3:{s:1:\"a\";i:4;s:1:\"b\";s:12:\"surveillante\";s:1:\"c\";s:3:\"web\";}i:4;a:3:{s:1:\"a\";i:6;s:1:\"b\";s:15:\"femme-de-menage\";s:1:\"c\";s:3:\"web\";}i:5;a:3:{s:1:\"a\";i:7;s:1:\"b\";s:9:\"chauffeur\";s:1:\"c\";s:3:\"web\";}i:6;a:3:{s:1:\"a\";i:8;s:1:\"b\";s:7:\"gardien\";s:1:\"c\";s:3:\"web\";}i:7;a:3:{s:1:\"a\";i:9;s:1:\"b\";s:10:\"enseignant\";s:1:\"c\";s:3:\"web\";}i:8;a:3:{s:1:\"a\";i:11;s:1:\"b\";s:20:\"assistante-transport\";s:1:\"c\";s:3:\"web\";}}}', 1789429628),
('rh-les-ecoles-albaraime-cache-livewire-rate-limiter:a38a3d2f067062e481720e843f6719e3343b84b8', 'i:2;', 1789409229),
('rh-les-ecoles-albaraime-cache-livewire-rate-limiter:a38a3d2f067062e481720e843f6719e3343b84b8:timer', 'i:1789409229;', 1789409229),
('rh-les-ecoles-albaraime-cache-spatie.permission.cache', 'a:3:{s:5:\"alias\";a:4:{s:1:\"a\";s:2:\"id\";s:1:\"b\";s:4:\"name\";s:1:\"c\";s:10:\"guard_name\";s:1:\"r\";s:5:\"roles\";}s:11:\"permissions\";a:194:{i:0;a:4:{s:1:\"a\";i:1;s:1:\"b\";s:16:\"ViewAny:Activity\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:1;a:4:{s:1:\"a\";i:2;s:1:\"b\";s:13:\"View:Activity\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:2;a:4:{s:1:\"a\";i:3;s:1:\"b\";s:15:\"Create:Activity\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:3;a:4:{s:1:\"a\";i:4;s:1:\"b\";s:15:\"Update:Activity\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:4;a:4:{s:1:\"a\";i:5;s:1:\"b\";s:15:\"Delete:Activity\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:5;a:4:{s:1:\"a\";i:6;s:1:\"b\";s:18:\"DeleteAny:Activity\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:6;a:4:{s:1:\"a\";i:7;s:1:\"b\";s:16:\"Restore:Activity\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:7;a:4:{s:1:\"a\";i:8;s:1:\"b\";s:20:\"ForceDelete:Activity\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:8;a:4:{s:1:\"a\";i:9;s:1:\"b\";s:23:\"ForceDeleteAny:Activity\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:9;a:4:{s:1:\"a\";i:10;s:1:\"b\";s:19:\"RestoreAny:Activity\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:10;a:4:{s:1:\"a\";i:11;s:1:\"b\";s:18:\"Replicate:Activity\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:11;a:4:{s:1:\"a\";i:12;s:1:\"b\";s:16:\"Reorder:Activity\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:12;a:4:{s:1:\"a\";i:13;s:1:\"b\";s:23:\"ViewAny:DocumentRequest\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:9:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:6;i:5;i:7;i:6;i:8;i:7;i:9;i:8;i:11;}}i:13;a:4:{s:1:\"a\";i:14;s:1:\"b\";s:20:\"View:DocumentRequest\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:9:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:6;i:5;i:7;i:6;i:8;i:7;i:9;i:8;i:11;}}i:14;a:4:{s:1:\"a\";i:15;s:1:\"b\";s:22:\"Create:DocumentRequest\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:9:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:6;i:5;i:7;i:6;i:8;i:7;i:9;i:8;i:11;}}i:15;a:4:{s:1:\"a\";i:16;s:1:\"b\";s:22:\"Update:DocumentRequest\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;}}i:16;a:4:{s:1:\"a\";i:17;s:1:\"b\";s:22:\"Delete:DocumentRequest\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:17;a:4:{s:1:\"a\";i:18;s:1:\"b\";s:25:\"DeleteAny:DocumentRequest\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:18;a:4:{s:1:\"a\";i:19;s:1:\"b\";s:23:\"Restore:DocumentRequest\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:19;a:4:{s:1:\"a\";i:20;s:1:\"b\";s:27:\"ForceDelete:DocumentRequest\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:20;a:4:{s:1:\"a\";i:21;s:1:\"b\";s:30:\"ForceDeleteAny:DocumentRequest\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:21;a:4:{s:1:\"a\";i:22;s:1:\"b\";s:26:\"RestoreAny:DocumentRequest\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:22;a:4:{s:1:\"a\";i:23;s:1:\"b\";s:25:\"Replicate:DocumentRequest\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:23;a:4:{s:1:\"a\";i:24;s:1:\"b\";s:23:\"Reorder:DocumentRequest\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:24;a:4:{s:1:\"a\";i:25;s:1:\"b\";s:27:\"ViewAny:CommunicationMethod\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:25;a:4:{s:1:\"a\";i:26;s:1:\"b\";s:24:\"View:CommunicationMethod\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:26;a:4:{s:1:\"a\";i:27;s:1:\"b\";s:26:\"Create:CommunicationMethod\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:27;a:4:{s:1:\"a\";i:28;s:1:\"b\";s:26:\"Update:CommunicationMethod\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:28;a:4:{s:1:\"a\";i:29;s:1:\"b\";s:26:\"Delete:CommunicationMethod\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:29;a:4:{s:1:\"a\";i:30;s:1:\"b\";s:29:\"DeleteAny:CommunicationMethod\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:30;a:4:{s:1:\"a\";i:31;s:1:\"b\";s:27:\"Restore:CommunicationMethod\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:31;a:4:{s:1:\"a\";i:32;s:1:\"b\";s:31:\"ForceDelete:CommunicationMethod\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:32;a:4:{s:1:\"a\";i:33;s:1:\"b\";s:34:\"ForceDeleteAny:CommunicationMethod\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:33;a:4:{s:1:\"a\";i:34;s:1:\"b\";s:30:\"RestoreAny:CommunicationMethod\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:34;a:4:{s:1:\"a\";i:35;s:1:\"b\";s:29:\"Replicate:CommunicationMethod\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:35;a:4:{s:1:\"a\";i:36;s:1:\"b\";s:27:\"Reorder:CommunicationMethod\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:36;a:4:{s:1:\"a\";i:37;s:1:\"b\";s:16:\"ViewAny:Employee\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;}}i:37;a:4:{s:1:\"a\";i:38;s:1:\"b\";s:13:\"View:Employee\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:9:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:6;i:5;i:7;i:6;i:8;i:7;i:9;i:8;i:11;}}i:38;a:4:{s:1:\"a\";i:39;s:1:\"b\";s:15:\"Create:Employee\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;}}i:39;a:4:{s:1:\"a\";i:40;s:1:\"b\";s:15:\"Update:Employee\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:9:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:6;i:5;i:7;i:6;i:8;i:7;i:9;i:8;i:11;}}i:40;a:4:{s:1:\"a\";i:41;s:1:\"b\";s:15:\"Delete:Employee\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:41;a:4:{s:1:\"a\";i:42;s:1:\"b\";s:18:\"DeleteAny:Employee\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:42;a:4:{s:1:\"a\";i:43;s:1:\"b\";s:16:\"Restore:Employee\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:43;a:4:{s:1:\"a\";i:44;s:1:\"b\";s:20:\"ForceDelete:Employee\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:44;a:4:{s:1:\"a\";i:45;s:1:\"b\";s:23:\"ForceDeleteAny:Employee\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:45;a:4:{s:1:\"a\";i:46;s:1:\"b\";s:19:\"RestoreAny:Employee\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:46;a:4:{s:1:\"a\";i:47;s:1:\"b\";s:18:\"Replicate:Employee\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:47;a:4:{s:1:\"a\";i:48;s:1:\"b\";s:16:\"Reorder:Employee\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:48;a:4:{s:1:\"a\";i:49;s:1:\"b\";s:14:\"ViewAny:Groupe\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:49;a:4:{s:1:\"a\";i:50;s:1:\"b\";s:11:\"View:Groupe\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:50;a:4:{s:1:\"a\";i:51;s:1:\"b\";s:13:\"Create:Groupe\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:51;a:4:{s:1:\"a\";i:52;s:1:\"b\";s:13:\"Update:Groupe\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:52;a:4:{s:1:\"a\";i:53;s:1:\"b\";s:13:\"Delete:Groupe\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:53;a:4:{s:1:\"a\";i:54;s:1:\"b\";s:16:\"DeleteAny:Groupe\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:54;a:4:{s:1:\"a\";i:55;s:1:\"b\";s:14:\"Restore:Groupe\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:55;a:4:{s:1:\"a\";i:56;s:1:\"b\";s:18:\"ForceDelete:Groupe\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:56;a:4:{s:1:\"a\";i:57;s:1:\"b\";s:21:\"ForceDeleteAny:Groupe\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:57;a:4:{s:1:\"a\";i:58;s:1:\"b\";s:17:\"RestoreAny:Groupe\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:58;a:4:{s:1:\"a\";i:59;s:1:\"b\";s:16:\"Replicate:Groupe\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:59;a:4:{s:1:\"a\";i:60;s:1:\"b\";s:14:\"Reorder:Groupe\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:60;a:4:{s:1:\"a\";i:61;s:1:\"b\";s:13:\"ViewAny:Leave\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:9:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:6;i:5;i:7;i:6;i:8;i:7;i:9;i:8;i:11;}}i:61;a:4:{s:1:\"a\";i:62;s:1:\"b\";s:10:\"View:Leave\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:9:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:6;i:5;i:7;i:6;i:8;i:7;i:9;i:8;i:11;}}i:62;a:4:{s:1:\"a\";i:63;s:1:\"b\";s:12:\"Create:Leave\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:9:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:6;i:5;i:7;i:6;i:8;i:7;i:9;i:8;i:11;}}i:63;a:4:{s:1:\"a\";i:64;s:1:\"b\";s:12:\"Update:Leave\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;}}i:64;a:4:{s:1:\"a\";i:65;s:1:\"b\";s:12:\"Delete:Leave\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:65;a:4:{s:1:\"a\";i:66;s:1:\"b\";s:15:\"DeleteAny:Leave\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:66;a:4:{s:1:\"a\";i:67;s:1:\"b\";s:13:\"Restore:Leave\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:67;a:4:{s:1:\"a\";i:68;s:1:\"b\";s:17:\"ForceDelete:Leave\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:68;a:4:{s:1:\"a\";i:69;s:1:\"b\";s:20:\"ForceDeleteAny:Leave\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:69;a:4:{s:1:\"a\";i:70;s:1:\"b\";s:16:\"RestoreAny:Leave\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:70;a:4:{s:1:\"a\";i:71;s:1:\"b\";s:15:\"Replicate:Leave\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:71;a:4:{s:1:\"a\";i:72;s:1:\"b\";s:13:\"Reorder:Leave\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:72;a:4:{s:1:\"a\";i:73;s:1:\"b\";s:17:\"ViewAny:LeaveType\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:73;a:4:{s:1:\"a\";i:74;s:1:\"b\";s:14:\"View:LeaveType\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:74;a:4:{s:1:\"a\";i:75;s:1:\"b\";s:16:\"Create:LeaveType\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:75;a:4:{s:1:\"a\";i:76;s:1:\"b\";s:16:\"Update:LeaveType\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:76;a:4:{s:1:\"a\";i:77;s:1:\"b\";s:16:\"Delete:LeaveType\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:77;a:4:{s:1:\"a\";i:78;s:1:\"b\";s:19:\"DeleteAny:LeaveType\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:78;a:4:{s:1:\"a\";i:79;s:1:\"b\";s:17:\"Restore:LeaveType\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:79;a:4:{s:1:\"a\";i:80;s:1:\"b\";s:21:\"ForceDelete:LeaveType\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:80;a:4:{s:1:\"a\";i:81;s:1:\"b\";s:24:\"ForceDeleteAny:LeaveType\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:81;a:4:{s:1:\"a\";i:82;s:1:\"b\";s:20:\"RestoreAny:LeaveType\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:82;a:4:{s:1:\"a\";i:83;s:1:\"b\";s:19:\"Replicate:LeaveType\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:83;a:4:{s:1:\"a\";i:84;s:1:\"b\";s:17:\"Reorder:LeaveType\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:84;a:4:{s:1:\"a\";i:85;s:1:\"b\";s:22:\"ViewAny:NatureDocument\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:85;a:4:{s:1:\"a\";i:86;s:1:\"b\";s:19:\"View:NatureDocument\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:86;a:4:{s:1:\"a\";i:87;s:1:\"b\";s:21:\"Create:NatureDocument\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:87;a:4:{s:1:\"a\";i:88;s:1:\"b\";s:21:\"Update:NatureDocument\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:88;a:4:{s:1:\"a\";i:89;s:1:\"b\";s:21:\"Delete:NatureDocument\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:89;a:4:{s:1:\"a\";i:90;s:1:\"b\";s:24:\"DeleteAny:NatureDocument\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:90;a:4:{s:1:\"a\";i:91;s:1:\"b\";s:22:\"Restore:NatureDocument\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:91;a:4:{s:1:\"a\";i:92;s:1:\"b\";s:26:\"ForceDelete:NatureDocument\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:92;a:4:{s:1:\"a\";i:93;s:1:\"b\";s:29:\"ForceDeleteAny:NatureDocument\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:93;a:4:{s:1:\"a\";i:94;s:1:\"b\";s:25:\"RestoreAny:NatureDocument\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:94;a:4:{s:1:\"a\";i:95;s:1:\"b\";s:24:\"Replicate:NatureDocument\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:95;a:4:{s:1:\"a\";i:96;s:1:\"b\";s:22:\"Reorder:NatureDocument\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:96;a:4:{s:1:\"a\";i:97;s:1:\"b\";s:22:\"ViewAny:NiveauScolaire\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:97;a:4:{s:1:\"a\";i:98;s:1:\"b\";s:19:\"View:NiveauScolaire\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:98;a:4:{s:1:\"a\";i:99;s:1:\"b\";s:21:\"Create:NiveauScolaire\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:99;a:4:{s:1:\"a\";i:100;s:1:\"b\";s:21:\"Update:NiveauScolaire\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:100;a:4:{s:1:\"a\";i:101;s:1:\"b\";s:21:\"Delete:NiveauScolaire\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:101;a:4:{s:1:\"a\";i:102;s:1:\"b\";s:24:\"DeleteAny:NiveauScolaire\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:102;a:4:{s:1:\"a\";i:103;s:1:\"b\";s:22:\"Restore:NiveauScolaire\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:103;a:4:{s:1:\"a\";i:104;s:1:\"b\";s:26:\"ForceDelete:NiveauScolaire\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:104;a:4:{s:1:\"a\";i:105;s:1:\"b\";s:29:\"ForceDeleteAny:NiveauScolaire\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:105;a:4:{s:1:\"a\";i:106;s:1:\"b\";s:25:\"RestoreAny:NiveauScolaire\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:106;a:4:{s:1:\"a\";i:107;s:1:\"b\";s:24:\"Replicate:NiveauScolaire\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:107;a:4:{s:1:\"a\";i:108;s:1:\"b\";s:22:\"Reorder:NiveauScolaire\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:108;a:4:{s:1:\"a\";i:109;s:1:\"b\";s:18:\"ViewAny:Profession\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:109;a:4:{s:1:\"a\";i:110;s:1:\"b\";s:15:\"View:Profession\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:110;a:4:{s:1:\"a\";i:111;s:1:\"b\";s:17:\"Create:Profession\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:111;a:4:{s:1:\"a\";i:112;s:1:\"b\";s:17:\"Update:Profession\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:112;a:4:{s:1:\"a\";i:113;s:1:\"b\";s:17:\"Delete:Profession\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:113;a:4:{s:1:\"a\";i:114;s:1:\"b\";s:20:\"DeleteAny:Profession\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:114;a:4:{s:1:\"a\";i:115;s:1:\"b\";s:18:\"Restore:Profession\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:115;a:4:{s:1:\"a\";i:116;s:1:\"b\";s:22:\"ForceDelete:Profession\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:116;a:4:{s:1:\"a\";i:117;s:1:\"b\";s:25:\"ForceDeleteAny:Profession\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:117;a:4:{s:1:\"a\";i:118;s:1:\"b\";s:21:\"RestoreAny:Profession\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:118;a:4:{s:1:\"a\";i:119;s:1:\"b\";s:20:\"Replicate:Profession\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:119;a:4:{s:1:\"a\";i:120;s:1:\"b\";s:18:\"Reorder:Profession\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:120;a:4:{s:1:\"a\";i:121;s:1:\"b\";s:17:\"ViewAny:Transport\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:121;a:4:{s:1:\"a\";i:122;s:1:\"b\";s:14:\"View:Transport\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:122;a:4:{s:1:\"a\";i:123;s:1:\"b\";s:16:\"Create:Transport\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:123;a:4:{s:1:\"a\";i:124;s:1:\"b\";s:16:\"Update:Transport\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:124;a:4:{s:1:\"a\";i:125;s:1:\"b\";s:16:\"Delete:Transport\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:125;a:4:{s:1:\"a\";i:126;s:1:\"b\";s:19:\"DeleteAny:Transport\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:126;a:4:{s:1:\"a\";i:127;s:1:\"b\";s:17:\"Restore:Transport\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:127;a:4:{s:1:\"a\";i:128;s:1:\"b\";s:21:\"ForceDelete:Transport\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:128;a:4:{s:1:\"a\";i:129;s:1:\"b\";s:24:\"ForceDeleteAny:Transport\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:129;a:4:{s:1:\"a\";i:130;s:1:\"b\";s:20:\"RestoreAny:Transport\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:130;a:4:{s:1:\"a\";i:131;s:1:\"b\";s:19:\"Replicate:Transport\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:131;a:4:{s:1:\"a\";i:132;s:1:\"b\";s:17:\"Reorder:Transport\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:132;a:4:{s:1:\"a\";i:133;s:1:\"b\";s:12:\"ViewAny:User\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;}}i:133;a:4:{s:1:\"a\";i:134;s:1:\"b\";s:9:\"View:User\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;}}i:134;a:4:{s:1:\"a\";i:135;s:1:\"b\";s:11:\"Create:User\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;}}i:135;a:4:{s:1:\"a\";i:136;s:1:\"b\";s:11:\"Update:User\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;}}i:136;a:4:{s:1:\"a\";i:137;s:1:\"b\";s:11:\"Delete:User\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:137;a:4:{s:1:\"a\";i:138;s:1:\"b\";s:14:\"DeleteAny:User\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:138;a:4:{s:1:\"a\";i:139;s:1:\"b\";s:12:\"Restore:User\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:139;a:4:{s:1:\"a\";i:140;s:1:\"b\";s:16:\"ForceDelete:User\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:140;a:4:{s:1:\"a\";i:141;s:1:\"b\";s:19:\"ForceDeleteAny:User\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:141;a:4:{s:1:\"a\";i:142;s:1:\"b\";s:15:\"RestoreAny:User\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:142;a:4:{s:1:\"a\";i:143;s:1:\"b\";s:14:\"Replicate:User\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:143;a:4:{s:1:\"a\";i:144;s:1:\"b\";s:12:\"Reorder:User\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:144;a:4:{s:1:\"a\";i:145;s:1:\"b\";s:12:\"ViewAny:Role\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:145;a:4:{s:1:\"a\";i:146;s:1:\"b\";s:9:\"View:Role\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:146;a:4:{s:1:\"a\";i:147;s:1:\"b\";s:11:\"Create:Role\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:147;a:4:{s:1:\"a\";i:148;s:1:\"b\";s:11:\"Update:Role\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:148;a:4:{s:1:\"a\";i:149;s:1:\"b\";s:11:\"Delete:Role\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:149;a:4:{s:1:\"a\";i:150;s:1:\"b\";s:14:\"DeleteAny:Role\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:150;a:4:{s:1:\"a\";i:151;s:1:\"b\";s:12:\"Restore:Role\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:151;a:4:{s:1:\"a\";i:152;s:1:\"b\";s:16:\"ForceDelete:Role\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:152;a:4:{s:1:\"a\";i:153;s:1:\"b\";s:19:\"ForceDeleteAny:Role\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:153;a:4:{s:1:\"a\";i:154;s:1:\"b\";s:15:\"RestoreAny:Role\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:154;a:4:{s:1:\"a\";i:155;s:1:\"b\";s:14:\"Replicate:Role\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:155;a:4:{s:1:\"a\";i:156;s:1:\"b\";s:12:\"Reorder:Role\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:156;a:4:{s:1:\"a\";i:157;s:1:\"b\";s:14:\"View:Dashboard\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:9:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:6;i:5;i:7;i:6;i:8;i:7;i:9;i:8;i:11;}}i:157;a:4:{s:1:\"a\";i:158;s:1:\"b\";s:18:\"View:EcoleSettings\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:158;a:4:{s:1:\"a\";i:159;s:1:\"b\";s:18:\"View:AccountWidget\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:159;a:3:{s:1:\"a\";i:160;s:1:\"b\";s:24:\"View:EmployeeStatsWidget\";s:1:\"c\";s:3:\"web\";}i:160;a:4:{s:1:\"a\";i:161;s:1:\"b\";s:20:\"View:HrStatsOverview\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:161;a:4:{s:1:\"a\";i:162;s:1:\"b\";s:17:\"View:LeavesWidget\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:162;a:4:{s:1:\"a\";i:163;s:1:\"b\";s:12:\"ApproveLeave\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;}}i:163;a:4:{s:1:\"a\";i:164;s:1:\"b\";s:15:\"ValidatePayroll\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:164;a:4:{s:1:\"a\";i:165;s:1:\"b\";s:15:\"MarkPayrollPaid\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:165;a:4:{s:1:\"a\";i:166;s:1:\"b\";s:16:\"ViewAny:AuditLog\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:166;a:4:{s:1:\"a\";i:167;s:1:\"b\";s:13:\"View:AuditLog\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:167;a:4:{s:1:\"a\";i:168;s:1:\"b\";s:14:\"View:MonEspace\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:8:{i:0;i:2;i:1;i:3;i:2;i:4;i:3;i:6;i:4;i:7;i:5;i:8;i:6;i:9;i:7;i:11;}}i:168;a:4:{s:1:\"a\";i:169;s:1:\"b\";s:34:\"View:DocumentsAdministratifsWidget\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:169;a:4:{s:1:\"a\";i:170;s:1:\"b\";s:24:\"View:AutreDemandesWidget\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:170;a:4:{s:1:\"a\";i:171;s:1:\"b\";s:20:\"ViewAny:DocumentType\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:171;a:4:{s:1:\"a\";i:172;s:1:\"b\";s:17:\"View:DocumentType\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:172;a:4:{s:1:\"a\";i:173;s:1:\"b\";s:19:\"Create:DocumentType\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:173;a:4:{s:1:\"a\";i:174;s:1:\"b\";s:19:\"Update:DocumentType\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:174;a:4:{s:1:\"a\";i:175;s:1:\"b\";s:19:\"Delete:DocumentType\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:175;a:4:{s:1:\"a\";i:176;s:1:\"b\";s:22:\"DeleteAny:DocumentType\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:176;a:4:{s:1:\"a\";i:177;s:1:\"b\";s:20:\"Restore:DocumentType\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:177;a:4:{s:1:\"a\";i:178;s:1:\"b\";s:24:\"ForceDelete:DocumentType\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:178;a:4:{s:1:\"a\";i:179;s:1:\"b\";s:27:\"ForceDeleteAny:DocumentType\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:179;a:4:{s:1:\"a\";i:180;s:1:\"b\";s:23:\"RestoreAny:DocumentType\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:180;a:4:{s:1:\"a\";i:181;s:1:\"b\";s:22:\"Replicate:DocumentType\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:181;a:4:{s:1:\"a\";i:182;s:1:\"b\";s:20:\"Reorder:DocumentType\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:182;a:4:{s:1:\"a\";i:183;s:1:\"b\";s:28:\"ViewAny:EmployeeDocumentType\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:183;a:4:{s:1:\"a\";i:184;s:1:\"b\";s:25:\"View:EmployeeDocumentType\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:184;a:4:{s:1:\"a\";i:185;s:1:\"b\";s:27:\"Create:EmployeeDocumentType\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:185;a:4:{s:1:\"a\";i:186;s:1:\"b\";s:27:\"Update:EmployeeDocumentType\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:186;a:4:{s:1:\"a\";i:187;s:1:\"b\";s:27:\"Delete:EmployeeDocumentType\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:187;a:4:{s:1:\"a\";i:188;s:1:\"b\";s:30:\"DeleteAny:EmployeeDocumentType\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:188;a:4:{s:1:\"a\";i:189;s:1:\"b\";s:28:\"Restore:EmployeeDocumentType\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:189;a:4:{s:1:\"a\";i:190;s:1:\"b\";s:32:\"ForceDelete:EmployeeDocumentType\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:190;a:4:{s:1:\"a\";i:191;s:1:\"b\";s:35:\"ForceDeleteAny:EmployeeDocumentType\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:191;a:4:{s:1:\"a\";i:192;s:1:\"b\";s:31:\"RestoreAny:EmployeeDocumentType\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:192;a:4:{s:1:\"a\";i:193;s:1:\"b\";s:30:\"Replicate:EmployeeDocumentType\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:193;a:4:{s:1:\"a\";i:194;s:1:\"b\";s:28:\"Reorder:EmployeeDocumentType\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}}s:5:\"roles\";a:9:{i:0;a:3:{s:1:\"a\";i:1;s:1:\"b\";s:11:\"super-admin\";s:1:\"c\";s:3:\"web\";}i:1;a:3:{s:1:\"a\";i:2;s:1:\"b\";s:9:\"directeur\";s:1:\"c\";s:3:\"web\";}i:2;a:3:{s:1:\"a\";i:3;s:1:\"b\";s:10:\"secretaire\";s:1:\"c\";s:3:\"web\";}i:3;a:3:{s:1:\"a\";i:4;s:1:\"b\";s:12:\"surveillante\";s:1:\"c\";s:3:\"web\";}i:4;a:3:{s:1:\"a\";i:6;s:1:\"b\";s:15:\"femme-de-menage\";s:1:\"c\";s:3:\"web\";}i:5;a:3:{s:1:\"a\";i:7;s:1:\"b\";s:9:\"chauffeur\";s:1:\"c\";s:3:\"web\";}i:6;a:3:{s:1:\"a\";i:8;s:1:\"b\";s:7:\"gardien\";s:1:\"c\";s:3:\"web\";}i:7;a:3:{s:1:\"a\";i:9;s:1:\"b\";s:10:\"enseignant\";s:1:\"c\";s:3:\"web\";}i:8;a:3:{s:1:\"a\";i:11;s:1:\"b\";s:20:\"assistante-transport\";s:1:\"c\";s:3:\"web\";}}}', 1789495575);

-- --------------------------------------------------------

--
-- Structure de la table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `communication_methods`
--

CREATE TABLE `communication_methods` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ecole_setting_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `communication_methods`
--

INSERT INTO `communication_methods` (`id`, `ecole_setting_id`, `name`, `code`, `active`, `sort_order`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'Email', 'email', 1, 1, '2026-09-10 22:38:22', '2026-09-10 22:38:22', NULL),
(2, 1, 'Téléphone', 'telephone', 1, 2, '2026-09-10 22:38:22', '2026-09-10 22:38:22', NULL),
(3, 1, 'WhatsApp', 'whatsapp', 1, 3, '2026-09-10 22:38:22', '2026-09-10 22:38:22', NULL),
(4, 1, 'SMS', 'sms', 1, 4, '2026-09-10 22:38:22', '2026-09-10 22:38:22', NULL),
(5, 1, 'Courrier', 'courrier', 1, 5, '2026-09-10 22:38:22', '2026-09-10 22:38:22', NULL),
(6, 1, 'Présentiel', 'presentiel', 1, 6, '2026-09-10 22:38:22', '2026-09-10 22:38:22', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `document_requests`
--

CREATE TABLE `document_requests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ecole_setting_id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `categorie` enum('document','autre') NOT NULL DEFAULT 'document',
  `type` enum('attestation_travail','attestation_salaire','bulletin_paie','attestation_ir','credit_irrevocable','attestation_cnss','ordre_mission','certificat_travail','materiel','grande_salle','photocopie','rencontre_parents','rencontre_direction','formation','activites','divers') DEFAULT NULL,
  `date_souhaitee` date DEFAULT NULL,
  `format` enum('digital','papier') NOT NULL DEFAULT 'digital',
  `reason` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `fichier_joint` varchar(255) DEFAULT NULL,
  `photocopie_sous_type` varchar(255) DEFAULT NULL,
  `photocopie_niveau` varchar(255) DEFAULT NULL,
  `photocopie_groupe` varchar(255) DEFAULT NULL,
  `photocopie_nb_copies` smallint(5) UNSIGNED DEFAULT NULL,
  `photocopie_date_souhaitee` date DEFAULT NULL,
  `rencontre_employee_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`rencontre_employee_ids`)),
  `status` enum('en_attente','approuvé','refusé') NOT NULL DEFAULT 'en_attente',
  `generated_file_path` varchar(255) DEFAULT NULL,
  `fichier_final` varchar(255) DEFAULT NULL,
  `nb_telechargements` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `processed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `processed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `document_types`
--

CREATE TABLE `document_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ecole_setting_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) DEFAULT NULL,
  `categorie` enum('document','autre') NOT NULL DEFAULT 'document',
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `document_types`
--

INSERT INTO `document_types` (`id`, `ecole_setting_id`, `name`, `code`, `categorie`, `active`, `sort_order`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, NULL, 'Attestation de travail', 'attestation_travail', 'document', 1, 1, '2026-09-10 22:38:22', '2026-09-10 22:38:22', NULL),
(2, NULL, 'Attestation de salaire', 'attestation_salaire', 'document', 1, 2, '2026-09-10 22:38:22', '2026-09-10 22:38:22', NULL),
(3, NULL, 'Bulletin de paie', 'bulletin_paie', 'document', 1, 3, '2026-09-10 22:38:22', '2026-09-10 22:38:22', NULL),
(4, NULL, 'Attestation IR', 'attestation_ir', 'document', 1, 4, '2026-09-10 22:38:22', '2026-09-10 22:38:22', NULL),
(5, NULL, 'Crédit irrévocable', 'credit_irrevocable', 'document', 1, 5, '2026-09-10 22:38:22', '2026-09-10 22:38:22', NULL),
(6, NULL, 'Attestation CNSS', 'attestation_cnss', 'document', 1, 6, '2026-09-10 22:38:22', '2026-09-10 22:38:22', NULL),
(9, NULL, 'Matériel', 'materiel', 'autre', 1, 1, '2026-09-10 22:38:22', '2026-09-10 22:38:22', NULL),
(10, NULL, 'Grande salle', 'grande_salle', 'autre', 1, 2, '2026-09-10 22:38:22', '2026-09-10 22:38:22', NULL),
(11, NULL, 'Photocopie', 'photocopie', 'autre', 1, 3, '2026-09-10 22:38:22', '2026-09-10 22:38:22', NULL),
(12, NULL, 'Rencontre parents', 'rencontre_parents', 'autre', 1, 4, '2026-09-10 22:38:22', '2026-09-10 22:38:22', NULL),
(13, NULL, 'Rencontre direction', 'rencontre_direction', 'autre', 1, 5, '2026-09-10 22:38:22', '2026-09-10 22:38:22', NULL),
(14, NULL, 'Formation', 'formation', 'autre', 1, 6, '2026-09-10 22:38:22', '2026-09-10 22:38:22', NULL),
(15, NULL, 'Activités', 'activites', 'autre', 1, 7, '2026-09-10 22:38:22', '2026-09-10 22:38:22', NULL),
(16, NULL, 'Divers', 'divers', 'autre', 1, 8, '2026-09-10 22:38:22', '2026-09-10 22:38:22', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `ecole_settings`
--

CREATE TABLE `ecole_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nom_ecole` varchar(255) NOT NULL DEFAULT 'Mon École',
  `code_massare` varchar(255) DEFAULT NULL,
  `cnss` varchar(255) DEFAULT NULL,
  `patente` varchar(255) DEFAULT NULL,
  `rc` varchar(255) DEFAULT NULL,
  `if_number` varchar(255) DEFAULT NULL,
  `adresse` text DEFAULT NULL,
  `ville` varchar(255) DEFAULT NULL,
  `code_postal` varchar(255) DEFAULT NULL,
  `pays` varchar(255) NOT NULL DEFAULT 'Maroc',
  `telephone` varchar(255) DEFAULT NULL,
  `fax` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `site_web` varchar(255) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `cachet` varchar(255) DEFAULT NULL,
  `afficher_logo_pdf` tinyint(1) NOT NULL DEFAULT 1,
  `afficher_cachet_pdf` tinyint(1) NOT NULL DEFAULT 1,
  `entete_document` text DEFAULT NULL,
  `pied_document` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `ecole_settings`
--

INSERT INTO `ecole_settings` (`id`, `nom_ecole`, `code_massare`, `cnss`, `patente`, `rc`, `if_number`, `adresse`, `ville`, `code_postal`, `pays`, `telephone`, `fax`, `email`, `site_web`, `logo`, `cachet`, `afficher_logo_pdf`, `afficher_cachet_pdf`, `entete_document`, `pied_document`, `created_at`, `updated_at`) VALUES
(1, 'Les écoles AL BARAIME', NULL, '2920222', '42500016', '16320', '64400070', 'Bvd Mohammed V, Bp 14, Azemmour, 24100 Maroc', 'Azemmour', '24100', 'Maroc', '+212523358346', NULL, 'albaraime.viescholaire@gmail.com', 'https://lesecolesalbaraime.com/', 'ecole/01M29J1HDNYV8Y3BNVJ5MS1HMC.png', NULL, 1, 1, NULL, NULL, '2026-09-10 22:38:22', '2026-09-11 23:59:53');

-- --------------------------------------------------------

--
-- Structure de la table `employees`
--

CREATE TABLE `employees` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` char(36) NOT NULL,
  `ecole_setting_id` bigint(20) UNSIGNED NOT NULL,
  `matricule` varchar(255) DEFAULT NULL,
  `cin` varchar(255) DEFAULT NULL,
  `cnss_number` varchar(255) DEFAULT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `gender` enum('M','F') DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `phone_fixed` varchar(255) DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `birth_place` varchar(255) DEFAULT NULL,
  `hire_date` date DEFAULT NULL,
  `diploma` varchar(255) DEFAULT NULL,
  `promotion` varchar(255) DEFAULT NULL,
  `exit_date` date DEFAULT NULL,
  `exit_reason` enum('demission','decision','sanction','autre') DEFAULT NULL,
  `exit_comment` text DEFAULT NULL,
  `contract_type` enum('CDI','CDD','Stage','ANAPEC') DEFAULT NULL,
  `marital_status` enum('celibataire','marie','divorce','veuf') DEFAULT NULL,
  `number_of_children` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `rib` varchar(255) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `nationality` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `status` enum('actif','inactif','sorti') NOT NULL DEFAULT 'actif',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `profession_id` bigint(20) UNSIGNED DEFAULT NULL,
  `profession_type` enum('permanent','stagiaire','vacataire') DEFAULT NULL,
  `transport_id` bigint(20) UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `employees`
--

INSERT INTO `employees` (`id`, `uuid`, `ecole_setting_id`, `matricule`, `cin`, `cnss_number`, `first_name`, `last_name`, `gender`, `email`, `phone`, `phone_fixed`, `birth_date`, `birth_place`, `hire_date`, `diploma`, `promotion`, `exit_date`, `exit_reason`, `exit_comment`, `contract_type`, `marital_status`, `number_of_children`, `rib`, `photo`, `address`, `nationality`, `city`, `status`, `created_at`, `updated_at`, `profession_id`, `profession_type`, `transport_id`, `deleted_at`) VALUES
(1, 'c863d9a9-c71b-4249-9c67-109c92a20931', 1, '21', 'MA4917', '131685855', 'SAADIA', 'AABIDA', 'F', 'saadia.aabida@lesecolesalbaraime.com', '0772041291', NULL, '1966-01-01', NULL, '2009-09-01', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 'HAY ESSALAME RUE 8 N  2ETAGE 2', 'Marocaine', NULL, 'actif', '2026-09-11 14:07:12', '2026-09-12 00:45:42', 3, NULL, NULL, NULL),
(2, '70b63c4c-a5dd-46b3-84fe-b825d459b44f', 1, '9', 'MA42841', '191733660', 'HASNAA', 'ABASSI', 'F', 'hasnaa.abassi@lesecolesalbaraime.com', '0644646246', NULL, '1979-06-25', NULL, '2006-09-01', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '11 COOPERATIVE  ETTADAMOUN', 'Marocaine', NULL, 'actif', '2026-09-11 14:07:12', '2026-09-11 14:07:12', 1, NULL, NULL, NULL),
(3, '0f5525cd-65f3-4840-a496-fee7fb9621bd', 1, '13', 'MA78380', '197214987', 'FOUZIA', 'ABAYAZID', 'F', 'fouzia.abayazid@lesecolesalbaraime.com', '0669155125', NULL, '1984-08-15', NULL, '2012-12-03', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '61 RUE  1 LOT WIFAK', 'Marocaine', NULL, 'actif', '2026-09-11 14:07:12', '2026-09-11 14:07:12', 1, NULL, NULL, NULL),
(4, 'f763ef4d-de65-452c-a371-903bbc20dfc4', 1, '1', 'MA36271', '144147868', 'TOURIA', 'ABID', 'F', 'touria.abid@lesecolesalbaraime.com', '0661687114', NULL, '1974-03-06', NULL, '2005-02-01', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '20LOT NAJMAT AZAMA', 'Marocaine', NULL, 'actif', '2026-09-11 14:07:12', '2026-09-11 14:07:12', 4, NULL, NULL, NULL),
(5, 'e2ca4a9c-3a78-4d36-9c42-23251b473d0c', 1, '11', 'VA26348', '149557278', 'LALLA AMINA', 'AIT SIDI HOUMAD', 'F', 'amina.aitsidihoumad@lesecolesalbaraime.com', '0625049365', NULL, '1971-05-14', NULL, '2008-09-30', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '3IMMEUBLE DE  FONCTIONNAIRES Bdmoulay el  hassan', 'Marocaine', NULL, 'actif', '2026-09-11 14:07:12', '2026-09-11 14:07:12', 1, NULL, NULL, NULL),
(6, 'e465ea8a-a275-45bc-834d-7d78e718ae7c', 1, '53', 'RA131663', NULL, 'HICHAM', 'AITBAHCINE', 'M', 'hicham.aitbahcine@lesecolesalbaraime.com', NULL, NULL, '1985-11-18', NULL, '2025-09-01', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 'N32 ETAGE 1 LOT HZMI2', 'Marocaine', NULL, 'actif', '2026-09-11 14:07:12', '2026-09-11 23:02:48', 2, NULL, NULL, NULL),
(7, '0c221df9-d67a-4917-a11a-5b3b81b7b6b0', 1, '48', 'IC63188', NULL, 'HOURIA', 'AKBIL', 'F', 'houria.akbil@lesecolesalbaraime.com', NULL, NULL, '1991-11-26', NULL, '2019-09-01', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 'LOT MARWA 46 ETAGE 2 APP 4', 'Marocaine', NULL, 'actif', '2026-09-11 14:07:12', '2026-09-11 23:23:26', 1, NULL, NULL, NULL),
(8, '5800d6a1-2702-44db-8adf-08bf91ff313f', 1, '72', 'MA55660', NULL, 'MUSTAPHA', 'AL KIHAL', 'M', 'mustapha.alkihal@lesecolesalbaraime.com', NULL, NULL, '1979-01-01', NULL, '2024-09-02', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '03 RUE LAMRAIKATE N 1', 'Marocaine', NULL, 'actif', '2026-09-11 14:07:12', '2026-09-11 23:02:48', 2, NULL, NULL, NULL),
(9, '6d9d8f61-13c7-430a-af49-a620b54e99dc', 1, '17', 'A741060', '134258903', 'SARA', 'AL MIAADI', 'F', 'sara.almiaadi@lesecolesalbaraime.com', '0771011013', NULL, '1991-09-13', NULL, '2016-11-08', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 'RUE 07 N 49 WIFAK', 'Marocaine', NULL, 'actif', '2026-09-11 14:07:12', '2026-09-11 14:07:12', 1, NULL, NULL, NULL),
(10, 'b7d481b5-d483-4240-978d-ce4832bafcc6', 1, '19', 'MA122051', '176106890', 'YOUSSEF', 'ARZAYNE', 'M', 'arzine.youssef@lesecolesalbaraime.com', '0682732465', NULL, '1993-01-26', NULL, '2015-10-29', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '83DERB EL QUARAA', 'Marocaine', NULL, 'actif', '2026-09-11 14:07:12', '2026-09-11 14:07:12', 2, NULL, NULL, NULL),
(11, '1e5c4b68-8691-4268-9b8b-c415066bcaa1', 1, '10', 'M374320', '188875871', 'TIBARIA', 'BAHOUJ', 'F', 'tibaria.bahouj@lesecolesalbaraime.com', '0670565218', NULL, '1983-01-01', NULL, '2010-01-01', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '20 RUE CHTOUKA', 'Marocaine', NULL, 'actif', '2026-09-11 14:07:12', '2026-09-11 14:07:12', 1, NULL, NULL, NULL),
(12, '0318686a-2d4c-4db7-8249-b9cc9e52fbb9', 1, '26', 'MA55441', '188875772', 'NAWAL', 'BELGHAOUTI', 'F', 'nawal.belghaouti@lesecolesalbaraime.com', '0604685288', NULL, '1980-01-23', NULL, '2010-01-01', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 'HAY OUM ERRABIE N 18', 'Marocaine', NULL, 'actif', '2026-09-11 14:07:12', '2026-09-11 14:07:12', 1, NULL, NULL, NULL),
(13, 'db8f84a5-7c22-4d9f-856b-eb032536e2bb', 1, '121', 'MA115349', '147034904', 'AMINA', 'BERHO', 'F', 'amina.berho@lesecolesalbaraime.com', NULL, NULL, '1992-09-25', NULL, '2024-09-01', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '12  APPT 4 ETAGE 2 LOT  ANSSAM', 'Marocaine', NULL, 'actif', '2026-09-11 14:07:12', '2026-09-11 23:23:26', 1, NULL, NULL, NULL),
(14, '97d6070c-a78d-4774-ac1b-133f95411eff', 1, '129', 'D315397', NULL, 'NOUREDDINE', 'BERTAL', 'M', 'noureddine.bertal@lesecolesalbaraime.com', NULL, NULL, '1969-12-16', NULL, '2026-03-10', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 'APPARTEMENT 12 RESIDENCE  NAJAH', 'Marocaine', NULL, 'actif', '2026-09-11 14:07:12', '2026-09-11 23:02:48', 2, NULL, NULL, NULL),
(15, '51ca038d-2c8e-4eec-888b-7d398b49584f', 1, '80', 'MA145900', '166008015', 'NOUHAILA', 'BIGIGUEN', 'F', 'nouhaila.bigiguen@lesecolesalbaraime.com', NULL, NULL, '2000-01-20', NULL, '2025-11-01', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 'RUE 8 N 3 HAY SALAM AL  JAMAA', 'Marocaine', NULL, 'actif', '2026-09-11 14:07:12', '2026-09-11 23:02:48', 1, NULL, NULL, NULL),
(16, '6e5f728c-31f6-46dc-96a2-8e66bbcc7cec', 1, '131', 'MC283525', NULL, 'OMAR', 'BOUAACHA', 'M', 'omar.bouaacha@lesecolesalbaraime.com', NULL, NULL, '1998-03-15', NULL, '2026-03-02', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 'BNI HILAL SIDI BENNOUR', 'Marocaine', NULL, 'actif', '2026-09-11 14:07:12', '2026-09-11 23:02:48', 2, NULL, NULL, NULL),
(17, '2d43805c-7ad1-493d-9713-f7dfb73b4be9', 1, '81', 'C739123', NULL, 'HAJIBA', 'BOUCHAKOUR', 'F', 'hajiba.bouchakour@lesecolesalbaraime.com', NULL, NULL, '1979-02-05', NULL, '2018-09-01', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '135/ZC LOT HADIKA', 'Marocaine', NULL, 'actif', '2026-09-11 14:07:12', '2026-09-11 23:02:48', 1, NULL, NULL, NULL),
(18, '57ac14bd-2442-48e8-9512-837379a44ddd', 1, '69', 'MA39191', '983904016', 'MUSTAPHA', 'CHAKIR', 'M', 'mustapha.chakir@lesecolesalbaraime.com', NULL, NULL, '1975-01-01', NULL, '2023-03-06', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 'RUE SIDI AHMED BEASSER', 'Marocaine', NULL, 'actif', '2026-09-11 14:07:12', '2026-09-11 23:02:48', 5, NULL, NULL, NULL),
(19, '4a0ac209-a937-4a82-b48d-a9f0831a7332', 1, '132', 'M589596', NULL, 'ABDERRAHMANE', 'CHAQROUN', 'M', 'a.chagroun@lesecolesalbaraime.com', NULL, NULL, '1995-06-22', NULL, '2026-03-02', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 'DR OLD BOUNAAIJ MLY  ABDELLAH', 'Marocaine', NULL, 'actif', '2026-09-11 14:07:12', '2026-09-11 23:02:48', 2, NULL, NULL, NULL),
(20, 'bf3bec10-416e-4ba4-8222-30e7c77a9ce2', 1, '85', 'M604158', NULL, 'HAROUNE', 'DARROUJI', 'M', 'harroune.darrouji@lesecolesalbaraime.com', NULL, NULL, '1996-06-24', NULL, '2023-12-15', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 'RES BEN ALLAL ROUTE  MARAKECHE', 'Marocaine', NULL, 'actif', '2026-09-11 14:07:12', '2026-09-11 23:02:48', 2, NULL, NULL, NULL),
(21, '5fe3d3ea-1de6-4076-8026-57282b3da529', 1, '130', 'HA34113', NULL, 'MOHAMMED', 'EDDAHI', 'M', 'm.eddahi@lesecolesalbaraime.com', NULL, NULL, '1972-03-06', NULL, '2026-03-10', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 'N9 COOPERATIVEEZZAITOUN', 'Marocaine', NULL, 'actif', '2026-09-11 14:07:12', '2026-09-11 23:02:48', 2, NULL, NULL, NULL),
(22, 'c2a58f0e-648b-474e-ae27-4c4f6225eb04', 1, '88', 'MA134347', '158998214', 'CHAIMAA', 'EDDOHAOUI', 'F', 'c.eddouhaoui@lesecolesalbaraime.com', NULL, NULL, '1995-12-23', NULL, '2026-04-01', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '23 RUE BENAICHA', 'Marocaine', NULL, 'actif', '2026-09-11 14:07:12', '2026-09-11 23:02:48', 3, NULL, NULL, NULL),
(23, '40c2d115-6f25-424e-85aa-ed84a1ffea21', 1, '87', 'MA123800', '949973704', 'ASMAE', 'EL AROUA', 'F', 'a.elaroua@lesecolesalbaraime.com', NULL, NULL, '1994-05-12', NULL, '2025-09-15', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 'Marocaine', NULL, 'actif', '2026-09-11 14:07:12', '2026-09-11 23:02:48', 1, NULL, NULL, NULL),
(24, '77e50285-ba5a-41ea-97c5-de2a7273fffa', 1, '6', 'MA4342', '163792355', 'MINA', 'EL JAD', 'F', 'mina.eljad@lesecolesalbaraime.com', '0667262697', NULL, '1967-06-24', NULL, '2002-09-30', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 'COP ALASDIKAA', 'Marocaine', NULL, 'actif', '2026-09-11 14:07:12', '2026-09-11 23:23:26', 1, NULL, NULL, NULL),
(25, '852ae808-9d1a-4859-8ce8-83234726efc5', 1, '22', 'MA48866', '144148262', 'RACHIDA', 'EL KHATIRI', 'F', 'rachida.elkhatiri@lesecolesalbaraime.com', '0620767045', NULL, '1978-09-15', NULL, '2005-02-01', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 'PAM SIDI ALI CHTOUKA', 'Marocaine', NULL, 'actif', '2026-09-11 14:07:12', '2026-09-11 23:23:26', 3, NULL, NULL, NULL),
(26, '5d588ae9-af98-4c6b-bca3-d2733114dcc7', 1, '29', 'M298075', '161582584', 'HASSAN', 'EL MASSOUDI', 'M', 'h.elmassoudi@lesecolesalbaraime.com', NULL, NULL, '1974-12-12', NULL, '2012-01-10', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 'DOUAR LAMCHARKA CAIADT  OULED RAHMOUNE', 'Marocaine', NULL, 'actif', '2026-09-11 14:07:12', '2026-09-11 23:02:48', 6, NULL, NULL, NULL),
(27, 'af8e9d7f-2cb7-43e1-8e84-da8bc7563ef6', 1, '16', 'M493435', '134259006', 'SALMA', 'ELKADIOUI EL IDRISSI', 'F', 'salma.elkadiouielidrissi@lesecolesalbaraime.com', '0627602944', NULL, '1991-03-29', NULL, '2016-09-19', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 'RES MOUAHIDINE A 22  IMMEUBLE 1 APPT 30ETAGE 1', 'Marocaine', NULL, 'actif', '2026-09-11 14:07:12', '2026-09-11 23:23:26', 1, NULL, NULL, NULL),
(28, 'd9a4ff47-bd09-4422-aadc-48e1cde0bd23', 1, '12', 'MA53349', '123899076', 'HASNA', 'ELMAHFOUDHY', 'F', 'hasna.elmahfoudhy@lesecolesalbaraime.com', '0621118529', NULL, '1983-12-10', NULL, '2011-09-01', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 'RES YAKOUT N 36 ETAGE 2 N 7', 'Marocaine', NULL, 'actif', '2026-09-11 14:07:12', '2026-09-11 23:23:26', 1, NULL, NULL, NULL),
(29, 'bb5b0e05-8846-4c1f-acf2-c9abf1c3c957', 1, '18', 'MA95538', '146941094', 'OMAR', 'ER RAMMAL', 'M', 'omar.errammal@lesecolesalbaraime.com', '0663584350', NULL, '1987-09-23', NULL, '2014-09-01', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 'PAM SIDI ALI', 'Marocaine', NULL, 'actif', '2026-09-11 14:07:12', '2026-09-11 14:07:12', 2, NULL, NULL, NULL),
(30, '42114cc3-f8ed-4d4f-bd4b-d859e6343373', 1, '110', 'M97306', NULL, 'NAJAT', 'ER-RBAHY', 'F', 'n.errbahy@lesecolesalbaraime.com', NULL, NULL, '1980-09-29', NULL, '2023-09-01', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 'Marocaine', NULL, 'actif', '2026-09-11 14:07:12', '2026-09-11 23:02:48', 1, NULL, NULL, NULL),
(31, '9adab5b3-b2f8-4200-9d89-a3862df432b2', 1, '83', 'MA158191', '172539753', 'SOUKAINA', 'ESSALAMA', 'F', 's.essalama@lesecolesalbaraime.com', NULL, NULL, '2001-08-14', NULL, '2024-10-14', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 'HAY SALAM LEMSALLAH 16 RUE  1', 'Marocaine', NULL, 'actif', '2026-09-11 14:07:12', '2026-09-11 23:02:48', 1, NULL, NULL, NULL),
(32, '907d3213-7dd5-43b4-8c10-bd82222d93ef', 1, '7', 'MA19798', '144148064', 'FATNA', 'ETTOUMI', 'F', 'fatna.ettoumi@lesecolesalbaraime.com', '0706440593', NULL, '1971-04-08', NULL, '2005-02-01', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '19 RUE BOUJIDA', 'Marocaine', NULL, 'actif', '2026-09-11 14:07:12', '2026-09-11 14:07:12', 1, NULL, NULL, NULL),
(33, 'd567916e-485f-430d-8f00-c08de2ee496a', 1, '23', 'MA28554', '149557377', 'AMINA', 'FARIS', 'F', 'amina.faris@lesecolesalbaraime.com', '0678912562', NULL, '1973-01-01', NULL, '2008-09-30', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '3 RUE BRINCHE', 'Marocaine', NULL, 'actif', '2026-09-11 14:07:12', '2026-09-11 23:23:26', 3, NULL, NULL, NULL),
(34, 'd6c06270-eaee-4e74-a6bf-c4d0cec48a6c', 1, '25', 'MA53897', '127742693', 'SAIDA', 'FIKRI', 'F', 'saida.fikri@lesecolesalbaraime.com', '0602887041', NULL, '1978-10-27', NULL, '2013-11-01', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '314 BLOC HAY OUM RBIA', 'Marocaine', NULL, 'actif', '2026-09-11 14:07:12', '2026-09-11 14:07:12', 1, NULL, NULL, NULL),
(35, 'f26f803e-01db-4583-a41c-3e61ab71e96e', 1, '57', 'M517491', NULL, 'AMINE', 'FIRAR', 'M', 'a.firar@lesecolesalbaraime.com', NULL, NULL, '1992-11-01', NULL, '2021-10-14', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '14 LOT SANAA', 'Marocaine', NULL, 'actif', '2026-09-11 14:07:12', '2026-09-11 23:02:48', 2, NULL, NULL, NULL),
(36, 'efad8602-68a8-4dc5-b3b5-ae99ecefecc6', 1, '52', 'M354568', NULL, 'ABDERRAHIM', 'GUEZRI', 'M', 'a.guezri@lesecolesalbaraime.com', NULL, NULL, '1981-11-08', NULL, '2025-09-01', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '101 RUE AHMED RAMI LOT  NARJIS', 'Marocaine', NULL, 'actif', '2026-09-11 14:07:12', '2026-09-11 23:02:48', 2, NULL, NULL, NULL),
(37, 'e292d7a6-dd65-46c0-96e8-40e522b5bbef', 1, '66', 'MA136131', '188130248', 'MOHAMMED', 'HAMAMAT', 'M', 'mohammed.hamamat@lesecolesalbaraime.com', '0661981156', NULL, '2001-09-21', NULL, '2022-10-01', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 'Bd  Med V', 'Marocaine', NULL, 'actif', '2026-09-11 14:07:12', '2026-09-11 14:07:12', 4, NULL, NULL, NULL),
(38, 'e5bdeb53-98fc-4b51-a9ed-017d48238a06', 1, '30', 'BJ224442', '156306973', 'MOHAMED', 'HAMDOUNE', 'M', 'm.hamdoune@lesecolesalbaraime.com', NULL, NULL, '1974-01-01', NULL, '2011-02-10', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 'DOUAR AITMOUKA CHTOUKA', 'Marocaine', NULL, 'actif', '2026-09-11 14:07:12', '2026-09-11 23:02:48', 5, NULL, NULL, NULL),
(39, '8cb19f8c-cb03-4efc-8e96-b9d1b1582f97', 1, '44', 'MA131762', '155820115', 'AZIZA', 'HAMIDI', 'F', 'aziza.hamidi@lesecolesalbaraime.com', '0635608896', NULL, '1996-04-09', NULL, '2022-03-01', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 'DR EL KOUARA SIDI ALI', 'Marocaine', NULL, 'actif', '2026-09-11 14:07:12', '2026-09-11 23:23:26', 1, NULL, NULL, NULL),
(40, 'a787d8b6-ec92-444c-8139-ebf31a8b6f0c', 1, '124', 'M380145', NULL, 'HAKIMA', 'HAMMOUR', 'F', 'h.hammour@lesecolesalbaraime.com', NULL, NULL, '1984-01-22', NULL, '2025-09-30', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '26 LOT BENDAHMAN', 'Marocaine', NULL, 'actif', '2026-09-11 14:07:12', '2026-09-11 23:02:48', 1, NULL, NULL, NULL),
(41, '11da978f-da13-4399-b2ff-b01e21e81801', 1, '128', 'M318531', NULL, 'BOUCHAIB', 'HASNAOUI', 'M', 'b.hasnaoui@lesecolesalbaraime.com', NULL, NULL, '1979-04-06', NULL, '2025-10-01', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 'الرقم 3 المنظر الجميل', 'Marocaine', NULL, 'actif', '2026-09-11 14:07:12', '2026-09-11 23:02:48', 2, NULL, NULL, NULL),
(42, '54aa4012-da3c-4d61-a861-e5b82120827d', 1, '122', 'MA97808', NULL, 'CHOUAIB', 'HRAIMACH', 'M', 'c.hraimach@lesecolesalbaraime.com', NULL, NULL, '1987-01-01', NULL, '2025-09-12', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 'IMMEUBLE 05 APRT 13 ETAGE3  SANIAT LBHAR', 'Marocaine', NULL, 'actif', '2026-09-11 14:07:12', '2026-09-11 23:02:48', 2, NULL, NULL, NULL),
(43, 'ddbd9c7d-ccb1-47bd-a2a1-1f65fa9dfe69', 1, '33', 'MA48861', '197235785', 'BOUCHRA', 'JEDAA', 'F', 'bouchra.jedaa@lesecolesalbaraime.com', '0637133901', NULL, '1976-04-17', NULL, '2013-09-01', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '23 RUE BRINCHE', 'Marocaine', NULL, 'actif', '2026-09-11 14:07:12', '2026-09-11 14:07:12', 7, NULL, NULL, NULL),
(44, '35a5eaae-2021-482f-9fc4-7f7d8f3e44d8', 1, '5', 'M87524', '149331052', 'SOUMIA', 'KABRITI', 'F', 'soumia.kabriti@lesecolesalbaraime.com', '0618867830', NULL, '1963-03-27', NULL, '2001-09-30', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 'COP  ASDIKAE', 'Marocaine', NULL, 'actif', '2026-09-11 14:07:12', '2026-09-11 14:07:12', 1, NULL, NULL, NULL),
(45, '654669ca-46ee-4669-8502-354421367fba', 1, '82', 'JH42355', '126133918', 'ZINEB', 'KARIMA', 'F', 'z.karima@lesecolesalbaraime.com', NULL, NULL, '1998-06-08', NULL, '2025-09-15', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 'HAY MAALLA BIOGRA', 'Marocaine', NULL, 'actif', '2026-09-11 14:07:12', '2026-09-11 23:02:48', 3, NULL, NULL, NULL),
(46, '53f2bcbf-c973-4423-8189-2f62b0c8c9ec', 1, '89', 'MA138674', '106816066', 'FATIMA.ZAHRA', 'KHOLTI', 'F', 'f.kholti@lesecolesalbaraime.com', NULL, NULL, '1997-01-29', NULL, '2025-09-15', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 'PAM RUE 3 NR 19', 'Marocaine', NULL, 'actif', '2026-09-11 14:07:12', '2026-09-11 23:02:48', 1, NULL, NULL, NULL),
(47, '794c5942-cf3d-446b-8024-89712752358e', 1, '45', 'MA133764', '147770106', 'AHLAM', 'LAAROUA', 'F', 'ahlam.laaroua.zekkar@lesecolesalbaraime.com', '0673650315', NULL, '1996-06-30', NULL, '2022-03-01', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 'EL WIFAK 2 RUE 15 N 14', 'Marocaine', NULL, 'actif', '2026-09-11 14:07:12', '2026-09-11 14:07:12', 1, NULL, NULL, NULL),
(48, 'e31ea2f8-54cc-452a-a68f-0a1863bee785', 1, '127', 'MC223122', NULL, 'M\'hamed', 'LAHMAR', 'M', 'm.lahmar@lesecolesalbaraime.com', NULL, NULL, '1992-09-01', NULL, '2024-09-01', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 'DOUAR BRAHIM EL GHOBRA', 'Marocaine', NULL, 'actif', '2026-09-11 14:07:12', '2026-09-11 23:02:48', 2, NULL, NULL, NULL),
(49, '893ac4bb-855c-43f4-9c72-49ef54c7f2e5', 1, '55', 'M596256', NULL, 'MOAD', 'LOBANI', 'M', 'm.lobani@lesecolesalbaraime.com', NULL, NULL, '1997-08-10', NULL, '2025-09-30', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 'APPT 04 IMM 72 AV MOHAMED  RIFI', 'Marocaine', NULL, 'actif', '2026-09-11 14:07:12', '2026-09-11 23:02:48', 2, NULL, NULL, NULL),
(50, '6d83c8bf-970c-4506-9744-da0af0203a21', 1, '3', 'H204814', '110466888', 'ESSEDIYA', 'LOUDINI', 'F', 'essediya.loudini@lesecolesalbaraime.com', '0640866533', NULL, '1971-12-26', NULL, '2010-09-01', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 'LOT ELFATH', 'Marocaine', NULL, 'actif', '2026-09-11 14:07:12', '2026-09-11 14:07:12', 8, NULL, NULL, NULL),
(51, '25f36e62-e030-4b5c-bb21-42a59e760f4b', 1, '54', 'MA110143', NULL, 'MOHAMED', 'MAKSAOUI', 'M', 'm.maksaoui@lesecolesalbaraime.com', NULL, NULL, '1989-04-01', NULL, '2025-09-01', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 'HAY EL AMAL RUE 14 N 23', 'Marocaine', NULL, 'actif', '2026-09-11 14:07:12', '2026-09-11 23:02:48', 2, NULL, NULL, NULL),
(52, 'ab3a8f68-3500-4cba-bc52-ac479b8b56a0', 1, '84', 'MA41066', '105958560', 'KHADIJA', 'MENAOUAR', 'F', 'k.menaouar@lesecolesalbaraime.com', NULL, NULL, '1978-01-01', NULL, '2025-09-15', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 'DOUAR ELKHERBA SIDI ALI', 'Marocaine', NULL, 'actif', '2026-09-11 14:07:12', '2026-09-11 23:23:26', 3, NULL, NULL, NULL),
(53, 'e16ba71b-7e17-49d7-bc9f-9cc90014366f', 1, '50', 'Q174833', NULL, 'SAID', 'MESSAOU', 'M', 's.massaou@lesecolesalbaraime.com', NULL, NULL, '1974-06-20', NULL, '2025-09-15', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '7 LOT PAVILLON BLEU ETAGE 2', 'Marocaine', NULL, 'actif', '2026-09-11 14:07:12', '2026-09-11 23:02:48', 2, NULL, NULL, NULL),
(54, '651df5e4-d136-49a3-809d-84f73c6a0fd2', 1, '123', 'MA121352', NULL, 'HALIMA', 'MOKTASSID', 'F', 'h.moktassid@lesecolesalbaraime.com', NULL, NULL, '1993-08-28', NULL, '2025-09-04', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '372 OUMERBIA', 'Marocaine', NULL, 'actif', '2026-09-11 14:07:12', '2026-09-11 23:23:26', 1, NULL, NULL, NULL),
(55, '1e187c03-e172-4f43-b971-e60cd3a5d2be', 1, '46', 'MA21046', '122453550', 'ZOUBIDA', 'MOUJANE', 'F', 'zoubida.moujane@lesecolesalbaraime.com', '0668936456', NULL, '1970-02-17', NULL, '2019-09-01', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 'BD CHOUAY RUE 7 N 4', 'Marocaine', NULL, 'actif', '2026-09-11 14:07:12', '2026-09-11 14:07:12', 1, NULL, NULL, NULL),
(56, '64053c55-8836-446e-99ec-e114c4d4de0f', 1, '20', 'MA69768', '188875178', 'HASNA', 'RAMI', 'F', 'hasna.rami@lesecolesalbaraime.com', '0634271989', NULL, '1983-01-01', NULL, '2017-09-01', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '111 LOT OUM ERBIA', 'Marocaine', NULL, 'actif', '2026-09-11 14:07:12', '2026-09-11 23:23:26', 1, NULL, NULL, NULL),
(57, 'f2fba9b5-31aa-4944-80fc-586fbe716e4b', 1, '76', 'M465128', NULL, 'MOUNIR', 'SARIH', 'M', 'm.sarih@lesecolesalbaraime.com', NULL, NULL, '1990-05-04', NULL, '2023-09-01', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 'RES NAJMAT EL JANOUB 2  IMMEUBLE  F APPT 15', 'Marocaine', NULL, 'actif', '2026-09-11 14:07:12', '2026-09-11 23:02:48', 2, NULL, NULL, NULL),
(58, '64ba8695-0510-406b-9a56-d67284f2bfd0', 1, '15', 'BL18825', '198571493', 'HANANE', 'TABTI', 'F', 'tabti.hanane@lesecolesalbaraime.com', '0671582191', NULL, '1976-06-03', NULL, '2015-11-07', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '47 APPT 3 LOT  EL HAMD', 'Marocaine', NULL, 'actif', '2026-09-11 14:07:12', '2026-09-11 14:07:12', 1, NULL, NULL, NULL),
(59, '84a7b919-0154-4590-a802-01e41916d86e', 1, '2', 'MA10969', '163539955', 'AMINA', 'TUIRTOU', 'F', 'touirtouamina@gmail.com', '0771367985', NULL, '1968-09-06', NULL, '2007-09-01', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 'IMMEUBLE 8 APPT 4 ETAGE 1  LOT TMIRI', 'Marocaine', NULL, 'actif', '2026-09-11 14:07:12', '2026-09-11 14:07:12', 9, NULL, NULL, NULL),
(60, 'fd42b1d4-9670-4006-a74d-d16583d6821e', 1, '8', 'MA36282', '191740061', 'KHADIJA', 'ZAIDOUH', 'F', 'khadija.zaidouh@lesecolesalbaraime.com', '0701035329', NULL, '1974-05-24', NULL, '2006-09-01', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 'Bd Med V N 5', 'Marocaine', NULL, 'actif', '2026-09-11 14:07:12', '2026-09-11 14:07:12', 1, NULL, NULL, NULL),
(61, '1ac2e7e5-81ac-430b-9f4a-2c7500b7dded', 1, '79', 'BE794956', '129489583', 'SANAA', 'ZAKKAR', 'F', 'sanaa.zekkar@lesecolesalbaraime.com', '0707383686', NULL, '1984-11-01', NULL, '2023-09-11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 'DR SOUANI MOUSSE CLE  HAOUZIA', 'Marocaine', NULL, 'actif', '2026-09-11 14:07:12', '2026-09-11 14:07:12', 1, NULL, NULL, NULL),
(62, 'c199cbff-396a-4365-8ce4-e9f6ec18a258', 1, '90', 'MA156920', '107047864', 'FATIMA-EZAHRA', 'ZAMZMI', 'F', 'f.zamzmi@lesecolesalbaraime.com', NULL, NULL, '1999-11-29', NULL, '2025-09-15', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 'DOUAR SOUANI ELMOUSSE', 'Marocaine', NULL, 'actif', '2026-09-11 14:07:12', '2026-09-11 23:02:48', 3, NULL, NULL, NULL),
(63, 'cc6fdcf0-02ad-44ff-9d66-59df523427e4', 1, '86', 'MA37162', '113729150', 'EL KEBIRA', 'ZEROUALI', 'F', 'elkebira.zerouali@lesecolesalbaraime.com', '0681721928', NULL, '1973-07-01', NULL, '2020-01-01', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 'DERB LALLA RKAIA JILALIA', 'Marocaine', NULL, 'actif', '2026-09-11 14:07:12', '2026-09-11 23:23:26', 3, NULL, NULL, NULL),
(64, 'c863d9a9-c71b-4249-9c67-109c92a2090', 1, '310000', '310000', '310000310000', 'ismail', 'abounasr', 'M', 'iabounasr@lesecolesalbaraime.com', '0649074204', NULL, '1966-01-01', NULL, '2009-09-01', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 'HAY ESSALAME RUE 8 N  2ETAGE 2', 'Marocaine', NULL, 'inactif', '2026-09-11 14:07:12', '2026-09-14 16:05:24', 4, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `employee_accidents`
--

CREATE TABLE `employee_accidents` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ecole_setting_id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `date_accident` datetime DEFAULT NULL,
  `rembourse` tinyint(1) NOT NULL DEFAULT 0,
  `montant` decimal(10,2) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `dossiers` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`dossiers`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `employee_credits`
--

CREATE TABLE `employee_credits` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ecole_setting_id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `types` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`types`)),
  `montant` decimal(12,2) DEFAULT NULL,
  `mensualite` decimal(10,2) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `employee_documents`
--

CREATE TABLE `employee_documents` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ecole_setting_id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `type_document` varchar(255) NOT NULL DEFAULT 'autre',
  `name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_type` varchar(255) DEFAULT NULL,
  `file_size` bigint(20) UNSIGNED DEFAULT NULL,
  `uploaded_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `employee_document_types`
--

CREATE TABLE `employee_document_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ecole_setting_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `employee_fondation_m6`
--

CREATE TABLE `employee_fondation_m6` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ecole_setting_id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `annee_scolaire` varchar(20) DEFAULT NULL,
  `fichiers` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`fichiers`)),
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `employee_groupe`
--

CREATE TABLE `employee_groupe` (
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `groupe_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `groupes`
--

CREATE TABLE `groupes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ecole_setting_id` bigint(20) UNSIGNED NOT NULL,
  `niveau_scolaire_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `groupes`
--

INSERT INTO `groupes` (`id`, `ecole_setting_id`, `niveau_scolaire_id`, `name`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 2, '1ère Année du primaire', '2026-09-10 22:38:23', '2026-09-10 22:38:23', NULL),
(2, 1, 2, '2ème Année du primaire', '2026-09-10 22:38:23', '2026-09-10 22:38:23', NULL),
(3, 1, 2, '3ème Année du primaire', '2026-09-10 22:38:23', '2026-09-10 22:38:23', NULL),
(4, 1, 2, '4ème Année du primaire', '2026-09-10 22:38:23', '2026-09-10 22:38:23', NULL),
(5, 1, 2, '5ème Année du primaire', '2026-09-10 22:38:23', '2026-09-10 22:38:23', NULL),
(6, 1, 2, '6ème Année du primaire', '2026-09-10 22:38:23', '2026-09-10 22:38:23', NULL),
(7, 1, 3, '1ère Année du collège – Parcours International', '2026-09-10 22:38:23', '2026-09-10 22:38:23', NULL),
(8, 1, 3, '2ème Année du collège – Parcours International', '2026-09-10 22:38:23', '2026-09-10 22:38:23', NULL),
(9, 1, 3, '3ème Année du collège – Parcours International', '2026-09-10 22:38:23', '2026-09-10 22:38:23', NULL),
(10, 1, 4, 'Tronc Commun Scientifiques – Parcours International', '2026-09-10 22:38:23', '2026-09-10 22:38:23', NULL),
(11, 1, 4, '1ère Année du Baccalauréat Sciences Expérimentales – Parcours International', '2026-09-10 22:38:23', '2026-09-10 22:38:23', NULL),
(12, 1, 4, '1ère Année du Baccalauréat Sciences Mathématiques', '2026-09-10 22:38:23', '2026-09-10 22:38:23', NULL),
(13, 1, 4, '2ème Année du Baccalauréat Sciences Physiques – Parcours International', '2026-09-10 22:38:23', '2026-09-10 22:38:23', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `jobs`
--

INSERT INTO `jobs` (`id`, `queue`, `payload`, `attempts`, `reserved_at`, `available_at`, `created_at`) VALUES
(1, 'default', '{\"uuid\":\"768262ca-4b7c-44cf-b087-6ca28a27645d\",\"displayName\":\"Filament\\\\Auth\\\\Notifications\\\\ResetPassword\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:3;}s:9:\\\"relations\\\";a:1:{i:0;s:5:\\\"roles\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:41:\\\"Filament\\\\Auth\\\\Notifications\\\\ResetPassword\\\":3:{s:3:\\\"url\\\";s:235:\\\"http:\\/\\/127.0.0.1:8000\\/admin\\/password-reset\\/reset?email=iabounasr%40lesecolesalbaraime.com&token=e135250e3fd9e9b919ed3039975c8d2d79d3489ac0f2d867537fb1dda78e0a6d&signature=db92e1c1114c005e4b690a7fa97233b18657ccbf606d7666026abc30c57a9a5d\\\";s:5:\\\"token\\\";s:64:\\\"e135250e3fd9e9b919ed3039975c8d2d79d3489ac0f2d867537fb1dda78e0a6d\\\";s:2:\\\"id\\\";s:36:\\\"8e85e7ab-cc6d-476b-b26c-a0c7727d9f3d\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:4:\\\"mail\\\";}}\",\"batchId\":null},\"createdAt\":1789160859,\"delay\":null}', 0, NULL, 1789160859, 1789160859);

-- --------------------------------------------------------

--
-- Structure de la table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `leaves`
--

CREATE TABLE `leaves` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ecole_setting_id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `categorie` enum('conge','absence') NOT NULL DEFAULT 'conge',
  `leave_type_id` bigint(20) UNSIGNED DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `reason` text DEFAULT NULL,
  `justificatif` varchar(255) DEFAULT NULL,
  `status` enum('en_attente','approuvé','refusé') NOT NULL DEFAULT 'en_attente',
  `communication_method` varchar(50) DEFAULT NULL,
  `appointment_date` datetime DEFAULT NULL,
  `actions_taken` text DEFAULT NULL,
  `rh_notes` text DEFAULT NULL,
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `remplacant_id` bigint(20) UNSIGNED DEFAULT NULL,
  `type_cours` enum('exercice','lecon','activite') DEFAULT NULL,
  `nb_pages` varchar(255) DEFAULT NULL,
  `intitule_lecon` varchar(255) DEFAULT NULL,
  `intitule_activite` varchar(255) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `leave_types`
--

CREATE TABLE `leave_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ecole_setting_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `leave_types`
--

INSERT INTO `leave_types` (`id`, `ecole_setting_id`, `name`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'Congé maladie', '2026-09-10 22:38:23', '2026-09-10 22:38:23', NULL),
(2, 1, 'Congé maternité', '2026-09-10 22:38:23', '2026-09-10 22:38:23', NULL),
(3, 1, 'Congé spécial', '2026-09-11 14:41:27', '2026-09-11 14:41:27', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_05_01_233704_create_permission_tables', 1),
(5, '2026_05_01_233805_create_ecole_settings_table', 1),
(6, '2026_05_01_233900_add_ecole_setting_id_to_users_table', 1),
(7, '2026_05_01_234246_create_employees_table', 1),
(8, '2026_05_01_235518_create_leave_types_table', 1),
(9, '2026_05_01_235518_create_leaves_table', 1),
(10, '2026_05_02_000003_create_document_requests_table', 1),
(11, '2026_05_02_171623_add_employee_id_to_users_table', 1),
(12, '2026_05_02_232555_add_photo_to_employees_table', 1),
(13, '2026_05_02_232555_create_employee_documents_table', 1),
(14, '2026_05_06_182102_create_activity_log_table', 1),
(15, '2026_05_06_182103_add_event_column_to_activity_log_table', 1),
(16, '2026_05_06_182104_add_batch_uuid_column_to_activity_log_table', 1),
(17, '2026_08_25_100000_create_professions_table', 1),
(18, '2026_08_25_100001_add_profession_fields_to_employees_table', 1),
(19, '2026_08_25_100002_create_niveaux_scolaires_table', 1),
(20, '2026_08_25_100003_create_groupes_table', 1),
(21, '2026_08_25_100004_create_employee_groupe_table', 1),
(22, '2026_08_25_100005_add_absence_fields_to_leaves_table', 1),
(23, '2026_08_25_100006_update_document_requests_table', 1),
(24, '2026_08_25_100007_add_type_document_to_employee_documents_table', 1),
(25, '2026_08_26_152957_create_transports_table', 1),
(26, '2026_08_27_135829_create_document_types_table', 1),
(27, '2026_08_27_152343_add_suivi_rh_to_leaves_table', 1),
(28, '2026_08_27_155448_remove_legal_days_per_year_from_leave_types_table', 1),
(29, '2026_08_28_141231_add_gender_nationality_to_employees_table', 1),
(30, '2026_08_29_163534_add_uuid_to_employees_table', 1),
(31, '2026_08_30_141457_drop_severity_level_from_leaves_table', 1),
(32, '2026_08_30_141646_create_communication_methods_table', 1),
(33, '2026_08_30_141704_change_communication_method_to_string_in_leaves_table', 1),
(34, '2026_08_30_195215_alter_marital_status_nullable_in_employees', 1),
(35, '2026_08_30_200600_alter_contract_type_nullable_in_employees', 1),
(36, '2026_08_30_221137_add_soft_deletes_to_all_tables', 1),
(37, '2026_08_31_171806_add_photocopie_fields_to_document_requests_table', 1),
(38, '2026_08_31_175017_add_rencontre_fields_to_document_requests_table', 1),
(39, '2026_08_31_175539_create_categorie_autre_demandes_table', 1),
(40, '2026_08_31_180000_create_nature_documents_table', 1),
(41, '2026_08_31_215618_drop_categorie_autre_demandes', 1),
(42, '2026_09_06_153231_drop_type_from_transports_table', 1),
(44, '2026_09_12_100000_create_employee_document_types_table', 2),
(45, '2026_09_12_012210_add_date_souhaitee_to_document_requests_table', 3),
(46, '2026_09_12_030122_add_fichier_joint_to_document_requests_table', 4),
(52, '2026_09_13_161357_add_social_fields_to_employees_table', 5),
(53, '2026_09_13_162816_create_employee_accidents_table', 5),
(54, '2026_09_13_162816_create_employee_fondation_m6_table', 5),
(55, '2026_09_13_162817_create_employee_credits_table', 5);

-- --------------------------------------------------------

--
-- Structure de la table `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 1),
(3, 'App\\Models\\User', 2),
(2, 'App\\Models\\User', 3),
(3, 'App\\Models\\User', 5),
(6, 'App\\Models\\User', 6),
(9, 'App\\Models\\User', 7),
(2, 'App\\Models\\User', 8),
(9, 'App\\Models\\User', 9),
(9, 'App\\Models\\User', 10),
(9, 'App\\Models\\User', 11),
(9, 'App\\Models\\User', 12),
(9, 'App\\Models\\User', 13),
(9, 'App\\Models\\User', 14),
(9, 'App\\Models\\User', 15),
(9, 'App\\Models\\User', 16),
(9, 'App\\Models\\User', 17),
(9, 'App\\Models\\User', 18),
(9, 'App\\Models\\User', 19),
(9, 'App\\Models\\User', 20),
(9, 'App\\Models\\User', 21),
(7, 'App\\Models\\User', 22),
(9, 'App\\Models\\User', 23),
(9, 'App\\Models\\User', 24),
(9, 'App\\Models\\User', 25),
(6, 'App\\Models\\User', 26),
(9, 'App\\Models\\User', 27),
(9, 'App\\Models\\User', 28),
(6, 'App\\Models\\User', 29),
(8, 'App\\Models\\User', 30),
(9, 'App\\Models\\User', 31),
(9, 'App\\Models\\User', 32),
(9, 'App\\Models\\User', 33),
(9, 'App\\Models\\User', 34),
(9, 'App\\Models\\User', 35),
(9, 'App\\Models\\User', 36),
(6, 'App\\Models\\User', 37),
(9, 'App\\Models\\User', 38),
(9, 'App\\Models\\User', 39),
(9, 'App\\Models\\User', 40),
(1, 'App\\Models\\User', 41),
(7, 'App\\Models\\User', 42),
(9, 'App\\Models\\User', 43),
(9, 'App\\Models\\User', 44),
(9, 'App\\Models\\User', 45),
(9, 'App\\Models\\User', 46),
(11, 'App\\Models\\User', 47),
(9, 'App\\Models\\User', 48),
(6, 'App\\Models\\User', 49),
(9, 'App\\Models\\User', 50),
(9, 'App\\Models\\User', 51),
(9, 'App\\Models\\User', 52),
(9, 'App\\Models\\User', 53),
(4, 'App\\Models\\User', 54),
(9, 'App\\Models\\User', 55),
(6, 'App\\Models\\User', 56),
(9, 'App\\Models\\User', 57),
(9, 'App\\Models\\User', 58),
(9, 'App\\Models\\User', 59),
(9, 'App\\Models\\User', 60),
(9, 'App\\Models\\User', 61),
(9, 'App\\Models\\User', 62),
(3, 'App\\Models\\User', 63),
(9, 'App\\Models\\User', 64),
(9, 'App\\Models\\User', 65),
(6, 'App\\Models\\User', 66),
(6, 'App\\Models\\User', 67),
(9, 'App\\Models\\User', 68),
(9, 'App\\Models\\User', 69);

-- --------------------------------------------------------

--
-- Structure de la table `nature_documents`
--

CREATE TABLE `nature_documents` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ecole_setting_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `nature_documents`
--

INSERT INTO `nature_documents` (`id`, `ecole_setting_id`, `name`, `active`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 1, 'Examen', 1, 1, '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(2, 1, 'Série d\'exercices', 1, 2, '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(3, 1, 'Contrôle continu', 1, 3, '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(4, 1, 'Contrôle d\'essai', 1, 4, '2026-09-10 22:38:23', '2026-09-10 22:38:23');

-- --------------------------------------------------------

--
-- Structure de la table `niveaux_scolaires`
--

CREATE TABLE `niveaux_scolaires` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ecole_setting_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `order` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `niveaux_scolaires`
--

INSERT INTO `niveaux_scolaires` (`id`, `ecole_setting_id`, `name`, `order`, `created_at`, `updated_at`) VALUES
(2, 1, 'Primaire', 2, '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(3, 1, 'Collège', 3, '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(4, 1, 'Lycée', 4, '2026-09-10 22:38:23', '2026-09-10 22:38:23');

-- --------------------------------------------------------

--
-- Structure de la table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `password_reset_tokens`
--

INSERT INTO `password_reset_tokens` (`email`, `token`, `created_at`) VALUES
('hasnaa.abassi@lesecolesalbaraime.com', '$2y$12$MEcN5Cgx28ugrnwUGmFa8OBhzRy6kKEp9onIur.V5C.vcYvoR48de', '2026-09-12 08:44:03'),
('y.alaoui@lesecolesalbaraime.com', '$2y$12$j0tCAvzXspjh5uL39.nPYe/Igz5tirgq2SfUeSw7yUVtx3vTLniHi', '2026-09-13 22:49:02');

-- --------------------------------------------------------

--
-- Structure de la table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'ViewAny:Activity', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(2, 'View:Activity', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(3, 'Create:Activity', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(4, 'Update:Activity', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(5, 'Delete:Activity', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(6, 'DeleteAny:Activity', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(7, 'Restore:Activity', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(8, 'ForceDelete:Activity', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(9, 'ForceDeleteAny:Activity', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(10, 'RestoreAny:Activity', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(11, 'Replicate:Activity', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(12, 'Reorder:Activity', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(13, 'ViewAny:DocumentRequest', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(14, 'View:DocumentRequest', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(15, 'Create:DocumentRequest', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(16, 'Update:DocumentRequest', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(17, 'Delete:DocumentRequest', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(18, 'DeleteAny:DocumentRequest', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(19, 'Restore:DocumentRequest', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(20, 'ForceDelete:DocumentRequest', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(21, 'ForceDeleteAny:DocumentRequest', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(22, 'RestoreAny:DocumentRequest', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(23, 'Replicate:DocumentRequest', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(24, 'Reorder:DocumentRequest', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(25, 'ViewAny:CommunicationMethod', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(26, 'View:CommunicationMethod', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(27, 'Create:CommunicationMethod', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(28, 'Update:CommunicationMethod', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(29, 'Delete:CommunicationMethod', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(30, 'DeleteAny:CommunicationMethod', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(31, 'Restore:CommunicationMethod', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(32, 'ForceDelete:CommunicationMethod', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(33, 'ForceDeleteAny:CommunicationMethod', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(34, 'RestoreAny:CommunicationMethod', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(35, 'Replicate:CommunicationMethod', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(36, 'Reorder:CommunicationMethod', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(37, 'ViewAny:Employee', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(38, 'View:Employee', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(39, 'Create:Employee', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(40, 'Update:Employee', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(41, 'Delete:Employee', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(42, 'DeleteAny:Employee', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(43, 'Restore:Employee', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(44, 'ForceDelete:Employee', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(45, 'ForceDeleteAny:Employee', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(46, 'RestoreAny:Employee', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(47, 'Replicate:Employee', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(48, 'Reorder:Employee', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(49, 'ViewAny:Groupe', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(50, 'View:Groupe', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(51, 'Create:Groupe', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(52, 'Update:Groupe', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(53, 'Delete:Groupe', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(54, 'DeleteAny:Groupe', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(55, 'Restore:Groupe', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(56, 'ForceDelete:Groupe', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(57, 'ForceDeleteAny:Groupe', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(58, 'RestoreAny:Groupe', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(59, 'Replicate:Groupe', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(60, 'Reorder:Groupe', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(61, 'ViewAny:Leave', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(62, 'View:Leave', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(63, 'Create:Leave', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(64, 'Update:Leave', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(65, 'Delete:Leave', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(66, 'DeleteAny:Leave', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(67, 'Restore:Leave', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(68, 'ForceDelete:Leave', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(69, 'ForceDeleteAny:Leave', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(70, 'RestoreAny:Leave', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(71, 'Replicate:Leave', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(72, 'Reorder:Leave', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(73, 'ViewAny:LeaveType', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(74, 'View:LeaveType', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(75, 'Create:LeaveType', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(76, 'Update:LeaveType', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(77, 'Delete:LeaveType', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(78, 'DeleteAny:LeaveType', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(79, 'Restore:LeaveType', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(80, 'ForceDelete:LeaveType', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(81, 'ForceDeleteAny:LeaveType', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(82, 'RestoreAny:LeaveType', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(83, 'Replicate:LeaveType', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(84, 'Reorder:LeaveType', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(85, 'ViewAny:NatureDocument', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(86, 'View:NatureDocument', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(87, 'Create:NatureDocument', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(88, 'Update:NatureDocument', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(89, 'Delete:NatureDocument', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(90, 'DeleteAny:NatureDocument', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(91, 'Restore:NatureDocument', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(92, 'ForceDelete:NatureDocument', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(93, 'ForceDeleteAny:NatureDocument', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(94, 'RestoreAny:NatureDocument', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(95, 'Replicate:NatureDocument', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(96, 'Reorder:NatureDocument', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(97, 'ViewAny:NiveauScolaire', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(98, 'View:NiveauScolaire', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(99, 'Create:NiveauScolaire', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(100, 'Update:NiveauScolaire', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(101, 'Delete:NiveauScolaire', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(102, 'DeleteAny:NiveauScolaire', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(103, 'Restore:NiveauScolaire', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(104, 'ForceDelete:NiveauScolaire', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(105, 'ForceDeleteAny:NiveauScolaire', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(106, 'RestoreAny:NiveauScolaire', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(107, 'Replicate:NiveauScolaire', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(108, 'Reorder:NiveauScolaire', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(109, 'ViewAny:Profession', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(110, 'View:Profession', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(111, 'Create:Profession', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(112, 'Update:Profession', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(113, 'Delete:Profession', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(114, 'DeleteAny:Profession', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(115, 'Restore:Profession', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(116, 'ForceDelete:Profession', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(117, 'ForceDeleteAny:Profession', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(118, 'RestoreAny:Profession', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(119, 'Replicate:Profession', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(120, 'Reorder:Profession', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(121, 'ViewAny:Transport', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(122, 'View:Transport', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(123, 'Create:Transport', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(124, 'Update:Transport', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(125, 'Delete:Transport', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(126, 'DeleteAny:Transport', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(127, 'Restore:Transport', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(128, 'ForceDelete:Transport', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(129, 'ForceDeleteAny:Transport', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(130, 'RestoreAny:Transport', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(131, 'Replicate:Transport', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(132, 'Reorder:Transport', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(133, 'ViewAny:User', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(134, 'View:User', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(135, 'Create:User', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(136, 'Update:User', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(137, 'Delete:User', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(138, 'DeleteAny:User', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(139, 'Restore:User', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(140, 'ForceDelete:User', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(141, 'ForceDeleteAny:User', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(142, 'RestoreAny:User', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(143, 'Replicate:User', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(144, 'Reorder:User', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(145, 'ViewAny:Role', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(146, 'View:Role', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(147, 'Create:Role', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(148, 'Update:Role', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(149, 'Delete:Role', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(150, 'DeleteAny:Role', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(151, 'Restore:Role', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(152, 'ForceDelete:Role', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(153, 'ForceDeleteAny:Role', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(154, 'RestoreAny:Role', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(155, 'Replicate:Role', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(156, 'Reorder:Role', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(157, 'View:Dashboard', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(158, 'View:EcoleSettings', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(159, 'View:AccountWidget', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(160, 'View:EmployeeStatsWidget', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(161, 'View:HrStatsOverview', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(162, 'View:LeavesWidget', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(163, 'ApproveLeave', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(164, 'ValidatePayroll', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(165, 'MarkPayrollPaid', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(166, 'ViewAny:AuditLog', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(167, 'View:AuditLog', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(168, 'View:MonEspace', 'web', '2026-09-10 22:38:23', '2026-09-10 22:38:23'),
(169, 'View:DocumentsAdministratifsWidget', 'web', '2026-09-11 23:54:17', '2026-09-11 23:54:17'),
(170, 'View:AutreDemandesWidget', 'web', '2026-09-11 23:54:17', '2026-09-11 23:54:17'),
(171, 'ViewAny:DocumentType', 'web', '2026-09-12 02:30:54', '2026-09-12 02:30:54'),
(172, 'View:DocumentType', 'web', '2026-09-12 02:30:54', '2026-09-12 02:30:54'),
(173, 'Create:DocumentType', 'web', '2026-09-12 02:30:54', '2026-09-12 02:30:54'),
(174, 'Update:DocumentType', 'web', '2026-09-12 02:30:54', '2026-09-12 02:30:54'),
(175, 'Delete:DocumentType', 'web', '2026-09-12 02:30:54', '2026-09-12 02:30:54'),
(176, 'DeleteAny:DocumentType', 'web', '2026-09-12 02:30:54', '2026-09-12 02:30:54'),
(177, 'Restore:DocumentType', 'web', '2026-09-12 02:30:54', '2026-09-12 02:30:54'),
(178, 'ForceDelete:DocumentType', 'web', '2026-09-12 02:30:54', '2026-09-12 02:30:54'),
(179, 'ForceDeleteAny:DocumentType', 'web', '2026-09-12 02:30:54', '2026-09-12 02:30:54'),
(180, 'RestoreAny:DocumentType', 'web', '2026-09-12 02:30:54', '2026-09-12 02:30:54'),
(181, 'Replicate:DocumentType', 'web', '2026-09-12 02:30:54', '2026-09-12 02:30:54'),
(182, 'Reorder:DocumentType', 'web', '2026-09-12 02:30:54', '2026-09-12 02:30:54'),
(183, 'ViewAny:EmployeeDocumentType', 'web', '2026-09-12 02:30:54', '2026-09-12 02:30:54'),
(184, 'View:EmployeeDocumentType', 'web', '2026-09-12 02:30:54', '2026-09-12 02:30:54'),
(185, 'Create:EmployeeDocumentType', 'web', '2026-09-12 02:30:54', '2026-09-12 02:30:54'),
(186, 'Update:EmployeeDocumentType', 'web', '2026-09-12 02:30:54', '2026-09-12 02:30:54'),
(187, 'Delete:EmployeeDocumentType', 'web', '2026-09-12 02:30:54', '2026-09-12 02:30:54'),
(188, 'DeleteAny:EmployeeDocumentType', 'web', '2026-09-12 02:30:54', '2026-09-12 02:30:54'),
(189, 'Restore:EmployeeDocumentType', 'web', '2026-09-12 02:30:54', '2026-09-12 02:30:54'),
(190, 'ForceDelete:EmployeeDocumentType', 'web', '2026-09-12 02:30:54', '2026-09-12 02:30:54'),
(191, 'ForceDeleteAny:EmployeeDocumentType', 'web', '2026-09-12 02:30:54', '2026-09-12 02:30:54'),
(192, 'RestoreAny:EmployeeDocumentType', 'web', '2026-09-12 02:30:54', '2026-09-12 02:30:54'),
(193, 'Replicate:EmployeeDocumentType', 'web', '2026-09-12 02:30:54', '2026-09-12 02:30:54'),
(194, 'Reorder:EmployeeDocumentType', 'web', '2026-09-12 02:30:54', '2026-09-12 02:30:54');

-- --------------------------------------------------------

--
-- Structure de la table `professions`
--

CREATE TABLE `professions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ecole_setting_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `professions`
--

INSERT INTO `professions` (`id`, `ecole_setting_id`, `name`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'Enseignante', '2026-09-10 22:38:23', '2026-09-10 22:38:23', NULL),
(2, 1, 'Enseignant', '2026-09-10 22:38:23', '2026-09-10 22:38:23', NULL),
(3, 1, 'Femme de ménage', '2026-09-10 22:38:23', '2026-09-10 22:38:23', NULL),
(4, 1, 'Directeur', '2026-09-10 22:38:23', '2026-09-10 22:38:23', NULL),
(5, 1, 'Chauffeur', '2026-09-10 22:38:23', '2026-09-10 22:38:23', NULL),
(6, 1, 'Gardien', '2026-09-10 22:38:23', '2026-09-10 22:38:23', NULL),
(7, 1, 'Assistante de transport', '2026-09-10 22:38:23', '2026-09-10 22:38:23', NULL),
(8, 1, 'Surveillant général', '2026-09-10 22:38:23', '2026-09-10 22:38:23', NULL),
(9, 1, 'Secrétaire', '2026-09-10 22:38:23', '2026-09-10 22:38:23', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'super-admin', 'web', '2026-09-10 22:38:22', '2026-09-10 22:38:22'),
(2, 'directeur', 'web', '2026-09-10 22:38:22', '2026-09-10 22:38:22'),
(3, 'secretaire', 'web', '2026-09-10 22:38:22', '2026-09-10 22:38:22'),
(4, 'surveillante', 'web', '2026-09-10 22:38:22', '2026-09-10 22:38:22'),
(6, 'femme-de-menage', 'web', '2026-09-11 23:31:40', '2026-09-11 23:31:40'),
(7, 'chauffeur', 'web', '2026-09-11 23:31:40', '2026-09-11 23:31:40'),
(8, 'gardien', 'web', '2026-09-11 23:31:40', '2026-09-11 23:31:40'),
(9, 'enseignant', 'web', '2026-09-11 23:35:05', '2026-09-11 23:35:05'),
(11, 'assistante-transport', 'web', '2026-09-11 23:35:05', '2026-09-11 23:35:05');

-- --------------------------------------------------------

--
-- Structure de la table `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `role_has_permissions`
--

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(1, 1),
(2, 1),
(3, 1),
(4, 1),
(5, 1),
(6, 1),
(7, 1),
(8, 1),
(9, 1),
(10, 1),
(11, 1),
(12, 1),
(13, 1),
(14, 1),
(15, 1),
(16, 1),
(17, 1),
(18, 1),
(19, 1),
(20, 1),
(21, 1),
(22, 1),
(23, 1),
(24, 1),
(25, 1),
(26, 1),
(27, 1),
(28, 1),
(29, 1),
(30, 1),
(31, 1),
(32, 1),
(33, 1),
(34, 1),
(35, 1),
(36, 1),
(37, 1),
(38, 1),
(39, 1),
(40, 1),
(41, 1),
(42, 1),
(43, 1),
(44, 1),
(45, 1),
(46, 1),
(47, 1),
(48, 1),
(49, 1),
(50, 1),
(51, 1),
(52, 1),
(53, 1),
(54, 1),
(55, 1),
(56, 1),
(57, 1),
(58, 1),
(59, 1),
(60, 1),
(61, 1),
(62, 1),
(63, 1),
(64, 1),
(65, 1),
(66, 1),
(67, 1),
(68, 1),
(69, 1),
(70, 1),
(71, 1),
(72, 1),
(73, 1),
(74, 1),
(75, 1),
(76, 1),
(77, 1),
(78, 1),
(79, 1),
(80, 1),
(81, 1),
(82, 1),
(83, 1),
(84, 1),
(85, 1),
(86, 1),
(87, 1),
(88, 1),
(89, 1),
(90, 1),
(91, 1),
(92, 1),
(93, 1),
(94, 1),
(95, 1),
(96, 1),
(97, 1),
(98, 1),
(99, 1),
(100, 1),
(101, 1),
(102, 1),
(103, 1),
(104, 1),
(105, 1),
(106, 1),
(107, 1),
(108, 1),
(109, 1),
(110, 1),
(111, 1),
(112, 1),
(113, 1),
(114, 1),
(115, 1),
(116, 1),
(117, 1),
(118, 1),
(119, 1),
(120, 1),
(121, 1),
(122, 1),
(123, 1),
(124, 1),
(125, 1),
(126, 1),
(127, 1),
(128, 1),
(129, 1),
(130, 1),
(131, 1),
(132, 1),
(133, 1),
(134, 1),
(135, 1),
(136, 1),
(137, 1),
(138, 1),
(139, 1),
(140, 1),
(141, 1),
(142, 1),
(143, 1),
(144, 1),
(145, 1),
(146, 1),
(147, 1),
(148, 1),
(149, 1),
(150, 1),
(151, 1),
(152, 1),
(153, 1),
(154, 1),
(155, 1),
(156, 1),
(157, 1),
(158, 1),
(159, 1),
(161, 1),
(162, 1),
(163, 1),
(164, 1),
(165, 1),
(169, 1),
(170, 1),
(171, 1),
(172, 1),
(173, 1),
(174, 1),
(175, 1),
(176, 1),
(177, 1),
(178, 1),
(179, 1),
(180, 1),
(181, 1),
(182, 1),
(183, 1),
(184, 1),
(185, 1),
(186, 1),
(187, 1),
(188, 1),
(189, 1),
(190, 1),
(191, 1),
(192, 1),
(193, 1),
(194, 1),
(13, 2),
(14, 2),
(15, 2),
(16, 2),
(17, 2),
(18, 2),
(37, 2),
(38, 2),
(39, 2),
(40, 2),
(41, 2),
(42, 2),
(61, 2),
(62, 2),
(63, 2),
(64, 2),
(65, 2),
(66, 2),
(73, 2),
(74, 2),
(75, 2),
(76, 2),
(77, 2),
(78, 2),
(133, 2),
(134, 2),
(135, 2),
(136, 2),
(137, 2),
(138, 2),
(157, 2),
(161, 2),
(163, 2),
(166, 2),
(167, 2),
(168, 2),
(13, 3),
(14, 3),
(15, 3),
(16, 3),
(37, 3),
(38, 3),
(39, 3),
(40, 3),
(61, 3),
(62, 3),
(63, 3),
(64, 3),
(133, 3),
(134, 3),
(135, 3),
(136, 3),
(157, 3),
(163, 3),
(168, 3),
(13, 4),
(14, 4),
(15, 4),
(16, 4),
(37, 4),
(38, 4),
(39, 4),
(40, 4),
(61, 4),
(62, 4),
(63, 4),
(64, 4),
(133, 4),
(134, 4),
(135, 4),
(136, 4),
(157, 4),
(163, 4),
(168, 4),
(13, 6),
(14, 6),
(15, 6),
(38, 6),
(40, 6),
(61, 6),
(62, 6),
(63, 6),
(157, 6),
(168, 6),
(13, 7),
(14, 7),
(15, 7),
(38, 7),
(40, 7),
(61, 7),
(62, 7),
(63, 7),
(157, 7),
(168, 7),
(13, 8),
(14, 8),
(15, 8),
(38, 8),
(40, 8),
(61, 8),
(62, 8),
(63, 8),
(157, 8),
(168, 8),
(13, 9),
(14, 9),
(15, 9),
(38, 9),
(40, 9),
(61, 9),
(62, 9),
(63, 9),
(157, 9),
(168, 9),
(13, 11),
(14, 11),
(15, 11),
(38, 11),
(40, 11),
(61, 11),
(62, 11),
(63, 11),
(157, 11),
(168, 11);

-- --------------------------------------------------------

--
-- Structure de la table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('MJOoMxy4494xiiqK8krAA7KMmnRFCXPgSusAacSn', NULL, '105.156.192.130', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiWW5lakFLREtCM1BlTWs4bGwwWXQ1aDNhYUZwZmJhbElqekRnMlVKTSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDU6Imh0dHBzOi8vcmgubGVzZWNvbGVzYWxiYXJhaW1lLmNvbS9hZG1pbi9sb2dpbiI7czo1OiJyb3V0ZSI7czoyMzoiZmlsYW1lbnQuYXBwLmF1dGgubG9naW4iO319', 1789409180),
('QD9ahQen06GKzP1dsdIJ6MXdiJNhb4gAV3Lh9ksb', NULL, '105.156.192.130', 'WhatsApp/2.23.20.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiOWloNGIxV095alFoQWxkM2FndjFQalZvOTJwWE5jZ2VLRHZHN3NNMSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDU6Imh0dHBzOi8vcmgubGVzZWNvbGVzYWxiYXJhaW1lLmNvbS9hZG1pbi9sb2dpbiI7czo1OiJyb3V0ZSI7czoyMzoiZmlsYW1lbnQuYXBwLmF1dGgubG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1789409130);

-- --------------------------------------------------------

--
-- Structure de la table `transports`
--

CREATE TABLE `transports` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ecole_setting_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `matricule` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `transports`
--

INSERT INTO `transports` (`id`, `ecole_setting_id`, `name`, `matricule`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'Transport 1', '12345/h/55', '2026-09-11 14:51:30', '2026-09-11 14:51:30', NULL),
(2, 1, 'Transport 2 ', '98765/h/55', '2026-09-11 14:51:41', '2026-09-11 14:51:41', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ecole_setting_id` bigint(20) UNSIGNED DEFAULT NULL,
  `employee_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `ecole_setting_id`, `employee_id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, NULL, NULL, 'Super Admin', 'admin@gestion-hr.ma', NULL, '$2y$12$aGvk6qrNwEclHoBp/15cl.2XibURtAD/dwwfyPqtwgRZx2SqL3VEK', NULL, '2026-09-10 22:38:22', '2026-09-10 22:38:22', NULL),
(3, 1, 64, 'ismail abounasr', 'iabounasr@lesecolesalbaraime.com', NULL, '$2y$12$XsC3isHlE0BMSUCLjFsw3OidQZUWob7eEKr6HScgoxVuWQIyeZKdu', 'pn9s3ipgx3SvSLJ2riewzCYPiZtEI3ItUf4hiFfZ1r7Lrjx0hwZf0AusbeZC', '2026-09-11 20:07:23', '2026-09-12 08:34:07', NULL),
(6, 1, 1, 'SAADIA AABIDA', 'saadia.aabida@lesecolesalbaraime.com', NULL, '$2y$12$C2HDldC.GNoBN8XSJ3e2YO1j6Tt38OcCOOot5w5O0iGfWE2PguOHK', NULL, '2026-09-12 00:41:14', '2026-09-12 00:45:52', NULL),
(7, 1, 3, 'FOUZIA ABAYAZID', 'fouzia.abayazid@lesecolesalbaraime.com', NULL, '$2y$12$C2HDldC.GNoBN8XSJ3e2YO1j6Tt38OcCOOot5w5O0iGfWE2PguOHK', NULL, '2026-09-12 00:41:14', '2026-09-12 00:45:52', NULL),
(8, 1, 4, 'TOURIA ABID', 'touria.abid@lesecolesalbaraime.com', NULL, '$2y$12$C2HDldC.GNoBN8XSJ3e2YO1j6Tt38OcCOOot5w5O0iGfWE2PguOHK', NULL, '2026-09-12 00:41:14', '2026-09-12 00:45:52', NULL),
(9, 1, 5, 'LALLA AMINA AIT SIDI HOUMAD', 'amina.aitsidihoumad@lesecolesalbaraime.com', NULL, '$2y$12$C2HDldC.GNoBN8XSJ3e2YO1j6Tt38OcCOOot5w5O0iGfWE2PguOHK', NULL, '2026-09-12 00:41:14', '2026-09-12 00:45:52', NULL),
(10, 1, 6, 'HICHAM AITBAHCINE', 'hicham.aitbahcine@lesecolesalbaraime.com', NULL, '$2y$12$C2HDldC.GNoBN8XSJ3e2YO1j6Tt38OcCOOot5w5O0iGfWE2PguOHK', NULL, '2026-09-12 00:41:14', '2026-09-12 00:45:52', NULL),
(11, 1, 7, 'HOURIA AKBIL', 'houria.akbil@lesecolesalbaraime.com', NULL, '$2y$12$C2HDldC.GNoBN8XSJ3e2YO1j6Tt38OcCOOot5w5O0iGfWE2PguOHK', NULL, '2026-09-12 00:41:14', '2026-09-12 00:45:52', NULL),
(12, 1, 8, 'MUSTAPHA AL KIHAL', 'mustapha.alkihal@lesecolesalbaraime.com', NULL, '$2y$12$C2HDldC.GNoBN8XSJ3e2YO1j6Tt38OcCOOot5w5O0iGfWE2PguOHK', NULL, '2026-09-12 00:41:14', '2026-09-12 00:45:52', NULL),
(13, 1, 9, 'SARA AL MIAADI', 'sara.almiaadi@lesecolesalbaraime.com', NULL, '$2y$12$C2HDldC.GNoBN8XSJ3e2YO1j6Tt38OcCOOot5w5O0iGfWE2PguOHK', NULL, '2026-09-12 00:41:14', '2026-09-12 00:45:52', NULL),
(14, 1, 10, 'YOUSSEF ARZAYNE', 'arzine.youssef@lesecolesalbaraime.com', NULL, '$2y$12$C2HDldC.GNoBN8XSJ3e2YO1j6Tt38OcCOOot5w5O0iGfWE2PguOHK', NULL, '2026-09-12 00:41:14', '2026-09-12 00:45:52', NULL),
(15, 1, 11, 'TIBARIA BAHOUJ', 'tibaria.bahouj@lesecolesalbaraime.com', NULL, '$2y$12$C2HDldC.GNoBN8XSJ3e2YO1j6Tt38OcCOOot5w5O0iGfWE2PguOHK', NULL, '2026-09-12 00:41:14', '2026-09-12 00:45:52', NULL),
(16, 1, 12, 'NAWAL BELGHAOUTI', 'nawal.belghaouti@lesecolesalbaraime.com', NULL, '$2y$12$C2HDldC.GNoBN8XSJ3e2YO1j6Tt38OcCOOot5w5O0iGfWE2PguOHK', NULL, '2026-09-12 00:41:14', '2026-09-12 00:45:52', NULL),
(17, 1, 13, 'AMINA BERHO', 'amina.berho@lesecolesalbaraime.com', NULL, '$2y$12$C2HDldC.GNoBN8XSJ3e2YO1j6Tt38OcCOOot5w5O0iGfWE2PguOHK', NULL, '2026-09-12 00:41:14', '2026-09-12 00:45:52', NULL),
(18, 1, 14, 'NOUREDDINE BERTAL', 'noureddine.bertal@lesecolesalbaraime.com', NULL, '$2y$12$C2HDldC.GNoBN8XSJ3e2YO1j6Tt38OcCOOot5w5O0iGfWE2PguOHK', NULL, '2026-09-12 00:41:14', '2026-09-12 00:45:52', NULL),
(19, 1, 15, 'NOUHAILA BIGIGUEN', 'nouhaila.bigiguen@lesecolesalbaraime.com', NULL, '$2y$12$C2HDldC.GNoBN8XSJ3e2YO1j6Tt38OcCOOot5w5O0iGfWE2PguOHK', NULL, '2026-09-12 00:41:14', '2026-09-12 00:45:52', NULL),
(20, 1, 16, 'OMAR BOUAACHA', 'omar.bouaacha@lesecolesalbaraime.com', NULL, '$2y$12$C2HDldC.GNoBN8XSJ3e2YO1j6Tt38OcCOOot5w5O0iGfWE2PguOHK', NULL, '2026-09-12 00:41:14', '2026-09-12 00:45:52', NULL),
(21, 1, 17, 'HAJIBA BOUCHAKOUR', 'hajiba.bouchakour@lesecolesalbaraime.com', NULL, '$2y$12$C2HDldC.GNoBN8XSJ3e2YO1j6Tt38OcCOOot5w5O0iGfWE2PguOHK', NULL, '2026-09-12 00:41:14', '2026-09-12 00:45:52', NULL),
(22, 1, 18, 'MUSTAPHA CHAKIR', 'mustapha.chakir@lesecolesalbaraime.com', NULL, '$2y$12$C2HDldC.GNoBN8XSJ3e2YO1j6Tt38OcCOOot5w5O0iGfWE2PguOHK', NULL, '2026-09-12 00:41:14', '2026-09-12 00:45:52', NULL),
(23, 1, 19, 'ABDERRAHMANE CHAQROUN', 'a.chagroun@lesecolesalbaraime.com', NULL, '$2y$12$C2HDldC.GNoBN8XSJ3e2YO1j6Tt38OcCOOot5w5O0iGfWE2PguOHK', NULL, '2026-09-12 00:41:14', '2026-09-12 00:45:52', NULL),
(24, 1, 20, 'HAROUNE DARROUJI', 'harroune.darrouji@lesecolesalbaraime.com', NULL, '$2y$12$C2HDldC.GNoBN8XSJ3e2YO1j6Tt38OcCOOot5w5O0iGfWE2PguOHK', NULL, '2026-09-12 00:41:14', '2026-09-12 00:45:52', NULL),
(25, 1, 21, 'MOHAMMED EDDAHI', 'm.eddahi@lesecolesalbaraime.com', NULL, '$2y$12$C2HDldC.GNoBN8XSJ3e2YO1j6Tt38OcCOOot5w5O0iGfWE2PguOHK', NULL, '2026-09-12 00:41:14', '2026-09-12 00:45:52', NULL),
(26, 1, 22, 'CHAIMAA EDDOHAOUI', 'c.eddouhaoui@lesecolesalbaraime.com', NULL, '$2y$12$C2HDldC.GNoBN8XSJ3e2YO1j6Tt38OcCOOot5w5O0iGfWE2PguOHK', NULL, '2026-09-12 00:41:14', '2026-09-12 00:45:52', NULL),
(27, 1, 23, 'ASMAE EL AROUA', 'a.elaroua@lesecolesalbaraime.com', NULL, '$2y$12$C2HDldC.GNoBN8XSJ3e2YO1j6Tt38OcCOOot5w5O0iGfWE2PguOHK', NULL, '2026-09-12 00:41:14', '2026-09-12 00:45:52', NULL),
(28, 1, 24, 'MINA EL JAD', 'mina.eljad@lesecolesalbaraime.com', NULL, '$2y$12$C2HDldC.GNoBN8XSJ3e2YO1j6Tt38OcCOOot5w5O0iGfWE2PguOHK', NULL, '2026-09-12 00:41:14', '2026-09-12 00:45:52', NULL),
(29, 1, 25, 'RACHIDA EL KHATIRI', 'rachida.elkhatiri@lesecolesalbaraime.com', NULL, '$2y$12$C2HDldC.GNoBN8XSJ3e2YO1j6Tt38OcCOOot5w5O0iGfWE2PguOHK', NULL, '2026-09-12 00:41:14', '2026-09-12 00:45:52', NULL),
(30, 1, 26, 'HASSAN EL MASSOUDI', 'h.elmassoudi@lesecolesalbaraime.com', NULL, '$2y$12$C2HDldC.GNoBN8XSJ3e2YO1j6Tt38OcCOOot5w5O0iGfWE2PguOHK', NULL, '2026-09-12 00:41:14', '2026-09-12 00:45:52', NULL),
(31, 1, 27, 'SALMA ELKADIOUI EL IDRISSI', 'salma.elkadiouielidrissi@lesecolesalbaraime.com', NULL, '$2y$12$C2HDldC.GNoBN8XSJ3e2YO1j6Tt38OcCOOot5w5O0iGfWE2PguOHK', NULL, '2026-09-12 00:41:14', '2026-09-12 00:45:52', NULL),
(32, 1, 28, 'HASNA ELMAHFOUDHY', 'hasna.elmahfoudhy@lesecolesalbaraime.com', NULL, '$2y$12$C2HDldC.GNoBN8XSJ3e2YO1j6Tt38OcCOOot5w5O0iGfWE2PguOHK', NULL, '2026-09-12 00:41:14', '2026-09-12 00:45:52', NULL),
(33, 1, 29, 'OMAR ER RAMMAL', 'omar.errammal@lesecolesalbaraime.com', NULL, '$2y$12$C2HDldC.GNoBN8XSJ3e2YO1j6Tt38OcCOOot5w5O0iGfWE2PguOHK', NULL, '2026-09-12 00:41:14', '2026-09-12 00:45:52', NULL),
(34, 1, 30, 'NAJAT ER-RBAHY', 'n.errbahy@lesecolesalbaraime.com', NULL, '$2y$12$C2HDldC.GNoBN8XSJ3e2YO1j6Tt38OcCOOot5w5O0iGfWE2PguOHK', NULL, '2026-09-12 00:41:14', '2026-09-12 00:45:52', NULL),
(35, 1, 31, 'SOUKAINA ESSALAMA', 's.essalama@lesecolesalbaraime.com', NULL, '$2y$12$C2HDldC.GNoBN8XSJ3e2YO1j6Tt38OcCOOot5w5O0iGfWE2PguOHK', NULL, '2026-09-12 00:41:14', '2026-09-12 00:45:52', NULL),
(36, 1, 32, 'FATNA ETTOUMI', 'fatna.ettoumi@lesecolesalbaraime.com', NULL, '$2y$12$C2HDldC.GNoBN8XSJ3e2YO1j6Tt38OcCOOot5w5O0iGfWE2PguOHK', NULL, '2026-09-12 00:41:14', '2026-09-12 00:45:52', NULL),
(37, 1, 33, 'AMINA FARIS', 'amina.faris@lesecolesalbaraime.com', NULL, '$2y$12$C2HDldC.GNoBN8XSJ3e2YO1j6Tt38OcCOOot5w5O0iGfWE2PguOHK', NULL, '2026-09-12 00:41:14', '2026-09-12 00:45:52', NULL),
(38, 1, 34, 'SAIDA FIKRI', 'saida.fikri@lesecolesalbaraime.com', NULL, '$2y$12$C2HDldC.GNoBN8XSJ3e2YO1j6Tt38OcCOOot5w5O0iGfWE2PguOHK', NULL, '2026-09-12 00:41:14', '2026-09-12 00:45:52', NULL),
(39, 1, 35, 'AMINE FIRAR', 'a.firar@lesecolesalbaraime.com', NULL, '$2y$12$C2HDldC.GNoBN8XSJ3e2YO1j6Tt38OcCOOot5w5O0iGfWE2PguOHK', NULL, '2026-09-12 00:41:14', '2026-09-12 00:45:52', NULL),
(40, 1, 36, 'ABDERRAHIM GUEZRI', 'a.guezri@lesecolesalbaraime.com', NULL, '$2y$12$C2HDldC.GNoBN8XSJ3e2YO1j6Tt38OcCOOot5w5O0iGfWE2PguOHK', NULL, '2026-09-12 00:41:14', '2026-09-12 00:45:52', NULL),
(41, 1, 37, 'MOHAMMED HAMAMAT', 'mohammed.hamamat@lesecolesalbaraime.com', NULL, '$2y$12$cbCuSgv2ycZnJRw2PGtJp.Dw/FvjshZfa4cFw0kNDu1GcQ2yQSuuG', NULL, '2026-09-12 00:41:14', '2026-09-14 16:00:45', NULL),
(42, 1, 38, 'MOHAMED HAMDOUNE', 'm.hamdoune@lesecolesalbaraime.com', NULL, '$2y$12$C2HDldC.GNoBN8XSJ3e2YO1j6Tt38OcCOOot5w5O0iGfWE2PguOHK', NULL, '2026-09-12 00:41:14', '2026-09-12 00:45:52', NULL),
(43, 1, 39, 'AZIZA HAMIDI', 'aziza.hamidi@lesecolesalbaraime.com', NULL, '$2y$12$C2HDldC.GNoBN8XSJ3e2YO1j6Tt38OcCOOot5w5O0iGfWE2PguOHK', NULL, '2026-09-12 00:41:14', '2026-09-12 00:45:52', NULL),
(44, 1, 40, 'HAKIMA HAMMOUR', 'h.hammour@lesecolesalbaraime.com', NULL, '$2y$12$C2HDldC.GNoBN8XSJ3e2YO1j6Tt38OcCOOot5w5O0iGfWE2PguOHK', NULL, '2026-09-12 00:41:14', '2026-09-12 00:45:52', NULL),
(45, 1, 41, 'BOUCHAIB HASNAOUI', 'b.hasnaoui@lesecolesalbaraime.com', NULL, '$2y$12$C2HDldC.GNoBN8XSJ3e2YO1j6Tt38OcCOOot5w5O0iGfWE2PguOHK', NULL, '2026-09-12 00:41:14', '2026-09-12 00:45:52', NULL),
(46, 1, 42, 'CHOUAIB HRAIMACH', 'c.hraimach@lesecolesalbaraime.com', NULL, '$2y$12$C2HDldC.GNoBN8XSJ3e2YO1j6Tt38OcCOOot5w5O0iGfWE2PguOHK', NULL, '2026-09-12 00:41:14', '2026-09-12 00:45:52', NULL),
(47, 1, 43, 'BOUCHRA JEDAA', 'bouchra.jedaa@lesecolesalbaraime.com', NULL, '$2y$12$C2HDldC.GNoBN8XSJ3e2YO1j6Tt38OcCOOot5w5O0iGfWE2PguOHK', NULL, '2026-09-12 00:41:14', '2026-09-12 00:45:52', NULL),
(48, 1, 44, 'SOUMIA KABRITI', 'soumia.kabriti@lesecolesalbaraime.com', NULL, '$2y$12$C2HDldC.GNoBN8XSJ3e2YO1j6Tt38OcCOOot5w5O0iGfWE2PguOHK', NULL, '2026-09-12 00:41:14', '2026-09-12 00:45:52', NULL),
(49, 1, 45, 'ZINEB KARIMA', 'z.karima@lesecolesalbaraime.com', NULL, '$2y$12$C2HDldC.GNoBN8XSJ3e2YO1j6Tt38OcCOOot5w5O0iGfWE2PguOHK', NULL, '2026-09-12 00:41:15', '2026-09-12 00:45:52', NULL),
(50, 1, 46, 'FATIMA.ZAHRA KHOLTI', 'f.kholti@lesecolesalbaraime.com', NULL, '$2y$12$C2HDldC.GNoBN8XSJ3e2YO1j6Tt38OcCOOot5w5O0iGfWE2PguOHK', NULL, '2026-09-12 00:41:15', '2026-09-12 00:45:52', NULL),
(51, 1, 47, 'AHLAM LAAROUA', 'ahlam.laaroua.zekkar@lesecolesalbaraime.com', NULL, '$2y$12$C2HDldC.GNoBN8XSJ3e2YO1j6Tt38OcCOOot5w5O0iGfWE2PguOHK', NULL, '2026-09-12 00:41:15', '2026-09-12 00:45:52', NULL),
(52, 1, 48, 'M\'hamed LAHMAR', 'm.lahmar@lesecolesalbaraime.com', NULL, '$2y$12$C2HDldC.GNoBN8XSJ3e2YO1j6Tt38OcCOOot5w5O0iGfWE2PguOHK', NULL, '2026-09-12 00:41:15', '2026-09-12 00:45:52', NULL),
(53, 1, 49, 'MOAD LOBANI', 'm.lobani@lesecolesalbaraime.com', NULL, '$2y$12$C2HDldC.GNoBN8XSJ3e2YO1j6Tt38OcCOOot5w5O0iGfWE2PguOHK', NULL, '2026-09-12 00:41:15', '2026-09-12 00:45:52', NULL),
(54, 1, 50, 'ESSEDIYA LOUDINI', 'essediya.loudini@lesecolesalbaraime.com', NULL, '$2y$12$C2HDldC.GNoBN8XSJ3e2YO1j6Tt38OcCOOot5w5O0iGfWE2PguOHK', NULL, '2026-09-12 00:41:15', '2026-09-12 00:45:52', NULL),
(55, 1, 51, 'MOHAMED MAKSAOUI', 'm.maksaoui@lesecolesalbaraime.com', NULL, '$2y$12$C2HDldC.GNoBN8XSJ3e2YO1j6Tt38OcCOOot5w5O0iGfWE2PguOHK', NULL, '2026-09-12 00:41:15', '2026-09-12 00:45:52', NULL),
(56, 1, 52, 'KHADIJA MENAOUAR', 'k.menaouar@lesecolesalbaraime.com', NULL, '$2y$12$C2HDldC.GNoBN8XSJ3e2YO1j6Tt38OcCOOot5w5O0iGfWE2PguOHK', NULL, '2026-09-12 00:41:15', '2026-09-12 00:45:52', NULL),
(57, 1, 53, 'SAID MESSAOU', 's.massaou@lesecolesalbaraime.com', NULL, '$2y$12$C2HDldC.GNoBN8XSJ3e2YO1j6Tt38OcCOOot5w5O0iGfWE2PguOHK', NULL, '2026-09-12 00:41:15', '2026-09-12 00:45:52', NULL),
(58, 1, 54, 'HALIMA MOKTASSID', 'h.moktassid@lesecolesalbaraime.com', NULL, '$2y$12$C2HDldC.GNoBN8XSJ3e2YO1j6Tt38OcCOOot5w5O0iGfWE2PguOHK', NULL, '2026-09-12 00:41:15', '2026-09-12 00:45:52', NULL),
(59, 1, 55, 'ZOUBIDA MOUJANE', 'zoubida.moujane@lesecolesalbaraime.com', NULL, '$2y$12$C2HDldC.GNoBN8XSJ3e2YO1j6Tt38OcCOOot5w5O0iGfWE2PguOHK', NULL, '2026-09-12 00:41:15', '2026-09-12 00:45:52', NULL),
(60, 1, 56, 'HASNA RAMI', 'hasna.rami@lesecolesalbaraime.com', NULL, '$2y$12$C2HDldC.GNoBN8XSJ3e2YO1j6Tt38OcCOOot5w5O0iGfWE2PguOHK', NULL, '2026-09-12 00:41:15', '2026-09-12 00:45:52', NULL),
(61, 1, 57, 'MOUNIR SARIH', 'm.sarih@lesecolesalbaraime.com', NULL, '$2y$12$C2HDldC.GNoBN8XSJ3e2YO1j6Tt38OcCOOot5w5O0iGfWE2PguOHK', NULL, '2026-09-12 00:41:15', '2026-09-12 00:45:52', NULL),
(62, 1, 58, 'HANANE TABTI', 'tabti.hanane@lesecolesalbaraime.com', NULL, '$2y$12$C2HDldC.GNoBN8XSJ3e2YO1j6Tt38OcCOOot5w5O0iGfWE2PguOHK', NULL, '2026-09-12 00:41:15', '2026-09-12 00:45:52', NULL),
(63, 1, 59, 'AMINA TUIRTOU', 'touirtouamina@gmail.com', NULL, '$2y$12$C2HDldC.GNoBN8XSJ3e2YO1j6Tt38OcCOOot5w5O0iGfWE2PguOHK', NULL, '2026-09-12 00:41:15', '2026-09-12 00:45:52', NULL),
(64, 1, 60, 'KHADIJA ZAIDOUH', 'khadija.zaidouh@lesecolesalbaraime.com', NULL, '$2y$12$C2HDldC.GNoBN8XSJ3e2YO1j6Tt38OcCOOot5w5O0iGfWE2PguOHK', NULL, '2026-09-12 00:41:15', '2026-09-12 00:45:52', NULL),
(65, 1, 61, 'SANAA ZAKKAR', 'sanaa.zekkar@lesecolesalbaraime.com', NULL, '$2y$12$C2HDldC.GNoBN8XSJ3e2YO1j6Tt38OcCOOot5w5O0iGfWE2PguOHK', NULL, '2026-09-12 00:41:15', '2026-09-12 00:45:52', NULL),
(66, 1, 62, 'FATIMA-EZAHRA ZAMZMI', 'f.zamzmi@lesecolesalbaraime.com', NULL, '$2y$12$C2HDldC.GNoBN8XSJ3e2YO1j6Tt38OcCOOot5w5O0iGfWE2PguOHK', NULL, '2026-09-12 00:41:15', '2026-09-12 00:45:52', NULL),
(67, 1, 63, 'EL KEBIRA ZEROUALI', 'elkebira.zerouali@lesecolesalbaraime.com', NULL, '$2y$12$C2HDldC.GNoBN8XSJ3e2YO1j6Tt38OcCOOot5w5O0iGfWE2PguOHK', NULL, '2026-09-12 00:41:15', '2026-09-12 00:45:52', NULL),
(68, 1, 2, 'HASNAA ABASSI', 'hasnaa.abassi@lesecolesalbaraime.com', NULL, '$2y$12$C2HDldC.GNoBN8XSJ3e2YO1j6Tt38OcCOOot5w5O0iGfWE2PguOHK', NULL, '2026-09-12 00:45:52', '2026-09-12 00:45:52', NULL);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `activity_log`
--
ALTER TABLE `activity_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subject` (`subject_type`,`subject_id`),
  ADD KEY `causer` (`causer_type`,`causer_id`),
  ADD KEY `activity_log_log_name_index` (`log_name`);

--
-- Index pour la table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Index pour la table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Index pour la table `communication_methods`
--
ALTER TABLE `communication_methods`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `communication_methods_code_unique` (`code`),
  ADD KEY `communication_methods_ecole_setting_id_foreign` (`ecole_setting_id`);

--
-- Index pour la table `document_requests`
--
ALTER TABLE `document_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `document_requests_ecole_setting_id_foreign` (`ecole_setting_id`),
  ADD KEY `document_requests_employee_id_foreign` (`employee_id`),
  ADD KEY `document_requests_processed_by_foreign` (`processed_by`);

--
-- Index pour la table `document_types`
--
ALTER TABLE `document_types`
  ADD PRIMARY KEY (`id`),
  ADD KEY `document_types_ecole_setting_id_foreign` (`ecole_setting_id`);

--
-- Index pour la table `ecole_settings`
--
ALTER TABLE `ecole_settings`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `employees_uuid_unique` (`uuid`),
  ADD KEY `employees_ecole_setting_id_foreign` (`ecole_setting_id`),
  ADD KEY `employees_profession_id_foreign` (`profession_id`),
  ADD KEY `employees_transport_id_foreign` (`transport_id`);

--
-- Index pour la table `employee_accidents`
--
ALTER TABLE `employee_accidents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_accidents_ecole_setting_id_foreign` (`ecole_setting_id`),
  ADD KEY `employee_accidents_employee_id_foreign` (`employee_id`);

--
-- Index pour la table `employee_credits`
--
ALTER TABLE `employee_credits`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_credits_ecole_setting_id_foreign` (`ecole_setting_id`),
  ADD KEY `employee_credits_employee_id_foreign` (`employee_id`);

--
-- Index pour la table `employee_documents`
--
ALTER TABLE `employee_documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_documents_ecole_setting_id_foreign` (`ecole_setting_id`),
  ADD KEY `employee_documents_employee_id_foreign` (`employee_id`),
  ADD KEY `employee_documents_uploaded_by_foreign` (`uploaded_by`);

--
-- Index pour la table `employee_document_types`
--
ALTER TABLE `employee_document_types`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_document_types_ecole_setting_id_foreign` (`ecole_setting_id`);

--
-- Index pour la table `employee_fondation_m6`
--
ALTER TABLE `employee_fondation_m6`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_fondation_m6_ecole_setting_id_foreign` (`ecole_setting_id`),
  ADD KEY `employee_fondation_m6_employee_id_foreign` (`employee_id`);

--
-- Index pour la table `employee_groupe`
--
ALTER TABLE `employee_groupe`
  ADD PRIMARY KEY (`employee_id`,`groupe_id`),
  ADD KEY `employee_groupe_groupe_id_foreign` (`groupe_id`);

--
-- Index pour la table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Index pour la table `groupes`
--
ALTER TABLE `groupes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `groupes_ecole_setting_id_foreign` (`ecole_setting_id`),
  ADD KEY `groupes_niveau_scolaire_id_foreign` (`niveau_scolaire_id`);

--
-- Index pour la table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Index pour la table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `leaves`
--
ALTER TABLE `leaves`
  ADD PRIMARY KEY (`id`),
  ADD KEY `leaves_ecole_setting_id_foreign` (`ecole_setting_id`),
  ADD KEY `leaves_employee_id_foreign` (`employee_id`),
  ADD KEY `leaves_leave_type_id_foreign` (`leave_type_id`),
  ADD KEY `leaves_approved_by_foreign` (`approved_by`),
  ADD KEY `leaves_remplacant_id_foreign` (`remplacant_id`);

--
-- Index pour la table `leave_types`
--
ALTER TABLE `leave_types`
  ADD PRIMARY KEY (`id`),
  ADD KEY `leave_types_ecole_setting_id_foreign` (`ecole_setting_id`);

--
-- Index pour la table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Index pour la table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Index pour la table `nature_documents`
--
ALTER TABLE `nature_documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `nature_documents_ecole_setting_id_index` (`ecole_setting_id`);

--
-- Index pour la table `niveaux_scolaires`
--
ALTER TABLE `niveaux_scolaires`
  ADD PRIMARY KEY (`id`),
  ADD KEY `niveaux_scolaires_ecole_setting_id_foreign` (`ecole_setting_id`);

--
-- Index pour la table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Index pour la table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Index pour la table `professions`
--
ALTER TABLE `professions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `professions_ecole_setting_id_foreign` (`ecole_setting_id`);

--
-- Index pour la table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Index pour la table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Index pour la table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Index pour la table `transports`
--
ALTER TABLE `transports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transports_ecole_setting_id_foreign` (`ecole_setting_id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_ecole_setting_id_foreign` (`ecole_setting_id`),
  ADD KEY `users_employee_id_foreign` (`employee_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `communication_methods`
--
ALTER TABLE `communication_methods`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `document_requests`
--
ALTER TABLE `document_requests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `document_types`
--
ALTER TABLE `document_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT pour la table `ecole_settings`
--
ALTER TABLE `ecole_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT pour la table `employee_accidents`
--
ALTER TABLE `employee_accidents`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `employee_credits`
--
ALTER TABLE `employee_credits`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `employee_documents`
--
ALTER TABLE `employee_documents`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `employee_document_types`
--
ALTER TABLE `employee_document_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `employee_fondation_m6`
--
ALTER TABLE `employee_fondation_m6`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `groupes`
--
ALTER TABLE `groupes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT pour la table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `leaves`
--
ALTER TABLE `leaves`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `leave_types`
--
ALTER TABLE `leave_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT pour la table `nature_documents`
--
ALTER TABLE `nature_documents`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `niveaux_scolaires`
--
ALTER TABLE `niveaux_scolaires`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=195;

--
-- AUTO_INCREMENT pour la table `professions`
--
ALTER TABLE `professions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT pour la table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT pour la table `transports`
--
ALTER TABLE `transports`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `communication_methods`
--
ALTER TABLE `communication_methods`
  ADD CONSTRAINT `communication_methods_ecole_setting_id_foreign` FOREIGN KEY (`ecole_setting_id`) REFERENCES `ecole_settings` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `document_requests`
--
ALTER TABLE `document_requests`
  ADD CONSTRAINT `document_requests_ecole_setting_id_foreign` FOREIGN KEY (`ecole_setting_id`) REFERENCES `ecole_settings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `document_requests_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `document_requests_processed_by_foreign` FOREIGN KEY (`processed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `document_types`
--
ALTER TABLE `document_types`
  ADD CONSTRAINT `document_types_ecole_setting_id_foreign` FOREIGN KEY (`ecole_setting_id`) REFERENCES `ecole_settings` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `employees`
--
ALTER TABLE `employees`
  ADD CONSTRAINT `employees_ecole_setting_id_foreign` FOREIGN KEY (`ecole_setting_id`) REFERENCES `ecole_settings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `employees_profession_id_foreign` FOREIGN KEY (`profession_id`) REFERENCES `professions` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `employees_transport_id_foreign` FOREIGN KEY (`transport_id`) REFERENCES `transports` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `employee_accidents`
--
ALTER TABLE `employee_accidents`
  ADD CONSTRAINT `employee_accidents_ecole_setting_id_foreign` FOREIGN KEY (`ecole_setting_id`) REFERENCES `ecole_settings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `employee_accidents_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `employee_credits`
--
ALTER TABLE `employee_credits`
  ADD CONSTRAINT `employee_credits_ecole_setting_id_foreign` FOREIGN KEY (`ecole_setting_id`) REFERENCES `ecole_settings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `employee_credits_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `employee_documents`
--
ALTER TABLE `employee_documents`
  ADD CONSTRAINT `employee_documents_ecole_setting_id_foreign` FOREIGN KEY (`ecole_setting_id`) REFERENCES `ecole_settings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `employee_documents_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `employee_documents_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `employee_document_types`
--
ALTER TABLE `employee_document_types`
  ADD CONSTRAINT `employee_document_types_ecole_setting_id_foreign` FOREIGN KEY (`ecole_setting_id`) REFERENCES `ecole_settings` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `employee_fondation_m6`
--
ALTER TABLE `employee_fondation_m6`
  ADD CONSTRAINT `employee_fondation_m6_ecole_setting_id_foreign` FOREIGN KEY (`ecole_setting_id`) REFERENCES `ecole_settings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `employee_fondation_m6_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `employee_groupe`
--
ALTER TABLE `employee_groupe`
  ADD CONSTRAINT `employee_groupe_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `employee_groupe_groupe_id_foreign` FOREIGN KEY (`groupe_id`) REFERENCES `groupes` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `groupes`
--
ALTER TABLE `groupes`
  ADD CONSTRAINT `groupes_ecole_setting_id_foreign` FOREIGN KEY (`ecole_setting_id`) REFERENCES `ecole_settings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `groupes_niveau_scolaire_id_foreign` FOREIGN KEY (`niveau_scolaire_id`) REFERENCES `niveaux_scolaires` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `leaves`
--
ALTER TABLE `leaves`
  ADD CONSTRAINT `leaves_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `leaves_ecole_setting_id_foreign` FOREIGN KEY (`ecole_setting_id`) REFERENCES `ecole_settings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `leaves_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `leaves_leave_type_id_foreign` FOREIGN KEY (`leave_type_id`) REFERENCES `leave_types` (`id`),
  ADD CONSTRAINT `leaves_remplacant_id_foreign` FOREIGN KEY (`remplacant_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `leave_types`
--
ALTER TABLE `leave_types`
  ADD CONSTRAINT `leave_types_ecole_setting_id_foreign` FOREIGN KEY (`ecole_setting_id`) REFERENCES `ecole_settings` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `nature_documents`
--
ALTER TABLE `nature_documents`
  ADD CONSTRAINT `nature_documents_ecole_setting_id_foreign` FOREIGN KEY (`ecole_setting_id`) REFERENCES `ecole_settings` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `niveaux_scolaires`
--
ALTER TABLE `niveaux_scolaires`
  ADD CONSTRAINT `niveaux_scolaires_ecole_setting_id_foreign` FOREIGN KEY (`ecole_setting_id`) REFERENCES `ecole_settings` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `professions`
--
ALTER TABLE `professions`
  ADD CONSTRAINT `professions_ecole_setting_id_foreign` FOREIGN KEY (`ecole_setting_id`) REFERENCES `ecole_settings` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `transports`
--
ALTER TABLE `transports`
  ADD CONSTRAINT `transports_ecole_setting_id_foreign` FOREIGN KEY (`ecole_setting_id`) REFERENCES `ecole_settings` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ecole_setting_id_foreign` FOREIGN KEY (`ecole_setting_id`) REFERENCES `ecole_settings` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `users_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
