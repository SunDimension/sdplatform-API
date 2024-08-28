/*
SQLyog Community v13.1.7 (64 bit)
MySQL - 11.1.2-MariaDB : Database - global-hamir-venture
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`global-hamir-venture` /*!40100 DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci */;

USE `global-hamir-venture`;

/*Table structure for table `account_groups` */

DROP TABLE IF EXISTS `account_groups`;

CREATE TABLE `account_groups` (
  `id` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `account_groups` */

/*Table structure for table `account_opening_balances` */

DROP TABLE IF EXISTS `account_opening_balances`;

CREATE TABLE `account_opening_balances` (
  `id` char(36) NOT NULL,
  `financial_year_id` char(36) NOT NULL,
  `financial_period_id` char(36) NOT NULL,
  `debit` double NOT NULL,
  `credit` double NOT NULL,
  `amount` double NOT NULL,
  `warehouse_id` bigint(20) unsigned NOT NULL,
  `account_no` varchar(255) NOT NULL,
  `account_id` char(36) NOT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `modified_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `account_opening_balances_financial_year_id_foreign` (`financial_year_id`),
  KEY `account_opening_balances_financial_period_id_foreign` (`financial_period_id`),
  KEY `account_opening_balances_warehouse_id_foreign` (`warehouse_id`),
  KEY `account_opening_balances_account_id_foreign` (`account_id`),
  KEY `account_opening_balances_created_by_foreign` (`created_by`),
  KEY `account_opening_balances_modified_by_foreign` (`modified_by`),
  KEY `account_opening_balances_deleted_by_foreign` (`deleted_by`),
  CONSTRAINT `account_opening_balances_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`),
  CONSTRAINT `account_opening_balances_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  CONSTRAINT `account_opening_balances_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`),
  CONSTRAINT `account_opening_balances_financial_period_id_foreign` FOREIGN KEY (`financial_period_id`) REFERENCES `financial_periods` (`id`),
  CONSTRAINT `account_opening_balances_financial_year_id_foreign` FOREIGN KEY (`financial_year_id`) REFERENCES `financial_years` (`id`),
  CONSTRAINT `account_opening_balances_modified_by_foreign` FOREIGN KEY (`modified_by`) REFERENCES `users` (`id`),
  CONSTRAINT `account_opening_balances_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `account_opening_balances` */

/*Table structure for table `account_subtypes` */

DROP TABLE IF EXISTS `account_subtypes`;

CREATE TABLE `account_subtypes` (
  `id` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `account_type_id` char(36) NOT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `modified_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
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

/*Table structure for table `account_types` */

DROP TABLE IF EXISTS `account_types`;

CREATE TABLE `account_types` (
  `id` char(36) NOT NULL,
  `account_group_id` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `modified_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
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

/*Table structure for table `accounts` */

DROP TABLE IF EXISTS `accounts`;

CREATE TABLE `accounts` (
  `id` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `account_group_id` char(36) NOT NULL,
  `account_type_id` char(36) NOT NULL,
  `account_subtype_id` char(36) NOT NULL,
  `account_owner_id` varchar(255) DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `modified_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
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

/*Table structure for table `adjustment_types` */

DROP TABLE IF EXISTS `adjustment_types`;

CREATE TABLE `adjustment_types` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `adjustment_types` */

/*Table structure for table `attributes` */

DROP TABLE IF EXISTS `attributes`;

CREATE TABLE `attributes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `attributes` */

/*Table structure for table `banks` */

DROP TABLE IF EXISTS `banks`;

CREATE TABLE `banks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `banks` */

insert  into `banks`(`id`,`name`,`created_at`,`updated_at`) values 
(1,Access Bank,NULL,NULL),
(2,First Bank,NULL,NULL),
(3,GTB,NULL,NULL),
(4,Fidelity Bank,NULL,NULL),
(5,UBA Bank,NULL,NULL),
(6,Jaiz Bank,NULL,NULL),
(7,FCMB,NULL,NULL),
(8,Eco Bank,NULL,NULL),
(9,Moniepoint,NULL,NULL),
(10,Opay,NULL,NULL);

/*Table structure for table `branches` */

DROP TABLE IF EXISTS `branches`;

CREATE TABLE `branches` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `contact_person` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `state_id` bigint(20) unsigned NOT NULL,
  `country_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `branches_state_id_foreign` (`state_id`),
  KEY `branches_country_id_foreign` (`country_id`),
  CONSTRAINT `branches_country_id_foreign` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`),
  CONSTRAINT `branches_state_id_foreign` FOREIGN KEY (`state_id`) REFERENCES `states` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `branches` */

insert  into `branches`(`id`,`name`,`address`,`contact_person`,`email`,`phone`,`state_id`,`country_id`,`created_at`,`updated_at`) values 
(1,Head Office Branch,Kano Branch,Adamu,adama@app.com,0826554655,23,1,NULL,NULL),
(2,Maraaba Branch,Kano Branch,Kabiru,adama@app.com,0826554655,9,1,NULL,NULL),
(3,Mabushi Branch,Abuja Branch,Kabiru,adama@app.com,0826554655,9,1,NULL,NULL);

/*Table structure for table `brands` */

DROP TABLE IF EXISTS `brands`;

CREATE TABLE `brands` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `brands` */

insert  into `brands`(`id`,`name`,`created_at`,`updated_at`) values 
(1,Dangote Industries Limited,NULL,NULL),
(2,Cadbury Nigeria Plc,NULL,NULL),
(3,Nestle Nigeria,NULL,NULL),
(4,Ayoola Foods,NULL,NULL),
(5,Crown Flour Mill Limited,NULL,NULL),
(6,DUFIL Prima Foods Plc,NULL,NULL),
(7,UAC Foods Limited,NULL,NULL);

/*Table structure for table `carriers` */

DROP TABLE IF EXISTS `carriers`;

CREATE TABLE `carriers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `carriers` */

/*Table structure for table `chart_cards` */

DROP TABLE IF EXISTS `chart_cards`;

CREATE TABLE `chart_cards` (
  `id` char(36) NOT NULL,
  `card_title` varchar(255) NOT NULL,
  `card_size` varchar(255) NOT NULL,
  `is_active` varchar(255) NOT NULL,
  `sql_query` text NOT NULL,
  `module_id` varchar(255) NOT NULL,
  `submodule_id` varchar(255) NOT NULL,
  `sequence` varchar(255) NOT NULL,
  `color` varchar(255) NOT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `modified_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `chart_cards_created_by_foreign` (`created_by`),
  KEY `chart_cards_modified_by_foreign` (`modified_by`),
  KEY `chart_cards_deleted_by_foreign` (`deleted_by`),
  CONSTRAINT `chart_cards_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  CONSTRAINT `chart_cards_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`),
  CONSTRAINT `chart_cards_modified_by_foreign` FOREIGN KEY (`modified_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `chart_cards` */

/*Table structure for table `chart_categories` */

DROP TABLE IF EXISTS `chart_categories`;

CREATE TABLE `chart_categories` (
  `id` char(36) NOT NULL,
  `chart_provider_id` char(36) NOT NULL,
  `chart_category` varchar(255) NOT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `modified_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `chart_categories_chart_provider_id_foreign` (`chart_provider_id`),
  KEY `chart_categories_created_by_foreign` (`created_by`),
  KEY `chart_categories_modified_by_foreign` (`modified_by`),
  KEY `chart_categories_deleted_by_foreign` (`deleted_by`),
  CONSTRAINT `chart_categories_chart_provider_id_foreign` FOREIGN KEY (`chart_provider_id`) REFERENCES `chart_providers` (`id`),
  CONSTRAINT `chart_categories_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  CONSTRAINT `chart_categories_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`),
  CONSTRAINT `chart_categories_modified_by_foreign` FOREIGN KEY (`modified_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `chart_categories` */

/*Table structure for table `chart_providers` */

DROP TABLE IF EXISTS `chart_providers`;

CREATE TABLE `chart_providers` (
  `id` char(36) NOT NULL,
  `chart_provider` varchar(255) NOT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `modified_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `chart_providers_created_by_foreign` (`created_by`),
  KEY `chart_providers_modified_by_foreign` (`modified_by`),
  KEY `chart_providers_deleted_by_foreign` (`deleted_by`),
  CONSTRAINT `chart_providers_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  CONSTRAINT `chart_providers_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`),
  CONSTRAINT `chart_providers_modified_by_foreign` FOREIGN KEY (`modified_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `chart_providers` */

/*Table structure for table `chart_types` */

DROP TABLE IF EXISTS `chart_types`;

CREATE TABLE `chart_types` (
  `id` char(36) NOT NULL,
  `chart_category_id` char(36) NOT NULL,
  `chart_type` varchar(255) NOT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `modified_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `chart_types_chart_category_id_foreign` (`chart_category_id`),
  KEY `chart_types_created_by_foreign` (`created_by`),
  KEY `chart_types_modified_by_foreign` (`modified_by`),
  KEY `chart_types_deleted_by_foreign` (`deleted_by`),
  CONSTRAINT `chart_types_chart_category_id_foreign` FOREIGN KEY (`chart_category_id`) REFERENCES `chart_categories` (`id`),
  CONSTRAINT `chart_types_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  CONSTRAINT `chart_types_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`),
  CONSTRAINT `chart_types_modified_by_foreign` FOREIGN KEY (`modified_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `chart_types` */

/*Table structure for table `charts` */

DROP TABLE IF EXISTS `charts`;

CREATE TABLE `charts` (
  `id` char(36) NOT NULL,
  `chart_title` varchar(255) NOT NULL,
  `chart_type_id` char(36) NOT NULL,
  `chart_category_id` char(36) NOT NULL,
  `sql_query` text NOT NULL,
  `is_active` varchar(255) NOT NULL,
  `module_id` varchar(255) NOT NULL,
  `filterColumn` varchar(255) NOT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `modified_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `charts_chart_type_id_foreign` (`chart_type_id`),
  KEY `charts_chart_category_id_foreign` (`chart_category_id`),
  KEY `charts_created_by_foreign` (`created_by`),
  KEY `charts_modified_by_foreign` (`modified_by`),
  KEY `charts_deleted_by_foreign` (`deleted_by`),
  CONSTRAINT `charts_chart_category_id_foreign` FOREIGN KEY (`chart_category_id`) REFERENCES `chart_categories` (`id`),
  CONSTRAINT `charts_chart_type_id_foreign` FOREIGN KEY (`chart_type_id`) REFERENCES `chart_types` (`id`),
  CONSTRAINT `charts_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  CONSTRAINT `charts_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`),
  CONSTRAINT `charts_modified_by_foreign` FOREIGN KEY (`modified_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `charts` */

/*Table structure for table `countries` */

DROP TABLE IF EXISTS `countries`;

CREATE TABLE `countries` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `countries` */

insert  into `countries`(`id`,`name`,`created_at`,`updated_at`) values 
(1,Nigeria,NULL,NULL),
(2,Ghana,NULL,NULL),
(3,Niger,NULL,NULL),
(4,Togo,NULL,NULL),
(5,Mali,NULL,NULL);

/*Table structure for table `create_items` */

DROP TABLE IF EXISTS `create_items`;

CREATE TABLE `create_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `item_category_id` bigint(20) unsigned NOT NULL,
  `item_type_id` bigint(20) unsigned NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `batch_number` varchar(255) DEFAULT NULL,
  `unit_id` bigint(20) unsigned NOT NULL,
  `brand_id` bigint(20) unsigned NOT NULL,
  `cost_price` double(8,2) DEFAULT NULL,
  `selling_price` double(8,2) DEFAULT NULL,
  `reorder_level` varchar(255) DEFAULT NULL,
  `dimension_id` bigint(20) unsigned NOT NULL,
  `Quantity` varchar(255) DEFAULT NULL,
  `weight_id` bigint(20) unsigned NOT NULL,
  `branch_id` bigint(20) unsigned NOT NULL,
  `warehouse` bigint(20) unsigned NOT NULL,
  `vendor_id` bigint(20) unsigned NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `store_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `barcode` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `create_items_item_category_id_foreign` (`item_category_id`),
  KEY `create_items_unit_id_foreign` (`unit_id`),
  KEY `create_items_brand_id_foreign` (`brand_id`),
  KEY `create_items_dimension_id_foreign` (`dimension_id`),
  KEY `create_items_weight_id_foreign` (`weight_id`),
  KEY `create_items_branch_id_foreign` (`branch_id`),
  KEY `create_items_warehouse_foreign` (`warehouse`),
  KEY `create_items_vendor_id_foreign` (`vendor_id`),
  KEY `create_store_fk` (`store_id`),
  KEY `create_user_fk` (`user_id`),
  CONSTRAINT `create_items_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`),
  CONSTRAINT `create_items_brand_id_foreign` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`),
  CONSTRAINT `create_items_dimension_id_foreign` FOREIGN KEY (`dimension_id`) REFERENCES `dimensions` (`id`),
  CONSTRAINT `create_items_item_category_id_foreign` FOREIGN KEY (`item_category_id`) REFERENCES `item_categories` (`id`),
  CONSTRAINT `create_items_unit_id_foreign` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`),
  CONSTRAINT `create_items_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`),
  CONSTRAINT `create_items_warehouse_foreign` FOREIGN KEY (`warehouse`) REFERENCES `warehouses` (`id`),
  CONSTRAINT `create_items_weight_id_foreign` FOREIGN KEY (`weight_id`) REFERENCES `weights` (`id`),
  CONSTRAINT `create_store_fk` FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`),
  CONSTRAINT `create_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `create_items` */

insert  into `create_items`(`id`,`name`,`item_category_id`,`item_type_id`,`description`,`batch_number`,`unit_id`,`brand_id`,`cost_price`,`selling_price`,`reorder_level`,`dimension_id`,`Quantity`,`weight_id`,`branch_id`,`warehouse`,`vendor_id`,`image_url`,`store_id`,`user_id`,`barcode`,`created_at`,`updated_at`) values 
(25,Viva 5,3,2,Green leather,HGV-20240805145602-4,2,2,2500.00,2400.00,Half,2,NULL,3,1,3,1,haakksnks,1,5,urljsnjjs,2024-08-05 14:56:02,2024-08-05 14:56:02),
(26,Viva 5,3,2,Green leather,HGV-20240805145759-9,2,2,2500.00,2400.00,Half,2,NULL,3,1,3,1,haakksnks,1,5,urljsnjjs,2024-08-05 14:57:59,2024-08-05 14:57:59),
(27,Viva 5,3,2,Green leather,HGV-20240805145957-951339176,2,2,2500.00,2400.00,Half,2,NULL,3,1,3,1,haakksnks,1,5,urljsnjjs,2024-08-05 14:59:57,2024-08-05 14:59:57),
(28,Viva 5,3,2,Green leather,HGV-20240805151143-200428831,2,2,2500.00,2400.00,Half,2,NULL,3,1,3,1,haakksnks,1,5,urljsnjjs,2024-08-05 15:11:43,2024-08-05 15:11:43),
(29,Viva 5,3,2,Green leather,HGV-20240805151351-1992434680,2,2,2500.00,2400.00,Half,2,NULL,3,1,3,1,haakksnks,1,5,urljsnjjs,2024-08-05 15:13:51,2024-08-05 15:13:51),
(30,Dangote Noodles,3,2,Green leather,HGV-20240805153113-521477792,2,2,2500.00,2400.00,Half,2,NULL,3,1,3,1,haakksnks,1,5,urljsnjjs,2024-08-05 15:31:13,2024-08-05 15:31:13),
(31,Bua Noodles,3,2,Green leather,HGV-20240805153238-661989438,2,2,2500.00,2400.00,Half,2,NULL,3,1,3,1,haakksnks,1,5,urljsnjjs,2024-08-05 15:32:38,2024-08-05 15:32:38),
(32,Big Bull rice,3,2,Green leather,HGV-20240805153539-1887199968,2,2,2500.00,2400.00,Half,2,500 carton,3,1,3,1,haakksnks,1,5,urljsnjjs,2024-08-05 15:35:39,2024-08-05 15:35:39),
(33,Bull rice,3,2,Green leather,HGV-20240815132550-1174055892,2,2,2500.00,2400.00,Half,2,500 carton,3,1,3,1,haakksnks,1,5,NULL,2024-08-15 13:25:50,2024-08-15 13:25:50);

/*Table structure for table `credit_limits` */

DROP TABLE IF EXISTS `credit_limits`;

CREATE TABLE `credit_limits` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `credit_limits` */

/*Table structure for table `credit_sales` */

DROP TABLE IF EXISTS `credit_sales`;

CREATE TABLE `credit_sales` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` bigint(20) unsigned NOT NULL,
  `branch_id` bigint(20) unsigned NOT NULL,
  `warehouse_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned NOT NULL,
  `credit_limit` bigint(20) unsigned NOT NULL,
  `credit_amount` varchar(255) NOT NULL,
  `credit_balance` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `credit_sales_customer_id_foreign` (`customer_id`),
  KEY `credit_sales_branch_id_foreign` (`branch_id`),
  KEY `credit_sales_warehouse_id_foreign` (`warehouse_id`),
  KEY `credit_sales_product_id_foreign` (`product_id`),
  KEY `credit_sales_credit_limit_foreign` (`credit_limit`),
  CONSTRAINT `credit_sales_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`),
  CONSTRAINT `credit_sales_credit_limit_foreign` FOREIGN KEY (`credit_limit`) REFERENCES `credit_limits` (`id`),
  CONSTRAINT `credit_sales_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  CONSTRAINT `credit_sales_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `create_items` (`id`),
  CONSTRAINT `credit_sales_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `credit_sales` */

/*Table structure for table `customer_types` */

DROP TABLE IF EXISTS `customer_types`;

CREATE TABLE `customer_types` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `customer_types` */

insert  into `customer_types`(`id`,`name`,`created_at`,`updated_at`) values 
(1,Major,2024-08-05 13:53:42,2024-08-05 13:53:42);

/*Table structure for table `customers` */

DROP TABLE IF EXISTS `customers`;

CREATE TABLE `customers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `customer_type_id` bigint(20) unsigned NOT NULL,
  `title_id` bigint(20) unsigned NOT NULL,
  `surname` varchar(255) NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `middlename` varchar(255) NOT NULL,
  `phone_number` varchar(255) NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `customers_customer_type_id_foreign` (`customer_type_id`),
  KEY `customers_title_id_foreign` (`title_id`),
  CONSTRAINT `customers_customer_type_id_foreign` FOREIGN KEY (`customer_type_id`) REFERENCES `customer_types` (`id`),
  CONSTRAINT `customers_title_id_foreign` FOREIGN KEY (`title_id`) REFERENCES `titles` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `customers` */

insert  into `customers`(`id`,`customer_type_id`,`title_id`,`surname`,`firstname`,`middlename`,`phone_number`,`fullname`,`created_at`,`updated_at`) values 
(1,1,1,ggg,bbb,dd,09999999999,ggg bbb,2024-08-05 13:56:55,2024-08-05 13:56:55);

/*Table structure for table `dashboard_settings` */

DROP TABLE IF EXISTS `dashboard_settings`;

CREATE TABLE `dashboard_settings` (
  `id` char(36) NOT NULL,
  `chart_id` char(36) NOT NULL,
  `module_id` char(36) NOT NULL,
  `chart_type_id` char(36) NOT NULL,
  `chart_category_id` char(36) NOT NULL,
  `chart_title` varchar(255) NOT NULL,
  `is_active` varchar(255) NOT NULL,
  `order_by` varchar(255) NOT NULL,
  `is_group` varchar(255) NOT NULL,
  `submodule_Id` varchar(255) NOT NULL,
  `add_condition` varchar(255) NOT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `modified_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `dashboard_settings_chart_id_foreign` (`chart_id`),
  KEY `dashboard_settings_module_id_foreign` (`module_id`),
  KEY `dashboard_settings_chart_type_id_foreign` (`chart_type_id`),
  KEY `dashboard_settings_chart_category_id_foreign` (`chart_category_id`),
  KEY `dashboard_settings_created_by_foreign` (`created_by`),
  KEY `dashboard_settings_modified_by_foreign` (`modified_by`),
  KEY `dashboard_settings_deleted_by_foreign` (`deleted_by`),
  CONSTRAINT `dashboard_settings_chart_category_id_foreign` FOREIGN KEY (`chart_category_id`) REFERENCES `chart_categories` (`id`),
  CONSTRAINT `dashboard_settings_chart_id_foreign` FOREIGN KEY (`chart_id`) REFERENCES `charts` (`id`),
  CONSTRAINT `dashboard_settings_chart_type_id_foreign` FOREIGN KEY (`chart_type_id`) REFERENCES `chart_types` (`id`),
  CONSTRAINT `dashboard_settings_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  CONSTRAINT `dashboard_settings_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`),
  CONSTRAINT `dashboard_settings_modified_by_foreign` FOREIGN KEY (`modified_by`) REFERENCES `users` (`id`),
  CONSTRAINT `dashboard_settings_module_id_foreign` FOREIGN KEY (`module_id`) REFERENCES `modules` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `dashboard_settings` */

/*Table structure for table `deliveries` */

DROP TABLE IF EXISTS `deliveries`;

CREATE TABLE `deliveries` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` bigint(20) unsigned NOT NULL,
  `sales_order_number` varchar(255) NOT NULL,
  `delivery_order_number` varchar(255) NOT NULL,
  `delivery_date` timestamp NOT NULL,
  `carrier_id` bigint(20) unsigned NOT NULL,
  `notes` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `deliveries_customer_id_foreign` (`customer_id`),
  KEY `deliveries_carrier_id_foreign` (`carrier_id`),
  CONSTRAINT `deliveries_carrier_id_foreign` FOREIGN KEY (`carrier_id`) REFERENCES `carriers` (`id`),
  CONSTRAINT `deliveries_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `deliveries` */

/*Table structure for table `designations` */

DROP TABLE IF EXISTS `designations`;

CREATE TABLE `designations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `designations` */

/*Table structure for table `dimensions` */

DROP TABLE IF EXISTS `dimensions`;

CREATE TABLE `dimensions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `dimensions` */

insert  into `dimensions`(`id`,`name`,`created_at`,`updated_at`) values 
(1,cm,NULL,NULL),
(2,in,NULL,NULL);

/*Table structure for table `discounts` */

DROP TABLE IF EXISTS `discounts`;

CREATE TABLE `discounts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `discounts` */

/*Table structure for table `expense_accounts` */

DROP TABLE IF EXISTS `expense_accounts`;

CREATE TABLE `expense_accounts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `expense_accounts` */

/*Table structure for table `failed_jobs` */

DROP TABLE IF EXISTS `failed_jobs`;

CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `failed_jobs` */

/*Table structure for table `financial_periods` */

DROP TABLE IF EXISTS `financial_periods`;

CREATE TABLE `financial_periods` (
  `id` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `date_from` date NOT NULL,
  `date_to` date NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `financial_year_id` char(36) NOT NULL,
  `financial_quarter_id` char(36) NOT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `modified_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `financial_periods_financial_year_id_foreign` (`financial_year_id`),
  KEY `financial_periods_financial_quarter_id_foreign` (`financial_quarter_id`),
  KEY `financial_periods_created_by_foreign` (`created_by`),
  KEY `financial_periods_modified_by_foreign` (`modified_by`),
  KEY `financial_periods_deleted_by_foreign` (`deleted_by`),
  CONSTRAINT `financial_periods_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  CONSTRAINT `financial_periods_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`),
  CONSTRAINT `financial_periods_financial_quarter_id_foreign` FOREIGN KEY (`financial_quarter_id`) REFERENCES `financial_quarters` (`id`),
  CONSTRAINT `financial_periods_financial_year_id_foreign` FOREIGN KEY (`financial_year_id`) REFERENCES `financial_years` (`id`),
  CONSTRAINT `financial_periods_modified_by_foreign` FOREIGN KEY (`modified_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `financial_periods` */

/*Table structure for table `financial_quarters` */

DROP TABLE IF EXISTS `financial_quarters`;

CREATE TABLE `financial_quarters` (
  `id` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `date_from` date NOT NULL,
  `date_to` date NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `financial_year_id` char(36) NOT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `modified_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `financial_quarters_financial_year_id_foreign` (`financial_year_id`),
  KEY `financial_quarters_created_by_foreign` (`created_by`),
  KEY `financial_quarters_modified_by_foreign` (`modified_by`),
  KEY `financial_quarters_deleted_by_foreign` (`deleted_by`),
  CONSTRAINT `financial_quarters_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  CONSTRAINT `financial_quarters_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`),
  CONSTRAINT `financial_quarters_financial_year_id_foreign` FOREIGN KEY (`financial_year_id`) REFERENCES `financial_years` (`id`),
  CONSTRAINT `financial_quarters_modified_by_foreign` FOREIGN KEY (`modified_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `financial_quarters` */

/*Table structure for table `financial_years` */

DROP TABLE IF EXISTS `financial_years`;

CREATE TABLE `financial_years` (
  `id` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `date_from` date NOT NULL,
  `date_to` date NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `modified_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `financial_years_created_by_foreign` (`created_by`),
  KEY `financial_years_modified_by_foreign` (`modified_by`),
  KEY `financial_years_deleted_by_foreign` (`deleted_by`),
  CONSTRAINT `financial_years_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  CONSTRAINT `financial_years_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`),
  CONSTRAINT `financial_years_modified_by_foreign` FOREIGN KEY (`modified_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `financial_years` */

/*Table structure for table `inventory_adjustments` */

DROP TABLE IF EXISTS `inventory_adjustments`;

CREATE TABLE `inventory_adjustments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `item_id` bigint(20) unsigned DEFAULT NULL,
  `adjustment_type_id` bigint(20) unsigned DEFAULT NULL,
  `date` timestamp NULL DEFAULT NULL,
  `reason_id` bigint(20) unsigned DEFAULT NULL,
  `branch_id` bigint(20) unsigned DEFAULT NULL,
  `warehouse_id` bigint(20) unsigned DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `item_category_id` bigint(20) unsigned DEFAULT NULL,
  `cost_price` double(8,2) DEFAULT NULL,
  `selling_price` double(8,2) DEFAULT NULL,
  `quantity` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `inventory_adjustments_item_id_foreign` (`item_id`),
  KEY `inventory_adjustments_adjustment_type_id_foreign` (`adjustment_type_id`),
  KEY `inventory_adjustments_reason_id_foreign` (`reason_id`),
  KEY `inventory_adjustments_branch_id_foreign` (`branch_id`),
  KEY `inventory_adjustments_warehouse_id_foreign` (`warehouse_id`),
  KEY `inventory_adjustments_item_category_id_foreign` (`item_category_id`),
  CONSTRAINT `inventory_adjustments_adjustment_type_id_foreign` FOREIGN KEY (`adjustment_type_id`) REFERENCES `adjustment_types` (`id`),
  CONSTRAINT `inventory_adjustments_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`),
  CONSTRAINT `inventory_adjustments_item_category_id_foreign` FOREIGN KEY (`item_category_id`) REFERENCES `item_categories` (`id`),
  CONSTRAINT `inventory_adjustments_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `create_items` (`id`),
  CONSTRAINT `inventory_adjustments_reason_id_foreign` FOREIGN KEY (`reason_id`) REFERENCES `reasons` (`id`),
  CONSTRAINT `inventory_adjustments_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `inventory_adjustments` */

/*Table structure for table `invoices` */

DROP TABLE IF EXISTS `invoices`;

CREATE TABLE `invoices` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` bigint(20) unsigned NOT NULL,
  `warehouse_id` bigint(20) unsigned NOT NULL,
  `customer_id` bigint(20) unsigned NOT NULL,
  `invoice_number` varchar(255) NOT NULL,
  `order_number` varchar(255) NOT NULL,
  `invoice_date` timestamp NOT NULL,
  `item_id` bigint(20) unsigned NOT NULL,
  `rate` varchar(255) NOT NULL,
  `quantity` varchar(255) NOT NULL,
  `discount_id` bigint(20) unsigned NOT NULL,
  `tax_id` bigint(20) unsigned NOT NULL,
  `amount` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `invoices_warehouse_id_foreign` (`warehouse_id`),
  KEY `invoices_customer_id_foreign` (`customer_id`),
  KEY `invoices_item_id_foreign` (`item_id`),
  KEY `invoices_discount_id_foreign` (`discount_id`),
  KEY `invoices_tax_id_foreign` (`tax_id`),
  CONSTRAINT `invoices_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customersses` (`id`),
  CONSTRAINT `invoices_discount_id_foreign` FOREIGN KEY (`discount_id`) REFERENCES `discounts` (`id`),
  CONSTRAINT `invoices_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `create_items` (`id`),
  CONSTRAINT `invoices_tax_id_foreign` FOREIGN KEY (`tax_id`) REFERENCES `taxes` (`id`),
  CONSTRAINT `invoices_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `invoices` */

/*Table structure for table `item_categories` */

DROP TABLE IF EXISTS `item_categories`;

CREATE TABLE `item_categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `item_categories` */

insert  into `item_categories`(`id`,`name`,`created_at`,`updated_at`) values 
(1,Dairy Product,NULL,NULL),
(2,Drink,NULL,NULL),
(3,Condiment,NULL,NULL),
(4,Personal care,NULL,NULL),
(5,Beverages snacks,NULL,NULL),
(6,Pasta,NULL,NULL),
(7,new-category,2024-08-03 16:32:01,2024-08-03 16:32:01);

/*Table structure for table `item_types` */

DROP TABLE IF EXISTS `item_types`;

CREATE TABLE `item_types` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `item_types` */

insert  into `item_types`(`id`,`name`,`created_at`,`updated_at`) values 
(1,Goods,NULL,NULL),
(2,Service,NULL,NULL);

/*Table structure for table `journal_entries` */

DROP TABLE IF EXISTS `journal_entries`;

CREATE TABLE `journal_entries` (
  `id` char(36) NOT NULL,
  `description` varchar(255) NOT NULL,
  `payment_date` timestamp NOT NULL,
  `warehouse_id` bigint(20) unsigned NOT NULL,
  `vendor_id` varchar(255) NOT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `modified_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `journal_entries_warehouse_id_foreign` (`warehouse_id`),
  KEY `journal_entries_created_by_foreign` (`created_by`),
  KEY `journal_entries_modified_by_foreign` (`modified_by`),
  KEY `journal_entries_deleted_by_foreign` (`deleted_by`),
  CONSTRAINT `journal_entries_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  CONSTRAINT `journal_entries_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`),
  CONSTRAINT `journal_entries_modified_by_foreign` FOREIGN KEY (`modified_by`) REFERENCES `users` (`id`),
  CONSTRAINT `journal_entries_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `journal_entries` */

/*Table structure for table `journal_entry_details` */

DROP TABLE IF EXISTS `journal_entry_details`;

CREATE TABLE `journal_entry_details` (
  `id` char(36) NOT NULL,
  `journal_entry_id` char(36) NOT NULL,
  `journal_type_id` char(36) NOT NULL,
  `amount` double NOT NULL,
  `description` text NOT NULL,
  `account_id` char(36) NOT NULL,
  `account_no` varchar(255) NOT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `modified_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `journal_entry_details_journal_entry_id_foreign` (`journal_entry_id`),
  KEY `journal_entry_details_journal_type_id_foreign` (`journal_type_id`),
  KEY `journal_entry_details_created_by_foreign` (`created_by`),
  KEY `journal_entry_details_modified_by_foreign` (`modified_by`),
  KEY `journal_entry_details_deleted_by_foreign` (`deleted_by`),
  CONSTRAINT `journal_entry_details_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  CONSTRAINT `journal_entry_details_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`),
  CONSTRAINT `journal_entry_details_journal_entry_id_foreign` FOREIGN KEY (`journal_entry_id`) REFERENCES `journal_entries` (`id`),
  CONSTRAINT `journal_entry_details_journal_type_id_foreign` FOREIGN KEY (`journal_type_id`) REFERENCES `journal_types` (`id`),
  CONSTRAINT `journal_entry_details_modified_by_foreign` FOREIGN KEY (`modified_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `journal_entry_details` */

/*Table structure for table `journal_types` */

DROP TABLE IF EXISTS `journal_types`;

CREATE TABLE `journal_types` (
  `id` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `sign` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `journal_types` */

/*Table structure for table `manufacturers` */

DROP TABLE IF EXISTS `manufacturers`;

CREATE TABLE `manufacturers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `manufacturers` */

/*Table structure for table `migrations` */

DROP TABLE IF EXISTS `migrations`;

CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=85 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `migrations` */

insert  into `migrations`(`id`,`migration`,`batch`) values 
(1,2014_10_12_100000_create_password_resets_table,1),
(2,2016_06_01_000001_create_oauth_auth_codes_table,1),
(3,2016_06_01_000002_create_oauth_access_tokens_table,1),
(4,2016_06_01_000003_create_oauth_refresh_tokens_table,1),
(5,2016_06_01_000004_create_oauth_clients_table,1),
(6,2016_06_01_000005_create_oauth_personal_access_clients_table,1),
(7,2019_08_19_000000_create_failed_jobs_table,1),
(8,2019_12_14_000001_create_personal_access_tokens_table,1),
(9,2024_06_21_161610_create_item_categories_table,1),
(10,2024_06_21_161611_create_payment_terms_table,1),
(11,2024_06_21_161612_create_item_types_table,1),
(12,2024_06_21_161613_create_units_table,1),
(13,2024_06_21_161614_create_dimensions_table,1),
(14,2024_06_21_161615_create_weights_table,1),
(15,2024_06_21_161616_create_expense_accounts_table,1),
(16,2024_06_21_161617_create_adjustment_types_table,1),
(17,2024_06_21_161618_create_statuses_table,1),
(18,2024_06_21_161619_create_customer_types_table,1),
(19,2024_06_21_161620_create_branches_table,1),
(20,2024_06_21_161621_create_titles_table,1),
(21,2024_06_21_161622_create_warehouses_table,1),
(22,2024_06_21_161623_create_taxes_table,1),
(23,2024_06_21_161624_create_discounts_table,1),
(24,2024_06_21_161625_create_payment_modes_table,1),
(25,2024_06_21_161626_create_carriers_table,1),
(26,2024_06_21_161627_create_payment_types_table,1),
(27,2024_06_21_161628_create_designations_table,1),
(28,2024_06_21_161629_create_banks_table,1),
(29,2024_06_21_161630_create_manufacturers_table,1),
(30,2024_06_21_161631_create_vendor_types_table,1),
(31,2024_06_21_161632_create_reasons_table,1),
(32,2024_06_21_161633_create_brands_table,1),
(33,2024_06_21_161634_create_attributes_table,1),
(34,2024_06_21_161635_create_countries_table,1),
(35,2024_06_21_161636_create_states_table,1),
(36,2024_06_21_161637_create_create_items_table,1),
(37,2024_06_21_161638_create_vendors_table,1),
(38,2024_06_21_161639_create_transfer_orders_table,1),
(39,2024_06_21_161640_create_deliveries_table,1),
(40,2024_06_21_161641_create_inventory_adjustments_table,1),
(41,2024_06_21_161642_create_invoices_table,1),
(42,2024_06_21_161643_create_sales_table,1),
(43,2024_06_21_161644_create_credit_limits_table,1),
(44,2024_06_21_161645_create_credit_sales_table,1),
(45,2024_06_21_161646_create_customers_table,1),
(46,2024_06_21_161647_create_payment_receiveds_table,1),
(47,2024_06_21_161648_create_sales_receipts_table,1),
(48,2024_06_21_161649_create_payment_vouchers_table,1),
(49,2024_06_21_161650_create_vendor_credits_table,1),
(50,2024_06_21_161651_create_purchase_received_details_table,1),
(51,2024_06_21_161653_create_users_table,1),
(52,2024_06_21_161654_create_new_purchase_orders_table,1),
(53,2024_06_21_161655_create_new_purchase_receiveds_table,1),
(54,2024_06_21_161656_create_new_payments_table,1),
(55,2024_06_21_161657_create_payment_voucher_details_table,1),
(56,2024_06_21_161658_create_purchase_order_details_table,1),
(57,2024_07_02_170456_create_permissions_table,1),
(58,2024_07_04_024659_create_account_groups_table,1),
(59,2024_07_04_024700_create_account_types_table,1),
(60,2024_07_04_024701_create_account_subtypes_table,1),
(61,2024_07_04_024702_create_accounts_table,1),
(62,2024_07_04_024703_create_charts_table,1),
(63,2024_07_04_024704_create_chart_cards_table,1),
(64,2024_07_04_024705_create_chart_categories_table,1),
(65,2024_07_04_024706_create_chart_providers_table,1),
(66,2024_07_04_024707_create_chart_types_table,1),
(67,2024_07_04_024708_create_dashboard_settings_table,1),
(68,2024_07_04_024709_create_financial_years_table,1),
(69,2024_07_04_024710_create_financial_quarters_table,1),
(70,2024_07_04_024711_create_financial_periods_table,1),
(71,2024_07_04_024712_create_journal_types_table,1),
(72,2024_07_04_024713_create_journal_entries_table,1),
(73,2024_07_04_024714_create_journal_entry_details_table,1),
(74,2024_07_04_024715_create_transactions_table,1),
(75,2024_07_04_024716_create_account_opening_balances_table,1),
(76,2024_07_04_024717_create_period_accounts_table,1),
(77,2024_07_04_024718_create_period_account_years_table,1),
(78,2024_07_04_024719_create_period_account_dailies_table,1),
(79,2024_07_11_145635_create_sales_type_table,1),
(80,2024_07_11_145713_create_store_types_table,1),
(81,2024_07_11_145731_create_stores_table,1),
(82,2024_07_11_145756_create_refund_types_table,1),
(83,2024_08_15_152833_create_role_permission_table,2),
(84,2024_08_16_120543_create_role_user_table,3);

/*Table structure for table `new_payments` */

DROP TABLE IF EXISTS `new_payments`;

CREATE TABLE `new_payments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `vendor_id` bigint(20) unsigned NOT NULL,
  `branch_id` bigint(20) unsigned NOT NULL,
  `warehouse_id` bigint(20) unsigned NOT NULL,
  `payment_amount` varchar(255) NOT NULL,
  `payment_mode_id` bigint(20) unsigned NOT NULL,
  `description` varchar(255) NOT NULL,
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
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `item_category_id` bigint(20) unsigned NOT NULL,
  `item_id` bigint(20) unsigned NOT NULL,
  `vendor_id` bigint(20) unsigned NOT NULL,
  `branch_id` bigint(20) unsigned NOT NULL,
  `payment_mode_id` bigint(20) unsigned NOT NULL,
  `purchase_order_number` varchar(255) NOT NULL,
  `purchase_amount` varchar(255) NOT NULL,
  `purchase_date` timestamp NOT NULL,
  `expected_delivery_date` date NOT NULL,
  `payment_type_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `new_purchase_orders_item_category_id_foreign` (`item_category_id`),
  KEY `new_purchase_orders_item_id_foreign` (`item_id`),
  KEY `new_purchase_orders_vendor_id_foreign` (`vendor_id`),
  KEY `new_purchase_orders_branch_id_foreign` (`branch_id`),
  KEY `new_purchase_orders_payment_mode_id_foreign` (`payment_mode_id`),
  KEY `new_purchase_orders_payment_type_id_foreign` (`payment_type_id`),
  CONSTRAINT `new_purchase_orders_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`),
  CONSTRAINT `new_purchase_orders_item_category_id_foreign` FOREIGN KEY (`item_category_id`) REFERENCES `item_categories` (`id`),
  CONSTRAINT `new_purchase_orders_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `create_items` (`id`),
  CONSTRAINT `new_purchase_orders_payment_mode_id_foreign` FOREIGN KEY (`payment_mode_id`) REFERENCES `payment_modes` (`id`),
  CONSTRAINT `new_purchase_orders_payment_type_id_foreign` FOREIGN KEY (`payment_type_id`) REFERENCES `payment_types` (`id`),
  CONSTRAINT `new_purchase_orders_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `new_purchase_orders` */

/*Table structure for table `new_purchase_receiveds` */

DROP TABLE IF EXISTS `new_purchase_receiveds`;

CREATE TABLE `new_purchase_receiveds` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `vendor_id` bigint(20) unsigned NOT NULL,
  `purchase_order_number` varchar(255) NOT NULL,
  `purchase_received_number` varchar(255) NOT NULL,
  `received_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `new_purchase_receiveds_vendor_id_foreign` (`vendor_id`),
  CONSTRAINT `new_purchase_receiveds_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `new_purchase_receiveds` */

/*Table structure for table `oauth_access_tokens` */

DROP TABLE IF EXISTS `oauth_access_tokens`;

CREATE TABLE `oauth_access_tokens` (
  `id` varchar(100) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `client_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `scopes` text DEFAULT NULL,
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
  `id` varchar(100) NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `client_id` bigint(20) unsigned NOT NULL,
  `scopes` text DEFAULT NULL,
  `revoked` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `oauth_auth_codes_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `oauth_auth_codes` */

/*Table structure for table `oauth_clients` */

DROP TABLE IF EXISTS `oauth_clients`;

CREATE TABLE `oauth_clients` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `secret` varchar(100) DEFAULT NULL,
  `provider` varchar(255) DEFAULT NULL,
  `redirect` text NOT NULL,
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
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `oauth_personal_access_clients` */

/*Table structure for table `oauth_refresh_tokens` */

DROP TABLE IF EXISTS `oauth_refresh_tokens`;

CREATE TABLE `oauth_refresh_tokens` (
  `id` varchar(100) NOT NULL,
  `access_token_id` varchar(100) NOT NULL,
  `revoked` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `oauth_refresh_tokens_access_token_id_index` (`access_token_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `oauth_refresh_tokens` */

/*Table structure for table `password_resets` */

DROP TABLE IF EXISTS `password_resets`;

CREATE TABLE `password_resets` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `password_resets` */

/*Table structure for table `payment_modes` */

DROP TABLE IF EXISTS `payment_modes`;

CREATE TABLE `payment_modes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `payment_modes` */

/*Table structure for table `payment_receiveds` */

DROP TABLE IF EXISTS `payment_receiveds`;

CREATE TABLE `payment_receiveds` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` bigint(20) unsigned NOT NULL,
  `amount_received` varchar(255) NOT NULL,
  `bank_charges` double(8,2) NOT NULL,
  `payment_number` varchar(255) NOT NULL,
  `deposit_bank_id` bigint(20) unsigned NOT NULL,
  `payment_mode_id` bigint(20) unsigned NOT NULL,
  `invoice_number` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payment_receiveds_customer_id_foreign` (`customer_id`),
  KEY `payment_receiveds_deposit_bank_id_foreign` (`deposit_bank_id`),
  KEY `payment_receiveds_payment_mode_id_foreign` (`payment_mode_id`),
  CONSTRAINT `payment_receiveds_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  CONSTRAINT `payment_receiveds_deposit_bank_id_foreign` FOREIGN KEY (`deposit_bank_id`) REFERENCES `banks` (`id`),
  CONSTRAINT `payment_receiveds_payment_mode_id_foreign` FOREIGN KEY (`payment_mode_id`) REFERENCES `payment_modes` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `payment_receiveds` */

/*Table structure for table `payment_terms` */

DROP TABLE IF EXISTS `payment_terms`;

CREATE TABLE `payment_terms` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `payment_terms` */

/*Table structure for table `payment_types` */

DROP TABLE IF EXISTS `payment_types`;

CREATE TABLE `payment_types` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `payment_types` */

/*Table structure for table `payment_voucher_details` */

DROP TABLE IF EXISTS `payment_voucher_details`;

CREATE TABLE `payment_voucher_details` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `Expense_account_id` varchar(255) NOT NULL,
  `amount` varchar(255) NOT NULL,
  `quantity` varchar(255) NOT NULL,
  `item_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payment_voucher_details_item_id_foreign` (`item_id`),
  CONSTRAINT `payment_voucher_details_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `create_items` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `payment_voucher_details` */

/*Table structure for table `payment_vouchers` */

DROP TABLE IF EXISTS `payment_vouchers`;

CREATE TABLE `payment_vouchers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint(20) unsigned NOT NULL,
  `expense_date` timestamp NOT NULL,
  `amount` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `branch_id` bigint(20) unsigned NOT NULL,
  `warehouse_id` bigint(20) unsigned NOT NULL,
  `tax_id` bigint(20) unsigned NOT NULL,
  `vendor_id` bigint(20) unsigned NOT NULL,
  `payment_mode_id` bigint(20) unsigned NOT NULL,
  `expense_account_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payment_vouchers_product_id_foreign` (`product_id`),
  KEY `payment_vouchers_branch_id_foreign` (`branch_id`),
  KEY `payment_vouchers_warehouse_id_foreign` (`warehouse_id`),
  KEY `payment_vouchers_tax_id_foreign` (`tax_id`),
  KEY `payment_vouchers_vendor_id_foreign` (`vendor_id`),
  KEY `payment_vouchers_payment_mode_id_foreign` (`payment_mode_id`),
  KEY `payment_vouchers_expense_account_id_foreign` (`expense_account_id`),
  CONSTRAINT `payment_vouchers_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`),
  CONSTRAINT `payment_vouchers_expense_account_id_foreign` FOREIGN KEY (`expense_account_id`) REFERENCES `expense_account_ids` (`id`),
  CONSTRAINT `payment_vouchers_payment_mode_id_foreign` FOREIGN KEY (`payment_mode_id`) REFERENCES `payment_modes` (`id`),
  CONSTRAINT `payment_vouchers_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `create_items` (`id`),
  CONSTRAINT `payment_vouchers_tax_id_foreign` FOREIGN KEY (`tax_id`) REFERENCES `taxes` (`id`),
  CONSTRAINT `payment_vouchers_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`),
  CONSTRAINT `payment_vouchers_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `payment_vouchers` */

/*Table structure for table `period_account_dailies` */

DROP TABLE IF EXISTS `period_account_dailies`;

CREATE TABLE `period_account_dailies` (
  `id` char(36) NOT NULL,
  `period_date` date NOT NULL,
  `debit` double NOT NULL,
  `credit` double NOT NULL,
  `amount` double NOT NULL,
  `warehouse_id` bigint(20) unsigned NOT NULL,
  `account_no` varchar(255) NOT NULL,
  `account_id` char(36) NOT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `modified_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `period_account_dailies_warehouse_id_foreign` (`warehouse_id`),
  KEY `period_account_dailies_account_id_foreign` (`account_id`),
  KEY `period_account_dailies_created_by_foreign` (`created_by`),
  KEY `period_account_dailies_modified_by_foreign` (`modified_by`),
  KEY `period_account_dailies_deleted_by_foreign` (`deleted_by`),
  CONSTRAINT `period_account_dailies_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`),
  CONSTRAINT `period_account_dailies_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  CONSTRAINT `period_account_dailies_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`),
  CONSTRAINT `period_account_dailies_modified_by_foreign` FOREIGN KEY (`modified_by`) REFERENCES `users` (`id`),
  CONSTRAINT `period_account_dailies_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `period_account_dailies` */

/*Table structure for table `period_account_years` */

DROP TABLE IF EXISTS `period_account_years`;

CREATE TABLE `period_account_years` (
  `id` char(36) NOT NULL,
  `financial_year_id` char(36) NOT NULL,
  `debit` double NOT NULL,
  `credit` double NOT NULL,
  `amount` double NOT NULL,
  `warehouse_id` bigint(20) unsigned NOT NULL,
  `account_no` varchar(255) DEFAULT NULL,
  `account_id` char(36) NOT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `modified_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `period_account_years_financial_year_id_foreign` (`financial_year_id`),
  KEY `period_account_years_warehouse_id_foreign` (`warehouse_id`),
  KEY `period_account_years_account_id_foreign` (`account_id`),
  KEY `period_account_years_created_by_foreign` (`created_by`),
  KEY `period_account_years_modified_by_foreign` (`modified_by`),
  KEY `period_account_years_deleted_by_foreign` (`deleted_by`),
  CONSTRAINT `period_account_years_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`),
  CONSTRAINT `period_account_years_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  CONSTRAINT `period_account_years_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`),
  CONSTRAINT `period_account_years_financial_year_id_foreign` FOREIGN KEY (`financial_year_id`) REFERENCES `financial_years` (`id`),
  CONSTRAINT `period_account_years_modified_by_foreign` FOREIGN KEY (`modified_by`) REFERENCES `users` (`id`),
  CONSTRAINT `period_account_years_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `period_account_years` */

/*Table structure for table `period_accounts` */

DROP TABLE IF EXISTS `period_accounts`;

CREATE TABLE `period_accounts` (
  `id` char(36) NOT NULL,
  `financial_period_id` char(36) NOT NULL,
  `debit` double NOT NULL,
  `credit` double NOT NULL,
  `amount` double NOT NULL,
  `warehouse_id` bigint(20) unsigned NOT NULL,
  `account_no` varchar(255) DEFAULT NULL,
  `account_id` char(36) NOT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `modified_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `period_accounts_financial_period_id_foreign` (`financial_period_id`),
  KEY `period_accounts_warehouse_id_foreign` (`warehouse_id`),
  KEY `period_accounts_account_id_foreign` (`account_id`),
  KEY `period_accounts_created_by_foreign` (`created_by`),
  KEY `period_accounts_modified_by_foreign` (`modified_by`),
  KEY `period_accounts_deleted_by_foreign` (`deleted_by`),
  CONSTRAINT `period_accounts_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`),
  CONSTRAINT `period_accounts_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  CONSTRAINT `period_accounts_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`),
  CONSTRAINT `period_accounts_financial_period_id_foreign` FOREIGN KEY (`financial_period_id`) REFERENCES `financial_periods` (`id`),
  CONSTRAINT `period_accounts_modified_by_foreign` FOREIGN KEY (`modified_by`) REFERENCES `users` (`id`),
  CONSTRAINT `period_accounts_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `period_accounts` */

/*Table structure for table `permissions` */

DROP TABLE IF EXISTS `permissions`;

CREATE TABLE `permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `permissions` */

insert  into `permissions`(`id`,`name`,`created_at`,`updated_at`) values 
(1,create item,2024-08-15 17:13:22,2024-08-15 17:13:22);

/*Table structure for table `personal_access_tokens` */

DROP TABLE IF EXISTS `personal_access_tokens`;

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `personal_access_tokens` */

/*Table structure for table `purchase_order_details` */

DROP TABLE IF EXISTS `purchase_order_details`;

CREATE TABLE `purchase_order_details` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `item_category_id` bigint(20) unsigned NOT NULL,
  `purchase_order_id` varchar(255) NOT NULL,
  `item_id` bigint(20) unsigned NOT NULL,
  `unit_price` varchar(255) NOT NULL,
  `quantity` varchar(255) NOT NULL,
  `unit_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `purchase_order_details_item_category_id_foreign` (`item_category_id`),
  KEY `purchase_order_details_item_id_foreign` (`item_id`),
  KEY `purchase_order_details_unit_id_foreign` (`unit_id`),
  CONSTRAINT `purchase_order_details_item_category_id_foreign` FOREIGN KEY (`item_category_id`) REFERENCES `item_categories` (`id`),
  CONSTRAINT `purchase_order_details_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `create_items` (`id`),
  CONSTRAINT `purchase_order_details_unit_id_foreign` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `purchase_order_details` */

/*Table structure for table `purchase_received_details` */

DROP TABLE IF EXISTS `purchase_received_details`;

CREATE TABLE `purchase_received_details` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `new_purchased_received_id` bigint(20) unsigned NOT NULL,
  `item_category_id` bigint(20) unsigned NOT NULL,
  `item_id` bigint(20) unsigned NOT NULL,
  `unit_price` varchar(255) NOT NULL,
  `quantity` varchar(255) NOT NULL,
  `unit_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `purchase_received_details_new_purchased_received_id_foreign` (`new_purchased_received_id`),
  KEY `purchase_received_details_item_category_id_foreign` (`item_category_id`),
  KEY `purchase_received_details_item_id_foreign` (`item_id`),
  KEY `purchase_received_details_unit_id_foreign` (`unit_id`),
  CONSTRAINT `purchase_received_details_item_category_id_foreign` FOREIGN KEY (`item_category_id`) REFERENCES `item_categories` (`id`),
  CONSTRAINT `purchase_received_details_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `create_items` (`id`),
  CONSTRAINT `purchase_received_details_new_purchased_received_id_foreign` FOREIGN KEY (`new_purchased_received_id`) REFERENCES `new_purchase_receiveds` (`id`),
  CONSTRAINT `purchase_received_details_unit_id_foreign` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `purchase_received_details` */

/*Table structure for table `reasons` */

DROP TABLE IF EXISTS `reasons`;

CREATE TABLE `reasons` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `reasons` */

insert  into `reasons`(`id`,`name`,`created_at`,`updated_at`) values 
(1,Stolen goods,NULL,NULL),
(2,Damaged goods,NULL,NULL),
(3,Stock Written off,NULL,NULL),
(4,Stocktaking results,NULL,NULL),
(5,Inventory Revaluation,NULL,NULL);

/*Table structure for table `refund_types` */

DROP TABLE IF EXISTS `refund_types`;

CREATE TABLE `refund_types` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `refund_types` */

insert  into `refund_types`(`id`,`name`,`created_at`,`updated_at`) values 
(1,Cash Refund,NULL,NULL),
(2,Bank Transfer,NULL,NULL);

/*Table structure for table `role_permission` */

DROP TABLE IF EXISTS `role_permission`;

CREATE TABLE `role_permission` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` bigint(20) unsigned NOT NULL,
  `permission_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `role_permission_role_id_foreign` (`role_id`),
  KEY `role_permission_permission_id_foreign` (`permission_id`),
  CONSTRAINT `role_permission_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_permission_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `role_permission` */

insert  into `role_permission`(`id`,`role_id`,`permission_id`,`created_at`,`updated_at`) values 
(1,5,1,2024-08-16 11:32:25,2024-08-16 11:32:25),
(2,1,1,2024-08-16 11:52:23,2024-08-16 11:52:23);

/*Table structure for table `role_user` */

DROP TABLE IF EXISTS `role_user`;

CREATE TABLE `role_user` (
  `id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `role_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `update_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_fk_id` (`user_id`),
  KEY `role_fk_id` (`role_id`),
  CONSTRAINT `role_fk_id` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_fk_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

/*Data for the table `role_user` */

/*Table structure for table `roles` */

DROP TABLE IF EXISTS `roles`;

CREATE TABLE `roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `guard_name` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

/*Data for the table `roles` */

insert  into `roles`(`id`,`name`,`guard_name`,`created_at`,`updated_at`) values 
(1,Admin,,2024-07-26 14:23:33,2024-07-26 14:23:33),
(2,Store Manager,web,2024-07-26 14:25:20,2024-07-26 14:25:20),
(3,Iventory-Manager,web,2024-08-16 09:39:12,2024-08-16 09:39:12),
(4,Sales Manager,web,2024-08-16 10:18:48,2024-08-16 10:18:48),
(5,Test Manager,web,2024-08-16 11:32:24,2024-08-16 11:32:24);

/*Table structure for table `sales` */

DROP TABLE IF EXISTS `sales`;

CREATE TABLE `sales` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned NOT NULL,
  `branch_id` bigint(20) unsigned NOT NULL,
  `warehouse_id` bigint(20) unsigned NOT NULL,
  `quantity` double(8,2) NOT NULL,
  `price` double(8,2) NOT NULL,
  `discount_id` bigint(20) unsigned NOT NULL,
  `discount` double(8,2) NOT NULL,
  `sales_order_number` varchar(255) NOT NULL,
  `total_amount` varchar(255) NOT NULL,
  `amount_paid` double(8,2) NOT NULL,
  `balance_amount` double(8,2) NOT NULL,
  `payment_mode` bigint(20) unsigned NOT NULL,
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
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` bigint(20) unsigned NOT NULL,
  `branch_id` bigint(20) unsigned NOT NULL,
  `warehouse_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned NOT NULL,
  `tax_id` bigint(20) unsigned NOT NULL,
  `payment_mode_id` bigint(20) unsigned NOT NULL,
  `discount_id` bigint(20) unsigned NOT NULL,
  `quantity` varchar(255) NOT NULL,
  `rate` varchar(255) NOT NULL,
  `amount` varchar(255) NOT NULL,
  `receipt_date` timestamp NOT NULL,
  `customer_note` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sales_receipts_customer_id_foreign` (`customer_id`),
  KEY `sales_receipts_branch_id_foreign` (`branch_id`),
  KEY `sales_receipts_warehouse_id_foreign` (`warehouse_id`),
  KEY `sales_receipts_product_id_foreign` (`product_id`),
  KEY `sales_receipts_tax_id_foreign` (`tax_id`),
  KEY `sales_receipts_payment_mode_id_foreign` (`payment_mode_id`),
  KEY `sales_receipts_discount_id_foreign` (`discount_id`),
  CONSTRAINT `sales_receipts_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`),
  CONSTRAINT `sales_receipts_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  CONSTRAINT `sales_receipts_discount_id_foreign` FOREIGN KEY (`discount_id`) REFERENCES `discounts` (`id`),
  CONSTRAINT `sales_receipts_payment_mode_id_foreign` FOREIGN KEY (`payment_mode_id`) REFERENCES `payment_modes` (`id`),
  CONSTRAINT `sales_receipts_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `create_items` (`id`),
  CONSTRAINT `sales_receipts_tax_id_foreign` FOREIGN KEY (`tax_id`) REFERENCES `taxes` (`id`),
  CONSTRAINT `sales_receipts_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `sales_receipts` */

/*Table structure for table `sales_type` */

DROP TABLE IF EXISTS `sales_type`;

CREATE TABLE `sales_type` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `sales_type` */

/*Table structure for table `states` */

DROP TABLE IF EXISTS `states`;

CREATE TABLE `states` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `states` */

insert  into `states`(`id`,`name`,`created_at`,`updated_at`) values 
(1,FCT,NULL,NULL),
(2,Abia,NULL,NULL),
(3,Adamawa,NULL,NULL),
(4,Akwa Ibom,NULL,NULL),
(5,Anambra,NULL,NULL),
(6,Bauchi,NULL,NULL),
(7,Bayelsa,NULL,NULL),
(8,Benue,NULL,NULL),
(9,Borno,NULL,NULL),
(10,Cross River,NULL,NULL),
(11,Delta,NULL,NULL),
(12,Ebonyi,NULL,NULL),
(13,Edo,NULL,NULL),
(14,Ekiti,NULL,NULL),
(15,Enugu,NULL,NULL),
(16,Gombe,NULL,NULL),
(17,Imo,NULL,NULL),
(18,Jigawa,NULL,NULL),
(19,Kaduna,NULL,NULL),
(20,Kano,NULL,NULL),
(21,Katsina,NULL,NULL),
(22,Kebbi,NULL,NULL),
(23,Kogi,NULL,NULL),
(24,Kwara,NULL,NULL),
(25,Lagos,NULL,NULL),
(26,Nasarawa,NULL,NULL),
(27,Niger,NULL,NULL),
(28,Ogun,NULL,NULL),
(29,Ondo,NULL,NULL),
(30,Osun,NULL,NULL),
(31,Oyo,NULL,NULL),
(32,Plateau,NULL,NULL),
(33,Rivers,NULL,NULL),
(34,Sokoto,NULL,NULL),
(35,Taraba,NULL,NULL),
(36,Yobe,NULL,NULL),
(37,Zamfara,NULL,NULL);

/*Table structure for table `statuses` */

DROP TABLE IF EXISTS `statuses`;

CREATE TABLE `statuses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `statuses` */

insert  into `statuses`(`id`,`name`,`created_at`,`updated_at`) values 
(1,Active,2024-06-27 09:41:23,2024-06-27 09:41:23),
(2,test,2024-08-05 13:06:52,2024-08-05 13:06:52);

/*Table structure for table `store_types` */

DROP TABLE IF EXISTS `store_types`;

CREATE TABLE `store_types` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `store_types` */

insert  into `store_types`(`id`,`name`,`created_at`,`updated_at`) values 
(1,Mega Store,NULL,NULL),
(2,Mini Store,NULL,NULL);

/*Table structure for table `stores` */

DROP TABLE IF EXISTS `stores`;

CREATE TABLE `stores` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `branch_id` bigint(20) unsigned NOT NULL,
  `store_type_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `stores_branch_id_foreign` (`branch_id`),
  KEY `stores_store_type_id_foreign` (`store_type_id`),
  CONSTRAINT `stores_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`),
  CONSTRAINT `stores_store_type_id_foreign` FOREIGN KEY (`store_type_id`) REFERENCES `store_types` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `stores` */

insert  into `stores`(`id`,`name`,`branch_id`,`store_type_id`,`created_at`,`updated_at`) values 
(1,Maraba Store,2,1,NULL,NULL),
(2,Mabushi Store,3,2,NULL,NULL);

/*Table structure for table `taxes` */

DROP TABLE IF EXISTS `taxes`;

CREATE TABLE `taxes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `taxes` */

insert  into `taxes`(`id`,`name`,`created_at`,`updated_at`) values 
(1,VAT,NULL,NULL),
(2,No Tax,NULL,NULL);

/*Table structure for table `titles` */

DROP TABLE IF EXISTS `titles`;

CREATE TABLE `titles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `titles` */

insert  into `titles`(`id`,`name`,`created_at`,`updated_at`) values 
(1,Mr.,NULL,NULL),
(2,Mrs.,NULL,NULL),
(3,Alhaji,NULL,NULL),
(4,Hajiya,NULL,NULL);

/*Table structure for table `transactions` */

DROP TABLE IF EXISTS `transactions`;

CREATE TABLE `transactions` (
  `id` char(36) NOT NULL,
  `financial_period_id` char(36) NOT NULL,
  `transaction_date` date NOT NULL,
  `transcode` varchar(255) NOT NULL,
  `transtype` varchar(255) NOT NULL,
  `naration` varchar(255) NOT NULL,
  `debit` double NOT NULL,
  `credit` double NOT NULL,
  `amount` double NOT NULL,
  `warehouse_id` bigint(20) unsigned NOT NULL,
  `account_no` varchar(255) NOT NULL,
  `account_id` char(36) NOT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `modified_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `transactions_financial_period_id_foreign` (`financial_period_id`),
  KEY `transactions_warehouse_id_foreign` (`warehouse_id`),
  KEY `transactions_account_id_foreign` (`account_id`),
  KEY `transactions_created_by_foreign` (`created_by`),
  KEY `transactions_modified_by_foreign` (`modified_by`),
  KEY `transactions_deleted_by_foreign` (`deleted_by`),
  CONSTRAINT `transactions_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`),
  CONSTRAINT `transactions_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  CONSTRAINT `transactions_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`),
  CONSTRAINT `transactions_financial_period_id_foreign` FOREIGN KEY (`financial_period_id`) REFERENCES `financial_periods` (`id`),
  CONSTRAINT `transactions_modified_by_foreign` FOREIGN KEY (`modified_by`) REFERENCES `users` (`id`),
  CONSTRAINT `transactions_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `transactions` */

/*Table structure for table `transfer_orders` */

DROP TABLE IF EXISTS `transfer_orders`;

CREATE TABLE `transfer_orders` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `transfer_order_number` varchar(255) NOT NULL,
  `transfer_date` date NOT NULL,
  `transfer_reason` varchar(255) NOT NULL,
  `source_id` bigint(20) unsigned NOT NULL,
  `destination_id` bigint(20) unsigned NOT NULL,
  `image_url` varchar(255) NOT NULL,
  `transfer_quantity` varchar(255) NOT NULL,
  `item_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `transfer_orders_item_id_foreign` (`item_id`),
  KEY `transfer_orders_destination_id_foreign` (`destination_id`),
  KEY `transfer_orders_source_id_foreign` (`source_id`),
  KEY `transfer_order_fk_user_createdby` (`created_by`),
  KEY `transfer_user_updated_fk` (`updated_by`),
  CONSTRAINT `transfer_order_fk_user_createdby` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `transfer_orders_destination_id_foreign` FOREIGN KEY (`destination_id`) REFERENCES `stores` (`id`),
  CONSTRAINT `transfer_orders_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `create_items` (`id`),
  CONSTRAINT `transfer_orders_source_id_foreign` FOREIGN KEY (`source_id`) REFERENCES `stores` (`id`),
  CONSTRAINT `transfer_user_updated_fk` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `transfer_orders` */

insert  into `transfer_orders`(`id`,`transfer_order_number`,`transfer_date`,`transfer_reason`,`source_id`,`destination_id`,`image_url`,`transfer_quantity`,`item_id`,`created_at`,`updated_at`,`created_by`,`updated_by`) values 
(1,HGV-TO-20240816155627-101602090,2024-08-16,1,1,2,http://example.com/image.jpg,100,32,2024-08-16 15:56:27,2024-08-16 16:07:08,NULL,NULL);

/*Table structure for table `units` */

DROP TABLE IF EXISTS `units`;

CREATE TABLE `units` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `units` */

insert  into `units`(`id`,`name`,`created_at`,`updated_at`) values 
(1,cm,NULL,NULL),
(2,ft,NULL,NULL),
(3,g,NULL,NULL),
(4,kg,NULL,NULL),
(5,mg,NULL,NULL),
(6,m,NULL,NULL),
(7,Ib,NULL,NULL),
(8,in,NULL,NULL),
(9,pcs,NULL,NULL);

/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `status_id` bigint(20) unsigned NOT NULL,
  `branch_id` bigint(20) unsigned NOT NULL,
  `warehouse_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `store_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `users_role_id_foreign` (`role_id`),
  KEY `users_status_id_foreign` (`status_id`),
  KEY `users_branch_id_foreign` (`branch_id`),
  KEY `users_warehouse_id_foreign` (`warehouse_id`),
  KEY `users_store_id_fk` (`store_id`),
  CONSTRAINT `users_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`),
  CONSTRAINT `users_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`),
  CONSTRAINT `users_status_id_foreign` FOREIGN KEY (`status_id`) REFERENCES `statuses` (`id`),
  CONSTRAINT `users_store_id_fk` FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`),
  CONSTRAINT `users_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `users` */

insert  into `users`(`id`,`role_id`,`name`,`email`,`password`,`status_id`,`branch_id`,`warehouse_id`,`created_at`,`updated_at`,`store_id`) values 
(3,1,SuperAdmin,admin@app.com,$2y$10$EE8vrboms3tCPM.pehainufxSv0Oc1CZ3GIZ5WtvqmFYT3Z1CGRF.,1,1,3,NULL,NULL,1),
(5,1,Hamiradmin,admin@hamirglobal.com,$2y$10$2YuBJv9pIlaS4ELhDjhelO1SNu7t9jF3t71ospUGkaZj8uuWh62tC,1,2,4,NULL,NULL,2);

/*Table structure for table `vendor_credits` */

DROP TABLE IF EXISTS `vendor_credits`;

CREATE TABLE `vendor_credits` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `vendor_id` bigint(20) unsigned NOT NULL,
  `warehouse_id` bigint(20) unsigned NOT NULL,
  `credit_number` varchar(255) NOT NULL,
  `purchase_order_number` varchar(255) NOT NULL,
  `vendor_credit_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `vendor_credits_vendor_id_foreign` (`vendor_id`),
  KEY `vendor_credits_warehouse_id_foreign` (`warehouse_id`),
  CONSTRAINT `vendor_credits_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`),
  CONSTRAINT `vendor_credits_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `vendor_credits` */

/*Table structure for table `vendor_types` */

DROP TABLE IF EXISTS `vendor_types`;

CREATE TABLE `vendor_types` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `vendor_types` */

insert  into `vendor_types`(`id`,`name`,`created_at`,`updated_at`) values 
(1,Service provider,NULL,NULL),
(2,Manufacturer,NULL,NULL),
(3,Retailer,NULL,NULL),
(4,Distributors,NULL,NULL);

/*Table structure for table `vendors` */

DROP TABLE IF EXISTS `vendors`;

CREATE TABLE `vendors` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `contact_title` varchar(255) NOT NULL,
  `contact_designation` varchar(255) NOT NULL,
  `contact_surname` varchar(255) NOT NULL,
  `contact_firstname` varchar(255) NOT NULL,
  `contact_middlename` varchar(255) NOT NULL,
  `contact_fullname` varchar(255) NOT NULL,
  `vendor_type_id` bigint(20) unsigned NOT NULL,
  `phone_number` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `image_url` varchar(255) NOT NULL,
  `tin` varchar(255) NOT NULL,
  `bank_id` bigint(20) unsigned NOT NULL,
  `account_number` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `vendors_vendor_type_id_foreign` (`vendor_type_id`),
  CONSTRAINT `vendors_vendor_type_id_foreign` FOREIGN KEY (`vendor_type_id`) REFERENCES `vendor_types` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `vendors` */

insert  into `vendors`(`id`,`name`,`address`,`contact_title`,`contact_designation`,`contact_surname`,`contact_firstname`,`contact_middlename`,`contact_fullname`,`vendor_type_id`,`phone_number`,`email`,`image_url`,`tin`,`bank_id`,`account_number`,`created_at`,`updated_at`) values 
(1,Dangote Vendor,Wuye District of Abuja,Chief Salesman,Chief,Kabiru,Mohammed,Adamu,Kabiru Mohammed Adamu,1,08123255452,kar@app.com,hahkensne,XLR-5235452,2,85522654225,2024-06-26 13:33:05,2024-06-26 13:33:05);

/*Table structure for table `warehouses` */

DROP TABLE IF EXISTS `warehouses`;

CREATE TABLE `warehouses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `branch_id` bigint(20) unsigned NOT NULL,
  `warehouse_address` varchar(255) NOT NULL,
  `zipcode` varchar(255) NOT NULL,
  `contact_person` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `warehouses_branch_id_foreign` (`branch_id`),
  CONSTRAINT `warehouses_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `warehouses` */

insert  into `warehouses`(`id`,`name`,`branch_id`,`warehouse_address`,`zipcode`,`contact_person`,`email`,`phone`,`created_at`,`updated_at`) values 
(3,Kano Warehouse,1,Sabon Geri Kano,522554,Hamza Mohammed,ham@app.com,0851115555,NULL,NULL),
(4,Abuja Warehouse,2,Sabon Geri Kano,522554,Hamza Mohammed,ham@app.com,0851115555,NULL,NULL);

/*Table structure for table `weights` */

DROP TABLE IF EXISTS `weights`;

CREATE TABLE `weights` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `weights` */

insert  into `weights`(`id`,`name`,`created_at`,`updated_at`) values 
(1,kg,NULL,NULL),
(2,g,NULL,NULL),
(3,ib,NULL,NULL),
(4,kg,NULL,NULL),
(5,g,NULL,NULL),
(6,ib,NULL,NULL);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
