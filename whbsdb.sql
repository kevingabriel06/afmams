-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 22, 2024 at 07:28 AM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 7.4.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `whbsdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `checked`
--

CREATE TABLE `checked` (
  `checked_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `name` varchar(255) NOT NULL,
  `contact_no` varchar(20) NOT NULL,
  `date_in` datetime NOT NULL,
  `date_out` datetime DEFAULT NULL,
  `date_book` timestamp NOT NULL DEFAULT current_timestamp(),
  `days` int(11) NOT NULL,
  `payment` int(11) NOT NULL,
  `status` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `checked`
--

INSERT INTO `checked` (`checked_id`, `room_id`, `user_id`, `name`, `contact_no`, `date_in`, `date_out`, `date_book`, `days`, `payment`, `status`) VALUES
(1, 3, 0, 'Test Guest', '1234567892', '2024-06-13 06:36:00', '2024-06-15 06:36:00', '2024-06-13 04:37:44', 2, 4000, '2'),
(2, 4, 0, 'Test Guest 2', '32145210011', '2024-06-13 06:38:00', '2024-06-15 06:38:00', '2024-06-13 04:39:25', 2, 0, '1'),
(3, 1, 0, 'Kevin Gabriel Maranan', '09555791093', '2024-06-13 06:45:00', '2024-06-15 06:45:00', '2024-06-13 04:45:25', 2, 0, '1'),
(4, 4, 0, 'Kevin Gabriel Maranan', '09555791093', '2024-06-13 06:48:00', '2024-06-15 06:48:00', '2024-06-13 04:48:49', 2, 6000, '1'),
(5, 2, 4, 'Kevin Gabriel Maranan', '09555791093', '2024-06-13 06:50:00', '2024-06-13 06:50:00', '2024-06-13 04:51:39', 2, 3000, '3'),
(6, 7, 4, 'Kevin Gabriel Maranan', '09555971093', '2024-06-13 06:52:00', '2024-06-13 06:52:00', '2024-06-13 04:52:52', 2, 0, '4');

-- --------------------------------------------------------

--
-- Table structure for table `floors`
--

CREATE TABLE `floors` (
  `floor_id` int(11) NOT NULL,
  `floor_name` varchar(255) NOT NULL,
  `floor_desc` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `floors`
--

INSERT INTO `floors` (`floor_id`, `floor_name`, `floor_desc`) VALUES
(1, 'GROUND FLOOR', 'WIDE and CLEAN'),
(2, 'FIRST FLOOR', 'WIDE and CLEAN'),
(3, 'SECOND FLOOR', 'WIDE, CLEAN, MABANGO');

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `room_id` int(11) NOT NULL,
  `room` varchar(255) NOT NULL,
  `amenities` varchar(255) NOT NULL,
  `type_id` int(11) NOT NULL,
  `floor_id` int(11) NOT NULL,
  `status` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`room_id`, `room`, `amenities`, `type_id`, `floor_id`, `status`) VALUES
(1, 'ROOM GS1 - A', 'MAY TOWEL', 1, 1, '1'),
(2, 'ROOM GS2 - B', 'WALANG TAE', 1, 1, '0'),
(3, 'ROOM FD1 - A', 'MABANGO', 2, 2, '1'),
(4, 'ROOM SKS - A', 'HINDI MABAHO', 4, 3, '1'),
(5, 'ROOM SVS - A', 'MAY SABON', 3, 3, '0'),
(6, 'ROOM FDR - 2', 'WALA', 2, 2, '0'),
(7, 'ROOM SDR - B', 'WALA', 2, 3, '1');

-- --------------------------------------------------------

--
-- Table structure for table `room_types`
--

CREATE TABLE `room_types` (
  `type_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` int(11) NOT NULL,
  `image` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `room_types`
--

INSERT INTO `room_types` (`type_id`, `name`, `price`, `image`) VALUES
(1, 'Standard Room', 1500, 'R_(2)1.jpg'),
(2, 'Deluxe Room', 2000, 'kuldt-guestroom-0017-hor-clsc.jpg'),
(3, 'View Suite', 1000, 'lasjw-guestroom-0111-hor-clsc1.jpg'),
(4, 'King Suite', 3000, 'leonardo-73523-160580005-516242.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `role` varchar(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `firstname`, `lastname`, `email`, `password`, `image`, `role`) VALUES
(1, 'Kevin Gabriel', 'Maranan', 'maranankevingabriel@gmail.com', '$2y$10$jQL.hG3CYa4Ay3tm/K3RdO0i7XkoUfrvfR5NKpBk.kqFu.qT9dTEy', '441973647_1485878995471456_2560528616773512910_n4.jpg', '2'),
(3, 'TEST ', 'DESK PERSONNEL', 'personnel@gmail.com', '$2y$10$6g9J6i7MNzSBJtzxxlHWheLosjHhBBhpVDRgXApB/GSX8n9uB48Ju', 'profile-user-icon-isolated-on-white-background-eps10-free-vector1.jpg', '1'),
(4, 'Kevin Gabriel', 'Maranan', 'guest@gmail.com', '$2y$10$z8xYr1kO58/wJOVzDZ6zHu5TsIKEWnTmhx4LOF4d6pc.MVt/v3cjW', 'profile-user-icon-isolated-on-white-background-eps10-free-vector.jpg', '3'),
(5, 'test', 'test', 'testuser@gmail.com', '$2y$10$bxQznBvalPUG5u6VYq5TG.dX6hiSuqWBO817445pX7.wbRotWahQ6', 'profile-user-icon-isolated-on-white-background-eps10-free-vector2.jpg', '3');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `checked`
--
ALTER TABLE `checked`
  ADD PRIMARY KEY (`checked_id`);

--
-- Indexes for table `floors`
--
ALTER TABLE `floors`
  ADD PRIMARY KEY (`floor_id`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`room_id`);

--
-- Indexes for table `room_types`
--
ALTER TABLE `room_types`
  ADD PRIMARY KEY (`type_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `checked`
--
ALTER TABLE `checked`
  MODIFY `checked_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `floors`
--
ALTER TABLE `floors`
  MODIFY `floor_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `room_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `room_types`
--
ALTER TABLE `room_types`
  MODIFY `type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
