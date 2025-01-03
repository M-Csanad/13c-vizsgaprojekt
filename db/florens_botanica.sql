-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Gép: 127.0.0.1:3307
-- Létrehozás ideje: 2025. Jan 03. 16:02
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

DROP TABLE IF EXISTS `cart`;
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
(1, 'A Tiszta Egészség', 'A Természet Esszenciája', 'Támogasd szervezetedet a természet legjavával! Vitaminok, ásványi anyagok, és növényi kivonatok gondoskodnak az energikus mindennapokról, miközben természetes forrásokból származó kiegészítők segítenek megőrizni egészséged harmóniáját. Válaszd a természet', 'http://localhost/fb-content/fb-categories/media/images/category-1/thumbnail_image_vertical.jpg', 'http://localhost/fb-content/fb-categories/media/images/category-1/thumbnail_image_horizontal.jpg', NULL, 0, 'a-tiszta-egeszseg'),
(2, 'A Nyugodt Elme', 'A Nyugalom Forrása', 'Találd meg a belső békédet természetes megoldásokkal! Ebben a kategóriában mindent megtalálsz, ami támogatja a relaxációt, csökkenti a stresszt és segít az éjszakai pihenésben, hogy minden nap energikusan és kiegyensúlyozottan induljon. Fedezd fel a nyuga', 'http://localhost/fb-content/fb-categories/media/images/category-2/thumbnail_image_vertical.jpg', 'http://localhost/fb-content/fb-categories/media/images/category-2/thumbnail_image_horizontal.jpg', NULL, 0, 'a-nyugodt-elme'),
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

--
-- Eseményindítók `category`
--
DROP TRIGGER IF EXISTS `category_slugify_after_insert`;
DELIMITER $$
CREATE TRIGGER `category_slugify_after_insert` AFTER INSERT ON `category` FOR EACH ROW BEGIN
    DECLARE slug_value VARCHAR(255);
    SET slug_value = LOWER(
        REPLACE(
            REPLACE(
                REPLACE(
                    REPLACE(
                        REPLACE(
                            REPLACE(
                                REPLACE(
                                    REPLACE(
                                        REPLACE(
                                            REPLACE(
                                                NEW.name, ' ', '-'),
                                            'á', 'a'),
                                        'é', 'e'),
                                    'í', 'i'),
                                'ó', 'o'),
                            'ö', 'o'),
                        'ő', 'o'),
                    'ú', 'u'),
                'ü', 'u'),
            'ű', 'u')
    );

    -- Frissítsük a slug mezőt
    UPDATE category
    SET slug = slug_value
    WHERE id = NEW.id;
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `category_slugify_after_update`;
DELIMITER $$
CREATE TRIGGER `category_slugify_after_update` AFTER UPDATE ON `category` FOR EACH ROW BEGIN
    DECLARE slug_value VARCHAR(255);
    SET slug_value = LOWER(
        REPLACE(
            REPLACE(
                REPLACE(
                    REPLACE(
                        REPLACE(
                            REPLACE(
                                REPLACE(
                                    REPLACE(
                                        REPLACE(
                                            REPLACE(
                                                NEW.name, ' ', '-'),
                                            'á', 'a'),
                                        'é', 'e'),
                                    'í', 'i'),
                                'ó', 'o'),
                            'ö', 'o'),
                        'ő', 'o'),
                    'ú', 'u'),
                'ü', 'u'),
            'ű', 'u')
    );

    -- Frissítsük a slug mezőt
    UPDATE category
    SET slug = slug_value
    WHERE id = NEW.id;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `delivery_info`
--

DROP TABLE IF EXISTS `delivery_info`;
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
(39, 'Hormonális zűrzavart', 'Néhány hatás befolyásolhatja a hormonális rendszert, ami menstruációs rendellenességekhez vezethet.', 0),
(40, 'Túlérzékenység', 'Néhány hatás bőrirritációt és egyéb allergiás reakciókat válthat ki, például viszketést vagy bőrpírt.', 0);

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

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `order`
--

DROP TABLE IF EXISTS `order`;
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

DROP TABLE IF EXISTS `product`;
CREATE TABLE `product` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `unit_price` int(11) DEFAULT NULL,
  `stock` int(11) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;

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
  `title` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;

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
(1, 1, 'Méregtelenítő Növények', 'A tisztulás támogatása természetes módon', 'A méregtelenítő növények segítenek a test tisztulási folyamatainak elősegítésében, eltávolítva a felhalmozódott toxikus anyagokat.', 'http://localhost/fb-content/fb-subcategories/media/images/category-1/thumbnail_image_vertical.jpg', 'http://localhost/fb-content/fb-subcategories/media/images/category-1/thumbnail_image_horizontal.jpg', NULL, 0, 'meregtelenito-novenyek'),
(2, 1, 'Immunerősítő Keverékek', 'A védekezőképesség fokozása', 'Erőteljes gyógynövény-keverékek, amelyek támogatják az immunrendszert, erősítve a szervezet védekezőképességét.', 'http://localhost/fb-content/fb-subcategories/media/images/category-2/thumbnail_image_vertical.jpg', 'http://localhost/fb-content/fb-subcategories/media/images/category-2/thumbnail_image_horizontal.jpg', NULL, 0, 'immunerosito-keverekek'),
(3, 2, 'Nyugtató Növények', 'Relaxáció és mentális egyensúly', 'Növények és gyógynövények, amelyek segítenek a stressz csökkentésében, és elősegítik a testi-lelki nyugalmat.', 'http://localhost/fb-content/fb-subcategories/media/images/category-3/thumbnail_image_vertical.jpg', 'http://localhost/fb-content/fb-subcategories/media/images/category-3/thumbnail_image_horizontal.jpg', NULL, 0, 'nyugtato-novenyek'),
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

--
-- Eseményindítók `subcategory`
--
DROP TRIGGER IF EXISTS `subcategory_slugify_after_insert`;
DELIMITER $$
CREATE TRIGGER `subcategory_slugify_after_insert` AFTER INSERT ON `subcategory` FOR EACH ROW BEGIN
    DECLARE slug_value VARCHAR(255);
    SET slug_value = LOWER(
        REPLACE(
            REPLACE(
                REPLACE(
                    REPLACE(
                        REPLACE(
                            REPLACE(
                                REPLACE(
                                    REPLACE(
                                        REPLACE(
                                            REPLACE(
                                                NEW.name, ' ', '-'),
                                            'á', 'a'),
                                        'é', 'e'),
                                    'í', 'i'),
                                'ó', 'o'),
                            'ö', 'o'),
                        'ő', 'o'),
                    'ú', 'u'),
                'ü', 'u'),
            'ű', 'u')
    );

    -- Frissítsük a slug mezőt
    UPDATE subcategory
    SET slug = slug_value
    WHERE id = NEW.id;
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `subcategory_slugify_after_update`;
DELIMITER $$
CREATE TRIGGER `subcategory_slugify_after_update` AFTER UPDATE ON `subcategory` FOR EACH ROW BEGIN
    DECLARE slug_value VARCHAR(255);
    SET slug_value = LOWER(
        REPLACE(
            REPLACE(
                REPLACE(
                    REPLACE(
                        REPLACE(
                            REPLACE(
                                REPLACE(
                                    REPLACE(
                                        REPLACE(
                                            REPLACE(
                                                NEW.name, ' ', '-'),
                                            'á', 'a'),
                                        'é', 'e'),
                                    'í', 'i'),
                                'ó', 'o'),
                            'ö', 'o'),
                        'ő', 'o'),
                    'ú', 'u'),
                'ü', 'u'),
            'ű', 'u')
    );

    -- Frissítsük a slug mezőt
    UPDATE subcategory
    SET slug = slug_value
    WHERE id = NEW.id;
END
$$
DELIMITER ;

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
(7, 'Pollen', 'http://localhost/fb-content/assets/media/images/tags/pollen.png'),
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
  ADD PRIMARY KEY (`session_id`),
  ADD KEY `order_ibfk_1` (`user_id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT a táblához `image`
--
ALTER TABLE `image`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT a táblához `product`
--
ALTER TABLE `product`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT a táblához `product_health_effect`
--
ALTER TABLE `product_health_effect`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT a táblához `product_image`
--
ALTER TABLE `product_image`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT a táblához `product_page`
--
ALTER TABLE `product_page`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT a táblához `product_tag`
--
ALTER TABLE `product_tag`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT a táblához `review`
--
ALTER TABLE `review`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

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
