
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
DROP TABLE IF EXISTS `wp_wc_admin_notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wp_wc_admin_notes` (
  `note_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `locale` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `content_data` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `source` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_reminder` datetime DEFAULT NULL,
  `is_snoozable` tinyint(1) NOT NULL DEFAULT 0,
  `layout` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `image` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `icon` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'info',
  PRIMARY KEY (`note_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `wp_wc_admin_notes` WRITE;
/*!40000 ALTER TABLE `wp_wc_admin_notes` DISABLE KEYS */;
INSERT INTO `wp_wc_admin_notes` VALUES (1,'wc-admin-wc-helper-connection','info','en_US','Connect to WooCommerce.com','Connect to get important product notifications and updates.','{}','unactioned','woocommerce-admin','2021-01-12 17:42:30',NULL,0,'plain','',0,'info');
INSERT INTO `wp_wc_admin_notes` VALUES (2,'wc-admin-onboarding-email-marketing','info','en_US','Tips, product updates, and inspiration','We\'re here for you - get tips, product updates and inspiration straight to your email box','{}','unactioned','woocommerce-admin','2021-01-12 17:42:31',NULL,0,'plain','',0,'info');
INSERT INTO `wp_wc_admin_notes` VALUES (3,'wc-admin-marketing-intro','info','en_US','Connect with your audience','Grow your customer base and increase your sales with marketing tools built for WooCommerce.','{}','unactioned','woocommerce-admin','2021-01-12 17:42:31',NULL,0,'plain','',0,'info');
INSERT INTO `wp_wc_admin_notes` VALUES (4,'ecomm-need-help-setting-up-your-store','info','en_US','Need help setting up your Store?','Schedule a free 30-min <a href=\"https://wordpress.com/support/concierge-support/\">quick start session</a> and get help from our specialists. We’re happy to walk through setup steps, show you around the WordPress.com dashboard, troubleshoot any issues you may have, and help you the find the features you need to accomplish your goals for your site.','{}','pending','woocommerce.com','2021-01-12 17:42:31',NULL,0,'plain','',0,'info');
INSERT INTO `wp_wc_admin_notes` VALUES (5,'woocommerce-services','info','en_US','WooCommerce Shipping & Tax','WooCommerce Shipping &amp; Tax helps get your store “ready to sell” as quickly as possible. You create your products. We take care of tax calculation, payment processing, and shipping label printing! Learn more about the extension that you just installed.','{}','pending','woocommerce.com','2021-01-12 17:42:31',NULL,0,'plain','',0,'info');
INSERT INTO `wp_wc_admin_notes` VALUES (6,'ecomm-unique-shopping-experience','info','en_US','For a shopping experience as unique as your customers','Product Add-Ons allow your customers to personalize products while they’re shopping on your online store. No more follow-up email requests—customers get what they want, before they’re done checking out. Learn more about this extension that comes included in your plan.','{}','pending','woocommerce.com','2021-01-12 17:42:31',NULL,0,'plain','',0,'info');
INSERT INTO `wp_wc_admin_notes` VALUES (7,'wcpay-promo-2020-11','marketing','en_US','Manage subscriber payments from your store\'s dashboard','Securely accept cards and manage transactions right from your dashboard with <a href=\"https://woocommerce.com/payments/?utm_medium=notification&amp;utm_source=product&amp;utm_campaign=wcpay_ctrl20\" target=\"_blank\">WooCommerce Payments</a>  – now supporting <a href=\"https://woocommerce.com/products/woocommerce-subscriptions/?utm_medium=notification&amp;utm_source=product&amp;utm_campaign=wcpay_ctrl20\" target=\"_blank\">WooCommerce Subscriptions</a>! <br /><br /><em>By clicking \"Install now,\" you agree to our <a href=\"https://wordpress.com/tos/?utm_medium=notification&amp;utm_source=product&amp;utm_campaign=wcpay_ctrl20\" target=\"_blank\">Terms of Service</a>.</em>','{}','pending','woocommerce.com','2021-01-12 17:42:31',NULL,0,'plain','',0,'info');
INSERT INTO `wp_wc_admin_notes` VALUES (8,'wcpay-subscriptions-2020-11','marketing','en_US','Manage subscriber payments from your store\'s dashboard','Securely accept cards and manage transactions right from your dashboard with <a href=\"https://woocommerce.com/payments/?utm_medium=notification&amp;utm_source=product&amp;utm_campaign=wcpay_ctrl20\" target=\"_blank\">WooCommerce Payments</a>  – now supporting <a href=\"https://woocommerce.com/products/woocommerce-subscriptions/?utm_medium=notification&amp;utm_source=product&amp;utm_campaign=wcpay_ctrl20\" target=\"_blank\">WooCommerce Subscriptions</a>! <br /><br /><em>By clicking \"Install now,\" you agree to our <a href=\"https://wordpress.com/tos/?utm_medium=notification&amp;utm_source=product&amp;utm_campaign=wcpay_ctrl20\" target=\"_blank\">Terms of Service</a>.</em>','{}','pending','woocommerce.com','2021-01-12 17:42:31',NULL,0,'plain','',0,'info');
INSERT INTO `wp_wc_admin_notes` VALUES (9,'wcpay-promo-2020-12','marketing','en_US','Get 50% off transaction fees with WooCommerce Payments','Keep more of your hard-earned cash by adding <a href=\"https://woocommerce.com/payments/?utm_medium=notification&amp;utm_source=product&amp;utm_campaign=wcpay_exp20\" target=\"_blank\">WooCommerce Payments</a> to your store. Lock in a discounted rate of 1.5% + $0.15 for $25,000 of payments (or three months, whichever comes first). Limited time offer – don’t miss out! <br /><br /><em>By clicking \"Install now,\" you agree to our promotional <a href=\"https://woocommerce.com/terms-conditions/woocommerce-payments-promotion/?utm_medium=notification&amp;utm_source=product&amp;utm_campaign=wcpay_exp20\" target=\"_blank\">Terms of Service</a>.</em>','{}','pending','woocommerce.com','2021-01-12 17:42:31',NULL,0,'plain','',0,'info');
INSERT INTO `wp_wc_admin_notes` VALUES (10,'wcpay-subscriptions-2020-12','marketing','en_US','Manage payments from your store\'s dashboard','Securely accept cards and manage transactions right from your dashboard with <a href=\"https://woocommerce.com/payments/?utm_medium=notification&amp;utm_source=product&amp;utm_campaign=wcpay_ctrl20\" target=\"_blank\">WooCommerce Payments</a>! Zero setup fees or monthly fees. Just pay-as-you-go, starting at just 2.9% + $0.30 per transaction for U.S.-issued cards. <br /><br /><em>By clicking \"Install now,\" you agree to our <a href=\"https://wordpress.com/tos/?utm_medium=notification&amp;utm_source=product&amp;utm_campaign=wcpay_ctrl20\" target=\"_blank\">Terms of Service</a>.</em>','{}','pending','woocommerce.com','2021-01-12 17:42:31',NULL,0,'plain','',0,'info');
/*!40000 ALTER TABLE `wp_wc_admin_notes` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

