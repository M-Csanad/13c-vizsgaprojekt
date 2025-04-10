-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Gép: 127.0.0.1:3307
-- Létrehozás ideje: 2025. Ápr 10. 21:56
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
DROP TABLE IF EXISTS `florens_botanica`
CREATE DATABASE IF NOT EXISTS `florens_botanica` DEFAULT CHARACTER SET utf8 COLLATE utf8_hungarian_ci;
USE `florens_botanica`;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `autofill_billing`
--

DROP TABLE IF EXISTS `autofill_billing`;
CREATE TABLE `autofill_billing` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `zip` int(11) NOT NULL,
  `city` varchar(255) NOT NULL,
  `street_house` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `autofill_delivery`
--

DROP TABLE IF EXISTS `autofill_delivery`;
CREATE TABLE `autofill_delivery` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `zip` int(11) NOT NULL,
  `city` varchar(255) NOT NULL,
  `street_house` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;

--
-- A tábla adatainak kiíratása `autofill_delivery`
--

INSERT INTO `autofill_delivery` (`id`, `user_id`, `name`, `zip`, `city`, `street_house`) VALUES
(1, 3, 'Szenegál', 8444, 'Szenegál', 'Valami Random utca 420.');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `avatar`
--

DROP TABLE IF EXISTS `avatar`;
CREATE TABLE `avatar` (
  `id` int(11) NOT NULL,
  `uri` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;

--
-- A tábla adatainak kiíratása `avatar`
--

INSERT INTO `avatar` (`id`, `uri`) VALUES
(1, 'http://localhost/fb-auth/assets/avatars/female1.jpg'),
(2, 'http://localhost/fb-auth/assets/avatars/female2.jpg'),
(3, 'http://localhost/fb-auth/assets/avatars/female3.jpg'),
(4, 'http://localhost/fb-auth/assets/avatars/female4.jpg'),
(5, 'http://localhost/fb-auth/assets/avatars/male1.jpg'),
(6, 'http://localhost/fb-auth/assets/avatars/male2.jpg'),
(7, 'http://localhost/fb-auth/assets/avatars/male3.jpg'),
(8, 'http://localhost/fb-auth/assets/avatars/male4.jpg'),
(9, 'http://localhost/fb-auth/assets/avatars/anonym.jpg');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `cart`
--

DROP TABLE IF EXISTS `cart`;
CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `product_id` int(11) NOT NULL,
  `page_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `modified_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;

--
-- A tábla adatainak kiíratása `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `product_id`, `page_id`, `quantity`, `created_at`, `modified_at`) VALUES
(80, 10, 14, 25, 1, '2025-04-09 20:05:07', '2025-04-09 20:05:07'),
(85, 10, 7, 9, 1, '2025-04-09 20:06:22', '2025-04-09 20:06:22'),
(122, 10, 25, 50, 1, '2025-04-09 20:14:08', '2025-04-09 20:14:08');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `category`
--

DROP TABLE IF EXISTS `category`;
CREATE TABLE `category` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `subname` varchar(255) DEFAULT NULL,
  `description` varchar(500) DEFAULT NULL,
  `thumbnail_image_vertical_uri` varchar(255) DEFAULT NULL,
  `thumbnail_image_horizontal_uri` varchar(255) DEFAULT NULL,
  `thumbnail_video_uri` varchar(255) DEFAULT NULL,
  `product_count` int(11) NOT NULL DEFAULT 0,
  `slug` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;

--
-- A tábla adatainak kiíratása `category`
--

INSERT INTO `category` (`id`, `name`, `subname`, `description`, `thumbnail_image_vertical_uri`, `thumbnail_image_horizontal_uri`, `thumbnail_video_uri`, `product_count`, `slug`) VALUES
(1, 'A Tiszta Egészség', 'A Természet Esszenciája', 'Támogasd szervezetedet a természet legjavával! Vitaminok, ásványi anyagok, és növényi kivonatok gondoskodnak az energikus mindennapokról, miközben természetes forrásokból származó kiegészítők segítenek megőrizni egészséged harmóniáját. Válaszd a természet', 'http://localhost/fb-content/fb-categories/media/images/category-1/thumbnail_image_vertical.jpg', 'http://localhost/fb-content/fb-categories/media/images/category-1/thumbnail_image_horizontal.jpg', NULL, 8, 'a-tiszta-egeszseg'),
(2, 'A Nyugodt Elme', 'A Nyugalom Forrása', 'Találd meg a belső békédet természetes megoldásokkal! Ebben a kategóriában mindent megtalálsz, ami támogatja a relaxációt, csökkenti a stresszt és segít az éjszakai pihenésben, hogy minden nap energikusan és kiegyensúlyozottan induljon. Fedezd fel a nyuga', 'http://localhost/fb-content/fb-categories/media/images/category-2/thumbnail_image_vertical.jpg', 'http://localhost/fb-content/fb-categories/media/images/category-2/thumbnail_image_horizontal.jpg', NULL, 13, 'a-nyugodt-elme'),
(3, 'A Detox Ereje', 'Méregtelenítés', 'Adj új lendületet testednek természetes méregtelenítő megoldásokkal! Ebben a kategóriában hatékony, természetes eszközöket találsz, amelyek segítenek a belső tisztulásban, támogatják a májat, vesét és az emésztőrendszert, hogy szervezeted felfrissüljön és', 'http://localhost/fb-content/fb-categories/media/images/category-3/thumbnail_image_vertical.jpg', 'http://localhost/fb-content/fb-categories/media/images/category-3/thumbnail_image_horizontal.jpg', NULL, 0, 'a-detox-ereje'),
(4, 'A Könnyed Emésztés', 'Az Anyagcsere', 'Támogasd tested természetes egyensúlyát! Ebben a kategóriában hatékony megoldásokat találsz, amelyek serkentik az anyagcserét, javítják az emésztést, és hozzájárulnak a megfelelő rostbevitelhez, hogy energikus és kiegyensúlyozott lehess minden nap.', 'http://localhost/fb-content/fb-categories/media/images/category-4/thumbnail_image_vertical.jpg', 'http://localhost/fb-content/fb-categories/media/images/category-4/thumbnail_image_horizontal.jpg', NULL, 5, 'a-konnyed-emesztes'),
(5, 'A Mozgás Ereje', 'A Mozgás Forrása', 'Turbózd fel teljesítményed természetes, erőt adó megoldásokkal! Ebben a kategóriában megtalálod mindazt, ami támogatja az aktív életmódot, növeli az energiát és segíti a regenerációt – természetes alapanyagokból, hogy minden mozdulat könnyed legyen és hat', 'http://localhost/fb-content/fb-categories/media/images/category-5/thumbnail_image_vertical.jpg', 'http://localhost/fb-content/fb-categories/media/images/category-5/thumbnail_image_horizontal.jpg', NULL, 12, 'a-mozgas-ereje'),
(6, 'A Pillanat Lángja', 'A Szenvedély Ereje', 'Ébreszd fel a belső tüzed a természet erejével! Ebben a kategóriában olyan különleges gyógynövények, aromaterápiás esszenciák és tápláló kiegészítők várnak rád, amelyek támogatják az érzelmi harmóniát, fokozzák az energiát és új lendületet adnak a mindennapoknak.', 'http://localhost/fb-content/fb-categories/media/images/category-6/thumbnail_image_vertical.jpg', 'http://localhost/fb-content/fb-categories/media/images/category-6/thumbnail_image_horizontal.jpg', NULL, 15, 'a-pillanat-langja'),
(7, 'A Szépség Titka', 'Az Fiatalos Szépség', 'Fedezd fel a ragyogó megjelenés titkát! Ebben a kategóriában bőrápoló, hajápoló és anti-aging megoldások várnak, amelyek természetes összetevőikkel támogatják a fiatalos szépséget és az egészséges ragyogást. Érd el a szépség új dimenzióját!', 'http://localhost/fb-content/fb-categories/media/images/category-7/thumbnail_image_vertical.jpg', 'http://localhost/fb-content/fb-categories/media/images/category-7/thumbnail_image_horizontal.jpg', NULL, 1, 'a-szepseg-titka'),
(8, 'Az Energia Alapjai', 'A Vitalitás Forrásai', 'Töltsd fel szervezeted a legfontosabb tápanyagokkal! Ebben a kategóriában vitaminok, aminosavak és enzimek kínálnak természetes támogatást az energiád fenntartásához és a mindennapi vitalitás eléréséhez. Érezd a különbséget, amit a természet ereje adhat!', 'http://localhost/fb-content/fb-categories/media/images/category-8/thumbnail_image_vertical.jpg', 'http://localhost/fb-content/fb-categories/media/images/category-8/thumbnail_image_horizontal.jpg', NULL, 8, 'az-energia-alapjai'),
(9, 'A Frissítő Élmény', 'A Frissesség Titka', 'Élvezd a természetesen frissítő italok és ízek világát! Ebben a kategóriában teák, kávék és gyümölcs alapú italok kínálnak energiát, vitalitást és egy kis kényeztetést, hogy felfrissülve nézhess szembe a nap kihívásaival.', 'http://localhost/fb-content/fb-categories/media/images/category-9/thumbnail_image_vertical.jpg', 'http://localhost/fb-content/fb-categories/media/images/category-9/thumbnail_image_horizontal.jpg', NULL, 0, 'a-frissito-elmeny'),
(10, 'A Természet Elixirjei', 'Az Egészség Esszenciája', 'Ismerd meg a természet rejtett kincseit! Ebben a kategóriában egzotikus gyógynövények, tinktúrák és különleges elixírek segítenek támogatni a vitalitást és a belső harmóniát, hogy a mindennapjaid energikusabbak és kiegyensúlyozottabbak legyenek.', 'http://localhost/fb-content/fb-categories/media/images/category-10/thumbnail_image_vertical.jpg', 'http://localhost/fb-content/fb-categories/media/images/category-10/thumbnail_image_horizontal.jpg', NULL, 0, 'a-termeszet-elixirjei'),
(11, 'A Tengerek Kincsei', 'Az Óceán Ereje', 'Meríts erőt a tengerek gazdagságából! Ebben a kategóriában tengeri algák, ásványok, halolajok és kollagén alapú kiegészítők várnak rád, hogy természetes támogatást nyújtsanak az egészséghez és a vitalitáshoz. Fedezd fel az óceán erejét!', 'http://localhost/fb-content/fb-categories/media/images/category-11/thumbnail_image_vertical.jpg', 'http://localhost/fb-content/fb-categories/media/images/category-11/thumbnail_image_horizontal.jpg', NULL, 0, 'a-tengerek-kincsei'),
(12, 'A Természet Illatai', 'A Lélek Illatai', 'Engedd, hogy a természet illatai harmóniát és nyugalmat hozzanak az életedbe! Ebben a kategóriában illóolajok, füstölők és illatgyertyák segítenek a relaxációban, a stresszoldásban és a tér energiájának megújításában. Fedezd fel a lélek illatainak erejét!', 'http://localhost/fb-content/fb-categories/media/images/category-12/thumbnail_image_vertical.jpg', 'http://localhost/fb-content/fb-categories/media/images/category-12/thumbnail_image_horizontal.jpg', NULL, 0, 'a-termeszet-illatai'),
(13, 'A Konyha Ízei', 'Az Ízek Harmóniája', 'Hozd ki a legtöbbet a konyhából természetes fűszerekkel és gyógyhatású kiegészítőkkel! Ebben a kategóriában ízletes és egészséges megoldások várnak, amelyek nemcsak ételeidet teszik különlegessé, hanem az egészségedet is támogatják.', 'http://localhost/fb-content/fb-categories/media/images/category-13/thumbnail_image_vertical.jpg', 'http://localhost/fb-content/fb-categories/media/images/category-13/thumbnail_image_horizontal.jpg', NULL, 3, 'a-konyha-izei'),
(14, 'A Szív Egészsége', 'A Szív Ereje', 'Támogasd szíved egészségét természetes megoldásokkal! Ebben a kategóriában olyan kiegészítőket találsz, amelyek segítik a keringést, erősítik az érrendszert és hozzájárulnak a szív optimális működéséhez. Adj lendületet az élet ritmusának!', 'http://localhost/fb-content/fb-categories/media/images/category-14/thumbnail_image_vertical.jpg', 'http://localhost/fb-content/fb-categories/media/images/category-14/thumbnail_image_horizontal.jpg', NULL, 3, 'a-sziv-egeszsege'),
(15, 'Az Erdő Ajándéka', 'Az Erdő Kincsei', 'Fedezd fel az erdő gazdagságát! Ebben a kategóriában erdei gombák, gyógynövények, gyümölcsök, mézek és aromaterápiás termékek várnak, hogy természetes módon támogassák egészségedet és kényeztessék érzékeidet. Hozd el otthonodba az erdő ajándékait!', 'http://localhost/fb-content/fb-categories/media/images/category-15/thumbnail_image_vertical.jpg', 'http://localhost/fb-content/fb-categories/media/images/category-15/thumbnail_image_horizontal.jpg', NULL, 1, 'az-erdo-ajandeka');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `health_effect`
--

DROP TABLE IF EXISTS `health_effect`;
CREATE TABLE `health_effect` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` varchar(255) NOT NULL,
  `benefit` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;

--
-- A tábla adatainak kiíratása `health_effect`
--

INSERT INTO `health_effect` (`id`, `name`, `description`, `benefit`) VALUES
(1, 'Immunerősítő', 'Segíti az immunrendszer erősítését, növeli a szervezet ellenálló képességét a fertőzésekkel szemben.', 1),
(2, 'Gyulladáscsökkentő', 'Segít csökkenteni a gyulladást a testben, enyhíti a fájdalmat és a duzzanatot.', 1),
(3, 'Emésztést segítő', 'Támogatja az emésztési folyamatokat, segíthet a puffadás, gyomorégés és egyéb emésztési problémák enyhítésében.', 1),
(4, 'Stresszoldó', 'Segít a stressz csökkentésében, nyugtatja az elmét és elősegíti a mélyebb pihenést.', 1),
(5, 'Fájdalomcsillapító', 'Enyhíti a különböző fájdalmakat, mint például az ízületi fájdalmak, fejfájás és izomfájdalmak.', 1),
(6, 'Antioxidáns', 'Segít semlegesíteni a szabad gyököket, amelyek hozzájárulhatnak az öregedéshez és a betegségek kialakulásához.', 1),
(7, 'Bélflóra támogató', 'Segíti az egészséges bélflóra fenntartását, elősegíti a normál emésztést és csökkentheti a bélrendszeri problémákat.', 1),
(8, 'Hormonális egyensúly', 'Segít fenntartani a hormonális egyensúlyt, különösen a menstruációs ciklus és a menopauza során.', 1),
(9, 'Bőrápoló', 'Támogatja a bőr egészségét, segíthet a bőrproblémák, mint az akne vagy az ekcéma kezelésében.', 1),
(10, 'Keringést javító', 'Segíti a vérkeringést, csökkentheti a magas vérnyomást, és támogathatja a szív- és érrendszer egészségét.', 1),
(11, 'Mérgező hatású', 'Túlzott mennyiségben vagy nem megfelelő módon alkalmazva mérgezést okozhat.', 0),
(12, 'Bélproblémákat okozó', 'Néhány hatás, mint például puffadás, hasmenés vagy székrekedés jelentkezhet érzékeny emésztőrendszerű egyéneknél.', 0),
(13, 'Allergiás reakció', 'Allergiás reakciókat, például bőrirritációt vagy légzési nehézségeket válthat ki érzékeny embereknél.', 0),
(14, 'Szédülést okozó', 'Néhány hatás szédülést és hányingert eredményezhet, különösen nagy mennyiségben alkalmazva.', 0),
(15, 'Hormonális zűrzavart okozhat', 'Zavarokat okozhat a hormonális rendszerben, például menstruációs rendellenességeket vagy a hormonális egyensúly felborulását.', 0),
(16, 'Magas vérnyomást okozó', 'Néhány hatás növelheti a vérnyomást, különösen azoknál, akik már magas vérnyomással küzdenek.', 0),
(17, 'Koffein hatású', 'Túladagolás esetén álmatlanságot, szorongást és fokozott pulzust okozhat.', 0),
(18, 'Toxikus hatású', 'Bizonyos hatások toxikusak lehetnek, különösen, ha nem megfelelően tárolják vagy alkalmazzák őket.', 0),
(19, 'Fokozott vérzés', 'Néhány hatás növelheti a vérzés kockázatát, különösen műtétek előtt vagy véralvadási problémákkal küzdőknél.', 0),
(20, 'Bélflóra egyensúlyvesztése', 'Néhány hatás károsan befolyásolhatja a bélflóra egyensúlyát, puffadást és hasmenést okozva.', 0),
(21, 'Légzésjavító', 'Segít javítani a légzési funkciókat, enyhíti a légszomjat és elősegíti a tiszta légutakat.', 1),
(22, 'Alvást segítő', 'Támogatja a pihentető alvást, csökkenti az inszomniát és segít az alvási ciklusok normalizálásában.', 1),
(23, 'Vércukor szabályozó', 'Segíti a vércukorszint stabilizálását, különösen cukorbetegség esetén.', 1),
(24, 'Májvédő', 'Segít a máj méregtelenítő funkcióinak javításában, véd a toxinok káros hatásai ellen.', 1),
(25, 'Tüdővédő', 'Segíthet megelőzni a légúti megbetegedéseket, támogatja a tüdő egészségét és tisztulását.', 1),
(26, 'Méregtelenítő', 'Segíti a test méregtelenítő rendszereinek működését, elősegítve a toxinok eltávolítását.', 1),
(27, 'Vérnyomás csökkentő', 'Segít csökkenteni a magas vérnyomást, elősegítve a szív- és érrendszeri egészséget.', 1),
(28, 'Antibakteriális', 'Baktériumölő tulajdonságokkal rendelkezik, segíthet a fertőzések kezelésében és megelőzésében.', 1),
(29, 'Antivirális', 'Segít a vírusok elleni védekezésben, erősíti a szervezet ellenálló képességét a vírusos megbetegedésekkel szemben.', 1),
(30, 'Sebgyógyító', 'Támogatja a sebgyógyulást, csökkenti a hegesedést és elősegíti a gyors regenerálódást.', 1),
(31, 'Hányingert okozó', 'Néhány hatás hányingert válthat ki, különösen üres gyomorra fogyasztva.', 0),
(32, 'Kábító hatású', 'Néhány hatás kábító érzést okozhat, ami szédülést vagy koncentrációzavart eredményezhet.', 0),
(33, 'Alacsony vérnyomást okozó', 'Segíthet csökkenteni a vérnyomást, de túladagolás esetén szédülést vagy ájulást is okozhat.', 0),
(34, 'Gyakori vizelést okozó', 'Bizonyos hatások fokozhatják a vizeletürítést, ami kényelmetlen lehet, különösen éjszaka.', 0),
(35, 'Bélflóra diszbiózis', 'Túlzott alkalmazás esetén felboríthatja a bélflóra egyensúlyát, puffadást és hasmenést okozva.', 0),
(36, 'Fokozott izzadás', 'Bizonyos hatások fokozhatják az izzadást, különösen nagyobb adagok esetén.', 0),
(37, 'Szívritmuszavart okozó', 'Néhány hatás befolyásolhatja a szívritmust, különösen szívproblémákkal küzdőknél.', 0),
(38, 'Fejfájást okozó', 'Bizonyos hatások, például túlzott mennyiségben alkalmazva, fejfájást válthatnak ki.', 0),
(39, 'Túlérzékenység', 'Néhány hatás bőrirritációt és egyéb allergiás reakciókat válthat ki, például viszketést vagy bőrpírt.', 0),
(40, 'Energizáló', 'Fokozza a test és elme energiaszintjét, csökkenti a fáradtságot és javítja a koncentrációt. Támogatja a fizikai teljesítményt és segít a frissesség fenntartásában.', 1);

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `image`
--

DROP TABLE IF EXISTS `image`;
CREATE TABLE `image` (
  `id` int(11) NOT NULL,
  `uri` varchar(255) NOT NULL,
  `orientation` varchar(10) NOT NULL,
  `media_type` varchar(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;

--
-- A tábla adatainak kiíratása `image`
--

INSERT INTO `image` (`id`, `uri`, `orientation`, `media_type`) VALUES
(1, 'http://localhost/fb-content/fb-products/media/images/product-3/thumbnail.jpg', 'horizontal', 'image'),
(2, 'http://localhost/fb-content/fb-products/media/images/product-3/image0.jpg', 'vertical', 'image'),
(3, 'http://localhost/fb-content/fb-products/media/images/product-3/image1.jpg', 'horizontal', 'image'),
(4, 'http://localhost/fb-content/fb-products/media/images/product-1/thumbnail.jpg', 'horizontal', 'image'),
(5, 'http://localhost/fb-content/fb-products/media/images/product-1/image0.jpg', 'horizontal', 'image'),
(6, 'http://localhost/fb-content/fb-products/media/images/product-1/image1.jpg', 'vertical', 'image'),
(7, 'http://localhost/fb-content/fb-products/media/images/product-4/thumbnail.jpg', 'horizontal', 'image'),
(8, 'http://localhost/fb-content/fb-products/media/images/product-4/image0.jpg', 'horizontal', 'image'),
(9, 'http://localhost/fb-content/fb-products/media/images/product-4/image1.jpg', 'vertical', 'image'),
(10, 'http://localhost/fb-content/fb-products/media/images/product-5/thumbnail.jpg', 'horizontal', 'image'),
(11, 'http://localhost/fb-content/fb-products/media/images/product-5/image0.jpg', 'vertical', 'image'),
(12, 'http://localhost/fb-content/fb-products/media/images/product-5/image1.jpg', 'horizontal', 'image'),
(13, 'http://localhost/fb-content/fb-products/media/images/product-2/thumbnail.jpg', 'horizontal', 'image'),
(14, 'http://localhost/fb-content/fb-products/media/images/product-2/image0.jpg', 'horizontal', 'image'),
(15, 'http://localhost/fb-content/fb-products/media/images/product-2/image1.jpg', 'vertical', 'image'),
(16, 'http://localhost/fb-content/fb-products/media/images/product-6/thumbnail.jpg', 'horizontal', 'image'),
(17, 'http://localhost/fb-content/fb-products/media/images/product-6/image0.jpg', 'vertical', 'image'),
(18, 'http://localhost/fb-content/fb-products/media/images/product-6/image1.jpg', 'horizontal', 'image'),
(20, 'http://localhost/fb-content/fb-products/media/images/product-7/image0.jpg', 'vertical', 'image'),
(21, 'http://localhost/fb-content/fb-products/media/images/product-7/image1.jpg', 'horizontal', 'image'),
(22, 'http://localhost/fb-content/fb-products/media/images/product-8/thumbnail.jpg', 'horizontal', 'image'),
(23, 'http://localhost/fb-content/fb-products/media/images/product-8/image0.jpg', 'vertical', 'image'),
(24, 'http://localhost/fb-content/fb-products/media/images/product-8/image1.jpg', 'horizontal', 'image'),
(25, 'http://localhost/fb-content/fb-products/media/images/product-9/thumbnail.jpg', 'horizontal', 'image'),
(26, 'http://localhost/fb-content/fb-products/media/images/product-9/image0.jpg', 'vertical', 'image'),
(27, 'http://localhost/fb-content/fb-products/media/images/product-9/image1.jpg', 'horizontal', 'image'),
(28, 'http://localhost/fb-content/fb-products/media/images/product-10/thumbnail.jpg', 'horizontal', 'image'),
(29, 'http://localhost/fb-content/fb-products/media/images/product-10/image0.jpg', 'vertical', 'image'),
(30, 'http://localhost/fb-content/fb-products/media/images/product-10/image1.jpg', 'horizontal', 'image'),
(31, 'http://localhost/fb-content/fb-products/media/images/product-11/thumbnail.jpg', 'horizontal', 'image'),
(32, 'http://localhost/fb-content/fb-products/media/images/product-11/image0.jpg', 'vertical', 'image'),
(33, 'http://localhost/fb-content/fb-products/media/images/product-11/image1.jpg', 'horizontal', 'image'),
(34, 'http://localhost/fb-content/fb-products/media/images/product-12/thumbnail.jpg', 'horizontal', 'image'),
(35, 'http://localhost/fb-content/fb-products/media/images/product-12/image0.jpg', 'vertical', 'image'),
(36, 'http://localhost/fb-content/fb-products/media/images/product-12/image1.jpg', 'horizontal', 'image'),
(37, 'http://localhost/fb-content/fb-products/media/images/product-13/thumbnail.jpg', 'horizontal', 'image'),
(38, 'http://localhost/fb-content/fb-products/media/images/product-13/image0.jpg', 'vertical', 'image'),
(39, 'http://localhost/fb-content/fb-products/media/images/product-13/image1.jpg', 'horizontal', 'image'),
(40, 'http://localhost/fb-content/fb-products/media/images/product-14/thumbnail.jpg', 'horizontal', 'image'),
(41, 'http://localhost/fb-content/fb-products/media/images/product-14/image0.jpg', 'vertical', 'image'),
(42, 'http://localhost/fb-content/fb-products/media/images/product-14/image1.jpg', 'horizontal', 'image'),
(43, 'http://localhost/fb-content/fb-products/media/images/product-15/thumbnail.jpg', 'horizontal', 'image'),
(44, 'http://localhost/fb-content/fb-products/media/images/product-15/image0.jpg', 'vertical', 'image'),
(45, 'http://localhost/fb-content/fb-products/media/images/product-15/image1.jpg', 'horizontal', 'image'),
(46, 'http://localhost/fb-content/fb-products/media/images/product-16/thumbnail.jpg', 'horizontal', 'image'),
(47, 'http://localhost/fb-content/fb-products/media/images/product-16/image0.jpg', 'vertical', 'image'),
(48, 'http://localhost/fb-content/fb-products/media/images/product-16/image1.jpg', 'horizontal', 'image'),
(49, 'http://localhost/fb-content/fb-products/media/images/product-17/thumbnail.jpg', 'horizontal', 'image'),
(50, 'http://localhost/fb-content/fb-products/media/images/product-17/image0.jpg', 'vertical', 'image'),
(51, 'http://localhost/fb-content/fb-products/media/images/product-17/image1.jpg', 'horizontal', 'image'),
(52, 'http://localhost/fb-content/fb-products/media/images/product-18/thumbnail.jpg', 'horizontal', 'image'),
(53, 'http://localhost/fb-content/fb-products/media/images/product-18/image0.jpg', 'vertical', 'image'),
(54, 'http://localhost/fb-content/fb-products/media/images/product-18/image1.jpg', 'horizontal', 'image'),
(55, 'http://localhost/fb-content/fb-products/media/images/product-7/thumbnail.jpg', 'horizontal', 'image'),
(56, 'http://localhost/fb-content/fb-products/media/images/product-19/thumbnail.jpg', 'horizontal', 'image'),
(57, 'http://localhost/fb-content/fb-products/media/images/product-19/image0.jpg', 'horizontal', 'image'),
(58, 'http://localhost/fb-content/fb-products/media/images/product-19/image1.jpg', 'vertical', 'image'),
(59, 'http://localhost/fb-content/fb-products/media/images/product-19/image2.jpg', 'horizontal', 'image'),
(64, 'http://localhost/fb-content/fb-products/media/images/product-21/thumbnail.jpg', 'horizontal', 'image'),
(65, 'http://localhost/fb-content/fb-products/media/images/product-21/image0.jpg', 'vertical', 'image'),
(66, 'http://localhost/fb-content/fb-products/media/images/product-21/image1.jpg', 'horizontal', 'image'),
(67, 'http://localhost/fb-content/fb-products/media/images/product-22/thumbnail.jpg', 'horizontal', 'image'),
(68, 'http://localhost/fb-content/fb-products/media/images/product-22/image0.png', 'horizontal', 'image'),
(69, 'http://localhost/fb-content/fb-products/media/images/product-22/image1.jpg', 'vertical', 'image'),
(70, 'http://localhost/fb-content/fb-products/media/images/product-23/thumbnail.jpg', 'horizontal', 'image'),
(71, 'http://localhost/fb-content/fb-products/media/images/product-23/image0.jpg', 'horizontal', 'image'),
(72, 'http://localhost/fb-content/fb-products/media/images/product-23/image1.jpg', 'vertical', 'image'),
(73, 'http://localhost/fb-content/fb-products/media/images/product-24/thumbnail.jpg', 'horizontal', 'image'),
(74, 'http://localhost/fb-content/fb-products/media/images/product-24/image0.jpg', 'horizontal', 'image'),
(75, 'http://localhost/fb-content/fb-products/media/images/product-24/image1.jpg', 'vertical', 'image'),
(76, 'http://localhost/fb-content/fb-products/media/images/product-25/thumbnail.jpg', 'horizontal', 'image'),
(77, 'http://localhost/fb-content/fb-products/media/images/product-25/image0.jpg', 'horizontal', 'image'),
(78, 'http://localhost/fb-content/fb-products/media/images/product-25/image1.jpg', 'vertical', 'image'),
(79, 'http://localhost/fb-content/fb-products/media/images/product-26/thumbnail.jpg', 'horizontal', 'image'),
(80, 'http://localhost/fb-content/fb-products/media/images/product-26/image0.jpg', 'horizontal', 'image'),
(81, 'http://localhost/fb-content/fb-products/media/images/product-26/image1.jpg', 'vertical', 'image'),
(82, 'http://localhost/fb-content/fb-products/media/images/product-27/thumbnail.jpg', 'horizontal', 'image'),
(83, 'http://localhost/fb-content/fb-products/media/images/product-27/image0.jpg', 'horizontal', 'image'),
(84, 'http://localhost/fb-content/fb-products/media/images/product-27/image1.jpg', 'vertical', 'image'),
(85, 'http://localhost/fb-content/fb-products/media/images/product-28/thumbnail.jpg', 'horizontal', 'image'),
(86, 'http://localhost/fb-content/fb-products/media/images/product-28/image0.jpg', 'horizontal', 'image'),
(87, 'http://localhost/fb-content/fb-products/media/images/product-28/image1.jpg', 'horizontal', 'image'),
(88, 'http://localhost/fb-content/fb-products/media/images/product-29/thumbnail.jpg', 'horizontal', 'image'),
(89, 'http://localhost/fb-content/fb-products/media/images/product-29/image0.jpg', 'horizontal', 'image'),
(90, 'http://localhost/fb-content/fb-products/media/images/product-30/thumbnail.jpg', 'horizontal', 'image'),
(91, 'http://localhost/fb-content/fb-products/media/images/product-30/image0.jpg', 'horizontal', 'image'),
(92, 'http://localhost/fb-content/fb-products/media/images/product-30/image1.jpg', 'vertical', 'image'),
(93, 'http://localhost/fb-content/fb-products/media/images/product-31/thumbnail.jpg', 'horizontal', 'image'),
(94, 'http://localhost/fb-content/fb-products/media/images/product-31/image0.jpg', 'horizontal', 'image'),
(95, 'http://localhost/fb-content/fb-products/media/images/product-31/image1.jpg', 'vertical', 'image'),
(102, 'http://localhost/fb-content/fb-products/media/images/product-32/thumbnail.jpg', 'horizontal', 'image'),
(103, 'http://localhost/fb-content/fb-products/media/images/product-32/image0.jpg', 'horizontal', 'image'),
(104, 'http://localhost/fb-content/fb-products/media/images/product-32/image1.jpg', 'vertical', 'image'),
(105, 'http://localhost/fb-content/fb-products/media/images/product-34/thumbnail.jpg', 'horizontal', 'image'),
(106, 'http://localhost/fb-content/fb-products/media/images/product-34/image0.jpg', 'horizontal', 'image'),
(107, 'http://localhost/fb-content/fb-products/media/images/product-34/image1.jpg', 'vertical', 'image');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `order`
--

DROP TABLE IF EXISTS `order`;
CREATE TABLE `order` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL COMMENT 'Vendég rendelések miatt lehet NULL is a user_id',
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `tax_number` varchar(20) DEFAULT NULL,
  `billing_address` varchar(255) DEFAULT NULL COMMENT 'NULL, ha megegyezik a szállítási címmel',
  `delivery_address` varchar(255) DEFAULT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'Visszaigazolva',
  `order_total` int(11) NOT NULL DEFAULT 0,
  `completed_at` timestamp NULL DEFAULT NULL COMMENT 'NULL, ha nyitott a rendelés',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;

--
-- A tábla adatainak kiíratása `order`
--

INSERT INTO `order` (`id`, `user_id`, `email`, `phone`, `first_name`, `last_name`, `company_name`, `tax_number`, `billing_address`, `delivery_address`, `status`, `order_total`, `completed_at`, `created_at`) VALUES
(1, 3, '13c-milkovics@ipari.vein.hu', '+36203094010', 'Csanád', 'Milkovics', NULL, NULL, NULL, '8444 Szentgál, Akoj utca 25.', 'Teljesítve', 5000, '2025-03-15 20:02:58', '2025-03-15 20:02:58'),
(2, NULL, 'szilvia.horvath@bot.example.org', '+36208797573', 'Szilvia', 'Horváth', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 82', 'Visszaigazolva', 5000, '2025-03-17 19:14:43', '2025-03-17 19:14:43'),
(4, NULL, 'emese.kovacs@bot.example.org', '+36305882137', 'Emese', 'Kovács', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 86', 'Teljesítve', 4000, '2025-03-17 19:34:37', '2025-03-17 19:34:37'),
(5, NULL, 'ilona.soos@bot.example.com', '+36705721018', 'Ilona', 'Soós', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 98', 'Teljesítve', 5500, '2025-03-17 19:34:37', '2025-03-17 19:34:37'),
(6, NULL, 'lilla.fazekas@bot.example.net', '+36207785351', 'Lilla', 'Fazekas', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 34', 'Teljesítve', 5000, '2025-03-17 19:34:37', '2025-03-17 19:34:37'),
(7, 3, '13c-milkovics@ipari.vein.hu', '+36202254566', 'Csanád', 'Milkovics', NULL, NULL, NULL, '8444 Szenegál, Valami Random utca 420.', 'Teljesítve', 6500, '2025-04-07 19:07:16', '2025-04-07 19:07:16'),
(8, NULL, 'erzsebet.szucs@bot.example.org', '+36705689622', 'Erzsébet', 'Szűcs', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 40', 'Teljesítve', 5500, '2025-04-09 15:29:01', '2025-04-09 15:29:01'),
(9, 10, 'lilla.kiss@bot.example.com', '+36203206443', 'Lilla', 'Kiss', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 49', 'Teljesítve', 5600, '2025-04-09 17:40:01', '2025-04-09 17:40:01'),
(10, 5, 'agnes.szep@bot.example.net', '+36304432387', 'Ágnes', 'Szép', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 7', 'Teljesítve', 4500, '2025-04-09 17:52:18', '2025-04-09 17:52:18'),
(11, 6, 'beata.kiss@bot.example.org', '+36709327586', 'Beáta', 'Kiss', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 6', 'Teljesítve', 5500, '2025-04-09 18:03:15', '2025-04-09 18:03:15'),
(12, 5, 'agnes.szep@bot.example.net', '+36303510433', 'Ágnes', 'Szép', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 61', 'Teljesítve', 5500, '2025-04-09 18:07:00', '2025-04-09 18:07:00'),
(13, 5, 'agnes.szep@bot.example.net', '+36308954390', 'Ágnes', 'Szép', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 56', 'Teljesítve', 3000, '2025-04-09 18:14:32', '2025-04-09 18:14:32'),
(14, 10, 'lilla.kiss@bot.example.com', '+36203547604', 'Lilla', 'Kiss', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 90', 'Teljesítve', 5500, '2025-04-09 18:26:09', '2025-04-09 18:26:09'),
(15, 7, 'maria.deak@bot.example.com', '+36304449693', 'Mária', 'Deák', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 93', 'Teljesítve', 5000, '2025-04-09 18:36:17', '2025-04-09 18:36:17'),
(16, 5, 'agnes.szep@bot.example.net', '+36706994765', 'Ágnes', 'Szép', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 45', 'Visszaigazolva', 5500, '2025-04-09 18:40:11', '2025-04-09 18:40:11'),
(17, 11, 'csilla.farkas@bot.example.com', '+36703712211', 'Csilla', 'Farkas', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 14', 'Teljesítve', 5500, '2025-04-09 18:50:41', '2025-04-09 18:50:41'),
(18, 4, 'kinga.blank@bot.example.org', '+36206498395', 'Kinga', 'Blank', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 69', 'Teljesítve', 6900, '2025-04-09 18:59:47', '2025-04-09 18:59:47'),
(19, 6, 'beata.kiss@bot.example.org', '+36207945168', 'Beáta', 'Kiss', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 35', 'Teljesítve', 4500, '2025-04-09 19:11:10', '2025-04-09 19:11:10'),
(20, 4, 'kinga.blank@bot.example.org', '+36708611439', 'Kinga', 'Blank', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 89', 'Teljesítve', 4500, '2025-04-09 19:11:10', '2025-04-09 19:11:10'),
(21, 7, 'maria.deak@bot.example.com', '+36709371900', 'Mária', 'Deák', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 46', 'Teljesítve', 5000, '2025-04-09 19:12:15', '2025-04-09 19:12:15'),
(22, 9, 'flora.szalai@bot.example.org', '+36706084009', 'Flóra', 'Szalai', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 26', 'Teljesítve', 5000, '2025-04-09 19:12:15', '2025-04-09 19:12:15'),
(23, 6, 'beata.kiss@bot.example.org', '+36308567811', 'Beáta', 'Kiss', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 23', 'Visszaigazolva', 9000, '2025-04-09 19:31:45', '2025-04-09 19:31:45'),
(24, 6, 'beata.kiss@bot.example.org', '+36709674104', 'Beáta', 'Kiss', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 45', 'Visszaigazolva', 9000, '2025-04-09 19:31:45', '2025-04-09 19:31:45'),
(25, 9, 'flora.szalai@bot.example.org', '+36705431016', 'Flóra', 'Szalai', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 28', 'Visszaigazolva', 5000, '2025-04-09 19:31:45', '2025-04-09 19:31:45'),
(26, 4, 'kinga.blank@bot.example.org', '+36202595654', 'Kinga', 'Blank', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 62', 'Visszaigazolva', 6000, '2025-04-09 19:32:51', '2025-04-09 19:32:51'),
(27, 7, 'maria.deak@bot.example.com', '+36707404306', 'Mária', 'Deák', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 18', 'Visszaigazolva', 5500, '2025-04-09 19:32:52', '2025-04-09 19:32:52'),
(28, 10, 'lilla.kiss@bot.example.com', '+36206924366', 'Lilla', 'Kiss', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 12', 'Visszaigazolva', 6200, '2025-04-09 19:32:52', '2025-04-09 19:32:52'),
(29, 10, 'lilla.kiss@bot.example.com', '+36701805136', 'Lilla', 'Kiss', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 58', 'Teljesítve', 5000, '2025-04-09 19:33:57', '2025-04-09 19:33:57'),
(30, 5, 'agnes.szep@bot.example.net', '+36202938780', 'Ágnes', 'Szép', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 8', 'Visszaigazolva', 10300, '2025-04-09 19:33:58', '2025-04-09 19:33:58'),
(31, 5, 'agnes.szep@bot.example.net', '+36706887043', 'Ágnes', 'Szép', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 1', 'Visszaigazolva', 10300, '2025-04-09 19:33:58', '2025-04-09 19:33:58'),
(32, 8, 'mimiviktoria.meszaros@bot.example.com', '+36307718644', 'MimiViktória', 'Mészáros', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 47', 'Visszaigazolva', 13000, '2025-04-09 19:35:03', '2025-04-09 19:35:03'),
(33, 8, 'mimiviktoria.meszaros@bot.example.com', '+36205558244', 'MimiViktória', 'Mészáros', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 19', 'Visszaigazolva', 13000, '2025-04-09 19:35:03', '2025-04-09 19:35:03'),
(34, 5, 'agnes.szep@bot.example.net', '+36309891394', 'Ágnes', 'Szép', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 28', 'Visszaigazolva', 5000, '2025-04-09 19:35:04', '2025-04-09 19:35:04'),
(35, 6, 'beata.kiss@bot.example.org', '+36301454474', 'Beáta', 'Kiss', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 23', 'Visszaigazolva', 9500, '2025-04-09 19:37:19', '2025-04-09 19:37:19'),
(36, 6, 'beata.kiss@bot.example.org', '+36207733882', 'Beáta', 'Kiss', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 47', 'Visszaigazolva', 9500, '2025-04-09 19:37:20', '2025-04-09 19:37:20'),
(37, 4, 'kinga.blank@bot.example.org', '+36208504490', 'Kinga', 'Blank', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 73', 'Visszaigazolva', 6800, '2025-04-09 19:37:21', '2025-04-09 19:37:21'),
(38, 8, 'mimiviktoria.meszaros@bot.example.com', '+36204349298', 'MimiViktória', 'Mészáros', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 34', 'Teljesítve', 4500, '2025-04-09 19:39:42', '2025-04-09 19:39:42'),
(39, 5, 'agnes.szep@bot.example.net', '+36303360988', 'Ágnes', 'Szép', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 99', 'Visszaigazolva', 6000, '2025-04-09 19:39:42', '2025-04-09 19:39:42'),
(40, 6, 'beata.kiss@bot.example.org', '+36707692025', 'Beáta', 'Kiss', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 55', 'Teljesítve', 4500, '2025-04-09 19:40:47', '2025-04-09 19:40:47'),
(41, 5, 'agnes.szep@bot.example.net', '+36205904066', 'Ágnes', 'Szép', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 51', 'Teljesítve', 4500, '2025-04-09 19:40:48', '2025-04-09 19:40:48'),
(42, 26, 'orsolya.rozsa@bot.example.com', '+36207800430', 'Orsolya', 'Rózsa', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 94', 'Visszaigazolva', 4000, '2025-04-09 19:58:44', '2025-04-09 19:58:44'),
(43, 13, 'hedvig.simon@bot.example.com', '+36306898143', 'Hedvig', 'Simon', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 36', 'Visszaigazolva', 5800, '2025-04-09 19:58:45', '2025-04-09 19:58:45'),
(44, 85, 'rozalia.molnar@bot.example.com', '+36203978890', 'Rozália', 'Molnár', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 4', 'Visszaigazolva', 5000, '2025-04-09 19:58:47', '2025-04-09 19:58:47'),
(45, 55, 'edina.kovacs@bot.example.com', '+36202499778', 'Edina', 'Kovács', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 16', 'Visszaigazolva', 5500, '2025-04-09 19:58:47', '2025-04-09 19:58:47'),
(46, 100, 'csilla.nemeth@bot.example.org', '+36307105840', 'Csilla', 'Németh', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 77', 'Teljesítve', 5000, '2025-04-09 19:58:48', '2025-04-09 19:58:48'),
(47, 64, 'barbara.balint@bot.example.com', '+36305870575', 'Barbara', 'Bálint', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 44', 'Visszaigazolva', 5500, '2025-04-09 19:58:48', '2025-04-09 19:58:48'),
(48, 68, 'adrienn.fulop@bot.example.net', '+36203421238', 'Adrienn', 'Fülöp', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 59', 'Visszaigazolva', 4500, '2025-04-09 19:58:49', '2025-04-09 19:58:49'),
(49, 39, 'csilla.kovacs@bot.example.org', '+36706781858', 'Csilla', 'Kovács', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 70', 'Teljesítve', 6300, '2025-04-09 19:58:59', '2025-04-09 19:58:59'),
(50, 51, 'agnes.papp@bot.example.com', '+36705951594', 'Ágnes', 'Papp', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 96', 'Teljesítve', 5000, '2025-04-09 20:00:06', '2025-04-09 20:00:06'),
(51, 58, 'eszter.racz@bot.example.net', '+36306721138', 'Eszter', 'Rácz', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 93', 'Visszaigazolva', 6000, '2025-04-09 20:00:06', '2025-04-09 20:00:06'),
(52, 60, 'eszter.mezei@bot.example.com', '+36304388321', 'Eszter', 'Mezei', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 65', 'Teljesítve', 6300, '2025-04-09 20:00:06', '2025-04-09 20:00:06'),
(53, 19, 'lilla.veres@bot.example.org', '+36706137738', 'Lilla', 'Veres', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 69', 'Teljesítve', 4500, '2025-04-09 20:00:08', '2025-04-09 20:00:08'),
(54, 73, 'piroska.voros@bot.example.com', '+36305449068', 'Piroska', 'Vörös', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 2', 'Teljesítve', 4000, '2025-04-09 20:00:09', '2025-04-09 20:00:09'),
(55, 108, 'iren.simon@bot.example.net', '+36207329519', 'Irén', 'Simon', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 43', 'Visszaigazolva', 5500, '2025-04-09 20:00:10', '2025-04-09 20:00:10'),
(56, 77, 'rozalia.fulop@bot.example.net', '+36701626958', 'Rozália', 'Fülöp', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 36', 'Visszaigazolva', 4200, '2025-04-09 20:00:12', '2025-04-09 20:00:12'),
(57, 48, 'anna.fazekas@bot.example.org', '+36302898614', 'Anna', 'Fazekas', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 61', 'Visszaigazolva', 5800, '2025-04-09 20:00:43', '2025-04-09 20:00:43'),
(58, 28, 'andrea.molnar@bot.example.com', '+36301372129', 'Andrea', 'Molnár', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 11', 'Visszaigazolva', 5000, '2025-04-09 20:01:25', '2025-04-09 20:01:25'),
(59, 85, 'rozalia.molnar@bot.example.com', '+36207113723', 'Rozália', 'Molnár', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 88', 'Teljesítve', 4000, '2025-04-09 20:01:25', '2025-04-09 20:01:25'),
(60, 41, 'nikolett.hegedus@bot.example.org', '+36308511136', 'Nikolett', 'Hegedűs', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 42', 'Visszaigazolva', 5800, '2025-04-09 20:01:26', '2025-04-09 20:01:26'),
(61, 61, 'iren.balogh@bot.example.net', '+36209143558', 'Irén', 'Balogh', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 85', 'Visszaigazolva', 7000, '2025-04-09 20:01:30', '2025-04-09 20:01:30'),
(62, 88, 'noemi.zsiga@bot.example.com', '+36705550986', 'Noémi', 'Zsiga', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 39', 'Teljesítve', 6300, '2025-04-09 20:01:31', '2025-04-09 20:01:31'),
(63, 5, 'agnes.szep@bot.example.net', '+36208655881', 'Ágnes', 'Szép', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 32', 'Visszaigazolva', 6200, '2025-04-09 20:01:32', '2025-04-09 20:01:32'),
(64, 74, 'bernadett.kukso@bot.example.net', '+36305193186', 'Bernadett', 'Kuksó', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 3', 'Visszaigazolva', 6900, '2025-04-09 20:01:33', '2025-04-09 20:01:33'),
(65, 91, 'noemi.fazekas@bot.example.net', '+36202280799', 'Noémi', 'Fazekas', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 23', 'Teljesítve', 6000, '2025-04-09 20:02:02', '2025-04-09 20:02:02'),
(66, 57, 'edina.lukacs@bot.example.com', '+36305646616', 'Edina', 'Lukács', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 79', 'Teljesítve', 4500, '2025-04-09 20:02:38', '2025-04-09 20:02:38'),
(67, 6, 'beata.kiss@bot.example.org', '+36708165407', 'Beáta', 'Kiss', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 16', 'Teljesítve', 5000, '2025-04-09 20:02:40', '2025-04-09 20:02:40'),
(68, 40, 'erika.nemes@bot.example.com', '+36209784164', 'Erika', 'Nemes', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 54', 'Visszaigazolva', 5500, '2025-04-09 20:02:43', '2025-04-09 20:02:43'),
(69, 12, 'lilla.barta@bot.example.org', '+36205624403', 'Lilla', 'Barta', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 51', 'Visszaigazolva', 6900, '2025-04-09 20:02:44', '2025-04-09 20:02:44'),
(70, 110, 'flora.peter@bot.example.net', '+36703221754', 'Flóra', 'Péter', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 28', 'Visszaigazolva', 11300, '2025-04-09 20:02:44', '2025-04-09 20:02:44'),
(71, 110, 'flora.peter@bot.example.net', '+36304997921', 'Flóra', 'Péter', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 6', 'Visszaigazolva', 11300, '2025-04-09 20:02:47', '2025-04-09 20:02:47'),
(72, 94, 'ilona.juhasz@bot.example.net', '+36201725957', 'Ilona', 'Juhász', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 87', 'Teljesítve', 4500, '2025-04-09 20:02:51', '2025-04-09 20:02:51'),
(73, 98, 'edina.soos@bot.example.com', '+36308674679', 'Edina', 'Soós', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 46', 'Visszaigazolva', 6000, '2025-04-09 20:03:15', '2025-04-09 20:03:15'),
(74, 27, 'edina.lakatos@bot.example.com', '+36303448658', 'Edina', 'Lakatos', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 24', 'Visszaigazolva', 5990, '2025-04-09 20:03:49', '2025-04-09 20:03:49'),
(75, 56, 'katalin.fulop@bot.example.org', '+36204599886', 'Katalin', 'Fülöp', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 56', 'Visszaigazolva', 5000, '2025-04-09 20:03:53', '2025-04-09 20:03:53'),
(76, 22, 'zsuzsanna.voros@bot.example.org', '+36202030547', 'Zsuzsanna', 'Vörös', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 45', 'Teljesítve', 4500, '2025-04-09 20:03:53', '2025-04-09 20:03:53'),
(77, 26, 'orsolya.rozsa@bot.example.com', '+36201770589', 'Orsolya', 'Rózsa', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 16', 'Teljesítve', 6800, '2025-04-09 20:03:56', '2025-04-09 20:03:56'),
(78, 36, 'aniko.racz@bot.example.com', '+36703482910', 'Anikó', 'Rácz', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 12', 'Visszaigazolva', 4000, '2025-04-09 20:03:59', '2025-04-09 20:03:59'),
(79, 104, 'ilona.veres@bot.example.net', '+36306598089', 'Ilona', 'Veres', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 36', 'Visszaigazolva', 5000, '2025-04-09 20:04:02', '2025-04-09 20:04:02'),
(80, 33, 'rita.farkas@bot.example.net', '+36206616946', 'Rita', 'Farkas', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 93', 'Visszaigazolva', 4500, '2025-04-09 20:04:06', '2025-04-09 20:04:06'),
(81, 83, 'lilla.szoke@bot.example.net', '+36308152102', 'Lilla', 'Szőke', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 46', 'Teljesítve', 4000, '2025-04-09 20:04:26', '2025-04-09 20:04:26'),
(82, 5, 'agnes.szep@bot.example.net', '+36202307382', 'Ágnes', 'Szép', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 94', 'Teljesítve', 5000, '2025-04-09 20:05:04', '2025-04-09 20:05:04'),
(83, 11, 'csilla.farkas@bot.example.com', '+36202817226', 'Csilla', 'Farkas', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 9', 'Teljesítve', 5500, '2025-04-09 20:05:08', '2025-04-09 20:05:08'),
(84, 27, 'edina.lakatos@bot.example.com', '+36208008975', 'Edina', 'Lakatos', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 29', 'Visszaigazolva', 9000, '2025-04-09 20:05:10', '2025-04-09 20:05:10'),
(85, 9, 'flora.szalai@bot.example.org', '+36704419119', 'Flóra', 'Szalai', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 30', 'Visszaigazolva', 4200, '2025-04-09 20:05:13', '2025-04-09 20:05:13'),
(86, 87, 'lilla.szabo@bot.example.org', '+36203309752', 'Lilla', 'Szabó', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 22', 'Teljesítve', 4500, '2025-04-09 20:05:17', '2025-04-09 20:05:17'),
(87, 34, 'maria.balogh@bot.example.com', '+36701442586', 'Mária', 'Balogh', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 90', 'Visszaigazolva', 5600, '2025-04-09 20:05:21', '2025-04-09 20:05:21'),
(88, 58, 'eszter.racz@bot.example.net', '+36301154398', 'Eszter', 'Rácz', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 95', 'Teljesítve', 5000, '2025-04-09 20:05:47', '2025-04-09 20:05:47'),
(89, 68, 'adrienn.fulop@bot.example.net', '+36207816332', 'Adrienn', 'Fülöp', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 75', 'Teljesítve', 5500, '2025-04-09 20:06:28', '2025-04-09 20:06:28'),
(90, 30, 'katalin.szilagyi@bot.example.org', '+36709329744', 'Katalin', 'Szilágyi', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 38', 'Teljesítve', 5000, '2025-04-09 20:06:29', '2025-04-09 20:06:29'),
(91, 13, 'hedvig.simon@bot.example.com', '+36209241037', 'Hedvig', 'Simon', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 34', 'Teljesítve', 5000, '2025-04-09 20:06:34', '2025-04-09 20:06:34'),
(92, 43, 'krisztina.szep@bot.example.net', '+36205987374', 'Krisztina', 'Szép', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 37', 'Teljesítve', 5800, '2025-04-09 20:06:39', '2025-04-09 20:06:39'),
(93, 36, 'aniko.racz@bot.example.com', '+36201057991', 'Anikó', 'Rácz', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 4', 'Visszaigazolva', 6200, '2025-04-09 20:06:39', '2025-04-09 20:06:39'),
(94, 59, 'adrienn.szucs@bot.example.org', '+36207472078', 'Adrienn', 'Szűcs', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 56', 'Teljesítve', 5600, '2025-04-09 20:07:12', '2025-04-09 20:07:12'),
(95, 24, 'nikolett.juhasz@bot.example.com', '+36303314575', 'Nikolett', 'Juhász', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 77', 'Teljesítve', 5500, '2025-04-09 20:07:49', '2025-04-09 20:07:49'),
(96, 28, 'andrea.molnar@bot.example.com', '+36703213115', 'Andrea', 'Molnár', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 32', 'Teljesítve', 6000, '2025-04-09 20:07:53', '2025-04-09 20:07:53'),
(97, 55, 'edina.kovacs@bot.example.com', '+36206994359', 'Edina', 'Kovács', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 16', 'Visszaigazolva', 4000, '2025-04-09 20:07:54', '2025-04-09 20:07:54'),
(98, 77, 'rozalia.fulop@bot.example.net', '+36709021973', 'Rozália', 'Fülöp', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 45', 'Teljesítve', 5800, '2025-04-09 20:07:59', '2025-04-09 20:07:59'),
(99, 97, 'hajnalka.zsiga@bot.example.net', '+36702701472', 'Hajnalka', 'Zsiga', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 54', 'Teljesítve', 5000, '2025-04-09 20:08:01', '2025-04-09 20:08:01'),
(100, 105, 'csillag.hegyessy@bot.example.org', '+36702281934', 'Csillag', 'Hegyessy', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 79', 'Visszaigazolva', 4500, '2025-04-09 20:08:02', '2025-04-09 20:08:02'),
(101, 12, 'lilla.barta@bot.example.org', '+36306406068', 'Lilla', 'Barta', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 78', 'Teljesítve', 4500, '2025-04-09 20:08:30', '2025-04-09 20:08:30'),
(102, 23, 'zsofia.bognar@bot.example.net', '+36306526170', 'Zsófia', 'Bognár', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 49', 'Teljesítve', 5990, '2025-04-09 20:09:05', '2025-04-09 20:09:05'),
(103, 95, 'tunde.fazekas@bot.example.org', '+36706210256', 'Tünde', 'Fazekas', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 32', 'Teljesítve', 5600, '2025-04-09 20:09:08', '2025-04-09 20:09:08'),
(104, 31, 'csillag.vass@bot.example.net', '+36209575016', 'Csillag', 'Vass', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 93', 'Visszaigazolva', 6200, '2025-04-09 20:09:15', '2025-04-09 20:09:15'),
(105, 63, 'zita.hegyessy@bot.example.com', '+36706851041', 'Zita', 'Hegyessy', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 47', 'Teljesítve', 6500, '2025-04-09 20:09:48', '2025-04-09 20:09:48'),
(106, 34, 'maria.balogh@bot.example.com', '+36305661335', 'Mária', 'Balogh', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 10', 'Visszaigazolva', 6000, '2025-04-09 20:09:50', '2025-04-09 20:09:50'),
(107, 81, 'krisztina.toth@bot.example.net', '+36307940579', 'Krisztina', 'Tóth', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 97', 'Visszaigazolva', 5000, '2025-04-09 20:10:21', '2025-04-09 20:10:21'),
(108, 46, 'adrienn.kecske@bot.example.org', '+36709925542', 'Adrienn', 'Kecske', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 27', 'Visszaigazolva', 5990, '2025-04-09 20:10:22', '2025-04-09 20:10:22'),
(109, 34, 'maria.balogh@bot.example.com', '+36301294132', 'Mária', 'Balogh', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 6', 'Teljesítve', 5000, '2025-04-09 20:10:23', '2025-04-09 20:10:23'),
(110, 27, 'edina.lakatos@bot.example.com', '+36301857658', 'Edina', 'Lakatos', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 23', 'Teljesítve', 4500, '2025-04-09 20:10:30', '2025-04-09 20:10:30'),
(111, 70, 'zsuzsanna.balint@bot.example.com', '+36702163203', 'Zsuzsanna', 'Bálint', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 14', 'Teljesítve', 4000, '2025-04-09 20:10:32', '2025-04-09 20:10:32'),
(112, 81, 'krisztina.toth@bot.example.net', '+36308548166', 'Krisztina', 'Tóth', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 86', 'Teljesítve', 6000, '2025-04-09 20:10:35', '2025-04-09 20:10:35'),
(113, 37, 'margit.nagy@bot.example.org', '+36709488098', 'Margit', 'Nagy', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 7', 'Visszaigazolva', 6200, '2025-04-09 20:11:07', '2025-04-09 20:11:07'),
(114, 67, 'maria.szoke@bot.example.com', '+36209705744', 'Mária', 'Szőke', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 18', 'Teljesítve', 6200, '2025-04-09 20:11:07', '2025-04-09 20:11:07'),
(115, 17, 'erika.szoke@bot.example.com', '+36209826365', 'Erika', 'Szőke', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 79', 'Visszaigazolva', 5000, '2025-04-09 20:11:38', '2025-04-09 20:11:38'),
(116, 8, 'mimiviktoria.meszaros@bot.example.com', '+36707954108', 'MimiViktória', 'Mészáros', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 21', 'Visszaigazolva', 3000, '2025-04-09 20:11:40', '2025-04-09 20:11:40'),
(117, 102, 'eszter.horvath@bot.example.org', '+36704821844', 'Eszter', 'Horváth', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 11', 'Teljesítve', 6800, '2025-04-09 20:11:46', '2025-04-09 20:11:46'),
(118, 61, 'iren.balogh@bot.example.net', '+36307162900', 'Irén', 'Balogh', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 10', 'Teljesítve', 5000, '2025-04-09 20:11:48', '2025-04-09 20:11:48'),
(119, 104, 'ilona.veres@bot.example.net', '+36706508749', 'Ilona', 'Veres', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 52', 'Teljesítve', 4000, '2025-04-09 20:12:23', '2025-04-09 20:12:23'),
(120, 72, 'flora.hegyessy@bot.example.org', '+36706783592', 'Flóra', 'Hegyessy', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 53', 'Teljesítve', 6300, '2025-04-09 20:12:24', '2025-04-09 20:12:24'),
(121, 17, 'erika.szoke@bot.example.com', '+36208846216', 'Erika', 'Szőke', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 55', 'Teljesítve', 6200, '2025-04-09 20:12:57', '2025-04-09 20:12:57'),
(122, 16, 'emese.hegyessy@bot.example.org', '+36709445512', 'Emese', 'Hegyessy', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 22', 'Teljesítve', 6000, '2025-04-09 20:13:00', '2025-04-09 20:13:00'),
(123, 37, 'margit.nagy@bot.example.org', '+36303857308', 'Margit', 'Nagy', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 51', 'Teljesítve', 6000, '2025-04-09 20:13:00', '2025-04-09 20:13:00'),
(124, 9, 'flora.szalai@bot.example.org', '+36701464820', 'Flóra', 'Szalai', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 93', 'Teljesítve', 5990, '2025-04-09 20:13:05', '2025-04-09 20:13:05'),
(125, 55, 'edina.kovacs@bot.example.com', '+36309836671', 'Edina', 'Kovács', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 81', 'Teljesítve', 6500, '2025-04-09 20:13:12', '2025-04-09 20:13:12'),
(126, 33, 'rita.farkas@bot.example.net', '+36203762443', 'Rita', 'Farkas', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 27', 'Teljesítve', 5000, '2025-04-09 20:13:44', '2025-04-09 20:13:44'),
(127, 46, 'adrienn.kecske@bot.example.org', '+36702658854', 'Adrienn', 'Kecske', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 63', 'Teljesítve', 4500, '2025-04-09 20:14:20', '2025-04-09 20:14:20'),
(128, 36, 'aniko.racz@bot.example.com', '+36704228605', 'Anikó', 'Rácz', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 17', 'Teljesítve', 6300, '2025-04-09 20:14:24', '2025-04-09 20:14:24'),
(129, 107, 'agnes.rozsa@bot.example.net', '+36702675005', 'Ágnes', 'Rózsa', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 47', 'Visszaigazolva', 5600, '2025-04-09 20:14:24', '2025-04-09 20:14:24'),
(130, 4, 'kinga.blank@bot.example.org', '+36209483321', 'Kinga', 'Blank', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 73', 'Teljesítve', 6200, '2025-04-09 21:43:42', '2025-04-09 21:43:42'),
(131, 15, 'agnes.szilagyi@bot.example.org', '+36704193590', 'Ágnes', 'Szilágyi', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 2', 'Visszaigazolva', 5000, '2025-04-09 21:43:44', '2025-04-09 21:43:44'),
(132, 20, 'anna.simon@bot.example.com', '+36704517013', 'Anna', 'Simon', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 1', 'Teljesítve', 6000, '2025-04-09 21:43:44', '2025-04-09 21:43:44'),
(133, 13, 'hedvig.simon@bot.example.com', '+36205528430', 'Hedvig', 'Simon', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 3', 'Visszaigazolva', 4500, '2025-04-09 21:43:44', '2025-04-09 21:43:44'),
(134, 87, 'lilla.szabo@bot.example.org', '+36206608803', 'Lilla', 'Szabó', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 44', 'Visszaigazolva', 6200, '2025-04-09 21:43:49', '2025-04-09 21:43:49'),
(135, 21, 'melinda.kovacs@bot.example.net', '+36305421444', 'Melinda', 'Kovács', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 44', 'Teljesítve', 5500, '2025-04-09 21:43:53', '2025-04-09 21:43:53'),
(136, 5, 'agnes.szep@bot.example.net', '+36301292613', 'Ágnes', 'Szép', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 26', 'Teljesítve', 6000, '2025-04-09 21:43:55', '2025-04-09 21:43:55'),
(137, 104, 'ilona.veres@bot.example.net', '+36204211759', 'Ilona', 'Veres', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 27', 'Teljesítve', 5800, '2025-04-09 21:44:53', '2025-04-09 21:44:53'),
(138, 84, 'mimiviktoria.racz@bot.example.net', '+36309301762', 'MimiViktória', 'Rácz', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 14', 'Visszaigazolva', 3000, '2025-04-09 21:44:55', '2025-04-09 21:44:55'),
(139, 87, 'lilla.szabo@bot.example.org', '+36307225522', 'Lilla', 'Szabó', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 30', 'Visszaigazolva', 4000, '2025-04-09 21:44:55', '2025-04-09 21:44:55'),
(140, 11, 'csilla.farkas@bot.example.com', '+36705774719', 'Csilla', 'Farkas', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 99', 'Visszaigazolva', 4500, '2025-04-09 21:44:57', '2025-04-09 21:44:57'),
(141, 24, 'nikolett.juhasz@bot.example.com', '+36201864312', 'Nikolett', 'Juhász', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 14', 'Teljesítve', 6000, '2025-04-09 21:45:05', '2025-04-09 21:45:05'),
(142, 64, 'barbara.balint@bot.example.com', '+36707675906', 'Barbara', 'Bálint', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 74', 'Teljesítve', 5500, '2025-04-09 21:45:06', '2025-04-09 21:45:06'),
(143, 108, 'iren.simon@bot.example.net', '+36701042711', 'Irén', 'Simon', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 20', 'Visszaigazolva', 5000, '2025-04-09 21:45:07', '2025-04-09 21:45:07'),
(144, 37, 'margit.nagy@bot.example.org', '+36304418709', 'Margit', 'Nagy', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 68', 'Teljesítve', 6300, '2025-04-09 21:46:05', '2025-04-09 21:46:05'),
(145, 102, 'eszter.horvath@bot.example.org', '+36302583386', 'Eszter', 'Horváth', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 35', 'Teljesítve', 6000, '2025-04-09 21:46:06', '2025-04-09 21:46:06'),
(146, 93, 'hajnalka.horvath@bot.example.net', '+36702263857', 'Hajnalka', 'Horváth', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 29', 'Visszaigazolva', 5000, '2025-04-09 21:46:07', '2025-04-09 21:46:07'),
(147, 82, 'nikolett.kukso@bot.example.org', '+36304556761', 'Nikolett', 'Kuksó', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 74', 'Teljesítve', 9000, '2025-04-09 21:46:10', '2025-04-09 21:46:10'),
(148, 28, 'andrea.molnar@bot.example.com', '+36702136095', 'Andrea', 'Molnár', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 69', 'Visszaigazolva', 6000, '2025-04-09 21:46:12', '2025-04-09 21:46:12'),
(149, 58, 'eszter.racz@bot.example.net', '+36706333884', 'Eszter', 'Rácz', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 19', 'Teljesítve', 4500, '2025-04-09 21:46:20', '2025-04-09 21:46:20'),
(150, 90, 'boglarka.zsiga@bot.example.org', '+36207878037', 'Boglárka', 'Zsiga', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 78', 'Visszaigazolva', 5000, '2025-04-09 21:46:20', '2025-04-09 21:46:20'),
(151, 81, 'krisztina.toth@bot.example.net', '+36307834856', 'Krisztina', 'Tóth', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 13', 'Visszaigazolva', 5800, '2025-04-09 21:46:21', '2025-04-09 21:46:21'),
(152, 54, 'iren.racz@bot.example.org', '+36305542866', 'Irén', 'Rácz', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 79', 'Visszaigazolva', 4500, '2025-04-09 21:47:18', '2025-04-09 21:47:18'),
(153, 9, 'flora.szalai@bot.example.org', '+36204398057', 'Flóra', 'Szalai', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 88', 'Teljesítve', 6500, '2025-04-09 21:47:21', '2025-04-09 21:47:21'),
(154, 95, 'tunde.fazekas@bot.example.org', '+36208721462', 'Tünde', 'Fazekas', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 62', 'Teljesítve', 5500, '2025-04-09 21:47:21', '2025-04-09 21:47:21'),
(155, 75, 'jazmin.peter@bot.example.net', '+36701928452', 'Jázmin', 'Péter', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 1', 'Visszaigazolva', 5000, '2025-04-09 21:47:26', '2025-04-09 21:47:26'),
(156, 43, 'krisztina.szep@bot.example.net', '+36203520903', 'Krisztina', 'Szép', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 70', 'Visszaigazolva', 5500, '2025-04-09 21:47:30', '2025-04-09 21:47:30'),
(157, 90, 'boglarka.zsiga@bot.example.org', '+36202026945', 'Boglárka', 'Zsiga', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 68', 'Teljesítve', 7000, '2025-04-09 21:47:38', '2025-04-09 21:47:38'),
(158, 66, 'agnes.fulop@bot.example.org', '+36305721075', 'Ágnes', 'Fülöp', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 62', 'Visszaigazolva', 6000, '2025-04-09 21:47:39', '2025-04-09 21:47:39'),
(159, 69, 'noemi.horvath@bot.example.com', '+36704805845', 'Noémi', 'Horváth', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 79', 'Visszaigazolva', 6500, '2025-04-09 21:47:41', '2025-04-09 21:47:41'),
(160, 97, 'hajnalka.zsiga@bot.example.net', '+36206174009', 'Hajnalka', 'Zsiga', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 73', 'Teljesítve', 4200, '2025-04-09 21:48:29', '2025-04-09 21:48:29'),
(161, 80, 'zsofia.papp@bot.example.org', '+36705447103', 'Zsófia', 'Papp', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 5', 'Teljesítve', 6000, '2025-04-09 21:48:32', '2025-04-09 21:48:32'),
(162, 48, 'anna.fazekas@bot.example.org', '+36206370374', 'Anna', 'Fazekas', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 73', 'Teljesítve', 6200, '2025-04-09 21:48:47', '2025-04-09 21:48:47'),
(163, 93, 'hajnalka.horvath@bot.example.net', '+36201057245', 'Hajnalka', 'Horváth', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 89', 'Teljesítve', 4500, '2025-04-09 21:48:54', '2025-04-09 21:48:54'),
(164, 8, 'mimiviktoria.meszaros@bot.example.com', '+36301300144', 'MimiViktória', 'Mészáros', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 94', 'Teljesítve', 4500, '2025-04-09 21:48:55', '2025-04-09 21:48:55'),
(165, 70, 'zsuzsanna.balint@bot.example.com', '+36205986952', 'Zsuzsanna', 'Bálint', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 25', 'Teljesítve', 6000, '2025-04-09 21:48:55', '2025-04-09 21:48:55'),
(166, 13, 'hedvig.simon@bot.example.com', '+36704814264', 'Hedvig', 'Simon', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 49', 'Teljesítve', 5800, '2025-04-09 21:49:36', '2025-04-09 21:49:36'),
(167, 11, 'csilla.farkas@bot.example.com', '+36704785264', 'Csilla', 'Farkas', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 25', 'Teljesítve', 4000, '2025-04-09 21:49:40', '2025-04-09 21:49:40'),
(168, 50, 'eszter.fazekas@bot.example.com', '+36207890125', 'Eszter', 'Fazekas', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 49', 'Teljesítve', 6800, '2025-04-09 21:49:42', '2025-04-09 21:49:42'),
(169, 40, 'erika.nemes@bot.example.com', '+36707532936', 'Erika', 'Nemes', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 11', 'Teljesítve', 6900, '2025-04-09 21:49:54', '2025-04-09 21:49:54'),
(170, 54, 'iren.racz@bot.example.org', '+36203104503', 'Irén', 'Rácz', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 59', 'Teljesítve', 5500, '2025-04-09 21:49:57', '2025-04-09 21:49:57'),
(171, 22, 'zsuzsanna.voros@bot.example.org', '+36302524013', 'Zsuzsanna', 'Vörös', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 37', 'Teljesítve', 4000, '2025-04-09 21:50:03', '2025-04-09 21:50:03'),
(172, 7, 'maria.deak@bot.example.com', '+36203237493', 'Mária', 'Deák', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 78', 'Teljesítve', 4000, '2025-04-09 21:50:10', '2025-04-09 21:50:10'),
(173, 73, 'piroska.voros@bot.example.com', '+36205024441', 'Piroska', 'Vörös', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 98', 'Visszaigazolva', 5500, '2025-04-09 21:50:10', '2025-04-09 21:50:10'),
(174, 87, 'lilla.szabo@bot.example.org', '+36306659795', 'Lilla', 'Szabó', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 74', 'Teljesítve', 6200, '2025-04-09 21:50:49', '2025-04-09 21:50:49'),
(175, 59, 'adrienn.szucs@bot.example.org', '+36206420950', 'Adrienn', 'Szűcs', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 2', 'Teljesítve', 6200, '2025-04-09 21:50:54', '2025-04-09 21:50:54'),
(176, 109, 'adrienn.takacs@bot.example.com', '+36206276451', 'Adrienn', 'Takács', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 51', 'Teljesítve', 5600, '2025-04-09 21:50:55', '2025-04-09 21:50:55'),
(177, 66, 'agnes.fulop@bot.example.org', '+36701964817', 'Ágnes', 'Fülöp', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 52', 'Teljesítve', 5000, '2025-04-09 21:51:11', '2025-04-09 21:51:11'),
(178, 84, 'mimiviktoria.racz@bot.example.net', '+36704812750', 'MimiViktória', 'Rácz', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 11', 'Teljesítve', 5500, '2025-04-09 21:51:12', '2025-04-09 21:51:12'),
(179, 55, 'edina.kovacs@bot.example.com', '+36709387258', 'Edina', 'Kovács', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 95', 'Teljesítve', 6800, '2025-04-09 21:51:13', '2025-04-09 21:51:13'),
(180, 53, 'katalin.szalai@bot.example.org', '+36308340894', 'Katalin', 'Szalai', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 38', 'Visszaigazolva', 6500, '2025-04-09 21:51:23', '2025-04-09 21:51:23'),
(181, 107, 'agnes.rozsa@bot.example.net', '+36206299657', 'Ágnes', 'Rózsa', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 69', 'Teljesítve', 6200, '2025-04-09 21:51:25', '2025-04-09 21:51:25'),
(182, 19, 'lilla.veres@bot.example.org', '+36203805064', 'Lilla', 'Veres', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 63', 'Teljesítve', 5800, '2025-04-09 21:51:58', '2025-04-09 21:51:58'),
(183, 31, 'csillag.vass@bot.example.net', '+36201303142', 'Csillag', 'Vass', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 72', 'Visszaigazolva', 6900, '2025-04-09 21:52:02', '2025-04-09 21:52:02'),
(184, 73, 'piroska.voros@bot.example.com', '+36706001516', 'Piroska', 'Vörös', NULL, NULL, NULL, '8200 Veszprém, Robotics utca 41', 'Teljesítve', 5500, '2025-04-09 21:52:18', '2025-04-09 21:52:18');

--
-- Eseményindítók `order`
--
DROP TRIGGER IF EXISTS `after_order_delete`;
DELIMITER $$
CREATE TRIGGER `after_order_delete` AFTER DELETE ON `order` FOR EACH ROW BEGIN
    DELETE FROM order_item WHERE order_id IS NULL;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `order_item`
--

DROP TABLE IF EXISTS `order_item`;
CREATE TABLE `order_item` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;

--
-- A tábla adatainak kiíratása `order_item`
--

INSERT INTO `order_item` (`id`, `order_id`, `product_id`, `quantity`, `created_at`) VALUES
(1, 1, 11, 1, '2025-03-15 20:02:58'),
(2, 2, 11, 1, '2025-03-17 19:14:43'),
(4, 4, 17, 1, '2025-03-17 19:34:37'),
(5, 5, 7, 1, '2025-03-17 19:34:37'),
(6, 6, 18, 1, '2025-03-17 19:34:37'),
(7, 7, 24, 1, '2025-04-07 19:07:16'),
(8, 8, 12, 1, '2025-04-09 15:29:01'),
(9, 9, 19, 1, '2025-04-09 17:40:01'),
(10, 10, 14, 1, '2025-04-09 17:52:18'),
(11, 11, 7, 1, '2025-04-09 18:03:15'),
(12, 12, 12, 1, '2025-04-09 18:07:00'),
(13, 13, 3, 1, '2025-04-09 18:14:32'),
(14, 14, 12, 1, '2025-04-09 18:26:09'),
(15, 15, 11, 1, '2025-04-09 18:36:17'),
(16, 16, 7, 1, '2025-04-09 18:40:11'),
(17, 17, 7, 1, '2025-04-09 18:50:41'),
(18, 18, 23, 1, '2025-04-09 18:59:47'),
(19, 19, 6, 1, '2025-04-09 19:11:10'),
(20, 20, 6, 1, '2025-04-09 19:11:10'),
(21, 21, 11, 1, '2025-04-09 19:12:15'),
(22, 22, 11, 1, '2025-04-09 19:12:15'),
(23, 23, 7, 1, '2025-04-09 19:31:45'),
(24, 23, 14, 1, '2025-04-09 19:31:45'),
(25, 24, 7, 1, '2025-04-09 19:31:45'),
(26, 24, 14, 1, '2025-04-09 19:31:45'),
(27, 25, 11, 1, '2025-04-09 19:31:45'),
(28, 26, 9, 1, '2025-04-09 19:32:51'),
(29, 27, 12, 1, '2025-04-09 19:32:52'),
(30, 28, 22, 1, '2025-04-09 19:32:52'),
(31, 29, 11, 1, '2025-04-09 19:33:57'),
(32, 30, 32, 1, '2025-04-09 19:33:58'),
(33, 30, 12, 1, '2025-04-09 19:33:58'),
(34, 31, 32, 1, '2025-04-09 19:33:58'),
(35, 31, 12, 1, '2025-04-09 19:33:58'),
(36, 32, 11, 1, '2025-04-09 19:35:03'),
(37, 32, 4, 1, '2025-04-09 19:35:03'),
(38, 33, 11, 1, '2025-04-09 19:35:03'),
(39, 33, 4, 1, '2025-04-09 19:35:03'),
(40, 34, 8, 1, '2025-04-09 19:35:04'),
(41, 35, 24, 1, '2025-04-09 19:37:19'),
(42, 35, 17, 1, '2025-04-09 19:37:19'),
(43, 36, 24, 1, '2025-04-09 19:37:21'),
(44, 36, 17, 1, '2025-04-09 19:37:21'),
(45, 37, 27, 1, '2025-04-09 19:37:21'),
(46, 38, 14, 1, '2025-04-09 19:39:42'),
(47, 39, 9, 1, '2025-04-09 19:39:42'),
(48, 40, 14, 1, '2025-04-09 19:40:47'),
(49, 41, 14, 1, '2025-04-09 19:40:48'),
(50, 42, 17, 1, '2025-04-09 19:58:44'),
(51, 43, 32, 1, '2025-04-09 19:58:45'),
(52, 44, 11, 1, '2025-04-09 19:58:47'),
(53, 45, 7, 1, '2025-04-09 19:58:47'),
(54, 46, 8, 1, '2025-04-09 19:58:48'),
(55, 47, 12, 1, '2025-04-09 19:58:49'),
(56, 48, 14, 1, '2025-04-09 19:58:49'),
(57, 49, 31, 1, '2025-04-09 19:58:59'),
(58, 50, 8, 1, '2025-04-09 20:00:06'),
(59, 51, 15, 1, '2025-04-09 20:00:06'),
(60, 52, 31, 1, '2025-04-09 20:00:06'),
(61, 53, 14, 1, '2025-04-09 20:00:08'),
(62, 54, 17, 1, '2025-04-09 20:00:09'),
(63, 55, 7, 1, '2025-04-09 20:00:10'),
(64, 56, 5, 1, '2025-04-09 20:00:12'),
(65, 57, 21, 1, '2025-04-09 20:00:43'),
(66, 58, 18, 1, '2025-04-09 20:01:25'),
(67, 59, 17, 1, '2025-04-09 20:01:25'),
(68, 60, 32, 1, '2025-04-09 20:01:26'),
(69, 61, 25, 1, '2025-04-09 20:01:30'),
(70, 62, 31, 1, '2025-04-09 20:01:31'),
(71, 63, 22, 1, '2025-04-09 20:01:32'),
(72, 64, 23, 1, '2025-04-09 20:01:33'),
(73, 65, 13, 1, '2025-04-09 20:02:03'),
(74, 66, 6, 1, '2025-04-09 20:02:38'),
(75, 67, 11, 1, '2025-04-09 20:02:40'),
(76, 68, 12, 1, '2025-04-09 20:02:43'),
(77, 69, 23, 1, '2025-04-09 20:02:44'),
(78, 70, 7, 1, '2025-04-09 20:02:44'),
(79, 70, 27, 1, '2025-04-09 20:02:44'),
(80, 71, 7, 1, '2025-04-09 20:02:47'),
(81, 71, 27, 1, '2025-04-09 20:02:47'),
(82, 72, 6, 1, '2025-04-09 20:02:51'),
(83, 73, 13, 1, '2025-04-09 20:03:15'),
(84, 74, 30, 1, '2025-04-09 20:03:49'),
(85, 75, 11, 1, '2025-04-09 20:03:53'),
(86, 76, 6, 1, '2025-04-09 20:03:53'),
(87, 77, 27, 1, '2025-04-09 20:03:56'),
(88, 78, 17, 1, '2025-04-09 20:03:59'),
(89, 79, 11, 1, '2025-04-09 20:04:02'),
(90, 80, 14, 1, '2025-04-09 20:04:06'),
(91, 81, 17, 1, '2025-04-09 20:04:26'),
(92, 82, 8, 1, '2025-04-09 20:05:04'),
(93, 83, 7, 1, '2025-04-09 20:05:09'),
(94, 84, 4, 1, '2025-04-09 20:05:10'),
(95, 85, 5, 1, '2025-04-09 20:05:13'),
(96, 86, 14, 1, '2025-04-09 20:05:17'),
(97, 87, 19, 1, '2025-04-09 20:05:21'),
(98, 88, 11, 1, '2025-04-09 20:05:47'),
(99, 89, 7, 1, '2025-04-09 20:06:28'),
(100, 90, 11, 1, '2025-04-09 20:06:29'),
(101, 91, 18, 1, '2025-04-09 20:06:34'),
(102, 92, 32, 1, '2025-04-09 20:06:39'),
(103, 93, 22, 1, '2025-04-09 20:06:39'),
(104, 94, 19, 1, '2025-04-09 20:07:12'),
(105, 95, 12, 1, '2025-04-09 20:07:49'),
(106, 96, 9, 1, '2025-04-09 20:07:53'),
(107, 97, 17, 1, '2025-04-09 20:07:54'),
(108, 98, 21, 1, '2025-04-09 20:07:59'),
(109, 99, 8, 1, '2025-04-09 20:08:01'),
(110, 100, 6, 1, '2025-04-09 20:08:02'),
(111, 101, 6, 1, '2025-04-09 20:08:30'),
(112, 102, 30, 1, '2025-04-09 20:09:05'),
(113, 103, 19, 1, '2025-04-09 20:09:08'),
(114, 104, 22, 1, '2025-04-09 20:09:15'),
(115, 105, 24, 1, '2025-04-09 20:09:48'),
(116, 106, 9, 1, '2025-04-09 20:09:50'),
(117, 107, 11, 1, '2025-04-09 20:10:21'),
(118, 108, 30, 1, '2025-04-09 20:10:22'),
(119, 109, 11, 1, '2025-04-09 20:10:23'),
(120, 110, 6, 1, '2025-04-09 20:10:30'),
(121, 111, 17, 1, '2025-04-09 20:10:32'),
(122, 112, 9, 1, '2025-04-09 20:10:35'),
(123, 113, 22, 1, '2025-04-09 20:11:07'),
(124, 114, 22, 1, '2025-04-09 20:11:07'),
(125, 115, 8, 1, '2025-04-09 20:11:38'),
(126, 116, 3, 1, '2025-04-09 20:11:40'),
(127, 117, 27, 1, '2025-04-09 20:11:46'),
(128, 118, 8, 1, '2025-04-09 20:11:48'),
(129, 119, 17, 1, '2025-04-09 20:12:23'),
(130, 120, 31, 1, '2025-04-09 20:12:24'),
(131, 121, 22, 1, '2025-04-09 20:12:57'),
(132, 122, 9, 1, '2025-04-09 20:13:00'),
(133, 123, 13, 1, '2025-04-09 20:13:00'),
(134, 124, 30, 1, '2025-04-09 20:13:05'),
(135, 125, 26, 1, '2025-04-09 20:13:12'),
(136, 126, 8, 1, '2025-04-09 20:13:44'),
(137, 127, 6, 1, '2025-04-09 20:14:20'),
(138, 128, 31, 1, '2025-04-09 20:14:24'),
(139, 129, 19, 1, '2025-04-09 20:14:24'),
(140, 130, 34, 1, '2025-04-09 21:43:42'),
(141, 131, 11, 1, '2025-04-09 21:43:44'),
(142, 132, 9, 1, '2025-04-09 21:43:44'),
(143, 133, 6, 1, '2025-04-09 21:43:44'),
(144, 134, 34, 1, '2025-04-09 21:43:49'),
(145, 135, 7, 1, '2025-04-09 21:43:53'),
(146, 136, 9, 1, '2025-04-09 21:43:55'),
(147, 137, 21, 1, '2025-04-09 21:44:53'),
(148, 138, 3, 1, '2025-04-09 21:44:55'),
(149, 139, 17, 1, '2025-04-09 21:44:55'),
(150, 140, 6, 1, '2025-04-09 21:44:57'),
(151, 141, 13, 1, '2025-04-09 21:45:05'),
(152, 142, 12, 1, '2025-04-09 21:45:06'),
(153, 143, 8, 1, '2025-04-09 21:45:07'),
(154, 144, 31, 1, '2025-04-09 21:46:05'),
(155, 145, 9, 1, '2025-04-09 21:46:06'),
(156, 146, 8, 1, '2025-04-09 21:46:07'),
(157, 147, 4, 1, '2025-04-09 21:46:10'),
(158, 148, 9, 1, '2025-04-09 21:46:12'),
(159, 149, 6, 1, '2025-04-09 21:46:20'),
(160, 150, 11, 1, '2025-04-09 21:46:20'),
(161, 151, 21, 1, '2025-04-09 21:46:21'),
(162, 152, 6, 1, '2025-04-09 21:47:18'),
(163, 153, 24, 1, '2025-04-09 21:47:21'),
(164, 154, 7, 1, '2025-04-09 21:47:21'),
(165, 155, 11, 1, '2025-04-09 21:47:26'),
(166, 156, 7, 1, '2025-04-09 21:47:30'),
(167, 157, 25, 1, '2025-04-09 21:47:38'),
(168, 158, 9, 1, '2025-04-09 21:47:39'),
(169, 159, 24, 1, '2025-04-09 21:47:41'),
(170, 160, 5, 1, '2025-04-09 21:48:30'),
(171, 161, 13, 1, '2025-04-09 21:48:32'),
(172, 162, 22, 1, '2025-04-09 21:48:47'),
(173, 163, 6, 1, '2025-04-09 21:48:54'),
(174, 164, 6, 1, '2025-04-09 21:48:55'),
(175, 165, 15, 1, '2025-04-09 21:48:55'),
(176, 166, 21, 1, '2025-04-09 21:49:36'),
(177, 167, 17, 1, '2025-04-09 21:49:40'),
(178, 168, 27, 1, '2025-04-09 21:49:42'),
(179, 169, 23, 1, '2025-04-09 21:49:54'),
(180, 170, 7, 1, '2025-04-09 21:49:57'),
(181, 171, 17, 1, '2025-04-09 21:50:03'),
(182, 172, 17, 1, '2025-04-09 21:50:10'),
(183, 173, 7, 1, '2025-04-09 21:50:10'),
(184, 174, 22, 1, '2025-04-09 21:50:49'),
(185, 175, 22, 1, '2025-04-09 21:50:54'),
(186, 176, 19, 1, '2025-04-09 21:50:55'),
(187, 177, 8, 1, '2025-04-09 21:51:11'),
(188, 178, 12, 1, '2025-04-09 21:51:12'),
(189, 179, 27, 1, '2025-04-09 21:51:13'),
(190, 180, 16, 1, '2025-04-09 21:51:23'),
(191, 181, 22, 1, '2025-04-09 21:51:25'),
(192, 182, 21, 1, '2025-04-09 21:51:58'),
(193, 183, 23, 1, '2025-04-09 21:52:02'),
(194, 184, 7, 1, '2025-04-09 21:52:18');

--
-- Eseményindítók `order_item`
--
DROP TRIGGER IF EXISTS `after_order_item_delete`;
DELIMITER $$
CREATE TRIGGER `after_order_item_delete` AFTER DELETE ON `order_item` FOR EACH ROW BEGIN
    UPDATE product
    SET stock = stock + OLD.quantity
    WHERE id = OLD.product_id;
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `after_order_item_insert`;
DELIMITER $$
CREATE TRIGGER `after_order_item_insert` AFTER INSERT ON `order_item` FOR EACH ROW BEGIN
    UPDATE product
    SET stock = stock - NEW.quantity
    WHERE id = NEW.product_id;
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `after_order_item_update`;
DELIMITER $$
CREATE TRIGGER `after_order_item_update` AFTER UPDATE ON `order_item` FOR EACH ROW BEGIN
    IF OLD.product_id = NEW.product_id THEN
        UPDATE product
        SET stock = stock - (NEW.quantity - OLD.quantity)
        WHERE id = OLD.product_id;
    ELSE
        UPDATE product
        SET stock = stock + OLD.quantity
        WHERE id = OLD.product_id;

        UPDATE product
        SET stock = stock - NEW.quantity
        WHERE id = NEW.product_id;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `product`
--

DROP TABLE IF EXISTS `product`;
CREATE TABLE `product` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `unit_price` int(11) DEFAULT NULL,
  `stock` int(11) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `net_weight` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;

--
-- A tábla adatainak kiíratása `product`
--

INSERT INTO `product` (`id`, `name`, `unit_price`, `stock`, `description`, `net_weight`) VALUES
(1, 'Kamilla virág', 4900, 30, 'A Matricaria chamomilla, azaz a kamilla, az egyik legismertebb és leggyengédebb gyógynövény. Gyakran teaként használják a relaxáció elősegítésére és az emésztés támogatására. A kamilla finoman nyugtatja a bőrt, ezért előszeretettel alkalmazzák bőrápolási ', 50),
(2, 'Levendula virág', 6000, 20, 'A Lavandula angustifolia, közismert nevén levendula, mediterrán eredetű növény, amely édes, nyugtató, virágos illatáról ismert. Virágai és levelei évszázadok óta részei a nyugati gyógynövényhasználatnak, és a szépségápolástól kezdve a főzésig sokoldalúan ', 50),
(3, 'Kurkuma gyökér', 2000, 47, 'A Curcuma longa, azaz a kurkuma, az indiai és délkelet-ázsiai trópusi régiókból származó, gyömbérfélék családjába tartozó évelő növény. Élénk narancssárga-sárga színe miatt fűszerként és természetes színezőanyagként egyaránt népszerű. A kurkuma különleges', 100),
(4, 'Kasvirág gyökér', 8000, 26, 'Az Echinacea angustifolia, egy észak-amerikai gyógynövény, amely támogatja az immunrendszert. Organikus gyökereit teák, tinktúrák és helyi olajok készítésére használják, hogy erősítse a test védelmi rendszereit és javítsa a közérzetet.', 75),
(5, 'Darált fokhagyma', 3200, 37, 'Az Allium sativum, közismert nevén fokhagyma, egy ízletes és erőteljes fűszer, mely számos kultúrában népszerű. Szárított, aprított fokhagymánk a legnagyobb szemcseméretű, és kiválóan alkalmas olajok infúziójához vagy főzéshez. Emellett a fokhagyma híres ', 125),
(6, 'Ánizs', 3500, 0, 'Az ánizs egy aromás gyógynövény, amely segíthet az emésztés támogatásában és a puffadás enyhítésében.', 50),
(7, 'Árnika', 4500, 2, 'Az árnika erőteljes gyulladáscsökkentő és fájdalomcsillapító hatásairól ismert, gyakran alkalmazzák zúzódások és izomfájdalmak kezelésére.', 50),
(8, 'Búzavirág', 4000, 14, 'A búzavirág nyugtató és gyulladáscsökkentő hatású gyógynövény, amelyet gyakran használnak szem- és bélproblémák kezelésére.', 50),
(9, 'Chili', 5000, 4, 'A chili erőteljes antioxidáns és fájdalomcsillapító hatású, és javítja a vérkeringést, ezáltal fokozza az anyagcserét.', 50),
(10, 'Citromfű', 3500, 20, 'A citromfű nyugtató hatásáról ismert, gyakran használják stresszoldásra és a pihentető alvás elősegítésére.', 50),
(11, 'Csipkebogyó', 4000, 0, 'A csipkebogyó rendkívül gazdag C-vitaminban, segít az immunrendszer erősítésében és a bélflóra támogatásában.', 75),
(12, 'Galagonya bogyók', 4500, 14, 'A galagonya kiváló szív- és érrendszeri támogatást nyújt, javítja a keringést és segít csökkenteni a vérnyomást.', 75),
(13, 'Ginzeng gyökér', 5000, 10, 'A ginzeng egy erőteljes gyógynövény, amely segít a fáradtság leküzdésében és az általános vitalitás növelésében.', 100),
(14, 'Körömvirág', 3500, 0, 'A körömvirág bőrápoló tulajdonságairól ismert, segít a sebgyógyulásban és enyhíti a bőr irritációit.', 50),
(15, 'Pillangóborsó', 5000, 18, 'A pillangóborsó gazdag antioxidánsokban, javítja a bélflóra működését és segít a méregtelenítésben.', 75),
(16, 'Reishi', 5500, 9, 'A reishi gomba immunerősítő és stresszoldó hatású, segít a test és az elme harmonizálásában.', 50),
(17, 'Rozmaring', 3000, 5, 'A rozmaring kiváló antioxidáns és memóriajavító hatással rendelkezik, valamint segít az emésztésben.', 50),
(18, 'Valeriána gyökér', 4000, 22, 'A valeriána nyugtató hatású, segít az alvás elősegítésében és a szorongás csökkentésében.', 50),
(19, 'Ashwagandha por', 4600, 19, 'Az Ashwagandha (Withania somnifera) az ájurvédikus gyógyászat egyik legismertebb adaptogén növénye, amely támogatja a stresszkezelést, a hormonális egyensúlyt és az általános energiaszintet.', 75),
(21, 'Ashwagandha durvára őrölt', 4800, 24, 'Az Ashwagandha (Withania somnifera) Indiában és Afrikában honos gyógynövény, amelyet hagyományosan az ájurvédikus gyógyászatban alkalmaznak. Durvára őrölt formában megőrzi természetes aromáját.', 100),
(22, 'Damiana őrölt', 5200, 9, 'A Damiana (Turnera diffusa) egy Közép- és Dél-Amerikában őshonos, lágy illatú gyógynövény, amelyet évszázadok óta hagyományosan aphrodisiakumként és enyhe stresszoldóként használnak. ', 75),
(23, 'Kanos Kecskefű őrölt', 5900, 30, 'Az Epimedium, közismert nevén Horny Goat Weed, egy Kelet-Ázsiából származó gyógynövény, amelyet a hagyományos kínai orvoslás már évszázadok óta alkalmaz.', 75),
(24, 'Kanos Kecskefű por', 5500, 24, 'Az Epimedium, közismert nevén Horny Goat Weed, egy Kelet-Ázsiából származó gyógynövény, amelyet a hagyományos kínai orvoslás már évszázadok óta alkalmaz.', 75),
(25, 'Ginzeng por', 6000, 58, 'A ginzeng (Panax ginseng) a hagyományos keleti orvoslás egyik legismertebb energianövelő és immunerősítő gyógynövénye.', 100),
(26, 'Maca por', 5500, 49, 'A maca (Lepidium meyenii) gyökérből készül, amely a perui Andok magasföldjein terem. Por formájában sokoldalúan felhasználható, és közismert arról, hogy támogathatja a hormonális egyensúlyt.', 100),
(27, 'Bársonybab por', 5800, 43, 'A Mucuna pruriens (bársonybab) a trópusi és szubtrópusi területeken honos növény, amelynek magja a dopamin előanyagaként ismert L-DOPA-ban gazdag.', 50),
(28, 'Muira Puama őrölt', 6800, 50, 'A Muira Puama (Ptychopetalum olacoides) az Amazonas-medence őshonos fájának kérgéből és gyökéréből származik, amit évtizedek óta aphrodisiakumként és általános erőnlétfokozóként alkalmaznak.', 75),
(29, 'Muira Puama por', 6300, 40, 'A Muira Puama (Ptychopetalum olacoides) az Amazonas-medence őshonos fájának kérgéből és gyökéréből származik, amit évtizedek óta aphrodisiakumként és általános erőnlétfokozóként alkalmaznak.', 75),
(30, 'Királydinnye', 4990, 46, 'A Tribulus terrestris, magyarul királydinnye, a hagyományos gyógyászatban főként a hormonális egyensúly és a szexuális egészség támogatására kedvelt növény', 50),
(31, 'Királydinnye por', 5300, 54, 'A Tribulus terrestris, magyarul királydinnye, a hagyományos gyógyászatban főként a hormonális egyensúly és a szexuális egészség támogatására kedvelt növény.', 50),
(32, 'Barátcserje por', 4800, 55, 'A Vitex agnus-castus (barátcserje) por formájában egy népszerű gyógynövény, amelyet elsősorban a női hormonális egyensúly támogatására alkalmaznak.', 50),
(34, 'Vitex szárított bogyók', 5200, 18, 'A Vitex agnus-castus, ismertebb nevén barátcserje, szárított, bio minősítésű bogyói. Hagyományosan a női hormonális egyensúly, valamint a menstruációs ciklus támogatására használják, de számos egyéb jótékony hatással is bír.', 50);

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `product_health_effect`
--

DROP TABLE IF EXISTS `product_health_effect`;
CREATE TABLE `product_health_effect` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `health_effect_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;

--
-- A tábla adatainak kiíratása `product_health_effect`
--

INSERT INTO `product_health_effect` (`id`, `product_id`, `health_effect_id`) VALUES
(1, 3, 2),
(2, 3, 6),
(3, 3, 3),
(4, 3, 24),
(5, 3, 12),
(6, 3, 19),
(7, 1, 4),
(8, 1, 22),
(9, 1, 13),
(10, 4, 1),
(11, 4, 2),
(12, 4, 28),
(13, 4, 29),
(14, 4, 13),
(15, 4, 12),
(16, 4, 15),
(17, 5, 1),
(18, 5, 6),
(19, 5, 28),
(20, 5, 29),
(21, 5, 10),
(22, 5, 12),
(23, 5, 19),
(24, 2, 4),
(25, 2, 22),
(26, 2, 13),
(27, 2, 15),
(28, 6, 3),
(29, 6, 6),
(30, 6, 13),
(42, 8, 2),
(43, 8, 4),
(44, 8, 6),
(45, 8, 14),
(46, 8, 35),
(47, 9, 40),
(48, 9, 10),
(49, 9, 6),
(50, 9, 12),
(51, 10, 4),
(52, 10, 22),
(53, 10, 6),
(54, 10, 14),
(55, 10, 15),
(56, 11, 1),
(57, 11, 6),
(58, 11, 23),
(59, 11, 31),
(60, 11, 35),
(61, 12, 10),
(62, 12, 27),
(63, 12, 6),
(64, 12, 33),
(65, 12, 14),
(66, 13, 40),
(67, 13, 1),
(68, 13, 6),
(69, 13, 14),
(70, 13, 15),
(71, 14, 9),
(72, 14, 30),
(73, 14, 6),
(74, 14, 13),
(75, 14, 39),
(76, 15, 26),
(77, 15, 6),
(78, 15, 7),
(79, 15, 31),
(80, 15, 35),
(81, 16, 1),
(82, 16, 4),
(83, 16, 6),
(84, 16, 14),
(85, 16, 12),
(86, 17, 3),
(87, 17, 6),
(88, 17, 35),
(89, 17, 14),
(90, 18, 4),
(91, 18, 22),
(92, 18, 6),
(93, 18, 14),
(94, 18, 15),
(95, 7, 2),
(96, 7, 5),
(97, 7, 6),
(98, 7, 30),
(99, 7, 20),
(100, 7, 39),
(227, 21, 1),
(228, 21, 4),
(229, 21, 8),
(230, 21, 22),
(231, 21, 40),
(232, 21, 13),
(233, 21, 14),
(234, 21, 31),
(235, 19, 1),
(236, 19, 4),
(237, 19, 8),
(238, 19, 22),
(239, 19, 40),
(240, 19, 13),
(241, 19, 14),
(242, 19, 31),
(243, 32, 4),
(244, 32, 8),
(245, 32, 13),
(246, 32, 14),
(247, 32, 15),
(248, 27, 4),
(249, 27, 8),
(250, 27, 40),
(251, 27, 13),
(252, 27, 14),
(253, 27, 31),
(254, 22, 4),
(255, 22, 8),
(256, 22, 40),
(257, 22, 13),
(258, 22, 14),
(259, 22, 31),
(260, 25, 1),
(261, 25, 6),
(262, 25, 40),
(263, 25, 14),
(264, 25, 15),
(265, 23, 8),
(266, 23, 10),
(267, 23, 40),
(268, 23, 13),
(269, 23, 14),
(270, 23, 16),
(271, 23, 37),
(272, 24, 8),
(273, 24, 10),
(274, 24, 40),
(275, 24, 13),
(276, 24, 14),
(277, 24, 16),
(278, 24, 37),
(279, 30, 8),
(280, 30, 10),
(281, 30, 40),
(282, 30, 14),
(283, 30, 16),
(284, 30, 37),
(285, 31, 8),
(286, 31, 10),
(287, 31, 40),
(288, 31, 14),
(289, 31, 16),
(290, 31, 37),
(291, 26, 1),
(292, 26, 4),
(293, 26, 8),
(294, 26, 40),
(295, 26, 13),
(296, 26, 14),
(297, 26, 15),
(298, 28, 4),
(299, 28, 8),
(300, 28, 40),
(301, 28, 13),
(302, 28, 14),
(303, 28, 16),
(304, 29, 4),
(305, 29, 8),
(306, 29, 40),
(307, 29, 13),
(308, 29, 14),
(309, 29, 16),
(310, 34, 4),
(311, 34, 8),
(312, 34, 13),
(313, 34, 14),
(314, 34, 15);

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `product_image`
--

DROP TABLE IF EXISTS `product_image`;
CREATE TABLE `product_image` (
  `id` int(11) NOT NULL,
  `image_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;

--
-- A tábla adatainak kiíratása `product_image`
--

INSERT INTO `product_image` (`id`, `image_id`, `product_id`) VALUES
(1, 1, 3),
(2, 2, 3),
(3, 3, 3),
(4, 4, 1),
(5, 5, 1),
(6, 6, 1),
(7, 7, 4),
(8, 8, 4),
(9, 9, 4),
(10, 10, 5),
(11, 11, 5),
(12, 12, 5),
(13, 13, 2),
(14, 14, 2),
(15, 15, 2),
(16, 16, 6),
(17, 17, 6),
(18, 18, 6),
(20, 20, 7),
(21, 21, 7),
(22, 22, 8),
(23, 23, 8),
(24, 24, 8),
(25, 25, 9),
(26, 26, 9),
(27, 27, 9),
(28, 28, 10),
(29, 29, 10),
(30, 30, 10),
(31, 31, 11),
(32, 32, 11),
(33, 33, 11),
(34, 34, 12),
(35, 35, 12),
(36, 36, 12),
(37, 37, 13),
(38, 38, 13),
(39, 39, 13),
(40, 40, 14),
(41, 41, 14),
(42, 42, 14),
(43, 43, 15),
(44, 44, 15),
(45, 45, 15),
(46, 46, 16),
(47, 47, 16),
(48, 48, 16),
(49, 49, 17),
(50, 50, 17),
(51, 51, 17),
(52, 52, 18),
(53, 53, 18),
(54, 54, 18),
(55, 55, 7),
(56, 56, 19),
(57, 57, 19),
(58, 58, 19),
(59, 59, 19),
(64, 64, 21),
(65, 65, 21),
(66, 66, 21),
(67, 67, 22),
(68, 68, 22),
(69, 69, 22),
(70, 70, 23),
(71, 71, 23),
(72, 72, 23),
(73, 73, 24),
(74, 74, 24),
(75, 75, 24),
(76, 76, 25),
(77, 77, 25),
(78, 78, 25),
(79, 79, 26),
(80, 80, 26),
(81, 81, 26),
(82, 82, 27),
(83, 83, 27),
(84, 84, 27),
(85, 85, 28),
(86, 86, 28),
(87, 87, 28),
(88, 88, 29),
(89, 89, 29),
(90, 90, 30),
(91, 91, 30),
(92, 92, 30),
(93, 93, 31),
(94, 94, 31),
(95, 95, 31),
(102, 102, 32),
(103, 103, 32),
(104, 104, 32),
(105, 105, 34),
(106, 106, 34),
(107, 107, 34);

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `product_page`
--

DROP TABLE IF EXISTS `product_page`;
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

--
-- A tábla adatainak kiíratása `product_page`
--

INSERT INTO `product_page` (`id`, `created_at`, `last_modified`, `product_id`, `link_slug`, `category_id`, `subcategory_id`, `page_title`, `page_content`) VALUES
(1, '2025-01-05 16:16:00', '2025-01-05 16:16:00', 1, 'a-nyugodt-elme/nyugtato-novenyek/kamilla-virag', 2, 3, 'Kamilla virág', 'A kamilla teaként fogyasztva relaxációt nyújt, megnyugtatja az emésztőrendszert, és olyan gyengéd, hogy csecsemőknek is adható. Külsőleg alkalmazva lágyítja és nyugtatja a bőrt, és természetes fényt kölcsönöz a hajnak. A növényt történelmileg széles körben használták Európában és Észak-Amerikában teák, fürdők, inhalációk és borogatások formájában.\r\nA kamilla az ókori Egyiptomban is kedvelt volt, ahol az isteneknek szánt áldozatok közé tartozott. Mexikóban manzanillaként ismerik, és a gyomor megnyugtatására, valamint a hangulat emelésére készítenek belőle teát. Az őslakos amerikai törzsek, például a Cherokee és az Aleuták, szintén sokoldalúan használták ezt a növényt.\r\nEz az organikus, sokoldalú növény megérdemelten nyerte el az „alles zutraut” – „mindenre képes” – becenevet.'),
(2, '2025-01-05 16:35:35', '2025-01-05 16:35:35', 2, 'a-nyugodt-elme/nyugtato-novenyek/levendula-virag', 2, 3, 'Levendula virág', 'Már az ókori egyiptomiak, görögök és rómaiak is használták fürdők, ételek és parfümök készítésére. A francia konyhában gyakori fűszer, amely különleges, virágos ízt kölcsönöz desszerteknek és húsoknak.\r\nA levendula az egyik legismertebb illat a világon, amely nyugtató hatása mellett a szépségápolásban és illatosító keverékekben is népszerű.'),
(3, '2025-01-05 17:29:03', '2025-01-05 17:29:03', 3, 'a-tiszta-egeszseg/meregtelenito-novenyek/kurkuma-gyoker', 1, 1, 'Kurkuma gyökér', 'A kurkuma (Curcuma longa) a gyömbérfélék családjába tartozó évelő növény, amely Indiából és Délkelet-Ázsiából származik. Élénk színe és földes íze miatt népszerű fűszer, különösen currykhez, rizses ételekhez és italokhoz, mint az aranytej.\r\nHatóanyaga, a kurkumin, adja aranyló színét és jótékony tulajdonságait. A kurkuma természetes színezékként és hagyományos ájurvédikus gyógyászatban is használatos. Szárított gyökere teákhoz, tinktúrákhoz és természetes festékekhez is kiváló.'),
(4, '2025-01-05 19:43:58', '2025-01-05 19:43:58', 4, 'a-tiszta-egeszseg/meregtelenito-novenyek/kasvirag-gyoker', 1, 1, 'Kasvirág gyökér', 'Az Echinacea angustifolia évezredek óta használatos a hagyományos gyógymódokban, elsősorban az immunrendszer erősítésére. A növény gyökerei teában, tinktúrában vagy olajban alkalmazhatóak, hogy támogassák a test védekezőképességét.\r\nA növény története a helyi amerikai törzsekhez vezethető vissza, akik számos egészségügyi problémára használták. Bár az Echinacea purpurea vált a legismertebb fajjá, az Echinacea angustifolia továbbra is értékes gyógynövény marad.'),
(5, '2025-01-05 19:55:24', '2025-01-05 19:55:24', 5, 'a-tiszta-egeszseg/meregtelenito-novenyek/daralt-fokhagyma', 1, 1, 'Darált fokhagyma', 'A fokhagyma az egyik legismertebb és legszélesebb körben használt gyógynövény, melyet évezredek óta alkalmaznak ízesítésre és gyógyításra. Az Allium sativum a liliomfélék családjába tartozik, és a világ minden táján termesztik. Különleges, erőteljes illatával és ízével a fokhagyma nemcsak a konyhák alapvető fűszere, hanem a tradicionális kínai orvoslásban és az ayurvédikus gyógyászatban is elismert. Az aprított fokhagyma kiválóan használható olajok készítéséhez vagy főzéshez, és segíthet fenntartani a normál koleszterinszintet is.\r\n\r\nA fokhagyma több ezer éve elterjedt kultúrákban, és rengeteg jótékony hatásával híres. Az ókori görög és római orvosok is nagyra értékelték, és mágikus erőt tulajdonítottak neki, mint védelmet a gonosz szellemek és betegségek ellen. Az ayurvédában és a hagyományos kínai orvoslásban is fontos szerepe van. Segíthet az egészséges emésztés támogatásában és a koleszterinszint egyensúlyának megőrzésében.'),
(6, '2025-02-08 17:06:37', '2025-02-08 17:06:37', 6, 'a-konnyed-emesztes/puffadasgatlo-novenyek/anizs', 4, 8, 'Ánizs', 'Az ánizs (Pimpinella anisum) egy édes és jellegzetesen fűszeres illatú növény, amelyet már az ókori Egyiptomban és Görögországban is használtak. Magját leginkább emésztést segítő hatása miatt fogyasztják, de gyakran alkalmazzák teákban, süteményekben és likőrökben is. \r\nAz ánizs természetes görcsoldóként működhet, enyhítheti a puffadást, és segíthet a légutak tisztításában. Illóolaja frissítő hatású, ezért gyakran használják aromaterápiában is.'),
(7, '2025-02-08 17:10:11', '2025-02-08 17:10:11', 6, 'a-konnyed-emesztes/emesztest-tamogatok/anizs', 4, 7, 'Ánizs', 'Az ánizs (Pimpinella anisum) egy édes és jellegzetesen fűszeres illatú növény, amelyet már az ókori Egyiptomban és Görögországban is használtak. Magját leginkább emésztést segítő hatása miatt fogyasztják, de gyakran alkalmazzák teákban, süteményekben és likőrökben is. Az ánizs természetes görcsoldóként működhet, enyhítheti a puffadást, és segíthet a légutak tisztításában. Illóolaja frissítő hatású, ezért gyakran használják aromaterápiában is.'),
(8, '2025-02-08 17:11:38', '2025-02-08 17:11:38', 6, 'a-konyha-izei/fuszernovenyek/anizs', 13, 24, 'Ánizs', 'Az ánizs (Pimpinella anisum) egy édes és jellegzetesen fűszeres illatú növény, amelyet már az ókori Egyiptomban és Görögországban is használtak. Magját leginkább emésztést segítő hatása miatt fogyasztják, de gyakran alkalmazzák teákban, süteményekben és likőrökben is. Az ánizs természetes görcsoldóként működhet, enyhítheti a puffadást, és segíthet a légutak tisztításában. Illóolaja frissítő hatású, ezért gyakran használják aromaterápiában is.'),
(9, '2025-02-08 17:14:33', '2025-02-08 17:14:33', 7, 'a-mozgas-ereje/izuleti-novenyek/arnika', 5, 9, 'Árnika', 'Az árnika (Arnica montana) egy hegyvidéki növény, amelyet hagyományosan bőrnyugtató és gyulladáscsökkentő hatásai miatt használnak. Külsőleg alkalmazva segíthet a zúzódások, izomfájdalmak és ízületi gyulladások enyhítésében. Az árnika kivonata kenőcsök és olajok gyakori összetevője. Belsőleg fogyasztása csak orvosi felügyelet mellett ajánlott, mivel nagyobb dózisban mérgező lehet. Teákban ritkábban alkalmazzák, főként külső borogatásokhoz használatos.'),
(10, '2025-02-08 17:18:55', '2025-02-08 17:18:55', 7, 'a-tiszta-egeszseg/immunerosito-keverekek/arnika', 1, 2, 'Árnika', 'Az árnika (Arnica montana) egy hegyvidéki növény, amelyet hagyományosan bőrnyugtató és gyulladáscsökkentő hatásai miatt használnak. Külsőleg alkalmazva segíthet a zúzódások, izomfájdalmak és ízületi gyulladások enyhítésében. Az árnika kivonata kenőcsök és olajok gyakori összetevője. Belsőleg fogyasztása csak orvosi felügyelet mellett ajánlott, mivel nagyobb dózisban mérgező lehet. Teákban ritkábban alkalmazzák, főként külső borogatásokhoz használatos.'),
(11, '2025-02-08 17:21:48', '2025-02-08 17:21:48', 8, 'a-nyugodt-elme/nyugtato-novenyek/buzavirag', 2, 3, 'Búzavirág', 'A búzavirág (Centaurea cyanus) gyönyörű kék szirmai miatt nemcsak dísznövényként ismert, hanem gyógyhatású teaként is kedvelt. Főként vízhajtó, gyulladáscsökkentő és szemnyugtató hatásai miatt fogyasztják. Teája segíthet a húgyutak egészségének fenntartásában, és enyhítheti a szem fáradtságát borogatásként alkalmazva. A kozmetikai iparban is népszerű, mivel természetes tonizáló \r\nhatású. Gyengéd íze miatt más virágos teakeverékekkel is jól kombinálható.'),
(12, '2025-02-08 17:33:10', '2025-02-08 17:33:10', 8, 'a-konnyed-emesztes/emesztest-tamogatok/buzavirag', 4, 7, 'Búzavirág', 'A búzavirág (Centaurea cyanus) gyönyörű kék szirmai miatt nemcsak dísznövényként ismert, hanem gyógyhatású teaként is kedvelt. Főként vízhajtó, gyulladáscsökkentő és szemnyugtató hatásai miatt fogyasztják. Teája segíthet a húgyutak egészségének fenntartásában, és enyhítheti a szem fáradtságát borogatásként alkalmazva. A kozmetikai iparban is népszerű, mivel természetes tonizáló \r\nhatású. Gyengéd íze miatt más virágos teakeverékekkel is jól kombinálható.'),
(13, '2025-02-08 17:34:06', '2025-02-08 17:34:06', 8, 'a-sziv-egeszsege/keringest-tamogatok/buzavirag', 14, 26, 'Búzavirág', 'A búzavirág (Centaurea cyanus) gyönyörű kék szirmai miatt nemcsak dísznövényként ismert, hanem gyógyhatású teaként is kedvelt. Főként vízhajtó, gyulladáscsökkentő és szemnyugtató hatásai miatt fogyasztják, azonban rendelkezik vértisztító és keringést javító hatásokkal is. Teája segíthet a húgyutak egészségének fenntartásában, és enyhítheti a szem fáradtságát borogatásként alkalmazva. A kozmetikai iparban is népszerű, mivel természetes tonizáló \r\nhatású. Gyengéd íze miatt más virágos teakeverékekkel is jól kombinálható.'),
(14, '2025-02-08 17:39:23', '2025-02-08 17:39:23', 9, 'a-mozgas-ereje/energizalo-novenyek/chili', 5, 10, 'Chili', 'A chili (Capsicum annuum) az egyik legismertebb csípős fűszer, amelyet világszerte használnak az ételek ízesítésére. A benne található kapszaicin felelős az erős, égető érzésért, emellett serkentheti az anyagcserét és javíthatja a vérkeringést. Gyulladáscsökkentő és fájdalomcsillapító hatása miatt külsőleg krémekben is alkalmazzák. Antibakteriális tulajdonságai révén támogathatja az immunrendszert. Frissen, szárítva vagy por formájában is fogyasztható.'),
(15, '2025-02-08 17:42:36', '2025-02-08 17:42:36', 9, 'a-konyha-izei/fuszernovenyek/chili', 13, 24, 'Chili', 'A chili (Capsicum annuum) az egyik legismertebb csípős fűszer, amelyet világszerte használnak az ételek ízesítésére. A benne található kapszaicin felelős az erős, égető érzésért, emellett serkentheti az anyagcserét és javíthatja a vérkeringést. Gyulladáscsökkentő és fájdalomcsillapító hatása miatt külsőleg krémekben is alkalmazzák. Antibakteriális tulajdonságai révén támogathatja az immunrendszert. Frissen, szárítva vagy por formájában is fogyasztható.'),
(16, '2025-02-08 17:42:59', '2025-02-08 17:42:59', 9, 'a-konnyed-emesztes/puffadasgatlo-novenyek/chili', 4, 8, 'Chili', 'A chili (Capsicum annuum) az egyik legismertebb csípős fűszer, amelyet világszerte használnak az ételek ízesítésére. A benne található kapszaicin felelős az erős, égető érzésért, emellett serkentheti az anyagcserét és javíthatja a vérkeringést. Gyulladáscsökkentő és fájdalomcsillapító hatása miatt külsőleg krémekben is alkalmazzák. Antibakteriális tulajdonságai révén támogathatja az immunrendszert. Frissen, szárítva vagy por formájában is fogyasztható.'),
(17, '2025-02-08 17:46:41', '2025-02-08 17:46:41', 10, 'a-nyugodt-elme/nyugtato-novenyek/citromfu', 2, 3, 'Citromfű', 'A Melissa officinalis, vagyis citromfű, a mentafélék családjába tartozó évelő növény, amely friss, citromos illatáról ismert. A mediterrán térségből származik, de mára világszerte elterjedt. Már az ókorban is használták nyugtató és bőrápoló hatásai miatt, valamint aromás teakeverékek és testápolók összetevőjeként. Gyengéd idegnyugtatóként tartják számon, amely elősegíti a relaxációt és a jó közérzetet.\r\nA citromfű nemcsak gyógyászati célokra, hanem konyhai felhasználásra is kiváló. Ízesíthetünk vele lekvárokat, salátákat, hal- és szárnyasételeket, de likőrök készítéséhez is alkalmazzák. Gyakran kombinálják más nyugtató hatású gyógynövényekkel, például valeriánával. Az évszázadok során számos kultúrában spirituális és érzelmi egyensúly elősegítésére is használták.'),
(18, '2025-02-08 18:26:48', '2025-02-08 18:26:48', 10, 'a-nyugodt-elme/alvassegitok/citromfu', 2, 4, 'Citromfű', 'A Melissa officinalis, vagyis citromfű, a mentafélék családjába tartozó évelő növény, amely friss, citromos illatáról ismert. A mediterrán térségből származik, de mára világszerte elterjedt. Már az ókorban is használták nyugtató és bőrápoló hatásai miatt, valamint aromás teakeverékek és testápolók összetevőjeként. Gyengéd idegnyugtatóként tartják számon, amely elősegíti a relaxációt és a jó közérzetet.\r\nA citromfű nemcsak gyógyászati célokra, hanem konyhai felhasználásra is kiváló. Ízesíthetünk vele lekvárokat, salátákat, hal- és szárnyasételeket, de likőrök készítéséhez is alkalmazzák. Gyakran kombinálják más nyugtató hatású gyógynövényekkel, például valeriánával. Az évszázadok során számos kultúrában spirituális és érzelmi egyensúly elősegítésére is használták.'),
(19, '2025-02-08 18:28:47', '2025-02-08 18:28:47', 11, 'a-tiszta-egeszseg/immunerosito-keverekek/csipkebogyo', 1, 2, 'Csipkebogyó', 'A csipkebogyó (Rosa canina) az egyik legjobb természetes C-vitamin-forrás, amely erősítheti az immunrendszert és támogathatja a szervezet védekezőképességét. Teája kellemesen savanykás ízű, gyakran fogyasztják a hidegebb hónapokban. Antioxidánsokban gazdag, ezért hozzájárulhat a bőr egészségéhez és a sejtek védelméhez. Gyulladáscsökkentő tulajdonságai révén segíthet az ízületi panaszok enyhítésében. Szárított formában vagy lekvárként is fogyasztható.'),
(20, '2025-02-08 18:34:06', '2025-02-08 18:34:06', 11, 'az-erdo-ajandeka/erdei-gyogynovenyek/csipkebogyo', 15, 28, 'Csipkebogyó', 'A csipkebogyó (Rosa canina) az egyik legjobb természetes C-vitamin-forrás, amely erősítheti az immunrendszert és támogathatja a szervezet védekezőképességét. Teája kellemesen savanykás ízű, gyakran fogyasztják a hidegebb hónapokban. Antioxidánsokban gazdag, ezért hozzájárulhat a bőr egészségéhez és a sejtek védelméhez. Gyulladáscsökkentő tulajdonságai révén segíthet az ízületi panaszok enyhítésében. Szárított formában vagy lekvárként is fogyasztható.'),
(21, '2025-02-08 18:36:23', '2025-02-08 18:36:23', 12, 'a-sziv-egeszsege/keringest-tamogatok/galagonya-bogyok', 14, 26, 'Galagonya bogyók', 'A Crataegus monogyna, vagyis galagonya, hagyományosan a szív és az érrendszer támogatására szolgáló gyógynövény. A bogyókat évszázadok óta készítik lekvárok, borok, szirupok és tinktúrák formájában. Az egyik legismertebb gyógynövény, amelyet a keringési rendszer egészségének támogatására használnak.\r\n\r\nA galagonya C-vitaminban gazdag, és gyakran kombinálják más gyógynövényekkel teákban és keverékekben. Az ókori kultúrákban is használták energetizáló és védelmező növényként. Gyakran készítenek belőle teát vagy alkoholos kivonatot, amelyeket rendszeresen fogyasztanak a szív erősítésére és az érzelmi stabilitás fokozására.'),
(22, '2025-02-08 18:51:23', '2025-02-08 18:51:23', 12, 'a-sziv-egeszsege/vernyomascsokkento-novenyek/galagonya-bogyok', 14, 27, 'Galagonya bogyók', 'A Crataegus monogyna, vagyis galagonya, hagyományosan a szív és az érrendszer támogatására szolgáló gyógynövény. A bogyókat évszázadok óta készítik lekvárok, borok, szirupok és tinktúrák formájában. Az egyik legismertebb gyógynövény, amelyet a keringési rendszer egészségének támogatására használnak.\r\nA galagonya C-vitaminban gazdag, és gyakran kombinálják más gyógynövényekkel teákban és keverékekben. Az ókori kultúrákban is használták energetizáló és védelmező növényként. Gyakran készítenek belőle teát vagy alkoholos kivonatot, amelyeket rendszeresen fogyasztanak a szív erősítésére és az érzelmi stabilitás fokozására.'),
(23, '2025-02-08 18:55:03', '2025-02-08 18:55:03', 13, 'az-energia-alapjai/vitalizalo-novenyek/ginzeng-gyoker', 8, 15, 'Ginzeng gyökér', 'A Panax ginseng az Araliaceae családba tartozó növény, amely Kína és Korea hegyvidéki területein őshonos. A ginzeng gyökerét a hagyományos kínai gyógyászatban több ezer éve használják a vitalitás és az energiaszint növelésére. A vörös ginzeng gőzöléssel és szárítással készül, míg a fehér ginzenget természetes módon szárítják.\r\nA ginzeng gyökér porrá őrölve, teaként vagy tinktúrában is fogyasztható. Kínai filozófia szerint a vörös ginzeng melegítő, míg a fehér hűsítő hatású. Nemcsak gyógyászati célokra, hanem levesekhez, italokhoz és energiafokozóként is használják. A Qi energiát erősítő tulajdonságai miatt évszázadok óta nagy becsben tartják.'),
(24, '2025-02-08 18:57:38', '2025-02-08 18:57:38', 13, 'a-mozgas-ereje/energizalo-novenyek/ginzeng-gyoker', 5, 10, 'Ginzeng gyökér', 'A Panax ginseng az Araliaceae családba tartozó növény, amely Kína és Korea hegyvidéki területein őshonos. A ginzeng gyökerét a hagyományos kínai gyógyászatban több ezer éve használják a vitalitás és az energiaszint növelésére. A vörös ginzeng gőzöléssel és szárítással készül, míg a fehér ginzenget természetes módon szárítják.\r\n\r\nA ginzeng gyökér porrá őrölve, teaként vagy tinktúrában is fogyasztható. Kínai filozófia szerint a vörös ginzeng melegítő, míg a fehér hűsítő hatású. Nemcsak gyógyászati célokra, hanem levesekhez, italokhoz és energiafokozóként is használják. A Qi energiát erősítő tulajdonságai miatt évszázadok óta nagy becsben tartják.'),
(25, '2025-02-08 18:59:36', '2025-02-08 18:59:36', 14, 'a-szepseg-titka/borapolo-novenyek/koromvirag', 7, 13, 'Körömvirág', 'A körömvirág (Calendula officinalis) az egyik legnépszerűbb gyógynövény a bőrproblémák kezelésére. Külsőleg alkalmazva segíthet a sebgyógyulásban, enyhítheti a bőr irritációját és csökkentheti a gyulladásokat. Teája belsőleg is fogyasztható, támogathatja az emésztőrendszer egészségét és enyhítheti a gyomorpanaszokat. Antibakteriális tulajdonságai miatt természetes fertőtlenítőszerként is alkalmazzák. Krémek, olajok és fürdőadalékok formájában is népszerű.'),
(26, '2025-02-08 19:06:12', '2025-02-08 19:06:12', 15, 'a-tiszta-egeszseg/meregtelenito-novenyek/pillangoborso', 1, 1, 'Pillangóborsó', 'A pillangóborsó (Clitoria ternatea) élénk kék virága miatt különleges gyógynövény, amely antioxidánsokban gazdag. Teája színváltó tulajdonságú: citromlé hozzáadásával kékből lilává változik. Javíthatja a memóriát, támogathatja az idegrendszert és segíthet a stressz csökkentésében. Gyakran alkalmazzák bőrápolásra és hajerősítésre is. Természetes színezőanyagként sütemények és italok díszítésére is használják.'),
(27, '2025-02-08 19:14:32', '2025-02-08 19:14:32', 16, 'a-tiszta-egeszseg/immunerosito-keverekek/reishi', 1, 2, 'Reishi', 'A reishi gomba (Ganoderma lucidum), más néven pecsétviaszgomba, az egyik legértékesebb és legrégebben használt gyógygomba a hagyományos kínai orvoslásban. Immunerősítő hatásáról ismert, segíthet a szervezet természetes védekezőképességének fokozásában, valamint támogathatja a stresszkezelést és a nyugodt alvást. Gyulladáscsökkentő tulajdonságai révén hozzájárulhat az általános egészség fenntartásához, és támogathatja a szív- és érrendszert.\r\nA reishi természetes adaptogénként működik, vagyis segíthet a szervezetnek alkalmazkodni a fizikai és mentális kihívásokhoz. Rendszeres fogyasztása csökkentheti a fáradtságot, növelheti a vitalitást és javíthatja az általános közérzetet. Teaként vagy por formájában is fogyasztható, de mivel íze kissé kesernyés és földes jellegű, gyakran kombinálják más gyógynövényekkel, például gyömbérrel vagy citromfűvel. Antioxidánsokban gazdag, így segíthet a sejtek védelmében és az öregedési folyamatok lassításában.'),
(28, '2025-02-08 19:17:51', '2025-02-08 19:17:51', 16, 'a-nyugodt-elme/nyugtato-novenyek/reishi', 2, 3, 'Reishi', 'A reishi gomba (Ganoderma lucidum), más néven pecsétviaszgomba, az egyik legértékesebb és legrégebben használt gyógygomba a hagyományos kínai orvoslásban. Immunerősítő hatásáról ismert, segíthet a szervezet természetes védekezőképességének fokozásában, valamint támogathatja a stresszkezelést és a nyugodt alvást. Gyulladáscsökkentő tulajdonságai révén hozzájárulhat az általános egészség fenntartásához, és támogathatja a szív- és érrendszert.\r\nA reishi természetes adaptogénként működik, vagyis segíthet a szervezetnek alkalmazkodni a fizikai és mentális kihívásokhoz. Rendszeres fogyasztása csökkentheti a fáradtságot, növelheti a vitalitást és javíthatja az általános közérzetet. Teaként vagy por formájában is fogyasztható, de mivel íze kissé kesernyés és földes jellegű, gyakran kombinálják más gyógynövényekkel, például gyömbérrel vagy citromfűvel. Antioxidánsokban gazdag, így segíthet a sejtek védelmében és az öregedési folyamatok lassításában.'),
(29, '2025-02-08 19:21:56', '2025-02-08 19:21:56', 17, 'a-konnyed-emesztes/emesztest-tamogatok/rozmaring', 4, 7, 'Rozmaring', 'A rozmaring (Rosmarinus officinalis) egy aromás mediterrán fűszernövény, amelyet nemcsak kulináris célokra, hanem gyógynövényként is széles körben alkalmaznak. Jellegzetes, fűszeres illata élénkítő hatású, és elősegítheti a koncentrációt, ezért gyakran használják természetes memóriaserkentőként. Serkentheti a vérkeringést, különösen a fejbőrben, így segíthet a hajnövekedés elősegítésében és a hajhullás csökkentésében. Illóolaj formájában masszázshoz és aromaterápiához is alkalmazzák.\r\nGyógyhatásai közé tartozik az emésztés javítása, mivel serkentheti az epetermelést, enyhítheti a puffadást és támogathatja a májműködést. Antibakteriális és gyulladáscsökkentő tulajdonságai révén hozzájárulhat a légúti fertőzések enyhítéséhez, valamint segíthet a torokfájás és a köhögés csillapításában. Teája élénkítő hatású, és segíthet a fáradtság leküzdésében, így kiváló választás lehet kimerültség vagy alacsony energiaszint esetén.'),
(30, '2025-02-08 19:24:52', '2025-02-08 19:24:52', 17, 'a-konyha-izei/fuszernovenyek/rozmaring', 13, 24, 'Rozmaring', 'A rozmaring (Rosmarinus officinalis) egy aromás mediterrán fűszernövény, amelyet nemcsak kulináris célokra, hanem gyógynövényként is széles körben alkalmaznak. Jellegzetes, fűszeres illata élénkítő hatású, és elősegítheti a koncentrációt, ezért gyakran használják természetes memóriaserkentőként. Serkentheti a vérkeringést, különösen a fejbőrben, így segíthet a hajnövekedés elősegítésében és a hajhullás csökkentésében. Illóolaj formájában masszázshoz és aromaterápiához is alkalmazzák.\r\nGyógyhatásai közé tartozik az emésztés javítása, mivel serkentheti az epetermelést, enyhítheti a puffadást és támogathatja a májműködést. Antibakteriális és gyulladáscsökkentő tulajdonságai révén hozzájárulhat a légúti fertőzések enyhítéséhez, valamint segíthet a torokfájás és a köhögés csillapításában. Teája élénkítő hatású, és segíthet a fáradtság leküzdésében, így kiváló választás lehet kimerültség vagy alacsony energiaszint esetén.'),
(31, '2025-02-08 19:27:00', '2025-02-08 19:27:00', 18, 'a-nyugodt-elme/nyugtato-novenyek/valeriana-gyoker', 2, 3, 'Valeriána gyökér', 'A Valeriana officinalis a loncfélék családjába tartozó évelő növény, amelyet régóta használnak nyugtató hatása miatt. Európában és Ázsiában honos, de Észak-Amerikában is elterjedt. Gyökere erőteljes, jellegzetes illatú, amely macskákat és rágcsálókat vonz. A valeriána tea vagy tinktúra formájában fogyasztható, és segíti a természetes ellazulást, valamint a stressz kezelését. Az ókori görögök és középkori gyógyítók is használták nyugtató hatásai miatt.\r\nA növény akár két méter magasra is megnő, fehér vagy rózsaszín virágokat hoz. Gyökerét alacsony hőmérsékleten szárítják, hogy megőrizzék hatóanyagait. Noha illata sokak számára kellemetlen, évszázadok óta népszerű gyógynövényként alkalmazzák. A valeriána különösen hasznos esti teaként vagy gyógynövényes keverékek részeként.'),
(32, '2025-02-08 19:31:03', '2025-02-08 19:31:03', 18, 'a-nyugodt-elme/alvassegitok/valeriana-gyoker', 2, 4, 'Valeriána gyökér', 'A Valeriana officinalis a loncfélék családjába tartozó évelő növény, amelyet régóta használnak nyugtató hatása miatt. Európában és Ázsiában honos, de Észak-Amerikában is elterjedt. Gyökere erőteljes, jellegzetes illatú, amely macskákat és rágcsálókat vonz. A valeriána tea vagy tinktúra formájában fogyasztható, és segíti a természetes ellazulást, valamint a stressz kezelését. Az ókori görögök és középkori gyógyítók is használták nyugtató hatásai miatt.\r\nA növény akár két méter magasra is megnő, fehér vagy rózsaszín virágokat hoz. Gyökerét alacsony hőmérsékleten szárítják, hogy megőrizzék hatóanyagait. Noha illata sokak számára kellemetlen, évszázadok óta népszerű gyógynövényként alkalmazzák. A valeriána különösen hasznos esti teaként vagy gyógynövényes keverékek részeként.'),
(33, '2025-02-09 10:28:54', '2025-02-09 10:28:54', 19, 'a-nyugodt-elme/alvassegitok/ashwagandha-por', 2, 4, 'Ashwagandha por', 'Az Ashwagandha, más néven „indiai ginzeng”, a burgonyafélék családjába tartozó évelő cserje, amely főként Indiában és néhány afrikai régióban honos. Az ájurvéda évszázadok óta alkalmazza a növény gyökerét és leveleit, főleg szárítva és porrá őrölve, hogy támogassa a test és az elme egyensúlyát.\r\n\r\nElsősorban Indiában termesztik, ahol a forró, száraz klíma és a termékeny talaj kiváló környezetet biztosít a növény növekedéséhez. Hagyományosan kis, családi gazdaságokban is megtalálható, ahol nagy gondot fordítanak a növények kézzel történő betakarítására és kíméletes szárítására.\r\n\r\n- Stresszoldás és nyugodt elme: Adaptogén hatású, vagyis segíthet a szervezetnek alkalmazkodni a mindennapi stresszhez, és csökkentheti a túlzott idegességet.\r\n- Hormonális egyensúly: Hozzájárulhat a normál hormonháztartás fenntartásához mind nők, mind férfiak esetében.\r\n- Energetizálás és állóképesség: Támogathatja a fizikai teljesítményt és a szellemi frissességet, hozzájárulva a hosszú távú vitalitáshoz.\r\n- Immunrendszer-erősítés: Rendszeres fogyasztása támogathatja a test védekező mechanizmusait.\r\n\r\nPor alakban sokoldalúan felhasználható: keverhető meleg italokba (teákba, „aranytej”-hez), smoothie-khoz, de akár levesek vagy egyéb ételek dúsítására is alkalmas.'),
(35, '2025-02-09 10:49:00', '2025-02-09 10:49:00', 19, 'a-pillanat-langja/stresszcsokkento-novenyek/ashwagandha-por', 6, 12, 'Ashwagandha por', 'Az Ashwagandha, más néven „indiai ginzeng”, a burgonyafélék családjába tartozó évelő cserje, amely főként Indiában és néhány afrikai régióban honos. Az ájurvéda évszázadok óta alkalmazza a növény gyökerét és leveleit, főleg szárítva és porrá őrölve, hogy támogassa a test és az elme egyensúlyát.\r\n\r\nElsősorban Indiában termesztik, ahol a forró, száraz klíma és a termékeny talaj kiváló környezetet biztosít a növény növekedéséhez. Hagyományosan kis, családi gazdaságokban is megtalálható, ahol nagy gondot fordítanak a növények kézzel történő betakarítására és kíméletes szárítására.\r\n\r\n- Stresszoldás és nyugodt elme: Adaptogén hatású, vagyis segíthet a szervezetnek alkalmazkodni a mindennapi stresszhez, és csökkentheti a túlzott idegességet.\r\n- Hormonális egyensúly: Hozzájárulhat a normál hormonháztartás fenntartásához mind nők, mind férfiak esetében.\r\n- Energetizálás és állóképesség: Támogathatja a fizikai teljesítményt és a szellemi frissességet, hozzájárulva a hosszú távú vitalitáshoz.\r\n- Immunrendszer-erősítés: Rendszeres fogyasztása támogathatja a test védekező mechanizmusait.\r\n\r\nPor alakban sokoldalúan felhasználható: keverhető meleg italokba (teákba, „aranytej”-hez), smoothie-khoz, de akár levesek vagy egyéb ételek dúsítására is alkalmas.'),
(36, '2025-02-09 11:28:34', '2025-02-09 11:28:34', 19, 'a-nyugodt-elme/nyugtato-novenyek/ashwagandha-por', 2, 3, 'Ashwagandha por', 'Az Ashwagandha, más néven „indiai ginzeng”, a burgonyafélék családjába tartozó évelő cserje, amely főként Indiában és néhány afrikai régióban honos. Az ájurvéda évszázadok óta alkalmazza a növény gyökerét és leveleit, főleg szárítva és porrá őrölve, hogy támogassa a test és az elme egyensúlyát.\r\n\r\nElsősorban Indiában termesztik, ahol a forró, száraz klíma és a termékeny talaj kiváló környezetet biztosít a növény növekedéséhez. Hagyományosan kis, családi gazdaságokban is megtalálható, ahol nagy gondot fordítanak a növények kézzel történő betakarítására és kíméletes szárítására.\r\n\r\n- Stresszoldás és nyugodt elme: Adaptogén hatású, vagyis segíthet a szervezetnek alkalmazkodni a mindennapi stresszhez, és csökkentheti a túlzott idegességet.\r\n- Hormonális egyensúly: Hozzájárulhat a normál hormonháztartás fenntartásához mind nők, mind férfiak esetében.\r\n- Energetizálás és állóképesség: Támogathatja a fizikai teljesítményt és a szellemi frissességet, hozzájárulva a hosszú távú vitalitáshoz.\r\n- Immunrendszer-erősítés: Rendszeres fogyasztása támogathatja a test védekező mechanizmusait.\r\n\r\nPor alakban sokoldalúan felhasználható: keverhető meleg italokba (teákba, „aranytej”-hez), smoothie-khoz, de akár levesek vagy egyéb ételek dúsítására is alkalmas.'),
(37, '2025-02-09 11:33:11', '2025-02-09 11:33:11', 21, 'a-tiszta-egeszseg/immunerosito-keverekek/ashwagandha-durvara-orolt', 1, 2, 'Ashwagandha durvára őrölt', 'Az Ashwagandha, más néven „indiai ginzeng”, egy évelő cserje a burgonyafélék családjából, amely főként a forró, száraz klímájú területeken nő. A növény gyökerét és leveleit évszázadok óta szárítják és aprítják - vagy finom porrá, vagy durva, „nagyszemű” őrleménnyé dolgozzák fel, attól függően, milyen felhasználási módra szánják.\r\n\r\n- Hagyományos felhasználás: Az ájurvédikus gyógyászatban az Ashwagandhát a stressz és a fáradtság enyhítésére, valamint a szervezet általános ellenállóképességének fokozására alkalmazzák.\r\n- Termesztés és betakarítás: Indiában jellemzően kis, családi gazdaságok gondozzák a növényeket, ahol nagy figyelmet fordítanak a kézi betakarításra és a kíméletes szárításra.\r\n\r\nFelhasználási tippek:\r\n- Teaként (forrázással vagy főzetként)\r\n- Turmixokban és smoothiekban\r\n- Krémlevesek, szószok ízesítésére vagy dúsítására\r\n\r\nElőny a durvára őrölt verziónál: A nagyobb szemcsék lassabban adják le hatóanyagaikat, ezért alkalmanként intenzívebb, „földesebb” íz és aroma érhető el vele (például hosszabb főzési idejű teákban).'),
(38, '2025-02-09 11:37:56', '2025-02-09 11:37:56', 21, 'a-nyugodt-elme/nyugtato-novenyek/ashwagandha-durvara-orolt', 2, 3, 'Ashwagandha durvára őrölt', 'Az Ashwagandha, más néven „indiai ginzeng”, egy évelő cserje a burgonyafélék családjából, amely főként a forró, száraz klímájú területeken nő. A növény gyökerét és leveleit évszázadok óta szárítják és aprítják – vagy finom porrá, vagy durva, „nagyszemű” őrleménnyé dolgozzák fel, attól függően, milyen felhasználási módra szánják.\r\n\r\n- Hagyományos felhasználás: Az ájurvédikus gyógyászatban az Ashwagandhát a stressz és a fáradtság enyhítésére, valamint a szervezet általános ellenállóképességének fokozására alkalmazzák.\r\n- Termesztés és betakarítás: Indiában jellemzően kis, családi gazdaságok gondozzák a növényeket, ahol nagy figyelmet fordítanak a kézi betakarításra és a kíméletes szárításra.\r\n\r\nFelhasználási tippek:\r\n- Teaként (forrázással vagy főzetként)\r\n- Turmixokban és smoothiekban\r\n- Krémlevesek, szószok ízesítésére vagy dúsítására\r\n\r\nElőny a durvára őrölt verziónál: A nagyobb szemcsék lassabban adják le hatóanyagaikat, ezért alkalmanként intenzívebb, „földesebb” íz és aroma érhető el vele (például hosszabb főzési idejű teákban).'),
(39, '2025-02-09 11:38:13', '2025-02-09 11:38:13', 21, 'a-nyugodt-elme/alvassegitok/ashwagandha-durvara-orolt', 2, 4, 'Ashwagandha durvára őrölt', 'Az Ashwagandha, más néven „indiai ginzeng”, egy évelő cserje a burgonyafélék családjából, amely főként a forró, száraz klímájú területeken nő. A növény gyökerét és leveleit évszázadok óta szárítják és aprítják – vagy finom porrá, vagy durva, „nagyszemű” őrleménnyé dolgozzák fel, attól függően, milyen felhasználási módra szánják.\r\n\r\n- Hagyományos felhasználás: Az ájurvédikus gyógyászatban az Ashwagandhát a stressz és a fáradtság enyhítésére, valamint a szervezet általános ellenállóképességének fokozására alkalmazzák.\r\n- Termesztés és betakarítás: Indiában jellemzően kis, családi gazdaságok gondozzák a növényeket, ahol nagy figyelmet fordítanak a kézi betakarításra és a kíméletes szárításra.\r\n\r\nFelhasználási tippek:\r\n- Teaként (forrázással vagy főzetként)\r\n- Turmixokban és smoothiekban\r\n- Krémlevesek, szószok ízesítésére vagy dúsítására\r\n\r\nElőny a durvára őrölt verziónál: A nagyobb szemcsék lassabban adják le hatóanyagaikat, ezért alkalmanként intenzívebb, „földesebb” íz és aroma érhető el vele (például hosszabb főzési idejű teákban).'),
(40, '2025-02-09 11:38:56', '2025-02-09 11:38:56', 21, 'a-pillanat-langja/stresszcsokkento-novenyek/ashwagandha-durvara-orolt', 6, 12, 'Ashwagandha durvára őrölt', 'Az Ashwagandha, más néven „indiai ginzeng”, egy évelő cserje a burgonyafélék családjából, amely főként a forró, száraz klímájú területeken nő. A növény gyökerét és leveleit évszázadok óta szárítják és aprítják – vagy finom porrá, vagy durva, „nagyszemű” őrleménnyé dolgozzák fel, attól függően, milyen felhasználási módra szánják.\r\n\r\n- Hagyományos felhasználás: Az ájurvédikus gyógyászatban az Ashwagandhát a stressz és a fáradtság enyhítésére, valamint a szervezet általános ellenállóképességének fokozására alkalmazzák.\r\n- Termesztés és betakarítás: Indiában jellemzően kis, családi gazdaságok gondozzák a növényeket, ahol nagy figyelmet fordítanak a kézi betakarításra és a kíméletes szárításra.\r\n\r\nFelhasználási tippek:\r\n- Teaként (forrázással vagy főzetként)\r\n- Turmixokban és smoothiekban\r\n- Krémlevesek, szószok ízesítésére vagy dúsítására\r\n\r\nElőny a durvára őrölt verziónál: A nagyobb szemcsék lassabban adják le hatóanyagaikat, ezért alkalmanként intenzívebb, „földesebb” íz és aroma érhető el vele (például hosszabb főzési idejű teákban).'),
(41, '2025-02-09 11:39:13', '2025-02-09 11:39:13', 21, 'a-pillanat-langja/erzeki-novenykincsek/ashwagandha-durvara-orolt', 6, 31, 'Ashwagandha durvára őrölt', 'Az Ashwagandha, más néven „indiai ginzeng”, egy évelő cserje a burgonyafélék családjából, amely főként a forró, száraz klímájú területeken nő. A növény gyökerét és leveleit évszázadok óta szárítják és aprítják – vagy finom porrá, vagy durva, „nagyszemű” őrleménnyé dolgozzák fel, attól függően, milyen felhasználási módra szánják.\r\n\r\n- Hagyományos felhasználás: Az ájurvédikus gyógyászatban az Ashwagandhát a stressz és a fáradtság enyhítésére, valamint a szervezet általános ellenállóképességének fokozására alkalmazzák.\r\n- Termesztés és betakarítás: Indiában jellemzően kis, családi gazdaságok gondozzák a növényeket, ahol nagy figyelmet fordítanak a kézi betakarításra és a kíméletes szárításra.\r\n\r\nFelhasználási tippek:\r\n- Teaként (forrázással vagy főzetként)\r\n- Turmixokban és smoothiekban\r\n- Krémlevesek, szószok ízesítésére vagy dúsítására\r\n\r\nElőny a durvára őrölt verziónál: A nagyobb szemcsék lassabban adják le hatóanyagaikat, ezért alkalmanként intenzívebb, „földesebb” íz és aroma érhető el vele (például hosszabb főzési idejű teákban).'),
(42, '2025-02-09 11:47:03', '2025-02-09 11:47:03', 22, 'a-pillanat-langja/stresszcsokkento-novenyek/damiana-orolt', 6, 12, 'Damiana őrölt', 'A Damiana, más néven „Mexikói Turneravirág”, apró, sárga virágaival és aromás leveleivel tűnik ki. Már az őslakos maja és azték kultúrákban is ismert és nagyra becsült növény volt, főként a hangulat és a libidó támogatására. Édeskés, kissé fűszeres illata és íze miatt gyakran keverik más gyógynövényekkel, hogy komplexebb, kiegyensúlyozottabb hatású főzeteket vagy füstölő-keverékeket hozzanak létre.\r\n\r\n- Aphrodisiakum-hagyomány: A népi gyógyászat szerint élénkítheti a szexuális vágyat és a hormonális működést, segítve mind a nőket, mind a férfiakat.\r\n- Hangulatjavító tulajdonság: Enyhe tonizáló és idegrendszert kiegyensúlyozó hatást tulajdonítanak neki, ezért egyesek stresszcsökkentő, lelki állapotot harmonizáló „növényi támogatásként” alkalmazzák.\r\n\r\nFelhasználási javaslatok:\r\n- Tea: Önállóan vagy más gyógynövényekkel vegyítve (pl. citromfű, levendula) kellemes, enyhén fűszeres aromájú főzetet ad.\r\n- Füstölő: Tömjénnel vagy más füstölőnövényekkel keverve különleges, édeskés illatot kínál.\r\n- Egyéb: Likőrök és tinktúrák alapanyagaként is régóta használják.'),
(43, '2025-02-09 11:50:48', '2025-02-09 11:50:48', 22, 'a-pillanat-langja/erzeki-novenykincsek/damiana-orolt', 6, 31, 'Damiana őrölt', 'A Damiana, más néven „Mexikói Turneravirág”, apró, sárga virágaival és aromás leveleivel tűnik ki. Már az őslakos maja és azték kultúrákban is ismert és nagyra becsült növény volt, főként a hangulat és a libidó támogatására. Édeskés, kissé fűszeres illata és íze miatt gyakran keverik más gyógynövényekkel, hogy komplexebb, kiegyensúlyozottabb hatású főzeteket vagy füstölő-keverékeket hozzanak létre.\r\n\r\n- Aphrodisiakum-hagyomány: A népi gyógyászat szerint élénkítheti a szexuális vágyat és a hormonális működést, segítve mind a nőket, mind a férfiakat.\r\n- Hangulatjavító tulajdonság: Enyhe tonizáló és idegrendszert kiegyensúlyozó hatást tulajdonítanak neki, ezért egyesek stresszcsökkentő, lelki állapotot harmonizáló „növényi támogatásként” alkalmazzák.\r\n\r\nFelhasználási javaslatok:\r\n- Tea: Önállóan vagy más gyógynövényekkel vegyítve (pl. citromfű, levendula) kellemes, enyhén fűszeres aromájú főzetet ad.\r\n- Füstölő: Tömjénnel vagy más füstölőnövényekkel keverve különleges, édeskés illatot kínál.\r\n- Egyéb: Likőrök és tinktúrák alapanyagaként is régóta használják.'),
(44, '2025-02-09 11:51:23', '2025-02-09 11:51:23', 22, 'a-nyugodt-elme/nyugtato-novenyek/damiana-orolt', 2, 3, 'Damiana őrölt', 'A Damiana, más néven „Mexikói Turneravirág”, apró, sárga virágaival és aromás leveleivel tűnik ki. Már az őslakos maja és azték kultúrákban is ismert és nagyra becsült növény volt, főként a hangulat és a libidó támogatására. Édeskés, kissé fűszeres illata és íze miatt gyakran keverik más gyógynövényekkel, hogy komplexebb, kiegyensúlyozottabb hatású főzeteket vagy füstölő-keverékeket hozzanak létre.\r\n\r\n- Aphrodisiakum-hagyomány: A népi gyógyászat szerint élénkítheti a szexuális vágyat és a hormonális működést, segítve mind a nőket, mind a férfiakat.\r\n- Hangulatjavító tulajdonság: Enyhe tonizáló és idegrendszert kiegyensúlyozó hatást tulajdonítanak neki, ezért egyesek stresszcsökkentő, lelki állapotot harmonizáló „növényi támogatásként” alkalmazzák.\r\n\r\nFelhasználási javaslatok:\r\n- Tea: Önállóan vagy más gyógynövényekkel vegyítve (pl. citromfű, levendula) kellemes, enyhén fűszeres aromájú főzetet ad.\r\n- Füstölő: Tömjénnel vagy más füstölőnövényekkel keverve különleges, édeskés illatot kínál.\r\n- Egyéb: Likőrök és tinktúrák alapanyagaként is régóta használják.'),
(45, '2025-02-09 12:02:32', '2025-02-09 12:02:32', 23, 'a-pillanat-langja/erzeki-novenykincsek/kanos-kecskefu-orolt', 6, 31, 'Kanos Kecskefű őrölt', 'Az Epimedium nemzetségbe tartozó növények (gyakran Epimedium sagittatum vagy Epimedium grandiflorum fajként forgalmazzák) őshazája főként Kínában és Japánban található, de a világ számos pontján termesztik. A „Horny Goat Weed” elnevezés mögött egy régi legenda áll, amely szerint a kecskepásztorok észrevették, hogy állataik kifejezetten „élénkek” lesznek, miután legelésztek ebből a növényből.\r\n\r\nHagyományos felhasználás\r\n- Aphrodisiakum: A népi gyógyászat szerint elősegíti a szexuális vágyat és fokozza a libidót.\r\n- Energetizáló hatás: Enyhe stimulánsként tartják számon, segítheti a testi és szellemi frissességet.\r\n- Keringés támogatása: Előfordulhat, hogy javítja a véráramlást, ezáltal hozzájárul a jobb állóképességhez.\r\n\r\nFelhasználási javaslatok\r\n- Tea vagy főzet: A szárított, darabos leveleket forró vízzel leöntve, kb. 10-15 percig áztatva fogyaszthatod. Íze enyhén kesernyés-fűszeres, így más, aromás gyógynövényekkel (pl. citromfű, borsmenta) érdemes kombinálni.\r\n- Tinktúra: Sokan alkoholos kivonatot készítenek belőle, hogy koncentrált formában használják ki a hatóanyagokat (ám ez a termék magában nem tinktúra, hanem darabolt levél).\r\n- Egyéb keverékek: Belekeverhető házi füstölő mixekbe is, de ez esetben fontos az óvatosság és a kis dózis alkalmazása.\r\n\r\nMegjegyzés az adagoláshoz: Mivel az Epimedium erősebb hatóanyagokat is tartalmaz (pl. ikarin), érdemes kisebb mennyiséggel kezdeni, és figyelni a szervezet reakcióit.'),
(46, '2025-02-09 12:13:59', '2025-02-09 12:13:59', 23, 'a-mozgas-ereje/energizalo-novenyek/kanos-kecskefu-orolt', 5, 10, 'Kanos Kecskefű őrölt', 'Az Epimedium nemzetségbe tartozó növények (gyakran Epimedium sagittatum vagy Epimedium grandiflorum fajként forgalmazzák) őshazája főként Kínában és Japánban található, de a világ számos pontján termesztik. A „Horny Goat Weed” elnevezés mögött egy régi legenda áll, amely szerint a kecskepásztorok észrevették, hogy állataik kifejezetten „élénkek” lesznek, miután legelésztek ebből a növényből.\r\n\r\nHagyományos felhasználás\r\n- Aphrodisiakum: A népi gyógyászat szerint elősegíti a szexuális vágyat és fokozza a libidót.\r\n- Energetizáló hatás: Enyhe stimulánsként tartják számon, segítheti a testi és szellemi frissességet.\r\n- Keringés támogatása: Előfordulhat, hogy javítja a véráramlást, ezáltal hozzájárul a jobb állóképességhez.\r\n\r\nFelhasználási javaslatok\r\n- Tea vagy főzet: A szárított, darabos leveleket forró vízzel leöntve, kb. 10-15 percig áztatva fogyaszthatod. Íze enyhén kesernyés-fűszeres, így más, aromás gyógynövényekkel (pl. citromfű, borsmenta) érdemes kombinálni.\r\n- Tinktúra: Sokan alkoholos kivonatot készítenek belőle, hogy koncentrált formában használják ki a hatóanyagokat (ám ez a termék magában nem tinktúra, hanem darabolt levél).\r\n- Egyéb keverékek: Belekeverhető házi füstölő mixekbe is, de ez esetben fontos az óvatosság és a kis dózis alkalmazása.\r\n\r\nMegjegyzés az adagoláshoz: Mivel az Epimedium erősebb hatóanyagokat is tartalmaz (pl. ikarin), érdemes kisebb mennyiséggel kezdeni, és figyelni a szervezet reakcióit.'),
(47, '2025-02-09 12:33:53', '2025-02-09 12:33:53', 24, 'a-pillanat-langja/erzeki-novenykincsek/kanos-kecskefu-por', 6, 31, 'Kanos Kecskefű por', 'Az Epimedium nemzetségbe tartozó növények (gyakran Epimedium sagittatum vagy Epimedium grandiflorum fajként forgalmazzák) őshazája főként Kínában és Japánban található, de a világ számos pontján termesztik. A „Horny Goat Weed” elnevezés mögött egy régi legenda áll, amely szerint a kecskepásztorok észrevették, hogy állataik kifejezetten „élénkek” lesznek, miután legelésztek ebből a növényből.\r\n\r\nHagyományos felhasználás\r\n- Aphrodisiakum: A népi gyógyászat szerint elősegíti a szexuális vágyat és fokozza a libidót.\r\n- Energetizáló hatás: Enyhe stimulánsként tartják számon, segítheti a testi és szellemi frissességet.\r\n- Keringés támogatása: Előfordulhat, hogy javítja a véráramlást, ezáltal hozzájárul a jobb állóképességhez.\r\n\r\nFelhasználási javaslatok\r\n- Tea vagy főzet: A szárított, darabos leveleket forró vízzel leöntve, kb. 10-15 percig áztatva fogyaszthatod. Íze enyhén kesernyés-fűszeres, így más, aromás gyógynövényekkel (pl. citromfű, borsmenta) érdemes kombinálni.\r\n- Tinktúra: Sokan alkoholos kivonatot készítenek belőle, hogy koncentrált formában használják ki a hatóanyagokat (ám ez a termék magában nem tinktúra, hanem darabolt levél).\r\n- Egyéb keverékek: Belekeverhető házi füstölő mixekbe is, de ez esetben fontos az óvatosság és a kis dózis alkalmazása.\r\n\r\nMegjegyzés az adagoláshoz: Mivel az Epimedium erősebb hatóanyagokat is tartalmaz (pl. ikarin), érdemes kisebb mennyiséggel kezdeni, és figyelni a szervezet reakcióit.'),
(48, '2025-02-09 12:36:57', '2025-02-09 12:36:57', 24, 'a-mozgas-ereje/energizalo-novenyek/kanos-kecskefu-por', 5, 10, 'Kanos Kecskefű por', 'Az Epimedium nemzetségbe tartozó növények (gyakran Epimedium sagittatum vagy Epimedium grandiflorum fajként forgalmazzák) őshazája főként Kínában és Japánban található, de a világ számos pontján termesztik. A „Horny Goat Weed” elnevezés mögött egy régi legenda áll, amely szerint a kecskepásztorok észrevették, hogy állataik kifejezetten „élénkek” lesznek, miután legelésztek ebből a növényből.\r\n\r\nHagyományos felhasználás\r\n- Aphrodisiakum: A népi gyógyászat szerint elősegíti a szexuális vágyat és fokozza a libidót.\r\n- Energetizáló hatás: Enyhe stimulánsként tartják számon, segítheti a testi és szellemi frissességet.\r\n- Keringés támogatása: Előfordulhat, hogy javítja a véráramlást, ezáltal hozzájárul a jobb állóképességhez.\r\n\r\nFelhasználási javaslatok\r\n- Tea vagy főzet: A szárított, darabos leveleket forró vízzel leöntve, kb. 10-15 percig áztatva fogyaszthatod. Íze enyhén kesernyés-fűszeres, így más, aromás gyógynövényekkel (pl. citromfű, borsmenta) érdemes kombinálni.\r\n- Tinktúra: Sokan alkoholos kivonatot készítenek belőle, hogy koncentrált formában használják ki a hatóanyagokat (ám ez a termék magában nem tinktúra, hanem darabolt levél).\r\n- Egyéb keverékek: Belekeverhető házi füstölő mixekbe is, de ez esetben fontos az óvatosság és a kis dózis alkalmazása.\r\n\r\nMegjegyzés az adagoláshoz: Mivel az Epimedium erősebb hatóanyagokat is tartalmaz (pl. ikarin), érdemes kisebb mennyiséggel kezdeni, és figyelni a szervezet reakcióit.'),
(49, '2025-02-09 12:45:05', '2025-02-09 12:45:05', 25, 'a-mozgas-ereje/energizalo-novenyek/ginzeng-por', 5, 10, 'Ginzeng por', 'A ginzeng több ezer éve része az ázsiai gyógyászati hagyományoknak, különösen Kínában és Koreában népszerű. A növény gyökerét gőzöléssel, szárítással, majd finomra őrléssel dolgozzák fel, így alakul ki az a por, amely megőrzi a ginzeng legértékesebb hatóanyagait, például a ginsenosidokat.\r\n\r\nÉlettani hatások:\r\n- Energiaszint fokozás: Segíti a szervezet vitalitását és csökkentheti a fáradtságot.\r\n- Immunerősítés: Hozzájárulhat a test védekező képességének fenntartásához.\r\n- Antioxidáns tulajdonságok: Elősegítheti a sejtvédelemben fontos folyamatokat, segítve az öregedés és a betegségek elleni küzdelmet.\r\n\r\nFelhasználási javaslatok:\r\n- Tea vagy főzet: 1 teáskanál őrölt ginzenget forró (de nem lobogó) vízzel önts le, majd hagyd állni 5–10 percig.\r\n- Smoothiek és turmixok: Egyszerűen adj egy kiskanállal a kedvenc gyümölcs- vagy zöldségitalodhoz.\r\n- Ételekbe keverve: Levesekhez, rizses fogásokhoz vagy akár salátaöntetekhez is adható a változatos ízvilágért és extra hatóanyagokért.\r\n\r\nMinőségi garancia: A gondos termesztés és ellenőrzött szárítás biztosítja, hogy a por megőrizze a ginzeng jellegzetes aromáját és hatóanyagait.'),
(50, '2025-02-09 12:48:21', '2025-02-09 12:48:21', 25, 'az-energia-alapjai/vitalizalo-novenyek/ginzeng-por', 8, 15, 'Ginzeng por', 'A ginzeng több ezer éve része az ázsiai gyógyászati hagyományoknak, különösen Kínában és Koreában népszerű. A növény gyökerét gőzöléssel, szárítással, majd finomra őrléssel dolgozzák fel, így alakul ki az a por, amely megőrzi a ginzeng legértékesebb hatóanyagait, például a ginsenosidokat.\r\n\r\nÉlettani hatások:\r\n- Energiaszint fokozás: Segíti a szervezet vitalitását és csökkentheti a fáradtságot.\r\n- Immunerősítés: Hozzájárulhat a test védekező képességének fenntartásához.\r\n- Antioxidáns tulajdonságok: Elősegítheti a sejtvédelemben fontos folyamatokat, segítve az öregedés és a betegségek elleni küzdelmet.\r\n\r\nFelhasználási javaslatok:\r\n- Tea vagy főzet: 1 teáskanál őrölt ginzenget forró (de nem lobogó) vízzel önts le, majd hagyd állni 5–10 percig.\r\n- Smoothiek és turmixok: Egyszerűen adj egy kiskanállal a kedvenc gyümölcs- vagy zöldségitalodhoz.\r\n- Ételekbe keverve: Levesekhez, rizses fogásokhoz vagy akár salátaöntetekhez is adható a változatos ízvilágért és extra hatóanyagokért.\r\n\r\nMinőségi garancia: A gondos termesztés és ellenőrzött szárítás biztosítja, hogy a por megőrizze a ginzeng jellegzetes aromáját és hatóanyagait.'),
(51, '2025-02-09 12:54:42', '2025-02-09 12:54:42', 26, 'a-pillanat-langja/erzeki-novenykincsek/maca-por', 6, 31, 'Maca por', 'A maca egy káposztafélékhez tartozó gyökérnövény, amelyet már az ősi inka civilizációk is nagyra becsültek testi és szellemi teljesítményfokozó hatásai miatt. A zord hegyvidéki körülmények között nevelt maca gazdag vitaminokban, ásványi anyagokban és növényi tápanyagokban (fitonutriensek).\r\n\r\nFő tulajdonságok és előnyök:\r\n- Hormonális egyensúly: A népgyógyászat szerint hozzájárulhat mind a női, mind a férfi hormonháztartás harmonizálásához, és támogathatja a termékenységet.\r\n- Energetizáló hatás: Segíthet csökkenteni a fáradtságot, és hosszú távú energiát biztosíthat a szervezetnek.\r\n- Libidófokozás: Sokszor említik természetes afrodiziákumként, melyet mindkét nem szívesen alkalmaz a szexuális élet fellendítésére.\r\n\r\nFelhasználási javaslatok:\r\n- Smoothie/koktél: Egy teáskanál maca port belekeverve finom, diós-maltás ízű, energizáló italt kaphatsz.\r\n- Reggeli kása vagy müzli: Tökéletes kiegészítés lehet gabonapelyhekhez, joghurtokhoz, zabkásához.\r\n- Sütés-főzés: Sütik, palacsinták, energiaszeletek ízesítéséhez – különösen, ha tápanyagban gazdagabb édességet szeretnél készíteni.\r\n\r\nÉrdemes tudni: A maca íze enyhén édeskés, kissé karamellás-maltás jellegű, amely jól harmonizál a gyümölcsökkel, csokoládéval, de akár sós ételekben is kipróbálhatod.'),
(52, '2025-02-09 13:14:05', '2025-02-09 13:14:05', 26, 'a-mozgas-ereje/energizalo-novenyek/maca-por', 5, 10, 'Maca por', 'A maca egy káposztafélékhez tartozó gyökérnövény, amelyet már az ősi inka civilizációk is nagyra becsültek testi és szellemi teljesítményfokozó hatásai miatt. A zord hegyvidéki körülmények között nevelt maca gazdag vitaminokban, ásványi anyagokban és növényi tápanyagokban (fitonutriensek).\r\n\r\nFő tulajdonságok és előnyök:\r\n- Hormonális egyensúly: A népgyógyászat szerint hozzájárulhat mind a női, mind a férfi hormonháztartás harmonizálásához, és támogathatja a termékenységet.\r\n- Energetizáló hatás: Segíthet csökkenteni a fáradtságot, és hosszú távú energiát biztosíthat a szervezetnek.\r\n- Libidófokozás: Sokszor említik természetes afrodiziákumként, melyet mindkét nem szívesen alkalmaz a szexuális élet fellendítésére.\r\n\r\nFelhasználási javaslatok:\r\n- Smoothie/koktél: Egy teáskanál maca port belekeverve finom, diós-maltás ízű, energizáló italt kaphatsz.\r\n- Reggeli kása vagy müzli: Tökéletes kiegészítés lehet gabonapelyhekhez, joghurtokhoz, zabkásához.\r\n- Sütés-főzés: Sütik, palacsinták, energiaszeletek ízesítéséhez – különösen, ha tápanyagban gazdagabb édességet szeretnél készíteni.\r\n\r\nÉrdemes tudni: A maca íze enyhén édeskés, kissé karamellás-maltás jellegű, amely jól harmonizál a gyümölcsökkel, csokoládéval, de akár sós ételekben is kipróbálhatod.');
INSERT INTO `product_page` (`id`, `created_at`, `last_modified`, `product_id`, `link_slug`, `category_id`, `subcategory_id`, `page_title`, `page_content`) VALUES
(53, '2025-02-09 13:14:33', '2025-02-09 13:14:33', 26, 'az-energia-alapjai/vitalizalo-novenyek/maca-por', 8, 15, 'Maca por', 'A maca egy káposztafélékhez tartozó gyökérnövény, amelyet már az ősi inka civilizációk is nagyra becsültek testi és szellemi teljesítményfokozó hatásai miatt. A zord hegyvidéki körülmények között nevelt maca gazdag vitaminokban, ásványi anyagokban és növényi tápanyagokban (fitonutriensek).\r\n\r\nFő tulajdonságok és előnyök:\r\n- Hormonális egyensúly: A népgyógyászat szerint hozzájárulhat mind a női, mind a férfi hormonháztartás harmonizálásához, és támogathatja a termékenységet.\r\n- Energetizáló hatás: Segíthet csökkenteni a fáradtságot, és hosszú távú energiát biztosíthat a szervezetnek.\r\n- Libidófokozás: Sokszor említik természetes afrodiziákumként, melyet mindkét nem szívesen alkalmaz a szexuális élet fellendítésére.\r\n\r\nFelhasználási javaslatok:\r\n- Smoothie/koktél: Egy teáskanál maca port belekeverve finom, diós-maltás ízű, energizáló italt kaphatsz.\r\n- Reggeli kása vagy müzli: Tökéletes kiegészítés lehet gabonapelyhekhez, joghurtokhoz, zabkásához.\r\n- Sütés-főzés: Sütik, palacsinták, energiaszeletek ízesítéséhez – különösen, ha tápanyagban gazdagabb édességet szeretnél készíteni.\r\n\r\nÉrdemes tudni: A maca íze enyhén édeskés, kissé karamellás-maltás jellegű, amely jól harmonizál a gyümölcsökkel, csokoládéval, de akár sós ételekben is kipróbálhatod.'),
(54, '2025-02-09 13:29:04', '2025-02-09 13:29:04', 27, 'a-pillanat-langja/erzeki-novenykincsek/barsonybab-por', 6, 31, 'Bársonybab por', 'A Mucuna pruriens évszázadok óta a hagyományos indiai (ájurvédikus) gyógyászat egyik kedvelt alapanyaga. A növény érett magjait alapos tisztítás után szárítják és finomra őrlik, így alakul ki a könnyen felhasználható por.\r\n\r\nFő összetevője: L-DOPA (levodopa), mely a dopamin termelésben játszik kulcsszerepet. A dopamin az idegrendszer egyik kiemelten fontos ingerületátvivő anyaga, befolyásolja a motivációt, a mozgáskoordinációt és a hangulatot.\r\n\r\nLehetséges előnyös tulajdonságok:\r\n- Hangulat és motiváció: A dopaminszint emelésével hozzájárulhat a pozitív hangulathoz és a szellemi frissességhez.\r\n- Hormonszint és libidó: Egyes források szerint támogathatja a reproduktív egészséget és a tesztoszteronszintet is, ezáltal segíthet a libidó fokozásában.\r\n- Stresszkezelés és energiaszint: Adaptogén jellegűnek tartják, vagyis segíthet a szervezetnek a stresszhez való alkalmazkodásban, és javíthatja az általános vitalitást.\r\n\r\nFelhasználási javaslatok:\r\n- Turmixokhoz, gyümölcslevekhez: 1 teáskanál Mucuna port egyszerűen elkeverhetsz naponta a kedvenc smoothie-dban.\r\n- Teához vagy meleg italokhoz: Hozzáadhatod teához, „aranytej” (pl. kurkumás ital) vagy kakaó alapú készítményekhez.\r\n- Ételekbe keverve: Akár levesekbe, zabkásába vagy joghurtba is szórhatod, ha nem zavar enyhén földes, diós aromája.'),
(55, '2025-02-09 13:37:39', '2025-02-09 13:37:39', 27, 'a-mozgas-ereje/energizalo-novenyek/barsonybab-por', 5, 10, 'Bársonybab por', 'A Mucuna pruriens évszázadok óta a hagyományos indiai (ájurvédikus) gyógyászat egyik kedvelt alapanyaga. A növény érett magjait alapos tisztítás után szárítják és finomra őrlik, így alakul ki a könnyen felhasználható por.\r\n\r\nFő összetevője: L-DOPA (levodopa), mely a dopamin termelésben játszik kulcsszerepet. A dopamin az idegrendszer egyik kiemelten fontos ingerületátvivő anyaga, befolyásolja a motivációt, a mozgáskoordinációt és a hangulatot.\r\n\r\nLehetséges előnyös tulajdonságok:\r\n- Hangulat és motiváció: A dopaminszint emelésével hozzájárulhat a pozitív hangulathoz és a szellemi frissességhez.\r\n- Hormonszint és libidó: Egyes források szerint támogathatja a reproduktív egészséget és a tesztoszteronszintet is, ezáltal segíthet a libidó fokozásában.\r\n- Stresszkezelés és energiaszint: Adaptogén jellegűnek tartják, vagyis segíthet a szervezetnek a stresszhez való alkalmazkodásban, és javíthatja az általános vitalitást.\r\n\r\nFelhasználási javaslatok:\r\n- Turmixokhoz, gyümölcslevekhez: 1 teáskanál Mucuna port egyszerűen elkeverhetsz naponta a kedvenc smoothie-dban.\r\n- Teához vagy meleg italokhoz: Hozzáadhatod teához, „aranytej” (pl. kurkumás ital) vagy kakaó alapú készítményekhez.\r\n- Ételekbe keverve: Akár levesekbe, zabkásába vagy joghurtba is szórhatod, ha nem zavar enyhén földes, diós aromája.'),
(56, '2025-02-09 13:37:50', '2025-02-09 13:37:50', 27, 'az-energia-alapjai/vitalizalo-novenyek/barsonybab-por', 8, 15, 'Bársonybab por', 'A Mucuna pruriens évszázadok óta a hagyományos indiai (ájurvédikus) gyógyászat egyik kedvelt alapanyaga. A növény érett magjait alapos tisztítás után szárítják és finomra őrlik, így alakul ki a könnyen felhasználható por.\r\n\r\nFő összetevője: L-DOPA (levodopa), mely a dopamin termelésben játszik kulcsszerepet. A dopamin az idegrendszer egyik kiemelten fontos ingerületátvivő anyaga, befolyásolja a motivációt, a mozgáskoordinációt és a hangulatot.\r\n\r\nLehetséges előnyös tulajdonságok:\r\n- Hangulat és motiváció: A dopaminszint emelésével hozzájárulhat a pozitív hangulathoz és a szellemi frissességhez.\r\n- Hormonszint és libidó: Egyes források szerint támogathatja a reproduktív egészséget és a tesztoszteronszintet is, ezáltal segíthet a libidó fokozásában.\r\n- Stresszkezelés és energiaszint: Adaptogén jellegűnek tartják, vagyis segíthet a szervezetnek a stresszhez való alkalmazkodásban, és javíthatja az általános vitalitást.\r\n\r\nFelhasználási javaslatok:\r\n- Turmixokhoz, gyümölcslevekhez: 1 teáskanál Mucuna port egyszerűen elkeverhetsz naponta a kedvenc smoothie-dban.\r\n- Teához vagy meleg italokhoz: Hozzáadhatod teához, „aranytej” (pl. kurkumás ital) vagy kakaó alapú készítményekhez.\r\n- Ételekbe keverve: Akár levesekbe, zabkásába vagy joghurtba is szórhatod, ha nem zavar enyhén földes, diós aromája.'),
(57, '2025-02-09 13:49:36', '2025-02-09 13:49:36', 28, 'a-pillanat-langja/erzeki-novenykincsek/muira-puama-orolt', 6, 31, 'Muira Puama őrölt', 'A Muira Puama – más néven „potenciafa” – elsősorban Brazíliában és a szomszédos dél-amerikai területeken honos. A helyi népi gyógyászatban a növény kérgét és gyökerét már a 19. század óta használják a libidó fokozására, a fáradtság csökkentésére és az idegrendszer erősítésére.\r\n\r\nHagyomány és hatás\r\n- Aphrodisiakum: Számos népgyógyászati forrás szerint hozzájárulhat a szexuális vágy és teljesítmény növeléséhez.\r\n- Energia és állóképesség: Általános erőnlétfokozó tulajdonságokkal ruházzák fel, segíthet a fizikai és szellemi kimerültség enyhítésében.\r\n- Emésztés és idegrendszer: Előfordul, hogy az idegrendszer kiegyensúlyozására, illetve gyomor- és emésztésjavítóként is alkalmazzák.\r\n\r\nFelhasználás\r\n- Tea vagy főzet: A durva őrleményt érdemes legalább 10–15 percig főzni, hogy az aromás- és hatóanyagok maradéktalanul kioldódjanak. A kesernyés, fás íz enyhíthető mézzel, citromfűvel vagy más gyógynövényekkel.\r\n- Alkoholos kivonat (tinktúra): Különösen népszerű forma, mert koncentráltan tartalmazhatja a hatóanyagokat. (Jelen termék önmagában nem tinktúra, de megfelelő alapanyag hozzá.)\r\n- Gyógyteakeverékek részeként: Kiválóan kombinálható más afrodiziákus vagy energizáló növényekkel (pl. Damiana, Ginzeng, Maca).'),
(58, '2025-02-09 13:53:05', '2025-02-09 13:53:05', 28, 'a-mozgas-ereje/energizalo-novenyek/muira-puama-orolt', 5, 10, 'Muira Puama őrölt', 'A Muira Puama – más néven „potenciafa” – elsősorban Brazíliában és a szomszédos dél-amerikai területeken honos. A helyi népi gyógyászatban a növény kérgét és gyökerét már a 19. század óta használják a libidó fokozására, a fáradtság csökkentésére és az idegrendszer erősítésére.\r\n\r\nHagyomány és hatás\r\n- Aphrodisiakum: Számos népgyógyászati forrás szerint hozzájárulhat a szexuális vágy és teljesítmény növeléséhez.\r\n- Energia és állóképesség: Általános erőnlétfokozó tulajdonságokkal ruházzák fel, segíthet a fizikai és szellemi kimerültség enyhítésében.\r\n- Emésztés és idegrendszer: Előfordul, hogy az idegrendszer kiegyensúlyozására, illetve gyomor- és emésztésjavítóként is alkalmazzák.\r\n\r\nFelhasználás\r\n- Tea vagy főzet: A durva őrleményt érdemes legalább 10–15 percig főzni, hogy az aromás- és hatóanyagok maradéktalanul kioldódjanak. A kesernyés, fás íz enyhíthető mézzel, citromfűvel vagy más gyógynövényekkel.\r\n- Alkoholos kivonat (tinktúra): Különösen népszerű forma, mert koncentráltan tartalmazhatja a hatóanyagokat. (Jelen termék önmagában nem tinktúra, de megfelelő alapanyag hozzá.)\r\n- Gyógyteakeverékek részeként: Kiválóan kombinálható más afrodiziákus vagy energizáló növényekkel (pl. Damiana, Ginzeng, Maca).'),
(59, '2025-02-09 13:55:44', '2025-02-09 13:55:44', 29, 'a-pillanat-langja/erzeki-novenykincsek/muira-puama-por', 6, 31, 'Muira Puama por', 'A Muira Puama – más néven „potenciafa” – elsősorban Brazíliában és a szomszédos dél-amerikai területeken honos. A helyi népi gyógyászatban a növény kérgét és gyökerét már a 19. század óta használják a libidó fokozására, a fáradtság csökkentésére és az idegrendszer erősítésére.\r\n\r\nHagyomány és hatás\r\n- Aphrodisiakum: Számos népgyógyászati forrás szerint hozzájárulhat a szexuális vágy és teljesítmény növeléséhez.\r\n- Energia és állóképesség: Általános erőnlétfokozó tulajdonságokkal ruházzák fel, segíthet a fizikai és szellemi kimerültség enyhítésében.\r\n- Emésztés és idegrendszer: Előfordul, hogy az idegrendszer kiegyensúlyozására, illetve gyomor- és emésztésjavítóként is alkalmazzák.\r\n\r\nFelhasználás\r\n- Tea vagy főzet: A durva őrleményt érdemes legalább 10–15 percig főzni, hogy az aromás- és hatóanyagok maradéktalanul kioldódjanak. A kesernyés, fás íz enyhíthető mézzel, citromfűvel vagy más gyógynövényekkel.\r\n- Alkoholos kivonat (tinktúra): Különösen népszerű forma, mert koncentráltan tartalmazhatja a hatóanyagokat. (Jelen termék önmagában nem tinktúra, de megfelelő alapanyag hozzá.)\r\n- Gyógyteakeverékek részeként: Kiválóan kombinálható más afrodiziákus vagy energizáló növényekkel (pl. Damiana, Ginzeng, Maca).'),
(60, '2025-02-09 13:59:03', '2025-02-09 13:59:03', 29, 'a-mozgas-ereje/energizalo-novenyek/muira-puama-por', 5, 10, 'Muira Puama por', 'A Muira Puama – más néven „potenciafa” – elsősorban Brazíliában és a szomszédos dél-amerikai területeken honos. A helyi népi gyógyászatban a növény kérgét és gyökerét már a 19. század óta használják a libidó fokozására, a fáradtság csökkentésére és az idegrendszer erősítésére.\r\n\r\nHagyomány és hatás\r\n- Aphrodisiakum: Számos népgyógyászati forrás szerint hozzájárulhat a szexuális vágy és teljesítmény növeléséhez.\r\n- Energia és állóképesség: Általános erőnlétfokozó tulajdonságokkal ruházzák fel, segíthet a fizikai és szellemi kimerültség enyhítésében.\r\n- Emésztés és idegrendszer: Előfordul, hogy az idegrendszer kiegyensúlyozására, illetve gyomor- és emésztésjavítóként is alkalmazzák.\r\n\r\nFelhasználás\r\n- Tea vagy főzet: A durva őrleményt érdemes legalább 10–15 percig főzni, hogy az aromás- és hatóanyagok maradéktalanul kioldódjanak. A kesernyés, fás íz enyhíthető mézzel, citromfűvel vagy más gyógynövényekkel.\r\n- Alkoholos kivonat (tinktúra): Különösen népszerű forma, mert koncentráltan tartalmazhatja a hatóanyagokat. (Jelen termék önmagában nem tinktúra, de megfelelő alapanyag hozzá.)\r\n- Gyógyteakeverékek részeként: Kiválóan kombinálható más afrodiziákus vagy energizáló növényekkel (pl. Damiana, Ginzeng, Maca).'),
(61, '2025-02-09 14:10:07', '2025-02-09 14:10:07', 30, 'a-pillanat-langja/erzeki-novenykincsek/kiralydinnye', 6, 31, 'Királydinnye', 'A Tribulus terrestris a forró, száraz éghajlatot kedvelő, kúszó szárú növény, melynek apró, tüskés terméseit évezredek óta használják a mediterrán, indiai és kínai gyógyászatban. A bennük található hatóanyagok, például a szteroid-szaponinok, elősegíthetik a hormonszint kiegyensúlyozását, különösen a tesztoszterontermeléshez kapcsolódó folyamatokat.\r\n\r\nHagyomány és hatás\r\n- Hormonális támogatás: A népgyógyászat elsősorban férfiak esetében használja a libidó fokozására, de nők számára is hasznos lehet a hormonális rendszer harmonizálására.\r\n- Teljesítményfokozás és állóképesség: Sportolók és aktív életmódot élők kedvelik a Tribulust annak energizáló, erősítő hatása miatt.\r\n- Keringéstámogatás: A szaponinoknak köszönhetően javíthatja a vérkeringést, ezáltal támogatva az általános fittségét.\r\n\r\nFelhasználási javaslatok\r\n- Főzet vagy tea: Mivel a termés meglehetősen kemény és szúrós, a használat előtt célszerű összetörni (mozsárban vagy konyhai aprítóban). Ezután lassú forralással, 10–15 perces főzet készíthető belőle, amelynek kesernyés ízét mézzel vagy édesebb gyógynövényekkel (pl. édesgyökér) lehet ellensúlyozni.\r\n- Tinktúra: Sok gyártó alkohollal vonja ki a hatóanyagokat, koncentrált formában (bár a termék maga nem tinktúra, alapanyagnak kiváló).\r\n- Porrá őrölve: Ha szeretnéd por formába továbbvinni, házilag is összedarálhatod, de előtte figyelj a hegyes, szúrós részekre!\r\n\r\nKisebb adaggal érdemes kezdeni, mivel erőteljes hatású lehet.'),
(62, '2025-02-09 14:16:18', '2025-02-09 14:16:18', 30, 'a-mozgas-ereje/energizalo-novenyek/kiralydinnye', 5, 10, 'Királydinnye', 'A Tribulus terrestris a forró, száraz éghajlatot kedvelő, kúszó szárú növény, melynek apró, tüskés terméseit évezredek óta használják a mediterrán, indiai és kínai gyógyászatban. A bennük található hatóanyagok, például a szteroid-szaponinok, elősegíthetik a hormonszint kiegyensúlyozását, különösen a tesztoszterontermeléshez kapcsolódó folyamatokat.\r\n\r\nHagyomány és hatás\r\n- Hormonális támogatás: A népgyógyászat elsősorban férfiak esetében használja a libidó fokozására, de nők számára is hasznos lehet a hormonális rendszer harmonizálására.\r\n- Teljesítményfokozás és állóképesség: Sportolók és aktív életmódot élők kedvelik a Tribulust annak energizáló, erősítő hatása miatt.\r\n- Keringéstámogatás: A szaponinoknak köszönhetően javíthatja a vérkeringést, ezáltal támogatva az általános fittségét.\r\n\r\nFelhasználási javaslatok\r\n- Főzet vagy tea: Mivel a termés meglehetősen kemény és szúrós, a használat előtt célszerű összetörni (mozsárban vagy konyhai aprítóban). Ezután lassú forralással, 10–15 perces főzet készíthető belőle, amelynek kesernyés ízét mézzel vagy édesebb gyógynövényekkel (pl. édesgyökér) lehet ellensúlyozni.\r\n- Tinktúra: Sok gyártó alkohollal vonja ki a hatóanyagokat, koncentrált formában (bár a termék maga nem tinktúra, alapanyagnak kiváló).\r\n- Porrá őrölve: Ha szeretnéd por formába továbbvinni, házilag is összedarálhatod, de előtte figyelj a hegyes, szúrós részekre!\r\n\r\nKisebb adaggal érdemes kezdeni, mivel erőteljes hatású lehet.'),
(63, '2025-02-09 14:16:36', '2025-02-09 14:16:36', 30, 'az-energia-alapjai/vitalizalo-novenyek/kiralydinnye', 8, 15, 'Királydinnye', 'A Tribulus terrestris a forró, száraz éghajlatot kedvelő, kúszó szárú növény, melynek apró, tüskés terméseit évezredek óta használják a mediterrán, indiai és kínai gyógyászatban. A bennük található hatóanyagok, például a szteroid-szaponinok, elősegíthetik a hormonszint kiegyensúlyozását, különösen a tesztoszterontermeléshez kapcsolódó folyamatokat.\r\n\r\nHagyomány és hatás\r\n- Hormonális támogatás: A népgyógyászat elsősorban férfiak esetében használja a libidó fokozására, de nők számára is hasznos lehet a hormonális rendszer harmonizálására.\r\n- Teljesítményfokozás és állóképesség: Sportolók és aktív életmódot élők kedvelik a Tribulust annak energizáló, erősítő hatása miatt.\r\n- Keringéstámogatás: A szaponinoknak köszönhetően javíthatja a vérkeringést, ezáltal támogatva az általános fittségét.\r\n\r\nFelhasználási javaslatok\r\n- Főzet vagy tea: Mivel a termés meglehetősen kemény és szúrós, a használat előtt célszerű összetörni (mozsárban vagy konyhai aprítóban). Ezután lassú forralással, 10–15 perces főzet készíthető belőle, amelynek kesernyés ízét mézzel vagy édesebb gyógynövényekkel (pl. édesgyökér) lehet ellensúlyozni.\r\n- Tinktúra: Sok gyártó alkohollal vonja ki a hatóanyagokat, koncentrált formában (bár a termék maga nem tinktúra, alapanyagnak kiváló).\r\n- Porrá őrölve: Ha szeretnéd por formába továbbvinni, házilag is összedarálhatod, de előtte figyelj a hegyes, szúrós részekre!\r\n\r\nKisebb adaggal érdemes kezdeni, mivel erőteljes hatású lehet.'),
(64, '2025-02-09 14:25:48', '2025-02-09 14:25:48', 31, 'a-pillanat-langja/erzeki-novenykincsek/kiralydinnye-por', 6, 31, 'Királydinnye por', 'A királydinnye (Tribulus terrestris) a mediterrán és szárazabb éghajlatú vidékek kedvelt növénye, melynek szúrós terméséből és leveléből ősidők óta készítenek főzeteket, tinktúrákat. A por formátum praktikus megoldás, hiszen egyszerűen adagolható, akár italokhoz, ételekhez is hozzákeverhető.\r\n\r\nFőbb tulajdonságok és előnyök:\r\n- Hormonális támogatás: A népi gyógyászat szerint segíthet a tesztoszteronszint optimalizálásában, ami mindkét nemnél hozzájárulhat a szexuális egészséghez.\r\n- Energia és teljesítmény: Sportolók és aktív életmódot élők is szívesen alkalmazzák a Tribulus-t a jobb erőnlét és állóképesség elérése érdekében.\r\n- Könnyű felhasználás: A por keverhető teába, turmixba, de akár bele is sütheted kenyérbe vagy süteménybe, ha szeretnéd kreatívan beilleszteni az étrendedbe.\r\n\r\nFelhasználási tippek:\r\n- Napi adagolás: Általában 1 teáskanál (kb. 2-3 g) ajánlott naponta, ám érdemes kisebb mennyiséggel kezdeni, és fokozatosan emelni a dózist.\r\n- Tea vagy főzet: Meleg (nem forrásban lévő) vízhez add hozzá, és hagyd állni 5–10 percig. Íze kissé kesernyés, így mézzel, steviával vagy más édesítőszerrel lágyíthatod.\r\n- Turmix, smoothie: Egyszerűen keverd bele a kedvenc italodba, hogy elfedd enyhén kesernyés ízét, és élvezd jótékony hatásait.'),
(65, '2025-02-09 14:39:12', '2025-02-09 14:39:12', 31, 'a-mozgas-ereje/energizalo-novenyek/kiralydinnye-por', 5, 10, 'Királydinnye por', 'A királydinnye (Tribulus terrestris) a mediterrán és szárazabb éghajlatú vidékek kedvelt növénye, melynek szúrós terméséből és leveléből ősidők óta készítenek főzeteket, tinktúrákat. A por formátum praktikus megoldás, hiszen egyszerűen adagolható, akár italokhoz, ételekhez is hozzákeverhető.\r\n\r\nFőbb tulajdonságok és előnyök:\r\n- Hormonális támogatás: A népi gyógyászat szerint segíthet a tesztoszteronszint optimalizálásában, ami mindkét nemnél hozzájárulhat a szexuális egészséghez.\r\n- Energia és teljesítmény: Sportolók és aktív életmódot élők is szívesen alkalmazzák a Tribulus-t a jobb erőnlét és állóképesség elérése érdekében.\r\n- Könnyű felhasználás: A por keverhető teába, turmixba, de akár bele is sütheted kenyérbe vagy süteménybe, ha szeretnéd kreatívan beilleszteni az étrendedbe.\r\n\r\nFelhasználási tippek:\r\n- Napi adagolás: Általában 1 teáskanál (kb. 2-3 g) ajánlott naponta, ám érdemes kisebb mennyiséggel kezdeni, és fokozatosan emelni a dózist.\r\n- Tea vagy főzet: Meleg (nem forrásban lévő) vízhez add hozzá, és hagyd állni 5–10 percig. Íze kissé kesernyés, így mézzel, steviával vagy más édesítőszerrel lágyíthatod.\r\n- Turmix, smoothie: Egyszerűen keverd bele a kedvenc italodba, hogy elfedd enyhén kesernyés ízét, és élvezd jótékony hatásait.'),
(66, '2025-02-09 14:39:23', '2025-02-09 14:39:23', 31, 'az-energia-alapjai/vitalizalo-novenyek/kiralydinnye-por', 8, 15, 'Királydinnye por', 'A királydinnye (Tribulus terrestris) a mediterrán és szárazabb éghajlatú vidékek kedvelt növénye, melynek szúrós terméséből és leveléből ősidők óta készítenek főzeteket, tinktúrákat. A por formátum praktikus megoldás, hiszen egyszerűen adagolható, akár italokhoz, ételekhez is hozzákeverhető.\r\n\r\nFőbb tulajdonságok és előnyök:\r\n- Hormonális támogatás: A népi gyógyászat szerint segíthet a tesztoszteronszint optimalizálásában, ami mindkét nemnél hozzájárulhat a szexuális egészséghez.\r\n- Energia és teljesítmény: Sportolók és aktív életmódot élők is szívesen alkalmazzák a Tribulus-t a jobb erőnlét és állóképesség elérése érdekében.\r\n- Könnyű felhasználás: A por keverhető teába, turmixba, de akár bele is sütheted kenyérbe vagy süteménybe, ha szeretnéd kreatívan beilleszteni az étrendedbe.\r\n\r\nFelhasználási tippek:\r\n- Napi adagolás: Általában 1 teáskanál (kb. 2-3 g) ajánlott naponta, ám érdemes kisebb mennyiséggel kezdeni, és fokozatosan emelni a dózist.\r\n- Tea vagy főzet: Meleg (nem forrásban lévő) vízhez add hozzá, és hagyd állni 5–10 percig. Íze kissé kesernyés, így mézzel, steviával vagy más édesítőszerrel lágyíthatod.\r\n- Turmix, smoothie: Egyszerűen keverd bele a kedvenc italodba, hogy elfedd enyhén kesernyés ízét, és élvezd jótékony hatásait.'),
(67, '2025-02-09 14:51:42', '2025-02-09 14:51:42', 32, 'a-pillanat-langja/erzeki-novenykincsek/baratcserje-por', 6, 31, 'Barátcserje por', 'A barátcserje, vagy Vitex agnus-castus, a Földközi-tenger térségéből származik, de ma már világszerte ismert és elterjedt gyógynövény. A terméséből vagy magjaiból készült por hosszú múltra tekint vissza a tradicionális gyógyászatban, különösen a nőgyógyászati problémák enyhítésében.\r\n\r\nFő előnyei és hagyományos felhasználása:\r\n- Hormonális egyensúly: A Vitex képes befolyásolni a prolaktin- és más nemi hormonok szintjét, így hozzájárulhat a menstruációs ciklus harmonizálásához, enyhítheti a menstruáció előtti diszkomfortérzetet.\r\n- Termékenység támogatása: Néhány forrás szerint a rendszeres Vitex-fogyasztás segíthet a termékenység elősegítésében, bár ezt érdemes előzetesen szakemberrel is egyeztetni.\r\n- Hangulatkiegyensúlyozás: Sokan tapasztalnak mérséklődést a PMS okozta hangulatingadozásokban és feszültségben.\r\n\r\nFelhasználási tippek:\r\n- Teában vagy főzetben: Adj 1 teáskanál Vitex port meleg vízhez, és hagyd állni 5–10 percig. Íze kissé földes, fűszeres jellegű; mézzel vagy édesebb növényekkel érdemes lágyítani.\r\n- Smoothiek és turmixok: Könnyen elrejthető a kedvenc gyümölcsös italodban, így kényelmesen beilleszthető a napi rutinodba.\r\n- Kapszulázva: Ha nem kedveled a növény ízét, a por adagolását megkönnyítheti, ha saját kapszulákat töltesz meg vele.\r\n\r\nÉrdemes legalább néhány héten keresztül rendszeresen fogyasztani a tartós hatás érdekében. A nagyon erős hormonális hatás miatt várandós vagy szoptató nőknek, illetve komolyabb hormonális problémákkal küzdőknek javasolt előzetesen szakorvossal konzultálni.'),
(69, '2025-02-09 14:56:21', '2025-02-09 14:56:21', 32, 'az-energia-alapjai/vitalizalo-novenyek/baratcserje-por', 8, 15, 'Barátcserje por', 'A barátcserje, vagy Vitex agnus-castus, a Földközi-tenger térségéből származik, de ma már világszerte ismert és elterjedt gyógynövény. A terméséből vagy magjaiból készült por hosszú múltra tekint vissza a tradicionális gyógyászatban, különösen a nőgyógyászati problémák enyhítésében.\r\n\r\nFő előnyei és hagyományos felhasználása:\r\n- Hormonális egyensúly: A Vitex képes befolyásolni a prolaktin- és más nemi hormonok szintjét, így hozzájárulhat a menstruációs ciklus harmonizálásához, enyhítheti a menstruáció előtti diszkomfortérzetet.\r\n- Termékenység támogatása: Néhány forrás szerint a rendszeres Vitex-fogyasztás segíthet a termékenység elősegítésében, bár ezt érdemes előzetesen szakemberrel is egyeztetni.\r\n- Hangulatkiegyensúlyozás: Sokan tapasztalnak mérséklődést a PMS okozta hangulatingadozásokban és feszültségben.\r\n\r\nFelhasználási tippek:\r\n- Teában vagy főzetben: Adj 1 teáskanál Vitex port meleg vízhez, és hagyd állni 5–10 percig. Íze kissé földes, fűszeres jellegű; mézzel vagy édesebb növényekkel érdemes lágyítani.\r\n- Smoothiek és turmixok: Könnyen elrejthető a kedvenc gyümölcsös italodban, így kényelmesen beilleszthető a napi rutinodba.\r\n- Kapszulázva: Ha nem kedveled a növény ízét, a por adagolását megkönnyítheti, ha saját kapszulákat töltesz meg vele.\r\n\r\nÉrdemes legalább néhány héten keresztül rendszeresen fogyasztani a tartós hatás érdekében. A nagyon erős hormonális hatás miatt várandós vagy szoptató nőknek, illetve komolyabb hormonális problémákkal küzdőknek javasolt előzetesen szakorvossal konzultálni.'),
(70, '2025-02-09 15:12:16', '2025-02-09 15:12:16', 34, 'a-pillanat-langja/erzeki-novenykincsek/vitex-szaritott-bogyok', 6, 31, 'Vitex szárított bogyók', 'A barátcserje bogyói évszázadok óta fontos szerepet töltenek be a mediterrán, közel-keleti és ázsiai gyógyászati hagyományokban. Az organikus körülmények között nevelt Vitex bogyók gondos szárítási folyamaton mennek át, hogy megőrizzék maximális hatóanyag-tartalmukat. A termés kellemesen fűszeres, kissé borsos ízzel rendelkezik, ezért régebben „szerzetesbors” néven is emlegették.\r\n\r\nFő tulajdonságok és előnyök\r\n- Hormonális egyensúly: A Vitex befolyásolhatja a prolaktin- és más nemi hormonok (pl. progeszteron, ösztrogén) szintjét, ezáltal segíthet a menstruációs ciklus harmonizálásában és a PMS tüneteinek enyhítésében.\r\n- Hangulatingadozások enyhítése: Sokan tapasztalnak kedvező hatást a ciklushoz kapcsolódó feszültség, ingerlékenység csökkentésében.\r\n- Termékenység támogatása: Egyes tradicionális orvoslási rendszerek termékenységfokozóként is említik a Vitexet, bár erről minden esetben javasolt szakértői véleményt is kikérni.\r\n\r\nFelhasználási javaslatok\r\n- Tea/főzet: A szárított bogyókat mozsárban kissé megtörve (vagy egészben, hosszabb főzési idővel) forrázd le meleg vízzel, és hagyd állni 10–15 percig. Az enyhén fűszeres ízt édes gyógynövényekkel (pl. édesgyökér) vagy mézzel teheted lágyabbá.\r\n- Tinktúra/alkoholos kivonat: Sok esetben a leghatékonyabb formának tartják, mert a hatóanyagok így koncentráltan kerülnek kioldásra. A bogyók tökéletes alapanyagot nyújtanak ehhez.\r\n- Kapszulázva vagy őrölve: Ha nem kedveled a fűszeres, borsos ízt, saját kezűleg is porrá törheted, majd kapszulákba töltheted.\r\n\r\nA kiegyensúlyozott hormonháztartás eléréséhez gyakran több hét vagy akár néhány hónap rendszeres fogyasztás szükséges. Várandós vagy szoptató nők, illetve hormonális terápián lévők kérjék ki herbal terapeuta tanácsát.'),
(71, '2025-02-09 15:18:50', '2025-02-09 15:18:50', 34, 'az-energia-alapjai/vitalizalo-novenyek/vitex-szaritott-bogyok', 8, 15, 'Vitex szárított bogyók', 'A barátcserje bogyói évszázadok óta fontos szerepet töltenek be a mediterrán, közel-keleti és ázsiai gyógyászati hagyományokban. Az organikus körülmények között nevelt Vitex bogyók gondos szárítási folyamaton mennek át, hogy megőrizzék maximális hatóanyag-tartalmukat. A termés kellemesen fűszeres, kissé borsos ízzel rendelkezik, ezért régebben „szerzetesbors” néven is emlegették.\r\n\r\nFő tulajdonságok és előnyök\r\n- Hormonális egyensúly: A Vitex befolyásolhatja a prolaktin- és más nemi hormonok (pl. progeszteron, ösztrogén) szintjét, ezáltal segíthet a menstruációs ciklus harmonizálásában és a PMS tüneteinek enyhítésében.\r\n- Hangulatingadozások enyhítése: Sokan tapasztalnak kedvező hatást a ciklushoz kapcsolódó feszültség, ingerlékenység csökkentésében.\r\n- Termékenység támogatása: Egyes tradicionális orvoslási rendszerek termékenységfokozóként is említik a Vitexet, bár erről minden esetben javasolt szakértői véleményt is kikérni.\r\n\r\nFelhasználási javaslatok\r\n- Tea/főzet: A szárított bogyókat mozsárban kissé megtörve (vagy egészben, hosszabb főzési idővel) forrázd le meleg vízzel, és hagyd állni 10–15 percig. Az enyhén fűszeres ízt édes gyógynövényekkel (pl. édesgyökér) vagy mézzel teheted lágyabbá.\r\n- Tinktúra/alkoholos kivonat: Sok esetben a leghatékonyabb formának tartják, mert a hatóanyagok így koncentráltan kerülnek kioldásra. A bogyók tökéletes alapanyagot nyújtanak ehhez.\r\n- Kapszulázva vagy őrölve: Ha nem kedveled a fűszeres, borsos ízt, saját kezűleg is porrá törheted, majd kapszulákba töltheted.\r\n\r\nA kiegyensúlyozott hormonháztartás eléréséhez gyakran több hét vagy akár néhány hónap rendszeres fogyasztás szükséges. Várandós vagy szoptató nők, illetve hormonális terápián lévők kérjék ki herbal terapeuta tanácsát.');

--
-- Eseményindítók `product_page`
--
DROP TRIGGER IF EXISTS `product_page_after_insert`;
DELIMITER $$
CREATE TRIGGER `product_page_after_insert` AFTER INSERT ON `product_page` FOR EACH ROW BEGIN
  -- Ha van subcategory_id
  IF NEW.subcategory_id IS NOT NULL THEN
    -- Növeljük az adott subcategory számlálóját
    UPDATE subcategory
      SET product_count = product_count + 1
      WHERE id = NEW.subcategory_id;

    -- Növeljük a főkategóriáét is, de ehhez le kell kérni, melyik category-hoz tartozik ez a subcategory
    UPDATE category
      SET product_count = product_count + 1
      WHERE id = (
        SELECT category_id
        FROM subcategory
        WHERE id = NEW.subcategory_id
      );
  END IF;
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `product_page_after_update`;
DELIMITER $$
CREATE TRIGGER `product_page_after_update` AFTER UPDATE ON `product_page` FOR EACH ROW BEGIN
  -- Ha a régiben volt subcategory, de az újban más
  IF OLD.subcategory_id <> NEW.subcategory_id THEN

    -- Ha volt 'régi' subcategory, akkor onnan levonunk 1-et
    IF OLD.subcategory_id IS NOT NULL THEN
      UPDATE subcategory
        SET product_count = product_count - 1
        WHERE id = OLD.subcategory_id;

      UPDATE category
        SET product_count = product_count - 1
        WHERE id = (
          SELECT category_id
          FROM subcategory
          WHERE id = OLD.subcategory_id
        );
    END IF;

    -- Ha van új subcategory, akkor ahhoz hozzáadunk 1-et
    IF NEW.subcategory_id IS NOT NULL THEN
      UPDATE subcategory
        SET product_count = product_count + 1
        WHERE id = NEW.subcategory_id;

      UPDATE category
        SET product_count = product_count + 1
        WHERE id = (
          SELECT category_id
          FROM subcategory
          WHERE id = NEW.subcategory_id
        );
    END IF;
  END IF;
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `product_page_before_delete`;
DELIMITER $$
CREATE TRIGGER `product_page_before_delete` BEFORE DELETE ON `product_page` FOR EACH ROW BEGIN
  IF OLD.subcategory_id IS NOT NULL THEN
    -- Csökkentjük az érintett alkategória termékszámát
    UPDATE subcategory
      SET product_count = product_count - 1
      WHERE id = OLD.subcategory_id;

    -- Csökkentjük a kapcsolódó főkategória termékszámát is
    UPDATE category
      SET product_count = product_count - 1
      WHERE id = (
        SELECT category_id
        FROM subcategory
        WHERE id = OLD.subcategory_id
      );
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `product_tag`
--

DROP TABLE IF EXISTS `product_tag`;
CREATE TABLE `product_tag` (
  `id` int(11) NOT NULL,
  `tag_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;

--
-- A tábla adatainak kiíratása `product_tag`
--

INSERT INTO `product_tag` (`id`, `tag_id`, `product_id`) VALUES
(1, 1, 3),
(2, 5, 3),
(3, 10, 3),
(4, 1, 1),
(5, 5, 1),
(6, 9, 1),
(7, 10, 1),
(8, 1, 4),
(9, 5, 4),
(10, 9, 4),
(11, 10, 4),
(12, 1, 5),
(13, 5, 5),
(14, 10, 5),
(15, 1, 2),
(16, 5, 2),
(17, 9, 2),
(18, 10, 2),
(19, 1, 6),
(20, 2, 6),
(21, 5, 6),
(22, 10, 6),
(31, 1, 8),
(32, 2, 8),
(33, 5, 8),
(34, 10, 8),
(35, 1, 9),
(36, 2, 9),
(37, 5, 9),
(38, 10, 9),
(39, 2, 10),
(40, 5, 10),
(41, 10, 10),
(42, 1, 11),
(43, 2, 11),
(44, 5, 11),
(45, 10, 11),
(46, 1, 12),
(47, 2, 12),
(48, 5, 12),
(49, 10, 12),
(50, 1, 13),
(51, 2, 13),
(52, 5, 13),
(53, 10, 13),
(54, 2, 14),
(55, 5, 14),
(56, 7, 14),
(57, 10, 14),
(58, 2, 15),
(59, 5, 15),
(60, 7, 15),
(61, 10, 15),
(62, 2, 16),
(63, 5, 16),
(64, 10, 16),
(65, 2, 17),
(66, 5, 17),
(67, 10, 17),
(68, 1, 18),
(69, 2, 18),
(70, 5, 18),
(71, 7, 18),
(72, 10, 18),
(73, 1, 7),
(74, 2, 7),
(75, 5, 7),
(76, 10, 7),
(230, 1, 21),
(231, 2, 21),
(232, 5, 21),
(233, 7, 21),
(234, 10, 21),
(235, 1, 19),
(236, 2, 19),
(237, 5, 19),
(238, 10, 19),
(239, 1, 32),
(240, 2, 32),
(241, 5, 32),
(242, 7, 32),
(243, 10, 32),
(244, 1, 27),
(245, 2, 27),
(246, 5, 27),
(247, 7, 27),
(248, 10, 27),
(249, 1, 22),
(250, 2, 22),
(251, 5, 22),
(252, 7, 22),
(253, 10, 22),
(254, 1, 25),
(255, 2, 25),
(256, 5, 25),
(257, 6, 25),
(258, 10, 25),
(259, 1, 23),
(260, 2, 23),
(261, 5, 23),
(262, 10, 23),
(263, 1, 24),
(264, 2, 24),
(265, 5, 24),
(266, 10, 24),
(267, 1, 30),
(268, 2, 30),
(269, 5, 30),
(270, 7, 30),
(271, 10, 30),
(272, 1, 31),
(273, 2, 31),
(274, 5, 31),
(275, 10, 31),
(276, 1, 26),
(277, 2, 26),
(278, 5, 26),
(279, 7, 26),
(280, 10, 26),
(281, 1, 28),
(282, 2, 28),
(283, 5, 28),
(284, 6, 28),
(285, 10, 28),
(286, 1, 29),
(287, 2, 29),
(288, 5, 29),
(289, 10, 29),
(290, 1, 34),
(291, 2, 34),
(292, 5, 34),
(293, 7, 34),
(294, 10, 34);

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `review`
--

DROP TABLE IF EXISTS `review`;
CREATE TABLE `review` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `rating` double DEFAULT NULL,
  `description` text DEFAULT NULL,
  `verified_purchase` tinyint(1) NOT NULL DEFAULT 0,
  `title` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;

--
-- A tábla adatainak kiíratása `review`
--

INSERT INTO `review` (`id`, `user_id`, `product_id`, `rating`, `description`, `verified_purchase`, `title`, `created_at`) VALUES
(2, 3, 11, 3.5, 'Ez ütött, de nagyot', 1, 'Jó a cucc báttya', '2025-03-15 20:06:19'),
(4, 3, 24, 4.5, 'Komoly a por. Jézussal azóta spanok vagyunk, mióta a cuccot szedem.', 1, 'Érdekes cucc volt', '2025-04-09 18:45:33'),
(5, 4, 23, 1, 'Adipisci consectetur porro ut. Magnam adipisci ipsum dolore. Quaerat sed sit dolorem quisquam sit. Modi ipsum eius modi quisquam. Eius numquam quisquam quisquam. Quaerat dolore eius numquam est aliquam. Modi sit porro etincidunt non. Sit numquam quiquia dolorem dolorem amet sed neque.', 1, 'Sit consectetur numquam sit velit quisquam est', '2025-04-09 19:00:55'),
(6, 6, 6, 3, 'Dolor dolorem amet adipisci modi quisquam porro dolore. Tempora eius dolorem etincidunt est. Porro neque eius tempora numquam aliquam est. Dolor non non labore adipisci. Porro ut consectetur magnam. Dolorem tempora adipisci eius. Aliquam dolore porro non consectetur. Eius amet voluptatem numquam labore sed labore est.', 1, 'Numquam sed modi est numquam.', '2025-04-09 19:13:24'),
(7, 9, 11, 3, 'Modi non velit quiquia amet. Amet numquam non est adipisci quiquia. Dolorem velit dolorem est etincidunt sed ut porro. Sit porro dolore dolorem. Consectetur velit ipsum modi amet tempora quaerat porro. Magnam quiquia est voluptatem consectetur labore numquam. Adipisci sed porro etincidunt sit voluptatem labore est.', 1, 'Quaerat non quaerat ipsum non quisquam', '2025-04-09 19:16:36'),
(8, 8, 14, 3, 'Ut neque tempora labore. Aliquam ipsum dolorem velit. Labore numquam sit tempora dolor consectetur. Ut velit quaerat sit labore dolor. Magnam velit adipisci neque consectetur amet. Sit ut quisquam eius amet labore est. Sed labore porro quaerat numquam eius ut. Numquam sed porro quiquia labore sit quiquia. Dolor quaerat porro quisquam tempora.', 1, 'Ipsum sed neque non.', '2025-04-09 19:41:55'),
(9, 6, 14, 3, 'Eius quiquia quisquam neque dolorem consectetur ipsum labore. Est velit labore modi consectetur modi consectetur eius. Sed labore voluptatem tempora non ipsum dolor quiquia. Modi porro voluptatem eius neque quisquam tempora ut. Dolorem voluptatem ipsum numquam. Ut neque adipisci tempora magnam. Etincidunt tempora consectetur neque. Est adipisci sit quaerat porro voluptatem dolorem.', 1, 'Dolor voluptatem etincidunt neque porro tempora.', '2025-04-09 19:43:55'),
(10, 5, 14, 5, 'Dolorem amet ut numquam non dolorem. Amet etincidunt magnam dolor etincidunt dolor dolorem aliquam. Quaerat est voluptatem sed. Magnam neque voluptatem dolore etincidunt non. Consectetur ipsum ut labore ipsum dolorem magnam. Velit tempora voluptatem labore. Velit non quisquam dolore porro magnam porro eius. Labore aliquam ut dolorem adipisci dolor. Dolor adipisci labore est aliquam magnam ut consectetur. Consectetur dolorem aliquam consectetur labore porro ut quisquam.', 1, 'Quisquam modi porro consectetur est.', '2025-04-09 19:44:55'),
(11, 100, 8, 2, 'Adipisci dolorem modi quaerat ut dolorem dolore eius. Adipisci tempora eius non modi ut est ut. Quiquia dolor ut sit velit tempora sit quisquam. Consectetur voluptatem dolore ut neque. Quisquam numquam dolorem eius adipisci. Consectetur neque adipisci dolore. Tempora aliquam eius quisquam. Dolor eius ipsum neque labore.', 1, 'Dolore etincidunt porro labore ipsum.', '2025-04-09 20:21:30'),
(12, 39, 31, 3, 'Velit sit velit sit consectetur ut etincidunt. Dolorem neque magnam non. Quiquia quiquia dolore aliquam. Ut tempora ut quiquia velit. Sed consectetur dolore modi. Non etincidunt porro modi.', 1, 'Ipsum tempora dolorem labore numquam', '2025-04-09 20:24:31'),
(13, 51, 8, 5, 'Labore adipisci tempora etincidunt est tempora. Dolore adipisci quaerat neque porro ipsum aliquam. Quaerat aliquam adipisci quaerat. Non amet sit aliquam. Adipisci quaerat tempora velit magnam non. Dolorem dolorem labore neque aliquam sed. Velit adipisci etincidunt porro sed. Ipsum sed etincidunt ut est velit sit amet. Etincidunt aliquam dolorem non non magnam adipisci velit. Etincidunt est ut aliquam.', 1, 'Consectetur dolorem numquam quiquia.', '2025-04-09 20:25:32'),
(14, 73, 17, 5, 'Porro eius quaerat amet voluptatem quaerat. Quaerat velit quaerat est neque neque. Dolor velit tempora ipsum quisquam ipsum. Aliquam sed labore porro quaerat non. Quaerat amet numquam sit amet dolor porro. Non consectetur consectetur aliquam est etincidunt dolore magnam.', 1, 'Numquam quaerat quaerat ipsum dolorem consectetur.', '2025-04-09 20:28:32'),
(15, 60, 31, 2, 'Tempora dolor velit quisquam dolorem. Tempora est sit ipsum sed. Consectetur labore consectetur est. Eius modi eius magnam. Tempora adipisci voluptatem magnam voluptatem porro modi dolore. Aliquam amet quisquam sit magnam ipsum. Est ipsum quiquia numquam dolore labore sit.', 1, 'Quiquia consectetur numquam eius ipsum dolorem', '2025-04-09 20:29:33'),
(16, 19, 14, 3, 'Etincidunt sed etincidunt etincidunt. Dolore sed numquam sed non sit. Neque quiquia labore voluptatem dolore ipsum. Modi numquam velit quisquam amet aliquam. Dolorem adipisci sed numquam est ipsum labore. Sed neque quaerat voluptatem amet magnam quaerat.', 1, 'Modi sit eius eius dolor est quisquam.', '2025-04-09 20:30:33'),
(17, 85, 17, 2, 'Velit ipsum dolorem non amet. Labore dolore eius quaerat tempora dolore. Velit tempora porro consectetur. Velit neque adipisci dolor dolorem. Est voluptatem amet dolorem adipisci modi aliquam. Adipisci sit quisquam velit adipisci numquam labore quaerat. Tempora ipsum adipisci sed amet adipisci.', 1, 'Quiquia dolorem quisquam dolor sit quisquam aliquam.', '2025-04-09 20:33:34'),
(18, 88, 31, 3, 'Numquam porro ut consectetur sed etincidunt. Dolor quaerat consectetur velit dolore neque etincidunt. Aliquam quisquam magnam amet. Voluptatem est voluptatem dolorem. Tempora dolor numquam labore consectetur amet. Voluptatem quaerat adipisci tempora quaerat velit aliquam ut. Velit voluptatem modi adipisci. Quisquam velit velit modi dolor voluptatem non. Aliquam dolorem non velit voluptatem consectetur ipsum sed.', 1, 'Ipsum ipsum eius etincidunt.', '2025-04-09 20:37:35'),
(19, 91, 13, 3, 'Sed voluptatem neque voluptatem. Ut tempora sed sit eius aliquam. Dolor neque modi neque dolore ipsum numquam. Dolore adipisci aliquam voluptatem. Sit numquam velit etincidunt sit consectetur. Eius modi magnam velit dolor neque est.', 1, 'Etincidunt amet velit quaerat etincidunt tempora', '2025-04-09 20:40:37'),
(20, 57, 6, 4, 'Quiquia est quaerat sed neque non est amet. Velit porro quiquia aliquam dolore eius voluptatem amet. Neque quiquia sed consectetur. Est quiquia ut consectetur quiquia magnam sit. Quisquam etincidunt eius neque labore magnam. Sed quaerat amet sed. Voluptatem dolore sit dolore consectetur tempora quisquam modi. Sed eius ipsum voluptatem ipsum non quiquia. Velit consectetur quiquia modi magnam. Non labore aliquam sit adipisci.', 1, 'Dolor voluptatem amet amet eius.', '2025-04-09 20:41:37'),
(21, 6, 11, 5, 'Ut modi numquam neque. Neque voluptatem numquam modi. Dolorem dolore ut non quaerat aliquam. Porro etincidunt voluptatem quisquam. Sed etincidunt quiquia labore aliquam voluptatem.', 1, 'Eius ut neque non.', '2025-04-09 20:42:37'),
(22, 94, 6, 2, 'Numquam sit ipsum numquam magnam aliquam adipisci ipsum. Dolorem quiquia labore numquam adipisci. Dolor quisquam numquam quaerat modi numquam ipsum magnam. Amet consectetur modi est dolor. Est eius magnam est velit labore dolore ut. Eius quisquam consectetur est etincidunt. Dolore sit sed quisquam numquam. Numquam numquam dolorem eius aliquam ut velit. Labore amet modi modi labore neque.', 1, 'Velit sed non modi.', '2025-04-09 20:47:38'),
(23, 22, 6, 5, 'Aliquam numquam magnam porro quisquam quaerat dolor est. Quiquia eius quiquia etincidunt. Aliquam eius consectetur voluptatem tempora magnam. Adipisci etincidunt neque velit numquam. Dolore sit etincidunt porro ut aliquam non.', 1, 'Velit modi consectetur adipisci quiquia quaerat ipsum.', '2025-04-09 20:51:37'),
(24, 26, 27, 4, 'Dolor labore etincidunt sit labore numquam etincidunt dolore. Neque dolorem est dolorem quiquia. Labore ut amet non ipsum tempora non. Sit quisquam tempora tempora velit consectetur modi. Velit dolore consectetur consectetur ut consectetur. Modi sit adipisci dolorem consectetur magnam ut quiquia.', 1, 'Labore ipsum consectetur sed.', '2025-04-09 20:52:37'),
(25, 83, 17, 4, 'Amet velit labore ipsum labore. Est labore non labore. Dolorem porro dolore ipsum est numquam. Quisquam aliquam ipsum ut ipsum tempora. Dolore voluptatem quisquam quaerat ipsum sit magnam. Modi quaerat labore sed ut. Quisquam numquam numquam labore ut ut. Neque eius quisquam amet sit modi.', 1, 'Eius porro modi labore sed consectetur quaerat.', '2025-04-09 20:56:38'),
(26, 5, 8, 2, 'Adipisci aliquam amet velit quaerat quiquia dolor. Amet voluptatem sed voluptatem consectetur dolor labore amet. Porro magnam est magnam dolore aliquam quaerat est. Sed quiquia ut tempora est dolorem neque. Quiquia neque quaerat quisquam numquam non. Consectetur porro ipsum magnam aliquam porro. Porro porro ipsum numquam dolore etincidunt. Sed porro etincidunt neque. Ipsum aliquam dolore ut aliquam labore non magnam.', 1, 'Dolor adipisci ut tempora magnam.', '2025-04-09 20:57:38'),
(27, 11, 7, 4, 'Sed ut velit porro quiquia quisquam. Etincidunt quiquia sit sit neque sit. Dolor non velit dolorem amet etincidunt sit. Amet labore quaerat tempora sit sit. Modi modi tempora quisquam neque labore labore. Aliquam labore neque dolor porro aliquam. Numquam amet ipsum tempora amet.', 1, 'Numquam dolorem non ut neque dolor.', '2025-04-09 20:59:38'),
(28, 87, 14, 2, 'Dolor adipisci sed est. Quaerat dolorem adipisci est. Aliquam modi tempora dolore. Dolor velit dolor sit non sit quaerat magnam. Dolorem tempora aliquam neque eius est. Magnam dolore numquam magnam sit. Tempora est dolor sed sed dolore non labore. Modi ut voluptatem etincidunt adipisci eius. Ut numquam velit dolorem dolorem. Modi quiquia modi porro numquam porro amet tempora.', 1, 'Labore sed consectetur quisquam etincidunt.', '2025-04-09 21:01:38'),
(29, 58, 11, 3, 'Adipisci adipisci consectetur etincidunt adipisci sit. Quisquam non non eius etincidunt ut. Ut dolore dolore aliquam sit aliquam. Neque velit numquam aliquam dolor numquam adipisci. Quiquia dolore sed quisquam neque amet. Quisquam modi dolore est sed porro voluptatem dolor. Ipsum labore sed dolor quisquam magnam. Consectetur modi ut non quisquam etincidunt non modi. Dolorem modi dolor est dolorem est quiquia. Tempora dolor porro est quiquia quiquia numquam.', 1, 'Ut sed sed dolorem.', '2025-04-09 21:04:37'),
(30, 68, 7, 2, 'Sit labore amet sit eius consectetur. Consectetur dolorem magnam eius. Magnam sit ipsum modi aliquam. Neque adipisci labore numquam ipsum tempora. Voluptatem quaerat porro ut adipisci. Tempora etincidunt adipisci tempora non.', 1, 'Dolorem tempora eius quaerat consectetur.', '2025-04-09 21:05:37'),
(31, 30, 11, 4, 'Labore ipsum adipisci non ipsum modi est. Ipsum modi etincidunt velit adipisci dolor. Ipsum eius quisquam quiquia ut numquam. Sit numquam quisquam tempora. Ipsum etincidunt sit quiquia est sit modi voluptatem.', 1, 'Neque est voluptatem sit ipsum ut est.', '2025-04-09 21:06:37'),
(32, 13, 18, 3, 'Ipsum labore aliquam non dolorem velit. Dolorem sit ipsum velit. Labore etincidunt sed etincidunt dolor quaerat. Ut neque porro porro amet voluptatem quisquam. Etincidunt etincidunt velit adipisci voluptatem neque amet sed. Sit non amet etincidunt. Ut quiquia sed quiquia porro modi. Dolorem neque modi labore non ut.', 1, 'Ut sit est sit ipsum dolorem quiquia.', '2025-04-09 21:07:37'),
(33, 43, 32, 5, 'Labore etincidunt dolore neque voluptatem labore. Dolore dolor consectetur quaerat. Tempora sed consectetur neque consectetur amet est. Aliquam non sit adipisci adipisci modi aliquam. Quiquia modi quaerat velit dolor. Ipsum magnam sed est. Dolorem modi magnam etincidunt dolor sed. Sit tempora non numquam quiquia quiquia quisquam.', 1, 'Tempora magnam quisquam modi ut quiquia.', '2025-04-09 21:08:37'),
(34, 59, 19, 4, 'Neque labore non numquam quaerat est. Velit modi magnam ut tempora consectetur est porro. Dolorem labore labore quisquam numquam dolor labore. Velit porro quisquam neque. Etincidunt ipsum eius ut adipisci. Dolore amet adipisci quisquam. Ut dolorem etincidunt quisquam ut sed adipisci voluptatem.', 1, 'Aliquam quisquam non labore porro', '2025-04-09 21:10:37'),
(35, 24, 12, 3, 'Numquam neque tempora quaerat tempora modi consectetur. Modi sit sit dolore etincidunt ipsum. Modi modi amet ipsum. Ut porro est sed dolor dolorem non. Neque ipsum non modi magnam. Porro quaerat consectetur quaerat consectetur amet. Ipsum dolore ipsum amet. Sit tempora modi neque quiquia ut sit.', 1, 'Amet quaerat eius neque ut.', '2025-04-09 21:11:37'),
(36, 28, 9, 3, 'Etincidunt dolor adipisci voluptatem sed. Quiquia ipsum dolore modi ipsum numquam etincidunt dolore. Dolorem ipsum numquam sit. Eius ut quaerat porro etincidunt quaerat. Modi aliquam labore porro voluptatem dolore amet.', 1, 'Adipisci dolor modi non.', '2025-04-09 21:12:37'),
(37, 77, 21, 2, 'Dolorem modi porro porro aliquam voluptatem amet. Numquam neque est magnam dolor. Ut consectetur porro velit etincidunt neque. Ut neque modi velit quaerat. Magnam ipsum numquam velit consectetur. Est numquam dolore est aliquam. Velit amet aliquam consectetur non. Ut dolore porro numquam amet modi. Voluptatem magnam est labore neque velit porro dolorem.', 1, 'Modi tempora amet neque quiquia', '2025-04-09 21:14:37'),
(38, 97, 8, 3, 'Quisquam porro aliquam tempora. Quaerat labore modi modi magnam porro. Sed sit aliquam sed neque dolore dolor. Velit tempora labore numquam quisquam porro ipsum sed. Modi neque dolore labore porro. Amet ut velit amet. Dolorem adipisci dolorem aliquam. Velit ut adipisci sed numquam velit dolore. Voluptatem numquam tempora numquam.', 1, 'Porro magnam labore neque labore ut ipsum.', '2025-04-09 21:16:37'),
(39, 12, 6, 3, 'Tempora numquam labore quaerat. Quiquia quisquam neque aliquam. Neque quiquia etincidunt consectetur adipisci sit. Neque tempora quiquia dolore ut. Velit labore neque neque. Voluptatem quisquam eius porro dolor est dolor. Adipisci est ipsum dolore labore tempora ut magnam. Ipsum neque dolore non porro labore porro. Non labore amet dolor quaerat. Dolor dolor quisquam dolorem voluptatem modi modi.', 1, 'Eius sed tempora adipisci dolore', '2025-04-09 21:17:37'),
(40, 23, 30, 4, 'Sed sed aliquam etincidunt numquam quiquia est. Dolore eius aliquam porro velit. Numquam consectetur velit aliquam. Labore sed sit sed modi. Modi voluptatem ut quisquam neque. Amet aliquam aliquam sed. Eius voluptatem consectetur dolor ut consectetur quaerat.', 1, 'Tempora porro magnam eius.', '2025-04-09 21:18:37'),
(41, 95, 19, 5, 'Dolor amet amet sed amet etincidunt dolorem labore. Magnam modi dolor aliquam sed. Dolorem quiquia aliquam magnam eius eius. Quaerat dolore velit non. Sit quisquam amet labore non est labore adipisci.', 1, 'Dolore labore tempora ut sit sit labore modi.', '2025-04-09 21:19:38'),
(42, 63, 24, 3, 'Adipisci consectetur quiquia magnam labore. Aliquam quiquia porro sed dolorem etincidunt. Non voluptatem voluptatem labore dolor quiquia dolor. Dolore dolor neque neque adipisci. Etincidunt amet est adipisci modi. Tempora dolorem etincidunt quaerat eius. Dolorem ipsum sed etincidunt eius.', 1, 'Est quisquam labore velit dolor quisquam quaerat', '2025-04-09 21:21:38'),
(43, 34, 11, 5, 'Sed quisquam etincidunt modi tempora numquam tempora amet. Tempora numquam consectetur sit etincidunt. Numquam numquam ut aliquam quiquia. Quisquam sed est consectetur velit ut modi. Voluptatem dolore quiquia neque numquam quisquam quaerat. Neque quaerat velit ipsum dolore quaerat tempora etincidunt. Modi ipsum amet dolor modi. Adipisci non non consectetur quaerat sed dolore tempora. Voluptatem voluptatem ipsum sit dolorem porro labore sit. Modi sit porro velit labore.', 1, 'Ipsum aliquam quisquam quaerat amet dolor', '2025-04-09 21:25:38'),
(44, 27, 6, 3, 'Numquam numquam ut porro ut etincidunt. Ipsum eius modi quaerat numquam voluptatem velit est. Magnam eius quiquia numquam velit. Magnam aliquam quiquia neque dolor amet quisquam etincidunt. Velit ipsum aliquam sit sit non. Ipsum adipisci dolore dolorem adipisci. Sed amet labore quaerat etincidunt ut etincidunt est. Quiquia amet dolorem dolor tempora. Eius tempora neque quiquia quaerat. Voluptatem sed dolore neque sed consectetur velit.', 1, 'Sit tempora tempora ut dolore est est.', '2025-04-09 21:26:38'),
(45, 70, 17, 4, 'Porro sed modi ut magnam. Quiquia non ut quiquia tempora dolorem eius dolore. Sed quisquam velit aliquam etincidunt velit modi dolor. Porro sed porro sed ipsum. Neque adipisci sed porro non quaerat neque labore. Sit non dolorem quiquia etincidunt quisquam eius. Numquam modi amet adipisci tempora ipsum eius numquam. Dolore dolore modi aliquam aliquam velit.', 1, 'Modi magnam etincidunt neque velit.', '2025-04-09 21:27:38'),
(46, 81, 9, 2, 'Dolorem quisquam ipsum numquam consectetur aliquam adipisci labore. Dolore velit voluptatem modi eius quaerat adipisci. Est ut eius ut ipsum. Est ipsum labore dolorem. Labore non tempora etincidunt quiquia porro. Velit porro quiquia consectetur. Sed labore magnam magnam neque porro ipsum. Sed amet ut etincidunt quisquam tempora. Magnam tempora porro voluptatem velit.', 1, 'Ut dolore voluptatem sit.', '2025-04-09 21:28:38'),
(47, 67, 22, 4, 'Est ut ut consectetur adipisci porro quiquia voluptatem. Porro dolore neque modi amet quisquam dolore. Amet neque sed dolorem. Ut velit sit adipisci consectetur dolor amet. Adipisci neque voluptatem ipsum quisquam. Labore aliquam est quaerat numquam. Tempora sit dolor labore voluptatem neque numquam. Quiquia sed tempora aliquam velit sed. Tempora adipisci ut quisquam est quisquam velit dolor.', 1, 'Dolore quiquia labore ipsum adipisci adipisci', '2025-04-09 21:30:37'),
(48, 102, 27, 5, 'Adipisci ut quiquia numquam magnam aliquam. Amet labore neque ipsum. Tempora velit quisquam quisquam quaerat non amet dolorem. Est voluptatem velit modi neque quaerat consectetur. Ut neque quisquam modi etincidunt. Amet ut quiquia dolor aliquam. Dolore quisquam quiquia porro ipsum consectetur ipsum. Sed ipsum porro adipisci quiquia sed modi.', 1, 'Porro sit dolorem amet.', '2025-04-09 21:33:38'),
(49, 61, 8, 5, 'Eius etincidunt eius ut quaerat porro porro. Numquam etincidunt labore modi sit ut. Labore dolorem sit velit neque. Dolore eius magnam porro dolor porro. Quiquia consectetur quisquam labore voluptatem amet neque. Aliquam eius numquam ut neque non ut. Ut labore labore adipisci non aliquam. Etincidunt consectetur voluptatem dolore.', 1, 'Porro sit est magnam non', '2025-04-09 21:34:38'),
(50, 104, 17, 4, 'Dolore etincidunt consectetur voluptatem sed eius. Numquam ut dolore magnam. Voluptatem porro adipisci modi magnam. Adipisci tempora adipisci magnam porro sed. Tempora amet magnam sit.', 1, 'Tempora eius etincidunt sed quaerat', '2025-04-09 21:35:37'),
(51, 72, 31, 3, 'Magnam labore tempora tempora velit dolor etincidunt sed. Magnam consectetur porro sed. Adipisci dolor quiquia amet voluptatem ipsum. Quiquia non ut dolore. Sit est quisquam modi dolorem dolore.', 1, 'Quiquia modi numquam adipisci dolorem magnam eius.', '2025-04-09 21:36:38'),
(52, 17, 22, 3, 'Velit dolorem modi sit. Dolor non ut quaerat quaerat magnam. Consectetur est sed quaerat neque sed. Aliquam adipisci sed dolorem. Tempora dolore quiquia etincidunt sed eius quiquia. Labore eius tempora voluptatem non labore. Quisquam labore voluptatem labore labore ut porro velit. Neque dolore numquam magnam sed non.', 1, 'Ut modi numquam amet dolorem adipisci porro.', '2025-04-09 21:37:38'),
(53, 37, 13, 3, 'Non neque magnam non est numquam. Velit dolore aliquam aliquam adipisci. Ipsum tempora sed quiquia sed sit. Tempora etincidunt adipisci magnam est ipsum. Ipsum adipisci velit labore magnam aliquam tempora. Modi magnam sit dolor adipisci.', 1, 'Velit adipisci magnam magnam ut dolor', '2025-04-09 21:38:37'),
(54, 16, 9, 4, 'Aliquam adipisci amet neque magnam. Magnam aliquam tempora dolor eius dolorem amet neque. Non tempora quiquia velit dolor. Modi magnam porro numquam. Quaerat modi etincidunt labore. Tempora porro est sit ut magnam dolorem neque. Ipsum velit modi aliquam quiquia adipisci. Velit eius quisquam quiquia tempora quaerat consectetur est. Velit sit quiquia etincidunt numquam aliquam ut. Velit quisquam amet est.', 1, 'Sed etincidunt quisquam adipisci quaerat', '2025-04-09 21:39:37'),
(55, 9, 30, 2, 'Dolor non tempora magnam adipisci neque ut aliquam. Ipsum neque sit quaerat est amet. Eius dolorem velit sit porro eius. Tempora velit quisquam dolore neque labore quiquia. Amet quiquia quaerat adipisci dolorem aliquam porro quisquam. Quisquam dolore dolorem numquam dolorem dolorem sit quiquia. Consectetur aliquam est dolore velit neque modi.', 1, 'Non aliquam dolorem aliquam consectetur', '2025-04-09 21:40:37'),
(56, 4, 34, 3, 'Dolor dolore voluptatem dolor quisquam. Quiquia ut neque modi. Non velit adipisci ipsum consectetur consectetur est porro. Dolore dolore sit tempora quaerat. Voluptatem aliquam dolorem numquam. Quaerat ipsum quiquia sit dolor numquam est velit. Dolor quaerat velit modi dolore adipisci quaerat. Magnam etincidunt non velit. Dolor neque amet numquam dolor. Quiquia quaerat ipsum labore.', 1, 'Ut ut voluptatem etincidunt tempora.', '2025-04-09 21:53:26'),
(57, 20, 9, 3, 'Amet voluptatem eius eius dolorem. Sed numquam ut porro sed magnam dolorem labore. Quiquia adipisci quiquia aliquam sed ipsum. Magnam eius sit sit neque dolorem. Velit aliquam tempora etincidunt modi sed. Aliquam quisquam non non. Dolor eius modi ipsum sed tempora labore sit. Amet est etincidunt sit dolore porro. Modi ipsum etincidunt dolor aliquam sit.', 1, 'Velit ipsum quiquia voluptatem aliquam sit consectetur.', '2025-04-09 21:55:25'),
(58, 21, 7, 4, 'Consectetur etincidunt dolor neque modi porro. Dolor dolorem modi quisquam. Sed velit dolor quaerat. Quaerat neque eius labore labore magnam sit. Dolor etincidunt porro magnam etincidunt numquam. Etincidunt consectetur modi voluptatem quiquia neque. Sed dolore quiquia non ipsum quisquam sit. Non velit dolore non neque quisquam. Quisquam porro velit quaerat labore.', 1, 'Amet numquam adipisci eius aliquam etincidunt dolorem.', '2025-04-09 21:58:25'),
(59, 5, 9, 3, 'Neque ut porro voluptatem sit modi. Non magnam etincidunt modi. Tempora non modi adipisci. Ut dolore est sed neque velit. Ipsum porro dolore est labore quisquam. Eius tempora ut numquam porro numquam dolore. Quaerat neque neque numquam modi. Etincidunt porro velit quisquam est.', 1, 'Sit magnam modi eius eius', '2025-04-09 21:59:25'),
(60, 104, 21, 5, 'Amet aliquam quaerat velit sed tempora labore. Ipsum quiquia dolor quaerat dolore. Quiquia quisquam dolor labore dolore adipisci amet. Modi ut adipisci modi. Porro adipisci quaerat quiquia etincidunt.', 1, 'Sit dolorem quaerat eius.', '2025-04-09 22:00:24'),
(61, 24, 13, 5, 'Amet sit sed non quisquam. Labore adipisci porro sed aliquam. Amet neque adipisci consectetur numquam modi. Quaerat ipsum etincidunt dolor. Porro neque eius eius numquam tempora quisquam.', 1, 'Ipsum adipisci voluptatem eius adipisci voluptatem.', '2025-04-09 22:04:24'),
(62, 64, 12, 4, 'Dolor labore velit non est porro. Velit eius dolore neque ut. Modi sed dolorem sit quaerat quisquam. Porro dolorem aliquam est. Amet aliquam dolore modi amet. Dolor quisquam porro numquam sed. Modi magnam consectetur quisquam quiquia non sed etincidunt. Porro labore est modi.', 1, 'Adipisci sit quiquia voluptatem aliquam labore neque ipsum.', '2025-04-09 22:06:24'),
(63, 37, 31, 5, 'Consectetur numquam eius quiquia neque. Modi magnam sit modi labore. Quiquia quiquia porro labore numquam. Aliquam non voluptatem tempora sed aliquam dolor porro. Sit quaerat est aliquam est. Consectetur labore adipisci voluptatem numquam.', 1, 'Dolor numquam dolore amet sit', '2025-04-09 22:07:24'),
(64, 102, 9, 3, 'Magnam quiquia modi labore. Ipsum etincidunt sit velit quaerat ut. Magnam dolore consectetur dolore adipisci etincidunt. Dolor neque sed dolor. Sit etincidunt tempora sit velit sit labore labore. Non eius ut sed. Etincidunt est sit aliquam. Eius quisquam non porro non dolore amet quaerat.', 1, 'Est magnam etincidunt quaerat labore.', '2025-04-09 22:08:24'),
(65, 82, 4, 3, 'Dolorem quaerat quaerat aliquam. Consectetur non magnam velit. Dolorem ut ut quiquia quaerat dolore. Dolor consectetur dolor sed neque dolore sit sit. Consectetur adipisci dolor porro amet sed magnam aliquam. Aliquam numquam adipisci aliquam dolore amet magnam. Dolore non eius numquam eius non dolorem.', 1, 'Quiquia non adipisci neque dolorem ut.', '2025-04-09 22:10:24'),
(66, 58, 6, 5, 'Quisquam labore porro est modi numquam. Voluptatem velit adipisci non dolore. Amet adipisci tempora dolorem etincidunt aliquam numquam. Non amet aliquam adipisci voluptatem sit consectetur dolorem. Tempora dolore quisquam voluptatem. Adipisci etincidunt porro quiquia adipisci amet adipisci ipsum. Ut velit etincidunt adipisci tempora. Est tempora velit velit neque dolore dolorem magnam.', 1, 'Quiquia voluptatem modi non ipsum.', '2025-04-09 22:12:24'),
(67, 9, 24, 4, 'Numquam quaerat voluptatem ipsum porro. Quaerat amet quiquia dolorem. Eius consectetur quiquia porro labore quiquia est. Voluptatem porro numquam est eius ipsum magnam consectetur. Adipisci aliquam adipisci numquam. Est quaerat quaerat quaerat. Velit velit adipisci sed. Aliquam labore velit velit adipisci dolore velit dolore. Eius consectetur est modi magnam voluptatem dolore magnam.', 1, 'Sed neque ipsum non.', '2025-04-09 22:16:24'),
(68, 95, 7, 3, 'Adipisci dolore magnam sit etincidunt eius. Amet dolore dolorem numquam etincidunt non modi. Est labore dolore ut dolorem quaerat tempora quiquia. Labore porro amet aliquam labore. Sed magnam amet quiquia sit sit adipisci. Etincidunt amet quiquia consectetur voluptatem. Tempora numquam eius numquam. Tempora voluptatem dolor sit. Amet quaerat labore adipisci neque eius ipsum. Non sed velit sed porro.', 1, 'Quaerat ut quaerat amet numquam.', '2025-04-09 22:17:24'),
(69, 90, 25, 5, 'Est consectetur consectetur eius amet consectetur quisquam consectetur. Non dolor velit est sit. Ipsum tempora quiquia dolorem non neque amet sit. Ipsum voluptatem ut velit. Neque adipisci dolor sit. Tempora quiquia consectetur dolor. Quaerat velit sit ut adipisci sed voluptatem amet.', 1, 'Modi dolor etincidunt quisquam velit numquam.', '2025-04-09 22:20:24'),
(70, 97, 5, 5, 'Labore est ipsum modi labore velit. Magnam non ipsum sed quisquam dolore amet. Dolore porro quiquia non modi porro voluptatem. Quiquia labore modi consectetur. Adipisci velit ipsum quaerat neque aliquam dolor voluptatem. Aliquam dolore neque tempora quaerat. Modi ipsum ipsum quisquam aliquam. Numquam adipisci non ut porro tempora.', 1, 'Aliquam non sed labore modi', '2025-04-09 22:23:29'),
(71, 80, 13, 3, 'Porro porro dolorem voluptatem etincidunt adipisci. Non adipisci dolore ipsum sit tempora. Etincidunt aliquam adipisci dolorem sit modi non quiquia. Modi numquam numquam voluptatem adipisci. Numquam ipsum consectetur sit tempora eius consectetur modi. Dolor quiquia voluptatem velit. Neque adipisci est eius. Velit labore magnam voluptatem quaerat quaerat dolore.', 1, 'Adipisci dolore ut consectetur dolor dolorem', '2025-04-09 22:24:28'),
(72, 48, 22, 5, 'Non sed quiquia dolore sed. Quiquia porro neque magnam aliquam sit ut. Consectetur porro quaerat velit est dolor numquam velit. Amet sit sit adipisci aliquam adipisci neque dolore. Etincidunt adipisci quaerat magnam labore adipisci. Labore magnam dolor quiquia dolore etincidunt quiquia quaerat. Adipisci modi ipsum eius dolor. Modi etincidunt dolor numquam porro.', 1, 'Dolor etincidunt etincidunt modi ut amet.', '2025-04-09 22:25:29'),
(73, 93, 6, 5, 'Sit porro porro quisquam neque quisquam dolorem. Neque neque adipisci numquam. Magnam voluptatem quisquam quiquia. Aliquam eius ipsum adipisci quaerat velit ipsum adipisci. Quaerat neque tempora consectetur ipsum amet. Porro aliquam ut voluptatem.', 1, 'Labore consectetur dolor quaerat.', '2025-04-09 22:26:29'),
(74, 8, 6, 5, 'Dolor amet eius sed. Amet modi non numquam consectetur ipsum eius porro. Est magnam aliquam amet. Adipisci neque modi magnam adipisci dolore voluptatem. Magnam labore sed etincidunt sed. Voluptatem est non amet labore voluptatem.', 1, 'Sed consectetur magnam aliquam dolore velit eius.', '2025-04-09 22:27:29'),
(75, 70, 15, 4, 'Dolorem dolor consectetur labore magnam sed. Aliquam labore sit ipsum etincidunt. Dolorem quaerat etincidunt magnam labore quisquam sit. Numquam voluptatem est quiquia voluptatem. Magnam est amet dolorem dolor neque quaerat. Ipsum ipsum magnam porro modi numquam ipsum. Consectetur neque ut voluptatem adipisci neque. Non eius magnam amet non quisquam voluptatem. Amet non etincidunt consectetur tempora quaerat adipisci magnam. Tempora tempora eius quisquam est modi.', 1, 'Ut neque dolore quaerat numquam est neque quisquam.', '2025-04-09 22:28:29'),
(76, 13, 21, 4, 'Dolor magnam est amet numquam. Labore tempora dolorem quisquam aliquam quisquam. Dolorem labore non sed numquam neque eius numquam. Magnam aliquam dolorem voluptatem velit tempora. Tempora numquam dolor non. Numquam quisquam adipisci quisquam. Ipsum dolorem etincidunt dolor est voluptatem quisquam dolorem. Ut est consectetur voluptatem velit dolorem est. Quisquam neque labore quiquia adipisci neque. Consectetur quaerat numquam est magnam.', 1, 'Ipsum quaerat sit neque dolor sed aliquam', '2025-04-09 22:29:30'),
(77, 11, 17, 5, 'Voluptatem magnam voluptatem non adipisci ipsum est est. Ut ut voluptatem dolorem dolorem voluptatem neque ipsum. Quiquia sed non modi amet quaerat neque. Voluptatem dolore magnam ipsum numquam magnam. Amet amet dolor tempora non neque etincidunt. Sed eius quiquia consectetur porro voluptatem. Quaerat quiquia quaerat modi. Adipisci dolor quiquia eius ut. Adipisci ut sit est. Quisquam sit sed dolore ut etincidunt.', 1, 'Magnam non ipsum quisquam aliquam', '2025-04-09 22:30:30'),
(78, 50, 27, 4, 'Dolore dolore quaerat dolor est quaerat. Non ipsum aliquam est sit magnam labore tempora. Aliquam quisquam eius modi etincidunt. Quisquam voluptatem velit consectetur. Dolore ut voluptatem adipisci sed quisquam quaerat. Amet modi adipisci magnam eius tempora ut labore. Numquam etincidunt tempora modi adipisci. Sit quaerat amet magnam quisquam sed ut.', 1, 'Neque sit quiquia voluptatem dolorem ipsum porro.', '2025-04-09 22:31:30'),
(79, 54, 7, 3, 'Velit ipsum tempora adipisci adipisci non. Porro labore eius est quisquam. Magnam magnam numquam ut. Tempora voluptatem aliquam labore quiquia sed non. Ipsum ipsum etincidunt modi dolore adipisci dolore numquam. Consectetur ipsum sed voluptatem ipsum.', 1, 'Neque quisquam modi adipisci etincidunt quaerat', '2025-04-09 22:32:29'),
(80, 40, 23, 5, 'Sed dolorem labore voluptatem porro consectetur voluptatem. Quiquia amet labore amet voluptatem est. Numquam ipsum est sit. Sit dolor etincidunt dolore etincidunt etincidunt voluptatem. Est etincidunt tempora tempora voluptatem sed est.', 1, 'Modi consectetur neque sit magnam.', '2025-04-09 22:33:30'),
(81, 22, 17, 3, 'Modi aliquam aliquam consectetur aliquam ipsum dolor. Adipisci sed ut sit velit consectetur aliquam. Est dolorem consectetur non. Voluptatem porro modi etincidunt velit ipsum. Sit tempora non etincidunt dolor consectetur. Neque labore modi sed voluptatem adipisci aliquam. Modi aliquam sed adipisci voluptatem magnam numquam modi. Quiquia etincidunt numquam adipisci ut aliquam neque sit.', 1, 'Labore quiquia modi velit dolor.', '2025-04-09 22:34:29'),
(82, 7, 17, 5, 'Ipsum adipisci voluptatem aliquam voluptatem amet ut. Ut tempora eius etincidunt. Sed dolor consectetur voluptatem quaerat est. Adipisci dolorem dolorem magnam consectetur modi magnam. Consectetur ipsum ut etincidunt sit magnam velit amet.', 1, 'Consectetur modi labore aliquam eius', '2025-04-09 22:35:29'),
(83, 73, 7, 5, 'Etincidunt consectetur amet amet. Etincidunt consectetur dolor dolore etincidunt. Labore dolor ipsum adipisci dolorem quaerat. Porro dolorem magnam velit dolor sit dolore dolore. Amet adipisci aliquam velit magnam labore dolor quiquia. Neque aliquam est dolor. Numquam non numquam dolor magnam amet dolore adipisci. Eius amet dolor adipisci dolor. Numquam etincidunt quiquia dolor dolore dolore. Tempora tempora tempora sed aliquam ipsum.', 1, 'Aliquam neque etincidunt eius.', '2025-04-09 22:36:30'),
(84, 87, 22, 3, 'Neque aliquam quiquia velit tempora quisquam velit consectetur. Quiquia magnam velit dolor eius sit est. Porro est porro neque amet amet. Etincidunt est eius est sit quaerat adipisci voluptatem. Aliquam neque ipsum neque tempora adipisci etincidunt quiquia. Eius ut neque non amet tempora.', 1, 'Neque velit dolor consectetur porro', '2025-04-09 22:37:30'),
(85, 59, 22, 3, 'Ipsum dolore tempora amet. Tempora aliquam velit porro labore quaerat modi. Sed ipsum voluptatem amet ipsum. Consectetur labore eius tempora dolor aliquam ipsum modi. Aliquam quiquia consectetur est. Quisquam dolorem velit sit porro. Magnam quaerat quisquam numquam. Est dolorem adipisci magnam. Dolore voluptatem aliquam numquam tempora quiquia numquam. Labore dolorem voluptatem dolorem dolor tempora amet.', 1, 'Ipsum tempora consectetur porro.', '2025-04-09 22:38:30'),
(86, 109, 19, 3, 'Dolor sit ipsum ipsum est tempora velit. Tempora dolor consectetur velit. Ut porro aliquam quaerat dolore adipisci. Voluptatem voluptatem consectetur numquam dolorem numquam. Sed quiquia sed ipsum numquam. Tempora consectetur neque sed. Aliquam modi magnam porro. Porro eius sit aliquam. Amet non numquam amet tempora porro.', 1, 'Tempora dolore etincidunt velit velit.', '2025-04-09 22:39:30'),
(87, 84, 12, 3, 'Consectetur sed consectetur dolor etincidunt tempora. Tempora magnam dolore numquam eius quiquia quiquia. Velit velit quisquam sit sed etincidunt adipisci non. Numquam eius tempora amet non tempora voluptatem modi. Eius tempora eius sed est. Dolore consectetur non non amet numquam. Sed tempora voluptatem aliquam consectetur dolor. Dolorem dolor quisquam dolorem adipisci.', 1, 'Consectetur porro sit ipsum velit', '2025-04-09 22:40:30'),
(88, 66, 8, 4, 'Dolor dolor voluptatem quaerat sed modi neque. Sit eius quisquam magnam velit numquam aliquam aliquam. Quaerat quaerat voluptatem quiquia voluptatem magnam. Velit numquam numquam labore ut. Ipsum quisquam adipisci dolorem ut est. Dolor etincidunt labore magnam.', 1, 'Quisquam eius tempora adipisci aliquam', '2025-04-09 22:41:30'),
(89, 55, 27, 3, 'Voluptatem eius tempora porro quiquia non. Modi quisquam dolor dolorem quiquia. Neque consectetur labore ut. Aliquam quisquam sit labore numquam sit modi est. Sit numquam quiquia ipsum voluptatem velit. Labore non etincidunt labore amet.', 1, 'Eius quisquam aliquam dolorem dolorem.', '2025-04-09 22:42:30'),
(90, 107, 22, 5, 'Etincidunt adipisci quiquia eius dolore. Sit quaerat ut etincidunt est etincidunt etincidunt quaerat. Est porro est dolor sit. Aliquam adipisci sit sed non magnam. Est voluptatem neque dolorem. Neque sed dolore aliquam amet velit ut. Ut labore sit dolore. Dolore non numquam non non labore velit magnam. Numquam magnam magnam voluptatem adipisci aliquam neque numquam.', 1, 'Non ipsum adipisci velit velit.', '2025-04-09 22:44:30'),
(91, 19, 21, 4, 'Sit quisquam modi aliquam dolorem tempora quiquia adipisci. Numquam ut tempora modi ut sed adipisci neque. Adipisci magnam dolore quaerat quaerat velit. Sed numquam tempora velit velit modi. Neque labore quisquam aliquam tempora.', 1, 'Dolore non non quiquia aliquam eius.', '2025-04-09 22:45:30');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `subcategory`
--

DROP TABLE IF EXISTS `subcategory`;
CREATE TABLE `subcategory` (
  `id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL COMMENT 'On delete: SET NULL',
  `name` varchar(255) DEFAULT NULL,
  `subname` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `thumbnail_image_vertical_uri` varchar(255) DEFAULT NULL,
  `thumbnail_image_horizontal_uri` varchar(255) DEFAULT NULL,
  `thumbnail_video_uri` varchar(255) DEFAULT NULL,
  `product_count` int(11) NOT NULL DEFAULT 0,
  `slug` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;

--
-- A tábla adatainak kiíratása `subcategory`
--

INSERT INTO `subcategory` (`id`, `category_id`, `name`, `subname`, `description`, `thumbnail_image_vertical_uri`, `thumbnail_image_horizontal_uri`, `thumbnail_video_uri`, `product_count`, `slug`) VALUES
(1, 1, 'Méregtelenítő Növények', 'A tisztulás támogatása természetes módon', 'A méregtelenítő növények segítenek a test tisztulási folyamatainak elősegítésében, eltávolítva a felhalmozódott toxikus anyagokat.', 'http://localhost/fb-content/fb-subcategories/media/images/category-1/thumbnail_image_vertical.jpg', 'http://localhost/fb-content/fb-subcategories/media/images/category-1/thumbnail_image_horizontal.jpg', NULL, 4, 'meregtelenito-novenyek'),
(2, 1, 'Immunerősítő Keverékek', 'A védekezőképesség fokozása', 'Erőteljes gyógynövény-keverékek, amelyek támogatják az immunrendszert, erősítve a szervezet védekezőképességét.', 'http://localhost/fb-content/fb-subcategories/media/images/category-2/thumbnail_image_vertical.jpg', 'http://localhost/fb-content/fb-subcategories/media/images/category-2/thumbnail_image_horizontal.jpg', NULL, 4, 'immunerosito-keverekek'),
(3, 2, 'Nyugtató Növények', 'Relaxáció és mentális egyensúly', 'Növények és gyógynövények, amelyek segítenek a stressz csökkentésében, és elősegítik a testi-lelki nyugalmat.', 'http://localhost/fb-content/fb-subcategories/media/images/category-3/thumbnail_image_vertical.jpg', 'http://localhost/fb-content/fb-subcategories/media/images/category-3/thumbnail_image_horizontal.jpg', NULL, 9, 'nyugtato-novenyek'),
(4, 2, 'Alvássegítők', 'A pihentető alvás titkai', 'Nyugtató hatású növények és kivonatok, amelyek javítják az alvást, segítve a pihentető, regeneráló éjszakákat.', 'http://localhost/fb-content/fb-subcategories/media/images/category-4/thumbnail_image_vertical.jpg', 'http://localhost/fb-content/fb-subcategories/media/images/category-4/thumbnail_image_horizontal.jpg', NULL, 4, 'alvassegitok'),
(5, 3, 'Májtisztító Növények', 'A méregtelenítés segítése a májban', 'Olyan gyógynövények, amelyek támogathatják a máj méregtelenítő és tisztító folyamatait.', 'http://localhost/fb-content/fb-subcategories/media/images/category-5/thumbnail_image_vertical.jpg', 'http://localhost/fb-content/fb-subcategories/media/images/category-5/thumbnail_image_horizontal.jpg', NULL, 0, 'majtisztito-novenyek'),
(6, 3, 'Lúgosító Növények', 'A sav-bázis egyensúly helyreállítása', 'Növények, amelyek elősegítik a szervezet lúgosítását, hozzájárulva a sav-bázis egyensúly fenntartásához.', 'http://localhost/fb-content/fb-subcategories/media/images/category-6/thumbnail_image_vertical.jpg', 'http://localhost/fb-content/fb-subcategories/media/images/category-6/thumbnail_image_horizontal.jpg', NULL, 0, 'lugosito-novenyek'),
(7, 4, 'Emésztést Támogatók', 'Az egészséges emésztés alapjai', 'Olyan gyógynövények, amelyek segítenek az emésztési folyamatok javításában és a bélflóra egészségének fenntartásában.', 'http://localhost/fb-content/fb-subcategories/media/images/category-7/thumbnail_image_vertical.jpg', 'http://localhost/fb-content/fb-subcategories/media/images/category-7/thumbnail_image_horizontal.jpg', NULL, 3, 'emesztest-tamogatok'),
(8, 4, 'Puffadásgátló Növények', 'A kellemetlen puffadás enyhítése', 'Növények, amelyek csökkenthetik a puffadást, segítve az emésztést és a bélműködés javítását.', 'http://localhost/fb-content/fb-subcategories/media/images/category-8/thumbnail_image_vertical.jpg', 'http://localhost/fb-content/fb-subcategories/media/images/category-8/thumbnail_image_horizontal.jpg', NULL, 2, 'puffadasgatlo-novenyek'),
(9, 5, 'Ízületi Növények', 'Az ízületek védelme és regenerálása', 'Olyan gyógynövények, amelyek segítenek az ízületek egészségének megőrzésében, enyhítve a fájdalmat és javítva a mobilitást.', 'http://localhost/fb-content/fb-subcategories/media/images/category-9/thumbnail_image_vertical.jpg', 'http://localhost/fb-content/fb-subcategories/media/images/category-9/thumbnail_image_horizontal.jpg', NULL, 1, 'izuleti-novenyek'),
(10, 5, 'Energizáló Növények', 'Az energia növelése természetes módon', 'Frissítő hatású növények, amelyek elősegítik a vitalitást és az energiaszint növelését.', 'http://localhost/fb-content/fb-subcategories/media/images/category-10/thumbnail_image_vertical.jpg', 'http://localhost/fb-content/fb-subcategories/media/images/category-10/thumbnail_image_horizontal.jpg', NULL, 11, 'energizalo-novenyek'),
(11, 6, 'Életerőt Adó Növények', 'Frissesség és vitalitás', 'A növények, amelyek erősítik a testet és az elmét, segítve a fáradtság leküzdését és a mindennapi életerő fenntartását.', 'http://localhost/fb-content/fb-subcategories/media/images/category-11/thumbnail_image_vertical.jpg', 'http://localhost/fb-content/fb-subcategories/media/images/category-11/thumbnail_image_horizontal.jpg', NULL, 0, 'eleterot-ado-novenyek'),
(12, 6, 'Stresszcsökkentő Növények', 'A nyugodt elme megteremtése', 'Nyugtató gyógynövények, amelyek segítenek csökkenteni a stresszt és elősegítik a lelki egyensúlyt.', 'http://localhost/fb-content/fb-subcategories/media/images/category-12/thumbnail_image_vertical.jpg', 'http://localhost/fb-content/fb-subcategories/media/images/category-12/thumbnail_image_horizontal.jpg', NULL, 3, 'stresszcsokkento-novenyek'),
(13, 7, 'Bőrápoló Növények', 'A bőr természetes ápolása', 'Olyan növények, amelyek hozzájárulnak a bőr hidratálásához, regenerálódásához és védelméhez.', 'http://localhost/fb-content/fb-subcategories/media/images/category-13/thumbnail_image_vertical.jpg', 'http://localhost/fb-content/fb-subcategories/media/images/category-13/thumbnail_image_horizontal.jpg', NULL, 1, 'borapolo-novenyek'),
(14, 7, 'Hajápoló Növények', 'A haj erősítése és táplálása', 'Növények, amelyek segítenek erősíteni a hajat, serkentik a növekedést és javítják annak egészségét.', 'http://localhost/fb-content/fb-subcategories/media/images/category-14/thumbnail_image_vertical.jpg', 'http://localhost/fb-content/fb-subcategories/media/images/category-14/thumbnail_image_horizontal.jpg', NULL, 0, 'hajapolo-novenyek'),
(15, 8, 'Vitalizáló Növények', 'A frissesség és életerő fokozása', 'Növények, amelyek fokozzák a vitalitást, segítenek frissíteni és revitalizálni a testet.', 'http://localhost/fb-content/fb-subcategories/media/images/category-15/thumbnail_image_vertical.jpg', 'http://localhost/fb-content/fb-subcategories/media/images/category-15/thumbnail_image_horizontal.jpg', NULL, 8, 'vitalizalo-novenyek'),
(16, 9, 'Energizáló Teák', 'Frissítő teaélmény a mindennapokhoz', 'Teák, amelyek növelik az energiát és frissítenek a nap bármely szakában.', 'http://localhost/fb-content/fb-subcategories/media/images/category-16/thumbnail_image_vertical.jpg', 'http://localhost/fb-content/fb-subcategories/media/images/category-16/thumbnail_image_horizontal.jpg', NULL, 0, 'energizalo-teak'),
(17, 9, 'Illatos Növények', 'Aromák a frissességért', 'Növényi levelek és illóolajok, amelyek segítenek a levegő frissítésében, hozzájárulva a hangulat javításához.', 'http://localhost/fb-content/fb-subcategories/media/images/category-17/thumbnail_image_vertical.jpg', 'http://localhost/fb-content/fb-subcategories/media/images/category-17/thumbnail_image_horizontal.jpg', NULL, 0, 'illatos-novenyek'),
(18, 9, 'Hűsítő Növények', 'A hűsítő hatás természetes ereje', 'Növények, amelyek hűsítő hatással vannak a testre, enyhítve a meleget és felfrissítve a bőrt.', 'http://localhost/fb-content/fb-subcategories/media/images/category-18/thumbnail_image_vertical.jpg', 'http://localhost/fb-content/fb-subcategories/media/images/category-18/thumbnail_image_horizontal.jpg', NULL, 0, 'husito-novenyek'),
(19, 10, 'Ritka Gyógynövények', 'Különleges és egyedi növények', 'Ritka, különleges gyógynövények, amelyek különleges jótékony hatásokkal bírnak a test és az elme számára.', 'http://localhost/fb-content/fb-subcategories/media/images/category-19/thumbnail_image_vertical.jpg', 'http://localhost/fb-content/fb-subcategories/media/images/category-19/thumbnail_image_horizontal.jpg', NULL, 0, 'ritka-gyogynovenyek'),
(20, 10, 'Esszenciális Kivonatok', 'Koncentrált növényi kivonatok', 'Koncentrált növényi kivonatok, amelyek gyorsan és hatékonyan nyújtanak jótékony hatásokat a testre és az elmére.', 'http://localhost/fb-content/fb-subcategories/media/images/category-20/thumbnail_image_vertical.jpg', 'http://localhost/fb-content/fb-subcategories/media/images/category-20/thumbnail_image_horizontal.jpg', NULL, 0, 'esszencialis-kivonatok'),
(21, 11, 'Tengeri Növények', 'Az óceán kincsei a bőr és a test számára', 'Tengeri növények, amelyek gazdagok ásványi anyagokban és tápanyagokban, segítve a hidratálást és a méregtelenítést.', 'http://localhost/fb-content/fb-subcategories/media/images/category-21/thumbnail_image_vertical.jpg', 'http://localhost/fb-content/fb-subcategories/media/images/category-21/thumbnail_image_horizontal.jpg', NULL, 0, 'tengeri-novenyek'),
(22, 12, 'Aromás Növények', 'Illatok a testi-lelki frissességhez', 'Aromás gyógynövények, amelyek segítenek a pihenésben és a hangulat javításában, frissítő illatokkal.', 'http://localhost/fb-content/fb-subcategories/media/images/category-22/thumbnail_image_vertical.jpg', 'http://localhost/fb-content/fb-subcategories/media/images/category-22/thumbnail_image_horizontal.jpg', NULL, 0, 'aromas-novenyek'),
(23, 12, 'Illóolajos Kivonatok', 'Természetes eredetű és jótékony hatású illóolajok', 'Illóolajok és növényi kivonatok, amelyek a lelki és fizikai jólét fenntartásában segítenek.', 'http://localhost/fb-content/fb-subcategories/media/images/category-23/thumbnail_image_vertical.jpg', 'http://localhost/fb-content/fb-subcategories/media/images/category-23/thumbnail_image_horizontal.jpg', NULL, 0, 'illoolajos-kivonatok'),
(24, 13, 'Fűszernövények', 'Az ízek és a gyógyító hatás egyensúlya', 'Konyhai fűszernövények, amelyek nemcsak az étkezéseket teszik finomabbá, hanem jótékony hatással vannak az egészségre is.', 'http://localhost/fb-content/fb-subcategories/media/images/category-24/thumbnail_image_vertical.jpg', 'http://localhost/fb-content/fb-subcategories/media/images/category-24/thumbnail_image_horizontal.jpg', NULL, 3, 'fuszernovenyek'),
(25, 13, 'Ehető Gyógynövények', 'Természetes ízek a konyhádban', 'Ezek a növények nemcsak finomak, hanem gazdagok értékes tápanyagokban is, így bármikor felhasználhatod őket az ételkészítéskor.', 'http://localhost/fb-content/fb-subcategories/media/images/category-25/thumbnail_image_vertical.jpg', 'http://localhost/fb-content/fb-subcategories/media/images/category-25/thumbnail_image_horizontal.jpg', NULL, 0, 'eheto-gyogynovenyek'),
(26, 14, 'Keringést Támogatók', ' A szív- és érrendszer egészsége', 'Növények és kivonatok, amelyek támogatják a szív- és érrendszert, javítva a vérkeringést.', 'http://localhost/fb-content/fb-subcategories/media/images/category-26/thumbnail_image_vertical.jpg', 'http://localhost/fb-content/fb-subcategories/media/images/category-26/thumbnail_image_horizontal.jpg', NULL, 2, 'keringest-tamogatok'),
(27, 14, 'Vérnyomáscsökkentő Növények', 'A szív egészségének védelme', 'Olyan növények, amelyek segítenek a vérnyomás szabályozásában, támogathatják a szív- és érrendszer működését.', 'http://localhost/fb-content/fb-subcategories/media/images/category-27/thumbnail_image_vertical.jpg', 'http://localhost/fb-content/fb-subcategories/media/images/category-27/thumbnail_image_horizontal.jpg', NULL, 1, 'vernyomascsokkento-novenyek'),
(28, 15, 'Erdei Gyógynövények', 'Jótékony és egészséges gyógynövények az erdőből', 'Olyan növények, amelyek az erdő mélyéről származnak, és jótékony hatással vannak a testre és az elmére.', 'http://localhost/fb-content/fb-subcategories/media/images/category-28/thumbnail_image_vertical.jpg', 'http://localhost/fb-content/fb-subcategories/media/images/category-28/thumbnail_image_horizontal.jpg', NULL, 1, 'erdei-gyogynovenyek'),
(29, 15, 'Erdei Kivonatok', 'Jótékony kivonatok az egészségért', 'Erdei növények kivonatai, amelyek különleges és erőteljes jótékony hatásokat kínálnak a méregtelenítéshez és regenerálódáshoz.', 'http://localhost/fb-content/fb-subcategories/media/images/category-29/thumbnail_image_vertical.jpg', 'http://localhost/fb-content/fb-subcategories/media/images/category-29/thumbnail_image_horizontal.jpg', NULL, 0, 'erdei-kivonatok'),
(30, 6, 'Aromaterápiás Esszenciák', 'Illatok, amelyek felébresztik az érzelmeket', 'Válogatott illóolajok, diffúzorok és párologtató keverékek, melyek segítségével otthonodba egy intenzív, érzelmekkel teli atmoszférát varázsolhatsz. Ezek a termékek serkentik a belső szenvedélyt és elősegítik az intimitást.', 'http://localhost/fb-content/fb-subcategories/media/images/category-30/thumbnail_image_vertical.jpeg', 'http://localhost/fb-content/fb-subcategories/media/images/category-30/thumbnail_image_horizontal.jpg', NULL, 0, 'aromaterapias-esszenciak'),
(31, 6, 'Érzéki Növénykincsek', 'A szenvedély felébresztése természetes módon', 'Ebben az alkategóriában olyan gyógynövények és természetes kivonatok találhatók, amelyek elősegítik a hormonális egyensúlyt, fokozzák az libidót és támogatják a szexuális egészséget. Válaszd a természet adta lehetőségeket, hogy a belső tűz újra lángra lob', 'http://localhost/fb-content/fb-subcategories/media/images/category-31/thumbnail_image_vertical.jpeg', 'http://localhost/fb-content/fb-subcategories/media/images/category-31/thumbnail_image_horizontal.jpg', NULL, 12, 'erzeki-novenykincsek');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `tag`
--

DROP TABLE IF EXISTS `tag`;
CREATE TABLE `tag` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `icon_uri` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;

--
-- A tábla adatainak kiíratása `tag`
--

INSERT INTO `tag` (`id`, `name`, `icon_uri`) VALUES
(1, 'BPA-mentes', 'http://localhost/fb-content/assets/media/images/tags/bpa-free.png'),
(2, 'Gluténmentes', 'http://localhost/fb-content/assets/media/images/tags/gluten-free.png'),
(3, 'Méz', 'http://localhost/fb-content/assets/media/images/tags/honeycomb.png'),
(4, 'Halmentes', 'http://localhost/fb-content/assets/media/images/tags/no-fish.png'),
(5, 'GMO-mentes', 'http://localhost/fb-content/assets/media/images/tags/non-gmo.png'),
(6, 'Mogyoró mentes', 'http://localhost/fb-content/assets/media/images/tags/peanut-free.png'),
(7, 'Pollenmentes', 'http://localhost/fb-content/assets/media/images/tags/pollen.png'),
(8, 'Tinktúra', 'http://localhost/fb-content/assets/media/images/tags/serum.png'),
(9, 'Cukormentes', 'http://localhost/fb-content/assets/media/images/tags/sugar-free.png'),
(10, 'Vegán', 'http://localhost/fb-content/assets/media/images/tags/vegan.png');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `user_name` varchar(255) DEFAULT NULL,
  `password_hash` varchar(255) DEFAULT NULL,
  `role` varchar(255) DEFAULT 'Guest',
  `cookie_id` varchar(64) DEFAULT NULL,
  `cookie_expires_at` int(11) DEFAULT NULL,
  `reset_token` varchar(64) DEFAULT NULL,
  `reset_token_expires_at` int(11) DEFAULT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `avatar_id` int(11) DEFAULT 9 COMMENT 'Profilkép',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;

--
-- A tábla adatainak kiíratása `user`
--

INSERT INTO `user` (`id`, `email`, `user_name`, `password_hash`, `role`, `cookie_id`, `cookie_expires_at`, `reset_token`, `reset_token_expires_at`, `first_name`, `last_name`, `phone`, `avatar_id`, `created_at`) VALUES
(1, '13c-blank@ipari.vein.hu', 'admin', '$2y$10$akYNgkIySgEjOb7zwRUo7utSWw3S0OGOJZ6i1gx3wQCT9hr.DGdHq', 'Administrator', NULL, NULL, NULL, NULL, 'Máté', 'Blank', '+36302166162', 7, '2024-11-02 13:27:24'),
(2, 'teszt-elek@gmail.com', 'teszt-elek', '$2y$10$.BZLWK4qrkNB7jVCWxpkyeCpo/wRGMA/7QmSb7j4MnSZc/Ez4huMa', 'Guest', NULL, NULL, NULL, NULL, 'Elek', 'Teszt', NULL, 7, '2024-11-26 17:24:56'),
(3, '13c-milkovics@ipari.vein.hu', 'Kecske', '$2y$10$VX.1kRDv2h6x0x4lqNYxMe1NWBWsPIAs1qxXA0/vk71YdCVugcCu6', 'Administrator', NULL, NULL, NULL, NULL, 'Csanád', 'Milkovics', NULL, 7, '2025-01-01 09:09:02'),
(4, 'kinga.blank@bot.example.org', 'blank-k596', '$2b$12$EVWIRPc/xvEIEKn1BQH6RuwPWGkes9PJPv1.Ke4/ILRcLdPa3S8ri', 'Bot', NULL, NULL, NULL, NULL, 'Kinga', 'Blank', NULL, 1, '2025-03-17 20:12:46'),
(5, 'agnes.szep@bot.example.net', 'szep-a6', '$2b$12$YejAJ25ZqDRSIUZtudpJEem08zUxgQMRDlE6I875whjE23DNQRlZ2', 'Bot', NULL, NULL, NULL, NULL, 'Ágnes', 'Szép', NULL, 1, '2025-03-17 20:12:46'),
(6, 'beata.kiss@bot.example.org', 'kiss-b745', '$2b$12$iPReEQ2GnHUJRMSi2wHS4.ox7ul3xK5Dk7BlWwjZeC2vWPc4VJCta', 'Bot', NULL, NULL, NULL, NULL, 'Beáta', 'Kiss', NULL, 1, '2025-03-17 20:12:46'),
(7, 'maria.deak@bot.example.com', 'deak_m961', '$2b$12$xha/xhSIYLCinUN8oKPkQefJIMxaUTJ9iWPVcwZc0a.37fAMl5vnW', 'Bot', NULL, NULL, NULL, NULL, 'Mária', 'Deák', NULL, 1, '2025-03-17 20:12:46'),
(8, 'mimiviktoria.meszaros@bot.example.com', 'meszarosm240', '$2b$12$32wJQbVoIUysVfj7j151zeSxwSKwvrdpvbFAJJeoz4BO6gIEfa.Gy', 'Bot', NULL, NULL, NULL, NULL, 'MimiViktória', 'Mészáros', NULL, 1, '2025-03-17 20:12:46'),
(9, 'flora.szalai@bot.example.org', 'szal_flo509', '$2b$12$797rai9DUEQ3VUJq5JQH6uz2ShPTeZQCPQ3qR8XABj94m4xq7sGJK', 'Bot', NULL, NULL, NULL, NULL, 'Flóra', 'Szalai', NULL, 1, '2025-03-17 20:12:46'),
(10, 'lilla.kiss@bot.example.com', 'kiss_l149', '$2b$12$a5WtikQVWbT1JleDheYUNueV0LXdCJBvLWeEthypdR4DC1cFdylE6', 'Bot', NULL, NULL, NULL, NULL, 'Lilla', 'Kiss', NULL, 1, '2025-03-17 20:15:53'),
(11, 'csilla.farkas@bot.example.com', 'c_farkas807', '$2b$12$nRRbAKw2S/3sSrxTheBwfObV3vsjNg1IJAtoFNL45SfUNDBMbcldG', 'Bot', NULL, NULL, NULL, NULL, 'Csilla', 'Farkas', NULL, 1, '2025-03-17 20:15:53'),
(12, 'lilla.barta@bot.example.org', 'l-barta511', '$2b$12$sFZpFffP8e4/PYeH/hZ5V.nAKcXMK0i1GcSuwonxZvakjJrOp3ot.', 'Bot', NULL, NULL, NULL, NULL, 'Lilla', 'Barta', NULL, 1, '2025-04-09 19:53:18'),
(13, 'hedvig.simon@bot.example.com', 'simonh317', '$2b$12$msTEO8Khh5amaxXUGYk4NOfLEHHA4QeN3vRit9v9iitG3UyK1qXFW', 'Bot', NULL, NULL, NULL, NULL, 'Hedvig', 'Simon', NULL, 1, '2025-04-09 19:53:18'),
(14, 'judit.kelemen@bot.example.org', 'j-kelemen554', '$2b$12$.n4/WRUnmXzysvtiXXesp.d0.I0.YWKlZNABBzdVoHkog3wsg/oM6', 'Bot', NULL, NULL, NULL, NULL, 'Judit', 'Kelemen', NULL, 1, '2025-04-09 19:53:19'),
(15, 'agnes.szilagyi@bot.example.org', 'a_szilagyi855', '$2b$12$aUaN/ouLa9ib7etxZwwTUOMFxVSpA.RYRE/mNNLxJMDc0t/VFrXQu', 'Bot', NULL, NULL, NULL, NULL, 'Ágnes', 'Szilágyi', NULL, 1, '2025-04-09 19:53:19'),
(16, 'emese.hegyessy@bot.example.org', 'e_hegyessy610', '$2b$12$m.eE5CJlM5DMbGjl0/5aguL3Xwm0EuU8CixgoF3.nuy7d8pj7bZM.', 'Bot', NULL, NULL, NULL, NULL, 'Emese', 'Hegyessy', NULL, 1, '2025-04-09 19:53:19'),
(17, 'erika.szoke@bot.example.com', 'szok_eri621', '$2b$12$2pAH8/HGUsG7edYgUPSbt.ChlRKKZHB7kFc0VWjvdJs9g2l3T3mIS', 'Bot', NULL, NULL, NULL, NULL, 'Erika', 'Szőke', NULL, 1, '2025-04-09 19:53:19'),
(18, 'erzsebet.szabo@bot.example.com', 'szaboe308', '$2b$12$ckVBW0p0LNgSEidzTzkdVOcKaibTuycSimQi7sKdRewlPR.Z5sbZW', 'Bot', NULL, NULL, NULL, NULL, 'Erzsébet', 'Szabó', NULL, 1, '2025-04-09 19:53:20'),
(19, 'lilla.veres@bot.example.org', 'l_veres139', '$2b$12$QYVldc0bGArhRHCxRPCCM.6zncCOqop7.8GVED7l5ytRcG7iPLTxO', 'Bot', NULL, NULL, NULL, NULL, 'Lilla', 'Veres', NULL, 1, '2025-04-09 19:53:20'),
(20, 'anna.simon@bot.example.com', 'a-simon308', '$2b$12$IK/XuweWNSAil6hdKy021OgD0Q22VgovHp5DpUnShvnsdxmfCL/tG', 'Bot', NULL, NULL, NULL, NULL, 'Anna', 'Simon', NULL, 1, '2025-04-09 19:53:20'),
(21, 'melinda.kovacs@bot.example.net', 'kovacs-m810', '$2b$12$EW9p7QZ1ERpU8PIJyC8hX.rp6nK09vHbmZQao0g3lvz1x.l49hGai', 'Bot', NULL, NULL, NULL, NULL, 'Melinda', 'Kovács', NULL, 1, '2025-04-09 19:53:20'),
(22, 'zsuzsanna.voros@bot.example.org', 'voro_zsu69', '$2b$12$cC8jM5fu4KMZGIR2yUSkeuD0qUlKO.YnsJyX4i8QfRFZE6ubPfW5a', 'Bot', NULL, NULL, NULL, NULL, 'Zsuzsanna', 'Vörös', NULL, 1, '2025-04-09 19:53:20'),
(23, 'zsofia.bognar@bot.example.net', 'z_bognar660', '$2b$12$LqUcp5hFfVY8hH62UH4l8ekCsd0nn2VA0FBf2h5h8fJo3p9hpC/OG', 'Bot', NULL, NULL, NULL, NULL, 'Zsófia', 'Bognár', NULL, 1, '2025-04-09 19:53:20'),
(24, 'nikolett.juhasz@bot.example.com', 'juhasz-n995', '$2b$12$52GhjDH.0QtzVMOGQYTfWe7agspTW9lluj66jlRLMAOamLSQxpaW6', 'Bot', NULL, NULL, NULL, NULL, 'Nikolett', 'Juhász', NULL, 1, '2025-04-09 19:53:21'),
(25, 'eszter.szep@bot.example.com', 'szep_e40', '$2b$12$g37EwE0xdurwl.4X/FUuU.JPPofmgWDCP8eMaWUaBhV9h03jdcE1S', 'Bot', NULL, NULL, NULL, NULL, 'Eszter', 'Szép', NULL, 1, '2025-04-09 19:53:21'),
(26, 'orsolya.rozsa@bot.example.com', 'o-rozsa119', '$2b$12$B2T4X8xXK6LKvX.ojUvqzuO5onsffXV8LnFSFM.aSVGVcjKeMfhBa', 'Bot', NULL, NULL, NULL, NULL, 'Orsolya', 'Rózsa', NULL, 1, '2025-04-09 19:53:21'),
(27, 'edina.lakatos@bot.example.com', 'lakatos_e174', '$2b$12$hHHUXu3DFmHTE3IZ4Pquh.q3gJnVhPgYtKzEObZZEqWO9VwPeTaKK', 'Bot', NULL, NULL, NULL, NULL, 'Edina', 'Lakatos', NULL, 1, '2025-04-09 19:53:21'),
(28, 'andrea.molnar@bot.example.com', 'a-molnar429', '$2b$12$9kzqNAa60bi0pafq/Mr3K.J8l/jAU2a.oIVDLqdru8f8WmD9AnAQy', 'Bot', NULL, NULL, NULL, NULL, 'Andrea', 'Molnár', NULL, 1, '2025-04-09 19:53:21'),
(29, 'noemi.szoke@bot.example.net', 'szoke-n681', '$2b$12$LoAJZp2eyq/dco8zRah0y.KKmqqHqMK98SyTHc1UtQIKCNTfDaYWS', 'Bot', NULL, NULL, NULL, NULL, 'Noémi', 'Szőke', NULL, 1, '2025-04-09 19:53:22'),
(30, 'katalin.szilagyi@bot.example.org', 'k_szilagyi382', '$2b$12$ZfAsmDV.V5t6LzEpsehYb.jiiGDUAslpRC9RFk.NqoinTP7Ccntqq', 'Bot', NULL, NULL, NULL, NULL, 'Katalin', 'Szilágyi', NULL, 1, '2025-04-09 19:53:22'),
(31, 'csillag.vass@bot.example.net', 'vassc791', '$2b$12$tJFFrlKR6ff9stbMk4.DVu7Y/w7Vkcrg24rx/p3ZWwY/SbtYUzebe', 'Bot', NULL, NULL, NULL, NULL, 'Csillag', 'Vass', NULL, 1, '2025-04-09 19:53:22'),
(32, 'katalin.barta@bot.example.org', 'barta_k940', '$2b$12$qO31GL33pdzSRjubDbPN2uMOpcH9oopdm.D1mud9LtNWndH3R0G0C', 'Bot', NULL, NULL, NULL, NULL, 'Katalin', 'Barta', NULL, 1, '2025-04-09 19:53:22'),
(33, 'rita.farkas@bot.example.net', 'r_farkas703', '$2b$12$DDqKh60g1ENS/qPwYBMHJOPyelU6LwMroZv8GRQ.E6G/FgGDXgufC', 'Bot', NULL, NULL, NULL, NULL, 'Rita', 'Farkas', NULL, 1, '2025-04-09 19:53:23'),
(34, 'maria.balogh@bot.example.com', 'baloghm453', '$2b$12$zvE7c8G8Ix.9OUUXxOJKPON85r2HKP0JsFGHp.5VoiK4MfzPMZLXm', 'Bot', NULL, NULL, NULL, NULL, 'Mária', 'Balogh', NULL, 1, '2025-04-09 19:53:23'),
(35, 'klara.varga@bot.example.net', 'k_varga341', '$2b$12$sbYQwuuhqGsOUqFT0VPBz.ttZXHgy7r8Ps0MmrBkwtgU.T6E947r6', 'Bot', NULL, NULL, NULL, NULL, 'Klára', 'Varga', NULL, 1, '2025-04-09 19:53:23'),
(36, 'aniko.racz@bot.example.com', 'a_racz789', '$2b$12$Kh2t0RfctzRyEHaQGJ5FRegQuVmNqc0KzZcpPKifHCcJf451X7BFy', 'Bot', NULL, NULL, NULL, NULL, 'Anikó', 'Rácz', NULL, 1, '2025-04-09 19:53:23'),
(37, 'margit.nagy@bot.example.org', 'nagy-m826', '$2b$12$eV5R07zcYQhtVXMX/EbENunMm5iPCkc3fY7Zbufm0wE6j9JPZwG1W', 'Bot', NULL, NULL, NULL, NULL, 'Margit', 'Nagy', NULL, 1, '2025-04-09 19:53:24'),
(38, 'flora.barta@bot.example.net', 'barta-f677', '$2b$12$s6VMQoo/8OpCfEmkKyVXs.vBQKadr4JHZuofk7VMIdLQy1/5xR3ki', 'Bot', NULL, NULL, NULL, NULL, 'Flóra', 'Barta', NULL, 1, '2025-04-09 19:53:24'),
(39, 'csilla.kovacs@bot.example.org', 'kovacsc583', '$2b$12$qPoKwqxgIn/ldcrYGJOhd.8SCKGyi57k.KAdI3aUhYt4ztqP2CCcS', 'Bot', NULL, NULL, NULL, NULL, 'Csilla', 'Kovács', NULL, 1, '2025-04-09 19:53:24'),
(40, 'erika.nemes@bot.example.com', 'nemes_e718', '$2b$12$sdoqeJSSHxFURWvr4rsWnO7UQ6HMxkFMFE2K3Wc5Pl9Jxl/KSQUs6', 'Bot', NULL, NULL, NULL, NULL, 'Erika', 'Nemes', NULL, 1, '2025-04-09 19:53:24'),
(41, 'nikolett.hegedus@bot.example.org', 'hegedus-n550', '$2b$12$qmJiubqZ1xwvzzbcnH.X4ue3fLpY9eJfeC.zR4Wpgx8obCpNUh9Se', 'Bot', NULL, NULL, NULL, NULL, 'Nikolett', 'Hegedűs', NULL, 1, '2025-04-09 19:53:24'),
(42, 'reka.takacs@bot.example.net', 'r-takacs339', '$2b$12$390CLkZk8HwwNhqgP7C/M.Ysd/1bvpbu1qTUrfFtBsHIIObLAAb4e', 'Bot', NULL, NULL, NULL, NULL, 'Réka', 'Takács', NULL, 1, '2025-04-09 19:53:24'),
(43, 'krisztina.szep@bot.example.net', 'k_szep450', '$2b$12$WdC6catkO7wpne5LrJAw7ebtKDlS/c3iuaN/Hxb5Xd2TBE7zVE8P.', 'Bot', NULL, NULL, NULL, NULL, 'Krisztina', 'Szép', NULL, 1, '2025-04-09 19:53:25'),
(44, 'bernadett.toth@bot.example.net', 'toth-b348', '$2b$12$2MYqi0ZyVhpUaZA57QcnDeQj5mVPzXbV3qs3QBVKFgAegwfLjoD1C', 'Bot', NULL, NULL, NULL, NULL, 'Bernadett', 'Tóth', NULL, 1, '2025-04-09 19:53:25'),
(45, 'agnes.farkas@bot.example.org', 'a-farkas758', '$2b$12$V2iJKgFDXng4O2yWx7WlKOmmEQ1wcPfzzGxvW9NTURTh2KvKpMh.6', 'Bot', NULL, NULL, NULL, NULL, 'Ágnes', 'Farkas', NULL, 1, '2025-04-09 19:53:25'),
(46, 'adrienn.kecske@bot.example.org', 'kecske-a903', '$2b$12$svyOfS6aEP/Y.SoeTh2WTunvcAlj3Xak6cmpjBl4cJk/XBep4APdG', 'Bot', NULL, NULL, NULL, NULL, 'Adrienn', 'Kecske', NULL, 1, '2025-04-09 19:53:25'),
(47, 'mimiviktoria.szoke@bot.example.org', 'szoke_m478', '$2b$12$zfnIawsUCF5StOhEhfmcI.8Xh35eTzlixgl4ZoktTas.8.oElElZW', 'Bot', NULL, NULL, NULL, NULL, 'MimiViktória', 'Szőke', NULL, 1, '2025-04-09 19:53:26'),
(48, 'anna.fazekas@bot.example.org', 'fazekas_a798', '$2b$12$FIN0vncY86IdjSFDxnGWNOkidesfARWfYOPvKzSEv4HThkw7Iixh6', 'Bot', NULL, NULL, NULL, NULL, 'Anna', 'Fazekas', NULL, 1, '2025-04-09 19:53:26'),
(49, 'rozalia.racz@bot.example.net', 'r-racz491', '$2b$12$8Qlq8f6WQxlCiHd1/C1vJOUpIIZ/9/rRSY0TWE0pbA2lPZAbRI/Jy', 'Bot', NULL, NULL, NULL, NULL, 'Rozália', 'Rácz', NULL, 1, '2025-04-09 19:53:26'),
(50, 'eszter.fazekas@bot.example.com', 'fazekas-e531', '$2b$12$hYGAgW74i6eRfANh.3ByZe7UCeLOUrs9fCWCcfBMD5Spu/MZDJpee', 'Bot', NULL, NULL, NULL, NULL, 'Eszter', 'Fazekas', NULL, 1, '2025-04-09 19:53:26'),
(51, 'agnes.papp@bot.example.com', 'a-papp911', '$2b$12$MdFRZfgR.lvm.6gAY3arQO7sT7ZoW/xPcjZIh15jG1lVPb3Ow8ui.', 'Bot', NULL, NULL, NULL, NULL, 'Ágnes', 'Papp', NULL, 1, '2025-04-09 19:53:27'),
(52, 'erzsebet.szoke@bot.example.com', 'szoke-e527', '$2b$12$3sf8OU3jYrYgVkYPLscLt.9s6A5SmU.HsQ4XK7w0tKWKwh5XCe5iW', 'Bot', NULL, NULL, NULL, NULL, 'Erzsébet', 'Szőke', NULL, 1, '2025-04-09 19:53:27'),
(53, 'katalin.szalai@bot.example.org', 'k-szalai607', '$2b$12$lJzPTCv2U/2ksxVJKY./3.Z30tuf3R8P18OGqyjmO11F62bQnM0hK', 'Bot', NULL, NULL, NULL, NULL, 'Katalin', 'Szalai', NULL, 1, '2025-04-09 19:53:27'),
(54, 'iren.racz@bot.example.org', 'i-racz974', '$2b$12$RehosayXWR4aSTNNY5ZPaOBRq7UCq89drS64Cmti1HIRu0OJ3meIa', 'Bot', NULL, NULL, NULL, NULL, 'Irén', 'Rácz', NULL, 1, '2025-04-09 19:53:27'),
(55, 'edina.kovacs@bot.example.com', 'e-kovacs83', '$2b$12$CaMVr5z/u5Uktu1WtflQKO/DeQrJ1rc5A8CHqwq2s3l7zV994bwMG', 'Bot', NULL, NULL, NULL, NULL, 'Edina', 'Kovács', NULL, 1, '2025-04-09 19:53:28'),
(56, 'katalin.fulop@bot.example.org', 'k_fulop958', '$2b$12$Zhisj..IEHv/6BwPtuQlIu14zGIXudi2BpuIFjXmRPPqDMPvIBEou', 'Bot', NULL, NULL, NULL, NULL, 'Katalin', 'Fülöp', NULL, 1, '2025-04-09 19:53:28'),
(57, 'edina.lukacs@bot.example.com', 'lukacs_e405', '$2b$12$871V70aOzzUY0AL0HnWmjuBOSwY46ohtPBTb.6JrWPv2iq82Ke29i', 'Bot', NULL, NULL, NULL, NULL, 'Edina', 'Lukács', NULL, 1, '2025-04-09 19:53:28'),
(58, 'eszter.racz@bot.example.net', 'e_racz17', '$2b$12$D/AUeBCyc50EQz4RWaI.yOXPwkVlKV6MKqRJPChcIaunQWH.1Lquq', 'Bot', NULL, NULL, NULL, NULL, 'Eszter', 'Rácz', NULL, 1, '2025-04-09 19:53:28'),
(59, 'adrienn.szucs@bot.example.org', 'a-szucs541', '$2b$12$xttIG8kgnymJ/5Kn/en0KenfpeBwyDEEN.MLh5qjJo7rduqLMi1qe', 'Bot', NULL, NULL, NULL, NULL, 'Adrienn', 'Szűcs', NULL, 1, '2025-04-09 19:53:29'),
(60, 'eszter.mezei@bot.example.com', 'e-mezei537', '$2b$12$UOMzZwMBQXiY5tWSvQBE3udlEbiRg3aOjs38KnTpV.jMJ.YM26Pam', 'Bot', NULL, NULL, NULL, NULL, 'Eszter', 'Mezei', NULL, 1, '2025-04-09 19:53:29'),
(61, 'iren.balogh@bot.example.net', 'balogh_i488', '$2b$12$Q28uocgjs89FuNPt4YXfx.OlM5i5x1hWi3E1Fml/UQ6QQH0qAaTBK', 'Bot', NULL, NULL, NULL, NULL, 'Irén', 'Balogh', NULL, 1, '2025-04-09 19:53:29'),
(62, 'szilvia.bodnar@bot.example.net', 'bodnars256', '$2b$12$rrT/wFDur5Ocf2VItqfKDO3BHFj/q8X0rWxVSP4067hPpooaQZXFi', 'Bot', NULL, NULL, NULL, NULL, 'Szilvia', 'Bodnár', NULL, 1, '2025-04-09 19:53:29'),
(63, 'zita.hegyessy@bot.example.com', 'hegyessy-z56', '$2b$12$FxCuPl2fZYiBS7XujTxj7OcxFHlSplOBe6KXtF1/knfLiarF/yHSC', 'Bot', NULL, NULL, NULL, NULL, 'Zita', 'Hegyessy', NULL, 1, '2025-04-09 19:53:30'),
(64, 'barbara.balint@bot.example.com', 'b-balint57', '$2b$12$3xaSHNa1PC3akuHqvjBvV.RBHMBFhTwz1eIxs7nlWtH4c9lw9GA7S', 'Bot', NULL, NULL, NULL, NULL, 'Barbara', 'Bálint', NULL, 1, '2025-04-09 19:53:30'),
(65, 'katalin.toth@bot.example.org', 'toth_k883', '$2b$12$B5jrhf5OZknQQEVp49501u1FlTXGoyZhXf0Y1N5eCoOYLuWuh3o22', 'Bot', NULL, NULL, NULL, NULL, 'Katalin', 'Tóth', NULL, 1, '2025-04-09 19:53:30'),
(66, 'agnes.fulop@bot.example.org', 'fulop_a556', '$2b$12$NY0UZn.bacb1zdBGGC1RBOuYiuAAqioW4Sw0hODMMxnptxO.8RfWm', 'Bot', NULL, NULL, NULL, NULL, 'Ágnes', 'Fülöp', NULL, 1, '2025-04-09 19:53:30'),
(67, 'maria.szoke@bot.example.com', 'szokem937', '$2b$12$kof84.dwWsqHX3oA6eejqeQrb9nxKckun/nWm6IUqaEKVC.4v/Zm6', 'Bot', NULL, NULL, NULL, NULL, 'Mária', 'Szőke', NULL, 1, '2025-04-09 19:53:30'),
(68, 'adrienn.fulop@bot.example.net', 'fulopa94', '$2b$12$jgU0HQIfb5GzelHiAHokDuIsqxSKrw0kXfFx3sCWriovSIvIegR76', 'Bot', NULL, NULL, NULL, NULL, 'Adrienn', 'Fülöp', NULL, 1, '2025-04-09 19:53:30'),
(69, 'noemi.horvath@bot.example.com', 'horvath-n82', '$2b$12$SPZQh1SYpec/Sp0Dg8Y5seEV8tG21HzGQZO.8cAyu6Ygns.1g7Hri', 'Bot', NULL, NULL, NULL, NULL, 'Noémi', 'Horváth', NULL, 1, '2025-04-09 19:53:31'),
(70, 'zsuzsanna.balint@bot.example.com', 'balint-z765', '$2b$12$Xhy/CKdpw0NO43GqCSwlgeoDb.OLI8YD6K8qzOdXWoFA/fWX9N.H.', 'Bot', NULL, NULL, NULL, NULL, 'Zsuzsanna', 'Bálint', NULL, 1, '2025-04-09 19:53:31'),
(71, 'judit.kiss@bot.example.net', 'kiss_jud438', '$2b$12$qssgmhlVzjBjhUpV916kVu6tmRrZOp8lwVOkdepl5mMmleIpYEyNC', 'Bot', NULL, NULL, NULL, NULL, 'Judit', 'Kiss', NULL, 1, '2025-04-09 19:53:31'),
(72, 'flora.hegyessy@bot.example.org', 'f_hegyessy842', '$2b$12$XHfeEo4bq8gSbmf5gyd0oO7PLGb3uLE1TByGB4z1zMzLecoixmj4.', 'Bot', NULL, NULL, NULL, NULL, 'Flóra', 'Hegyessy', NULL, 1, '2025-04-09 19:53:31'),
(73, 'piroska.voros@bot.example.com', 'vorosp751', '$2b$12$6p3C0h.Wob55PFsUaFNfOuan9cZeXc292dQ8ziXYOjOiDbt/wBFju', 'Bot', NULL, NULL, NULL, NULL, 'Piroska', 'Vörös', NULL, 1, '2025-04-09 19:53:32'),
(74, 'bernadett.kukso@bot.example.net', 'b-kukso756', '$2b$12$DNecrgsipkzgNNjslCVSLeki1Jek6JBdOa56cAOOZpbTiG2RHvlSa', 'Bot', NULL, NULL, NULL, NULL, 'Bernadett', 'Kuksó', NULL, 1, '2025-04-09 19:53:32'),
(75, 'jazmin.peter@bot.example.net', 'j_peter643', '$2b$12$VW9IeWOmynZ49ntzSxv2ZO0veb3kyR.VwJLloc5V0G7X6Jm.Zmeem', 'Bot', NULL, NULL, NULL, NULL, 'Jázmin', 'Péter', NULL, 1, '2025-04-09 19:53:32'),
(76, 'csillag.lakatos@bot.example.org', 'laka_csi844', '$2b$12$7udqjm0Zj.jSxMkE.ysOEu6CT2co.bU7bT4RoVPd5e0/vGKBjMNza', 'Bot', NULL, NULL, NULL, NULL, 'Csillag', 'Lakatos', NULL, 1, '2025-04-09 19:53:32'),
(77, 'rozalia.fulop@bot.example.net', 'r-fulop345', '$2b$12$.YEoX9EWf99BmA1NR0ujP.3SvyGt1J2mpqipvmeHUBtkzfde0xgEK', 'Bot', NULL, NULL, NULL, NULL, 'Rozália', 'Fülöp', NULL, 1, '2025-04-09 19:53:32'),
(78, 'beata.vass@bot.example.net', 'vassb476', '$2b$12$FIkKKtR00nml5bHX9UAPYOE/7VOIWn3H9wyb.y6QGuJK1dZM79zX.', 'Bot', NULL, NULL, NULL, NULL, 'Beáta', 'Vass', NULL, 1, '2025-04-09 19:53:32'),
(79, 'tunde.nemeth@bot.example.net', 'nemeth-t178', '$2b$12$p22SBUr6QYa/bkmDuH4DHu.9VqirJo6AGWWw2BXlKQ0zt2/rZdBoG', 'Bot', NULL, NULL, NULL, NULL, 'Tünde', 'Németh', NULL, 1, '2025-04-09 19:53:33'),
(80, 'zsofia.papp@bot.example.org', 'z_papp205', '$2b$12$WH3P6oTzsWoAoiQmOEFUHOdJ/.qhFURHy3JXc7u9edsnQz044orlq', 'Bot', NULL, NULL, NULL, NULL, 'Zsófia', 'Papp', NULL, 1, '2025-04-09 19:53:33'),
(81, 'krisztina.toth@bot.example.net', 'k_toth847', '$2b$12$kMwzx4icrJ17x4R0se2kk.4cubxo5f2Cj6JOO9WO9wu7gQYSU85vG', 'Bot', NULL, NULL, NULL, NULL, 'Krisztina', 'Tóth', NULL, 1, '2025-04-09 19:53:33'),
(82, 'nikolett.kukso@bot.example.org', 'kuks_nik231', '$2b$12$8qkSys4pZTF4bAFga3Q3lOA8cfvDQgpER1IHS/v/O0h3mv7OqWPmO', 'Bot', NULL, NULL, NULL, NULL, 'Nikolett', 'Kuksó', NULL, 1, '2025-04-09 19:53:33'),
(83, 'lilla.szoke@bot.example.net', 'szok_lil666', '$2b$12$A9UIVJkxZlifVRXU1ArZMeqwxJ/8oa6j5ZeU9uD13HkhVnGb3Kf6i', 'Bot', NULL, NULL, NULL, NULL, 'Lilla', 'Szőke', NULL, 1, '2025-04-09 19:53:34'),
(84, 'mimiviktoria.racz@bot.example.net', 'raczm739', '$2b$12$w2T31dz2NlqNlTN9qUKN6Oo7a8md0zBNPYCSxx0Mo3Pma3Efs7aFK', 'Bot', NULL, NULL, NULL, NULL, 'MimiViktória', 'Rácz', NULL, 1, '2025-04-09 19:53:34'),
(85, 'rozalia.molnar@bot.example.com', 'molnarr498', '$2b$12$uYTqp1gYJi9yh/kk7TXI2OgpSV2UCGGtORb0W9RO61Pp5pASykJJ6', 'Bot', NULL, NULL, NULL, NULL, 'Rozália', 'Molnár', NULL, 1, '2025-04-09 19:53:34'),
(86, 'gabriella.fulop@bot.example.net', 'g-fulop443', '$2b$12$x0GNZB.OqwvLiGZVaFriFuOmzqTreES4NoemE4DYTGmQOpgE5h3vq', 'Bot', NULL, NULL, NULL, NULL, 'Gabriella', 'Fülöp', NULL, 1, '2025-04-09 19:53:34'),
(87, 'lilla.szabo@bot.example.org', 'szab_lil982', '$2b$12$Dv10xlX9YofCN9.y2TG.YOVaM3FlDRz4fihEwruUzAviUYcLv.qTS', 'Bot', NULL, NULL, NULL, NULL, 'Lilla', 'Szabó', NULL, 1, '2025-04-09 19:53:35'),
(88, 'noemi.zsiga@bot.example.com', 'zsiga-n818', '$2b$12$PfdRpGnz1ONaQreh7dDx../8s6.WCc3YvW83Y/254tWj.BOLRptmy', 'Bot', NULL, NULL, NULL, NULL, 'Noémi', 'Zsiga', NULL, 1, '2025-04-09 19:53:35'),
(89, 'aniko.zsiga@bot.example.org', 'zsiga_a326', '$2b$12$i94KYyrm/dJsc846ILmiOesQb3Vm2dBAPAfm4lDFDARK4.E/6rUeW', 'Bot', NULL, NULL, NULL, NULL, 'Anikó', 'Zsiga', NULL, 1, '2025-04-09 19:53:35'),
(90, 'boglarka.zsiga@bot.example.org', 'zsigab16', '$2b$12$jRYT3EYQl1FngWKBPDxRXes13elS7qw55hNrEPsmsADAVVVJMyCKq', 'Bot', NULL, NULL, NULL, NULL, 'Boglárka', 'Zsiga', NULL, 1, '2025-04-09 19:53:35'),
(91, 'noemi.fazekas@bot.example.net', 'n-fazekas881', '$2b$12$5I1qnQ6wgeZZPHgROFmWHu.P3LZDZdX4Yf05at9/CxgG30F.riG0y', 'Bot', NULL, NULL, NULL, NULL, 'Noémi', 'Fazekas', NULL, 1, '2025-04-09 19:53:35'),
(92, 'rozalia.soos@bot.example.net', 'soos_r843', '$2b$12$qCOhvFQqvW2WK/4gW3.E9OIw4TuDyv81o/yZfiUQeOpLfeegjufkS', 'Bot', NULL, NULL, NULL, NULL, 'Rozália', 'Soós', NULL, 1, '2025-04-09 19:53:35'),
(93, 'hajnalka.horvath@bot.example.net', 'h_horvath666', '$2b$12$isT3xM5khN7yejZx9K0.sOm/72c5/cz5xaRSCFq.vN.VDLe5t65RW', 'Bot', NULL, NULL, NULL, NULL, 'Hajnalka', 'Horváth', NULL, 1, '2025-04-09 19:53:36'),
(94, 'ilona.juhasz@bot.example.net', 'juhaszi648', '$2b$12$m4BmUwIq4Zo/xuUa0NBTHuh0CLeRN0USTbE0.lOrf4VHtGlsz0dw2', 'Bot', NULL, NULL, NULL, NULL, 'Ilona', 'Juhász', NULL, 1, '2025-04-09 19:53:36'),
(95, 'tunde.fazekas@bot.example.org', 'fazekas-t87', '$2b$12$Z1lSc2Wef7bSKXI4LzS7y.j6YQNh7KRw974BsMk0fxmjJM4RuFH1S', 'Bot', NULL, NULL, NULL, NULL, 'Tünde', 'Fazekas', NULL, 1, '2025-04-09 19:53:36'),
(96, 'mimiviktoria.balint@bot.example.org', 'balintm589', '$2b$12$n3pH974p/7Vbji2INHF9heLralnfu8cW99afGkKxv75RWiIUKxSY2', 'Bot', NULL, NULL, NULL, NULL, 'MimiViktória', 'Bálint', NULL, 1, '2025-04-09 19:53:36'),
(97, 'hajnalka.zsiga@bot.example.net', 'zsiga_h614', '$2b$12$VfjxMp6/AZBxKkSEP3SZSeo94rcwyARB0ZDyaPLWmP31LSgGn1Wkm', 'Bot', NULL, NULL, NULL, NULL, 'Hajnalka', 'Zsiga', NULL, 1, '2025-04-09 19:53:37'),
(98, 'edina.soos@bot.example.com', 'soos_e372', '$2b$12$KBhJ2Cpb51sdq7Tb0j9Bheqn1OBVJmVlwe0bEXM727.Xx3qRGJSEa', 'Bot', NULL, NULL, NULL, NULL, 'Edina', 'Soós', NULL, 1, '2025-04-09 19:53:37'),
(99, 'ilona.farkas@bot.example.com', 'farkasi730', '$2b$12$WnxRwpzr1Ja7V4MVDyojDeEMopl4yQGmROrcxrn9yuC6lVvxeq/US', 'Bot', NULL, NULL, NULL, NULL, 'Ilona', 'Farkas', NULL, 1, '2025-04-09 19:53:37'),
(100, 'csilla.nemeth@bot.example.org', 'neme_csi973', '$2b$12$IXfbx3gqZJohIfSyDKBqSO2J5XdsuECWjpCs69uGeJWA8nLvIJFj6', 'Bot', NULL, NULL, NULL, NULL, 'Csilla', 'Németh', NULL, 1, '2025-04-09 19:53:37'),
(101, 'erika.voros@bot.example.com', 'e-voros392', '$2b$12$.k9IbXGSL3hBI4FQ4mCkVeeK8rMThQalVwMsq6nUvdPsKvUXhD6xq', 'Bot', NULL, NULL, NULL, NULL, 'Erika', 'Vörös', NULL, 1, '2025-04-09 19:53:37'),
(102, 'eszter.horvath@bot.example.org', 'e_horvath297', '$2b$12$H7SOjEPAE2Tj4KuA6PYBuuwA5fGLUeONfroH2/XocAkW5n8/ZTt.C', 'Bot', NULL, NULL, NULL, NULL, 'Eszter', 'Horváth', NULL, 1, '2025-04-09 19:53:37'),
(103, 'agnes.bognar@bot.example.com', 'a_bognar89', '$2b$12$fWPD45SWVogVkkCRgV9XGO/66ozHoIs7lfXExH.1kgUKSILO1NImS', 'Bot', NULL, NULL, NULL, NULL, 'Ágnes', 'Bognár', NULL, 1, '2025-04-09 19:53:38'),
(104, 'ilona.veres@bot.example.net', 'veres-i751', '$2b$12$Me/6wnipZKy4TJlz2MIGQ.M.yvZ54V2hTgYjjHXRhVLHBkntOuATa', 'Bot', NULL, NULL, NULL, NULL, 'Ilona', 'Veres', NULL, 1, '2025-04-09 19:53:38'),
(105, 'csillag.hegyessy@bot.example.org', 'hegy_csi616', '$2b$12$YsEg0j3pkZis65AfJFU.yeq0EBnU3DbEMackUalBgWn1sqvLDWN/m', 'Bot', NULL, NULL, NULL, NULL, 'Csillag', 'Hegyessy', NULL, 1, '2025-04-09 19:53:38'),
(106, 'flora.nemeth@bot.example.com', 'nemeth_f933', '$2b$12$voLiIOcLfNDV0EKyB6B4J.tz39emYptHJ.15bIuZ6KjB1e.FuL4G.', 'Bot', NULL, NULL, NULL, NULL, 'Flóra', 'Németh', NULL, 1, '2025-04-09 19:53:38'),
(107, 'agnes.rozsa@bot.example.net', 'rozs_agn947', '$2b$12$znftTvqdJBAFH4c2pDhwzOprVQth77MudFbOPO8rker.PTubNJK4O', 'Bot', NULL, NULL, NULL, NULL, 'Ágnes', 'Rózsa', NULL, 1, '2025-04-09 19:53:39'),
(108, 'iren.simon@bot.example.net', 'simon-i3', '$2b$12$xYDj9wuiF21IJTDR9CarFeo.5CsmhcFL57lP94nv503yxzWpVTEem', 'Bot', NULL, NULL, NULL, NULL, 'Irén', 'Simon', NULL, 1, '2025-04-09 19:53:39'),
(109, 'adrienn.takacs@bot.example.com', 'takacs_a928', '$2b$12$qdjemjAqUmZfyEW62z3PSOyXGAxCUczzZezudGprClkrM5zFjg64S', 'Bot', NULL, NULL, NULL, NULL, 'Adrienn', 'Takács', NULL, 1, '2025-04-09 19:53:39'),
(110, 'flora.peter@bot.example.net', 'peter_f658', '$2b$12$HuzixbWXutR03JuJvUgFeOy95mNr.u9RvqczAMVPGMCBMbVXz9VKu', 'Bot', NULL, NULL, NULL, NULL, 'Flóra', 'Péter', NULL, 1, '2025-04-09 19:53:39');

--
-- Indexek a kiírt táblákhoz
--

--
-- A tábla indexei `autofill_billing`
--
ALTER TABLE `autofill_billing`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- A tábla indexei `autofill_delivery`
--
ALTER TABLE `autofill_delivery`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- A tábla indexei `avatar`
--
ALTER TABLE `avatar`
  ADD PRIMARY KEY (`id`);

--
-- A tábla indexei `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_product` (`user_id`,`product_id`),
  ADD KEY `cart_ibfk_2` (`product_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `page_id` (`page_id`);

--
-- A tábla indexei `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`);

--
-- A tábla indexei `health_effect`
--
ALTER TABLE `health_effect`
  ADD PRIMARY KEY (`id`);

--
-- A tábla indexei `image`
--
ALTER TABLE `image`
  ADD PRIMARY KEY (`id`);

--
-- A tábla indexei `order`
--
ALTER TABLE `order`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_ibfk_1` (`user_id`);

--
-- A tábla indexei `order_item`
--
ALTER TABLE `order_item`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- A tábla indexei `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`id`);

--
-- A tábla indexei `product_health_effect`
--
ALTER TABLE `product_health_effect`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `health_effect_id` (`health_effect_id`);

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
  ADD UNIQUE KEY `link_slug` (`link_slug`),
  ADD KEY `product_page_ibfk_1` (`product_id`),
  ADD KEY `product_page_ibfk_2` (`category_id`),
  ADD KEY `product_page_ibfk_3` (`subcategory_id`);

--
-- A tábla indexei `product_tag`
--
ALTER TABLE `product_tag`
  ADD PRIMARY KEY (`id`),
  ADD KEY `allergy_id` (`tag_id`),
  ADD KEY `product_id` (`product_id`);

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
-- A tábla indexei `tag`
--
ALTER TABLE `tag`
  ADD PRIMARY KEY (`id`);

--
-- A tábla indexei `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `user_name` (`user_name`),
  ADD KEY `avatar_id` (`avatar_id`);

--
-- A kiírt táblák AUTO_INCREMENT értéke
--

--
-- AUTO_INCREMENT a táblához `autofill_billing`
--
ALTER TABLE `autofill_billing`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT a táblához `autofill_delivery`
--
ALTER TABLE `autofill_delivery`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT a táblához `avatar`
--
ALTER TABLE `avatar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT a táblához `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=181;

--
-- AUTO_INCREMENT a táblához `category`
--
ALTER TABLE `category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT a táblához `health_effect`
--
ALTER TABLE `health_effect`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT a táblához `image`
--
ALTER TABLE `image`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=108;

--
-- AUTO_INCREMENT a táblához `order`
--
ALTER TABLE `order`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=163451;

--
-- AUTO_INCREMENT a táblához `order_item`
--
ALTER TABLE `order_item`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=195;

--
-- AUTO_INCREMENT a táblához `product`
--
ALTER TABLE `product`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT a táblához `product_health_effect`
--
ALTER TABLE `product_health_effect`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=315;

--
-- AUTO_INCREMENT a táblához `product_image`
--
ALTER TABLE `product_image`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=108;

--
-- AUTO_INCREMENT a táblához `product_page`
--
ALTER TABLE `product_page`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT a táblához `product_tag`
--
ALTER TABLE `product_tag`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=295;

--
-- AUTO_INCREMENT a táblához `review`
--
ALTER TABLE `review`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=92;

--
-- AUTO_INCREMENT a táblához `subcategory`
--
ALTER TABLE `subcategory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT a táblához `tag`
--
ALTER TABLE `tag`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT a táblához `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=111;

--
-- Megkötések a kiírt táblákhoz
--

--
-- Megkötések a táblához `autofill_billing`
--
ALTER TABLE `autofill_billing`
  ADD CONSTRAINT `autofill_billing_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Megkötések a táblához `autofill_delivery`
--
ALTER TABLE `autofill_delivery`
  ADD CONSTRAINT `autofill_delivery_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Megkötések a táblához `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cart_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cart_ibfk_4` FOREIGN KEY (`page_id`) REFERENCES `product_page` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Megkötések a táblához `order`
--
ALTER TABLE `order`
  ADD CONSTRAINT `order_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Megkötések a táblához `order_item`
--
ALTER TABLE `order_item`
  ADD CONSTRAINT `order_item_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `order_item_ibfk_3` FOREIGN KEY (`order_id`) REFERENCES `order` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Megkötések a táblához `product_health_effect`
--
ALTER TABLE `product_health_effect`
  ADD CONSTRAINT `product_health_effect_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `product_health_effect_ibfk_2` FOREIGN KEY (`health_effect_id`) REFERENCES `health_effect` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

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
-- Megkötések a táblához `product_tag`
--
ALTER TABLE `product_tag`
  ADD CONSTRAINT `product_tag_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `product_tag_ibfk_2` FOREIGN KEY (`tag_id`) REFERENCES `tag` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

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

--
-- Megkötések a táblához `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `user_ibfk_1` FOREIGN KEY (`avatar_id`) REFERENCES `avatar` (`id`) ON DELETE SET NULL ON UPDATE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
