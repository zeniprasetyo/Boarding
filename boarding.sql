-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Waktu pembuatan: 11 Feb 2026 pada 01.18
-- Versi server: 8.0.30
-- Versi PHP: 8.2.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `boarding`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `absen`
--

CREATE TABLE `absen` (
  `id` int NOT NULL,
  `kode` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `santri_id` int NOT NULL,
  `tanggal` date NOT NULL,
  `pagi` varchar(1) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `malam` varchar(1) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keterangan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `absen`
--

INSERT INTO `absen` (`id`, `kode`, `santri_id`, `tanggal`, `pagi`, `malam`, `keterangan`, `created_at`, `deleted_at`, `updated_at`) VALUES
(1, '3', 0, '2025-12-05', '1', '0', 'hadir', '2025-12-05 03:38:48', NULL, '2025-12-05 03:43:08'),
(2, '9', 0, '2025-12-05', '1', '1', 'hadir', '2025-12-05 03:38:48', NULL, '2025-12-05 03:38:48'),
(3, '8', 0, '2026-01-21', '1', '1', NULL, '2026-01-21 04:48:39', NULL, '2026-01-21 04:48:39'),
(4, '10', 0, '2026-01-21', '1', '1', NULL, '2026-01-21 04:48:39', NULL, '2026-01-21 04:48:39'),
(5, 'ABS-2026-01-28-8-38g2', 8, '2026-01-28', 'H', 'H', NULL, '2026-01-28 04:23:05', NULL, '2026-01-28 04:23:05'),
(6, 'ABS-2026-01-28-10-ghCb', 10, '2026-01-28', 'H', 'H', NULL, '2026-01-28 04:23:05', NULL, '2026-01-28 04:23:05'),
(7, 'ABS-2026-02-04-8-PRcd', 8, '2026-02-04', 'H', 'H', NULL, '2026-02-04 04:04:36', NULL, '2026-02-04 04:04:36'),
(8, 'ABS-2026-02-04-10-n9bC', 10, '2026-02-04', 'H', 'H', NULL, '2026-02-04 04:04:36', NULL, '2026-02-04 04:04:36'),
(9, 'ABS-2026-02-04-3-tv77', 3, '2026-02-04', 'H', 'H', NULL, '2026-02-04 04:06:34', NULL, '2026-02-04 04:06:34'),
(10, 'ABS-2026-02-04-9-HGAH', 9, '2026-02-04', 'H', 'H', NULL, '2026-02-04 04:06:34', NULL, '2026-02-04 04:06:34');

-- --------------------------------------------------------

--
-- Struktur dari tabel `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `ceklist_kegiatan`
--

CREATE TABLE `ceklist_kegiatan` (
  `id` int NOT NULL,
  `kode` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal` date NOT NULL,
  `santri_id` int NOT NULL,
  `kegiatan_id` int NOT NULL,
  `status` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `ceklist_kegiatan`
--

INSERT INTO `ceklist_kegiatan` (`id`, `kode`, `tanggal`, `santri_id`, `kegiatan_id`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, '59a75954-446b-4e14-9b9f-2ab447eeed0a', '2025-11-05', 3, 10, 1, '2025-11-05 00:06:18', '2025-11-05 00:06:19', NULL),
(2, '54d480a3-3dd0-42d2-82bb-ebd1f51ebc26', '2025-11-05', 3, 11, 1, '2025-11-05 00:06:18', '2025-11-05 00:06:19', NULL),
(3, '1ed842f3-2bd8-4b08-b056-42683f88c35c', '2025-11-05', 3, 9, 1, '2025-11-05 00:06:18', '2025-11-05 00:06:19', NULL),
(4, '1507d0fc-a6f2-49f2-88c0-4f29495f44d0', '2025-11-05', 8, 10, 1, '2025-11-05 00:06:18', '2025-11-05 00:06:19', NULL),
(5, 'd9148813-3d60-4c5f-a4cb-7dbb3b8a9080', '2025-11-05', 8, 11, 1, '2025-11-05 00:06:18', '2025-11-05 00:06:19', NULL),
(6, 'fcd40728-f0bb-498a-b10e-6341b3cf12a3', '2025-11-05', 8, 9, 1, '2025-11-05 00:06:18', '2025-11-05 00:06:19', NULL),
(7, 'a52c0091-1703-4299-b439-d9ff37f08bc4', '2025-11-05', 3, 13, 1, '2025-11-05 00:10:18', '2025-11-05 00:10:18', NULL),
(8, 'c36ada5c-b5c7-4669-a80d-b87ff834cad0', '2025-11-05', 8, 8, 1, '2025-11-05 00:10:18', '2025-11-05 00:10:18', NULL),
(9, 'c61af856-3b11-49b6-9017-9e03c154a86e', '2025-11-05', 3, 16, 1, '2025-11-05 00:12:11', '2025-11-05 00:13:07', NULL),
(10, '55037ea5-87a7-4b88-8dfc-0c43f7d17106', '2025-11-05', 8, 16, 1, '2025-11-05 00:13:07', '2025-11-05 00:13:07', NULL),
(11, 'b0d38fdf-94c5-40a1-ab0d-d793e1a0d350', '2025-11-25', 8, 12, 1, '2025-11-25 16:59:11', '2025-11-25 16:59:11', NULL),
(12, '34f5a6fd-4878-48b2-9efa-f0c82d4b88bb', '2025-11-25', 8, 4, 1, '2025-11-25 16:59:11', '2025-11-25 16:59:11', NULL),
(13, '14fe2f80-a217-443e-a820-7662d5a59edd', '2025-11-25', 8, 5, 1, '2025-11-25 16:59:11', '2025-11-25 16:59:11', NULL),
(14, 'bd6957d7-9df3-41e3-9f6d-930b01489b00', '2025-11-26', 3, 12, 1, '2025-11-25 20:39:49', '2025-11-25 20:39:49', NULL),
(15, 'a0bcf3ea-6f8e-45dc-88d8-36110d345871', '2025-11-26', 3, 14, 1, '2025-11-25 20:39:49', '2025-11-25 20:39:49', NULL),
(16, '4d8e4b2b-5d9d-44eb-82ff-07e64f444bd7', '2025-11-26', 3, 6, 1, '2025-11-25 20:39:49', '2025-11-25 20:39:49', NULL),
(17, '03527218-e8bb-4912-93fd-9ed72309234f', '2025-11-26', 3, 4, 1, '2025-11-25 20:39:49', '2025-11-25 20:39:49', NULL),
(18, 'f4810da1-a9b3-426c-9306-c228bbb8e6f6', '2025-12-05', 3, 12, 1, '2025-12-04 18:58:03', '2025-12-04 18:58:03', NULL),
(19, 'cf3551d6-edae-485d-b26a-f86f1af600d7', '2025-12-05', 3, 6, 1, '2025-12-04 18:58:03', '2025-12-04 18:58:03', NULL),
(20, '03ce5a06-376a-4cf4-a0b4-5916392e7db8', '2025-12-05', 3, 4, 1, '2025-12-04 18:58:03', '2025-12-04 18:58:03', NULL),
(21, '5746df21-d810-41ad-809a-e710187cbde7', '2025-12-05', 9, 14, 1, '2025-12-04 18:58:03', '2025-12-04 18:58:03', NULL),
(22, '814a45b5-e09f-4a46-bb73-6740d4b58898', '2025-12-05', 9, 6, 1, '2025-12-04 18:58:03', '2025-12-04 18:58:03', NULL),
(23, '085448d0-51ab-465e-a041-b04ec1ee62b2', '2025-12-05', 9, 4, 1, '2025-12-04 18:58:03', '2025-12-04 18:58:03', NULL),
(24, 'fd4c34a2-0652-4527-b99d-f7d4c3ccd2f3', '2025-12-05', 9, 5, 1, '2025-12-04 18:58:03', '2025-12-04 18:58:03', NULL),
(25, '37e8f883-aa7c-43c2-8f12-004082e273fa', '2025-12-03', 3, 12, 1, '2025-12-04 18:58:40', '2025-12-04 18:58:40', NULL),
(26, '804261b7-c6ec-4757-9f18-802153612ca9', '2025-12-03', 3, 14, 1, '2025-12-04 18:58:40', '2025-12-04 18:58:40', NULL),
(27, 'b4281a34-5e35-40c2-a123-ac8c22d456a7', '2025-12-03', 3, 4, 1, '2025-12-04 18:58:40', '2025-12-04 18:58:40', NULL),
(28, '0ba9d18f-9ab3-4671-80b8-ca2fa57de6f7', '2025-12-03', 3, 5, 1, '2025-12-04 18:58:40', '2025-12-04 18:58:40', NULL),
(29, '5b8a5db0-6e5c-423a-9e28-60f45131aa4a', '2025-12-03', 9, 12, 1, '2025-12-04 18:58:40', '2025-12-04 18:58:40', NULL),
(30, '3c36368e-4b4e-4523-a317-072fea85cdcb', '2025-12-03', 9, 14, 1, '2025-12-04 18:58:40', '2025-12-04 18:58:40', NULL),
(31, 'f6205284-371d-4e5c-a29a-0d7e1c593f3b', '2025-12-03', 9, 6, 1, '2025-12-04 18:58:40', '2025-12-04 18:58:40', NULL),
(32, 'a700780f-b4a9-424d-85ac-3934a118075a', '2025-12-03', 9, 4, 1, '2025-12-04 18:58:40', '2025-12-04 18:58:40', NULL),
(33, '98b2dc33-5d36-4018-a3a8-c5e7ba32cc31', '2025-12-03', 9, 5, 1, '2025-12-04 18:58:40', '2025-12-04 18:58:40', NULL),
(34, '9956ec6a-c524-4ce3-a854-37dfe5cfd083', '2025-12-04', 3, 12, 1, '2025-12-04 18:59:11', '2025-12-04 18:59:11', NULL),
(35, 'e6954c8e-c994-49b3-9e1a-6fc7d57bd8b4', '2025-12-04', 3, 14, 1, '2025-12-04 18:59:11', '2025-12-04 18:59:11', NULL),
(36, 'f9970113-c3a2-4825-8a7a-6a8d00601ca4', '2025-12-04', 3, 6, 1, '2025-12-04 18:59:11', '2025-12-04 18:59:11', NULL),
(37, '777a7ca4-76ec-41f1-9999-21f5032d3025', '2025-12-04', 3, 4, 1, '2025-12-04 18:59:11', '2025-12-04 18:59:11', NULL),
(38, '44fc96e5-593e-4474-8f3a-419ae60ed8b9', '2025-12-04', 3, 5, 1, '2025-12-04 18:59:11', '2025-12-04 18:59:11', NULL),
(39, '3505904b-5cc3-4689-b76a-9e334e713ddc', '2025-12-04', 9, 12, 1, '2025-12-04 18:59:11', '2025-12-04 18:59:11', NULL),
(40, 'c18dda43-f41e-4a55-8ad1-b336ae5c3c72', '2025-12-04', 9, 14, 1, '2025-12-04 18:59:11', '2025-12-04 18:59:11', NULL),
(41, 'ada39b0d-f37a-43cf-bcdc-283ddf60f9df', '2025-12-04', 9, 6, 1, '2025-12-04 18:59:11', '2025-12-04 18:59:11', NULL),
(42, '0b4c0149-2b0f-47bc-b2e2-f2da5940ec3c', '2025-12-04', 9, 4, 1, '2025-12-04 18:59:11', '2025-12-04 18:59:11', NULL),
(43, 'd4c2b8ef-ac90-4c06-88ae-d916d2eb86ba', '2025-12-04', 9, 5, 1, '2025-12-04 18:59:11', '2025-12-04 18:59:11', NULL),
(44, 'b0d48fe8-a685-4acf-8782-11eb1b4109c2', '2026-01-14', 8, 10, 1, '2026-01-13 19:16:55', '2026-01-13 19:16:55', NULL),
(45, '64e73f51-5dc8-4617-95ec-d06a33efab23', '2026-01-14', 8, 11, 1, '2026-01-13 19:16:55', '2026-01-13 19:16:55', NULL),
(46, '9cf60425-1aa9-41bb-a9e0-36c5e4b4611d', '2026-01-14', 8, 9, 1, '2026-01-13 19:16:55', '2026-01-13 19:16:55', NULL),
(47, 'cf06522d-4e44-4d0b-9924-f11881a38db3', '2026-01-14', 3, 12, 1, '2026-01-13 19:17:18', '2026-01-13 19:17:50', NULL),
(48, '77f71bfe-38a6-45cd-921d-6188a5c82871', '2026-01-14', 3, 14, 1, '2026-01-13 19:17:18', '2026-01-13 19:17:50', NULL),
(49, '7c073c76-6160-41c9-8235-10f5dfa7a47c', '2026-01-14', 3, 6, 1, '2026-01-13 19:17:18', '2026-01-13 19:17:50', NULL),
(50, '380f60ca-4e01-4646-ae38-e39c71e435f8', '2026-01-14', 3, 4, 1, '2026-01-13 19:17:18', '2026-01-13 19:17:50', NULL),
(51, '3ce8b556-6fca-4ae6-bd5a-f75aac964a4c', '2026-01-14', 3, 5, 1, '2026-01-13 19:17:18', '2026-01-13 19:17:50', NULL),
(52, '3181b8ec-00e5-4237-8a7a-b9ea697dc87f', '2026-01-14', 9, 12, 1, '2026-01-13 19:17:18', '2026-01-13 19:17:50', NULL),
(53, 'ea62a50b-87bb-452e-b2cb-3a0ab2f4025b', '2026-01-14', 9, 14, 1, '2026-01-13 19:17:18', '2026-01-13 19:17:50', NULL),
(54, '9a945226-fa75-49e2-81fd-c4c9cf8da738', '2026-01-14', 9, 6, 1, '2026-01-13 19:17:18', '2026-01-13 19:17:50', NULL),
(55, '8b466de4-fab2-4f21-b5d4-6a638ad51a11', '2026-01-14', 9, 4, 1, '2026-01-13 19:17:18', '2026-01-13 19:17:50', NULL),
(56, '1d3cac98-6476-4363-b45d-496eb96b8bf9', '2026-01-14', 9, 5, 1, '2026-01-13 19:17:18', '2026-01-13 19:17:50', NULL),
(57, '6e96c45e-ddc0-4187-8e68-2f3ee84da815', '2026-01-14', 8, 12, 1, '2026-01-13 19:17:31', '2026-01-13 19:17:31', NULL),
(58, '72b06dbf-e0ab-4d0f-9a6d-10a3f592c89c', '2026-01-14', 8, 14, 1, '2026-01-13 19:17:31', '2026-01-13 19:17:31', NULL),
(59, '357c5b02-d5da-4212-837b-bc6b0fcab291', '2026-01-14', 8, 6, 1, '2026-01-13 19:17:31', '2026-01-13 19:17:31', NULL),
(60, 'b3ad492f-d803-4091-bed5-9c6f888263fc', '2026-01-14', 8, 4, 1, '2026-01-13 19:17:31', '2026-01-13 19:17:31', NULL),
(61, '3f4cef67-85ba-4ee2-999e-da9e69eb71ad', '2026-01-14', 8, 5, 1, '2026-01-13 19:17:31', '2026-01-13 19:17:31', NULL),
(62, 'a21a8e32-c065-4f38-93a5-98cab8905796', '2026-01-14', 3, 10, 1, '2026-01-13 19:18:07', '2026-01-13 19:18:07', NULL),
(63, '883a14e2-e2ab-43e5-be15-6a20977cc986', '2026-01-14', 3, 11, 1, '2026-01-13 19:18:07', '2026-01-13 19:18:07', NULL),
(64, '5fee66b9-8d07-4d4d-8b99-7589d1ff3c9b', '2026-01-14', 3, 9, 1, '2026-01-13 19:18:07', '2026-01-13 19:18:07', NULL),
(65, '899784ee-d5eb-44b4-9db1-8c7d25330892', '2026-01-14', 9, 10, 1, '2026-01-13 19:18:07', '2026-01-13 19:18:07', NULL),
(66, 'fdb5e4a5-ef33-403e-b25d-0c55a36ad04b', '2026-01-14', 9, 11, 1, '2026-01-13 19:18:07', '2026-01-13 19:18:07', NULL),
(67, '7420cb8e-e7a6-45d4-b431-3c61218d077d', '2026-01-14', 9, 9, 1, '2026-01-13 19:18:07', '2026-01-13 19:18:07', NULL),
(68, '62740cb6-11ca-441d-ac91-7c3f2b5f0f03', '2026-01-21', 8, 10, 1, '2026-01-20 18:49:39', '2026-01-20 18:49:39', NULL),
(69, '0bf0b514-dd16-47bf-8178-80c3c4c4060f', '2026-01-21', 8, 11, 1, '2026-01-20 18:49:39', '2026-01-20 18:49:39', NULL),
(70, '6632f157-f549-42de-a883-88156b489e2d', '2026-01-21', 8, 9, 1, '2026-01-20 18:49:39', '2026-01-20 18:49:39', NULL),
(71, '39f2b093-de80-4d37-ab4e-e19b37be71f8', '2026-01-21', 3, 10, 1, '2026-01-20 19:10:09', '2026-01-20 19:10:09', NULL),
(72, 'cb7eb4d4-fd7b-4292-9bec-3480bb0c9ea5', '2026-01-21', 3, 11, 1, '2026-01-20 19:10:09', '2026-01-20 19:10:09', NULL),
(73, 'aa3c828b-b5b7-4928-b1c7-f651ce9a539b', '2026-01-21', 3, 9, 1, '2026-01-20 19:10:09', '2026-01-20 19:10:09', NULL),
(74, 'b52e7e6a-65a9-494c-ac2f-7add99dc5588', '2026-01-21', 9, 10, 1, '2026-01-20 19:10:09', '2026-01-20 19:10:09', NULL),
(75, 'ed83a3b2-bae1-4bea-b391-382a08af3583', '2026-01-21', 9, 11, 1, '2026-01-20 19:10:09', '2026-01-20 19:10:09', NULL),
(76, 'ec3a092e-5cc6-4547-8156-c6c25c1231ae', '2026-01-21', 9, 9, 1, '2026-01-20 19:10:09', '2026-01-20 19:10:09', NULL),
(77, 'e9a5b487-5d9f-42c4-87b2-48b72f9565de', '2026-01-21', 8, 12, 1, '2026-01-20 19:15:36', '2026-01-20 19:15:36', NULL),
(78, 'eab608f9-61e1-40e0-88c3-847a8ac165e2', '2026-01-21', 8, 14, 1, '2026-01-20 19:15:36', '2026-01-20 19:15:36', NULL),
(79, 'b675018a-67c9-47f8-b45c-27679c6642bb', '2026-01-21', 8, 6, 1, '2026-01-20 19:15:36', '2026-01-20 19:15:36', NULL),
(80, '440ad359-eadc-46ff-8e3c-a332896cef1c', '2026-01-21', 8, 4, 1, '2026-01-20 19:15:36', '2026-01-20 19:15:36', NULL),
(81, 'db28d897-00e5-4550-b0f7-ab6c83fc6d28', '2026-01-28', 8, 10, 1, '2026-01-27 19:07:55', '2026-01-27 19:07:56', NULL),
(82, '70e37da6-9e3a-4562-a491-23adaf4f621a', '2026-01-28', 8, 11, 1, '2026-01-27 19:07:55', '2026-01-27 19:07:56', NULL),
(83, '5653c348-06e2-44f6-9175-c5f3a803f7f2', '2026-01-28', 8, 9, 1, '2026-01-27 19:07:55', '2026-01-27 19:07:56', NULL),
(84, '30a47472-0802-4558-adbe-aab403363fce', '2026-01-28', 10, 10, 1, '2026-01-27 19:07:55', '2026-01-27 19:07:56', NULL),
(85, 'e9e80941-6be0-4759-9ec6-6965e713ae6e', '2026-01-28', 10, 11, 1, '2026-01-27 19:07:55', '2026-01-27 19:07:56', NULL),
(86, '9e9dc042-cb2e-4e30-80f5-108e166c1410', '2026-01-28', 10, 9, 1, '2026-01-27 19:07:55', '2026-01-27 19:07:56', NULL),
(87, '3def00fc-5cab-4133-ad63-385cc3af40e1', '2026-01-28', 8, 12, 1, '2026-01-27 19:08:20', '2026-01-27 19:08:20', NULL),
(88, '85f116c5-c816-496c-ba1a-1feb5d9af218', '2026-01-28', 8, 14, 1, '2026-01-27 19:08:20', '2026-01-27 19:08:20', NULL),
(89, '586a7b9e-f003-4d90-85db-32274855bcfb', '2026-01-28', 8, 6, 1, '2026-01-27 19:08:20', '2026-01-27 19:08:20', NULL),
(90, 'd043b74d-f508-4613-ab4e-6f5d6ea2195d', '2026-01-28', 8, 4, 1, '2026-01-27 19:08:20', '2026-01-27 19:08:20', NULL),
(91, '7256cc00-2957-42ce-9634-9baac1265928', '2026-01-28', 8, 5, 1, '2026-01-27 19:08:20', '2026-01-27 19:08:20', NULL),
(92, '949efe82-cf63-46f7-9254-6a7fd853fa46', '2026-01-28', 10, 12, 1, '2026-01-27 19:08:20', '2026-01-27 19:08:20', NULL),
(93, 'faa23896-4fbc-4951-b623-fe66d11f2a08', '2026-01-28', 10, 14, 1, '2026-01-27 19:08:20', '2026-01-27 19:08:20', NULL),
(94, '808f8bd4-f334-4b67-b68d-e9b4854b3b7b', '2026-01-28', 10, 6, 1, '2026-01-27 19:08:20', '2026-01-27 19:08:20', NULL),
(95, 'f3698e88-1618-41c2-87ee-72ea6fadb7b9', '2026-01-28', 10, 4, 1, '2026-01-27 19:08:20', '2026-01-27 19:08:20', NULL),
(96, '67e35906-9202-4ac4-a04c-24ec92b4aeb6', '2026-01-28', 10, 5, 1, '2026-01-27 19:08:20', '2026-01-27 19:08:20', NULL),
(97, '99937cfa-92df-4ac4-be54-233eaa517e81', '2026-02-04', 8, 11, 1, '2026-02-03 19:28:01', '2026-02-03 19:29:46', NULL),
(98, 'dafbaa35-3bea-440c-9949-4dfe3348c855', '2026-02-04', 8, 9, 1, '2026-02-03 19:28:01', '2026-02-03 19:29:46', NULL),
(99, '965820df-b0d1-4110-bcbe-dbe5e037c875', '2026-02-04', 10, 11, 1, '2026-02-03 19:28:01', '2026-02-03 19:29:46', NULL),
(100, 'ea895eb5-3549-4c53-940e-8ceb0ea4f0e7', '2026-02-04', 10, 10, 1, '2026-02-03 19:29:46', '2026-02-03 19:29:46', NULL),
(101, '4d2bf30c-8005-457f-916c-3c4c7ef3b29b', '2026-02-04', 8, 12, 1, '2026-02-03 21:05:04', '2026-02-03 21:05:04', NULL),
(102, 'd8478a20-7b1e-4ac6-827c-5b4411d06e92', '2026-02-04', 8, 14, 1, '2026-02-03 21:05:04', '2026-02-03 21:05:04', NULL),
(103, '86c4f1bc-4a75-4e43-bc5a-bee26dc2482d', '2026-02-04', 8, 6, 1, '2026-02-03 21:05:04', '2026-02-03 21:05:04', NULL),
(104, '4b6e0fa1-a57d-4fc1-a14f-33b591debe25', '2026-02-04', 8, 4, 1, '2026-02-03 21:05:04', '2026-02-03 21:05:04', NULL),
(105, '14f312ce-bfbb-41a7-848d-a9e7b556f8b9', '2026-02-04', 8, 5, 1, '2026-02-03 21:05:04', '2026-02-03 21:05:04', NULL),
(106, '4bd56c30-f1f4-4cc2-8169-70bf3e05a3f0', '2026-02-04', 10, 12, 1, '2026-02-03 21:05:04', '2026-02-03 21:05:04', NULL),
(107, '13ebbad5-fb4f-48d3-8590-09ae8ccc17de', '2026-02-04', 10, 14, 1, '2026-02-03 21:05:04', '2026-02-03 21:05:04', NULL),
(108, '3c4e4a35-c695-4fd2-bfcb-9e13854a6a35', '2026-02-04', 10, 6, 1, '2026-02-03 21:05:04', '2026-02-03 21:05:04', NULL),
(109, '641ad1f5-42b1-4912-a133-018aecd6f503', '2026-02-04', 10, 4, 1, '2026-02-03 21:05:05', '2026-02-03 21:05:05', NULL),
(110, '9165b7f2-9880-495c-bd24-9d1fefff4b6a', '2026-02-04', 10, 5, 1, '2026-02-03 21:05:05', '2026-02-03 21:05:05', NULL),
(111, '8b574f20-8850-4ea2-a40f-9a80b6dda40d', '2026-02-04', 3, 12, 1, '2026-02-03 21:05:22', '2026-02-03 21:05:22', NULL),
(112, 'e8ae2f55-a3b8-44b4-a391-73fea58d9fbe', '2026-02-04', 3, 14, 1, '2026-02-03 21:05:22', '2026-02-03 21:05:22', NULL),
(113, '44769311-5715-479a-ab85-00e91beba994', '2026-02-04', 3, 6, 1, '2026-02-03 21:05:22', '2026-02-03 21:05:22', NULL),
(114, '24ab98c7-ba4a-49ed-94bc-423fe736d21e', '2026-02-04', 3, 4, 1, '2026-02-03 21:05:22', '2026-02-03 21:05:22', NULL),
(115, '5062f04d-0a57-4395-9355-88e55fd27533', '2026-02-04', 3, 5, 1, '2026-02-03 21:05:22', '2026-02-03 21:05:22', NULL),
(116, '7ba65728-2f9b-407d-99ed-79b66191d614', '2026-02-04', 9, 12, 1, '2026-02-03 21:05:22', '2026-02-03 21:05:22', NULL),
(117, '8b48a26a-99d8-4dbf-85cc-aeb98bdea598', '2026-02-04', 9, 14, 1, '2026-02-03 21:05:22', '2026-02-03 21:05:22', NULL),
(118, '3a551aa6-3123-473e-928b-dd716cda97b9', '2026-02-04', 9, 6, 1, '2026-02-03 21:05:22', '2026-02-03 21:05:22', NULL),
(119, '3f7a9b2e-d572-4194-97e0-bc28811cc395', '2026-02-04', 9, 4, 1, '2026-02-03 21:05:22', '2026-02-03 21:05:22', NULL),
(120, '14f4a5e6-1702-4993-9863-799254c4b6e3', '2026-02-04', 9, 5, 1, '2026-02-03 21:05:22', '2026-02-03 21:05:22', NULL),
(121, 'e9a21646-87c6-46c5-b739-3dafafd5d6bd', '2026-02-04', 3, 10, 1, '2026-02-03 21:06:17', '2026-02-03 21:06:17', NULL),
(122, 'a597b090-be4b-47ce-88c1-74e8d635b680', '2026-02-04', 3, 11, 1, '2026-02-03 21:06:17', '2026-02-03 21:06:17', NULL),
(123, '35cd4eb5-38ab-426c-baae-2ae530d642ce', '2026-02-04', 3, 9, 1, '2026-02-03 21:06:17', '2026-02-03 21:06:17', NULL),
(124, 'a47c99f0-07b9-45ad-8f19-10828a4ce4da', '2026-02-04', 9, 10, 1, '2026-02-03 21:06:17', '2026-02-03 21:06:17', NULL),
(125, 'a15d2338-a65c-42d5-b8a5-e587d5d3db5c', '2026-02-04', 9, 11, 1, '2026-02-03 21:06:17', '2026-02-03 21:06:17', NULL),
(126, '1368dc69-6bbb-4691-ab7b-04151b72636e', '2026-02-04', 9, 9, 1, '2026-02-03 21:06:17', '2026-02-03 21:06:17', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `halaqah`
--

CREATE TABLE `halaqah` (
  `id` int NOT NULL,
  `kode` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_halaqah` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `musyrif_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `halaqah`
--

INSERT INTO `halaqah` (`id`, `kode`, `nama_halaqah`, `musyrif_id`, `created_at`, `updated_at`, `deleted_at`) VALUES
(6, 'H001', 'HALAQAH IBNU ABBAS 2', 4, '2025-10-21 19:08:24', '2025-10-21 21:25:37', NULL),
(7, 'H002', 'Halaqah Ibnu Ruys', 2, '2025-10-28 17:53:39', '2025-10-28 17:53:39', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `job_batches`
--

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
  `finished_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `kegiatan`
--

CREATE TABLE `kegiatan` (
  `id` int NOT NULL,
  `kode` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_kegiatan` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `parent_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `kegiatan`
--

INSERT INTO `kegiatan` (`id`, `kode`, `nama_kegiatan`, `parent_id`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'PG001', 'Kegiatan Pagi', NULL, '2025-10-08 01:42:40', '2025-10-08 01:42:40', NULL),
(2, 'SR001', 'Kegiatan Sore', NULL, '2025-10-08 01:42:40', '2025-11-25 23:36:56', '2025-11-25 23:36:56'),
(3, 'ML001', 'Kegiatan Malam', NULL, '2025-10-08 01:42:40', '2025-11-25 23:37:06', NULL),
(4, 'PG101', 'Sholat Subuh Berjamaah', 1, '2025-10-08 01:42:40', '2025-10-08 01:42:40', NULL),
(5, 'PG102', 'Tilawah Pagi', 1, '2025-10-08 01:42:40', '2025-10-08 01:42:40', NULL),
(6, 'PG103', 'Senam Pagi', 1, '2025-10-08 01:42:40', '2025-10-08 01:42:40', NULL),
(7, 'SR101', 'Sholat Ashar Berjamaah', 2, '2025-10-08 01:42:40', '2025-10-08 01:42:40', NULL),
(8, 'SR102', 'Murojaah Hafalan', 2, '2025-10-08 01:42:40', '2025-10-08 01:42:40', NULL),
(9, 'ML101', 'Sholat Isya Berjamaah', 3, '2025-10-08 01:42:40', '2025-10-08 01:42:40', NULL),
(10, 'ML102', 'Belajar Malam', 3, '2025-10-08 01:42:40', '2025-10-08 01:42:40', NULL),
(11, 'ML103', 'Doa Penutup', 3, '2025-10-08 01:42:40', '2025-10-08 01:42:40', NULL),
(12, 'PG104', 'Al Ma\'surat Pagi Bersama', 1, '2025-10-07 18:44:07', '2025-10-07 18:44:48', NULL),
(13, 'KG0001', 'Al Ma\'surat Sore', 2, '2025-10-07 22:30:53', '2025-10-07 22:30:53', NULL),
(14, 'KG0002', 'Makan Pagi', 1, '2025-10-07 22:40:37', '2025-10-07 22:40:37', NULL),
(15, 'KG0003', 'Kegiatan Siang', NULL, '2025-10-07 22:53:54', '2025-11-25 17:33:54', '2025-11-25 17:33:54'),
(16, 'KG0004', 'sekolah', 15, '2025-10-14 17:06:23', '2025-10-14 17:06:23', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `kesehatan`
--

CREATE TABLE `kesehatan` (
  `id` int NOT NULL,
  `kode` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `santri_id` int NOT NULL,
  `tanggal` date NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `keterangan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `kesehatan`
--

INSERT INTO `kesehatan` (`id`, `kode`, `santri_id`, `tanggal`, `status`, `keterangan`, `created_at`, `deleted_at`, `updated_at`) VALUES
(1, '3', 0, '2025-12-05', 'sehat', '', '2025-12-05 04:07:35', NULL, '2025-12-05 04:07:35'),
(2, '9', 0, '2025-12-05', 'sehat', '', '2025-12-05 04:07:35', NULL, '2025-12-05 04:07:35'),
(3, '8', 0, '2026-01-14', 'izin', '', '2026-01-14 03:32:17', NULL, '2026-01-14 03:32:17'),
(4, '8', 0, '2026-01-21', 'sehat', '', '2026-01-21 04:48:54', NULL, '2026-01-21 04:48:54'),
(5, '10', 0, '2026-01-21', 'sehat', '', '2026-01-21 04:48:54', NULL, '2026-01-21 04:48:54'),
(6, 'KST-2026-01-28-8-ieWp', 8, '2026-01-28', 'sehat', NULL, '2026-01-28 04:17:24', NULL, '2026-01-28 04:17:24'),
(7, 'KST-2026-01-28-10-f4Or', 10, '2026-01-28', 'sehat', NULL, '2026-01-28 04:17:24', NULL, '2026-01-28 04:17:24'),
(8, 'KST-20260204-3-NhqY', 3, '2026-02-04', 'sehat', NULL, '2026-02-04 13:12:24', NULL, '2026-02-04 13:12:24'),
(9, 'KST-20260204-9-tXnM', 9, '2026-02-04', 'sehat', NULL, '2026-02-04 13:12:24', NULL, '2026-02-04 13:12:24');

-- --------------------------------------------------------

--
-- Struktur dari tabel `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_09_24_080439_create_permission_tables', 2);

-- --------------------------------------------------------

--
-- Struktur dari tabel `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 1),
(2, 'App\\Models\\User', 2),
(3, 'App\\Models\\User', 3),
(2, 'App\\Models\\User', 4),
(2, 'App\\Models\\User', 5),
(2, 'App\\Models\\User', 6),
(2, 'App\\Models\\User', 7),
(3, 'App\\Models\\User', 8),
(3, 'App\\Models\\User', 9),
(3, 'App\\Models\\User', 10);

-- --------------------------------------------------------

--
-- Struktur dari tabel `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `roles`
--

CREATE TABLE `roles` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'web', '2025-09-24 01:07:44', '2025-09-24 01:07:44'),
(2, 'musyrif', 'web', '2025-09-24 01:07:44', '2025-09-24 01:07:44'),
(3, 'santri', 'web', '2025-09-24 01:07:44', '2025-09-24 01:07:44');

-- --------------------------------------------------------

--
-- Struktur dari tabel `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `role_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('wtxbTSupgLkgwiYFOLMO7N5JE8Phjf16ayCsllNk', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiTGJ1b1R1MDk5bUd2RVhWYlhBR3Q4QjdGME9OM25nR3l6dDYxd2VsQiI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjI4OiJodHRwOi8vYm9hcmRpbmcudGVzdC9oYWxhcWFoIjtzOjU6InJvdXRlIjtzOjEzOiJoYWxhcWFoLmluZGV4Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6Mjt9', 1770216159);

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telephone` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `halaqah_id` int DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `telephone`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `halaqah_id`, `deleted_at`) VALUES
(1, 'Super Admin', 'admin@boarding.test', NULL, NULL, '$2y$12$uLq.Y5YSQHt/Cns0uj4w1OXscUgUc3hlhaV9z4mtq5Dld6PbOxP.m', '867ljCAWAhdruRQoskd99CKsRJ23IjVYd0IuGm1bG8iK17CsQv3YuuSFKCII', '2025-09-24 01:11:14', '2025-09-24 01:11:14', NULL, NULL),
(2, 'Ustadz Musyrif', 'musyrif@boarding.test', '08123816283', NULL, '$2y$12$pmqFLAeLCNaj8aYt8OjfcuZc/mgYMwTEpq11WYzo9725aA7QjlS8S', 'suQRjB8l5ZDVzLLdfgvMNoooShlWJ71aBtqxIhXylBOe51VsK3SDRq9liMHR', '2025-09-24 01:11:15', '2026-01-20 21:46:54', NULL, NULL),
(3, 'Santri 1', 'santri@boarding.test', '6281235672593', NULL, '$2y$12$kg./FodZoDO53CtyCInFX.i3tZHn34ZNmFB6bMqvUagubpt5SSOdy', NULL, '2025-09-24 01:11:15', '2026-01-21 00:54:59', 7, NULL),
(4, 'USTADZ HABIB', 'HABIB@gmail.com', '081273617263', NULL, '$2y$12$e6RDo5iGkNmSuDIY9N6pgePzgllOWebIVkTL6gnpdTMoMWb9ZJZdq', NULL, '2025-10-14 23:07:02', '2026-01-20 21:46:47', NULL, NULL),
(5, 'USTADZ HAFIZ-', 'HAFIZ@gmail.com', NULL, NULL, '$2y$12$bipxQuLuEGK2AqwddtbWCuj5GADNZW9OrjFQ18tZlqIMEvPXaUBCS', NULL, '2025-10-14 23:07:48', '2025-10-14 23:08:16', NULL, '2025-10-14 23:08:16'),
(6, 'Ustadz Nur Ahmad', 'ahmad@gmail.com', NULL, NULL, '$2y$12$jx.VdDfOPiWUybLLIKUDSO4Yex3AHlCJ3auOXdCeVmEYUh8d/kYwq', NULL, '2025-10-28 17:54:49', '2025-10-28 17:56:02', NULL, '2025-10-28 17:56:02'),
(7, 'Ustadz Khoir', 'khoir@gmail.com', '081235647434', NULL, '$2y$12$Ik2ohspYn6qEmYM.jpBdKeoD7AXJBbm2DJauvS6lHHwEUSRv2xu2i', NULL, '2025-10-28 17:57:36', '2026-01-20 21:46:40', NULL, NULL),
(8, 'ABY', 'aby@gmail.com', '62895340360140', NULL, '$2y$12$dSD9piGVJZ4ti1yYpGkV6u1T8HSKOAlWVvv19qbT5S/4nTaA9pqtG', NULL, '2025-11-04 19:07:58', '2026-01-21 00:55:42', 6, NULL),
(9, 'wahyu', 'wahyu@gmail.com', '6282345654345', NULL, '$2y$12$M7Lr.jLTnPc8XQQ37O86eeZYx155A5WPqu3TSEmf3AreO1d/XVycC', NULL, '2025-12-04 18:57:21', '2026-01-21 00:55:59', 7, NULL),
(10, 'ZAIN', 'zein@gmail.com', '6281263747485', NULL, '$2y$12$bVkTXyGnQQPPx9iTLoHyY.fmKKf2u0LHlbFDdwOQ/LjnkXHtq094u', NULL, '2026-01-20 21:33:00', '2026-01-21 00:56:11', 6, NULL);

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `absen`
--
ALTER TABLE `absen`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indeks untuk tabel `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indeks untuk tabel `ceklist_kegiatan`
--
ALTER TABLE `ceklist_kegiatan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode` (`kode`),
  ADD KEY `santri_id` (`santri_id`),
  ADD KEY `kegiatan_id` (`kegiatan_id`);

--
-- Indeks untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indeks untuk tabel `halaqah`
--
ALTER TABLE `halaqah`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode` (`kode`),
  ADD KEY `musyrif_id` (`musyrif_id`);

--
-- Indeks untuk tabel `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indeks untuk tabel `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `kegiatan`
--
ALTER TABLE `kegiatan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode` (`kode`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Indeks untuk tabel `kesehatan`
--
ALTER TABLE `kesehatan`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indeks untuk tabel `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indeks untuk tabel `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indeks untuk tabel `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indeks untuk tabel `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indeks untuk tabel `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indeks untuk tabel `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `halaqah_id` (`halaqah_id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `absen`
--
ALTER TABLE `absen`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT untuk tabel `ceklist_kegiatan`
--
ALTER TABLE `ceklist_kegiatan`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=127;

--
-- AUTO_INCREMENT untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `halaqah`
--
ALTER TABLE `halaqah`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `kegiatan`
--
ALTER TABLE `kegiatan`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT untuk tabel `kesehatan`
--
ALTER TABLE `kesehatan`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `ceklist_kegiatan`
--
ALTER TABLE `ceklist_kegiatan`
  ADD CONSTRAINT `ceklist_kegiatan_ibfk_1` FOREIGN KEY (`santri_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ceklist_kegiatan_ibfk_2` FOREIGN KEY (`kegiatan_id`) REFERENCES `kegiatan` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `halaqah`
--
ALTER TABLE `halaqah`
  ADD CONSTRAINT `halaqah_ibfk_1` FOREIGN KEY (`musyrif_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `kegiatan`
--
ALTER TABLE `kegiatan`
  ADD CONSTRAINT `kegiatan_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `kegiatan` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`halaqah_id`) REFERENCES `halaqah` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
