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
) ENGINE=InnoDB AUTO_INCREMENT=162 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity_logs`
--

LOCK TABLES `activity_logs` WRITE;
/*!40000 ALTER TABLE `activity_logs` DISABLE KEYS */;
INSERT INTO `activity_logs` VALUES (6,1,'user_login','user',1,NULL,'User logged in','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-22 12:50:17'),(7,1,'user_logout','user',1,NULL,'User logged out','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-22 12:50:21'),(8,1,'user_login','user',1,NULL,'User logged in','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-22 12:50:26'),(9,1,'folder_create','folder',32,'h','Created folder \'h\'','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 12:59:17'),(10,1,'user_logout','user',1,NULL,'User logged out','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-22 13:12:50'),(11,1,'user_login','user',1,NULL,'User logged in','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-22 13:13:31'),(12,1,'user_logout','user',1,NULL,'User logged out','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-22 13:13:34'),(13,1,'user_login','user',1,NULL,'User logged in','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-22 13:14:01'),(14,1,'user_logout','user',1,NULL,'User logged out','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-22 13:18:55'),(15,1,'user_login','user',1,NULL,'User logged in','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-22 13:19:41'),(16,1,'user_logout','user',1,NULL,'User logged out','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-22 13:37:39'),(17,1,'user_login','user',1,NULL,'User logged in','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-22 13:37:41'),(18,1,'user_logout','user',1,NULL,'User logged out','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-22 13:38:05'),(19,2,'user_login','user',2,NULL,'User logged in','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-22 13:38:22'),(20,2,'user_logout','user',2,NULL,'User logged out','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-22 13:39:44'),(21,1,'user_login','user',1,NULL,'User logged in','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-22 13:39:46'),(22,1,'user_logout','user',1,NULL,'User logged out','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-22 13:39:48'),(23,1,'user_login','user',1,NULL,'User logged in','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-22 13:45:32'),(24,1,'user_logout','user',1,NULL,'User logged out','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-22 13:56:02'),(25,2,'user_login','user',2,NULL,'User logged in','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-22 13:56:05'),(26,2,'user_logout','user',2,NULL,'User logged out','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-22 14:10:07'),(27,1,'user_login','user',1,NULL,'User logged in','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-22 14:10:09'),(28,1,'user_logout','user',1,NULL,'User logged out','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-22 14:22:16'),(29,1,'user_login','user',1,NULL,'User logged in','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-22 14:22:18'),(30,1,'user_logout','user',1,NULL,'User logged out','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-22 14:30:44'),(31,1,'user_login','user',1,NULL,'User logged in','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-22 14:30:45'),(32,1,'user_login','user',1,NULL,'User logged in','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-22 21:35:21'),(33,1,'user_logout','user',1,NULL,'User logged out','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-22 21:44:02'),(34,1,'user_login','user',1,NULL,'User logged in','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-22 21:44:04'),(35,1,'profile_image_update',NULL,NULL,NULL,'Updated profile picture','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-22 23:17:18'),(36,1,'profile_image_update',NULL,NULL,NULL,'Updated profile picture','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-22 23:21:04'),(37,1,'profile_image_update',NULL,NULL,NULL,'Updated profile picture','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-22 23:23:36'),(38,1,'profile_update',NULL,NULL,NULL,'Updated profile information','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','{\"fields_updated\": [\"name\", \"email\"]}','2025-10-22 23:23:47'),(39,1,'language_update',NULL,NULL,NULL,'Changed language to bahasa','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','{\"language\": \"bahasa\"}','2025-10-22 23:27:37'),(40,1,'language_update',NULL,NULL,NULL,'Changed language to english','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','{\"language\": \"english\"}','2025-10-22 23:43:02'),(41,1,'language_update',NULL,NULL,NULL,'Changed language to bahasa','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','{\"language\": \"bahasa\"}','2025-10-22 23:43:53'),(42,1,'language_update',NULL,NULL,NULL,'Changed language to english','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','{\"language\": \"english\"}','2025-10-22 23:44:42'),(43,1,'language_update',NULL,NULL,NULL,'Changed language to bahasa','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','{\"language\": \"bahasa\"}','2025-10-22 23:44:46'),(44,1,'language_update',NULL,NULL,NULL,'Changed language to bahasa','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','{\"language\": \"bahasa\"}','2025-10-22 23:44:55'),(45,1,'user_login','user',1,NULL,'User logged in','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-23 02:46:46'),(46,1,'folder_create','folder',33,'aaa','Created folder \'aaa\'','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','{\"parent_id\": \"25\"}','2025-10-23 02:47:23'),(47,1,'user_login','user',1,NULL,'User logged in','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-26 11:26:31'),(48,1,'item_rename','file',37,'README_1.md','Renamed file from \'README_1.md\' to \'README_1.md\'','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','{\"new_name\": \"README_1.md\", \"old_name\": \"README_1.md\", \"item_type\": \"file\"}','2025-10-26 11:49:12'),(49,1,'item_rename','folder',26,'b_1','Renamed folder from \'b_1\' to \'b_1\'','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','{\"new_name\": \"b_1\", \"old_name\": \"b_1\", \"item_type\": \"folder\"}','2025-10-26 11:49:36'),(50,1,'user_login','user',1,NULL,'User logged in','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-27 08:21:34'),(51,1,'file_preview','file',40,'WhatsApp Image 2025-10-06 at 8.47.46 AM.jpeg','Previewed file: WhatsApp Image 2025-10-06 at 8.47.46 AM.jpeg','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-27 08:23:51'),(52,1,'file_preview','file',40,'WhatsApp Image 2025-10-06 at 8.47.46 AM.jpeg','Previewed file: WhatsApp Image 2025-10-06 at 8.47.46 AM.jpeg','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-27 08:24:40'),(53,1,'file_preview','file',40,'WhatsApp Image 2025-10-06 at 8.47.46 AM.jpeg','Previewed file: WhatsApp Image 2025-10-06 at 8.47.46 AM.jpeg','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-27 08:24:50'),(54,1,'file_preview','file',40,'WhatsApp Image 2025-10-06 at 8.47.46 AM.jpeg','Previewed file: WhatsApp Image 2025-10-06 at 8.47.46 AM.jpeg','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-27 08:25:28'),(55,1,'file_preview','file',40,'WhatsApp Image 2025-10-06 at 8.47.46 AM.jpeg','Previewed file: WhatsApp Image 2025-10-06 at 8.47.46 AM.jpeg','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-27 08:25:59'),(56,1,'file_preview','file',40,'WhatsApp Image 2025-10-06 at 8.47.46 AM.jpeg','Previewed file: WhatsApp Image 2025-10-06 at 8.47.46 AM.jpeg','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-27 08:26:00'),(57,1,'file_preview','file',42,'WhatsApp Image 2025-10-06 at 8.35.28 AM.jpeg','Previewed file: WhatsApp Image 2025-10-06 at 8.35.28 AM.jpeg','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-27 08:26:07'),(58,1,'file_upload','file',52,'cemflo.png','Uploaded file \'cemflo.png\' (103.83 KB)','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','{\"file_size\": 106323}','2025-10-27 08:36:41'),(59,1,'file_upload','file',53,'cempro.png','Uploaded file \'cempro.png\' (113.77 KB)','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','{\"file_size\": 116503}','2025-10-27 08:37:02'),(60,1,'file_preview','file',53,'cempro.png','Previewed file: cempro.png','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-27 08:37:07'),(61,1,'file_upload','file',54,'FILLED- Supply_Chain_Risk_Pulse_Technical_Skills_Assessment.pdf','Uploaded file \'FILLED- Supply_Chain_Risk_Pulse_Technical_Skills_Assessment.pdf\' (7.56 KB)','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','{\"file_size\": 7740}','2025-10-27 08:37:57'),(62,1,'file_preview','file',54,'FILLED- Supply_Chain_Risk_Pulse_Technical_Skills_Assessment.pdf','Previewed file: FILLED- Supply_Chain_Risk_Pulse_Technical_Skills_Assessment.pdf','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-27 08:38:02'),(63,1,'folder_delete','folder',31,'g','Moved folder \'g\' to trash','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-27 09:00:43'),(64,1,'folder_delete','folder',32,'h','Moved folder \'h\' to trash','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-27 09:00:43'),(65,1,'folder_delete','folder',29,'e','Moved folder \'e\' to trash','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-27 09:00:59'),(66,1,'folder_delete','folder',30,'f','Moved folder \'f\' to trash','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-27 09:00:59'),(67,1,'file_upload','file',55,'cemflo.png','Uploaded file \'cemflo.png\' (103.83 KB)','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','{\"file_size\": 106323}','2025-10-27 09:01:18'),(68,1,'folder_delete','folder',27,'c','Moved folder \'c\' to trash','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-27 09:01:45'),(69,1,'file_delete','file',55,'cemflo.png','Moved file \'cemflo.png\' to trash','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-27 09:01:45'),(70,1,'folder_delete','folder',28,'d','Moved folder \'d\' to trash','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-27 09:01:45'),(71,1,'folder_restore','folder',27,'c','Restored folder \'c\' from trash','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-27 09:07:20'),(72,1,'file_restore','file',55,'cemflo.png','Restored file \'cemflo.png\' from trash','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-27 09:07:35'),(73,1,'folder_restore','folder',28,'d','Restored folder \'d\' from trash','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-27 09:07:35'),(74,1,'folder_delete','folder',27,'c','Moved folder \'c\' to trash','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-27 09:10:47'),(75,1,'file_delete','file',55,'cemflo.png','Moved file \'cemflo.png\' to trash','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-27 09:10:47'),(76,1,'folder_delete','folder',28,'d','Moved folder \'d\' to trash','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-27 09:10:47'),(77,1,'folder_delete','folder',27,'c','Moved folder \'c\' to trash','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-27 09:11:14'),(78,1,'file_delete','file',55,'cemflo.png','Moved file \'cemflo.png\' to trash','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-27 09:11:14'),(79,1,'folder_delete','folder',28,'d','Moved folder \'d\' to trash','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-27 09:11:14'),(80,1,'user_logout','user',1,NULL,'User logged out','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-27 09:20:51'),(81,1,'user_login','user',1,NULL,'User logged in','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-27 09:20:52'),(82,1,'user_logout','user',1,NULL,'User logged out','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-27 10:07:36'),(83,1,'user_login','user',1,NULL,'User logged in','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-27 10:07:38'),(84,1,'file_preview','file',54,'FILLED- Supply_Chain_Risk_Pulse_Technical_Skills_Assessment.pdf','Previewed file: FILLED- Supply_Chain_Risk_Pulse_Technical_Skills_Assessment.pdf','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-27 11:27:52'),(85,1,'file_preview','file',54,'FILLED- Supply_Chain_Risk_Pulse_Technical_Skills_Assessment.pdf','Previewed file: FILLED- Supply_Chain_Risk_Pulse_Technical_Skills_Assessment.pdf','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-27 11:28:20'),(86,1,'file_preview','file',54,'FILLED- Supply_Chain_Risk_Pulse_Technical_Skills_Assessment.pdf','Previewed file: FILLED- Supply_Chain_Risk_Pulse_Technical_Skills_Assessment.pdf','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-27 11:28:29'),(87,1,'file_upload','file',56,'1203210012_Nikko Yudha Asmara Adi_TA2.pdf','Uploaded file \'1203210012_Nikko Yudha Asmara Adi_TA2.pdf\' (1.53 MB)','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','{\"file_size\": 1601071}','2025-10-27 11:30:10'),(88,1,'file_preview','file',56,'1203210012_Nikko Yudha Asmara Adi_TA2.pdf','Previewed file: 1203210012_Nikko Yudha Asmara Adi_TA2.pdf','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-27 11:30:14'),(89,1,'file_upload','file',57,'CTO Technical Handoff – Supply Chain Risk Pulse™.pdf','Uploaded file \'CTO Technical Handoff – Supply Chain Risk Pulse™.pdf\' (1.55 MB)','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','{\"file_size\": 1622349}','2025-10-27 11:34:03'),(90,1,'file_preview','file',57,'CTO Technical Handoff – Supply Chain Risk Pulse™.pdf','Previewed file: CTO Technical Handoff – Supply Chain Risk Pulse™.pdf','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-27 11:46:40'),(91,1,'file_preview','file',57,'CTO Technical Handoff – Supply Chain Risk Pulse™.pdf','Previewed file: CTO Technical Handoff – Supply Chain Risk Pulse™.pdf','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-27 11:52:14'),(92,1,'file_preview','file',57,'CTO Technical Handoff – Supply Chain Risk Pulse™.pdf','Previewed file: CTO Technical Handoff – Supply Chain Risk Pulse™.pdf','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-27 11:54:18'),(93,1,'user_logout','user',1,NULL,'User logged out','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-27 12:06:45'),(94,1,'user_login','user',1,NULL,'User logged in','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-27 12:06:46'),(95,1,'profile_image_update',NULL,NULL,NULL,'Updated profile picture','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-27 12:08:37'),(96,1,'user_logout','user',1,NULL,'User logged out','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-27 12:11:36'),(97,1,'user_login','user',1,NULL,'User logged in','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-27 12:11:37'),(98,1,'user_logout','user',1,NULL,'User logged out','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-27 12:12:21'),(99,1,'user_login','user',1,NULL,'User logged in','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-27 12:12:22'),(100,1,'user_logout','user',1,NULL,'User logged out','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-27 12:19:54'),(101,2,'user_login','user',2,NULL,'User logged in','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-27 12:20:02'),(102,2,'user_logout','user',2,NULL,'User logged out','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-27 12:20:59'),(103,2,'user_login','user',2,NULL,'User logged in','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-27 12:21:01'),(104,2,'user_logout','user',2,NULL,'User logged out','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-27 12:21:05'),(105,2,'user_login','user',2,NULL,'User logged in','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-27 12:22:30'),(106,2,'user_logout','user',2,NULL,'User logged out','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-27 12:24:20'),(107,2,'user_login','user',2,NULL,'User logged in','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-27 12:24:22'),(108,2,'user_logout','user',2,NULL,'User logged out','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-27 12:24:55'),(109,2,'user_login','user',2,NULL,'User logged in','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-27 12:24:58'),(110,2,'user_logout','user',2,NULL,'User logged out','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-27 12:26:36'),(111,2,'user_login','user',2,NULL,'User logged in','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-27 12:26:39'),(112,2,'user_logout','user',2,NULL,'User logged out','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-27 12:29:16'),(113,2,'user_login','user',2,NULL,'User logged in','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-27 12:29:19'),(114,2,'user_logout','user',2,NULL,'User logged out','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-27 12:40:16'),(115,2,'user_login','user',2,NULL,'User logged in','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-27 12:40:19'),(116,2,'user_logout','user',2,NULL,'User logged out','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-27 12:41:17'),(117,1,'user_login','user',1,NULL,'User logged in','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-27 12:54:01'),(118,1,'file_upload','file',58,'nun logo.png','Uploaded file \'nun logo.png\' (74.64 KB)','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','{\"file_size\": 76435}','2025-10-27 13:09:38'),(119,1,'folder_create','folder',34,'a','Created folder \'a\'','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-27 13:09:43'),(120,1,'user_login','user',1,NULL,'User logged in','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-27 20:07:04'),(121,1,'file_download','file',57,'CTO Technical Handoff – Supply Chain Risk Pulse™.pdf','Downloaded file \'CTO Technical Handoff – Supply Chain Risk Pulse™.pdf\'','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-27 20:10:17'),(122,1,'file_download','file',57,'CTO Technical Handoff – Supply Chain Risk Pulse™.pdf','Downloaded file \'CTO Technical Handoff – Supply Chain Risk Pulse™.pdf\'','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-27 20:14:59'),(123,1,'file_upload','file',59,'nun logo.png','Uploaded file \'nun logo.png\' (74.64 KB)','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','{\"file_size\": 76435}','2025-10-27 20:27:53'),(124,1,'folder_create','folder',35,'aa','Created folder \'aa\'','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','{\"parent_id\": \"34\"}','2025-10-27 20:35:35'),(125,1,'file_download','file',59,'nun logo.png','Downloaded file \'nun logo.png\'','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-27 20:37:24'),(126,1,'file_preview','file',57,'CTO Technical Handoff – Supply Chain Risk Pulse™.pdf','Previewed file: CTO Technical Handoff – Supply Chain Risk Pulse™.pdf','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-27 22:07:05'),(127,1,'user_logout','user',1,NULL,'User logged out','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-27 22:45:45'),(128,2,'user_login','user',2,NULL,'User logged in','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-27 22:46:24'),(129,2,'user_logout','user',2,NULL,'User logged out','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-27 22:46:26'),(130,2,'user_login','user',2,NULL,'User logged in','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-27 22:46:29'),(131,2,'user_logout','user',2,NULL,'User logged out','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-27 23:59:05'),(132,1,'user_login','user',1,NULL,'User logged in','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-27 23:59:07'),(133,1,'file_preview','file',58,'nun logo.png','Previewed file: nun logo.png','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-28 00:00:34'),(134,1,'file_preview','file',58,'nun logo.png','Previewed file: nun logo.png','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-28 00:01:01'),(135,1,'file_preview','file',58,'nun logo.png','Previewed file: nun logo.png','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-28 00:06:29'),(136,1,'file_preview','file',58,'nun logo.png','Previewed file: nun logo.png','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-28 01:33:16'),(137,1,'file_preview','file',58,'nun logo.png','Previewed file: nun logo.png','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-28 01:34:04'),(138,1,'file_preview','file',58,'nun logo.png','Previewed file: nun logo.png','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-28 01:35:00'),(139,1,'file_preview','file',57,'CTO Technical Handoff – Supply Chain Risk Pulse™.pdf','Previewed file: CTO Technical Handoff – Supply Chain Risk Pulse™.pdf','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-28 01:35:21'),(140,1,'file_preview','file',57,'CTO Technical Handoff – Supply Chain Risk Pulse™.pdf','Previewed file: CTO Technical Handoff – Supply Chain Risk Pulse™.pdf','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-28 02:00:16'),(141,1,'file_preview','file',57,'CTO Technical Handoff – Supply Chain Risk Pulse™.pdf','Previewed file: CTO Technical Handoff – Supply Chain Risk Pulse™.pdf','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-28 02:00:42'),(142,1,'user_login','user',1,NULL,'User logged in','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-29 00:47:59'),(143,1,'user_logout','user',1,NULL,'User logged out','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-29 00:48:03'),(144,1,'user_login','user',1,NULL,'User logged in','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-29 00:48:04'),(145,1,'user_logout','user',1,NULL,'User logged out','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-29 00:48:06'),(146,1,'user_login','user',1,NULL,'User logged in','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-29 00:48:07'),(147,1,'language_update',NULL,NULL,NULL,'Changed language to bahasa','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','{\"language\": \"bahasa\"}','2025-10-29 00:51:10'),(148,1,'language_update',NULL,NULL,NULL,'Changed language to bahasa','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','{\"language\": \"bahasa\"}','2025-10-29 00:53:36'),(149,1,'language_update',NULL,NULL,NULL,'Changed language to bahasa','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','{\"language\": \"bahasa\"}','2025-10-29 00:53:39'),(150,1,'language_update',NULL,NULL,NULL,'Changed language to bahasa','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','{\"language\": \"bahasa\"}','2025-10-29 00:53:49'),(151,1,'language_update',NULL,NULL,NULL,'Changed language to bahasa','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','{\"language\": \"bahasa\"}','2025-10-29 00:56:07'),(152,1,'language_update',NULL,NULL,NULL,'Changed language to bahasa','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','{\"language\": \"bahasa\"}','2025-10-29 00:58:09'),(153,1,'language_update',NULL,NULL,NULL,'Changed language to bahasa','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','{\"language\": \"bahasa\"}','2025-10-29 00:58:14'),(154,1,'language_update',NULL,NULL,NULL,'Changed language to bahasa','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','{\"language\": \"bahasa\"}','2025-10-29 00:58:33'),(155,1,'language_update',NULL,NULL,NULL,'Changed language to english','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','{\"language\": \"english\"}','2025-10-29 00:58:46'),(156,1,'language_update',NULL,NULL,NULL,'Changed language to bahasa','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','{\"language\": \"bahasa\"}','2025-10-29 00:58:52'),(157,1,'language_update',NULL,NULL,NULL,'Changed language to english','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','{\"language\": \"english\"}','2025-10-29 01:03:47'),(158,1,'language_update',NULL,NULL,NULL,'Changed language to bahasa','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','{\"language\": \"bahasa\"}','2025-10-29 01:04:34'),(159,1,'user_login','user',1,NULL,'User logged in','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',NULL,'2025-10-29 06:39:20'),(160,1,'file_upload','file',60,'College.zip','Uploaded file \'College.zip\' (17.83 MB)','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','{\"file_size\": 18693990}','2025-10-29 09:14:06'),(161,1,'file_upload','file',61,'EGG_0050.MP4','Uploaded file \'EGG_0050.MP4\' (50.4 MB)','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','{\"file_size\": 52845668}','2025-10-29 09:16:11');
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
  `is_deleted` tinyint(1) DEFAULT '0',
  `deleted_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_files_user` (`user_id`),
  KEY `idx_files_folder` (`folder_id`),
  CONSTRAINT `files_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `files_ibfk_2` FOREIGN KEY (`folder_id`) REFERENCES `folders` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=62 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `files`
--

LOCK TABLES `files` WRITE;
/*!40000 ALTER TABLE `files` DISABLE KEYS */;
INSERT INTO `files` VALUES (57,1,NULL,'CTO Technical Handoff – Supply Chain Risk Pulse™.pdf','1761564843_cdb2a73fa4225fcb38d4.pdf',NULL,1622349,0,NULL,NULL,NULL),(58,1,NULL,'nun logo.png','1761570578_753428b48df957192999.png',NULL,76435,0,NULL,NULL,NULL),(60,1,NULL,'College.zip','1761729246_bb956f4699ce445ca20a.zip',NULL,18693990,0,NULL,NULL,NULL),(61,1,NULL,'EGG_0050.MP4','1761729371_4af09f2ce3767c465445.mp4',NULL,52845668,0,NULL,NULL,NULL);
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
  `is_deleted` tinyint(1) DEFAULT '0',
  `deleted_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_folders_user` (`user_id`),
  CONSTRAINT `folders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `folders`
--

LOCK TABLES `folders` WRITE;
/*!40000 ALTER TABLE `folders` DISABLE KEYS */;
INSERT INTO `folders` VALUES (34,1,'a',NULL,NULL,0,NULL,NULL,NULL),(35,1,'aa',34,NULL,0,NULL,NULL,NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (2,'20251023060600','App\\Database\\Migrations\\AddProfileFieldsToUsers','default','App',1761604128,1),(3,'2025-10-27-223017','App\\Database\\Migrations\\CreateUserEmailsTable','default','App',1761604250,2);
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
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `starred_items`
--

LOCK TABLES `starred_items` WRITE;
/*!40000 ALTER TABLE `starred_items` DISABLE KEYS */;
INSERT INTO `starred_items` VALUES (9,1,37,'file','2025-10-22 03:24:49','2025-10-22 03:24:49'),(11,1,38,'file','2025-10-22 03:58:05','2025-10-22 03:58:05');
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
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_emails`
--

LOCK TABLES `user_emails` WRITE;
/*!40000 ALTER TABLE `user_emails` DISABLE KEYS */;
INSERT INTO `user_emails` VALUES (1,2,'userbackup1@example.com',0,1,'3709eda41f46b5464bda0658aa134b883d842cb6e7abc1313cc33679748a16bb',NULL,NULL),(21,2,'huda23aduh@gmail.com',0,1,NULL,'2025-10-27 23:45:41','2025-10-27 23:45:41');
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
INSERT INTO `users` VALUES (1,'admin@example.com','$2y$10$qwUQ1jsXN39jE2tbgiW8Qu.eeppbkluMc.vyMqkThwPUXfU4vc1jO','Admin Useraa',NULL,'2025-10-20 10:25:25','2025-10-29 01:04:34',1,1,'bahasa'),(2,'user@example.com','$2y$10$qwUQ1jsXN39jE2tbgiW8Qu.eeppbkluMc.vyMqkThwPUXfU4vc1jO','Test User',NULL,'2025-10-20 10:25:25',NULL,0,1,'english'),(4,'asas@asas.asas','$2y$10$iMgb1kr6Dps/kM1uGs3aP.Tz/RNQ2AsRgDwHBFiT36aH/q97nzTZq','aasas',NULL,'2025-10-22 21:54:47','2025-10-22 21:58:55',0,0,'english');
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

-- Dump completed on 2025-10-29 11:24:28
