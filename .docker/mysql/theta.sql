-- MySQL dump 10.13  Distrib 8.0.28, for Linux (x86_64)
--
-- Host: localhost    Database: theta
-- ------------------------------------------------------
-- Server version	8.0.28

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
-- Table structure for table `admins`
--

DROP TABLE IF EXISTS `admins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `admins` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `admins_username_unique` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admins`
--

LOCK TABLES `admins` WRITE;
/*!40000 ALTER TABLE `admins` DISABLE KEYS */;
INSERT INTO `admins` VALUES (1,'mitchliu','Mitch Liu','luongfox@gmail.com','$2y$10$Ww5bZbb5GJ47awfdsk81oum2Je2DNWwUe7xVuEZjH.1Lp7sbIns5u','admin',NULL,NULL),(2,'jieyilong','Jieyi Long','luongfox@gmail.com','$2y$10$SQvZ/JfviguglIhOiDgVcOb5Gh6lYypXVPo8ATZBLg7mJ5cYhV2CW','admin',NULL,NULL);
/*!40000 ALTER TABLE `admins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `daily_chains`
--

DROP TABLE IF EXISTS `daily_chains`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `daily_chains` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `chain` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `onchain_wallets` int DEFAULT NULL,
  `active_wallets` int DEFAULT NULL,
  `validators` int DEFAULT NULL,
  `metadata` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_date_chain` (`date`,`chain`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `daily_chains`
--

LOCK TABLES `daily_chains` WRITE;
/*!40000 ALTER TABLE `daily_chains` DISABLE KEYS */;
INSERT INTO `daily_chains` VALUES (1,'2022-05-24','theta',3139370,17568,23,'{\"edge_nodes\": 125354, \"guardian_nodes\": 3471}','2022-05-24 12:18:13','2022-05-24 12:18:48'),(4,'2022-05-25','theta',3139623,26547,24,'{\"edge_nodes\": 125062, \"guardian_nodes\": 3471}','2022-05-25 08:39:57','2022-05-25 08:40:36'),(5,'2022-05-26','theta',3140115,17118,24,'{\"edge_nodes\": 125536, \"guardian_nodes\": 3463}','2022-05-26 15:43:22','2022-05-26 15:43:22'),(6,'2022-05-27','theta',3140452,17493,24,'{\"edge_nodes\": 125630, \"guardian_nodes\": 3471}','2022-05-27 17:31:42','2022-05-27 17:31:42');
/*!40000 ALTER TABLE `daily_chains` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `daily_coins`
--

DROP TABLE IF EXISTS `daily_coins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `daily_coins` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `coin` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` double DEFAULT NULL,
  `market_cap` double DEFAULT NULL,
  `volume_24h` double DEFAULT NULL,
  `supply` bigint DEFAULT NULL,
  `total_stakes` int DEFAULT NULL,
  `staked_nodes` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_date_coin` (`date`,`coin`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `daily_coins`
--

LOCK TABLES `daily_coins` WRITE;
/*!40000 ALTER TABLE `daily_coins` DISABLE KEYS */;
INSERT INTO `daily_coins` VALUES (1,'2022-05-24','theta',1.2373136256139,1237313625.6139,85475743.814331,1000000000,631304218,3419,'2022-05-24 12:18:13','2022-05-24 12:18:13'),(2,'2022-05-24','tfuel',0.063406228546156,336130011.81857,22607399.533108,5714605790,2114994615,7623,'2022-05-24 12:18:13','2022-05-24 12:18:13'),(3,'2022-05-25','theta',1.2402893381114,1240289338.1114,75338909.615961,1000000000,631966803,3422,'2022-05-25 03:37:39','2022-05-25 08:37:42'),(4,'2022-05-25','tfuel',0.064131516734837,339974920.00856,25495554.076893,5715622080,2118279205,7614,'2022-05-25 03:37:39','2022-05-25 08:39:55'),(5,'2022-05-26','theta',1.1450478146561,1145047814.6561,93951057.118031,1000000000,630537326,3419,'2022-05-26 15:43:19','2022-05-26 15:43:19'),(6,'2022-05-26','tfuel',0.060006547519956,318107573.80707,41007877.046523,5717181687,2125949342,7604,'2022-05-26 15:43:19','2022-05-26 15:43:19'),(7,'2022-05-27','theta',1.096544158678,1096544158.678,81457332.263532,1000000000,630917086,3424,'2022-05-27 17:31:39','2022-05-27 17:31:39'),(8,'2022-05-27','tfuel',0.056239428203479,298137266.64005,22091995.189394,5718475437,2128855482,7604,'2022-05-27 17:31:39','2022-05-27 17:31:39');
/*!40000 ALTER TABLE `daily_coins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `holders`
--

DROP TABLE IF EXISTS `holders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `holders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `chain` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `assets` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `holders`
--

LOCK TABLES `holders` WRITE;
/*!40000 ALTER TABLE `holders` DISABLE KEYS */;
INSERT INTO `holders` VALUES (1,'0x80eab22e27d4b94511f5906484369b868d6552d2','Binance','theta',NULL,'2022-05-25 07:22:11','2022-05-25 07:22:11'),(2,'0xa61abd72cdc50d17a3cbdceb57d3d5e4d8839bce','ThetaLabs','theta',NULL,'2022-05-25 07:22:11','2022-05-25 07:22:11'),(3,'0x4b80a68a8469d33449eb101082e5500b932a23ce','ThetaLabs','theta',NULL,'2022-05-25 07:22:11','2022-05-25 07:22:11'),(4,'0xe2408dff7a1f9bc247c803e43efa2f0a37b10ba6','ThetaLabs','theta',NULL,'2022-05-25 07:22:11','2022-05-25 07:22:11'),(5,'0xa144e6a98b967e585b214bfa7f6692af81987e5b','ThetaLabs','theta',NULL,'2022-05-25 07:22:11','2022-05-25 07:22:11'),(6,'0x15cc4c3f21417c392119054c8fe5895146e1a493','ThetaLabs','theta',NULL,'2022-05-25 07:22:11','2022-05-25 07:22:11'),(7,'0xafcc901b0e8eac02f0e91bd12791888a0df8a252','ThetaLabs','theta',NULL,'2022-05-25 07:22:11','2022-05-25 07:22:11'),(8,'0x3905663153b7f2ba8a21f020f87df6fcf13580c5','*','theta',NULL,'2022-05-25 07:22:11','2022-05-25 07:22:11'),(9,'0xaab4faa8dbd835854e2e724a753b1c4d4020475a','*','theta',NULL,'2022-05-25 07:22:11','2022-05-25 07:22:11'),(10,'0x53dee6603cd4a1dd549b3d46116a239138945ab0','*','theta',NULL,'2022-05-25 07:22:11','2022-05-25 07:22:11'),(11,'0xc4e68435b0f12c6664377a961e7c459f414b6ef1','*','theta',NULL,'2022-05-25 07:22:11','2022-05-25 07:22:11'),(12,'0x7f0f88a29daa41e988aad71668e84d575af8ad28','*','theta',NULL,'2022-05-25 07:22:11','2022-05-25 07:22:11'),(13,'0xcbcef62ca7a2e367a9c93aba07ea4e63139da99d','*','theta',NULL,'2022-05-25 07:22:11','2022-05-25 07:22:11'),(14,'0x6872b883464bfba456ab674bbef9824849db91e2','CAA','theta',NULL,'2022-05-25 07:22:11','2022-05-25 07:22:11'),(15,'0x578f5ddd2221a94f095bc7c81ddf95ee9e0cb58f','*','theta',NULL,'2022-05-25 07:22:11','2022-05-25 07:22:11'),(16,'0x1786d878cb76a53f5950f41fed7d61617e12dfb5','DHVC','theta',NULL,'2022-05-25 07:22:11','2022-05-25 07:22:11'),(17,'0x050bb1210802cf5c624a4b3f501f1c12f68dcc05','*','theta',NULL,'2022-05-25 07:22:11','2022-05-25 07:22:11'),(18,'0x3dd37990b722249f81375c3298eabe491d44944d','*','theta',NULL,'2022-05-25 07:22:11','2022-05-25 07:22:11'),(19,'0x73668d14e3b69ac9c986d5de2bd96c00377610a1','*','theta',NULL,'2022-05-25 07:22:11','2022-05-25 07:22:11'),(20,'0x08f927f6212f842ce5af107f2ab5e6efac729de6','*','theta',NULL,'2022-05-25 07:22:11','2022-05-25 07:22:11'),(21,'0xe4c05fab358c4d253cb519997854a7c2d9384b01','*','theta',NULL,'2022-05-25 07:22:11','2022-05-25 07:22:11'),(22,'0xfae4efad7fcc8e3d76dc53ee92c91d88fb7388aa','BridgeTower','theta',NULL,'2022-05-25 07:22:11','2022-05-25 07:22:11'),(23,'0x66f8aa626b5ccf5d7bee2ad6435a11cf22bed789','*','theta',NULL,'2022-05-25 07:22:11','2022-05-25 07:22:11'),(24,'0x099e156352ab4de87a20801288edb7d753770db8','Sierra','theta',NULL,'2022-05-25 07:22:11','2022-05-25 07:22:11');
/*!40000 ALTER TABLE `holders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (12,'2022_05_24_085946_create_daily_coins_table',1),(13,'2022_05_24_101631_create_daily_chains_table',1),(14,'2022_05_24_114510_update_daily_chains_table',1),(17,'2022_05_25_032522_update_index_daily_chains_table',2),(18,'2022_05_25_032558_update_index_daily_coins_table',2),(22,'2022_05_25_061313_create_node_validators_table',3),(23,'2022_05_25_064159_create_holders_table',3),(24,'2022_06_06_083643_create_admins_table',4),(25,'2022_06_06_100224_rename_node_validators_table',5);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `validators`
--

DROP TABLE IF EXISTS `validators`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `validators` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `holder` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `chain` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` double NOT NULL,
  `coin` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_holder_chain` (`holder`,`chain`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `validators`
--

LOCK TABLES `validators` WRITE;
/*!40000 ALTER TABLE `validators` DISABLE KEYS */;
INSERT INTO `validators` VALUES (1,'0x80eab22e27d4b94511f5906484369b868d6552d2','Binance','theta',60000000,'theta','2022-06-07 09:46:54','2022-06-07 09:46:54'),(2,'0xa61abd72cdc50d17a3cbdceb57d3d5e4d8839bce','ThetaLabs','theta',32000000,'theta','2022-06-07 09:46:54','2022-06-07 09:46:54'),(3,'0x4b80a68a8469d33449eb101082e5500b932a23ce','ThetaLabs','theta',30500000,'theta','2022-06-07 09:46:54','2022-06-07 09:46:54'),(4,'0xe2408dff7a1f9bc247c803e43efa2f0a37b10ba6','ThetaLabs','theta',30000000,'theta','2022-06-07 09:46:54','2022-06-07 09:46:54'),(5,'0xa144e6a98b967e585b214bfa7f6692af81987e5b','ThetaLabs','theta',30000000,'theta','2022-06-07 09:46:54','2022-06-07 09:46:54'),(6,'0x15cc4c3f21417c392119054c8fe5895146e1a493','ThetaLabs','theta',30000000,'theta','2022-06-07 09:46:54','2022-06-07 09:46:54'),(7,'0xafcc901b0e8eac02f0e91bd12791888a0df8a252','ThetaLabs','theta',13000000,'theta','2022-06-07 09:46:54','2022-06-07 09:46:54'),(8,'0x3905663153b7f2ba8a21f020f87df6fcf13580c5','*','theta',6000000,'theta','2022-06-07 09:46:54','2022-06-07 09:46:54'),(9,'0xaab4faa8dbd835854e2e724a753b1c4d4020475a','*','theta',5000000,'theta','2022-06-07 09:46:54','2022-06-07 09:46:54'),(10,'0x53dee6603cd4a1dd549b3d46116a239138945ab0','*','theta',3584412,'theta','2022-06-07 09:46:54','2022-06-07 09:46:54'),(11,'0xc4e68435b0f12c6664377a961e7c459f414b6ef1','*','theta',2326087,'theta','2022-06-07 09:46:54','2022-06-07 09:46:54'),(12,'0x7f0f88a29daa41e988aad71668e84d575af8ad28','*','theta',2002083,'theta','2022-06-07 09:46:54','2022-06-07 09:46:54'),(13,'0xcbcef62ca7a2e367a9c93aba07ea4e63139da99d','*','theta',2000000,'theta','2022-06-07 09:46:54','2022-06-07 09:46:54'),(14,'0x6872b883464bfba456ab674bbef9824849db91e2','CAA','theta',2000000,'theta','2022-06-07 09:46:54','2022-06-07 09:46:54'),(15,'0x578f5ddd2221a94f095bc7c81ddf95ee9e0cb58f','*','theta',2000000,'theta','2022-06-07 09:46:54','2022-06-07 09:46:54'),(16,'0x1786d878cb76a53f5950f41fed7d61617e12dfb5','DHVC','theta',2000000,'theta','2022-06-07 09:46:54','2022-06-07 09:46:54'),(17,'0x050bb1210802cf5c624a4b3f501f1c12f68dcc05','*','theta',2000000,'theta','2022-06-07 09:46:54','2022-06-07 09:46:54'),(18,'0x3dd37990b722249f81375c3298eabe491d44944d','*','theta',1300000,'theta','2022-06-07 09:46:54','2022-06-07 09:46:54'),(19,'0x73668d14e3b69ac9c986d5de2bd96c00377610a1','*','theta',404577,'theta','2022-06-07 09:46:54','2022-06-07 09:46:54'),(20,'0x08f927f6212f842ce5af107f2ab5e6efac729de6','*','theta',350001,'theta','2022-06-07 09:46:54','2022-06-07 09:46:54'),(21,'0xe4c05fab358c4d253cb519997854a7c2d9384b01','*','theta',226000,'theta','2022-06-07 09:46:54','2022-06-07 09:46:54'),(22,'0xfae4efad7fcc8e3d76dc53ee92c91d88fb7388aa','BridgeTower','theta',200000,'theta','2022-06-07 09:46:54','2022-06-07 09:46:54'),(23,'0x66f8aa626b5ccf5d7bee2ad6435a11cf22bed789','*','theta',200000,'theta','2022-06-07 09:46:54','2022-06-07 09:46:54'),(24,'0x099e156352ab4de87a20801288edb7d753770db8','Sierra','theta',200000,'theta','2022-06-07 09:46:54','2022-06-07 09:46:54');
/*!40000 ALTER TABLE `validators` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2022-06-08  4:27:07
