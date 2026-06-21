-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 20, 2026 at 03:48 PM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.0.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `thebuddiesss`
--

-- --------------------------------------------------------

--
-- Table structure for table `about`
--

CREATE TABLE `about` (
  `member_id` int(11) NOT NULL,
  `member_name` varchar(100) DEFAULT NULL,
  `project1_contribution` text DEFAULT NULL,
  `project2_contribution` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `about`
--

INSERT INTO `about` (`member_id`, `member_name`, `project1_contribution`, `project2_contribution`) VALUES
(1, 'Prit Vinesh Kumar', 'Created index.html, including the company logo, name, slogan, description, image, navigation menu, and footer with Jira, GitHub, and email links.', 'Created the database and changed index.html to index.php'),
(2, 'Thanish Thevan', 'Created jobs.html and wrote two job position descriptions using semantic HTML, sections, and ordered and unordered lists.', 'Created jobs.php, jobs table, footer.inc, header.inc, and nav.inc'),
(3, 'Maithini Sundaram', 'Reviewed the pages, added an aside section to jobs.html, checked navigation links, and helped set up GitHub and Jira.', 'Created about.php and about table'),
(4, 'Muhammad Ishaq Shoukat', 'Created apply.html and designed the job application form with HTML5 validation using the POST method to formtest.php.', 'Created apply.php and process_eoi.php');

-- --------------------------------------------------------

--
-- Table structure for table `eoi`
--

CREATE TABLE `eoi` (
  `EOInumber` int(11) NOT NULL,
  `job_reference` varchar(5) NOT NULL,
  `first_name` varchar(20) NOT NULL,
  `last_name` varchar(20) NOT NULL,
  `date_of_birth` date NOT NULL,
  `gender` enum('male','female','nonbinary','prefer-not') NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(12) NOT NULL,
  `street` varchar(40) NOT NULL,
  `suburb` varchar(40) NOT NULL,
  `state` enum('VIC','NSW','QLD','NT','WA','SA','TAS','ACT') NOT NULL,
  `postcode` char(4) NOT NULL,
  `skills` set('communication','teamwork','leadership','it-support','marketing','other') DEFAULT NULL,
  `other_skills` text DEFAULT NULL,
  `position` varchar(5) NOT NULL,
  `position_type` enum('volunteer','part-time','full-time') NOT NULL,
  `motivation` text NOT NULL,
  `years_experience` tinyint(3) UNSIGNED NOT NULL,
  `experience_desc` text DEFAULT NULL,
  `available_from` date NOT NULL,
  `interview_time` time NOT NULL,
  `days_available` set('monday','tuesday','wednesday','thursday','friday','weekend') DEFAULT NULL,
  `hours_per_week` tinyint(3) UNSIGNED NOT NULL,
  `cv_path` varchar(255) NOT NULL,
  `cover_letter_path` varchar(255) NOT NULL,
  `linkedin_url` varchar(255) DEFAULT NULL,
  `github_url` varchar(255) DEFAULT NULL,
  `portfolio_url` varchar(255) DEFAULT NULL,
  `ref_name` varchar(100) DEFAULT NULL,
  `ref_contact` varchar(100) DEFAULT NULL,
  `referral_source` enum('website','social','friend','event') DEFAULT NULL,
  `preferred_contact` enum('email','phone') NOT NULL,
  `consent_accurate` tinyint(1) NOT NULL DEFAULT 0,
  `consent_privacy` tinyint(1) NOT NULL DEFAULT 0,
  `consent_background` tinyint(1) NOT NULL DEFAULT 0,
  `status` enum('New','Current','Final') NOT NULL DEFAULT 'New',
  `submitted_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `job_id` int(11) NOT NULL,
  `reference_number` varchar(20) DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `reporting_line` varchar(100) DEFAULT NULL,
  `salary` varchar(50) DEFAULT NULL,
  `responsibilities` text DEFAULT NULL,
  `essential_requirements` text DEFAULT NULL,
  `preferable_requirements` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jobs`
--

INSERT INTO `jobs` (`job_id`, `reference_number`, `title`, `description`, `reporting_line`, `salary`, `responsibilities`, `essential_requirements`, `preferable_requirements`) VALUES
(1, 'IT101', 'IT Helpdesk Volunteer', 'Helps both staff and customers with technical issues', 'Reports to IT Coordinator', 'Unpaid(Volunteer position)', 'Troubleshoot hardware and software issues|Set up laptops, accounts, and email access|Assists user with technical problems', 'Basic knowledge of computer systems and operating systems|Good problem-solving skills|Ability to communicate clearly with users', 'Experience in IT support or troubleshooting|Familiarity with networks or printers'),
(2, 'FR202', 'Fundraising Officer', 'Responsible for planning, organizing, and implementing campaigns, and events to secure income for the organization.', 'Reports to Fundraising Manager', 'Unpaid(Volunteer position)', 'Plan and support fundraising events|Maintain relationship with existing donors|Track and record donors information accurately', 'Good communication and interpersonal skills|Basic organisational skills|Interest in non-profit work', 'Experience in fundraising, sales and marketing|Have public speaking skills'),
(3, 'SM303', 'Social Media Manager', 'To take care of the company\'s social media accounts', 'Reports to Head of Marketing', 'Unpaid(Volunteer position)', 'Create and schedule social media posts|Respond to comments and messages|Track engagement and performance', 'Good written and verbal communication skills|Ability to create engaging and clear content|Consistency and good time management', 'Experience managing social media pages|Basic knowledge of analytics tools');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`) VALUES
(1, 'Admin', 'Admin');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `about`
--
ALTER TABLE `about`
  ADD PRIMARY KEY (`member_id`);

--
-- Indexes for table `eoi`
--
ALTER TABLE `eoi`
  ADD PRIMARY KEY (`EOInumber`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`job_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `about`
--
ALTER TABLE `about`
  MODIFY `member_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `eoi`
--
ALTER TABLE `eoi`
  MODIFY `EOInumber` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `job_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
