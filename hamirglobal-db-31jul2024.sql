/*
SQLyog Community v13.1.6 (64 bit)
MySQL - 8.0.30 : Database - global-venture2
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`global-venture2` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;

USE `global-venture2`;

/*Table structure for table `account_groups` */

DROP TABLE IF EXISTS `account_groups`;

CREATE TABLE `account_groups` (
  `id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_by` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `modified_by` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted_by` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `account_groups` */

insert  into `account_groups`(`id`,`name`,`created_by`,`modified_by`,`deleted_by`,`created_at`,`updated_at`) values 
('9ca30fce-7720-40c5-8a78-a76143723f03','Test Account','','','','2024-07-29 08:26:29','2024-07-29 08:26:29');

/*Table structure for table `account_subtypes` */

DROP TABLE IF EXISTS `account_subtypes`;

CREATE TABLE `account_subtypes` (
  `id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `account_type_id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `modified_by` bigint unsigned DEFAULT NULL,
  `deleted_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `account_subtypes_account_type_id_foreign` (`account_type_id`),
  KEY `account_subtypes_created_by_foreign` (`created_by`),
  KEY `account_subtypes_modified_by_foreign` (`modified_by`),
  KEY `account_subtypes_deleted_by_foreign` (`deleted_by`),
  CONSTRAINT `account_subtypes_account_type_id_foreign` FOREIGN KEY (`account_type_id`) REFERENCES `account_types` (`id`),
  CONSTRAINT `account_subtypes_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  CONSTRAINT `account_subtypes_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`),
  CONSTRAINT `account_subtypes_modified_by_foreign` FOREIGN KEY (`modified_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `account_subtypes` */

insert  into `account_subtypes`(`id`,`name`,`account_type_id`,`created_by`,`modified_by`,`deleted_by`,`created_at`,`updated_at`) values 
('9ca31214-295b-4468-85cb-418731d04924','Account Subtype','9ca311c6-9b09-4cc9-acaa-df2430ac0ac5',NULL,NULL,NULL,'2024-07-29 08:32:49','2024-07-29 08:32:49'),
('9ca7e563-3c07-4462-bcec-b40302c0823b','test sub type2ii','9ca311c6-9b09-4cc9-acaa-df2430ac0ac5',NULL,NULL,NULL,'2024-07-31 18:06:59','2024-07-31 18:07:08');

/*Table structure for table `account_types` */

DROP TABLE IF EXISTS `account_types`;

CREATE TABLE `account_types` (
  `id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `account_group_id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `modified_by` bigint unsigned DEFAULT NULL,
  `deleted_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `account_types_account_group_id_foreign` (`account_group_id`),
  KEY `account_types_created_by_foreign` (`created_by`),
  KEY `account_types_modified_by_foreign` (`modified_by`),
  KEY `account_types_deleted_by_foreign` (`deleted_by`),
  CONSTRAINT `account_types_account_group_id_foreign` FOREIGN KEY (`account_group_id`) REFERENCES `account_groups` (`id`),
  CONSTRAINT `account_types_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  CONSTRAINT `account_types_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`),
  CONSTRAINT `account_types_modified_by_foreign` FOREIGN KEY (`modified_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `account_types` */

insert  into `account_types`(`id`,`account_group_id`,`name`,`code`,`created_by`,`modified_by`,`deleted_by`,`created_at`,`updated_at`) values 
('9ca311c6-9b09-4cc9-acaa-df2430ac0ac5','9ca30fce-7720-40c5-8a78-a76143723f03','Test account type','DC2',NULL,NULL,NULL,'2024-07-29 08:31:58','2024-07-29 08:31:58');

/*Table structure for table `accounts` */

DROP TABLE IF EXISTS `accounts`;

CREATE TABLE `accounts` (
  `id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `account_group_id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `account_type_id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `account_subtype_id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `account_owner_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `modified_by` bigint unsigned DEFAULT NULL,
  `deleted_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `accounts_account_group_id_foreign` (`account_group_id`),
  KEY `accounts_account_type_id_foreign` (`account_type_id`),
  KEY `accounts_account_subtype_id_foreign` (`account_subtype_id`),
  KEY `accounts_created_by_foreign` (`created_by`),
  KEY `accounts_modified_by_foreign` (`modified_by`),
  KEY `accounts_deleted_by_foreign` (`deleted_by`),
  CONSTRAINT `accounts_account_group_id_foreign` FOREIGN KEY (`account_group_id`) REFERENCES `account_groups` (`id`),
  CONSTRAINT `accounts_account_subtype_id_foreign` FOREIGN KEY (`account_subtype_id`) REFERENCES `account_subtypes` (`id`),
  CONSTRAINT `accounts_account_type_id_foreign` FOREIGN KEY (`account_type_id`) REFERENCES `account_types` (`id`),
  CONSTRAINT `accounts_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  CONSTRAINT `accounts_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`),
  CONSTRAINT `accounts_modified_by_foreign` FOREIGN KEY (`modified_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `accounts` */

insert  into `accounts`(`id`,`name`,`code`,`account_group_id`,`account_type_id`,`account_subtype_id`,`account_owner_id`,`created_by`,`modified_by`,`deleted_by`,`created_at`,`updated_at`) values 
('9ca31334-f38e-47e5-95b8-0cb374e4bcee','Testing Account','TST23','9ca30fce-7720-40c5-8a78-a76143723f03','9ca311c6-9b09-4cc9-acaa-df2430ac0ac5','9ca31214-295b-4468-85cb-418731d04924','',NULL,NULL,NULL,'2024-07-29 08:35:58','2024-07-29 08:35:58');

/*Table structure for table `adjustment_types` */

DROP TABLE IF EXISTS `adjustment_types`;

CREATE TABLE `adjustment_types` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `adjustment_types` */

insert  into `adjustment_types`(`id`,`name`,`created_at`,`updated_at`) values 
(1,'Quantity Adjustment',NULL,NULL),
(2,'Value Adjustment',NULL,NULL);

/*Table structure for table `attributes` */

DROP TABLE IF EXISTS `attributes`;

CREATE TABLE `attributes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `attributes` */

insert  into `attributes`(`id`,`name`,`created_at`,`updated_at`) values 
(1,'Size',NULL,NULL),
(2,'Colour',NULL,NULL);

/*Table structure for table `banks` */

DROP TABLE IF EXISTS `banks`;

CREATE TABLE `banks` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `banks` */

/*Table structure for table `branches` */

DROP TABLE IF EXISTS `branches`;

CREATE TABLE `branches` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_person` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `state_id` bigint unsigned NOT NULL,
  `country_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `branches_country_id_foreign` (`country_id`),
  KEY `branches_state_id_foreign` (`state_id`),
  CONSTRAINT `branches_country_id_foreign` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `branches_state_id_foreign` FOREIGN KEY (`state_id`) REFERENCES `states` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `branches` */

insert  into `branches`(`id`,`name`,`address`,`contact_person`,`email`,`phone`,`state_id`,`country_id`,`created_at`,`updated_at`) values 
(3,'Head Office Branch','Kano Branch','Adamu','adama@app.com','0826554655',23,1,NULL,NULL),
(4,'Maraaba Branch','Kano Branch','Kabiru','adama@app.com','0826554655',9,1,NULL,NULL),
(5,'Mabushi Branch','Abuja Branch','Kabiru','adama@app.com','0826554655',9,1,NULL,NULL),
(6,'to be edited Maindddddddddddddddddddddiiiiiii','Maraba IjjjjI','Kabiru','kab@app.com','08866778765522',7,1,'2024-07-27 11:03:51','2024-07-27 11:03:51');

/*Table structure for table `brands` */

DROP TABLE IF EXISTS `brands`;

CREATE TABLE `brands` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `brands` */

insert  into `brands`(`id`,`name`,`created_at`,`updated_at`) values 
(1,'dangote brand','2024-06-26 13:02:36','2024-06-26 13:02:36'),
(2,'Dangote Industries Limited',NULL,NULL),
(3,'Cadbury Nigeria Plc',NULL,NULL),
(4,'Nestle Nigeria',NULL,NULL),
(5,'Ayoola Foods',NULL,NULL),
(6,'Crown Flour Mill Limited',NULL,NULL),
(7,'DUFIL Prima Foods Plc',NULL,NULL),
(8,'UAC Foods Limited',NULL,NULL);

/*Table structure for table `carriers` */

DROP TABLE IF EXISTS `carriers`;

CREATE TABLE `carriers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `carriers` */

insert  into `carriers`(`id`,`name`,`created_at`,`updated_at`) values 
(2,'Kwik Delivery',NULL,NULL),
(3,'Gokada',NULL,NULL),
(4,'GIG Logistics',NULL,NULL),
(5,'DHL Delivery Company',NULL,NULL);

/*Table structure for table `countries` */

DROP TABLE IF EXISTS `countries`;

CREATE TABLE `countries` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `countries` */

insert  into `countries`(`id`,`name`,`created_at`,`updated_at`) values 
(1,'Nigeria','2024-06-26 12:41:50','2024-06-26 12:41:50'),
(2,'Russia','2024-06-26 12:43:25','2024-06-26 12:43:25'),
(3,'United Kingdom','2024-06-27 15:43:49','2024-06-27 15:43:49'),
(4,'Nigeria',NULL,NULL),
(5,'Ghana',NULL,NULL),
(6,'Niger',NULL,NULL),
(7,'Togo',NULL,NULL),
(8,'Mali',NULL,NULL);

/*Table structure for table `create_items` */

DROP TABLE IF EXISTS `create_items`;

CREATE TABLE `create_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `item_category_id` bigint unsigned NOT NULL,
  `item_type_id` bigint unsigned NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `unit_id` bigint unsigned NOT NULL,
  `brand_id` bigint unsigned NOT NULL,
  `cost_price` double(8,2) NOT NULL,
  `selling_price` double(8,2) NOT NULL,
  `quantity` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reorder_level` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dimension_id` bigint unsigned NOT NULL,
  `weight_id` bigint unsigned NOT NULL,
  `branch_id` bigint unsigned NOT NULL,
  `warehouse` bigint unsigned NOT NULL,
  `vendor_id` bigint unsigned NOT NULL,
  `image_url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `barcode` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `create_items_weight_id_foreign` (`weight_id`),
  KEY `create_items_branch_id_foreign` (`branch_id`),
  KEY `create_items_brand_id_foreign` (`brand_id`),
  KEY `create_items_dimension_id_foreign` (`dimension_id`),
  KEY `create_items_item_category_id_foreign` (`item_category_id`),
  KEY `create_items_unit_id_foreign` (`unit_id`),
  KEY `create_items_vendor_id_foreign` (`vendor_id`),
  KEY `create_items_warehouse_foreign` (`warehouse`),
  CONSTRAINT `create_items_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `create_items_brand_id_foreign` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `create_items_dimension_id_foreign` FOREIGN KEY (`dimension_id`) REFERENCES `dimensions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `create_items_item_category_id_foreign` FOREIGN KEY (`item_category_id`) REFERENCES `item_categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `create_items_unit_id_foreign` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `create_items_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `create_items_warehouse_foreign` FOREIGN KEY (`warehouse`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `create_items_weight_id_foreign` FOREIGN KEY (`weight_id`) REFERENCES `weights` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `create_items` */

insert  into `create_items`(`id`,`name`,`item_category_id`,`item_type_id`,`description`,`batch_number`,`unit_id`,`brand_id`,`cost_price`,`selling_price`,`quantity`,`reorder_level`,`dimension_id`,`weight_id`,`branch_id`,`warehouse`,`vendor_id`,`image_url`,`barcode`,`created_at`,`updated_at`) values 
(2,'Viva Plus 180G',5,2,'Detergent','5545554',5,2,5000.00,0.00,NULL,'',2,3,3,5,1,'jjkkjhh','jjjkhjjj',NULL,NULL),
(3,'Delfin White Soap x 24',5,2,'Detergent','5545554',5,2,5000.00,0.00,NULL,'',2,3,3,5,1,'jjkkjhh','jjjkhjjj',NULL,NULL),
(4,'Chic Soap',5,2,'Detergent','5545554',5,2,5000.00,0.00,NULL,'',2,3,3,5,1,'jjkkjhh','jjjkhjjj',NULL,NULL),
(5,'Dangote Flour',5,2,'Detergent','5545554',5,2,5000.00,0.00,NULL,'',2,3,3,5,1,'jjkkjhh','jjjkhjjj',NULL,NULL),
(6,'Kings 5Ltrs x 4',5,2,'Detergent','5545554',5,2,5000.00,0.00,NULL,'',2,3,3,5,1,'jjkkjhh','jjjkhjjj',NULL,NULL),
(7,'Nittol Antiodour 90g x 51',5,2,'Detergent','5545554',5,2,5000.00,0.00,NULL,'',2,3,3,5,1,'jjkkjhh','jjjkhjjj',NULL,NULL),
(8,'Goldenvita 1kg',5,2,'Detergent','5545554',5,2,5000.00,0.00,NULL,'',2,3,3,5,1,'jjkkjhh','jjjkhjjj',NULL,NULL),
(9,'Viva Plus 350G',5,2,'Detergent','5545554',5,2,5000.00,0.00,NULL,'',2,3,3,5,1,'jjkkjhh','jjjkhjjj',NULL,NULL),
(11,'Viva 180',3,2,'Green leather','HGM12354',2,2,2500.00,2800.00,NULL,'Half',2,3,3,5,1,'haakksnks','urljsnjjs','2024-07-26 14:55:03','2024-07-26 14:55:03'),
(12,'Postsesrfe',2,2,'Green leather','HGM12354',1,1,2500.00,2800.00,NULL,'Half',2,2,3,5,1,'haakksnks','urljsnjjs','2024-07-29 11:32:18','2024-07-29 11:32:18'),
(13,'ttttt',2,2,'ttttt','6676',6,3,66767.00,676767.00,NULL,'6666',3,5,6,5,1,'6666','6666','2024-07-29 16:23:17','2024-07-29 16:23:17'),
(14,'777777777777',4,3,'777777777777','77777',6,7,999999.99,999999.99,NULL,'777777',4,5,5,6,1,'image url not available','777777','2024-07-30 04:58:41','2024-07-30 04:58:41'),
(15,'test create',3,2,'test create','899889',6,2,88888.00,888889.00,NULL,'mii',2,4,6,6,1,'image url not available','8980','2024-07-30 05:07:50','2024-07-30 05:07:50'),
(16,'test create',3,2,'test create','899889',2,2,88888.00,888889.00,NULL,'mii',3,5,5,7,1,'image url not available','8980','2024-07-30 05:24:42','2024-07-30 05:24:42');

/*Table structure for table `credit_limits` */

DROP TABLE IF EXISTS `credit_limits`;

CREATE TABLE `credit_limits` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `credit_limits` */

insert  into `credit_limits`(`id`,`name`,`created_at`,`updated_at`) values 
(1,'500000','2024-06-28 11:48:12','2024-06-28 11:48:12');

/*Table structure for table `credit_sales` */

DROP TABLE IF EXISTS `credit_sales`;

CREATE TABLE `credit_sales` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` bigint unsigned NOT NULL,
  `branch_id` bigint unsigned NOT NULL,
  `warehouse_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `credit_limit` bigint unsigned NOT NULL,
  `credit_amount` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `credit_balance` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `credit_sales_product_id_foreign` (`product_id`),
  KEY `credit_sales_branch_id_foreign` (`branch_id`),
  KEY `credit_sales_credit_limit_foreign` (`credit_limit`),
  KEY `credit_sales_customer_id_foreign` (`customer_id`),
  KEY `credit_sales_warehouse_id_foreign` (`warehouse_id`),
  CONSTRAINT `credit_sales_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `credit_sales_credit_limit_foreign` FOREIGN KEY (`credit_limit`) REFERENCES `credit_limits` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `credit_sales_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `credit_sales_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `create_items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `credit_sales_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `credit_sales` */

/*Table structure for table `customer_types` */

DROP TABLE IF EXISTS `customer_types`;

CREATE TABLE `customer_types` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `customer_types` */

insert  into `customer_types`(`id`,`name`,`created_at`,`updated_at`) values 
(2,'credit customer','2024-06-28 11:25:10','2024-06-28 11:25:10'),
(3,'Individual',NULL,NULL),
(4,'Business',NULL,NULL);

/*Table structure for table `customers` */

DROP TABLE IF EXISTS `customers`;

CREATE TABLE `customers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `customer_type_id` bigint unsigned NOT NULL,
  `title_id` bigint unsigned NOT NULL,
  `surname` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `firstname` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `middlename` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fullname` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `customers_customer_type_id_foreign` (`customer_type_id`),
  KEY `customers_title_id_foreign` (`title_id`),
  CONSTRAINT `customers_customer_type_id_foreign` FOREIGN KEY (`customer_type_id`) REFERENCES `customer_types` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `customers_title_id_foreign` FOREIGN KEY (`title_id`) REFERENCES `titles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `customers` */

/*Table structure for table `deliveries` */

DROP TABLE IF EXISTS `deliveries`;

CREATE TABLE `deliveries` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` bigint unsigned NOT NULL,
  `sales_order_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `delivery_order_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `delivery_date` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `carrier_id` bigint unsigned NOT NULL,
  `notes` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `deliveries_carrier_id_foreign` (`carrier_id`),
  KEY `deliveries_customer_id_foreign` (`customer_id`),
  CONSTRAINT `deliveries_carrier_id_foreign` FOREIGN KEY (`carrier_id`) REFERENCES `carriers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `deliveries_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `deliveries` */

/*Table structure for table `designations` */

DROP TABLE IF EXISTS `designations`;

CREATE TABLE `designations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `designations` */

insert  into `designations`(`id`,`name`,`created_at`,`updated_at`) values 
(1,'chief Cashier','2024-06-28 11:27:11','2024-06-28 11:27:11'),
(2,'Customer Service Manager',NULL,NULL),
(3,'Human Resource Manager',NULL,NULL),
(4,'Accountant',NULL,NULL);

/*Table structure for table `dimensions` */

DROP TABLE IF EXISTS `dimensions`;

CREATE TABLE `dimensions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `dimensions` */

insert  into `dimensions`(`id`,`name`,`created_at`,`updated_at`) values 
(2,'cm',NULL,NULL),
(3,'in',NULL,NULL),
(4,'KG','2024-07-10 13:15:35','2024-07-10 13:15:35');

/*Table structure for table `discounts` */

DROP TABLE IF EXISTS `discounts`;

CREATE TABLE `discounts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `discounts` */

insert  into `discounts`(`id`,`name`,`created_at`,`updated_at`) values 
(1,'1%',NULL,NULL),
(2,'2.5%',NULL,NULL),
(3,'5%',NULL,NULL),
(4,'7.5%',NULL,NULL),
(5,'10%',NULL,NULL);

/*Table structure for table `expense_accounts` */

DROP TABLE IF EXISTS `expense_accounts`;

CREATE TABLE `expense_accounts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `expense_accounts` */

/*Table structure for table `failed_jobs` */

DROP TABLE IF EXISTS `failed_jobs`;

CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `failed_jobs` */

/*Table structure for table `inventory_adjustments` */

DROP TABLE IF EXISTS `inventory_adjustments`;

CREATE TABLE `inventory_adjustments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `item_id` bigint unsigned NOT NULL,
  `adjustment_type_id` bigint unsigned DEFAULT NULL,
  `date` timestamp NOT NULL,
  `reason_id` bigint unsigned NOT NULL,
  `branch_id` bigint unsigned DEFAULT NULL,
  `warehouse_id` bigint unsigned DEFAULT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `item_category_id` bigint unsigned DEFAULT NULL,
  `cost_price` double(8,2) NOT NULL,
  `selling_price` double(8,2) NOT NULL,
  `quantity` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `inventory_adjustments_adjustment_type_id_foreign` (`adjustment_type_id`),
  KEY `inventory_adjustments_branch_id_foreign` (`branch_id`),
  KEY `inventory_adjustments_item_category_id_foreign` (`item_category_id`),
  KEY `inventory_adjustments_item_id_foreign` (`item_id`),
  KEY `inventory_adjustments_reason_id_foreign` (`reason_id`),
  KEY `inventory_adjustments_warehouse_id_foreign` (`warehouse_id`),
  CONSTRAINT `inventory_adjustments_adjustment_type_id_foreign` FOREIGN KEY (`adjustment_type_id`) REFERENCES `adjustment_types` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `inventory_adjustments_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `inventory_adjustments_item_category_id_foreign` FOREIGN KEY (`item_category_id`) REFERENCES `item_categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `inventory_adjustments_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `create_items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `inventory_adjustments_reason_id_foreign` FOREIGN KEY (`reason_id`) REFERENCES `reasons` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `inventory_adjustments_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `inventory_adjustments` */

insert  into `inventory_adjustments`(`id`,`item_id`,`adjustment_type_id`,`date`,`reason_id`,`branch_id`,`warehouse_id`,`description`,`item_category_id`,`cost_price`,`selling_price`,`quantity`,`created_at`,`updated_at`) values 
(6,2,1,'2024-07-13 00:00:00',2,NULL,NULL,'ooo',NULL,0.00,0.00,'90','2024-07-31 14:09:48','2024-07-31 14:09:48'),
(7,4,2,'2024-07-11 00:00:00',2,NULL,NULL,'',NULL,0.00,0.00,'','2024-07-31 14:19:41','2024-07-31 14:19:41'),
(9,3,1,'2024-07-20 00:00:00',2,NULL,NULL,'iiiii',NULL,0.00,0.00,'77','2024-07-31 16:45:15','2024-07-31 16:45:15');

/*Table structure for table `invoices` */

DROP TABLE IF EXISTS `invoices`;

CREATE TABLE `invoices` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` bigint unsigned NOT NULL,
  `warehouse_id` bigint unsigned NOT NULL,
  `customer_id` bigint unsigned NOT NULL,
  `invoice_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `order_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `invoice_date` timestamp NOT NULL,
  `item_id` bigint unsigned NOT NULL,
  `rate` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `discount_id` bigint unsigned NOT NULL,
  `tax_id` bigint unsigned NOT NULL,
  `amount` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `invoices_customer_id_foreign` (`customer_id`),
  KEY `invoices_discount_id_foreign` (`discount_id`),
  KEY `invoices_item_id_foreign` (`item_id`),
  KEY `invoices_tax_id_foreign` (`tax_id`),
  KEY `invoices_warehouse_id_foreign` (`warehouse_id`),
  CONSTRAINT `invoices_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `invoices_discount_id_foreign` FOREIGN KEY (`discount_id`) REFERENCES `discounts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `invoices_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `create_items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `invoices_tax_id_foreign` FOREIGN KEY (`tax_id`) REFERENCES `taxes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `invoices_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `invoices` */

/*Table structure for table `item_categories` */

DROP TABLE IF EXISTS `item_categories`;

CREATE TABLE `item_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `item_categories` */

insert  into `item_categories`(`id`,`name`,`created_at`,`updated_at`) values 
(2,'Dairy Product',NULL,NULL),
(3,'Drink',NULL,NULL),
(4,'Condiment',NULL,NULL),
(5,'Personal care',NULL,NULL),
(6,'Beverages snacks',NULL,NULL),
(7,'Pasta',NULL,NULL);

/*Table structure for table `item_types` */

DROP TABLE IF EXISTS `item_types`;

CREATE TABLE `item_types` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `item_types` */

insert  into `item_types`(`id`,`name`,`created_at`,`updated_at`) values 
(2,'Goods',NULL,NULL),
(3,'Service',NULL,NULL),
(4,'tested','2024-07-27 09:43:00','2024-07-27 09:43:00');

/*Table structure for table `manufacturers` */

DROP TABLE IF EXISTS `manufacturers`;

CREATE TABLE `manufacturers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `manufacturers` */

insert  into `manufacturers`(`id`,`name`,`created_at`,`updated_at`) values 
(1,'BUA Foods',NULL,NULL),
(2,'Golden Penny Foods',NULL,NULL),
(3,'Flour Mills of Nigeria',NULL,NULL),
(4,'Dangote Group',NULL,NULL),
(5,'Flour Mills of Nigeria plc.',NULL,NULL),
(6,'Nestle Nigeria plc.',NULL,NULL);

/*Table structure for table `migrations` */

DROP TABLE IF EXISTS `migrations`;

CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=62 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `migrations` */

insert  into `migrations`(`id`,`migration`,`batch`) values 
(1,'2014_10_12_100000_create_password_resets_table',1),
(2,'2016_06_01_000001_create_oauth_auth_codes_table',1),
(3,'2016_06_01_000002_create_oauth_access_tokens_table',1),
(4,'2016_06_01_000003_create_oauth_refresh_tokens_table',1),
(5,'2016_06_01_000004_create_oauth_clients_table',1),
(6,'2016_06_01_000005_create_oauth_personal_access_clients_table',1),
(7,'2019_08_19_000000_create_failed_jobs_table',1),
(8,'2019_12_14_000001_create_personal_access_tokens_table',1),
(9,'2024_06_21_161610_create_item_categories_table',1),
(10,'2024_06_21_161611_create_payment_terms_table',1),
(11,'2024_06_21_161612_create_item_types_table',1),
(12,'2024_06_21_161613_create_units_table',1),
(13,'2024_06_21_161614_create_dimensions_table',1),
(14,'2024_06_21_161615_create_weights_table',1),
(15,'2024_06_21_161616_create_expense_accounts_table',1),
(16,'2024_06_21_161617_create_adjustment_types_table',1),
(17,'2024_06_21_161618_create_statuses_table',1),
(18,'2024_06_21_161619_create_customer_types_table',1),
(19,'2024_06_21_161620_create_branches_table',1),
(20,'2024_06_21_161621_create_titles_table',1),
(21,'2024_06_21_161622_create_warehouses_table',1),
(22,'2024_06_21_161623_create_taxes_table',1),
(23,'2024_06_21_161624_create_discounts_table',1),
(24,'2024_06_21_161625_create_payment_modes_table',1),
(25,'2024_06_21_161626_create_carriers_table',1),
(26,'2024_06_21_161627_create_payment_types_table',1),
(27,'2024_06_21_161628_create_designations_table',1),
(28,'2024_06_21_161629_create_banks_table',1),
(29,'2024_06_21_161630_create_manufacturers_table',1),
(30,'2024_06_21_161631_create_vendor_types_table',1),
(31,'2024_06_21_161632_create_reasons_table',1),
(32,'2024_06_21_161633_create_brands_table',1),
(33,'2024_06_21_161634_create_attributes_table',1),
(34,'2024_06_21_161635_create_countries_table',1),
(35,'2024_06_21_161636_create_states_table',1),
(36,'2024_06_21_161637_create_create_items_table',1),
(37,'2024_06_21_161638_create_vendors_table',1),
(38,'2024_06_21_161639_create_transfer_orders_table',1),
(39,'2024_06_21_161640_create_deliveries_table',1),
(40,'2024_06_21_161641_create_inventory_adjustments_table',1),
(41,'2024_06_21_161642_create_invoices_table',1),
(42,'2024_06_21_161643_create_sales_table',1),
(43,'2024_06_21_161644_create_credit_limits_table',1),
(44,'2024_06_21_161645_create_credit_sales_table',1),
(45,'2024_06_21_161646_create_customers_table',1),
(46,'2024_06_21_161647_create_payment_receiveds_table',1),
(47,'2024_06_21_161648_create_sales_receipts_table',1),
(48,'2024_06_21_161649_create_payment_vouchers_table',1),
(49,'2024_06_21_161650_create_vendor_credits_table',1),
(50,'2024_06_21_161651_create_purchase_received_details_table',1),
(51,'2024_06_21_161652_create_roles_table',1),
(52,'2024_06_21_161653_create_users_table',1),
(53,'2024_06_21_161654_create_new_purchase_orders_table',1),
(54,'2024_06_21_161655_create_new_purchase_receiveds_table',1),
(55,'2024_06_21_161656_create_new_payments_table',1),
(56,'2024_06_21_161657_create_payment_voucher_details_table',1),
(57,'2024_06_21_161658_create_purchase_order_details_table',1),
(58,'2024_07_11_145756_create_refund_types_table',2),
(59,'2024_07_11_145713_create_store_types_table',3),
(60,'2024_07_11_145635_create_sales_type_table',4),
(61,'2024_07_25_152616_create_permission_tables',5);

/*Table structure for table `model_has_permissions` */

DROP TABLE IF EXISTS `model_has_permissions`;

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `model_has_permissions` */

/*Table structure for table `model_has_persmissions` */

DROP TABLE IF EXISTS `model_has_persmissions`;

CREATE TABLE `model_has_persmissions` (
  `permission_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_type`,`model_id`),
  CONSTRAINT `model_has_permissions_team_foreign_key_index` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `model_has_persmissions` */

/*Table structure for table `model_has_roles` */

DROP TABLE IF EXISTS `model_has_roles`;

CREATE TABLE `model_has_roles` (
  `role_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `model_has_roles` */

/*Table structure for table `new_payments` */

DROP TABLE IF EXISTS `new_payments`;

CREATE TABLE `new_payments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `vendor_id` bigint unsigned NOT NULL,
  `branch_id` bigint unsigned NOT NULL,
  `warehouse_id` bigint unsigned NOT NULL,
  `payment_amount` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_mode_id` bigint unsigned NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `new_payments_vendor_id_foreign` (`vendor_id`),
  KEY `new_payments_branch_id_foreign` (`branch_id`),
  KEY `new_payments_warehouse_id_foreign` (`warehouse_id`),
  KEY `new_payments_payment_mode_id_foreign` (`payment_mode_id`),
  CONSTRAINT `new_payments_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`),
  CONSTRAINT `new_payments_payment_mode_id_foreign` FOREIGN KEY (`payment_mode_id`) REFERENCES `payment_modes` (`id`),
  CONSTRAINT `new_payments_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`),
  CONSTRAINT `new_payments_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `new_payments` */

/*Table structure for table `new_purchase_orders` */

DROP TABLE IF EXISTS `new_purchase_orders`;

CREATE TABLE `new_purchase_orders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `item_category_id` bigint unsigned NOT NULL,
  `item_id` bigint unsigned NOT NULL,
  `vendor_id` bigint unsigned NOT NULL,
  `branch_id` bigint unsigned NOT NULL,
  `payment_mode_id` bigint unsigned NOT NULL,
  `purchase_order_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `purchase_amount` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `purchase_date` timestamp NOT NULL,
  `expected_delivery_date` date NOT NULL,
  `payment_type_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `new_purchase_orders_branch_id_foreign` (`branch_id`),
  KEY `new_purchase_orders_item_category_id_foreign` (`item_category_id`),
  KEY `new_purchase_orders_item_id_foreign` (`item_id`),
  KEY `new_purchase_orders_payment_mode_id_foreign` (`payment_mode_id`),
  KEY `new_purchase_orders_payment_type_id_foreign` (`payment_type_id`),
  KEY `new_purchase_orders_vendor_id_foreign` (`vendor_id`),
  CONSTRAINT `new_purchase_orders_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `new_purchase_orders_item_category_id_foreign` FOREIGN KEY (`item_category_id`) REFERENCES `item_categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `new_purchase_orders_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `create_items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `new_purchase_orders_payment_mode_id_foreign` FOREIGN KEY (`payment_mode_id`) REFERENCES `payment_modes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `new_purchase_orders_payment_type_id_foreign` FOREIGN KEY (`payment_type_id`) REFERENCES `payment_types` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `new_purchase_orders_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `new_purchase_orders` */

/*Table structure for table `new_purchase_receiveds` */

DROP TABLE IF EXISTS `new_purchase_receiveds`;

CREATE TABLE `new_purchase_receiveds` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `vendor_id` bigint unsigned NOT NULL,
  `purchase_order_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `purchase_received_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `received_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `new_purchase_receiveds_vendor_id_foreign` (`vendor_id`),
  CONSTRAINT `new_purchase_receiveds_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `new_purchase_receiveds` */

/*Table structure for table `oauth_access_tokens` */

DROP TABLE IF EXISTS `oauth_access_tokens`;

CREATE TABLE `oauth_access_tokens` (
  `id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `client_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `scopes` text COLLATE utf8mb4_unicode_ci,
  `revoked` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `oauth_access_tokens_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `oauth_access_tokens` */

/*Table structure for table `oauth_auth_codes` */

DROP TABLE IF EXISTS `oauth_auth_codes`;

CREATE TABLE `oauth_auth_codes` (
  `id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `client_id` bigint unsigned NOT NULL,
  `scopes` text COLLATE utf8mb4_unicode_ci,
  `revoked` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `oauth_auth_codes_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `oauth_auth_codes` */

/*Table structure for table `oauth_clients` */

DROP TABLE IF EXISTS `oauth_clients`;

CREATE TABLE `oauth_clients` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `secret` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `provider` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `redirect` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `personal_access_client` tinyint(1) NOT NULL,
  `password_client` tinyint(1) NOT NULL,
  `revoked` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `oauth_clients_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `oauth_clients` */

/*Table structure for table `oauth_personal_access_clients` */

DROP TABLE IF EXISTS `oauth_personal_access_clients`;

CREATE TABLE `oauth_personal_access_clients` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `oauth_personal_access_clients` */

/*Table structure for table `oauth_refresh_tokens` */

DROP TABLE IF EXISTS `oauth_refresh_tokens`;

CREATE TABLE `oauth_refresh_tokens` (
  `id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `access_token_id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `revoked` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `oauth_refresh_tokens_access_token_id_index` (`access_token_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `oauth_refresh_tokens` */

/*Table structure for table `password_resets` */

DROP TABLE IF EXISTS `password_resets`;

CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `password_resets` */

/*Table structure for table `payment_modes` */

DROP TABLE IF EXISTS `payment_modes`;

CREATE TABLE `payment_modes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `payment_modes` */

insert  into `payment_modes`(`id`,`name`,`created_at`,`updated_at`) values 
(1,'Cash',NULL,NULL),
(2,'Bank Tranfer',NULL,NULL),
(3,'Bank Remittance',NULL,NULL),
(4,'Credit',NULL,NULL),
(5,'Deposit',NULL,NULL);

/*Table structure for table `payment_receiveds` */

DROP TABLE IF EXISTS `payment_receiveds`;

CREATE TABLE `payment_receiveds` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` bigint unsigned NOT NULL,
  `amount_received` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bank_charges` double(8,2) NOT NULL,
  `payment_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deposit_bank_id` bigint unsigned NOT NULL,
  `payment_mode_id` bigint unsigned NOT NULL,
  `invoice_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payment_receiveds_customer_id_foreign` (`customer_id`),
  KEY `payment_receiveds_deposit_bank_id_foreign` (`deposit_bank_id`),
  KEY `payment_receiveds_payment_mode_id_foreign` (`payment_mode_id`),
  CONSTRAINT `payment_receiveds_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `payment_receiveds_deposit_bank_id_foreign` FOREIGN KEY (`deposit_bank_id`) REFERENCES `banks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `payment_receiveds_payment_mode_id_foreign` FOREIGN KEY (`payment_mode_id`) REFERENCES `payment_modes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `payment_receiveds` */

/*Table structure for table `payment_terms` */

DROP TABLE IF EXISTS `payment_terms`;

CREATE TABLE `payment_terms` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `payment_terms` */

insert  into `payment_terms`(`id`,`name`,`created_at`,`updated_at`) values 
(1,'Immediately',NULL,NULL),
(2,'Due on expected release date',NULL,NULL),
(3,'Due end of the week',NULL,NULL),
(4,'Due end of the month',NULL,NULL);

/*Table structure for table `payment_types` */

DROP TABLE IF EXISTS `payment_types`;

CREATE TABLE `payment_types` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `payment_types` */

insert  into `payment_types`(`id`,`name`,`created_at`,`updated_at`) values 
(1,'Payment before delivery',NULL,NULL);

/*Table structure for table `payment_voucher_details` */

DROP TABLE IF EXISTS `payment_voucher_details`;

CREATE TABLE `payment_voucher_details` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `Expense_account_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `item_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payment_voucher_details_item_id_foreign` (`item_id`),
  CONSTRAINT `payment_voucher_details_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `create_items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `payment_voucher_details` */

/*Table structure for table `payment_vouchers` */

DROP TABLE IF EXISTS `payment_vouchers`;

CREATE TABLE `payment_vouchers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint unsigned NOT NULL,
  `expense_date` timestamp NOT NULL,
  `amount` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `branch_id` bigint unsigned NOT NULL,
  `warehouse_id` bigint unsigned NOT NULL,
  `tax_id` bigint unsigned NOT NULL,
  `vendor_id` bigint unsigned NOT NULL,
  `payment_mode_id` bigint unsigned NOT NULL,
  `expense_account_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payment_vouchers_branch_id_foreign` (`branch_id`),
  KEY `payment_vouchers_expense_account_id_foreign` (`expense_account_id`),
  KEY `payment_vouchers_payment_mode_id_foreign` (`payment_mode_id`),
  KEY `payment_vouchers_product_id_foreign` (`product_id`),
  KEY `payment_vouchers_tax_id_foreign` (`tax_id`),
  KEY `payment_vouchers_vendor_id_foreign` (`vendor_id`),
  KEY `payment_vouchers_warehouse_id_foreign` (`warehouse_id`),
  CONSTRAINT `payment_vouchers_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `payment_vouchers_expense_account_id_foreign` FOREIGN KEY (`expense_account_id`) REFERENCES `expense_accounts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `payment_vouchers_payment_mode_id_foreign` FOREIGN KEY (`payment_mode_id`) REFERENCES `payment_modes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `payment_vouchers_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `create_items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `payment_vouchers_tax_id_foreign` FOREIGN KEY (`tax_id`) REFERENCES `taxes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `payment_vouchers_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `payment_vouchers_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `payment_vouchers` */

/*Table structure for table `permissions` */

DROP TABLE IF EXISTS `permissions`;

CREATE TABLE `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `permissions` */

/*Table structure for table `personal_access_tokens` */

DROP TABLE IF EXISTS `personal_access_tokens`;

CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `personal_access_tokens` */

insert  into `personal_access_tokens`(`id`,`tokenable_type`,`tokenable_id`,`name`,`token`,`abilities`,`last_used_at`,`expires_at`,`created_at`,`updated_at`) values 
(1,'App\\Models\\User',1,'authToken','a293995c33c2189b4401e60c0bdaccc780fab9f18e4f3d04fb8964439c7dc5cb','[\"*\"]',NULL,NULL,'2024-06-27 10:11:00','2024-06-27 10:11:00'),
(2,'App\\Models\\User',2,'authToken','9b9e3bd84799b613b0d980b125138847b0a23339f345a45564d13238f3874b05','[\"*\"]',NULL,NULL,'2024-07-05 10:39:09','2024-07-05 10:39:09');

/*Table structure for table `purchase_order_details` */

DROP TABLE IF EXISTS `purchase_order_details`;

CREATE TABLE `purchase_order_details` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `item_category_id` bigint unsigned NOT NULL,
  `purchase_order_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `item_id` bigint unsigned NOT NULL,
  `unit_price` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `unit_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `purchase_order_details_item_category_id_foreign` (`item_category_id`),
  KEY `purchase_order_details_item_id_foreign` (`item_id`),
  KEY `purchase_order_details_unit_id_foreign` (`unit_id`),
  CONSTRAINT `purchase_order_details_item_category_id_foreign` FOREIGN KEY (`item_category_id`) REFERENCES `item_categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `purchase_order_details_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `create_items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `purchase_order_details_unit_id_foreign` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `purchase_order_details` */

/*Table structure for table `purchase_received_details` */

DROP TABLE IF EXISTS `purchase_received_details`;

CREATE TABLE `purchase_received_details` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `new_purchased_received_id` bigint unsigned NOT NULL,
  `item_category_id` bigint unsigned NOT NULL,
  `item_id` bigint unsigned NOT NULL,
  `unit_price` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `unit_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `purchase_received_details_item_category_id_foreign` (`item_category_id`),
  KEY `purchase_received_details_item_id_foreign` (`item_id`),
  KEY `purchase_received_details_new_purchased_received_id_foreign` (`new_purchased_received_id`),
  KEY `purchase_received_details_unit_id_foreign` (`unit_id`),
  CONSTRAINT `purchase_received_details_item_category_id_foreign` FOREIGN KEY (`item_category_id`) REFERENCES `item_categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `purchase_received_details_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `create_items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `purchase_received_details_new_purchased_received_id_foreign` FOREIGN KEY (`new_purchased_received_id`) REFERENCES `new_purchase_receiveds` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `purchase_received_details_unit_id_foreign` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `purchase_received_details` */

/*Table structure for table `reasons` */

DROP TABLE IF EXISTS `reasons`;

CREATE TABLE `reasons` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `reasons` */

insert  into `reasons`(`id`,`name`,`created_at`,`updated_at`) values 
(1,'Stolen goods',NULL,NULL),
(2,'Damaged goods',NULL,NULL),
(3,'Stock Written off',NULL,NULL),
(4,'Stocktaking results',NULL,NULL),
(5,'Inventory Revaluation',NULL,NULL);

/*Table structure for table `refund_types` */

DROP TABLE IF EXISTS `refund_types`;

CREATE TABLE `refund_types` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `refund_types` */

insert  into `refund_types`(`id`,`name`,`created_at`,`updated_at`) values 
(1,'Cash Refund',NULL,NULL),
(2,'Bank Transfer',NULL,NULL);

/*Table structure for table `role_has_permissions` */

DROP TABLE IF EXISTS `role_has_permissions`;

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `role_has_permissions` */

/*Table structure for table `roles` */

DROP TABLE IF EXISTS `roles`;

CREATE TABLE `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `roles` */

insert  into `roles`(`id`,`name`,`guard_name`,`created_at`,`updated_at`) values 
(1,'Admin','','2024-07-26 14:23:33','2024-07-26 14:23:33'),
(2,'Store Manager','web','2024-07-26 14:25:20','2024-07-26 14:25:20');

/*Table structure for table `sales` */

DROP TABLE IF EXISTS `sales`;

CREATE TABLE `sales` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `branch_id` bigint unsigned NOT NULL,
  `warehouse_id` bigint unsigned NOT NULL,
  `quantity` double(8,2) NOT NULL,
  `price` double(8,2) NOT NULL,
  `discount_id` bigint unsigned NOT NULL,
  `discount` double(8,2) NOT NULL,
  `sales_order_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_amount` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount_paid` double(8,2) NOT NULL,
  `balance_amount` double(8,2) NOT NULL,
  `payment_mode` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sales_customer_id_foreign` (`customer_id`),
  KEY `sales_product_id_foreign` (`product_id`),
  KEY `sales_branch_id_foreign` (`branch_id`),
  KEY `sales_warehouse_id_foreign` (`warehouse_id`),
  KEY `sales_discount_id_foreign` (`discount_id`),
  KEY `sales_payment_mode_foreign` (`payment_mode`),
  CONSTRAINT `sales_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`),
  CONSTRAINT `sales_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  CONSTRAINT `sales_discount_id_foreign` FOREIGN KEY (`discount_id`) REFERENCES `discounts` (`id`),
  CONSTRAINT `sales_payment_mode_foreign` FOREIGN KEY (`payment_mode`) REFERENCES `payment_modes` (`id`),
  CONSTRAINT `sales_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `create_items` (`id`),
  CONSTRAINT `sales_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `sales` */

/*Table structure for table `sales_receipts` */

DROP TABLE IF EXISTS `sales_receipts`;

CREATE TABLE `sales_receipts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` bigint unsigned NOT NULL,
  `branch_id` bigint unsigned NOT NULL,
  `warehouse_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `tax_id` bigint unsigned NOT NULL,
  `payment_mode_id` bigint unsigned NOT NULL,
  `discount_id` bigint unsigned NOT NULL,
  `quantity` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rate` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `receipt_date` timestamp NOT NULL,
  `customer_note` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sales_receipts_branch_id_foreign` (`branch_id`),
  KEY `sales_receipts_customer_id_foreign` (`customer_id`),
  KEY `sales_receipts_discount_id_foreign` (`discount_id`),
  KEY `sales_receipts_payment_mode_id_foreign` (`payment_mode_id`),
  KEY `sales_receipts_product_id_foreign` (`product_id`),
  KEY `sales_receipts_tax_id_foreign` (`tax_id`),
  KEY `sales_receipts_warehouse_id_foreign` (`warehouse_id`),
  CONSTRAINT `sales_receipts_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `sales_receipts_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `sales_receipts_discount_id_foreign` FOREIGN KEY (`discount_id`) REFERENCES `discounts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `sales_receipts_payment_mode_id_foreign` FOREIGN KEY (`payment_mode_id`) REFERENCES `payment_modes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `sales_receipts_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `create_items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `sales_receipts_tax_id_foreign` FOREIGN KEY (`tax_id`) REFERENCES `taxes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `sales_receipts_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `sales_receipts` */

/*Table structure for table `sales_type` */

DROP TABLE IF EXISTS `sales_type`;

CREATE TABLE `sales_type` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `sales_type` */

insert  into `sales_type`(`id`,`name`,`created_at`,`updated_at`) values 
(1,'Cash Sales',NULL,NULL),
(2,'Credit Sales',NULL,NULL),
(3,'Bank Sales',NULL,NULL);

/*Table structure for table `states` */

DROP TABLE IF EXISTS `states`;

CREATE TABLE `states` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `states` */

insert  into `states`(`id`,`name`,`created_at`,`updated_at`) values 
(4,'FCT',NULL,NULL),
(5,'Abia',NULL,NULL),
(6,'Adamawa',NULL,NULL),
(7,'Akwa Ibom',NULL,NULL),
(8,'Anambra',NULL,NULL),
(9,'Bauchi',NULL,NULL),
(10,'Bayelsa',NULL,NULL),
(11,'Benue',NULL,NULL),
(12,'Borno',NULL,NULL),
(13,'Cross River',NULL,NULL),
(14,'Delta',NULL,NULL),
(15,'Ebonyi',NULL,NULL),
(16,'Edo',NULL,NULL),
(17,'Ekiti',NULL,NULL),
(18,'Enugu',NULL,NULL),
(19,'Gombe',NULL,NULL),
(20,'Imo',NULL,NULL),
(21,'Jigawa',NULL,NULL),
(22,'Kaduna',NULL,NULL),
(23,'Kano',NULL,NULL),
(24,'Katsina',NULL,NULL),
(25,'Kebbi',NULL,NULL),
(26,'Kogi',NULL,NULL),
(27,'Kwara',NULL,NULL),
(28,'Lagos',NULL,NULL),
(29,'Nasarawa',NULL,NULL),
(30,'Niger',NULL,NULL),
(31,'Ogun',NULL,NULL),
(32,'Ondo',NULL,NULL),
(33,'Osun',NULL,NULL),
(34,'Oyo',NULL,NULL),
(35,'Plateau',NULL,NULL),
(36,'Rivers',NULL,NULL),
(37,'Sokoto',NULL,NULL),
(38,'Taraba',NULL,NULL),
(39,'Yobe',NULL,NULL),
(40,'Zamfara',NULL,NULL);

/*Table structure for table `statuses` */

DROP TABLE IF EXISTS `statuses`;

CREATE TABLE `statuses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `statuses` */

insert  into `statuses`(`id`,`name`,`created_at`,`updated_at`) values 
(1,'Active','2024-06-27 09:41:23','2024-06-27 09:41:23');

/*Table structure for table `store_types` */

DROP TABLE IF EXISTS `store_types`;

CREATE TABLE `store_types` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `store_types` */

insert  into `store_types`(`id`,`name`,`created_at`,`updated_at`) values 
(1,'Mega Store',NULL,NULL),
(2,'Mini Store',NULL,NULL);

/*Table structure for table `stores` */

DROP TABLE IF EXISTS `stores`;

CREATE TABLE `stores` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `branch_id` bigint unsigned NOT NULL,
  `store_type_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `store_type_fk_id` (`store_type_id`),
  KEY `stores_branch_id_foreign` (`branch_id`),
  CONSTRAINT `store_type_fk_id` FOREIGN KEY (`store_type_id`) REFERENCES `store_types` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `stores_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `stores` */

insert  into `stores`(`id`,`name`,`branch_id`,`store_type_id`,`created_at`,`updated_at`) values 
(1,'Maraba Store',3,1,NULL,NULL),
(2,'Mabushi Store',3,1,NULL,NULL);

/*Table structure for table `taxes` */

DROP TABLE IF EXISTS `taxes`;

CREATE TABLE `taxes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `taxes` */

insert  into `taxes`(`id`,`name`,`created_at`,`updated_at`) values 
(1,'VAT',NULL,NULL),
(2,'No Tax',NULL,NULL);

/*Table structure for table `titles` */

DROP TABLE IF EXISTS `titles`;

CREATE TABLE `titles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `titles` */

insert  into `titles`(`id`,`name`,`created_at`,`updated_at`) values 
(1,'Sales Rep','2024-06-28 13:45:41','2024-06-28 13:45:41'),
(2,'Mr.',NULL,NULL),
(3,'Mrs.',NULL,NULL),
(4,'Alhaji',NULL,NULL),
(5,'Hajiya',NULL,NULL);

/*Table structure for table `transfer_orders` */

DROP TABLE IF EXISTS `transfer_orders`;

CREATE TABLE `transfer_orders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `transfer_order_number` varchar(100) DEFAULT NULL,
  `transfer_date` timestamp NULL DEFAULT NULL,
  `transfer_reason` bigint DEFAULT NULL,
  `source_id` bigint unsigned NOT NULL,
  `destination_id` bigint unsigned NOT NULL,
  `image_url` varchar(150) DEFAULT NULL,
  `transfer_quantity` varchar(150) DEFAULT NULL,
  `item_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `source_key_id` (`source_id`),
  KEY `destination_key_id` (`destination_id`),
  KEY `key_item_id` (`item_id`),
  CONSTRAINT `destination_key_id` FOREIGN KEY (`destination_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `key_item_id` FOREIGN KEY (`item_id`) REFERENCES `create_items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `source_key_id` FOREIGN KEY (`source_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

/*Data for the table `transfer_orders` */

insert  into `transfer_orders`(`id`,`transfer_order_number`,`transfer_date`,`transfer_reason`,`source_id`,`destination_id`,`image_url`,`transfer_quantity`,`item_id`,`created_at`,`updated_at`) values 
(3,NULL,'2024-07-11 00:00:00',3,6,5,NULL,'77',6,'2024-07-31 17:15:11','2024-07-31 17:15:11'),
(4,NULL,'2024-07-11 00:00:00',2,6,5,NULL,'66',3,'2024-07-31 17:57:58','2024-07-31 17:57:58'),
(5,NULL,'2024-07-11 00:00:00',4,7,6,NULL,'66',9,'2024-07-31 17:58:40','2024-07-31 17:58:40');

/*Table structure for table `units` */

DROP TABLE IF EXISTS `units`;

CREATE TABLE `units` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `units` */

insert  into `units`(`id`,`name`,`created_at`,`updated_at`) values 
(1,'tested','2024-06-26 13:03:31','2024-06-26 13:03:31'),
(2,'cm',NULL,NULL),
(3,'ft',NULL,NULL),
(4,'g',NULL,NULL),
(5,'kg',NULL,NULL),
(6,'mg',NULL,NULL),
(7,'m',NULL,NULL),
(8,'Ib',NULL,NULL),
(9,'in',NULL,NULL),
(10,'pcs',NULL,NULL),
(11,'cm',NULL,NULL),
(12,'in',NULL,NULL),
(13,'cm',NULL,NULL),
(14,'in',NULL,NULL);

/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `role_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status_id` bigint unsigned NOT NULL,
  `branch_id` bigint unsigned NOT NULL,
  `warehouse_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `users_branch_id_foreign` (`branch_id`),
  KEY `users_role_id_foreign` (`role_id`),
  KEY `users_status_id_foreign` (`status_id`),
  KEY `users_warehouse_id_foreign` (`warehouse_id`),
  CONSTRAINT `users_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `users_status_id_foreign` FOREIGN KEY (`status_id`) REFERENCES `statuses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `users_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `users` */

/*Table structure for table `vendor_credits` */

DROP TABLE IF EXISTS `vendor_credits`;

CREATE TABLE `vendor_credits` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `vendor_id` bigint unsigned NOT NULL,
  `warehouse_id` bigint unsigned NOT NULL,
  `credit_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `purchase_order_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `vendor_credit_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `vendor_credits_vendor_id_foreign` (`vendor_id`),
  KEY `vendor_credits_warehouse_id_foreign` (`warehouse_id`),
  CONSTRAINT `vendor_credits_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `vendor_credits_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `vendor_credits` */

/*Table structure for table `vendor_types` */

DROP TABLE IF EXISTS `vendor_types`;

CREATE TABLE `vendor_types` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `vendor_types` */

insert  into `vendor_types`(`id`,`name`,`created_at`,`updated_at`) values 
(1,'Bau','2024-06-26 13:12:45','2024-06-26 13:12:45'),
(2,'Bau','2024-06-27 14:01:09','2024-06-27 14:01:09'),
(3,'Service provider',NULL,NULL),
(4,'Manufacturer',NULL,NULL),
(5,'Retailer',NULL,NULL),
(6,'Distributors',NULL,NULL),
(7,'Access Bank',NULL,NULL),
(8,'First Bank',NULL,NULL),
(9,'GTB',NULL,NULL),
(10,'Fidelity Bank',NULL,NULL),
(11,'UBA Bank',NULL,NULL),
(12,'Jaiz Bank',NULL,NULL),
(13,'FCMB',NULL,NULL),
(14,'Eco Bank',NULL,NULL),
(15,'Moniepoint',NULL,NULL),
(16,'Opay',NULL,NULL);

/*Table structure for table `vendors` */

DROP TABLE IF EXISTS `vendors`;

CREATE TABLE `vendors` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_designation` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_surname` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_firstname` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_middlename` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_fullname` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `vendor_type_id` bigint unsigned NOT NULL,
  `phone_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image_url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tin` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bank_id` bigint unsigned NOT NULL,
  `account_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `vendors_vendor_type_id_foreign` (`vendor_type_id`),
  CONSTRAINT `vendors_vendor_type_id_foreign` FOREIGN KEY (`vendor_type_id`) REFERENCES `vendor_types` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `vendors` */

insert  into `vendors`(`id`,`name`,`address`,`contact_title`,`contact_designation`,`contact_surname`,`contact_firstname`,`contact_middlename`,`contact_fullname`,`vendor_type_id`,`phone_number`,`email`,`image_url`,`tin`,`bank_id`,`account_number`,`created_at`,`updated_at`) values 
(1,'Dangote Vendor','Wuye District of Abuja','Chief Salesman','Chief','Kabiru','Mohammed','Adamu','Kabiru Mohammed Adamu',1,'08123255452','kar@app.com','hahkensne','XLR-5235452',2,'85522654225','2024-06-26 13:33:05','2024-06-26 13:33:05');

/*Table structure for table `warehouses` */

DROP TABLE IF EXISTS `warehouses`;

CREATE TABLE `warehouses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `branch_id` bigint unsigned NOT NULL,
  `warehouse_address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `zipcode` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_person` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `warehouses_branch_id_foreign` (`branch_id`),
  CONSTRAINT `warehouses_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `warehouses` */

insert  into `warehouses`(`id`,`name`,`branch_id`,`warehouse_address`,`zipcode`,`contact_person`,`email`,`phone`,`created_at`,`updated_at`) values 
(5,'Kano Warehouse',3,'Sabon Geri Kano','522554','Hamza Mohammed','ham@app.com','0851115555',NULL,NULL),
(6,'Abuja Warehouse',5,'Sabon Geri Kano','522554','Hamza Mohammed','ham@app.com','0851115555',NULL,NULL),
(7,'Main',4,'Abuja-maraba','1255245','Usman','main@app.co','08132556428','2024-07-27 11:07:45','2024-07-27 11:07:45');

/*Table structure for table `weights` */

DROP TABLE IF EXISTS `weights`;

CREATE TABLE `weights` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `weights` */

insert  into `weights`(`id`,`name`,`created_at`,`updated_at`) values 
(2,'50mg','2024-07-02 10:35:21','2024-07-02 10:35:21'),
(3,'kg',NULL,NULL),
(4,'g',NULL,NULL),
(5,'ib',NULL,NULL),
(6,'500kg','2024-07-26 14:18:39','2024-07-26 14:18:39');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
