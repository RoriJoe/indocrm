-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Feb 13, 2013 at 06:08 AM
-- Server version: 5.1.66
-- PHP Version: 5.3.19

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `k9071857_indocrm`
--

-- --------------------------------------------------------

--
-- Table structure for table `abuse_report`
--

CREATE TABLE IF NOT EXISTS `abuse_report` (
  `abuse_id` int(11) NOT NULL AUTO_INCREMENT,
  `abuse_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `reason` text COLLATE utf8_unicode_ci,
  `mail_id` int(11) DEFAULT '0',
  `campaign_id` int(11) DEFAULT '0',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0',
  `status` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`abuse_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Table structure for table `adhoc_sms`
--

CREATE TABLE IF NOT EXISTS `adhoc_sms` (
  `user_id` int(11) NOT NULL,
  `sent_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`),
  KEY `sent_time` (`sent_time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `api_limit`
--

CREATE TABLE IF NOT EXISTS `api_limit` (
  `counter_limit` int(11) DEFAULT '12000',
  `counter` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  PRIMARY KEY (`client_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `api_log`
--

CREATE TABLE IF NOT EXISTS `api_log` (
  `id_log` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) DEFAULT NULL,
  `tgl_akses` date DEFAULT NULL,
  `jam_akses` time DEFAULT NULL,
  `request` varchar(45) NOT NULL,
  `variables` text,
  `results` text,
  PRIMARY KEY (`id_log`),
  KEY `client_id` (`client_id`),
  KEY `tgl_akses` (`tgl_akses`,`jam_akses`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=13 ;

-- --------------------------------------------------------

--
-- Table structure for table `assets`
--

CREATE TABLE IF NOT EXISTS `assets` (
  `asset_id` int(11) NOT NULL AUTO_INCREMENT,
  `image` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `client_id` int(11) NOT NULL DEFAULT '0',
  `thumb` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`asset_id`),
  KEY `client_id` (`client_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=46 ;

-- --------------------------------------------------------

--
-- Table structure for table `auto_code`
--

CREATE TABLE IF NOT EXISTS `auto_code` (
  `prefix` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `sequence` int(11) NOT NULL DEFAULT '1000',
  PRIMARY KEY (`prefix`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `campaign`
--

CREATE TABLE IF NOT EXISTS `campaign` (
  `campaign_id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL DEFAULT '0',
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `campaign_title` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `campaign_description` text COLLATE utf8_unicode_ci,
  `signature` text COLLATE utf8_unicode_ci NOT NULL,
  `is_delete` int(11) NOT NULL DEFAULT '0',
  `campaign_count` int(11) NOT NULL DEFAULT '0',
  `sent_date` datetime DEFAULT NULL,
  `campaign_source` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `campaign_type` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `template_id` int(11) DEFAULT NULL,
  `template` text COLLATE utf8_unicode_ci,
  `plaintemplate` text COLLATE utf8_unicode_ci,
  `is_sent` int(11) NOT NULL DEFAULT '0',
  `is_direct` tinyint(4) NOT NULL DEFAULT '0',
  `is_crontab` tinyint(1) NOT NULL DEFAULT '0',
  `log_message` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`campaign_id`),
  KEY `client_id` (`client_id`,`is_delete`),
  KEY `sent_date` (`sent_date`),
  KEY `template_id` (`template_id`),
  KEY `is_crontab` (`is_crontab`),
  KEY `is_direct` (`is_direct`),
  KEY `campaign_type` (`campaign_type`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=665 ;

-- --------------------------------------------------------

--
-- Table structure for table `campaign_details`
--

CREATE TABLE IF NOT EXISTS `campaign_details` (
  `campaign_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL DEFAULT '0',
  KEY `campaign_id` (`campaign_id`,`customer_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `carts`
--

CREATE TABLE IF NOT EXISTS `carts` (
  `cart_id` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `data` text CHARACTER SET utf8 NOT NULL,
  `last_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`cart_id`),
  KEY `last_update` (`last_update`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cities`
--

CREATE TABLE IF NOT EXISTS `cities` (
  `City` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `Country` char(3) COLLATE utf8_unicode_ci NOT NULL,
  `State` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  KEY `Country` (`Country`,`State`(10))
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--

CREATE TABLE IF NOT EXISTS `clients` (
  `client_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `address` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `city` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `state` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `zip_code` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `country` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `mobile` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `website` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `image` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `signature` text COLLATE utf8_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  `reg_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `mail_count` int(11) NOT NULL DEFAULT '0',
  `mail_quota` int(11) NOT NULL DEFAULT '0',
  `sms_count` int(11) NOT NULL DEFAULT '0',
  `sms_quota` int(11) NOT NULL DEFAULT '0',
  `active_date` int(11) DEFAULT NULL,
  `invoice_status` int(11) DEFAULT '0',
  `sms_free` int(11) DEFAULT '0',
  `mail_free` int(11) DEFAULT '0',
  `client_type` int(11) DEFAULT '0',
  `counter_limit` int(11) NOT NULL DEFAULT '0',
  `privatekey` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`client_id`),
  KEY `reg_date` (`reg_date`),
  KEY `active_date` (`active_date`,`invoice_status`,`client_type`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=121 ;

-- --------------------------------------------------------

--
-- Table structure for table `client_invoices`
--

CREATE TABLE IF NOT EXISTS `client_invoices` (
  `invoice_id` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `due_date` datetime DEFAULT NULL,
  `client_id` int(11) NOT NULL,
  `to_name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `to_company` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `to_email` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `to_address` text COLLATE utf8_unicode_ci,
  `customer_id` int(11) DEFAULT NULL,
  `subtotal` double DEFAULT NULL,
  `discount` double DEFAULT NULL,
  `discount_percent` double DEFAULT NULL,
  `tax` double DEFAULT NULL,
  `tax_percent` double DEFAULT NULL,
  `total` double DEFAULT NULL,
  `pay_total` double DEFAULT NULL,
  `pay_date` datetime DEFAULT NULL,
  `status` int(11) DEFAULT '0',
  `template_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`invoice_id`),
  KEY `status` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `client_invoices_details`
--

CREATE TABLE IF NOT EXISTS `client_invoices_details` (
  `detail_id` int(11) NOT NULL AUTO_INCREMENT,
  `invoice_id` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `quantity` double DEFAULT NULL,
  `price` double DEFAULT NULL,
  `discount` double DEFAULT NULL,
  `discount_percent` double DEFAULT NULL,
  `tax` double DEFAULT NULL,
  `tax_percent` double DEFAULT NULL,
  `subtotal` double DEFAULT NULL,
  `total` double DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `product_code` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`detail_id`),
  KEY `invoice_id` (`invoice_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=51 ;

-- --------------------------------------------------------

--
-- Table structure for table `countries`
--

CREATE TABLE IF NOT EXISTS `countries` (
  `Code` char(3) COLLATE utf8_unicode_ci NOT NULL,
  `Country` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`Code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cron_schedule`
--

CREATE TABLE IF NOT EXISTS `cron_schedule` (
  `schedule_id` int(11) NOT NULL AUTO_INCREMENT,
  `campaign_id` int(11) NOT NULL,
  `menit` char(2) DEFAULT '*',
  `jam` char(2) DEFAULT '*',
  `tgl` char(2) DEFAULT '*',
  `bln` char(2) DEFAULT '*',
  `hari` varchar(20) DEFAULT '*',
  `week_of_month` tinyint(2) NOT NULL DEFAULT '0',
  `once_a_year` tinyint(1) NOT NULL DEFAULT '0',
  `tgl_start` date DEFAULT NULL,
  `time_start` time DEFAULT NULL,
  `tgl_end` date DEFAULT NULL,
  `time_end` time DEFAULT NULL,
  `exec_date` date NOT NULL,
  `exec_time` time NOT NULL,
  `is_repeat` tinyint(1) NOT NULL DEFAULT '0',
  `counter` int(11) NOT NULL DEFAULT '0',
  `counter_limit` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`schedule_id`,`campaign_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE IF NOT EXISTS `customers` (
  `customer_id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `last_name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `city` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `state` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `zip_code` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `mobile` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `photo` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `facebook` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `twitter` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `bb_pin` varchar(8) COLLATE utf8_unicode_ci DEFAULT NULL,
  `website` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `client_id` int(11) NOT NULL DEFAULT '0',
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_delete` int(11) NOT NULL DEFAULT '0',
  `country` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `category` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`customer_id`),
  KEY `client_id` (`client_id`),
  KEY `client_id_2` (`client_id`,`is_delete`),
  KEY `category` (`category`),
  KEY `mobile` (`mobile`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=43769 ;

-- --------------------------------------------------------

--
-- Table structure for table `customer_categories`
--

CREATE TABLE IF NOT EXISTS `customer_categories` (
  `category_id` int(11) NOT NULL AUTO_INCREMENT,
  `category` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `client_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`category_id`),
  UNIQUE KEY `category` (`category`,`client_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=194 ;

-- --------------------------------------------------------

--
-- Table structure for table `customer_details`
--

CREATE TABLE IF NOT EXISTS `customer_details` (
  `customer_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  UNIQUE KEY `customer_id` (`customer_id`,`category_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `customer_notes`
--

CREATE TABLE IF NOT EXISTS `customer_notes` (
  `note_id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `notes` text,
  `date_posted` date DEFAULT NULL,
  `time_posted` time DEFAULT NULL,
  PRIMARY KEY (`note_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE IF NOT EXISTS `groups` (
  `group_id` int(11) NOT NULL AUTO_INCREMENT,
  `group_name` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `admin_group` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`group_id`),
  KEY `admin_group` (`admin_group`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=8 ;

-- --------------------------------------------------------

--
-- Table structure for table `group_perms`
--

CREATE TABLE IF NOT EXISTS `group_perms` (
  `group_id` int(11) NOT NULL,
  `perm_id` int(11) NOT NULL,
  PRIMARY KEY (`group_id`,`perm_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE IF NOT EXISTS `invoices` (
  `invoice_id` int(11) NOT NULL AUTO_INCREMENT,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `due_date` datetime DEFAULT NULL,
  `client_id` int(11) NOT NULL,
  `to_name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `to_company` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `to_address` text COLLATE utf8_unicode_ci,
  `pay_from` text COLLATE utf8_unicode_ci,
  `pay_type` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `pay_detail` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `subtotal` double DEFAULT NULL,
  `discount` double DEFAULT NULL,
  `discount_percent` double DEFAULT NULL,
  `tax` double DEFAULT NULL,
  `tax_percent` double DEFAULT NULL,
  `total` double DEFAULT NULL,
  `pay_total` double DEFAULT NULL,
  `pay_date` datetime DEFAULT NULL,
  `status` int(11) DEFAULT '0',
  PRIMARY KEY (`invoice_id`),
  KEY `status` (`status`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=26 ;

-- --------------------------------------------------------

--
-- Table structure for table `invoice_detail`
--

CREATE TABLE IF NOT EXISTS `invoice_detail` (
  `detail_id` int(11) NOT NULL AUTO_INCREMENT,
  `invoice_id` int(11) NOT NULL,
  `description` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `quantity` double DEFAULT NULL,
  `price` double DEFAULT NULL,
  `discount` double DEFAULT NULL,
  `discount_percent` double DEFAULT NULL,
  `tax` double DEFAULT NULL,
  `tax_percent` double DEFAULT NULL,
  `subtotal` double DEFAULT NULL,
  `total` double DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  PRIMARY KEY (`detail_id`),
  KEY `invoice_id` (`invoice_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=25 ;

-- --------------------------------------------------------

--
-- Table structure for table `invoice_templates`
--

CREATE TABLE IF NOT EXISTS `invoice_templates` (
  `template_id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL DEFAULT '0',
  `is_delete` int(11) NOT NULL DEFAULT '0',
  `name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `template` text COLLATE utf8_unicode_ci,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `thumbnail` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`template_id`),
  KEY `client_id` (`client_id`,`is_delete`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=10 ;

-- --------------------------------------------------------

--
-- Table structure for table `keywords`
--

CREATE TABLE IF NOT EXISTS `keywords` (
  `keyword_id` int(11) NOT NULL AUTO_INCREMENT,
  `keyword` varchar(20) NOT NULL,
  `class_action` varchar(50) DEFAULT NULL,
  `description` varchar(200) NOT NULL,
  `create_date` timestamp NULL DEFAULT NULL,
  `modif_date` timestamp NULL DEFAULT NULL,
  `client_id` int(11) DEFAULT NULL,
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`keyword_id`,`keyword`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=27 ;

-- --------------------------------------------------------

--
-- Table structure for table `keyword_params`
--

CREATE TABLE IF NOT EXISTS `keyword_params` (
  `param_id` int(11) NOT NULL AUTO_INCREMENT,
  `keyword_id` int(11) NOT NULL,
  `param` varchar(160) NOT NULL,
  `reply` varchar(160) DEFAULT NULL,
  PRIMARY KEY (`param_id`),
  KEY `param` (`param`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12 ;

-- --------------------------------------------------------

--
-- Table structure for table `limit_sms`
--

CREATE TABLE IF NOT EXISTS `limit_sms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `modem` varchar(20) DEFAULT NULL,
  `phone` varchar(20) NOT NULL,
  `counter_limit` int(11) DEFAULT NULL,
  `counter` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `modem` (`modem`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

--
-- Table structure for table `log_mo`
--

CREATE TABLE IF NOT EXISTS `log_mo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `msisdn` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `sms` text COLLATE utf8_unicode_ci NOT NULL,
  `src` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `client_id` int(11) NOT NULL,
  `date` date DEFAULT NULL,
  `time` time DEFAULT NULL,
  `is_read` enum('0','1','2') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`,`is_read`,`date`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=48285 ;

-- --------------------------------------------------------

--
-- Table structure for table `log_mo_widgets_options`
--

CREATE TABLE IF NOT EXISTS `log_mo_widgets_options` (
  `client_id` int(11) NOT NULL,
  `judul` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `blacklistnumber` text COLLATE utf8_unicode_ci,
  `blacklistwords` text COLLATE utf8_unicode_ci,
  `view` enum('10','15','20','25') COLLATE utf8_unicode_ci NOT NULL DEFAULT '10',
  `width` int(11) NOT NULL,
  `height` int(11) NOT NULL,
  `sizefont` int(11) NOT NULL,
  `colorsms` varchar(7) COLLATE utf8_unicode_ci NOT NULL,
  `colormobile` varchar(7) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`client_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mailconfig`
--

CREATE TABLE IF NOT EXISTS `mailconfig` (
  `client_id` int(11) NOT NULL,
  `mail_name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `username` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `password` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `host` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `port` int(11) DEFAULT NULL,
  `bcc` text COLLATE utf8_unicode_ci,
  `cc` text COLLATE utf8_unicode_ci,
  `ssl` tinyint(1) NOT NULL DEFAULT '0',
  `tls` tinyint(1) NOT NULL DEFAULT '0',
  `popauth` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`client_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mailjob`
--

CREATE TABLE IF NOT EXISTS `mailjob` (
  `job_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sent_date` datetime DEFAULT NULL,
  `campaign_id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `is_delete` int(11) NOT NULL DEFAULT '0',
  `is_sent` tinyint(1) NOT NULL DEFAULT '0',
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `template_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`job_id`),
  KEY `campaign_id` (`campaign_id`,`client_id`,`is_delete`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `maillog`
--

CREATE TABLE IF NOT EXISTS `maillog` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `sent_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `from_name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `from_email` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `to_name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `to_email` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `body_html` text COLLATE utf8_unicode_ci,
  `body_plain` text COLLATE utf8_unicode_ci,
  `is_sent` tinyint(1) DEFAULT '0',
  `campaign_id` int(11) DEFAULT '0',
  `email_number` int(11) NOT NULL DEFAULT '0',
  `total_count` int(11) NOT NULL DEFAULT '0',
  `customer_id` int(11) NOT NULL DEFAULT '0',
  `is_success` tinyint(1) DEFAULT '0',
  `client_id` int(11) DEFAULT '0',
  PRIMARY KEY (`log_id`),
  KEY `customer_id` (`customer_id`),
  KEY `campaign_id` (`campaign_id`,`email_number`),
  KEY `client_id` (`client_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=210 ;

-- --------------------------------------------------------

--
-- Table structure for table `mailtemplates`
--

CREATE TABLE IF NOT EXISTS `mailtemplates` (
  `template_id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL DEFAULT '0',
  `is_delete` int(11) NOT NULL DEFAULT '0',
  `name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `template` text COLLATE utf8_unicode_ci,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `thumbnail` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`template_id`),
  KEY `client_id` (`client_id`,`is_delete`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=28 ;

-- --------------------------------------------------------

--
-- Table structure for table `paket`
--

CREATE TABLE IF NOT EXISTS `paket` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_paket` varchar(45) NOT NULL,
  `biaya` int(11) NOT NULL,
  `description` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Table structure for table `perms`
--

CREATE TABLE IF NOT EXISTS `perms` (
  `perm_id` int(11) NOT NULL AUTO_INCREMENT,
  `perm_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `perm_path` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `children_count` int(11) NOT NULL DEFAULT '0',
  `public` tinyint(4) NOT NULL DEFAULT '0',
  `perm_order` int(11) DEFAULT '0',
  `perm_class` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`perm_id`),
  KEY `parent_id` (`parent_id`),
  KEY `perm_path` (`perm_path`,`public`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=49 ;

-- --------------------------------------------------------

--
-- Table structure for table `polling`
--

CREATE TABLE IF NOT EXISTS `polling` (
  `polling_id` int(11) NOT NULL AUTO_INCREMENT,
  `keyword` varchar(20) NOT NULL,
  `judul_polling` varchar(100) NOT NULL,
  `pilihan1` varchar(160) NOT NULL,
  `pilihan2` varchar(160) NOT NULL,
  `pilihan3` varchar(160) NOT NULL,
  `pilihan4` varchar(160) NOT NULL,
  `pilihan5` varchar(160) NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `client_id` int(11) NOT NULL,
  PRIMARY KEY (`polling_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table `polling_result`
--

CREATE TABLE IF NOT EXISTS `polling_result` (
  `result_id` int(11) NOT NULL AUTO_INCREMENT,
  `polling_id` int(11) NOT NULL,
  `pilihan1` int(11) DEFAULT NULL,
  `pilihan2` int(11) DEFAULT NULL,
  `pilihan3` int(11) DEFAULT NULL,
  `pilihan4` int(11) DEFAULT NULL,
  `pilihan5` int(11) DEFAULT NULL,
  PRIMARY KEY (`result_id`,`polling_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE IF NOT EXISTS `products` (
  `product_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_code` varchar(50) DEFAULT NULL,
  `product_name` varchar(100) DEFAULT NULL,
  `product_description` text,
  `product_images` varchar(100) DEFAULT NULL,
  `product_price` double DEFAULT NULL,
  `tax` double DEFAULT NULL,
  `discount` double DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `client_id` int(11) DEFAULT NULL,
  `is_delete` tinyint(1) DEFAULT '0' COMMENT '0:ACTIVE;1:READY DELETE;2:FULL DELETE',
  `product_thumb` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`product_id`),
  KEY `client_id` (`client_id`,`is_delete`,`product_name`(30))
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=24 ;

-- --------------------------------------------------------

--
-- Table structure for table `products_categories`
--

CREATE TABLE IF NOT EXISTS `products_categories` (
  `category_id` int(11) NOT NULL AUTO_INCREMENT,
  `category` varchar(100) NOT NULL,
  `client_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`category_id`),
  UNIQUE KEY `category` (`category`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=26 ;

-- --------------------------------------------------------

--
-- Table structure for table `radio_request`
--

CREATE TABLE IF NOT EXISTS `radio_request` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `radio_id` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `message` text COLLATE utf8_unicode_ci,
  `sender` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_delete` tinyint(1) NOT NULL DEFAULT '0',
  `post_date` datetime NOT NULL,
  `msisdn` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `radio_id` (`radio_id`,`is_delete`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=25 ;

-- --------------------------------------------------------

--
-- Table structure for table `smslog`
--

CREATE TABLE IF NOT EXISTS `smslog` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `sent_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `to_number` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `body_plain` text COLLATE utf8_unicode_ci,
  `is_sent` tinyint(1) DEFAULT '0',
  `campaign_id` int(11) DEFAULT '0',
  `sms_number` int(11) NOT NULL DEFAULT '0',
  `total_count` int(11) NOT NULL DEFAULT '0',
  `customer_id` int(11) NOT NULL DEFAULT '0',
  `is_success` tinyint(1) DEFAULT '0',
  `client_id` int(11) DEFAULT '0',
  `queue_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`log_id`),
  KEY `campaign_id` (`campaign_id`,`sms_number`),
  KEY `queue_id` (`queue_id`),
  KEY `client_id` (`client_id`),
  KEY `to_number` (`to_number`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=84582 ;

-- --------------------------------------------------------

--
-- Table structure for table `smsqueue`
--

CREATE TABLE IF NOT EXISTS `smsqueue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tanggal` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `number` varchar(20) NOT NULL,
  `message` varchar(750) NOT NULL,
  `status` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=50932 ;

-- --------------------------------------------------------

--
-- Table structure for table `smsqueue_partner`
--

CREATE TABLE IF NOT EXISTS `smsqueue_partner` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tanggal` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `number` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `message` varchar(750) COLLATE utf8_unicode_ci NOT NULL,
  `status` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=43793 ;

-- --------------------------------------------------------

--
-- Table structure for table `tagihan_client`
--

CREATE TABLE IF NOT EXISTS `tagihan_client` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL,
  `paket_id` int(2) NOT NULL,
  `due_date` date DEFAULT NULL,
  `paid` enum('0','1') DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=37 ;

-- --------------------------------------------------------

--
-- Table structure for table `temp_mobile1`
--

CREATE TABLE IF NOT EXISTS `temp_mobile1` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mobile` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mobile` (`mobile`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=93055 ;

-- --------------------------------------------------------

--
-- Table structure for table `temp_mobile2`
--

CREATE TABLE IF NOT EXISTS `temp_mobile2` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mobile` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mobile` (`mobile`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=93052 ;

-- --------------------------------------------------------

--
-- Table structure for table `Tickets32`
--

CREATE TABLE IF NOT EXISTS `Tickets32` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `stub` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `stub` (`stub`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=96743 ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(80) COLLATE utf8_unicode_ci DEFAULT NULL,
  `client_id` int(11) NOT NULL DEFAULT '0',
  `photo` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `confirm_hash` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_confirmed` tinyint(1) NOT NULL DEFAULT '0',
  `timezone` tinyint(3) NOT NULL,
  `timezone_id` varchar(65) COLLATE utf8_unicode_ci NOT NULL,
  `bdate` date NOT NULL,
  `mobile` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `username` (`username`),
  KEY `email` (`email`),
  KEY `confirm_hash` (`confirm_hash`),
  KEY `bdate` (`bdate`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=144 ;

-- --------------------------------------------------------

--
-- Table structure for table `user_groups`
--

CREATE TABLE IF NOT EXISTS `user_groups` (
  `user_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  PRIMARY KEY (`user_id`,`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_perms`
--

CREATE TABLE IF NOT EXISTS `user_perms` (
  `user_id` int(11) NOT NULL,
  `perm_id` int(11) NOT NULL,
  PRIMARY KEY (`user_id`,`perm_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_profiles`
--

CREATE TABLE IF NOT EXISTS `user_profiles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `country` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `website` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `photo` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `vouchers`
--

CREATE TABLE IF NOT EXISTS `vouchers` (
  `voucher_id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL DEFAULT '0',
  `voucher_code` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `use_date` datetime DEFAULT NULL,
  `customer_id` int(11) DEFAULT '0',
  `campaign_id` int(11) DEFAULT '0',
  `valid_from` datetime DEFAULT NULL,
  `valid_thru` datetime DEFAULT NULL,
  `voucher_value` decimal(10,2) NOT NULL,
  `is_delete` int(11) NOT NULL DEFAULT '0',
  `is_sent` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`voucher_id`),
  KEY `is_delete` (`is_delete`),
  KEY `client_id` (`client_id`,`is_delete`,`create_date`),
  KEY `voucher_code` (`voucher_code`(8))
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=644 ;

-- --------------------------------------------------------

--
-- Table structure for table `whitelist_email`
--

CREATE TABLE IF NOT EXISTS `whitelist_email` (
  `whitelist_id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `client_id` int(11) DEFAULT '0',
  `last_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `ip_address` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`whitelist_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
