
/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `wp_wc_admin_note_actions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wp_wc_admin_note_actions` (
  `action_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `note_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `label` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `query` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT 0,
  `actioned_text` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`action_id`),
  KEY `note_id` (`note_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `wp_wc_admin_note_actions` WRITE;
/*!40000 ALTER TABLE `wp_wc_admin_note_actions` DISABLE KEYS */;
INSERT INTO `wp_wc_admin_note_actions` VALUES (1,1,'connect','Connect','?page=wc-addons&section=helper','unactioned',0,'');
INSERT INTO `wp_wc_admin_note_actions` VALUES (2,2,'yes-please','Yes please!','https://woocommerce.us8.list-manage.com/subscribe/post?u=2c1434dc56f9506bf3c3ecd21&amp;id=13860df971&amp;SIGNUPPAGE=plugin','actioned',0,'');
INSERT INTO `wp_wc_admin_note_actions` VALUES (3,3,'open-marketing-hub','Open marketing hub','https://www.goldenfleecetrading.co.uk/wp-admin/admin.php?page=wc-admin&path=/marketing','actioned',0,'');
INSERT INTO `wp_wc_admin_note_actions` VALUES (4,4,'set-up-concierge','Schedule free session','https://wordpress.com/me/concierge','actioned',1,'');
INSERT INTO `wp_wc_admin_note_actions` VALUES (5,5,'learn-more','Learn more','https://docs.woocommerce.com/document/woocommerce-shipping-and-tax/?utm_source=inbox','unactioned',1,'');
INSERT INTO `wp_wc_admin_note_actions` VALUES (6,6,'learn-more-ecomm-unique-shopping-experience','Learn more','https://docs.woocommerce.com/document/product-add-ons/?utm_source=inbox','actioned',1,'');
INSERT INTO `wp_wc_admin_note_actions` VALUES (7,7,'install-now','Install now','admin.php?page=wc-admin&action=setup-woocommerce-payments','actioned',1,'');
INSERT INTO `wp_wc_admin_note_actions` VALUES (8,8,'install-now','Install now','admin.php?page=wc-admin&action=setup-woocommerce-payments','actioned',1,'');
INSERT INTO `wp_wc_admin_note_actions` VALUES (9,9,'install-now','Install now','admin.php?page=wc-admin&action=setup-woocommerce-payments','actioned',1,'');
INSERT INTO `wp_wc_admin_note_actions` VALUES (10,10,'install-now','Install now','admin.php?page=wc-admin&action=setup-woocommerce-payments','actioned',1,'');
/*!40000 ALTER TABLE `wp_wc_admin_note_actions` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

