/*
SQLyog Community v13.1.7 (64 bit)
MySQL - 11.1.2-MariaDB : Database - global-venture
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`global-venture` /*!40100 DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci */;

USE `global-venture`;

/*Table structure for table `adjustment_types` */

DROP TABLE IF EXISTS `adjustment_types`;

CREATE TABLE `adjustment_types` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `adjustment_types` */

insert  into `adjustment_types`(`id`,`name`,`created_at`,`updated_at`) values 
(1,Tested,2024-06-21 16:45:28,2024-06-21 16:45:28);

/*Table structure for table `attributes` */

DROP TABLE IF EXISTS `attributes`;

CREATE TABLE `attributes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `attributes` */

insert  into `attributes`(`id`,`name`,`created_at`,`updated_at`) values 
(1,Tested,2024-06-21 16:46:18,2024-06-21 16:46:18);

/*Table structure for table `banks` */

DROP TABLE IF EXISTS `banks`;

CREATE TABLE `banks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `banks` */

insert  into `banks`(`id`,`name`,`created_at`,`updated_at`) values 
(1,UBA Bank,2024-06-21 16:39:00,2024-06-21 16:39:00),
(2,Access Bank,2024-06-21 16:39:22,2024-06-21 16:39:22);

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `branches` */

/*Table structure for table `brands` */

DROP TABLE IF EXISTS `brands`;

CREATE TABLE `brands` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `brands` */

insert  into `brands`(`id`,`name`,`created_at`,`updated_at`) values 
(1,Dangote Brand,2024-06-21 16:40:09,2024-06-21 16:40:09);

/*Table structure for table `carriers` */

DROP TABLE IF EXISTS `carriers`;

CREATE TABLE `carriers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `carriers` */

insert  into `carriers`(`id`,`name`,`created_at`,`updated_at`) values 
(1,Glovo,2024-06-21 16:57:29,2024-06-21 16:57:29);

/*Table structure for table `countries` */

DROP TABLE IF EXISTS `countries`;

CREATE TABLE `countries` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `countries` */

insert  into `countries`(`id`,`name`,`created_at`,`updated_at`) values 
(1,Nigeria,2024-06-21 16:59:25,2024-06-21 16:59:25);

/*Table structure for table `create_items` */

DROP TABLE IF EXISTS `create_items`;

CREATE TABLE `create_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `item_category_id` bigint(20) unsigned NOT NULL,
  `item_type_id` bigint(20) unsigned NOT NULL,
  `description` varchar(255) NOT NULL,
  `batch_number` varchar(255) NOT NULL,
  `unit_id` bigint(20) unsigned NOT NULL,
  `brand_id` bigint(20) unsigned NOT NULL,
  `cost_price` double(8,2) NOT NULL,
  `selling_price` double(8,2) NOT NULL,
  `reorder_level` varchar(255) NOT NULL,
  `dimension_id` bigint(20) unsigned NOT NULL,
  `weight_id` bigint(20) unsigned NOT NULL,
  `branch_id` bigint(20) unsigned NOT NULL,
  `warehouse` bigint(20) unsigned NOT NULL,
  `vendor_id` bigint(20) unsigned NOT NULL,
  `image_url` varchar(255) NOT NULL,
  `barcode` varchar(255) NOT NULL,
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
  CONSTRAINT `create_items_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`),
  CONSTRAINT `create_items_brand_id_foreign` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`),
  CONSTRAINT `create_items_dimension_id_foreign` FOREIGN KEY (`dimension_id`) REFERENCES `dimensions` (`id`),
  CONSTRAINT `create_items_item_category_id_foreign` FOREIGN KEY (`item_category_id`) REFERENCES `item_categories` (`id`),
  CONSTRAINT `create_items_unit_id_foreign` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`),
  CONSTRAINT `create_items_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`),
  CONSTRAINT `create_items_warehouse_foreign` FOREIGN KEY (`warehouse`) REFERENCES `warehouses` (`id`),
  CONSTRAINT `create_items_weight_id_foreign` FOREIGN KEY (`weight_id`) REFERENCES `weights` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `create_items` */

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
(1,Credit Customer,2024-06-21 16:48:49,2024-06-21 16:48:49);

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `customers` */

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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `designations` */

insert  into `designations`(`id`,`name`,`created_at`,`updated_at`) values 
(1,Chief Cashier,2024-06-21 16:58:31,2024-06-21 16:58:31);

/*Table structure for table `dimensions` */

DROP TABLE IF EXISTS `dimensions`;

CREATE TABLE `dimensions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `dimensions` */

insert  into `dimensions`(`id`,`name`,`created_at`,`updated_at`) values 
(1,Tested,2024-06-21 16:47:08,2024-06-21 16:47:08);

/*Table structure for table `discounts` */

DROP TABLE IF EXISTS `discounts`;

CREATE TABLE `discounts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `discounts` */

insert  into `discounts`(`id`,`name`,`created_at`,`updated_at`) values 
(1,50000,2024-06-21 17:00:47,2024-06-21 17:00:47);

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

/*Table structure for table `inventory_adjustments` */

DROP TABLE IF EXISTS `inventory_adjustments`;

CREATE TABLE `inventory_adjustments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `item_id` bigint(20) unsigned NOT NULL,
  `adjustment_type_id` bigint(20) unsigned NOT NULL,
  `date` timestamp NOT NULL,
  `reason_id` bigint(20) unsigned NOT NULL,
  `branch_id` bigint(20) unsigned NOT NULL,
  `warehouse_id` bigint(20) unsigned NOT NULL,
  `description` varchar(255) NOT NULL,
  `item_category_id` bigint(20) unsigned NOT NULL,
  `cost_price` double(8,2) NOT NULL,
  `selling_price` double(8,2) NOT NULL,
  `quantity` varchar(255) NOT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `item_categories` */

insert  into `item_categories`(`id`,`name`,`created_at`,`updated_at`) values 
(1,Tested,2024-06-21 16:46:51,2024-06-21 16:46:51);

/*Table structure for table `item_types` */

DROP TABLE IF EXISTS `item_types`;

CREATE TABLE `item_types` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `item_types` */

insert  into `item_types`(`id`,`name`,`created_at`,`updated_at`) values 
(1,Large,2024-06-21 16:48:12,2024-06-21 16:48:12);

/*Table structure for table `manufacturers` */

DROP TABLE IF EXISTS `manufacturers`;

CREATE TABLE `manufacturers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `manufacturers` */

insert  into `manufacturers`(`id`,`name`,`created_at`,`updated_at`) values 
(1,Golden Penny,2024-06-21 17:02:09,2024-06-21 17:02:09);

/*Table structure for table `migrations` */

DROP TABLE IF EXISTS `migrations`;

CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=58 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(51,2024_06_21_161652_create_roles_table,1),
(52,2024_06_21_161653_create_users_table,1),
(53,2024_06_21_161654_create_new_purchase_orders_table,1),
(54,2024_06_21_161655_create_new_purchase_receiveds_table,1),
(55,2024_06_21_161656_create_new_payments_table,1),
(56,2024_06_21_161657_create_payment_voucher_details_table,1),
(57,2024_06_21_161658_create_purchase_order_details_table,1);

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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `payment_modes` */

insert  into `payment_modes`(`id`,`name`,`created_at`,`updated_at`) values 
(1,Transfer,2024-06-21 16:57:57,2024-06-21 16:57:57);

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `reasons` */

/*Table structure for table `roles` */

DROP TABLE IF EXISTS `roles`;

CREATE TABLE `roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `roles` */

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

/*Table structure for table `states` */

DROP TABLE IF EXISTS `states`;

CREATE TABLE `states` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `states` */

/*Table structure for table `statuses` */

DROP TABLE IF EXISTS `statuses`;

CREATE TABLE `statuses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `statuses` */

/*Table structure for table `taxes` */

DROP TABLE IF EXISTS `taxes`;

CREATE TABLE `taxes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `taxes` */

/*Table structure for table `titles` */

DROP TABLE IF EXISTS `titles`;

CREATE TABLE `titles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `titles` */

/*Table structure for table `transfer_orders` */

DROP TABLE IF EXISTS `transfer_orders`;

CREATE TABLE `transfer_orders` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `transfer_order_number` varchar(255) NOT NULL,
  `transfer_date` timestamp NOT NULL,
  `transfer_reason` varchar(255) NOT NULL,
  `source_warehouse_id` bigint(20) unsigned NOT NULL,
  `destination_warehouse_id` bigint(20) unsigned NOT NULL,
  `image_url` varchar(255) NOT NULL,
  `transfer_quantity` varchar(255) NOT NULL,
  `item_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `transfer_orders_source_warehouse_id_foreign` (`source_warehouse_id`),
  KEY `transfer_orders_destination_warehouse_id_foreign` (`destination_warehouse_id`),
  KEY `transfer_orders_item_id_foreign` (`item_id`),
  CONSTRAINT `transfer_orders_destination_warehouse_id_foreign` FOREIGN KEY (`destination_warehouse_id`) REFERENCES `warehouses` (`id`),
  CONSTRAINT `transfer_orders_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `create_items` (`id`),
  CONSTRAINT `transfer_orders_source_warehouse_id_foreign` FOREIGN KEY (`source_warehouse_id`) REFERENCES `warehouses` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `transfer_orders` */

/*Table structure for table `units` */

DROP TABLE IF EXISTS `units`;

CREATE TABLE `units` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `units` */

insert  into `units`(`id`,`name`,`created_at`,`updated_at`) values 
(1,Tested,2024-06-21 16:49:16,2024-06-21 16:49:16);

/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` bigint(20) unsigned NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `status_id` bigint(20) unsigned NOT NULL,
  `branch_id` bigint(20) unsigned NOT NULL,
  `warehouse_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `users_role_id_foreign` (`role_id`),
  KEY `users_status_id_foreign` (`status_id`),
  KEY `users_branch_id_foreign` (`branch_id`),
  KEY `users_warehouse_id_foreign` (`warehouse_id`),
  CONSTRAINT `users_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`),
  CONSTRAINT `users_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`),
  CONSTRAINT `users_status_id_foreign` FOREIGN KEY (`status_id`) REFERENCES `statuses` (`id`),
  CONSTRAINT `users_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `users` */

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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `vendor_types` */

insert  into `vendor_types`(`id`,`name`,`created_at`,`updated_at`) values 
(1,Dangote,2024-06-21 17:00:03,2024-06-21 17:00:03);

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `vendors` */

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `warehouses` */

/*Table structure for table `weights` */

DROP TABLE IF EXISTS `weights`;

CREATE TABLE `weights` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `weights` */

insert  into `weights`(`id`,`name`,`created_at`,`updated_at`) values 
(1,Kilogram,2024-06-21 16:47:35,2024-06-21 16:47:35);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
