-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1
-- Время создания: Ноя 15 2026 г., 18:21
-- Версия сервера: 10.4.32-MariaDB
-- Версия PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `autorent`
--

-- --------------------------------------------------------

--
-- Структура таблицы `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `car_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `cars`
--

CREATE TABLE `cars` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `brand` varchar(50) NOT NULL,
  `model` varchar(50) NOT NULL,
  `year` int(11) NOT NULL,
  `price_per_day` decimal(10,2) NOT NULL,
  `description` text DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'available',
  `transmission` varchar(20) NOT NULL DEFAULT 'Автомат',
  `fuel` varchar(20) NOT NULL DEFAULT 'Бензин',
  `seats` int(11) NOT NULL DEFAULT 5,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `cars`
--

INSERT INTO `cars` (`id`, `category_id`, `brand`, `model`, `year`, `price_per_day`, `description`, `status`, `transmission`, `fuel`, `seats`, `created_at`) VALUES
(1, 1, 'Toyota', 'Camry', 2022, 18000.00, 'Таза, күтімі жақсы көлік қалалық және қалааралық сапарларға.', 'available', 'Автомат', 'Бензин', 5, '2026-11-15 17:18:55'),
(2, 2, 'Toyota', 'Land Cruiser Prado', 2021, 38000.00, 'Таза, күтімі жақсы көлік қалалық және қалааралық сапарларға.', 'available', 'Автомат', 'Дизель', 7, '2026-11-15 17:18:55'),
(3, 2, 'Hyundai', 'Tucson', 2023, 25000.00, 'Таза, күтімі жақсы көлік қалалық және қалааралық сапарларға.', 'available', 'Автомат', 'Бензин', 5, '2026-11-15 17:18:55'),
(4, 1, 'Kia', 'Rio', 2020, 12000.00, 'Таза, күтімі жақсы көлік қалалық және қалааралық сапарларға.', 'available', 'Механика', 'Бензин', 5, '2026-11-15 17:18:55'),
(5, 1, 'Chevrolet', 'Cobalt', 2021, 10000.00, 'Таза, күтімі жақсы көлік қалалық және қалааралық сапарларға.', 'available', 'Автомат', 'Бензин', 5, '2026-11-15 17:18:55'),
(6, 3, 'BMW', '5 Series', 2022, 45000.00, 'Таза, күтімі жақсы көлік қалалық және қалааралық сапарларға.', 'available', 'Автомат', 'Бензин', 5, '2026-11-15 17:18:55'),
(7, 4, 'Mercedes-Benz', 'V-Class', 2021, 60000.00, 'Таза, күтімі жақсы көлік қалалық және қалааралық сапарларға.', 'available', 'Автомат', 'Дизель', 7, '2026-11-15 17:18:55'),
(8, 1, 'Skoda', 'Octavia', 2022, 20000.00, 'Таза, күтімі жақсы көлік қалалық және қалааралық сапарларға.', 'available', 'Автомат', 'Бензин', 5, '2026-11-15 17:18:55'),
(9, 2, 'Kia', 'Sportage', 2023, 27000.00, 'Таза, күтімі жақсы көлік қалалық және қалааралық сапарларға.', 'available', 'Автомат', 'Бензин', 5, '2026-11-15 17:18:55');

-- --------------------------------------------------------

--
-- Структура таблицы `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `categories`
--

INSERT INTO `categories` (`id`, `name`) VALUES
(2, 'Кроссовер'),
(4, 'Минивэн'),
(3, 'Премиум'),
(1, 'Седан');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(20) NOT NULL DEFAULT 'user',
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `phone`, `password`, `role`, `created_at`) VALUES
(1, 'Aselya Ernazarova', 'aselyaernazarova09@gmail.com', '+77472335371', '$2y$10$/ZaiK88bXdGAQJ/46gwP4.GZ/yLBSHw.ifHuYMhF0B3/VRcqsAApu', 'user', '2026-11-15 17:20:53');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `car_id` (`car_id`);

--
-- Индексы таблицы `cars`
--
ALTER TABLE `cars`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Индексы таблицы `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `cars`
--
ALTER TABLE `cars`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT для таблицы `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`car_id`) REFERENCES `cars` (`id`);

--
-- Ограничения внешнего ключа таблицы `cars`
--
ALTER TABLE `cars`
  ADD CONSTRAINT `cars_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
