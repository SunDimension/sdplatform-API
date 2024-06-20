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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `adjustment_types` */

/*Table structure for table `attributes` */

DROP TABLE IF EXISTS `attributes`;

CREATE TABLE `attributes` (
  `id` char(36) NOT NULL,
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `banks` */

/*Table structure for table `branches` */

DROP TABLE IF EXISTS `branches`;

CREATE TABLE `branches` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `branch_name` varchar(255) NOT NULL,
  `branch_address` varchar(255) NOT NULL,
  `contact_person` varchar(255) NOT NULL,
  `branch_email` varchar(255) NOT NULL,
  `branch_phone` varchar(255) NOT NULL,
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
  `id` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `brands` */

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

/*Table structure for table `countries` */

DROP TABLE IF EXISTS `countries`;

CREATE TABLE `countries` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `countries` */

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
  `warehouse _id` bigint(20) unsigned NOT NULL,
  `vendor_id` bigint(20) unsigned NOT NULL,
  `image_url` varchar(255) NOT NULL,
  `barcode` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `create_items_item_category_id_foreign` (`item_category_id`),
  KEY `create_items_unit_id_foreign` (`unit_id`),
  KEY `create_items_dimension_id_foreign` (`dimension_id`),
  KEY `create_items_weight_id_foreign` (`weight_id`),
  KEY `create_items_branch_id_foreign` (`branch_id`),
  KEY `create_items_vendor_id_foreign` (`vendor_id`),
  CONSTRAINT `create_items_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`),
  CONSTRAINT `create_items_dimension_id_foreign` FOREIGN KEY (`dimension_id`) REFERENCES `dimensions` (`id`),
  CONSTRAINT `create_items_item_category_id_foreign` FOREIGN KEY (`item_category_id`) REFERENCES `item_categories` (`id`),
  CONSTRAINT `create_items_unit_id_foreign` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`),
  CONSTRAINT `create_items_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`),
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
  `id` char(36) NOT NULL,
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
  CONSTRAINT `credit_sales_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`),
  CONSTRAINT `credit_sales_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customersses` (`id`),
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `customer_types` */

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
  `id` char(36) NOT NULL,
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
  CONSTRAINT `deliveries_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `deliveries` */

/*Table structure for table `designations` */

DROP TABLE IF EXISTS `designations`;

CREATE TABLE `designations` (
  `id` char(36) NOT NULL,
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `dimensions` */

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

/*Table structure for table `inventory_adjustments` */

DROP TABLE IF EXISTS `inventory_adjustments`;

CREATE TABLE `inventory_adjustments` (
  `id` char(36) NOT NULL,
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
  KEY `inventory_adjustments_branch_id_foreign` (`branch_id`),
  KEY `inventory_adjustments_warehouse_id_foreign` (`warehouse_id`),
  KEY `inventory_adjustments_item_category_id_foreign` (`item_category_id`),
  CONSTRAINT `inventory_adjustments_adjustment_type_id_foreign` FOREIGN KEY (`adjustment_type_id`) REFERENCES `adjustment_types` (`id`),
  CONSTRAINT `inventory_adjustments_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`),
  CONSTRAINT `inventory_adjustments_item_category_id_foreign` FOREIGN KEY (`item_category_id`) REFERENCES `item_categories` (`id`),
  CONSTRAINT `inventory_adjustments_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `create_items` (`id`),
  CONSTRAINT `inventory_adjustments_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `inventory_adjustments` */

/*Table structure for table `invoices` */

DROP TABLE IF EXISTS `invoices`;

CREATE TABLE `invoices` (
  `id` char(36) NOT NULL,
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
  CONSTRAINT `invoices_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customersses` (`id`),
  CONSTRAINT `invoices_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `create_items` (`id`),
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `item_categories` */

/*Table structure for table `item_types` */

DROP TABLE IF EXISTS `item_types`;

CREATE TABLE `item_types` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `item_types` */

/*Table structure for table `manufacturers` */

DROP TABLE IF EXISTS `manufacturers`;

CREATE TABLE `manufacturers` (
  `id` char(36) NOT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `migrations` */

insert  into `migrations`(`id`,`migration`,`batch`) values 
(1,2014_10_12_100000_create_password_resets_table,1),
(2,2019_08_19_000000_create_failed_jobs_table,1),
(3,2019_12_14_000001_create_personal_access_tokens_table,1),
(4,2024_06_13_171549_create_item_categories_table,1),
(5,2024_06_13_171550_create_item_types_table,1),
(6,2024_06_13_171551_create_units_table,1),
(7,2024_06_13_171552_create_dimensions_table,1),
(8,2024_06_13_171553_create_weights_table,1),
(9,2024_06_13_171554_create_expense_accounts_table,1),
(10,2024_06_13_171555_create_adjustment_types_table,1),
(11,2024_06_13_171556_create_statuses_table,1),
(12,2024_06_13_171557_create_customer_types_table,1),
(13,2024_06_13_171558_create_branches_table,1),
(14,2024_06_13_171559_create_titles_table,1),
(15,2024_06_13_171600_create_warehouses_table,1),
(16,2024_06_14_095023_create_taxes_table,1),
(17,2024_06_14_095024_create_discounts_table,1),
(18,2024_06_14_095025_create_payment_modes_table,1),
(19,2024_06_14_095026_create_carriers_table,1),
(20,2024_06_14_095027_create_payment_types_table,1),
(21,2024_06_14_095028_create_designations_table,1),
(22,2024_06_14_095029_create_banks_table,1),
(23,2024_06_14_095030_create_manufacturers_table,1),
(24,2024_06_14_095031_create_vendor_types_table,1),
(25,2024_06_14_095032_create_reasons_table,1),
(26,2024_06_14_095033_create_brands_table,1),
(27,2024_06_14_095034_create_attributes_table,1),
(28,2024_06_14_095035_create_countries_table,1),
(29,2024_06_14_095036_create_states_table,1),
(30,2024_06_14_095037_create_create_items_table,1),
(31,2024_06_14_095038_create_vendors_table,1),
(32,2024_06_14_095039_create_transfer_orders_table,1),
(33,2024_06_14_095040_create_deliveries_table,1),
(34,2024_06_14_095041_create_inventory_adjustments_table,1),
(35,2024_06_14_095042_create_invoices_table,1),
(36,2024_06_14_095043_create_sales_table,1),
(37,2024_06_14_095044_create_credit_limits_table,1),
(38,2024_06_14_095045_create_credit_sales_table,1),
(39,2024_06_14_095046_create_customers_table,1),
(40,2024_06_14_095047_create_payment_receiveds_table,1),
(41,2024_06_14_095048_create_sales_receipts_table,1),
(42,2024_06_14_095049_create_payment_vouchers_table,1),
(43,2024_06_14_095050_create_vendor_credits_table,1),
(44,2024_06_14_095051_create_purchase_received_details_table,1),
(45,2024_06_14_095054_create_new_purchas_orders_table,1),
(46,2024_06_14_095055_create_new_purchase_receiveds_table,1),
(47,2024_06_14_095056_create_new_payments_table,1),
(48,2024_06_14_095057_create_payment_voucher_details_table,1),
(49,2024_06_14_095058_create_purchase_order_details_table,1),
(50,2024_06_14_095707_create_payment_terms_table,1),
(51,2024_06_14_101033_create_roles_table,1),
(52,2024_06_14_101034_create_users_table,1);

/*Table structure for table `new_payments` */

DROP TABLE IF EXISTS `new_payments`;

CREATE TABLE `new_payments` (
  `id` char(36) NOT NULL,
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

/*Table structure for table `new_purchas_orders` */

DROP TABLE IF EXISTS `new_purchas_orders`;

CREATE TABLE `new_purchas_orders` (
  `id` char(36) NOT NULL,
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
  KEY `new_purchas_orders_item_category_id_foreign` (`item_category_id`),
  KEY `new_purchas_orders_item_id_foreign` (`item_id`),
  KEY `new_purchas_orders_vendor_id_foreign` (`vendor_id`),
  KEY `new_purchas_orders_branch_id_foreign` (`branch_id`),
  KEY `new_purchas_orders_payment_mode_id_foreign` (`payment_mode_id`),
  CONSTRAINT `new_purchas_orders_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`),
  CONSTRAINT `new_purchas_orders_item_category_id_foreign` FOREIGN KEY (`item_category_id`) REFERENCES `item_categories` (`id`),
  CONSTRAINT `new_purchas_orders_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `create_items` (`id`),
  CONSTRAINT `new_purchas_orders_payment_mode_id_foreign` FOREIGN KEY (`payment_mode_id`) REFERENCES `payment_modes` (`id`),
  CONSTRAINT `new_purchas_orders_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `new_purchas_orders` */

/*Table structure for table `new_purchase_receiveds` */

DROP TABLE IF EXISTS `new_purchase_receiveds`;

CREATE TABLE `new_purchase_receiveds` (
  `id` char(36) NOT NULL,
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
  `id` char(36) NOT NULL,
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
  KEY `payment_receiveds_payment_mode_id_foreign` (`payment_mode_id`),
  CONSTRAINT `payment_receiveds_payment_mode_id_foreign` FOREIGN KEY (`payment_mode_id`) REFERENCES `payment_modes` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `payment_receiveds` */

/*Table structure for table `payment_terms` */

DROP TABLE IF EXISTS `payment_terms`;

CREATE TABLE `payment_terms` (
  `id` char(36) NOT NULL,
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
  `id` char(36) NOT NULL,
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
  `id` char(36) NOT NULL,
  `product_id` bigint(20) unsigned NOT NULL,
  `expense_date` timestamp NOT NULL,
  `amount` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `branch_id` bigint(20) unsigned NOT NULL,
  `warehouse_id` bigint(20) unsigned NOT NULL,
  `tax_id` bigint(20) unsigned NOT NULL,
  `vendor_id` bigint(20) unsigned NOT NULL,
  `payment_mode_id` bigint(20) unsigned NOT NULL,
  `expense_account_id` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payment_vouchers_product_id_foreign` (`product_id`),
  KEY `payment_vouchers_branch_id_foreign` (`branch_id`),
  KEY `payment_vouchers_warehouse_id_foreign` (`warehouse_id`),
  KEY `payment_vouchers_tax_id_foreign` (`tax_id`),
  KEY `payment_vouchers_payment_mode_id_foreign` (`payment_mode_id`),
  CONSTRAINT `payment_vouchers_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`),
  CONSTRAINT `payment_vouchers_payment_mode_id_foreign` FOREIGN KEY (`payment_mode_id`) REFERENCES `payment_modes` (`id`),
  CONSTRAINT `payment_vouchers_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `create_items` (`id`),
  CONSTRAINT `payment_vouchers_tax_id_foreign` FOREIGN KEY (`tax_id`) REFERENCES `taxes` (`id`),
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `personal_access_tokens` */

insert  into `personal_access_tokens`(`id`,`tokenable_type`,`tokenable_id`,`name`,`token`,`abilities`,`last_used_at`,`expires_at`,`created_at`,`updated_at`) values 
(1,App\Models\User,9,authToken,fd809423c9aab66c9e9276494db23aec945c3ad18848988eec3c40f969bd32c7,["*"],NULL,NULL,2024-06-20 11:19:51,2024-06-20 11:19:51);

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
  `id` char(36) NOT NULL,
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
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `roles` */

/*Table structure for table `sales` */

DROP TABLE IF EXISTS `sales`;

CREATE TABLE `sales` (
  `id` char(36) NOT NULL,
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
  CONSTRAINT `sales_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`),
  CONSTRAINT `sales_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customersses` (`id`),
  CONSTRAINT `sales_discount_id_foreign` FOREIGN KEY (`discount_id`) REFERENCES `discounts` (`id`),
  CONSTRAINT `sales_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `create_items` (`id`),
  CONSTRAINT `sales_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `sales` */

/*Table structure for table `sales_receipts` */

DROP TABLE IF EXISTS `sales_receipts`;

CREATE TABLE `sales_receipts` (
  `id` char(36) NOT NULL,
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
  `id` char(36) NOT NULL,
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
  `id` char(36) NOT NULL,
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
  CONSTRAINT `transfer_orders_destination_warehouse_id_foreign` FOREIGN KEY (`destination_warehouse_id`) REFERENCES `warehouses` (`id`),
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `units` */

/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `branch_id` bigint(20) unsigned NOT NULL,
  `warehouse_id` bigint(20) unsigned NOT NULL,
  `status_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_branch_id_foreign` (`branch_id`),
  KEY `users_warehouse_id_foreign` (`warehouse_id`),
  KEY `users_status_id_foreign` (`status_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `users` */

insert  into `users`(`id`,`role_id`,`name`,`email`,`email_verified_at`,`password`,`remember_token`,`branch_id`,`warehouse_id`,`status_id`,`created_at`,`updated_at`) values 
(9,1,john,john@doe.com,NULL,$2y$10$rPeiB6EQKxOtmZgyI5z4Wegijph.mddVzqxisB5fWq4JR.vAP17MK,NULL,1,2,1,2024-06-20 10:08:54,2024-06-20 10:08:54);

/*Table structure for table `vendor_credits` */

DROP TABLE IF EXISTS `vendor_credits`;

CREATE TABLE `vendor_credits` (
  `id` char(36) NOT NULL,
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `vendor_types` */

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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `vendors` */

/*Table structure for table `warehouses` */

DROP TABLE IF EXISTS `warehouses`;

CREATE TABLE `warehouses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `warehouse_name` varchar(255) NOT NULL,
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `weights` */

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
