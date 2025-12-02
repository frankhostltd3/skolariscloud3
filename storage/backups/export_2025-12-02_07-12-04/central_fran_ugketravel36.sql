-- MySQL dump 10.13  Distrib 9.1.0, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: fran_ugketravel36
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
-- Table structure for table `academic_reports`
--

DROP TABLE IF EXISTS `academic_reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `academic_reports` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint unsigned NOT NULL,
  `term_id` bigint unsigned NOT NULL,
  `class_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_marks` double DEFAULT NULL,
  `average_score` double DEFAULT NULL,
  `rank` int DEFAULT NULL,
  `class_teacher_remarks` text COLLATE utf8mb4_unicode_ci,
  `principal_remarks` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `academic_reports_term_id_foreign` (`term_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `academic_reports`
--

LOCK TABLES `academic_reports` WRITE;
/*!40000 ALTER TABLE `academic_reports` DISABLE KEYS */;
/*!40000 ALTER TABLE `academic_reports` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `academic_terms`
--

DROP TABLE IF EXISTS `academic_terms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `academic_terms` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `academic_terms`
--

LOCK TABLES `academic_terms` WRITE;
/*!40000 ALTER TABLE `academic_terms` DISABLE KEYS */;
/*!40000 ALTER TABLE `academic_terms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `billing_plans`
--

DROP TABLE IF EXISTS `billing_plans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `billing_plans` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tagline` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `price_amount` decimal(10,2) DEFAULT NULL,
  `price_display` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `currency` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'USD',
  `billing_period` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `billing_period_label` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cta_label` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Get Started',
  `is_highlighted` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `position` int unsigned NOT NULL DEFAULT '0',
  `features` json DEFAULT NULL,
  `modules` json DEFAULT NULL,
  `limits` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `billing_plans_slug_unique` (`slug`),
  KEY `billing_plans_is_active_position_index` (`is_active`,`position`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `billing_plans`
--

LOCK TABLES `billing_plans` WRITE;
/*!40000 ALTER TABLE `billing_plans` DISABLE KEYS */;
INSERT INTO `billing_plans` VALUES (1,'Starter','starter','Perfect for small schools','Everything you need to get started with school management',80.00,NULL,'USD','semester','/semester','Get Started',0,1,1,'[\"Up to 100 students\", \"Basic attendance tracking\", \"Grade management\", \"Parent portal\", \"Email support\", \"5 GB storage\"]','{\"hr\": false, \"grades\": true, \"hostel\": false, \"classes\": true, \"finance\": false, \"library\": false, \"parents\": true, \"reports\": true, \"students\": true, \"subjects\": true, \"inventory\": false, \"messaging\": false, \"transport\": false, \"api_access\": false, \"attendance\": true, \"online_exams\": false}','{\"users\": 5, \"students\": 100, \"storage_gb\": 5}','2025-12-01 05:38:33','2025-12-01 06:11:59'),(2,'Growth','growth','For growing institutions','Advanced features for expanding schools',129.00,NULL,'USD','month','/month','Start Free Trial',0,1,2,'[\"Up to 500 students\", \"Advanced attendance & biometrics\", \"Online exams & assessments\", \"Financial management\", \"SMS & WhatsApp notifications\", \"Priority support\", \"25 GB storage\"]','{\"hr\": false, \"events\": true, \"grades\": true, \"hostel\": false, \"classes\": true, \"finance\": true, \"library\": false, \"parents\": true, \"reports\": true, \"students\": true, \"subjects\": true, \"inventory\": false, \"messaging\": true, \"transport\": false, \"api_access\": false, \"attendance\": true, \"online_exams\": true}','{\"users\": 20, \"students\": 500, \"storage_gb\": 25}','2025-12-01 05:38:33','2025-12-01 06:08:35'),(3,'Premium','premium','Most Popular','Complete school management solution',189.00,NULL,'USD','month','/month','Start Free Trial',1,1,3,'[\"Up to 2,000 students\", \"All Growth features\", \"Library management\", \"HR & Payroll\", \"Custom domain\", \"API access\", \"Dedicated account manager\", \"100 GB storage\"]','{\"hr\": true, \"events\": true, \"grades\": true, \"hostel\": true, \"classes\": true, \"finance\": true, \"library\": true, \"parents\": true, \"reports\": true, \"students\": true, \"subjects\": true, \"inventory\": true, \"messaging\": true, \"transport\": true, \"api_access\": true, \"attendance\": true, \"online_exams\": true, \"custom_domain\": true}','{\"users\": 50, \"students\": 2000, \"storage_gb\": 100}','2025-12-01 05:38:33','2025-12-01 06:08:35'),(4,'Enterprise','enterprise','For large institutions','Tailored solutions for universities and school networks',NULL,'Custom','USD',NULL,NULL,'Contact Sales',0,1,4,'[\"Unlimited students\", \"All Premium features\", \"Multi-campus support\", \"Custom integrations\", \"On-premise deployment option\", \"SLA guarantee\", \"24/7 phone support\", \"Unlimited storage\"]','{\"hr\": true, \"sso\": true, \"events\": true, \"grades\": true, \"hostel\": true, \"classes\": true, \"finance\": true, \"library\": true, \"parents\": true, \"reports\": true, \"students\": true, \"subjects\": true, \"inventory\": true, \"messaging\": true, \"transport\": true, \"api_access\": true, \"attendance\": true, \"audit_logs\": true, \"multi_campus\": true, \"online_exams\": true, \"custom_domain\": true}','{\"users\": -1, \"students\": -1, \"storage_gb\": -1}','2025-12-01 05:38:33','2025-12-01 06:08:35');
/*!40000 ALTER TABLE `billing_plans` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `books`
--

DROP TABLE IF EXISTS `books`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `books` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `sku` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `author` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `is_published` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `books_sku_unique` (`sku`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `books`
--

LOCK TABLES `books` WRITE;
/*!40000 ALTER TABLE `books` DISABLE KEYS */;
/*!40000 ALTER TABLE `books` ENABLE KEYS */;
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
-- Table structure for table `class_streams`
--

DROP TABLE IF EXISTS `class_streams`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `class_streams` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `class_id` bigint unsigned NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `class_streams_class_id_foreign` (`class_id`)
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
-- Table structure for table `class_subjects`
--

DROP TABLE IF EXISTS `class_subjects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `class_subjects` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `class_id` bigint unsigned NOT NULL,
  `subject_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `class_subjects_class_id_subject_id_unique` (`class_id`,`subject_id`),
  KEY `class_subjects_subject_id_foreign` (`subject_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `class_subjects`
--

LOCK TABLES `class_subjects` WRITE;
/*!40000 ALTER TABLE `class_subjects` DISABLE KEYS */;
/*!40000 ALTER TABLE `class_subjects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `countries`
--

DROP TABLE IF EXISTS `countries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `countries` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `region` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name_translations` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `countries_code_unique` (`code`)
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
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
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
  `school_id` bigint unsigned NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `departments_school_id_index` (`school_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `departments`
--

LOCK TABLES `departments` WRITE;
/*!40000 ALTER TABLE `departments` DISABLE KEYS */;
/*!40000 ALTER TABLE `departments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `discussion_likes`
--

DROP TABLE IF EXISTS `discussion_likes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `discussion_likes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `likeable_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `likeable_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `discussion_likes_user_id_likeable_id_likeable_type_unique` (`user_id`,`likeable_id`,`likeable_type`),
  KEY `discussion_likes_likeable_type_likeable_id_index` (`likeable_type`,`likeable_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `discussion_likes`
--

LOCK TABLES `discussion_likes` WRITE;
/*!40000 ALTER TABLE `discussion_likes` DISABLE KEYS */;
/*!40000 ALTER TABLE `discussion_likes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `discussion_replies`
--

DROP TABLE IF EXISTS `discussion_replies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `discussion_replies` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `discussion_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `parent_id` bigint unsigned DEFAULT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_approved` tinyint(1) NOT NULL DEFAULT '1',
  `is_best_answer` tinyint(1) NOT NULL DEFAULT '0',
  `likes_count` int NOT NULL DEFAULT '0',
  `attachments` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `discussion_replies_user_id_foreign` (`user_id`),
  KEY `discussion_replies_parent_id_foreign` (`parent_id`),
  KEY `discussion_replies_discussion_id_created_at_index` (`discussion_id`,`created_at`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `discussion_replies`
--

LOCK TABLES `discussion_replies` WRITE;
/*!40000 ALTER TABLE `discussion_replies` DISABLE KEYS */;
/*!40000 ALTER TABLE `discussion_replies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `discussions`
--

DROP TABLE IF EXISTS `discussions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `discussions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `teacher_id` bigint unsigned NOT NULL,
  `class_id` bigint unsigned NOT NULL,
  `subject_id` bigint unsigned DEFAULT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('general','question','announcement','poll') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general',
  `is_pinned` tinyint(1) NOT NULL DEFAULT '0',
  `is_locked` tinyint(1) NOT NULL DEFAULT '0',
  `allow_replies` tinyint(1) NOT NULL DEFAULT '1',
  `requires_approval` tinyint(1) NOT NULL DEFAULT '0',
  `views_count` int NOT NULL DEFAULT '0',
  `replies_count` int NOT NULL DEFAULT '0',
  `attachments` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `discussions_subject_id_foreign` (`subject_id`),
  KEY `discussions_class_id_is_pinned_index` (`class_id`,`is_pinned`),
  KEY `discussions_teacher_id_created_at_index` (`teacher_id`,`created_at`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `discussions`
--

LOCK TABLES `discussions` WRITE;
/*!40000 ALTER TABLE `discussions` DISABLE KEYS */;
/*!40000 ALTER TABLE `discussions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `education_levels`
--

DROP TABLE IF EXISTS `education_levels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `education_levels` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_translations` json DEFAULT NULL,
  `country` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `min_year` tinyint unsigned DEFAULT NULL,
  `max_year` tinyint unsigned DEFAULT NULL,
  `order` tinyint unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee_id_settings`
--

LOCK TABLES `employee_id_settings` WRITE;
/*!40000 ALTER TABLE `employee_id_settings` DISABLE KEYS */;
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
  `metadata` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `employees_department_id_foreign` (`department_id`),
  KEY `employees_position_id_foreign` (`position_id`),
  KEY `employees_salary_scale_id_foreign` (`salary_scale_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employees`
--

LOCK TABLES `employees` WRITE;
/*!40000 ALTER TABLE `employees` DISABLE KEYS */;
/*!40000 ALTER TABLE `employees` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fee_assignments`
--

DROP TABLE IF EXISTS `fee_assignments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fee_assignments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `fee_id` bigint unsigned NOT NULL,
  `assignment_type` enum('class','student') COLLATE utf8mb4_unicode_ci NOT NULL,
  `class_id` bigint unsigned DEFAULT NULL,
  `student_id` bigint unsigned DEFAULT NULL,
  `effective_date` date NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fee_assignments_fee_id_foreign` (`fee_id`),
  KEY `fee_assignments_class_id_foreign` (`class_id`),
  KEY `fee_assignments_student_id_foreign` (`student_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fee_assignments`
--

LOCK TABLES `fee_assignments` WRITE;
/*!40000 ALTER TABLE `fee_assignments` DISABLE KEYS */;
/*!40000 ALTER TABLE `fee_assignments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fees`
--

DROP TABLE IF EXISTS `fees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fees` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `amount` decimal(10,2) NOT NULL,
  `category` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `due_date` date DEFAULT NULL,
  `recurring_type` enum('one-time','monthly','yearly','term-based') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'one-time',
  `applicable_to` enum('all','specific_class','specific_student') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'all',
  `class_id` bigint unsigned DEFAULT NULL,
  `student_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fees_class_id_foreign` (`class_id`),
  KEY `fees_student_id_foreign` (`student_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fees`
--

LOCK TABLES `fees` WRITE;
/*!40000 ALTER TABLE `fees` DISABLE KEYS */;
/*!40000 ALTER TABLE `fees` ENABLE KEYS */;
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
  `code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `label` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `min_score` smallint unsigned NOT NULL,
  `max_score` smallint unsigned NOT NULL,
  `order` tinyint unsigned NOT NULL DEFAULT '0',
  `awards` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `grading_bands_grading_scheme_id_foreign` (`grading_scheme_id`)
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
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `country` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `examination_body_id` bigint unsigned DEFAULT NULL,
  `is_current` tinyint(1) NOT NULL DEFAULT '0',
  `meta` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `grading_schemes_examination_body_id_foreign` (`examination_body_id`)
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
-- Table structure for table `health_check_result_history_items`
--

DROP TABLE IF EXISTS `health_check_result_history_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `health_check_result_history_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `check_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `check_label` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notification_message` text COLLATE utf8mb4_unicode_ci,
  `short_summary` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta` json NOT NULL,
  `ended_at` timestamp NOT NULL,
  `batch` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `health_check_result_history_items_created_at_index` (`created_at`),
  KEY `health_check_result_history_items_batch_index` (`batch`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `health_check_result_history_items`
--

LOCK TABLES `health_check_result_history_items` WRITE;
/*!40000 ALTER TABLE `health_check_result_history_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `health_check_result_history_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hero_slides`
--

DROP TABLE IF EXISTS `hero_slides`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `hero_slides` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `image_path` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subtitle` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cta_text` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cta_link` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hero_slides`
--

LOCK TABLES `hero_slides` WRITE;
/*!40000 ALTER TABLE `hero_slides` DISABLE KEYS */;
/*!40000 ALTER TABLE `hero_slides` ENABLE KEYS */;
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
-- Table structure for table `landing_faqs`
--

DROP TABLE IF EXISTS `landing_faqs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `landing_faqs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `question` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `answer` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `landing_faqs`
--

LOCK TABLES `landing_faqs` WRITE;
/*!40000 ALTER TABLE `landing_faqs` DISABLE KEYS */;
INSERT INTO `landing_faqs` VALUES (1,'How quickly can we get started?','You can start using SMATCAMPUS immediately after signup. Our team will help you import your existing data and train your staff within days.',1,1,'2025-12-01 08:11:30','2025-12-01 08:11:30'),(2,'Is my data secure?','Absolutely! We use bank-level encryption, regular backups, and comply with international data protection standards including GDPR.',2,1,'2025-12-01 08:11:30','2025-12-01 08:11:30'),(3,'Can I customize the system for my school?','Yes! Our system is highly customizable. You can configure workflows, reports, and even request custom features for Enterprise plans.',3,1,'2025-12-01 08:11:30','2025-12-01 08:11:30'),(4,'What kind of support do you offer?','We provide email support for all plans, priority support for Professional, and 24/7 dedicated support for Enterprise customers.',4,1,'2025-12-01 08:11:30','2025-12-01 08:11:30'),(5,'Is there a free trial?','Yes! We offer a 14-day free trial with full access to all features. No credit card required to start.',5,1,'2025-12-01 08:11:30','2025-12-01 08:11:30');
/*!40000 ALTER TABLE `landing_faqs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `landing_features`
--

DROP TABLE IF EXISTS `landing_features`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `landing_features` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon_color` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `icon_bg_color` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `landing_features`
--

LOCK TABLES `landing_features` WRITE;
/*!40000 ALTER TABLE `landing_features` DISABLE KEYS */;
INSERT INTO `landing_features` VALUES (1,'Student Management','Comprehensive student profiles, enrollment tracking, and academic records management.','bi-people-fill','text-primary','rgba(79, 70, 229, 0.1)',1,1,'2025-12-01 07:30:53','2025-12-01 07:30:53'),(2,'Attendance & Timetable','Digital attendance tracking and smart timetable generation with conflict detection.','bi-calendar-check','var(--secondary-color)','rgba(6, 182, 212, 0.1)',2,1,'2025-12-01 07:30:53','2025-12-01 07:30:53'),(3,'Academic Management','Grade books, report cards, and comprehensive academic performance analytics.','bi-journal-text','var(--accent-color)','rgba(245, 158, 11, 0.15)',3,1,'2025-12-01 07:30:53','2025-12-01 07:30:53'),(4,'Fee Management','Automated fee collection, receipts, and financial reporting made simple.','bi-cash-stack','text-primary','rgba(79, 70, 229, 0.1)',4,1,'2025-12-01 07:30:53','2025-12-01 07:30:53'),(5,'Communication Hub','SMS, email, and in-app messaging to keep everyone connected.','bi-chat-dots','var(--secondary-color)','rgba(6, 182, 212, 0.1)',5,1,'2025-12-01 07:30:53','2025-12-01 07:30:53'),(6,'Analytics & Reports','Insightful dashboards and customizable reports for data-driven decisions.','bi-graph-up','var(--accent-color)','rgba(245, 158, 11, 0.15)',6,1,'2025-12-01 07:30:53','2025-12-01 07:30:53');
/*!40000 ALTER TABLE `landing_features` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `landing_pages`
--

DROP TABLE IF EXISTS `landing_pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `landing_pages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `meta_title` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_description` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_published` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `landing_pages_slug_unique` (`slug`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `landing_pages`
--

LOCK TABLES `landing_pages` WRITE;
/*!40000 ALTER TABLE `landing_pages` DISABLE KEYS */;
/*!40000 ALTER TABLE `landing_pages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `landing_sections`
--

DROP TABLE IF EXISTS `landing_sections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `landing_sections` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `component` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `settings` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `landing_sections`
--

LOCK TABLES `landing_sections` WRITE;
/*!40000 ALTER TABLE `landing_sections` DISABLE KEYS */;
INSERT INTO `landing_sections` VALUES (1,'Hero Section','landing.hero',1,1,NULL,'2025-12-01 08:11:30','2025-12-01 08:11:30'),(2,'Stats Section','landing.stats',2,1,NULL,'2025-12-01 08:11:30','2025-12-01 08:11:30'),(3,'Features Section','landing.features',3,1,NULL,'2025-12-01 08:11:30','2025-12-01 08:11:30'),(4,'Pricing Section','landing.pricing',4,1,NULL,'2025-12-01 08:11:30','2025-12-01 08:11:30'),(5,'Testimonials Section','landing.testimonials',5,1,NULL,'2025-12-01 08:11:30','2025-12-01 08:11:30'),(6,'FAQ Section','landing.faq',6,1,NULL,'2025-12-01 08:11:30','2025-12-01 08:11:30'),(7,'CTA Section','landing.cta',7,1,NULL,'2025-12-01 08:11:30','2025-12-01 08:11:30');
/*!40000 ALTER TABLE `landing_sections` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `landing_stats`
--

DROP TABLE IF EXISTS `landing_stats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `landing_stats` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `value` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `label` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `landing_stats`
--

LOCK TABLES `landing_stats` WRITE;
/*!40000 ALTER TABLE `landing_stats` DISABLE KEYS */;
INSERT INTO `landing_stats` VALUES (1,'500+','Schools Trust Us',NULL,1,1,'2025-12-01 08:11:30','2025-12-01 08:11:30'),(2,'50K+','Active Students',NULL,2,1,'2025-12-01 08:11:30','2025-12-01 08:11:30'),(3,'99.9%','Uptime Guaranteed',NULL,3,1,'2025-12-01 08:11:30','2025-12-01 08:11:30'),(4,'4.9/5','Customer Rating',NULL,4,1,'2025-12-01 08:11:30','2025-12-01 08:11:30');
/*!40000 ALTER TABLE `landing_stats` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `landing_testimonials`
--

DROP TABLE IF EXISTS `landing_testimonials`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `landing_testimonials` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `rating` int NOT NULL DEFAULT '5',
  `avatar_url` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `landing_testimonials`
--

LOCK TABLES `landing_testimonials` WRITE;
/*!40000 ALTER TABLE `landing_testimonials` DISABLE KEYS */;
INSERT INTO `landing_testimonials` VALUES (1,'Jane Doe','Principal, Greenwood High','\"SMATCAMPUS has revolutionized how we manage our school. The interface is intuitive and the support team is exceptional.\"',5,NULL,1,1,'2025-12-01 08:11:30','2025-12-01 08:11:30'),(2,'Michael Smith','Administrator, Valley School','\"The automation features saved us countless hours. Parent communication has never been easier!\"',5,NULL,2,1,'2025-12-01 08:11:30','2025-12-01 08:11:30'),(3,'Sarah Johnson','Director, Riverside Academy','\"Outstanding platform! The analytics help us make informed decisions about our academic programs.\"',5,NULL,3,1,'2025-12-01 08:11:30','2025-12-01 08:11:30');
/*!40000 ALTER TABLE `landing_testimonials` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `landlord_audit_logs`
--

DROP TABLE IF EXISTS `landlord_audit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `landlord_audit_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `action` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `context` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `landlord_audit_logs_action_created_at_index` (`action`,`created_at`),
  KEY `landlord_audit_logs_user_id_index` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `landlord_audit_logs`
--

LOCK TABLES `landlord_audit_logs` WRITE;
/*!40000 ALTER TABLE `landlord_audit_logs` DISABLE KEYS */;
INSERT INTO `landlord_audit_logs` VALUES (1,2,'landlord_login','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0','{\"email\": \"frankhostltd3@gmail.com\"}','2025-11-30 06:05:25','2025-11-30 06:05:25'),(2,2,'landlord_logout','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0',NULL,'2025-11-30 06:09:54','2025-11-30 06:09:54'),(3,2,'landlord_logout','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0',NULL,'2025-11-30 06:12:32','2025-11-30 06:12:32'),(4,2,'landlord_logout','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0',NULL,'2025-11-30 06:14:47','2025-11-30 06:14:47'),(5,2,'landlord_logout','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0',NULL,'2025-11-30 06:20:49','2025-11-30 06:20:49'),(6,2,'landlord_logout','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0',NULL,'2025-11-30 06:23:42','2025-11-30 06:23:42'),(7,2,'landlord_login','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0','{\"email\": \"frankhostltd3@gmail.com\"}','2025-11-30 06:24:16','2025-11-30 06:24:16'),(8,2,'landlord_logout','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0',NULL,'2025-11-30 06:26:28','2025-11-30 06:26:28'),(9,2,'landlord_login','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0','{\"email\": \"frankhostltd3@gmail.com\"}','2025-11-30 06:27:06','2025-11-30 06:27:06'),(10,2,'landlord_logout','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0',NULL,'2025-11-30 06:31:21','2025-11-30 06:31:21'),(11,2,'landlord_login','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0','{\"email\": \"frankhostltd3@gmail.com\"}','2025-11-30 06:31:47','2025-11-30 06:31:47'),(12,2,'landlord_logout','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0',NULL,'2025-11-30 06:41:43','2025-11-30 06:41:43'),(13,2,'landlord_login','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0','{\"email\": \"frankhostltd3@gmail.com\"}','2025-11-30 06:42:16','2025-11-30 06:42:16'),(14,2,'landlord_logout','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0',NULL,'2025-11-30 07:04:17','2025-11-30 07:04:17'),(15,2,'landlord_login','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0','{\"email\": \"frankhostltd3@gmail.com\"}','2025-11-30 07:04:41','2025-11-30 07:04:41'),(16,2,'landlord_login','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0','{\"email\": \"frankhostltd3@gmail.com\"}','2025-11-30 07:27:07','2025-11-30 07:27:07'),(17,2,'landlord_logout','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0',NULL,'2025-11-30 07:45:25','2025-11-30 07:45:25'),(18,2,'landlord_login','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0','{\"email\": \"frankhostltd3@gmail.com\"}','2025-11-30 07:46:12','2025-11-30 07:46:12'),(19,2,'landlord_login','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0','{\"email\": \"frankhostltd3@gmail.com\"}','2025-11-30 09:23:43','2025-11-30 09:23:43'),(20,2,'landlord_logout','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0',NULL,'2025-11-30 09:38:17','2025-11-30 09:38:17'),(21,2,'landlord_login','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0','{\"email\": \"frankhostltd3@gmail.com\"}','2025-11-30 09:38:37','2025-11-30 09:38:37'),(22,2,'landlord_logout','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0',NULL,'2025-11-30 11:35:29','2025-11-30 11:35:29'),(23,2,'landlord_login','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0','{\"email\": \"frankhostltd3@gmail.com\"}','2025-11-30 11:35:45','2025-11-30 11:35:45'),(24,2,'landlord_logout','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0',NULL,'2025-11-30 12:00:41','2025-11-30 12:00:41'),(25,2,'landlord_login','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0','{\"email\": \"frankhostltd3@gmail.com\"}','2025-11-30 12:01:29','2025-11-30 12:01:29'),(26,2,'landlord_logout','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0',NULL,'2025-11-30 12:05:38','2025-11-30 12:05:38'),(27,2,'landlord_login','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0','{\"email\": \"frankhostltd3@gmail.com\"}','2025-11-30 12:06:10','2025-11-30 12:06:10'),(28,2,'landlord_logout','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0',NULL,'2025-11-30 12:16:34','2025-11-30 12:16:34'),(29,2,'landlord_login','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0','{\"email\": \"frankhostltd3@gmail.com\"}','2025-11-30 12:17:00','2025-11-30 12:17:00'),(30,2,'landlord_login','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','{\"email\": \"frankhostltd3@gmail.com\"}','2025-12-01 05:02:06','2025-12-01 05:02:06'),(31,2,'landlord_login','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','{\"email\": \"frankhostltd3@gmail.com\"}','2025-12-01 05:27:36','2025-12-01 05:27:36'),(32,2,'landlord_login','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','{\"email\": \"frankhostltd3@gmail.com\"}','2025-12-01 05:33:01','2025-12-01 05:33:01'),(33,2,'landlord_logout','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36',NULL,'2025-12-01 05:33:24','2025-12-01 05:33:24'),(34,3,'landlord_login','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','{\"email\": \"admin@landlord.local\"}','2025-12-01 05:34:01','2025-12-01 05:34:01'),(35,3,'landlord_logout','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36',NULL,'2025-12-01 05:44:27','2025-12-01 05:44:27'),(36,2,'landlord_login','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','{\"email\": \"frankhostltd3@gmail.com\"}','2025-12-01 05:44:32','2025-12-01 05:44:32'),(37,2,'landlord_login','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','{\"email\": \"frankhostltd3@gmail.com\"}','2025-12-01 08:35:52','2025-12-01 08:35:52'),(38,2,'landlord_login','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','{\"email\": \"frankhostltd3@gmail.com\"}','2025-12-01 16:31:02','2025-12-01 16:31:02');
/*!40000 ALTER TABLE `landlord_audit_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `landlord_dunning_policies`
--

DROP TABLE IF EXISTS `landlord_dunning_policies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `landlord_dunning_policies` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Default Policy',
  `warning_threshold_days` int unsigned NOT NULL DEFAULT '5',
  `suspension_grace_days` int unsigned NOT NULL DEFAULT '7',
  `termination_grace_days` int unsigned NOT NULL DEFAULT '30',
  `reminder_windows` json DEFAULT NULL,
  `late_fee_percent` decimal(5,2) DEFAULT NULL,
  `late_fee_flat` decimal(10,2) DEFAULT NULL,
  `warning_channels` json DEFAULT NULL,
  `suspension_channels` json DEFAULT NULL,
  `termination_channels` json DEFAULT NULL,
  `warning_recipients` json DEFAULT NULL,
  `suspension_recipients` json DEFAULT NULL,
  `termination_recipients` json DEFAULT NULL,
  `warning_phones` json DEFAULT NULL,
  `suspension_phones` json DEFAULT NULL,
  `termination_phones` json DEFAULT NULL,
  `warning_webhooks` json DEFAULT NULL,
  `suspension_webhooks` json DEFAULT NULL,
  `termination_webhooks` json DEFAULT NULL,
  `warning_slack_webhooks` json DEFAULT NULL,
  `suspension_slack_webhooks` json DEFAULT NULL,
  `termination_slack_webhooks` json DEFAULT NULL,
  `templates` json DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `landlord_dunning_policies`
--

LOCK TABLES `landlord_dunning_policies` WRITE;
/*!40000 ALTER TABLE `landlord_dunning_policies` DISABLE KEYS */;
INSERT INTO `landlord_dunning_policies` VALUES (1,'Default Policy',5,7,30,'[-7, -3, -1, 0, 3]',NULL,NULL,'[\"mail\"]','[\"mail\"]','[\"mail\"]','[]','[]','[]',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'[]',1,'2025-11-30 13:25:56','2025-11-30 13:25:56');
/*!40000 ALTER TABLE `landlord_dunning_policies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `landlord_invoice_items`
--

DROP TABLE IF EXISTS `landlord_invoice_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `landlord_invoice_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `landlord_invoice_id` bigint unsigned NOT NULL,
  `line_type` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'service',
  `description` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quantity` int unsigned NOT NULL DEFAULT '1',
  `unit_price` decimal(12,2) NOT NULL DEFAULT '0.00',
  `line_total` decimal(12,2) NOT NULL DEFAULT '0.00',
  `metadata` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `landlord_invoice_items_landlord_invoice_id_foreign` (`landlord_invoice_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `landlord_invoice_items`
--

LOCK TABLES `landlord_invoice_items` WRITE;
/*!40000 ALTER TABLE `landlord_invoice_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `landlord_invoice_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `landlord_invoices`
--

DROP TABLE IF EXISTS `landlord_invoices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `landlord_invoices` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `invoice_number` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tenant_id` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tenant_name_snapshot` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tenant_plan_snapshot` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `auto_generated` tinyint(1) NOT NULL DEFAULT '0',
  `issued_at` date DEFAULT NULL,
  `due_at` date DEFAULT NULL,
  `period_start` date DEFAULT NULL,
  `period_end` date DEFAULT NULL,
  `subtotal` decimal(12,2) NOT NULL DEFAULT '0.00',
  `tax_total` decimal(12,2) NOT NULL DEFAULT '0.00',
  `discount_total` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total` decimal(12,2) NOT NULL DEFAULT '0.00',
  `balance_due` decimal(12,2) NOT NULL DEFAULT '0.00',
  `warning_level` int NOT NULL DEFAULT '0',
  `last_warning_sent_at` timestamp NULL DEFAULT NULL,
  `suspension_at` timestamp NULL DEFAULT NULL,
  `termination_at` timestamp NULL DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `metadata` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `landlord_invoices_invoice_number_unique` (`invoice_number`),
  KEY `landlord_invoices_tenant_id_index` (`tenant_id`),
  KEY `landlord_invoices_status_index` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `landlord_invoices`
--

LOCK TABLES `landlord_invoices` WRITE;
/*!40000 ALTER TABLE `landlord_invoices` DISABLE KEYS */;
/*!40000 ALTER TABLE `landlord_invoices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `landlord_notifications`
--

DROP TABLE IF EXISTS `landlord_notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `landlord_notifications` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_by` bigint unsigned DEFAULT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `channel` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'system',
  `audience` json DEFAULT NULL,
  `meta` json DEFAULT NULL,
  `scheduled_at` timestamp NULL DEFAULT NULL,
  `sent_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `landlord_notifications_channel_scheduled_at_index` (`channel`,`scheduled_at`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `landlord_notifications`
--

LOCK TABLES `landlord_notifications` WRITE;
/*!40000 ALTER TABLE `landlord_notifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `landlord_notifications` ENABLE KEYS */;
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mail_settings`
--

LOCK TABLES `mail_settings` WRITE;
/*!40000 ALTER TABLE `mail_settings` DISABLE KEYS */;
/*!40000 ALTER TABLE `mail_settings` ENABLE KEYS */;
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
  `config` text COLLATE utf8mb4_unicode_ci,
  `meta` text COLLATE utf8mb4_unicode_ci,
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
INSERT INTO `messaging_channel_settings` VALUES (1,'sms','twilio',0,'eyJpdiI6InBxb1A0aHZUT0YwNUVna1B4UDltQ3c9PSIsInZhbHVlIjoiWHJpdFRZMnlGMytaSVlWVnpGeVU0QT09IiwibWFjIjoiN2YzZWFlZGVjMmRlZTRkNDQ4ZmVjZDBkYTRlOGMzNTk5YzY2MjU4YzBiMzc2Y2YwMGVlNzcxY2IyMWU4ZTU1NSIsInRhZyI6IiJ9',NULL,'2025-11-22 08:44:39','2025-11-22 08:44:39'),(2,'sms','vonage',0,'eyJpdiI6InduYkFpUTFkaENTekl3S0wwR0Vnc3c9PSIsInZhbHVlIjoidVgzaEUzMm1LdkhycTFsZlVWb1JoUT09IiwibWFjIjoiOWUzYmViYjk4OWJiZGI2NmQzY2Y4OThiZGEyZjA3MWQ2NTJmNTBmNDJjZjljMjNhNjZlZmJkZGFhOGMyYmIwNyIsInRhZyI6IiJ9',NULL,'2025-11-22 08:44:39','2025-11-22 08:44:39'),(3,'sms','africastalking',0,'eyJpdiI6Iks0eGRNNjVONjBsQ2FsNDd5Rm9WT2c9PSIsInZhbHVlIjoiTUlRVEh2OG1GdERtdXdkS2Q0RkVjZz09IiwibWFjIjoiOTQ3YjYzMmNmNjhmZjcyMjZjYWVmNWQ1YzdiMWQ4OTUwYmJiMDJlNDM0MDEzOTI5NTBlNzA4MGFhYjE4M2U0NCIsInRhZyI6IiJ9',NULL,'2025-11-22 08:44:39','2025-11-22 08:44:39'),(4,'sms','custom',0,'eyJpdiI6Ilhsc3lPNjFSUE9taFVOOUVGcW50Y2c9PSIsInZhbHVlIjoicENZL25ueEJIbVQrTHBDT1ZmdzNpUT09IiwibWFjIjoiNDUwYWVlYmVmYTYxMDZkYWQwZmZiYzI3MTk1NTk2YTg3MGQxMTk2ZTAwZjg5NmZlYjE0MDZhYzMxYmIyNjYzMCIsInRhZyI6IiJ9',NULL,'2025-11-22 08:44:39','2025-11-22 08:44:39'),(5,'whatsapp','twilio_whatsapp',0,'eyJpdiI6Ii9UMXFqRlNQbGF2WjZONWV1c0MwYVE9PSIsInZhbHVlIjoiUnpIL0NiRDhZVnRqNFB3Q3lnK0RhZz09IiwibWFjIjoiNDRlY2FjZjQ5OGRmYTE1MWJjZGY0MWE0NTViNGZiZTgzMzE2ZjQ3Y2ZiOWMxZGVkODcwZWE0OWFkNDAzYmJiZiIsInRhZyI6IiJ9',NULL,'2025-11-22 08:44:39','2025-11-22 08:44:39'),(6,'whatsapp','meta_cloud',0,'eyJpdiI6IndidlNCcWl1R21UdVRKR3lEWDF3b1E9PSIsInZhbHVlIjoialQzYkszU2VxcjB2UWJJam1HaXFHQT09IiwibWFjIjoiNmFkZDViMGM3MTkxYWYxYzM3NWNkOTA5NjNmZTM0YThjMjIzOGRkNWM0MWZjOTBkNDczMmVkOTgxM2VjYzk4MiIsInRhZyI6IiJ9',NULL,'2025-11-22 08:44:39','2025-11-22 08:44:39'),(7,'whatsapp','custom',0,'eyJpdiI6InZlNk5vTkVoTFZHN0t0SFBlQjdwZGc9PSIsInZhbHVlIjoidGY2dXJsaXJHaGJJSVNvTTM1NktaQT09IiwibWFjIjoiZTQ1M2FiMDJkODBiNTAzZDkzMTdhNDMzM2FiNjk4ZDZiYWE4MGM3MWI0NzEzMDc2MTUzYmMwNTI5OGExOTMwNyIsInRhZyI6IiJ9',NULL,'2025-11-22 08:44:39','2025-11-22 08:44:39'),(8,'telegram','telegram_bot',0,'eyJpdiI6IjYrYzRleXh0U3ZGbTdTZDRYajBpMEE9PSIsInZhbHVlIjoiOE54S0Z2ZWM5cHJRSE16TjZBRVJlZklsUHBNSnRDclBiUUdZakM2UkExaz0iLCJtYWMiOiJlMGRjMWEyNjIzMTU0ZjBlMjk0NGE0NzM5YzhhYTU0NzYzMzIwN2QyOWYxNmFlZjhiZDNkN2MyM2JjNmQ1N2UyIiwidGFnIjoiIn0=',NULL,'2025-11-22 08:44:39','2025-11-22 08:44:39'),(9,'telegram','custom',0,'eyJpdiI6InZSaU1BRDFqWXU1NUJSRll0Z3NHcFE9PSIsInZhbHVlIjoidjkyNVFjWjZZcFZPNWlMU0wwbjB4QT09IiwibWFjIjoiMzkyNjY3MjFhNTk1YmMwNTg5MzgyNDg5MmQ4ODVlZmY3ODk0ZjA1Mzg0M2JjMDY5MTI5MGE4ODc2MzkyYjgxMyIsInRhZyI6IiJ9',NULL,'2025-11-22 08:44:39','2025-11-22 08:44:39');
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
) ENGINE=MyISAM AUTO_INCREMENT=91 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2025_09_29_163018_add_profile_fields_to_users_table',1),(5,'2025_10_01_000001_create_landlord_dunning_policies_table',1),(6,'2025_10_01_000001_create_landlord_invoices_table',1),(7,'2025_10_01_000002_create_landlord_invoice_items_table',1),(8,'2025_10_01_120000_create_landlord_audit_logs_table',1),(9,'2025_10_01_121000_create_landlord_notifications_table',1),(10,'2025_10_03_190000_add_password_security_fields_to_users_table',1),(11,'2025_10_03_190309_add_two_factor_fields_to_users_table',1),(12,'2025_10_09_130458_add_last_activity_at_to_users_table',1),(13,'2025_11_13_000000_create_schools_table',1),(14,'2025_11_13_000001_add_user_type_to_users_table',1),(15,'2025_11_13_000002_add_school_id_to_users_table',1),(16,'2025_11_13_000003_create_school_user_invitations_table',1),(17,'2025_11_13_120000_add_subdomain_to_schools_table',1),(18,'2025_11_13_130000_create_mail_settings_table',1),(19,'2025_11_13_131500_update_mail_settings_to_global',1),(20,'2025_11_13_193540_add_approval_fields_to_users_table',1),(21,'2025_11_13_193545_add_approval_fields_to_users_table',1),(22,'2025_11_13_195522_create_departments_table',1),(23,'2025_11_13_195523_create_positions_table',1),(24,'2025_11_15_090000_create_payment_gateway_settings_table',1),(25,'2025_11_15_110100_create_messaging_channel_settings_table',1),(26,'2025_11_15_120000_create_settings_table',1),(27,'2025_11_15_140504_create_settings_table',1),(28,'2025_11_15_152429_add_two_factor_columns_to_users_table',1),(29,'2025_11_15_152632_add_two_factor_columns_to_users_table',1),(30,'2025_11_15_165413_create_currencies_table',1),(31,'2025_01_01_000000_create_academic_reports_tables',2),(32,'2025_11_17_021154_create_permission_tables',3),(33,'2025_12_01_083725_create_billing_plans_table',4),(34,'2025_12_01_090606_add_modules_to_billing_plans_table',5),(35,'2025_12_01_091623_create_hero_slides_table',6),(36,'2025_12_01_102849_create_landing_features_table',7),(37,'2025_12_01_110000_create_landing_cms_tables',8),(38,'2025_12_01_120000_create_payment_gateway_configs_table',9),(39,'2025_11_15_171007_add_exchange_rate_metadata_to_currencies_table',10),(40,'2025_11_15_180000_create_sessions_table',10),(41,'2025_11_17_021154_add_admin_notes_to_orders_table',10),(42,'2025_11_17_021154_add_band_fields_to_grades',10),(43,'2025_11_17_021154_add_capacity_teacher_to_classes',10),(44,'2025_11_17_021154_add_capacity_to_class_streams',10),(45,'2025_11_17_021154_add_days_requested_to_leave_requests_table',10),(46,'2025_11_17_021154_add_education_level_id_to_subjects',10),(47,'2025_11_17_021154_add_employee_number_to_employees_table',10),(48,'2025_11_17_021154_add_employee_type_to_employees_table',10),(49,'2025_11_17_021154_add_featured_to_books_and_pamphlets',10),(50,'2025_11_17_021154_add_grading_scheme_id_to_education_levels',10),(51,'2025_11_17_021154_add_grading_scheme_id_to_terms',10),(52,'2025_11_17_021154_add_name_translations_to_examination_bodies_table',10),(53,'2025_11_17_021154_add_payment_fields_to_orders_table',10),(54,'2025_11_17_021154_add_soft_deletes_to_virtual_class_attendances',10),(55,'2025_11_17_021154_add_unique_index_on_classes_class_teacher_id',10),(56,'2025_11_17_021154_create_books_table',10),(57,'2025_11_17_021154_create_cache_tables',10),(58,'2025_11_17_021154_create_class_streams_table',10),(59,'2025_11_17_021154_create_class_subjects_table',10),(60,'2025_11_17_021154_create_countries_table',10),(61,'2025_11_17_021154_create_currencies_table',10),(62,'2025_11_17_021154_create_departments_table',10),(63,'2025_11_17_021154_create_discussions_table',10),(64,'2025_11_17_021154_create_education_levels_table',10),(65,'2025_11_17_021154_create_employee_id_settings_table',10),(66,'2025_11_17_021154_create_employees_table',10),(67,'2025_11_17_021154_create_examination_bodies_table',10),(68,'2025_11_17_021154_create_fee_assignments_table',10),(69,'2025_11_17_021154_create_fees_table',10),(70,'2025_11_17_021154_create_grading_bands_table',10),(71,'2025_11_17_021154_create_grading_schemes_table',10),(72,'2025_11_17_021154_create_orders_table',10),(73,'2025_11_17_021154_create_pamphlets_table',10),(74,'2025_11_17_021154_create_payment_events_table',10),(75,'2025_11_17_021154_create_payroll_settings_table',10),(76,'2025_11_17_021154_create_platform_integrations_table',10),(77,'2025_11_17_021154_create_positions_table',10),(78,'2025_11_17_021154_create_queue_tables',10),(79,'2025_11_17_021154_create_salary_scales_table',10),(80,'2025_11_17_021154_create_schools_table',10),(81,'2025_11_17_021154_create_sessions_table',10),(82,'2025_11_17_021154_create_settings_table',10),(83,'2025_11_17_021154_create_terms_table',10),(84,'2025_11_17_021154_create_timetable_constraints_table',10),(85,'2025_11_17_021154_create_users_table',10),(86,'2025_11_17_021154_extend_education_levels_with_translations_and_range',10),(87,'2025_11_17_021154_update_payroll_settings_table_structure',10),(88,'2025_11_17_021155_add_bookstore_fields_to_library_books_table',10),(89,'2025_12_01_194204_create_health_tables',11),(90,'2025_11_17_021155_add_comprehensive_fields_to_grades_table',12);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model_has_permissions`
--

DROP TABLE IF EXISTS `model_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `model_type` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  `tenant_id` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`tenant_id`,`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  KEY `model_has_permissions_team_foreign_key_index` (`tenant_id`),
  KEY `model_has_permissions_permission_id_foreign` (`permission_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_has_permissions`
--

LOCK TABLES `model_has_permissions` WRITE;
/*!40000 ALTER TABLE `model_has_permissions` DISABLE KEYS */;
INSERT INTO `model_has_permissions` VALUES (1,'App\\Models\\LandlordUser',2,'landlord'),(1,'App\\Models\\User',2,'landlord'),(1,'App\\Models\\LandlordUser',3,'landlord');
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
  `model_type` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  `tenant_id` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`tenant_id`,`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  KEY `model_has_roles_team_foreign_key_index` (`tenant_id`),
  KEY `model_has_roles_role_id_foreign` (`role_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_has_roles`
--

LOCK TABLES `model_has_roles` WRITE;
/*!40000 ALTER TABLE `model_has_roles` DISABLE KEYS */;
INSERT INTO `model_has_roles` VALUES (1,'App\\Models\\LandlordUser',2,'landlord'),(2,'App\\Models\\LandlordUser',2,'landlord'),(2,'App\\Models\\User',2,'landlord'),(3,'App\\Models\\LandlordUser',2,'landlord'),(3,'App\\Models\\User',2,'landlord'),(3,'App\\Models\\LandlordUser',3,'landlord'),(6,'App\\Models\\LandlordUser',2,'landlord'),(6,'App\\Models\\LandlordUser',3,'landlord');
/*!40000 ALTER TABLE `model_has_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `item_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `item_id` bigint unsigned NOT NULL,
  `item_title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `buyer_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `buyer_email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pamphlets`
--

DROP TABLE IF EXISTS `pamphlets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pamphlets` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `sku` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `is_published` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pamphlets_sku_unique` (`sku`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pamphlets`
--

LOCK TABLES `pamphlets` WRITE;
/*!40000 ALTER TABLE `pamphlets` DISABLE KEYS */;
/*!40000 ALTER TABLE `pamphlets` ENABLE KEYS */;
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
-- Table structure for table `payment_events`
--

DROP TABLE IF EXISTS `payment_events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payment_events` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint unsigned NOT NULL,
  `provider` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `event_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payload` json NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payment_events_order_id_foreign` (`order_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payment_events`
--

LOCK TABLES `payment_events` WRITE;
/*!40000 ALTER TABLE `payment_events` DISABLE KEYS */;
/*!40000 ALTER TABLE `payment_events` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payment_gateway_configs`
--

DROP TABLE IF EXISTS `payment_gateway_configs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payment_gateway_configs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `gateway` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `context` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'landlord',
  `display_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logo` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  `is_custom` tinyint(1) NOT NULL DEFAULT '0',
  `is_test_mode` tinyint(1) NOT NULL DEFAULT '1',
  `credentials` text COLLATE utf8mb4_unicode_ci,
  `settings` json DEFAULT NULL,
  `custom_config` json DEFAULT NULL,
  `supported_currencies` json DEFAULT NULL,
  `display_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payment_gateway_configs_gateway_context_unique` (`gateway`,`context`),
  KEY `payment_gateway_configs_gateway_index` (`gateway`),
  KEY `payment_gateway_configs_context_index` (`context`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payment_gateway_configs`
--

LOCK TABLES `payment_gateway_configs` WRITE;
/*!40000 ALTER TABLE `payment_gateway_configs` DISABLE KEYS */;
/*!40000 ALTER TABLE `payment_gateway_configs` ENABLE KEYS */;
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
  `config` text COLLATE utf8mb4_unicode_ci,
  `meta` text COLLATE utf8mb4_unicode_ci,
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
INSERT INTO `payment_gateway_settings` VALUES (1,'paypal',0,'eyJpdiI6IktweW10cjJQNWpoZUhMY0MzQ0oyNlE9PSIsInZhbHVlIjoiQXZPUUU0aHcwVzI5eXgxb1N5VTJDM2dKMmphaEJ4d0N0L1R2WTFXOGRuUT0iLCJtYWMiOiI4NjY1ZmU2MThmNzQwOGQ5NjQyMTkyZmJhZTA0NmNlYWNkYmUxZDE0ODQ3ZDU4NzIzMGI5YTE3YTU3ODI1NDk0IiwidGFnIjoiIn0=',NULL,'2025-11-22 08:44:39','2025-11-22 08:44:39'),(2,'stripe',0,'eyJpdiI6Imt3VCtVbVNCa081QU9LKzErVUhiR1E9PSIsInZhbHVlIjoiQm9ucU1oQ1FOVU5qa1g2V1RzQ09MQT09IiwibWFjIjoiZGUxYjg1NGI3NDhjZWViNjg2MWEzNDU5NzlmYzBmYWI2Y2U2Mjk2MTUyMTMxNDE5MjQ2NTYxNzE3OWE1YzhjMSIsInRhZyI6IiJ9',NULL,'2025-11-22 08:44:39','2025-11-22 08:44:39'),(3,'flutterwave',0,'eyJpdiI6IjhPdlpqczZnMXZRZzJXZjl1NmYrTmc9PSIsInZhbHVlIjoiRnNSdWU4aW5uTGFuYnVZTkxmZ21yTlgxZTlmdzVwZEs2Z3pQd2g2ZWdJaz0iLCJtYWMiOiIwMDViODQ3ZDAwYzM0ODgxNzZkZDc2OTQyNTIzODZiZmYwMmRjNjU3YjdjODUxZWNjZjhmZWE2YTc0N2QzODE0IiwidGFnIjoiIn0=',NULL,'2025-11-22 08:44:39','2025-11-22 08:44:39'),(4,'mtn_momo',0,'eyJpdiI6IjlvQ0k1M01qMnZKdUYyN3A2TThlWWc9PSIsInZhbHVlIjoieG8wcWxxeHdJM0JDNTY1SytjZU9RVGdCdzFWa1p6YitCZ2x4bTFOSEVvST0iLCJtYWMiOiI5NmEzZmFjZGNiZTUzYWJjYmEzMWY4NjUwNzRkMTM2Y2VjZGFlODhlM2Q3NTNiM2I2ZWUwZWQ5NmI3MTU2NWMwIiwidGFnIjoiIn0=',NULL,'2025-11-22 08:44:39','2025-11-22 08:44:39'),(5,'airtel_money',0,'eyJpdiI6InlWRmk4VnFYMGtZU3lHbEJjaWlMc0E9PSIsInZhbHVlIjoiSTBzWENSM3QwMmFCaHJrbXB4S1Nla3hXUjExQ1hrYzRmMk9Oa2R3WS9zZz0iLCJtYWMiOiI3ZjAzMWM5ODMwYjJhMWVkZDllZTliNjhlMjlkNDRkNTQ5MTcwMmNiN2I1Y2ViZjVkM2Q2MWE4NmU4NDJjMThiIiwidGFnIjoiIn0=',NULL,'2025-11-22 08:44:39','2025-11-22 08:44:39'),(6,'pesapal',0,'eyJpdiI6IjBVeXFqVW9HWDBOeVZsYUdDMU0wYlE9PSIsInZhbHVlIjoiaTJBZXNxUVRycURWYkloY3VyVXdBdXZtQ3E4Q0dpNFZMMkdvS2J0dlpOST0iLCJtYWMiOiJjNmFlMmIxODY1NWY1Nzc4YzRhZjJkMDUxODU1OTM3NmY3OWYzZGU3OWJkNjY1NzIzMWRkMzY4OGQ5M2FjNmVkIiwidGFnIjoiIn0=',NULL,'2025-11-22 08:44:39','2025-11-22 08:44:39'),(7,'bank_transfer',0,'eyJpdiI6Ilg4MG9SMXlzNitaN3dmeXRPYjJ3UWc9PSIsInZhbHVlIjoiN1BjTzFncUQrbjJ0djI1WENDSjZlSEw4RS96d3Z4dXh1TlgrWVkvZks1SDN2a3dOV3JJcVg1QjRKQTRtUkFzcThSK2IzaUFLWjhvQ05nbVdzeG8vNkFVdTdIVW91SjJFMGwxZ2NLU2Z1ZkgwaHNqbW5Eek1VRUk3QTZZa1UzeGJocnptbEI3OFlWRjJXcGU4VDBFTWtnPT0iLCJtYWMiOiIyNDlmMGVlN2IzNzc0NTNmMWVhODdiOWMwNTI0YTIzZWM5OGM2NzcxZTc0YzU5NmU3ZTZmNjY3MmVhOWNiNDYxIiwidGFnIjoiIn0=',NULL,'2025-11-22 08:44:39','2025-11-22 08:44:39'),(8,'custom',0,'eyJpdiI6ImRTck9DOUpYQVdhZEhrcXlrMnNQVXc9PSIsInZhbHVlIjoiS3ZvUjRZUkN4WDkxMTR5RHk1YWhJQT09IiwibWFjIjoiOWZjOTkzMDZkZTlkOWM0ZmIyZTYyYTY5YjFiMzlmMzNlN2Q0MThiZjhlYjNlNDAyY2UwNWMyNDVlZGVlMTQ0YiIsInRhZyI6IiJ9',NULL,'2025-11-22 08:44:39','2025-11-22 08:44:39');
/*!40000 ALTER TABLE `payment_gateway_settings` ENABLE KEYS */;
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
  KEY `payroll_settings_category_is_active_index` (`category`,`is_active`),
  KEY `payroll_settings_key_index` (`key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payroll_settings`
--

LOCK TABLES `payroll_settings` WRITE;
/*!40000 ALTER TABLE `payroll_settings` DISABLE KEYS */;
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
  `name` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tenant_id` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_team_name_guard_unique` (`tenant_id`,`name`,`guard_name`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES (1,'access landlord dashboard','landlord','landlord','2025-11-30 04:40:47','2025-11-30 04:40:47'),(2,'manage tenants','landlord','landlord','2025-12-01 05:14:32','2025-12-01 05:14:32'),(3,'view billing','landlord','landlord','2025-12-01 05:14:32','2025-12-01 05:14:32'),(4,'manage landlord billing','landlord','landlord','2025-12-01 05:14:32','2025-12-01 05:14:32'),(5,'manage invoices','landlord','landlord','2025-12-01 05:14:32','2025-12-01 05:14:32'),(6,'manage payment methods','landlord','landlord','2025-12-01 05:14:32','2025-12-01 05:14:32'),(7,'view analytics','landlord','landlord','2025-12-01 05:14:32','2025-12-01 05:14:32'),(8,'manage settings','landlord','landlord','2025-12-01 05:14:32','2025-12-01 05:14:32'),(9,'view audit logs','landlord','landlord','2025-12-01 05:14:32','2025-12-01 05:14:32'),(10,'manage notifications','landlord','landlord','2025-12-01 05:14:32','2025-12-01 05:14:32'),(11,'manage users','landlord','landlord','2025-12-01 05:14:32','2025-12-01 05:14:32'),(12,'view system health','landlord','landlord','2025-12-01 05:14:32','2025-12-01 05:14:32'),(13,'manage integrations','landlord','landlord','2025-12-01 05:14:32','2025-12-01 05:14:32'),(14,'create tenants','landlord','landlord','2025-12-01 05:14:32','2025-12-01 05:14:32'),(15,'edit tenants','landlord','landlord','2025-12-01 05:14:32','2025-12-01 05:14:32'),(16,'delete tenants','landlord','landlord','2025-12-01 05:14:32','2025-12-01 05:14:32'),(17,'export tenants','landlord','landlord','2025-12-01 05:14:32','2025-12-01 05:14:32'),(18,'import tenants','landlord','landlord','2025-12-01 05:14:32','2025-12-01 05:14:32');
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
  `api_key` text COLLATE utf8mb4_unicode_ci,
  `api_secret` text COLLATE utf8mb4_unicode_ci,
  `client_id` text COLLATE utf8mb4_unicode_ci,
  `client_secret` text COLLATE utf8mb4_unicode_ci,
  `redirect_uri` text COLLATE utf8mb4_unicode_ci,
  `access_token` text COLLATE utf8mb4_unicode_ci,
  `refresh_token` text COLLATE utf8mb4_unicode_ci,
  `token_expires_at` timestamp NULL DEFAULT NULL,
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
  `department_id` bigint unsigned NOT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `positions_department_id_index` (`department_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `positions`
--

LOCK TABLES `positions` WRITE;
/*!40000 ALTER TABLE `positions` DISABLE KEYS */;
/*!40000 ALTER TABLE `positions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `report_marks`
--

DROP TABLE IF EXISTS `report_marks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `report_marks` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `report_id` bigint unsigned NOT NULL,
  `subject_id` bigint unsigned NOT NULL,
  `score` double NOT NULL,
  `grade` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remarks` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `report_marks_report_id_foreign` (`report_id`),
  KEY `report_marks_subject_id_foreign` (`subject_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `report_marks`
--

LOCK TABLES `report_marks` WRITE;
/*!40000 ALTER TABLE `report_marks` DISABLE KEYS */;
/*!40000 ALTER TABLE `report_marks` ENABLE KEYS */;
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
INSERT INTO `role_has_permissions` VALUES (1,2),(1,3),(1,4),(1,6),(2,6),(3,6),(4,6),(5,6),(6,6),(7,6),(8,6),(9,6),(10,6),(11,6),(12,6),(13,6),(14,6),(15,6),(16,6),(17,6),(18,6);
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
  `tenant_id` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_team_name_guard_unique` (`tenant_id`,`name`,`guard_name`),
  KEY `roles_team_foreign_key_index` (`tenant_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'skolaris-root','landlord-admin','landlord','2025-11-29 20:53:12','2025-11-29 20:53:12'),(2,'landlord','Super Admin','landlord','2025-11-30 04:40:47','2025-11-30 04:40:47'),(3,'landlord','Landlord Admin','landlord','2025-11-30 04:50:15','2025-11-30 04:50:15'),(4,'landlord','super-admin','landlord','2025-11-30 08:08:25','2025-11-30 08:08:25'),(5,'landlord','super-admin','web','2025-11-30 08:12:10','2025-11-30 08:12:10'),(6,'landlord','landlord-admin','landlord','2025-12-01 05:14:32','2025-12-01 05:14:32');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
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
-- Table structure for table `school_user_invitations`
--

DROP TABLE IF EXISTS `school_user_invitations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `school_user_invitations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `school_id` bigint unsigned NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general_staff',
  `token` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `accepted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `school_user_invitations_school_id_email_unique` (`school_id`,`email`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `school_user_invitations`
--

LOCK TABLES `school_user_invitations` WRITE;
/*!40000 ALTER TABLE `school_user_invitations` DISABLE KEYS */;
INSERT INTO `school_user_invitations` VALUES (1,1,'test@example.com','admin',NULL,'2025-12-22 08:48:36','2025-11-22 08:44:16','2025-11-22 08:42:09','2025-11-22 08:48:36');
/*!40000 ALTER TABLE `school_user_invitations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `schools`
--

DROP TABLE IF EXISTS `schools`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `schools` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subdomain` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `domain` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `database` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `schools_code_unique` (`code`),
  UNIQUE KEY `schools_subdomain_unique` (`subdomain`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `schools`
--

LOCK TABLES `schools` WRITE;
/*!40000 ALTER TABLE `schools` DISABLE KEYS */;
INSERT INTO `schools` VALUES (1,'SMATCAMPUS Demo School','SMATCAMPUS','demo','demo.localhost:8000','tenant_000001',NULL,'2025-11-22 08:42:09','2025-11-22 08:48:36'),(2,'Victoria Nile School','VICTORIANILE','victorianileschool','victorianileschool.localhost:8000','tenant_000002',NULL,'2025-11-22 08:44:16','2025-11-22 08:48:36'),(3,'FrankHost School','FRANKHOST','frankhost','localhost','tenant_000003',NULL,'2025-11-22 08:44:25','2025-11-22 08:44:25'),(4,'Makerere College','MAK','makererecollege',NULL,'tenant_makererecollege','{\"plan\": \"starter\", \"phones\": [\"+256752954723\"], \"country\": \"KE\", \"admin_email\": \"mukobi@outlook.com\", \"contact_email\": \"mukobi@outlook.com\"}','2025-11-30 13:33:09','2025-11-30 13:33:09');
/*!40000 ALTER TABLE `schools` ENABLE KEYS */;
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
INSERT INTO `sessions` VALUES ('VxNIvboCEcJntFJQ5kuBL8rjfFBgAC8Z9khMIwIG',2,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','YTo4OntzOjY6Il90b2tlbiI7czo0MDoibTNJYlptSTg4bkhhM3ZIamlLNXdtWU53U2VEUThOTWNxdG9qTURrVSI7czoxNjoidGVuYW50X3NjaG9vbF9pZCI7aTozO3M6MTY6InRlbmFudF9zdWJkb21haW4iO3M6OToiZnJhbmtob3N0IjtzOjE1OiJ0ZW5hbnRfZGF0YWJhc2UiO3M6MTM6InRlbmFudF8wMDAwMDMiO3M6MzoidXJsIjthOjA6e31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozOToiaHR0cDovL2xvY2FsaG9zdDo4MDAwL2xhbmRsb3JkL3NldHRpbmdzIjtzOjU6InJvdXRlIjtzOjE3OiJsYW5kbG9yZC5zZXR0aW5ncyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTU6ImxvZ2luX2xhbmRsb3JkXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6Mjt9',1764623459);
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
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` VALUES (1,'bookstore_enabled','1','2025-11-24 10:17:21','2025-11-24 10:17:21');
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `student_fees`
--

DROP TABLE IF EXISTS `student_fees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `student_fees` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint unsigned NOT NULL,
  `term_id` bigint unsigned NOT NULL,
  `amount_due` decimal(10,2) NOT NULL,
  `amount_paid` decimal(10,2) NOT NULL DEFAULT '0.00',
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `student_fees_term_id_foreign` (`term_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `student_fees`
--

LOCK TABLES `student_fees` WRITE;
/*!40000 ALTER TABLE `student_fees` DISABLE KEYS */;
/*!40000 ALTER TABLE `student_fees` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subjects`
--

DROP TABLE IF EXISTS `subjects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `subjects` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `education_level_id` bigint unsigned DEFAULT NULL,
  `code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `subjects_code_unique` (`code`),
  KEY `subjects_education_level_id_foreign` (`education_level_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subjects`
--

LOCK TABLES `subjects` WRITE;
/*!40000 ALTER TABLE `subjects` DISABLE KEYS */;
/*!40000 ALTER TABLE `subjects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `terms`
--

DROP TABLE IF EXISTS `terms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `terms` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `is_current` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `terms_is_current_index` (`is_current`)
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
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `school_id` bigint unsigned DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general_staff',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `approval_status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'approved',
  `approved_by` bigint unsigned DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `rejection_reason` text COLLATE utf8mb4_unicode_ci,
  `registration_data` text COLLATE utf8mb4_unicode_ci,
  `suspension_reason` text COLLATE utf8mb4_unicode_ci,
  `suspended_at` timestamp NULL DEFAULT NULL,
  `suspended_by` bigint unsigned DEFAULT NULL,
  `expelled_at` timestamp NULL DEFAULT NULL,
  `expulsion_reason` text COLLATE utf8mb4_unicode_ci,
  `expelled_by` bigint unsigned DEFAULT NULL,
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gender` enum('male','female','other') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `qualification` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `specialization` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `profile_photo` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notification_preferences` json DEFAULT NULL,
  `emergency_contact_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `emergency_contact_phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `two_factor_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `two_factor_secret` text COLLATE utf8mb4_unicode_ci,
  `two_factor_recovery_codes` text COLLATE utf8mb4_unicode_ci,
  `two_factor_confirmed_at` timestamp NULL DEFAULT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_changed_at` timestamp NULL DEFAULT NULL,
  `password_expires_at` timestamp NULL DEFAULT NULL,
  `password_history` json DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_activity_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `photo_path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_school_id_foreign` (`school_id`),
  KEY `users_approved_by_foreign` (`approved_by`),
  KEY `users_suspended_by_foreign` (`suspended_by`),
  KEY `users_expelled_by_foreign` (`expelled_by`),
  KEY `users_approval_status_index` (`approval_status`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,NULL,'Frank Host','frankhost@skolariscloud.com','admin',1,'approved',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,'$2y$12$0DShHYMFlUeSMrxSwlVOauguyV2J7xAuxckVt3FWzTPa2nkg/K4I.',NULL,NULL,NULL,NULL,NULL,'2025-11-29 20:53:12','2025-11-29 20:53:12',NULL),(2,NULL,'Francis Mukobi','frankhostltd3@gmail.com','admin',1,'approved',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'+256784975651',NULL,NULL,'70A Ttula Road, Kakungululu Zone',NULL,NULL,'landlord-profiles/UFPW4cmsUV8Ti6ZATY55iNXxSsXjQ9dspBFDceHi.jpg',NULL,NULL,NULL,'2025-11-30 05:34:41',0,NULL,NULL,NULL,'$2y$12$yNwmLFXX2Aqic2Q1M9kyFOyaRVmzaPdVYwjAzSkMmBXUTvfXgWw2a',NULL,NULL,NULL,'Cg0kY3U3dj7jR6wuhQy7Ntkl4aEeXvG9OW91pXNDmy62P9DPJq5mVKZqKyeO',NULL,'2025-11-29 21:29:31','2025-11-30 13:02:03',NULL),(3,NULL,'Landlord Admin','admin@landlord.local','general_staff',1,'approved',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-12-01 05:14:32',0,NULL,NULL,NULL,'$2y$12$L/SWHhXInURz8TzPjGBqxOEb.S5TrYKPZRrjr07dsw.Er01t11pjG',NULL,NULL,NULL,'DVx1Polc7eE5stWVgNMqkTMn7tSCjew4OcFdJATuMOHWcbxswwFzn9e6ptgo',NULL,'2025-12-01 05:14:32','2025-12-01 05:14:32',NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'fran_ugketravel36'
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
