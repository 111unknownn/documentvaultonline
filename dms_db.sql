-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 10, 2024 at 03:30 PM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dms_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `document`
--

CREATE TABLE `document` (
  `document_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `author` varchar(100) NOT NULL,
  `category` varchar(100) NOT NULL,
  `tags` varchar(100) NOT NULL,
  `file_size` varchar(100) NOT NULL,
  `file_path` varchar(100) NOT NULL,
  `user_id` int(11) NOT NULL,
  `version` int(11) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `folder_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `document`
--

INSERT INTO `document` (`document_id`, `title`, `author`, `category`, `tags`, `file_size`, `file_path`, `user_id`, `version`, `created_at`, `folder_id`) VALUES
(109, 'Certificate', 'Raffaela', '.docx', 'resume', '87889', 'c58578da8d02e57ee5600a73785928e0390cc9ec.docx', 209, 0, '2024-02-04 01:02:06', NULL),
(110, 'Certificate_v1', 'Raffaela', '.docx', 'resume', '87889', 'c58578da8d02e57ee5600a73785928e0390cc9ec.docx', 209, 1, '2024-02-04 01:03:15', NULL),
(111, 'Certificate_v1', 'Raffaela', '.docx', 'resume', '303882', '6efef944f582e9edb326e98db476943d5d5f44eb.docx', 209, 1, '2024-02-04 01:03:44', NULL),
(112, 'Certificate_v2', 'Raffaela', '.docx', 'docs', '303882', '6efef944f582e9edb326e98db476943d5d5f44eb.docx', 209, 2, '2024-02-04 01:06:21', NULL),
(113, 'Certificate_v3', 'Marl', '.docx', 'txt', '303882', '6efef944f582e9edb326e98db476943d5d5f44eb.docx', 209, 3, '2024-02-04 01:06:40', NULL),
(114, 'Certificate_v4', 'Raffaela', '.docx', 'resume', '87889', 'c58578da8d02e57ee5600a73785928e0390cc9ec.docx', 209, 4, '2024-02-04 18:06:56', NULL),
(115, 'Certificate_v5', 'Raffaela', '.docx', 'resume', '87889', 'c58578da8d02e57ee5600a73785928e0390cc9ec.docx', 209, 5, '2024-02-04 18:07:33', NULL),
(116, 'Certificate_v6', 'Raffaela', '.docx', 'resume', '303882', '6efef944f582e9edb326e98db476943d5d5f44eb.docx', 209, 6, '2024-02-04 18:09:11', NULL),
(117, 'Certificate_v7', 'Raffaela', '.docx', 'resume', '303882', '6efef944f582e9edb326e98db476943d5d5f44eb.docx', 209, 7, '2024-02-05 00:09:55', NULL),
(120, 'Certificate_v10', 'Raffaela', '.docx', 'resume', '87889', 'c58578da8d02e57ee5600a73785928e0390cc9ec.docx', 312, 1, '2024-02-06 12:08:42', NULL),
(121, 'Certificate_v11', 'Alyssa RAs', '.docx', 'resume', '87889', 'c58578da8d02e57ee5600a73785928e0390cc9ec.docx', 312, 1, '2024-02-06 12:10:20', NULL),
(122, 'Certificate_v12', 'Raffaela', '.docx', 'docs', '87889', 'c58578da8d02e57ee5600a73785928e0390cc9ec.docx', 312, 1, '2024-02-06 12:12:09', NULL),
(123, 'Application_v1', 'Marl', '.docx', 'docs', '87889', 'c58578da8d02e57ee5600a73785928e0390cc9ec.docx', 209, 1, '2024-02-06 12:18:06', NULL),
(124, 'File_v1', 'Marl', '.pdf', 'docs', '226674', '7976e997ccbd48ed89f0842639bfe7a18098f538.pdf', 312, 1, '2024-02-06 15:53:45', NULL),
(125, 'consent_v1', 'Marl', '.docx', 'docs', '15104', '01b5cd6aaa3cd7b135d5dce533345c8a5d63c68c.docx', 309, 1, '2024-02-29 07:19:12', NULL),
(126, 'Application_v2', 'Raffaela', '.docx', 'resume', '1716584', '26edcff304c159bbe23c53b23cf9e3a10193e07f.docx', 312, 1, '2024-03-23 06:43:27', NULL),
(127, 'Certificate_v13', 'Raffaela', '.docx', 'resume', '1716584', '26edcff304c159bbe23c53b23cf9e3a10193e07f.docx', 312, 1, '2024-03-23 06:48:40', NULL),
(128, 'Certificate_v14', 'Raffaela', '.docx', 'docs', '11950', '28dd3cd57237fbde94e9198e8436c2d17f8e30ba.docx', 312, 1, '2024-03-23 06:50:30', NULL),
(129, 'Certificates_v1', 'Raffaela', '.docx', 'docs', '1716584', '26edcff304c159bbe23c53b23cf9e3a10193e07f.docx', 312, 1, '2024-03-23 06:55:14', NULL),
(130, 'Certificates_v2', 'Raffaela', '.docx', 'docs', '1716584', '26edcff304c159bbe23c53b23cf9e3a10193e07f.docx', 312, 1, '2024-03-23 06:55:35', NULL),
(131, 'Application_v3', 'Raffaela', '.docx', 'resume', '84540', 'c58578da8d02e57ee5600a73785928e0390cc9ec.docx', 312, 1, '2024-03-23 07:11:52', NULL),
(132, 'DOH_v1', 'MARL', '.pdf', 'DOCS', '213800', '0c73a9f59c49c580185fb8a37c20dc557abfa8fd.pdf', 312, 1, '2024-03-23 12:52:50', NULL),
(133, 'Certificates_v3', 'Raffaela', '.docx', 'resume', '1716584', '26edcff304c159bbe23c53b23cf9e3a10193e07f.docx', 312, 1, '2024-03-25 02:22:06', NULL),
(134, 'Certificates_v4', 'Raffaela', '.docx', 'resume', '1716584', '26edcff304c159bbe23c53b23cf9e3a10193e07f.docx', 312, 1, '2024-03-25 02:22:35', NULL),
(135, 'Certificates_v5', 'Raffaela', '.docx', 'resume', '1716584', '26edcff304c159bbe23c53b23cf9e3a10193e07f.docx', 312, 1, '2024-03-25 02:22:46', NULL),
(136, 'Certificates_v6', 'Raffaela', '.docx', 'resume', '1716584', '26edcff304c159bbe23c53b23cf9e3a10193e07f.docx', 312, 1, '2024-03-25 02:23:05', NULL),
(137, 'Test_v1', 'Test', '.pdf', 'docs', '4322418', '6a9c15fc4e5b99a4feaac7341f12a12e5c94d4be.pdf', 312, 1, '2024-03-25 02:49:02', NULL),
(138, 'Test_v2', 'Test', '.pdf', 'test', '4322418', '6a9c15fc4e5b99a4feaac7341f12a12e5c94d4be.pdf', 312, 1, '2024-03-25 02:50:23', NULL),
(139, 'Test_v3', 'test', '.pdf', 'test', '4322418', '6a9c15fc4e5b99a4feaac7341f12a12e5c94d4be.pdf', 312, 1, '2024-03-25 02:51:04', NULL),
(140, 'dost_v1', 'dost', '.pdf', 'test', '4322418', '6a9c15fc4e5b99a4feaac7341f12a12e5c94d4be.pdf', 309, 1, '2024-03-25 03:34:14', NULL),
(141, 'Certificates_v7', 'Me', '.pdf', 'txt', '4322418', '6a9c15fc4e5b99a4feaac7341f12a12e5c94d4be.pdf', 315, 1, '2024-03-25 05:12:11', NULL),
(142, 'report_v1', 'Marl', '.pdf', 'confidential', '1791888', '8ed18199e6f529ac61bad8a80356e301a86c6121.pdf', 312, 1, '2024-03-25 07:53:55', NULL),
(143, 'SALN_v1', 'TEST', '.docx', 'confidential', '1709678', 'dcd5d0faea3dd880b2d29ef6616d16fe11e503a8.docx', 309, 1, '2024-03-25 08:55:01', NULL),
(144, 'secret_v1', 'secret', '.pdf', 'wara', '4322418', '6a9c15fc4e5b99a4feaac7341f12a12e5c94d4be.pdf', 312, 1, '2024-03-25 19:18:00', NULL),
(145, 'Certificates_v8', 'Raffaela', '.pdf', 'docs', '4322418', '6a9c15fc4e5b99a4feaac7341f12a12e5c94d4be.pdf', 312, 1, '2024-04-04 13:15:04', NULL),
(146, 'Certificates_v9', 'Raffaela', 'pdf', 'resume', '1791888', '8ed18199e6f529ac61bad8a80356e301a86c6121.pdf', 312, 1, '2024-04-05 06:27:33', NULL),
(147, 'none_v1', 'lyly', 'pdf', 'docs', '4322418', '6a9c15fc4e5b99a4feaac7341f12a12e5c94d4be.pdf', 312, 1, '2024-04-05 06:33:31', NULL),
(148, 'none_v2', 'lyly', 'pdf', 'docs', '4322418', '6a9c15fc4e5b99a4feaac7341f12a12e5c94d4be.pdf', 312, 1, '2024-04-05 06:37:31', NULL),
(149, 'none_v3', 'lyly', 'pdf', 'docs', '4322418', '6a9c15fc4e5b99a4feaac7341f12a12e5c94d4be.pdf', 312, 1, '2024-04-05 06:38:12', NULL),
(150, 'none_v4', 'lyly', 'pdf', 'docs', '4322418', '6a9c15fc4e5b99a4feaac7341f12a12e5c94d4be.pdf', 312, 1, '2024-04-05 06:40:51', NULL),
(151, 'Certificates_v10', 'Raffaela', 'pdf', 'resume', '1791888', '8ed18199e6f529ac61bad8a80356e301a86c6121.pdf', 312, 1, '2024-04-05 06:41:46', NULL),
(152, 'HI_v1', 'HI', 'pdf', 'HI', '4322418', '6a9c15fc4e5b99a4feaac7341f12a12e5c94d4be.pdf', 209, 1, '2024-04-05 06:42:46', NULL),
(153, 'heh_v1', 'heh', 'pdf', 'heh', '4322418', '6a9c15fc4e5b99a4feaac7341f12a12e5c94d4be.pdf', 312, 1, '2024-04-05 06:44:57', NULL),
(154, 'GE Lng!_v1', 'marl', 'pdf', 'marl', '1791888', '8ed18199e6f529ac61bad8a80356e301a86c6121.pdf', 312, 1, '2024-04-05 06:52:42', NULL),
(155, 'GE Lng!_v2', 'marl', 'pdf', 'marl', '1791888', '8ed18199e6f529ac61bad8a80356e301a86c6121.pdf', 312, 1, '2024-04-05 06:53:34', NULL),
(156, 'GE Lng!_v3', 'marl', 'pdf', 'marl', '1791888', '8ed18199e6f529ac61bad8a80356e301a86c6121.pdf', 312, 1, '2024-04-05 06:54:08', NULL),
(157, 'GE Lng!_v4', 'marl', 'pdf', 'marl', '1791888', '8ed18199e6f529ac61bad8a80356e301a86c6121.pdf', 312, 1, '2024-04-05 06:54:13', NULL),
(158, 'GE Lng!_v5', 'marl', 'pdf', 'marl', '1791888', '8ed18199e6f529ac61bad8a80356e301a86c6121.pdf', 312, 1, '2024-04-05 06:55:47', NULL),
(159, 'hwllo_v1', 'hello', 'pdf', 'hello', '1791888', '8ed18199e6f529ac61bad8a80356e301a86c6121.pdf', 312, 1, '2024-04-05 06:57:21', NULL),
(160, 'hwllo_v2', 'hello', 'pdf', 'hello', '1791888', '8ed18199e6f529ac61bad8a80356e301a86c6121.pdf', 312, 1, '2024-04-05 07:00:40', NULL),
(161, 'heh_v2', 'heh', 'pdf', 'heh', '4322418', '6a9c15fc4e5b99a4feaac7341f12a12e5c94d4be.pdf', 312, 1, '2024-04-05 07:04:52', NULL),
(162, 'heh_v3', 'heh', 'pdf', 'heh', '4322418', '6a9c15fc4e5b99a4feaac7341f12a12e5c94d4be.pdf', 312, 1, '2024-04-05 07:05:11', NULL),
(163, 'heh_v4', 'heh', 'pdf', 'heh', '4322418', '6a9c15fc4e5b99a4feaac7341f12a12e5c94d4be.pdf', 312, 1, '2024-04-05 07:05:31', NULL),
(164, 'dadad_v1', 'dadad', 'pdf', 'dadad', '4322418', '6a9c15fc4e5b99a4feaac7341f12a12e5c94d4be.pdf', 312, 1, '2024-04-05 07:09:32', NULL),
(165, 'dadad_v2', 'dadad', 'pdf', 'dadad', '4322418', '6a9c15fc4e5b99a4feaac7341f12a12e5c94d4be.pdf', 312, 1, '2024-04-05 07:09:48', NULL),
(166, 'aa_v1', 'aa', 'pdf', 'aa', '4322418', '6a9c15fc4e5b99a4feaac7341f12a12e5c94d4be.pdf', 312, 1, '2024-04-05 07:17:00', NULL),
(167, 'aa_v2', 'aa', 'pdf', 'aa', '4322418', '6a9c15fc4e5b99a4feaac7341f12a12e5c94d4be.pdf', 312, 1, '2024-04-05 07:17:18', NULL),
(168, 'aa_v3', 'aa', 'pdf', 'aa', '4322418', '6a9c15fc4e5b99a4feaac7341f12a12e5c94d4be.pdf', 312, 1, '2024-04-05 07:21:00', NULL),
(169, 'Certificates_v11', 'Alyssa RAs', 'pdf', 'resume', '4322418', '6a9c15fc4e5b99a4feaac7341f12a12e5c94d4be.pdf', 312, 1, '2024-04-05 07:33:38', NULL),
(170, 'Certificates_v12', 'Alyssa RAs', 'pdf', 'resume', '4322418', '6a9c15fc4e5b99a4feaac7341f12a12e5c94d4be.pdf', 312, 1, '2024-04-05 07:34:00', NULL),
(171, 'fe_v1', 'e', 'pdf', 'fe', '4322418', '6a9c15fc4e5b99a4feaac7341f12a12e5c94d4be.pdf', 312, 1, '2024-04-05 07:37:13', NULL),
(172, 'fw_v1', 'fw', 'pdf', 'fw', '1791888', '8ed18199e6f529ac61bad8a80356e301a86c6121.pdf', 312, 1, '2024-04-05 07:39:17', NULL),
(173, 'GE Lng!_v6', 'ge', 'pdf', 'ge', '4322418', '6a9c15fc4e5b99a4feaac7341f12a12e5c94d4be.pdf', 312, 1, '2024-04-05 07:40:16', NULL),
(174, 'GE Lng!_v7', 'ge', 'pdf', 'ge', '4322418', '6a9c15fc4e5b99a4feaac7341f12a12e5c94d4be.pdf', 312, 1, '2024-04-05 07:43:08', NULL),
(175, 'ahm_v1', 'ahm', 'pdf', 'ahm', '1791888', '8ed18199e6f529ac61bad8a80356e301a86c6121.pdf', 209, 1, '2024-04-05 07:44:15', NULL),
(176, 'wow_v1', 'wow', 'pdf', 'wow', '4322418', '6a9c15fc4e5b99a4feaac7341f12a12e5c94d4be.pdf', 312, 1, '2024-04-05 07:46:39', NULL),
(177, 'ahm_v2', 'ahm', 'pdf', 'ahm', '1791888', '8ed18199e6f529ac61bad8a80356e301a86c6121.pdf', 209, 1, '2024-04-05 07:47:39', NULL),
(178, 'GE Lng!_v8', 'ge', 'pdf', 'ge', '1791888', '8ed18199e6f529ac61bad8a80356e301a86c6121.pdf', 209, 1, '2024-04-05 07:50:40', NULL),
(179, 'hello_v1', 'hello', 'pdf', 'hello', '4322418', '6a9c15fc4e5b99a4feaac7341f12a12e5c94d4be.pdf', 312, 1, '2024-04-05 12:24:34', NULL),
(180, 'huehue_v1', 'heuhue', 'pdf', 'huehue', '4322418', '6a9c15fc4e5b99a4feaac7341f12a12e5c94d4be.pdf', 209, 1, '2024-04-05 12:36:24', NULL),
(181, 'night_v1', 'night', 'pdf', 'night', '4322418', '6a9c15fc4e5b99a4feaac7341f12a12e5c94d4be.pdf', 312, 1, '2024-04-05 12:42:38', NULL),
(182, 'ahm_v3', 'ahm', 'pdf', 'ahm', '4322418', '6a9c15fc4e5b99a4feaac7341f12a12e5c94d4be.pdf', 312, 1, '2024-04-05 12:46:27', NULL),
(183, 'lasst_v1', 'lasst', 'pdf', 'laasst', '1791888', '8ed18199e6f529ac61bad8a80356e301a86c6121.pdf', 312, 1, '2024-04-05 12:47:32', NULL),
(184, 'weee_v1', 'weee', '.pdf', 'weee', '1791888', '8ed18199e6f529ac61bad8a80356e301a86c6121.pdf', 312, 1, '2024-04-05 12:49:27', NULL),
(185, 'loq_v1', 'loq', '.pdf', 'loq', '1791888', '8ed18199e6f529ac61bad8a80356e301a86c6121.pdf', 312, 1, '2024-04-05 12:50:07', NULL),
(186, 'dadad_v3', 'd', 'pdf', 'd', '1791888', '8ed18199e6f529ac61bad8a80356e301a86c6121.pdf', 312, 1, '2024-04-05 12:50:32', NULL),
(187, 'marl_v1', 'marl', 'pdf', 'marl', '1791888', '8ed18199e6f529ac61bad8a80356e301a86c6121.pdf', 312, 1, '2024-04-05 12:56:56', NULL),
(188, 'GE Lng!_v9', 'ge', '.pdf', 'ge', '1791888', '8ed18199e6f529ac61bad8a80356e301a86c6121.pdf', 312, 1, '2024-04-05 12:57:50', NULL),
(189, 'bayd_v1', 'bayd', 'pdf', '1', '4322418', '6a9c15fc4e5b99a4feaac7341f12a12e5c94d4be.pdf', 312, 1, '2024-04-05 12:59:41', NULL),
(190, 'kem_v1', 'kem', '.pdf', 'kem\\\\', '1791888', '8ed18199e6f529ac61bad8a80356e301a86c6121.pdf', 312, 1, '2024-04-05 13:03:50', NULL),
(191, 'hello_v2', 'hello', '.pdf', '123', '1791888', '8ed18199e6f529ac61bad8a80356e301a86c6121.pdf', 312, 1, '2024-04-05 13:08:12', NULL),
(192, 'hellow_v1', 'hellow', '.pdf', 'hellow', '1791888', '8ed18199e6f529ac61bad8a80356e301a86c6121.pdf', 312, 1, '2024-04-05 13:20:14', NULL),
(193, 'grabe_v1', 'grabe', '.pdf', 'grabe', '1791888', '8ed18199e6f529ac61bad8a80356e301a86c6121.pdf', 312, 1, '2024-04-05 13:20:48', NULL),
(194, 'grabe_v2', 'grabe', '.pdf', 'grabe', '1791888', '8ed18199e6f529ac61bad8a80356e301a86c6121.pdf', 312, 1, '2024-04-05 13:21:08', NULL),
(195, 'grabe_v3', 'grabe', '.pdf', 'grabe', '1791888', '8ed18199e6f529ac61bad8a80356e301a86c6121.pdf', 312, 1, '2024-04-05 13:22:54', NULL),
(196, 'grabe_v4', 'grabe', '.pdf', 'grabe', '1791888', '8ed18199e6f529ac61bad8a80356e301a86c6121.pdf', 312, 1, '2024-04-05 13:23:45', NULL),
(197, 'HI_v2', 'hi', '.pdf', 'hi', '4322418', '6a9c15fc4e5b99a4feaac7341f12a12e5c94d4be.pdf', 312, 1, '2024-04-05 13:28:18', NULL),
(198, 'Certificates_v13', 'Raffaela', '.pdf', 'resume', '1791888', '8ed18199e6f529ac61bad8a80356e301a86c6121.pdf', 312, 1, '2024-04-05 13:33:21', NULL),
(199, 'docs_v1', 'docs ', '.docx', 'docs', '17711', '50d037d315ad4612a6e71ce9fd316d080351981f.docx', 312, 1, '2024-04-05 13:33:52', NULL),
(200, 'fwfw_v1', 'fwfwfw', '.docx', 'fwfw', '17711', '50d037d315ad4612a6e71ce9fd316d080351981f.docx', 312, 1, '2024-04-05 13:39:59', NULL),
(201, 'legs and peos_v1', 'Marl', '.pdf', 'docs', '2470385', '6abca838adc77a538dad970cde2815ac655f9076.pdf', 312, 1, '2024-04-08 02:31:50', NULL),
(202, 'gege_v1', 'gegeg', '.pptx', 'gege', '346989', 'fb2a2e4913b03fe04ccef2f753dad486b9383a7e.pptx', 312, 1, '2024-04-09 02:59:31', NULL),
(203, 'fe_v2', 'fe', '.docx', 'fe', '1716584', '26edcff304c159bbe23c53b23cf9e3a10193e07f.docx', 312, 1, '2024-04-09 03:01:45', NULL),
(204, 'gege_v2', 'ge', '.pptx', 'ge', '346989', 'fb2a2e4913b03fe04ccef2f753dad486b9383a7e.pptx', 312, 1, '2024-04-09 03:25:09', NULL),
(205, 'HI_v3', 'hi', '.pdf', 'test', '10572031', 'c0e16afe31e4380ad3fda5b5eecf4ba8dcd2b9cb.pdf', 312, 1, '2024-04-09 03:26:30', NULL),
(206, 'ojt_v1', 'ojt', '.docx', 'ojt', '1709819', 'd2c9074b11ce243042ad6620854d3ee2f76df883.docx', 312, 1, '2024-04-09 03:29:39', NULL),
(207, '1_v1', '1', '.pdf', '1', '10572031', 'c0e16afe31e4380ad3fda5b5eecf4ba8dcd2b9cb.pdf', 312, 1, '2024-04-09 03:32:04', NULL),
(208, 'Certificates_v14', 'Marl', '.pdf', '1', '2470385', '6abca838adc77a538dad970cde2815ac655f9076.pdf', 312, 1, '2024-04-09 03:33:27', NULL),
(209, 'peos_v1', 'peos', '.pptx', 'peos', '346989', 'fb2a2e4913b03fe04ccef2f753dad486b9383a7e.pptx', 312, 1, '2024-04-09 03:36:04', NULL),
(210, 'wara_v1', 'wara', '.pdf', 'wara', '10572031', 'c0e16afe31e4380ad3fda5b5eecf4ba8dcd2b9cb.pdf', 312, 1, '2024-04-09 03:37:32', NULL),
(211, 'DOH_v2', 'doh', '.docx', 'doh', '46460', '270281072c581f982e61523f0194fc5d4128ee85.docx', 312, 1, '2024-04-09 03:40:02', NULL),
(212, 'HI_v4', 'ge', '.pdf', 'ge', '226674', '7976e997ccbd48ed89f0842639bfe7a18098f538.pdf', 312, 1, '2024-04-09 03:42:44', NULL),
(213, 'FDd_v1', 'adAD', '.pdf', 'DadA', '2470385', '6abca838adc77a538dad970cde2815ac655f9076.pdf', 312, 1, '2024-04-09 03:43:35', NULL),
(214, 'SAFASF_v1', 'ASFSAFSA', '.docx', 'FSAFA', '1709795', 'e9afe7928a6d6c547b4694ad46bab1a5516ef5b1.docx', 312, 1, '2024-04-09 03:43:58', NULL),
(215, 'FASFSA_v1', 'SAFSAF', '.docx', 'FSAFSA', '46460', '270281072c581f982e61523f0194fc5d4128ee85.docx', 312, 1, '2024-04-09 03:44:12', NULL),
(216, 'VSAFSA_v1', 'FASFSA', '.docx', 'FSAFSA', '349485', '93931cb1568203df2b0912605ba6ee7b1062d119.docx', 312, 1, '2024-04-09 03:44:24', NULL),
(217, 'play_v1', 'play', '.pptx', 'play', '346989', 'fb2a2e4913b03fe04ccef2f753dad486b9383a7e.pptx', 312, 1, '2024-04-09 03:45:03', NULL),
(218, 'afasf_v1', 'fsafsaf', '.pdf', 'fsafsa', '2470385', '6abca838adc77a538dad970cde2815ac655f9076.pdf', 312, 1, '2024-04-09 03:47:47', NULL),
(219, 'check_v1', ' check', '.docx', 'check', '349485', '93931cb1568203df2b0912605ba6ee7b1062d119.docx', 312, 1, '2024-04-09 03:50:27', NULL),
(220, 'HI_v5', 'hi', '.docx', 'hi', '1716584', '26edcff304c159bbe23c53b23cf9e3a10193e07f.docx', 312, 1, '2024-04-09 04:02:22', NULL),
(221, 'fge_v1', 'ge', '.docx', 'ege', '46460', '270281072c581f982e61523f0194fc5d4128ee85.docx', 312, 1, '2024-04-09 04:10:19', NULL),
(222, 'gege_v3', 'ge', '.pdf', 'ge', '2470385', '6abca838adc77a538dad970cde2815ac655f9076.pdf', 312, 1, '2024-04-09 04:22:34', NULL),
(223, 'gege_v4', 'ge', '.docx', 'ge', '1709819', 'd2c9074b11ce243042ad6620854d3ee2f76df883.docx', 312, 1, '2024-04-09 04:24:24', NULL),
(224, 'gege_v5', 'ge', '.docx', 'ge', '1716584', '26edcff304c159bbe23c53b23cf9e3a10193e07f.docx', 209, 1, '2024-04-09 09:16:29', NULL),
(225, 'Certificates_v15', 'Raffaela', '.docx', 'resume', '303882', '6efef944f582e9edb326e98db476943d5d5f44eb.docx', 209, 1, '2024-04-09 09:17:13', NULL),
(226, 'HI_v6', 'marl', '.pdf', 'marl', '226674', '7976e997ccbd48ed89f0842639bfe7a18098f538.pdf', 209, 1, '2024-04-09 09:21:26', NULL),
(227, 'img_v1', 'img', '.pdf', 'img', '226674', '7976e997ccbd48ed89f0842639bfe7a18098f538.pdf', 312, 1, '2024-04-10 05:43:07', NULL),
(228, 'gege_v6', 'ge', '.pdf', 'ge', '226674', '7976e997ccbd48ed89f0842639bfe7a18098f538.pdf', 312, 1, '2024-04-10 12:56:19', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `encrypted_file`
--

CREATE TABLE `encrypted_file` (
  `id` int(11) NOT NULL,
  `name_file` varchar(255) NOT NULL,
  `recipient` varchar(255) NOT NULL,
  `encrypted_file_path` varchar(255) NOT NULL,
  `encryption_key` varchar(255) NOT NULL,
  `upload_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `encrypted_file`
--

INSERT INTO `encrypted_file` (`id`, `name_file`, `recipient`, `encrypted_file_path`, `encryption_key`, `upload_time`) VALUES
(1, 'ge', 'Marl Palanog', 'uploads/20231112220756_Godofredo (1).docx', 'Û&VÚ6IÔö¼¢WEÏ‘õJ.ë›¢Ðwà]ëp„', '2023-11-20 07:49:00'),
(2, 'ge', 'Marl Palanog', 'uploads/20231112220756_Godofredo (1).docx', '7>?·°(“¶ª÷gŒJ76á%~!<¦Dyõ`í«æÂ a«', '2023-11-20 07:50:43'),
(3, 'ge', 'Marl Palanog', 'uploads/20231112220756_Godofredo (1).docx', 'ÙÅÎû<£xŒ»ÙIï2Il’j01þ—a¤(©$', '2023-11-20 07:56:43'),
(4, 'HAHA', 'Marl Palanog', 'uploads/20231111235818_jusko.docx', 'Æ)üMV*Zà¹æI6$ý<žÿ£(>EIvoª-ú±®', '2023-11-20 07:56:53');

-- --------------------------------------------------------

--
-- Table structure for table `encryption_keys`
--

CREATE TABLE `encryption_keys` (
  `id` int(11) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `encryption_key_path` varchar(255) NOT NULL,
  `decryption_key_path` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `encryption_keys`
--

INSERT INTO `encryption_keys` (`id`, `file_path`, `encryption_key_path`, `decryption_key_path`) VALUES
(1, 'file1.txt', '../key/encryption_key.txt', '../key/decryption_key.txt'),
(2, '1', '../key/encryption_key.txt', '../key/decryption_key.txt'),
(3, '1', '../key/encryption_key.txt', '../key/decryption_key.txt'),
(4, '1', '../key/encryption_key.txt', '../key/decryption_key.txt'),
(5, '1', '../key/encryption_key.txt', '../key/decryption_key.txt'),
(6, '1', '../key/encryption_key.txt', '../key/decryption_key.txt'),
(7, '1', '../key/encryption_key.txt', '../key/decryption_key.txt'),
(8, '1', '../key/encryption_key.txt', '../key/decryption_key.txt'),
(9, '1', '../key/encryption_key.txt', '../key/decryption_key.txt'),
(10, '1', '../key/encryption_key.txt', '../key/decryption_key.txt'),
(11, '1', '../key/encryption_key.txt', '../key/decryption_key.txt'),
(12, '1', '../key/encryption_key.txt', '../key/decryption_key.txt'),
(13, '1', '../key/encryption_key.txt', '../key/decryption_key.txt'),
(14, '1', '../key/encryption_key.txt', '../key/decryption_key.txt'),
(15, '1', '../key/encryption_key.txt', '../key/decryption_key.txt'),
(16, '1', '../key/encryption_key.txt', '../key/decryption_key.txt'),
(17, '1', '../key/encryption_key.txt', '../key/decryption_key.txt'),
(18, '1', '../key/encryption_key.txt', '../key/decryption_key.txt'),
(19, '1', '../key/encryption_key.txt', '../key/decryption_key.txt'),
(20, '1', '../key/encryption_key.txt', '../key/decryption_key.txt'),
(21, '1', '../key/encryption_key.txt', '../key/decryption_key.txt'),
(22, '1', '../key/encryption_key.txt', '../key/decryption_key.txt'),
(23, '1', '../key/encryption_key.txt', '../key/decryption_key.txt'),
(24, '1', '../key/encryption_key.txt', '../key/decryption_key.txt'),
(25, '1', '../key/encryption_key.txt', '../key/decryption_key.txt'),
(26, '1', '../key/encryption_key.txt', '../key/decryption_key.txt'),
(27, '1', '../key/encryption_key.txt', '../key/decryption_key.txt'),
(28, '1', '../key/encryption_key.txt', '../key/decryption_key.txt'),
(29, '1', '../key/encryption_key.txt', '../key/decryption_key.txt'),
(30, '1', '../key/encryption_key.txt', '../key/decryption_key.txt'),
(31, '1', '../key/encryption_key.txt', '../key/decryption_key.txt'),
(32, '1', '../key/encryption_key.txt', '../key/decryption_key.txt'),
(33, '1', '../key/encryption_key.txt', '../key/decryption_key.txt'),
(34, '1', '../key/encryption_key.txt', '../key/decryption_key.txt'),
(35, '1', '../../key/encryption_key.txt', '../../key/decryption_key.txt'),
(36, '1', '../../key/encryption_key.txt', '../../key/decryption_key.txt'),
(37, '1', '../../key/encryption_key.txt', '../../key/decryption_key.txt'),
(38, '1', '../../key/encryption_key.txt', '../../key/decryption_key.txt'),
(39, '1', '../../key/encryption_key.txt', '../../key/decryption_key.txt'),
(40, '1', '../../key/encryption_key.txt', '../../key/decryption_key.txt'),
(41, '1', '../../key/encryption_key.txt', '../../key/decryption_key.txt'),
(42, '1', '../../key/encryption_key.txt', '../../key/decryption_key.txt'),
(43, '1', '../../key/encryption_key.txt', '../../key/decryption_key.txt'),
(44, '1', '../../key/encryption_key.txt', '../../key/decryption_key.txt'),
(45, '1', '../../key/encryption_key.txt', '../../key/decryption_key.txt'),
(46, '1', '../../key/encryption_key.txt', '../../key/decryption_key.txt'),
(47, '1', '../../key/encryption_key.txt', '../../key/decryption_key.txt'),
(48, '1', '../../key/encryption_key.txt', '../../key/decryption_key.txt'),
(49, '1', '../../key/encryption_key.txt', '../../key/decryption_key.txt'),
(50, '1', '../../key/encryption_key.txt', '../../key/decryption_key.txt'),
(51, '1', '../../key/encryption_key.txt', '../../key/decryption_key.txt'),
(52, '1', '../../key/encryption_key.txt', '../../key/decryption_key.txt'),
(53, '1', '../../key/encryption_key.txt', '../../key/decryption_key.txt'),
(54, '1', '../../key/encryption_key.txt', '../../key/decryption_key.txt'),
(55, '1', '../../key/encryption_key.txt', '../../key/decryption_key.txt'),
(56, '1', '../../key/encryption_key.txt', '../../key/decryption_key.txt'),
(57, '1', '../../key/encryption_key.txt', '../../key/decryption_key.txt'),
(58, '1', '../../key/encryption_key.txt', '../../key/decryption_key.txt'),
(59, '1', '../../key/encryption_key.txt', '../../key/decryption_key.txt'),
(60, '1', '../../key/encryption_key.txt', ''),
(61, '1', '../../key/encryption_key.txt', ''),
(62, '1', '../../key/encryption_key.txt', ''),
(63, '1', '../../key/encryption_key.txt', ''),
(64, '1', '../../key/encryption_key.txt', ''),
(65, '1', '../../key/encryption_key.txt', ''),
(66, '1', '../../key/encryption_key.txt', ''),
(67, '1', '../../key/encryption_key.txt', ''),
(68, '1', '../../key/encryption_key.txt', ''),
(69, '1', '../../key/encryption_key.txt', ''),
(70, '1', '../../key/encryption_key.txt', ''),
(71, '../upload/encrypted_file/14f05f35ca2d1f17914cae926891e91d.docx', '../../key/encryption_key.txt', ''),
(72, '../upload/encrypted_file/45c3ae25c3b821071982b8ea6648695d.docx', '../../key/encryption_key.txt', ''),
(73, '1', '../../key/encryption_key.txt', ''),
(74, '1', '../../key/encryption_key.txt', ''),
(75, '../upload/encrypted_files/2b53dd591bcd3bb913bd9e775938b10a.docx.enc', '../../key/encryption_key.txt', ''),
(76, '../upload/encrypted_files/f82c3fe4479373a8f2c54cc486c0e3c0.docx.enc', '../../key/encryption_key.txt', ''),
(77, '../upload/encrypted_files/70a343074969d560918a16cda8347580.docx.enc', '../../key/encryption_key.txt', ''),
(78, '../upload/encrypted_files/501b889c52a6ae5bb4f7c318ac3031b8.docx.enc', '../../key/encryption_key.txt', ''),
(79, '../upload/encrypted_files/c618ff5607f75eb6b0ef9ac71511cf8b.docx.enc', '../../key/encryption_key.txt', ''),
(80, '../upload/encrypted_files/e7ad38c3b4270c2f7f74011588482b21.docx.enc', '../../key/encryption_key.txt', ''),
(81, '../upload/encrypted_files/c5bea2e229c94e8467465e512963225b.docx.enc', '../../key/encryption_key.txt', ''),
(82, '../upload/encrypted_files/1a8639e8fa90ee7f7f10eaebd8fe88de.docx.enc', '../../key/encryption_key.txt', ''),
(83, '1', '../../key/encryption_key.txt', ''),
(84, '1', '../../key/encryption_key.txt', ''),
(85, '1', '../../key/encryption_key.txt', ''),
(86, '1', '../../key/encryption_key.txt', ''),
(87, '1', '../../key/encryption_key.txt', ''),
(88, '1', '../../key/encryption_key.txt', ''),
(89, '1', '../../key/encryption_key.txt', ''),
(90, '1', '../../key/encryption_key.txt', ''),
(91, '1', '../../key/encryption_key.txt', ''),
(92, '1', '../../key/encryption_key.txt', ''),
(93, '1', '../../key/encryption_key.txt', ''),
(94, '1', '../../key/encryption_key.txt', ''),
(95, '1', '../../key/encryption_key.txt', ''),
(96, '1', '../../key/encryption_key.txt', ''),
(97, '1', '../../key/encryption_key.txt', ''),
(98, '1', '../../key/encryption_key.txt', ''),
(99, '1', '../../key/encryption_key.txt', ''),
(100, '1', '../../key/encryption_key.txt', ''),
(101, '1', '../../key/encryption_key.txt', ''),
(102, '1', '../../key/encryption_key.txt', ''),
(103, '1', '../../key/encryption_key.txt', ''),
(104, '1', '../../key/encryption_key.txt', ''),
(105, '1', '../../key/encryption_key.txt', ''),
(106, '1', '../../key/encryption_key.txt', ''),
(107, '1', '../../key/encryption_key.txt', ''),
(108, '1', '../../key/encryption_key.txt', ''),
(109, '1', '../../key/encryption_key.txt', ''),
(110, '1', '../../key/encryption_key.txt', ''),
(111, '1', '../../key/encryption_key.txt', ''),
(112, '1', '../../key/encryption_key.txt', ''),
(113, '1', '../../key/encryption_key.txt', ''),
(114, '1', '../../key/encryption_key.txt', ''),
(115, '1', '../../key/encryption_key.txt', ''),
(116, '1', '../../key/encryption_key.txt', ''),
(117, '1', '../../key/encryption_key.txt', ''),
(118, '1', '../../key/encryption_key.txt', ''),
(119, '1', '../../key/encryption_key.txt', ''),
(120, '1', '../../key/encryption_key.txt', ''),
(121, '1', '../../key/encryption_key.txt', ''),
(122, '1', '../../key/encryption_key.txt', ''),
(123, '1', '../../key/encryption_key.txt', ''),
(124, '1', '../../key/encryption_key.txt', ''),
(125, '1', '../../key/encryption_key.txt', ''),
(126, '1', '../../key/encryption_key.txt', ''),
(127, '1', '../../key/encryption_key.txt', ''),
(128, '1', '../../key/encryption_key.txt', ''),
(129, '1', '../../key/encryption_key.txt', ''),
(130, '1', '../../key/encryption_key.txt', ''),
(131, '1', '../../key/encryption_key.txt', ''),
(132, '1', '../../key/encryption_key.txt', ''),
(133, '1', '../../key/encryption_key.txt', ''),
(134, '1', '../../key/encryption_key.txt', ''),
(135, '1', '../../key/encryption_key.txt', ''),
(136, '1', '../../key/encryption_key.txt', ''),
(137, '1', '../../key/encryption_key.txt', ''),
(138, '1', '../../key/encryption_key.txt', ''),
(139, '1', '../../key/encryption_key.txt', ''),
(140, '1', '../../key/encryption_key.txt', ''),
(141, '1', '../../key/encryption_key.txt', ''),
(142, '1', '../../key/encryption_key.txt', ''),
(143, '1', '../../key/encryption_key.txt', ''),
(144, '1', '../../key/encryption_key.txt', ''),
(145, '1', '../../key/encryption_key.txt', ''),
(146, '1', '../../key/encryption_key.txt', ''),
(147, '1', '../../key/encryption_key.txt', ''),
(148, '1', '../../key/encryption_key.txt', ''),
(149, '1', '../../key/encryption_key.txt', ''),
(150, '1', '../../key/encryption_key.txt', ''),
(151, '1', '../../key/encryption_key.txt', ''),
(152, '1', '../../key/encryption_key.txt', ''),
(153, '1', '../../key/encryption_key.txt', ''),
(154, '1', '../../key/encryption_key.txt', ''),
(155, '1', '../../key/encryption_key.txt', ''),
(156, '1', '../../key/encryption_key.txt', ''),
(157, '1', '../../key/encryption_key.txt', ''),
(158, '1', '../../key/encryption_key.txt', '');

-- --------------------------------------------------------

--
-- Table structure for table `files`
--

CREATE TABLE `files` (
  `id` int(11) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `path` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `files`
--

INSERT INTO `files` (`id`, `filename`, `path`) VALUES
(0, '[vvalue.docs]', '[upload/documents]');

-- --------------------------------------------------------

--
-- Table structure for table `folders`
--

CREATE TABLE `folders` (
  `folder_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `folder_name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `login_attempts`
--

CREATE TABLE `login_attempts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `failed_login_attempts` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `login_attempts`
--
DELIMITER $$
CREATE TRIGGER `validate_login` BEFORE INSERT ON `login_attempts` FOR EACH ROW BEGIN
  DECLARE max_attempts INT DEFAULT 5;
  DECLARE remaining_attempts INT;
  
  SELECT failed_login_attempts INTO remaining_attempts
  FROM user_form
  WHERE id = NEW.user_id;
  
  IF remaining_attempts >= max_attempts THEN
    CALL lock_account(NEW.user_id);
  ELSE
    CALL increment_failed_attempts(NEW.user_id);
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `message_id` int(11) NOT NULL,
  `sender_id` int(11) DEFAULT NULL,
  `recipient_id` int(11) DEFAULT NULL,
  `message_type` varchar(255) DEFAULT NULL,
  `message_content` text DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notification_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `unread_count` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notification_data`
--

CREATE TABLE `notification_data` (
  `description` varchar(255) NOT NULL,
  `status` int(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  `receiver_user_id` int(11) NOT NULL,
  `notification_count` int(11) DEFAULT 0,
  `is_notification` tinyint(1) DEFAULT 0,
  `notification_message` varchar(255) DEFAULT NULL,
  `sender_user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notification_data`
--

INSERT INTO `notification_data` (`description`, `status`, `user_id`, `receiver_user_id`, `notification_count`, `is_notification`, `notification_message`, `sender_user_id`) VALUES
('You have received a new message.', 0, 309, 0, 1, 0, NULL, NULL),
('here', 0, 0, 0, 1, 0, NULL, NULL),
('safafssaa', 0, 0, 0, 1, 0, NULL, NULL),
('You have received a new message.', 0, 0, 312, 3, 0, NULL, NULL),
('You have received a new message.', 0, 0, 309, 0, 0, NULL, NULL),
('You have received a new message.', 0, 0, 312, 3, 0, NULL, NULL),
('You have received a new message.', 0, 0, 309, 0, 0, NULL, NULL),
('You have received a new message.', 0, 0, 312, 3, 0, NULL, NULL),
('HAHHAA', 1, 309, 312, 3, 0, NULL, NULL),
('saafafsafa', 1, 312, 0, 1, 0, NULL, NULL),
('You have received a new message.', 0, 0, 309, 0, 0, NULL, NULL),
('You have received a new message.', 0, 0, 313, 0, 0, NULL, NULL),
('You have received a new message.', 0, 0, 313, 0, 0, NULL, NULL),
('You have received a new message.', 0, 0, 207, 0, 0, NULL, NULL),
('You have received a new message.', 0, 0, 207, 0, 0, NULL, NULL),
('You have received a new message.', 0, 0, 209, 1, 0, NULL, NULL),
('You have received a new message.', 0, 0, 312, 3, 0, NULL, NULL),
('You have received a new message.', 0, 0, 312, 3, 0, NULL, NULL),
('You have received a new message.', 0, 0, 312, 3, 0, NULL, NULL),
('You have received a new message.', 0, 0, 312, 3, 0, NULL, NULL),
('You have received a new message.', 0, 0, 307, 0, 0, NULL, NULL),
('You have received a new message.', 0, 0, 311, 0, 0, NULL, NULL),
('You have received a new message.', 0, 0, 314, 3, 0, NULL, NULL),
('You have received a new message.', 0, 0, 284, 3, 0, NULL, NULL),
('', 0, 0, 314, 2, 1, 'You have received a reply from user 284', 284),
('', 0, 0, 284, 3, 1, 'You have received a reply from user 314', 314),
('', 0, 0, 314, 1, 1, 'You have received a reply from user 284', 284),
('', 0, 0, 284, 2, 1, 'You have received a reply from user 314', 314),
('You have received a new message.', 0, 0, 309, 0, 0, NULL, NULL),
('', 0, 0, 284, 1, 1, 'You have received a reply from user 312', 312),
('', 0, 0, 0, 1, 1, 'You have received a reply from user 312', 312),
('You have received a new message.', 0, 0, 309, 0, 0, NULL, NULL),
('', 0, 0, 312, 3, 1, 'You have received a reply from user 309', 309),
('You have received a new message.', 0, 0, 312, 2, 0, NULL, NULL),
('You have received a new message.', 0, 0, 312, 2, 0, NULL, NULL),
('You have received a new message.', 0, 0, 309, 0, 0, NULL, NULL),
('You have received a new message.', 0, 0, 315, 0, 0, NULL, NULL),
('You have received a new message.', 0, 0, 316, 0, 0, NULL, NULL),
('', 0, 0, 312, 2, 1, 'You have received a reply from user 316', 316),
('You have received a new message.', 0, 0, 209, 1, 0, NULL, NULL),
('', 0, 0, 312, 1, 1, 'You have received a reply from user 209', 209),
('You have received a new message.', 0, 0, 312, 0, 0, NULL, NULL),
('', 0, 0, 209, 1, 1, 'You have received a reply from user 312', 312),
('You have received a new message.', 0, 0, 312, 0, 0, NULL, NULL),
('You have received a new message.', 0, 0, 312, 0, 0, NULL, NULL),
('You have received a new message.', 0, 0, 312, 0, 0, NULL, NULL),
('You have received a new message.', 0, 0, 280, 0, 0, NULL, NULL),
('You have received a new message.', 0, 0, 209, 0, 0, NULL, NULL),
('You have received a new message.', 0, 0, 312, 0, 0, NULL, NULL),
('You have received a new message.', 0, 0, 312, 0, 0, NULL, NULL),
('You have received a new message.', 0, 0, 312, 0, 0, NULL, NULL),
('You have received a new message.', 0, 0, 312, 0, 0, NULL, NULL),
('You have received a new message.', 0, 0, 312, 0, 0, NULL, NULL),
('You have received a new message.', 0, 0, 284, 0, 0, NULL, NULL),
('You have received a new message.', 0, 0, 209, 0, 0, NULL, NULL),
('You have received a new message.', 0, 0, 312, 0, 0, NULL, NULL),
('You have received a new message.', 0, 0, 312, 0, 0, NULL, NULL),
('You have received a new message.', 0, 0, 209, 0, 0, NULL, NULL),
('You have received a new message.', 0, 0, 209, 0, 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `received_files`
--

CREATE TABLE `received_files` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `sender_name` varchar(255) NOT NULL,
  `receiver_name` varchar(255) NOT NULL,
  `message_content` varchar(255) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `recipient_email` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `received_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `received_files`
--

INSERT INTO `received_files` (`id`, `email`, `sender_name`, `receiver_name`, `message_content`, `file_name`, `recipient_email`, `file_path`, `received_time`) VALUES
(505, '', 'default_username', 'Mark Palanog', '', 'afsafsafa', 'cfpamore1224@gmail.com', '../upload/encrypted_file/8aa8ddc1105fa4bbb91ecdad557888a5.docx', '2023-12-16 22:01:22'),
(506, '', '', 'Mark Palanog', '', 'your_file_name', 'cfpamore1224@gmail.com', 'your_file_path', '2023-12-16 15:08:25'),
(507, '', '', 'Mark Palanog', '', 'your_file_name', 'cfpamore1224@gmail.com', 'your_file_path', '2023-12-16 15:11:01'),
(508, '', '', 'Alyssa Ras', '', 'your_file_name', 'daemontzyy@gmail.com', 'your_file_path', '2023-12-16 15:14:30'),
(509, '', '', 'Alyssa Ras', '', 'your_file_name', 'daemontzyy@gmail.com', 'your_file_path', '2023-12-16 15:15:35'),
(510, '', 'default_username', '', '', 'gege', 'docuvault0@gmail.com', '../upload/encrypted_file/3ddd7a317d9b971179b44792baca4499.docx', '2023-12-16 22:26:36'),
(511, '', 'default_username', '', '', 'gegege', 'docuvault0@gmail.com', '../upload/encrypted_file/e35ced317a1a42deafd6d1df91702c8a.docx', '2023-12-16 23:03:34'),
(512, '', 'default_username', '', '', 'fafasf', 'docuvault0@gmail.com', '../upload/encrypted_file/2dcdffdd45de7c7aa104bcaecae5eca9.docx', '2023-12-17 11:34:16'),
(513, '', 'default_username', '', '', 'ge', 'docuvault0@gmail.com', '../upload/encrypted_file/99a60fb2c6708b142102702f2716129d.docx', '2023-12-18 12:03:02'),
(514, '', 'default_username', '', '', 'ge', 'docuvault0@gmail.com', '../upload/encrypted_file/36ea9ddaa83cc9baff74697e59fa7a45.docx', '2023-12-18 13:40:54'),
(515, '', 'default_username', '', '', 'ge', 'docuvault0@gmail.com', '../upload/encrypted_file/1d055d6081c4c6114fa35017fc7105bd.doc', '2023-12-18 15:11:54'),
(516, '', 'default_username', 'Alyssa Ras', '', 'asdasfasfsa', 'daemontzyy@gmail.com', '../upload/encrypted_file/64740f60cd8b25d57adcae360d67a50d.docx', '2023-12-18 19:22:35'),
(517, '', 'default_username', '', '', 'geeg', 'docuvault0@gmail.com', '../upload/encrypted_file/4e7009b3b6fb4f6fb62d147c3f6e8988.docx', '2023-12-18 19:25:58'),
(518, '', 'default_username', '', '', 'ahm', 'docuvault0@gmail.com', '../upload/encrypted_file/214e129510bd1db037d79b1b0b389415.docx', '2023-12-18 19:27:45'),
(519, '', 'default_username', '', '', 'ahm', 'docuvault0@gmail.com', '../upload/encrypted_file/c83482b0ecf35b471d6b1093586bf84b.docx', '2023-12-18 19:28:47'),
(520, '', 'default_username', 'Alyssa Ras', '', 'asdasfasfsa', 'daemontzyy@gmail.com', '../upload/encrypted_file/234c8041627b86c553e7ff445380ff62.docx', '2023-12-18 19:29:49'),
(521, '', 'DefaultSenderName', 'Alyssa Ras', '', 'asdasfasfsa', 'daemontzyy@gmail.com', '../upload/encrypted_file/54c70531df7aab98a54cb2a183e1df97.docx', '2023-12-18 19:33:24'),
(522, '', 'DefaultSenderName', 'Alyssa Ras', '', 's', 'daemontzyy@gmail.com', '../upload/encrypted_file/20808c1b6fdb565f14b169e5e7dcb815.docx', '2023-12-18 19:35:45'),
(523, '', 'DefaultSenderName', 'Alyssa Ras', '', 'ss', 'daemontzyy@gmail.com', '../upload/encrypted_file/fc03f3313a82e8fb8a2c9cd749cbe16f.docx', '2023-12-18 19:38:54'),
(524, '', 'DefaultSenderName', 'Alyssa Ras', '', 'sss', 'daemontzyy@gmail.com', '../upload/encrypted_file/4fcf8898a93a95eec93a62717aea9bc6.docx', '2023-12-18 19:41:00'),
(525, '', 'DefaultSenderName', 'Alyssa Ras', '', 'ssss', 'daemontzyy@gmail.com', '../upload/encrypted_file/654a3a72ae3f6154e2c8f56d0c4a106a.docx', '2023-12-18 19:42:15'),
(526, '', 'DefaultSenderName', 'John', '', 'gege', 'm4rlreynan@gmail.com', '../upload/encrypted_file/039d79f357fa303d08bc988cebe4f19b.docx', '2023-12-18 20:21:51'),
(527, '', 'DefaultSenderName', 'Alyssa Ras', '', 'sfafas', 'daemontzyy@gmail.com', '../upload/encrypted_file/43ec8e4f2843164fda847588551ebbba.docx', '2023-12-18 21:04:50'),
(528, '', 'DefaultSenderName', 'Alyssa Ras', '', 'ge', 'daemontzyy@gmail.com', '../upload/encrypted_file/89d93c19581a415ebba9bd47b24c5176.docx', '2023-12-18 21:10:52'),
(529, '', 'DefaultSenderName', 'Mark Palanog', '', 'ddd', 'cfpamore1224@gmail.com', '../upload/encrypted_file/8ce618802378e47b6fd5bcc340e1dfb3.docx', '2023-12-18 21:30:23'),
(530, '', 'default_username', '', '', 'sfasfafa', 'docuvault0@gmail.com', '../upload/encrypted_file/144726a06960ad7c430d5f631aa28ff7.docx', '2023-12-19 00:18:12'),
(531, '', 'default_username', 'Mark Palanog', '', 'hehe', 'cfpamore1224@gmail.com', '../upload/encrypted_file/72e2c176aafb0bc666e243e20425b2ed.docx', '2023-12-19 21:30:10'),
(532, '', 'default_username', 'John', '', 'faAASF', 'm4rlreynan@gmail.com', '../upload/encrypted_file/c989a55b9e0f2edad8dbd5bedcd162a9.docx', '2023-12-19 21:51:50'),
(533, '', 'default_username', 'John', '', 'egeg', 'm4rlreynan@gmail.com', '../upload/encrypted_file/a574056bb5f3cf7c61824810d8e6d7f4.docx', '2023-12-19 21:52:43'),
(534, '', 'default_username', 'Alyssa Ras', '', 'ge', 'daemontzyy@gmail.com', '../upload/encrypted_file/e2ab6a56b70df26a40e2bf494326bce6.docx', '2023-12-19 23:50:51'),
(535, '', 'default_username', 'Alyssa Ras', '', 'geegeg', 'daemontzyy@gmail.com', '../upload/encrypted_file/412fd26f80deba4f078cde5c5319450b.docx', '2023-12-19 23:52:44'),
(536, '', 'default_username', 'Alyssa Ras', '', 'design', 'daemontzyy@gmail.com', '../upload/encrypted_file/1a213df450d828a3b07eead4d45c53bf.docx', '2023-12-20 09:42:43'),
(537, '', 'default_username', 'Alyssa Ras', '', 'heh!', 'daemontzyy@gmail.com', '../upload/encrypted_file/093f0fe9a8cd54e44567028efcaabbd9.docx', '2024-01-05 09:53:21'),
(538, '', 'default_username', 'John', '', 'hays', 'm4rlreynan@gmail.com', '../upload/encrypted_file/be7b609d776b00bb6d805b3a051aeb07.docx', '2024-01-05 10:11:34'),
(539, '', 'default_username', 'Alyssa Ras', '', 'gegegege', 'daemontzyy@gmail.com', '../upload/encrypted_file/d5c58652fcf070c699b3e509566cfb44.docx', '2024-01-05 10:22:45'),
(540, '', 'default_username', 'Alyssa Ras', '', 'ah,', 'daemontzyy@gmail.com', '../upload/encrypted_file/c6dc262bab18895d1a1f765a124eaec2.docx', '2024-01-05 10:30:16'),
(541, '', 'default_username', 'Alyssa Ras', '', 'hello gd', 'daemontzyy@gmail.com', '../upload/encrypted_file/ccd05c010433cb2589367babfa318d5b.pdf', '2024-01-05 10:39:01'),
(542, '', 'default_username', 'Alyssa Ras', '', 'hello gd', 'daemontzyy@gmail.com', '../upload/encrypted_file/b5037ddc4a88f398046f0d713a6ab6f6.docx', '2024-01-05 10:40:21'),
(543, '', 'default_username', 'Alyssa Ras', '', 'hello gd', 'daemontzyy@gmail.com', '../upload/encrypted_file/c59947ca0f8db36864284da8ca5139db.docx', '2024-01-05 10:41:49'),
(544, '', 'default_sender_name', 'Alyssa Ras', '', 'hello gd', 'daemontzyy@gmail.com', '../upload/encrypted_file/8d05f9d32b47dd0fd161131f37c3789a.docx', '2024-01-05 10:47:26'),
(545, '', 'default_sender_name', 'Alyssa Ras', '', 'egege', 'daemontzyy@gmail.com', '../upload/encrypted_file/43c634365856e446a2418d0804246f90.pdf', '2024-01-05 11:02:31'),
(546, '', 'default_sender_name', 'Alyssa Ras', '', 'maan ahhh', 'daemontzyy@gmail.com', '../upload/encrypted_file/d0234f64294f66bf52807ede7a13dfa6.pdf', '2024-01-05 11:08:43'),
(547, '', 'default_sender_name', 'John', '', 'saasffa', 'm4rlreynan@gmail.com', '../upload/encrypted_file/078ececd5b05e8d085ab22fde7359557.docx', '2024-01-05 11:16:03'),
(548, '', 'default_sender_name', 'Alyssa Ras', '', 'eee', 'daemontzyy@gmail.com', '../upload/encrypted_file/77ce0e7874190c681ed2110b01b20ab9.docx', '2024-01-05 11:21:24'),
(549, '', 'default_sender_name', 'John', '', 'fff', 'm4rlreynan@gmail.com', '../upload/encrypted_file/20e0e7290955561afe5e82bbf3f7fc18.docx', '2024-01-05 11:24:33'),
(550, '', 'default_sender_name', 'John', '', 'alyssa', 'm4rlreynan@gmail.com', '../upload/encrypted_file/0b80919209ee147027e9d20f82c27ba0.docx', '2024-01-05 16:19:52'),
(551, '', 'default_username', 'Alyssa Ras', '', 'hey', 'daemontzyy@gmail.com', '../upload/encrypted_file/b659379a1ef7b036a2322e89644194bd.docx', '2024-01-05 16:30:18'),
(552, '', 'default_username', 'John', '', 'aafasfasa', 'm4rlreynan@gmail.com', '../upload/encrypted_file/c8401964b6b0efaefad9a032ea41aee4.docx', '2024-01-05 16:47:19'),
(553, '', 'default_username', 'Alyssa Ras', '', 'no', 'daemontzyy@gmail.com', '../upload/encrypted_file/dd30d4e3572ef4da47325c9d78f28490.pdf', '2024-01-05 16:54:09'),
(554, '', 'default_username', 'Alyssa Ras', '', 'gegegegeeeeee', 'daemontzyy@gmail.com', '../upload/encrypted_file/f83b705e51c735a8e1a0a1829fc09373.docx', '2024-01-05 19:07:55'),
(555, '', 'default_username', 'Alyssa Ras', '', 'mamamo!', 'daemontzyy@gmail.com', '../upload/encrypted_file/3a36e0aacd0470f153b60becff6fdcf4.docx', '2024-01-05 19:28:25'),
(556, '', 'default_username', 'Alyssa Ras', '', 'ge lang', 'daemontzyy@gmail.com', '../upload/encrypted_file/59c97da54b6d7ab342b565422dcc8fe7.docx', '2024-01-05 19:56:34'),
(557, '', 'default_username', 'Alyssa Ras', '', 'yongmi', 'daemontzyy@gmail.com', '../upload/encrypted_file/5b4b4ba4f4f739edd33389633161d3ee.docx', '2024-01-05 20:07:48');

-- --------------------------------------------------------

--
-- Table structure for table `upload_file`
--

CREATE TABLE `upload_file` (
  `file_id` int(10) UNSIGNED NOT NULL,
  `name_file` varchar(255) NOT NULL,
  `sender_name` varchar(255) NOT NULL,
  `upload_time` datetime NOT NULL DEFAULT current_timestamp(),
  `file_content` varchar(255) NOT NULL,
  `last_modified` datetime NOT NULL DEFAULT current_timestamp(),
  `file_path` varchar(255) NOT NULL,
  `recipient_name` varchar(2555) NOT NULL,
  `recipient_email` varchar(255) NOT NULL,
  `file_extension` varchar(255) NOT NULL,
  `filesize` int(11) NOT NULL,
  `original_filename` varchar(255) DEFAULT NULL,
  `sender_id` int(11) DEFAULT NULL,
  `recipient_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL,
  `unique_id` int(255) NOT NULL,
  `fname` varchar(255) NOT NULL,
  `lname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `img` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_form`
--

CREATE TABLE `user_form` (
  `user_id` int(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `otp` varchar(255) NOT NULL,
  `verify_status` tinyint(2) NOT NULL DEFAULT 0 COMMENT '0=no,1=yes',
  `user_type` varchar(255) DEFAULT 'user',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `failed_login_attempts` int(11) DEFAULT 0,
  `locked_until` datetime DEFAULT NULL,
  `otp_expiry` datetime DEFAULT current_timestamp(),
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_expiry` datetime DEFAULT NULL,
  `status` enum('enabled','disabled') NOT NULL DEFAULT 'enabled'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_form`
--

INSERT INTO `user_form` (`user_id`, `name`, `email`, `password`, `otp`, `verify_status`, `user_type`, `created_at`, `failed_login_attempts`, `locked_until`, `otp_expiry`, `reset_token`, `reset_expiry`, `status`) VALUES
(207, 'Alyssa Ras', 'daemontzyy1@gmail.com', '$2y$10$Golq30UfvsE7hAeHLNffUO4e5KbQgxFVM76GB0R0Q675U8NGxNWEm', '455714', 1, 'admin', '2023-12-01 02:15:06', 0, '2024-01-22 23:46:10', '2024-02-05 01:38:34', NULL, NULL, 'enabled'),
(209, 'Marl Palanog', 'palanogbiboy@gmail.com', '$2y$10$GxxKrxzn1ndfA.FfysaNHennIQ7wCUZ8D5tmfYUkpIGJJclMVEZJ.', '277474', 1, 'user', '2023-12-03 05:33:40', 0, '2023-12-04 13:46:02', '2024-04-09 23:16:08', 'c92c0ef87e4e1b72aacd1627a660b18a104f1d36e9bae2ff73c448ece8b89800', '2024-02-02 00:47:34', 'enabled'),
(280, 'Mark Palanogg', 'cfpamore1224@gmail.com', '$2y$10$ewowN/u1GJirEJMlKq9Qr.PyTUdB8MsuG/8JlEowRWHzjlBECtYke', '695311', 0, 'user', '2024-01-29 14:30:00', 4, NULL, '2024-01-29 22:30:00', NULL, NULL, 'enabled'),
(284, 'Ben Tumbling', 'marl@gmail.com', '$2y$10$JXisp7dNyNQHc1ebhPRZeuLO/MExxLiPAOf.cMIw9mdnT1xe2XDIK', '523824', 1, 'user', '2024-01-29 15:45:40', 0, '2024-03-23 13:09:07', '2024-03-24 11:15:58', NULL, NULL, 'enabled'),
(306, 'Marl Palanog', 'docuvault1111@gmail.com', '$2y$10$jZ/kCEZIZ8XUQfnws39E.uDKpRB9grmn9M0FG95yGKgJdi9dvh5Nu', '603517', 1, 'user', '2024-02-03 03:15:24', 0, NULL, '2024-02-03 11:15:24', NULL, NULL, 'enabled'),
(307, 'biboy marl', 'docuvault0@gmail.com', '$2y$10$vhk/QbbEjkhR.TppAKgTh.ZnIF8w9cbiF9FfZeUqiJq4AOkfYyDtW', '605182', 1, 'user', '2024-02-03 03:22:24', 0, NULL, '2024-02-05 11:56:22', NULL, NULL, 'enabled'),
(308, 'Toki Boy', 'docuvault01@gmail.com', '$2y$10$kw1TTHmq9/tOVQIlNLolcuosObZYtINGtBL2tjMrLjT8DaP5VpfVG', '863693', 1, 'admin', '2024-02-03 06:51:55', 3, NULL, '2024-02-03 14:51:55', NULL, NULL, 'enabled'),
(309, 'Marl Reynan Palanog', 'daemontzyy@gmail.com', '$2y$10$WKJYd3bLiIviOXFdYqTwX.86xpfZKlHe08S9M6aXV7Lp56Zyikeqe', '555127', 1, 'user', '2024-02-04 17:34:50', 0, NULL, '2024-03-25 16:53:52', NULL, NULL, 'enabled'),
(311, 'Lys', 'test', '$2y$10$IXfHE3MS1lq8vPDGLaCCVOduV9hbNv7zISyLLsUgcv4/vSgeuOaBu', '231162', 1, 'admin', '2024-02-05 13:27:44', 0, NULL, '2024-02-05 21:57:23', NULL, NULL, 'enabled'),
(312, 'Wendel Corral', 'marlreynan.palanog@antiquespride.edu.ph', '$2y$10$Penr.P4k6caAlpm6qeyOyerYJx8TcmcemZJTv/7YH/P.Vw3lKoQPC', '655519', 1, 'admin', '2024-02-05 13:53:50', 0, '2024-04-06 12:08:26', '2024-04-10 20:46:04', NULL, NULL, 'enabled'),
(313, 'Dodge', 'marl123@gmail.com', '$2y$10$5MwVogOIBixjpwilhLlZPu7M4M/Juu4ZRGbtEiCiBWmb6bggI72By', '436043', 1, 'user', '2024-03-23 16:38:45', 0, NULL, '2024-03-24 00:38:45', NULL, NULL, 'enabled'),
(314, 'Ipog Pogi', 'm4rlreynan@gmail.com', '$2y$10$/yZol9Vdw0FbpNvSMQF9y.MoJ/mlXUdqA49q9W3Pvd1S4vVhzBpJu', '152498', 1, 'user', '2024-03-24 03:02:16', 4, NULL, '2024-03-25 16:54:40', NULL, NULL, 'enabled'),
(315, 'Albert Anthony F. Polong', 'polongalbert243@gmail.com', '$2y$10$stgPG55HPikxR7AhbNLCJu8qgW0L9mn6St7VOYA7105yvf4z.I0Ke', '299321', 1, 'user', '2024-03-25 05:09:02', 0, NULL, '2024-03-25 13:09:02', NULL, NULL, 'enabled'),
(316, 'rina', 'nrinabelle@gmail.com', '$2y$10$ekKO0xDpebxgeTPPd2F.6.N8gcIk4ehB1F2/M7FthF1QuAGZ34vYu', '850057', 1, 'user', '2024-03-25 06:06:20', 1, NULL, '2024-03-25 14:22:47', NULL, NULL, 'enabled'),
(317, 'Test', 'Test@gmail.com', '$2y$10$PyRHNvxrCJM0BR0zeD6WS.UYhwczzrgbO4ipf4z8xBxicCS5LQ7Au', '460244', 0, 'user', '2024-04-06 05:40:23', 0, NULL, '2024-04-06 13:40:23', NULL, NULL, 'enabled'),
(318, 'Johny Deaf', 'johnydeaf@gmail.com', '$2y$10$W.2qoCHw.RB/Uy9L3wdNluqYHjbZqR/XePzWlhsddz4KQGHtzVA/m', '728646', 1, 'user', '2024-04-06 05:55:47', 0, NULL, '2024-04-06 14:02:23', NULL, NULL, 'enabled'),
(319, 'Alyssa Rasaaaa', 'm4rlreynan123@gmail.com', '$2y$10$wxusVWOSjmDCEpx.Fosi7ufHnuGfuCdLN4BR3E1nmy/Xo9iDkHHXi', '138364', 1, 'user', '2024-04-06 06:30:11', 0, NULL, '2024-04-06 14:30:11', NULL, NULL, 'enabled');

-- --------------------------------------------------------

--
-- Table structure for table `user_logs`
--

CREATE TABLE `user_logs` (
  `log_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `login_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `logout_time` datetime DEFAULT NULL,
  `status` enum('online','offline') DEFAULT 'offline',
  `action_id` int(11) DEFAULT NULL,
  `action_description` varchar(255) DEFAULT NULL,
  `status_symbol` varchar(1) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `current_action` varchar(255) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `toggle_status` enum('enabled','disabled') NOT NULL DEFAULT 'enabled'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_logs`
--

INSERT INTO `user_logs` (`log_id`, `user_id`, `login_time`, `logout_time`, `status`, `action_id`, `action_description`, `status_symbol`, `name`, `current_action`, `timestamp`, `toggle_status`) VALUES
(182, 312, '2024-04-08 13:12:45', '2024-04-08 21:12:52', 'offline', NULL, NULL, '○', 'Wendel Corral', 'Logged In', '2024-04-08 13:12:45', 'enabled'),
(183, 312, '2024-04-08 13:20:00', '2024-04-08 21:21:07', 'offline', NULL, NULL, '○', 'Wendel Corral', 'Putting Wrong Credentials!', '2024-04-08 13:20:00', 'enabled'),
(184, 312, '2024-04-08 13:20:31', '2024-04-08 21:21:07', 'offline', NULL, NULL, '○', 'Wendel Corral', 'Logged In', '2024-04-08 13:20:31', 'enabled'),
(185, 312, '2024-04-08 13:22:06', '2024-04-08 21:34:10', 'offline', NULL, NULL, '○', 'Wendel Corral', 'Logged In', '2024-04-08 13:22:06', 'enabled'),
(186, 312, '2024-04-08 13:36:59', '2024-04-08 21:37:07', 'offline', NULL, NULL, '○', 'Wendel Corral', 'Logged In', '2024-04-08 13:36:59', 'enabled'),
(187, 312, '2024-04-08 13:37:43', '2024-04-08 21:40:06', 'offline', NULL, NULL, '○', 'Wendel Corral', 'Logged In', '2024-04-08 13:37:43', 'enabled'),
(188, 312, '2024-04-08 13:42:03', '2024-04-08 21:43:24', 'offline', NULL, NULL, '○', 'Wendel Corral', 'Logged In', '2024-04-08 13:42:03', 'enabled'),
(189, 312, '2024-04-08 13:44:26', '2024-04-08 21:47:38', 'offline', NULL, NULL, '○', 'Wendel Corral', 'Logged In', '2024-04-08 13:44:26', 'enabled'),
(190, 312, '2024-04-08 13:48:05', '2024-04-08 21:48:08', 'offline', NULL, NULL, '○', 'Wendel Corral', 'Logged In', '2024-04-08 13:48:05', 'enabled'),
(191, 209, '2024-04-08 13:49:16', '2024-04-08 21:49:20', 'offline', NULL, NULL, '○', 'Marl Palanog', 'Logged In', '2024-04-08 13:49:16', 'enabled'),
(192, 312, '2024-04-08 13:49:59', '2024-04-08 23:25:47', 'offline', NULL, NULL, '○', 'Wendel Corral', 'Logged In', '2024-04-08 13:49:59', 'enabled'),
(193, 312, '2024-04-08 14:11:40', '2024-04-08 23:25:47', 'offline', NULL, NULL, '○', 'Wendel Corral', 'Logged In', '2024-04-08 14:11:40', 'enabled'),
(194, 209, '2024-04-08 14:18:30', '2024-04-09 17:46:28', 'offline', NULL, NULL, '○', 'Marl Palanog', 'Putting Wrong Credentials!', '2024-04-08 14:18:30', 'enabled'),
(195, 209, '2024-04-08 14:18:54', '2024-04-09 17:46:28', 'offline', NULL, NULL, '○', 'Marl Palanog', 'Logged In', '2024-04-08 14:18:54', 'enabled'),
(196, 312, '2024-04-08 14:50:51', '2024-04-08 23:25:47', 'offline', NULL, 'Wendel Corral sent a message to Marl Palanog', '○', 'Wendel Corral', 'Send Message', '2024-04-08 08:50:51', 'enabled'),
(197, 312, '2024-04-08 15:51:24', '2024-04-09 11:17:09', 'offline', NULL, NULL, '○', 'Wendel Corral', 'Logged In', '2024-04-08 15:51:24', 'enabled'),
(198, 312, '2024-04-08 16:04:47', '2024-04-09 11:17:09', 'offline', NULL, NULL, '○', 'Wendel Corral', 'Logged In', '2024-04-08 16:04:47', 'enabled'),
(199, 312, '2024-04-08 17:11:10', '2024-04-09 11:17:09', 'offline', NULL, NULL, '○', 'Wendel Corral', 'Logged In', '2024-04-08 17:11:10', 'enabled'),
(200, 312, '2024-04-09 02:41:35', '2024-04-09 11:17:09', 'offline', NULL, NULL, '○', 'Wendel Corral', 'Logged In', '2024-04-09 02:41:35', 'enabled'),
(201, 312, '2024-04-09 02:59:31', '2024-04-09 11:17:09', 'offline', NULL, ' Wendel Corral uploaded a file with title: gege_v1', '○', 'Wendel Corral', 'Upload', '2024-04-08 20:59:31', 'enabled'),
(202, 312, '2024-04-09 03:01:45', '2024-04-09 11:17:09', 'offline', NULL, ' Wendel Corral uploaded a file with title: fe_v2', '○', 'Wendel Corral', 'Upload', '2024-04-08 21:01:45', 'enabled'),
(203, 312, '2024-04-09 03:06:47', '2024-04-09 11:17:09', 'offline', NULL, NULL, '○', 'Wendel Corral', 'Logged In', '2024-04-09 03:06:47', 'enabled'),
(204, 312, '2024-04-09 03:17:36', '2024-04-09 17:04:42', 'offline', NULL, NULL, '○', 'Wendel Corral', 'Logged In', '2024-04-09 03:17:36', 'enabled'),
(205, 312, '2024-04-09 03:25:09', '2024-04-09 17:04:42', 'offline', NULL, ' Wendel Corral uploaded a file with title: gege_v2', '○', 'Wendel Corral', 'Upload', '2024-04-08 21:25:09', 'enabled'),
(206, 312, '2024-04-09 03:26:30', '2024-04-09 17:04:42', 'offline', NULL, ' Wendel Corral uploaded a file with title: HI_v3', '○', 'Wendel Corral', 'Upload', '2024-04-08 21:26:30', 'enabled'),
(207, 312, '2024-04-09 03:29:39', '2024-04-09 17:04:42', 'offline', NULL, ' Wendel Corral uploaded a file with title: ojt_v1', '○', 'Wendel Corral', 'Upload', '2024-04-08 21:29:39', 'enabled'),
(208, 312, '2024-04-09 03:32:04', '2024-04-09 17:04:42', 'offline', NULL, ' Wendel Corral uploaded a file with title: 1_v1', '○', 'Wendel Corral', 'Upload', '2024-04-08 21:32:04', 'enabled'),
(209, 312, '2024-04-09 03:33:27', '2024-04-09 17:04:42', 'offline', NULL, ' Wendel Corral uploaded a file with title: Certificates_v14', '○', 'Wendel Corral', 'Upload', '2024-04-08 21:33:27', 'enabled'),
(210, 312, '2024-04-09 03:36:04', '2024-04-09 17:04:42', 'offline', NULL, ' Wendel Corral uploaded a file with title: peos_v1', '○', 'Wendel Corral', 'Upload', '2024-04-08 21:36:04', 'enabled'),
(211, 312, '2024-04-09 03:37:32', '2024-04-09 17:04:42', 'offline', NULL, ' Wendel Corral uploaded a file with title: wara_v1', '○', 'Wendel Corral', 'Upload', '2024-04-08 21:37:32', 'enabled'),
(212, 312, '2024-04-09 03:40:02', '2024-04-09 17:04:42', 'offline', NULL, ' Wendel Corral uploaded a file with title: DOH_v2', '○', 'Wendel Corral', 'Upload', '2024-04-08 21:40:02', 'enabled'),
(213, 312, '2024-04-09 03:42:44', '2024-04-09 17:04:42', 'offline', NULL, ' Wendel Corral uploaded a file with title: HI_v4', '○', 'Wendel Corral', 'Upload', '2024-04-08 21:42:44', 'enabled'),
(214, 312, '2024-04-09 03:43:35', '2024-04-09 17:04:42', 'offline', NULL, ' Wendel Corral uploaded a file with title: FDd_v1', '○', 'Wendel Corral', 'Upload', '2024-04-08 21:43:35', 'enabled'),
(215, 312, '2024-04-09 03:43:59', '2024-04-09 17:04:42', 'offline', NULL, ' Wendel Corral uploaded a file with title: SAFASF_v1', '○', 'Wendel Corral', 'Upload', '2024-04-08 21:43:59', 'enabled'),
(216, 312, '2024-04-09 03:44:12', '2024-04-09 17:04:42', 'offline', NULL, ' Wendel Corral uploaded a file with title: FASFSA_v1', '○', 'Wendel Corral', 'Upload', '2024-04-08 21:44:12', 'enabled'),
(217, 312, '2024-04-09 03:44:24', '2024-04-09 17:04:42', 'offline', NULL, ' Wendel Corral uploaded a file with title: VSAFSA_v1', '○', 'Wendel Corral', 'Upload', '2024-04-08 21:44:24', 'enabled'),
(218, 312, '2024-04-09 03:45:03', '2024-04-09 17:04:42', 'offline', NULL, ' Wendel Corral uploaded a file with title: play_v1', '○', 'Wendel Corral', 'Upload', '2024-04-08 21:45:03', 'enabled'),
(219, 312, '2024-04-09 03:47:47', '2024-04-09 17:04:42', 'offline', NULL, ' Wendel Corral uploaded a file with title: afasf_v1', '○', 'Wendel Corral', 'Upload', '2024-04-08 21:47:47', 'enabled'),
(220, 312, '2024-04-09 03:50:27', '2024-04-09 17:04:42', 'offline', NULL, ' Wendel Corral uploaded a file with title: check_v1', '○', 'Wendel Corral', 'Upload', '2024-04-08 21:50:27', 'enabled'),
(221, 312, '2024-04-09 04:02:22', '2024-04-09 17:04:42', 'offline', NULL, ' Wendel Corral uploaded a file with title: HI_v5', '○', 'Wendel Corral', 'Upload', '2024-04-08 22:02:22', 'enabled'),
(222, 312, '2024-04-09 04:10:19', '2024-04-09 17:04:42', 'offline', NULL, ' Wendel Corral uploaded a file with title: fge_v1', '○', 'Wendel Corral', 'Upload', '2024-04-08 22:10:19', 'enabled'),
(223, 312, '2024-04-09 04:22:34', '2024-04-09 17:04:42', 'offline', NULL, ' Wendel Corral uploaded a file with title: gege_v3', '○', 'Wendel Corral', 'Upload', '2024-04-08 22:22:34', 'enabled'),
(224, 312, '2024-04-09 04:24:24', '2024-04-09 17:04:42', 'offline', NULL, ' Wendel Corral uploaded a file with title: gege_v4', '○', 'Wendel Corral', 'Upload', '2024-04-08 22:24:24', 'enabled'),
(225, 312, '2024-04-09 04:42:46', '2024-04-09 17:04:42', 'offline', NULL, 'Wendel Corral replied to a message of ', '○', 'Wendel Corral', 'Replied to message', '2024-04-09 04:42:46', 'enabled'),
(226, 209, '2024-04-09 09:05:09', '2024-04-09 17:46:28', 'offline', NULL, NULL, '○', 'Marl Palanog', 'Logged In', '2024-04-09 09:05:09', 'enabled'),
(227, 209, '2024-04-09 09:16:29', '2024-04-09 17:46:28', 'offline', NULL, ' Marl Palanog uploaded a file with title: gege_v5', '○', 'Marl Palanog', 'Upload', '2024-04-09 03:16:29', 'enabled'),
(228, 209, '2024-04-09 09:17:13', '2024-04-09 17:46:28', 'offline', NULL, ' Marl Palanog uploaded a file with title: Certificates_v15', '○', 'Marl Palanog', 'Upload', '2024-04-09 03:17:13', 'enabled'),
(229, 209, '2024-04-09 09:21:26', '2024-04-09 17:46:28', 'offline', NULL, ' Marl Palanog uploaded a file with title: HI_v6', '○', 'Marl Palanog', 'Upload', '2024-04-09 03:21:26', 'enabled'),
(230, 209, '2024-04-09 09:29:55', '2024-04-09 17:46:28', 'offline', NULL, 'Marl Palanog sent a message to Marl Palanog', '○', 'Marl Palanog', 'Send Message', '2024-04-09 03:29:55', 'enabled'),
(231, 209, '2024-04-09 09:30:11', '2024-04-09 17:46:28', 'offline', NULL, 'Marl Palanog sent a message to Mark Palanogg', '○', 'Marl Palanog', 'Send Message', '2024-04-09 03:30:11', 'enabled'),
(232, 312, '2024-04-09 09:32:24', '2024-04-09 17:48:00', 'offline', NULL, NULL, '○', 'Wendel Corral', 'Logged In', '2024-04-09 09:32:24', 'enabled'),
(233, 312, '2024-04-09 09:32:40', '2024-04-09 17:48:00', 'offline', NULL, 'Wendel Corral sent a message to Marl Palanog', '○', 'Wendel Corral', 'Send Message', '2024-04-09 03:32:40', 'enabled'),
(234, 312, '2024-04-09 09:33:49', '2024-04-09 17:48:00', 'offline', NULL, 'Wendel Corral sent a message to Marl Palanog', '○', 'Wendel Corral', 'Send Message', '2024-04-09 03:33:49', 'enabled'),
(235, 312, '2024-04-09 09:37:10', '2024-04-09 17:48:00', 'offline', NULL, 'Wendel Corral sent a message to Marl Palanog', '○', 'Wendel Corral', 'Send Message', '2024-04-09 03:37:10', 'enabled'),
(236, 209, '2024-04-09 09:37:33', '2024-04-09 17:46:28', 'offline', NULL, 'Marl Palanog replied to a message of Wendel Corral', '○', 'Marl Palanog', 'Replied to message', '2024-04-09 09:37:33', 'enabled'),
(237, 312, '2024-04-09 09:47:44', '2024-04-09 17:48:00', 'offline', NULL, NULL, '○', 'Wendel Corral', 'Putting Wrong Credentials!', '2024-04-09 09:47:44', 'enabled'),
(238, 312, '2024-04-09 09:47:47', '2024-04-09 17:48:00', 'offline', NULL, NULL, '○', 'Wendel Corral', 'Putting Wrong Credentials!', '2024-04-09 09:47:47', 'enabled'),
(239, 312, '2024-04-09 13:47:49', '2024-04-09 23:10:14', 'offline', NULL, NULL, '○', 'Wendel Corral', 'Putting Wrong Credentials!', '2024-04-09 13:47:49', 'enabled'),
(240, 312, '2024-04-09 13:48:45', '2024-04-09 23:10:14', 'offline', NULL, NULL, '○', 'Wendel Corral', 'Logged In', '2024-04-09 13:48:45', 'enabled'),
(241, 312, '2024-04-09 13:50:46', '2024-04-09 23:10:14', 'offline', NULL, 'Wendel Corral sent a message to Ben Tumbling', '○', 'Wendel Corral', 'Send Message', '2024-04-09 07:50:46', 'enabled'),
(242, 312, '2024-04-09 14:56:51', '2024-04-09 23:10:14', 'offline', NULL, NULL, '○', 'Wendel Corral', 'Logged In', '2024-04-09 14:56:51', 'enabled'),
(243, 312, '2024-04-09 15:10:30', '2024-04-09 23:14:35', 'offline', NULL, NULL, '○', 'Wendel Corral', 'Putting Wrong Credentials!', '2024-04-09 15:10:30', 'enabled'),
(244, 312, '2024-04-09 15:10:54', '2024-04-09 23:14:35', 'offline', NULL, NULL, '○', 'Wendel Corral', 'Logged In', '2024-04-09 15:10:54', 'enabled'),
(245, 209, '2024-04-09 15:11:20', NULL, 'online', NULL, NULL, '●', 'Marl Palanog', 'Logged In', '2024-04-09 15:11:20', 'enabled'),
(246, 209, '2024-04-09 15:11:59', NULL, 'offline', NULL, 'Marl Palanog sent a message to Wendel Corral', '', 'Marl Palanog', 'Send Message', '2024-04-09 09:11:59', 'enabled'),
(247, 209, '2024-04-09 15:11:59', NULL, 'offline', NULL, 'Marl Palanog sent a message to Wendel Corral', '', 'Marl Palanog', 'Send Message', '2024-04-09 09:11:59', 'enabled'),
(248, 312, '2024-04-10 05:17:54', '2024-04-10 13:46:18', 'offline', NULL, NULL, '○', 'Wendel Corral', 'Logged In', '2024-04-10 05:17:54', 'enabled'),
(249, 312, '2024-04-10 05:43:07', '2024-04-10 13:46:18', 'offline', NULL, ' Wendel Corral uploaded a file with title: img_v1', '○', 'Wendel Corral', 'Upload', '2024-04-09 23:43:07', 'enabled'),
(250, 312, '2024-04-10 12:37:52', '2024-04-10 20:40:00', 'offline', NULL, NULL, '○', 'Wendel Corral', 'Logged In', '2024-04-10 12:37:52', 'enabled'),
(251, 312, '2024-04-10 12:40:57', '2024-04-10 20:56:28', 'offline', NULL, NULL, '○', 'Wendel Corral', 'Putting Wrong Credentials!', '2024-04-10 12:40:57', 'enabled'),
(252, 312, '2024-04-10 12:42:01', '2024-04-10 20:56:28', 'offline', NULL, NULL, '○', 'Wendel Corral', 'Logged In', '2024-04-10 12:42:01', 'enabled'),
(253, 312, '2024-04-10 12:56:19', '2024-04-10 20:56:28', 'offline', NULL, ' Wendel Corral uploaded a file with title: gege_v6', '○', 'Wendel Corral', 'Upload', '2024-04-10 06:56:19', 'enabled');

-- --------------------------------------------------------

--
-- Table structure for table `user_messages`
--

CREATE TABLE `user_messages` (
  `message_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `recipient_id` int(11) NOT NULL,
  `message_type` enum('message','file') NOT NULL,
  `message_content` text DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_messages`
--

INSERT INTO `user_messages` (`message_id`, `sender_id`, `recipient_id`, `message_type`, `message_content`, `file_path`, `created_at`) VALUES
(307, 312, 309, 'message', 'can you give me the encryption key for this file consent_v1.docs.', 'messages/user_messages/', '2024-03-25 02:54:48'),
(308, 309, 312, 'message', '9a5161e69fce626e12b8f2951c0654a76b9bf971', 'messages/user_messages/', '2024-03-25 03:01:46'),
(309, 309, 312, 'message', 'can i get the key file named ?', 'messages/user_messages/', '2024-03-25 03:37:23'),
(310, 312, 309, 'message', '82c39bad18cd150f34024d0a5824b1685bb2728d', 'messages/user_messages/', '2024-03-25 03:38:31'),
(311, 316, 315, 'message', 'pst oi', 'messages/user_messages/', '2024-03-25 06:11:08'),
(312, 312, 316, 'message', '82c39bad18cd150f34024d0a5824b1685bb2728d    ', 'messages/user_messages/', '2024-03-25 06:16:53'),
(313, 312, 209, 'message', 'hi', 'messages/user_messages/', '2024-04-04 11:16:22'),
(314, 209, 312, 'message', 'hi', 'messages/user_messages/', '2024-04-04 11:17:29'),
(315, 312, 209, '', 'hi', NULL, '2024-04-04 11:24:59'),
(316, 209, 312, '', 'maiwan?', NULL, '2024-04-04 11:25:10'),
(317, 209, 312, 'message', 'good afternoon!', 'messages/user_messages/', '2024-04-05 07:58:53'),
(318, 209, 312, 'message', 'ge', 'messages/user_messages/', '2024-04-05 08:02:46'),
(319, 209, 312, 'message', 'hello\r\n', 'messages/user_messages/', '2024-04-05 08:04:47'),
(320, 312, 280, 'message', 'gege', 'messages/user_messages/', '2024-04-05 08:08:35'),
(321, 312, 209, 'message', 'hi', 'messages/user_messages/', '2024-04-05 08:24:31'),
(322, 209, 312, 'message', 'hi', 'messages/user_messages/', '2024-04-05 08:41:30'),
(323, 209, 312, 'message', 'last', 'messages/user_messages/', '2024-04-05 08:44:16'),
(324, 209, 312, 'message', 'hi', 'messages/user_messages/', '2024-04-05 08:45:32'),
(325, 209, 312, 'message', 'hi', 'messages/user_messages/', '2024-04-05 08:46:33'),
(326, 209, 312, 'message', 'ge', 'messages/user_messages/', '2024-04-05 08:47:13'),
(327, 312, 284, 'message', 'gwge', 'messages/user_messages/', '2024-04-05 08:47:56'),
(328, 312, 209, 'message', 'gooodafternoon!\r\n', 'messages/user_messages/', '2024-04-05 08:49:04'),
(329, 209, 312, 'message', 'marl', 'messages/user_messages/', '2024-04-05 12:07:33'),
(330, 209, 312, 'message', 'hey', 'messages/user_messages/', '2024-04-05 12:08:43'),
(331, 312, 209, 'message', 'test', 'messages/user_messages/', '2024-04-05 12:09:18'),
(332, 312, 308, 'message', 'hi', 'messages/user_messages/', '2024-04-05 12:15:51'),
(333, 318, 209, 'message', 's', 'messages/user_messages/', '2024-04-06 06:11:00'),
(334, 312, 315, 'message', 'Polong', 'messages/user_messages/', '2024-04-06 06:11:57'),
(335, 318, 308, 'message', 'ge', 'messages/user_messages/', '2024-04-06 06:15:13'),
(336, 318, 312, 'message', 'fw', 'messages/user_messages/', '2024-04-06 06:18:59'),
(337, 312, 318, '', 'what?', NULL, '2024-04-06 06:19:21'),
(338, 318, 312, '', 'war aah!!!!', NULL, '2024-04-06 06:24:05'),
(339, 318, 312, '', 'aahm', NULL, '2024-04-06 06:25:01'),
(340, 318, 312, '', 'wawers', NULL, '2024-04-06 06:27:08'),
(341, 312, 207, 'message', 'hi', 'messages/user_messages/', '2024-04-08 03:45:58'),
(342, 312, 207, 'message', 'ge', 'messages/user_messages/', '2024-04-08 03:47:31'),
(343, 312, 207, 'message', 'ge', 'messages/user_messages/', '2024-04-08 03:49:04'),
(344, 312, 207, 'message', 'ge', 'messages/user_messages/', '2024-04-08 03:49:04'),
(345, 312, 307, 'message', 'ji', 'messages/user_messages/', '2024-04-08 03:49:19'),
(346, 312, 307, 'message', 'hoy!', 'messages/user_messages/', '2024-04-08 03:51:15'),
(347, 312, 284, 'message', 'ge', 'messages/user_messages/', '2024-04-08 03:53:01'),
(348, 312, 209, 'message', 'hy=oy!', 'messages/user_messages/', '2024-04-08 06:46:36'),
(349, 312, 209, 'message', 'bebe', 'messages/user_messages/', '2024-04-08 06:47:02'),
(350, 312, 209, 'message', 'hi', 'messages/user_messages/', '2024-04-08 14:50:51'),
(351, 312, 309, '', 'fe', NULL, '2024-04-09 04:42:46'),
(352, 209, 209, 'message', 'HI BABE', 'messages/user_messages/', '2024-04-09 09:29:55'),
(353, 209, 280, 'message', 'HI', 'messages/user_messages/', '2024-04-09 09:30:11'),
(354, 312, 209, 'message', '', 'messages/user_messages/', '2024-04-09 09:32:40'),
(355, 312, 209, 'message', 'hu', 'messages/user_messages/', '2024-04-09 09:33:49'),
(356, 312, 209, 'message', 'goodafternoon', 'messages/user_messages/', '2024-04-09 09:37:10'),
(357, 209, 312, '', 'yes?', NULL, '2024-04-09 09:37:33'),
(358, 312, 284, 'message', 'hi', 'messages/user_messages/', '2024-04-09 13:50:46'),
(359, 209, 312, 'message', 'hi goodevening!', 'messages/user_messages/', '2024-04-09 15:11:59'),
(360, 209, 312, 'message', 'hi goodevening!', 'messages/user_messages/', '2024-04-09 15:11:59');

-- --------------------------------------------------------

--
-- Table structure for table `user_otp`
--

CREATE TABLE `user_otp` (
  `user_id` int(11) NOT NULL,
  `otp` varchar(6) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_otp`
--

INSERT INTO `user_otp` (`user_id`, `otp`, `created_at`) VALUES
(207, '345805', '2024-01-24 15:36:06'),
(207, '596532', '2024-01-24 15:39:05'),
(207, '214484', '2024-01-24 15:41:32'),
(207, '265571', '2024-01-24 15:42:03'),
(207, '870893', '2024-01-24 15:43:52'),
(207, '881901', '2024-01-24 15:47:23'),
(207, '845679', '2024-01-24 15:47:36'),
(207, '595863', '2024-01-24 15:49:55'),
(207, '513526', '2024-01-24 15:50:13'),
(207, '789996', '2024-01-24 15:52:48'),
(207, '428295', '2024-01-24 15:53:59'),
(207, '471837', '2024-01-24 15:56:06'),
(207, '172592', '2024-01-24 15:56:55'),
(207, '878774', '2024-01-24 15:59:44'),
(207, '678098', '2024-01-24 16:01:22'),
(207, '203185', '2024-01-24 16:01:46'),
(207, '408385', '2024-01-24 16:27:59'),
(207, '376490', '2024-01-24 16:28:20'),
(207, '125441', '2024-01-24 16:31:29'),
(207, '110753', '2024-01-24 16:32:59'),
(209, '796680', '2024-01-24 16:36:38'),
(207, '943735', '2024-01-24 16:41:07'),
(207, '557482', '2024-01-24 16:43:01'),
(207, '286794', '2024-01-24 16:43:42'),
(207, '515509', '2024-01-24 16:43:53'),
(207, '719084', '2024-01-25 02:26:14'),
(207, '948830', '2024-01-25 02:26:20'),
(207, '678502', '2024-01-25 02:32:22'),
(207, '548283', '2024-01-25 02:36:16'),
(207, '175222', '2024-01-25 02:47:18'),
(207, '327366', '2024-01-25 03:03:05'),
(207, '246947', '2024-01-25 03:03:43'),
(207, '557460', '2024-01-25 03:07:58'),
(207, '614186', '2024-01-25 03:10:37'),
(207, '180249', '2024-01-25 03:14:29');

-- --------------------------------------------------------

--
-- Table structure for table `version`
--

CREATE TABLE `version` (
  `version_id` int(11) NOT NULL,
  `value` varchar(100) NOT NULL,
  `document_id` int(11) NOT NULL,
  `date_uploaded` date NOT NULL,
  `download` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `version`
--

INSERT INTO `version` (`version_id`, `value`, `document_id`, `date_uploaded`, `download`) VALUES
(109, '1.1.1', 109, '2024-02-04', '0'),
(110, '1.1.1', 110, '2024-02-04', '0'),
(111, '1.0.1', 111, '2024-02-04', '0'),
(112, '1', 112, '2024-02-04', '0'),
(113, '1.1.1', 113, '2024-02-04', '0'),
(114, '1', 114, '2024-02-05', '0'),
(115, '1', 115, '2024-02-05', '0'),
(116, '1.1.1', 116, '2024-02-05', '0'),
(117, '1.1.1', 117, '2024-02-05', '0'),
(120, '1.1.1', 120, '2024-02-06', '0'),
(121, '1', 121, '2024-02-06', '0'),
(122, '1', 122, '2024-02-06', '1'),
(123, '1', 123, '2024-02-06', '0'),
(124, '1', 124, '2024-02-06', '0'),
(125, '1', 125, '2024-02-29', '2'),
(126, '1.1.1', 126, '2024-03-23', '0'),
(127, '1', 127, '2024-03-23', '0'),
(128, 'sfafasfasfas', 128, '2024-03-23', '0'),
(129, '', 129, '2024-03-23', '0'),
(130, '1', 132, '2024-03-23', '2'),
(131, '', 136, '2024-03-25', '0'),
(132, '', 137, '2024-03-25', '0'),
(133, '', 138, '2024-03-25', '1'),
(134, '', 139, '2024-03-25', '5'),
(135, '', 140, '2024-03-25', '3'),
(136, '', 141, '2024-03-25', '3'),
(137, '', 142, '2024-03-25', '2'),
(138, '', 143, '2024-03-25', '2'),
(139, '', 144, '2024-03-26', '0'),
(140, '', 145, '2024-04-04', '0'),
(141, '', 146, '2024-04-05', '0'),
(142, '', 147, '2024-04-05', '0'),
(143, '', 148, '2024-04-05', '0'),
(144, '', 149, '2024-04-05', '0'),
(145, '', 150, '2024-04-05', '0'),
(146, '', 151, '2024-04-05', '0'),
(147, '', 152, '2024-04-05', '0'),
(148, '', 153, '2024-04-05', '0'),
(149, '', 154, '2024-04-05', '0'),
(150, '', 155, '2024-04-05', '0'),
(151, '', 156, '2024-04-05', '0'),
(152, '', 157, '2024-04-05', '0'),
(153, '', 158, '2024-04-05', '0'),
(154, '', 159, '2024-04-05', '0'),
(155, '', 160, '2024-04-05', '0'),
(156, '', 161, '2024-04-05', '0'),
(157, '', 162, '2024-04-05', '0'),
(158, '', 163, '2024-04-05', '0'),
(159, '', 164, '2024-04-05', '0'),
(160, '', 165, '2024-04-05', '0'),
(161, '', 166, '2024-04-05', '0'),
(162, '', 167, '2024-04-05', '0'),
(163, '', 168, '2024-04-05', '0'),
(164, '', 169, '2024-04-05', '0'),
(165, '', 170, '2024-04-05', '0'),
(166, '', 171, '2024-04-05', '0'),
(167, '', 172, '2024-04-05', '0'),
(168, '', 173, '2024-04-05', '0'),
(169, '', 174, '2024-04-05', '0'),
(170, '', 175, '2024-04-05', '0'),
(171, '', 176, '2024-04-05', '0'),
(172, '', 177, '2024-04-05', '0'),
(173, '', 178, '2024-04-05', '0'),
(174, '', 179, '2024-04-05', '0'),
(175, '', 180, '2024-04-05', '0'),
(176, '', 181, '2024-04-05', '0'),
(177, '', 182, '2024-04-05', '0'),
(178, '', 183, '2024-04-05', '0'),
(179, '', 184, '2024-04-05', '0'),
(180, '', 185, '2024-04-05', '0'),
(181, '', 186, '2024-04-05', '0'),
(182, '', 187, '2024-04-05', '0'),
(183, '', 188, '2024-04-05', '0'),
(184, '', 189, '2024-04-05', '0'),
(185, '', 190, '2024-04-05', '0'),
(186, '', 191, '2024-04-05', '0'),
(187, '', 192, '2024-04-05', '0'),
(188, '', 193, '2024-04-05', '0'),
(189, '', 194, '2024-04-05', '0'),
(190, '', 195, '2024-04-05', '0'),
(191, '', 196, '2024-04-05', '0'),
(192, '', 197, '2024-04-05', '0'),
(193, '', 198, '2024-04-05', '0'),
(194, '', 199, '2024-04-05', '1'),
(195, '', 200, '2024-04-05', '0'),
(196, '', 201, '2024-04-08', '0'),
(197, '', 202, '2024-04-09', '0'),
(198, '', 203, '2024-04-09', '0'),
(199, '', 204, '2024-04-09', '0'),
(200, '', 205, '2024-04-09', '0'),
(201, '', 206, '2024-04-09', '0'),
(202, '', 207, '2024-04-09', '0'),
(203, '', 208, '2024-04-09', '0'),
(204, '', 209, '2024-04-09', '0'),
(205, '', 210, '2024-04-09', '0'),
(206, '', 211, '2024-04-09', '2'),
(207, '', 212, '2024-04-09', '0'),
(208, '', 213, '2024-04-09', '0'),
(209, '', 214, '2024-04-09', '0'),
(210, '', 215, '2024-04-09', '0'),
(211, '', 216, '2024-04-09', '0'),
(212, '', 217, '2024-04-09', '0'),
(213, '', 218, '2024-04-09', '0'),
(214, '', 219, '2024-04-09', '0'),
(215, '', 220, '2024-04-09', '0'),
(216, '', 221, '2024-04-09', '0'),
(217, '', 222, '2024-04-09', '0'),
(218, '', 223, '2024-04-09', '0'),
(219, '', 224, '2024-04-09', '0'),
(220, '', 225, '2024-04-09', '0'),
(221, '', 226, '2024-04-09', '0'),
(222, '', 227, '2024-04-10', '0'),
(223, '', 228, '2024-04-10', '0');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `document`
--
ALTER TABLE `document`
  ADD PRIMARY KEY (`document_id`),
  ADD KEY `document_user_id` (`user_id`),
  ADD KEY `fk_folder` (`folder_id`);

--
-- Indexes for table `encrypted_file`
--
ALTER TABLE `encrypted_file`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_name_file` (`name_file`),
  ADD KEY `idx_recipient` (`recipient`);

--
-- Indexes for table `encryption_keys`
--
ALTER TABLE `encryption_keys`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `files`
--
ALTER TABLE `files`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `folders`
--
ALTER TABLE `folders`
  ADD PRIMARY KEY (`folder_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `recipient_id` (`recipient_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `received_files`
--
ALTER TABLE `received_files`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `user_form`
--
ALTER TABLE `user_form`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `user_logs`
--
ALTER TABLE `user_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `action_id` (`action_id`);

--
-- Indexes for table `user_messages`
--
ALTER TABLE `user_messages`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `recipient_id` (`recipient_id`);

--
-- Indexes for table `user_otp`
--
ALTER TABLE `user_otp`
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `version`
--
ALTER TABLE `version`
  ADD PRIMARY KEY (`version_id`),
  ADD KEY `version_document_id` (`document_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `document`
--
ALTER TABLE `document`
  MODIFY `document_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=229;

--
-- AUTO_INCREMENT for table `encrypted_file`
--
ALTER TABLE `encrypted_file`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `encryption_keys`
--
ALTER TABLE `encryption_keys`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=159;

--
-- AUTO_INCREMENT for table `folders`
--
ALTER TABLE `folders`
  MODIFY `folder_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `received_files`
--
ALTER TABLE `received_files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=558;

--
-- AUTO_INCREMENT for table `user_form`
--
ALTER TABLE `user_form`
  MODIFY `user_id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=320;

--
-- AUTO_INCREMENT for table `user_logs`
--
ALTER TABLE `user_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=254;

--
-- AUTO_INCREMENT for table `user_messages`
--
ALTER TABLE `user_messages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=361;

--
-- AUTO_INCREMENT for table `version`
--
ALTER TABLE `version`
  MODIFY `version_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=224;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `document`
--
ALTER TABLE `document`
  ADD CONSTRAINT `document_user_id` FOREIGN KEY (`user_id`) REFERENCES `user_form` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_folder` FOREIGN KEY (`folder_id`) REFERENCES `document` (`document_id`) ON DELETE SET NULL;

--
-- Constraints for table `folders`
--
ALTER TABLE `folders`
  ADD CONSTRAINT `folders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`);

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`recipient_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `user_logs`
--
ALTER TABLE `user_logs`
  ADD CONSTRAINT `user_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user_form` (`user_id`),
  ADD CONSTRAINT `user_logs_ibfk_2` FOREIGN KEY (`action_id`) REFERENCES `user_actions` (`action_id`);

--
-- Constraints for table `user_messages`
--
ALTER TABLE `user_messages`
  ADD CONSTRAINT `user_messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `user_form` (`user_id`),
  ADD CONSTRAINT `user_messages_ibfk_2` FOREIGN KEY (`recipient_id`) REFERENCES `user_form` (`user_id`);

--
-- Constraints for table `user_otp`
--
ALTER TABLE `user_otp`
  ADD CONSTRAINT `user_otp_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user_form` (`user_id`);

--
-- Constraints for table `version`
--
ALTER TABLE `version`
  ADD CONSTRAINT `version_document_id` FOREIGN KEY (`document_id`) REFERENCES `document` (`document_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
