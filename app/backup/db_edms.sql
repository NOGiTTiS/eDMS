-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: db:3306
-- Generation Time: Nov 01, 2025 at 11:45 AM
-- Server version: 8.0.44
-- PHP Version: 8.3.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_edms`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `username` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `action` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `details` text COLLATE utf8mb4_general_ci,
  `ip_address` varchar(45) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` int NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `name`, `created_at`) VALUES
(1, 'ฝ่ายวิชาการ', '2025-10-28 09:54:05'),
(2, 'ฝ่ายงบประมาณ', '2025-10-28 09:54:05'),
(3, 'ฝ่ายบุคคล', '2025-10-28 09:54:05'),
(4, 'ฝ่ายทั่วไป', '2025-10-28 09:54:05'),
(5, 'ฝ่ายกิจการนักเรียน', '2025-10-28 09:54:05');

-- --------------------------------------------------------

--
-- Table structure for table `documents`
--

CREATE TABLE `documents` (
  `id` int NOT NULL,
  `registration_date` date NOT NULL DEFAULT (curdate()),
  `doc_registration_number` varchar(50) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'N/A',
  `doc_incoming_number` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `doc_date` date DEFAULT NULL,
  `doc_from` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `doc_to` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `doc_subject` text COLLATE utf8mb4_general_ci NOT NULL,
  `remarks` text COLLATE utf8mb4_general_ci,
  `status` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `created_by_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `document_files`
--

CREATE TABLE `document_files` (
  `id` int NOT NULL,
  `document_id` int NOT NULL,
  `file_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `original_file_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `uploaded_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `document_flow`
--

CREATE TABLE `document_flow` (
  `id` int NOT NULL,
  `document_id` int NOT NULL,
  `action_by_id` int NOT NULL,
  `action` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `comment` text COLLATE utf8mb4_general_ci,
  `signature_path` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `forward_to_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int NOT NULL,
  `setting_key` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `setting_value` varchar(255) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `setting_key`, `setting_value`) VALUES
(1, 'doc_registration_counter', '1998'),
(2, 'site_name', 'eDMS - ระบบสารบรรณอิเล็กทรอนิกส์'),
(3, 'site_logo', 'uploads/system/logo_1761908904_เตรียมอุดมศึกษา ภาคเหนือ.png'),
(4, 'site_favicon', 'uploads/system/favicon_1761898401_favicon-32x32.png'),
(5, 'theme_color', '#fe86f4'),
(6, 'doc_number_format', 'continuous'),
(30, 'bg_gradient_start', '#fbc2eb'),
(31, 'bg_gradient_end', '#a6c1ee'),
(36, 'site_copyright', '© 2025 S6N. All Rights Reserved.'),
(98, 'telegram_bot_token', '8261605253:AAGsh_Iu3r3wTfZ8mr8zxcsdB3IzSJG4jcU'),
(99, 'telegram_bot_username', 'TUNeDMS_bot');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `full_name` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `role` enum('central_admin','director','deputy_director','dept_admin','section_head') COLLATE utf8mb4_general_ci NOT NULL,
  `department_id` int DEFAULT NULL,
  `telegram_chat_id` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `full_name`, `role`, `department_id`, `telegram_chat_id`, `created_at`) VALUES
(1, 'admin', '$2y$10$jOrc6v/6.FmneHIKPXhZ1uxlSBJqgu1prdnQdpzaylmXCgcZ5l4u6', 'ผู้ดูแลระบบ', 'central_admin', NULL, NULL, '2025-10-28 05:59:52'),
(2, 'director', '$2y$10$9ULqg1OQONfSm2hCIFEUiuRJfLHqY5dO3JW/cxUkT6BV7MgzSbs6G', 'ผู้อำนวยการ', 'director', NULL, '7606578887', '2025-10-28 06:44:33'),
(3, 'central', '$2y$10$SDtCT/lJ9/JsTghlh.Oyi.fvZyvehqUoA5Ablu6EKBAsIheOVpo/S', 'ธุรการกลาง', 'central_admin', NULL, '7606578887', '2025-10-28 06:46:18'),
(4, 'academic_admin', '$2y$10$sYwN5v6RYAcBivq8akwxj.jgl/1FT5xoQPHAYpFXkrGYkVdg7Lk7G', 'ธุรการฝ่ายวิชาการ', 'dept_admin', 1, '7606578887', '2025-10-28 09:46:03'),
(5, 'budget_admin', '$2y$10$mpTHQvZQlhGazAg1Cr7H0OyOzCQnb4XHMnbe9HViLb4uum3h1Yatm', 'ธุรการฝ่ายงบประมาณ', 'dept_admin', 2, '7606578887', '2025-10-28 09:46:25'),
(6, 'hr_admin', '$2y$10$ZUVitwftceuELx7852NtKeojYMfyfc.IkAxxEs8bAIHZ1FD7jAwHi', 'ธุรการฝ่ายบุคคล', 'dept_admin', 3, '7606578887', '2025-10-28 09:46:59'),
(7, 'general_admin', '$2y$10$Xyu7zhYpaBt3LE/Cn3u0.e2qJu9EK2/TT2RJ9v/vEJtEzBe8zUtZO', 'ธุรการฝ่ายทั่วไป', 'dept_admin', 4, '7606578887', '2025-10-28 09:47:17'),
(8, 'student_affairs_admin', '$2y$10$d5uv/2xLQXNTTXNL9v6pd.1o4zjG76YIN/kZ2pypP82qn4nxXiRfe', 'ธุรการฝ่ายกิจการนักเรียน', 'dept_admin', 5, '7606578887', '2025-10-28 09:47:32'),
(9, 'deputy_academic', '$2y$10$ZxNP.MH4wbPZf6KfMomrmeZNptMbr4anmrJPNFnTrCSQcbLejEHxS', 'รองฯ ฝ่ายวิชาการ', 'deputy_director', 1, '7606578887', '2025-10-28 12:00:07'),
(10, 'deputy_budget', '$2y$10$jemdU2z6qlrM9Ch/K.6t6.jsX2//YDM2SwqjJgW4/ytzNZx1nZ69W', 'รองฯ ฝ่ายงบประมาณ', 'deputy_director', 2, '7606578887', '2025-10-28 12:03:06'),
(11, 'deputy_hr', '$2y$10$uRVXnsYFM04YEoyx3hJ03uLVRBuvoS1CylDQwuGecgBRHFl8zhp6W', 'รองฯ ฝ่ายบุคคล', 'deputy_director', 3, '7606578887', '2025-10-28 12:04:57'),
(12, 'deputy_general', '$2y$10$aRAZsJp79aeUzJsKcenmr.dsIu4Ce3lrWFedjTPuQ2jB1trPr6H6y', 'รองฯ ฝ่ายทั่วไป', 'deputy_director', 4, '7606578887', '2025-10-28 12:05:30'),
(13, 'deputy_student_affairs', '$2y$10$YhGE2WnDFjN8sOqq4sUuTud7bkjHERgKopN5wR/FXc.9d8PRqMw8G', 'รองฯ ฝ่ายกิจการนักเรียน', 'deputy_director', 5, '7606578887', '2025-10-28 12:06:47'),
(14, 'g.1', '$2y$10$AOheOYeEwK2FU..c5j4cTuHtIsgJiJiwp2/ivqgQbwE.HKp2yFUte', 'หัวหน้างาน ทั่วไป', 'section_head', 4, '7606578887', '2025-10-29 13:35:56'),
(16, 'ทดสอบ2', '$2y$10$lidr/al2lilkr1iXrxWiIO/CXAvRPhApoRht8cIP1L.y6ly2DscKa', 'ทดสอบ2', 'section_head', 4, NULL, '2025-11-01 04:23:53');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `documents`
--
ALTER TABLE `documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by_id` (`created_by_id`);

--
-- Indexes for table `document_files`
--
ALTER TABLE `document_files`
  ADD PRIMARY KEY (`id`),
  ADD KEY `document_id` (`document_id`);

--
-- Indexes for table `document_flow`
--
ALTER TABLE `document_flow`
  ADD PRIMARY KEY (`id`),
  ADD KEY `document_id` (`document_id`),
  ADD KEY `action_by_id` (`action_by_id`),
  ADD KEY `forward_to_id` (`forward_to_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `documents`
--
ALTER TABLE `documents`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `document_files`
--
ALTER TABLE `document_files`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `document_flow`
--
ALTER TABLE `document_flow`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=117;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `documents`
--
ALTER TABLE `documents`
  ADD CONSTRAINT `documents_ibfk_1` FOREIGN KEY (`created_by_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `document_files`
--
ALTER TABLE `document_files`
  ADD CONSTRAINT `document_files_ibfk_1` FOREIGN KEY (`document_id`) REFERENCES `documents` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `document_flow`
--
ALTER TABLE `document_flow`
  ADD CONSTRAINT `document_flow_ibfk_1` FOREIGN KEY (`document_id`) REFERENCES `documents` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `document_flow_ibfk_2` FOREIGN KEY (`action_by_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `document_flow_ibfk_3` FOREIGN KEY (`forward_to_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
