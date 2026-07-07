/*
SQLyog Ultimate v12.5.1 (64 bit)
MySQL - 8.0.30 : Database - kucingmu
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`kucingmu` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;

USE `kucingmu`;

/*Table structure for table `appointments` */

DROP TABLE IF EXISTS `appointments`;

CREATE TABLE `appointments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `cat_id` bigint unsigned NOT NULL,
  `date` date NOT NULL,
  `time_slot` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('scheduled','checked_in','completed','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'scheduled',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `appointments_cat_id_foreign` (`cat_id`),
  CONSTRAINT `appointments_cat_id_foreign` FOREIGN KEY (`cat_id`) REFERENCES `cats` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `appointments` */

insert  into `appointments`(`id`,`cat_id`,`date`,`time_slot`,`status`,`notes`,`created_at`,`updated_at`) values 
(1,1,'2026-07-07','On-site Registration','completed','Registrasi langsung di lokasi event.','2026-07-07 14:30:39','2026-07-07 14:34:14'),
(2,2,'2026-07-07','Sesi Sore (16:00 - 17:30)','scheduled','Kutu dan Cacing','2026-07-07 14:33:10','2026-07-07 14:33:10');

/*Table structure for table `cache` */

DROP TABLE IF EXISTS `cache`;

CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `cache` */

/*Table structure for table `cache_locks` */

DROP TABLE IF EXISTS `cache_locks`;

CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `cache_locks` */

/*Table structure for table `cats` */

DROP TABLE IF EXISTS `cats`;

CREATE TABLE `cats` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `breed` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `gender` enum('male','female') COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_of_birth` date NOT NULL,
  `photo_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `allergies` text COLLATE utf8mb4_unicode_ci,
  `vaccine_history` text COLLATE utf8mb4_unicode_ci,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cats_user_id_foreign` (`user_id`),
  CONSTRAINT `cats_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `cats` */

insert  into `cats`(`id`,`user_id`,`name`,`breed`,`gender`,`date_of_birth`,`photo_path`,`allergies`,`vaccine_history`,`notes`,`created_at`,`updated_at`) values 
(1,5,'Didi','Persia Medium','female','2020-08-18',NULL,NULL,NULL,NULL,'2026-07-07 14:30:39','2026-07-07 14:30:39'),
(2,4,'Zizie','Persia Medium','female','2026-01-08','cats/Ayj5CbUaV1hzuBCGCoFsCAyut6S5jsmjothexEEt.jpg',NULL,NULL,NULL,'2026-07-07 14:32:43','2026-07-07 14:32:43');

/*Table structure for table `failed_jobs` */

DROP TABLE IF EXISTS `failed_jobs`;

CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `failed_jobs` */

/*Table structure for table `job_batches` */

DROP TABLE IF EXISTS `job_batches`;

CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `job_batches` */

/*Table structure for table `jobs` */

DROP TABLE IF EXISTS `jobs`;

CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` smallint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `jobs` */

/*Table structure for table `ktam_cards` */

DROP TABLE IF EXISTS `ktam_cards`;

CREATE TABLE `ktam_cards` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `cat_id` bigint unsigned NOT NULL,
  `ktam_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `issue_date` date NOT NULL,
  `qr_code_payload` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_printed` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ktam_cards_cat_id_unique` (`cat_id`),
  UNIQUE KEY `ktam_cards_ktam_number_unique` (`ktam_number`),
  CONSTRAINT `ktam_cards_cat_id_foreign` FOREIGN KEY (`cat_id`) REFERENCES `cats` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `ktam_cards` */

insert  into `ktam_cards`(`id`,`cat_id`,`ktam_number`,`issue_date`,`qr_code_payload`,`is_printed`,`created_at`,`updated_at`) values 
(1,1,'KM-20260707-0001','2026-07-07','data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiPz4KPHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZlcnNpb249IjEuMSIgd2lkdGg9IjIwMCIgaGVpZ2h0PSIyMDAiIHZpZXdCb3g9IjAgMCAyMDAgMjAwIj48cmVjdCB4PSIwIiB5PSIwIiB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgZmlsbD0iI2ZmZmZmZiIvPjxnIHRyYW5zZm9ybT0ic2NhbGUoNi44OTcpIj48ZyB0cmFuc2Zvcm09InRyYW5zbGF0ZSgwLDApIj48cGF0aCBmaWxsLXJ1bGU9ImV2ZW5vZGQiIGQ9Ik04IDBMOCAxTDkgMUw5IDJMOCAyTDggNUw5IDVMOSA0TDExIDRMMTEgNUwxMCA1TDEwIDEwTDkgMTBMOSA4TDggOEw4IDExTDcgMTFMNyAxMEw2IDEwTDYgOUw3IDlMNyA4TDYgOEw2IDlMNCA5TDQgOEwwIDhMMCA5TDQgOUw0IDEwTDMgMTBMMyAxMUw0IDExTDQgMTJMNSAxMkw1IDE0TDYgMTRMNiAxNUw3IDE1TDcgMTZMNSAxNkw1IDE1TDQgMTVMNCAxNkw1IDE2TDUgMThMNCAxOEw0IDE3TDMgMTdMMyAxNEwyIDE0TDIgMTBMMSAxMEwxIDExTDAgMTFMMCAxNEwyIDE0TDIgMTZMMSAxNkwxIDE3TDIgMTdMMiAxOEwzIDE4TDMgMTlMNCAxOUw0IDIwTDUgMjBMNSAyMUw4IDIxTDggMjJMMTEgMjJMMTEgMjBMMTIgMjBMMTIgMTlMMTEgMTlMMTEgMjBMMTAgMjBMMTAgMTlMOSAxOUw5IDE4TDggMThMOCAyMEw2IDIwTDYgMTlMNyAxOUw3IDE4TDYgMThMNiAxN0w3IDE3TDcgMTZMOSAxNkw5IDE1TDExIDE1TDExIDE2TDEwIDE2TDEwIDE4TDExIDE4TDExIDE2TDEyIDE2TDEyIDE3TDEzIDE3TDEzIDE4TDE1IDE4TDE1IDE5TDE0IDE5TDE0IDIwTDE2IDIwTDE2IDE4TDE4IDE4TDE4IDE5TDE3IDE5TDE3IDIyTDE2IDIyTDE2IDIzTDE3IDIzTDE3IDIyTDE4IDIyTDE4IDI0TDE5IDI0TDE5IDIzTDIwIDIzTDIwIDI2TDIxIDI2TDIxIDI1TDIzIDI1TDIzIDI3TDIyIDI3TDIyIDI5TDIzIDI5TDIzIDI4TDI1IDI4TDI1IDI5TDI2IDI5TDI2IDI3TDI3IDI3TDI3IDI5TDI4IDI5TDI4IDI3TDI5IDI3TDI5IDIzTDI4IDIzTDI4IDIyTDI5IDIyTDI5IDIxTDI3IDIxTDI3IDE4TDI4IDE4TDI4IDE1TDI5IDE1TDI5IDEzTDI3IDEzTDI3IDEyTDI2IDEyTDI2IDExTDI4IDExTDI4IDEyTDI5IDEyTDI5IDExTDI4IDExTDI4IDEwTDI5IDEwTDI5IDhMMjggOEwyOCAxMEwyNiAxMEwyNiAxMUwyNSAxMUwyNSA5TDI3IDlMMjcgOEwyNCA4TDI0IDlMMjIgOUwyMiA4TDIxIDhMMjEgMTBMMjAgMTBMMjAgOUwxOSA5TDE5IDhMMjAgOEwyMCA3TDIxIDdMMjEgNUwyMCA1TDIwIDRMMjEgNEwyMSAwTDE4IDBMMTggMUwxNyAxTDE3IDJMMTYgMkwxNiAzTDE1IDNMMTUgMkwxNCAyTDE0IDFMMTUgMUwxNSAwTDE0IDBMMTQgMUwxMyAxTDEzIDBMMTIgMEwxMiAxTDExIDFMMTEgMEwxMCAwTDEwIDFMOSAxTDkgMFpNMTkgMUwxOSAzTDE4IDNMMTggMkwxNyAyTDE3IDNMMTggM0wxOCA0TDIwIDRMMjAgMVpNMTEgMkwxMSA0TDEyIDRMMTIgM0wxMyAzTDEzIDJaTTE0IDNMMTQgNEwxMyA0TDEzIDVMMTIgNUwxMiA2TDExIDZMMTEgOEwxMiA4TDEyIDZMMTMgNkwxMyAxMEwxNCAxMEwxNCAxMUwxNyAxMUwxNyAxMkwxOCAxMkwxOCAxNEwxOSAxNEwxOSAxM0wyMCAxM0wyMCAxNEwyMiAxNEwyMiAxNUwyMCAxNUwyMCAxNkwxOSAxNkwxOSAyMEwxOCAyMEwxOCAyMUwxOSAyMUwxOSAyMkwyMCAyMkwyMCAyMUwxOSAyMUwxOSAyMEwyMSAyMEwyMSAxOUwyMiAxOUwyMiAyMEwyMyAyMEwyMyAxNkwyNSAxNkwyNSAxN0wyNCAxN0wyNCAyMEwyNiAyMEwyNiAxOUwyNSAxOUwyNSAxOEwyNiAxOEwyNiAxN0wyNyAxN0wyNyAxNUwyNSAxNUwyNSAxNEwyNyAxNEwyNyAxM0wyNSAxM0wyNSAxMkwyMyAxMkwyMyAxMEwyMiAxMEwyMiAxMkwyMSAxMkwyMSAxM0wyMCAxM0wyMCAxMkwxOCAxMkwxOCAxMUwyMCAxMUwyMCAxMEwxOCAxMEwxOCAxMUwxNyAxMUwxNyA4TDE0IDhMMTQgNkwxNSA2TDE1IDdMMTYgN0wxNiA1TDE3IDVMMTcgNEwxNiA0TDE2IDVMMTUgNUwxNSAzWk0xMyA1TDEzIDZMMTQgNkwxNCA1Wk0xOCA1TDE4IDZMMTcgNkwxNyA3TDE4IDdMMTggOEwxOSA4TDE5IDdMMjAgN0wyMCA2TDE5IDZMMTkgNVpNOCA2TDggN0w5IDdMOSA2Wk0xOCA2TDE4IDdMMTkgN0wxOSA2Wk0xMSA5TDExIDEwTDEyIDEwTDEyIDlaTTE0IDlMMTQgMTBMMTUgMTBMMTUgOVpNNCAxMEw0IDExTDUgMTFMNSAxMkw3IDEyTDcgMTFMNiAxMUw2IDEwWk04IDExTDggMTRMOSAxNEw5IDExWk0xMSAxMUwxMSAxM0wxMCAxM0wxMCAxNEwxMSAxNEwxMSAxM0wxMiAxM0wxMiAxMkwxMyAxMkwxMyAxMVpNMTQgMTJMMTQgMTNMMTUgMTNMMTUgMTJaTTYgMTNMNiAxNEw3IDE0TDcgMTNaTTE2IDEzTDE2IDE0TDE1IDE0TDE1IDE1TDE2IDE1TDE2IDE2TDE1IDE2TDE1IDE3TDE2IDE3TDE2IDE2TDE3IDE2TDE3IDE3TDE4IDE3TDE4IDE1TDE2IDE1TDE2IDE0TDE3IDE0TDE3IDEzWk0yMiAxM0wyMiAxNEwyMyAxNEwyMyAxNUwyNCAxNUwyNCAxNEwyMyAxNEwyMyAxM1pNMTIgMTRMMTIgMTZMMTQgMTZMMTQgMTVMMTMgMTVMMTMgMTRaTTIxIDE2TDIxIDE3TDIwIDE3TDIwIDE5TDIxIDE5TDIxIDE4TDIyIDE4TDIyIDE2Wk0wIDE4TDAgMTlMMSAxOUwxIDE4Wk0xIDIwTDEgMjFMMyAyMUwzIDIwWk05IDIwTDkgMjFMMTAgMjFMMTAgMjBaTTE0IDIxTDE0IDIzTDEwIDIzTDEwIDI1TDExIDI1TDExIDI2TDEyIDI2TDEyIDI4TDkgMjhMOSAyNUw4IDI1TDggMjlMMTYgMjlMMTYgMjhMMTcgMjhMMTcgMjlMMjEgMjlMMjEgMjdMMTkgMjdMMTkgMjhMMTcgMjhMMTcgMjdMMTggMjdMMTggMjZMMTcgMjZMMTcgMjdMMTUgMjdMMTUgMjhMMTMgMjhMMTMgMjdMMTQgMjdMMTQgMjZMMTIgMjZMMTIgMjVMMTQgMjVMMTQgMjNMMTUgMjNMMTUgMjFaTTIxIDIxTDIxIDI0TDI0IDI0TDI0IDIxWk0yMiAyMkwyMiAyM0wyMyAyM0wyMyAyMlpNMjYgMjJMMjYgMjNMMjUgMjNMMjUgMjZMMjQgMjZMMjQgMjdMMjYgMjdMMjYgMjZMMjcgMjZMMjcgMjdMMjggMjdMMjggMjNMMjcgMjNMMjcgMjJaTTI2IDI0TDI2IDI1TDI3IDI1TDI3IDI0Wk0xNSAyNUwxNSAyNkwxNiAyNkwxNiAyNVpNMCAwTDAgN0w3IDdMNyAwWk0xIDFMMSA2TDYgNkw2IDFaTTIgMkwyIDVMNSA1TDUgMlpNMjIgMEwyMiA3TDI5IDdMMjkgMFpNMjMgMUwyMyA2TDI4IDZMMjggMVpNMjQgMkwyNCA1TDI3IDVMMjcgMlpNMCAyMkwwIDI5TDcgMjlMNyAyMlpNMSAyM0wxIDI4TDYgMjhMNiAyM1pNMiAyNEwyIDI3TDUgMjdMNSAyNFoiIGZpbGw9IiMwZjc2NmUiLz48L2c+PC9nPjwvc3ZnPgo=',0,'2026-07-07 14:34:16','2026-07-07 14:34:16');

/*Table structure for table `medical_records` */

DROP TABLE IF EXISTS `medical_records`;

CREATE TABLE `medical_records` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `appointment_id` bigint unsigned DEFAULT NULL,
  `cat_id` bigint unsigned NOT NULL,
  `vet_id` bigint unsigned NOT NULL,
  `weight` decimal(5,2) NOT NULL,
  `temperature` decimal(4,1) NOT NULL,
  `general_condition` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deworming_given` tinyint(1) NOT NULL DEFAULT '0',
  `anti_flea_given` tinyint(1) NOT NULL DEFAULT '0',
  `supplement_given` tinyint(1) NOT NULL DEFAULT '0',
  `treatment_notes` text COLLATE utf8mb4_unicode_ci,
  `recommendation` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `medical_records_appointment_id_foreign` (`appointment_id`),
  KEY `medical_records_cat_id_foreign` (`cat_id`),
  KEY `medical_records_vet_id_foreign` (`vet_id`),
  CONSTRAINT `medical_records_appointment_id_foreign` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `medical_records_cat_id_foreign` FOREIGN KEY (`cat_id`) REFERENCES `cats` (`id`) ON DELETE CASCADE,
  CONSTRAINT `medical_records_vet_id_foreign` FOREIGN KEY (`vet_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `medical_records` */

insert  into `medical_records`(`id`,`appointment_id`,`cat_id`,`vet_id`,`weight`,`temperature`,`general_condition`,`deworming_given`,`anti_flea_given`,`supplement_given`,`treatment_notes`,`recommendation`,`created_at`,`updated_at`) values 
(1,1,1,2,6.00,31.0,'Sehat',1,1,1,NULL,'Grooming 1 Bulan Sekali, Jangan Keluar Rumah','2026-07-07 14:34:14','2026-07-07 14:34:14');

/*Table structure for table `migrations` */

DROP TABLE IF EXISTS `migrations`;

CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `migrations` */

insert  into `migrations`(`id`,`migration`,`batch`) values 
(1,'0001_01_01_000000_create_users_table',1),
(2,'0001_01_01_000001_create_cache_table',1),
(3,'0001_01_01_000002_create_jobs_table',1),
(4,'2026_07_07_135128_create_cats_table',1),
(5,'2026_07_07_135149_create_appointments_table',1),
(6,'2026_07_07_141511_create_medical_records_table',1),
(7,'2026_07_07_141528_create_ktam_cards_table',1);

/*Table structure for table `password_reset_tokens` */

DROP TABLE IF EXISTS `password_reset_tokens`;

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `password_reset_tokens` */

/*Table structure for table `sessions` */

DROP TABLE IF EXISTS `sessions`;

CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `sessions` */

insert  into `sessions`(`id`,`user_id`,`ip_address`,`user_agent`,`payload`,`last_activity`) values 
('emG82c4Idgp0fLc7jGmkemZQwAwKhFuEBMRMxFdb',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJNWUFkb3J1MHM5UDJEbDlxOE1DOXAwQ3pmY2JTNjlQVkVsMTBGZUtIIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wva3VjaW5nbXUudGVzdCIsInJvdXRlIjpudWxsfX0=',1783434906);

/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` enum('admin','dokter','volunteer','member') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'member',
  `muhammadiyah_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `users` */

insert  into `users`(`id`,`name`,`email`,`email_verified_at`,`password`,`phone`,`role`,`muhammadiyah_id`,`remember_token`,`created_at`,`updated_at`) values 
(1,'Admin KucingMu','admin@kucingmu.com',NULL,'$2y$12$2XEmuJMApTsLOPYZSWJ.l.kvaEotFmWOJ22hIOYs.q5jLoreDi2Te','081234567890','admin','NBM-ADMIN-01','chiVpJ3Y6c9DwsZjdd4HZJEuPWBCJBdJ7Cvgfy5MzLguvM2BrXYgutuN6rMJ','2026-07-07 14:17:10','2026-07-07 14:17:10'),
(2,'Drh. Ahmad','dokter@kucingmu.com',NULL,'$2y$12$PH/mUzD2V3/x4qASLlY8dOGB.6HJOXuHGp5eAIE6BrLuKTRceNSgS','081234567891','dokter','NBM-DOKTER-01',NULL,'2026-07-07 14:17:10','2026-07-07 14:17:10'),
(3,'Relawan Budi','relawan@kucingmu.com',NULL,'$2y$12$qlWHDlkOM2/tr9hwOJBsoeSBLe0/zvFy8gTbd0Vs9NR2vQHOIuKR6','081234567892','volunteer','NBM-RELAWAN-01',NULL,'2026-07-07 14:17:10','2026-07-07 14:17:10'),
(4,'Siti Pemilik Kucing','member@kucingmu.com',NULL,'$2y$12$SVrZBsxHj1dHivYSYLyNrOjg9tOPIjXP2MD.Va6qszTCBq/CVZCey','081234567893','member','NBM-MEMBER-01',NULL,'2026-07-07 14:17:11','2026-07-07 14:17:11'),
(5,'Denis Setiaji','denissetiaji.dst@gmail.com',NULL,'$2y$12$EfCisV4F177MiqB.vq91tOtr794NcMc0/zp52N17bRZro0fENts8K','089669651907','member',NULL,NULL,'2026-07-07 14:30:39','2026-07-07 14:30:39');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
