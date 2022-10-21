-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               10.1.38-MariaDB - mariadb.org binary distribution
-- Server OS:                    Win64
-- HeidiSQL Version:             11.3.0.6295
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Dumping structure for table jcis_db.tbl_banks
CREATE TABLE IF NOT EXISTS `tbl_banks` (
  `bank_id` int(11) NOT NULL AUTO_INCREMENT,
  `bank_name` varchar(50) DEFAULT NULL,
  `bank_branch` varchar(150) DEFAULT NULL,
  `bank_account_name` varchar(50) DEFAULT NULL,
  `bank_account_number` varchar(50) DEFAULT NULL,
  `date_added` datetime DEFAULT CURRENT_TIMESTAMP,
  `date_last_modified` datetime DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`bank_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

-- Dumping data for table jcis_db.tbl_banks: ~2 rows (approximately)
/*!40000 ALTER TABLE `tbl_banks` DISABLE KEYS */;
INSERT INTO `tbl_banks` (`bank_id`, `bank_name`, `bank_branch`, `bank_account_name`, `bank_account_number`, `date_added`, `date_last_modified`) VALUES
	(1, 'BDO', 'BDO SM Branch', 'Juan Dela Cruz', '0215454444', '2022-10-03 10:42:03', '2022-10-12 08:41:51'),
	(2, 'BPI', 'BPI Main Branch', 'Juan Dela Cruz', '02121100001', '2022-10-21 09:31:23', '0000-00-00 00:00:00');
/*!40000 ALTER TABLE `tbl_banks` ENABLE KEYS */;

-- Dumping structure for table jcis_db.tbl_beginning_balance
CREATE TABLE IF NOT EXISTS `tbl_beginning_balance` (
  `bb_id` int(11) NOT NULL AUTO_INCREMENT,
  `bb_module` varchar(3) NOT NULL COMMENT 'AP ; AR : INV',
  `reference_number` varchar(30) NOT NULL,
  `bb_ref_id` int(11) NOT NULL,
  `bb_qty` decimal(12,2) NOT NULL DEFAULT '1.00',
  `bb_amount` decimal(12,2) NOT NULL,
  `bb_date` date NOT NULL,
  `bb_remarks` varchar(255) NOT NULL,
  `bb_paid_status` int(1) NOT NULL DEFAULT '0' COMMENT '1 = Paid : 0 = Unpaid',
  `encoded_by` int(11) NOT NULL,
  `date_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_last_modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`bb_id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=latin1;

-- Dumping data for table jcis_db.tbl_beginning_balance: ~7 rows (approximately)
/*!40000 ALTER TABLE `tbl_beginning_balance` DISABLE KEYS */;
INSERT INTO `tbl_beginning_balance` (`bb_id`, `bb_module`, `reference_number`, `bb_ref_id`, `bb_qty`, `bb_amount`, `bb_date`, `bb_remarks`, `bb_paid_status`, `encoded_by`, `date_added`, `date_last_modified`) VALUES
	(20, 'INV', 'BB-20221021080218', 4, 500.00, 250.00, '2022-10-21', '', 0, 1, '2022-10-21 14:02:27', '2022-10-21 14:02:27'),
	(21, 'INV', 'BB-20221021080229', 1, 500.00, 750000.00, '2022-10-21', '', 0, 1, '2022-10-21 14:02:40', '2022-10-21 14:02:40'),
	(22, 'INV', 'BB-20221021080250', 3, 500.00, 1500.00, '2022-10-21', '', 0, 1, '2022-10-21 14:03:15', '2022-10-21 14:03:15'),
	(23, 'INV', 'BB-20221021080316', 5, 500.00, 250000.00, '2022-10-21', '', 0, 1, '2022-10-21 14:03:27', '2022-10-21 14:03:27'),
	(24, 'AP', 'BB-20221021080753', 3, 0.00, 15000.00, '2022-10-21', '', 1, 1, '2022-10-21 14:08:00', '2022-10-21 14:08:43'),
	(25, 'AP', 'BB-20221021080801', 4, 0.00, 5000.00, '2022-10-21', '', 0, 1, '2022-10-21 14:08:11', '2022-10-21 14:08:11'),
	(26, 'AR', 'BB-20221021080812', 24, 0.00, 50000.00, '2022-10-21', '', 0, 1, '2022-10-21 14:08:19', '2022-10-21 14:08:19');
/*!40000 ALTER TABLE `tbl_beginning_balance` ENABLE KEYS */;

-- Dumping structure for table jcis_db.tbl_customers
CREATE TABLE IF NOT EXISTS `tbl_customers` (
  `customer_id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_name` varchar(100) NOT NULL,
  `customer_address` varchar(255) NOT NULL,
  `customer_contact_number` varchar(15) NOT NULL,
  `remarks` varchar(255) NOT NULL,
  `date_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_last_modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`customer_id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=latin1;

-- Dumping data for table jcis_db.tbl_customers: ~2 rows (approximately)
/*!40000 ALTER TABLE `tbl_customers` DISABLE KEYS */;
INSERT INTO `tbl_customers` (`customer_id`, `customer_name`, `customer_address`, `customer_contact_number`, `remarks`, `date_added`, `date_last_modified`) VALUES
	(0, 'Walk-in', '---', '---', 'PRESET ENTRY', '2022-10-05 09:05:24', '2022-10-05 09:05:40'),
	(24, 'Juan Ponce', 'Bacolod City', '02121', '', '2022-05-25 09:25:11', '2022-10-12 08:41:59');
/*!40000 ALTER TABLE `tbl_customers` ENABLE KEYS */;

-- Dumping structure for table jcis_db.tbl_customer_payment
CREATE TABLE IF NOT EXISTS `tbl_customer_payment` (
  `cp_id` int(11) NOT NULL AUTO_INCREMENT,
  `reference_number` varchar(50) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `payment_type` varchar(1) NOT NULL,
  `payment_option_id` int(11) NOT NULL,
  `payment_date` date NOT NULL,
  `remarks` varchar(255) NOT NULL,
  `status` varchar(1) NOT NULL,
  `deposit_status` int(1) NOT NULL DEFAULT '0' COMMENT '1 = Deposited',
  `check_number` varchar(30) NOT NULL,
  `check_bank` varchar(30) NOT NULL,
  `check_date` date NOT NULL,
  `encoded_by` int(11) NOT NULL,
  `date_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_last_modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`cp_id`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=latin1;

-- Dumping data for table jcis_db.tbl_customer_payment: ~4 rows (approximately)
/*!40000 ALTER TABLE `tbl_customer_payment` DISABLE KEYS */;
INSERT INTO `tbl_customer_payment` (`cp_id`, `reference_number`, `customer_id`, `payment_type`, `payment_option_id`, `payment_date`, `remarks`, `status`, `deposit_status`, `check_number`, `check_bank`, `check_date`, `encoded_by`, `date_added`, `date_last_modified`) VALUES
	(41, 'CP-221021090441', 0, 'C', 0, '2022-10-21', '', 'F', 0, '', '', '2022-10-21', 1, '2022-10-21 15:04:45', '2022-10-21 15:04:45'),
	(42, 'CP-221021092830', 24, 'H', 0, '2022-10-21', '', 'F', 1, '0001', 'BPI', '2022-10-21', 1, '2022-10-21 15:28:39', '2022-10-21 15:28:39'),
	(43, 'CP-221021092853', 24, 'C', 0, '2022-10-21', '', 'F', 0, '', '', '2022-10-21', 1, '2022-10-21 15:28:58', '2022-10-21 15:28:58'),
	(44, 'CP-221021092909', 0, 'H', 0, '2022-10-21', '', 'F', 1, '101010', 'bdo', '2022-10-21', 1, '2022-10-21 15:29:19', '2022-10-21 15:29:19');
/*!40000 ALTER TABLE `tbl_customer_payment` ENABLE KEYS */;

-- Dumping structure for table jcis_db.tbl_customer_payment_details
CREATE TABLE IF NOT EXISTS `tbl_customer_payment_details` (
  `cpd_id` int(11) NOT NULL AUTO_INCREMENT,
  `cp_id` int(11) NOT NULL,
  `ref_id` int(11) NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `type` varchar(2) NOT NULL COMMENT 'DR = sales ; BB = beginning balance',
  PRIMARY KEY (`cpd_id`)
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=latin1;

-- Dumping data for table jcis_db.tbl_customer_payment_details: ~4 rows (approximately)
/*!40000 ALTER TABLE `tbl_customer_payment_details` DISABLE KEYS */;
INSERT INTO `tbl_customer_payment_details` (`cpd_id`, `cp_id`, `ref_id`, `amount`, `type`) VALUES
	(52, 41, 71, 258.00, 'DR'),
	(53, 42, 72, 2000.00, 'DR'),
	(54, 43, 72, 5500.00, 'DR'),
	(55, 44, 71, 3000.00, 'DR');
/*!40000 ALTER TABLE `tbl_customer_payment_details` ENABLE KEYS */;

-- Dumping structure for table jcis_db.tbl_deposit
CREATE TABLE IF NOT EXISTS `tbl_deposit` (
  `deposit_id` int(11) NOT NULL AUTO_INCREMENT,
  `reference_number` varchar(30) NOT NULL,
  `bank_id` int(11) NOT NULL DEFAULT '0',
  `remarks` varchar(250) NOT NULL,
  `deposit_date` date NOT NULL,
  `deposit_type` varchar(1) NOT NULL COMMENT 'H = check ; C = cash',
  `status` varchar(1) NOT NULL DEFAULT 'S',
  `encoded_by` int(11) NOT NULL,
  `date_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_last_modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`deposit_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;

-- Dumping data for table jcis_db.tbl_deposit: ~1 rows (approximately)
/*!40000 ALTER TABLE `tbl_deposit` DISABLE KEYS */;
INSERT INTO `tbl_deposit` (`deposit_id`, `reference_number`, `bank_id`, `remarks`, `deposit_date`, `deposit_type`, `status`, `encoded_by`, `date_added`, `date_last_modified`) VALUES
	(12, 'DEP-20221021092812', 1, '', '2022-10-21', 'H', 'F', 1, '2022-10-21 15:28:18', '2022-10-21 15:29:41');
/*!40000 ALTER TABLE `tbl_deposit` ENABLE KEYS */;

-- Dumping structure for table jcis_db.tbl_deposit_details
CREATE TABLE IF NOT EXISTS `tbl_deposit_details` (
  `deposit_detail_id` int(11) NOT NULL AUTO_INCREMENT,
  `deposit_id` int(11) NOT NULL,
  `cp_id` int(11) NOT NULL,
  `amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`deposit_detail_id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=latin1;

-- Dumping data for table jcis_db.tbl_deposit_details: ~17 rows (approximately)
/*!40000 ALTER TABLE `tbl_deposit_details` DISABLE KEYS */;
INSERT INTO `tbl_deposit_details` (`deposit_detail_id`, `deposit_id`, `cp_id`, `amount`) VALUES
	(1, 3, 16, 25096.00),
	(11, 4, 0, 400.00),
	(12, 5, 16, 25096.00),
	(13, 6, 16, 25096.00),
	(18, 7, 16, 25096.00),
	(19, 7, 20, 1800.00),
	(21, 8, 0, 24000.00),
	(22, 8, 0, 50000.00),
	(23, 8, 0, 31231.00),
	(24, 8, 0, 11222.00),
	(25, 8, 0, 435.55),
	(26, 9, 16, 25096.00),
	(27, 9, 20, 1800.00),
	(28, 10, 0, 1500.00),
	(29, 11, 23, 10000.00),
	(30, 12, 42, 2000.00),
	(31, 12, 44, 3000.00);
/*!40000 ALTER TABLE `tbl_deposit_details` ENABLE KEYS */;

-- Dumping structure for table jcis_db.tbl_discounts
CREATE TABLE IF NOT EXISTS `tbl_discounts` (
  `discount_id` int(11) NOT NULL AUTO_INCREMENT,
  `discount_name` varchar(75) NOT NULL,
  `status` varchar(1) NOT NULL,
  `description` varchar(255) NOT NULL,
  `coverage_type` varchar(1) NOT NULL COMMENT 'A=All;L=Limited',
  `discount_type` varchar(1) NOT NULL COMMENT 'A=Automatic;M=Manual',
  `discount_percent` decimal(12,2) NOT NULL,
  `discount_start` datetime NOT NULL,
  `discount_end` datetime NOT NULL,
  `date_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_last_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `is_percentage` int(1) NOT NULL,
  `discount_amount` decimal(12,2) NOT NULL,
  PRIMARY KEY (`discount_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

-- Dumping data for table jcis_db.tbl_discounts: ~4 rows (approximately)
/*!40000 ALTER TABLE `tbl_discounts` DISABLE KEYS */;
INSERT INTO `tbl_discounts` (`discount_id`, `discount_name`, `status`, `description`, `coverage_type`, `discount_type`, `discount_percent`, `discount_start`, `discount_end`, `date_added`, `date_last_modified`, `is_percentage`, `discount_amount`) VALUES
	(1, '3', 'F', '3', 'A', 'A', 3.00, '2022-05-24 00:00:00', '2022-05-24 00:00:00', '2022-05-24 15:54:16', '2022-09-27 09:54:57', 0, 0.00),
	(2, 'Masskara Sale', 'F', '', 'L', 'M', 0.00, '2022-09-01 00:00:00', '2022-09-30 00:00:00', '2022-09-27 09:56:15', '2022-09-27 10:12:33', 0, 0.00),
	(3, 'Christmas Sale', 'F', '', 'L', 'A', 0.00, '2022-09-28 00:00:00', '2022-10-02 00:00:00', '2022-09-27 10:13:16', '2022-09-27 10:14:24', 0, 0.00),
	(4, 'buy 1 take 1', '', '', 'L', 'A', 0.00, '2022-09-27 00:00:00', '2022-09-27 00:00:00', '2022-09-27 10:21:38', '0000-00-00 00:00:00', 0, 0.00);
/*!40000 ALTER TABLE `tbl_discounts` ENABLE KEYS */;

-- Dumping structure for table jcis_db.tbl_discount_details
CREATE TABLE IF NOT EXISTS `tbl_discount_details` (
  `discount_detail_id` int(11) NOT NULL AUTO_INCREMENT,
  `discount_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `date_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_last_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`discount_detail_id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=latin1;

-- Dumping data for table jcis_db.tbl_discount_details: ~9 rows (approximately)
/*!40000 ALTER TABLE `tbl_discount_details` DISABLE KEYS */;
INSERT INTO `tbl_discount_details` (`discount_detail_id`, `discount_id`, `product_id`, `date_added`, `date_last_modified`) VALUES
	(5, 2, 3, '2022-09-27 10:12:03', '0000-00-00 00:00:00'),
	(6, 2, 4, '2022-09-27 10:12:03', '0000-00-00 00:00:00'),
	(9, 2, 5, '2022-09-27 10:12:20', '0000-00-00 00:00:00'),
	(10, 2, 1, '2022-09-27 10:12:20', '0000-00-00 00:00:00'),
	(11, 3, 1, '2022-09-27 10:13:26', '0000-00-00 00:00:00'),
	(12, 3, 3, '2022-09-27 10:13:26', '0000-00-00 00:00:00'),
	(13, 3, 6, '2022-09-27 10:13:29', '0000-00-00 00:00:00'),
	(14, 4, 1, '2022-09-27 10:21:51', '0000-00-00 00:00:00'),
	(15, 4, 5, '2022-09-27 10:21:53', '0000-00-00 00:00:00');
/*!40000 ALTER TABLE `tbl_discount_details` ENABLE KEYS */;

-- Dumping structure for table jcis_db.tbl_expense
CREATE TABLE IF NOT EXISTS `tbl_expense` (
  `expense_id` int(11) NOT NULL AUTO_INCREMENT,
  `reference_number` varchar(50) NOT NULL DEFAULT '',
  `expense_date` date NOT NULL,
  `remarks` varchar(255) NOT NULL DEFAULT '',
  `encoded_by` int(11) NOT NULL,
  `status` varchar(1) NOT NULL DEFAULT '',
  `date_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_last_modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`expense_id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=latin1;

-- Dumping data for table jcis_db.tbl_expense: ~0 rows (approximately)
/*!40000 ALTER TABLE `tbl_expense` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_expense` ENABLE KEYS */;

-- Dumping structure for table jcis_db.tbl_expense_category
CREATE TABLE IF NOT EXISTS `tbl_expense_category` (
  `expense_category_id` int(11) NOT NULL AUTO_INCREMENT,
  `expense_category_code` varchar(10) NOT NULL DEFAULT '',
  `expense_category` varchar(75) NOT NULL DEFAULT '',
  `expense_type` varchar(2) NOT NULL,
  `date_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_last_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`expense_category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

-- Dumping data for table jcis_db.tbl_expense_category: ~3 rows (approximately)
/*!40000 ALTER TABLE `tbl_expense_category` DISABLE KEYS */;
INSERT INTO `tbl_expense_category` (`expense_category_id`, `expense_category_code`, `expense_category`, `expense_type`, `date_added`, `date_last_modified`) VALUES
	(1, 'WTRs', 'Waterbills', 'O', '2022-02-28 14:35:33', '2022-10-18 09:28:45'),
	(2, 'asdasd', 'asdas', 'OE', '2022-02-28 15:11:23', '2022-10-18 09:38:08'),
	(3, '0002', 'Rental Fee', 'O', '2022-10-18 09:42:50', '2022-10-18 11:05:08');
/*!40000 ALTER TABLE `tbl_expense_category` ENABLE KEYS */;

-- Dumping structure for table jcis_db.tbl_expense_details
CREATE TABLE IF NOT EXISTS `tbl_expense_details` (
  `expense_detail_id` int(11) NOT NULL AUTO_INCREMENT,
  `expense_id` int(11) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `expense_category_id` int(11) NOT NULL,
  `invoice_no` varchar(15) NOT NULL DEFAULT '',
  `amount` decimal(12,2) NOT NULL,
  PRIMARY KEY (`expense_detail_id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=latin1;

-- Dumping data for table jcis_db.tbl_expense_details: ~0 rows (approximately)
/*!40000 ALTER TABLE `tbl_expense_details` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_expense_details` ENABLE KEYS */;

-- Dumping structure for table jcis_db.tbl_formulation
CREATE TABLE IF NOT EXISTS `tbl_formulation` (
  `formulation_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(75) NOT NULL,
  `remarks` varchar(255) NOT NULL,
  `date_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_last_modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`formulation_id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=latin1;

-- Dumping data for table jcis_db.tbl_formulation: ~0 rows (approximately)
/*!40000 ALTER TABLE `tbl_formulation` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_formulation` ENABLE KEYS */;

-- Dumping structure for table jcis_db.tbl_formulation_details
CREATE TABLE IF NOT EXISTS `tbl_formulation_details` (
  `formulation_detail_id` int(11) NOT NULL AUTO_INCREMENT,
  `formulation_id` int(11) NOT NULL DEFAULT '0',
  `product_id` int(11) NOT NULL DEFAULT '0',
  `qty` decimal(7,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`formulation_detail_id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=latin1;

-- Dumping data for table jcis_db.tbl_formulation_details: ~0 rows (approximately)
/*!40000 ALTER TABLE `tbl_formulation_details` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_formulation_details` ENABLE KEYS */;

-- Dumping structure for table jcis_db.tbl_inventory_adjustments
CREATE TABLE IF NOT EXISTS `tbl_inventory_adjustments` (
  `adjustment_id` int(11) NOT NULL AUTO_INCREMENT,
  `reference_number` varchar(75) NOT NULL,
  `remarks` varchar(255) NOT NULL,
  `status` varchar(1) NOT NULL,
  `encoded_by` int(11) NOT NULL,
  `adjustment_date` datetime NOT NULL,
  `date_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_last_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`adjustment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table jcis_db.tbl_inventory_adjustments: ~0 rows (approximately)
/*!40000 ALTER TABLE `tbl_inventory_adjustments` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_inventory_adjustments` ENABLE KEYS */;

-- Dumping structure for table jcis_db.tbl_inventory_adjustment_details
CREATE TABLE IF NOT EXISTS `tbl_inventory_adjustment_details` (
  `adjustment_detail_id` int(11) NOT NULL AUTO_INCREMENT,
  `adjustment_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` decimal(12,2) NOT NULL,
  `cost` decimal(12,2) NOT NULL,
  `price` decimal(12,2) NOT NULL,
  `date_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_last_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`adjustment_detail_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table jcis_db.tbl_inventory_adjustment_details: ~0 rows (approximately)
/*!40000 ALTER TABLE `tbl_inventory_adjustment_details` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_inventory_adjustment_details` ENABLE KEYS */;

-- Dumping structure for table jcis_db.tbl_job_orders
CREATE TABLE IF NOT EXISTS `tbl_job_orders` (
  `job_order_id` int(11) NOT NULL AUTO_INCREMENT,
  `reference_number` varchar(75) NOT NULL,
  `product_id` int(11) NOT NULL,
  `no_of_batches` int(3) NOT NULL,
  `remarks` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  `job_order_date` date NOT NULL,
  `encoded_by` int(11) NOT NULL,
  `status` varchar(1) NOT NULL,
  `date_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_last_modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`job_order_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;

-- Dumping data for table jcis_db.tbl_job_orders: ~0 rows (approximately)
/*!40000 ALTER TABLE `tbl_job_orders` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_job_orders` ENABLE KEYS */;

-- Dumping structure for table jcis_db.tbl_job_order_details
CREATE TABLE IF NOT EXISTS `tbl_job_order_details` (
  `jo_detail_id` int(11) NOT NULL AUTO_INCREMENT,
  `job_order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `qty` decimal(7,2) NOT NULL,
  `cost` decimal(12,5) NOT NULL,
  PRIMARY KEY (`jo_detail_id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=latin1;

-- Dumping data for table jcis_db.tbl_job_order_details: ~0 rows (approximately)
/*!40000 ALTER TABLE `tbl_job_order_details` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_job_order_details` ENABLE KEYS */;

-- Dumping structure for table jcis_db.tbl_payment_option
CREATE TABLE IF NOT EXISTS `tbl_payment_option` (
  `payment_option_id` int(11) NOT NULL AUTO_INCREMENT,
  `payment_option` varchar(50) NOT NULL,
  `date_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_last_modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`payment_option_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

-- Dumping data for table jcis_db.tbl_payment_option: ~2 rows (approximately)
/*!40000 ALTER TABLE `tbl_payment_option` DISABLE KEYS */;
INSERT INTO `tbl_payment_option` (`payment_option_id`, `payment_option`, `date_added`, `date_last_modified`) VALUES
	(1, 'GCash', '2022-05-24 15:55:09', '2022-05-24 15:55:09'),
	(2, 'Paymaya', '2022-09-27 15:47:00', '2022-09-27 15:47:00');
/*!40000 ALTER TABLE `tbl_payment_option` ENABLE KEYS */;

-- Dumping structure for table jcis_db.tbl_price_notice
CREATE TABLE IF NOT EXISTS `tbl_price_notice` (
  `price_id` int(11) NOT NULL AUTO_INCREMENT,
  `reference_number` varchar(75) NOT NULL,
  `effective_date` date NOT NULL,
  `effective_status` int(1) NOT NULL,
  `remarks` varchar(255) NOT NULL,
  `status` varchar(1) NOT NULL,
  `date_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_last_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`price_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table jcis_db.tbl_price_notice: ~0 rows (approximately)
/*!40000 ALTER TABLE `tbl_price_notice` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_price_notice` ENABLE KEYS */;

-- Dumping structure for table jcis_db.tbl_price_notice_details
CREATE TABLE IF NOT EXISTS `tbl_price_notice_details` (
  `price_detail_id` int(11) NOT NULL AUTO_INCREMENT,
  `price_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `old_price` decimal(12,2) NOT NULL,
  `price` decimal(12,2) NOT NULL,
  `date_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_last_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`price_detail_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table jcis_db.tbl_price_notice_details: ~0 rows (approximately)
/*!40000 ALTER TABLE `tbl_price_notice_details` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_price_notice_details` ENABLE KEYS */;

-- Dumping structure for table jcis_db.tbl_products
CREATE TABLE IF NOT EXISTS `tbl_products` (
  `product_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_code` varchar(100) NOT NULL,
  `product_barcode` varchar(50) NOT NULL,
  `product_name` varchar(75) NOT NULL,
  `product_price` decimal(11,2) NOT NULL,
  `product_cost` decimal(12,5) NOT NULL,
  `product_img` text NOT NULL,
  `product_category_id` int(11) NOT NULL,
  `is_package` int(1) NOT NULL,
  `remarks` varchar(255) NOT NULL,
  `date_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_last_modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;

-- Dumping data for table jcis_db.tbl_products: ~8 rows (approximately)
/*!40000 ALTER TABLE `tbl_products` DISABLE KEYS */;
INSERT INTO `tbl_products` (`product_id`, `product_code`, `product_barcode`, `product_name`, `product_price`, `product_cost`, `product_img`, `product_category_id`, `is_package`, `remarks`, `date_added`, `date_last_modified`) VALUES
	(1, '0001', '5654654656546', 'Swinr Gestating', 1250.00, 1500.00000, 'default.jpg', 1, 1, '', '2022-02-10 11:37:33', '2022-10-21 14:02:40'),
	(3, '0002', '', 'DTC 100ml', 23.00, 3.00000, 'default.jpg', 2, 0, 'aqwd\r\n', '2022-02-16 09:43:33', '2022-10-21 14:03:15'),
	(4, '0003', '', 'Sack', 8.00, 0.50000, 'default.jpg', 5, 0, '312', '2022-02-16 11:02:15', '2022-10-21 14:02:27'),
	(5, '0004', '', 'Layer-1', 1500.00, 500.00000, 'default.jpg', 1, 1, 'as', '2022-02-21 15:15:41', '2022-10-21 14:03:27'),
	(6, '0005', '', 'Grower Mash', 1324.00, 2500.00000, 'default.jpg', 1, 1, 'sdfsf', '2022-02-21 15:15:55', '2022-10-21 14:04:44'),
	(8, '000222', '', 'Lactating Mash', 1254.00, 1000.00000, 'default.jpg', 1, 1, 'dadas', '2022-04-22 09:05:43', '2022-10-21 14:04:44'),
	(9, '00021', '', 'Copra Meal', 2.00, 4.00000, 'default.jpg', 6, 0, '', '2022-09-27 15:56:43', '2022-10-21 14:04:44'),
	(10, '234234', '2334234', 'sample', 233.00, 200.00000, '', 1, 1, '', '2022-10-13 14:00:51', '2022-10-13 15:40:43');
/*!40000 ALTER TABLE `tbl_products` ENABLE KEYS */;

-- Dumping structure for table jcis_db.tbl_product_categories
CREATE TABLE IF NOT EXISTS `tbl_product_categories` (
  `product_category_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_category` varchar(75) NOT NULL,
  `date_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_last_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`product_category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

-- Dumping data for table jcis_db.tbl_product_categories: ~5 rows (approximately)
/*!40000 ALTER TABLE `tbl_product_categories` DISABLE KEYS */;
INSERT INTO `tbl_product_categories` (`product_category_id`, `product_category`, `date_added`, `date_last_modified`) VALUES
	(1, 'Feeds', '2022-02-09 14:41:36', '2022-09-27 15:53:39'),
	(2, 'Medicine', '2022-02-11 14:49:11', '2022-09-27 15:53:46'),
	(4, 'Vaccine', '2022-02-16 09:01:10', '2022-09-27 15:53:53'),
	(5, 'Packaging Materials', '2022-02-16 09:39:18', '2022-09-27 15:54:00'),
	(6, 'Raw Materials/Ingredients', '2022-02-17 13:03:52', '2022-09-27 15:54:14');
/*!40000 ALTER TABLE `tbl_product_categories` ENABLE KEYS */;

-- Dumping structure for table jcis_db.tbl_product_conversions
CREATE TABLE IF NOT EXISTS `tbl_product_conversions` (
  `conversion_id` int(11) NOT NULL AUTO_INCREMENT,
  `reference_number` varchar(75) NOT NULL,
  `original_product_id` int(11) NOT NULL,
  `original_qty` decimal(12,2) NOT NULL,
  `converted_product_id` int(11) NOT NULL,
  `converted_qty` decimal(12,2) NOT NULL,
  `remarks` varchar(255) NOT NULL,
  `encoded_by` int(11) NOT NULL,
  `conversion_date` datetime NOT NULL,
  `date_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_last_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`conversion_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table jcis_db.tbl_product_conversions: ~0 rows (approximately)
/*!40000 ALTER TABLE `tbl_product_conversions` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_product_conversions` ENABLE KEYS */;

-- Dumping structure for table jcis_db.tbl_product_transactions
CREATE TABLE IF NOT EXISTS `tbl_product_transactions` (
  `transaction_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `quantity` decimal(12,2) NOT NULL,
  `cost` decimal(12,5) NOT NULL,
  `price` decimal(12,2) NOT NULL,
  `header_id` int(11) NOT NULL,
  `detail_id` int(11) NOT NULL,
  `module` varchar(3) NOT NULL DEFAULT '' COMMENT 'SLS=Sales',
  `type` varchar(3) NOT NULL DEFAULT '' COMMENT 'IN,OUT',
  `status` int(1) NOT NULL DEFAULT '0' COMMENT '1=Finished;0=Saved',
  `date_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`transaction_id`)
) ENGINE=InnoDB AUTO_INCREMENT=255 DEFAULT CHARSET=latin1;

-- Dumping data for table jcis_db.tbl_product_transactions: ~11 rows (approximately)
/*!40000 ALTER TABLE `tbl_product_transactions` DISABLE KEYS */;
INSERT INTO `tbl_product_transactions` (`transaction_id`, `product_id`, `quantity`, `cost`, `price`, `header_id`, `detail_id`, `module`, `type`, `status`, `date_added`, `date_modified`) VALUES
	(259, 4, 500.00, 0.50000, 0.00, 20, 0, 'BB', 'IN', 1, '2022-10-21 14:02:27', '0000-00-00 00:00:00'),
	(260, 1, 500.00, 1500.00000, 0.00, 21, 0, 'BB', 'IN', 1, '2022-10-21 14:02:40', '0000-00-00 00:00:00'),
	(261, 3, 500.00, 3.00000, 0.00, 22, 0, 'BB', 'IN', 1, '2022-10-21 14:03:15', '0000-00-00 00:00:00'),
	(262, 5, 500.00, 500.00000, 0.00, 23, 0, 'BB', 'IN', 1, '2022-10-21 14:03:27', '0000-00-00 00:00:00'),
	(263, 6, 500.00, 2500.00000, 2500.00, 32, 56, 'PO', 'IN', 1, '2022-10-21 14:04:24', '2022-10-21 14:04:44'),
	(264, 8, 5000.00, 1000.00000, 1000.00, 32, 57, 'PO', 'IN', 1, '2022-10-21 14:04:32', '2022-10-21 14:04:44'),
	(265, 9, 5000.00, 4.00000, 4.00, 32, 58, 'PO', 'IN', 1, '2022-10-21 14:04:38', '2022-10-21 14:04:44'),
	(266, 1, 5.00, 1500.00000, 1250.00, 71, 106, 'SLS', 'OUT', 1, '2022-10-21 14:09:25', '2022-10-21 14:09:31'),
	(267, 4, 1.00, 0.50000, 8.00, 71, 107, 'SLS', 'OUT', 1, '2022-10-21 14:09:28', '2022-10-21 14:09:31'),
	(268, 1, 5.00, 1500.00000, 1250.00, 72, 108, 'SLS', 'OUT', 1, '2022-10-21 14:23:37', '2022-10-21 14:23:42'),
	(269, 1, 1.00, 1500.00000, 1250.00, 72, 109, 'SLS', 'OUT', 1, '2022-10-21 14:23:40', '2022-10-21 14:23:42');
/*!40000 ALTER TABLE `tbl_product_transactions` ENABLE KEYS */;

-- Dumping structure for table jcis_db.tbl_purchase_order
CREATE TABLE IF NOT EXISTS `tbl_purchase_order` (
  `po_id` int(11) NOT NULL AUTO_INCREMENT,
  `reference_number` varchar(30) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `po_invoice` varchar(30) NOT NULL,
  `po_terms` int(5) NOT NULL DEFAULT '0',
  `po_date` date NOT NULL,
  `po_type` varchar(1) NOT NULL,
  `paid_status` int(1) NOT NULL DEFAULT '0' COMMENT '1 = Paid; 0 = Unpaid',
  `po_remarks` varchar(255) NOT NULL,
  `status` varchar(1) NOT NULL,
  `encoded_by` int(11) NOT NULL,
  `date_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_last_modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`po_id`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=latin1;

-- Dumping data for table jcis_db.tbl_purchase_order: ~1 rows (approximately)
/*!40000 ALTER TABLE `tbl_purchase_order` DISABLE KEYS */;
INSERT INTO `tbl_purchase_order` (`po_id`, `reference_number`, `supplier_id`, `po_invoice`, `po_terms`, `po_date`, `po_type`, `paid_status`, `po_remarks`, `status`, `encoded_by`, `date_added`, `date_last_modified`) VALUES
	(32, 'PO-20221021080344', 3, '0001', 2, '2022-10-21', 'H', 1, '', 'F', 1, '2022-10-21 14:04:02', '2022-10-21 14:04:02');
/*!40000 ALTER TABLE `tbl_purchase_order` ENABLE KEYS */;

-- Dumping structure for table jcis_db.tbl_purchase_order_details
CREATE TABLE IF NOT EXISTS `tbl_purchase_order_details` (
  `po_detail_id` int(11) NOT NULL AUTO_INCREMENT,
  `po_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `qty` decimal(11,2) NOT NULL,
  `supplier_price` decimal(11,2) NOT NULL,
  PRIMARY KEY (`po_detail_id`)
) ENGINE=InnoDB AUTO_INCREMENT=56 DEFAULT CHARSET=latin1;

-- Dumping data for table jcis_db.tbl_purchase_order_details: ~3 rows (approximately)
/*!40000 ALTER TABLE `tbl_purchase_order_details` DISABLE KEYS */;
INSERT INTO `tbl_purchase_order_details` (`po_detail_id`, `po_id`, `product_id`, `qty`, `supplier_price`) VALUES
	(56, 32, 6, 500.00, 2500.00),
	(57, 32, 8, 5000.00, 1000.00),
	(58, 32, 9, 5000.00, 4.00);
/*!40000 ALTER TABLE `tbl_purchase_order_details` ENABLE KEYS */;

-- Dumping structure for table jcis_db.tbl_purchase_return
CREATE TABLE IF NOT EXISTS `tbl_purchase_return` (
  `pr_id` int(11) NOT NULL AUTO_INCREMENT,
  `reference_number` varchar(50) NOT NULL,
  `po_id` int(11) NOT NULL,
  `return_date` date NOT NULL,
  `remarks` varchar(255) NOT NULL,
  `status` varchar(1) NOT NULL,
  `encoded_by` int(11) NOT NULL,
  `date_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_last_modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`pr_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

-- Dumping data for table jcis_db.tbl_purchase_return: ~0 rows (approximately)
/*!40000 ALTER TABLE `tbl_purchase_return` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_purchase_return` ENABLE KEYS */;

-- Dumping structure for table jcis_db.tbl_purchase_return_details
CREATE TABLE IF NOT EXISTS `tbl_purchase_return_details` (
  `pr_detail_id` int(11) NOT NULL AUTO_INCREMENT,
  `pr_id` int(11) NOT NULL,
  `po_detail_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `qty` decimal(12,2) NOT NULL DEFAULT '0.00',
  `qty_return` decimal(12,2) NOT NULL DEFAULT '0.00',
  `supplier_price` decimal(12,5) NOT NULL,
  PRIMARY KEY (`pr_detail_id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=latin1;

-- Dumping data for table jcis_db.tbl_purchase_return_details: ~0 rows (approximately)
/*!40000 ALTER TABLE `tbl_purchase_return_details` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_purchase_return_details` ENABLE KEYS */;

-- Dumping structure for table jcis_db.tbl_sales
CREATE TABLE IF NOT EXISTS `tbl_sales` (
  `sales_id` int(11) NOT NULL AUTO_INCREMENT,
  `withdrawal_status` int(1) DEFAULT '0' COMMENT '0 = Fully served ; 1 = Pending',
  `sales_invoice` int(11) NOT NULL,
  `reference_number` varchar(30) NOT NULL DEFAULT '',
  `customer_id` int(11) NOT NULL,
  `for_pick_up` int(1) NOT NULL DEFAULT '0' COMMENT '1 = Yes; 0 = No',
  `status` varchar(1) NOT NULL DEFAULT 'S' COMMENT 'F',
  `sales_type` varchar(1) NOT NULL DEFAULT '' COMMENT 'C=Cash;H=Charge',
  `remarks` varchar(255) NOT NULL DEFAULT '',
  `sales_date` date NOT NULL,
  `paid_status` varchar(1) NOT NULL DEFAULT '0' COMMENT 'COMMENT ''1 = Paid; 0 = Unpaid'',',
  `sales_summary_id` int(11) NOT NULL,
  `encoded_by` int(11) NOT NULL,
  `date_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_last_modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`sales_id`)
) ENGINE=InnoDB AUTO_INCREMENT=71 DEFAULT CHARSET=latin1;

-- Dumping data for table jcis_db.tbl_sales: ~2 rows (approximately)
/*!40000 ALTER TABLE `tbl_sales` DISABLE KEYS */;
INSERT INTO `tbl_sales` (`sales_id`, `withdrawal_status`, `sales_invoice`, `reference_number`, `customer_id`, `for_pick_up`, `status`, `sales_type`, `remarks`, `sales_date`, `paid_status`, `sales_summary_id`, `encoded_by`, `date_added`, `date_last_modified`) VALUES
	(71, 1, 0, 'DR-221021080918', 0, 1, 'F', 'H', '', '2022-10-21', '0', 0, 1, '2022-10-21 14:09:22', '2022-10-21 14:19:32'),
	(72, 1, 0, 'DR-221021082319', 24, 1, 'F', 'H', '', '2022-10-21', '1', 0, 1, '2022-10-21 14:23:28', '2022-10-21 15:29:07');
/*!40000 ALTER TABLE `tbl_sales` ENABLE KEYS */;

-- Dumping structure for table jcis_db.tbl_sales_details
CREATE TABLE IF NOT EXISTS `tbl_sales_details` (
  `sales_detail_id` int(11) NOT NULL AUTO_INCREMENT,
  `sales_id` int(11) NOT NULL,
  `product_category_id` int(11) NOT NULL,
  `discount_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `discount` decimal(8,2) NOT NULL,
  `quantity` decimal(12,2) NOT NULL,
  `cost` decimal(12,5) NOT NULL,
  `price` decimal(12,2) NOT NULL,
  `date_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`sales_detail_id`)
) ENGINE=InnoDB AUTO_INCREMENT=106 DEFAULT CHARSET=latin1;

-- Dumping data for table jcis_db.tbl_sales_details: ~4 rows (approximately)
/*!40000 ALTER TABLE `tbl_sales_details` DISABLE KEYS */;
INSERT INTO `tbl_sales_details` (`sales_detail_id`, `sales_id`, `product_category_id`, `discount_id`, `product_id`, `discount`, `quantity`, `cost`, `price`, `date_added`) VALUES
	(106, 71, 0, 0, 1, 0.00, 5.00, 1500.00000, 1250.00, '2022-10-21 14:09:25'),
	(107, 71, 0, 0, 4, 0.00, 1.00, 0.50000, 8.00, '2022-10-21 14:09:28'),
	(108, 72, 0, 0, 1, 0.00, 5.00, 1500.00000, 1250.00, '2022-10-21 14:23:37'),
	(109, 72, 0, 0, 1, 0.00, 1.00, 1500.00000, 1250.00, '2022-10-21 14:23:40');
/*!40000 ALTER TABLE `tbl_sales_details` ENABLE KEYS */;

-- Dumping structure for table jcis_db.tbl_sales_return
CREATE TABLE IF NOT EXISTS `tbl_sales_return` (
  `sales_return_id` int(11) NOT NULL AUTO_INCREMENT,
  `sales_id` int(11) NOT NULL,
  `sales_reference_number` varchar(30) NOT NULL DEFAULT '',
  `reference_number` varchar(30) NOT NULL DEFAULT '',
  `return_date` date NOT NULL,
  `status` varchar(1) NOT NULL DEFAULT 'S',
  `encoded_by` int(11) NOT NULL,
  `remarks` varchar(255) NOT NULL DEFAULT '',
  `date_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_last_modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`sales_return_id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=latin1;

-- Dumping data for table jcis_db.tbl_sales_return: ~0 rows (approximately)
/*!40000 ALTER TABLE `tbl_sales_return` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_sales_return` ENABLE KEYS */;

-- Dumping structure for table jcis_db.tbl_sales_return_details
CREATE TABLE IF NOT EXISTS `tbl_sales_return_details` (
  `return_detail_id` int(11) NOT NULL AUTO_INCREMENT,
  `sales_return_id` int(11) NOT NULL,
  `sales_detail_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` decimal(12,2) NOT NULL,
  `quantity_return` decimal(12,2) NOT NULL,
  `price` decimal(12,2) NOT NULL,
  `cost` decimal(12,5) NOT NULL,
  `date_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`return_detail_id`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=latin1;

-- Dumping data for table jcis_db.tbl_sales_return_details: ~0 rows (approximately)
/*!40000 ALTER TABLE `tbl_sales_return_details` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_sales_return_details` ENABLE KEYS */;

-- Dumping structure for table jcis_db.tbl_sales_summary
CREATE TABLE IF NOT EXISTS `tbl_sales_summary` (
  `sales_summary_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `cashier_id` int(11) NOT NULL,
  `starting_balance` decimal(11,2) NOT NULL,
  `total_sales_amount` decimal(11,2) NOT NULL,
  `total_amount_collected` decimal(11,2) NOT NULL,
  `total_deficit` decimal(9,2) DEFAULT NULL,
  `encoded_by` int(11) NOT NULL,
  `status` varchar(1) NOT NULL DEFAULT '',
  `date_added` datetime NOT NULL,
  PRIMARY KEY (`sales_summary_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table jcis_db.tbl_sales_summary: ~0 rows (approximately)
/*!40000 ALTER TABLE `tbl_sales_summary` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_sales_summary` ENABLE KEYS */;

-- Dumping structure for table jcis_db.tbl_settings
CREATE TABLE IF NOT EXISTS `tbl_settings` (
  `settings_id` int(11) NOT NULL AUTO_INCREMENT,
  `module_discount` varchar(32) NOT NULL,
  `module_cancel` varchar(32) NOT NULL,
  `module_delete` varchar(32) NOT NULL,
  `date_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_last_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `module_add_customer` varchar(32) NOT NULL,
  `module_change_payment_type` varchar(32) NOT NULL,
  `module_remove_online_payment` varchar(32) NOT NULL,
  `company_name` varchar(50) NOT NULL,
  `company_address` varchar(100) NOT NULL,
  `print_header` text NOT NULL,
  `print_footer` text NOT NULL,
  PRIMARY KEY (`settings_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

-- Dumping data for table jcis_db.tbl_settings: ~1 rows (approximately)
/*!40000 ALTER TABLE `tbl_settings` DISABLE KEYS */;
INSERT INTO `tbl_settings` (`settings_id`, `module_discount`, `module_cancel`, `module_delete`, `date_added`, `date_last_modified`, `module_add_customer`, `module_change_payment_type`, `module_remove_online_payment`, `company_name`, `company_address`, `print_header`, `print_footer`) VALUES
	(1, '', '', '', '2022-10-21 11:14:31', '2022-10-21 11:45:25', '', '', '', 'QL POULTRY AND AGRICULTURAL SUPPLY', 'Davao City, Mindanao, Philippines', '', '');
/*!40000 ALTER TABLE `tbl_settings` ENABLE KEYS */;

-- Dumping structure for table jcis_db.tbl_stock_withdrawal
CREATE TABLE IF NOT EXISTS `tbl_stock_withdrawal` (
  `withdrawal_id` int(11) NOT NULL AUTO_INCREMENT,
  `reference_number` varchar(50) NOT NULL,
  `sales_id` int(11) NOT NULL DEFAULT '0',
  `status` varchar(1) NOT NULL DEFAULT 'S',
  `remarks` varchar(250) NOT NULL,
  `withdrawal_date` date NOT NULL,
  `encoded_by` int(11) NOT NULL,
  `date_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_last_modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`withdrawal_id`)
) ENGINE=InnoDB AUTO_INCREMENT=71 DEFAULT CHARSET=latin1;

-- Dumping data for table jcis_db.tbl_stock_withdrawal: ~3 rows (approximately)
/*!40000 ALTER TABLE `tbl_stock_withdrawal` DISABLE KEYS */;
INSERT INTO `tbl_stock_withdrawal` (`withdrawal_id`, `reference_number`, `sales_id`, `status`, `remarks`, `withdrawal_date`, `encoded_by`, `date_added`, `date_last_modified`) VALUES
	(71, 'SW-20221021080934', 71, 'F', '', '2022-10-21', 1, '2022-10-21 14:09:34', '2022-10-21 14:09:34'),
	(72, 'SW-20221021082344', 72, 'F', '', '2022-10-21', 1, '2022-10-21 14:23:44', '2022-10-21 14:23:44'),
	(73, 'SW-221021082349', 71, 'S', '', '2022-10-21', 1, '2022-10-21 14:23:52', '2022-10-21 14:23:52');
/*!40000 ALTER TABLE `tbl_stock_withdrawal` ENABLE KEYS */;

-- Dumping structure for table jcis_db.tbl_stock_withdrawal_details
CREATE TABLE IF NOT EXISTS `tbl_stock_withdrawal_details` (
  `sw_detail_id` int(11) NOT NULL AUTO_INCREMENT,
  `withdrawal_id` int(11) NOT NULL DEFAULT '0',
  `sales_detail_id` int(11) NOT NULL DEFAULT '0',
  `product_id` int(11) DEFAULT NULL,
  `qty` decimal(8,2) NOT NULL DEFAULT '0.00',
  `status` varchar(1) NOT NULL DEFAULT 'S',
  PRIMARY KEY (`sw_detail_id`)
) ENGINE=InnoDB AUTO_INCREMENT=141 DEFAULT CHARSET=latin1;

-- Dumping data for table jcis_db.tbl_stock_withdrawal_details: ~5 rows (approximately)
/*!40000 ALTER TABLE `tbl_stock_withdrawal_details` DISABLE KEYS */;
INSERT INTO `tbl_stock_withdrawal_details` (`sw_detail_id`, `withdrawal_id`, `sales_detail_id`, `product_id`, `qty`, `status`) VALUES
	(141, 71, 107, 4, 1.00, 'F'),
	(142, 72, 108, 1, 0.00, 'F'),
	(143, 72, 109, 1, 1.00, 'F'),
	(144, 73, 106, 1, 0.00, 'S'),
	(145, 73, 107, 4, 0.00, 'S');
/*!40000 ALTER TABLE `tbl_stock_withdrawal_details` ENABLE KEYS */;

-- Dumping structure for table jcis_db.tbl_suppliers
CREATE TABLE IF NOT EXISTS `tbl_suppliers` (
  `supplier_id` int(11) NOT NULL AUTO_INCREMENT,
  `supplier_name` varchar(100) NOT NULL,
  `supplier_address` varchar(255) NOT NULL,
  `contact_number` varchar(15) NOT NULL,
  `remarks` varchar(255) NOT NULL,
  `date_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_last_modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`supplier_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

-- Dumping data for table jcis_db.tbl_suppliers: ~2 rows (approximately)
/*!40000 ALTER TABLE `tbl_suppliers` DISABLE KEYS */;
INSERT INTO `tbl_suppliers` (`supplier_id`, `supplier_name`, `supplier_address`, `contact_number`, `remarks`, `date_added`, `date_last_modified`) VALUES
	(3, 'Grand Mall', 'bacolod', '032111', '', '2022-10-21 12:12:16', '2022-10-21 12:12:16'),
	(4, 'Juan Shop', 'Bacolod', '0211', '', '2022-10-21 12:12:33', '2022-10-21 12:12:33');
/*!40000 ALTER TABLE `tbl_suppliers` ENABLE KEYS */;

-- Dumping structure for table jcis_db.tbl_supplier_payment
CREATE TABLE IF NOT EXISTS `tbl_supplier_payment` (
  `sp_id` int(11) NOT NULL AUTO_INCREMENT,
  `reference_number` varchar(50) NOT NULL,
  `payment_type` varchar(1) NOT NULL COMMENT 'C = Cash; H = Check',
  `bank_id` int(11) NOT NULL,
  `check_number` varchar(30) NOT NULL,
  `check_date` date NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `payment_date` date NOT NULL,
  `remarks` varchar(255) NOT NULL,
  `status` varchar(1) NOT NULL,
  `encoded_by` int(11) NOT NULL,
  `due_status` int(1) NOT NULL DEFAULT '0' COMMENT '0 = Pending; 1 = cleared',
  `date_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_last_modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`sp_id`)
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=latin1;

-- Dumping data for table jcis_db.tbl_supplier_payment: ~4 rows (approximately)
/*!40000 ALTER TABLE `tbl_supplier_payment` DISABLE KEYS */;
INSERT INTO `tbl_supplier_payment` (`sp_id`, `reference_number`, `payment_type`, `bank_id`, `check_number`, `check_date`, `supplier_id`, `payment_date`, `remarks`, `status`, `encoded_by`, `due_status`, `date_added`, `date_last_modified`) VALUES
	(43, 'SP-20221021080548', 'H', 1, '50001101', '2022-10-21', 3, '2022-10-21', '', 'F', 1, 0, '2022-10-21 14:05:58', '2022-10-21 14:05:58'),
	(44, 'SP-20221021080631', 'H', 2, '00002', '2022-12-09', 3, '2022-10-21', '', 'F', 1, 0, '2022-10-21 14:06:44', '2022-10-21 14:06:44'),
	(45, 'SP-20221021080708', 'C', 0, '', '2022-10-21', 3, '2022-10-21', '', 'F', 1, 0, '2022-10-21 14:07:13', '2022-10-21 14:07:13'),
	(46, 'SP-20221021080823', 'C', 0, '', '2022-10-21', 3, '2022-10-21', '', 'F', 1, 0, '2022-10-21 14:08:28', '2022-10-21 14:08:28');
/*!40000 ALTER TABLE `tbl_supplier_payment` ENABLE KEYS */;

-- Dumping structure for table jcis_db.tbl_supplier_payment_details
CREATE TABLE IF NOT EXISTS `tbl_supplier_payment_details` (
  `spd_id` int(11) NOT NULL AUTO_INCREMENT,
  `sp_id` int(11) NOT NULL,
  `ref_id` int(11) NOT NULL,
  `type` varchar(2) NOT NULL COMMENT 'BB, PO',
  `amount` decimal(12,2) NOT NULL,
  PRIMARY KEY (`spd_id`)
) ENGINE=InnoDB AUTO_INCREMENT=74 DEFAULT CHARSET=latin1;

-- Dumping data for table jcis_db.tbl_supplier_payment_details: ~6 rows (approximately)
/*!40000 ALTER TABLE `tbl_supplier_payment_details` DISABLE KEYS */;
INSERT INTO `tbl_supplier_payment_details` (`spd_id`, `sp_id`, `ref_id`, `type`, `amount`) VALUES
	(74, 43, 32, 'PO', 270000.00),
	(75, 44, 32, 'PO', 60000.00),
	(76, 44, 32, 'PO', 250000.00),
	(77, 45, 32, 'PO', 5600000.00),
	(78, 45, 32, 'PO', 90000.00),
	(79, 46, 24, 'BB', 15000.00);
/*!40000 ALTER TABLE `tbl_supplier_payment_details` ENABLE KEYS */;

-- Dumping structure for table jcis_db.tbl_users
CREATE TABLE IF NOT EXISTS `tbl_users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_fullname` varchar(100) NOT NULL,
  `user_category` varchar(1) NOT NULL,
  `username` varchar(30) NOT NULL,
  `password` text NOT NULL,
  `date_added` datetime NOT NULL,
  `date_last_modified` datetime NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

-- Dumping data for table jcis_db.tbl_users: ~3 rows (approximately)
/*!40000 ALTER TABLE `tbl_users` DISABLE KEYS */;
INSERT INTO `tbl_users` (`user_id`, `user_fullname`, `user_category`, `username`, `password`, `date_added`, `date_last_modified`) VALUES
	(1, 'Juan Dela Cruz', 'A', 'admin', '0cc175b9c0f1b6a831c399e269772661', '2022-09-27 16:39:33', '2022-09-27 16:39:34'),
	(2, 'Staff Account', 'S', 'staff', '0cc175b9c0f1b6a831c399e269772661', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
	(3, 'Cashier Account', 'C', 'cashier', '0cc175b9c0f1b6a831c399e269772661', '0000-00-00 00:00:00', '0000-00-00 00:00:00');
/*!40000 ALTER TABLE `tbl_users` ENABLE KEYS */;

-- Dumping structure for table jcis_db.tbl_user_privileges
CREATE TABLE IF NOT EXISTS `tbl_user_privileges` (
  `privilege_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `url` varchar(50) NOT NULL,
  `status` int(1) NOT NULL,
  `date_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_last_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`privilege_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;

-- Dumping data for table jcis_db.tbl_user_privileges: ~8 rows (approximately)
/*!40000 ALTER TABLE `tbl_user_privileges` DISABLE KEYS */;
INSERT INTO `tbl_user_privileges` (`privilege_id`, `user_id`, `url`, `status`, `date_added`, `date_last_modified`) VALUES
	(1, 2, 'purchase-order', 1, '2022-10-12 15:52:04', '0000-00-00 00:00:00'),
	(2, 2, 'purchase-return', 1, '2022-10-12 15:52:04', '0000-00-00 00:00:00'),
	(3, 2, 'sales', 1, '2022-10-12 15:52:04', '0000-00-00 00:00:00'),
	(4, 2, 'sales-return', 1, '2022-10-12 15:52:04', '0000-00-00 00:00:00'),
	(5, 2, 'stock-withdrawal', 1, '2022-10-12 15:52:04', '0000-00-00 00:00:00'),
	(6, 2, 'expense', 1, '2022-10-12 15:52:04', '0000-00-00 00:00:00'),
	(7, 2, 'sales-report', 1, '2022-10-12 15:52:04', '0000-00-00 00:00:00'),
	(8, 2, 'inventory-report', 1, '2022-10-12 15:52:04', '0000-00-00 00:00:00');
/*!40000 ALTER TABLE `tbl_user_privileges` ENABLE KEYS */;

-- Dumping structure for trigger jcis_db.add_transaction_in_bb
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `add_transaction_in_bb` AFTER INSERT ON `tbl_beginning_balance` FOR EACH ROW IF(NEW.bb_module = 'INV') 
  THEN
    INSERT INTO tbl_product_transactions (product_id,quantity,header_id,module,TYPE,STATUS,cost) VALUES (NEW.bb_ref_id,NEW.bb_qty,NEW.bb_id,'BB','IN', '1', (NEW.bb_amount/NEW.bb_qty));
  END IF//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Dumping structure for trigger jcis_db.add_transaction_in_jo
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `add_transaction_in_jo` AFTER INSERT ON `tbl_job_orders` FOR EACH ROW INSERT INTO tbl_product_transactions (product_id,quantity,header_id,module,type) VALUES (NEW.product_id,NEW.no_of_batches,NEW.job_order_id,'JO','IN')//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Dumping structure for trigger jcis_db.add_transaction_in_po
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `add_transaction_in_po` AFTER INSERT ON `tbl_purchase_order_details` FOR EACH ROW INSERT INTO tbl_product_transactions (product_id,quantity,cost,price,header_id,detail_id,module,type) VALUES (NEW.product_id,NEW.qty,NEW.supplier_price,NEW.supplier_price,NEW.po_id,NEW.po_detail_id,'PO','IN')//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Dumping structure for trigger jcis_db.add_transaction_in_sales_return
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `add_transaction_in_sales_return` AFTER INSERT ON `tbl_sales_return_details` FOR EACH ROW INSERT INTO tbl_product_transactions (product_id,quantity,cost,price,header_id,detail_id,module,type) VALUES (NEW.product_id,NEW.quantity_return,NEW.cost,NEW.price,NEW.sales_return_id,NEW.return_detail_id,'SR','IN')//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Dumping structure for trigger jcis_db.add_transaction_out_jo
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `add_transaction_out_jo` AFTER INSERT ON `tbl_job_order_details` FOR EACH ROW INSERT INTO tbl_product_transactions (product_id,quantity,cost,price,header_id,detail_id,module,type) VALUES (NEW.product_id,NEW.qty,NEW.cost,NEW.cost,NEW.job_order_id,NEW.jo_detail_id,'JO','OUT')//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Dumping structure for trigger jcis_db.add_transaction_out_purchase_return
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `add_transaction_out_purchase_return` AFTER INSERT ON `tbl_purchase_return_details` FOR EACH ROW INSERT INTO tbl_product_transactions (product_id,quantity,cost,price,header_id,detail_id,module,type) VALUES (NEW.product_id,NEW.qty_return,NEW.supplier_price,NEW.supplier_price,NEW.pr_id,NEW.pr_detail_id,'PR','OUT')//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Dumping structure for trigger jcis_db.add_transaction_out_sales
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `add_transaction_out_sales` AFTER INSERT ON `tbl_sales_details` FOR EACH ROW INSERT INTO tbl_product_transactions (product_id,quantity,cost,price,header_id,detail_id,module,type) VALUES (NEW.product_id,NEW.quantity,NEW.cost,NEW.price,NEW.sales_id,NEW.sales_detail_id,'SLS','OUT')//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Dumping structure for trigger jcis_db.delete_custmer_payment_details
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `delete_custmer_payment_details` AFTER DELETE ON `tbl_customer_payment` FOR EACH ROW DELETE FROM tbl_customer_payment_details WHERE cp_id = OLD.cp_id//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Dumping structure for trigger jcis_db.delete_expenses_details
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `delete_expenses_details` AFTER DELETE ON `tbl_expense` FOR EACH ROW DELETE FROM tbl_expense_details WHERE expense_id = OLD.expense_id//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Dumping structure for trigger jcis_db.delete_formulation_details
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `delete_formulation_details` AFTER DELETE ON `tbl_formulation` FOR EACH ROW DELETE FROM tbl_formulation_details WHERE formulation_id= OLD.formulation_id//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Dumping structure for trigger jcis_db.delete_job_order_details
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `delete_job_order_details` AFTER DELETE ON `tbl_job_orders` FOR EACH ROW BEGIN
DELETE FROM tbl_job_order_details WHERE job_order_id = OLD.job_order_id;
DELETE FROM tbl_product_transactions WHERE header_id = OLD.job_order_id AND module = 'JO';
END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Dumping structure for trigger jcis_db.delete_po_details
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `delete_po_details` AFTER DELETE ON `tbl_purchase_order` FOR EACH ROW DELETE FROM tbl_purchase_order_details WHERE po_id = OLD.po_id//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Dumping structure for trigger jcis_db.delete_purchase_return_details
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `delete_purchase_return_details` AFTER DELETE ON `tbl_purchase_return` FOR EACH ROW DELETE FROM tbl_purchase_return_details WHERE pr_id = OLD.pr_id//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Dumping structure for trigger jcis_db.delete_sales_details
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `delete_sales_details` AFTER DELETE ON `tbl_sales` FOR EACH ROW DELETE FROM tbl_sales_details WHERE sales_id = OLD.sales_id//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Dumping structure for trigger jcis_db.delete_sales_return_details
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `delete_sales_return_details` AFTER DELETE ON `tbl_sales_return` FOR EACH ROW DELETE FROM tbl_sales_return_details WHERE sales_return_id = OLD.sales_return_id//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Dumping structure for trigger jcis_db.delete_stock_withdrawal_details
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `delete_stock_withdrawal_details` AFTER DELETE ON `tbl_stock_withdrawal` FOR EACH ROW DELETE FROM tbl_stock_withdrawal_details WHERE withdrawal_id = OLD.withdrawal_id//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Dumping structure for trigger jcis_db.delete_supplier_payment_details
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `delete_supplier_payment_details` AFTER DELETE ON `tbl_supplier_payment` FOR EACH ROW DELETE FROM tbl_supplier_payment_details WHERE sp_id = OLD.sp_id//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Dumping structure for trigger jcis_db.delete_transaction_bb
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `delete_transaction_bb` AFTER DELETE ON `tbl_beginning_balance` FOR EACH ROW DELETE FROM tbl_product_transactions WHERE header_id = OLD.bb_id AND module = 'BB'//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Dumping structure for trigger jcis_db.delete_transaction_jo_details
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `delete_transaction_jo_details` AFTER DELETE ON `tbl_job_order_details` FOR EACH ROW DELETE FROM tbl_product_transactions WHERE detail_id = OLD.jo_detail_id AND header_id = OLD.job_order_id AND module = 'JO'//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Dumping structure for trigger jcis_db.delete_transaction_po_details
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `delete_transaction_po_details` AFTER DELETE ON `tbl_purchase_order_details` FOR EACH ROW DELETE FROM tbl_product_transactions WHERE detail_id = OLD.po_detail_id AND header_id = OLD.po_id AND module = 'PO'//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Dumping structure for trigger jcis_db.delete_transaction_purchase_return
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `delete_transaction_purchase_return` AFTER DELETE ON `tbl_purchase_return_details` FOR EACH ROW DELETE FROM tbl_product_transactions WHERE detail_id = OLD.pr_detail_id AND header_id = OLD.pr_id AND module = 'PR'//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Dumping structure for trigger jcis_db.delete_transaction_sales
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `delete_transaction_sales` AFTER DELETE ON `tbl_sales_details` FOR EACH ROW DELETE FROM tbl_product_transactions WHERE detail_id = OLD.sales_detail_id AND header_id = OLD.sales_id AND module = 'SLS'//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Dumping structure for trigger jcis_db.delete_transaction_sales_return
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `delete_transaction_sales_return` AFTER DELETE ON `tbl_sales_return_details` FOR EACH ROW DELETE FROM tbl_product_transactions WHERE detail_id = OLD.return_detail_id AND header_id = OLD.sales_return_id AND module = 'SR'//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Dumping structure for trigger jcis_db.finish_transaction_jo
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `finish_transaction_jo` AFTER UPDATE ON `tbl_job_orders` FOR EACH ROW UPDATE tbl_product_transactions SET status = IF (NEW.status = 'F', 1, 0) WHERE header_id = NEW.job_order_id AND module = 'JO'//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Dumping structure for trigger jcis_db.finish_transaction_po
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `finish_transaction_po` AFTER UPDATE ON `tbl_purchase_order` FOR EACH ROW UPDATE tbl_product_transactions SET status = IF (NEW.status = 'F', 1, 0) WHERE header_id = NEW.po_id AND module = 'PO'//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Dumping structure for trigger jcis_db.finish_transaction_purchase_return
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `finish_transaction_purchase_return` AFTER UPDATE ON `tbl_purchase_return` FOR EACH ROW UPDATE tbl_product_transactions SET status = IF (NEW.status = 'F', 1, 0) WHERE header_id = NEW.pr_id AND module = 'PR'//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Dumping structure for trigger jcis_db.finish_transaction_sales
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `finish_transaction_sales` AFTER UPDATE ON `tbl_sales` FOR EACH ROW UPDATE tbl_product_transactions SET status = IF (NEW.status = 'F', 1, 0) WHERE header_id = NEW.sales_id AND module = 'SLS'//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Dumping structure for trigger jcis_db.finish_transaction_sales_return
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `finish_transaction_sales_return` AFTER UPDATE ON `tbl_sales_return` FOR EACH ROW UPDATE tbl_product_transactions SET status = IF (NEW.status = 'F', 1, 0) WHERE header_id = NEW.sales_return_id AND module = 'SR'//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Dumping structure for trigger jcis_db.update_transaction
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO';
DELIMITER //
CREATE TRIGGER `update_transaction` AFTER UPDATE ON `tbl_sales_details` FOR EACH ROW UPDATE tbl_product_transactions SET product_id = NEW.product_id,quantity = NEW.quantity,cost=NEW.cost, price = NEW.price WHERE detail_id = OLD.sales_detail_id AND header_id = OLD.sales_id AND module = 'SLS'//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Dumping structure for trigger jcis_db.update_transaction_qty_pr
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `update_transaction_qty_pr` AFTER UPDATE ON `tbl_purchase_return_details` FOR EACH ROW UPDATE tbl_product_transactions SET quantity=NEW.qty_return WHERE header_id = NEW.pr_id AND detail_id= NEW.pr_detail_id AND module = 'PR'//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Dumping structure for trigger jcis_db.update_transaction_qty_sr
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `update_transaction_qty_sr` AFTER UPDATE ON `tbl_sales_return_details` FOR EACH ROW UPDATE tbl_product_transactions SET quantity=NEW.quantity_return WHERE header_id = NEW.sales_return_id AND detail_id= NEW.return_detail_id AND module = 'SR'//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
