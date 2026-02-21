-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Июн 16 2025 г., 22:54
-- Версия сервера: 8.0.30
-- Версия PHP: 8.1.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `diploma`
--

-- --------------------------------------------------------

--
-- Структура таблицы `categories`
--

CREATE TABLE `categories` (
  `id` int NOT NULL,
  `name` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `categories`
--

INSERT INTO `categories` (`id`, `name`) VALUES
(1, 'Дом'),
(2, 'Работа'),
(3, 'Личное'),
(4, 'Учеба'),
(5, 'Спорт и здоровье');

-- --------------------------------------------------------

--
-- Структура таблицы `failed_jobs`
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
-- Структура таблицы `following_list`
--

CREATE TABLE `following_list` (
  `id` int UNSIGNED NOT NULL,
  `subscriber_id` int UNSIGNED NOT NULL,
  `subscribed_to_id` int UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `following_list`
--

INSERT INTO `following_list` (`id`, `subscriber_id`, `subscribed_to_id`, `created_at`) VALUES
(19, 22, 23, '2025-06-08 13:32:16'),
(20, 23, 22, '2025-06-08 16:03:11'),
(21, 24, 23, '2025-06-08 17:58:18'),
(23, 23, 24, '2025-06-15 22:01:41');

-- --------------------------------------------------------

--
-- Структура таблицы `goals`
--

CREATE TABLE `goals` (
  `id` int NOT NULL,
  `description` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `task_id` int NOT NULL,
  `is_completed` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `goals`
--

INSERT INTO `goals` (`id`, `description`, `task_id`, `is_completed`) VALUES
(381, 'Посмотреть информацию об альбоме на официальном сайте исполнителя.', 136, NULL),
(382, 'Послушать альбом полностью.', 136, NULL),
(383, 'Прочитать рецензии и отзывы о альбоме.', 136, NULL),
(384, 'Выяснить контекст и историю создания альбома.', 136, NULL),
(385, 'Обсудить альбом с друзьями или в музыкальных сообществах.', 136, NULL),
(407, 'Изучить требования к презентации дипломного проекта.', 141, 1),
(408, 'Подготовить структуру презентации.', 141, 1),
(409, 'Собрать необходимые материалы и информацию.', 141, 1),
(410, 'Создать слайды с текстом и графикой.', 141, 1),
(411, 'Провести репетицию презентации и отрепетировать выступление.', 141, 1),
(412, 'Подготовить все необходимое.', 142, 1),
(413, 'Найти свободное время для выполнения задачи.', 142, 1),
(414, 'Очистить рамы окон от пыли и грязи.', 142, 1),
(415, 'Помыть стекла окон снаружи и внутри.', 142, 1),
(416, 'Убедиться, что окна высохли без разводов и следов мыльной пленки.', 142, 1),
(417, 'Создать тестовые данные для проверки функционала.', 143, NULL),
(418, 'Пройти тестирование различных сценариев использования системы.', 143, NULL),
(419, 'Предоставить отчёт о результатах тестирования.', 143, 1),
(420, 'Определить тему и основные идеи речи.', 144, NULL),
(421, 'Провести исследование и собрать необходимую информацию.', 144, NULL),
(422, 'Составить план структуры речи: вступление, основная часть, заключение.', 144, NULL),
(423, 'Написать первый черновик речи, уделяя внимание логике и ясности высказываний.', 144, NULL),
(444, 'Изучить материалы для написания документации.', 150, 1),
(445, 'Написать первую часть документации.', 150, 0),
(446, 'Получить обратную связь', 150, NULL),
(447, 'Завершить работу над всей документацией.', 150, 0),
(448, 'Отформатировать готовый текст.', 150, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(9, '2014_10_12_000000_create_users_table', 1),
(10, '2014_10_12_100000_create_password_reset_tokens_table', 1),
(11, '2019_08_19_000000_create_failed_jobs_table', 1),
(12, '2019_12_14_000001_create_personal_access_tokens_table', 1);

-- --------------------------------------------------------

--
-- Структура таблицы `notifications`
--

CREATE TABLE `notifications` (
  `id` int NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `task_id` int NOT NULL,
  `type_id` int NOT NULL,
  `is_read` tinyint(1) DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `task_id`, `type_id`, `is_read`, `created_at`) VALUES
(39, 23, 141, 2, 1, '2025-06-15 22:59:25'),
(40, 23, 141, 3, 1, '2025-06-15 22:59:36');

-- --------------------------------------------------------

--
-- Структура таблицы `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `personal_access_tokens`
--

INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES
(1, 'App\\Models\\User', 4, 'api', '83d2bbcfd90e3b15e3e2af671909b6abc52fe771173ff982d5d5138633c8f4cd', '[\"*\"]', NULL, NULL, '2025-05-10 12:47:06', '2025-05-10 12:47:06'),
(2, 'App\\Models\\User', 4, 'api', 'ed3ce837512c84ce4ed3ce82f517eab8c9d8cb600728a4e0416cbd307a1aa0fb', '[\"*\"]', NULL, NULL, '2025-05-10 12:53:59', '2025-05-10 12:53:59'),
(8, 'App\\Models\\User', 8, 'api', '7347cbb922ac42e4398f567d6026ffd70d32e8314a08e3a3262a33255e603b0d', '[\"*\"]', NULL, NULL, '2025-05-10 15:57:54', '2025-05-10 15:57:54'),
(42, 'App\\Models\\User', 8, 'api', '51810095531d7e35d9bc64a92e496eeb62f75bff8617ee1c43ff48349e78fe5c', '[\"*\"]', NULL, NULL, '2025-05-11 10:39:58', '2025-05-11 10:39:58'),
(43, 'App\\Models\\User', 18, 'api', '8bf13add81a1c5ba29b0a4026c427d890570c0ba9a7fe3d62c76f825bfcd9ec5', '[\"*\"]', NULL, NULL, '2025-05-11 13:21:01', '2025-05-11 13:21:01'),
(45, 'App\\Models\\User', 20, 'api', '7e70e4a8917363a87a65379c1f537828fabbbef8fa558615fd9640e71cb1e99a', '[\"*\"]', '2025-05-11 14:01:34', NULL, '2025-05-11 14:01:29', '2025-05-11 14:01:34'),
(46, 'App\\Models\\User', 19, 'api', 'c51b27ae05105cdcbb9e54a39324559c2fcbd7cbae8f74ef40f729ec8464a380', '[\"*\"]', '2025-05-11 14:03:23', NULL, '2025-05-11 14:01:55', '2025-05-11 14:03:23'),
(47, 'App\\Models\\User', 19, 'api', '71c9ef59ff7c47329aa2740472aad12c53dece0967f266c6c011da76e3bd9e9f', '[\"*\"]', '2025-05-11 14:55:10', NULL, '2025-05-11 14:48:07', '2025-05-11 14:55:10'),
(50, 'App\\Models\\User', 19, 'api', 'a2d177bc482bcf33ef534896b8f5524f12b09ecce9d1f849747cf47db768db08', '[\"*\"]', '2025-05-12 12:33:22', NULL, '2025-05-11 18:14:54', '2025-05-12 12:33:22'),
(54, 'App\\Models\\User', 19, 'api', '6faf4bb26268a973c8305d5d8e982d30bc2deb4aaf79815667070c601cb7a7a5', '[\"*\"]', '2025-05-12 20:34:41', NULL, '2025-05-12 12:33:30', '2025-05-12 20:34:41'),
(70, 'App\\Models\\User', 19, 'api', 'b28e1762895db3d8a98bb882ef49fd22f96c0251c1e21423f87eee6aaf0a571c', '[\"*\"]', NULL, NULL, '2025-05-16 17:21:30', '2025-05-16 17:21:30'),
(71, 'App\\Models\\User', 19, 'api', '3243851236b675a9d8efe7c744ecb82355695ababe8683dc55ccc9f2b57dc81d', '[\"*\"]', '2025-06-01 16:26:18', NULL, '2025-05-16 17:32:13', '2025-06-01 16:26:18'),
(77, 'App\\Models\\User', 19, 'api', 'ec8920f7e4df31d43ea18af5dd3cdbdd3ebc5c7c5eb147ac5789710222ff854c', '[\"*\"]', '2025-05-19 18:30:11', NULL, '2025-05-17 13:41:07', '2025-05-19 18:30:11'),
(78, 'App\\Models\\User', 19, 'api', 'b978527be66f43d49b727f639a56cf12f13a4b1a7ae159dc7d0939d4f82637f2', '[\"*\"]', '2025-05-17 14:44:59', NULL, '2025-05-17 14:44:46', '2025-05-17 14:44:59'),
(99, 'App\\Models\\User', 19, 'api', '75a919256b32316517e72494dd9fbaaa66122e8347e8e52db7690610cdf86b13', '[\"*\"]', '2025-06-07 21:23:01', NULL, '2025-06-07 19:21:08', '2025-06-07 21:23:01'),
(103, 'App\\Models\\User', 22, 'api', '2e0965b1de7825ad3c882677a3e2852919a4e2a6eca4c39f8eb02a0bbbc48f2e', '[\"*\"]', NULL, NULL, '2025-06-07 23:06:19', '2025-06-07 23:06:19'),
(104, 'App\\Models\\User', 22, 'api', 'bbcda98929c4deecd8b512c2d2692d3783686e50449f90b6023d719c8413eeaa', '[\"*\"]', NULL, NULL, '2025-06-07 23:07:11', '2025-06-07 23:07:11'),
(105, 'App\\Models\\User', 22, 'api', 'd7690351d0daf7ebf83d8578ef27967d31f56bf045c0ea6d1d168d4ff817200c', '[\"*\"]', '2025-06-07 23:51:50', NULL, '2025-06-07 23:14:51', '2025-06-07 23:51:50'),
(117, 'App\\Models\\User', 22, 'api', '5fe45875c65db4783f0295926a00cbdcdb876eba30f925465916d2b14c4c7c5d', '[\"*\"]', NULL, NULL, '2025-06-08 15:27:54', '2025-06-08 15:27:54'),
(118, 'App\\Models\\User', 22, 'api', '0da4c30157e1459b5c332db3fca1b21a5cdd5c04abe8670f7109c4befce0520b', '[\"*\"]', NULL, NULL, '2025-06-08 15:28:05', '2025-06-08 15:28:05'),
(119, 'App\\Models\\User', 22, 'api', '47f7b8daa912ec101d332c5eb32370c7d4b694a36ed717485701cbd3d8b299a9', '[\"*\"]', '2025-06-08 15:28:13', NULL, '2025-06-08 15:28:09', '2025-06-08 15:28:13'),
(121, 'App\\Models\\User', 23, 'api', 'a70e8d2c72102b823a4954ec0374bbdb2324f581f2646d80f75a9acdd08dbe52', '[\"*\"]', '2025-06-08 15:57:58', NULL, '2025-06-08 15:53:33', '2025-06-08 15:57:58'),
(125, 'App\\Models\\User', 22, 'api', '41df0abd9de906a05e5ebf976eee9412e24f2e985f5e81c046e45148d5d3993a', '[\"*\"]', '2025-06-08 20:34:08', NULL, '2025-06-08 16:56:40', '2025-06-08 20:34:08'),
(133, 'App\\Models\\User', 23, 'api', '0396f1ce4db61eeae0b4908a38ee211b0a6d9e5ada470cf0bb9303be7a8d67de', '[\"*\"]', '2025-06-16 01:00:24', NULL, '2025-06-15 20:08:57', '2025-06-16 01:00:24'),
(134, 'App\\Models\\User', 22, 'api', '182de83f780623896327b1efa106c2a24d05b0cb380e7c2099e328f3ce2cf878', '[\"*\"]', '2025-06-15 22:16:40', NULL, '2025-06-15 22:16:31', '2025-06-15 22:16:40');

-- --------------------------------------------------------

--
-- Структура таблицы `tasks`
--

CREATE TABLE `tasks` (
  `id` int NOT NULL,
  `title` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `deadline` date NOT NULL,
  `status` enum('0','1','2','3','4') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '0',
  `public_token` varchar(36) DEFAULT NULL,
  `category_id` int DEFAULT NULL,
  `created_at` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `tasks`
--

INSERT INTO `tasks` (`id`, `title`, `user_id`, `deadline`, `status`, `public_token`, `category_id`, `created_at`) VALUES
(133, 'Узнать про альбом', 22, '2025-06-20', '0', NULL, 3, '2025-06-08'),
(136, 'Узнать про альбом SOPHIE', 22, '2025-06-26', '0', NULL, 4, '2025-06-08'),
(141, 'Создать презентацию для дипломного проектa', 23, '2025-06-16', '1', NULL, 4, '2025-06-15'),
(142, 'Протереть окна', 23, '2025-06-27', '1', NULL, 1, '2025-06-15'),
(143, 'Протестировать эту систему', 23, '2025-06-19', '2', NULL, 4, '2025-06-15'),
(144, 'Написать речь для выступления', 23, '2025-06-10', '3', NULL, 4, '2025-06-01'),
(150, 'Написать документацию', 23, '2025-06-20', '2', 'b8480a9a-1064-4241-9576-f3f6db4d2612', 4, '2025-06-16');

-- --------------------------------------------------------

--
-- Структура таблицы `types`
--

CREATE TABLE `types` (
  `id` int NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `types`
--

INSERT INTO `types` (`id`, `name`) VALUES
(1, 'Заметка просрочена'),
(2, 'Подходит срок сдачи'),
(3, 'Вы успели выполнить заметку');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`) VALUES
(22, 'Денис', 'btqc4nqs2@gmail.com', '$2y$12$/O5Zvxn8CJwFj7BOYW4D5eWuAhvmWPsevzWN7azOHPPIm8LUhMRrC'),
(23, 'Альбина', 'test@gmail.com', '$2y$12$A3sEzM4S0gDUNW9DcGSg9.0ScVbEEyceictD5U9SwfAFuj3DFIr.e'),
(24, 'Ринат', 'admin@gmail.com', '$2y$12$ZdOWyBz.LNyqCymZMQHW6.meNH3W/bX3p/IXN687lvqtVtTu30YXq'),
(25, 'Денис', 'test22@gmail.com', '$2y$12$fhOGU/SBZhCI5sZ9l1znF.KSt9VGQQuo1m7AF5PsS8D2TpC17yKRy');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Индексы таблицы `following_list`
--
ALTER TABLE `following_list`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_following` (`subscriber_id`,`subscribed_to_id`),
  ADD KEY `subscribed_to_id` (`subscribed_to_id`);

--
-- Индексы таблицы `goals`
--
ALTER TABLE `goals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `task_id` (`task_id`);

--
-- Индексы таблицы `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`,`task_id`),
  ADD KEY `task_id` (`task_id`),
  ADD KEY `type_id` (`type_id`);

--
-- Индексы таблицы `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Индексы таблицы `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Индексы таблицы `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `public_token` (`public_token`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Индексы таблицы `types`
--
ALTER TABLE `types`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT для таблицы `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `following_list`
--
ALTER TABLE `following_list`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT для таблицы `goals`
--
ALTER TABLE `goals`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=449;

--
-- AUTO_INCREMENT для таблицы `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT для таблицы `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT для таблицы `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=135;

--
-- AUTO_INCREMENT для таблицы `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=151;

--
-- AUTO_INCREMENT для таблицы `types`
--
ALTER TABLE `types`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `following_list`
--
ALTER TABLE `following_list`
  ADD CONSTRAINT `following_list_ibfk_1` FOREIGN KEY (`subscriber_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `following_list_ibfk_2` FOREIGN KEY (`subscribed_to_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `goals`
--
ALTER TABLE `goals`
  ADD CONSTRAINT `goals_ibfk_1` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_2` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `notifications_ibfk_3` FOREIGN KEY (`type_id`) REFERENCES `types` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `notifications_ibfk_4` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `tasks_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tasks_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
