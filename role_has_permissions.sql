-- Adminer 5.3.0 MySQL 9.4.0 dump

SET NAMES utf8;

SET time_zone = '+00:00';

SET foreign_key_checks = 0;

SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `employee_atro`;

CREATE TABLE `employee_atro` (
    `id` bigint unsigned NOT NULL AUTO_INCREMENT,
    `employee_no` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `date` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `start_time` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `end_time` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `justification` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `disapproval_note` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    `status` enum(
        'approved',
        'disapproved',
        'pending',
        'cancelled'
    ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
    `remarks` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    `action_by_id` bigint unsigned DEFAULT NULL,
    `isDeleted` tinyint(1) NOT NULL DEFAULT '0',
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `employee_atro_action_by_id_foreign` (`action_by_id`),
    CONSTRAINT `employee_atro_action_by_id_foreign` FOREIGN KEY (`action_by_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

INSERT INTO
    `employee_atro` (
        `id`,
        `employee_no`,
        `date`,
        `start_time`,
        `end_time`,
        `justification`,
        `disapproval_note`,
        `status`,
        `remarks`,
        `action_by_id`,
        `isDeleted`,
        `created_at`,
        `updated_at`
    )
VALUES (
        4,
        'EMP-TEST-01',
        '2025-12-02',
        '05:00 PM',
        '07:00 PM',
        'sample',
        '',
        'approved',
        NULL,
        1,
        0,
        '2025-12-01 23:59:25',
        '2025-12-03 08:05:48'
    ),
    (
        5,
        'EMP-TEST-01',
        '2025-12-08',
        '05:00 PM',
        '08:00 PM',
        'try',
        '',
        'approved',
        NULL,
        2,
        0,
        '2025-12-08 10:41:07',
        '2025-12-08 10:42:19'
    ),
    (
        6,
        'EMP-TEST-01',
        '2025-12-09',
        '05:00 PM',
        '07:00 PM',
        'OT',
        '',
        'approved',
        NULL,
        1,
        0,
        '2025-12-10 03:54:00',
        '2025-12-11 05:03:51'
    ),
    (
        7,
        'EMP-TEST-01',
        '2025-12-05',
        '05:00 PM',
        '08:00 PM',
        'testibg',
        'checking',
        'disapproved',
        NULL,
        1,
        0,
        '2025-12-11 05:58:51',
        '2025-12-11 07:21:57'
    ),
    (
        8,
        'EMP-TEST-01',
        '2025-12-03',
        '05:00 PM',
        '08:00 PM',
        'try',
        'try',
        'disapproved',
        NULL,
        1,
        0,
        '2025-12-11 06:02:47',
        '2025-12-11 06:46:30'
    ),
    (
        9,
        'EMP-TEST-01',
        '2025-11-05',
        '04:00 PM',
        '08:00 PM',
        'ot ko po tan',
        'disapproved',
        'disapproved',
        NULL,
        1,
        0,
        '2025-12-11 07:02:12',
        '2025-12-11 07:02:49'
    ),
    (
        10,
        'EMP-TEST-01',
        '2025-09-01',
        '05:00 PM',
        '07:00 PM',
        'heys',
        NULL,
        'disapproved',
        NULL,
        1,
        0,
        '2025-12-11 09:00:03',
        '2025-12-15 00:34:54'
    ),
    (
        11,
        'EMP-TEST-01',
        '2025-10-27',
        '05:00 PM',
        '07:00 PM',
        'try again',
        NULL,
        'cancelled',
        NULL,
        NULL,
        1,
        '2025-12-14 02:19:14',
        '2025-12-15 00:34:05'
    ),
    (
        12,
        'EMP-TEST-01',
        '2025-11-13',
        '05:00 PM',
        '09:00 PM',
        'for testings',
        NULL,
        'approved',
        NULL,
        1,
        0,
        '2025-12-14 23:45:14',
        '2025-12-15 00:09:14'
    );

-- 2025-12-15 06:28:38 UTC