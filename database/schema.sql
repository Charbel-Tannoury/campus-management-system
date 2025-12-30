-- Campus Management System Database Schema
-- Professionally designed for academic administration

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- --------------------------------------------------------
-- Core User Tables with Strategic ID Ranges
-- --------------------------------------------------------

-- Students: IDs starting at 12345 (5-digit student numbers)
CREATE TABLE `students` (
  `student_id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone_num` int(30) DEFAULT NULL,
  `verification` tinyint(1) NOT NULL DEFAULT 0,
  `verified_at` datetime(6) DEFAULT NULL,
  `created_at` datetime(6) NOT NULL DEFAULT current_timestamp(6),
  PRIMARY KEY (`student_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=12345 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Doctors (Faculty): IDs starting at 1234 (4-digit faculty IDs)
CREATE TABLE `doctors` (
  `dr_id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'active',
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `fixed` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime(6) NOT NULL DEFAULT current_timestamp(6),
  `fixed_at` datetime(6) DEFAULT NULL,
  PRIMARY KEY (`dr_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=1234 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Employee (Administrative Staff): IDs starting at 123 (3-digit staff IDs)
CREATE TABLE `employee` (
  `emp_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(250) NOT NULL,
  `p_number` int(50) NOT NULL,
  PRIMARY KEY (`emp_id`)
) ENGINE=InnoDB AUTO_INCREMENT=123 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Supporting Tables
-- --------------------------------------------------------

-- University faculties/colleges
CREATE TABLE `university` (
  `faculty_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `faculty_name` varchar(100) NOT NULL,
  `faculty_number` int(11) NOT NULL,
  PRIMARY KEY (`faculty_id`) USING BTREE,
  UNIQUE KEY `branch` (`faculty_name`,`faculty_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Academic programs/majors
CREATE TABLE `majors` (
  `major_id` varchar(30) NOT NULL,
  `major_name` varchar(100) NOT NULL,
  PRIMARY KEY (`major_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Academic courses
CREATE TABLE `courses` (
  `course_id` varchar(30) NOT NULL,
  `course_name` varchar(50) NOT NULL,
  `course_details` varchar(100) NOT NULL,
  PRIMARY KEY (`course_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Academic semesters
CREATE TABLE `semester` (
  `semester_id` varchar(30) NOT NULL,
  PRIMARY KEY (`semester_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Curriculum mapping: Major-Course-Semester relationships
CREATE TABLE `major_course_semester` (
  `mcs_id` int(11) NOT NULL AUTO_INCREMENT,
  `major_id` varchar(30) NOT NULL,
  `course_id` varchar(30) NOT NULL,
  `semester_id` varchar(30) NOT NULL,
  `credits` int(11) NOT NULL,
  PRIMARY KEY (`mcs_id`),
  UNIQUE KEY `per_semester` (`major_id`,`course_id`,`semester_id`),
  KEY `semester_id` (`semester_id`),
  KEY `course_id` (`course_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Course offerings by semester
CREATE TABLE `to_enrol` (
  `to_enrol_id` int(11) NOT NULL AUTO_INCREMENT,
  `faculty_id` int(11) UNSIGNED NOT NULL,
  `dr_id` int(11) NOT NULL,
  `mcs_id` int(11) NOT NULL,
  `year` year(4) NOT NULL,
  PRIMARY KEY (`to_enrol_id`),
  UNIQUE KEY `mcs_ydf` (`mcs_id`,`year`,`dr_id`,`faculty_id`),
  KEY `dr_id` (`dr_id`),
  KEY `faculty_id` (`faculty_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Student course enrollment
CREATE TABLE `enrollment` (
  `enrol_id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `to_enrol_id` int(11) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`enrol_id`),
  UNIQUE KEY `courses_enroled` (`student_id`,`to_enrol_id`),
  KEY `to_enrol_id` (`to_enrol_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Academic grades
CREATE TABLE `grades` (
  `grade_id` int(11) NOT NULL AUTO_INCREMENT,
  `enrol_id` int(11) NOT NULL,
  `project_grade` int(10) DEFAULT NULL,
  `mid_grade` int(10) DEFAULT NULL,
  `first_final` int(10) DEFAULT NULL,
  `second_final` int(10) DEFAULT NULL,
  PRIMARY KEY (`grade_id`),
  UNIQUE KEY `enrol_peryear` (`enrol_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tuition payment tracking
CREATE TABLE `paid_students` (
  `paid_id` int(11) NOT NULL AUTO_INCREMENT,
  `faculty_id` int(11) UNSIGNED NOT NULL,
  `student_id` int(11) NOT NULL,
  `major_id` varchar(30) NOT NULL,
  `year` year(4) NOT NULL DEFAULT current_timestamp(),
  `created_at` datetime(6) NOT NULL DEFAULT current_timestamp(6),
  `status` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`paid_id`),
  UNIQUE KEY `student_id` (`student_id`,`faculty_id`,`major_id`,`year`),
  KEY `faculty_id` (`faculty_id`),
  KEY `major_id` (`major_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- University administrators
CREATE TABLE `uniadmins` (
  `emp_id` int(11) UNSIGNED NOT NULL,
  `faculty_id` int(11) UNSIGNED NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`faculty_id`,`emp_id`),
  KEY `faculty_id` (`faculty_id`),
  KEY `emp_id` (`emp_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- System announcements and notifications
CREATE TABLE `mails` (
  `mail_id` int(11) NOT NULL AUTO_INCREMENT,
  `faculty_id` int(11) UNSIGNED NOT NULL,
  `receivers` int(11) NOT NULL,
  `priority` varchar(20) NOT NULL,
  `related_faculties` int(11) NOT NULL DEFAULT 0,
  `mail_title` varchar(255) NOT NULL,
  `mail_info` longtext NOT NULL,
  `created_at` datetime(6) NOT NULL DEFAULT current_timestamp(6),
  `attached_doc` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`mail_id`),
  KEY `how_send_it` (`faculty_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Student support tickets
CREATE TABLE `student_statment` (
  `ss_id` int(11) NOT NULL AUTO_INCREMENT,
  `faculty_id` int(11) UNSIGNED NOT NULL,
  `student_id` int(11) NOT NULL,
  `created_at` datetime(6) NOT NULL DEFAULT current_timestamp(6),
  PRIMARY KEY (`ss_id`),
  UNIQUE KEY `sf_chat` (`student_id`,`faculty_id`),
  KEY `faculty_id` (`faculty_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Student-staff communication messages
CREATE TABLE `ss_messages` (
  `message_id` int(11) NOT NULL AUTO_INCREMENT,
  `ss_id` int(11) NOT NULL,
  `from_id` int(11) NOT NULL,
  `message` longtext NOT NULL,
  `attach` varchar(255) DEFAULT NULL,
  `created_at` datetime(6) NOT NULL DEFAULT current_timestamp(6),
  PRIMARY KEY (`message_id`),
  KEY `ss_id` (`ss_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Anonymous feedback/complaints
CREATE TABLE `anonymous_complaint` (
  `ac_id` int(11) NOT NULL AUTO_INCREMENT,
  `faculty_id` int(11) UNSIGNED NOT NULL,
  `message` longtext NOT NULL,
  `created_at` datetime(6) NOT NULL DEFAULT current_timestamp(6),
  PRIMARY KEY (`ac_id`),
  KEY `faculty_id` (`faculty_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Foreign Key Constraints
-- --------------------------------------------------------

ALTER TABLE `anonymous_complaint`
  ADD CONSTRAINT `anonymous_complaint_ibfk_1` FOREIGN KEY (`faculty_id`) REFERENCES `university` (`faculty_id`);

ALTER TABLE `enrollment`
  ADD CONSTRAINT `enrollment_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`),
  ADD CONSTRAINT `enrollment_ibfk_2` FOREIGN KEY (`to_enrol_id`) REFERENCES `to_enrol` (`to_enrol_id`);

ALTER TABLE `grades`
  ADD CONSTRAINT `grades_ibfk_1` FOREIGN KEY (`enrol_id`) REFERENCES `enrollment` (`enrol_id`);

ALTER TABLE `mails`
  ADD CONSTRAINT `mails_ibfk_1` FOREIGN KEY (`faculty_id`) REFERENCES `university` (`faculty_id`);

ALTER TABLE `major_course_semester`
  ADD CONSTRAINT `major_course_semester_ibfk_1` FOREIGN KEY (`major_id`) REFERENCES `majors` (`major_id`),
  ADD CONSTRAINT `major_course_semester_ibfk_2` FOREIGN KEY (`semester_id`) REFERENCES `semester` (`semester_id`),
  ADD CONSTRAINT `major_course_semester_ibfk_3` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`);

ALTER TABLE `paid_students`
  ADD CONSTRAINT `paid_students_ibfk_1` FOREIGN KEY (`faculty_id`) REFERENCES `university` (`faculty_id`),
  ADD CONSTRAINT `paid_students_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`),
  ADD CONSTRAINT `paid_students_ibfk_3` FOREIGN KEY (`major_id`) REFERENCES `majors` (`major_id`);

ALTER TABLE `ss_messages`
  ADD CONSTRAINT `ss_messages_ibfk_1` FOREIGN KEY (`ss_id`) REFERENCES `student_statment` (`ss_id`);

ALTER TABLE `student_statment`
  ADD CONSTRAINT `student_statment_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`),
  ADD CONSTRAINT `student_statment_ibfk_2` FOREIGN KEY (`faculty_id`) REFERENCES `university` (`faculty_id`);

ALTER TABLE `to_enrol`
  ADD CONSTRAINT `to_enrol_ibfk_1` FOREIGN KEY (`mcs_id`) REFERENCES `major_course_semester` (`mcs_id`),
  ADD CONSTRAINT `to_enrol_ibfk_2` FOREIGN KEY (`dr_id`) REFERENCES `doctors` (`dr_id`),
  ADD CONSTRAINT `to_enrol_ibfk_3` FOREIGN KEY (`faculty_id`) REFERENCES `university` (`faculty_id`);

ALTER TABLE `uniadmins`
  ADD CONSTRAINT `uniadmins_ibfk_1` FOREIGN KEY (`faculty_id`) REFERENCES `university` (`faculty_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `uniadmins_ibfk_2` FOREIGN KEY (`emp_id`) REFERENCES `employee` (`emp_id`) ON DELETE CASCADE ON UPDATE CASCADE;

COMMIT;