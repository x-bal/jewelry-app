

CREATE TABLE `devices` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nama_device` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` int(11) NOT NULL DEFAULT '1',
  `is_sync` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


INSERT INTO devices (id, nama_device, status, is_sync, created_at, updated_at) VALUES ('1','Device 1','1','0','2023-03-26 19:57:49','2023-03-26 19:57:49');

INSERT INTO devices (id, nama_device, status, is_sync, created_at, updated_at) VALUES ('2','Device 2','1','0','2023-03-26 19:57:49','2023-03-26 19:57:49');


CREATE TABLE `dummy_barangs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tipe_barang_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `locator_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rfid` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nama_barang` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kode_barang` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `berat` double(8,2) NOT NULL,
  `satuan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Gram',
  `harga` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE `locators` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nama_locator` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_sync` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


INSERT INTO locators (id, nama_locator, is_sync, created_at, updated_at) VALUES ('1','Locator 1','0','2023-03-26 19:57:49','2023-03-26 19:57:49');

INSERT INTO locators (id, nama_locator, is_sync, created_at, updated_at) VALUES ('2','Locator 2','0','2023-03-26 19:57:49','2023-03-26 19:57:49');


CREATE TABLE `lost_stoks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `locator_id` bigint(20) unsigned NOT NULL,
  `tanggal` date NOT NULL,
  `is_sync` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lost_stoks_locator_id_foreign` (`locator_id`),
  CONSTRAINT `lost_stoks_locator_id_foreign` FOREIGN KEY (`locator_id`) REFERENCES `locators` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


INSERT INTO migrations (id, migration, batch) VALUES ('1','2014_10_12_000000_create_users_table','1');

INSERT INTO migrations (id, migration, batch) VALUES ('2','2014_10_12_100000_create_password_resets_table','1');

INSERT INTO migrations (id, migration, batch) VALUES ('3','2019_08_19_000000_create_failed_jobs_table','1');

INSERT INTO migrations (id, migration, batch) VALUES ('4','2019_12_14_000001_create_personal_access_tokens_table','1');

INSERT INTO migrations (id, migration, batch) VALUES ('5','2023_02_24_031423_create_tipe_barangs_table','1');

INSERT INTO migrations (id, migration, batch) VALUES ('6','2023_02_24_031435_create_locators_table','1');

INSERT INTO migrations (id, migration, batch) VALUES ('7','2023_02_24_031529_create_sub_tipe_barangs_table','1');

INSERT INTO migrations (id, migration, batch) VALUES ('8','2023_02_24_031530_create_barangs_table','1');

INSERT INTO migrations (id, migration, batch) VALUES ('9','2023_02_27_023000_create_stok_opnames_table','1');

INSERT INTO migrations (id, migration, batch) VALUES ('10','2023_02_27_023129_create_detail_stok_opnames_table','1');

INSERT INTO migrations (id, migration, batch) VALUES ('11','2023_02_28_012137_add_kode_barang_to_barangs_table','1');

INSERT INTO migrations (id, migration, batch) VALUES ('12','2023_02_28_013302_create_lost_stoks_table','1');

INSERT INTO migrations (id, migration, batch) VALUES ('13','2023_02_28_013434_create_detail_lost_stoks_table','1');

INSERT INTO migrations (id, migration, batch) VALUES ('14','2023_02_28_081200_create_penjualans_table','1');

INSERT INTO migrations (id, migration, batch) VALUES ('15','2023_03_01_092535_create_detail_penjualans_table','1');

INSERT INTO migrations (id, migration, batch) VALUES ('16','2023_03_02_014323_create_devices_table','1');

INSERT INTO migrations (id, migration, batch) VALUES ('17','2023_03_02_015959_create_device_user_table','1');

INSERT INTO migrations (id, migration, batch) VALUES ('18','2023_03_03_013845_create_penarikans_table','1');

INSERT INTO migrations (id, migration, batch) VALUES ('19','2023_03_03_014024_create_barang_penarikan_table','1');

INSERT INTO migrations (id, migration, batch) VALUES ('20','2023_03_07_092543_create_permission_tables','1');

INSERT INTO migrations (id, migration, batch) VALUES ('21','2023_03_08_092103_add_foto_to_barangs_table','1');

INSERT INTO migrations (id, migration, batch) VALUES ('22','2023_03_08_101949_create_settings_table','1');

INSERT INTO migrations (id, migration, batch) VALUES ('23','2023_03_09_151707_add_kode_tipe_to_tipe_barangs_table','1');

INSERT INTO migrations (id, migration, batch) VALUES ('24','2023_03_15_095721_create_dummy_barangs_table','1');

INSERT INTO migrations (id, migration, batch) VALUES ('25','2023_03_27_093217_add_status_to_penarikans_table','2');


CREATE TABLE `penarikans` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `locator_id` bigint(20) unsigned NOT NULL,
  `tanggal` date NOT NULL,
  `is_sync` int(11) NOT NULL DEFAULT '0',
  `status` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `penarikans_locator_id_foreign` (`locator_id`),
  CONSTRAINT `penarikans_locator_id_foreign` FOREIGN KEY (`locator_id`) REFERENCES `locators` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


INSERT INTO penarikans (id, locator_id, tanggal, is_sync, status, created_at, updated_at) VALUES ('1','1','2023-03-27','0','0','2023-03-27 09:31:34','2023-03-27 09:31:34');


CREATE TABLE `permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


INSERT INTO permissions (id, name, guard_name, created_at, updated_at) VALUES ('1','master-access','web','2023-03-26 19:57:49','2023-03-26 19:57:49');

INSERT INTO permissions (id, name, guard_name, created_at, updated_at) VALUES ('2','master-device-access','web','2023-03-26 19:57:49','2023-03-26 19:57:49');

INSERT INTO permissions (id, name, guard_name, created_at, updated_at) VALUES ('3','inventory-access','web','2023-03-26 19:57:49','2023-03-26 19:57:49');

INSERT INTO permissions (id, name, guard_name, created_at, updated_at) VALUES ('4','penjualan-access','web','2023-03-26 19:57:49','2023-03-26 19:57:49');

INSERT INTO permissions (id, name, guard_name, created_at, updated_at) VALUES ('5','management-access','web','2023-03-26 19:57:49','2023-03-26 19:57:49');

INSERT INTO permissions (id, name, guard_name, created_at, updated_at) VALUES ('6','report-access','web','2023-03-26 19:57:49','2023-03-26 19:57:49');

INSERT INTO permissions (id, name, guard_name, created_at, updated_at) VALUES ('7','setting-access','web','2023-03-26 19:57:49','2023-03-26 19:57:49');


CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE `roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


INSERT INTO roles (id, name, guard_name, created_at, updated_at) VALUES ('1','Admin','web','2023-03-26 19:57:49','2023-03-26 19:57:49');


CREATE TABLE `settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `val` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_sync` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


INSERT INTO settings (id, name, val, is_sync, created_at, updated_at) VALUES ('1','title','Jewelry','0','2023-03-26 19:57:49','2023-03-26 19:57:49');

INSERT INTO settings (id, name, val, is_sync, created_at, updated_at) VALUES ('2','tagline','Toko Perhiasan Hade Putra Ciwidey','0','2023-03-26 19:57:49','2023-03-26 19:57:49');

INSERT INTO settings (id, name, val, is_sync, created_at, updated_at) VALUES ('3','url','https://hadetigasaudara-ciwidey.com','0','2023-03-26 19:57:49','2023-03-27 14:25:52');

INSERT INTO settings (id, name, val, is_sync, created_at, updated_at) VALUES ('4','bg','background/login-bg.jpg','0','2023-03-27 14:09:17','2023-03-27 14:10:18');


CREATE TABLE `stok_opnames` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `locator_id` bigint(20) unsigned NOT NULL,
  `tanggal` date NOT NULL,
  `status` int(11) NOT NULL DEFAULT '0',
  `is_sync` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `stok_opnames_locator_id_foreign` (`locator_id`),
  CONSTRAINT `stok_opnames_locator_id_foreign` FOREIGN KEY (`locator_id`) REFERENCES `locators` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE `tipe_barangs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nama_tipe` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kode` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_sync` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tipe_barangs_kode_unique` (`kode`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


INSERT INTO tipe_barangs (id, nama_tipe, kode, is_sync, created_at, updated_at) VALUES ('1','Cincin','CC','0','2023-03-26 19:57:49','2023-03-26 19:57:49');

INSERT INTO tipe_barangs (id, nama_tipe, kode, is_sync, created_at, updated_at) VALUES ('2','Gelang','FF','0','2023-03-26 19:57:49','2023-03-26 19:57:49');


CREATE TABLE `sub_tipe_barangs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tipe_barang_id` bigint(20) unsigned NOT NULL,
  `kode` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama` varchar(35) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sub_tipe_barangs_tipe_barang_id_foreign` (`tipe_barang_id`),
  CONSTRAINT `sub_tipe_barangs_tipe_barang_id_foreign` FOREIGN KEY (`tipe_barang_id`) REFERENCES `tipe_barangs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


INSERT INTO sub_tipe_barangs (id, tipe_barang_id, kode, nama, created_at, updated_at) VALUES ('1','1','CA','Cincin Anak','2023-03-27 11:34:59','2023-03-27 00:00:00');


CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `foto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_sync` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_username_unique` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


INSERT INTO users (id, name, username, password, foto, is_sync, created_at, updated_at) VALUES ('1','Developer','developer','$2y$10$yGpq82dT5wyaIWAoMT3PFO.jkMYE2aj7VlQmWpiEC1ZEQGEJn.49q','','0','2023-03-26 19:57:49','2023-03-27 14:25:36');


CREATE TABLE `penjualans` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `tanggal` date NOT NULL,
  `invoice` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('Input','Batal','Selesai') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Input',
  `is_sync` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `penjualans_user_id_foreign` (`user_id`),
  CONSTRAINT `penjualans_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE `barangs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sub_tipe_barang_id` bigint(20) unsigned DEFAULT NULL,
  `locator_id` bigint(20) unsigned NOT NULL,
  `rfid` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kode_barang` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nama_barang` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `berat` double(8,2) NOT NULL,
  `satuan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Gram',
  `harga` int(11) NOT NULL,
  `status` enum('Tersedia','Terjual','Loss','Ditarik') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Tersedia',
  `old_rfid` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `foto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_sync` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `barangs_sub_tipe_barang_id_foreign` (`sub_tipe_barang_id`),
  KEY `barangs_locator_id_foreign` (`locator_id`),
  CONSTRAINT `barangs_locator_id_foreign` FOREIGN KEY (`locator_id`) REFERENCES `locators` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `barangs_sub_tipe_barang_id_foreign` FOREIGN KEY (`sub_tipe_barang_id`) REFERENCES `sub_tipe_barangs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


INSERT INTO barangs (id, sub_tipe_barang_id, locator_id, rfid, kode_barang, nama_barang, berat, satuan, harga, status, old_rfid, foto, is_sync, created_at, updated_at) VALUES ('1','1','1','','CA','Cincin 5','1.2','Gram','1000000','Ditarik','CN1002','barang/260323-CB.jpg','0','2023-03-26 20:01:53','2023-03-27 09:49:56');

INSERT INTO barangs (id, sub_tipe_barang_id, locator_id, rfid, kode_barang, nama_barang, berat, satuan, harga, status, old_rfid, foto, is_sync, created_at, updated_at) VALUES ('2','1','1','','CA','Cincin New','1.2','Gram','1000000','Ditarik','CN1001','barang/270323-.png','0','2023-03-27 09:01:39','2023-03-27 09:49:56');

INSERT INTO barangs (id, sub_tipe_barang_id, locator_id, rfid, kode_barang, nama_barang, berat, satuan, harga, status, old_rfid, foto, is_sync, created_at, updated_at) VALUES ('3','1','1','CN1001','CA','Cincin New','1.2','Gram','1000000','Tersedia','','barang/270323-.png','0','2023-03-27 14:23:23','2023-03-27 14:23:23');


CREATE TABLE `barang_lost_stok` (
  `lost_stok_id` bigint(20) unsigned NOT NULL,
  `barang_id` bigint(20) unsigned NOT NULL,
  `ket` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_sync` int(11) NOT NULL DEFAULT '0',
  KEY `barang_lost_stok_lost_stok_id_foreign` (`lost_stok_id`),
  KEY `barang_lost_stok_barang_id_foreign` (`barang_id`),
  CONSTRAINT `barang_lost_stok_barang_id_foreign` FOREIGN KEY (`barang_id`) REFERENCES `barangs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `barang_lost_stok_lost_stok_id_foreign` FOREIGN KEY (`lost_stok_id`) REFERENCES `lost_stoks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE `barang_penarikan` (
  `barang_id` bigint(20) unsigned NOT NULL,
  `penarikan_id` bigint(20) unsigned NOT NULL,
  `ket` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_sync` int(11) NOT NULL DEFAULT '0',
  KEY `barang_penarikan_barang_id_foreign` (`barang_id`),
  KEY `barang_penarikan_penarikan_id_foreign` (`penarikan_id`),
  CONSTRAINT `barang_penarikan_barang_id_foreign` FOREIGN KEY (`barang_id`) REFERENCES `barangs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `barang_penarikan_penarikan_id_foreign` FOREIGN KEY (`penarikan_id`) REFERENCES `penarikans` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


INSERT INTO barang_penarikan (barang_id, penarikan_id, ket, is_sync) VALUES ('2','1','Barang Lama','0');

INSERT INTO barang_penarikan (barang_id, penarikan_id, ket, is_sync) VALUES ('1','1','Barang Lama','0');


CREATE TABLE `barang_penjualan` (
  `penjualan_id` bigint(20) unsigned NOT NULL,
  `barang_id` bigint(20) unsigned NOT NULL,
  `is_sync` int(11) NOT NULL DEFAULT '0',
  KEY `barang_penjualan_penjualan_id_foreign` (`penjualan_id`),
  KEY `barang_penjualan_barang_id_foreign` (`barang_id`),
  CONSTRAINT `barang_penjualan_barang_id_foreign` FOREIGN KEY (`barang_id`) REFERENCES `barangs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `barang_penjualan_penjualan_id_foreign` FOREIGN KEY (`penjualan_id`) REFERENCES `penjualans` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE `barang_stok_opname` (
  `stok_opname_id` bigint(20) unsigned NOT NULL,
  `barang_id` bigint(20) unsigned NOT NULL,
  `is_sync` int(11) NOT NULL DEFAULT '0',
  KEY `barang_stok_opname_stok_opname_id_foreign` (`stok_opname_id`),
  KEY `barang_stok_opname_barang_id_foreign` (`barang_id`),
  CONSTRAINT `barang_stok_opname_barang_id_foreign` FOREIGN KEY (`barang_id`) REFERENCES `barangs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `barang_stok_opname_stok_opname_id_foreign` FOREIGN KEY (`stok_opname_id`) REFERENCES `stok_opnames` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE `device_user` (
  `device_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `is_sync` int(11) NOT NULL DEFAULT '0',
  KEY `device_user_device_id_foreign` (`device_id`),
  KEY `device_user_user_id_foreign` (`user_id`),
  CONSTRAINT `device_user_device_id_foreign` FOREIGN KEY (`device_id`) REFERENCES `devices` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `device_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


INSERT INTO model_has_roles (role_id, model_type, model_id) VALUES ('1','App\Models\User','1');


CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `role_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


INSERT INTO role_has_permissions (permission_id, role_id) VALUES ('1','1');

INSERT INTO role_has_permissions (permission_id, role_id) VALUES ('2','1');

INSERT INTO role_has_permissions (permission_id, role_id) VALUES ('3','1');

INSERT INTO role_has_permissions (permission_id, role_id) VALUES ('4','1');

INSERT INTO role_has_permissions (permission_id, role_id) VALUES ('5','1');

INSERT INTO role_has_permissions (permission_id, role_id) VALUES ('6','1');

INSERT INTO role_has_permissions (permission_id, role_id) VALUES ('7','1');
