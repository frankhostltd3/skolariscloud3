-- MySQL dump 10.13  Distrib 9.1.0, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: tenant_000001
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `attendance`
--

LOCK TABLES `attendance` WRITE;
/*!40000 ALTER TABLE `attendance` DISABLE KEYS */;
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `attendance_records`
--

LOCK TABLES `attendance_records` WRITE;
/*!40000 ALTER TABLE `attendance_records` DISABLE KEYS */;
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `attendance_settings`
--

LOCK TABLES `attendance_settings` WRITE;
/*!40000 ALTER TABLE `attendance_settings` DISABLE KEYS */;
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
INSERT INTO `cache` VALUES ('smatcampus-cache-spatie.permission.cache.tenant.1','a:3:{s:5:\"alias\";a:5:{s:1:\"a\";s:2:\"id\";s:1:\"b\";s:4:\"name\";s:1:\"c\";s:10:\"guard_name\";s:1:\"r\";s:5:\"roles\";s:1:\"j\";s:9:\"tenant_id\";}s:11:\"permissions\";a:121:{i:0;a:4:{s:1:\"a\";i:130;s:1:\"b\";s:10:\"users.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:12;i:1;i:13;}}i:1;a:4:{s:1:\"a\";i:131;s:1:\"b\";s:12:\"users.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:12;i:1;i:13;}}i:2;a:4:{s:1:\"a\";i:132;s:1:\"b\";s:10:\"users.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:12;i:1;i:13;}}i:3;a:4:{s:1:\"a\";i:133;s:1:\"b\";s:12:\"users.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:12;}}i:4;a:4:{s:1:\"a\";i:134;s:1:\"b\";s:13:\"users.approve\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:12;i:1;i:13;}}i:5;a:4:{s:1:\"a\";i:135;s:1:\"b\";s:13:\"users.suspend\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:12;i:1;i:13;}}i:6;a:4:{s:1:\"a\";i:136;s:1:\"b\";s:12:\"users.export\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:12;i:1;i:13;}}i:7;a:4:{s:1:\"a\";i:137;s:1:\"b\";s:10:\"roles.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:12;i:1;i:13;}}i:8;a:4:{s:1:\"a\";i:138;s:1:\"b\";s:12:\"roles.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:12;i:1;i:13;}}i:9;a:4:{s:1:\"a\";i:139;s:1:\"b\";s:10:\"roles.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:12;i:1;i:13;}}i:10;a:4:{s:1:\"a\";i:140;s:1:\"b\";s:12:\"roles.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:12;}}i:11;a:4:{s:1:\"a\";i:141;s:1:\"b\";s:18:\"permissions.assign\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:12;i:1;i:13;}}i:12;a:4:{s:1:\"a\";i:142;s:1:\"b\";s:13:\"students.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:6:{i:0;i:12;i:1;i:13;i:2;i:14;i:3;i:16;i:4;i:18;i:5;i:19;}}i:13;a:4:{s:1:\"a\";i:143;s:1:\"b\";s:15:\"students.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:12;i:1;i:13;}}i:14;a:4:{s:1:\"a\";i:144;s:1:\"b\";s:13:\"students.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:12;i:1;i:13;i:2;i:14;i:3;i:19;}}i:15;a:4:{s:1:\"a\";i:145;s:1:\"b\";s:15:\"students.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:12;i:1;i:13;}}i:16;a:4:{s:1:\"a\";i:146;s:1:\"b\";s:15:\"students.enroll\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:12;i:1;i:13;}}i:17;a:4:{s:1:\"a\";i:147;s:1:\"b\";s:17:\"students.transfer\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:12;i:1;i:13;}}i:18;a:4:{s:1:\"a\";i:148;s:1:\"b\";s:17:\"students.graduate\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:12;i:1;i:13;}}i:19;a:4:{s:1:\"a\";i:149;s:1:\"b\";s:13:\"teachers.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:12;i:1;i:13;i:2;i:18;i:3;i:19;}}i:20;a:4:{s:1:\"a\";i:150;s:1:\"b\";s:15:\"teachers.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:12;i:1;i:13;}}i:21;a:4:{s:1:\"a\";i:151;s:1:\"b\";s:13:\"teachers.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:12;i:1;i:13;}}i:22;a:4:{s:1:\"a\";i:152;s:1:\"b\";s:15:\"teachers.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:12;i:1;i:13;}}i:23;a:4:{s:1:\"a\";i:153;s:1:\"b\";s:15:\"teachers.assign\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:12;i:1;i:13;i:2;i:19;}}i:24;a:4:{s:1:\"a\";i:154;s:1:\"b\";s:12:\"classes.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:5:{i:0;i:12;i:1;i:13;i:2;i:14;i:3;i:15;i:4;i:19;}}i:25;a:4:{s:1:\"a\";i:155;s:1:\"b\";s:14:\"classes.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:12;i:1;i:13;i:2;i:19;}}i:26;a:4:{s:1:\"a\";i:156;s:1:\"b\";s:12:\"classes.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:12;i:1;i:13;i:2;i:19;}}i:27;a:4:{s:1:\"a\";i:157;s:1:\"b\";s:14:\"classes.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:12;i:1;i:13;}}i:28;a:4:{s:1:\"a\";i:158;s:1:\"b\";s:14:\"classes.assign\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:12;i:1;i:13;i:2;i:14;}}i:29;a:4:{s:1:\"a\";i:159;s:1:\"b\";s:13:\"subjects.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:5:{i:0;i:12;i:1;i:13;i:2;i:14;i:3;i:15;i:4;i:19;}}i:30;a:4:{s:1:\"a\";i:160;s:1:\"b\";s:15:\"subjects.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:12;i:1;i:13;i:2;i:19;}}i:31;a:4:{s:1:\"a\";i:161;s:1:\"b\";s:13:\"subjects.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:12;i:1;i:13;i:2;i:19;}}i:32;a:4:{s:1:\"a\";i:162;s:1:\"b\";s:15:\"subjects.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:12;i:1;i:13;}}i:33;a:4:{s:1:\"a\";i:163;s:1:\"b\";s:15:\"attendance.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:6:{i:0;i:12;i:1;i:13;i:2;i:14;i:3;i:15;i:4;i:16;i:5;i:19;}}i:34;a:4:{s:1:\"a\";i:164;s:1:\"b\";s:15:\"attendance.mark\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:12;i:1;i:13;i:2;i:14;}}i:35;a:4:{s:1:\"a\";i:165;s:1:\"b\";s:15:\"attendance.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:12;i:1;i:13;i:2;i:14;}}i:36;a:4:{s:1:\"a\";i:166;s:1:\"b\";s:17:\"attendance.report\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:12;i:1;i:13;i:2;i:19;}}i:37;a:4:{s:1:\"a\";i:167;s:1:\"b\";s:11:\"grades.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:6:{i:0;i:12;i:1;i:13;i:2;i:14;i:3;i:15;i:4;i:16;i:5;i:19;}}i:38;a:4:{s:1:\"a\";i:168;s:1:\"b\";s:13:\"grades.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:12;i:1;i:13;i:2;i:14;}}i:39;a:4:{s:1:\"a\";i:169;s:1:\"b\";s:11:\"grades.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:12;i:1;i:13;i:2;i:14;}}i:40;a:4:{s:1:\"a\";i:170;s:1:\"b\";s:13:\"grades.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:12;i:1;i:13;}}i:41;a:4:{s:1:\"a\";i:171;s:1:\"b\";s:14:\"grades.approve\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:12;i:1;i:13;i:2;i:19;}}i:42;a:4:{s:1:\"a\";i:172;s:1:\"b\";s:13:\"grades.report\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:12;i:1;i:13;}}i:43;a:4:{s:1:\"a\";i:173;s:1:\"b\";s:16:\"assignments.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:12;i:1;i:13;i:2;i:14;i:3;i:15;}}i:44;a:4:{s:1:\"a\";i:174;s:1:\"b\";s:18:\"assignments.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:12;i:1;i:13;i:2;i:14;}}i:45;a:4:{s:1:\"a\";i:175;s:1:\"b\";s:16:\"assignments.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:12;i:1;i:13;i:2;i:14;}}i:46;a:4:{s:1:\"a\";i:176;s:1:\"b\";s:18:\"assignments.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:12;i:1;i:13;}}i:47;a:4:{s:1:\"a\";i:177;s:1:\"b\";s:17:\"assignments.grade\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:12;i:1;i:13;i:2;i:14;}}i:48;a:4:{s:1:\"a\";i:178;s:1:\"b\";s:10:\"exams.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:12;i:1;i:13;i:2;i:14;i:3;i:15;}}i:49;a:4:{s:1:\"a\";i:179;s:1:\"b\";s:12:\"exams.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:12;i:1;i:13;}}i:50;a:4:{s:1:\"a\";i:180;s:1:\"b\";s:10:\"exams.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:12;i:1;i:13;}}i:51;a:4:{s:1:\"a\";i:181;s:1:\"b\";s:12:\"exams.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:12;i:1;i:13;}}i:52;a:4:{s:1:\"a\";i:182;s:1:\"b\";s:14:\"exams.schedule\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:12;i:1;i:13;}}i:53;a:4:{s:1:\"a\";i:183;s:1:\"b\";s:14:\"timetable.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:5:{i:0;i:12;i:1;i:13;i:2;i:14;i:3;i:15;i:4;i:19;}}i:54;a:4:{s:1:\"a\";i:184;s:1:\"b\";s:16:\"timetable.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:12;i:1;i:13;i:2;i:19;}}i:55;a:4:{s:1:\"a\";i:185;s:1:\"b\";s:14:\"timetable.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:12;i:1;i:13;i:2;i:19;}}i:56;a:4:{s:1:\"a\";i:186;s:1:\"b\";s:16:\"timetable.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:12;i:1;i:13;}}i:57;a:4:{s:1:\"a\";i:187;s:1:\"b\";s:12:\"finance.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:5:{i:0;i:12;i:1;i:13;i:2;i:16;i:3;i:17;i:4;i:21;}}i:58;a:4:{s:1:\"a\";i:188;s:1:\"b\";s:14:\"finance.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:12;i:1;i:13;i:2;i:17;i:3;i:21;}}i:59;a:4:{s:1:\"a\";i:189;s:1:\"b\";s:12:\"finance.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:12;i:1;i:13;i:2;i:17;i:3;i:21;}}i:60;a:4:{s:1:\"a\";i:190;s:1:\"b\";s:14:\"finance.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:12;i:1;i:13;}}i:61;a:4:{s:1:\"a\";i:191;s:1:\"b\";s:11:\"fees.manage\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:12;i:1;i:13;i:2;i:17;i:3;i:21;}}i:62;a:4:{s:1:\"a\";i:192;s:1:\"b\";s:16:\"payments.process\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:12;i:1;i:13;i:2;i:17;i:3;i:21;}}i:63;a:4:{s:1:\"a\";i:193;s:1:\"b\";s:15:\"payments.refund\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:12;i:1;i:13;i:2;i:17;i:3;i:21;}}i:64;a:4:{s:1:\"a\";i:194;s:1:\"b\";s:17:\"invoices.generate\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:5:{i:0;i:12;i:1;i:13;i:2;i:16;i:3;i:17;i:4;i:21;}}i:65;a:4:{s:1:\"a\";i:195;s:1:\"b\";s:7:\"hr.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:12;i:1;i:13;}}i:66;a:4:{s:1:\"a\";i:196;s:1:\"b\";s:9:\"hr.manage\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:12;i:1;i:13;}}i:67;a:4:{s:1:\"a\";i:197;s:1:\"b\";s:14:\"employees.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:12;i:1;i:13;}}i:68;a:4:{s:1:\"a\";i:198;s:1:\"b\";s:16:\"employees.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:12;i:1;i:13;}}i:69;a:4:{s:1:\"a\";i:199;s:1:\"b\";s:14:\"employees.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:12;i:1;i:13;}}i:70;a:4:{s:1:\"a\";i:200;s:1:\"b\";s:16:\"employees.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:12;i:1;i:13;}}i:71;a:4:{s:1:\"a\";i:201;s:1:\"b\";s:19:\"leave-requests.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:12;i:1;i:13;}}i:72;a:4:{s:1:\"a\";i:202;s:1:\"b\";s:21:\"leave-requests.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:12;i:1;i:13;}}i:73;a:4:{s:1:\"a\";i:203;s:1:\"b\";s:22:\"leave-requests.approve\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:12;i:1;i:13;}}i:74;a:4:{s:1:\"a\";i:204;s:1:\"b\";s:21:\"leave-requests.reject\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:12;i:1;i:13;}}i:75;a:4:{s:1:\"a\";i:205;s:1:\"b\";s:14:\"pamphlets.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:12;i:1;i:13;i:2;i:14;}}i:76;a:4:{s:1:\"a\";i:206;s:1:\"b\";s:16:\"pamphlets.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:12;i:1;i:13;}}i:77;a:4:{s:1:\"a\";i:207;s:1:\"b\";s:14:\"pamphlets.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:12;i:1;i:13;}}i:78;a:4:{s:1:\"a\";i:208;s:1:\"b\";s:16:\"pamphlets.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:12;i:1;i:13;}}i:79;a:4:{s:1:\"a\";i:209;s:1:\"b\";s:17:\"pamphlets.publish\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:12;i:1;i:13;}}i:80;a:4:{s:1:\"a\";i:210;s:1:\"b\";s:10:\"books.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:5:{i:0;i:12;i:1;i:13;i:2;i:14;i:3;i:15;i:4;i:18;}}i:81;a:4:{s:1:\"a\";i:211;s:1:\"b\";s:12:\"books.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:12;i:1;i:13;}}i:82;a:4:{s:1:\"a\";i:212;s:1:\"b\";s:10:\"books.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:12;i:1;i:13;}}i:83;a:4:{s:1:\"a\";i:213;s:1:\"b\";s:12:\"books.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:12;i:1;i:13;}}i:84;a:4:{s:1:\"a\";i:214;s:1:\"b\";s:14:\"bookstore.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:5:{i:0;i:12;i:1;i:13;i:2;i:14;i:3;i:15;i:4;i:18;}}i:85;a:4:{s:1:\"a\";i:215;s:1:\"b\";s:16:\"bookstore.manage\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:12;i:1;i:13;}}i:86;a:4:{s:1:\"a\";i:216;s:1:\"b\";s:16:\"bookstore.orders\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:12;i:1;i:13;}}i:87;a:4:{s:1:\"a\";i:217;s:1:\"b\";s:18:\"bookstore.purchase\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:12;i:1;i:13;i:2;i:15;i:3;i:18;}}i:88;a:4:{s:1:\"a\";i:218;s:1:\"b\";s:12:\"library.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:12;i:1;i:13;i:2;i:15;i:3;i:18;}}i:89;a:4:{s:1:\"a\";i:219;s:1:\"b\";s:14:\"library.manage\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:12;i:1;i:13;i:2;i:18;}}i:90;a:4:{s:1:\"a\";i:220;s:1:\"b\";s:13:\"library.issue\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:12;i:1;i:13;i:2;i:18;}}i:91;a:4:{s:1:\"a\";i:221;s:1:\"b\";s:14:\"library.return\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:12;i:1;i:13;i:2;i:18;}}i:92;a:4:{s:1:\"a\";i:222;s:1:\"b\";s:12:\"reports.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:10:{i:0;i:12;i:1;i:13;i:2;i:14;i:3;i:16;i:4;i:17;i:5;i:18;i:6;i:19;i:7;i:20;i:8;i:21;i:9;i:22;}}i:93;a:4:{s:1:\"a\";i:223;s:1:\"b\";s:16:\"reports.generate\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:7:{i:0;i:12;i:1;i:13;i:2;i:14;i:3;i:17;i:4;i:18;i:5;i:19;i:6;i:21;}}i:94;a:4:{s:1:\"a\";i:224;s:1:\"b\";s:14:\"reports.export\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:12;i:1;i:13;i:2;i:17;i:3;i:21;}}i:95;a:4:{s:1:\"a\";i:225;s:1:\"b\";s:14:\"reports.custom\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:12;i:1;i:13;i:2;i:19;}}i:96;a:4:{s:1:\"a\";i:226;s:1:\"b\";s:13:\"settings.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:12;i:1;i:13;}}i:97;a:4:{s:1:\"a\";i:227;s:1:\"b\";s:13:\"settings.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:12;i:1;i:13;}}i:98;a:4:{s:1:\"a\";i:228;s:1:\"b\";s:16:\"settings.general\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:12;i:1;i:13;}}i:99;a:4:{s:1:\"a\";i:229;s:1:\"b\";s:17:\"settings.academic\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:12;i:1;i:13;}}i:100;a:4:{s:1:\"a\";i:230;s:1:\"b\";s:15:\"settings.system\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:12;}}i:101;a:4:{s:1:\"a\";i:231;s:1:\"b\";s:13:\"settings.mail\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:12;i:1;i:13;}}i:102;a:4:{s:1:\"a\";i:232;s:1:\"b\";s:16:\"settings.payment\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:12;i:1;i:13;}}i:103;a:4:{s:1:\"a\";i:233;s:1:\"b\";s:18:\"settings.messaging\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:12;i:1;i:13;}}i:104;a:4:{s:1:\"a\";i:234;s:1:\"b\";s:13:\"messages.send\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:12;i:1;i:13;i:2;i:14;i:3;i:16;}}i:105;a:4:{s:1:\"a\";i:235;s:1:\"b\";s:13:\"messages.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:7:{i:0;i:12;i:1;i:13;i:2;i:14;i:3;i:15;i:4;i:16;i:5;i:20;i:6;i:22;}}i:106;a:4:{s:1:\"a\";i:236;s:1:\"b\";s:20:\"announcements.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:12;i:1;i:13;}}i:107;a:4:{s:1:\"a\";i:237;s:1:\"b\";s:18:\"announcements.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:12;i:1;i:13;}}i:108;a:4:{s:1:\"a\";i:238;s:1:\"b\";s:18:\"notifications.send\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:12;i:1;i:13;}}i:109;a:4:{s:1:\"a\";i:239;s:1:\"b\";s:14:\"documents.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:6:{i:0;i:12;i:1;i:13;i:2;i:14;i:3;i:15;i:4;i:20;i:5;i:22;}}i:110;a:4:{s:1:\"a\";i:240;s:1:\"b\";s:16:\"documents.upload\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:12;i:1;i:13;i:2;i:14;}}i:111;a:4:{s:1:\"a\";i:241;s:1:\"b\";s:18:\"documents.download\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:12;i:1;i:13;i:2;i:14;i:3;i:15;}}i:112;a:4:{s:1:\"a\";i:242;s:1:\"b\";s:16:\"documents.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:12;i:1;i:13;}}i:113;a:4:{s:1:\"a\";i:243;s:1:\"b\";s:16:\"departments.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:12;i:1;i:13;i:2;i:19;}}i:114;a:4:{s:1:\"a\";i:244;s:1:\"b\";s:18:\"departments.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:12;i:1;i:13;}}i:115;a:4:{s:1:\"a\";i:245;s:1:\"b\";s:16:\"departments.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:12;i:1;i:13;i:2;i:19;}}i:116;a:4:{s:1:\"a\";i:246;s:1:\"b\";s:18:\"departments.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:12;i:1;i:13;}}i:117;a:4:{s:1:\"a\";i:247;s:1:\"b\";s:14:\"positions.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:12;i:1;i:13;}}i:118;a:4:{s:1:\"a\";i:248;s:1:\"b\";s:16:\"positions.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:12;i:1;i:13;}}i:119;a:4:{s:1:\"a\";i:249;s:1:\"b\";s:14:\"positions.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:12;i:1;i:13;}}i:120;a:4:{s:1:\"a\";i:250;s:1:\"b\";s:16:\"positions.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:12;i:1;i:13;}}}s:5:\"roles\";a:11:{i:0;a:4:{s:1:\"a\";i:12;s:1:\"j\";N;s:1:\"b\";s:11:\"Super-Admin\";s:1:\"c\";s:3:\"web\";}i:1;a:4:{s:1:\"a\";i:13;s:1:\"j\";N;s:1:\"b\";s:5:\"Admin\";s:1:\"c\";s:3:\"web\";}i:2;a:4:{s:1:\"a\";i:14;s:1:\"j\";N;s:1:\"b\";s:7:\"Teacher\";s:1:\"c\";s:3:\"web\";}i:3;a:4:{s:1:\"a\";i:16;s:1:\"j\";N;s:1:\"b\";s:6:\"Parent\";s:1:\"c\";s:3:\"web\";}i:4;a:4:{s:1:\"a\";i:18;s:1:\"j\";N;s:1:\"b\";s:9:\"Librarian\";s:1:\"c\";s:3:\"web\";}i:5;a:4:{s:1:\"a\";i:19;s:1:\"j\";N;s:1:\"b\";s:18:\"Head-of-Department\";s:1:\"c\";s:3:\"web\";}i:6;a:4:{s:1:\"a\";i:15;s:1:\"j\";N;s:1:\"b\";s:7:\"Student\";s:1:\"c\";s:3:\"web\";}i:7;a:4:{s:1:\"a\";i:17;s:1:\"j\";N;s:1:\"b\";s:10:\"Accountant\";s:1:\"c\";s:3:\"web\";}i:8;a:4:{s:1:\"a\";i:21;s:1:\"j\";N;s:1:\"b\";s:6:\"Bursar\";s:1:\"c\";s:3:\"web\";}i:9;a:4:{s:1:\"a\";i:20;s:1:\"j\";N;s:1:\"b\";s:5:\"Staff\";s:1:\"c\";s:3:\"web\";}i:10;a:4:{s:1:\"a\";i:22;s:1:\"j\";N;s:1:\"b\";s:5:\"Nurse\";s:1:\"c\";s:3:\"web\";}}}',1764112434);
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `class_streams`
--

LOCK TABLES `class_streams` WRITE;
/*!40000 ALTER TABLE `class_streams` DISABLE KEYS */;
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `class_subject`
--

LOCK TABLES `class_subject` WRITE;
/*!40000 ALTER TABLE `class_subject` DISABLE KEYS */;
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `classes`
--

LOCK TABLES `classes` WRITE;
/*!40000 ALTER TABLE `classes` DISABLE KEYS */;
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `countries`
--

LOCK TABLES `countries` WRITE;
/*!40000 ALTER TABLE `countries` DISABLE KEYS */;
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `currencies`
--

LOCK TABLES `currencies` WRITE;
/*!40000 ALTER TABLE `currencies` DISABLE KEYS */;
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
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `departments`
--

LOCK TABLES `departments` WRITE;
/*!40000 ALTER TABLE `departments` DISABLE KEYS */;
INSERT INTO `departments` VALUES (1,'Teaching Staff','TEA','Academic teaching staff department','2025-11-24 16:56:02','2025-11-24 16:56:02');
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `education_levels`
--

LOCK TABLES `education_levels` WRITE;
/*!40000 ALTER TABLE `education_levels` DISABLE KEYS */;
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
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employees`
--

LOCK TABLES `employees` WRITE;
/*!40000 ALTER TABLE `employees` DISABLE KEYS */;
INSERT INTO `employees` VALUES (1,'full_time','EMP20255912',NULL,'male',1,1,NULL,'Test','Teacher','test.teacher@example.com','1234567890','2023-01-01','1990-01-01','active',1,1,NULL,NULL,'2025-11-24 16:56:02','2025-11-24 16:56:02',NULL);
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `enrollments`
--

LOCK TABLES `enrollments` WRITE;
/*!40000 ALTER TABLE `enrollments` DISABLE KEYS */;
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `examination_bodies`
--

LOCK TABLES `examination_bodies` WRITE;
/*!40000 ALTER TABLE `examination_bodies` DISABLE KEYS */;
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `exercises`
--

LOCK TABLES `exercises` WRITE;
/*!40000 ALTER TABLE `exercises` DISABLE KEYS */;
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `expense_categories`
--

LOCK TABLES `expense_categories` WRITE;
/*!40000 ALTER TABLE `expense_categories` DISABLE KEYS */;
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fee_structures`
--

LOCK TABLES `fee_structures` WRITE;
/*!40000 ALTER TABLE `fee_structures` DISABLE KEYS */;
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `grading_bands`
--

LOCK TABLES `grading_bands` WRITE;
/*!40000 ALTER TABLE `grading_bands` DISABLE KEYS */;
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `grading_schemes`
--

LOCK TABLES `grading_schemes` WRITE;
/*!40000 ALTER TABLE `grading_schemes` DISABLE KEYS */;
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `invoices`
--

LOCK TABLES `invoices` WRITE;
/*!40000 ALTER TABLE `invoices` DISABLE KEYS */;
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `leave_requests`
--

LOCK TABLES `leave_requests` WRITE;
/*!40000 ALTER TABLE `leave_requests` DISABLE KEYS */;
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lesson_plans`
--

LOCK TABLES `lesson_plans` WRITE;
/*!40000 ALTER TABLE `lesson_plans` DISABLE KEYS */;
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
INSERT INTO `mail_settings` VALUES (1,'mail','SMATCAMPUS Demo School','no-reply@demo.localhost','eyJpdiI6IlB6dHlZeDl5MmpaQXpteDdNWTd6R0E9PSIsInZhbHVlIjoiQVZjSENZcU9GTUFMbHNYZVNyVURtQT09IiwibWFjIjoiYzVkNjUwMWI1YzI3MGJiMTM0YzJkZWQ5Y2M1MmM4ZGQwNmQxOTk2YTE1NzczZmM1NTFmMzBjZTQzNzkxNjA0MyIsInRhZyI6IiJ9','2025-11-22 08:44:16','2025-11-22 08:44:16');
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
INSERT INTO `messaging_channel_settings` VALUES (1,'sms','twilio',0,'eyJpdiI6InZkdVNTMVFVTm9jUDA0dmlZLzRjM3c9PSIsInZhbHVlIjoiVDF1b0VncytqaE85ZXhRc2tDQmI4UT09IiwibWFjIjoiYTQ2MzFjOTY2NDc2ZTcwM2JlZWZiZjUyYTVkMjNjMGE3YWU4MDg1ODE5M2ViYTRkMzM3ZjkzYWY2YWJmM2Y3YiIsInRhZyI6IiJ9',NULL,'2025-11-22 08:44:16','2025-11-22 08:44:16'),(2,'sms','vonage',0,'eyJpdiI6IitQNjNEUml0OHVjcjNHdzJCNDFpN2c9PSIsInZhbHVlIjoiZ1hJbUVRRzk4WWY2SVg1c3FNUlZ0Zz09IiwibWFjIjoiMDNhYWZmMzM0MmYxODRhMDliYzExODE5ZGQyYmE3YWU0OWI4ZWYwZWRiOGIzMDZiNWMxMTdkMGJkOTJlMjRjOSIsInRhZyI6IiJ9',NULL,'2025-11-22 08:44:16','2025-11-22 08:44:16'),(3,'sms','africastalking',0,'eyJpdiI6IitZUFgzREFTZFVIamk0RG95SUgvb3c9PSIsInZhbHVlIjoiVWJHK3FpRVMwcEM4TUFhSWpJZTFhQT09IiwibWFjIjoiOTc3NzU4MGY2NmI1MGIzOTg4ZGVhNWVkZjk2NmMxNTE3ZGI4NTA3YzA3ZjA0MmY0MzNkNGY4MDdiOWQ1MDQ5NyIsInRhZyI6IiJ9',NULL,'2025-11-22 08:44:16','2025-11-22 08:44:16'),(4,'sms','custom',0,'eyJpdiI6InVwZDlzK0VRZHZQVUZIMzlWOTh0anc9PSIsInZhbHVlIjoic2c1amdkc0Q1MjFWN2tZWmt6UCtqQT09IiwibWFjIjoiNWFhZjcxMzRkYTc3ZDQwNjg0YTEyYTZlOWFmNjYxMTk4ZGJhNzAwNjNiMjFjOGJhMDJmZGEzMGIwMzVhNDU1MiIsInRhZyI6IiJ9',NULL,'2025-11-22 08:44:16','2025-11-22 08:44:16'),(5,'whatsapp','twilio_whatsapp',0,'eyJpdiI6IlYwU3hEcng3QWkzMHE4Mzd6aThLK3c9PSIsInZhbHVlIjoiTm9FdnhNMkJDNGlmbXoxc2Y5WThsZz09IiwibWFjIjoiNzFkMDBkMWUzMDNhYmI0ZTRjNTM4NWMyMWI3MDA2MmNhNWFmYWFiNWNhNTA5MTM5ZDZlODM5MWI2N2ZlZDVlMCIsInRhZyI6IiJ9',NULL,'2025-11-22 08:44:16','2025-11-22 08:44:16'),(6,'whatsapp','meta_cloud',0,'eyJpdiI6ImdmN1NlWnlSczdIQmh3eDZOc0gySmc9PSIsInZhbHVlIjoiRUdlMFpIbUdXY3UvWm0rM0d6ckR4QT09IiwibWFjIjoiODkxMDc4MWE0ODA1NDBiZmExZmJkMDYwMzMwMWU2ZGYyZTQwNzU2NjI3MTAwZjQ2OTlkNTViOTBkYTZmNzRlNyIsInRhZyI6IiJ9',NULL,'2025-11-22 08:44:16','2025-11-22 08:44:16'),(7,'whatsapp','custom',0,'eyJpdiI6Ilh0ZHIrbEVkSVBVd041dXdFaHJORkE9PSIsInZhbHVlIjoiOE5HNWhteitkalRycEgyd21nOVBTUT09IiwibWFjIjoiOTg0YWMzMTdmNzJkNTc5NzNhZjhmOWMwNmJmNDQxNjAyNTYzZjM1NGJiOTI4Nzg1MzRkNzg5MDY0N2VhODk0NSIsInRhZyI6IiJ9',NULL,'2025-11-22 08:44:16','2025-11-22 08:44:16'),(8,'telegram','telegram_bot',0,'eyJpdiI6IlE1V3VHeUJ0ZGp1emVSRE00ZVNjYlE9PSIsInZhbHVlIjoiMUo4d0lvRnlTaUpOYS9oMyt6a2FZY3VBbk9mUHJUZVFkek8wYWUxQ1pQQT0iLCJtYWMiOiI0NTgxOGY3NmUyYTc3ZjNlOTkxZWMxMDdkMjQyNDY4ZjA3YWU0MGQ3OWNlZGFkMjY0MDY0MGM4MjBmZTY1YWEzIiwidGFnIjoiIn0=',NULL,'2025-11-22 08:44:16','2025-11-22 08:44:16'),(9,'telegram','custom',0,'eyJpdiI6IjF0UDVkb1BwOTczZng1UU5NUTVIcUE9PSIsInZhbHVlIjoiMlZLQnVYc3FVSVF4RzBTMWlPdGlRdz09IiwibWFjIjoiOWRmZDM5Zjk2MjM5ZmYzYTgyNjkxNmJjNWZkNTcxMzU2YmZiM2IzZjJjMWUyOWVmYzNkZDlmODQxOWQyZThkNiIsInRhZyI6IiJ9',NULL,'2025-11-22 08:44:16','2025-11-22 08:44:16');
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
INSERT INTO `migrations` VALUES (1,'2024_01_01_000001_create_users_table',1),(2,'2024_01_01_000002_create_cache_table',1),(3,'2024_01_01_000003_create_jobs_table',1),(4,'2024_01_01_000010_create_settings_table',1),(5,'2024_01_01_000011_create_mail_settings_table',1),(6,'2024_01_01_000012_create_payment_gateway_settings_table',1),(7,'2024_01_01_000013_create_messaging_channel_settings_table',1),(8,'2024_01_01_000014_create_attendance_settings_table',1),(9,'2024_01_01_000015_create_platform_integrations_table',1),(10,'2024_01_01_000020_create_currencies_table',1),(11,'2024_01_01_000021_add_exchange_rate_metadata_to_currencies_table',1),(12,'2024_01_01_000030_create_permission_tables',1),(13,'2024_01_01_000031_add_approval_fields_to_users_table',1),(14,'2024_01_01_000040_create_departments_table',1),(15,'2024_01_01_000041_create_positions_table',1),(16,'2024_01_01_000042_create_salary_scales_table',1),(17,'2024_01_01_000050_create_employees_table',1),(18,'2024_01_01_000051_add_employee_number_to_employees_table',1),(19,'2024_01_01_000052_add_employee_type_to_employees_table',1),(20,'2024_01_01_000053_add_identity_fields_to_employees_table',1),(21,'2024_01_01_000060_create_teachers_table',1),(22,'2024_01_01_000061_enhance_teachers_table',1),(23,'2024_01_01_000062_add_sync_fields_for_teacher_employee',1),(24,'2024_01_01_000070_create_education_levels_table',1),(25,'2024_01_01_000071_create_countries_and_examination_bodies_tables',1),(26,'2024_01_01_000072_create_grading_schemes_tables',1),(27,'2024_01_01_000073_create_terms_table',1),(28,'2024_01_01_000074_create_rooms_table',1),(29,'2024_01_01_000080_create_classes_table',1),(30,'2024_01_01_000081_create_class_streams_table',1),(31,'2024_01_01_000090_create_subjects_table',1),(32,'2024_01_01_000091_add_required_periods_to_subjects_table',1),(33,'2024_01_01_000100_create_class_subjects_table',1),(34,'2024_01_01_000101_update_subjects_and_class_subjects_tables',1),(35,'2024_01_01_000110_create_timetable_entries_table',1),(36,'2024_01_01_000111_add_room_id_to_timetable_entries_table',1),(37,'2024_01_01_000112_create_teacher_availabilities_table',1),(38,'2024_01_01_000113_create_timetable_constraints_table',1),(39,'2024_01_01_000120_create_attendance_table',1),(40,'2024_01_01_000121_create_attendance_records_table',1),(41,'2024_01_01_000122_create_staff_attendance_table',1),(42,'2024_01_01_000123_create_biometric_templates_table',1),(43,'2024_01_01_000124_add_attendance_method_tracking',1),(44,'2024_01_01_000130_create_expense_categories_table',1),(45,'2024_01_01_000131_create_transactions_table',1),(46,'2024_01_01_000132_create_fee_structures_table',1),(47,'2024_01_01_000133_create_expenses_table',1),(48,'2024_01_01_000134_create_invoices_table',1),(49,'2024_01_01_000135_create_payments_table',1),(50,'2024_01_01_000136_create_payment_gateway_configs_table',1),(51,'2024_01_01_000137_create_payment_transactions_table',1),(52,'2024_01_01_000140_create_quiz_tables',1),(53,'2024_01_01_000150_create_report_logs_table',1),(54,'2025_11_17_021154_create_leave_requests_table',1),(55,'2025_11_17_021154_create_leave_types_table',1),(56,'2025_11_17_021154_create_students_table',1),(57,'2025_11_17_021155_add_financial_tracking_to_leave_requests_table',1),(58,'2025_11_17_021155_add_policy_fields_to_leave_types_table',1),(59,'2025_11_19_000200_update_payment_gateway_settings_columns',1),(60,'2025_11_19_000210_update_messaging_channel_settings_columns',1),(61,'2025_11_20_000500_create_payments_table',1),(62,'2025_11_20_001100_add_school_id_to_expenses_table',1),(63,'2025_11_20_001200_add_school_id_to_invoices_table',1),(64,'2025_11_20_001300_update_invoices_finance_columns',1),(65,'2025_11_21_000001_add_academic_permissions',1),(66,'2025_11_21_000001_add_missing_columns_to_expense_categories_table',1),(67,'2025_11_21_000002_make_tenant_id_nullable_in_expenses_table',1),(68,'2025_11_21_083000_create_lesson_plans_table',1),(69,'2025_11_22_000000_add_missing_columns_to_leave_requests_table',2),(70,'2025_11_23_000000_create_security_audit_logs_table',3),(71,'2025_11_24_000000_create_library_tables',4),(72,'2025_11_17_021155_create_bookstore_order_items_table',5),(73,'2025_11_17_021155_create_bookstore_orders_table',5),(74,'2025_11_24_081657_add_digital_fields_to_library_books_table',6),(75,'2025_11_17_021155_create_academic_years_table',7),(76,'2025_11_17_021155_create_enrollments_table',8),(77,'2025_11_17_021155_create_message_recipients_table',9),(78,'2025_11_17_021155_create_message_threads_table',10),(79,'2025_11_17_021155_create_messages_table',10),(80,'2025_11_17_021154_create_classes_table',11),(81,'2025_11_17_021154_create_grades_table',11),(82,'2025_11_17_021154_create_online_exams_table',11),(83,'2025_11_17_021154_create_quizzes_table',11),(84,'2025_11_17_021154_create_subjects_table',11),(85,'2025_11_17_021154_create_teachers_table',11),(86,'2025_11_17_021154_create_timetable_entries_table',11),(87,'2025_11_24_000000_create_virtual_classrooms_tables',12),(88,'2025_11_17_021154_create_exercises_table',13),(89,'2025_11_24_000002_add_school_id_to_exercises_table',14),(90,'2025_11_17_021154_add_soft_deletes_to_exercise_submissions',15),(91,'2025_11_24_000003_add_soft_deletes_to_exercise_submissions',15),(92,'2025_11_24_090000_update_grades_table_schema',16),(93,'2025_11_24_100000_create_otp_codes_table',17),(94,'2025_11_17_021155_create_allocation_pivot_tables',18),(95,'2025_11_17_021155_create_assignment_submissions_table',19),(96,'2025_11_17_021155_create_assignments_table',19),(97,'2025_11_17_021155_create_payroll_records_table',20),(98,'2025_11_25_000001_create_class_stream_teacher_table',21),(99,'2025_11_25_120000_create_employee_id_settings_table',21),(100,'2025_11_25_150000_create_payroll_settings_table',22),(101,'2025_11_26_000001_add_class_stream_id_to_enrollments_table',23),(102,'2025_11_26_144000_add_user_id_to_teachers_table',24),(103,'2025_11_26_160000_fix_timetable_entries_teacher_fk',25),(104,'2025_11_26_203000_add_recurring_fields_to_fee_structures_table',26),(105,'2025_11_27_120000_create_notifications_table',27),(106,'2025_11_27_120000_add_profile_photo_to_users_table',28),(107,'2025_11_27_000001_add_workflow_fields_to_online_exams_table',29),(108,'2025_11_27_130000_create_user_preferences_table',29),(109,'2025_11_27_130000_add_soft_deletes_to_quizzes_table',30),(110,'2025_11_27_133000_add_instructions_to_quizzes_table',31),(111,'2025_11_27_134000_add_class_and_subject_to_quizzes_table',32),(112,'2025_11_27_135000_add_missing_columns_to_quizzes_table',33),(113,'2025_11_27_140000_add_soft_deletes_to_quiz_questions_table',34),(114,'2025_11_28_120000_rename_shuffle_options_to_shuffle_answers_in_online_exams_table',35),(115,'2025_11_28_121000_add_deleted_at_to_online_exam_attempts_table',36),(116,'2025_11_28_122000_add_deleted_at_to_online_exam_related_tables',37),(117,'2025_11_28_105212_add_content_to_exercises_table',38),(118,'2025_11_28_110000_add_exercise_questions_and_settings',39),(119,'2025_11_28_115000_add_exercise_questions_and_settings',39),(120,'2025_11_28_120000_add_advanced_features_to_exercises_table',40),(121,'2025_11_28_160000_add_missing_assignment_columns',41),(122,'2025_11_17_021155_create_student_notes_table',42),(123,'2025_11_29_000000_create_forum_tables',43),(124,'2025_11_29_100000_create_parent_portal_tables',44),(125,'2025_11_29_120000_create_sessions_table',45),(126,'2025_11_29_130000_create_parents_table_fix',46),(127,'2025_11_29_130100_create_parent_student_table_fix',46),(128,'2025_11_29_100000_create_meetings_table',47),(129,'2025_11_29_100500_add_student_teacher_to_meetings_table',48),(130,'2025_11_29_133000_fix_parent_student_columns',49),(131,'2025_11_29_140000_add_profile_columns_to_users_table',50),(132,'2025_11_29_145800_add_emergency_contact_to_users_table',51),(133,'2025_11_29_220000_add_reason_fields_to_invoices_table',52),(134,'2025_11_29_300000_create_mobile_money_gateways_table',53),(135,'2025_11_30_100000_create_payment_transactions_table',54);
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
INSERT INTO `model_has_roles` VALUES (13,'App\\Models\\User',1,1);
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `online_exam_questions`
--

LOCK TABLES `online_exam_questions` WRITE;
/*!40000 ALTER TABLE `online_exam_questions` DISABLE KEYS */;
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `online_exam_sections`
--

LOCK TABLES `online_exam_sections` WRITE;
/*!40000 ALTER TABLE `online_exam_sections` DISABLE KEYS */;
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `online_exams`
--

LOCK TABLES `online_exams` WRITE;
/*!40000 ALTER TABLE `online_exams` DISABLE KEYS */;
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `parent_student`
--

LOCK TABLES `parent_student` WRITE;
/*!40000 ALTER TABLE `parent_student` DISABLE KEYS */;
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `parents`
--

LOCK TABLES `parents` WRITE;
/*!40000 ALTER TABLE `parents` DISABLE KEYS */;
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
INSERT INTO `payment_gateway_settings` VALUES (1,'paypal',0,'eyJpdiI6ImF2ZjNBeDdReG5LYS9uamt0V0FpWkE9PSIsInZhbHVlIjoiRk1tNmh3WE5zZ1NudjRYUkJxM3FqT3ZZeFlDQUxFNGgrU2c0N0FNTHF1cz0iLCJtYWMiOiI5MTdiYjNlYjY4NTU5YmQ3NjFkNWMzZDg0YmUwYTdjZTMwMTFlYTM0M2JjZWM3NzE1Nzk5MTAwNzE3ZGVhNTljIiwidGFnIjoiIn0=',NULL,'2025-11-22 08:44:16','2025-11-22 08:44:16'),(2,'stripe',0,'eyJpdiI6ImV2RTBGaDRGSlduMC9kbzNiVXJtemc9PSIsInZhbHVlIjoiWm00emVsUG1wcGlUSDdyNnhpZVd5QT09IiwibWFjIjoiOGUxYjM5ZmM5MTlkN2ZjMDNkZDU5OTUzOWM1NWE5OGUwYzA0NWE5MGUwMjgxNDhjM2RmMGM0ZjEzMjlkNGYxZSIsInRhZyI6IiJ9',NULL,'2025-11-22 08:44:16','2025-11-22 08:44:16'),(3,'flutterwave',0,'eyJpdiI6IkVhVEpUM3Naa3MyMmJTV3BpRjB1cUE9PSIsInZhbHVlIjoicFpxYVpHejdKNzJBMWNQOG9ESUQxY0doaFpJejQwWHFFM3o5Qmh4QkMrWT0iLCJtYWMiOiI3OTVjNmYwMzcwY2JmMDUwMzVlZmNkZWViZTdkZDU3Y2ViZDU3ZmVlYjcwYzBjYTBjMzE4NWU1NTlhMzY4ZTdlIiwidGFnIjoiIn0=',NULL,'2025-11-22 08:44:16','2025-11-22 08:44:16'),(4,'mtn_momo',0,'eyJpdiI6Ijl0dys1RnRnQmRrZWdUdHdnUUNHbnc9PSIsInZhbHVlIjoiSUFBYTZTbTB6NjNteFRVclRhZ1k0aHdEc1ZINVBpWTRCTURWemp2eDVkWT0iLCJtYWMiOiIyYmYwZjRkMWExMGVjMzg0MWUzNGRlZDdkMzFjYmIyYzMxOTBmM2U5MzA0NjdmNjIzYjE1M2Y2MTZiNDE1OTAyIiwidGFnIjoiIn0=',NULL,'2025-11-22 08:44:16','2025-11-22 08:44:16'),(5,'airtel_money',0,'eyJpdiI6ImdqVnNCTzVFUjYvL3JoVFVWUGtGMVE9PSIsInZhbHVlIjoieDhLR1J5a1lwLy80UVJjUFpYcWpaTlNQM20rUVVZdHRJdW1VQzhIUEZiMD0iLCJtYWMiOiIzY2EzOTIwOWRlM2RmZWRiYjE1ZmFlYWEyZDE3MGFhODgxMjA0MWFlMTg2OWE5OTQ1MjUwZmE1MzhhYmFkMTZmIiwidGFnIjoiIn0=',NULL,'2025-11-22 08:44:16','2025-11-22 08:44:16'),(6,'pesapal',0,'eyJpdiI6InY4Rm9QNGRoYWNMTmI0V2ZqZDdhMnc9PSIsInZhbHVlIjoiRFJBVEg3TG5tdG1kbHRPeGQyRUhHZVV0OFZsSkx3dWNNOUluUGpoT0Vraz0iLCJtYWMiOiJmNjVlNDIyMDlhOTM5OWJkYjgxYWJjNzUyNzhkNDAxZjRjMGZiZDgxYWI3YTBjYzkxMTdjNWIxYWZhNDUyYWIyIiwidGFnIjoiIn0=',NULL,'2025-11-22 08:44:16','2025-11-22 08:44:16'),(7,'bank_transfer',0,'eyJpdiI6ImN6bzhTWFBVQUREYW9KTTdld3RJZUE9PSIsInZhbHVlIjoieGsxQXZ4S2FQcko0R2tFYUVpRTJrbkl4SER0YmJOai9ubCsvT2lJeG9WWFQ0RFdBVS9yQ2JkK1JTV1ZLTTloOFNXbjBnNHhnVGpCTWhqMmdiMG1CSmlMQlU3VUFGVHoyVFlOOE9lZTVXdzY1dGNuRDcrYkdVc3VhRWtmeTdKd3E1U2NMKzJSUWdsOTlEMXc0clRtUXZBPT0iLCJtYWMiOiJkZWEwN2E5MjFhYmM5NDFmN2ZjNTY5ZmJmM2MwNGZiNmU3OWVkZDMwNDlmZDdhM2U0N2M4MDkzZTQxMjhlZjg3IiwidGFnIjoiIn0=',NULL,'2025-11-22 08:44:16','2025-11-22 08:44:16'),(8,'custom',0,'eyJpdiI6Ik5kWStXaW50ck1qR3pqRjFDTk1udkE9PSIsInZhbHVlIjoiM2tsdlo0VDNBSklIaU1BaDV0N0JEQT09IiwibWFjIjoiYWQ0MjhmMmE3MmZkMmM1Mzg2OGU2NDhlNTMxOTIyZjdjMWUwMjhhYWVhMTM0ZDNjZmZlZTg4ZjNlYmQyYmZkOSIsInRhZyI6IiJ9',NULL,'2025-11-22 08:44:16','2025-11-22 08:44:16');
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
INSERT INTO `permissions` VALUES (130,'users.view','web','2025-11-24 19:51:29','2025-11-24 19:51:29'),(131,'users.create','web','2025-11-24 19:51:29','2025-11-24 19:51:29'),(132,'users.edit','web','2025-11-24 19:51:29','2025-11-24 19:51:29'),(133,'users.delete','web','2025-11-24 19:51:29','2025-11-24 19:51:29'),(134,'users.approve','web','2025-11-24 19:51:29','2025-11-24 19:51:29'),(135,'users.suspend','web','2025-11-24 19:51:29','2025-11-24 19:51:29'),(136,'users.export','web','2025-11-24 19:51:29','2025-11-24 19:51:29'),(137,'roles.view','web','2025-11-24 19:51:29','2025-11-24 19:51:29'),(138,'roles.create','web','2025-11-24 19:51:29','2025-11-24 19:51:29'),(139,'roles.edit','web','2025-11-24 19:51:29','2025-11-24 19:51:29'),(140,'roles.delete','web','2025-11-24 19:51:29','2025-11-24 19:51:29'),(141,'permissions.assign','web','2025-11-24 19:51:29','2025-11-24 19:51:29'),(142,'students.view','web','2025-11-24 19:51:29','2025-11-24 19:51:29'),(143,'students.create','web','2025-11-24 19:51:29','2025-11-24 19:51:29'),(144,'students.edit','web','2025-11-24 19:51:29','2025-11-24 19:51:29'),(145,'students.delete','web','2025-11-24 19:51:29','2025-11-24 19:51:29'),(146,'students.enroll','web','2025-11-24 19:51:29','2025-11-24 19:51:29'),(147,'students.transfer','web','2025-11-24 19:51:29','2025-11-24 19:51:29'),(148,'students.graduate','web','2025-11-24 19:51:29','2025-11-24 19:51:29'),(149,'teachers.view','web','2025-11-24 19:51:29','2025-11-24 19:51:29'),(150,'teachers.create','web','2025-11-24 19:51:29','2025-11-24 19:51:29'),(151,'teachers.edit','web','2025-11-24 19:51:29','2025-11-24 19:51:29'),(152,'teachers.delete','web','2025-11-24 19:51:30','2025-11-24 19:51:30'),(153,'teachers.assign','web','2025-11-24 19:51:30','2025-11-24 19:51:30'),(154,'classes.view','web','2025-11-24 19:51:30','2025-11-24 19:51:30'),(155,'classes.create','web','2025-11-24 19:51:30','2025-11-24 19:51:30'),(156,'classes.edit','web','2025-11-24 19:51:30','2025-11-24 19:51:30'),(157,'classes.delete','web','2025-11-24 19:51:30','2025-11-24 19:51:30'),(158,'classes.assign','web','2025-11-24 19:51:30','2025-11-24 19:51:30'),(159,'subjects.view','web','2025-11-24 19:51:30','2025-11-24 19:51:30'),(160,'subjects.create','web','2025-11-24 19:51:30','2025-11-24 19:51:30'),(161,'subjects.edit','web','2025-11-24 19:51:30','2025-11-24 19:51:30'),(162,'subjects.delete','web','2025-11-24 19:51:30','2025-11-24 19:51:30'),(163,'attendance.view','web','2025-11-24 19:51:30','2025-11-24 19:51:30'),(164,'attendance.mark','web','2025-11-24 19:51:30','2025-11-24 19:51:30'),(165,'attendance.edit','web','2025-11-24 19:51:30','2025-11-24 19:51:30'),(166,'attendance.report','web','2025-11-24 19:51:30','2025-11-24 19:51:30'),(167,'grades.view','web','2025-11-24 19:51:30','2025-11-24 19:51:30'),(168,'grades.create','web','2025-11-24 19:51:30','2025-11-24 19:51:30'),(169,'grades.edit','web','2025-11-24 19:51:30','2025-11-24 19:51:30'),(170,'grades.delete','web','2025-11-24 19:51:30','2025-11-24 19:51:30'),(171,'grades.approve','web','2025-11-24 19:51:30','2025-11-24 19:51:30'),(172,'grades.report','web','2025-11-24 19:51:30','2025-11-24 19:51:30'),(173,'assignments.view','web','2025-11-24 19:51:30','2025-11-24 19:51:30'),(174,'assignments.create','web','2025-11-24 19:51:30','2025-11-24 19:51:30'),(175,'assignments.edit','web','2025-11-24 19:51:30','2025-11-24 19:51:30'),(176,'assignments.delete','web','2025-11-24 19:51:30','2025-11-24 19:51:30'),(177,'assignments.grade','web','2025-11-24 19:51:30','2025-11-24 19:51:30'),(178,'exams.view','web','2025-11-24 19:51:30','2025-11-24 19:51:30'),(179,'exams.create','web','2025-11-24 19:51:30','2025-11-24 19:51:30'),(180,'exams.edit','web','2025-11-24 19:51:30','2025-11-24 19:51:30'),(181,'exams.delete','web','2025-11-24 19:51:30','2025-11-24 19:51:30'),(182,'exams.schedule','web','2025-11-24 19:51:30','2025-11-24 19:51:30'),(183,'timetable.view','web','2025-11-24 19:51:30','2025-11-24 19:51:30'),(184,'timetable.create','web','2025-11-24 19:51:30','2025-11-24 19:51:30'),(185,'timetable.edit','web','2025-11-24 19:51:30','2025-11-24 19:51:30'),(186,'timetable.delete','web','2025-11-24 19:51:30','2025-11-24 19:51:30'),(187,'finance.view','web','2025-11-24 19:51:30','2025-11-24 19:51:30'),(188,'finance.create','web','2025-11-24 19:51:30','2025-11-24 19:51:30'),(189,'finance.edit','web','2025-11-24 19:51:30','2025-11-24 19:51:30'),(190,'finance.delete','web','2025-11-24 19:51:30','2025-11-24 19:51:30'),(191,'fees.manage','web','2025-11-24 19:51:30','2025-11-24 19:51:30'),(192,'payments.process','web','2025-11-24 19:51:30','2025-11-24 19:51:30'),(193,'payments.refund','web','2025-11-24 19:51:30','2025-11-24 19:51:30'),(194,'invoices.generate','web','2025-11-24 19:51:30','2025-11-24 19:51:30'),(195,'hr.view','web','2025-11-24 19:51:31','2025-11-24 19:51:31'),(196,'hr.manage','web','2025-11-24 19:51:31','2025-11-24 19:51:31'),(197,'employees.view','web','2025-11-24 19:51:31','2025-11-24 19:51:31'),(198,'employees.create','web','2025-11-24 19:51:31','2025-11-24 19:51:31'),(199,'employees.edit','web','2025-11-24 19:51:31','2025-11-24 19:51:31'),(200,'employees.delete','web','2025-11-24 19:51:31','2025-11-24 19:51:31'),(201,'leave-requests.view','web','2025-11-24 19:51:31','2025-11-24 19:51:31'),(202,'leave-requests.create','web','2025-11-24 19:51:31','2025-11-24 19:51:31'),(203,'leave-requests.approve','web','2025-11-24 19:51:31','2025-11-24 19:51:31'),(204,'leave-requests.reject','web','2025-11-24 19:51:31','2025-11-24 19:51:31'),(205,'pamphlets.view','web','2025-11-24 19:51:31','2025-11-24 19:51:31'),(206,'pamphlets.create','web','2025-11-24 19:51:31','2025-11-24 19:51:31'),(207,'pamphlets.edit','web','2025-11-24 19:51:31','2025-11-24 19:51:31'),(208,'pamphlets.delete','web','2025-11-24 19:51:31','2025-11-24 19:51:31'),(209,'pamphlets.publish','web','2025-11-24 19:51:31','2025-11-24 19:51:31'),(210,'books.view','web','2025-11-24 19:51:31','2025-11-24 19:51:31'),(211,'books.create','web','2025-11-24 19:51:31','2025-11-24 19:51:31'),(212,'books.edit','web','2025-11-24 19:51:31','2025-11-24 19:51:31'),(213,'books.delete','web','2025-11-24 19:51:31','2025-11-24 19:51:31'),(214,'bookstore.view','web','2025-11-24 19:51:31','2025-11-24 19:51:31'),(215,'bookstore.manage','web','2025-11-24 19:51:31','2025-11-24 19:51:31'),(216,'bookstore.orders','web','2025-11-24 19:51:31','2025-11-24 19:51:31'),(217,'bookstore.purchase','web','2025-11-24 19:51:31','2025-11-24 19:51:31'),(218,'library.view','web','2025-11-24 19:51:31','2025-11-24 19:51:31'),(219,'library.manage','web','2025-11-24 19:51:31','2025-11-24 19:51:31'),(220,'library.issue','web','2025-11-24 19:51:31','2025-11-24 19:51:31'),(221,'library.return','web','2025-11-24 19:51:31','2025-11-24 19:51:31'),(222,'reports.view','web','2025-11-24 19:51:31','2025-11-24 19:51:31'),(223,'reports.generate','web','2025-11-24 19:51:32','2025-11-24 19:51:32'),(224,'reports.export','web','2025-11-24 19:51:32','2025-11-24 19:51:32'),(225,'reports.custom','web','2025-11-24 19:51:32','2025-11-24 19:51:32'),(226,'settings.view','web','2025-11-24 19:51:32','2025-11-24 19:51:32'),(227,'settings.edit','web','2025-11-24 19:51:32','2025-11-24 19:51:32'),(228,'settings.general','web','2025-11-24 19:51:32','2025-11-24 19:51:32'),(229,'settings.academic','web','2025-11-24 19:51:32','2025-11-24 19:51:32'),(230,'settings.system','web','2025-11-24 19:51:32','2025-11-24 19:51:32'),(231,'settings.mail','web','2025-11-24 19:51:32','2025-11-24 19:51:32'),(232,'settings.payment','web','2025-11-24 19:51:32','2025-11-24 19:51:32'),(233,'settings.messaging','web','2025-11-24 19:51:32','2025-11-24 19:51:32'),(234,'messages.send','web','2025-11-24 19:51:32','2025-11-24 19:51:32'),(235,'messages.view','web','2025-11-24 19:51:32','2025-11-24 19:51:32'),(236,'announcements.create','web','2025-11-24 19:51:32','2025-11-24 19:51:32'),(237,'announcements.edit','web','2025-11-24 19:51:32','2025-11-24 19:51:32'),(238,'notifications.send','web','2025-11-24 19:51:32','2025-11-24 19:51:32'),(239,'documents.view','web','2025-11-24 19:51:32','2025-11-24 19:51:32'),(240,'documents.upload','web','2025-11-24 19:51:32','2025-11-24 19:51:32'),(241,'documents.download','web','2025-11-24 19:51:32','2025-11-24 19:51:32'),(242,'documents.delete','web','2025-11-24 19:51:32','2025-11-24 19:51:32'),(243,'departments.view','web','2025-11-24 19:51:32','2025-11-24 19:51:32'),(244,'departments.create','web','2025-11-24 19:51:32','2025-11-24 19:51:32'),(245,'departments.edit','web','2025-11-24 19:51:33','2025-11-24 19:51:33'),(246,'departments.delete','web','2025-11-24 19:51:33','2025-11-24 19:51:33'),(247,'positions.view','web','2025-11-24 19:51:33','2025-11-24 19:51:33'),(248,'positions.create','web','2025-11-24 19:51:33','2025-11-24 19:51:33'),(249,'positions.edit','web','2025-11-24 19:51:33','2025-11-24 19:51:33'),(250,'positions.delete','web','2025-11-24 19:51:33','2025-11-24 19:51:33');
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
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `positions`
--

LOCK TABLES `positions` WRITE;
/*!40000 ALTER TABLE `positions` DISABLE KEYS */;
INSERT INTO `positions` VALUES (1,1,'Teacher',NULL,'Teaching staff position','2025-11-24 16:56:02','2025-11-24 16:56:02');
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `quiz_attempts`
--

LOCK TABLES `quiz_attempts` WRITE;
/*!40000 ALTER TABLE `quiz_attempts` DISABLE KEYS */;
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `quiz_questions`
--

LOCK TABLES `quiz_questions` WRITE;
/*!40000 ALTER TABLE `quiz_questions` DISABLE KEYS */;
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `quizzes`
--

LOCK TABLES `quizzes` WRITE;
/*!40000 ALTER TABLE `quizzes` DISABLE KEYS */;
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
INSERT INTO `role_has_permissions` VALUES (1,1),(1,2),(2,1),(2,2),(3,1),(3,2),(4,1),(4,2),(5,1),(5,2),(6,1),(6,2),(7,1),(7,2),(8,1),(8,2),(9,1),(9,2),(10,1),(10,2),(11,1),(11,2),(12,1),(13,1),(13,2),(14,1),(14,2),(15,1),(15,2),(16,1),(16,2),(17,1),(17,2),(18,1),(18,2),(19,1),(20,1),(20,2),(21,1),(21,2),(21,3),(21,5),(21,7),(21,8),(22,1),(22,2),(23,1),(23,2),(23,3),(23,8),(24,1),(24,2),(25,1),(25,2),(26,1),(26,2),(27,1),(27,2),(28,1),(28,2),(28,7),(28,8),(29,1),(29,2),(30,1),(30,2),(31,1),(31,2),(32,1),(32,2),(32,8),(33,1),(33,2),(33,3),(33,4),(33,8),(34,1),(34,2),(34,8),(35,1),(35,2),(35,8),(36,1),(36,2),(37,1),(37,2),(37,3),(38,1),(38,2),(38,3),(38,4),(38,8),(39,1),(39,2),(39,8),(40,1),(40,2),(40,8),(41,1),(41,2),(42,1),(42,2),(42,3),(42,4),(42,5),(42,8),(43,1),(43,2),(43,3),(44,1),(44,2),(44,3),(45,1),(45,2),(45,8),(46,1),(46,2),(46,3),(46,4),(46,5),(46,8),(47,1),(47,2),(47,3),(48,1),(48,2),(48,3),(49,1),(49,2),(50,1),(50,2),(50,8),(51,1),(51,2),(52,1),(52,2),(52,3),(52,4),(53,1),(53,2),(53,3),(54,1),(54,2),(54,3),(55,1),(55,2),(56,1),(56,2),(56,3),(57,1),(57,2),(57,3),(57,4),(58,1),(58,2),(59,1),(59,2),(60,1),(60,2),(61,1),(61,2),(62,1),(62,2),(62,3),(62,4),(62,8),(63,1),(63,2),(63,8),(64,1),(64,2),(64,8),(65,1),(65,2),(66,1),(66,2),(66,5),(66,6),(66,10),(67,1),(67,2),(67,6),(67,10),(68,1),(68,2),(68,6),(68,10),(69,1),(69,2),(70,1),(70,2),(70,6),(70,10),(71,1),(71,2),(71,6),(71,10),(72,1),(72,2),(72,6),(72,10),(73,1),(73,2),(73,5),(73,6),(73,10),(74,1),(74,2),(75,1),(75,2),(76,1),(76,2),(77,1),(77,2),(78,1),(78,2),(79,1),(79,2),(80,1),(80,2),(81,1),(81,2),(82,1),(82,2),(83,1),(83,2),(84,1),(84,2),(84,3),(85,1),(85,2),(86,1),(86,2),(87,1),(87,2),(88,1),(88,2),(89,1),(89,2),(89,3),(89,4),(89,7),(90,1),(90,2),(91,1),(91,2),(92,1),(92,2),(93,1),(93,2),(93,3),(93,4),(93,7),(94,1),(94,2),(95,1),(95,2),(96,1),(96,2),(96,4),(96,7),(97,1),(97,2),(97,4),(97,7),(98,1),(98,2),(98,7),(99,1),(99,2),(99,7),(100,1),(100,2),(100,7),(101,1),(101,2),(101,3),(101,5),(101,6),(101,7),(101,8),(101,9),(101,10),(101,11),(102,1),(102,2),(102,3),(102,6),(102,7),(102,8),(102,10),(103,1),(103,2),(103,6),(103,10),(104,1),(104,2),(104,8),(105,1),(105,2),(106,1),(106,2),(107,1),(107,2),(108,1),(108,2),(109,1),(110,1),(110,2),(111,1),(111,2),(112,1),(112,2),(113,1),(113,2),(113,3),(113,5),(114,1),(114,2),(114,3),(114,4),(114,5),(114,9),(114,11),(115,1),(115,2),(116,1),(116,2),(117,1),(117,2),(118,1),(118,2),(118,3),(118,4),(118,9),(118,11),(119,1),(119,2),(119,3),(120,1),(120,2),(120,3),(120,4),(121,1),(121,2),(122,1),(122,2),(122,8),(123,1),(123,2),(124,1),(124,2),(124,8),(125,1),(125,2),(126,1),(126,2),(127,1),(127,2),(128,1),(128,2),(129,1),(129,2),(130,12),(130,13),(131,12),(131,13),(132,12),(132,13),(133,12),(134,12),(134,13),(135,12),(135,13),(136,12),(136,13),(137,12),(137,13),(138,12),(138,13),(139,12),(139,13),(140,12),(141,12),(141,13),(142,12),(142,13),(142,14),(142,16),(142,18),(142,19),(143,12),(143,13),(144,12),(144,13),(144,14),(144,19),(145,12),(145,13),(146,12),(146,13),(147,12),(147,13),(148,12),(148,13),(149,12),(149,13),(149,18),(149,19),(150,12),(150,13),(151,12),(151,13),(152,12),(152,13),(153,12),(153,13),(153,19),(154,12),(154,13),(154,14),(154,15),(154,19),(155,12),(155,13),(155,19),(156,12),(156,13),(156,19),(157,12),(157,13),(158,12),(158,13),(158,14),(159,12),(159,13),(159,14),(159,15),(159,19),(160,12),(160,13),(160,19),(161,12),(161,13),(161,19),(162,12),(162,13),(163,12),(163,13),(163,14),(163,15),(163,16),(163,19),(164,12),(164,13),(164,14),(165,12),(165,13),(165,14),(166,12),(166,13),(166,19),(167,12),(167,13),(167,14),(167,15),(167,16),(167,19),(168,12),(168,13),(168,14),(169,12),(169,13),(169,14),(170,12),(170,13),(171,12),(171,13),(171,19),(172,12),(172,13),(173,12),(173,13),(173,14),(173,15),(174,12),(174,13),(174,14),(175,12),(175,13),(175,14),(176,12),(176,13),(177,12),(177,13),(177,14),(178,12),(178,13),(178,14),(178,15),(179,12),(179,13),(180,12),(180,13),(181,12),(181,13),(182,12),(182,13),(183,12),(183,13),(183,14),(183,15),(183,19),(184,12),(184,13),(184,19),(185,12),(185,13),(185,19),(186,12),(186,13),(187,12),(187,13),(187,16),(187,17),(187,21),(188,12),(188,13),(188,17),(188,21),(189,12),(189,13),(189,17),(189,21),(190,12),(190,13),(191,12),(191,13),(191,17),(191,21),(192,12),(192,13),(192,17),(192,21),(193,12),(193,13),(193,17),(193,21),(194,12),(194,13),(194,16),(194,17),(194,21),(195,12),(195,13),(196,12),(196,13),(197,12),(197,13),(198,12),(198,13),(199,12),(199,13),(200,12),(200,13),(201,12),(201,13),(202,12),(202,13),(203,12),(203,13),(204,12),(204,13),(205,12),(205,13),(205,14),(206,12),(206,13),(207,12),(207,13),(208,12),(208,13),(209,12),(209,13),(210,12),(210,13),(210,14),(210,15),(210,18),(211,12),(211,13),(212,12),(212,13),(213,12),(213,13),(214,12),(214,13),(214,14),(214,15),(214,18),(215,12),(215,13),(216,12),(216,13),(217,12),(217,13),(217,15),(217,18),(218,12),(218,13),(218,15),(218,18),(219,12),(219,13),(219,18),(220,12),(220,13),(220,18),(221,12),(221,13),(221,18),(222,12),(222,13),(222,14),(222,16),(222,17),(222,18),(222,19),(222,20),(222,21),(222,22),(223,12),(223,13),(223,14),(223,17),(223,18),(223,19),(223,21),(224,12),(224,13),(224,17),(224,21),(225,12),(225,13),(225,19),(226,12),(226,13),(227,12),(227,13),(228,12),(228,13),(229,12),(229,13),(230,12),(231,12),(231,13),(232,12),(232,13),(233,12),(233,13),(234,12),(234,13),(234,14),(234,16),(235,12),(235,13),(235,14),(235,15),(235,16),(235,20),(235,22),(236,12),(236,13),(237,12),(237,13),(238,12),(238,13),(239,12),(239,13),(239,14),(239,15),(239,20),(239,22),(240,12),(240,13),(240,14),(241,12),(241,13),(241,14),(241,15),(242,12),(242,13),(243,12),(243,13),(243,19),(244,12),(244,13),(245,12),(245,13),(245,19),(246,12),(246,13),(247,12),(247,13),(248,12),(248,13),(249,12),(249,13),(250,12),(250,13);
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
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (12,NULL,'Super-Admin','web','2025-11-24 19:51:33','2025-11-24 19:51:33'),(13,NULL,'Admin','web','2025-11-24 19:51:33','2025-11-24 19:51:33'),(14,NULL,'Teacher','web','2025-11-24 19:51:33','2025-11-24 19:51:33'),(15,NULL,'Student','web','2025-11-24 19:51:33','2025-11-24 19:51:33'),(16,NULL,'Parent','web','2025-11-24 19:51:33','2025-11-24 19:51:33'),(17,NULL,'Accountant','web','2025-11-24 19:51:33','2025-11-24 19:51:33'),(18,NULL,'Librarian','web','2025-11-24 19:51:34','2025-11-24 19:51:34'),(19,NULL,'Head-of-Department','web','2025-11-24 19:51:34','2025-11-24 19:51:34'),(20,NULL,'Staff','web','2025-11-24 19:51:34','2025-11-24 19:51:34'),(21,NULL,'Bursar','web','2025-11-24 19:51:34','2025-11-24 19:51:34'),(22,NULL,'Nurse','web','2025-11-24 19:51:34','2025-11-24 19:51:34');
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rooms`
--

LOCK TABLES `rooms` WRITE;
/*!40000 ALTER TABLE `rooms` DISABLE KEYS */;
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `security_audit_logs`
--

LOCK TABLES `security_audit_logs` WRITE;
/*!40000 ALTER TABLE `security_audit_logs` DISABLE KEYS */;
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `student_subject`
--

LOCK TABLES `student_subject` WRITE;
/*!40000 ALTER TABLE `student_subject` DISABLE KEYS */;
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `students`
--

LOCK TABLES `students` WRITE;
/*!40000 ALTER TABLE `students` DISABLE KEYS */;
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
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subject_teacher`
--

LOCK TABLES `subject_teacher` WRITE;
/*!40000 ALTER TABLE `subject_teacher` DISABLE KEYS */;
INSERT INTO `subject_teacher` VALUES (1,1,1,NULL,NULL,'2025-11-24 19:46:29','2025-11-24 19:46:29');
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
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subjects`
--

LOCK TABLES `subjects` WRITE;
/*!40000 ALTER TABLE `subjects` DISABLE KEYS */;
INSERT INTO `subjects` VALUES (1,1,NULL,'Mathematics','MATH101',NULL,'core',3,1,0,40,100,NULL,'2025-11-24 19:46:29','2025-11-24 19:46:29');
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
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `teachers`
--

LOCK TABLES `teachers` WRITE;
/*!40000 ALTER TABLE `teachers` DISABLE KEYS */;
INSERT INTO `teachers` VALUES (1,NULL,'Test Teacher','Test','Teacher','male','1990-01-01',NULL,NULL,'test.teacher@example.com','1234567890',NULL,NULL,NULL,NULL,'Kenya',NULL,'EMP20255912',1,NULL,NULL,NULL,'2023-01-01','full_time','active',NULL,NULL,NULL,NULL,NULL,NULL,'2025-11-24 16:56:02','2025-11-24 16:56:02',NULL);
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `terms`
--

LOCK TABLES `terms` WRITE;
/*!40000 ALTER TABLE `terms` DISABLE KEYS */;
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `timetable_entries`
--

LOCK TABLES `timetable_entries` WRITE;
/*!40000 ALTER TABLE `timetable_entries` DISABLE KEYS */;
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_preferences`
--

LOCK TABLES `user_preferences` WRITE;
/*!40000 ALTER TABLE `user_preferences` DISABLE KEYS */;
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
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,1,'Test Admin',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'test@example.com',NULL,'admin',1,'2025-11-22 08:44:16','$2y$12$zPjLuZEpay2u.OfA/l8OPepjVvwhcWMlI8jFM0N1nriCJWIeu9sHi','approved',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pfsatbBfdIlSmyo8UKoCMrJJTA85ojWFCjD7pfEXm4lH1OS7YyBufhEH7QJL','2025-11-22 08:44:16','2025-11-22 08:48:36');
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `virtual_classes`
--

LOCK TABLES `virtual_classes` WRITE;
/*!40000 ALTER TABLE `virtual_classes` DISABLE KEYS */;
/*!40000 ALTER TABLE `virtual_classes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'tenant_000001'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-12-02 10:12:05
