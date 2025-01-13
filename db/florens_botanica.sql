-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Gép: 127.0.0.1:3307
-- Létrehozás ideje: 2025. Jan 13. 21:35
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
-- Tábla szerkezet ehhez a táblához `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `session_id` varchar(255) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `modified_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `category`
--

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
(1, 'A Tiszta Egészség', 'A Természet Esszenciája', 'Támogasd szervezetedet a természet legjavával! Vitaminok, ásványi anyagok, és növényi kivonatok gondoskodnak az energikus mindennapokról, miközben természetes forrásokból származó kiegészítők segítenek megőrizni egészséged harmóniáját. Válaszd a természet', 'http://localhost/fb-content/fb-categories/media/images/category-1/thumbnail_image_vertical.jpg', 'http://localhost/fb-content/fb-categories/media/images/category-1/thumbnail_image_horizontal.jpg', NULL, 3, 'a-tiszta-egeszseg'),
(2, 'A Nyugodt Elme', 'A Nyugalom Forrása', 'Találd meg a belső békédet természetes megoldásokkal! Ebben a kategóriában mindent megtalálsz, ami támogatja a relaxációt, csökkenti a stresszt és segít az éjszakai pihenésben, hogy minden nap energikusan és kiegyensúlyozottan induljon. Fedezd fel a nyuga', 'http://localhost/fb-content/fb-categories/media/images/category-2/thumbnail_image_vertical.jpg', 'http://localhost/fb-content/fb-categories/media/images/category-2/thumbnail_image_horizontal.jpg', NULL, 2, 'a-nyugodt-elme'),
(3, 'A Detox Ereje', 'Méregtelenítés', 'Adj új lendületet testednek természetes méregtelenítő megoldásokkal! Ebben a kategóriában hatékony, természetes eszközöket találsz, amelyek segítenek a belső tisztulásban, támogatják a májat, vesét és az emésztőrendszert, hogy szervezeted felfrissüljön és', 'http://localhost/fb-content/fb-categories/media/images/category-3/thumbnail_image_vertical.jpg', 'http://localhost/fb-content/fb-categories/media/images/category-3/thumbnail_image_horizontal.jpg', NULL, 0, 'a-detox-ereje'),
(4, 'A Könnyed Emésztés', 'Az Anyagcsere', 'Támogasd tested természetes egyensúlyát! Ebben a kategóriában hatékony megoldásokat találsz, amelyek serkentik az anyagcserét, javítják az emésztést, és hozzájárulnak a megfelelő rostbevitelhez, hogy energikus és kiegyensúlyozott lehess minden nap.', 'http://localhost/fb-content/fb-categories/media/images/category-4/thumbnail_image_vertical.jpg', 'http://localhost/fb-content/fb-categories/media/images/category-4/thumbnail_image_horizontal.jpg', NULL, 0, 'a-konnyed-emesztes'),
(5, 'A Mozgás Ereje', 'A Mozgás Forrása', 'Turbózd fel teljesítményed természetes, erőt adó megoldásokkal! Ebben a kategóriában megtalálod mindazt, ami támogatja az aktív életmódot, növeli az energiát és segíti a regenerációt – természetes alapanyagokból, hogy minden mozdulat könnyed legyen és hat', 'http://localhost/fb-content/fb-categories/media/images/category-5/thumbnail_image_vertical.jpg', 'http://localhost/fb-content/fb-categories/media/images/category-5/thumbnail_image_horizontal.jpg', NULL, 0, 'a-mozgas-ereje'),
(6, 'A Pillanat Lángja', 'A Szenvedély Ereje', 'Ébreszd fel a belső tüzed a természet erejével! Ebben a kategóriában olyan különleges gyógynövények, aromaterápiás esszenciák és tápláló kiegészítők várnak rád, amelyek támogatják az érzelmi harmóniát, fokozzák az energiát és új lendületet adnak a mindennapoknak.', 'http://localhost/fb-content/fb-categories/media/images/category-6/thumbnail_image_vertical.jpg', 'http://localhost/fb-content/fb-categories/media/images/category-6/thumbnail_image_horizontal.jpg', NULL, 0, 'a-pillanat-langja'),
(7, 'A Szépség Titka', 'Az Fiatalos Szépség', 'Fedezd fel a ragyogó megjelenés titkát! Ebben a kategóriában bőrápoló, hajápoló és anti-aging megoldások várnak, amelyek természetes összetevőikkel támogatják a fiatalos szépséget és az egészséges ragyogást. Érd el a szépség új dimenzióját!', 'http://localhost/fb-content/fb-categories/media/images/category-7/thumbnail_image_vertical.jpg', 'http://localhost/fb-content/fb-categories/media/images/category-7/thumbnail_image_horizontal.jpg', NULL, 0, 'a-szepseg-titka'),
(8, 'Az Energia Alapjai', 'A Vitalitás Forrásai', 'Töltsd fel szervezeted a legfontosabb tápanyagokkal! Ebben a kategóriában vitaminok, aminosavak és enzimek kínálnak természetes támogatást az energiád fenntartásához és a mindennapi vitalitás eléréséhez. Érezd a különbséget, amit a természet ereje adhat!', 'http://localhost/fb-content/fb-categories/media/images/category-8/thumbnail_image_vertical.jpg', 'http://localhost/fb-content/fb-categories/media/images/category-8/thumbnail_image_horizontal.jpg', NULL, 0, 'az-energia-alapjai'),
(9, 'A Frissítő Élmény', 'A Frissesség Titka', 'Élvezd a természetesen frissítő italok és ízek világát! Ebben a kategóriában teák, kávék és gyümölcs alapú italok kínálnak energiát, vitalitást és egy kis kényeztetést, hogy felfrissülve nézhess szembe a nap kihívásaival.', 'http://localhost/fb-content/fb-categories/media/images/category-9/thumbnail_image_vertical.jpg', 'http://localhost/fb-content/fb-categories/media/images/category-9/thumbnail_image_horizontal.jpg', NULL, 0, 'a-frissito-élmeny'),
(10, 'A Természet Elixirjei', 'Az Egészség Esszenciája', 'Ismerd meg a természet rejtett kincseit! Ebben a kategóriában egzotikus gyógynövények, tinktúrák és különleges elixírek segítenek támogatni a vitalitást és a belső harmóniát, hogy a mindennapjaid energikusabbak és kiegyensúlyozottabbak legyenek.', 'http://localhost/fb-content/fb-categories/media/images/category-10/thumbnail_image_vertical.jpg', 'http://localhost/fb-content/fb-categories/media/images/category-10/thumbnail_image_horizontal.jpg', NULL, 0, 'a-termeszet-elixirjei'),
(11, 'A Tengerek Kincsei', 'Az Óceán Ereje', 'Meríts erőt a tengerek gazdagságából! Ebben a kategóriában tengeri algák, ásványok, halolajok és kollagén alapú kiegészítők várnak rád, hogy természetes támogatást nyújtsanak az egészséghez és a vitalitáshoz. Fedezd fel az óceán erejét!', 'http://localhost/fb-content/fb-categories/media/images/category-11/thumbnail_image_vertical.jpg', 'http://localhost/fb-content/fb-categories/media/images/category-11/thumbnail_image_horizontal.jpg', NULL, 0, 'a-tengerek-kincsei'),
(12, 'A Természet Illatai', 'A Lélek Illatai', 'Engedd, hogy a természet illatai harmóniát és nyugalmat hozzanak az életedbe! Ebben a kategóriában illóolajok, füstölők és illatgyertyák segítenek a relaxációban, a stresszoldásban és a tér energiájának megújításában. Fedezd fel a lélek illatainak erejét!', 'http://localhost/fb-content/fb-categories/media/images/category-12/thumbnail_image_vertical.jpg', 'http://localhost/fb-content/fb-categories/media/images/category-12/thumbnail_image_horizontal.jpg', NULL, 0, 'a-termeszet-illatai'),
(13, 'A Konyha Ízei', 'Az Ízek Harmóniája', 'Hozd ki a legtöbbet a konyhából természetes fűszerekkel és gyógyhatású kiegészítőkkel! Ebben a kategóriában ízletes és egészséges megoldások várnak, amelyek nemcsak ételeidet teszik különlegessé, hanem az egészségedet is támogatják.', 'http://localhost/fb-content/fb-categories/media/images/category-13/thumbnail_image_vertical.jpg', 'http://localhost/fb-content/fb-categories/media/images/category-13/thumbnail_image_horizontal.jpg', NULL, 0, 'a-konyha-ízei'),
(14, 'A Szív Egészsége', 'A Szív Ereje', 'Támogasd szíved egészségét természetes megoldásokkal! Ebben a kategóriában olyan kiegészítőket találsz, amelyek segítik a keringést, erősítik az érrendszert és hozzájárulnak a szív optimális működéséhez. Adj lendületet az élet ritmusának!', 'http://localhost/fb-content/fb-categories/media/images/category-14/thumbnail_image_vertical.jpg', 'http://localhost/fb-content/fb-categories/media/images/category-14/thumbnail_image_horizontal.jpg', NULL, 0, 'a-sziv-egeszsege'),
(15, 'Az Erdő Ajándéka', 'Az Erdő Kincsei', 'Fedezd fel az erdő gazdagságát! Ebben a kategóriában erdei gombák, gyógynövények, gyümölcsök, mézek és aromaterápiás termékek várnak, hogy természetes módon támogassák egészségedet és kényeztessék érzékeidet. Hozd el otthonodba az erdő ajándékait!', 'http://localhost/fb-content/fb-categories/media/images/category-15/thumbnail_image_vertical.jpg', 'http://localhost/fb-content/fb-categories/media/images/category-15/thumbnail_image_horizontal.jpg', NULL, 0, 'az-erdo-ajandeka');

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
-- Tábla szerkezet ehhez a táblához `health_effect`
--

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
(39, 'Túlérzékenység', 'Néhány hatás bőrirritációt és egyéb allergiás reakciókat válthat ki, például viszketést vagy bőrpírt.', 0);

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
(15, 'http://localhost/fb-content/fb-products/media/images/product-2/image1.jpg', 'vertical', 'image');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `order`
--

CREATE TABLE `order` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL COMMENT 'Vendég rendelések miatt lehet NULL is a user_id',
  `cart_id` int(11) DEFAULT NULL,
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

--
-- A tábla adatainak kiíratása `product`
--

INSERT INTO `product` (`id`, `name`, `unit_price`, `stock`, `description`) VALUES
(1, 'Kamilla virág', 4900, 30, 'A Matricaria chamomilla, azaz a kamilla, az egyik legismertebb és leggyengédebb gyógynövény. Gyakran teaként használják a relaxáció elősegítésére és az emésztés támogatására. A kamilla finoman nyugtatja a bőrt, ezért előszeretettel alkalmazzák bőrápolási '),
(2, 'Levendula virág', 6000, 20, 'A Lavandula angustifolia, közismert nevén levendula, mediterrán eredetű növény, amely édes, nyugtató, virágos illatáról ismert. Virágai és levelei évszázadok óta részei a nyugati gyógynövényhasználatnak, és a szépségápolástól kezdve a főzésig sokoldalúan '),
(3, 'Kurkuma gyökér', 2000, 50, 'A Curcuma longa, azaz a kurkuma, az indiai és délkelet-ázsiai trópusi régiókból származó, gyömbérfélék családjába tartozó évelő növény. Élénk narancssárga-sárga színe miatt fűszerként és természetes színezőanyagként egyaránt népszerű. A kurkuma különleges'),
(4, 'Kasvirág gyökér', 8000, 30, 'Az Echinacea angustifolia, egy észak-amerikai gyógynövény, amely támogatja az immunrendszert. Organikus gyökereit teák, tinktúrák és helyi olajok készítésére használják, hogy erősítse a test védelmi rendszereit és javítsa a közérzetet.'),
(5, 'Darált fokhagyma', 3200, 40, 'Az Allium sativum, közismert nevén fokhagyma, egy ízletes és erőteljes fűszer, mely számos kultúrában népszerű. Szárított, aprított fokhagymánk a legnagyobb szemcseméretű, és kiválóan alkalmas olajok infúziójához vagy főzéshez. Emellett a fokhagyma híres ');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `product_health_effect`
--

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
(27, 2, 15);

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `product_image`
--

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
(15, 15, 2);

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

--
-- A tábla adatainak kiíratása `product_page`
--

INSERT INTO `product_page` (`id`, `created_at`, `last_modified`, `product_id`, `link_slug`, `category_id`, `subcategory_id`, `page_title`, `page_content`) VALUES
(1, '2025-01-05 16:16:00', '2025-01-05 16:16:00', 1, 'a-nyugodt-elme/nyugtato-novenyek/kamilla-virag', 2, 3, 'Kamilla virág', 'A kamilla teaként fogyasztva relaxációt nyújt, megnyugtatja az emésztőrendszert, és olyan gyengéd, hogy csecsemőknek is adható. Külsőleg alkalmazva lágyítja és nyugtatja a bőrt, és természetes fényt kölcsönöz a hajnak. A növényt történelmileg széles körben használták Európában és Észak-Amerikában teák, fürdők, inhalációk és borogatások formájában.\r\nA kamilla az ókori Egyiptomban is kedvelt volt, ahol az isteneknek szánt áldozatok közé tartozott. Mexikóban manzanillaként ismerik, és a gyomor megnyugtatására, valamint a hangulat emelésére készítenek belőle teát. Az őslakos amerikai törzsek, például a Cherokee és az Aleuták, szintén sokoldalúan használták ezt a növényt.\r\nEz az organikus, sokoldalú növény megérdemelten nyerte el az „alles zutraut” – „mindenre képes” – becenevet.'),
(2, '2025-01-05 16:35:35', '2025-01-05 16:35:35', 2, 'a-nyugodt-elme/nyugtato-novenyek/levendula-virag', 2, 3, 'Levendula virág', 'Már az ókori egyiptomiak, görögök és rómaiak is használták fürdők, ételek és parfümök készítésére. A francia konyhában gyakori fűszer, amely különleges, virágos ízt kölcsönöz desszerteknek és húsoknak.\r\nA levendula az egyik legismertebb illat a világon, amely nyugtató hatása mellett a szépségápolásban és illatosító keverékekben is népszerű.'),
(3, '2025-01-05 17:29:03', '2025-01-05 17:29:03', 3, 'a-tiszta-egeszseg/meregtelenito-novenyek/kurkuma-gyoker', 1, 1, 'Kurkuma gyökér', 'A kurkuma (Curcuma longa) a gyömbérfélék családjába tartozó évelő növény, amely Indiából és Délkelet-Ázsiából származik. Élénk színe és földes íze miatt népszerű fűszer, különösen currykhez, rizses ételekhez és italokhoz, mint az aranytej.\r\nHatóanyaga, a kurkumin, adja aranyló színét és jótékony tulajdonságait. A kurkuma természetes színezékként és hagyományos ájurvédikus gyógyászatban is használatos. Szárított gyökere teákhoz, tinktúrákhoz és természetes festékekhez is kiváló.'),
(4, '2025-01-05 19:43:58', '2025-01-05 19:43:58', 4, 'a-tiszta-egeszseg/meregtelenito-novenyek/kasvirag-gyoker', 1, 1, 'Kasvirág gyökér', 'Az Echinacea angustifolia évezredek óta használatos a hagyományos gyógymódokban, elsősorban az immunrendszer erősítésére. A növény gyökerei teában, tinktúrában vagy olajban alkalmazhatóak, hogy támogassák a test védekezőképességét.\r\nA növény története a helyi amerikai törzsekhez vezethető vissza, akik számos egészségügyi problémára használták. Bár az Echinacea purpurea vált a legismertebb fajjá, az Echinacea angustifolia továbbra is értékes gyógynövény marad.'),
(5, '2025-01-05 19:55:24', '2025-01-05 19:55:24', 5, 'a-tiszta-egeszseg/meregtelenito-novenyek/daralt-fokhagyma', 1, 1, 'Darált fokhagyma', 'A fokhagyma az egyik legismertebb és legszélesebb körben használt gyógynövény, melyet évezredek óta alkalmaznak ízesítésre és gyógyításra. Az Allium sativum a liliomfélék családjába tartozik, és a világ minden táján termesztik. Különleges, erőteljes illatával és ízével a fokhagyma nemcsak a konyhák alapvető fűszere, hanem a tradicionális kínai orvoslásban és az ayurvédikus gyógyászatban is elismert. Az aprított fokhagyma kiválóan használható olajok készítéséhez vagy főzéshez, és segíthet fenntartani a normál koleszterinszintet is.\r\n\r\nA fokhagyma több ezer éve elterjedt kultúrákban, és rengeteg jótékony hatásával híres. Az ókori görög és római orvosok is nagyra értékelték, és mágikus erőt tulajdonítottak neki, mint védelmet a gonosz szellemek és betegségek ellen. Az ayurvédában és a hagyományos kínai orvoslásban is fontos szerepe van. Segíthet az egészséges emésztés támogatásában és a koleszterinszint egyensúlyának megőrzésében.');

--
-- Eseményindítók `product_page`
--
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
(18, 10, 2);

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `review`
--

CREATE TABLE `review` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `rating` double DEFAULT NULL,
  `description` text DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
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
  `thumbnail_video_uri` varchar(255) DEFAULT NULL,
  `product_count` int(11) NOT NULL DEFAULT 0,
  `slug` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;

--
-- A tábla adatainak kiíratása `subcategory`
--

INSERT INTO `subcategory` (`id`, `category_id`, `name`, `subname`, `description`, `thumbnail_image_vertical_uri`, `thumbnail_image_horizontal_uri`, `thumbnail_video_uri`, `product_count`, `slug`) VALUES
(1, 1, 'Méregtelenítő Növények', 'A tisztulás támogatása természetes módon', 'A méregtelenítő növények segítenek a test tisztulási folyamatainak elősegítésében, eltávolítva a felhalmozódott toxikus anyagokat.', 'http://localhost/fb-content/fb-subcategories/media/images/category-1/thumbnail_image_vertical.jpg', 'http://localhost/fb-content/fb-subcategories/media/images/category-1/thumbnail_image_horizontal.jpg', NULL, 3, 'meregtelenito-novenyek'),
(2, 1, 'Immunerősítő Keverékek', 'A védekezőképesség fokozása', 'Erőteljes gyógynövény-keverékek, amelyek támogatják az immunrendszert, erősítve a szervezet védekezőképességét.', 'http://localhost/fb-content/fb-subcategories/media/images/category-2/thumbnail_image_vertical.jpg', 'http://localhost/fb-content/fb-subcategories/media/images/category-2/thumbnail_image_horizontal.jpg', NULL, 0, 'immunerosito-keverekek'),
(3, 2, 'Nyugtató Növények', 'Relaxáció és mentális egyensúly', 'Növények és gyógynövények, amelyek segítenek a stressz csökkentésében, és elősegítik a testi-lelki nyugalmat.', 'http://localhost/fb-content/fb-subcategories/media/images/category-3/thumbnail_image_vertical.jpg', 'http://localhost/fb-content/fb-subcategories/media/images/category-3/thumbnail_image_horizontal.jpg', NULL, 2, 'nyugtato-novenyek'),
(4, 2, 'Alvássegítők', 'A pihentető alvás titkai', 'Nyugtató hatású növények és kivonatok, amelyek javítják az alvást, segítve a pihentető, regeneráló éjszakákat.', 'http://localhost/fb-content/fb-subcategories/media/images/category-4/thumbnail_image_vertical.jpg', 'http://localhost/fb-content/fb-subcategories/media/images/category-4/thumbnail_image_horizontal.jpg', NULL, 0, 'alvassegitok'),
(5, 3, 'Májtisztító Növények', 'A méregtelenítés segítése a májban', 'Olyan gyógynövények, amelyek támogathatják a máj méregtelenítő és tisztító folyamatait.', 'http://localhost/fb-content/fb-subcategories/media/images/category-5/thumbnail_image_vertical.jpg', 'http://localhost/fb-content/fb-subcategories/media/images/category-5/thumbnail_image_horizontal.jpg', NULL, 0, 'majtisztito-novenyek'),
(6, 3, 'Lúgosító Növények', 'A sav-bázis egyensúly helyreállítása', 'Növények, amelyek elősegítik a szervezet lúgosítását, hozzájárulva a sav-bázis egyensúly fenntartásához.', 'http://localhost/fb-content/fb-subcategories/media/images/category-6/thumbnail_image_vertical.jpg', 'http://localhost/fb-content/fb-subcategories/media/images/category-6/thumbnail_image_horizontal.jpg', NULL, 0, 'lugosito-novenyek'),
(7, 4, 'Emésztést Támogatók', 'Az egészséges emésztés alapjai', 'Olyan gyógynövények, amelyek segítenek az emésztési folyamatok javításában és a bélflóra egészségének fenntartásában.', 'http://localhost/fb-content/fb-subcategories/media/images/category-7/thumbnail_image_vertical.jpg', 'http://localhost/fb-content/fb-subcategories/media/images/category-7/thumbnail_image_horizontal.jpg', NULL, 0, 'emesztest-tamogatok'),
(8, 4, 'Puffadásgátló Növények', 'A kellemetlen puffadás enyhítése', 'Növények, amelyek csökkenthetik a puffadást, segítve az emésztést és a bélműködés javítását.', 'http://localhost/fb-content/fb-subcategories/media/images/category-8/thumbnail_image_vertical.jpg', 'http://localhost/fb-content/fb-subcategories/media/images/category-8/thumbnail_image_horizontal.jpg', NULL, 0, 'puffadasgatlo-novenyek'),
(9, 5, 'Ízületi Növények', 'Az ízületek védelme és regenerálása', 'Olyan gyógynövények, amelyek segítenek az ízületek egészségének megőrzésében, enyhítve a fájdalmat és javítva a mobilitást.', 'http://localhost/fb-content/fb-subcategories/media/images/category-9/thumbnail_image_vertical.jpg', 'http://localhost/fb-content/fb-subcategories/media/images/category-9/thumbnail_image_horizontal.jpg', NULL, 0, 'ízuleti-novenyek'),
(10, 5, 'Energizáló Növények', 'Az energia növelése természetes módon', 'Frissítő hatású növények, amelyek elősegítik a vitalitást és az energiaszint növelését.', 'http://localhost/fb-content/fb-subcategories/media/images/category-10/thumbnail_image_vertical.jpg', 'http://localhost/fb-content/fb-subcategories/media/images/category-10/thumbnail_image_horizontal.jpg', NULL, 0, 'energizalo-novenyek'),
(11, 6, 'Életerőt Adó Növények', 'Frissesség és vitalitás', 'A növények, amelyek erősítik a testet és az elmét, segítve a fáradtság leküzdését és a mindennapi életerő fenntartását.', 'http://localhost/fb-content/fb-subcategories/media/images/category-11/thumbnail_image_vertical.jpg', 'http://localhost/fb-content/fb-subcategories/media/images/category-11/thumbnail_image_horizontal.jpg', NULL, 0, 'életerot-ado-novenyek'),
(12, 6, 'Stresszcsökkentő Növények', 'A nyugodt elme megteremtése', 'Nyugtató gyógynövények, amelyek segítenek csökkenteni a stresszt és elősegítik a lelki egyensúlyt.', 'http://localhost/fb-content/fb-subcategories/media/images/category-12/thumbnail_image_vertical.jpg', 'http://localhost/fb-content/fb-subcategories/media/images/category-12/thumbnail_image_horizontal.jpg', NULL, 0, 'stresszcsokkento-novenyek'),
(13, 7, 'Bőrápoló Növények', 'A bőr természetes ápolása', 'Olyan növények, amelyek hozzájárulnak a bőr hidratálásához, regenerálódásához és védelméhez.', 'http://localhost/fb-content/fb-subcategories/media/images/category-13/thumbnail_image_vertical.jpg', 'http://localhost/fb-content/fb-subcategories/media/images/category-13/thumbnail_image_horizontal.jpg', NULL, 0, 'borapolo-novenyek'),
(14, 7, 'Hajápoló Növények', 'A haj erősítése és táplálása', 'Növények, amelyek segítenek erősíteni a hajat, serkentik a növekedést és javítják annak egészségét.', 'http://localhost/fb-content/fb-subcategories/media/images/category-14/thumbnail_image_vertical.jpg', 'http://localhost/fb-content/fb-subcategories/media/images/category-14/thumbnail_image_horizontal.jpg', NULL, 0, 'hajapolo-novenyek'),
(15, 8, 'Vitalizáló Növények', 'A frissesség és életerő fokozása', 'Növények, amelyek fokozzák a vitalitást, segítenek frissíteni és revitalizálni a testet.', 'http://localhost/fb-content/fb-subcategories/media/images/category-15/thumbnail_image_vertical.jpg', 'http://localhost/fb-content/fb-subcategories/media/images/category-15/thumbnail_image_horizontal.jpg', NULL, 0, 'vitalizalo-novenyek'),
(16, 9, 'Energizáló Teák', 'Frissítő teaélmény a mindennapokhoz', 'Teák, amelyek növelik az energiát és frissítenek a nap bármely szakában.', 'http://localhost/fb-content/fb-subcategories/media/images/category-16/thumbnail_image_vertical.jpg', 'http://localhost/fb-content/fb-subcategories/media/images/category-16/thumbnail_image_horizontal.jpg', NULL, 0, 'energizalo-teak'),
(17, 9, 'Illatos Növények', 'Aromák a frissességért', 'Növényi levelek és illóolajok, amelyek segítenek a levegő frissítésében, hozzájárulva a hangulat javításához.', 'http://localhost/fb-content/fb-subcategories/media/images/category-17/thumbnail_image_vertical.jpg', 'http://localhost/fb-content/fb-subcategories/media/images/category-17/thumbnail_image_horizontal.jpg', NULL, 0, 'illatos-novenyek'),
(18, 9, 'Hűsítő Növények', 'A hűsítő hatás természetes ereje', 'Növények, amelyek hűsítő hatással vannak a testre, enyhítve a meleget és felfrissítve a bőrt.', 'http://localhost/fb-content/fb-subcategories/media/images/category-18/thumbnail_image_vertical.jpg', 'http://localhost/fb-content/fb-subcategories/media/images/category-18/thumbnail_image_horizontal.jpg', NULL, 0, 'husito-novenyek'),
(19, 10, 'Ritka Gyógynövények', 'Különleges és egyedi növények', 'Ritka, különleges gyógynövények, amelyek különleges jótékony hatásokkal bírnak a test és az elme számára.', 'http://localhost/fb-content/fb-subcategories/media/images/category-19/thumbnail_image_vertical.jpg', 'http://localhost/fb-content/fb-subcategories/media/images/category-19/thumbnail_image_horizontal.jpg', NULL, 0, 'ritka-gyogynovenyek'),
(20, 10, 'Esszenciális Kivonatok', 'Koncentrált növényi kivonatok', 'Koncentrált növényi kivonatok, amelyek gyorsan és hatékonyan nyújtanak jótékony hatásokat a testre és az elmére.', 'http://localhost/fb-content/fb-subcategories/media/images/category-20/thumbnail_image_vertical.jpg', 'http://localhost/fb-content/fb-subcategories/media/images/category-20/thumbnail_image_horizontal.jpg', NULL, 0, 'esszencialis-kivonatok'),
(21, 11, 'Tengeri Növények', 'Az óceán kincsei a bőr és a test számára', 'Tengeri növények, amelyek gazdagok ásványi anyagokban és tápanyagokban, segítve a hidratálást és a méregtelenítést.', 'http://localhost/fb-content/fb-subcategories/media/images/category-21/thumbnail_image_vertical.jpg', 'http://localhost/fb-content/fb-subcategories/media/images/category-21/thumbnail_image_horizontal.jpg', NULL, 0, 'tengeri-novenyek'),
(22, 12, 'Aromás Növények', 'Illatok a testi-lelki frissességhez', 'Aromás gyógynövények, amelyek segítenek a pihenésben és a hangulat javításában, frissítő illatokkal.', 'http://localhost/fb-content/fb-subcategories/media/images/category-22/thumbnail_image_vertical.jpg', 'http://localhost/fb-content/fb-subcategories/media/images/category-22/thumbnail_image_horizontal.jpg', NULL, 0, 'aromas-novenyek'),
(23, 12, 'Illóolajos Kivonatok', 'Természetes eredetű és jótékony hatású illóolajok', 'Illóolajok és növényi kivonatok, amelyek a lelki és fizikai jólét fenntartásában segítenek.', 'http://localhost/fb-content/fb-subcategories/media/images/category-23/thumbnail_image_vertical.jpg', 'http://localhost/fb-content/fb-subcategories/media/images/category-23/thumbnail_image_horizontal.jpg', NULL, 0, 'illoolajos-kivonatok'),
(24, 13, 'Fűszernövények', 'Az ízek és a gyógyító hatás egyensúlya', 'Konyhai fűszernövények, amelyek nemcsak az étkezéseket teszik finomabbá, hanem jótékony hatással vannak az egészségre is.', 'http://localhost/fb-content/fb-subcategories/media/images/category-24/thumbnail_image_vertical.jpg', 'http://localhost/fb-content/fb-subcategories/media/images/category-24/thumbnail_image_horizontal.jpg', NULL, 0, 'fuszernovenyek'),
(25, 13, 'Ehető Gyógynövények', 'Természetes ízek a konyhádban', 'Ezek a növények nemcsak finomak, hanem gazdagok értékes tápanyagokban is, így bármikor felhasználhatod őket az ételkészítéskor.', 'http://localhost/fb-content/fb-subcategories/media/images/category-25/thumbnail_image_vertical.jpg', 'http://localhost/fb-content/fb-subcategories/media/images/category-25/thumbnail_image_horizontal.jpg', NULL, 0, 'eheto-gyogynovenyek'),
(26, 14, 'Keringést Támogatók', ' A szív- és érrendszer egészsége', 'Növények és kivonatok, amelyek támogatják a szív- és érrendszert, javítva a vérkeringést.', 'http://localhost/fb-content/fb-subcategories/media/images/category-26/thumbnail_image_vertical.jpg', 'http://localhost/fb-content/fb-subcategories/media/images/category-26/thumbnail_image_horizontal.jpg', NULL, 0, 'keringest-tamogatok'),
(27, 14, 'Vérnyomáscsökkentő Növények', 'A szív egészségének védelme', 'Olyan növények, amelyek segítenek a vérnyomás szabályozásában, támogathatják a szív- és érrendszer működését.', 'http://localhost/fb-content/fb-subcategories/media/images/category-27/thumbnail_image_vertical.jpg', 'http://localhost/fb-content/fb-subcategories/media/images/category-27/thumbnail_image_horizontal.jpg', NULL, 0, 'vernyomascsokkento-novenyek'),
(28, 15, 'Erdei Gyógynövények', 'Jótékony és egészséges gyógynövények az erdőből', 'Olyan növények, amelyek az erdő mélyéről származnak, és jótékony hatással vannak a testre és az elmére.', 'http://localhost/fb-content/fb-subcategories/media/images/category-28/thumbnail_image_vertical.jpg', 'http://localhost/fb-content/fb-subcategories/media/images/category-28/thumbnail_image_horizontal.jpg', NULL, 0, 'erdei-gyogynovenyek'),
(29, 15, 'Erdei Kivonatok', 'Jótékony kivonatok az egészségért', 'Erdei növények kivonatai, amelyek különleges és erőteljes jótékony hatásokat kínálnak a méregtelenítéshez és regenerálódáshoz.', 'http://localhost/fb-content/fb-subcategories/media/images/category-29/thumbnail_image_vertical.jpg', 'http://localhost/fb-content/fb-subcategories/media/images/category-29/thumbnail_image_horizontal.jpg', NULL, 0, 'erdei-kivonatok');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `tag`
--

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
(7, 'Pollen', 'http://localhost/fb-content/assets/media/images/tags/pollen.png'),
(8, 'Tinktúra', 'http://localhost/fb-content/assets/media/images/tags/serum.png'),
(9, 'Cukormentes', 'http://localhost/fb-content/assets/media/images/tags/sugar-free.png'),
(10, 'Vegán', 'http://localhost/fb-content/assets/media/images/tags/vegan.png');

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
  `pfp_uri` varchar(255) DEFAULT NULL COMMENT 'Profilkép',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;

--
-- A tábla adatainak kiíratása `user`
--

INSERT INTO `user` (`id`, `email`, `user_name`, `password_hash`, `role`, `cookie_id`, `cookie_expires_at`, `first_name`, `last_name`, `pfp_uri`, `created_at`) VALUES
(1, '13c-blank@ipari.vein.hu', 'admin', '$2y$10$ZkZHsn0wcwOPzVBBFXfqTe0tjYFoUyLWc2OS/3L5t.fD1ag.FJFrm', 'Administrator', NULL, NULL, 'Máté', 'Blank', 'https://ui-avatars.com/api/?name=Blank+Máté&background=9CB5A6&bold=true&format=svg', '2024-11-02 13:27:24'),
(2, 'teszt-elek@gmail.com', 'teszt-elek', '$2y$10$.BZLWK4qrkNB7jVCWxpkyeCpo/wRGMA/7QmSb7j4MnSZc/Ez4huMa', 'Guest', NULL, NULL, 'Elek', 'Teszt', 'https://ui-avatars.com/api/?name=Teszt+Elek&background=9CB5A6&bold=true&format=svg', '2024-11-26 17:24:56');

--
-- Indexek a kiírt táblákhoz
--

--
-- A tábla indexei `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_product` (`user_id`,`product_id`),
  ADD KEY `cart_ibfk_2` (`product_id`),
  ADD KEY `user_id` (`user_id`);

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
  ADD KEY `order_ibfk_1` (`user_id`),
  ADD KEY `cart_id` (`cart_id`);

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
  ADD UNIQUE KEY `user_name` (`user_name`);

--
-- A kiírt táblák AUTO_INCREMENT értéke
--

--
-- AUTO_INCREMENT a táblához `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT a táblához `category`
--
ALTER TABLE `category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT a táblához `delivery_info`
--
ALTER TABLE `delivery_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT a táblához `health_effect`
--
ALTER TABLE `health_effect`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT a táblához `image`
--
ALTER TABLE `image`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT a táblához `order`
--
ALTER TABLE `order`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT a táblához `product`
--
ALTER TABLE `product`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT a táblához `product_health_effect`
--
ALTER TABLE `product_health_effect`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT a táblához `product_image`
--
ALTER TABLE `product_image`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT a táblához `product_page`
--
ALTER TABLE `product_page`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT a táblához `product_tag`
--
ALTER TABLE `product_tag`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT a táblához `review`
--
ALTER TABLE `review`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT a táblához `subcategory`
--
ALTER TABLE `subcategory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT a táblához `tag`
--
ALTER TABLE `tag`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT a táblához `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Megkötések a kiírt táblákhoz
--

--
-- Megkötések a táblához `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cart_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Megkötések a táblához `delivery_info`
--
ALTER TABLE `delivery_info`
  ADD CONSTRAINT `delivery_info_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Megkötések a táblához `order`
--
ALTER TABLE `order`
  ADD CONSTRAINT `order_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `order_ibfk_2` FOREIGN KEY (`cart_id`) REFERENCES `cart` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
