-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 10, 2025 at 04:37 AM
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

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `UpdateActivityStatus` ()   BEGIN
    -- Update Completed Activities
    UPDATE activity
    SET status = 'Completed'
    WHERE start_date < CURDATE();

    -- Update Ongoing Activities
    UPDATE activity
    SET status = 'Ongoing'
    WHERE start_date = CURDATE();

    -- Update Upcoming Activities
    UPDATE activity
    SET status = 'Upcoming'
    WHERE start_date > CURDATE();
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `activity`
--

CREATE TABLE `activity` (
  `activity_id` int(11) NOT NULL,
  `activity_title` varchar(255) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `registration_deadline` date DEFAULT NULL,
  `registration_fee` int(11) DEFAULT NULL,
  `am_in` time DEFAULT NULL,
  `am_out` time DEFAULT NULL,
  `pm_in` time DEFAULT NULL,
  `pm_out` time DEFAULT NULL,
  `am_in_cut` time DEFAULT NULL,
  `am_out_cut` time DEFAULT NULL,
  `pm_in_cut` time DEFAULT NULL,
  `pm_out_cut` time DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `activity_image` varchar(255) NOT NULL,
  `privacy` varchar(255) DEFAULT NULL,
  `org_id` int(11) DEFAULT NULL,
  `dept_id` int(11) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'Upcoming',
  `is_shared` varchar(255) NOT NULL DEFAULT 'No',
  `fines` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `activity`
--

INSERT INTO `activity` (`activity_id`, `activity_title`, `start_date`, `end_date`, `registration_deadline`, `registration_fee`, `am_in`, `am_out`, `pm_in`, `pm_out`, `am_in_cut`, `am_out_cut`, `pm_in_cut`, `pm_out_cut`, `description`, `activity_image`, `privacy`, `org_id`, `dept_id`, `status`, `is_shared`, `fines`, `created_at`, `updated_at`) VALUES
(1, 'Valentines Day MWA', '2025-02-14', '2025-02-14', '2025-02-10', 50, NULL, NULL, '14:00:00', '18:00:00', NULL, NULL, '14:20:00', '18:25:00', 'Valentines Celebration 1', '67a659cbd9d43_pexels-photo-5226950.jpeg', 'Public', 0, 0, 'Upcoming', 'Yes', 25, '2025-02-07 22:57:44', '2025-02-09 18:53:20'),
(2, 'Book Week - LTS 2024', '2025-02-07', '2025-02-07', '0000-00-00', 0, NULL, NULL, '14:00:00', '15:00:00', NULL, NULL, '14:15:00', '15:15:00', 'asd', 'pexels-photo-522695025.jpeg', 'Public', 2, NULL, 'Completed', 'No', 50, '2025-02-07 23:04:33', '2025-02-07 17:23:27'),
(3, 'Valentines Day v', '2025-02-08', '2025-02-08', '2025-02-07', 123, NULL, NULL, '13:00:00', '19:00:00', NULL, NULL, '13:15:00', '19:15:00', 'asdasdas', 'pexels-photo-522695026.jpeg', 'Private', 1, NULL, 'Completed', 'Yes', 25, '2025-02-07 23:08:53', '2025-02-09 04:13:37'),
(4, 'Sample Activity - SPARK', '2025-02-10', '2025-02-10', '2025-02-09', 50, '07:00:00', '07:00:00', '13:00:00', '19:00:00', '07:25:00', '12:25:00', '13:10:00', '19:40:00', 'Sample Description', 'pexels-photo-522695027.jpeg', 'Public', 0, 0, 'Ongoing', 'Yes', 50, '2025-02-08 04:01:28', '2025-02-09 17:42:07'),
(5, 'Sample Activity - SPARK 2', '2025-02-08', '2025-02-08', '0000-00-00', 50, '07:00:00', '11:00:00', NULL, NULL, '07:15:00', '11:15:00', NULL, NULL, 'HEHE', '5059155010_52d16236dc_w1.jpg', 'Private', 2, NULL, 'Completed', 'No', 50, '2025-02-08 15:33:16', '2025-02-08 16:05:02'),
(6, 'Sample Activity - SPARK 21', '2025-02-08', '2025-02-08', '0000-00-00', 50, '06:00:00', '12:01:00', NULL, NULL, '06:15:00', '12:15:00', NULL, NULL, 'fghgfhgfh', 'pexels-photo-522695028.jpeg', 'Public', 0, 0, 'Completed', 'No', 50, '2025-02-08 15:34:43', '2025-02-08 16:05:02'),
(7, 'Activity 1', '2025-02-10', '2025-02-10', '0000-00-00', 0, '13:00:00', '20:00:00', NULL, NULL, '13:15:00', '20:15:00', NULL, NULL, 'Sample Description', 'IMG_20240105_145605_0284.jpg', 'Private', 2, NULL, 'Ongoing', 'Yes', 25, '2025-02-09 00:07:57', '2025-02-09 17:42:07');

--
-- Triggers `activity`
--
DELIMITER $$
CREATE TRIGGER `updateCutOff` BEFORE INSERT ON `activity` FOR EACH ROW BEGIN
    -- Handle AM In
    IF NEW.am_in IS NULL THEN
        SET NEW.am_in_cut = NULL;  -- Set to NULL if 'am_in' is '00:00:00'
    ELSE
        SET NEW.am_in_cut = DATE_ADD(NEW.am_in, INTERVAL 15 MINUTE);  -- Add 15 minutes if 'am_in' is not '00:00:00'
    END IF;

    -- Handle AM Out
    IF NEW.am_out IS NULL  THEN
        SET NEW.am_out_cut = NULL;  -- Set to NULL if 'am_out' is '00:00:00'
    ELSE
        SET NEW.am_out_cut = DATE_ADD(NEW.am_out, INTERVAL 15 MINUTE);  -- Add 15 minutes if 'am_out' is not '00:00:00'
    END IF;

    -- Handle PM In
    IF NEW.pm_in IS NULL  THEN
        SET NEW.pm_in_cut = NULL;  -- Set to NULL if 'pm_in' is '00:00:00'
    ELSE
        SET NEW.pm_in_cut = DATE_ADD(NEW.pm_in, INTERVAL 15 MINUTE);  -- Add 15 minutes if 'pm_in' is not '00:00:00'
    END IF;

    -- Handle PM Out
    IF NEW.pm_out IS NULL  THEN
        SET NEW.pm_out_cut = NULL;  -- Set to NULL if 'pm_out' is '00:00:00'
    ELSE
        SET NEW.pm_out_cut = DATE_ADD(NEW.pm_out, INTERVAL 15 MINUTE);  -- Add 15 minutes if 'pm_out' is not '00:00:00'
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `update_activity_time_nulls` BEFORE INSERT ON `activity` FOR EACH ROW BEGIN
    IF NEW.am_in = '00:00:00' THEN
        SET NEW.am_in = NULL;
    END IF;

    IF NEW.am_out = '00:00:00' THEN
        SET NEW.am_out = NULL;
    END IF;

    IF NEW.pm_in = '00:00:00' THEN
        SET NEW.pm_in = NULL;
    END IF;

    IF NEW.pm_out = '00:00:00' THEN
        SET NEW.pm_out = NULL;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `update_time_if_updated` BEFORE UPDATE ON `activity` FOR EACH ROW BEGIN
    IF NEW.am_in = '00:00:00' THEN
        SET NEW.am_in = NULL;
    END IF;

    IF NEW.am_out = '00:00:00' THEN
        SET NEW.am_out = NULL;
    END IF;

    IF NEW.pm_in = '00:00:00' THEN
        SET NEW.pm_in = NULL;
    END IF;

    IF NEW.pm_out = '00:00:00' THEN
        SET NEW.pm_out = NULL;
    END IF;
    
    IF NEW.am_in_cut = '00:00:00' THEN
        SET NEW.am_in_cut = NULL;
    END IF;

    IF NEW.am_out_cut = '00:00:00' THEN
        SET NEW.am_out_cut = NULL;
    END IF;

    IF NEW.pm_in_cut = '00:00:00' THEN
        SET NEW.pm_in_cut = NULL;
    END IF;

    IF NEW.pm_out_cut = '00:00:00' THEN
        SET NEW.pm_out_cut = NULL;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `attendance_id` int(11) NOT NULL,
  `student_id` varchar(255) NOT NULL,
  `activity_id` int(11) NOT NULL,
  `am_in` timestamp NULL DEFAULT NULL,
  `am_out` timestamp NULL DEFAULT NULL,
  `pm_in` timestamp NULL DEFAULT NULL,
  `pm_out` timestamp NULL DEFAULT NULL,
  `attendance_status` varchar(255) NOT NULL,
  `photo_path` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`attendance_id`, `student_id`, `activity_id`, `am_in`, `am_out`, `pm_in`, `pm_out`, `attendance_status`, `photo_path`) VALUES
(1, '21-03528', 2, '2025-02-05 23:18:44', '2025-02-05 23:18:44', '2025-02-05 23:18:44', '2025-02-05 23:18:44', 'Present', ''),
(2, '21-03528', 0, '2025-02-06 03:01:34', '2025-02-06 10:01:34', '2025-02-06 10:01:34', '2025-02-06 10:01:34', 'Incomplete', ''),
(3, '21-03528', 23, '2025-02-06 18:50:27', '2025-02-06 10:03:28', '2025-02-06 10:03:28', '2025-02-06 10:03:28', 'Incomplete', ''),
(4, '21-03528', 23, '2025-02-06 18:50:27', '2025-02-06 10:07:28', '2025-02-06 10:07:28', '2025-02-06 10:07:28', 'Incomplete', ''),
(5, '21-03528', 23, '2025-02-06 18:50:27', '2025-02-06 10:07:34', '2025-02-06 10:07:34', '2025-02-06 10:07:34', 'Incomplete', ''),
(6, '21-03528', 23, '2025-02-06 18:50:27', '2025-02-06 10:08:53', '2025-02-06 10:08:53', '2025-02-06 10:08:53', 'Incomplete', ''),
(7, '21-03528', 23, '2025-02-06 18:50:27', '2025-02-06 10:09:19', '2025-02-06 10:09:19', '2025-02-06 10:09:19', 'Incomplete', ''),
(8, '21-03528', 23, '2025-02-06 18:50:27', NULL, NULL, NULL, 'Incomplete', ''),
(9, '21-03530', 23, '2025-02-06 18:51:35', NULL, NULL, NULL, '', ''),
(10, '21-03528', 19, '2025-02-07 05:38:04', NULL, NULL, NULL, '', ''),
(11, '21-03530', 2, '2025-02-07 05:41:07', NULL, NULL, NULL, 'Incomplete', ''),
(12, '21-03528', 1, '2025-02-07 09:55:29', NULL, NULL, NULL, '', ''),
(13, '21-03530', 1, '2025-02-07 09:54:41', NULL, NULL, NULL, '', ''),
(14, '21-03528', 7, '2025-02-09 19:48:07', NULL, NULL, NULL, 'Incomplete', '');

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
(1, '21-03529', 1, 0, 'hi', '2025-02-08 10:20:45'),
(2, '21-03789', 0, 0, 'hello', '2025-02-08 10:30:56'),
(3, '21-03789', 0, 0, 'asdasd', '2025-02-08 10:35:59'),
(4, '21-03789', 1, 0, 'asdasd', '2025-02-08 10:41:13'),
(5, '21-03789', 2, 0, 'he', '2025-02-08 15:55:02'),
(6, '21-03789', 2, 0, 'yeah', '2025-02-08 15:55:10'),
(7, '21-03789', 2, 0, 'hello', '2025-02-08 16:09:43'),
(8, '21-03789', 2, 0, 'hello', '2025-02-08 16:10:54'),
(9, '21-03789', 2, 0, 'hello', '2025-02-08 16:43:09'),
(10, '21-03789', 2, 0, 'sample comment', '2025-02-08 16:43:43'),
(11, '21-03789', 2, 0, '2', '2025-02-08 16:46:22'),
(12, '21-03789', 2, 0, '34', '2025-02-08 16:50:29'),
(13, '21-03789', 2, 0, '34', '2025-02-08 16:50:43'),
(14, '21-03789', 2, 0, 'asdasd', '2025-02-08 16:52:11'),
(15, '21-03789', 2, 0, 'test', '2025-02-08 16:53:05'),
(16, '21-03789', 2, 0, 'test comment', '2025-02-08 16:54:40'),
(17, '21-03789', 2, 0, 'test', '2025-02-08 16:55:08'),
(18, '21-03789', 2, 0, 'new comment', '2025-02-08 16:55:22'),
(19, '21-03789', 2, 0, 'new comment 1', '2025-02-08 16:55:37'),
(20, '21-03789', 2, 0, 'new 1', '2025-02-08 16:57:13'),
(21, '21-03789', 2, 0, 'comment 2', '2025-02-08 16:58:47'),
(22, '21-03789', 2, 0, 'append', '2025-02-08 16:59:12'),
(23, '21-03789', 2, 0, 'asdsad', '2025-02-08 16:59:35'),
(24, '21-03789', 2, 0, 'prepend', '2025-02-08 17:00:06'),
(25, '21-03789', 2, 0, 'prepend', '2025-02-08 17:01:38'),
(26, '21-03789', 2, 0, 'new', '2025-02-08 17:01:46'),
(27, '21-03789', 2, 0, 'qwe', '2025-02-08 17:03:32'),
(28, '21-03789', 2, 0, 'working na kaya?', '2025-02-08 17:05:07'),
(29, '21-03789', 2, 0, 'eto', '2025-02-08 17:05:35'),
(30, '21-03789', 2, 0, 'working na kaya?', '2025-02-08 17:06:47'),
(31, '21-03789', 2, 0, 'ngi', '2025-02-08 17:07:41'),
(32, '21-03789', 2, 0, 'qw', '2025-02-08 17:08:50'),
(33, '21-03789', 2, 0, '1', '2025-02-08 17:09:01'),
(38, '21-03789', 11, 0, 'asdasd', '2025-02-08 19:43:15');

-- --------------------------------------------------------

--
-- Table structure for table `department`
--

CREATE TABLE `department` (
  `dept_id` int(11) NOT NULL,
  `dept_name` varchar(255) NOT NULL,
  `logo` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `department`
--

INSERT INTO `department` (`dept_id`, `dept_name`, `logo`) VALUES
(1, 'Bachelor of Science in Information System', 'bsis.jpg'),
(2, 'Bachelor of Science in Tourism Management', ''),
(3, 'Bachelor of Science in Hospitality Management', ''),
(4, 'Bachelor of Library and Information Science', 'blis.jpg'),
(5, 'Bachelor of Secondary Education - Science', ''),
(6, 'Bachelor of Secondary Education - Mathematics', ''),
(7, 'Bachelor of Special Needs Education', '');

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
  `student_id` varchar(255) NOT NULL,
  `activity_id` int(11) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `document` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'Pending',
  `remarks` text NOT NULL DEFAULT 'Pending application wait for the admin to check.',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `remarks_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `excuse_application`
--

INSERT INTO `excuse_application` (`excuse_id`, `student_id`, `activity_id`, `subject`, `content`, `document`, `status`, `remarks`, `created_at`, `remarks_at`) VALUES
(1, '21-03528', 1, 'May concern ako', 'Kimi BAWHAHHAHHA', 'download311.png', 'Pending', 'OO na napakaligalig mo', '2025-02-05 21:26:11', '2025-02-09 19:06:39'),
(2, '21-03528', 2, 'Approved', 'Mamamo', 'download311.png', 'Approved', 'Edi approved na sino ba naman kami para humindi', '2025-02-05 21:53:49', '2025-02-08 11:28:22');

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

--
-- Dumping data for table `fines`
--

INSERT INTO `fines` (`fines_id`, `student_id`, `activity_id`, `am_in`, `am_out`, `pm_in`, `pm_out`, `total_amount`) VALUES
(1, '21-03528', 23, 5, 5, 5, 5, 0);

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
(35, 22, '21-03789', '2025-02-04 08:45:57'),
(36, 49, '21-03789', '2025-02-04 10:53:36'),
(37, 33, '21-03789', '2025-02-04 10:55:44'),
(39, 51, '21-03529', '2025-02-06 15:11:53'),
(40, 52, '21-03789', '2025-02-06 15:13:32'),
(60, 2, '21-03789', '2025-02-09 01:13:35'),
(62, 11, '21-03789', '2025-02-09 02:43:08');

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
(1, '21-03529', 'Hayst pagod na ako', 'a96e188e0aeb62d708ad5aff7da91ede.jpeg', 0, 0, 'Public', '2025-02-09 01:40:20', 0),
(2, '21-03789', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium. Integer tincidunt. Cras dapibu', NULL, NULL, 2, 'Private', '2025-02-09 01:13:35', 1),
(11, '21-03529', 'asdasdasdasd', NULL, NULL, NULL, 'Public', '2025-02-09 02:43:08', 1);

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
  `is_officer` varchar(255) DEFAULT 'No'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `student_org`
--

INSERT INTO `student_org` (`student_org_id`, `student_id`, `org_id`, `is_officer`) VALUES
(1, '21-03529', 1, 'No'),
(2, '21-03789', 2, 'Yes'),
(3, '21-03529', 3, 'No');

-- --------------------------------------------------------

--
-- Table structure for table `uploaded_images`
--

CREATE TABLE `uploaded_images` (
  `image_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

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
(2, '21-03528', 'Ann', NULL, 'Doe', '', '', '54321', 1, 'Ann Doe', 'pic2.jpg', 'Student', 'No', 'No', '2025-01-30 04:24:24', '2025-01-30 04:24:24'),
(3, '21-03789', 'John', NULL, 'Doe', '', '', '123456', 2, 'John Doe', 'pic3.jpg', 'Officer', 'No', 'Yes', '2025-01-30 04:24:24', '2025-01-30 04:24:24'),
(4, '21-03530', 'Johnny', 'Hemr', 'Worth', 'Male', '', '12345', 2, 'asd', 'pic3.jpg', 'Student', 'No', 'No', '2025-02-07 01:35:01', '2025-02-07 01:35:01');

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
  ADD PRIMARY KEY (`student_org_id`);

--
-- Indexes for table `uploaded_images`
--
ALTER TABLE `uploaded_images`
  ADD PRIMARY KEY (`image_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity`
--
ALTER TABLE `activity`
  MODIFY `activity_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `attendance_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `comment`
--
ALTER TABLE `comment`
  MODIFY `comment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `evaluation_responses`
--
ALTER TABLE `evaluation_responses`
  MODIFY `evaluation_response_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `excuse_application`
--
ALTER TABLE `excuse_application`
  MODIFY `excuse_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `fines`
--
ALTER TABLE `fines`
  MODIFY `fines_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
  MODIFY `like_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT for table `post`
--
ALTER TABLE `post`
  MODIFY `post_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `response_answer`
--
ALTER TABLE `response_answer`
  MODIFY `reponse_answer_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `student_org`
--
ALTER TABLE `student_org`
  MODIFY `student_org_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `uploaded_images`
--
ALTER TABLE `uploaded_images`
  MODIFY `image_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

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

DELIMITER $$
--
-- Events
--
CREATE DEFINER=`root`@`localhost` EVENT `AutoUpdateActivityStatus` ON SCHEDULE EVERY 10 MINUTE STARTS '2025-02-05 11:05:02' ON COMPLETION NOT PRESERVE ENABLE DO CALL UpdateActivityStatus()$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
