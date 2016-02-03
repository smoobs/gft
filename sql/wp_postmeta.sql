-- MySQL dump 10.15  Distrib 10.0.23-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: gft
-- ------------------------------------------------------
-- Server version	10.0.23-MariaDB-1~wheezy-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `wp_postmeta`
--

DROP TABLE IF EXISTS `wp_postmeta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wp_postmeta` (
  `meta_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `post_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `meta_key` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_value` longtext COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`meta_id`),
  KEY `post_id` (`post_id`),
  KEY `meta_key` (`meta_key`(191))
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wp_postmeta`
--

LOCK TABLES `wp_postmeta` WRITE;
/*!40000 ALTER TABLE `wp_postmeta` DISABLE KEYS */;
INSERT INTO `wp_postmeta` VALUES (1,2,'_wp_page_template','default');
INSERT INTO `wp_postmeta` VALUES (2,4,'_edit_last','1');
INSERT INTO `wp_postmeta` VALUES (3,4,'_edit_lock','1454502374:1');
INSERT INTO `wp_postmeta` VALUES (6,7,'_et_pb_predefined_layout','on');
INSERT INTO `wp_postmeta` VALUES (7,8,'_et_pb_predefined_layout','on');
INSERT INTO `wp_postmeta` VALUES (8,9,'_et_pb_predefined_layout','on');
INSERT INTO `wp_postmeta` VALUES (9,10,'_et_pb_predefined_layout','on');
INSERT INTO `wp_postmeta` VALUES (10,11,'_et_pb_predefined_layout','on');
INSERT INTO `wp_postmeta` VALUES (11,12,'_et_pb_predefined_layout','on');
INSERT INTO `wp_postmeta` VALUES (12,13,'_et_pb_predefined_layout','on');
INSERT INTO `wp_postmeta` VALUES (13,14,'_et_pb_predefined_layout','on');
INSERT INTO `wp_postmeta` VALUES (14,15,'_et_pb_predefined_layout','on');
INSERT INTO `wp_postmeta` VALUES (15,16,'_et_pb_predefined_layout','on');
INSERT INTO `wp_postmeta` VALUES (16,17,'_et_pb_predefined_layout','on');
INSERT INTO `wp_postmeta` VALUES (17,18,'_et_pb_predefined_layout','on');
INSERT INTO `wp_postmeta` VALUES (18,19,'_et_pb_predefined_layout','on');
INSERT INTO `wp_postmeta` VALUES (19,20,'_et_pb_predefined_layout','on');
INSERT INTO `wp_postmeta` VALUES (20,21,'_et_pb_predefined_layout','on');
INSERT INTO `wp_postmeta` VALUES (21,22,'_et_pb_predefined_layout','on');
INSERT INTO `wp_postmeta` VALUES (22,23,'_et_pb_predefined_layout','on');
INSERT INTO `wp_postmeta` VALUES (23,24,'_et_pb_predefined_layout','on');
INSERT INTO `wp_postmeta` VALUES (24,25,'_et_pb_predefined_layout','on');
INSERT INTO `wp_postmeta` VALUES (25,26,'_et_pb_predefined_layout','on');
INSERT INTO `wp_postmeta` VALUES (26,27,'_et_pb_predefined_layout','on');
INSERT INTO `wp_postmeta` VALUES (27,28,'_et_pb_predefined_layout','on');
INSERT INTO `wp_postmeta` VALUES (28,29,'_et_pb_predefined_layout','on');
INSERT INTO `wp_postmeta` VALUES (29,30,'_et_pb_predefined_layout','on');
INSERT INTO `wp_postmeta` VALUES (30,31,'_et_pb_predefined_layout','on');
INSERT INTO `wp_postmeta` VALUES (31,32,'_et_pb_predefined_layout','on');
INSERT INTO `wp_postmeta` VALUES (32,33,'_et_pb_predefined_layout','on');
INSERT INTO `wp_postmeta` VALUES (33,34,'_et_pb_predefined_layout','on');
INSERT INTO `wp_postmeta` VALUES (34,35,'_et_pb_predefined_layout','on');
INSERT INTO `wp_postmeta` VALUES (35,36,'_et_pb_predefined_layout','on');
INSERT INTO `wp_postmeta` VALUES (36,37,'_et_pb_predefined_layout','on');
INSERT INTO `wp_postmeta` VALUES (37,38,'_et_pb_predefined_layout','on');
/*!40000 ALTER TABLE `wp_postmeta` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed
