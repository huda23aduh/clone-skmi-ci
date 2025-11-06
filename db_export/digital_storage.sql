-- MySQL dump 10.13  Distrib 8.0.43, for Linux (aarch64)
--
-- Host: localhost    Database: digital_storage
-- ------------------------------------------------------
-- Server version	8.0.43

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
-- Table structure for table `activity_logs`
--

DROP TABLE IF EXISTS `activity_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `activity_logs` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `activity_type` varchar(50) NOT NULL,
  `item_type` enum('file','folder','user','system') DEFAULT NULL,
  `item_id` bigint DEFAULT NULL,
  `item_name` varchar(255) DEFAULT NULL,
  `description` text,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text,
  `metadata` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_activity_type` (`activity_type`),
  KEY `idx_created_at` (`created_at`),
  KEY `idx_item` (`item_type`,`item_id`),
  CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=302 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity_logs`
--

LOCK TABLES `activity_logs` WRITE;
/*!40000 ALTER TABLE `activity_logs` DISABLE KEYS */;
INSERT INTO `activity_logs` VALUES (208,1,'file_upload','file',90,'524ax.png','Uploaded file \'524ax.png\' (2.09 MB)','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','{\"file_size\": 2188318}','2025-11-03 22:51:59'),(209,1,'folder_create','folder',42,'a','Created folder \'a\'','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-11-03 22:55:05'),(210,1,'folder_create','folder',43,'aa','Created folder \'aa\'','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-11-03 22:55:11'),(211,1,'folder_create','folder',44,'aa','Created folder \'aa\'','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','{\"parent_id\": \"42\"}','2025-11-03 22:55:20'),(212,1,'folder_create','folder',45,'aaa','Created folder \'aaa\'','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','{\"parent_id\": \"44\"}','2025-11-03 22:56:03'),(213,1,'file_upload','file',91,'dekrap.png','Uploaded file \'dekrap.png\' (141.45 KB)','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','{\"file_size\": 144849, \"folder_id\": 42}','2025-11-03 22:56:55'),(214,1,'user_login','user',1,NULL,'User logged in','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-11-04 02:01:04'),(215,1,'file_upload','file',96,'sp101 bentuk.png','Uploaded file \'sp101 bentuk.png\' (638.44 KB)','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','{\"file_size\": 653761}','2025-11-04 02:01:17'),(216,1,'file_upload','file',97,'sp101.png','Uploaded file \'sp101.png\' (941.77 KB)','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','{\"file_size\": 964371}','2025-11-04 02:01:17'),(217,1,'file_preview','file',98,'compressed_1762221684.zip','Previewed file: compressed_1762221684.zip','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-11-04 02:01:42'),(218,1,'folder_create','folder',48,'a','Created folder \'a\'','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-11-04 02:01:53'),(219,1,'file_upload','file',99,'524ax bentuk.png','Uploaded file \'524ax bentuk.png\' (406.74 KB)','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','{\"file_size\": 416498, \"folder_id\": 48}','2025-11-04 02:02:06'),(220,1,'file_upload','file',100,'524ax.png','Uploaded file \'524ax.png\' (2.09 MB)','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','{\"file_size\": 2188318, \"folder_id\": 48}','2025-11-04 02:02:06'),(221,1,'file_preview','file',101,'compressed_1762221735.zip','Previewed file: compressed_1762221735.zip','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-11-04 02:02:29'),(222,1,'file_preview','file',102,'compressed_1762222595.zip','Previewed file: compressed_1762222595.zip','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-11-04 02:16:43'),(223,1,'file_upload','file',103,'524ax bentuk.png','Uploaded file \'524ax bentuk.png\' (406.74 KB)','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','{\"file_size\": 416498}','2025-11-04 02:39:20'),(224,1,'file_upload','file',104,'524ax.png','Uploaded file \'524ax.png\' (2.09 MB)','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','{\"file_size\": 2188318}','2025-11-04 02:39:20'),(225,1,'folder_create','folder',49,'aa','Created folder \'aa\'','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','{\"parent_id\": \"48\"}','2025-11-04 02:39:26'),(226,1,'file_upload','file',105,'dekrap.png','Uploaded file \'dekrap.png\' (141.45 KB)','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','{\"file_size\": 144849, \"folder_id\": 49}','2025-11-04 02:39:33'),(227,1,'file_upload','file',106,'sp101 bentuk.png','Uploaded file \'sp101 bentuk.png\' (638.44 KB)','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','{\"file_size\": 653761, \"folder_id\": 48}','2025-11-04 02:39:57'),(228,1,'file_upload','file',107,'sp101.png','Uploaded file \'sp101.png\' (941.77 KB)','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','{\"file_size\": 964371, \"folder_id\": 48}','2025-11-04 02:39:57'),(229,1,'folder_create','folder',50,'b','Created folder \'b\'','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-11-04 02:40:47'),(230,1,'file_upload','file',110,'524ax bentuk.png','Uploaded file \'524ax bentuk.png\' (406.74 KB)','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','{\"file_size\": 416498, \"folder_id\": 48}','2025-11-04 02:52:01'),(231,1,'file_upload','file',111,'524ax.png','Uploaded file \'524ax.png\' (2.09 MB)','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','{\"file_size\": 2188318, \"folder_id\": 48}','2025-11-04 02:52:01'),(232,1,'user_login','user',1,NULL,'User logged in','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-11-04 06:02:01'),(233,1,'folder_create','folder',51,'a','Created folder \'a\'','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-11-04 06:02:07'),(234,1,'folder_create','folder',52,'aa','Created folder \'aa\'','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','{\"parent_id\": \"51\"}','2025-11-04 06:02:23'),(235,1,'file_upload','file',114,'524ax bentuk.png','Uploaded file \'524ax bentuk.png\' (406.74 KB)','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','{\"file_size\": 416498, \"folder_id\": 51}','2025-11-04 06:02:39'),(236,1,'file_upload','file',115,'524ax.png','Uploaded file \'524ax.png\' (2.09 MB)','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','{\"file_size\": 2188318, \"folder_id\": 51}','2025-11-04 06:02:39'),(237,1,'file_upload','file',116,'dekrap.png','Uploaded file \'dekrap.png\' (141.45 KB)','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','{\"file_size\": 144849, \"folder_id\": 52}','2025-11-04 06:02:58'),(238,1,'user_logout','user',1,NULL,'User logged out','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-11-04 06:11:37'),(239,1,'user_login','user',1,NULL,'User logged in','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-11-04 06:11:41'),(240,1,'file_upload','file',118,'cognitive aptitude assessment.png','Uploaded file \'cognitive aptitude assessment.png\' (2.42 MB)','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','{\"file_size\": 2538492}','2025-11-04 06:33:55'),(241,1,'file_upload','file',119,'Personality Assessment.png','Uploaded file \'Personality Assessment.png\' (358.45 KB)','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','{\"file_size\": 367053}','2025-11-04 06:33:55'),(242,1,'folder_create','folder',53,'a','Created folder \'a\'','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-11-04 06:43:31'),(243,1,'folder_create','folder',54,'aa','Created folder \'aa\'','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','{\"parent_id\": \"53\"}','2025-11-04 06:43:35'),(244,1,'file_upload','file',121,'sp101 bentuk.png','Uploaded file \'sp101 bentuk.png\' (638.44 KB)','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','{\"file_size\": 653761}','2025-11-04 06:44:06'),(245,1,'file_upload','file',122,'sp101.png','Uploaded file \'sp101.png\' (941.77 KB)','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','{\"file_size\": 964371}','2025-11-04 06:44:06'),(246,1,'file_upload','file',123,'dekrap.png','Uploaded file \'dekrap.png\' (141.45 KB)','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','{\"file_size\": 144849, \"folder_id\": 53}','2025-11-04 06:44:17'),(247,1,'file_upload','file',124,'deepseek server busy.png','Uploaded file \'deepseek server busy.png\' (283.54 KB)','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','{\"file_size\": 290343, \"folder_id\": 54}','2025-11-04 06:44:27'),(248,1,'file_preview','file',125,'compressed_1762238679.zip','Previewed file: compressed_1762238679.zip','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-11-04 06:44:42'),(249,1,'profile_update',NULL,NULL,NULL,'Updated profile information','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','{\"fields_updated\": [\"name\", \"email\"]}','2025-11-04 06:46:41'),(250,1,'user_logout','user',1,NULL,'User logged out','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-11-04 06:53:56'),(251,1,'user_login','user',1,NULL,'User logged in','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-11-04 06:53:57'),(252,1,'profile_update',NULL,NULL,NULL,'Updated profile information','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','{\"fields_updated\": [\"id\", \"email\", \"password\", \"name\", \"profile_image\", \"created_at\", \"updated_at\", \"isAdmin\", \"isActive\", \"language\"]}','2025-11-04 06:55:01'),(253,1,'profile_update',NULL,NULL,NULL,'Updated profile information','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','{\"fields_updated\": [\"id\", \"email\", \"password\", \"name\", \"profile_image\", \"created_at\", \"updated_at\", \"isAdmin\", \"isActive\", \"language\"]}','2025-11-04 06:55:09'),(254,1,'profile_image_update',NULL,NULL,NULL,'Updated profile picture','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-11-04 06:55:22'),(255,1,'profile_image_update',NULL,NULL,NULL,'Updated profile picture','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-11-04 06:55:53'),(256,1,'profile_image_update',NULL,NULL,NULL,'Updated profile picture','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-11-04 06:56:41'),(257,1,'file_preview','file',121,'sp101 bentuk.png','Previewed file: sp101 bentuk.png','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-11-04 08:16:27'),(258,1,'file_preview','file',121,'sp101 bentuk.png','Previewed file: sp101 bentuk.png','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-11-04 08:16:32'),(259,1,'file_preview','file',121,'sp101 bentuk.png','Previewed file: sp101 bentuk.png','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-11-04 08:18:25'),(260,1,'file_upload','file',126,'FILLED - SSB-TMP-013 Employee Application Form V1.0.3.docx.pdf','Uploaded file \'FILLED - SSB-TMP-013 Employee Application Form V1.0.3.docx.pdf\' (283.84 KB)','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','{\"file_size\": 290652}','2025-11-04 10:09:19'),(261,1,'file_preview','file',126,'FILLED - SSB-TMP-013 Employee Application Form V1.0.3.docx.pdf','Previewed file: FILLED - SSB-TMP-013 Employee Application Form V1.0.3.docx.pdf','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-11-04 10:09:23'),(262,1,'file_preview','file',126,'FILLED - SSB-TMP-013 Employee Application Form V1.0.3.docx.pdf','Previewed file: FILLED - SSB-TMP-013 Employee Application Form V1.0.3.docx.pdf','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-11-04 10:09:52'),(263,1,'file_preview','file',126,'FILLED - SSB-TMP-013 Employee Application Form V1.0.3.docx.pdf','Previewed file: FILLED - SSB-TMP-013 Employee Application Form V1.0.3.docx.pdf','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-11-04 10:10:05'),(264,1,'file_preview','file',126,'FILLED - SSB-TMP-013 Employee Application Form V1.0.3.docx.pdf','Previewed file: FILLED - SSB-TMP-013 Employee Application Form V1.0.3.docx.pdf','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-11-04 10:10:21'),(265,1,'file_preview','file',126,'FILLED - SSB-TMP-013 Employee Application Form V1.0.3.docx.pdf','Previewed file: FILLED - SSB-TMP-013 Employee Application Form V1.0.3.docx.pdf','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-11-04 10:11:00'),(266,1,'file_preview','file',126,'FILLED - SSB-TMP-013 Employee Application Form V1.0.3.docx.pdf','Previewed file: FILLED - SSB-TMP-013 Employee Application Form V1.0.3.docx.pdf','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-11-04 10:11:11'),(267,1,'file_preview','file',126,'FILLED - SSB-TMP-013 Employee Application Form V1.0.3.docx.pdf','Previewed file: FILLED - SSB-TMP-013 Employee Application Form V1.0.3.docx.pdf','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-11-04 10:11:20'),(268,1,'file_preview','file',126,'FILLED - SSB-TMP-013 Employee Application Form V1.0.3.docx.pdf','Previewed file: FILLED - SSB-TMP-013 Employee Application Form V1.0.3.docx.pdf','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-11-04 10:11:34'),(269,1,'file_preview','file',126,'FILLED - SSB-TMP-013 Employee Application Form V1.0.3.docx.pdf','Previewed file: FILLED - SSB-TMP-013 Employee Application Form V1.0.3.docx.pdf','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-11-04 10:11:46'),(270,1,'file_preview','file',126,'FILLED - SSB-TMP-013 Employee Application Form V1.0.3.docx.pdf','Previewed file: FILLED - SSB-TMP-013 Employee Application Form V1.0.3.docx.pdf','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-11-04 10:11:56'),(271,1,'file_preview','file',126,'FILLED - SSB-TMP-013 Employee Application Form V1.0.3.docx.pdf','Previewed file: FILLED - SSB-TMP-013 Employee Application Form V1.0.3.docx.pdf','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-11-04 10:12:57'),(272,1,'file_preview','file',126,'FILLED - SSB-TMP-013 Employee Application Form V1.0.3.docx.pdf','Previewed file: FILLED - SSB-TMP-013 Employee Application Form V1.0.3.docx.pdf','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-11-04 10:13:07'),(273,1,'file_preview','file',126,'FILLED - SSB-TMP-013 Employee Application Form V1.0.3.docx.pdf','Previewed file: FILLED - SSB-TMP-013 Employee Application Form V1.0.3.docx.pdf','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-11-04 10:13:17'),(274,1,'file_preview','file',126,'FILLED - SSB-TMP-013 Employee Application Form V1.0.3.docx.pdf','Previewed file: FILLED - SSB-TMP-013 Employee Application Form V1.0.3.docx.pdf','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-11-04 10:13:37'),(275,1,'file_preview','file',126,'FILLED - SSB-TMP-013 Employee Application Form V1.0.3.docx.pdf','Previewed file: FILLED - SSB-TMP-013 Employee Application Form V1.0.3.docx.pdf','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-11-04 10:13:50'),(276,1,'file_preview','file',126,'FILLED - SSB-TMP-013 Employee Application Form V1.0.3.docx.pdf','Previewed file: FILLED - SSB-TMP-013 Employee Application Form V1.0.3.docx.pdf','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-11-04 10:14:00'),(277,1,'file_preview','file',126,'FILLED - SSB-TMP-013 Employee Application Form V1.0.3.docx.pdf','Previewed file: FILLED - SSB-TMP-013 Employee Application Form V1.0.3.docx.pdf','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-11-04 10:14:15'),(278,1,'file_preview','file',126,'FILLED - SSB-TMP-013 Employee Application Form V1.0.3.docx.pdf','Previewed file: FILLED - SSB-TMP-013 Employee Application Form V1.0.3.docx.pdf','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-11-04 10:14:23'),(279,1,'file_preview','file',126,'FILLED - SSB-TMP-013 Employee Application Form V1.0.3.docx.pdf','Previewed file: FILLED - SSB-TMP-013 Employee Application Form V1.0.3.docx.pdf','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-11-04 10:14:32'),(280,1,'file_preview','file',126,'FILLED - SSB-TMP-013 Employee Application Form V1.0.3.docx.pdf','Previewed file: FILLED - SSB-TMP-013 Employee Application Form V1.0.3.docx.pdf','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-11-04 10:14:40'),(281,1,'file_preview','file',126,'FILLED - SSB-TMP-013 Employee Application Form V1.0.3.docx.pdf','Previewed file: FILLED - SSB-TMP-013 Employee Application Form V1.0.3.docx.pdf','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-11-04 10:14:56'),(282,1,'file_preview','file',126,'FILLED - SSB-TMP-013 Employee Application Form V1.0.3.docx.pdf','Previewed file: FILLED - SSB-TMP-013 Employee Application Form V1.0.3.docx.pdf','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-11-04 10:15:02'),(283,1,'file_preview','file',126,'FILLED - SSB-TMP-013 Employee Application Form V1.0.3.docx.pdf','Previewed file: FILLED - SSB-TMP-013 Employee Application Form V1.0.3.docx.pdf','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-11-04 10:15:27'),(284,1,'user_logout','user',1,NULL,'User logged out','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-11-04 11:07:05'),(285,1,'user_login','user',1,NULL,'User logged in','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-11-04 11:07:07'),(286,1,'user_logout','user',1,NULL,'User logged out','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-11-04 11:09:56'),(287,1,'user_login','user',1,NULL,'User logged in','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-11-04 11:31:15'),(288,1,'file_preview','file',121,'sp101 bentuk.png','Previewed file: sp101 bentuk.png','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-11-04 11:32:55'),(289,1,'user_logout','user',1,NULL,'User logged out','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-11-04 12:19:42'),(290,1,'user_login','user',1,NULL,'User logged in','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-11-04 12:19:44'),(291,1,'user_login','user',1,NULL,'User logged in','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-11-06 02:00:23'),(292,1,'folder_create','folder',55,'b','Created folder \'b\'','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-11-06 02:23:51'),(293,1,'user_logout','user',1,NULL,'User logged out','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-11-06 02:40:49'),(294,5,'user_login','user',5,NULL,'User logged in','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-11-06 02:40:52'),(295,5,'user_logout','user',5,NULL,'User logged out','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-11-06 02:49:23'),(296,1,'user_login','user',1,NULL,'User logged in','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-11-06 02:49:24'),(297,1,'file_upload','file',127,'524ax.png','Uploaded file \'524ax.png\' (2.09 MB)','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','{\"file_size\": 2188318, \"folder_id\": 55}','2025-11-06 02:49:32'),(298,1,'user_logout','user',1,NULL,'User logged out','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-11-06 02:49:50'),(299,5,'user_login','user',5,NULL,'User logged in','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-11-06 02:49:52'),(300,5,'user_logout','user',5,NULL,'User logged out','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-11-06 02:56:44'),(301,5,'user_login','user',5,NULL,'User logged in','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-11-06 02:56:46');
/*!40000 ALTER TABLE `activity_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `files`
--

DROP TABLE IF EXISTS `files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `files` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `folder_id` bigint unsigned DEFAULT NULL,
  `original_name` varchar(1024) NOT NULL,
  `storage_name` varchar(1024) NOT NULL,
  `mime` varchar(255) DEFAULT NULL,
  `size` bigint unsigned DEFAULT '0',
  `is_public` tinyint(1) DEFAULT '0',
  `public_token` varchar(32) DEFAULT NULL,
  `is_deleted` tinyint(1) DEFAULT '0',
  `deleted_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_files_user` (`user_id`),
  KEY `idx_files_folder` (`folder_id`),
  KEY `public_token` (`public_token`),
  CONSTRAINT `files_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `files_ibfk_2` FOREIGN KEY (`folder_id`) REFERENCES `folders` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=128 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `files`
--

LOCK TABLES `files` WRITE;
/*!40000 ALTER TABLE `files` DISABLE KEYS */;
INSERT INTO `files` VALUES (121,1,NULL,'sp101 bentuk.png','1762238646_90940989b61c76645bdc.png',NULL,653761,0,NULL,0,NULL,'2025-11-04 06:44:06','2025-11-04 06:44:06'),(122,1,NULL,'sp101.png','1762238646_ba32492d0ef2f21b9892.png',NULL,964371,0,NULL,0,NULL,'2025-11-04 06:44:06','2025-11-04 06:44:06'),(123,1,53,'dekrap.png','1762238657_e90bbb707df4e28e6a2c.png',NULL,144849,0,NULL,0,NULL,'2025-11-04 06:44:17','2025-11-04 06:44:17'),(124,1,54,'deepseek server busy.png','1762238667_f11150d8ef3b97e82049.png',NULL,290343,0,NULL,0,NULL,'2025-11-04 06:44:27','2025-11-04 06:44:27'),(125,1,NULL,'compressed_1762238679.zip','compressed_1762238679.zip','application/zip',342060,0,NULL,0,NULL,'2025-11-04 06:44:39','2025-11-04 06:44:39'),(126,1,NULL,'FILLED - SSB-TMP-013 Employee Application Form V1.0.3.docx.pdf','1762250959_05a249a12b0784778bc4.pdf',NULL,290652,0,NULL,1,'2025-11-04 11:07:23','2025-11-04 10:09:19','2025-11-04 11:07:23'),(127,1,55,'524ax.png','1762397372_fab608629c80a8942c59.png',NULL,2188318,1,'03477f9b4d039bda78e8bcd7f15069c4',0,NULL,'2025-11-06 02:49:32','2025-11-06 02:49:32');
/*!40000 ALTER TABLE `files` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `folders`
--

DROP TABLE IF EXISTS `folders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `folders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `parent_id` bigint unsigned DEFAULT NULL,
  `path` varchar(2048) DEFAULT NULL,
  `is_public` tinyint(1) DEFAULT '0',
  `public_token` varchar(32) DEFAULT NULL,
  `is_deleted` tinyint(1) DEFAULT '0',
  `deleted_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_folders_user` (`user_id`),
  KEY `public_token` (`public_token`),
  CONSTRAINT `folders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=56 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `folders`
--

LOCK TABLES `folders` WRITE;
/*!40000 ALTER TABLE `folders` DISABLE KEYS */;
INSERT INTO `folders` VALUES (53,1,'a',NULL,'a',0,NULL,0,NULL,NULL,NULL),(54,1,'aa',53,'a/aa',0,NULL,0,NULL,NULL,NULL),(55,1,'b',NULL,'b',1,'03477f9b4d039bda78e8bcd7f15069c4',0,NULL,NULL,NULL);
/*!40000 ALTER TABLE `folders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `version` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `class` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `group` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `namespace` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `time` int NOT NULL,
  `batch` int unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (8,'20251023060600','App\\Database\\Migrations\\AddProfileFieldsToUsers','default','App',1762396442,1),(9,'2025-10-27-223017','App\\Database\\Migrations\\CreateUserEmailsTable','default','App',1762396442,1),(10,'2025-11-06-100900','App\\Database\\Migrations\\AddIsPublicToFilesAndFolders','default','App',1762396585,2);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `starred_items`
--

DROP TABLE IF EXISTS `starred_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `starred_items` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `item_id` bigint NOT NULL,
  `item_type` enum('file','folder') NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_starred_item` (`user_id`,`item_id`,`item_type`),
  CONSTRAINT `starred_items_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `starred_items`
--

LOCK TABLES `starred_items` WRITE;
/*!40000 ALTER TABLE `starred_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `starred_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_emails`
--

DROP TABLE IF EXISTS `user_emails`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_emails` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT '0',
  `is_verified` tinyint(1) NOT NULL DEFAULT '0',
  `verification_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `user_id_is_primary` (`user_id`,`is_primary`),
  CONSTRAINT `user_emails_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_emails`
--

LOCK TABLES `user_emails` WRITE;
/*!40000 ALTER TABLE `user_emails` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_emails` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  `isAdmin` tinyint(1) NOT NULL DEFAULT '0',
  `isActive` tinyint(1) NOT NULL DEFAULT '1',
  `language` varchar(20) DEFAULT 'english',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'admin@example.com','$2y$10$qwUQ1jsXN39jE2tbgiW8Qu.eeppbkluMc.vyMqkThwPUXfU4vc1jO','dd',NULL,'2025-10-20 10:25:25','2025-11-04 06:56:41',1,1,'english'),(2,'user@example.com','$2y$10$qwUQ1jsXN39jE2tbgiW8Qu.eeppbkluMc.vyMqkThwPUXfU4vc1jO','Test User',NULL,'2025-10-20 10:25:25',NULL,0,1,'english'),(5,'user1@example.com','$2y$10$qwUQ1jsXN39jE2tbgiW8Qu.eeppbkluMc.vyMqkThwPUXfU4vc1jO','Test User1',NULL,'2025-10-20 10:25:25',NULL,0,1,'english');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-11-06  2:57:52
