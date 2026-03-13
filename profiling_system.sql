-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 23, 2025 at 02:22 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `profiling_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `forms`
--

CREATE TABLE `forms` (
  `id` int(11) NOT NULL,
  `form_title` varchar(255) NOT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `forms`
--

INSERT INTO `forms` (`id`, `form_title`, `photo`, `updated_at`) VALUES
(3, 'Student Form', NULL, '2025-01-07 10:08:17');

-- --------------------------------------------------------

--
-- Table structure for table `form_fields`
--

CREATE TABLE `form_fields` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` enum('text','email','number','date','textarea') NOT NULL,
  `required` tinyint(1) NOT NULL DEFAULT 0,
  `photo` varchar(255) DEFAULT NULL,
  `form_id` int(11) NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `dropdown_choices` text DEFAULT NULL,
  `dropdown_options` text DEFAULT NULL,
  `choices` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `form_fields`
--

INSERT INTO `form_fields` (`id`, `name`, `type`, `required`, `photo`, `form_id`, `image_path`, `updated_at`, `dropdown_choices`, `dropdown_options`, `choices`) VALUES
(0, 'Student ID', 'number', 1, NULL, 0, NULL, NULL, NULL, NULL, NULL),
(0, 'Name', 'text', 1, NULL, 0, NULL, NULL, NULL, NULL, NULL),
(0, 'Email', 'email', 1, NULL, 0, NULL, NULL, NULL, NULL, NULL),
(0, 'Age', 'number', 1, NULL, 0, NULL, NULL, NULL, NULL, NULL),
(0, 'Birthdate', 'date', 1, NULL, 0, NULL, NULL, NULL, NULL, NULL),
(0, 'Course', 'text', 1, NULL, 0, NULL, NULL, NULL, NULL, NULL),
(0, 'Contact Number', 'number', 1, NULL, 0, NULL, NULL, NULL, NULL, NULL),
(0, 'Password', 'text', 1, NULL, 0, NULL, NULL, NULL, NULL, NULL),
(0, 'Message', 'textarea', 1, NULL, 0, NULL, NULL, NULL, NULL, NULL),
(0, 'something about yourself', 'text', 1, NULL, 0, NULL, NULL, NULL, NULL, NULL),
(0, 'First Name', 'text', 1, NULL, 3, NULL, NULL, NULL, NULL, NULL),
(0, 'Middle Name', 'text', 1, NULL, 3, NULL, NULL, NULL, NULL, NULL),
(0, 'Last Name', 'text', 1, NULL, 3, NULL, NULL, NULL, NULL, NULL),
(0, 'ID Number', 'number', 1, NULL, 3, NULL, NULL, NULL, NULL, NULL),
(0, 'Birthdate', 'date', 1, NULL, 3, NULL, NULL, NULL, NULL, NULL),
(0, 'Age', 'number', 1, NULL, 3, NULL, NULL, NULL, NULL, NULL),
(0, 'Gender', 'text', 1, NULL, 3, NULL, NULL, NULL, NULL, NULL),
(0, 'Civil Status', 'text', 1, NULL, 3, NULL, NULL, NULL, NULL, NULL),
(0, 'Student Status', 'text', 1, NULL, 3, NULL, NULL, NULL, NULL, NULL),
(0, 'Contact Number', 'number', 1, NULL, 3, NULL, NULL, NULL, NULL, NULL),
(0, 'Email', 'email', 1, NULL, 3, NULL, NULL, NULL, NULL, NULL),
(0, 'Hobby/Hobbies', 'textarea', 1, NULL, 3, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `form_responses`
--

CREATE TABLE `form_responses` (
  `id` int(11) NOT NULL,
  `form_id` int(11) NOT NULL,
  `response_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`response_data`)),
  `student_id` int(10) UNSIGNED NOT NULL,
  `is_submitted` tinyint(1) DEFAULT 0,
  `last_updated_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `form_responses`
--

INSERT INTO `form_responses` (`id`, `form_id`, `response_data`, `student_id`, `is_submitted`, `last_updated_at`) VALUES
(7, 3, '[{\"field_name\":\"First Name\",\"field_value\":\"Jerald\"},{\"field_name\":\"Middle Name\",\"field_value\":\"Yrog-irog\"},{\"field_name\":\"Last Name\",\"field_value\":\"Collamar\"},{\"field_name\":\"ID Number\",\"field_value\":\"5220105\"},{\"field_name\":\"Birthdate\",\"field_value\":\"2004-04-18\"},{\"field_name\":\"Age\",\"field_value\":\"20\"},{\"field_name\":\"Gender\",\"field_value\":\"Male\"},{\"field_name\":\"Civil Status\",\"field_value\":\"Single\"},{\"field_name\":\"Student Status\",\"field_value\":\"Regular\"},{\"field_name\":\"Contact Number\",\"field_value\":\"09317864716\"},{\"field_name\":\"Email\",\"field_value\":\"jerald.collamar@gmail.com\"},{\"field_name\":\"Hobby\\/Hobbies\",\"field_value\":\"Watching anime\\r\\nPlaying mobile games\\r\\nEating\\r\\nSometimes work out\"}]', 5220105, 0, '2025-01-22 15:28:07'),
(12, 3, '[{\"field_name\":\"First Name\",\"field_value\":\"Nash \"},{\"field_name\":\"Middle Name\",\"field_value\":\"Nicole\"},{\"field_name\":\"Last Name\",\"field_value\":\"Noel\"},{\"field_name\":\"ID Number\",\"field_value\":\"5220027\"},{\"field_name\":\"Birthdate\",\"field_value\":\"2025-01-22\"},{\"field_name\":\"Age\",\"field_value\":\"24\"},{\"field_name\":\"Gender\",\"field_value\":\"Male\"},{\"field_name\":\"Civil Status\",\"field_value\":\"Single\"},{\"field_name\":\"Student Status\",\"field_value\":\"Regular\"},{\"field_name\":\"Contact Number\",\"field_value\":\"0987654321\"},{\"field_name\":\"Email\",\"field_value\":\"nash@gmail.com\"},{\"field_name\":\"Hobby\\/Hobbies\",\"field_value\":\"nothing\"}]', 5220027, 0, '2025-01-22 16:57:09');

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `description` text NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('Pending','Approved','Rejected') DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`id`, `description`, `image`, `created_at`, `status`) VALUES
(2, 'Announcement!!!!\r\nFINALS WEEK\r\nJANUARY 6 TO JANUARY 13', '', '2025-01-03 14:22:10', 'Pending'),
(11, 'Summer Season is Coming!!!', 'uploads/wc1706527.jpg', '2025-01-08 02:50:34', 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `register`
--

CREATE TABLE `register` (
  `id` int(10) UNSIGNED DEFAULT NULL,
  `username` varchar(150) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `program` varchar(10) NOT NULL,
  `password` varchar(150) DEFAULT NULL,
  `status` varchar(20) NOT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `full_name` varchar(255) DEFAULT NULL,
  `user_type` varchar(50) DEFAULT 'student',
  `usertype` varchar(50) DEFAULT 'student',
  `profile_picture` varchar(255) DEFAULT NULL,
  `year_section` varchar(255) NOT NULL,
  `middle_name` varchar(255) DEFAULT NULL,
  `gender` enum('Male','Female') DEFAULT NULL,
  `address` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `register`
--

INSERT INTO `register` (`id`, `username`, `email`, `phone`, `program`, `password`, `status`, `first_name`, `last_name`, `full_name`, `user_type`, `usertype`, `profile_picture`, `year_section`, `middle_name`, `gender`, `address`) VALUES
(5220105, 'jerald', 'jerald.collamar@gmail.com', '09317864716', 'Day', '$2y$10$/xE92yxBY3kQD3oG6.Vf2uyLXB79OCP5AS4F1K682JTwn2HCmDrDG', 'Regular', 'Jerald ', 'Collamar ', 'Jerald  Yrog-irog Collamar', 'student', 'student', 'uploads/5220105_67905a8e7cf71.png', '3C', 'Yrog-irog', 'Male', 'Mangga,Tuburan,Cebu'),
(5220027, 'nash', 'nash@gmail.com', '09876543212', 'Day', '$2y$10$7/C8kC40AmQxWPvAl25kMunbJA3TO5AuGlxZo3Ixn5DHkSiLRy39q', 'Regular', 'Nash', 'Noel', 'Nash Nicole Noel', 'student', 'student', 'uploads/5220027_67905fa835976.jpeg', '3C', 'Nicole', 'Male', 'Agtugop,Asturias,Cebu'),
(5221234, 'marie', 'marie@gmail.com', '091234567887', 'Day', '$2y$10$XqG/dTk4ZXGaT1WWFUmRQOEBc6nJLar7DR8wTG.AO6HgR/5H71Ysu', 'Regular', 'Mariniel', 'Butaslac', 'Mariniel Orio Butaslac', 'student', 'student', NULL, '3C', 'Orio', 'Male', 'Kamansi,Tuburan,Cebu');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `age` int(11) NOT NULL,
  `password` varchar(255) NOT NULL,
  `is_approved` tinyint(1) DEFAULT 0,
  `phone` varchar(20) DEFAULT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `usertype` varchar(20) NOT NULL DEFAULT 'admin'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `age`, `password`, `is_approved`, `phone`, `first_name`, `last_name`, `usertype`) VALUES
(7654321, 'admin', 'prime.optimus@gmail.com', 0, '$2y$10$/LQJxGjpbhYU37PiELTQ9us0PGQ12tuy6NvoYwmPV4HcvOi.mxIye', 0, '09123454321', 'Prime', 'Optimus', 'admin');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `forms`
--
ALTER TABLE `forms`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `form_responses`
--
ALTER TABLE `form_responses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `form_id` (`form_id`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `forms`
--
ALTER TABLE `forms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `form_responses`
--
ALTER TABLE `form_responses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7654322;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `form_responses`
--
ALTER TABLE `form_responses`
  ADD CONSTRAINT `form_responses_ibfk_1` FOREIGN KEY (`form_id`) REFERENCES `forms` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
