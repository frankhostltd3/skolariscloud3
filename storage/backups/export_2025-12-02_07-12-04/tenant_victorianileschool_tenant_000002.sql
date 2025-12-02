-- MySQL dump 10.13  Distrib 9.1.0, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: tenant_000002
-- ------------------------------------------------------
-- Server version	9.1.0

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `academic_years`
--

DROP TABLE IF EXISTS `academic_years`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `academic_years` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `is_current` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `academic_years_name_unique` (`name`),
  KEY `academic_years_start_date_end_date_index` (`start_date`,`end_date`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `academic_years`
--

LOCK TABLES `academic_years` WRITE;
/*!40000 ALTER TABLE `academic_years` DISABLE KEYS */;
INSERT INTO `academic_years` VALUES (1,'2025','2025-01-01','2025-12-31',1,'2025-11-24 12:33:43','2025-11-24 12:33:43'),(2,'2026','2026-01-01','2026-12-31',0,'2025-11-24 12:33:43','2025-11-24 12:33:43');
/*!40000 ALTER TABLE `academic_years` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `announcements`
--

DROP TABLE IF EXISTS `announcements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `announcements` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'news',
  `target_audience` json DEFAULT NULL,
  `published_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `author_id` bigint unsigned NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `priority` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'normal',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `announcements_author_id_foreign` (`author_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `announcements`
--

LOCK TABLES `announcements` WRITE;
/*!40000 ALTER TABLE `announcements` DISABLE KEYS */;
/*!40000 ALTER TABLE `announcements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `assignment_submissions`
--

DROP TABLE IF EXISTS `assignment_submissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `assignment_submissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `assignment_id` bigint unsigned NOT NULL,
  `student_id` bigint unsigned NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `attachment_path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `submitted_at` datetime DEFAULT NULL,
  `marks` decimal(5,2) DEFAULT NULL,
  `feedback` text COLLATE utf8mb4_unicode_ci,
  `graded_at` datetime DEFAULT NULL,
  `graded_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `assignment_submissions_assignment_id_student_id_unique` (`assignment_id`,`student_id`),
  KEY `assignment_submissions_student_id_submitted_at_index` (`student_id`,`submitted_at`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `assignment_submissions`
--

LOCK TABLES `assignment_submissions` WRITE;
/*!40000 ALTER TABLE `assignment_submissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `assignment_submissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `assignments`
--

DROP TABLE IF EXISTS `assignments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `assignments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `teacher_id` bigint unsigned NOT NULL,
  `class_id` bigint unsigned NOT NULL,
  `subject_id` bigint unsigned NOT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `due_date` datetime DEFAULT NULL,
  `max_marks` int NOT NULL DEFAULT '100',
  `attachment_path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `allow_resubmission` tinyint(1) NOT NULL DEFAULT '0',
  `published` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `assignments_class_id_published_index` (`class_id`,`published`),
  KEY `assignments_due_date_index` (`due_date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `assignments`
--

LOCK TABLES `assignments` WRITE;
/*!40000 ALTER TABLE `assignments` DISABLE KEYS */;
/*!40000 ALTER TABLE `assignments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `attendance`
--

DROP TABLE IF EXISTS `attendance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `attendance` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `school_id` bigint unsigned NOT NULL,
  `class_id` bigint unsigned DEFAULT NULL,
  `class_stream_id` bigint unsigned DEFAULT NULL,
  `subject_id` bigint unsigned DEFAULT NULL,
  `teacher_id` bigint unsigned DEFAULT NULL,
  `attendance_date` date NOT NULL,
  `time_in` time DEFAULT NULL,
  `time_out` time DEFAULT NULL,
  `attendance_type` enum('classroom','exam','event','general') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'classroom',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `attendance_class_stream_id_foreign` (`class_stream_id`),
  KEY `attendance_subject_id_foreign` (`subject_id`),
  KEY `attendance_teacher_id_foreign` (`teacher_id`),
  KEY `attendance_school_id_index` (`school_id`),
  KEY `attendance_class_id_index` (`class_id`),
  KEY `attendance_attendance_date_index` (`attendance_date`),
  KEY `attendance_school_id_attendance_date_index` (`school_id`,`attendance_date`),
  KEY `attendance_class_id_attendance_date_index` (`class_id`,`attendance_date`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `attendance`
--

LOCK TABLES `attendance` WRITE;
/*!40000 ALTER TABLE `attendance` DISABLE KEYS */;
INSERT INTO `attendance` VALUES (1,2,1,NULL,NULL,2,'2025-11-23','18:47:00',NULL,'classroom',NULL,'2025-11-23 15:48:21','2025-11-23 15:48:21'),(2,2,1,NULL,NULL,2,'2025-11-24','17:42:00',NULL,'classroom',NULL,'2025-11-24 14:42:25','2025-11-24 14:42:25'),(3,2,1,NULL,NULL,16,'2025-11-29',NULL,NULL,'classroom',NULL,'2025-11-29 15:14:55','2025-11-29 15:14:55');
/*!40000 ALTER TABLE `attendance` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `attendance_records`
--

DROP TABLE IF EXISTS `attendance_records`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `attendance_records` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `attendance_id` bigint unsigned NOT NULL,
  `student_id` bigint unsigned NOT NULL,
  `status` enum('present','absent','late','excused','sick','half_day') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'present',
  `arrival_time` time DEFAULT NULL,
  `departure_time` time DEFAULT NULL,
  `minutes_late` int NOT NULL DEFAULT '0',
  `excuse_reason` text COLLATE utf8mb4_unicode_ci,
  `excuse_document` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notified_parent` tinyint(1) NOT NULL DEFAULT '0',
  `notification_sent_at` timestamp NULL DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `attendance_records_attendance_id_student_id_unique` (`attendance_id`,`student_id`),
  KEY `attendance_records_attendance_id_index` (`attendance_id`),
  KEY `attendance_records_student_id_index` (`student_id`),
  KEY `attendance_records_status_index` (`status`),
  KEY `attendance_records_student_id_attendance_id_index` (`student_id`,`attendance_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `attendance_records`
--

LOCK TABLES `attendance_records` WRITE;
/*!40000 ALTER TABLE `attendance_records` DISABLE KEYS */;
INSERT INTO `attendance_records` VALUES (1,3,13,'present',NULL,NULL,0,NULL,NULL,0,NULL,NULL,'2025-11-29 15:14:55','2025-11-29 15:14:55'),(2,3,14,'present',NULL,NULL,0,NULL,NULL,0,NULL,NULL,'2025-11-29 15:14:55','2025-11-29 15:14:55'),(3,3,12,'late',NULL,NULL,0,NULL,NULL,0,NULL,NULL,'2025-11-29 15:14:55','2025-11-29 15:14:55'),(4,3,9,'absent',NULL,NULL,0,NULL,NULL,0,NULL,NULL,'2025-11-29 15:14:55','2025-11-29 15:14:55'),(5,3,11,'present',NULL,NULL,0,NULL,NULL,0,NULL,NULL,'2025-11-29 15:14:55','2025-11-29 15:14:55');
/*!40000 ALTER TABLE `attendance_records` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `attendance_settings`
--

DROP TABLE IF EXISTS `attendance_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `attendance_settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `school_id` bigint unsigned NOT NULL,
  `student_manual_enabled` tinyint(1) NOT NULL DEFAULT '1',
  `student_qr_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `student_barcode_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `student_fingerprint_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `student_optical_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `staff_manual_enabled` tinyint(1) NOT NULL DEFAULT '1',
  `staff_qr_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `staff_barcode_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `staff_fingerprint_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `staff_optical_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `qr_code_format` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'qr',
  `qr_code_size` int NOT NULL DEFAULT '200',
  `qr_code_prefix` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `auto_generate_codes` tinyint(1) NOT NULL DEFAULT '1',
  `fingerprint_device_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fingerprint_device_ip` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fingerprint_device_port` int DEFAULT NULL,
  `fingerprint_device_config` text COLLATE utf8mb4_unicode_ci,
  `fingerprint_timeout` int NOT NULL DEFAULT '30',
  `fingerprint_threshold` int NOT NULL DEFAULT '80',
  `optical_enable_omr` tinyint(1) NOT NULL DEFAULT '0',
  `optical_sheet_template` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `optical_detection_sensitivity` int NOT NULL DEFAULT '70',
  `optical_auto_process` tinyint(1) NOT NULL DEFAULT '0',
  `attendance_grace_period` int NOT NULL DEFAULT '15',
  `allow_manual_override` tinyint(1) NOT NULL DEFAULT '1',
  `require_approval` tinyint(1) NOT NULL DEFAULT '0',
  `notification_settings` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `attendance_settings_school_id_index` (`school_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `attendance_settings`
--

LOCK TABLES `attendance_settings` WRITE;
/*!40000 ALTER TABLE `attendance_settings` DISABLE KEYS */;
INSERT INTO `attendance_settings` VALUES (1,2,1,1,1,1,1,1,1,1,1,1,'qr',200,NULL,1,NULL,NULL,NULL,NULL,30,80,0,NULL,70,0,15,1,0,NULL,'2025-11-23 15:00:43','2025-11-23 15:01:34'),(2,1,1,0,0,0,0,1,0,0,0,0,'qr',200,NULL,1,NULL,NULL,NULL,NULL,30,80,0,NULL,70,0,15,1,0,NULL,'2025-11-24 14:42:25','2025-11-24 14:42:25');
/*!40000 ALTER TABLE `attendance_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `biometric_templates`
--

DROP TABLE IF EXISTS `biometric_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `biometric_templates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `school_id` bigint unsigned NOT NULL,
  `user_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `biometric_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'fingerprint',
  `finger_position` int DEFAULT NULL,
  `template_data` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `device_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quality_score` int DEFAULT NULL,
  `enrolled_at` timestamp NOT NULL,
  `enrolled_by` bigint unsigned NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `biometric_templates_user_type_user_id_index` (`user_type`,`user_id`),
  KEY `biometric_templates_school_id_user_type_user_id_index` (`school_id`,`user_type`,`user_id`),
  KEY `biometric_templates_device_id_index` (`device_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `biometric_templates`
--

LOCK TABLES `biometric_templates` WRITE;
/*!40000 ALTER TABLE `biometric_templates` DISABLE KEYS */;
/*!40000 ALTER TABLE `biometric_templates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bookstore_order_items`
--

DROP TABLE IF EXISTS `bookstore_order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bookstore_order_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `bookstore_order_id` bigint unsigned NOT NULL,
  `library_book_id` bigint unsigned NOT NULL,
  `quantity` int NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `discount_percentage` decimal(5,2) NOT NULL DEFAULT '0.00',
  `discount_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `subtotal` decimal(10,2) NOT NULL,
  `book_title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `book_author` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `book_isbn` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `bookstore_order_items_bookstore_order_id_index` (`bookstore_order_id`),
  KEY `bookstore_order_items_library_book_id_index` (`library_book_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bookstore_order_items`
--

LOCK TABLES `bookstore_order_items` WRITE;
/*!40000 ALTER TABLE `bookstore_order_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `bookstore_order_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bookstore_orders`
--

DROP TABLE IF EXISTS `bookstore_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bookstore_orders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_number` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `customer_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipping_address` text COLLATE utf8mb4_unicode_ci,
  `subtotal` decimal(10,2) NOT NULL,
  `discount_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `tax_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `shipping_cost` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total` decimal(10,2) NOT NULL,
  `status` enum('pending','confirmed','processing','shipped','delivered','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `payment_status` enum('pending','paid','failed','refunded') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `payment_method` enum('cash','card','bank_transfer','mobile_money') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `admin_notes` text COLLATE utf8mb4_unicode_ci,
  `confirmed_at` timestamp NULL DEFAULT NULL,
  `shipped_at` timestamp NULL DEFAULT NULL,
  `delivered_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `bookstore_orders_order_number_unique` (`order_number`),
  KEY `bookstore_orders_order_number_index` (`order_number`),
  KEY `bookstore_orders_user_id_index` (`user_id`),
  KEY `bookstore_orders_status_index` (`status`),
  KEY `bookstore_orders_payment_status_index` (`payment_status`),
  KEY `bookstore_orders_created_at_index` (`created_at`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bookstore_orders`
--

LOCK TABLES `bookstore_orders` WRITE;
/*!40000 ALTER TABLE `bookstore_orders` DISABLE KEYS */;
/*!40000 ALTER TABLE `bookstore_orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
INSERT INTO `cache` VALUES ('smatcampus-cache-student_9_unread_counts','a:2:{s:8:\"messages\";i:0;s:13:\"notifications\";i:0;}',1764108590),('smatcampus-cache-student_9_last_material','N;',1764108650),('smatcampus-cache-admin@victorianileschool.com|127.0.0.1:timer','i:1763813342;',1763813342),('smatcampus-cache-admin@victorianileschool.com|127.0.0.1','i:1;',1763813342),('smatcampus-cache-student_2_unread_counts','a:2:{s:8:\"messages\";i:0;s:13:\"notifications\";i:0;}',1764007824),('smatcampus-cache-student_2_last_quiz_attempt','N;',1764007884),('smatcampus-cache-student_2_last_material','N;',1764007884),('smatcampus-cache-student_6_unread_counts','a:2:{s:8:\"messages\";i:0;s:13:\"notifications\";i:0;}',1764009277),('smatcampus-cache-student_6_last_material','N;',1764009337),('smatcampus-cache-student_6_last_quiz_attempt','N;',1764009337),('smatcampus-cache-student_9_last_quiz_attempt','N;',1764108650),('smatcampus-cache-abdulrasul@example.com.com|127.0.0.1:timer','i:1764188625;',1764188625),('smatcampus-cache-abdulrasul@example.com.com|127.0.0.1','i:3;',1764188625),('smatcampus-cache-student_14_total_grades','i:0;',1764424658),('smatcampus-cache-student_14_all_grades','O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:0:{}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}',1764424658),('smatcampus-cache-student_14_pending_assignments_35dba5d75538a9bbe0b4da4422759a0e_3','O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:0:{}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}',1764424600),('smatcampus-cache-student_14_unread_counts','a:2:{s:8:\"messages\";i:0;s:13:\"notifications\";i:0;}',1764424540),('smatcampus-cache-student_14_last_material','N;',1764424600),('smatcampus-cache-student_14_last_quiz_attempt','N;',1764424600),('smatcampus-cache-student_14_next_class','N;',1764424540),('smatcampus-cache-student_14_upcoming_exams','O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:1:{i:0;O:21:\"App\\Models\\OnlineExam\":34:{s:13:\"\0*\0connection\";s:6:\"tenant\";s:8:\"\0*\0table\";s:12:\"online_exams\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:39:{s:2:\"id\";i:1;s:10:\"teacher_id\";i:8;s:15:\"creation_method\";s:6:\"manual\";s:15:\"activation_mode\";s:6:\"manual\";s:8:\"class_id\";i:1;s:10:\"subject_id\";i:1;s:5:\"title\";s:45:\"Mathematics Around Us: Primary One Assessment\";s:11:\"description\";s:298:\"This exam assesses Primary One pupils on their understanding of basic mathematics concepts, including counting, addition, subtraction, number recognition, and simple shapes. It is designed to measure learners’ progress over the term and encourage accuracy, confidence, and problem-solving skills.\";s:12:\"instructions\";s:572:\"Write your name and class clearly at the top of your answer sheet.\r\n\r\nRead each question carefully before answering.\r\n\r\nAnswer all questions in the spaces provided.\r\n\r\nDo not talk or share answers with other pupils during the exam.\r\n\r\nRaise your hand if you need help or have a question for the teacher.\r\n\r\nUse a pencil to write your answers neatly.\r\n\r\nCheck your work before submitting to make sure you have answered all questions.\r\n\r\nStop writing immediately when the teacher says “Time is up.”\r\n\r\nRemember: this exam is to show what you have learned—do your best!\";s:9:\"starts_at\";s:19:\"2025-12-03 01:01:00\";s:7:\"ends_at\";s:19:\"2025-12-04 02:01:00\";s:16:\"duration_minutes\";i:60;s:11:\"total_marks\";i:100;s:10:\"pass_marks\";i:70;s:17:\"shuffle_questions\";i:1;s:15:\"shuffle_answers\";i:0;s:15:\"allow_backtrack\";i:1;s:24:\"show_results_immediately\";i:1;s:9:\"proctored\";i:1;s:16:\"max_tab_switches\";i:5;s:18:\"disable_copy_paste\";i:1;s:14:\"auto_submit_on\";s:7:\"time_up\";s:14:\"grading_method\";s:4:\"auto\";s:6:\"status\";s:9:\"scheduled\";s:15:\"approval_status\";s:8:\"approved\";s:12:\"review_notes\";N;s:11:\"reviewed_by\";i:2;s:11:\"reviewed_at\";s:19:\"2025-11-28 18:00:10\";s:23:\"submitted_for_review_at\";s:19:\"2025-11-28 17:39:10\";s:17:\"generation_status\";s:4:\"idle\";s:19:\"generation_provider\";N;s:19:\"generation_metadata\";s:2:\"[]\";s:12:\"activated_at\";N;s:12:\"completed_at\";N;s:10:\"created_at\";s:19:\"2025-11-27 22:09:21\";s:10:\"updated_at\";s:19:\"2025-11-28 18:00:10\";s:10:\"deleted_at\";N;s:10:\"start_time\";s:19:\"2025-12-03 01:01:00\";s:8:\"end_time\";s:19:\"2025-12-04 02:01:00\";}s:11:\"\0*\0original\";a:39:{s:2:\"id\";i:1;s:10:\"teacher_id\";i:8;s:15:\"creation_method\";s:6:\"manual\";s:15:\"activation_mode\";s:6:\"manual\";s:8:\"class_id\";i:1;s:10:\"subject_id\";i:1;s:5:\"title\";s:45:\"Mathematics Around Us: Primary One Assessment\";s:11:\"description\";s:298:\"This exam assesses Primary One pupils on their understanding of basic mathematics concepts, including counting, addition, subtraction, number recognition, and simple shapes. It is designed to measure learners’ progress over the term and encourage accuracy, confidence, and problem-solving skills.\";s:12:\"instructions\";s:572:\"Write your name and class clearly at the top of your answer sheet.\r\n\r\nRead each question carefully before answering.\r\n\r\nAnswer all questions in the spaces provided.\r\n\r\nDo not talk or share answers with other pupils during the exam.\r\n\r\nRaise your hand if you need help or have a question for the teacher.\r\n\r\nUse a pencil to write your answers neatly.\r\n\r\nCheck your work before submitting to make sure you have answered all questions.\r\n\r\nStop writing immediately when the teacher says “Time is up.”\r\n\r\nRemember: this exam is to show what you have learned—do your best!\";s:9:\"starts_at\";s:19:\"2025-12-03 01:01:00\";s:7:\"ends_at\";s:19:\"2025-12-04 02:01:00\";s:16:\"duration_minutes\";i:60;s:11:\"total_marks\";i:100;s:10:\"pass_marks\";i:70;s:17:\"shuffle_questions\";i:1;s:15:\"shuffle_answers\";i:0;s:15:\"allow_backtrack\";i:1;s:24:\"show_results_immediately\";i:1;s:9:\"proctored\";i:1;s:16:\"max_tab_switches\";i:5;s:18:\"disable_copy_paste\";i:1;s:14:\"auto_submit_on\";s:7:\"time_up\";s:14:\"grading_method\";s:4:\"auto\";s:6:\"status\";s:9:\"scheduled\";s:15:\"approval_status\";s:8:\"approved\";s:12:\"review_notes\";N;s:11:\"reviewed_by\";i:2;s:11:\"reviewed_at\";s:19:\"2025-11-28 18:00:10\";s:23:\"submitted_for_review_at\";s:19:\"2025-11-28 17:39:10\";s:17:\"generation_status\";s:4:\"idle\";s:19:\"generation_provider\";N;s:19:\"generation_metadata\";s:2:\"[]\";s:12:\"activated_at\";N;s:12:\"completed_at\";N;s:10:\"created_at\";s:19:\"2025-11-27 22:09:21\";s:10:\"updated_at\";s:19:\"2025-11-28 18:00:10\";s:10:\"deleted_at\";N;s:10:\"start_time\";s:19:\"2025-12-03 01:01:00\";s:8:\"end_time\";s:19:\"2025-12-04 02:01:00\";}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:15:{s:9:\"exam_date\";s:4:\"date\";s:9:\"starts_at\";s:8:\"datetime\";s:7:\"ends_at\";s:8:\"datetime\";s:9:\"proctored\";s:7:\"boolean\";s:18:\"disable_copy_paste\";s:7:\"boolean\";s:17:\"shuffle_questions\";s:7:\"boolean\";s:15:\"shuffle_answers\";s:7:\"boolean\";s:24:\"show_results_immediately\";s:7:\"boolean\";s:15:\"allow_backtrack\";s:7:\"boolean\";s:11:\"reviewed_at\";s:8:\"datetime\";s:23:\"submitted_for_review_at\";s:8:\"datetime\";s:19:\"generation_metadata\";s:5:\"array\";s:12:\"activated_at\";s:8:\"datetime\";s:12:\"completed_at\";s:8:\"datetime\";s:10:\"deleted_at\";s:8:\"datetime\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:3:{i:0;s:12:\"status_label\";i:1;s:12:\"is_available\";i:2;s:14:\"attempts_count\";}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:2:{s:5:\"class\";O:22:\"App\\Models\\SchoolClass\":33:{s:13:\"\0*\0connection\";s:6:\"tenant\";s:8:\"\0*\0table\";s:7:\"classes\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:12:{s:2:\"id\";i:1;s:9:\"school_id\";i:2;s:18:\"education_level_id\";i:2;s:4:\"name\";s:9:\"Primary 1\";s:16:\"class_teacher_id\";N;s:4:\"code\";s:2:\"P1\";s:11:\"description\";N;s:8:\"capacity\";i:40;s:21:\"active_students_count\";i:5;s:9:\"is_active\";i:1;s:10:\"created_at\";s:19:\"2025-11-23 08:18:05\";s:10:\"updated_at\";s:19:\"2025-11-26 14:03:57\";}s:11:\"\0*\0original\";a:12:{s:2:\"id\";i:1;s:9:\"school_id\";i:2;s:18:\"education_level_id\";i:2;s:4:\"name\";s:9:\"Primary 1\";s:16:\"class_teacher_id\";N;s:4:\"code\";s:2:\"P1\";s:11:\"description\";N;s:8:\"capacity\";i:40;s:21:\"active_students_count\";i:5;s:9:\"is_active\";i:1;s:10:\"created_at\";s:19:\"2025-11-23 08:18:05\";s:10:\"updated_at\";s:19:\"2025-11-26 14:03:57\";}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:2:{s:8:\"capacity\";s:7:\"integer\";s:9:\"is_active\";s:7:\"boolean\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:8:{i:0;s:9:\"school_id\";i:1;s:4:\"name\";i:2;s:11:\"grade_level\";i:3;s:6:\"stream\";i:4;s:8:\"capacity\";i:5;s:10:\"teacher_id\";i:6;s:11:\"room_number\";i:7;s:9:\"is_active\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}s:7:\"subject\";O:18:\"App\\Models\\Subject\":33:{s:13:\"\0*\0connection\";s:6:\"tenant\";s:8:\"\0*\0table\";s:8:\"subjects\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:15:{s:2:\"id\";i:1;s:9:\"school_id\";i:2;s:18:\"education_level_id\";i:2;s:4:\"name\";s:11:\"Mathematics\";s:4:\"code\";s:7:\"MATH101\";s:11:\"description\";N;s:4:\"type\";s:4:\"core\";s:12:\"credit_hours\";i:60;s:9:\"is_active\";i:1;s:10:\"sort_order\";i:1;s:9:\"pass_mark\";i:60;s:9:\"max_marks\";i:100;s:25:\"required_periods_per_week\";N;s:10:\"created_at\";s:19:\"2025-11-23 08:21:16\";s:10:\"updated_at\";s:19:\"2025-11-23 08:21:16\";}s:11:\"\0*\0original\";a:15:{s:2:\"id\";i:1;s:9:\"school_id\";i:2;s:18:\"education_level_id\";i:2;s:4:\"name\";s:11:\"Mathematics\";s:4:\"code\";s:7:\"MATH101\";s:11:\"description\";N;s:4:\"type\";s:4:\"core\";s:12:\"credit_hours\";i:60;s:9:\"is_active\";i:1;s:10:\"sort_order\";i:1;s:9:\"pass_mark\";i:60;s:9:\"max_marks\";i:100;s:25:\"required_periods_per_week\";N;s:10:\"created_at\";s:19:\"2025-11-23 08:21:16\";s:10:\"updated_at\";s:19:\"2025-11-23 08:21:16\";}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:0:{}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:3:{i:0;s:4:\"code\";i:1;s:4:\"name\";i:2;s:18:\"education_level_id\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:34:{i:0;s:10:\"teacher_id\";i:1;s:8:\"class_id\";i:2;s:10:\"subject_id\";i:3;s:5:\"title\";i:4;s:11:\"description\";i:5;s:12:\"instructions\";i:6;s:16:\"duration_minutes\";i:7;s:11:\"total_marks\";i:8;s:10:\"pass_marks\";i:9;s:9:\"exam_date\";i:10;s:9:\"starts_at\";i:11;s:7:\"ends_at\";i:12;s:6:\"status\";i:13;s:9:\"proctored\";i:14;s:15:\"allow_backtrack\";i:15;s:14:\"auto_submit_on\";i:16;s:16:\"max_tab_switches\";i:17;s:18:\"disable_copy_paste\";i:18;s:17:\"shuffle_questions\";i:19;s:15:\"shuffle_answers\";i:20;s:24:\"show_results_immediately\";i:21;s:14:\"grading_method\";i:22;s:15:\"creation_method\";i:23;s:15:\"activation_mode\";i:24;s:15:\"approval_status\";i:25;s:12:\"review_notes\";i:26;s:11:\"reviewed_by\";i:27;s:11:\"reviewed_at\";i:28;s:23:\"submitted_for_review_at\";i:29;s:17:\"generation_status\";i:30;s:19:\"generation_provider\";i:31;s:19:\"generation_metadata\";i:32;s:12:\"activated_at\";i:33;s:12:\"completed_at\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}s:16:\"\0*\0forceDeleting\";b:0;}}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}',1764424600),('smatcampus-cache-godwinmaje@example.com|127.0.0.1:timer','i:1764199507;',1764199507),('smatcampus-cache-godwinmaje@example.com|127.0.0.1','i:1;',1764199507),('smatcampus-cache-student_14_recent_grades','O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:0:{}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}',1764424600),('smatcampus-cache-ibrahimgowon@example.com|127.0.0.1:timer','i:1764199569;',1764199569),('smatcampus-cache-ibrahimgowon@example.com|127.0.0.1','i:1;',1764199569),('smatcampus-cache-student_14_upcoming_quizzes','O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:0:{}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}',1764424600),('smatcampus-cache-spatie.permission.cache.tenant.2','a:3:{s:5:\"alias\";a:5:{s:1:\"a\";s:2:\"id\";s:1:\"b\";s:4:\"name\";s:1:\"c\";s:10:\"guard_name\";s:1:\"r\";s:5:\"roles\";s:1:\"j\";s:9:\"tenant_id\";}s:11:\"permissions\";a:121:{i:0;a:4:{s:1:\"a\";i:130;s:1:\"b\";s:10:\"users.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:11;i:1;i:12;}}i:1;a:4:{s:1:\"a\";i:131;s:1:\"b\";s:12:\"users.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:11;i:1;i:12;}}i:2;a:4:{s:1:\"a\";i:132;s:1:\"b\";s:10:\"users.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:11;i:1;i:12;}}i:3;a:4:{s:1:\"a\";i:133;s:1:\"b\";s:12:\"users.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:11;}}i:4;a:4:{s:1:\"a\";i:134;s:1:\"b\";s:13:\"users.approve\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:11;i:1;i:12;}}i:5;a:4:{s:1:\"a\";i:135;s:1:\"b\";s:13:\"users.suspend\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:11;i:1;i:12;}}i:6;a:4:{s:1:\"a\";i:136;s:1:\"b\";s:12:\"users.export\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:11;i:1;i:12;}}i:7;a:4:{s:1:\"a\";i:137;s:1:\"b\";s:10:\"roles.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:11;i:1;i:12;}}i:8;a:4:{s:1:\"a\";i:138;s:1:\"b\";s:12:\"roles.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:11;i:1;i:12;}}i:9;a:4:{s:1:\"a\";i:139;s:1:\"b\";s:10:\"roles.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:11;i:1;i:12;}}i:10;a:4:{s:1:\"a\";i:140;s:1:\"b\";s:12:\"roles.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:11;}}i:11;a:4:{s:1:\"a\";i:141;s:1:\"b\";s:18:\"permissions.assign\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:11;i:1;i:12;}}i:12;a:4:{s:1:\"a\";i:142;s:1:\"b\";s:13:\"students.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:6:{i:0;i:11;i:1;i:12;i:2;i:13;i:3;i:15;i:4;i:17;i:5;i:18;}}i:13;a:4:{s:1:\"a\";i:143;s:1:\"b\";s:15:\"students.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:11;i:1;i:12;}}i:14;a:4:{s:1:\"a\";i:144;s:1:\"b\";s:13:\"students.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:11;i:1;i:12;i:2;i:13;i:3;i:18;}}i:15;a:4:{s:1:\"a\";i:145;s:1:\"b\";s:15:\"students.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:11;i:1;i:12;}}i:16;a:4:{s:1:\"a\";i:146;s:1:\"b\";s:15:\"students.enroll\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:11;i:1;i:12;}}i:17;a:4:{s:1:\"a\";i:147;s:1:\"b\";s:17:\"students.transfer\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:11;i:1;i:12;}}i:18;a:4:{s:1:\"a\";i:148;s:1:\"b\";s:17:\"students.graduate\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:11;i:1;i:12;}}i:19;a:4:{s:1:\"a\";i:149;s:1:\"b\";s:13:\"teachers.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:11;i:1;i:12;i:2;i:17;i:3;i:18;}}i:20;a:4:{s:1:\"a\";i:150;s:1:\"b\";s:15:\"teachers.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:11;i:1;i:12;}}i:21;a:4:{s:1:\"a\";i:151;s:1:\"b\";s:13:\"teachers.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:11;i:1;i:12;}}i:22;a:4:{s:1:\"a\";i:152;s:1:\"b\";s:15:\"teachers.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:11;i:1;i:12;}}i:23;a:4:{s:1:\"a\";i:153;s:1:\"b\";s:15:\"teachers.assign\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:11;i:1;i:12;i:2;i:18;}}i:24;a:4:{s:1:\"a\";i:154;s:1:\"b\";s:12:\"classes.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:5:{i:0;i:11;i:1;i:12;i:2;i:13;i:3;i:14;i:4;i:18;}}i:25;a:4:{s:1:\"a\";i:155;s:1:\"b\";s:14:\"classes.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:11;i:1;i:12;i:2;i:18;}}i:26;a:4:{s:1:\"a\";i:156;s:1:\"b\";s:12:\"classes.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:11;i:1;i:12;i:2;i:18;}}i:27;a:4:{s:1:\"a\";i:157;s:1:\"b\";s:14:\"classes.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:11;i:1;i:12;}}i:28;a:4:{s:1:\"a\";i:158;s:1:\"b\";s:14:\"classes.assign\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:11;i:1;i:12;i:2;i:13;}}i:29;a:4:{s:1:\"a\";i:159;s:1:\"b\";s:13:\"subjects.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:5:{i:0;i:11;i:1;i:12;i:2;i:13;i:3;i:14;i:4;i:18;}}i:30;a:4:{s:1:\"a\";i:160;s:1:\"b\";s:15:\"subjects.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:11;i:1;i:12;i:2;i:18;}}i:31;a:4:{s:1:\"a\";i:161;s:1:\"b\";s:13:\"subjects.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:11;i:1;i:12;i:2;i:18;}}i:32;a:4:{s:1:\"a\";i:162;s:1:\"b\";s:15:\"subjects.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:11;i:1;i:12;}}i:33;a:4:{s:1:\"a\";i:163;s:1:\"b\";s:15:\"attendance.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:6:{i:0;i:11;i:1;i:12;i:2;i:13;i:3;i:14;i:4;i:15;i:5;i:18;}}i:34;a:4:{s:1:\"a\";i:164;s:1:\"b\";s:15:\"attendance.mark\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:11;i:1;i:12;i:2;i:13;}}i:35;a:4:{s:1:\"a\";i:165;s:1:\"b\";s:15:\"attendance.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:11;i:1;i:12;i:2;i:13;}}i:36;a:4:{s:1:\"a\";i:166;s:1:\"b\";s:17:\"attendance.report\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:11;i:1;i:12;i:2;i:18;}}i:37;a:4:{s:1:\"a\";i:167;s:1:\"b\";s:11:\"grades.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:6:{i:0;i:11;i:1;i:12;i:2;i:13;i:3;i:14;i:4;i:15;i:5;i:18;}}i:38;a:4:{s:1:\"a\";i:168;s:1:\"b\";s:13:\"grades.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:11;i:1;i:12;i:2;i:13;}}i:39;a:4:{s:1:\"a\";i:169;s:1:\"b\";s:11:\"grades.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:11;i:1;i:12;i:2;i:13;}}i:40;a:4:{s:1:\"a\";i:170;s:1:\"b\";s:13:\"grades.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:11;i:1;i:12;}}i:41;a:4:{s:1:\"a\";i:171;s:1:\"b\";s:14:\"grades.approve\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:11;i:1;i:12;i:2;i:18;}}i:42;a:4:{s:1:\"a\";i:172;s:1:\"b\";s:13:\"grades.report\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:11;i:1;i:12;}}i:43;a:4:{s:1:\"a\";i:173;s:1:\"b\";s:16:\"assignments.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:11;i:1;i:12;i:2;i:13;i:3;i:14;}}i:44;a:4:{s:1:\"a\";i:174;s:1:\"b\";s:18:\"assignments.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:11;i:1;i:12;i:2;i:13;}}i:45;a:4:{s:1:\"a\";i:175;s:1:\"b\";s:16:\"assignments.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:11;i:1;i:12;i:2;i:13;}}i:46;a:4:{s:1:\"a\";i:176;s:1:\"b\";s:18:\"assignments.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:11;i:1;i:12;}}i:47;a:4:{s:1:\"a\";i:177;s:1:\"b\";s:17:\"assignments.grade\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:11;i:1;i:12;i:2;i:13;}}i:48;a:4:{s:1:\"a\";i:178;s:1:\"b\";s:10:\"exams.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:11;i:1;i:12;i:2;i:13;i:3;i:14;}}i:49;a:4:{s:1:\"a\";i:179;s:1:\"b\";s:12:\"exams.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:11;i:1;i:12;}}i:50;a:4:{s:1:\"a\";i:180;s:1:\"b\";s:10:\"exams.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:11;i:1;i:12;}}i:51;a:4:{s:1:\"a\";i:181;s:1:\"b\";s:12:\"exams.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:11;i:1;i:12;}}i:52;a:4:{s:1:\"a\";i:182;s:1:\"b\";s:14:\"exams.schedule\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:11;i:1;i:12;}}i:53;a:4:{s:1:\"a\";i:183;s:1:\"b\";s:14:\"timetable.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:5:{i:0;i:11;i:1;i:12;i:2;i:13;i:3;i:14;i:4;i:18;}}i:54;a:4:{s:1:\"a\";i:184;s:1:\"b\";s:16:\"timetable.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:11;i:1;i:12;i:2;i:18;}}i:55;a:4:{s:1:\"a\";i:185;s:1:\"b\";s:14:\"timetable.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:11;i:1;i:12;i:2;i:18;}}i:56;a:4:{s:1:\"a\";i:186;s:1:\"b\";s:16:\"timetable.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:11;i:1;i:12;}}i:57;a:4:{s:1:\"a\";i:187;s:1:\"b\";s:12:\"finance.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:5:{i:0;i:11;i:1;i:12;i:2;i:15;i:3;i:16;i:4;i:20;}}i:58;a:4:{s:1:\"a\";i:188;s:1:\"b\";s:14:\"finance.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:11;i:1;i:12;i:2;i:16;i:3;i:20;}}i:59;a:4:{s:1:\"a\";i:189;s:1:\"b\";s:12:\"finance.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:11;i:1;i:12;i:2;i:16;i:3;i:20;}}i:60;a:4:{s:1:\"a\";i:190;s:1:\"b\";s:14:\"finance.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:11;i:1;i:12;}}i:61;a:4:{s:1:\"a\";i:191;s:1:\"b\";s:11:\"fees.manage\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:11;i:1;i:12;i:2;i:16;i:3;i:20;}}i:62;a:4:{s:1:\"a\";i:192;s:1:\"b\";s:16:\"payments.process\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:11;i:1;i:12;i:2;i:16;i:3;i:20;}}i:63;a:4:{s:1:\"a\";i:193;s:1:\"b\";s:15:\"payments.refund\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:11;i:1;i:12;i:2;i:16;i:3;i:20;}}i:64;a:4:{s:1:\"a\";i:194;s:1:\"b\";s:17:\"invoices.generate\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:5:{i:0;i:11;i:1;i:12;i:2;i:15;i:3;i:16;i:4;i:20;}}i:65;a:4:{s:1:\"a\";i:195;s:1:\"b\";s:7:\"hr.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:11;i:1;i:12;}}i:66;a:4:{s:1:\"a\";i:196;s:1:\"b\";s:9:\"hr.manage\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:11;i:1;i:12;}}i:67;a:4:{s:1:\"a\";i:197;s:1:\"b\";s:14:\"employees.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:11;i:1;i:12;}}i:68;a:4:{s:1:\"a\";i:198;s:1:\"b\";s:16:\"employees.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:11;i:1;i:12;}}i:69;a:4:{s:1:\"a\";i:199;s:1:\"b\";s:14:\"employees.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:11;i:1;i:12;}}i:70;a:4:{s:1:\"a\";i:200;s:1:\"b\";s:16:\"employees.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:11;i:1;i:12;}}i:71;a:4:{s:1:\"a\";i:201;s:1:\"b\";s:19:\"leave-requests.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:11;i:1;i:12;}}i:72;a:4:{s:1:\"a\";i:202;s:1:\"b\";s:21:\"leave-requests.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:11;i:1;i:12;}}i:73;a:4:{s:1:\"a\";i:203;s:1:\"b\";s:22:\"leave-requests.approve\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:11;i:1;i:12;}}i:74;a:4:{s:1:\"a\";i:204;s:1:\"b\";s:21:\"leave-requests.reject\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:11;i:1;i:12;}}i:75;a:4:{s:1:\"a\";i:205;s:1:\"b\";s:14:\"pamphlets.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:11;i:1;i:12;i:2;i:13;}}i:76;a:4:{s:1:\"a\";i:206;s:1:\"b\";s:16:\"pamphlets.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:11;i:1;i:12;}}i:77;a:4:{s:1:\"a\";i:207;s:1:\"b\";s:14:\"pamphlets.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:11;i:1;i:12;}}i:78;a:4:{s:1:\"a\";i:208;s:1:\"b\";s:16:\"pamphlets.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:11;i:1;i:12;}}i:79;a:4:{s:1:\"a\";i:209;s:1:\"b\";s:17:\"pamphlets.publish\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:11;i:1;i:12;}}i:80;a:4:{s:1:\"a\";i:210;s:1:\"b\";s:10:\"books.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:5:{i:0;i:11;i:1;i:12;i:2;i:13;i:3;i:14;i:4;i:17;}}i:81;a:4:{s:1:\"a\";i:211;s:1:\"b\";s:12:\"books.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:11;i:1;i:12;}}i:82;a:4:{s:1:\"a\";i:212;s:1:\"b\";s:10:\"books.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:11;i:1;i:12;}}i:83;a:4:{s:1:\"a\";i:213;s:1:\"b\";s:12:\"books.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:11;i:1;i:12;}}i:84;a:4:{s:1:\"a\";i:214;s:1:\"b\";s:14:\"bookstore.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:5:{i:0;i:11;i:1;i:12;i:2;i:13;i:3;i:14;i:4;i:17;}}i:85;a:4:{s:1:\"a\";i:215;s:1:\"b\";s:16:\"bookstore.manage\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:11;i:1;i:12;}}i:86;a:4:{s:1:\"a\";i:216;s:1:\"b\";s:16:\"bookstore.orders\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:11;i:1;i:12;}}i:87;a:4:{s:1:\"a\";i:217;s:1:\"b\";s:18:\"bookstore.purchase\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:11;i:1;i:12;i:2;i:14;i:3;i:17;}}i:88;a:4:{s:1:\"a\";i:218;s:1:\"b\";s:12:\"library.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:11;i:1;i:12;i:2;i:14;i:3;i:17;}}i:89;a:4:{s:1:\"a\";i:219;s:1:\"b\";s:14:\"library.manage\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:11;i:1;i:12;i:2;i:17;}}i:90;a:4:{s:1:\"a\";i:220;s:1:\"b\";s:13:\"library.issue\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:11;i:1;i:12;i:2;i:17;}}i:91;a:4:{s:1:\"a\";i:221;s:1:\"b\";s:14:\"library.return\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:11;i:1;i:12;i:2;i:17;}}i:92;a:4:{s:1:\"a\";i:222;s:1:\"b\";s:12:\"reports.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:10:{i:0;i:11;i:1;i:12;i:2;i:13;i:3;i:15;i:4;i:16;i:5;i:17;i:6;i:18;i:7;i:19;i:8;i:20;i:9;i:21;}}i:93;a:4:{s:1:\"a\";i:223;s:1:\"b\";s:16:\"reports.generate\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:7:{i:0;i:11;i:1;i:12;i:2;i:13;i:3;i:16;i:4;i:17;i:5;i:18;i:6;i:20;}}i:94;a:4:{s:1:\"a\";i:224;s:1:\"b\";s:14:\"reports.export\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:11;i:1;i:12;i:2;i:16;i:3;i:20;}}i:95;a:4:{s:1:\"a\";i:225;s:1:\"b\";s:14:\"reports.custom\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:11;i:1;i:12;i:2;i:18;}}i:96;a:4:{s:1:\"a\";i:226;s:1:\"b\";s:13:\"settings.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:11;i:1;i:12;}}i:97;a:4:{s:1:\"a\";i:227;s:1:\"b\";s:13:\"settings.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:11;i:1;i:12;}}i:98;a:4:{s:1:\"a\";i:228;s:1:\"b\";s:16:\"settings.general\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:11;i:1;i:12;}}i:99;a:4:{s:1:\"a\";i:229;s:1:\"b\";s:17:\"settings.academic\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:11;i:1;i:12;}}i:100;a:4:{s:1:\"a\";i:230;s:1:\"b\";s:15:\"settings.system\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:11;}}i:101;a:4:{s:1:\"a\";i:231;s:1:\"b\";s:13:\"settings.mail\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:11;i:1;i:12;}}i:102;a:4:{s:1:\"a\";i:232;s:1:\"b\";s:16:\"settings.payment\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:11;i:1;i:12;}}i:103;a:4:{s:1:\"a\";i:233;s:1:\"b\";s:18:\"settings.messaging\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:11;i:1;i:12;}}i:104;a:4:{s:1:\"a\";i:234;s:1:\"b\";s:13:\"messages.send\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:11;i:1;i:12;i:2;i:13;i:3;i:15;}}i:105;a:4:{s:1:\"a\";i:235;s:1:\"b\";s:13:\"messages.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:7:{i:0;i:11;i:1;i:12;i:2;i:13;i:3;i:14;i:4;i:15;i:5;i:19;i:6;i:21;}}i:106;a:4:{s:1:\"a\";i:236;s:1:\"b\";s:20:\"announcements.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:11;i:1;i:12;}}i:107;a:4:{s:1:\"a\";i:237;s:1:\"b\";s:18:\"announcements.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:11;i:1;i:12;}}i:108;a:4:{s:1:\"a\";i:238;s:1:\"b\";s:18:\"notifications.send\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:11;i:1;i:12;}}i:109;a:4:{s:1:\"a\";i:239;s:1:\"b\";s:14:\"documents.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:6:{i:0;i:11;i:1;i:12;i:2;i:13;i:3;i:14;i:4;i:19;i:5;i:21;}}i:110;a:4:{s:1:\"a\";i:240;s:1:\"b\";s:16:\"documents.upload\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:11;i:1;i:12;i:2;i:13;}}i:111;a:4:{s:1:\"a\";i:241;s:1:\"b\";s:18:\"documents.download\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:11;i:1;i:12;i:2;i:13;i:3;i:14;}}i:112;a:4:{s:1:\"a\";i:242;s:1:\"b\";s:16:\"documents.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:11;i:1;i:12;}}i:113;a:4:{s:1:\"a\";i:243;s:1:\"b\";s:16:\"departments.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:11;i:1;i:12;i:2;i:18;}}i:114;a:4:{s:1:\"a\";i:244;s:1:\"b\";s:18:\"departments.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:11;i:1;i:12;}}i:115;a:4:{s:1:\"a\";i:245;s:1:\"b\";s:16:\"departments.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:11;i:1;i:12;i:2;i:18;}}i:116;a:4:{s:1:\"a\";i:246;s:1:\"b\";s:18:\"departments.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:11;i:1;i:12;}}i:117;a:4:{s:1:\"a\";i:247;s:1:\"b\";s:14:\"positions.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:11;i:1;i:12;}}i:118;a:4:{s:1:\"a\";i:248;s:1:\"b\";s:16:\"positions.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:11;i:1;i:12;}}i:119;a:4:{s:1:\"a\";i:249;s:1:\"b\";s:14:\"positions.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:11;i:1;i:12;}}i:120;a:4:{s:1:\"a\";i:250;s:1:\"b\";s:16:\"positions.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:11;i:1;i:12;}}}s:5:\"roles\";a:11:{i:0;a:4:{s:1:\"a\";i:11;s:1:\"j\";N;s:1:\"b\";s:11:\"Super-Admin\";s:1:\"c\";s:3:\"web\";}i:1;a:4:{s:1:\"a\";i:12;s:1:\"j\";N;s:1:\"b\";s:5:\"Admin\";s:1:\"c\";s:3:\"web\";}i:2;a:4:{s:1:\"a\";i:13;s:1:\"j\";N;s:1:\"b\";s:7:\"Teacher\";s:1:\"c\";s:3:\"web\";}i:3;a:4:{s:1:\"a\";i:15;s:1:\"j\";N;s:1:\"b\";s:6:\"Parent\";s:1:\"c\";s:3:\"web\";}i:4;a:4:{s:1:\"a\";i:17;s:1:\"j\";N;s:1:\"b\";s:9:\"Librarian\";s:1:\"c\";s:3:\"web\";}i:5;a:4:{s:1:\"a\";i:18;s:1:\"j\";N;s:1:\"b\";s:18:\"Head-of-Department\";s:1:\"c\";s:3:\"web\";}i:6;a:4:{s:1:\"a\";i:14;s:1:\"j\";N;s:1:\"b\";s:7:\"Student\";s:1:\"c\";s:3:\"web\";}i:7;a:4:{s:1:\"a\";i:16;s:1:\"j\";N;s:1:\"b\";s:10:\"Accountant\";s:1:\"c\";s:3:\"web\";}i:8;a:4:{s:1:\"a\";i:20;s:1:\"j\";N;s:1:\"b\";s:6:\"Bursar\";s:1:\"c\";s:3:\"web\";}i:9;a:4:{s:1:\"a\";i:19;s:1:\"j\";N;s:1:\"b\";s:5:\"Staff\";s:1:\"c\";s:3:\"web\";}i:10;a:4:{s:1:\"a\";i:21;s:1:\"j\";N;s:1:\"b\";s:5:\"Nurse\";s:1:\"c\";s:3:\"web\";}}}',1764617797);
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `class_stream_teacher`
--

DROP TABLE IF EXISTS `class_stream_teacher`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `class_stream_teacher` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `class_stream_id` bigint unsigned NOT NULL,
  `teacher_id` bigint unsigned NOT NULL,
  `academic_year` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `class_stream_teacher_unique` (`class_stream_id`,`teacher_id`,`academic_year`),
  KEY `class_stream_teacher_teacher_id_foreign` (`teacher_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `class_stream_teacher`
--

LOCK TABLES `class_stream_teacher` WRITE;
/*!40000 ALTER TABLE `class_stream_teacher` DISABLE KEYS */;
/*!40000 ALTER TABLE `class_stream_teacher` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `class_streams`
--

DROP TABLE IF EXISTS `class_streams`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `class_streams` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `class_id` bigint unsigned NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `capacity` int DEFAULT NULL,
  `active_students_count` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `class_streams_class_id_index` (`class_id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `class_streams`
--

LOCK TABLES `class_streams` WRITE;
/*!40000 ALTER TABLE `class_streams` DISABLE KEYS */;
INSERT INTO `class_streams` VALUES (1,1,'Blue','B',NULL,50,4,1,'2025-11-23 05:19:18','2025-11-26 10:22:57'),(2,1,'Yellow','Y',NULL,50,1,1,'2025-11-23 05:19:42','2025-11-26 11:12:46'),(3,2,'Blue','B',NULL,50,0,1,'2025-11-23 05:25:32','2025-11-23 05:25:32'),(4,2,'Yellow','Y',NULL,50,0,1,'2025-11-23 05:25:54','2025-11-23 05:25:54'),(5,7,'Blue','P7-BL',NULL,NULL,0,1,'2025-11-23 07:25:42','2025-11-23 07:25:42'),(6,7,'Yellow','P7-YE',NULL,NULL,0,1,'2025-11-23 07:25:42','2025-11-23 07:25:42'),(7,6,'Blue','P6-BL',NULL,NULL,0,1,'2025-11-23 07:27:06','2025-11-23 07:27:06'),(8,6,'Yellow','P6-YE',NULL,NULL,0,1,'2025-11-23 07:27:06','2025-11-23 07:27:06'),(9,5,'Blue','P5-BL',NULL,NULL,0,1,'2025-11-23 07:28:16','2025-11-23 07:28:16'),(10,5,'Yellow','P5-YE',NULL,NULL,0,1,'2025-11-23 07:28:16','2025-11-23 07:28:16'),(11,4,'Blue','P4-BL',NULL,NULL,0,1,'2025-11-23 07:29:23','2025-11-23 07:29:23'),(12,4,'Yellow','P4-YE',NULL,NULL,0,1,'2025-11-23 07:29:23','2025-11-23 07:29:23'),(13,3,'Blue','P3-BL',NULL,NULL,0,1,'2025-11-23 07:30:03','2025-11-23 07:30:03'),(14,3,'Yellow','P3-YE',NULL,NULL,0,1,'2025-11-23 07:30:03','2025-11-23 07:30:03');
/*!40000 ALTER TABLE `class_streams` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `class_subject`
--

DROP TABLE IF EXISTS `class_subject`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `class_subject` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `class_id` bigint unsigned NOT NULL,
  `subject_id` bigint unsigned NOT NULL,
  `teacher_id` bigint unsigned DEFAULT NULL,
  `is_compulsory` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `class_subjects_class_id_subject_id_unique` (`class_id`,`subject_id`),
  KEY `class_subjects_class_id_index` (`class_id`),
  KEY `class_subjects_subject_id_index` (`subject_id`),
  KEY `class_subjects_teacher_id_index` (`teacher_id`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `class_subject`
--

LOCK TABLES `class_subject` WRITE;
/*!40000 ALTER TABLE `class_subject` DISABLE KEYS */;
INSERT INTO `class_subject` VALUES (1,1,1,8,1,'2025-11-23 05:21:30','2025-11-26 12:03:53'),(2,1,2,15,1,'2025-11-23 05:23:41','2025-11-26 12:03:05'),(3,2,1,NULL,1,'2025-11-23 05:26:37','2025-11-23 07:23:17'),(4,2,2,NULL,1,'2025-11-23 05:29:40','2025-11-23 07:23:55'),(5,3,1,NULL,1,'2025-11-23 07:23:17','2025-11-23 07:23:17'),(6,4,1,NULL,1,'2025-11-23 07:23:17','2025-11-23 07:23:17'),(7,5,1,NULL,1,'2025-11-23 07:23:17','2025-11-23 07:23:17'),(8,6,1,NULL,1,'2025-11-23 07:23:17','2025-11-23 07:23:17'),(9,7,1,NULL,1,'2025-11-23 07:23:17','2025-11-23 07:23:17'),(10,3,2,NULL,1,'2025-11-23 07:23:55','2025-11-23 07:23:55'),(11,4,2,NULL,1,'2025-11-23 07:23:55','2025-11-23 07:23:55'),(12,5,2,NULL,1,'2025-11-23 07:23:55','2025-11-23 07:23:55'),(13,6,2,NULL,1,'2025-11-23 07:23:55','2025-11-23 07:23:55'),(14,7,2,NULL,1,'2025-11-23 07:23:55','2025-11-23 07:23:55'),(15,1,6,19,1,'2025-11-26 10:41:58','2025-11-26 12:30:11'),(16,1,3,16,1,'2025-11-26 10:41:58','2025-11-26 12:14:38'),(17,1,7,18,1,'2025-11-26 10:41:58','2025-11-26 12:23:36');
/*!40000 ALTER TABLE `class_subject` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `class_teacher`
--

DROP TABLE IF EXISTS `class_teacher`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `class_teacher` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `class_id` bigint unsigned NOT NULL,
  `teacher_id` bigint unsigned NOT NULL,
  `academic_year` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_class_teacher` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `class_teacher_class_id_teacher_id_academic_year_unique` (`class_id`,`teacher_id`,`academic_year`),
  KEY `class_teacher_teacher_id_foreign` (`teacher_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `class_teacher`
--

LOCK TABLES `class_teacher` WRITE;
/*!40000 ALTER TABLE `class_teacher` DISABLE KEYS */;
/*!40000 ALTER TABLE `class_teacher` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `classes`
--

DROP TABLE IF EXISTS `classes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `classes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `school_id` bigint unsigned NOT NULL,
  `education_level_id` bigint unsigned DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `class_teacher_id` bigint unsigned DEFAULT NULL,
  `code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `capacity` int DEFAULT NULL,
  `active_students_count` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `classes_school_id_index` (`school_id`),
  KEY `classes_education_level_id_index` (`education_level_id`),
  KEY `classes_class_teacher_id_foreign` (`class_teacher_id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `classes`
--

LOCK TABLES `classes` WRITE;
/*!40000 ALTER TABLE `classes` DISABLE KEYS */;
INSERT INTO `classes` VALUES (1,2,2,'Primary 1',NULL,'P1',NULL,40,5,1,'2025-11-23 05:18:05','2025-11-26 11:03:57'),(2,2,2,'Primary 2',NULL,'P2',NULL,50,0,1,'2025-11-23 05:24:56','2025-11-23 05:24:56'),(3,2,2,'Primary 3',NULL,'P3',NULL,50,0,1,'2025-11-23 05:30:38','2025-11-23 05:30:38'),(4,2,2,'Primary 4',NULL,'P4',NULL,50,0,1,'2025-11-23 05:31:02','2025-11-23 05:31:02'),(5,2,2,'Primary 5',NULL,'P5',NULL,50,0,1,'2025-11-23 05:31:20','2025-11-23 05:31:20'),(6,2,2,'Primary 6',NULL,'P6',NULL,50,0,1,'2025-11-23 05:31:39','2025-11-23 05:31:39'),(7,2,2,'Primary 7',NULL,'P7',NULL,50,0,1,'2025-11-23 05:32:01','2025-11-23 05:32:01');
/*!40000 ALTER TABLE `classes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `countries`
--

DROP TABLE IF EXISTS `countries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `countries` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `iso_code_2` varchar(2) COLLATE utf8mb4_unicode_ci NOT NULL,
  `iso_code_3` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone_code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `currency_code` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `currency_symbol` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `timezone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `flag_emoji` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `countries_iso_code_2_unique` (`iso_code_2`),
  UNIQUE KEY `countries_iso_code_3_unique` (`iso_code_3`),
  KEY `countries_iso_code_2_index` (`iso_code_2`),
  KEY `countries_is_active_index` (`is_active`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `countries`
--

LOCK TABLES `countries` WRITE;
/*!40000 ALTER TABLE `countries` DISABLE KEYS */;
INSERT INTO `countries` VALUES (1,'Uganda','UG','UGA','+256','UGX','UGX','Africa/Kampala',NULL,1,'2025-11-23 05:01:39','2025-11-23 05:01:39'),(2,'Kenya','KE','KEN','+254','KSH','KSH','Africa/Nairobi','KE',1,'2025-11-23 05:02:56','2025-11-23 05:02:56');
/*!40000 ALTER TABLE `countries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `currencies`
--

DROP TABLE IF EXISTS `currencies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `currencies` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `symbol` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `exchange_rate` decimal(15,6) NOT NULL DEFAULT '1.000000',
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `auto_update_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `last_updated_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `currencies_code_unique` (`code`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `currencies`
--

LOCK TABLES `currencies` WRITE;
/*!40000 ALTER TABLE `currencies` DISABLE KEYS */;
INSERT INTO `currencies` VALUES (1,'USD','US Dollar','$',1.000000,0,1,1,NULL,'2025-11-23 07:43:37','2025-11-23 07:44:43'),(2,'UGX','Uganda Shilling','UGX',3700.000000,1,1,1,NULL,'2025-11-23 07:44:26','2025-11-23 07:44:43');
/*!40000 ALTER TABLE `currencies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `departments`
--

DROP TABLE IF EXISTS `departments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `departments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `departments`
--

LOCK TABLES `departments` WRITE;
/*!40000 ALTER TABLE `departments` DISABLE KEYS */;
INSERT INTO `departments` VALUES (1,'Administration Department','D-001',NULL,'2025-11-23 13:29:59','2025-11-23 13:29:59'),(2,'Accounts & Finance Department','D-002',NULL,'2025-11-23 13:30:53','2025-11-23 13:30:53'),(3,'Academics Department','D-003',NULL,'2025-11-23 13:31:34','2025-11-23 13:31:34'),(4,'Human Resource Department','D-004',NULL,'2025-11-23 13:32:29','2025-11-23 13:32:29'),(5,'Health / Medical Department','D-005',NULL,'2025-11-23 13:33:08','2025-11-23 13:33:08'),(6,'IT Department','D-006',NULL,'2025-11-23 13:33:37','2025-11-23 13:33:37'),(7,'Boarding & Welfare','D-007',NULL,'2025-11-23 13:34:08','2025-11-23 13:34:08'),(8,'Security Department','D-008',NULL,'2025-11-23 13:34:32','2025-11-23 13:34:32'),(9,'Maintenance & Operations','D-009',NULL,'2025-11-23 13:35:09','2025-11-23 13:35:09'),(10,'Catering / Kitchen','D-010',NULL,'2025-11-23 13:35:47','2025-11-23 13:35:47'),(11,'Transport','D-011',NULL,'2025-11-23 13:36:56','2025-11-23 13:36:56'),(12,'Sports & Co-curricular','D-012',NULL,'2025-11-23 13:37:33','2025-11-23 13:37:33'),(13,'Teaching Staff','TEA','Academic teaching staff department','2025-11-25 12:25:00','2025-11-25 12:25:00'),(14,'Administration','ADM','Administrative leadership','2025-11-25 12:33:54','2025-11-25 12:33:54');
/*!40000 ALTER TABLE `departments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `education_levels`
--

DROP TABLE IF EXISTS `education_levels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `education_levels` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `school_id` bigint unsigned NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `min_grade` int DEFAULT NULL,
  `max_grade` int DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `education_levels_school_id_index` (`school_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `education_levels`
--

LOCK TABLES `education_levels` WRITE;
/*!40000 ALTER TABLE `education_levels` DISABLE KEYS */;
INSERT INTO `education_levels` VALUES (1,2,'Pre-school','N',NULL,1,3,1,0,'2025-11-23 04:58:10','2025-11-23 04:58:10'),(2,2,'Primary','P',NULL,1,7,1,0,'2025-11-23 04:58:33','2025-11-23 04:58:33'),(3,2,'Ordinary Level','O',NULL,1,4,1,0,'2025-11-23 04:59:16','2025-11-23 05:00:08'),(4,2,'Advanced Level','A',NULL,1,2,1,0,'2025-11-23 04:59:51','2025-11-23 04:59:51');
/*!40000 ALTER TABLE `education_levels` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employee_id_settings`
--

DROP TABLE IF EXISTS `employee_id_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `employee_id_settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `template_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `card_width` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '85.6',
  `card_height` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '54',
  `background_color` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#ffffff',
  `text_color` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#000000',
  `header_text` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Employee ID Card',
  `header_color` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#2563eb',
  `logo_path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fields_to_display` json NOT NULL,
  `include_qr_code` tinyint(1) NOT NULL DEFAULT '1',
  `qr_code_position` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'bottom-right',
  `qr_code_size` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '80',
  `include_photo` tinyint(1) NOT NULL DEFAULT '1',
  `photo_position` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'top-right',
  `photo_size` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '100',
  `font_family` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Arial, sans-serif',
  `font_size` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '12',
  `layout_settings` json DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee_id_settings`
--

LOCK TABLES `employee_id_settings` WRITE;
/*!40000 ALTER TABLE `employee_id_settings` DISABLE KEYS */;
INSERT INTO `employee_id_settings` VALUES (1,'Classic Royal Blue','85.6','54','#0d47a1','#ffffff','Official Staff Identification','#0a2e6d',NULL,'[\"full_name\", \"position\", \"employee_number\", \"department\", \"phone\"]',1,'bottom-right','80',1,'top-left','110','Poppins, sans-serif','13','{\"border_color\": \"#0a2e6d\", \"border_width\": 2, \"border_radius\": 14, \"field_spacing\": 8, \"header_font_size\": 18, \"header_font_weight\": 700}',1,1,'2025-11-25 16:12:03','2025-11-25 16:12:03'),(2,'Minimal Slate','85.6','54','#ffffff','#1f2937','Employee Identification Card','#1f2937',NULL,'[\"full_name\", \"position\", \"employee_type\", \"department\"]',1,'bottom-left','70',1,'top-right','100','Inter, sans-serif','12','{\"border_color\": \"#e5e7eb\", \"border_width\": 1, \"border_radius\": 10, \"field_spacing\": 6, \"header_font_size\": 16}',0,1,'2025-11-25 16:12:03','2025-11-25 16:12:03'),(3,'Vertical Night Badge','54','85.6','#111827','#f3f4f6','Staff Access Pass','#60a5fa',NULL,'[\"full_name\", \"position\", \"employee_number\", \"hire_date\"]',1,'top-right','60',1,'center','130','Montserrat, sans-serif','12','{\"border_color\": \"#1f2937\", \"border_width\": 0, \"border_radius\": 18, \"field_spacing\": 7, \"header_font_size\": 17}',0,1,'2025-11-25 16:12:03','2025-11-25 16:12:03'),(4,'Sunset Gradient','85.6','54','#f97316','#fff7ed','Employee Pass','#fcd34d',NULL,'[\"full_name\", \"position\", \"department\", \"email\"]',1,'bottom-right','75',1,'top-left','105','Nunito, sans-serif','13','{\"border_color\": \"#fb923c\", \"border_width\": 2, \"border_radius\": 16, \"field_spacing\": 7, \"header_font_size\": 17}',0,1,'2025-11-25 16:12:03','2025-11-25 16:12:03');
/*!40000 ALTER TABLE `employee_id_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employees`
--

DROP TABLE IF EXISTS `employees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `employees` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `employee_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'full_time',
  `employee_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `national_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gender` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `department_id` bigint unsigned DEFAULT NULL,
  `position_id` bigint unsigned DEFAULT NULL,
  `salary_scale_id` bigint unsigned DEFAULT NULL,
  `first_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hire_date` date DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `employment_status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `is_teacher` tinyint(1) NOT NULL DEFAULT '0',
  `teacher_id` bigint unsigned DEFAULT NULL,
  `photo_path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `metadata` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `employees_employee_number_unique` (`employee_number`),
  UNIQUE KEY `employees_national_id_unique` (`national_id`),
  KEY `employees_department_id_foreign` (`department_id`),
  KEY `employees_position_id_foreign` (`position_id`),
  KEY `employees_salary_scale_id_foreign` (`salary_scale_id`),
  KEY `employees_user_id_foreign` (`user_id`),
  KEY `employees_teacher_id_foreign` (`teacher_id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employees`
--

LOCK TABLES `employees` WRITE;
/*!40000 ALTER TABLE `employees` DISABLE KEYS */;
INSERT INTO `employees` VALUES (1,'full_time','EMP20252788','CM1111AAAD67','male',3,8,NULL,'James','Menya','jamesmenya@example.com','+25677210628','2023-03-01','1995-09-03','active',0,NULL,'https://ui-avatars.com/api/?name=James+Menya&color=7F9CF5&background=EBF4FF',NULL,'2025-11-23 14:46:57','2025-11-23 14:46:58',5),(2,'teacher','EMP20259392',NULL,'male',13,20,NULL,'Jimmy','Musisi','jimmymusisi@example.com',NULL,NULL,NULL,'active',1,6,NULL,NULL,'2025-11-25 12:00:49','2025-11-27 10:39:08',8),(3,'full_time','EMP20257870',NULL,'female',3,8,NULL,'Patricia','Nimungu','frankhostltd3@gmail.com','256784975651','2025-01-03','1997-08-21','active',0,NULL,'passport_photos/f39d5859-e90f-4e2c-8f3f-85dc27f6cb60.JPG',NULL,'2025-11-25 15:43:23','2025-11-25 15:50:44',4),(4,'full_time','EMP-20251126-0014',NULL,NULL,NULL,NULL,NULL,'Fred','Musoke','fredmusoke@example.com',NULL,'2025-11-26',NULL,'active',0,NULL,NULL,NULL,'2025-11-26 11:02:53','2025-11-26 11:02:53',14),(5,'full_time','EMP20259020','978654333','female',3,1,NULL,'Rose','Gago','rosegago@example.com','+256709111222','2020-04-26','1990-12-22','active',1,1,NULL,NULL,'2025-11-26 12:00:51','2025-11-26 12:00:51',NULL),(6,'full_time','EMP-20251126-0016',NULL,NULL,3,8,NULL,'Sam','Mirondo','sammirondo@example.com',NULL,'2025-11-26',NULL,'active',1,2,NULL,NULL,'2025-11-26 12:11:10','2025-11-26 12:12:10',16),(7,'full_time','EMP20254373','111222333','female',3,1,NULL,'Doreen','Katusabe','doreenkatusabe@example.com','+256711000123','2019-04-13','1995-05-14','active',1,3,NULL,NULL,'2025-11-26 12:19:25','2025-11-26 12:19:25',NULL),(8,'full_time','EMP20255590','456456456','male',3,1,NULL,'Sam','Okello','samokello@example.com','+256712000101','2025-11-26','1991-06-12','active',1,4,NULL,NULL,'2025-11-26 12:22:00','2025-11-26 12:22:00',NULL),(9,'full_time','EMP20256785','8794256','female',3,1,NULL,'Martha','Gudoyi','marthagudoyi@example.com','+256704000003','2025-11-26','2001-01-30','active',1,5,NULL,NULL,'2025-11-26 12:26:47','2025-11-26 12:26:47',NULL),(10,'full_time','EMP-20251129-0020',NULL,NULL,NULL,NULL,NULL,'George','Seku','georgeseku@example.com',NULL,'2025-11-29',NULL,'active',0,NULL,NULL,NULL,'2025-11-29 09:34:03','2025-11-29 09:34:03',20);
/*!40000 ALTER TABLE `employees` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `enrollments`
--

DROP TABLE IF EXISTS `enrollments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `enrollments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint unsigned NOT NULL,
  `class_id` bigint unsigned NOT NULL,
  `class_stream_id` bigint unsigned DEFAULT NULL,
  `academic_year_id` bigint unsigned NOT NULL,
  `semester_id` bigint unsigned DEFAULT NULL,
  `enrollment_date` date NOT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `fees_paid` decimal(10,2) NOT NULL DEFAULT '0.00',
  `fees_total` decimal(10,2) NOT NULL DEFAULT '0.00',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `enrolled_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `enrollments_student_id_class_id_academic_year_id_unique` (`student_id`,`class_id`,`academic_year_id`),
  KEY `enrollments_semester_id_foreign` (`semester_id`),
  KEY `enrollments_enrolled_by_foreign` (`enrolled_by`),
  KEY `enrollments_class_id_status_index` (`class_id`,`status`),
  KEY `enrollments_academic_year_id_status_index` (`academic_year_id`,`status`),
  KEY `enrollments_enrollment_date_index` (`enrollment_date`),
  KEY `enrollments_class_stream_id_foreign` (`class_stream_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `enrollments`
--

LOCK TABLES `enrollments` WRITE;
/*!40000 ALTER TABLE `enrollments` DISABLE KEYS */;
INSERT INTO `enrollments` VALUES (1,9,1,1,1,NULL,'2025-02-25','active',0.00,800000.00,NULL,2,'2025-11-25 19:34:44','2025-11-26 07:09:52'),(2,11,1,1,1,NULL,'2025-02-26','active',0.00,0.00,'Auto-synced after approval.',2,'2025-11-26 09:10:13','2025-11-26 10:00:33'),(3,12,1,1,1,NULL,'2025-11-26','active',0.00,0.00,'Auto-synced after approval.',2,'2025-11-26 10:04:17','2025-11-26 10:16:01'),(4,13,1,1,1,NULL,'2025-03-26','active',0.00,0.00,'Auto-synced after approval.',2,'2025-11-26 10:20:20','2025-11-26 10:59:56'),(5,14,1,2,1,NULL,'2025-11-26','active',0.00,0.00,NULL,2,'2025-11-26 11:03:57','2025-11-26 11:03:57');
/*!40000 ALTER TABLE `enrollments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `examination_bodies`
--

DROP TABLE IF EXISTS `examination_bodies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `examination_bodies` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `school_id` bigint unsigned NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country_id` bigint unsigned DEFAULT NULL,
  `website` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_international` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `examination_bodies_school_id_index` (`school_id`),
  KEY `examination_bodies_country_id_index` (`country_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `examination_bodies`
--

LOCK TABLES `examination_bodies` WRITE;
/*!40000 ALTER TABLE `examination_bodies` DISABLE KEYS */;
INSERT INTO `examination_bodies` VALUES (1,2,'Uganda National Examination Board','UNEB',1,NULL,NULL,0,1,'2025-11-23 05:03:44','2025-11-23 05:03:44');
/*!40000 ALTER TABLE `examination_bodies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `exercise_attachments`
--

DROP TABLE IF EXISTS `exercise_attachments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `exercise_attachments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `exercise_id` bigint unsigned NOT NULL,
  `filename` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `original_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mime_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_size` bigint NOT NULL,
  `path` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `exercise_attachments_exercise_id_foreign` (`exercise_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `exercise_attachments`
--

LOCK TABLES `exercise_attachments` WRITE;
/*!40000 ALTER TABLE `exercise_attachments` DISABLE KEYS */;
/*!40000 ALTER TABLE `exercise_attachments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `exercise_questions`
--

DROP TABLE IF EXISTS `exercise_questions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `exercise_questions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `exercise_id` bigint unsigned NOT NULL,
  `type` enum('multiple_choice','true_false','short_answer','essay','fill_blank','matching') COLLATE utf8mb4_unicode_ci NOT NULL,
  `question` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` json DEFAULT NULL,
  `correct_answer` json DEFAULT NULL,
  `marks` decimal(8,2) NOT NULL DEFAULT '1.00',
  `order` int NOT NULL DEFAULT '0',
  `explanation` text COLLATE utf8mb4_unicode_ci,
  `is_required` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `exercise_questions_exercise_id_order_index` (`exercise_id`,`order`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `exercise_questions`
--

LOCK TABLES `exercise_questions` WRITE;
/*!40000 ALTER TABLE `exercise_questions` DISABLE KEYS */;
/*!40000 ALTER TABLE `exercise_questions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `exercise_submissions`
--

DROP TABLE IF EXISTS `exercise_submissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `exercise_submissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `exercise_id` bigint unsigned NOT NULL,
  `student_id` bigint unsigned NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci,
  `question_answers` json DEFAULT NULL,
  `file_path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `submitted_at` datetime NOT NULL,
  `is_late` tinyint(1) NOT NULL DEFAULT '0',
  `score` decimal(5,2) DEFAULT NULL,
  `auto_score` decimal(8,2) DEFAULT NULL,
  `manual_score` decimal(8,2) DEFAULT NULL,
  `is_graded` tinyint(1) NOT NULL DEFAULT '0',
  `grade` decimal(5,2) DEFAULT NULL,
  `teacher_feedback` text COLLATE utf8mb4_unicode_ci,
  `graded_by` bigint unsigned DEFAULT NULL,
  `graded_at` datetime DEFAULT NULL,
  `status` enum('submitted','graded','returned') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'submitted',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `exercise_submissions_exercise_id_student_id_unique` (`exercise_id`,`student_id`),
  KEY `exercise_submissions_student_id_foreign` (`student_id`),
  KEY `exercise_submissions_graded_by_foreign` (`graded_by`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `exercise_submissions`
--

LOCK TABLES `exercise_submissions` WRITE;
/*!40000 ALTER TABLE `exercise_submissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `exercise_submissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `exercises`
--

DROP TABLE IF EXISTS `exercises`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `exercises` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `school_id` bigint unsigned DEFAULT NULL,
  `teacher_id` bigint unsigned NOT NULL,
  `class_id` bigint unsigned NOT NULL,
  `subject_id` bigint unsigned NOT NULL,
  `lesson_plan_id` bigint unsigned DEFAULT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `instructions` text COLLATE utf8mb4_unicode_ci,
  `content` longtext COLLATE utf8mb4_unicode_ci,
  `assigned_at` datetime DEFAULT NULL,
  `due_date` datetime DEFAULT NULL,
  `max_score` int NOT NULL DEFAULT '100',
  `allow_late_submission` tinyint(1) NOT NULL DEFAULT '0',
  `late_penalty_percent` int NOT NULL DEFAULT '0',
  `submission_type` enum('file','text','both') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'both',
  `attachments` json DEFAULT NULL,
  `auto_grade` tinyint(1) NOT NULL DEFAULT '0',
  `show_answers_after_submit` tinyint(1) NOT NULL DEFAULT '0',
  `allow_file_upload` tinyint(1) NOT NULL DEFAULT '1',
  `allow_text_response` tinyint(1) NOT NULL DEFAULT '1',
  `attachment_path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `allowed_file_types` json DEFAULT NULL,
  `max_file_size_mb` int NOT NULL DEFAULT '10',
  `max_file_size` int NOT NULL DEFAULT '10240',
  `is_graded` tinyint(1) NOT NULL DEFAULT '1',
  `status` enum('draft','published','closed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `rubric` json DEFAULT NULL,
  `plagiarism_check_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `peer_review_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `peer_review_count` int NOT NULL DEFAULT '1',
  `version` int NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `exercises_subject_id_foreign` (`subject_id`),
  KEY `exercises_lesson_plan_id_foreign` (`lesson_plan_id`),
  KEY `exercises_teacher_id_due_date_index` (`teacher_id`,`due_date`),
  KEY `exercises_class_id_status_index` (`class_id`,`status`),
  KEY `exercises_school_id_foreign` (`school_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `exercises`
--

LOCK TABLES `exercises` WRITE;
/*!40000 ALTER TABLE `exercises` DISABLE KEYS */;
INSERT INTO `exercises` VALUES (1,NULL,8,1,1,NULL,'Numbers in Everyday Life: Counting Objects at Home','This assignment helps Primary One pupils strengthen their number skills by practicing counting, simple addition, and recognizing numbers up to 20. Learners will work through short exercises that encourage accuracy, confidence, and a love for mathematics.','Write your name at the top of your work.\r\n\r\nCount the objects in each question and write the correct number.\r\n\r\nSolve the addition problems carefully.\r\n\r\nShow your working where possible (e.g., draw lines or dots to help you count).\r\n\r\nAnswer all questions neatly in your exercise book.\r\n\r\nSubmit your assignment by the due date.',NULL,NULL,'2025-11-30 00:31:00',5,1,5,'both',NULL,0,0,1,1,NULL,NULL,10,10240,1,'draft','2025-11-27 18:36:23','2025-11-28 11:30:56',NULL,NULL,0,0,1,1),(2,NULL,8,1,1,NULL,'Numbers up to 100 Problem Set','<p>This problem set helps students practice counting, reading, and writing numbers up to 100. It includes exercises on ordering numbers, identifying missing numbers in sequences, and solving simple addition and subtraction problems within this range. The goal is to strengthen number sense, build confidence in working with larger numbers, and prepare learners for more advanced mathematical concepts.</p>','<ul><li><p>Write your name and class at the top of your work.</p></li><li><p>Read each question carefully before answering.</p></li><li><p>Count and write numbers correctly up to 100.</p></li><li><p>Fill in missing numbers in sequences (e.g., 45, 46, __, 48).</p></li><li><p>Compare numbers and circle the bigger or smaller one when asked.</p></li><li><p>Solve simple addition and subtraction problems within 100.</p></li><li><p>Show your working where possible, not just the final answer.</p></li><li><p>Check your answers before submitting.</p></li><li><p>Submit your work by the due date.</p></li><li><p>Remember: neatness and accuracy will earn you more marks!</p></li></ul>',NULL,NULL,'2025-11-28 13:37:00',10,1,2,'both',NULL,0,0,1,1,NULL,NULL,10,10240,1,'draft','2025-11-28 07:38:12','2025-11-28 07:45:25','2025-11-28 07:45:25',NULL,0,0,1,1),(3,NULL,8,1,1,NULL,'Numbers up to 100 Problem Set','<p>This problem set helps students practice counting, reading, and writing numbers up to 100. It includes exercises on ordering numbers, identifying missing numbers in sequences, and solving simple addition and subtraction problems within this range. The goal is to strengthen number sense, build confidence in working with larger numbers, and prepare learners for more advanced mathematical concepts.</p>','<ul><li><p>Write your name and class at the top of your work.</p></li><li><p>Read each question carefully before answering.</p></li><li><p>Count and write numbers correctly up to 100.</p></li><li><p>Fill in missing numbers in sequences (e.g., 45, 46, __, 48).</p></li><li><p>Compare numbers and circle the bigger or smaller one when asked.</p></li><li><p>Solve simple addition and subtraction problems within 100.</p></li><li><p>Show your working where possible, not just the final answer.</p></li><li><p>Check your answers before submitting.</p></li><li><p>Submit your work by the due date.</p></li><li><p>Remember: neatness and accuracy will earn you more marks!</p></li></ul>',NULL,NULL,'2025-11-28 13:47:00',10,1,2,'both',NULL,0,0,1,1,NULL,NULL,10,10240,1,'draft','2025-11-28 07:47:25','2025-11-28 07:54:53','2025-11-28 07:54:53',NULL,0,0,1,1),(4,NULL,8,1,1,NULL,'Let’s explore numbers together and see how far you can count!','<p>This assignment focuses on strengthening students’ skills in basic addition and subtraction. Learners will solve problems using numbers up to 100, practice combining and separating groups, and apply strategies such as counting on, counting back, and using number lines. The exercises are designed to build accuracy, confidence, and speed in solving everyday math problems.</p>','<ol><li><p>Write your name and class at the top of your work.</p></li><li><p>Read each question carefully before answering.</p></li><li><p>Use numbers up to 100 for all problems.</p></li><li><p>For <strong>addition problems</strong>, count forward or use number lines to find the answer.</p></li><li><p>For <strong>subtraction problems</strong>, count backward or use number lines to solve.</p></li><li><p>Show your working clearly, not just the final answer.</p></li><li><p>Check each answer after you finish to avoid mistakes.</p></li><li><p>Keep your work neat and organized.</p></li><li><p>Submit your assignment by the due date.</p></li><li><p>Remember: accuracy and effort will earn you more marks!</p></li></ol>','<p><strong>Part A: Simple Addition</strong></p><ol start=\"1\"><li><p>12 + 5 = ____</p></li><li><p>23 + 7 = ____</p></li><li><p>45 + 10 = ____</p></li><li><p>8 + 9 = ____</p></li><li><p>30 + 25 = ____</p></li></ol><p><strong>Part B: Simple Subtraction</strong></p><ol start=\"6\"><li><p>20 − 6 = ____</p></li><li><p>50 − 15 = ____</p></li><li><p>18 − 9 = ____</p></li><li><p>72 − 30 = ____</p></li><li><p>100 − 45 = ____</p></li></ol><p><strong>Part C: Word Problems</strong></p><ol start=\"11\"><li><p>Sarah has 12 apples. She buys 8 more. How many apples does she have now?</p></li><li><p>A basket has 25 oranges. If 10 are eaten, how many are left?</p></li><li><p>James had 40 shillings. He spent 15 shillings. How much money does he have left?</p></li><li><p>There are 18 birds in a tree. 7 fly away. How many birds remain?</p></li><li><p>A shop sells 35 pencils in the morning and 20 pencils in the afternoon. How many pencils are sold in total?</p></li></ol>',NULL,'2025-11-28 17:29:00',20,1,5,'both','[]',0,0,1,1,NULL,NULL,10,10240,1,'draft','2025-11-28 11:29:30','2025-11-28 11:29:30',NULL,NULL,0,0,1,1),(5,NULL,8,1,1,NULL,'Counting and Comparing Numbers Assignment','<p>This assignment helps students practice counting numbers up to 50, identifying missing numbers in sequences, and comparing which numbers are greater or smaller. It strengthens number sense and prepares learners for addition and subtraction tasks.</p>','<ol><li>Write your name and class at the top of your work.</li><li>Count carefully and write the missing numbers in each sequence.</li><li>Circle the bigger number when asked to compare.</li><li>Answer all questions neatly and clearly.</li><li>Check your work before submitting.</li><li>Submit your assignment by the due date.</li></ol>','<p><strong>Part A: Counting</strong></p><ol><li>Count from 1 to 20 and write the numbers.</li><li>Fill in the missing numbers: 5, 6, __, 8, 9, __, 11.</li><li>Fill in the missing numbers: 15, __, 17, __, 19, 20.</li></ol><p><strong>Part B: Comparing Numbers</strong></p><ol><li>Circle the bigger number: 12 or 9</li><li>Circle the smaller number: 25 or 30</li><li>Which is greater: 18 or 21?</li></ol><p><strong>Part C: Word Problems</strong></p><ol><li>Mary has 7 sweets. John has 10 sweets. Who has more?</li><li>A basket has 20 mangoes. If 5 are taken out, how many are left?</li><li>Peter counted 14 cows in the field. His friend counted 12. Who counted more?</li></ol>',NULL,'2025-12-02 18:06:00',20,1,5,'both','[]',0,0,1,1,NULL,NULL,10,10240,1,'draft','2025-11-28 12:16:17','2025-11-28 12:16:17',NULL,NULL,0,0,1,1);
/*!40000 ALTER TABLE `exercises` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `expense_categories`
--

DROP TABLE IF EXISTS `expense_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `expense_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `school_id` bigint unsigned NOT NULL,
  `parent_id` bigint unsigned DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `color` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#6c757d',
  `icon` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'bi-receipt',
  `budget_limit` decimal(15,2) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `expense_categories_school_id_index` (`school_id`),
  KEY `expense_categories_is_active_index` (`is_active`),
  KEY `expense_categories_parent_id_foreign` (`parent_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `expense_categories`
--

LOCK TABLES `expense_categories` WRITE;
/*!40000 ALTER TABLE `expense_categories` DISABLE KEYS */;
INSERT INTO `expense_categories` VALUES (1,2,NULL,'Transport','1000',NULL,'#4694d8','bi-truck-front',1600000.00,1,'2025-11-23 07:35:36','2025-11-23 07:35:36');
/*!40000 ALTER TABLE `expense_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `expenses`
--

DROP TABLE IF EXISTS `expenses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `expenses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `school_id` bigint unsigned DEFAULT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `amount` decimal(15,2) NOT NULL,
  `currency_id` bigint unsigned NOT NULL,
  `expense_category_id` bigint unsigned NOT NULL,
  `expense_date` date NOT NULL,
  `payment_method` enum('cash','bank_transfer','credit_card','debit_card','check','online_payment','other') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'cash',
  `reference_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vendor_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vendor_contact` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `receipt_path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('pending','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `approved_by` bigint unsigned DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `rejected_reason` text COLLATE utf8mb4_unicode_ci,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint unsigned NOT NULL,
  `tenant_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `expenses_currency_id_foreign` (`currency_id`),
  KEY `expenses_approved_by_foreign` (`approved_by`),
  KEY `expenses_created_by_foreign` (`created_by`),
  KEY `expenses_tenant_id_status_index` (`tenant_id`,`status`),
  KEY `expenses_expense_date_tenant_id_index` (`expense_date`,`tenant_id`),
  KEY `expenses_expense_category_id_tenant_id_index` (`expense_category_id`,`tenant_id`),
  KEY `expenses_school_id_index` (`school_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `expenses`
--

LOCK TABLES `expenses` WRITE;
/*!40000 ALTER TABLE `expenses` DISABLE KEYS */;
/*!40000 ALTER TABLE `expenses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fee_payments`
--

DROP TABLE IF EXISTS `fee_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fee_payments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `fee_invoice_id` bigint unsigned NOT NULL,
  `student_id` bigint unsigned NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `currency` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'UGX',
  `method` enum('cash','bank','mtn','airtel','card') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'cash',
  `reference` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `paid_at` datetime DEFAULT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'confirmed',
  `meta` json DEFAULT NULL,
  `received_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fee_payments_fee_invoice_id_foreign` (`fee_invoice_id`),
  KEY `fee_payments_student_id_foreign` (`student_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fee_payments`
--

LOCK TABLES `fee_payments` WRITE;
/*!40000 ALTER TABLE `fee_payments` DISABLE KEYS */;
/*!40000 ALTER TABLE `fee_payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fee_structures`
--

DROP TABLE IF EXISTS `fee_structures`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fee_structures` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `school_id` bigint unsigned NOT NULL,
  `fee_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fee_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `academic_year` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `term` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `class` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `is_mandatory` tinyint(1) NOT NULL DEFAULT '1',
  `is_recurring` tinyint(1) NOT NULL DEFAULT '0',
  `frequency` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'once, per_term, per_year',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fee_structures_school_id_academic_year_index` (`school_id`,`academic_year`),
  KEY `fee_structures_school_id_class_index` (`school_id`,`class`),
  KEY `fee_structures_is_active_index` (`is_active`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fee_structures`
--

LOCK TABLES `fee_structures` WRITE;
/*!40000 ALTER TABLE `fee_structures` DISABLE KEYS */;
INSERT INTO `fee_structures` VALUES (1,2,'Tution Fees','tuition',800000.00,'2025','1',NULL,'2025-02-02',0,0,NULL,1,NULL,'2025-11-26 14:18:18','2025-11-26 14:18:18');
/*!40000 ALTER TABLE `fee_structures` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `forum_posts`
--

DROP TABLE IF EXISTS `forum_posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `forum_posts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `school_id` bigint unsigned NOT NULL,
  `forum_thread_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `parent_id` bigint unsigned DEFAULT NULL,
  `is_solution` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `forum_posts_school_id_foreign` (`school_id`),
  KEY `forum_posts_forum_thread_id_foreign` (`forum_thread_id`),
  KEY `forum_posts_user_id_foreign` (`user_id`),
  KEY `forum_posts_parent_id_foreign` (`parent_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `forum_posts`
--

LOCK TABLES `forum_posts` WRITE;
/*!40000 ALTER TABLE `forum_posts` DISABLE KEYS */;
/*!40000 ALTER TABLE `forum_posts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `forum_threads`
--

DROP TABLE IF EXISTS `forum_threads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `forum_threads` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `school_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci,
  `context_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `context_id` bigint unsigned DEFAULT NULL,
  `status` enum('active','closed','blocked') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `moderator_id` bigint unsigned DEFAULT NULL,
  `is_pinned` tinyint(1) NOT NULL DEFAULT '0',
  `views_count` bigint unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `forum_threads_user_id_foreign` (`user_id`),
  KEY `forum_threads_moderator_id_foreign` (`moderator_id`),
  KEY `forum_threads_school_id_context_type_context_id_index` (`school_id`,`context_type`,`context_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `forum_threads`
--

LOCK TABLES `forum_threads` WRITE;
/*!40000 ALTER TABLE `forum_threads` DISABLE KEYS */;
/*!40000 ALTER TABLE `forum_threads` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `grades`
--

DROP TABLE IF EXISTS `grades`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `grades` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint unsigned NOT NULL,
  `subject_id` bigint unsigned NOT NULL,
  `teacher_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `class_id` bigint unsigned DEFAULT NULL,
  `semester_id` bigint unsigned DEFAULT NULL,
  `assessment_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `assessment_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `marks_obtained` decimal(5,2) DEFAULT NULL,
  `total_marks` decimal(5,2) DEFAULT NULL,
  `grade_letter` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `grade_point` decimal(3,2) DEFAULT NULL,
  `assessment_date` date DEFAULT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `is_published` tinyint(1) NOT NULL DEFAULT '0',
  `entered_by` bigint unsigned DEFAULT NULL,
  `published_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `grades_student_id_subject_id_term_unique` (`student_id`,`subject_id`),
  KEY `grades_subject_id_foreign` (`subject_id`),
  KEY `grades_teacher_id_foreign` (`teacher_id`),
  KEY `grades_class_id_foreign` (`class_id`),
  KEY `grades_semester_id_foreign` (`semester_id`),
  KEY `grades_entered_by_foreign` (`entered_by`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `grades`
--

LOCK TABLES `grades` WRITE;
/*!40000 ALTER TABLE `grades` DISABLE KEYS */;
/*!40000 ALTER TABLE `grades` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `grading_bands`
--

DROP TABLE IF EXISTS `grading_bands`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `grading_bands` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `grading_scheme_id` bigint unsigned NOT NULL,
  `grade` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `label` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `min_score` decimal(5,2) NOT NULL,
  `max_score` decimal(5,2) NOT NULL,
  `grade_point` decimal(4,2) DEFAULT NULL COMMENT 'GPA equivalent',
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `grading_bands_grading_scheme_id_sort_order_index` (`grading_scheme_id`,`sort_order`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `grading_bands`
--

LOCK TABLES `grading_bands` WRITE;
/*!40000 ALTER TABLE `grading_bands` DISABLE KEYS */;
INSERT INTO `grading_bands` VALUES (8,1,'D','Basic',50.00,59.00,NULL,NULL,3,'2025-11-23 05:17:03','2025-11-23 05:17:03'),(7,1,'C','Satisfactory',60.00,69.00,NULL,NULL,2,'2025-11-23 05:17:03','2025-11-23 05:17:03'),(6,1,'B','Outstanding',70.00,79.00,NULL,NULL,1,'2025-11-23 05:17:03','2025-11-23 05:17:03'),(5,1,'A','Exceptional',80.00,100.00,NULL,NULL,0,'2025-11-23 05:17:03','2025-11-23 05:17:03'),(9,1,'E','Elementary',1.00,49.00,NULL,NULL,4,'2025-11-23 05:17:03','2025-11-23 05:17:03');
/*!40000 ALTER TABLE `grading_bands` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `grading_schemes`
--

DROP TABLE IF EXISTS `grading_schemes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `grading_schemes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `school_id` bigint unsigned NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `country` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `examination_body_id` bigint unsigned DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_current` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Currently active scheme',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `grading_schemes_examination_body_id_foreign` (`examination_body_id`),
  KEY `grading_schemes_school_id_is_current_index` (`school_id`,`is_current`),
  KEY `grading_schemes_school_id_is_active_index` (`school_id`,`is_active`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `grading_schemes`
--

LOCK TABLES `grading_schemes` WRITE;
/*!40000 ALTER TABLE `grading_schemes` DISABLE KEYS */;
INSERT INTO `grading_schemes` VALUES (1,2,'UNEB Primary Division System','Uganda',1,NULL,1,1,'2025-11-23 05:16:14','2025-11-23 05:16:14');
/*!40000 ALTER TABLE `grading_schemes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `invoices`
--

DROP TABLE IF EXISTS `invoices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `invoices` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `school_id` bigint unsigned DEFAULT NULL,
  `invoice_number` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `student_id` bigint unsigned NOT NULL,
  `fee_structure_id` bigint unsigned DEFAULT NULL,
  `parent_id` bigint unsigned DEFAULT NULL,
  `issue_date` date NOT NULL,
  `due_date` date NOT NULL,
  `subtotal` decimal(10,2) NOT NULL DEFAULT '0.00',
  `tax_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `discount_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `paid_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `balance` decimal(12,2) NOT NULL DEFAULT '0.00',
  `status` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'unpaid',
  `academic_year` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `term` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `cancellation_reason` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint unsigned NOT NULL,
  `sent_at` timestamp NULL DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `cancelled_by` bigint unsigned DEFAULT NULL,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `deletion_reason` text COLLATE utf8mb4_unicode_ci,
  `revision_reason` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  UNIQUE KEY `invoices_invoice_number_unique` (`invoice_number`),
  KEY `invoices_parent_id_foreign` (`parent_id`),
  KEY `invoices_created_by_foreign` (`created_by`),
  KEY `invoices_school_id_index` (`school_id`),
  KEY `invoices_student_id_foreign` (`student_id`),
  KEY `invoices_fee_structure_id_foreign` (`fee_structure_id`),
  KEY `invoices_cancelled_by_foreign` (`cancelled_by`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `invoices`
--

LOCK TABLES `invoices` WRITE;
/*!40000 ALTER TABLE `invoices` DISABLE KEYS */;
INSERT INTO `invoices` VALUES (1,2,'INV202500001',14,1,NULL,'2025-11-29','2025-12-06',0.00,0.00,0.00,800000.00,0.00,800000.00,'unpaid','2025','Term 3',NULL,NULL,2,NULL,NULL,'2025-11-29 16:16:36','2025-11-29 16:16:36',NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `invoices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=MyISAM AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
INSERT INTO `jobs` VALUES (1,'default','{\"uuid\":\"1b44dde8-ebd7-428c-8e2b-18ce2b47945b\",\"displayName\":\"App\\\\Notifications\\\\UserApprovedNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:6;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:6:\\\"tenant\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:42:\\\"App\\\\Notifications\\\\UserApprovedNotification\\\":1:{s:2:\\\"id\\\";s:36:\\\"388f2509-ab54-4d30-9290-1dca5da846c0\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:4:\\\"mail\\\";}}\"},\"createdAt\":1764009107,\"delay\":null}',0,NULL,1764009107,1764009107),(2,'default','{\"uuid\":\"6f2acfb2-7f11-4bd5-8f20-79505c8654e5\",\"displayName\":\"App\\\\Notifications\\\\UserApprovedNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:7;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:6:\\\"tenant\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:42:\\\"App\\\\Notifications\\\\UserApprovedNotification\\\":1:{s:2:\\\"id\\\";s:36:\\\"746bf709-e2ba-4021-96bb-a1d4a8842a5b\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:4:\\\"mail\\\";}}\"},\"createdAt\":1764013272,\"delay\":null}',0,NULL,1764013272,1764013272),(3,'default','{\"uuid\":\"31915610-7e00-466e-8232-c7c2a578eb39\",\"displayName\":\"App\\\\Notifications\\\\UserApprovedNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:8;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:6:\\\"tenant\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:42:\\\"App\\\\Notifications\\\\UserApprovedNotification\\\":1:{s:2:\\\"id\\\";s:36:\\\"b4e22dcc-7402-4f4d-811f-3683430fa6db\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:4:\\\"mail\\\";}}\"},\"createdAt\":1764014998,\"delay\":null}',0,NULL,1764014998,1764014998),(4,'default','{\"uuid\":\"d38797b9-6cea-4011-b32a-41ef2d2f139c\",\"displayName\":\"App\\\\Notifications\\\\UserApprovedNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:9;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:6:\\\"tenant\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:42:\\\"App\\\\Notifications\\\\UserApprovedNotification\\\":1:{s:2:\\\"id\\\";s:36:\\\"1ae0c311-46a8-46e7-bc08-fe96522b64ad\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:4:\\\"mail\\\";}}\"},\"createdAt\":1764108653,\"delay\":null}',0,NULL,1764108653,1764108653),(5,'default','{\"uuid\":\"883eb3d3-0925-4d29-a5da-2861a25553b2\",\"displayName\":\"App\\\\Notifications\\\\StudentEnrolledToClass\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:9;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:6:\\\"tenant\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:40:\\\"App\\\\Notifications\\\\StudentEnrolledToClass\\\":4:{s:9:\\\"className\\\";s:9:\\\"Primary 1\\\";s:10:\\\"streamName\\\";N;s:6:\\\"status\\\";s:6:\\\"active\\\";s:2:\\\"id\\\";s:36:\\\"b7d89b7d-3f70-4e38-ab81-07c6da4b6518\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:4:\\\"mail\\\";}}\"},\"createdAt\":1764110084,\"delay\":null}',0,NULL,1764110084,1764110084),(6,'default','{\"uuid\":\"1eb777dd-4300-4208-be54-316c18f68963\",\"displayName\":\"App\\\\Notifications\\\\StudentEnrolledToClass\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:11;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:6:\\\"tenant\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:40:\\\"App\\\\Notifications\\\\StudentEnrolledToClass\\\":4:{s:9:\\\"className\\\";s:9:\\\"Primary 1\\\";s:10:\\\"streamName\\\";s:4:\\\"Blue\\\";s:6:\\\"status\\\";s:7:\\\"pending\\\";s:2:\\\"id\\\";s:36:\\\"2636254d-d1f9-4a8c-afcb-200492e2f4e9\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:4:\\\"mail\\\";}}\"},\"createdAt\":1764159013,\"delay\":null}',0,NULL,1764159013,1764159013),(7,'default','{\"uuid\":\"6689e3c1-d2d1-4eb6-a075-aa4e8e106562\",\"displayName\":\"App\\\\Notifications\\\\UserApprovedNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:11;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:6:\\\"tenant\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:42:\\\"App\\\\Notifications\\\\UserApprovedNotification\\\":1:{s:2:\\\"id\\\";s:36:\\\"bddb24c2-a9e8-4cbd-b6f3-ffac21575959\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:4:\\\"mail\\\";}}\"},\"createdAt\":1764159124,\"delay\":null}',0,NULL,1764159124,1764159124),(8,'default','{\"uuid\":\"8e693d28-ccda-4047-8e18-f3a7b567dd09\",\"displayName\":\"App\\\\Notifications\\\\StudentEnrolledToClass\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:12;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:6:\\\"tenant\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:40:\\\"App\\\\Notifications\\\\StudentEnrolledToClass\\\":4:{s:9:\\\"className\\\";s:9:\\\"Primary 1\\\";s:10:\\\"streamName\\\";s:4:\\\"Blue\\\";s:6:\\\"status\\\";s:7:\\\"pending\\\";s:2:\\\"id\\\";s:36:\\\"8a8bd9f4-c52a-4d75-ad84-81ca129a9f69\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:4:\\\"mail\\\";}}\"},\"createdAt\":1764162257,\"delay\":null}',0,NULL,1764162257,1764162257),(9,'default','{\"uuid\":\"c5a4a212-b731-4892-9a15-ca72eb7012ce\",\"displayName\":\"App\\\\Notifications\\\\UserApprovedNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:12;}s:9:\\\"relations\\\";a:1:{i:0;s:5:\\\"roles\\\";}s:10:\\\"connection\\\";s:6:\\\"tenant\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:42:\\\"App\\\\Notifications\\\\UserApprovedNotification\\\":1:{s:2:\\\"id\\\";s:36:\\\"b778d578-e477-4869-a0c5-a9da9738578e\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:4:\\\"mail\\\";}}\"},\"createdAt\":1764162432,\"delay\":null}',0,NULL,1764162432,1764162432),(10,'default','{\"uuid\":\"f81f1221-8197-4811-b379-c183b5e1bca7\",\"displayName\":\"App\\\\Notifications\\\\StudentEnrolledToClass\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:13;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:6:\\\"tenant\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:40:\\\"App\\\\Notifications\\\\StudentEnrolledToClass\\\":4:{s:9:\\\"className\\\";s:9:\\\"Primary 1\\\";s:10:\\\"streamName\\\";s:4:\\\"Blue\\\";s:6:\\\"status\\\";s:7:\\\"pending\\\";s:2:\\\"id\\\";s:36:\\\"51469ab5-9c25-41e0-b2a8-4bc7a9edfa2e\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:4:\\\"mail\\\";}}\"},\"createdAt\":1764163220,\"delay\":null}',0,NULL,1764163220,1764163220),(11,'default','{\"uuid\":\"6ea84862-4f95-4fde-b946-e3fc27763083\",\"displayName\":\"App\\\\Notifications\\\\UserApprovedNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:13;}s:9:\\\"relations\\\";a:1:{i:0;s:5:\\\"roles\\\";}s:10:\\\"connection\\\";s:6:\\\"tenant\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:42:\\\"App\\\\Notifications\\\\UserApprovedNotification\\\":1:{s:2:\\\"id\\\";s:36:\\\"5ad15434-32ad-4643-a780-5d9652473559\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:4:\\\"mail\\\";}}\"},\"createdAt\":1764163377,\"delay\":null}',0,NULL,1764163377,1764163377),(12,'default','{\"uuid\":\"c164e12b-c3e5-4112-a6a7-a4e69a9507ee\",\"displayName\":\"App\\\\Notifications\\\\UserApprovedNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:14;}s:9:\\\"relations\\\";a:1:{i:0;s:5:\\\"roles\\\";}s:10:\\\"connection\\\";s:6:\\\"tenant\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:42:\\\"App\\\\Notifications\\\\UserApprovedNotification\\\":1:{s:2:\\\"id\\\";s:36:\\\"e6716c56-92bf-4d47-a647-e3014c9ea727\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:4:\\\"mail\\\";}}\"},\"createdAt\":1764165773,\"delay\":null}',0,NULL,1764165773,1764165773),(13,'default','{\"uuid\":\"834a8aef-eb2b-4949-ada9-e284746f5a74\",\"displayName\":\"App\\\\Notifications\\\\UserRejectedNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:3;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:6:\\\"tenant\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:42:\\\"App\\\\Notifications\\\\UserRejectedNotification\\\":2:{s:6:\\\"reason\\\";s:17:\\\"Record not proper\\\";s:2:\\\"id\\\";s:36:\\\"b552353f-9018-4798-9a73-657996fdf96b\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:4:\\\"mail\\\";}}\"},\"createdAt\":1764169837,\"delay\":null}',0,NULL,1764169837,1764169837),(14,'default','{\"uuid\":\"da30cad8-2820-49af-95ae-013362b64cd0\",\"displayName\":\"App\\\\Notifications\\\\UserRejectedNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:4;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:6:\\\"tenant\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:42:\\\"App\\\\Notifications\\\\UserRejectedNotification\\\":2:{s:6:\\\"reason\\\";s:17:\\\"Record not proper\\\";s:2:\\\"id\\\";s:36:\\\"84cb8bbc-2182-40c9-891d-858439f278d0\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:4:\\\"mail\\\";}}\"},\"createdAt\":1764169853,\"delay\":null}',0,NULL,1764169853,1764169853),(15,'default','{\"uuid\":\"f52ac021-b4c2-4074-a7da-322b4750b85c\",\"displayName\":\"App\\\\Notifications\\\\UserApprovedNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:16;}s:9:\\\"relations\\\";a:1:{i:0;s:5:\\\"roles\\\";}s:10:\\\"connection\\\";s:6:\\\"tenant\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:42:\\\"App\\\\Notifications\\\\UserApprovedNotification\\\":1:{s:2:\\\"id\\\";s:36:\\\"cb4fd1ef-fbcb-40fb-a876-a1c5a7e48708\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:4:\\\"mail\\\";}}\"},\"createdAt\":1764169870,\"delay\":null}',0,NULL,1764169870,1764169870),(16,'default','{\"uuid\":\"37bd737e-1f10-4013-8892-f680ff851258\",\"displayName\":\"App\\\\Notifications\\\\UserApprovedNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:20;}s:9:\\\"relations\\\";a:1:{i:0;s:5:\\\"roles\\\";}s:10:\\\"connection\\\";s:6:\\\"tenant\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:42:\\\"App\\\\Notifications\\\\UserApprovedNotification\\\":1:{s:2:\\\"id\\\";s:36:\\\"9d3b148d-0493-433d-9ddc-20c1e0460935\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:4:\\\"mail\\\";}}\"},\"createdAt\":1764419643,\"delay\":null}',0,NULL,1764419643,1764419643),(17,'default','{\"uuid\":\"ccd34763-a4c3-4921-bb96-c2fca4674a9f\",\"displayName\":\"App\\\\Notifications\\\\InvoiceNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:14;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:6:\\\"tenant\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\InvoiceNotification\\\":3:{s:10:\\\"\\u0000*\\u0000invoice\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:18:\\\"App\\\\Models\\\\Invoice\\\";s:2:\\\"id\\\";i:1;s:9:\\\"relations\\\";a:1:{i:0;s:7:\\\"student\\\";}s:10:\\\"connection\\\";s:6:\\\"tenant\\\";s:15:\\\"collectionClass\\\";N;}s:16:\\\"\\u0000*\\u0000recipientType\\\";s:7:\\\"student\\\";s:2:\\\"id\\\";s:36:\\\"e6a3dc73-c3d0-4c30-9d06-724f6b625cec\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:4:\\\"mail\\\";}}\"},\"createdAt\":1764443796,\"delay\":null}',0,NULL,1764443796,1764443796),(18,'default','{\"uuid\":\"3e1ee5f8-b2fc-488a-a298-74fe386d06af\",\"displayName\":\"App\\\\Notifications\\\\InvoiceNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:14;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:6:\\\"tenant\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\InvoiceNotification\\\":3:{s:10:\\\"\\u0000*\\u0000invoice\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:18:\\\"App\\\\Models\\\\Invoice\\\";s:2:\\\"id\\\";i:1;s:9:\\\"relations\\\";a:1:{i:0;s:7:\\\"student\\\";}s:10:\\\"connection\\\";s:6:\\\"tenant\\\";s:15:\\\"collectionClass\\\";N;}s:16:\\\"\\u0000*\\u0000recipientType\\\";s:7:\\\"student\\\";s:2:\\\"id\\\";s:36:\\\"e6a3dc73-c3d0-4c30-9d06-724f6b625cec\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}}\"},\"createdAt\":1764443796,\"delay\":null}',0,NULL,1764443796,1764443796),(19,'default','{\"uuid\":\"e9bfd6e1-7186-49d7-be2d-dcba1bb89323\",\"displayName\":\"App\\\\Notifications\\\\InvoiceNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:14;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:6:\\\"tenant\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\InvoiceNotification\\\":3:{s:10:\\\"\\u0000*\\u0000invoice\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:18:\\\"App\\\\Models\\\\Invoice\\\";s:2:\\\"id\\\";i:2;s:9:\\\"relations\\\";a:1:{i:0;s:7:\\\"student\\\";}s:10:\\\"connection\\\";s:6:\\\"tenant\\\";s:15:\\\"collectionClass\\\";N;}s:16:\\\"\\u0000*\\u0000recipientType\\\";s:7:\\\"student\\\";s:2:\\\"id\\\";s:36:\\\"d386cbcc-57ae-40dd-b2f2-dca3924396a9\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:4:\\\"mail\\\";}}\"},\"createdAt\":1764443949,\"delay\":null}',0,NULL,1764443949,1764443949),(20,'default','{\"uuid\":\"14ab6ff0-44e4-412a-8805-8b5c20b7a297\",\"displayName\":\"App\\\\Notifications\\\\InvoiceNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:14;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:6:\\\"tenant\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\InvoiceNotification\\\":3:{s:10:\\\"\\u0000*\\u0000invoice\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:18:\\\"App\\\\Models\\\\Invoice\\\";s:2:\\\"id\\\";i:2;s:9:\\\"relations\\\";a:1:{i:0;s:7:\\\"student\\\";}s:10:\\\"connection\\\";s:6:\\\"tenant\\\";s:15:\\\"collectionClass\\\";N;}s:16:\\\"\\u0000*\\u0000recipientType\\\";s:7:\\\"student\\\";s:2:\\\"id\\\";s:36:\\\"d386cbcc-57ae-40dd-b2f2-dca3924396a9\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}}\"},\"createdAt\":1764443949,\"delay\":null}',0,NULL,1764443949,1764443949),(21,'default','{\"uuid\":\"370155f3-e9ba-46e5-ae57-48373742476e\",\"displayName\":\"App\\\\Notifications\\\\InvoiceNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:20;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:6:\\\"tenant\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\InvoiceNotification\\\":3:{s:10:\\\"\\u0000*\\u0000invoice\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:18:\\\"App\\\\Models\\\\Invoice\\\";s:2:\\\"id\\\";i:2;s:9:\\\"relations\\\";a:1:{i:0;s:7:\\\"student\\\";}s:10:\\\"connection\\\";s:6:\\\"tenant\\\";s:15:\\\"collectionClass\\\";N;}s:16:\\\"\\u0000*\\u0000recipientType\\\";s:6:\\\"parent\\\";s:2:\\\"id\\\";s:36:\\\"28dc5028-50fb-4983-b881-fb1e79196187\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:4:\\\"mail\\\";}}\"},\"createdAt\":1764443949,\"delay\":null}',0,NULL,1764443949,1764443949),(22,'default','{\"uuid\":\"d523bef2-a78e-4e4b-855b-19609cbab0ba\",\"displayName\":\"App\\\\Notifications\\\\InvoiceNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:20;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:6:\\\"tenant\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\InvoiceNotification\\\":3:{s:10:\\\"\\u0000*\\u0000invoice\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:18:\\\"App\\\\Models\\\\Invoice\\\";s:2:\\\"id\\\";i:2;s:9:\\\"relations\\\";a:1:{i:0;s:7:\\\"student\\\";}s:10:\\\"connection\\\";s:6:\\\"tenant\\\";s:15:\\\"collectionClass\\\";N;}s:16:\\\"\\u0000*\\u0000recipientType\\\";s:6:\\\"parent\\\";s:2:\\\"id\\\";s:36:\\\"28dc5028-50fb-4983-b881-fb1e79196187\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}}\"},\"createdAt\":1764443949,\"delay\":null}',0,NULL,1764443949,1764443949),(23,'default','{\"uuid\":\"37cd9f98-8950-4711-89b7-114bb99cc40d\",\"displayName\":\"App\\\\Notifications\\\\InvoiceNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:14;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:6:\\\"tenant\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\InvoiceNotification\\\":3:{s:10:\\\"\\u0000*\\u0000invoice\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:18:\\\"App\\\\Models\\\\Invoice\\\";s:2:\\\"id\\\";i:2;s:9:\\\"relations\\\";a:1:{i:0;s:7:\\\"student\\\";}s:10:\\\"connection\\\";s:6:\\\"tenant\\\";s:15:\\\"collectionClass\\\";N;}s:16:\\\"\\u0000*\\u0000recipientType\\\";s:7:\\\"student\\\";s:2:\\\"id\\\";s:36:\\\"56b094d2-b9aa-4c67-bd9b-dfd7fd563ca3\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:4:\\\"mail\\\";}}\"},\"createdAt\":1764444188,\"delay\":null}',0,NULL,1764444188,1764444188),(24,'default','{\"uuid\":\"233d8ffb-fb28-4a06-a371-084ba307e6ed\",\"displayName\":\"App\\\\Notifications\\\\InvoiceNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:14;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:6:\\\"tenant\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\InvoiceNotification\\\":3:{s:10:\\\"\\u0000*\\u0000invoice\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:18:\\\"App\\\\Models\\\\Invoice\\\";s:2:\\\"id\\\";i:2;s:9:\\\"relations\\\";a:1:{i:0;s:7:\\\"student\\\";}s:10:\\\"connection\\\";s:6:\\\"tenant\\\";s:15:\\\"collectionClass\\\";N;}s:16:\\\"\\u0000*\\u0000recipientType\\\";s:7:\\\"student\\\";s:2:\\\"id\\\";s:36:\\\"56b094d2-b9aa-4c67-bd9b-dfd7fd563ca3\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}}\"},\"createdAt\":1764444188,\"delay\":null}',0,NULL,1764444188,1764444188),(25,'default','{\"uuid\":\"2f74217e-b3f7-4ea7-842e-9f10d230d617\",\"displayName\":\"App\\\\Notifications\\\\InvoiceNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:20;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:6:\\\"tenant\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\InvoiceNotification\\\":3:{s:10:\\\"\\u0000*\\u0000invoice\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:18:\\\"App\\\\Models\\\\Invoice\\\";s:2:\\\"id\\\";i:2;s:9:\\\"relations\\\";a:1:{i:0;s:7:\\\"student\\\";}s:10:\\\"connection\\\";s:6:\\\"tenant\\\";s:15:\\\"collectionClass\\\";N;}s:16:\\\"\\u0000*\\u0000recipientType\\\";s:6:\\\"parent\\\";s:2:\\\"id\\\";s:36:\\\"46e90de9-c498-401c-bd9c-77e62c4dd3c7\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:4:\\\"mail\\\";}}\"},\"createdAt\":1764444188,\"delay\":null}',0,NULL,1764444188,1764444188),(26,'default','{\"uuid\":\"1d9add80-6bb3-48f4-9400-9ad7fb98b3b2\",\"displayName\":\"App\\\\Notifications\\\\InvoiceNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:20;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:6:\\\"tenant\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\InvoiceNotification\\\":3:{s:10:\\\"\\u0000*\\u0000invoice\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:18:\\\"App\\\\Models\\\\Invoice\\\";s:2:\\\"id\\\";i:2;s:9:\\\"relations\\\";a:1:{i:0;s:7:\\\"student\\\";}s:10:\\\"connection\\\";s:6:\\\"tenant\\\";s:15:\\\"collectionClass\\\";N;}s:16:\\\"\\u0000*\\u0000recipientType\\\";s:6:\\\"parent\\\";s:2:\\\"id\\\";s:36:\\\"46e90de9-c498-401c-bd9c-77e62c4dd3c7\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}}\"},\"createdAt\":1764444188,\"delay\":null}',0,NULL,1764444188,1764444188);
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `learning_materials`
--

DROP TABLE IF EXISTS `learning_materials`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `learning_materials` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `teacher_id` bigint unsigned NOT NULL,
  `class_id` bigint unsigned NOT NULL,
  `subject_id` bigint unsigned NOT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'document',
  `file_path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_size` bigint DEFAULT NULL,
  `file_mime` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `external_url` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `youtube_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_downloadable` tinyint(1) NOT NULL DEFAULT '1',
  `views_count` int NOT NULL DEFAULT '0',
  `downloads_count` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `learning_materials_subject_id_foreign` (`subject_id`),
  KEY `learning_materials_class_id_type_index` (`class_id`,`type`),
  KEY `learning_materials_teacher_id_created_at_index` (`teacher_id`,`created_at`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `learning_materials`
--

LOCK TABLES `learning_materials` WRITE;
/*!40000 ALTER TABLE `learning_materials` DISABLE KEYS */;
/*!40000 ALTER TABLE `learning_materials` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `leave_requests`
--

DROP TABLE IF EXISTS `leave_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `leave_requests` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint unsigned NOT NULL,
  `leave_type_id` bigint unsigned NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `days_requested` int DEFAULT NULL,
  `daily_rate` decimal(12,2) DEFAULT NULL COMMENT 'Employee daily salary rate at time of request',
  `financial_impact` decimal(12,2) DEFAULT NULL COMMENT 'Total financial impact (deduction for unpaid, value for paid)',
  `is_paid` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Whether this leave type is paid or unpaid',
  `status` enum('pending','approved','declined','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `approved_by` bigint unsigned DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL COMMENT 'When the request was approved/declined',
  `reason` text COLLATE utf8mb4_unicode_ci,
  `manager_comment` text COLLATE utf8mb4_unicode_ci,
  `financial_notes` text COLLATE utf8mb4_unicode_ci COMMENT 'Detailed financial calculation notes',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `leave_requests_employee_id_foreign` (`employee_id`),
  KEY `leave_requests_leave_type_id_foreign` (`leave_type_id`),
  KEY `leave_requests_approved_by_foreign` (`approved_by`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `leave_requests`
--

LOCK TABLES `leave_requests` WRITE;
/*!40000 ALTER TABLE `leave_requests` DISABLE KEYS */;
INSERT INTO `leave_requests` VALUES (1,3,5,'2025-11-26','2025-12-02',5,36363.64,181818.20,1,'pending',NULL,NULL,'Grand mothre\'s funeral',NULL,'PAID LEAVE: 5 days @ UGX 36,363.64/day = UGX 181,818.20. No salary deduction. Deducted from annual entitlement.','2025-11-25 17:17:26','2025-11-25 17:17:26');
/*!40000 ALTER TABLE `leave_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `leave_types`
--

DROP TABLE IF EXISTS `leave_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `leave_types` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `default_days` int DEFAULT NULL,
  `annual_entitlement` int DEFAULT NULL,
  `accrual_type` enum('fixed','monthly','per_period') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `accrual_rate` decimal(8,2) DEFAULT NULL,
  `carry_forward_limit` int DEFAULT NULL,
  `paid` tinyint(1) NOT NULL DEFAULT '1',
  `max_consecutive_days` int DEFAULT NULL,
  `requires_approval` tinyint(1) NOT NULL DEFAULT '1',
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `leave_types`
--

LOCK TABLES `leave_types` WRITE;
/*!40000 ALTER TABLE `leave_types` DISABLE KEYS */;
INSERT INTO `leave_types` VALUES (1,'Annual Leave','AL',25,NULL,NULL,NULL,NULL,1,NULL,1,'Paid leave given once a year to all staff',NULL,NULL),(2,'Sick Leave','SL',10,NULL,NULL,NULL,NULL,1,NULL,1,'Leave for illness or medical reasons',NULL,NULL),(3,'Maternity Leave','ML',90,NULL,NULL,NULL,NULL,1,NULL,0,'Leave for new mothers after childbirth',NULL,NULL),(4,'Paternity Leave','PL',5,NULL,NULL,NULL,NULL,1,NULL,0,'Leave for new fathers after childbirth',NULL,NULL),(5,'Compassionate Leave','CL',5,NULL,NULL,NULL,NULL,1,NULL,0,'Leave for family emergencies or bereavement',NULL,NULL),(6,'Study Leave','STL',15,NULL,NULL,NULL,NULL,1,NULL,1,'Leave for further education or training',NULL,NULL),(7,'Unpaid Leave','UL',0,NULL,NULL,NULL,NULL,1,NULL,1,'Leave taken without pay',NULL,NULL),(8,'Personal Leave','PSL',3,NULL,NULL,NULL,NULL,1,NULL,1,'Leave for personal matters',NULL,NULL),(9,'Public Holiday','PH',1,NULL,NULL,NULL,NULL,1,NULL,0,'Leave for official public holidays',NULL,NULL),(10,'Emergency Leave','EL',2,NULL,NULL,NULL,NULL,1,NULL,1,'Leave for urgent, unforeseen circumstances',NULL,NULL);
/*!40000 ALTER TABLE `leave_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lesson_plans`
--

DROP TABLE IF EXISTS `lesson_plans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lesson_plans` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `teacher_id` bigint unsigned NOT NULL,
  `class_id` bigint unsigned DEFAULT NULL,
  `subject_id` bigint unsigned DEFAULT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lesson_date` date DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `duration_minutes` smallint unsigned DEFAULT NULL,
  `objectives` json DEFAULT NULL,
  `materials_needed` json DEFAULT NULL,
  `activities` json DEFAULT NULL,
  `introduction` longtext COLLATE utf8mb4_unicode_ci,
  `main_content` longtext COLLATE utf8mb4_unicode_ci,
  `assessment` longtext COLLATE utf8mb4_unicode_ci,
  `homework` longtext COLLATE utf8mb4_unicode_ci,
  `notes` longtext COLLATE utf8mb4_unicode_ci,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `review_status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'not_submitted',
  `submitted_at` timestamp NULL DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `reviewed_by` bigint unsigned DEFAULT NULL,
  `review_feedback` text COLLATE utf8mb4_unicode_ci,
  `is_template` tinyint(1) NOT NULL DEFAULT '0',
  `delivered_at` timestamp NULL DEFAULT NULL,
  `requires_revision` tinyint(1) NOT NULL DEFAULT '0',
  `archived_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lesson_plans_class_id_foreign` (`class_id`),
  KEY `lesson_plans_subject_id_foreign` (`subject_id`),
  KEY `lesson_plans_reviewed_by_foreign` (`reviewed_by`),
  KEY `lesson_plans_teacher_id_lesson_date_index` (`teacher_id`,`lesson_date`),
  KEY `lesson_plans_status_index` (`status`),
  KEY `lesson_plans_review_status_index` (`review_status`),
  KEY `lesson_plans_is_template_index` (`is_template`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lesson_plans`
--

LOCK TABLES `lesson_plans` WRITE;
/*!40000 ALTER TABLE `lesson_plans` DISABLE KEYS */;
INSERT INTO `lesson_plans` VALUES (1,8,1,1,'Introduction to Numbers and Counting up to 25','2025-12-01','08:00:00','08:40:00',40,'[\"Identify and recognize numbers from 1 to 20 by sight and name.\", \"Count objects accurately up to 20 using one‑to‑one correspondence.\", \"Write numbers from 1 to 20 correctly with proper formation.\", \"Compare numbers within 20 to determine which is greater, smaller, or equal.\"]','[\"Number flashcards (1–20): For visual recognition and group activities.\", \"Counting objects: Small items like bottle tops, beans, sticks, or blocks for hands‑on practice.\", \"Whiteboard and markers: To demonstrate counting, writing numbers, and comparing values.\", \"Number chart (1–20): A classroom wall chart for reference and reinforcement.\", \"Worksheets: Simple exercises for writing numbers, filling in missing numbers, and matching objects to numbers.\", \"Interactive tools (optional): Digital number games or slides if technology is available.\", \"Notebook and pencils: For students to practice writing numbers neatly.\"]','[\"Counting Objects in Groups: Students receive small items (beans, bottle tops, or blocks).\", \"Number Comparison Game: Teacher writes two numbers on the board (e.g., 7 and 12).  Students decide which is bigger or smaller, and use objects to prove their answer.\"]','. Number Song or Chant\r\n\r\nBegin with a fun counting song (e.g., “1, 2, buckle my shoe…” or a simple 1–20 chant).\r\n\r\nEncourage students to clap or stomp as they count along to make it interactive.','<ol><li>Number Recognition (10 minutes)\r\n\r\nShow number flashcards (1–20) one by one.\r\n\r\nAsk students to read the numbers aloud together.\r\n\r\nInvite individual students to come forward and identify a number.\r\n\r\nDisplay a number chart and highlight tricky numbers (like 11, 12, 19, 20).\r\n</li><li>Counting with Objects (10 minutes)\r\n\r\nDistribute small items (beans, bottle tops, or blocks).\r\n\r\nAsk students to count objects in groups (e.g., “Count 7 beans”).\r\n\r\nReinforce one‑to‑one correspondence: one object = one count.\r\n\r\nPractice counting up to 20 as a class, then in pairs.</li><li>Writing Numbers (10 minutes)\r\n\r\nDemonstrate writing numbers 1–20 on the board with correct formation.\r\n\r\nStudents copy into their notebooks.\r\n\r\nGive short exercises: “Write numbers from 1 to 10” or “Fill in missing numbers: 15, __, 17, __, 19.”\r\n\r\n4. Comparing Numbers (10 minutes)\r\n\r\nPresent pairs of numbers (e.g., 8 vs 12, 15 vs 19).\r\n\r\nAsk: “Which is bigger? Which is smaller?”\r\n\r\nUse real objects: show 5 blocks vs 9 blocks and let students decide.\r\n\r\nReinforce vocabulary: greater than, less than, equal to.</li></ol>','<p>1. Oral Questions\r\n\r\nAsk individual students to identify numbers on flashcards (e.g., “What number is this?”).\r\n\r\nHave students count aloud objects up to 20 in front of the class.\r\n</p><p>\r\n2. Written Exercise\r\n\r\nProvide a short worksheet where students:\r\n\r\nFill in missing numbers in a sequence (e.g., 11, 12, __, 14, __).\r\n\r\nWrite numbers from 1 to 20 neatly.\r\n\r\nCompare two numbers and circle the bigger one.\r\n</p><p>\r\n3. Practical Demonstration\r\n\r\nGive students small items (beans, bottle tops, or blocks).\r\n\r\nAsk them to count out a specific number (e.g., “Count 15 beans”).\r\n\r\nObserve accuracy and confidence in handling the task.</p>','1. Number Writing Practice\r\n\r\nWrite numbers from 1 to 20 neatly in your exercise book.\r\n\r\n2. Missing Numbers\r\n\r\nFill in the blanks:\r\n\r\n1, 2, __, 4, 5, __, 7, 8, __, 10\r\n\r\n11, __, 13, __, 15, 16, __, 18, __, 20\r\n\r\n3. Real‑Life Counting Activity\r\n\r\nAt home, count objects around you (e.g., cups, chairs, or fruits).\r\n\r\nWrite down how many you counted and the number in words (e.g., “I counted 6 cups – six”).',NULL,'scheduled','approved','2025-11-28 16:17:45','2025-11-28 16:18:58','2025-11-28 16:18:58',2,NULL,1,NULL,0,NULL,'2025-11-28 15:25:04','2025-11-28 16:18:58',NULL);
/*!40000 ALTER TABLE `lesson_plans` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `library_books`
--

DROP TABLE IF EXISTS `library_books`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `library_books` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `author` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `isbn` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  `available_quantity` int NOT NULL DEFAULT '1',
  `publisher` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `publication_year` int DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `short_description` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cover_image` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cover_image_path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `location` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'available',
  `purchase_price` decimal(10,2) DEFAULT NULL,
  `language` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pages` int DEFAULT NULL,
  `is_for_sale` tinyint(1) NOT NULL DEFAULT '0',
  `is_digital` tinyint(1) NOT NULL DEFAULT '0',
  `digital_file_path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sale_price` decimal(10,2) DEFAULT NULL,
  `stock_quantity` int NOT NULL DEFAULT '0',
  `is_featured` tinyint(1) NOT NULL DEFAULT '0',
  `sold_count` int NOT NULL DEFAULT '0',
  `discount_percentage` decimal(5,2) NOT NULL DEFAULT '0.00',
  `school_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `library_books`
--

LOCK TABLES `library_books` WRITE;
/*!40000 ALTER TABLE `library_books` DISABLE KEYS */;
/*!40000 ALTER TABLE `library_books` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `library_transactions`
--

DROP TABLE IF EXISTS `library_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `library_transactions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `library_book_id` bigint unsigned NOT NULL,
  `issued_by` bigint unsigned DEFAULT NULL,
  `returned_to` bigint unsigned DEFAULT NULL,
  `borrowed_at` datetime NOT NULL,
  `due_date` datetime NOT NULL,
  `returned_at` datetime DEFAULT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'borrowed',
  `fine_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `fine_paid` tinyint(1) NOT NULL DEFAULT '0',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `condition_notes` text COLLATE utf8mb4_unicode_ci,
  `renewal_count` int NOT NULL DEFAULT '0',
  `school_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `library_transactions_user_id_foreign` (`user_id`),
  KEY `library_transactions_library_book_id_foreign` (`library_book_id`),
  KEY `library_transactions_issued_by_foreign` (`issued_by`),
  KEY `library_transactions_returned_to_foreign` (`returned_to`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `library_transactions`
--

LOCK TABLES `library_transactions` WRITE;
/*!40000 ALTER TABLE `library_transactions` DISABLE KEYS */;
/*!40000 ALTER TABLE `library_transactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mail_settings`
--

DROP TABLE IF EXISTS `mail_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mail_settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `mailer` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'mail',
  `from_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `from_address` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `config` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mail_settings`
--

LOCK TABLES `mail_settings` WRITE;
/*!40000 ALTER TABLE `mail_settings` DISABLE KEYS */;
INSERT INTO `mail_settings` VALUES (1,'log','Victoria Nile School','no-reply@victorianileschool.localhost','eyJpdiI6IlQ5R3JLZHJYMHJiV25BVk5qNGNHSlE9PSIsInZhbHVlIjoiQUFHQjA0bmh4TGlSYmNDUG9nNitDUT09IiwibWFjIjoiZTZhMDQ1MTA5YmZmZjRkNzlhYTBhMzc2Y2NkMGJmM2RhYWM3ZTU0YjRhMGQ4MzZkMDNlMWMyMDYwZTk4NTc3NSIsInRhZyI6IiJ9','2025-11-22 08:44:25','2025-11-23 06:23:23');
/*!40000 ALTER TABLE `mail_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `material_accesses`
--

DROP TABLE IF EXISTS `material_accesses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `material_accesses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `learning_material_id` bigint unsigned NOT NULL,
  `student_id` bigint unsigned NOT NULL,
  `action` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'view',
  `accessed_at` datetime NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `material_accesses_student_id_foreign` (`student_id`),
  KEY `material_accesses_learning_material_id_student_id_index` (`learning_material_id`,`student_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `material_accesses`
--

LOCK TABLES `material_accesses` WRITE;
/*!40000 ALTER TABLE `material_accesses` DISABLE KEYS */;
/*!40000 ALTER TABLE `material_accesses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `meetings`
--

DROP TABLE IF EXISTS `meetings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `meetings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `scheduled_at` datetime NOT NULL,
  `duration_minutes` int NOT NULL DEFAULT '30',
  `meeting_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'in-person',
  `platform` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meeting_link` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `location` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'scheduled',
  `organizer_id` bigint unsigned NOT NULL,
  `participants` json DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `student_id` bigint unsigned DEFAULT NULL,
  `teacher_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `meetings_organizer_id_foreign` (`organizer_id`),
  KEY `meetings_student_id_foreign` (`student_id`),
  KEY `meetings_teacher_id_foreign` (`teacher_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `meetings`
--

LOCK TABLES `meetings` WRITE;
/*!40000 ALTER TABLE `meetings` DISABLE KEYS */;
/*!40000 ALTER TABLE `meetings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `message_recipients`
--

DROP TABLE IF EXISTS `message_recipients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `message_recipients` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `message_id` bigint unsigned NOT NULL,
  `recipient_id` bigint unsigned NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `message_recipients_message_id_recipient_id_unique` (`message_id`,`recipient_id`),
  KEY `message_recipients_recipient_id_is_read_index` (`recipient_id`,`is_read`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `message_recipients`
--

LOCK TABLES `message_recipients` WRITE;
/*!40000 ALTER TABLE `message_recipients` DISABLE KEYS */;
/*!40000 ALTER TABLE `message_recipients` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `message_threads`
--

DROP TABLE IF EXISTS `message_threads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `message_threads` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `subject` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'direct',
  `created_by` bigint unsigned NOT NULL,
  `last_message_at` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `participants` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `message_threads_type_is_active_index` (`type`,`is_active`),
  KEY `message_threads_created_by_index` (`created_by`),
  KEY `message_threads_last_message_at_index` (`last_message_at`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `message_threads`
--

LOCK TABLES `message_threads` WRITE;
/*!40000 ALTER TABLE `message_threads` DISABLE KEYS */;
/*!40000 ALTER TABLE `message_threads` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `messages`
--

DROP TABLE IF EXISTS `messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `messages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `thread_id` bigint unsigned NOT NULL,
  `sender_id` bigint unsigned NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `message_type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'text',
  `attachments` json DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `messages_thread_id_created_at_index` (`thread_id`,`created_at`),
  KEY `messages_sender_id_is_read_index` (`sender_id`,`is_read`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `messages`
--

LOCK TABLES `messages` WRITE;
/*!40000 ALTER TABLE `messages` DISABLE KEYS */;
/*!40000 ALTER TABLE `messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `messaging_channel_settings`
--

DROP TABLE IF EXISTS `messaging_channel_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `messaging_channel_settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `channel` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `provider` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `config` longtext COLLATE utf8mb4_unicode_ci,
  `meta` longtext COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `messaging_channel_settings_channel_provider_unique` (`channel`,`provider`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `messaging_channel_settings`
--

LOCK TABLES `messaging_channel_settings` WRITE;
/*!40000 ALTER TABLE `messaging_channel_settings` DISABLE KEYS */;
INSERT INTO `messaging_channel_settings` VALUES (1,'sms','twilio',0,'eyJpdiI6IkY1dXVMWnkvZW9nRFV4OGtzRmprRnc9PSIsInZhbHVlIjoiQWNOdG5yano5MUlXZDVXYWEzWHE5Zz09IiwibWFjIjoiZTg0NDYyYzQyMzYzNDY3NjczNDkyMDVmOTA3ODkzNGJjN2Q3OTAxNjZlMjNkOGQwMjIyNDlkY2Q5YzQwMzE2MSIsInRhZyI6IiJ9',NULL,'2025-11-22 08:44:25','2025-11-22 08:44:25'),(2,'sms','vonage',0,'eyJpdiI6IlBzcytLQ3hsZytqUHBzNUtkSzZ6Znc9PSIsInZhbHVlIjoiRlpicm5HakNwQXpxRVBuZmt6WWM3QT09IiwibWFjIjoiYzZjNmEyNDc1ODM5ZjZmZmQwYTE2MmYwYmFiMTZhOTUyM2RjMjM5MjNhNWY5ZTk1MjVjZjVkYzZkNzk2NjRhMCIsInRhZyI6IiJ9',NULL,'2025-11-22 08:44:25','2025-11-22 08:44:25'),(3,'sms','africastalking',0,'eyJpdiI6ImM5S3BaSzhRV0NpdWVmYVpUV25yN0E9PSIsInZhbHVlIjoiVXkxVi95NXl4U3NVYmFHTExTbDNYQT09IiwibWFjIjoiYjU1YThkNmI1ZjIwMWIyOWYwZjRiNWNmYWFkNTRhMzE4MWMwYTg4MjRmYTY4NWU3MzRlNmVlNWRkM2JjMGU4NCIsInRhZyI6IiJ9',NULL,'2025-11-22 08:44:25','2025-11-22 08:44:25'),(4,'sms','custom',0,'eyJpdiI6IjlCSWl0amV4RzVyUnVsZm5Ua1hCUFE9PSIsInZhbHVlIjoiUkY1cFpWUDhKbkhJVTJmK1ZLdXR0Zz09IiwibWFjIjoiZjE2MjA0ZThmYjY4YjVlZDAyZjNhZDMzNWViZmNiMzFiZjFmM2U4MDQ3OTg4MmNlYThiMTlkYWU5ZmU1ZjAxZSIsInRhZyI6IiJ9',NULL,'2025-11-22 08:44:25','2025-11-22 08:44:25'),(5,'whatsapp','twilio_whatsapp',0,'eyJpdiI6InAyd1g4U29TZlhKMmxOZTNpcitld1E9PSIsInZhbHVlIjoicVg2dk9VWHk4R2xPWDFvVHUvOHI5UT09IiwibWFjIjoiZjAxYzc5YTczZmUxMGQ4YjNiOTAzMjRjOTVlY2E1MDNiNDY2Mzg1YjcxOTk3OTliMWRiYzgzMTVjMDliMDJlMSIsInRhZyI6IiJ9',NULL,'2025-11-22 08:44:25','2025-11-22 08:44:25'),(6,'whatsapp','meta_cloud',0,'eyJpdiI6InVLbXZncG1PVkxzUU4vNmVFdlp2U0E9PSIsInZhbHVlIjoicUU2N0ZLR2FlZm9EazlBVjRMTkNoQT09IiwibWFjIjoiMmZkNzJiMzgyOGJkYWM3NzY0ZmU2ZmY5YjU2YTg5ZDBmZDFmZmEyNTkzYjAzZjM4MmE5OTUyMmU2NGU4YTMxNSIsInRhZyI6IiJ9',NULL,'2025-11-22 08:44:25','2025-11-22 08:44:25'),(7,'whatsapp','custom',0,'eyJpdiI6IkYzMGpDL2xmeEVXU3k5bkhPci80N1E9PSIsInZhbHVlIjoid0dpZ3g5UDREZEpFNkN5R05XRzQ3UT09IiwibWFjIjoiNGFlNWFlN2FhODFiMjc0MWJhYTczNmYwMDQ0NjdiYTlhMzYzY2Y5NzdmZTMzMGIyOTk2MDk1NjY2OGRiYjM5NyIsInRhZyI6IiJ9',NULL,'2025-11-22 08:44:25','2025-11-22 08:44:25'),(8,'telegram','telegram_bot',0,'eyJpdiI6IkJUQWEwcUFGMGltYnY2UFNhemRUWEE9PSIsInZhbHVlIjoiRVhtVGRwZ24yN0pxMmEwemZzM0szcGt1eVZ4SkRiVk4wYUxtVXJQbW0zdz0iLCJtYWMiOiJkZTZmZDM1NDI1Zjc5YWVmMjExY2ZjNmVhNzc3N2JjYjBmZDBlNWZiN2ZjOTQwN2Y3YTY0ZGUxYWMyY2ZiODc4IiwidGFnIjoiIn0=',NULL,'2025-11-22 08:44:25','2025-11-22 08:44:25'),(9,'telegram','custom',0,'eyJpdiI6InNWM2EwYmdERGkxUXpreTJ2VUN0eHc9PSIsInZhbHVlIjoiYThjZHZ2Uy91bElnSnIyamNtMkJrQT09IiwibWFjIjoiMWRhMzVkZmMwNmQxYzNjNjI3ZWFkYmFhY2IyNmQ2YmM1OWYwOTI0M2FkOWE2OTcxN2NhY2YxYzhiYjE3NTM1MyIsInRhZyI6IiJ9',NULL,'2025-11-22 08:44:25','2025-11-22 08:44:25');
/*!40000 ALTER TABLE `messaging_channel_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=136 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2024_01_01_000001_create_users_table',1),(2,'2024_01_01_000002_create_cache_table',1),(3,'2024_01_01_000003_create_jobs_table',1),(4,'2024_01_01_000010_create_settings_table',1),(5,'2024_01_01_000011_create_mail_settings_table',1),(6,'2024_01_01_000012_create_payment_gateway_settings_table',1),(7,'2024_01_01_000013_create_messaging_channel_settings_table',1),(8,'2024_01_01_000014_create_attendance_settings_table',1),(9,'2024_01_01_000015_create_platform_integrations_table',1),(10,'2024_01_01_000020_create_currencies_table',1),(11,'2024_01_01_000021_add_exchange_rate_metadata_to_currencies_table',1),(12,'2024_01_01_000030_create_permission_tables',1),(13,'2024_01_01_000031_add_approval_fields_to_users_table',1),(14,'2024_01_01_000040_create_departments_table',1),(15,'2024_01_01_000041_create_positions_table',1),(16,'2024_01_01_000042_create_salary_scales_table',1),(17,'2024_01_01_000050_create_employees_table',1),(18,'2024_01_01_000051_add_employee_number_to_employees_table',1),(19,'2024_01_01_000052_add_employee_type_to_employees_table',1),(20,'2024_01_01_000053_add_identity_fields_to_employees_table',1),(21,'2024_01_01_000060_create_teachers_table',1),(22,'2024_01_01_000061_enhance_teachers_table',1),(23,'2024_01_01_000062_add_sync_fields_for_teacher_employee',1),(24,'2024_01_01_000070_create_education_levels_table',1),(25,'2024_01_01_000071_create_countries_and_examination_bodies_tables',1),(26,'2024_01_01_000072_create_grading_schemes_tables',1),(27,'2024_01_01_000073_create_terms_table',1),(28,'2024_01_01_000074_create_rooms_table',1),(29,'2024_01_01_000080_create_classes_table',1),(30,'2024_01_01_000081_create_class_streams_table',1),(31,'2024_01_01_000090_create_subjects_table',1),(32,'2024_01_01_000091_add_required_periods_to_subjects_table',1),(33,'2024_01_01_000100_create_class_subjects_table',1),(34,'2024_01_01_000101_update_subjects_and_class_subjects_tables',1),(35,'2024_01_01_000110_create_timetable_entries_table',1),(36,'2024_01_01_000111_add_room_id_to_timetable_entries_table',1),(37,'2024_01_01_000112_create_teacher_availabilities_table',1),(38,'2024_01_01_000113_create_timetable_constraints_table',1),(39,'2024_01_01_000120_create_attendance_table',1),(40,'2024_01_01_000121_create_attendance_records_table',1),(41,'2024_01_01_000122_create_staff_attendance_table',1),(42,'2024_01_01_000123_create_biometric_templates_table',1),(43,'2024_01_01_000124_add_attendance_method_tracking',1),(44,'2024_01_01_000130_create_expense_categories_table',1),(45,'2024_01_01_000131_create_transactions_table',1),(46,'2024_01_01_000132_create_fee_structures_table',1),(47,'2024_01_01_000133_create_expenses_table',1),(48,'2024_01_01_000134_create_invoices_table',1),(49,'2024_01_01_000135_create_payments_table',1),(50,'2024_01_01_000136_create_payment_gateway_configs_table',1),(51,'2024_01_01_000137_create_payment_transactions_table',1),(52,'2024_01_01_000140_create_quiz_tables',1),(53,'2024_01_01_000150_create_report_logs_table',1),(54,'2025_11_17_021154_create_leave_requests_table',1),(55,'2025_11_17_021154_create_leave_types_table',1),(56,'2025_11_17_021154_create_students_table',1),(57,'2025_11_17_021155_add_financial_tracking_to_leave_requests_table',1),(58,'2025_11_17_021155_add_policy_fields_to_leave_types_table',1),(59,'2025_11_19_000200_update_payment_gateway_settings_columns',1),(60,'2025_11_19_000210_update_messaging_channel_settings_columns',1),(61,'2025_11_20_000500_create_payments_table',1),(62,'2025_11_20_001100_add_school_id_to_expenses_table',1),(63,'2025_11_20_001200_add_school_id_to_invoices_table',1),(64,'2025_11_20_001300_update_invoices_finance_columns',1),(65,'2025_11_21_000001_add_academic_permissions',1),(66,'2025_11_21_000001_add_missing_columns_to_expense_categories_table',1),(67,'2025_11_21_000002_make_tenant_id_nullable_in_expenses_table',1),(68,'2025_11_21_083000_create_lesson_plans_table',1),(69,'2025_11_22_000000_add_missing_columns_to_leave_requests_table',2),(70,'2025_11_23_000000_create_security_audit_logs_table',3),(71,'2025_11_24_000000_create_library_tables',4),(72,'2025_11_17_021155_create_bookstore_order_items_table',5),(73,'2025_11_17_021155_create_bookstore_orders_table',5),(74,'2025_11_24_081657_add_digital_fields_to_library_books_table',6),(75,'2025_11_17_021155_create_academic_years_table',7),(76,'2025_11_17_021155_create_enrollments_table',8),(77,'2025_11_17_021155_create_message_recipients_table',9),(78,'2025_11_17_021155_create_message_threads_table',10),(79,'2025_11_17_021155_create_messages_table',10),(80,'2025_11_17_021154_create_classes_table',11),(81,'2025_11_17_021154_create_grades_table',11),(82,'2025_11_17_021154_create_online_exams_table',11),(83,'2025_11_17_021154_create_quizzes_table',11),(84,'2025_11_17_021154_create_subjects_table',11),(85,'2025_11_17_021154_create_teachers_table',11),(86,'2025_11_17_021154_create_timetable_entries_table',11),(87,'2025_11_24_000000_create_virtual_classrooms_tables',12),(88,'2025_11_17_021154_create_exercises_table',13),(89,'2025_11_24_000002_add_school_id_to_exercises_table',14),(90,'2025_11_17_021154_add_soft_deletes_to_exercise_submissions',15),(91,'2025_11_24_000003_add_soft_deletes_to_exercise_submissions',15),(92,'2025_11_24_090000_update_grades_table_schema',16),(93,'2025_11_24_100000_create_otp_codes_table',17),(94,'2025_11_17_021155_create_allocation_pivot_tables',18),(95,'2025_11_17_021155_create_assignment_submissions_table',18),(96,'2025_11_17_021155_create_assignments_table',18),(97,'2025_11_17_021155_create_payroll_records_table',19),(98,'2025_11_25_000001_create_class_stream_teacher_table',20),(99,'2025_11_25_120000_create_employee_id_settings_table',20),(100,'2025_11_25_150000_create_payroll_settings_table',21),(101,'2025_11_26_000001_add_class_stream_id_to_enrollments_table',22),(102,'2025_11_26_144000_add_user_id_to_teachers_table',23),(103,'2025_11_26_160000_fix_timetable_entries_teacher_fk',24),(104,'2025_11_26_203000_add_recurring_fields_to_fee_structures_table',25),(105,'2025_11_27_120000_create_notifications_table',26),(106,'2025_11_27_120000_add_profile_photo_to_users_table',27),(107,'2025_11_27_000001_add_workflow_fields_to_online_exams_table',28),(108,'2025_11_27_130000_create_user_preferences_table',28),(109,'2025_11_27_130000_add_soft_deletes_to_quizzes_table',29),(110,'2025_11_27_133000_add_instructions_to_quizzes_table',30),(111,'2025_11_27_134000_add_class_and_subject_to_quizzes_table',31),(112,'2025_11_27_135000_add_missing_columns_to_quizzes_table',32),(113,'2025_11_27_140000_add_soft_deletes_to_quiz_questions_table',33),(114,'2025_11_28_120000_rename_shuffle_options_to_shuffle_answers_in_online_exams_table',34),(115,'2025_11_28_121000_add_deleted_at_to_online_exam_attempts_table',35),(116,'2025_11_28_122000_add_deleted_at_to_online_exam_related_tables',36),(117,'2025_11_28_105212_add_content_to_exercises_table',37),(118,'2025_11_28_110000_add_exercise_questions_and_settings',38),(119,'2025_11_28_115000_add_exercise_questions_and_settings',38),(120,'2025_11_28_120000_add_advanced_features_to_exercises_table',39),(121,'2025_11_28_160000_add_missing_assignment_columns',40),(122,'2025_11_17_021155_create_student_notes_table',41),(123,'2025_11_29_000000_create_forum_tables',42),(124,'2025_11_29_100000_create_parent_portal_tables',43),(125,'2025_11_29_120000_create_sessions_table',44),(126,'2025_11_29_130000_create_parents_table_fix',45),(127,'2025_11_29_130100_create_parent_student_table_fix',45),(128,'2025_11_29_100000_create_meetings_table',46),(129,'2025_11_29_100500_add_student_teacher_to_meetings_table',47),(130,'2025_11_29_133000_fix_parent_student_columns',48),(131,'2025_11_29_140000_add_profile_columns_to_users_table',49),(132,'2025_11_29_145800_add_emergency_contact_to_users_table',50),(133,'2025_11_29_220000_add_reason_fields_to_invoices_table',51),(134,'2025_11_29_300000_create_mobile_money_gateways_table',52),(135,'2025_11_30_100000_create_payment_transactions_table',53);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mobile_money_gateways`
--

DROP TABLE IF EXISTS `mobile_money_gateways`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mobile_money_gateways` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `school_id` bigint unsigned NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `provider` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `country_code` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `currency_code` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `api_base_url` text COLLATE utf8mb4_unicode_ci,
  `public_key` text COLLATE utf8mb4_unicode_ci,
  `secret_key` text COLLATE utf8mb4_unicode_ci,
  `api_user` text COLLATE utf8mb4_unicode_ci,
  `api_password` text COLLATE utf8mb4_unicode_ci,
  `subscription_key` text COLLATE utf8mb4_unicode_ci,
  `webhook_secret` text COLLATE utf8mb4_unicode_ci,
  `encryption_key` text COLLATE utf8mb4_unicode_ci,
  `client_id` text COLLATE utf8mb4_unicode_ci,
  `client_secret` text COLLATE utf8mb4_unicode_ci,
  `access_token` text COLLATE utf8mb4_unicode_ci,
  `token_expires_at` timestamp NULL DEFAULT NULL,
  `merchant_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `merchant_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `short_code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `till_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `account_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sender_phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `callback_phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `environment` enum('sandbox','production') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'sandbox',
  `callback_url` text COLLATE utf8mb4_unicode_ci,
  `return_url` text COLLATE utf8mb4_unicode_ci,
  `cancel_url` text COLLATE utf8mb4_unicode_ci,
  `custom_fields` json DEFAULT NULL,
  `supported_networks` json DEFAULT NULL,
  `fee_structure` json DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `sort_order` int NOT NULL DEFAULT '0',
  `description` text COLLATE utf8mb4_unicode_ci,
  `logo_url` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `support_email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `support_phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_tested_at` timestamp NULL DEFAULT NULL,
  `test_successful` tinyint(1) DEFAULT NULL,
  `test_message` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mobile_money_gateways_school_id_slug_unique` (`school_id`,`slug`),
  KEY `mobile_money_gateways_school_id_is_active_index` (`school_id`,`is_active`),
  KEY `mobile_money_gateways_school_id_provider_index` (`school_id`,`provider`),
  KEY `mobile_money_gateways_slug_index` (`slug`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mobile_money_gateways`
--

LOCK TABLES `mobile_money_gateways` WRITE;
/*!40000 ALTER TABLE `mobile_money_gateways` DISABLE KEYS */;
/*!40000 ALTER TABLE `mobile_money_gateways` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model_has_permissions`
--

DROP TABLE IF EXISTS `model_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `model_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  `tenant_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`tenant_id`,`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  KEY `model_has_permissions_permission_id_foreign` (`permission_id`),
  KEY `model_has_permissions_team_foreign_key_index` (`tenant_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_has_permissions`
--

LOCK TABLES `model_has_permissions` WRITE;
/*!40000 ALTER TABLE `model_has_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `model_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model_has_roles`
--

DROP TABLE IF EXISTS `model_has_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_roles` (
  `role_id` bigint unsigned NOT NULL,
  `model_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  `tenant_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`tenant_id`,`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  KEY `model_has_roles_role_id_foreign` (`role_id`),
  KEY `model_has_roles_team_foreign_key_index` (`tenant_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_has_roles`
--

LOCK TABLES `model_has_roles` WRITE;
/*!40000 ALTER TABLE `model_has_roles` DISABLE KEYS */;
INSERT INTO `model_has_roles` VALUES (1,'App\\Models\\User',2,2),(2,'App\\Models\\User',2,2),(3,'App\\Models\\User',7,2),(4,'App\\Models\\User',6,2),(9,'App\\Models\\User',3,2),(9,'App\\Models\\User',4,2),(12,'App\\Models\\User',1,2),(13,'App\\Models\\User',8,2),(13,'App\\Models\\User',15,2),(13,'App\\Models\\User',16,2),(13,'App\\Models\\User',17,2),(13,'App\\Models\\User',18,2),(13,'App\\Models\\User',19,2),(14,'App\\Models\\User',9,2),(14,'App\\Models\\User',10,2),(14,'App\\Models\\User',11,2),(14,'App\\Models\\User',12,2),(14,'App\\Models\\User',13,2),(14,'App\\Models\\User',14,2),(15,'App\\Models\\User',20,2),(19,'App\\Models\\User',16,2);
/*!40000 ALTER TABLE `model_has_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifications` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_id` bigint unsigned NOT NULL,
  `data` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
INSERT INTO `notifications` VALUES ('26ea3048-19ae-4975-bf4d-16ca8145730c','App\\Notifications\\ExamSubmittedForReviewNotification','App\\Models\\User',1,'{\"exam_id\":1,\"title\":\"Mathematics Around Us: Primary One Assessment\",\"teacher\":{\"id\":8,\"name\":\"Jimmy Musisi\",\"email\":\"jimmymusisi@example.com\"},\"submitted_for_review_at\":\"2025-11-28T17:39:10.000000Z\",\"status\":\"pending_review\"}',NULL,'2025-11-28 14:39:10','2025-11-28 14:39:10'),('52ee4fbb-37b2-4266-87c1-c77586ccddb3','App\\Notifications\\ExamReviewDecisionNotification','App\\Models\\User',8,'{\"exam_id\":1,\"title\":\"Mathematics Around Us: Primary One Assessment\",\"decision\":\"approved\",\"notes\":null,\"status\":\"approved\"}',NULL,'2025-11-28 15:00:10','2025-11-28 15:00:10');
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `online_exam_answers`
--

DROP TABLE IF EXISTS `online_exam_answers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `online_exam_answers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `online_exam_attempt_id` bigint unsigned NOT NULL,
  `online_exam_question_id` bigint unsigned NOT NULL,
  `answer` text COLLATE utf8mb4_unicode_ci,
  `selected_options` json DEFAULT NULL,
  `is_correct` tinyint(1) DEFAULT NULL,
  `marks_awarded` decimal(5,2) NOT NULL DEFAULT '0.00',
  `teacher_feedback` text COLLATE utf8mb4_unicode_ci,
  `graded_by` bigint unsigned DEFAULT NULL,
  `graded_at` datetime DEFAULT NULL,
  `answered_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `attempt_question_unique` (`online_exam_attempt_id`,`online_exam_question_id`),
  KEY `online_exam_answers_online_exam_question_id_foreign` (`online_exam_question_id`),
  KEY `online_exam_answers_graded_by_foreign` (`graded_by`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `online_exam_answers`
--

LOCK TABLES `online_exam_answers` WRITE;
/*!40000 ALTER TABLE `online_exam_answers` DISABLE KEYS */;
/*!40000 ALTER TABLE `online_exam_answers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `online_exam_attempts`
--

DROP TABLE IF EXISTS `online_exam_attempts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `online_exam_attempts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `online_exam_id` bigint unsigned NOT NULL,
  `student_id` bigint unsigned NOT NULL,
  `started_at` datetime NOT NULL,
  `submitted_at` datetime DEFAULT NULL,
  `auto_submitted_at` datetime DEFAULT NULL,
  `time_taken_minutes` int DEFAULT NULL,
  `score` decimal(6,2) DEFAULT NULL,
  `percentage` decimal(5,2) DEFAULT NULL,
  `status` enum('in_progress','submitted','graded','flagged') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'in_progress',
  `tab_switches_count` int NOT NULL DEFAULT '0',
  `violation_logs` json DEFAULT NULL,
  `proctor_notes` text COLLATE utf8mb4_unicode_ci,
  `is_verified` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `online_exam_attempts_student_id_foreign` (`student_id`),
  KEY `online_exam_attempts_online_exam_id_student_id_index` (`online_exam_id`,`student_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `online_exam_attempts`
--

LOCK TABLES `online_exam_attempts` WRITE;
/*!40000 ALTER TABLE `online_exam_attempts` DISABLE KEYS */;
/*!40000 ALTER TABLE `online_exam_attempts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `online_exam_questions`
--

DROP TABLE IF EXISTS `online_exam_questions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `online_exam_questions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `online_exam_id` bigint unsigned NOT NULL,
  `section_id` bigint unsigned DEFAULT NULL,
  `type` enum('multiple_choice','multiple_answer','true_false','short_answer','essay','fill_blank','matching') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'multiple_choice',
  `question` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `question_image` text COLLATE utf8mb4_unicode_ci,
  `explanation` text COLLATE utf8mb4_unicode_ci,
  `options` json DEFAULT NULL,
  `correct_answer` text COLLATE utf8mb4_unicode_ci,
  `marks` int NOT NULL,
  `negative_marks` int NOT NULL DEFAULT '0',
  `order` int NOT NULL DEFAULT '0',
  `is_required` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `online_exam_questions_section_id_foreign` (`section_id`),
  KEY `online_exam_questions_online_exam_id_order_index` (`online_exam_id`,`order`)
) ENGINE=MyISAM AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `online_exam_questions`
--

LOCK TABLES `online_exam_questions` WRITE;
/*!40000 ALTER TABLE `online_exam_questions` DISABLE KEYS */;
INSERT INTO `online_exam_questions` VALUES (1,1,1,'multiple_choice','Test Question from Script',NULL,'Test Explanation','[\"Option A\", \"Option B\"]','\"Option A\"',1,0,1,1,'2025-11-27 19:49:55','2025-11-28 12:50:51','2025-11-28 12:50:51'),(2,1,1,'multiple_choice','Which number comes after 29?',NULL,NULL,'{\"A\": \"28\", \"B\": \"30\", \"C\": \"31\", \"D\": \"27\"}','\"B\"',4,0,2,1,'2025-11-28 12:50:35','2025-11-28 12:50:35',NULL),(3,1,1,'multiple_choice','What is 15 + 6?',NULL,NULL,'{\"A\": \"20\", \"B\": \"21\", \"C\": \"22\", \"D\": \"23\"}','\"B\"',4,0,3,1,'2025-11-28 13:59:54','2025-11-28 13:59:54',NULL),(4,1,1,'multiple_choice','Which shape has 4 equal sides',NULL,NULL,'{\"A\": \"Rectangle\", \"B\": \"Triangle\", \"C\": \"Square\", \"D\": \"Circle\"}','\"C\"',4,0,4,1,'2025-11-28 14:01:29','2025-11-28 14:01:29',NULL),(5,1,1,'multiple_choice','What is 18 − 9?',NULL,NULL,'{\"A\": \"7\", \"B\": \"8\", \"C\": \"9\", \"D\": \"10\"}','\"C\"',4,0,5,1,'2025-11-28 14:02:35','2025-11-28 14:02:35',NULL),(6,1,1,'multiple_choice','Look at this pattern: 🔵 🔴 🔵 🔴 … What comes next?',NULL,NULL,'{\"A\": \"🔵\", \"B\": \"🔴\", \"C\": \"🟢\", \"D\": \"⚫\"}','\"A\"',4,0,6,1,'2025-11-28 14:04:01','2025-11-28 14:05:39',NULL),(7,1,1,'multiple_choice','Which number is the smallest?',NULL,NULL,'{\"A\": \"45\", \"B\": \"32\", \"C\": \"28\", \"D\": \"50\"}','\"C\"',4,0,7,1,'2025-11-28 14:05:18','2025-11-28 14:05:18',NULL),(8,1,1,'multiple_choice','If you have 10 sweets and eat 4, how many are left?',NULL,NULL,'{\"A\": \"5\", \"B\": \"6\", \"C\": \"7\", \"D\": \"8\"}','\"B\"',4,0,8,1,'2025-11-28 14:06:54','2025-11-28 14:06:54',NULL),(9,1,1,'multiple_choice','Which number is missing: 11, 12, __, 14, 15?',NULL,NULL,'{\"A\": \"10\", \"B\": \"13\", \"C\": \"16\", \"D\": \"9\"}','\"B\"',4,0,9,1,'2025-11-28 14:08:43','2025-11-28 14:08:43',NULL),(10,1,1,'multiple_choice','What is 25 + 25?',NULL,NULL,'{\"A\": \"40\", \"B\": \"45\", \"C\": \"50\", \"D\": \"55\"}','\"C\"',4,0,10,1,'2025-11-28 14:09:46','2025-11-28 14:09:46',NULL),(11,1,1,'multiple_choice','A basket has 20 mangoes. If 5 are taken out, how many remain?',NULL,NULL,'{\"A\": \"10\", \"B\": \"15\", \"C\": \"25\", \"D\": \"30\"}','\"B\"',4,0,11,1,'2025-11-28 14:10:46','2025-11-28 14:10:46',NULL),(12,1,2,'multiple_choice','Which shape has 3 sides?',NULL,NULL,'{\"A\": \"Circle\", \"B\": \"Triangle\", \"C\": \"Square\", \"D\": \"Rectangle\"}','\"B\"',4,0,1,1,'2025-11-28 14:13:43','2025-11-28 14:13:43',NULL),(13,1,2,'multiple_choice','Which shape is round and has no corners?',NULL,NULL,'{\"A\": \"Triangle\", \"B\": \"Circle\", \"C\": \"Square\", \"D\": \"Rectangle\"}','\"B\"',4,0,2,1,'2025-11-28 14:14:44','2025-11-28 14:15:02',NULL),(14,1,2,'multiple_choice','Look at this sequence: 2, 4, 6, __, 10. What number should fill the blank?',NULL,NULL,'{\"A\": \"7\", \"B\": \"8\", \"C\": \"9\", \"D\": \"11\"}','\"B\"',4,0,3,1,'2025-11-28 14:16:13','2025-11-28 14:16:13',NULL),(15,1,2,'multiple_choice','Which number is missing: 15, 16, __, 18, 19?',NULL,NULL,'{\"A\": \"14\", \"B\": \"17\", \"C\": \"20\", \"D\": \"13\"}','\"B\"',4,0,4,1,'2025-11-28 14:17:55','2025-11-28 14:17:55',NULL),(16,1,2,'multiple_choice','Which shape has 4 unequal sides?',NULL,NULL,'{\"A\": \"Rectangle\", \"B\": \"Square\", \"C\": \"Triangle\", \"D\": \"Circle\"}','\"A\"',4,0,5,1,'2025-11-28 14:19:47','2025-11-28 14:19:47',NULL),(17,1,3,'multiple_choice','Anna has 12 pencils. She gives 5 to her friend. How many pencils does Anna have left?',NULL,NULL,'{\"A\": \"6\", \"B\": \"7\", \"C\": \"8\", \"D\": \"9\"}','\"B\"',4,0,1,1,'2025-11-28 14:22:10','2025-11-28 14:32:39',NULL),(18,1,3,'multiple_choice','A basket has 20 mangoes. If 8 are eaten, how many mangoes remain?',NULL,NULL,'{\"A\": \"10\", \"B\": \"11\", \"C\": \"12\", \"D\": \"13\"}','\"C\"',4,0,2,1,'2025-11-28 14:28:32','2025-11-28 14:28:32',NULL),(19,1,3,'multiple_choice','Peter has 15 marbles. His brother gives him 10 more. How many marbles does Peter have now?',NULL,NULL,'{\"A\": \"24\", \"B\": \"26\", \"C\": \"27\", \"D\": \"25\"}','\"D\"',4,0,3,1,'2025-11-28 14:30:24','2025-11-28 14:30:24',NULL),(20,1,3,'multiple_choice','There are 18 birds in a tree. 7 fly away. How many birds are still in the tree?',NULL,NULL,'{\"A\": \"13\", \"B\": \"16\", \"C\": \"12\", \"D\": \"11\"}','\"D\"',4,0,4,1,'2025-11-28 14:32:12','2025-11-28 14:32:12',NULL),(21,1,3,'multiple_choice','A shop sells 25 pencils in the morning and 30 pencils in the afternoon. How many pencils are sold in total?',NULL,NULL,'{\"A\": \"65\", \"B\": \"73\", \"C\": \"55\", \"D\": \"71\"}','\"C\"',4,0,5,1,'2025-11-28 14:34:16','2025-11-28 14:34:16',NULL);
/*!40000 ALTER TABLE `online_exam_questions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `online_exam_sections`
--

DROP TABLE IF EXISTS `online_exam_sections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `online_exam_sections` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `online_exam_id` bigint unsigned NOT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `order` int NOT NULL DEFAULT '0',
  `time_limit_minutes` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `online_exam_sections_online_exam_id_foreign` (`online_exam_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `online_exam_sections`
--

LOCK TABLES `online_exam_sections` WRITE;
/*!40000 ALTER TABLE `online_exam_sections` DISABLE KEYS */;
INSERT INTO `online_exam_sections` VALUES (1,1,'Part A: Multiple Choice Questions',NULL,1,NULL,'2025-11-27 19:21:42','2025-11-27 19:21:42',NULL),(2,1,'Part B: Shapes and Number Recognition',NULL,2,NULL,'2025-11-27 19:22:17','2025-11-27 19:22:17',NULL),(3,1,'Part C: Word Problems',NULL,3,NULL,'2025-11-27 19:22:53','2025-11-27 19:22:53',NULL);
/*!40000 ALTER TABLE `online_exam_sections` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `online_exams`
--

DROP TABLE IF EXISTS `online_exams`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `online_exams` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `teacher_id` bigint unsigned NOT NULL,
  `creation_method` enum('manual','automatic','ai') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'manual',
  `activation_mode` enum('manual','schedule','auto') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'schedule',
  `class_id` bigint unsigned NOT NULL,
  `subject_id` bigint unsigned NOT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `instructions` text COLLATE utf8mb4_unicode_ci,
  `starts_at` datetime NOT NULL,
  `ends_at` datetime NOT NULL,
  `duration_minutes` int NOT NULL,
  `total_marks` int NOT NULL,
  `pass_marks` int DEFAULT NULL,
  `shuffle_questions` tinyint(1) NOT NULL DEFAULT '0',
  `shuffle_answers` tinyint(1) NOT NULL DEFAULT '0',
  `allow_backtrack` tinyint(1) NOT NULL DEFAULT '1',
  `show_results_immediately` tinyint(1) NOT NULL DEFAULT '0',
  `proctored` tinyint(1) NOT NULL DEFAULT '0',
  `max_tab_switches` int NOT NULL DEFAULT '5',
  `disable_copy_paste` tinyint(1) NOT NULL DEFAULT '1',
  `auto_submit_on` enum('time_up','manual','both') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'both',
  `grading_method` enum('auto','manual','mixed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'auto',
  `status` enum('draft','scheduled','active','completed','archived') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `approval_status` enum('draft','pending_review','changes_requested','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `review_notes` text COLLATE utf8mb4_unicode_ci,
  `reviewed_by` bigint unsigned DEFAULT NULL,
  `reviewed_at` datetime DEFAULT NULL,
  `submitted_for_review_at` datetime DEFAULT NULL,
  `generation_status` enum('idle','requested','processing','completed','failed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'idle',
  `generation_provider` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `generation_metadata` json DEFAULT NULL,
  `activated_at` datetime DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `online_exams_subject_id_foreign` (`subject_id`),
  KEY `online_exams_teacher_id_starts_at_index` (`teacher_id`,`starts_at`),
  KEY `online_exams_class_id_status_index` (`class_id`,`status`),
  KEY `online_exams_reviewed_by_foreign` (`reviewed_by`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `online_exams`
--

LOCK TABLES `online_exams` WRITE;
/*!40000 ALTER TABLE `online_exams` DISABLE KEYS */;
INSERT INTO `online_exams` VALUES (1,8,'manual','manual',1,1,'Mathematics Around Us: Primary One Assessment','This exam assesses Primary One pupils on their understanding of basic mathematics concepts, including counting, addition, subtraction, number recognition, and simple shapes. It is designed to measure learners’ progress over the term and encourage accuracy, confidence, and problem-solving skills.','Write your name and class clearly at the top of your answer sheet.\r\n\r\nRead each question carefully before answering.\r\n\r\nAnswer all questions in the spaces provided.\r\n\r\nDo not talk or share answers with other pupils during the exam.\r\n\r\nRaise your hand if you need help or have a question for the teacher.\r\n\r\nUse a pencil to write your answers neatly.\r\n\r\nCheck your work before submitting to make sure you have answered all questions.\r\n\r\nStop writing immediately when the teacher says “Time is up.”\r\n\r\nRemember: this exam is to show what you have learned—do your best!','2025-12-03 01:01:00','2025-12-04 02:01:00',60,100,70,1,0,1,1,1,5,1,'time_up','auto','scheduled','approved',NULL,2,'2025-11-28 18:00:10','2025-11-28 17:39:10','idle',NULL,'[]',NULL,NULL,'2025-11-27 19:09:21','2025-11-28 15:00:10',NULL);
/*!40000 ALTER TABLE `online_exams` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `otp_codes`
--

DROP TABLE IF EXISTS `otp_codes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `otp_codes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expires_at` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `otp_codes_user_id_foreign` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `otp_codes`
--

LOCK TABLES `otp_codes` WRITE;
/*!40000 ALTER TABLE `otp_codes` DISABLE KEYS */;
/*!40000 ALTER TABLE `otp_codes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `parent_student`
--

DROP TABLE IF EXISTS `parent_student`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `parent_student` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` bigint unsigned NOT NULL,
  `student_id` bigint unsigned NOT NULL,
  `relationship` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT '0',
  `can_pickup` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `financial_responsibility` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `parent_student_parent_id_student_id_unique` (`parent_id`,`student_id`),
  KEY `parent_student_student_id_foreign` (`student_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `parent_student`
--

LOCK TABLES `parent_student` WRITE;
/*!40000 ALTER TABLE `parent_student` DISABLE KEYS */;
INSERT INTO `parent_student` VALUES (1,1,6,'father',0,1,'2025-11-29 09:49:37','2025-11-29 10:19:19',1),(2,1,3,'father',1,1,'2025-11-29 09:49:37','2025-11-29 10:19:19',1);
/*!40000 ALTER TABLE `parent_student` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `parents`
--

DROP TABLE IF EXISTS `parents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `parents` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `first_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `middle_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gender` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `national_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `blood_group` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `profile_photo` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alternate_phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `city` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `postal_code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `occupation` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `employer` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `work_phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `work_address` text COLLATE utf8mb4_unicode_ci,
  `annual_income` decimal(15,2) DEFAULT NULL,
  `relation_to_students` json DEFAULT NULL,
  `emergency_contact_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `emergency_contact_phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `emergency_contact_relation` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `medical_conditions` text COLLATE utf8mb4_unicode_ci,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `parents_user_id_foreign` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `parents`
--

LOCK TABLES `parents` WRITE;
/*!40000 ALTER TABLE `parents` DISABLE KEYS */;
INSERT INTO `parents` VALUES (1,20,'George','Seku',NULL,'male','1982-06-18','CM1234567890','B+','parents/profiles/5btncIWh3076os5sQpyGS07OZiTZzsYDurrblp5v.jpg','+256704000017',NULL,NULL,'Jinja','Central','00256','KUgandaenya','Banker','Stanbic Bank Uganda LTD',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active','2025-11-29 09:42:17','2025-11-29 10:19:19',NULL);
/*!40000 ALTER TABLE `parents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payment_gateway_settings`
--

DROP TABLE IF EXISTS `payment_gateway_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payment_gateway_settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `gateway` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `config` longtext COLLATE utf8mb4_unicode_ci,
  `meta` longtext COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payment_gateway_settings_gateway_unique` (`gateway`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payment_gateway_settings`
--

LOCK TABLES `payment_gateway_settings` WRITE;
/*!40000 ALTER TABLE `payment_gateway_settings` DISABLE KEYS */;
INSERT INTO `payment_gateway_settings` VALUES (1,'paypal',0,'eyJpdiI6Ik1nWkpYaUF3Q29YQ0I4T2VvS1dsS1E9PSIsInZhbHVlIjoibERhWFFESi9pOWUzNzlHNmhYWUUrR3VqQldNZDZNOW1Wd20yWVVVOHYwTT0iLCJtYWMiOiIwZDUyZDBjMTQzYzQ1NjcyMDU2NDZlOTc3YTViMWY5ZDRmNWRjMmQ0MTAyZjlhNjQ2ZDA2ZTJjZDg3MTM1Y2Q2IiwidGFnIjoiIn0=',NULL,'2025-11-22 08:44:25','2025-11-22 08:44:25'),(2,'stripe',0,'eyJpdiI6IlRWMld0cmZSRUphbzBLQUlyL1Y1Wmc9PSIsInZhbHVlIjoiYUpKdmZvM1NrUWxNVVZuZUFrT0JqQT09IiwibWFjIjoiOTM2MmNkYzA1YTk2NTc3YjhkZmI3ZTRlYmViNDNhNmNkNWU4ZjAyMDRiMGUxNTc2MGIyMjg1ZWQ3YTNhNGE2YSIsInRhZyI6IiJ9',NULL,'2025-11-22 08:44:25','2025-11-22 08:44:25'),(3,'flutterwave',0,'eyJpdiI6InpiaDN6QXYzdGdlcjFCc1c5UXhwZWc9PSIsInZhbHVlIjoiOFYyOGlKOEw4VXhTYzZRMENmOHN0cDIvQ1N0RkNhcWM2eENMT0FEVGtOMD0iLCJtYWMiOiI0MjkwMGQxMjk5ZjRiMDU0ZTVhNTVkMjUzMmU5YzU5NDFlNDE0OWNlNGVlMGRhMjllOWNjMjc1MjJmMGVjZDE0IiwidGFnIjoiIn0=',NULL,'2025-11-22 08:44:25','2025-11-22 08:44:25'),(4,'mtn_momo',0,'eyJpdiI6IlBOU2xxUlJtNmpGNzZHOEFmYmkwYXc9PSIsInZhbHVlIjoiYVpuMTBUS0M2M0JVaElNUG1JWUtRY0xDZnVUVEdsZ1h2ZUhNbnQwamd4dz0iLCJtYWMiOiJiYjc1MGE5YTk0ZWRjZDE2YzI4NGNlYmQ2ZDIyNTMzM2Y0NDU5NjRjMThlYjlhZmIyNTVlOWNmY2EyZGM1OTQzIiwidGFnIjoiIn0=',NULL,'2025-11-22 08:44:25','2025-11-22 08:44:25'),(5,'airtel_money',0,'eyJpdiI6ImVkZEFueXJZb1dESzVERzJDaC80aUE9PSIsInZhbHVlIjoiQm9IMi8wVHo5WEEvbVNOVUo4eE5TUzVuQTQvOXUrcVVjOGJmcGs4ZVpFOD0iLCJtYWMiOiJmZWE4Mjk5ZDEwMWNlMTUyZTEyZDc1YjUzYjFhMzVhMjM4NjFhZDcyZjFjNjllY2ZhNDFhMzJmOWQxZDUxYWFkIiwidGFnIjoiIn0=',NULL,'2025-11-22 08:44:25','2025-11-22 08:44:25'),(6,'pesapal',0,'eyJpdiI6IkJWN1hVbkR4UHJ2RXBwaFNXUk1hNkE9PSIsInZhbHVlIjoiMFBYNi91OTdweUo1eC9ROERzU0RSVmNQMi9Mb0VLRjA3TGt0SUtscWZUOD0iLCJtYWMiOiI1NTk4NTFjYTc5ODhlNmQ4NDJhODRlNGIxZThkZmNkMjNjMjY0OGM5MTg1NTQ2MjA1ZWM2NmE5YmIzZGExODNkIiwidGFnIjoiIn0=',NULL,'2025-11-22 08:44:25','2025-11-22 08:44:25'),(7,'bank_transfer',0,'eyJpdiI6ImE4VDNTZ29wUmZsUFRtMWgyRDlNV2c9PSIsInZhbHVlIjoidkRrd3VMQTd4eHprQWJYdTUxZHl3dE9iRDRCUHo3ajEvTXhzeUVPa3VOS1FURDB5SWROeHVRK216YTF4Nk9pS0NQNm43elYzWVZ3M1VIUFduTU5Oa1drb0JaSWZFYVVoZ3U5QVB2NU5nR2sybWxyTGtkUnRNWXMzMkJpN2V5Rld3dnRFSnUzdy9seGFkSkRLZlNEaDRRPT0iLCJtYWMiOiJhZTJkNzM1ZGE5N2YzOTE3NDI5MDlmZTM4NWVmNmNlZTBiZTU5MjE4NjE0ZDdkMzViMmZiNDM3NTcwZTg1ZGVjIiwidGFnIjoiIn0=',NULL,'2025-11-22 08:44:25','2025-11-22 08:44:25'),(8,'custom',0,'eyJpdiI6IlE0dHR6SzJvN2MzdHFCY08vZXpqRUE9PSIsInZhbHVlIjoiVlc1NXFJc1Nhc1ZBTlFBRGRrVVlVZz09IiwibWFjIjoiM2VjNTY3NTEyMjZhZTZkZGI4MWU5MTFmNDViMjhlOTJhYTQwNTk1ZDU0MDE4Y2I4NjExODIxYWFiNmJhNTUzMiIsInRhZyI6IiJ9',NULL,'2025-11-22 08:44:25','2025-11-22 08:44:25');
/*!40000 ALTER TABLE `payment_gateway_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payment_transactions`
--

DROP TABLE IF EXISTS `payment_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payment_transactions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `school_id` bigint unsigned NOT NULL,
  `gateway_id` bigint unsigned NOT NULL,
  `transaction_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `external_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `request_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount` decimal(15,2) NOT NULL,
  `currency` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'UGX',
  `phone_number` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payable_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payable_id` bigint unsigned DEFAULT NULL,
  `description` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `metadata` json DEFAULT NULL,
  `status` enum('pending','processing','completed','failed','cancelled','expired','refunded') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `failure_reason` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `failure_code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `provider_request` json DEFAULT NULL,
  `provider_response` json DEFAULT NULL,
  `callback_data` json DEFAULT NULL,
  `initiated_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `callback_received_at` timestamp NULL DEFAULT NULL,
  `processing_time_ms` int DEFAULT NULL,
  `initiated_by` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payment_transactions_transaction_id_unique` (`transaction_id`),
  KEY `payment_transactions_gateway_id_foreign` (`gateway_id`),
  KEY `payment_transactions_initiated_by_foreign` (`initiated_by`),
  KEY `payment_transactions_school_id_status_index` (`school_id`,`status`),
  KEY `payment_transactions_school_id_created_at_index` (`school_id`,`created_at`),
  KEY `payment_transactions_payable_type_payable_id_index` (`payable_type`,`payable_id`),
  KEY `payment_transactions_phone_number_index` (`phone_number`),
  KEY `payment_transactions_external_id_index` (`external_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payment_transactions`
--

LOCK TABLES `payment_transactions` WRITE;
/*!40000 ALTER TABLE `payment_transactions` DISABLE KEYS */;
/*!40000 ALTER TABLE `payment_transactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `school_id` bigint unsigned NOT NULL,
  `invoice_id` bigint unsigned NOT NULL,
  `student_id` bigint unsigned NOT NULL,
  `receipt_number` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `payment_method` enum('cash','card','bank_transfer','check','mobile_money') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'cash',
  `payment_date` date NOT NULL,
  `reference_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `received_by` bigint unsigned DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payments_school_id_receipt_number_unique` (`school_id`,`receipt_number`),
  KEY `payments_invoice_id_foreign` (`invoice_id`),
  KEY `payments_student_id_foreign` (`student_id`),
  KEY `payments_school_id_index` (`school_id`),
  KEY `payments_payment_method_index` (`payment_method`),
  KEY `payments_payment_date_index` (`payment_date`),
  KEY `payments_received_by_foreign` (`received_by`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payroll_records`
--

DROP TABLE IF EXISTS `payroll_records`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payroll_records` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint unsigned NOT NULL,
  `payroll_number` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `period_month` varchar(2) COLLATE utf8mb4_unicode_ci NOT NULL,
  `period_year` varchar(4) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_date` date NOT NULL,
  `basic_salary` decimal(15,2) NOT NULL DEFAULT '0.00',
  `allowances` decimal(15,2) NOT NULL DEFAULT '0.00',
  `bonuses` decimal(15,2) NOT NULL DEFAULT '0.00',
  `overtime_pay` decimal(15,2) NOT NULL DEFAULT '0.00',
  `gross_salary` decimal(15,2) NOT NULL DEFAULT '0.00',
  `tax_deduction` decimal(15,2) NOT NULL DEFAULT '0.00',
  `nssf_deduction` decimal(15,2) NOT NULL DEFAULT '0.00',
  `health_insurance` decimal(15,2) NOT NULL DEFAULT '0.00',
  `loan_deduction` decimal(15,2) NOT NULL DEFAULT '0.00',
  `other_deductions` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total_deductions` decimal(15,2) NOT NULL DEFAULT '0.00',
  `net_salary` decimal(15,2) NOT NULL DEFAULT '0.00',
  `payment_method` enum('bank_transfer','cash','cheque','mobile_money') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'bank_transfer',
  `payment_reference` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('draft','pending','approved','paid','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `working_days` int DEFAULT NULL,
  `days_worked` int DEFAULT NULL,
  `overtime_hours` decimal(8,2) DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `metadata` json DEFAULT NULL,
  `approved_by` bigint unsigned DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `paid_by` bigint unsigned DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payroll_records_payroll_number_unique` (`payroll_number`),
  KEY `payroll_records_approved_by_foreign` (`approved_by`),
  KEY `payroll_records_paid_by_foreign` (`paid_by`),
  KEY `payroll_records_employee_id_period_year_period_month_index` (`employee_id`,`period_year`,`period_month`),
  KEY `payroll_records_payment_date_index` (`payment_date`),
  KEY `payroll_records_status_index` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payroll_records`
--

LOCK TABLES `payroll_records` WRITE;
/*!40000 ALTER TABLE `payroll_records` DISABLE KEYS */;
/*!40000 ALTER TABLE `payroll_records` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payroll_settings`
--

DROP TABLE IF EXISTS `payroll_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payroll_settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `category` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` json DEFAULT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'text',
  `label` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int NOT NULL DEFAULT '0',
  `validation_rules` json DEFAULT NULL,
  `options` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payroll_settings_key_unique` (`key`),
  KEY `payroll_settings_category_is_active_index` (`category`,`is_active`)
) ENGINE=MyISAM AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payroll_settings`
--

LOCK TABLES `payroll_settings` WRITE;
/*!40000 ALTER TABLE `payroll_settings` DISABLE KEYS */;
INSERT INTO `payroll_settings` VALUES (1,'pay_period','pay_frequency','\"monthly\"','select','Pay Frequency','How often employees receive their salary payments.',1,10,'[\"required\", \"in:monthly,semi_monthly,bi_weekly,weekly\"]','{\"weekly\": \"Weekly\", \"monthly\": \"Monthly\", \"bi_weekly\": \"Bi-Weekly (every two weeks)\", \"semi_monthly\": \"Semi-Monthly (twice a month)\"}','2025-11-25 17:29:48','2025-11-25 17:29:48'),(2,'pay_period','pay_day','25','number','Pay Day','Calendar day that payroll is processed (1 - 31).',1,20,'[\"nullable\", \"integer\", \"between:1,31\"]',NULL,'2025-11-25 17:29:48','2025-11-25 17:29:48'),(3,'pay_period','fiscal_year_start','\"01-01\"','text','Fiscal Year Start','Month and day when the new fiscal year begins (MM-DD).',1,30,'[\"nullable\", \"regex:/^\\\\d{2}-\\\\d{2}$/\"]',NULL,'2025-11-25 17:29:48','2025-11-25 17:29:48'),(4,'currency','default_currency','\"USD\"','select','Default Currency','Primary currency used for payroll calculations and reporting.',1,10,'[\"required\", \"string\", \"size:3\"]','{\"EUR\": \"EUR - Euro\", \"GBP\": \"GBP - British Pound\", \"GHS\": \"GHS - Ghanaian Cedi\", \"KES\": \"KES - Kenyan Shilling\", \"NGN\": \"NGN - Nigerian Naira\", \"TZS\": \"TZS - Tanzanian Shilling\", \"UGX\": \"UGX - Ugandan Shilling\", \"USD\": \"USD - US Dollar\", \"ZAR\": \"ZAR - South African Rand\"}','2025-11-25 17:29:48','2025-11-25 17:29:48'),(5,'currency','currency_symbol','\"$\"','text','Currency Symbol','Symbol displayed on payslips and payroll reports (auto-derived if left blank).',1,20,'[\"nullable\", \"string\", \"max:5\"]',NULL,'2025-11-25 17:29:48','2025-11-25 17:29:48'),(6,'currency','decimal_places','2','number','Decimal Places','Number of decimal places to use when formatting salary amounts.',1,30,'[\"nullable\", \"integer\", \"between:0,4\"]',NULL,'2025-11-25 17:29:48','2025-11-25 17:29:48'),(7,'salary_components','basic_salary_percentage','70','number','Basic Salary (%)','Percentage of total salary allocated to the basic salary component.',1,10,'[\"nullable\", \"numeric\", \"between:0,100\"]',NULL,'2025-11-25 17:29:48','2025-11-25 17:29:48'),(8,'salary_components','house_allowance_percentage','15','number','House Allowance (%)','Percentage allocation for housing allowance.',1,20,'[\"nullable\", \"numeric\", \"between:0,100\"]',NULL,'2025-11-25 17:29:48','2025-11-25 17:29:48'),(9,'salary_components','transport_allowance_percentage','10','number','Transport Allowance (%)','Percentage allocation for transport allowance.',1,30,'[\"nullable\", \"numeric\", \"between:0,100\"]',NULL,'2025-11-25 17:29:48','2025-11-25 17:29:48'),(10,'salary_components','medical_allowance_percentage','5','number','Medical Allowance (%)','Percentage allocation for medical allowance.',1,40,'[\"nullable\", \"numeric\", \"between:0,100\"]',NULL,'2025-11-25 17:29:48','2025-11-25 17:29:48'),(11,'deductions','income_tax_rate','25','number','Income Tax Rate (%)','Default income tax rate applied to taxable salary components.',1,10,'[\"nullable\", \"numeric\", \"between:0,100\"]',NULL,'2025-11-25 17:29:48','2025-11-25 17:29:48'),(12,'deductions','social_security_rate','10','number','Social Security Rate (%)','Employer contribution to national social security funds.',1,20,'[\"nullable\", \"numeric\", \"between:0,100\"]',NULL,'2025-11-25 17:29:48','2025-11-25 17:29:48'),(13,'deductions','provident_fund_rate','8','number','Provident Fund Rate (%)','Contribution allocated to provident or pension funds.',1,30,'[\"nullable\", \"numeric\", \"between:0,100\"]',NULL,'2025-11-25 17:29:48','2025-11-25 17:29:48'),(14,'overtime','overtime_rate_regular','1.5','number','Regular Overtime Rate (x)','Multiplier applied to standard hourly rate for overtime hours.',1,10,'[\"nullable\", \"numeric\", \"between:1,5\"]',NULL,'2025-11-25 17:29:48','2025-11-25 17:29:48'),(15,'overtime','overtime_rate_holiday','2','number','Holiday Overtime Rate (x)','Multiplier applied on public holidays or rest days.',1,20,'[\"nullable\", \"numeric\", \"between:1,5\"]',NULL,'2025-11-25 17:29:48','2025-11-25 17:29:48'),(16,'overtime','max_overtime_hours_monthly','40','number','Max Overtime Hours (Monthly)','Maximum overtime hours allowed per employee each month.',1,30,'[\"nullable\", \"integer\", \"between:0,200\"]',NULL,'2025-11-25 17:29:48','2025-11-25 17:29:48'),(17,'banking','bank_name','\"\"','text','Bank Name','Primary bank that handles payroll disbursements.',1,10,'[\"nullable\", \"string\", \"max:255\"]',NULL,'2025-11-25 17:29:48','2025-11-25 17:29:48'),(18,'banking','bank_account_number','\"\"','text','Bank Account Number','Account number used for payroll transfers.',1,20,'[\"nullable\", \"string\", \"max:64\"]',NULL,'2025-11-25 17:29:48','2025-11-25 17:29:48'),(19,'banking','payment_method','\"bank_transfer\"','select','Default Payment Method','Preferred method for paying employees.',1,30,'[\"nullable\", \"string\"]','{\"cash\": \"Cash\", \"check\": \"Check / Cheque\", \"mobile_money\": \"Mobile Money\", \"payroll_card\": \"Payroll Card\", \"bank_transfer\": \"Bank Transfer\", \"direct_deposit\": \"Direct Deposit\"}','2025-11-25 17:29:48','2025-11-25 17:29:48'),(20,'compliance','minimum_wage','50000','number','Minimum Monthly Wage','Legally mandated minimum wage for full-time employees.',1,10,'[\"nullable\", \"numeric\", \"min:0\"]',NULL,'2025-11-25 17:29:48','2025-11-25 17:29:48'),(21,'compliance','working_hours_per_day','8','number','Working Hours Per Day','Standard number of working hours per day.',1,20,'[\"nullable\", \"integer\", \"between:1,24\"]',NULL,'2025-11-25 17:29:48','2025-11-25 17:29:48'),(22,'compliance','working_days_per_week','5','number','Working Days Per Week','Number of working days per week.',1,30,'[\"nullable\", \"integer\", \"between:1,7\"]',NULL,'2025-11-25 17:29:48','2025-11-25 17:29:48'),(23,'integration','auto_process_payroll','false','boolean','Auto Process Payroll','Automatically queue payroll processing jobs when pay day is reached.',1,10,'[\"nullable\", \"boolean\"]',NULL,'2025-11-25 17:29:48','2025-11-25 17:29:48'),(24,'integration','email_pay_slips','true','boolean','Email Pay Slips','Send pay slips to employees by email after payroll is processed.',1,20,'[\"nullable\", \"boolean\"]',NULL,'2025-11-25 17:29:48','2025-11-25 17:29:48'),(25,'integration','export_to_accounting','false','boolean','Export to Accounting','Generate export files for accounting platforms after payroll completion.',1,30,'[\"nullable\", \"boolean\"]',NULL,'2025-11-25 17:29:48','2025-11-25 17:29:48'),(26,'integration','export_format','\"csv\"','select','Export Format','Default file format used when exporting payroll data.',1,40,'[\"nullable\", \"in:csv,json,xml\"]','{\"csv\": \"CSV (Spreadsheet)\", \"xml\": \"XML\", \"json\": \"JSON\"}','2025-11-25 17:29:48','2025-11-25 17:29:48');
/*!40000 ALTER TABLE `payroll_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=MyISAM AUTO_INCREMENT=251 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES (130,'users.view','web','2025-11-24 19:51:34','2025-11-24 19:51:34'),(131,'users.create','web','2025-11-24 19:51:34','2025-11-24 19:51:34'),(132,'users.edit','web','2025-11-24 19:51:34','2025-11-24 19:51:34'),(133,'users.delete','web','2025-11-24 19:51:34','2025-11-24 19:51:34'),(134,'users.approve','web','2025-11-24 19:51:34','2025-11-24 19:51:34'),(135,'users.suspend','web','2025-11-24 19:51:34','2025-11-24 19:51:34'),(136,'users.export','web','2025-11-24 19:51:34','2025-11-24 19:51:34'),(137,'roles.view','web','2025-11-24 19:51:34','2025-11-24 19:51:34'),(138,'roles.create','web','2025-11-24 19:51:34','2025-11-24 19:51:34'),(139,'roles.edit','web','2025-11-24 19:51:34','2025-11-24 19:51:34'),(140,'roles.delete','web','2025-11-24 19:51:34','2025-11-24 19:51:34'),(141,'permissions.assign','web','2025-11-24 19:51:34','2025-11-24 19:51:34'),(142,'students.view','web','2025-11-24 19:51:34','2025-11-24 19:51:34'),(143,'students.create','web','2025-11-24 19:51:34','2025-11-24 19:51:34'),(144,'students.edit','web','2025-11-24 19:51:34','2025-11-24 19:51:34'),(145,'students.delete','web','2025-11-24 19:51:34','2025-11-24 19:51:34'),(146,'students.enroll','web','2025-11-24 19:51:35','2025-11-24 19:51:35'),(147,'students.transfer','web','2025-11-24 19:51:35','2025-11-24 19:51:35'),(148,'students.graduate','web','2025-11-24 19:51:35','2025-11-24 19:51:35'),(149,'teachers.view','web','2025-11-24 19:51:35','2025-11-24 19:51:35'),(150,'teachers.create','web','2025-11-24 19:51:35','2025-11-24 19:51:35'),(151,'teachers.edit','web','2025-11-24 19:51:35','2025-11-24 19:51:35'),(152,'teachers.delete','web','2025-11-24 19:51:35','2025-11-24 19:51:35'),(153,'teachers.assign','web','2025-11-24 19:51:35','2025-11-24 19:51:35'),(154,'classes.view','web','2025-11-24 19:51:35','2025-11-24 19:51:35'),(155,'classes.create','web','2025-11-24 19:51:35','2025-11-24 19:51:35'),(156,'classes.edit','web','2025-11-24 19:51:35','2025-11-24 19:51:35'),(157,'classes.delete','web','2025-11-24 19:51:35','2025-11-24 19:51:35'),(158,'classes.assign','web','2025-11-24 19:51:35','2025-11-24 19:51:35'),(159,'subjects.view','web','2025-11-24 19:51:35','2025-11-24 19:51:35'),(160,'subjects.create','web','2025-11-24 19:51:35','2025-11-24 19:51:35'),(161,'subjects.edit','web','2025-11-24 19:51:35','2025-11-24 19:51:35'),(162,'subjects.delete','web','2025-11-24 19:51:35','2025-11-24 19:51:35'),(163,'attendance.view','web','2025-11-24 19:51:35','2025-11-24 19:51:35'),(164,'attendance.mark','web','2025-11-24 19:51:35','2025-11-24 19:51:35'),(165,'attendance.edit','web','2025-11-24 19:51:35','2025-11-24 19:51:35'),(166,'attendance.report','web','2025-11-24 19:51:35','2025-11-24 19:51:35'),(167,'grades.view','web','2025-11-24 19:51:35','2025-11-24 19:51:35'),(168,'grades.create','web','2025-11-24 19:51:35','2025-11-24 19:51:35'),(169,'grades.edit','web','2025-11-24 19:51:35','2025-11-24 19:51:35'),(170,'grades.delete','web','2025-11-24 19:51:35','2025-11-24 19:51:35'),(171,'grades.approve','web','2025-11-24 19:51:35','2025-11-24 19:51:35'),(172,'grades.report','web','2025-11-24 19:51:35','2025-11-24 19:51:35'),(173,'assignments.view','web','2025-11-24 19:51:35','2025-11-24 19:51:35'),(174,'assignments.create','web','2025-11-24 19:51:35','2025-11-24 19:51:35'),(175,'assignments.edit','web','2025-11-24 19:51:35','2025-11-24 19:51:35'),(176,'assignments.delete','web','2025-11-24 19:51:35','2025-11-24 19:51:35'),(177,'assignments.grade','web','2025-11-24 19:51:35','2025-11-24 19:51:35'),(178,'exams.view','web','2025-11-24 19:51:35','2025-11-24 19:51:35'),(179,'exams.create','web','2025-11-24 19:51:35','2025-11-24 19:51:35'),(180,'exams.edit','web','2025-11-24 19:51:35','2025-11-24 19:51:35'),(181,'exams.delete','web','2025-11-24 19:51:35','2025-11-24 19:51:35'),(182,'exams.schedule','web','2025-11-24 19:51:35','2025-11-24 19:51:35'),(183,'timetable.view','web','2025-11-24 19:51:35','2025-11-24 19:51:35'),(184,'timetable.create','web','2025-11-24 19:51:35','2025-11-24 19:51:35'),(185,'timetable.edit','web','2025-11-24 19:51:35','2025-11-24 19:51:35'),(186,'timetable.delete','web','2025-11-24 19:51:35','2025-11-24 19:51:35'),(187,'finance.view','web','2025-11-24 19:51:35','2025-11-24 19:51:35'),(188,'finance.create','web','2025-11-24 19:51:35','2025-11-24 19:51:35'),(189,'finance.edit','web','2025-11-24 19:51:35','2025-11-24 19:51:35'),(190,'finance.delete','web','2025-11-24 19:51:35','2025-11-24 19:51:35'),(191,'fees.manage','web','2025-11-24 19:51:35','2025-11-24 19:51:35'),(192,'payments.process','web','2025-11-24 19:51:35','2025-11-24 19:51:35'),(193,'payments.refund','web','2025-11-24 19:51:35','2025-11-24 19:51:35'),(194,'invoices.generate','web','2025-11-24 19:51:36','2025-11-24 19:51:36'),(195,'hr.view','web','2025-11-24 19:51:36','2025-11-24 19:51:36'),(196,'hr.manage','web','2025-11-24 19:51:36','2025-11-24 19:51:36'),(197,'employees.view','web','2025-11-24 19:51:36','2025-11-24 19:51:36'),(198,'employees.create','web','2025-11-24 19:51:36','2025-11-24 19:51:36'),(199,'employees.edit','web','2025-11-24 19:51:36','2025-11-24 19:51:36'),(200,'employees.delete','web','2025-11-24 19:51:36','2025-11-24 19:51:36'),(201,'leave-requests.view','web','2025-11-24 19:51:36','2025-11-24 19:51:36'),(202,'leave-requests.create','web','2025-11-24 19:51:36','2025-11-24 19:51:36'),(203,'leave-requests.approve','web','2025-11-24 19:51:36','2025-11-24 19:51:36'),(204,'leave-requests.reject','web','2025-11-24 19:51:36','2025-11-24 19:51:36'),(205,'pamphlets.view','web','2025-11-24 19:51:36','2025-11-24 19:51:36'),(206,'pamphlets.create','web','2025-11-24 19:51:36','2025-11-24 19:51:36'),(207,'pamphlets.edit','web','2025-11-24 19:51:36','2025-11-24 19:51:36'),(208,'pamphlets.delete','web','2025-11-24 19:51:36','2025-11-24 19:51:36'),(209,'pamphlets.publish','web','2025-11-24 19:51:36','2025-11-24 19:51:36'),(210,'books.view','web','2025-11-24 19:51:36','2025-11-24 19:51:36'),(211,'books.create','web','2025-11-24 19:51:36','2025-11-24 19:51:36'),(212,'books.edit','web','2025-11-24 19:51:36','2025-11-24 19:51:36'),(213,'books.delete','web','2025-11-24 19:51:36','2025-11-24 19:51:36'),(214,'bookstore.view','web','2025-11-24 19:51:36','2025-11-24 19:51:36'),(215,'bookstore.manage','web','2025-11-24 19:51:36','2025-11-24 19:51:36'),(216,'bookstore.orders','web','2025-11-24 19:51:36','2025-11-24 19:51:36'),(217,'bookstore.purchase','web','2025-11-24 19:51:36','2025-11-24 19:51:36'),(218,'library.view','web','2025-11-24 19:51:36','2025-11-24 19:51:36'),(219,'library.manage','web','2025-11-24 19:51:36','2025-11-24 19:51:36'),(220,'library.issue','web','2025-11-24 19:51:36','2025-11-24 19:51:36'),(221,'library.return','web','2025-11-24 19:51:36','2025-11-24 19:51:36'),(222,'reports.view','web','2025-11-24 19:51:37','2025-11-24 19:51:37'),(223,'reports.generate','web','2025-11-24 19:51:37','2025-11-24 19:51:37'),(224,'reports.export','web','2025-11-24 19:51:37','2025-11-24 19:51:37'),(225,'reports.custom','web','2025-11-24 19:51:37','2025-11-24 19:51:37'),(226,'settings.view','web','2025-11-24 19:51:37','2025-11-24 19:51:37'),(227,'settings.edit','web','2025-11-24 19:51:37','2025-11-24 19:51:37'),(228,'settings.general','web','2025-11-24 19:51:37','2025-11-24 19:51:37'),(229,'settings.academic','web','2025-11-24 19:51:37','2025-11-24 19:51:37'),(230,'settings.system','web','2025-11-24 19:51:37','2025-11-24 19:51:37'),(231,'settings.mail','web','2025-11-24 19:51:37','2025-11-24 19:51:37'),(232,'settings.payment','web','2025-11-24 19:51:37','2025-11-24 19:51:37'),(233,'settings.messaging','web','2025-11-24 19:51:37','2025-11-24 19:51:37'),(234,'messages.send','web','2025-11-24 19:51:37','2025-11-24 19:51:37'),(235,'messages.view','web','2025-11-24 19:51:37','2025-11-24 19:51:37'),(236,'announcements.create','web','2025-11-24 19:51:37','2025-11-24 19:51:37'),(237,'announcements.edit','web','2025-11-24 19:51:37','2025-11-24 19:51:37'),(238,'notifications.send','web','2025-11-24 19:51:37','2025-11-24 19:51:37'),(239,'documents.view','web','2025-11-24 19:51:37','2025-11-24 19:51:37'),(240,'documents.upload','web','2025-11-24 19:51:37','2025-11-24 19:51:37'),(241,'documents.download','web','2025-11-24 19:51:37','2025-11-24 19:51:37'),(242,'documents.delete','web','2025-11-24 19:51:37','2025-11-24 19:51:37'),(243,'departments.view','web','2025-11-24 19:51:37','2025-11-24 19:51:37'),(244,'departments.create','web','2025-11-24 19:51:38','2025-11-24 19:51:38'),(245,'departments.edit','web','2025-11-24 19:51:38','2025-11-24 19:51:38'),(246,'departments.delete','web','2025-11-24 19:51:38','2025-11-24 19:51:38'),(247,'positions.view','web','2025-11-24 19:51:38','2025-11-24 19:51:38'),(248,'positions.create','web','2025-11-24 19:51:38','2025-11-24 19:51:38'),(249,'positions.edit','web','2025-11-24 19:51:38','2025-11-24 19:51:38'),(250,'positions.delete','web','2025-11-24 19:51:38','2025-11-24 19:51:38');
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `platform_integrations`
--

DROP TABLE IF EXISTS `platform_integrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `platform_integrations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `platform` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `managed_by_admin` tinyint(1) NOT NULL DEFAULT '0',
  `api_key` text COLLATE utf8mb4_unicode_ci,
  `api_secret` text COLLATE utf8mb4_unicode_ci,
  `client_id` text COLLATE utf8mb4_unicode_ci,
  `client_secret` text COLLATE utf8mb4_unicode_ci,
  `redirect_uri` text COLLATE utf8mb4_unicode_ci,
  `access_token` text COLLATE utf8mb4_unicode_ci,
  `refresh_token` text COLLATE utf8mb4_unicode_ci,
  `token_expires_at` timestamp NULL DEFAULT NULL,
  `last_tested_at` timestamp NULL DEFAULT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'needs_configuration',
  `status_message` text COLLATE utf8mb4_unicode_ci,
  `additional_settings` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `platform_integrations_platform_unique` (`platform`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `platform_integrations`
--

LOCK TABLES `platform_integrations` WRITE;
/*!40000 ALTER TABLE `platform_integrations` DISABLE KEYS */;
/*!40000 ALTER TABLE `platform_integrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `positions`
--

DROP TABLE IF EXISTS `positions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `positions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `department_id` bigint unsigned DEFAULT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `positions_department_id_foreign` (`department_id`)
) ENGINE=MyISAM AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `positions`
--

LOCK TABLES `positions` WRITE;
/*!40000 ALTER TABLE `positions` DISABLE KEYS */;
INSERT INTO `positions` VALUES (1,1,'Head Teacher / Principal','P-0001',NULL,'2025-11-23 13:40:08','2025-11-23 13:40:08'),(2,1,'Deputy Head Teacher (Administration)','P-0002',NULL,'2025-11-23 13:40:57','2025-11-23 13:40:57'),(3,1,'Secretary / Receptionist','P-0003',NULL,'2025-11-23 14:22:07','2025-11-23 14:22:21'),(4,2,'Accountant','P-0004',NULL,'2025-11-23 14:23:19','2025-11-23 14:23:19'),(5,2,'Storekeeper','P-0005',NULL,'2025-11-23 14:24:25','2025-11-23 14:24:25'),(6,3,'Director of Studies','P-0006',NULL,'2025-11-23 14:25:02','2025-11-23 14:25:02'),(7,3,'Librarian','P-0007',NULL,'2025-11-23 14:25:54','2025-11-23 14:25:54'),(8,3,'Classroom Teacher','P-0008',NULL,'2025-11-23 14:26:43','2025-11-23 14:26:58'),(9,3,'Laboratory Technician','P-0009',NULL,'2025-11-23 14:27:47','2025-11-23 14:27:47'),(10,4,'HR Manager','P-0010',NULL,'2025-11-23 14:28:29','2025-11-23 14:28:29'),(11,5,'School Nurse','P-0011',NULL,'2025-11-23 14:29:12','2025-11-23 14:29:12'),(12,6,'ICT Support Technician','P-0012',NULL,'2025-11-23 14:29:59','2025-11-23 14:29:59'),(13,7,'Boarding Master / Matron','P-0013',NULL,'2025-11-23 14:30:43','2025-11-23 14:30:43'),(14,8,'Security Department','P-0014',NULL,'2025-11-23 14:31:26','2025-11-23 14:31:26'),(15,9,'Electrician','P-0015',NULL,'2025-11-23 14:33:16','2025-11-23 14:33:16'),(16,9,'Plumber','P-0016',NULL,'2025-11-23 14:34:17','2025-11-23 14:34:17'),(17,9,'Carpenter','P-0017',NULL,'2025-11-23 14:35:23','2025-11-23 14:35:23'),(18,10,'Catering Manager','P-0018',NULL,'2025-11-23 14:36:17','2025-11-23 14:36:17'),(19,10,'Chef / Cook','P-0019',NULL,'2025-11-23 14:36:59','2025-11-23 14:36:59'),(20,13,'Teacher','TEACH','Teaching staff position','2025-11-25 12:25:00','2025-11-25 12:25:00'),(21,14,'Administrator','ADMIN','School administrator role','2025-11-25 12:33:54','2025-11-25 12:33:54');
/*!40000 ALTER TABLE `positions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `quiz_answers`
--

DROP TABLE IF EXISTS `quiz_answers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `quiz_answers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `quiz_attempt_id` bigint unsigned NOT NULL,
  `quiz_question_id` bigint unsigned NOT NULL,
  `answer` text COLLATE utf8mb4_unicode_ci,
  `is_correct` tinyint(1) DEFAULT NULL,
  `marks_awarded` decimal(5,2) NOT NULL DEFAULT '0.00',
  `feedback` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `quiz_answers_quiz_attempt_id_quiz_question_id_unique` (`quiz_attempt_id`,`quiz_question_id`),
  KEY `quiz_answers_quiz_question_id_foreign` (`quiz_question_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `quiz_answers`
--

LOCK TABLES `quiz_answers` WRITE;
/*!40000 ALTER TABLE `quiz_answers` DISABLE KEYS */;
/*!40000 ALTER TABLE `quiz_answers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `quiz_attempts`
--

DROP TABLE IF EXISTS `quiz_attempts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `quiz_attempts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `quiz_id` bigint unsigned NOT NULL,
  `student_id` bigint unsigned NOT NULL,
  `started_at` timestamp NULL DEFAULT NULL,
  `submitted_at` timestamp NULL DEFAULT NULL,
  `score_auto` int NOT NULL DEFAULT '0',
  `score_manual` int NOT NULL DEFAULT '0',
  `score_total` int NOT NULL DEFAULT '0',
  `answers` json DEFAULT NULL,
  `is_late` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `quiz_attempts_student_id_foreign` (`student_id`),
  KEY `quiz_attempts_quiz_id_student_id_index` (`quiz_id`,`student_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `quiz_attempts`
--

LOCK TABLES `quiz_attempts` WRITE;
/*!40000 ALTER TABLE `quiz_attempts` DISABLE KEYS */;
INSERT INTO `quiz_attempts` VALUES (1,1,14,'2025-11-28 05:55:53','2025-11-28 05:56:48',10,0,10,'{\"1\": \"C\", \"2\": \"A\", \"3\": \"B\", \"4\": \"B\", \"5\": \"C\"}',0,'2025-11-28 05:55:53','2025-11-28 07:14:13');
/*!40000 ALTER TABLE `quiz_attempts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `quiz_class`
--

DROP TABLE IF EXISTS `quiz_class`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `quiz_class` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `quiz_id` bigint unsigned NOT NULL,
  `class_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `quiz_class_quiz_id_foreign` (`quiz_id`),
  KEY `quiz_class_class_id_foreign` (`class_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `quiz_class`
--

LOCK TABLES `quiz_class` WRITE;
/*!40000 ALTER TABLE `quiz_class` DISABLE KEYS */;
/*!40000 ALTER TABLE `quiz_class` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `quiz_question`
--

DROP TABLE IF EXISTS `quiz_question`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `quiz_question` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `quiz_id` bigint unsigned NOT NULL,
  `question_id` bigint unsigned NOT NULL,
  `points` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `quiz_question_quiz_id_foreign` (`quiz_id`),
  KEY `quiz_question_question_id_foreign` (`question_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `quiz_question`
--

LOCK TABLES `quiz_question` WRITE;
/*!40000 ALTER TABLE `quiz_question` DISABLE KEYS */;
/*!40000 ALTER TABLE `quiz_question` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `quiz_questions`
--

DROP TABLE IF EXISTS `quiz_questions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `quiz_questions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `quiz_id` bigint unsigned NOT NULL,
  `type` enum('multiple_choice','true_false','short_answer','essay') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'multiple_choice',
  `question` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `explanation` text COLLATE utf8mb4_unicode_ci,
  `marks` int NOT NULL DEFAULT '1',
  `order` int NOT NULL DEFAULT '0',
  `options` json DEFAULT NULL,
  `correct_answer` text COLLATE utf8mb4_unicode_ci,
  `is_required` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `quiz_questions_quiz_id_order_index` (`quiz_id`,`order`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `quiz_questions`
--

LOCK TABLES `quiz_questions` WRITE;
/*!40000 ALTER TABLE `quiz_questions` DISABLE KEYS */;
INSERT INTO `quiz_questions` VALUES (1,1,'multiple_choice','Question 1: Counting Objects Look at the picture of apples 🍎🍎🍎🍎. How many apples are there?',NULL,2,0,'{\"A\": \"2\", \"B\": \"3\", \"C\": \"4\", \"D\": \"5\"}','\"C\"',1,'2025-11-27 18:19:27','2025-11-28 07:14:13',NULL),(2,1,'multiple_choice','What is 2+3?','Explain why this is the correct answer',2,0,'{\"A\": \"4\", \"B\": \"5\", \"C\": \"6\", \"D\": \"7\"}','\"A\"',1,'2025-11-27 18:21:04','2025-11-28 07:14:13',NULL),(3,1,'multiple_choice','Simple Subtraction: If you have 5 bananas and eat 2, how many are left?','Explain why this answer is correct',2,0,'{\"A\": \"2\", \"B\": \"3\", \"C\": \"4\", \"D\": \"5\"}','\"B\"',1,'2025-11-27 18:22:26','2025-11-28 07:14:13',NULL),(4,1,'multiple_choice','Number Recognition: Which number comes after 7?',NULL,2,0,'{\"A\": \"6\", \"B\": \"8\", \"C\": \"9\", \"D\": \"10\"}','\"B\"',1,'2025-11-27 18:24:02','2025-11-28 07:14:13',NULL),(5,1,'multiple_choice','Shapes: Which shape has 3 sides?',NULL,2,0,'{\"A\": \"Circle\", \"B\": \"Square\", \"C\": \"Triangle\", \"D\": \"Rectangle\"}','\"C\"',1,'2025-11-27 18:26:23','2025-11-28 07:14:13',NULL),(6,2,'multiple_choice','Which shape has 3 sides?',NULL,2,0,'{\"A\": \"Circle\", \"B\": \"Square\", \"C\": \"Triangle\", \"D\": \"Rectangle\"}',NULL,1,'2025-11-28 06:17:00','2025-11-28 06:17:00',NULL),(7,2,'multiple_choice','Look at this pattern: 🔵 🔴 🔵 🔴 … What comes',NULL,2,0,'{\"A\": \"🔵\", \"B\": \"🔴\", \"C\": \"🟢\", \"D\": \"⚫\"}',NULL,1,'2025-11-28 06:18:29','2025-11-28 06:18:29',NULL),(8,2,'multiple_choice','Which shape is round and has no corners?',NULL,2,0,'{\"A\": \"Triangle\", \"B\": \"Circle\", \"C\": \"Square\", \"D\": \"Rectangle\"}',NULL,1,'2025-11-28 06:19:39','2025-11-28 06:19:39',NULL),(9,2,'multiple_choice','Complete the pattern: ◼ ◼ ◻ ◼ ◼ ◻ …',NULL,2,0,'{\"A\": \"◼\", \"B\": \"◻\", \"C\": \"🔵\", \"D\": \"Triangle\"}',NULL,1,'2025-11-28 06:20:52','2025-11-28 06:20:52',NULL),(10,2,'multiple_choice','Which of these shapes has 4 equal sides?',NULL,1,0,'{\"A\": \"Rectangle\", \"B\": \"Square\", \"C\": \"Triangle\", \"D\": \"Circle\"}',NULL,1,'2025-11-28 06:24:26','2025-11-28 06:24:38','2025-11-28 06:24:38'),(11,2,'multiple_choice','Which of these shapes has 4 equal sides?',NULL,2,0,'{\"A\": \"Rectangle\", \"B\": \"Square\", \"C\": \"Triangle\", \"D\": \"Circle\"}',NULL,1,'2025-11-28 06:25:39','2025-11-28 06:25:39',NULL);
/*!40000 ALTER TABLE `quiz_questions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `quizzes`
--

DROP TABLE IF EXISTS `quizzes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `quizzes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `teacher_id` bigint unsigned NOT NULL,
  `class_id` bigint unsigned DEFAULT NULL,
  `subject_id` bigint unsigned DEFAULT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `instructions` text COLLATE utf8mb4_unicode_ci,
  `available_from` datetime DEFAULT NULL,
  `available_until` datetime DEFAULT NULL,
  `total_points` int NOT NULL DEFAULT '0',
  `start_at` timestamp NULL DEFAULT NULL,
  `end_at` timestamp NULL DEFAULT NULL,
  `duration_minutes` int DEFAULT NULL,
  `total_marks` int NOT NULL DEFAULT '0',
  `pass_marks` int DEFAULT NULL,
  `max_attempts` int NOT NULL DEFAULT '1',
  `shuffle_questions` tinyint(1) NOT NULL DEFAULT '0',
  `shuffle_answers` tinyint(1) NOT NULL DEFAULT '0',
  `show_results_immediately` tinyint(1) NOT NULL DEFAULT '1',
  `show_correct_answers` tinyint(1) NOT NULL DEFAULT '1',
  `allow_review` tinyint(1) NOT NULL DEFAULT '1',
  `status` enum('draft','published','archived') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `is_published` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `quizzes_teacher_id_foreign` (`teacher_id`),
  KEY `quizzes_class_id_foreign` (`class_id`),
  KEY `quizzes_subject_id_foreign` (`subject_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `quizzes`
--

LOCK TABLES `quizzes` WRITE;
/*!40000 ALTER TABLE `quizzes` DISABLE KEYS */;
INSERT INTO `quizzes` VALUES (1,8,1,1,'Counting Fun: Primary One Math Review','This quiz helps Primary One pupils practice their counting skills in a fun and interactive way. Learners will review numbers, simple addition, and subtraction through playful questions designed to build confidence and strengthen their understanding of basic mathematics.','Read each question carefully before answering.\r\n\r\nChoose the correct answer from the options given.\r\n\r\nCount slowly and check your work before moving to the next question.\r\n\r\nYou have 30 minutes to finish the quiz.\r\n\r\nTry your best—don’t worry if you make mistakes, this quiz is for practice and learning.\r\n\r\nWhen you finish, click Submit to see your results.','2025-11-28 00:04:00','2025-11-29 00:04:00',0,NULL,NULL,45,20,15,2,1,1,1,1,1,'published',0,'2025-11-27 18:13:01','2025-11-27 18:28:09',NULL),(2,8,1,1,'Shapes and Patterns Practice Quiz','This quiz helps students practice identifying, comparing, and completing basic shapes and patterns. It reinforces visual recognition skills, encourages logical thinking, and prepares learners to apply these concepts in everyday problem-solving.','Read each question carefully before answering.\r\n\r\nChoose the best answer from the options provided.\r\n\r\nLook closely at the shapes and patterns shown in the questions.\r\n\r\nPay attention to details such as size, color, and sequence.\r\n\r\nDo not rush—take your time to think before selecting an answer.\r\n\r\nYou have 30 minutes to complete the quiz.\r\n\r\nOnce you submit, you will see your results immediately.','2025-11-28 12:14:00','2025-11-29 12:14:00',0,NULL,NULL,30,10,8,1,1,1,1,1,1,'published',0,'2025-11-28 06:14:50','2025-11-28 06:15:36',NULL);
/*!40000 ALTER TABLE `quizzes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `report_logs`
--

DROP TABLE IF EXISTS `report_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `report_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `format` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'csv',
  `parameters` json DEFAULT NULL,
  `file_path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `generated_by` bigint unsigned DEFAULT NULL,
  `started_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `generated_at` timestamp NULL DEFAULT NULL,
  `rows_count` int unsigned NOT NULL DEFAULT '0',
  `size_bytes` bigint unsigned NOT NULL DEFAULT '0',
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'completed',
  `error` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `report_logs_type_generated_at_index` (`type`,`generated_at`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `report_logs`
--

LOCK TABLES `report_logs` WRITE;
/*!40000 ALTER TABLE `report_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `report_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_has_permissions`
--

DROP TABLE IF EXISTS `role_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_has_permissions`
--

LOCK TABLES `role_has_permissions` WRITE;
/*!40000 ALTER TABLE `role_has_permissions` DISABLE KEYS */;
INSERT INTO `role_has_permissions` VALUES (9,1),(9,2),(10,1),(10,2),(11,1),(11,2),(12,1),(13,1),(13,2),(14,1),(14,2),(15,1),(15,2),(16,1),(16,2),(17,1),(17,2),(18,1),(18,2),(19,1),(20,1),(20,2),(21,1),(21,2),(21,3),(21,5),(21,7),(21,8),(22,1),(22,2),(23,1),(23,2),(23,3),(23,8),(24,1),(24,2),(25,1),(25,2),(26,1),(26,2),(27,1),(27,2),(28,1),(28,2),(28,7),(28,8),(29,1),(29,2),(30,1),(30,2),(31,1),(31,2),(32,1),(32,2),(32,8),(33,1),(33,2),(33,3),(33,4),(33,8),(34,1),(34,2),(34,8),(35,1),(35,2),(35,8),(36,1),(36,2),(37,1),(37,2),(37,3),(38,1),(38,2),(38,3),(38,4),(38,8),(39,1),(39,2),(39,8),(40,1),(40,2),(40,8),(41,1),(41,2),(42,1),(42,2),(42,3),(42,4),(42,5),(42,8),(43,1),(43,2),(43,3),(44,1),(44,2),(44,3),(45,1),(45,2),(45,8),(46,1),(46,2),(46,3),(46,4),(46,5),(46,8),(47,1),(47,2),(47,3),(48,1),(48,2),(48,3),(49,1),(49,2),(50,1),(50,2),(50,8),(51,1),(51,2),(52,1),(52,2),(52,3),(52,4),(53,1),(53,2),(53,3),(54,1),(54,2),(54,3),(55,1),(55,2),(56,1),(56,2),(56,3),(57,1),(57,2),(57,3),(57,4),(58,1),(58,2),(59,1),(59,2),(60,1),(60,2),(61,1),(61,2),(62,1),(62,2),(62,3),(62,4),(62,8),(63,1),(63,2),(63,8),(64,1),(64,2),(64,8),(65,1),(65,2),(66,1),(66,2),(66,5),(66,6),(67,1),(67,2),(67,6),(68,1),(68,2),(68,6),(69,1),(69,2),(70,1),(70,2),(70,6),(71,1),(71,2),(71,6),(72,1),(72,2),(72,6),(73,1),(73,2),(73,5),(73,6),(74,1),(74,2),(75,1),(75,2),(76,1),(76,2),(77,1),(77,2),(78,1),(78,2),(79,1),(79,2),(80,1),(80,2),(81,1),(81,2),(82,1),(82,2),(83,1),(83,2),(84,1),(84,2),(84,3),(85,1),(85,2),(86,1),(86,2),(87,1),(87,2),(88,1),(88,2),(89,1),(89,2),(89,3),(89,4),(89,7),(90,1),(90,2),(91,1),(91,2),(92,1),(92,2),(93,1),(93,2),(93,3),(93,4),(93,7),(94,1),(94,2),(95,1),(95,2),(96,1),(96,2),(96,4),(96,7),(97,1),(97,2),(97,4),(97,7),(98,1),(98,2),(98,7),(99,1),(99,2),(99,7),(100,1),(100,2),(100,7),(101,1),(101,2),(101,3),(101,5),(101,6),(101,7),(101,8),(102,1),(102,2),(102,3),(102,6),(102,7),(102,8),(103,1),(103,2),(103,6),(104,1),(104,2),(104,8),(105,1),(105,2),(106,1),(106,2),(107,1),(107,2),(108,1),(108,2),(109,1),(110,1),(110,2),(111,1),(111,2),(112,1),(112,2),(113,1),(113,2),(113,3),(113,5),(114,1),(114,2),(114,3),(114,4),(114,5),(115,1),(115,2),(116,1),(116,2),(117,1),(117,2),(118,1),(118,2),(118,3),(118,4),(119,1),(119,2),(119,3),(120,1),(120,2),(120,3),(120,4),(121,1),(121,2),(122,1),(122,2),(122,8),(123,1),(123,2),(124,1),(124,2),(124,8),(125,1),(125,2),(126,1),(126,2),(127,1),(127,2),(128,1),(128,2),(129,1),(129,2),(130,11),(130,12),(131,11),(131,12),(132,11),(132,12),(133,11),(134,11),(134,12),(135,11),(135,12),(136,11),(136,12),(137,11),(137,12),(138,11),(138,12),(139,11),(139,12),(140,11),(141,11),(141,12),(142,11),(142,12),(142,13),(142,15),(142,17),(142,18),(143,11),(143,12),(144,11),(144,12),(144,13),(144,18),(145,11),(145,12),(146,11),(146,12),(147,11),(147,12),(148,11),(148,12),(149,11),(149,12),(149,17),(149,18),(150,11),(150,12),(151,11),(151,12),(152,11),(152,12),(153,11),(153,12),(153,18),(154,11),(154,12),(154,13),(154,14),(154,18),(155,11),(155,12),(155,18),(156,11),(156,12),(156,18),(157,11),(157,12),(158,11),(158,12),(158,13),(159,11),(159,12),(159,13),(159,14),(159,18),(160,11),(160,12),(160,18),(161,11),(161,12),(161,18),(162,11),(162,12),(163,11),(163,12),(163,13),(163,14),(163,15),(163,18),(164,11),(164,12),(164,13),(165,11),(165,12),(165,13),(166,11),(166,12),(166,18),(167,11),(167,12),(167,13),(167,14),(167,15),(167,18),(168,11),(168,12),(168,13),(169,11),(169,12),(169,13),(170,11),(170,12),(171,11),(171,12),(171,18),(172,11),(172,12),(173,11),(173,12),(173,13),(173,14),(174,11),(174,12),(174,13),(175,11),(175,12),(175,13),(176,11),(176,12),(177,11),(177,12),(177,13),(178,11),(178,12),(178,13),(178,14),(179,11),(179,12),(180,11),(180,12),(181,11),(181,12),(182,11),(182,12),(183,11),(183,12),(183,13),(183,14),(183,18),(184,11),(184,12),(184,18),(185,11),(185,12),(185,18),(186,11),(186,12),(187,11),(187,12),(187,15),(187,16),(187,20),(188,11),(188,12),(188,16),(188,20),(189,11),(189,12),(189,16),(189,20),(190,11),(190,12),(191,11),(191,12),(191,16),(191,20),(192,11),(192,12),(192,16),(192,20),(193,11),(193,12),(193,16),(193,20),(194,11),(194,12),(194,15),(194,16),(194,20),(195,11),(195,12),(196,11),(196,12),(197,11),(197,12),(198,11),(198,12),(199,11),(199,12),(200,11),(200,12),(201,11),(201,12),(202,11),(202,12),(203,11),(203,12),(204,11),(204,12),(205,11),(205,12),(205,13),(206,11),(206,12),(207,11),(207,12),(208,11),(208,12),(209,11),(209,12),(210,11),(210,12),(210,13),(210,14),(210,17),(211,11),(211,12),(212,11),(212,12),(213,11),(213,12),(214,11),(214,12),(214,13),(214,14),(214,17),(215,11),(215,12),(216,11),(216,12),(217,11),(217,12),(217,14),(217,17),(218,11),(218,12),(218,14),(218,17),(219,11),(219,12),(219,17),(220,11),(220,12),(220,17),(221,11),(221,12),(221,17),(222,11),(222,12),(222,13),(222,15),(222,16),(222,17),(222,18),(222,19),(222,20),(222,21),(223,11),(223,12),(223,13),(223,16),(223,17),(223,18),(223,20),(224,11),(224,12),(224,16),(224,20),(225,11),(225,12),(225,18),(226,11),(226,12),(227,11),(227,12),(228,11),(228,12),(229,11),(229,12),(230,11),(231,11),(231,12),(232,11),(232,12),(233,11),(233,12),(234,11),(234,12),(234,13),(234,15),(235,11),(235,12),(235,13),(235,14),(235,15),(235,19),(235,21),(236,11),(236,12),(237,11),(237,12),(238,11),(238,12),(239,11),(239,12),(239,13),(239,14),(239,19),(239,21),(240,11),(240,12),(240,13),(241,11),(241,12),(241,13),(241,14),(242,11),(242,12),(243,11),(243,12),(243,18),(244,11),(244,12),(245,11),(245,12),(245,18),(246,11),(246,12),(247,11),(247,12),(248,11),(248,12),(249,11),(249,12),(250,11),(250,12);
/*!40000 ALTER TABLE `role_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned DEFAULT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_tenant_id_name_guard_name_unique` (`tenant_id`,`name`,`guard_name`),
  KEY `roles_team_foreign_key_index` (`tenant_id`)
) ENGINE=MyISAM AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (11,NULL,'Super-Admin','web','2025-11-24 19:51:38','2025-11-24 19:51:38'),(12,NULL,'Admin','web','2025-11-24 19:51:38','2025-11-24 19:51:38'),(13,NULL,'Teacher','web','2025-11-24 19:51:38','2025-11-24 19:51:38'),(14,NULL,'Student','web','2025-11-24 19:51:38','2025-11-24 19:51:38'),(15,NULL,'Parent','web','2025-11-24 19:51:38','2025-11-24 19:51:38'),(16,NULL,'Accountant','web','2025-11-24 19:51:38','2025-11-24 19:51:38'),(17,NULL,'Librarian','web','2025-11-24 19:51:39','2025-11-24 19:51:39'),(18,NULL,'Head-of-Department','web','2025-11-24 19:51:39','2025-11-24 19:51:39'),(19,NULL,'Staff','web','2025-11-24 19:51:39','2025-11-24 19:51:39'),(20,NULL,'Bursar','web','2025-11-24 19:51:39','2025-11-24 19:51:39'),(21,NULL,'Nurse','web','2025-11-24 19:51:39','2025-11-24 19:51:39'),(22,2,'student','web','2025-11-25 19:04:12','2025-11-25 19:04:12'),(23,2,'staff','web','2025-11-26 12:07:27','2025-11-26 12:07:27'),(24,2,'parent','web','2025-11-29 08:42:12','2025-11-29 08:42:12');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rooms`
--

DROP TABLE IF EXISTS `rooms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rooms` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `school_id` bigint unsigned NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `capacity` smallint unsigned DEFAULT NULL,
  `type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `rooms_school_id_name_index` (`school_id`,`name`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rooms`
--

LOCK TABLES `rooms` WRITE;
/*!40000 ALTER TABLE `rooms` DISABLE KEYS */;
INSERT INTO `rooms` VALUES (1,2,'Block A - Room 101','A-101',50,'Classroom',1,'2025-11-26 14:11:14','2025-11-26 14:11:14'),(2,2,'Block A - Room 102','A-102',50,'Classroom',1,'2025-11-26 14:12:07','2025-11-26 14:12:07'),(3,2,'Block B - Room 101','B-101',50,'Classroom',1,'2025-11-26 14:13:42','2025-11-26 14:13:42');
/*!40000 ALTER TABLE `rooms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `salary_scales`
--

DROP TABLE IF EXISTS `salary_scales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `salary_scales` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `grade` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `min_amount` decimal(12,2) DEFAULT NULL,
  `max_amount` decimal(12,2) DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `salary_scales`
--

LOCK TABLES `salary_scales` WRITE;
/*!40000 ALTER TABLE `salary_scales` DISABLE KEYS */;
/*!40000 ALTER TABLE `salary_scales` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `security_audit_logs`
--

DROP TABLE IF EXISTS `security_audit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `security_audit_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `event_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `description` text COLLATE utf8mb4_unicode_ci,
  `metadata` json DEFAULT NULL,
  `severity` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'info',
  `tenant_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `security_audit_logs_user_id_index` (`user_id`),
  KEY `security_audit_logs_event_type_index` (`event_type`),
  KEY `security_audit_logs_created_at_index` (`created_at`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `security_audit_logs`
--

LOCK TABLES `security_audit_logs` WRITE;
/*!40000 ALTER TABLE `security_audit_logs` DISABLE KEYS */;
INSERT INTO `security_audit_logs` VALUES (1,'password_reset','jamesmenya@example.com',5,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0','Password reset by admin: Requested by employee','{\"reset_by\": 2}','warning','2','2025-11-23 15:00:04','2025-11-23 15:00:04');
/*!40000 ALTER TABLE `security_audit_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `settings_key_unique` (`key`)
) ENGINE=MyISAM AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` VALUES (1,'app_name','SMATCAMPUS','2025-11-24 10:21:57','2025-11-24 10:21:57'),(2,'timezone','UTC','2025-11-24 10:21:57','2025-11-24 10:21:57'),(3,'date_format','d/m/Y','2025-11-24 10:21:57','2025-11-27 04:48:00'),(4,'time_format','g:i A','2025-11-24 10:21:57','2025-11-27 04:48:00'),(5,'default_language','en','2025-11-24 10:21:57','2025-11-24 10:21:57'),(6,'records_per_page','25','2025-11-24 10:21:57','2025-11-27 04:48:00'),(7,'bookstore_enabled','1','2025-11-24 10:21:57','2025-11-24 11:28:22'),(8,'school_name','School Management System','2025-11-27 05:05:28','2025-11-27 05:05:28'),(9,'website_title',NULL,'2025-11-27 05:05:28','2025-11-27 05:05:28'),(10,'school_code','SCH001','2025-11-27 05:05:28','2025-11-27 05:05:28'),(11,'school_email','info@school.com','2025-11-27 05:05:28','2025-11-27 05:05:28'),(12,'school_phone','+1-234-567-8900','2025-11-27 05:05:28','2025-11-27 05:05:28'),(13,'school_address','123 Education Street, Learning City, State 12345','2025-11-27 05:05:28','2025-11-27 05:05:28'),(14,'school_website','https://www.school.com','2025-11-27 05:05:28','2025-11-27 05:05:28'),(15,'school_logo','logos/4DlS3XoKfWSOUftcvsQa9zflU1mucHXdLVCXinfz.png','2025-11-27 05:05:28','2025-11-27 05:05:28'),(16,'principal_name','Dr. Jane Smith','2025-11-27 05:05:28','2025-11-27 05:05:28'),(17,'school_type','private','2025-11-27 05:05:28','2025-11-27 05:05:28'),(18,'school_category','day','2025-11-27 05:05:28','2025-11-27 05:05:28'),(19,'gender_type','mixed','2025-11-27 05:05:28','2025-11-27 05:05:28'),(20,'report_card_show_logo','1','2025-11-27 05:42:14','2025-11-27 05:42:14'),(21,'report_card_school_name','Victoria Nile School','2025-11-27 05:42:14','2025-11-27 05:42:14'),(22,'report_card_address','123 Education Street, Learning City, State 12345\r\nTel: +256-755-567-890 | Email: info@victorianileschool.com','2025-11-27 05:42:14','2025-11-27 06:21:30'),(23,'report_card_color_theme','#0066cc','2025-11-27 05:42:14','2025-11-27 05:42:14'),(24,'report_card_template','default','2025-11-27 05:42:14','2025-11-27 05:42:14'),(25,'report_card_signature_1','Class Teacher','2025-11-27 05:42:14','2025-11-27 05:42:14'),(26,'report_card_signature_2','Principal','2025-11-27 05:42:14','2025-11-27 05:42:14'),(27,'report_card_signature_3','Parent/Guardian','2025-11-27 05:42:14','2025-11-27 05:42:14'),(28,'report_card_font_family','Quicksand','2025-11-27 05:42:14','2025-11-27 06:21:30'),(29,'report_card_font_size','11','2025-11-27 05:42:14','2025-11-27 05:42:14'),(30,'report_card_heading_font_weight','bold','2025-11-27 05:42:14','2025-11-27 05:42:14'),(31,'report_card_logo_width','250','2025-11-27 06:21:30','2025-11-27 06:21:30'),(32,'report_card_logo_height','125','2025-11-27 06:21:30','2025-11-27 06:21:30'),(33,'report_card_photo_width','80','2025-11-27 06:21:30','2025-11-27 06:21:30'),(34,'report_card_photo_height','80','2025-11-27 06:21:30','2025-11-27 06:21:30'),(35,'report_card_assessments','[\"MOT\",\"EOT\"]','2025-11-27 08:43:39','2025-11-27 08:44:23');
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `staff_attendance`
--

DROP TABLE IF EXISTS `staff_attendance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `staff_attendance` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `school_id` bigint unsigned NOT NULL,
  `staff_id` bigint unsigned NOT NULL,
  `attendance_date` date NOT NULL,
  `status` enum('present','absent','late','half_day','on_leave','sick_leave','official_duty') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'present',
  `check_in` time DEFAULT NULL,
  `check_out` time DEFAULT NULL,
  `minutes_late` int NOT NULL DEFAULT '0',
  `hours_worked` decimal(5,2) NOT NULL DEFAULT '0.00',
  `leave_reason` text COLLATE utf8mb4_unicode_ci,
  `leave_document` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approved` tinyint(1) NOT NULL DEFAULT '0',
  `approved_by` bigint unsigned DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `staff_attendance_staff_id_attendance_date_unique` (`staff_id`,`attendance_date`),
  KEY `staff_attendance_approved_by_foreign` (`approved_by`),
  KEY `staff_attendance_school_id_index` (`school_id`),
  KEY `staff_attendance_staff_id_index` (`staff_id`),
  KEY `staff_attendance_attendance_date_index` (`attendance_date`),
  KEY `staff_attendance_status_index` (`status`),
  KEY `staff_attendance_school_id_attendance_date_index` (`school_id`,`attendance_date`),
  KEY `staff_attendance_staff_id_attendance_date_index` (`staff_id`,`attendance_date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `staff_attendance`
--

LOCK TABLES `staff_attendance` WRITE;
/*!40000 ALTER TABLE `staff_attendance` DISABLE KEYS */;
/*!40000 ALTER TABLE `staff_attendance` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `student_behaviours`
--

DROP TABLE IF EXISTS `student_behaviours`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `student_behaviours` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint unsigned NOT NULL,
  `reporter_id` bigint unsigned NOT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `points` int NOT NULL DEFAULT '0',
  `incident_date` date NOT NULL,
  `action_taken` text COLLATE utf8mb4_unicode_ci,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'recorded',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `student_behaviours_student_id_foreign` (`student_id`),
  KEY `student_behaviours_reporter_id_foreign` (`reporter_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `student_behaviours`
--

LOCK TABLES `student_behaviours` WRITE;
/*!40000 ALTER TABLE `student_behaviours` DISABLE KEYS */;
/*!40000 ALTER TABLE `student_behaviours` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `student_notes`
--

DROP TABLE IF EXISTS `student_notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `student_notes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint unsigned NOT NULL,
  `subject_id` bigint unsigned DEFAULT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` longtext COLLATE utf8mb4_unicode_ci,
  `tags` json DEFAULT NULL,
  `is_favorite` tinyint(1) NOT NULL DEFAULT '0',
  `color` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `student_notes_subject_id_foreign` (`subject_id`),
  KEY `student_notes_student_id_subject_id_index` (`student_id`,`subject_id`),
  KEY `student_notes_is_favorite_index` (`is_favorite`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `student_notes`
--

LOCK TABLES `student_notes` WRITE;
/*!40000 ALTER TABLE `student_notes` DISABLE KEYS */;
/*!40000 ALTER TABLE `student_notes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `student_subject`
--

DROP TABLE IF EXISTS `student_subject`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `student_subject` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint unsigned NOT NULL,
  `subject_id` bigint unsigned NOT NULL,
  `academic_year` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_core` tinyint(1) NOT NULL DEFAULT '1',
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `student_subject_student_id_subject_id_academic_year_unique` (`student_id`,`subject_id`,`academic_year`),
  KEY `student_subject_subject_id_foreign` (`subject_id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `student_subject`
--

LOCK TABLES `student_subject` WRITE;
/*!40000 ALTER TABLE `student_subject` DISABLE KEYS */;
INSERT INTO `student_subject` VALUES (1,4,6,NULL,1,'active','2025-11-26 10:16:01','2025-11-26 10:16:01'),(2,4,2,NULL,1,'active','2025-11-26 10:16:01','2025-11-26 10:16:01'),(3,4,3,NULL,1,'active','2025-11-26 10:16:01','2025-11-26 10:16:01'),(4,4,1,NULL,1,'active','2025-11-26 10:16:01','2025-11-26 10:16:01'),(5,5,6,NULL,1,'active','2025-11-26 10:23:29','2025-11-26 10:23:29'),(6,5,2,NULL,1,'active','2025-11-26 10:23:29','2025-11-26 10:23:29'),(7,5,3,NULL,1,'active','2025-11-26 10:23:29','2025-11-26 10:23:29'),(8,5,1,NULL,1,'active','2025-11-26 10:23:29','2025-11-26 10:23:29'),(9,5,7,NULL,1,'active','2025-11-26 10:23:29','2025-11-26 10:23:29'),(10,6,6,NULL,1,'active','2025-11-26 11:03:57','2025-11-26 11:03:57'),(11,6,2,NULL,1,'active','2025-11-26 11:03:57','2025-11-26 11:03:57'),(12,6,3,NULL,1,'active','2025-11-26 11:03:57','2025-11-26 11:03:57'),(13,6,1,NULL,1,'active','2025-11-26 11:03:57','2025-11-26 11:03:57'),(14,6,7,NULL,1,'active','2025-11-26 11:03:57','2025-11-26 11:03:57');
/*!40000 ALTER TABLE `student_subject` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `students`
--

DROP TABLE IF EXISTS `students`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `students` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `first_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `admission_no` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `gender` enum('male','female','other') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `national_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `profile_photo` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `blood_group` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `city` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `postal_code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Kenya',
  `class_id` bigint unsigned DEFAULT NULL,
  `class_stream_id` bigint unsigned DEFAULT NULL,
  `roll_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `section` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `admission_date` date DEFAULT NULL,
  `status` enum('active','inactive','graduated','transferred','expelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `father_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `father_phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `father_occupation` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `father_email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mother_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mother_phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mother_occupation` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mother_email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `guardian_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `guardian_phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `guardian_relation` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `guardian_email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `emergency_contact_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `emergency_contact_phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `emergency_contact_relation` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `medical_conditions` text COLLATE utf8mb4_unicode_ci,
  `allergies` text COLLATE utf8mb4_unicode_ci,
  `medications` text COLLATE utf8mb4_unicode_ci,
  `previous_school` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `previous_class` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transfer_reason` text COLLATE utf8mb4_unicode_ci,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `has_special_needs` tinyint(1) NOT NULL DEFAULT '0',
  `special_needs_description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `students_admission_no_unique` (`admission_no`),
  UNIQUE KEY `students_email_unique` (`email`),
  KEY `students_class_id_foreign` (`class_id`),
  KEY `students_class_stream_id_foreign` (`class_stream_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `students`
--

LOCK TABLES `students` WRITE;
/*!40000 ALTER TABLE `students` DISABLE KEYS */;
INSERT INTO `students` VALUES (1,'John Kirya','John','Kirya','ADM-2025-001','johnkirya@example.com','2025-02-02','male',NULL,'students/profiles/EXSkyvkKofMp1QTbaGsWs7uyM4QCVxUlrvoFq9cO.jpg','O+',NULL,NULL,NULL,NULL,NULL,'Uganda',1,NULL,NULL,NULL,'2025-11-25','active','Mukoova Davis',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,'2025-11-25 17:46:10','2025-11-25 17:46:10',NULL),(2,'Newton Ojok','Newton','Ojok','ADM-2025-002','newtonojok@example.com','2025-03-05','male','CM2000HAYF0000','students/profiles/w58cc9fG5OW3Q55I0Z4kCaYI1sxKcmpstWbx5sYB.jpg','B+','+256750000000',NULL,NULL,NULL,'00256','Uganda',1,NULL,'002','Blue','2025-03-06','active','Ojok Davis','+256750000111',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Ojok Davis','+256750000111','Father',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,'He is deaf in one ear','2025-11-26 07:26:40','2025-11-26 07:26:40',NULL),(3,'Sam Daka','Sam','Daka','ADM-2025-005','samdaka@example.com','2025-02-04','male','12465873',NULL,NULL,'+256700000001','','','','','',1,1,'004','Blue','2025-02-26','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'','',NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,'2025-11-26 09:10:13','2025-11-26 10:00:33',NULL),(4,'Godwin Maje','Godwin','Maje','ADM-2025-006','godwinmaje@example.com','1995-03-02','male','123456888','students/profiles/qiGvoOQ2ihei4EoDI7RoU9VpJaJsVw5sMvVyDP0V.jpg','B-','+256700000002','','','','','',1,1,'006','Blue','2025-11-26','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'','',NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,'2025-11-26 10:04:17','2025-11-26 10:07:12',NULL),(5,'Abdul Rasul','Abdul','Rasul','ADM-2025-007','abdulrasul@example.com','1994-03-06','male','123123123','students/profiles/Q5w87HqVsXe32E7chSekKgnalroOBoT7KWUl3tVd.jpg','A+','+256700000003','','','','','',1,1,'007','Blue','2025-03-26','active','Juma Rasul',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'','',NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,'2025-11-26 10:20:20','2025-11-26 10:22:57',NULL),(6,'Fred Musoke','Fred','Musoke','STU-20251126-0014','fredmusoke@example.com',NULL,NULL,NULL,NULL,NULL,'','',NULL,NULL,NULL,'Kenya',1,2,NULL,NULL,'2025-11-26','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'','',NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,'2025-11-26 11:02:53','2025-11-26 11:03:57',NULL);
/*!40000 ALTER TABLE `students` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subject_teacher`
--

DROP TABLE IF EXISTS `subject_teacher`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `subject_teacher` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `subject_id` bigint unsigned NOT NULL,
  `teacher_id` bigint unsigned NOT NULL,
  `class_id` bigint unsigned DEFAULT NULL,
  `academic_year` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `subject_teacher_unique` (`subject_id`,`teacher_id`,`class_id`,`academic_year`),
  KEY `subject_teacher_teacher_id_foreign` (`teacher_id`),
  KEY `subject_teacher_class_id_foreign` (`class_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subject_teacher`
--

LOCK TABLES `subject_teacher` WRITE;
/*!40000 ALTER TABLE `subject_teacher` DISABLE KEYS */;
/*!40000 ALTER TABLE `subject_teacher` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subjects`
--

DROP TABLE IF EXISTS `subjects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `subjects` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `school_id` bigint unsigned NOT NULL,
  `education_level_id` bigint unsigned DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `type` enum('core','elective','optional') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'core',
  `credit_hours` int DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int NOT NULL DEFAULT '0',
  `pass_mark` int NOT NULL DEFAULT '40',
  `max_marks` int NOT NULL DEFAULT '100',
  `required_periods_per_week` tinyint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_school_code` (`school_id`,`code`),
  KEY `subjects_school_id_index` (`school_id`),
  KEY `subjects_education_level_id_index` (`education_level_id`),
  KEY `subjects_school_id_is_active_index` (`school_id`,`is_active`),
  KEY `subjects_school_id_education_level_id_index` (`school_id`,`education_level_id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subjects`
--

LOCK TABLES `subjects` WRITE;
/*!40000 ALTER TABLE `subjects` DISABLE KEYS */;
INSERT INTO `subjects` VALUES (1,2,2,'Mathematics','MATH101',NULL,'core',60,1,1,60,100,NULL,'2025-11-23 05:21:16','2025-11-23 05:21:16'),(2,2,2,'English','ENG102',NULL,'core',60,1,2,70,100,NULL,'2025-11-23 05:23:26','2025-11-23 05:23:26'),(3,2,2,'Literacy (including local language at early stages)','LIT103',NULL,'core',60,1,0,70,100,NULL,'2025-11-23 05:35:29','2025-11-23 05:35:29'),(4,2,2,'Science (Integrated Science)','SCI104',NULL,'core',70,1,4,70,100,NULL,'2025-11-23 05:36:49','2025-11-23 05:36:49'),(5,2,2,'Social Studies (including Citizenship Education)','SST101',NULL,'core',60,1,0,70,100,NULL,'2025-11-23 05:37:32','2025-11-23 05:37:32'),(6,2,2,'Creative Performing Arts (Music, Art and Crafts)','CPA',NULL,'elective',60,1,0,70,100,NULL,'2025-11-23 05:39:08','2025-11-23 05:39:08'),(7,2,2,'Physical Education','PE101',NULL,'core',60,1,6,70,100,NULL,'2025-11-23 05:40:08','2025-11-23 05:40:08'),(8,2,2,'Religious Education','RE101',NULL,'core',60,1,7,70,100,NULL,'2025-11-23 05:41:15','2025-11-23 05:41:15');
/*!40000 ALTER TABLE `subjects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `teacher_availabilities`
--

DROP TABLE IF EXISTS `teacher_availabilities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `teacher_availabilities` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `school_id` bigint unsigned NOT NULL,
  `teacher_id` bigint unsigned NOT NULL,
  `day_of_week` tinyint unsigned NOT NULL,
  `available_start` time NOT NULL,
  `available_end` time NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `teacher_availabilities_school_id_teacher_id_day_of_week_index` (`school_id`,`teacher_id`,`day_of_week`),
  KEY `teacher_availabilities_teacher_id_foreign` (`teacher_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `teacher_availabilities`
--

LOCK TABLES `teacher_availabilities` WRITE;
/*!40000 ALTER TABLE `teacher_availabilities` DISABLE KEYS */;
/*!40000 ALTER TABLE `teacher_availabilities` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `teachers`
--

DROP TABLE IF EXISTS `teachers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `teachers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `first_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gender` enum('male','female','other') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `national_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `profile_photo` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `city` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `postal_code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Kenya',
  `employee_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `employee_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `employee_record_id` bigint unsigned DEFAULT NULL,
  `qualification` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `specialization` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `experience_years` int DEFAULT NULL,
  `joining_date` date DEFAULT NULL,
  `employment_type` enum('full_time','part_time','contract','visiting') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'full_time',
  `status` enum('active','on_leave','resigned','terminated') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `emergency_contact_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `emergency_contact_phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `emergency_contact_relation` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `blood_group` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `medical_conditions` text COLLATE utf8mb4_unicode_ci,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `teachers_email_unique` (`email`),
  UNIQUE KEY `teachers_national_id_unique` (`national_id`),
  UNIQUE KEY `teachers_employee_id_unique` (`employee_id`),
  KEY `teachers_employee_record_id_foreign` (`employee_record_id`),
  KEY `teachers_user_id_foreign` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `teachers`
--

LOCK TABLES `teachers` WRITE;
/*!40000 ALTER TABLE `teachers` DISABLE KEYS */;
INSERT INTO `teachers` VALUES (1,15,'Rose Gago','Rose','Gago','female','1990-12-22','978654333',NULL,'rosegago@example.com','+256709111222',NULL,NULL,NULL,NULL,'Kenya',NULL,'EMP20259020',5,'Bachelors','English',3,'2020-04-26','full_time','active',NULL,NULL,NULL,'O+',NULL,NULL,'2025-11-26 12:00:51','2025-11-26 12:00:51',NULL),(2,NULL,'Sam Mirondo','Sam','Mirondo',NULL,NULL,NULL,NULL,'sammirondo@example.com',NULL,NULL,NULL,NULL,NULL,'Kenya',NULL,'EMP-20251126-0016',6,NULL,NULL,NULL,'2025-11-26','full_time','active',NULL,NULL,NULL,NULL,NULL,NULL,'2025-11-26 12:12:10','2025-11-26 12:12:10',NULL),(3,17,'Doreen Katusabe','Doreen','Katusabe','female','1995-05-14','111222333',NULL,'doreenkatusabe@example.com','+256711000123',NULL,'Jinja','Central',NULL,'Uganda',NULL,'EMP20254373',7,'Bachelors','Physical Education',5,'2019-04-13','full_time','active',NULL,NULL,NULL,'O+',NULL,NULL,'2025-11-26 12:19:25','2025-11-26 12:19:25',NULL),(4,18,'Sam Okello','Sam','Okello','male','1991-06-12','456456456','teachers/profiles/gecFFm7sxPU1kTV7VaezEkfJeCzbjLRvmMa5GK38.jpg','samokello@example.com','+256712000101',NULL,'Jinja','Kimaka',NULL,'Uganda',NULL,'EMP20255590',8,NULL,NULL,NULL,'2025-11-26','full_time','active',NULL,NULL,NULL,'O+',NULL,NULL,'2025-11-26 12:22:00','2025-11-26 12:22:00',NULL),(5,19,'Martha Gudoyi','Martha','Gudoyi','female','2001-01-30','8794256','teachers/profiles/aKnpdlM2a2KSiGIzSEiJ3uAHmCINQKmhyHCIDJyB.jpg','marthagudoyi@example.com','+256704000003',NULL,NULL,NULL,NULL,'Kenya',NULL,'EMP20256785',9,NULL,NULL,NULL,'2025-11-26','full_time','active',NULL,NULL,NULL,'AB-',NULL,NULL,'2025-11-26 12:26:47','2025-11-26 12:26:47',NULL),(6,8,'Jimmy Musisi','Jimmy','Musisi',NULL,NULL,NULL,NULL,'jimmymusisi@example.com','N/A',NULL,NULL,NULL,NULL,'Kenya','EMP-3580','EMP20259392',2,NULL,NULL,NULL,'2025-11-27','full_time','active',NULL,NULL,NULL,NULL,NULL,NULL,'2025-11-27 10:39:08','2025-11-27 10:39:08',NULL);
/*!40000 ALTER TABLE `teachers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tenant_payment_gateway_configs`
--

DROP TABLE IF EXISTS `tenant_payment_gateway_configs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tenant_payment_gateway_configs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `gateway` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  `is_test_mode` tinyint(1) NOT NULL DEFAULT '1',
  `credentials` text COLLATE utf8mb4_unicode_ci,
  `settings` json DEFAULT NULL,
  `supported_currencies` json DEFAULT NULL,
  `display_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tenant_payment_gateway_configs_gateway_unique` (`gateway`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tenant_payment_gateway_configs`
--

LOCK TABLES `tenant_payment_gateway_configs` WRITE;
/*!40000 ALTER TABLE `tenant_payment_gateway_configs` DISABLE KEYS */;
/*!40000 ALTER TABLE `tenant_payment_gateway_configs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tenant_payment_transactions`
--

DROP TABLE IF EXISTS `tenant_payment_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tenant_payment_transactions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `transaction_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'fee',
  `related_id` bigint unsigned NOT NULL,
  `gateway` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `transaction_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reference` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount` decimal(15,2) NOT NULL,
  `currency` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `payer_email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payer_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payer_phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `payment_url` text COLLATE utf8mb4_unicode_ci,
  `raw_request` text COLLATE utf8mb4_unicode_ci,
  `raw_response` text COLLATE utf8mb4_unicode_ci,
  `webhook_data` text COLLATE utf8mb4_unicode_ci,
  `initiated_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `failed_at` timestamp NULL DEFAULT NULL,
  `failure_reason` text COLLATE utf8mb4_unicode_ci,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tenant_payment_transactions_transaction_id_unique` (`transaction_id`),
  KEY `tenant_payment_transactions_transaction_type_related_id_index` (`transaction_type`,`related_id`),
  KEY `tenant_payment_transactions_gateway_index` (`gateway`),
  KEY `tenant_payment_transactions_status_index` (`status`),
  KEY `tenant_payment_transactions_created_at_index` (`created_at`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tenant_payment_transactions`
--

LOCK TABLES `tenant_payment_transactions` WRITE;
/*!40000 ALTER TABLE `tenant_payment_transactions` DISABLE KEYS */;
/*!40000 ALTER TABLE `tenant_payment_transactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `terms`
--

DROP TABLE IF EXISTS `terms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `terms` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `school_id` bigint unsigned NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `academic_year` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_current` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `terms_school_id_name_academic_year_unique` (`school_id`,`name`,`academic_year`),
  KEY `terms_school_id_index` (`school_id`),
  KEY `terms_school_id_is_current_index` (`school_id`,`is_current`),
  KEY `terms_school_id_academic_year_index` (`school_id`,`academic_year`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `terms`
--

LOCK TABLES `terms` WRITE;
/*!40000 ALTER TABLE `terms` DISABLE KEYS */;
INSERT INTO `terms` VALUES (1,2,'Term 1','T1','2025','2025-02-02','2025-04-04',NULL,0,1,'2025-11-23 05:43:57','2025-11-23 05:45:33'),(2,2,'Term 2','T2','2025','2025-05-04','2025-08-08',NULL,0,1,'2025-11-23 05:44:59','2025-11-23 05:45:33'),(3,2,'Term 3','T3','2025','2025-09-04','2025-11-08',NULL,1,1,'2025-11-23 05:45:33','2025-11-23 05:45:33');
/*!40000 ALTER TABLE `terms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `timetable_constraints`
--

DROP TABLE IF EXISTS `timetable_constraints`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `timetable_constraints` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `constraints` json NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `timetable_constraints_type_is_active_index` (`type`,`is_active`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `timetable_constraints`
--

LOCK TABLES `timetable_constraints` WRITE;
/*!40000 ALTER TABLE `timetable_constraints` DISABLE KEYS */;
/*!40000 ALTER TABLE `timetable_constraints` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `timetable_entries`
--

DROP TABLE IF EXISTS `timetable_entries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `timetable_entries` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `school_id` bigint unsigned NOT NULL,
  `class_id` bigint unsigned NOT NULL,
  `class_stream_id` bigint unsigned DEFAULT NULL,
  `subject_id` bigint unsigned NOT NULL,
  `teacher_id` bigint unsigned DEFAULT NULL,
  `room_id` bigint unsigned DEFAULT NULL,
  `day_of_week` tinyint unsigned NOT NULL,
  `starts_at` time NOT NULL,
  `ends_at` time NOT NULL,
  `room` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `timetable_entries_school_id_index` (`school_id`),
  KEY `timetable_entries_school_id_class_id_index` (`school_id`,`class_id`),
  KEY `timetable_entries_school_id_day_of_week_index` (`school_id`,`day_of_week`),
  KEY `timetable_entries_school_id_teacher_id_index` (`school_id`,`teacher_id`),
  KEY `timetable_entries_class_id_day_of_week_starts_at_index` (`class_id`,`day_of_week`,`starts_at`),
  KEY `timetable_entries_class_stream_id_foreign` (`class_stream_id`),
  KEY `timetable_entries_subject_id_foreign` (`subject_id`),
  KEY `timetable_entries_school_id_room_id_index` (`school_id`,`room_id`),
  KEY `timetable_entries_room_id_foreign` (`room_id`),
  KEY `timetable_entries_teacher_id_foreign` (`teacher_id`)
) ENGINE=MyISAM AUTO_INCREMENT=61 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `timetable_entries`
--

LOCK TABLES `timetable_entries` WRITE;
/*!40000 ALTER TABLE `timetable_entries` DISABLE KEYS */;
INSERT INTO `timetable_entries` VALUES (1,2,1,NULL,1,8,NULL,1,'08:00:00','08:40:00',NULL,NULL,'2025-11-26 13:00:45','2025-11-26 13:00:45'),(2,2,1,NULL,1,8,NULL,1,'08:55:00','09:35:00',NULL,NULL,'2025-11-26 13:00:45','2025-11-26 13:00:45'),(3,2,1,NULL,6,19,NULL,1,'09:50:00','10:30:00',NULL,NULL,'2025-11-26 13:00:45','2025-11-26 13:00:45'),(4,2,1,NULL,3,16,NULL,1,'10:45:00','11:25:00',NULL,NULL,'2025-11-26 13:00:45','2025-11-26 13:00:45'),(5,2,1,NULL,7,18,NULL,1,'11:40:00','12:20:00',NULL,NULL,'2025-11-26 13:00:45','2025-11-26 13:00:45'),(6,2,1,NULL,2,15,NULL,1,'12:35:00','13:15:00',NULL,NULL,'2025-11-26 13:00:45','2025-11-26 13:00:45'),(7,2,1,NULL,1,8,NULL,1,'13:30:00','14:10:00',NULL,NULL,'2025-11-26 13:00:45','2025-11-26 13:00:45'),(8,2,1,NULL,3,16,NULL,1,'14:25:00','15:05:00',NULL,NULL,'2025-11-26 13:00:45','2025-11-26 13:00:45'),(9,2,1,NULL,6,19,NULL,2,'08:00:00','08:40:00',NULL,NULL,'2025-11-26 13:00:45','2025-11-26 13:00:45'),(10,2,1,NULL,3,16,NULL,2,'08:55:00','09:35:00',NULL,NULL,'2025-11-26 13:00:45','2025-11-26 13:00:45'),(11,2,1,NULL,7,18,NULL,2,'09:50:00','10:30:00',NULL,NULL,'2025-11-26 13:00:45','2025-11-26 13:00:45'),(12,2,1,NULL,6,19,NULL,2,'10:45:00','11:25:00',NULL,NULL,'2025-11-26 13:00:45','2025-11-26 13:00:45'),(13,2,1,NULL,2,15,NULL,2,'11:40:00','12:20:00',NULL,NULL,'2025-11-26 13:00:45','2025-11-26 13:00:45'),(14,2,1,NULL,7,18,NULL,2,'12:35:00','13:15:00',NULL,NULL,'2025-11-26 13:00:45','2025-11-26 13:00:45'),(15,2,1,NULL,7,18,NULL,2,'13:30:00','14:10:00',NULL,NULL,'2025-11-26 13:00:45','2025-11-26 13:00:45'),(16,2,1,NULL,2,15,NULL,2,'14:25:00','15:05:00',NULL,NULL,'2025-11-26 13:00:45','2025-11-26 13:00:45'),(17,2,1,NULL,2,15,NULL,3,'08:00:00','08:40:00',NULL,NULL,'2025-11-26 13:00:45','2025-11-26 13:00:45'),(18,2,1,NULL,1,8,NULL,3,'08:55:00','09:35:00',NULL,NULL,'2025-11-26 13:00:45','2025-11-26 13:00:45'),(19,2,1,NULL,6,19,NULL,3,'09:50:00','10:30:00',NULL,NULL,'2025-11-26 13:00:45','2025-11-26 13:00:45'),(20,2,1,NULL,3,16,NULL,3,'10:45:00','11:25:00',NULL,NULL,'2025-11-26 13:00:45','2025-11-26 13:00:45'),(21,2,1,1,3,16,NULL,1,'08:00:00','08:40:00',NULL,NULL,'2025-11-26 13:53:28','2025-11-26 13:53:28'),(22,2,1,1,7,18,NULL,2,'08:00:00','08:40:00',NULL,NULL,'2025-11-26 13:53:28','2025-11-26 13:53:28'),(23,2,1,1,2,15,NULL,4,'08:00:00','08:40:00',NULL,NULL,'2025-11-26 13:53:28','2025-11-26 13:53:28'),(24,2,1,1,1,8,NULL,3,'08:00:00','08:40:00',NULL,NULL,'2025-11-26 13:53:28','2025-11-26 13:53:28'),(25,2,1,1,6,19,NULL,5,'08:00:00','08:40:00',NULL,NULL,'2025-11-26 13:53:28','2025-11-26 13:53:28'),(26,2,1,1,3,16,NULL,1,'08:55:00','09:35:00',NULL,NULL,'2025-11-26 13:53:28','2025-11-26 13:53:28'),(27,2,1,1,1,8,NULL,2,'08:55:00','09:35:00',NULL,NULL,'2025-11-26 13:53:28','2025-11-26 13:53:28'),(28,2,1,1,1,8,NULL,4,'08:55:00','09:35:00',NULL,NULL,'2025-11-26 13:53:28','2025-11-26 13:53:28'),(29,2,1,1,6,19,NULL,3,'08:55:00','09:35:00',NULL,NULL,'2025-11-26 13:53:28','2025-11-26 13:53:28'),(30,2,1,1,7,18,NULL,5,'08:55:00','09:35:00',NULL,NULL,'2025-11-26 13:53:28','2025-11-26 13:53:28'),(31,2,1,1,3,16,NULL,1,'09:50:00','10:30:00',NULL,NULL,'2025-11-26 13:53:28','2025-11-26 13:53:28'),(32,2,1,1,2,15,NULL,2,'09:50:00','10:30:00',NULL,NULL,'2025-11-26 13:53:28','2025-11-26 13:53:28'),(33,2,1,1,2,15,NULL,3,'09:50:00','10:30:00',NULL,NULL,'2025-11-26 13:53:28','2025-11-26 13:53:28'),(34,2,1,1,6,19,NULL,4,'09:50:00','10:30:00',NULL,NULL,'2025-11-26 13:53:28','2025-11-26 13:53:28'),(35,2,1,1,7,18,NULL,5,'09:50:00','10:30:00',NULL,NULL,'2025-11-26 13:53:28','2025-11-26 13:53:28'),(36,2,1,1,3,16,NULL,2,'10:45:00','11:25:00',NULL,NULL,'2025-11-26 13:53:28','2025-11-26 13:53:28'),(37,2,1,1,6,19,NULL,1,'10:45:00','11:25:00',NULL,NULL,'2025-11-26 13:53:28','2025-11-26 13:53:28'),(38,2,1,1,1,8,NULL,3,'10:45:00','11:25:00',NULL,NULL,'2025-11-26 13:53:28','2025-11-26 13:53:28'),(39,2,1,1,7,18,NULL,4,'10:45:00','11:25:00',NULL,NULL,'2025-11-26 13:53:28','2025-11-26 13:53:28'),(40,2,1,1,2,15,NULL,5,'10:45:00','11:25:00',NULL,NULL,'2025-11-26 13:53:28','2025-11-26 13:53:28'),(41,2,1,2,2,15,NULL,1,'08:00:00','08:40:00',NULL,NULL,'2025-11-26 13:53:28','2025-11-26 13:53:28'),(42,2,1,2,2,15,NULL,2,'08:00:00','08:40:00',NULL,NULL,'2025-11-26 13:53:28','2025-11-26 13:53:28'),(43,2,1,2,6,19,NULL,3,'08:00:00','08:40:00',NULL,NULL,'2025-11-26 13:53:28','2025-11-26 13:53:28'),(44,2,1,2,7,18,NULL,4,'08:00:00','08:40:00',NULL,NULL,'2025-11-26 13:53:28','2025-11-26 13:53:28'),(45,2,1,2,2,15,NULL,5,'08:00:00','08:40:00',NULL,NULL,'2025-11-26 13:53:28','2025-11-26 13:53:28'),(46,2,1,2,3,16,NULL,3,'08:55:00','09:35:00',NULL,NULL,'2025-11-26 13:53:28','2025-11-26 13:53:28'),(47,2,1,2,2,15,NULL,1,'08:55:00','09:35:00',NULL,NULL,'2025-11-26 13:53:28','2025-11-26 13:53:28'),(48,2,1,2,1,8,NULL,5,'08:55:00','09:35:00',NULL,NULL,'2025-11-26 13:53:28','2025-11-26 13:53:28'),(49,2,1,2,6,19,NULL,2,'08:55:00','09:35:00',NULL,NULL,'2025-11-26 13:53:28','2025-11-26 13:53:28'),(50,2,1,2,7,18,NULL,4,'08:55:00','09:35:00',NULL,NULL,'2025-11-26 13:53:28','2025-11-26 13:53:28'),(51,2,1,2,7,18,NULL,1,'09:50:00','10:30:00',NULL,NULL,'2025-11-26 13:53:28','2025-11-26 13:53:28'),(52,2,1,2,6,19,NULL,2,'09:50:00','10:30:00',NULL,NULL,'2025-11-26 13:53:28','2025-11-26 13:53:28'),(53,2,1,2,6,19,NULL,5,'09:50:00','10:30:00',NULL,NULL,'2025-11-26 13:53:28','2025-11-26 13:53:28'),(54,2,1,2,1,8,NULL,3,'09:50:00','10:30:00',NULL,NULL,'2025-11-26 13:53:28','2025-11-26 13:53:28'),(55,2,1,2,3,16,NULL,4,'09:50:00','10:30:00',NULL,NULL,'2025-11-26 13:53:28','2025-11-26 13:53:28'),(56,2,1,2,1,8,NULL,1,'10:45:00','11:25:00',NULL,NULL,'2025-11-26 13:53:28','2025-11-26 13:53:28'),(57,2,1,2,3,16,NULL,4,'10:45:00','11:25:00',NULL,NULL,'2025-11-26 13:53:28','2025-11-26 13:53:28'),(58,2,1,2,3,16,NULL,5,'10:45:00','11:25:00',NULL,NULL,'2025-11-26 13:53:28','2025-11-26 13:53:28'),(59,2,1,2,1,8,NULL,2,'10:45:00','11:25:00',NULL,NULL,'2025-11-26 13:53:28','2025-11-26 13:53:28'),(60,2,1,2,7,18,NULL,3,'10:45:00','11:25:00',NULL,NULL,'2025-11-26 13:53:28','2025-11-26 13:53:28');
/*!40000 ALTER TABLE `timetable_entries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transactions`
--

DROP TABLE IF EXISTS `transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `transactions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `school_id` bigint unsigned NOT NULL,
  `transaction_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `description` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category_id` bigint unsigned DEFAULT NULL,
  `payment_method` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `transaction_date` date NOT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'completed',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `transactions_category_id_foreign` (`category_id`),
  KEY `transactions_created_by_foreign` (`created_by`),
  KEY `transactions_school_id_transaction_date_index` (`school_id`,`transaction_date`),
  KEY `transactions_school_id_transaction_type_index` (`school_id`,`transaction_type`),
  KEY `transactions_status_index` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transactions`
--

LOCK TABLES `transactions` WRITE;
/*!40000 ALTER TABLE `transactions` DISABLE KEYS */;
/*!40000 ALTER TABLE `transactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_preferences`
--

DROP TABLE IF EXISTS `user_preferences`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_preferences` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `email_notifications` tinyint(1) NOT NULL DEFAULT '1',
  `sms_notifications` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_preferences_user_id_foreign` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_preferences`
--

LOCK TABLES `user_preferences` WRITE;
/*!40000 ALTER TABLE `user_preferences` DISABLE KEYS */;
INSERT INTO `user_preferences` VALUES (1,17,1,1,'2025-11-27 10:07:49','2025-11-27 10:07:49'),(2,8,1,1,'2025-11-28 16:24:27','2025-11-28 16:24:27');
/*!40000 ALTER TABLE `user_preferences` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `school_id` bigint unsigned DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gender` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `emergency_contact_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `emergency_contact_phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qualification` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `specialization` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `profile_photo` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general_staff',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `approval_status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'approved',
  `approved_by` bigint unsigned DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `rejection_reason` text COLLATE utf8mb4_unicode_ci,
  `registration_data` json DEFAULT NULL,
  `suspension_reason` text COLLATE utf8mb4_unicode_ci,
  `suspended_at` timestamp NULL DEFAULT NULL,
  `suspended_by` bigint unsigned DEFAULT NULL,
  `expelled_at` timestamp NULL DEFAULT NULL,
  `expulsion_reason` text COLLATE utf8mb4_unicode_ci,
  `expelled_by` bigint unsigned DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_school_id_index` (`school_id`),
  KEY `users_approved_by_foreign` (`approved_by`),
  KEY `users_suspended_by_foreign` (`suspended_by`),
  KEY `users_expelled_by_foreign` (`expelled_by`),
  KEY `users_approval_status_index` (`approval_status`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,2,'Victoria Nile Admin',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'admin@victorianileschool.com',NULL,'admin',1,'2025-11-22 08:44:25','$2y$12$hLxJSgwOXBVMSuyxSj3YI.QzyDau724Q8UytrTmWgzEUFBYjHmXbG','approved',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-11-22 08:44:25','2025-11-22 08:48:37'),(2,2,'Victoria Nile User',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'victorianileschool@example.com',NULL,'admin',1,NULL,'$2y$12$AFBjRwhyMC5TusUZaKqunu1Pp/f4G50oZuOk0OeSEQ5NNU5PT7aOi','approved',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'xO6koMEalIXlw0OcRAhvdBy8hDnjolvXGTzNX0SNNINwdujwOHe7njsNb1En','2025-11-22 09:14:30','2025-11-22 09:14:30'),(3,NULL,'Ahmed Mwondha',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'ahmedmwondha@example.com',NULL,'general_staff',1,NULL,'$2y$12$ZUMnZmCz7qmUK/bo6E2GBuUDzU7fZ7pV5QLNfJEQPCz8twnNVryxi','rejected',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-11-23 05:47:30','2025-11-26 12:10:37'),(4,NULL,'Francis Mukobi',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'frankhostltd3@gmail.com',NULL,'general_staff',1,NULL,'$2y$12$ER5RW1gMjDlI6iZzalhnoez1lFGGtizUrM0.wZ5tTPA2ZaV.yHYWa','rejected',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-11-23 05:48:42','2025-11-26 12:10:53'),(5,NULL,'James Menya',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'jamesmenya@example.com',NULL,'general_staff',1,NULL,'$2y$12$TZg.tu73LwqGW/wXntimZuSI.fD4vZazawp/aYh3XRTXXzvW6OwZq','approved',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-11-23 14:46:58','2025-11-23 15:00:04'),(6,2,'Hillary Okello',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'hillaryokello@example.com',NULL,'general_staff',1,NULL,'$2y$12$7sjHLQC3XqzunadflguOQ.Y3MlwwkNrdgtas2pden/oDtPgWqXcNK','approved',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-11-24 15:29:47','2025-11-24 15:31:47'),(7,2,'Raph Muge',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'raphmuge@example.com',NULL,'general_staff',1,NULL,'$2y$12$d7O1lvHRGmok.PTBbc2n9ejewMGeL1Wm4VNEzweC7yaz2qH6WpMra','approved',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-11-24 16:39:06','2025-11-24 16:41:12'),(8,2,'Jimmy Musisi',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'jimmymusisi@example.com',NULL,'teaching_staff',1,NULL,'$2y$12$pKGRJayF6pbR/LAPkdfWreNKSRW/zDVVge3Al/DCOIBcSSmDjBL46','approved',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'7iPCESdKFbcmWhisdfJ2TfG7fAiaeVTZaWJa82hx00NhSYOFo1Ep1Uce649p','2025-11-24 17:07:58','2025-11-25 12:34:18'),(9,2,'Marvin Muti',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'marvinmuti@example.com',NULL,'general_staff',1,NULL,'$2y$12$go9dD84y7uiO9nt.cyV1AOsu5IVYTXaVfcWk8rBruCSSBEnaTTYH6','approved',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-11-25 19:04:12','2025-11-25 19:10:53'),(10,2,'Ibrahim Gowon',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'ibrahimgowon@example.com',NULL,'student',1,NULL,'$2y$12$DYeLh3LAwxxR6bW9HGD3teRpcHv3vi7TG82FHOwvhr1SBtDag/ivu','approved',2,'2025-11-26 08:45:59',NULL,'{\"source\": \"tenant_student_module\", \"submitted_at\": \"2025-11-26T11:07:54+00:00\", \"submitted_by\": 2}',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-11-26 08:07:54','2025-11-26 08:45:59'),(11,2,'Sam Daka',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'samdaka@example.com',NULL,'student',0,NULL,'$2y$12$qEZih2M5RO.q9gbO7s9QYuFtCC0OqrJt/zY7ULzDzIY.jJHK30wei','approved',NULL,NULL,NULL,'{\"source\": \"tenant_student_module\", \"submitted_at\": \"2025-11-26T12:10:13+00:00\", \"submitted_by\": 2, \"student_profile\": {\"dob\": \"2025-02-04\", \"gender\": \"male\", \"status\": \"active\", \"class_id\": \"1\", \"last_name\": \"Daka\", \"first_name\": \"Sam\", \"admission_no\": \"ADM-2025-005\", \"admission_date\": \"2025-02-26\", \"class_stream_id\": \"1\", \"academic_year_id\": \"1\"}}',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-11-26 09:10:13','2025-11-26 09:12:04'),(12,2,'Godwin Maje',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'godwinmaje@example.com',NULL,'student',0,NULL,'$2y$12$SKWUF3FiuyveDQCzWapfvO7vtf8GANKAl86EUgEOwIbbziJpbrxH6','approved',NULL,NULL,NULL,'{\"source\": \"tenant_student_module\", \"submitted_at\": \"2025-11-26T13:04:17+00:00\", \"submitted_by\": 2, \"student_profile\": {\"dob\": \"1995-03-02\", \"gender\": \"male\", \"status\": \"active\", \"class_id\": \"1\", \"last_name\": \"Maje\", \"first_name\": \"Godwin\", \"admission_no\": \"ADM-2025-006\", \"admission_date\": \"2025-11-26\", \"class_stream_id\": \"1\", \"academic_year_id\": \"1\"}}',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-11-26 10:04:17','2025-11-26 10:07:12'),(13,2,'Abdul Rasul',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'abdulrasul@example.com',NULL,'student',0,NULL,'$2y$12$M274L3kc44q8KW6xNAAodOsk5at/fjgno65rp20RkBnBvS4Sfgcsy','approved',NULL,NULL,NULL,'{\"source\": \"tenant_student_module\", \"submitted_at\": \"2025-11-26T13:20:20+00:00\", \"submitted_by\": 2, \"student_profile\": {\"dob\": \"1994-03-06\", \"gender\": \"male\", \"status\": \"active\", \"class_id\": \"1\", \"last_name\": \"Rasul\", \"first_name\": \"Abdul\", \"admission_no\": \"ADM-2025-007\", \"admission_date\": \"2025-03-26\", \"class_stream_id\": \"1\", \"academic_year_id\": \"1\"}}',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-11-26 10:20:20','2025-11-26 10:22:57'),(14,2,'Fred Musoke',NULL,'male',NULL,NULL,NULL,NULL,NULL,NULL,'fredmusoke@example.com','profile-photos/XtRqdxNCCpr1CML8qOqBwn58o7pmAddbPXZwRixP.jpg','general_staff',1,NULL,'$2y$12$DDbX8dJ6IM53ITzrOSlRWewjEOAoWCqZ7fmTYtyAAgtjeaYgdjijq','approved',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-11-26 11:01:13','2025-11-29 10:59:46'),(15,2,'Rose Gago',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'rosegago@example.com',NULL,'teaching_staff',1,NULL,'$2y$12$l8XXhd1QYgvm7MggWQ/SEe0hpvVlc.UtqvrnFOM2O.HAXRLfveayy','approved',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-11-26 11:29:49','2025-11-26 11:29:49'),(16,2,'Sam Mirondo','+256704000417','male','1980-04-15',NULL,NULL,NULL,'Bachelor of Education (B.Ed)',NULL,'sammirondo@example.com','profile/photos/yYhMHZgYcNpCeATKx3wxvaCh5CkUFR3mHzeH1DKa.jpg','general_staff',1,NULL,'$2y$12$diA1HXs5yjj1NmgT.Ns1kuvz9NiTldJjPmMA3tca7.7Ny3yk2Hm/6','approved',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'zwR0gAV3BAEKLlG5xlYgEos3fDJn0xmYtLtdEzG2905XkwjdWEUsCA4Nlnsu','2025-11-26 12:07:27','2025-11-29 10:43:50'),(17,2,'Doreen Katusabe',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'doreenkatusabe@example.com',NULL,'teaching_staff',1,NULL,'$2y$12$Z2V07VcKCC045x2wL0WkGe4em4L0qVH25dvSid2FERQON.ci3GbKG','approved',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'RrsawF4UcKurnl7b7r6T7QF9eD3cCaAOmFoDayATsG2hiSvbYWrm6yrIF9BA','2025-11-26 12:19:25','2025-11-26 12:19:25'),(18,2,'Sam Okello',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'samokello@example.com',NULL,'teaching_staff',1,NULL,'$2y$12$r2NZTY/aCuN59w93fu55Ru7HTf0swr1WFGqjPBdHWk1jvey.bpI5W','approved',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-11-26 12:22:00','2025-11-26 12:22:00'),(19,2,'Martha Gudoyi',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'marthagudoyi@example.com',NULL,'teaching_staff',1,NULL,'$2y$12$mTTKvUN/x1HbuX1o4pPZOO16QvnFCjpgZXXv/nq2aoRtGSl6Y1lD.','approved',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-11-26 12:26:47','2025-11-26 12:26:47'),(20,2,'George Seku',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'georgeseku@example.com','parents/profiles/5btncIWh3076os5sQpyGS07OZiTZzsYDurrblp5v.jpg','general_staff',1,NULL,'$2y$12$/OUC6crAbxbONU16BR4qn.AnU2VpY3vPDpIcIILpq7sCwLmkpsY5y','approved',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'lwYQsLfdYbjz7FSXDLYmOUs9m2DvYjDtidXS8KIzpbCjC9a0ezEGuSC4nUgZ','2025-11-29 08:42:12','2025-11-29 10:19:19');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `virtual_class_attendances`
--

DROP TABLE IF EXISTS `virtual_class_attendances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `virtual_class_attendances` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `virtual_class_id` bigint unsigned NOT NULL,
  `student_id` bigint unsigned NOT NULL,
  `joined_at` datetime NOT NULL,
  `left_at` datetime DEFAULT NULL,
  `duration_minutes` int DEFAULT NULL,
  `status` enum('present','late','absent') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'present',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `virtual_class_attendances_student_id_foreign` (`student_id`),
  KEY `virtual_class_attendances_virtual_class_id_student_id_index` (`virtual_class_id`,`student_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `virtual_class_attendances`
--

LOCK TABLES `virtual_class_attendances` WRITE;
/*!40000 ALTER TABLE `virtual_class_attendances` DISABLE KEYS */;
/*!40000 ALTER TABLE `virtual_class_attendances` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `virtual_classes`
--

DROP TABLE IF EXISTS `virtual_classes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `virtual_classes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `teacher_id` bigint unsigned NOT NULL,
  `class_id` bigint unsigned NOT NULL,
  `subject_id` bigint unsigned NOT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `platform` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'zoom',
  `meeting_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meeting_password` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meeting_url` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `scheduled_at` datetime NOT NULL,
  `duration_minutes` int NOT NULL DEFAULT '60',
  `status` enum('scheduled','live','completed','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'scheduled',
  `recording_url` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `auto_record` tinyint(1) NOT NULL DEFAULT '0',
  `is_recurring` tinyint(1) NOT NULL DEFAULT '0',
  `recurrence_pattern` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `recurrence_end_date` date DEFAULT NULL,
  `started_at` datetime DEFAULT NULL,
  `ended_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `virtual_classes_subject_id_foreign` (`subject_id`),
  KEY `virtual_classes_teacher_id_scheduled_at_index` (`teacher_id`,`scheduled_at`),
  KEY `virtual_classes_class_id_status_index` (`class_id`,`status`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `virtual_classes`
--

LOCK TABLES `virtual_classes` WRITE;
/*!40000 ALTER TABLE `virtual_classes` DISABLE KEYS */;
INSERT INTO `virtual_classes` VALUES (1,8,1,1,'Introduction to algebra','Find out the underlying principles that help one to mature into quadratic equasions','google_meet',NULL,NULL,NULL,'2025-11-28 10:00:00',90,'live',NULL,1,0,'daily',NULL,'2025-11-27 15:07:47',NULL,'2025-11-27 11:50:10','2025-11-27 12:07:47',NULL);
/*!40000 ALTER TABLE `virtual_classes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'tenant_000002'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-12-02 10:12:06
