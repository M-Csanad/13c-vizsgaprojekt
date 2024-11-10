-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Gép: 127.0.0.1:3307
-- Létrehozás ideje: 2024. Nov 10. 11:45
-- Kiszolgáló verziója: 10.4.32-MariaDB
-- PHP verzió: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Adatbázis: `florens_botanica`
--
CREATE DATABASE IF NOT EXISTS `florens_botanica` DEFAULT CHARACTER SET utf8 COLLATE utf8_hungarian_ci;
USE `florens_botanica`;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `allergy`
--

CREATE TABLE `allergy` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `icon_uri` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `category`
--

CREATE TABLE `category` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `subname` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `thumbnail_image_vertical_uri` varchar(255) DEFAULT NULL,
  `thumbnail_image_horizontal_uri` varchar(255) DEFAULT NULL,
  `thumbnail_video_uri` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;

--
-- A tábla adatainak kiíratása `category`
--

INSERT INTO `category` (`id`, `name`, `subname`, `description`, `thumbnail_image_vertical_uri`, `thumbnail_image_horizontal_uri`, `thumbnail_video_uri`) VALUES
(2, 'Immunerősítés', 'Természetes megoldások az immunrendszer támogatására', 'Olyan termékek, amelyek segíthetnek az immunrendszer erősítésében és a szervezet védekezőképességének fokozásában.', './images/categories/immunerősítés/thumbnail_image_vertical.jpg', './images/categories/immunerősítés/thumbnail_image_horizontal.jpg', NULL),
(3, 'Emésztés és Bélrendszer', 'Gyógynövények az emésztőrendszer egészségéért', 'Termékek, amelyek hozzájárulhatnak az emésztési folyamatok javításához és a bélflóra egyensúlyának fenntartásához.', './images/categories/emésztés-és-bélrendszer/thumbnail_image_vertical.jpg', './images/categories/emésztés-és-bélrendszer/thumbnail_image_horizontal.jpg', NULL),
(4, 'Stressz és Alvás', 'Nyugalmat és pihentető alvást támogató gyógynövények', 'Nyugtató és alvást segítő készítmények, amelyek hozzájárulhatnak a mindennapi stressz enyhítéséhez.', './images/categories/stressz-és-alvás/thumbnail_image_vertical.jpg', './images/categories/stressz-és-alvás/thumbnail_image_horizontal.jpg', NULL),
(5, 'Szív és Keringés', 'A szív és az érrendszer támogatása gyógynövényekkel', 'Termékek, amelyek hozzájárulhatnak a szív- és érrendszer egészségének fenntartásához.', './images/categories/szív-és-keringés/thumbnail_image_vertical.jpg', './images/categories/szív-és-keringés/thumbnail_image_horizontal.jpg', NULL),
(22, 'asd', 'asd', 'asd', './images/categories/asd/thumbnail_image_vertical.jpg', './images/categories/asd/thumbnail_image_horizontal.jpg', NULL);

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `delivery_info`
--

CREATE TABLE `delivery_info` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `phone` int(11) DEFAULT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `zip` int(11) DEFAULT NULL,
  `billing_address` varchar(255) DEFAULT NULL,
  `delivery_address` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `image`
--

CREATE TABLE `image` (
  `id` int(11) NOT NULL,
  `uri` varchar(255) NOT NULL,
  `orientation` varchar(10) NOT NULL,
  `media_type` varchar(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `order`
--

CREATE TABLE `order` (
  `session_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL COMMENT 'Vendég rendelések miatt lehet NULL is a user_id',
  `email` varchar(255) DEFAULT NULL,
  `phone` int(11) DEFAULT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `zip` int(11) DEFAULT NULL,
  `billing_address` varchar(255) DEFAULT NULL,
  `delivery_address` varchar(255) DEFAULT NULL,
  `completed_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'NULL, ha nyitott a rendelés',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `product`
--

CREATE TABLE `product` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `unit_price` int(11) DEFAULT NULL,
  `stock` int(11) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `product_allergy`
--

CREATE TABLE `product_allergy` (
  `id` int(11) NOT NULL,
  `allergy_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `product_image`
--

CREATE TABLE `product_image` (
  `id` int(11) NOT NULL,
  `image_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `product_page`
--

CREATE TABLE `product_page` (
  `id` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `last_modified` datetime DEFAULT current_timestamp(),
  `product_id` int(11) DEFAULT NULL,
  `link_slug` varchar(255) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL COMMENT 'On delete: SET NULL',
  `subcategory_id` int(11) DEFAULT NULL COMMENT 'On delete: SET NULL',
  `page_title` varchar(255) DEFAULT NULL,
  `page_content` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `review`
--

CREATE TABLE `review` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `rating` double DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `subcategory`
--

CREATE TABLE `subcategory` (
  `id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL COMMENT 'On delete: SET NULL',
  `name` varchar(255) DEFAULT NULL,
  `subname` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `thumbnail_image_vertical_uri` varchar(255) DEFAULT NULL,
  `thumbnail_image_horizontal_uri` varchar(255) DEFAULT NULL,
  `thumbnail_video_uri` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;

--
-- A tábla adatainak kiíratása `subcategory`
--

INSERT INTO `subcategory` (`id`, `category_id`, `name`, `subname`, `description`, `thumbnail_image_vertical_uri`, `thumbnail_image_horizontal_uri`, `thumbnail_video_uri`) VALUES
(5, 2, 'Vitaminok és Ásványi Anyagok', 'Az immunrendszer alappillérei', 'A megfelelő vitamin- és ásványi anyag szint fenntartása elengedhetetlen az egészséges immunrendszerhez. Fókuszban a C-vitamin, D-vitamin, cink és szelén.', './images/categories/immunerősítés/vitaminok-és-ásványi-anyagok/thumbnail_image_vertical.jpg', './images/categories/immunerősítés/vitaminok-és-ásványi-anyagok/thumbnail_image_horizontal.jpg', NULL),
(6, 3, 'Probiotikumok és Prebiotikumok', 'Az egészséges bélflóra titka', 'A probiotikumok és prebiotikumok segítik a bélflóra egyensúlyának megőrzését, ami az emésztőrendszer megfelelő működéséhez és az immunitás fenntartásához szükséges.', './images/categories/emésztés-és-bélrendszer/probiotikumok-és-prebiotikumok/thumbnail_image_vertical.jpg', './images/categories/emésztés-és-bélrendszer/probiotikumok-és-prebiotikumok/thumbnail_image_horizontal.jpg', NULL);

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `user_name` varchar(255) DEFAULT NULL,
  `password_hash` varchar(255) DEFAULT NULL,
  `role` varchar(255) DEFAULT 'Guest',
  `cookie_id` varchar(64) DEFAULT NULL,
  `cookie_expires_at` int(11) DEFAULT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;

--
-- A tábla adatainak kiíratása `user`
--

INSERT INTO `user` (`id`, `email`, `user_name`, `password_hash`, `role`, `cookie_id`, `cookie_expires_at`, `first_name`, `last_name`, `created_at`) VALUES
(1, '13c-blank@ipari.vein.hu', 'admin', '$2y$10$ZkZHsn0wcwOPzVBBFXfqTe0tjYFoUyLWc2OS/3L5t.fD1ag.FJFrm', 'Administrator', NULL, NULL, NULL, NULL, '2024-11-02 13:27:24'),
(2, 'teszt.elek@gmail.com', 'teszt-elek', '$2y$10$UVG8PY.3iPVbbTZzaYFtHePxgt2u2fu53oXPEuZ3sBXNaO3eGNzGe', 'Guest', NULL, NULL, NULL, NULL, '2024-11-02 19:55:36');

--
-- Indexek a kiírt táblákhoz
--

--
-- A tábla indexei `allergy`
--
ALTER TABLE `allergy`
  ADD PRIMARY KEY (`id`);

--
-- A tábla indexei `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cart_ibfk_1` (`order_id`),
  ADD KEY `cart_ibfk_2` (`product_id`);

--
-- A tábla indexei `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`);

--
-- A tábla indexei `delivery_info`
--
ALTER TABLE `delivery_info`
  ADD PRIMARY KEY (`id`),
  ADD KEY `delivery_info_ibfk_1` (`user_id`);

--
-- A tábla indexei `image`
--
ALTER TABLE `image`
  ADD PRIMARY KEY (`id`);

--
-- A tábla indexei `order`
--
ALTER TABLE `order`
  ADD PRIMARY KEY (`session_id`),
  ADD KEY `order_ibfk_1` (`user_id`);

--
-- A tábla indexei `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`id`);

--
-- A tábla indexei `product_allergy`
--
ALTER TABLE `product_allergy`
  ADD PRIMARY KEY (`id`),
  ADD KEY `allergy_id` (`allergy_id`),
  ADD KEY `product_id` (`product_id`);

--
-- A tábla indexei `product_image`
--
ALTER TABLE `product_image`
  ADD PRIMARY KEY (`id`),
  ADD KEY `image_id` (`image_id`),
  ADD KEY `product_id` (`product_id`);

--
-- A tábla indexei `product_page`
--
ALTER TABLE `product_page`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_page_ibfk_1` (`product_id`),
  ADD KEY `product_page_ibfk_2` (`category_id`),
  ADD KEY `product_page_ibfk_3` (`subcategory_id`);

--
-- A tábla indexei `review`
--
ALTER TABLE `review`
  ADD PRIMARY KEY (`id`),
  ADD KEY `review_ibfk_1` (`user_id`),
  ADD KEY `review_ibfk_2` (`product_id`);

--
-- A tábla indexei `subcategory`
--
ALTER TABLE `subcategory`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subcategory_ibfk_1` (`category_id`);

--
-- A tábla indexei `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `user_name` (`user_name`);

--
-- A kiírt táblák AUTO_INCREMENT értéke
--

--
-- AUTO_INCREMENT a táblához `allergy`
--
ALTER TABLE `allergy`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT a táblához `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT a táblához `category`
--
ALTER TABLE `category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT a táblához `delivery_info`
--
ALTER TABLE `delivery_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT a táblához `image`
--
ALTER TABLE `image`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;

--
-- AUTO_INCREMENT a táblához `product`
--
ALTER TABLE `product`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT a táblához `product_allergy`
--
ALTER TABLE `product_allergy`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT a táblához `product_image`
--
ALTER TABLE `product_image`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- AUTO_INCREMENT a táblához `product_page`
--
ALTER TABLE `product_page`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT a táblához `review`
--
ALTER TABLE `review`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT a táblához `subcategory`
--
ALTER TABLE `subcategory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT a táblához `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Megkötések a kiírt táblákhoz
--

--
-- Megkötések a táblához `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `order` (`session_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Megkötések a táblához `delivery_info`
--
ALTER TABLE `delivery_info`
  ADD CONSTRAINT `delivery_info_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Megkötések a táblához `order`
--
ALTER TABLE `order`
  ADD CONSTRAINT `order_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Megkötések a táblához `product_allergy`
--
ALTER TABLE `product_allergy`
  ADD CONSTRAINT `product_allergy_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`),
  ADD CONSTRAINT `product_allergy_ibfk_2` FOREIGN KEY (`allergy_id`) REFERENCES `allergy` (`id`);

--
-- Megkötések a táblához `product_image`
--
ALTER TABLE `product_image`
  ADD CONSTRAINT `product_image_ibfk_1` FOREIGN KEY (`image_id`) REFERENCES `image` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `product_image_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Megkötések a táblához `product_page`
--
ALTER TABLE `product_page`
  ADD CONSTRAINT `product_page_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `product_page_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `product_page_ibfk_3` FOREIGN KEY (`subcategory_id`) REFERENCES `subcategory` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Megkötések a táblához `review`
--
ALTER TABLE `review`
  ADD CONSTRAINT `review_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `review_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Megkötések a táblához `subcategory`
--
ALTER TABLE `subcategory`
  ADD CONSTRAINT `subcategory_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
