-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 25, 2026 at 09:55 PM
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
-- Database: `library_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `PASSWORD` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `PASSWORD`) VALUES
(1, 'admin', '$2y$10$./XSfYTdeWoSfwwbIL9IEubleUWdQse8jz.tjQWN/oWMCCbIGiJwO'),
(2, 'harshita', '$2y$10$yXfaHlInCWq5ynXOXzLNpOy5EhJTi8hphawXf9K9l39Sb0GC0wuAi'),
(3, 'Alex', '$2y$10$sTBkGrkFJEjtxJh5.iMwSuxCXQYRrW1m0O8N3HmjQAwUy4bFmqD16');

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `author` varchar(100) NOT NULL,
  `isbn` varchar(50) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `available` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`id`, `title`, `author`, `isbn`, `category`, `quantity`, `available`, `created_at`) VALUES
(2, 'The Alchemist', 'Paulo Coelho', '9780062315007', 'Fiction', 5, 5, '2026-08-19 16:11:07'),
(3, 'Atomic Habits', 'James Clear', '9780735211292', 'Self Help', 8, 8, '2026-08-19 16:11:43'),
(4, 'Harry Potter and the Philosopher\'s Stone', 'J.K. Rowling', '9780747532743', 'Fantasy', 6, 6, '2026-08-19 16:12:12'),
(5, 'The Psychology of Money', 'Morgan Housel', '9780857197689', 'Finance', 8, 8, '2026-08-19 16:12:41'),
(6, 'Clean Code', 'Robert C. Martin', '9780132350884', 'Programming', 9, 9, '2026-08-19 16:13:15'),
(7, 'Ikigai', 'Hector Garcia', '9780143130727', 'Self Help', 8, 8, '2026-08-19 16:13:47'),
(8, 'Sherlock Holmes', 'Arthur Conan Doyle', '9780553328257', 'Mystery', 4, 4, '2026-08-19 16:14:21'),
(9, 'Rich Dad Poor Dad', 'Robert Kiyosaki', '9781612681139', '', 9, 9, '2026-08-19 16:14:49'),
(12, 'The Kite Runner', 'Khaled Hosseini', '9781594631931', '', 5, 4, '2026-08-20 18:15:12');

-- --------------------------------------------------------

--
-- Table structure for table `book_requests`
--

CREATE TABLE `book_requests` (
  `id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `request_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('Pending','Approved','Rejected') NOT NULL DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `book_requests`
--

INSERT INTO `book_requests` (`id`, `member_id`, `book_id`, `request_date`, `status`) VALUES
(1, 7, 12, '2026-09-25 06:28:40', 'Approved');

-- --------------------------------------------------------

--
-- Table structure for table `members`
--

CREATE TABLE `members` (
  `id` int(11) NOT NULL,
  `member_name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `members`
--

INSERT INTO `members` (`id`, `member_name`, `email`, `phone`, `address`, `password`, `created_at`) VALUES
(1, 'Rahul Sharma', 'rahul@gmail.com', '9876543210', 'noida', NULL, '2026-08-19 16:25:22'),
(2, 'Ananya Singh', 'ananya@gmail.com', '9876543211', 'Delhi', NULL, '2026-08-19 16:26:00'),
(3, 'Priya Verma', 'priya@gmail.com', '9876543212', 'Delhi', NULL, '2026-08-19 16:26:27'),
(4, 'Arjun Mehta', 'arjun@gmail.com', '9876543213', 'Gurugram', NULL, '2026-08-19 16:26:54'),
(6, 'Yashika Gangwar', 'yashika@gmail.com', '6879534188', 'Greater Noida', NULL, '2026-08-20 19:00:28'),
(7, 'Harshita', 'harshita@gmail.com', '9876543211', 'delhi', '$2y$10$L9r0AGgvVfKCd2QzW3XcEuVYPY/JJ9tUBPnAS.ZuUUBDzYa78c2Wm', '2026-09-24 13:08:37');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `issue_date` date NOT NULL,
  `due_date` date NOT NULL,
  `return_date` date DEFAULT NULL,
  `fine` decimal(10,2) DEFAULT 0.00,
  `STATUS` varchar(20) DEFAULT 'Issued'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `book_id`, `member_id`, `issue_date`, `due_date`, `return_date`, `fine`, `STATUS`) VALUES
(1, 2, 3, '2026-08-19', '2026-08-25', '2026-08-19', 0.00, 'Returned'),
(2, 12, 2, '2026-08-20', '2026-08-25', '2026-09-25', 155.00, 'Returned'),
(3, 12, 7, '2026-09-25', '2026-10-09', NULL, 0.00, 'Issued');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `isbn` (`isbn`);

--
-- Indexes for table `book_requests`
--
ALTER TABLE `book_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `member_id` (`member_id`),
  ADD KEY `book_id` (`book_id`);

--
-- Indexes for table `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `book_id` (`book_id`),
  ADD KEY `member_id` (`member_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `book_requests`
--
ALTER TABLE `book_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `members`
--
ALTER TABLE `members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `book_requests`
--
ALTER TABLE `book_requests`
  ADD CONSTRAINT `book_requests_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `book_requests_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`),
  ADD CONSTRAINT `transactions_ibfk_2` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
