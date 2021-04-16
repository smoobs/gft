
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
DROP TABLE IF EXISTS `wp_usermeta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wp_usermeta` (
  `umeta_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL DEFAULT 0,
  `meta_key` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_value` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`umeta_id`),
  KEY `user_id` (`user_id`),
  KEY `meta_key` (`meta_key`(191))
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `wp_usermeta` WRITE;
/*!40000 ALTER TABLE `wp_usermeta` DISABLE KEYS */;
INSERT INTO `wp_usermeta` VALUES (1,1,'nickname','g-flAdm1n');
INSERT INTO `wp_usermeta` VALUES (2,1,'first_name','');
INSERT INTO `wp_usermeta` VALUES (3,1,'last_name','');
INSERT INTO `wp_usermeta` VALUES (4,1,'description','');
INSERT INTO `wp_usermeta` VALUES (5,1,'rich_editing','true');
INSERT INTO `wp_usermeta` VALUES (6,1,'comment_shortcuts','false');
INSERT INTO `wp_usermeta` VALUES (7,1,'admin_color','fresh');
INSERT INTO `wp_usermeta` VALUES (8,1,'use_ssl','0');
INSERT INTO `wp_usermeta` VALUES (9,1,'show_admin_bar_front','false');
INSERT INTO `wp_usermeta` VALUES (10,1,'wp_capabilities','a:1:{s:13:\"administrator\";b:1;}');
INSERT INTO `wp_usermeta` VALUES (11,1,'wp_user_level','10');
INSERT INTO `wp_usermeta` VALUES (12,1,'dismissed_wp_pointers','');
INSERT INTO `wp_usermeta` VALUES (13,1,'show_welcome_panel','1');
INSERT INTO `wp_usermeta` VALUES (14,1,'session_tokens','a:1:{s:64:\"0bd3aeb77dc2a8a1fd5e8c6e84a9f235c8cee032ad2b01acaf185c8905e14f0b\";a:4:{s:10:\"expiration\";i:1610644517;s:2:\"ip\";s:12:\"192.168.1.26\";s:2:\"ua\";s:120:\"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88 Safari/537.36\";s:5:\"login\";i:1610471717;}}');
INSERT INTO `wp_usermeta` VALUES (15,1,'wp_dashboard_quick_press_last_post_id','400');
INSERT INTO `wp_usermeta` VALUES (16,1,'wporg_favorites','');
INSERT INTO `wp_usermeta` VALUES (17,1,'wp_user-settings','editor=tinymce&libraryContent=browse&imgsize=');
INSERT INTO `wp_usermeta` VALUES (18,1,'wp_user-settings-time','1604511504');
INSERT INTO `wp_usermeta` VALUES (19,1,'managenav-menuscolumnshidden','a:5:{i:0;s:11:\"link-target\";i:1;s:11:\"css-classes\";i:2;s:3:\"xfn\";i:3;s:11:\"description\";i:4;s:15:\"title-attribute\";}');
INSERT INTO `wp_usermeta` VALUES (20,1,'metaboxhidden_nav-menus','a:5:{i:0;s:21:\"add-post-type-project\";i:1;s:12:\"add-post_tag\";i:2;s:15:\"add-post_format\";i:3;s:20:\"add-project_category\";i:4;s:15:\"add-project_tag\";}');
INSERT INTO `wp_usermeta` VALUES (21,1,'nav_menu_recently_edited','2');
INSERT INTO `wp_usermeta` VALUES (22,1,'community-events-location','a:1:{s:2:\"ip\";s:11:\"192.168.1.0\";}');
INSERT INTO `wp_usermeta` VALUES (23,1,'_woocommerce_tracks_anon_id','woo:ATUsyQCxJdUxuhs4ou2Cl3eE');
INSERT INTO `wp_usermeta` VALUES (24,1,'syntax_highlighting','true');
INSERT INTO `wp_usermeta` VALUES (25,1,'locale','');
INSERT INTO `wp_usermeta` VALUES (26,1,'last_update','1610473354');
INSERT INTO `wp_usermeta` VALUES (27,1,'woocommerce_admin_activity_panel_inbox_last_read','1610473353971');
INSERT INTO `wp_usermeta` VALUES (28,1,'wc_last_active','1610409600');
/*!40000 ALTER TABLE `wp_usermeta` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

