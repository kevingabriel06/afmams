-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 30, 2025 at 01:58 PM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cpdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity`
--

CREATE TABLE `activity` (
  `activity_id` int(11) NOT NULL,
  `activity_title` varchar(255) NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `registration_deadline` datetime NOT NULL,
  `registration_fee` int(11) NOT NULL,
  `am_in` time NOT NULL,
  `am_out` time NOT NULL,
  `pm_in` time NOT NULL,
  `pm_out` time NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `activity_image` varchar(255) DEFAULT NULL,
  `privacy` varchar(255) NOT NULL,
  `org_id` int(11) DEFAULT NULL,
  `dept_id` int(11) DEFAULT NULL,
  `status` varchar(255) NOT NULL,
  `is_shared` varchar(255) NOT NULL DEFAULT 'No',
  `fines` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `activity`
--

INSERT INTO `activity` (`activity_id`, `activity_title`, `start_date`, `end_date`, `registration_deadline`, `registration_fee`, `am_in`, `am_out`, `pm_in`, `pm_out`, `description`, `activity_image`, `privacy`, `org_id`, `dept_id`, `status`, `is_shared`, `fines`) VALUES
(1, 'asdas', '2025-01-16 00:00:00', '2025-01-16 00:00:00', '0000-00-00 00:00:00', 0, '00:00:00', '00:00:00', '00:00:00', '00:00:00', NULL, 'download3.png', '', NULL, NULL, '', 'No', 0),
(2, 'Valentines Day v', '2024-01-16 00:00:00', '2025-01-16 00:00:00', '2025-01-16 00:00:00', 5, '12:00:00', '12:00:00', '12:00:00', '12:00:00', NULL, 'guests-line-green-and-black-icon-vector-removebg-preview_(1).png', '', NULL, NULL, '', 'No', 0),
(3, 'dasdsa', '2025-01-16 00:00:00', '2025-01-16 00:00:00', '2025-01-16 00:00:00', 5, '12:00:00', '12:00:00', '12:00:00', '12:00:00', 'gfgd', 'guests-line-green-and-black-icon-vector-removebg-preview.png', '', NULL, 1, '', 'No', 0),
(4, 'asdas', '2025-01-18 00:00:00', '2025-01-18 00:00:00', '2025-01-18 00:00:00', 5, '12:00:00', '12:00:00', '12:00:00', '12:00:00', 'dasdas', 'download31.png', 'on', NULL, NULL, '', 'No', 0),
(5, 'asdas', '2025-01-18 00:00:00', '2025-01-18 00:00:00', '0000-00-00 00:00:00', 5, '00:00:00', '00:00:00', '00:00:00', '00:00:00', 'dasdas', 'download32.png', 'on', NULL, NULL, '', 'No', 0),
(6, 'asdas', '2024-12-18 00:00:00', '2024-12-18 00:00:00', '2025-01-18 00:00:00', 5, '12:00:00', '12:00:00', '12:00:00', '12:00:00', 'oy', 'guests-line-green-and-black-icon-vector-removebg-preview1.png', 'on', 2, NULL, '', 'No', 0),
(7, 'sadas', '2025-01-18 00:00:00', '2025-01-18 00:00:00', '0000-00-00 00:00:00', 0, '00:00:00', '00:00:00', '00:00:00', '00:00:00', '', 'guests-line-green-and-black-icon-vector-removebg-preview2.png', 'on', NULL, NULL, '', 'No', 0),
(8, 'sadas', '2025-01-18 00:00:00', '2025-01-18 00:00:00', '2025-01-18 00:00:00', 5, '12:00:00', '12:00:00', '12:00:00', '12:00:00', 'asdasdas', 'guests-line-green-and-black-icon-vector-removebg-preview3.png', 'on', NULL, NULL, 'Ongoing', 'No', 0),
(9, 'sadas', '2025-08-18 00:00:00', '2025-01-18 00:00:00', '2025-01-18 00:00:00', 5, '12:00:00', '12:00:00', '12:00:00', '12:00:00', 'asdasdas', 'guests-line-green-and-black-icon-vector-removebg-preview4.png', 'on', NULL, NULL, 'Completed', 'No', 0),
(10, 'sadas', '2025-01-20 00:00:00', '2025-01-18 00:00:00', '2025-01-18 00:00:00', 5, '12:00:00', '12:00:00', '12:00:00', '12:00:00', 'fsgs', 'guests-line-green-and-black-icon-vector-removebg-preview5.png', 'private', 1, NULL, 'Upcoming', 'Yes', 0),
(11, 'sadas', '2025-01-19 00:00:00', '2025-01-19 00:00:00', '2025-01-19 00:00:00', 0, '12:00:00', '12:00:00', '12:00:00', '12:00:00', 'fsdfgsd', 'download33.png', 'private', 1, NULL, '', 'Yes', 0);

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `attendance_id` int(11) NOT NULL,
  `student_id` varchar(255) NOT NULL,
  `activity_id` int(11) NOT NULL,
  `am_in` timestamp NOT NULL DEFAULT current_timestamp(),
  `am_out` timestamp NOT NULL DEFAULT current_timestamp(),
  `pm_in` timestamp NOT NULL DEFAULT current_timestamp(),
  `pm_out` timestamp NOT NULL DEFAULT current_timestamp(),
  `attendance_status` varchar(255) NOT NULL,
  `photo_path` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `comment`
--

CREATE TABLE `comment` (
  `comment_id` int(11) NOT NULL,
  `student_id` varchar(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `parent_comment_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `comment`
--

INSERT INTO `comment` (`comment_id`, `student_id`, `post_id`, `parent_comment_id`, `content`, `created_at`) VALUES
(1, '21-03529', 3, 0, 'adfafdsafadsfasfdfdas', '2025-01-23 12:27:07'),
(4, '21-03529', 3, 0, 'asdsadasd', '2025-01-25 02:50:12'),
(5, '21-03529', 4, 0, 'asdasd', '2025-01-25 12:24:49'),
(6, '21-03529', 4, 0, 'sdfdsfdsfsdf', '2025-01-25 05:49:48'),
(7, '21-03529', 4, 0, 'adasdasd', '2025-01-25 05:50:59'),
(8, '21-03529', 4, 0, 'adasdasd', '2025-01-25 05:55:13'),
(9, '21-03529', 3, 0, 'asdas', '2025-01-25 06:03:38'),
(10, '21-03529', 3, 0, 'asdasd', '2025-01-25 06:05:36'),
(11, '21-03529', 3, 0, 'asdasd', '2025-01-25 06:07:27'),
(12, '21-03529', 16, 0, 'dadsad', '2025-01-25 19:59:32'),
(13, '21-03529', 19, 0, 'jasdasjdasl', '2025-01-27 18:54:14'),
(14, '21-03529', 18, 0, 'jnkjnu', '2025-01-27 19:59:45');

-- --------------------------------------------------------

--
-- Table structure for table `department`
--

CREATE TABLE `department` (
  `dept_id` int(11) NOT NULL,
  `dept_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `department`
--

INSERT INTO `department` (`dept_id`, `dept_name`) VALUES
(1, 'BS Information System'),
(2, 'BS Tourism Management'),
(3, 'BS Hospitality Management'),
(4, 'B Library and Information Science'),
(5, 'BS Education - Science'),
(6, 'BS Education - Mathematics'),
(7, 'BS Special Needs Education');

-- --------------------------------------------------------

--
-- Table structure for table `evaluation_responses`
--

CREATE TABLE `evaluation_responses` (
  `evaluation_response_id` int(11) NOT NULL,
  `form_id` int(11) NOT NULL,
  `student_id` varchar(255) NOT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `excuse_application`
--

CREATE TABLE `excuse_application` (
  `excuse_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `activity_id` int(11) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `document` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fines`
--

CREATE TABLE `fines` (
  `fines_id` int(11) NOT NULL,
  `student_id` varchar(255) NOT NULL,
  `activity_id` int(11) NOT NULL,
  `am_in` int(11) NOT NULL,
  `am_out` int(11) NOT NULL,
  `pm_in` int(11) NOT NULL,
  `pm_out` int(11) NOT NULL,
  `total_amount` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `formfields`
--

CREATE TABLE `formfields` (
  `form_fields_id` int(11) NOT NULL,
  `form_id` int(11) NOT NULL,
  `label` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `placeholder` varchar(255) DEFAULT NULL,
  `required` tinyint(1) NOT NULL DEFAULT 0,
  `order` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `formfields`
--

INSERT INTO `formfields` (`form_fields_id`, `form_id`, `label`, `type`, `placeholder`, `required`, `order`) VALUES
(1, 4, 'Question 1', '', 'Enter your answer', 1, 1),
(2, 4, 'Question 2', '', 'Enter detailed answer', 0, 2),
(3, 9, 'Question\n                Required\n              Short Answer', '', 'on', 1, 1),
(4, 9, 'Question\n                Required\n              ', '', 'on', 1, 2),
(5, 10, 'Question\n                Required\n              Short Answer', '', 'on', 1, 1),
(6, 10, 'Question\n                Required\n              ', '', 'on', 1, 2),
(7, 11, 'QuestionRequired', '', 'on', 1, 1),
(8, 12, 'QuestionRequired', 'text', 'on', 1, 1),
(9, 14, 'QuestionRequired', 'text', 'on', 0, 1),
(10, 15, 'QuestionRequired', 'text', 'on', 0, 1),
(11, 16, 'fdfa', 'short', 'sdfsdf', 1, 1),
(12, 16, 'name', 'textarea', '', 1, 2),
(13, 17, 'name', 'short', 'tft', 1, 1),
(14, 17, 'ftyty', 'textarea', '', 1, 2),
(15, 17, 'uhyug', 'rating', '', 1, 3);

-- --------------------------------------------------------

--
-- Table structure for table `forms`
--

CREATE TABLE `forms` (
  `form_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `activity_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `forms`
--

INSERT INTO `forms` (`form_id`, `title`, `description`, `activity_id`, `created_at`) VALUES
(4, 'Sample Form Title', 'This is a description of the form', 0, '2025-01-27 11:27:25'),
(5, 'afdf', 'afa', 0, '2025-01-27 11:49:20'),
(6, 'afdf', 'afa', 0, '2025-01-27 11:55:31'),
(7, 'afdf', 'afa', 0, '2025-01-27 11:55:32'),
(8, 'afdf', 'afa', 0, '2025-01-27 11:55:32'),
(9, 'adsfa', 'adsfasdf', 0, '2025-01-27 12:04:13'),
(10, 'adsfa', 'adsfasdf', 0, '2025-01-27 12:04:14'),
(11, 'afadfa', 'ada', 0, '2025-01-27 12:13:26'),
(12, 'afa', 'adfaf', 0, '2025-01-27 12:15:25'),
(14, 'sfg', 'sfdgdsfg', 0, '2025-01-27 12:31:37'),
(15, 'afadfa', 'asdas', 0, '2025-01-27 14:10:43'),
(16, 'adfads', 'adfasdf', 0, '2025-01-28 01:37:13'),
(17, 'afadfa', 'yfyf', 0, '2025-01-28 02:50:23');

-- --------------------------------------------------------

--
-- Table structure for table `likes`
--

CREATE TABLE `likes` (
  `like_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `student_id` varchar(255) NOT NULL,
  `liked_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `likes`
--

INSERT INTO `likes` (`like_id`, `post_id`, `student_id`, `liked_at`) VALUES
(23, 16, '21-03529', '2025-01-26 02:59:28'),
(28, 19, '21-03529', '2025-01-28 01:54:00'),
(31, 3, '21-03529', '2025-01-29 03:14:11');

-- --------------------------------------------------------

--
-- Table structure for table `organization`
--

CREATE TABLE `organization` (
  `org_id` int(11) NOT NULL,
  `org_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `organization`
--

INSERT INTO `organization` (`org_id`, `org_name`) VALUES
(1, 'JPCS-CCC Organization'),
(2, 'SPARK Organization'),
(3, 'ACTS Organization'),
(4, 'CHEF\'s Organization'),
(5, 'Math Wizard Organization'),
(6, 'Scinatics Club'),
(7, 'IDEAS Organization');

-- --------------------------------------------------------

--
-- Table structure for table `post`
--

CREATE TABLE `post` (
  `post_id` int(11) NOT NULL,
  `student_id` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `media` varchar(255) DEFAULT NULL,
  `dept_id` int(11) DEFAULT NULL,
  `org_id` int(11) DEFAULT NULL,
  `privacy` varchar(255) NOT NULL DEFAULT 'Private',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `like_count` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `post`
--

INSERT INTO `post` (`post_id`, `student_id`, `content`, `media`, `dept_id`, `org_id`, `privacy`, `created_at`, `like_count`) VALUES
(3, '21-03529', 'What is Lorem Ipsum?\nLorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.\n\nWhy do we use it?\nIt is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using \'Content here, content here\', making it look like readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for \'lorem ipsum\' will uncover many web sites still in their infancy. Various versions have evolved over the years, sometimes by accident, sometimes on purpose (injected humour and the like).\n\n', '', 1, 1, 'Public', '2025-01-29 03:14:11', 1),
(4, '21-03528', 'What is Lorem Ipsum?\nLorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type sp', '', 0, 2, 'Private', '2025-01-26 10:06:03', 0),
(16, '21-03529', 'asdasd', 'guests-line-green-and-black-icon-vector-removebg-preview.png', 0, 1, 'Public', '2025-01-26 02:59:28', 1),
(17, '21-03529', 'asdsadasd', NULL, 0, 1, 'Private', '2025-01-29 03:21:54', 0),
(18, '21-03529', 'fdafdfaf', 'hotel-booking-icon-vector-removebg-preview.png', 0, 1, 'Public', '2025-01-26 10:30:52', 0),
(19, '21-03529', 'fhahdkfhkjdshf', 'download1.png', 0, 1, 'Private', '2025-01-28 01:54:00', 1);

-- --------------------------------------------------------

--
-- Table structure for table `response_answer`
--

CREATE TABLE `response_answer` (
  `reponse_answer_id` int(11) NOT NULL,
  `evaluation_response_id` int(11) NOT NULL,
  `form_fields_id` int(11) NOT NULL,
  `answer` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_org`
--

CREATE TABLE `student_org` (
  `student_org_id` int(11) NOT NULL,
  `student_id` varchar(11) NOT NULL,
  `org_id` int(11) NOT NULL,
  `is_officer` varchar(255) DEFAULT 'No',
  `is_admin` varchar(255) NOT NULL DEFAULT 'No'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `student_org`
--

INSERT INTO `student_org` (`student_org_id`, `student_id`, `org_id`, `is_officer`, `is_admin`) VALUES
(1, '21-03529', 1, 'Yes', 'Yes'),
(2, '21-03529', 2, 'Yes', 'No');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `student_id` varchar(11) DEFAULT NULL,
  `first_name` varchar(255) NOT NULL,
  `middle_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) NOT NULL,
  `sex` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `dept_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `profile_pic` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'Student',
  `is_officer_dept` varchar(255) NOT NULL DEFAULT 'No',
  `is_admin` varchar(255) DEFAULT 'No',
  `date_created` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_updated` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `student_id`, `first_name`, `middle_name`, `last_name`, `sex`, `email`, `password`, `dept_id`, `name`, `profile_pic`, `role`, `is_officer_dept`, `is_admin`, `date_created`, `date_updated`) VALUES
(1, '21-03529', 'Kevin Gabriel', 'Lanuza', 'Maranan', '', '', 'kevin123', 1, 'Kevin Gabriel Maranan', 'pic.png', 'Admin', '', 'Yes', '2025-01-30 04:24:24', '2025-01-30 04:24:24'),
(2, '21-03528', '', NULL, '', '', '', '54321', 4, 'Ann Doe', '', 'Student', 'No', 'No', '2025-01-30 04:24:24', '2025-01-30 04:24:24'),
(3, '21-03789', '', NULL, '', '', '', '123456', 2, 'John Doe', '', 'Officer', 'Yes', 'Yes', '2025-01-30 04:24:24', '2025-01-30 04:24:24');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity`
--
ALTER TABLE `activity`
  ADD PRIMARY KEY (`activity_id`);

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`attendance_id`);

--
-- Indexes for table `comment`
--
ALTER TABLE `comment`
  ADD PRIMARY KEY (`comment_id`);

--
-- Indexes for table `department`
--
ALTER TABLE `department`
  ADD PRIMARY KEY (`dept_id`);

--
-- Indexes for table `evaluation_responses`
--
ALTER TABLE `evaluation_responses`
  ADD PRIMARY KEY (`evaluation_response_id`);

--
-- Indexes for table `excuse_application`
--
ALTER TABLE `excuse_application`
  ADD PRIMARY KEY (`excuse_id`);

--
-- Indexes for table `fines`
--
ALTER TABLE `fines`
  ADD PRIMARY KEY (`fines_id`);

--
-- Indexes for table `formfields`
--
ALTER TABLE `formfields`
  ADD PRIMARY KEY (`form_fields_id`),
  ADD KEY `form_id` (`form_id`);

--
-- Indexes for table `forms`
--
ALTER TABLE `forms`
  ADD PRIMARY KEY (`form_id`);

--
-- Indexes for table `likes`
--
ALTER TABLE `likes`
  ADD PRIMARY KEY (`like_id`),
  ADD UNIQUE KEY `post_id` (`post_id`);

--
-- Indexes for table `organization`
--
ALTER TABLE `organization`
  ADD PRIMARY KEY (`org_id`);

--
-- Indexes for table `post`
--
ALTER TABLE `post`
  ADD PRIMARY KEY (`post_id`);

--
-- Indexes for table `response_answer`
--
ALTER TABLE `response_answer`
  ADD PRIMARY KEY (`reponse_answer_id`);

--
-- Indexes for table `student_org`
--
ALTER TABLE `student_org`
  ADD PRIMARY KEY (`student_org_id`),
  ADD KEY `organization_id` (`org_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `dept_id` (`dept_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity`
--
ALTER TABLE `activity`
  MODIFY `activity_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `attendance_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `comment`
--
ALTER TABLE `comment`
  MODIFY `comment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `evaluation_responses`
--
ALTER TABLE `evaluation_responses`
  MODIFY `evaluation_response_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `excuse_application`
--
ALTER TABLE `excuse_application`
  MODIFY `excuse_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fines`
--
ALTER TABLE `fines`
  MODIFY `fines_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `formfields`
--
ALTER TABLE `formfields`
  MODIFY `form_fields_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `forms`
--
ALTER TABLE `forms`
  MODIFY `form_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `likes`
--
ALTER TABLE `likes`
  MODIFY `like_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `post`
--
ALTER TABLE `post`
  MODIFY `post_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `response_answer`
--
ALTER TABLE `response_answer`
  MODIFY `reponse_answer_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `student_org`
--
ALTER TABLE `student_org`
  MODIFY `student_org_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity`
--
ALTER TABLE `activity`
  ADD CONSTRAINT `fk_dept_id` FOREIGN KEY (`dept_id`) REFERENCES `department` (`dept_id`),
  ADD CONSTRAINT `fk_org_id` FOREIGN KEY (`org_id`) REFERENCES `organization` (`org_id`);

--
-- Constraints for table `formfields`
--
ALTER TABLE `formfields`
  ADD CONSTRAINT `formfields_ibfk_1` FOREIGN KEY (`form_id`) REFERENCES `forms` (`form_id`) ON DELETE CASCADE;

--
-- Constraints for table `student_org`
--
ALTER TABLE `student_org`
  ADD CONSTRAINT `student_org_ibfk_1` FOREIGN KEY (`org_id`) REFERENCES `organization` (`org_id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`dept_id`) REFERENCES `department` (`dept_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
