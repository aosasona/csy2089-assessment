-- MariaDB dump 10.19  Distrib 10.5.19-MariaDB, for Linux (x86_64)
--
-- Host: mysql    Database: eds_electronics
-- ------------------------------------------------------
-- Server version	11.2.2-MariaDB-1:11.2.2+maria~ubu2204

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Current Database: `eds_electronics`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `eds_electronics` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;

USE `eds_electronics`;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_name` (`name`),
  UNIQUE KEY `uq_slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (3,'Monitors','monitors'),(5,'Chargers','chargers'),(6,'Consoles','consoles');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `enquiries`
--

DROP TABLE IF EXISTS `enquiries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `enquiries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `asked_by` int(11) DEFAULT NULL,
  `answered_by` int(11) DEFAULT NULL,
  `question` text NOT NULL,
  `answer` text DEFAULT NULL,
  `is_published` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  KEY `asked_by` (`asked_by`),
  KEY `answered_by` (`answered_by`),
  CONSTRAINT `enquiries_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  CONSTRAINT `enquiries_ibfk_2` FOREIGN KEY (`asked_by`) REFERENCES `users` (`id`),
  CONSTRAINT `enquiries_ibfk_3` FOREIGN KEY (`answered_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `enquiries`
--

LOCK TABLES `enquiries` WRITE;
/*!40000 ALTER TABLE `enquiries` DISABLE KEYS */;
INSERT INTO `enquiries` VALUES (1,8,NULL,1,'Does it support HDMI?','Yes, it does.',1,'2023-12-25 20:57:49','2023-12-25 20:59:41'),(2,8,2,1,'Can I VESA-mount instead?\r\n','Yes, although you would need to buy a version that comes with the VESA adaptor',1,'2023-12-25 20:58:45','2023-12-25 20:59:34'),(4,6,2,NULL,'Is this a GaN charger?',NULL,0,'2023-12-25 22:28:42','2023-12-25 22:28:42');
/*!40000 ALTER TABLE `enquiries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `public_id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` float NOT NULL,
  `description` text NOT NULL,
  `image_name` varchar(255) DEFAULT NULL,
  `category_id` int(11) NOT NULL,
  `is_listed` tinyint(1) NOT NULL DEFAULT 1,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `listed_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `manufacturer` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_public_id` (`public_id`),
  KEY `category_id` (`category_id`),
  KEY `listed_by` (`listed_by`),
  CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
  CONSTRAINT `products_ibfk_2` FOREIGN KEY (`listed_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (5,'lenovo-24-inch-monitor-170319','Lenovo L29w-30 29 Inch Pc Monitor',10999,'Its 29-inch IPS panel display with expansive 178°/178° view angle delivers quality visuals whichever way you look at it, whether you’re laying out spreadsheets, taking out opponents or streaming your favorite comedy with family members.\r\n\r\nAMD freesync eliminates image tearing for a cleaner daily work and there’s no ghosting due to the monitor’s 4ms response time.\r\nBrightness of 300 nits with contrast ratio of 1000:1 and color gamut 99% sRGB and anti-glare display this Lenovo monitor is suitable for all your family.\r\n\r\nThe L29w-30 is compatible with AMD FreeSync technology allowing for unencumbered video streaming and game rendering. The 90 Hz refresh rate reduces screen stuttering and provides smooth in-game motion. Image ghosting is minimized with a 4ms response time.\r\n\r\nProtect your vision: Designed with your eyes in mind, flicker-free TUV Rheinland low blue light filtering reduces eye strain without affecting colour accuracy\r\n\r\n3 year of base warranty included.','prod_6589ec5ee098f.jpg',3,1,1,1,'2023-12-21 22:17:19','2023-12-25 20:55:58','Lenovo'),(6,'20w-anker-charger','20w Anker Charger',799,'Model Number: A2149\r\n\r\nPowerPort III 20W Cube\r\n\r\nThe Powerful Charger for Phones and Tablets\r\n\r\nFast Charging\r\n\r\nCharge the iPhone 15 series to 50% in just 25 minutes—that’s up to 3× faster than with an original 5W charger.\r\n\r\nCompact and Portable\r\n\r\nTake this powerful charger with you wherever you go thanks to the miniature design.\r\n\r\nUniversal Compatibility\r\n\r\nCharge phones, tablets, earbuds, and more at top speed thanks to our signature PowerIQ 3.0 technology.\r\n\r\nCompatibility\r\n\r\niPhone iPhone 15 / 15 Pro / 14 / 14 Plus / 14 Pro / 14 Pro Max / 13 / 13 Mini / 13 Pro / 13 Pro Max / iPhone 12 / 12 mini / 12 Pro / 12 Pro Max / iPhone SE (2nd generation) /11 / 11 Pro / 11 Pro Max / XS / XS Max / XR / X / 8 Plus / 8;\r\n\r\nWireless Charger;\r\n\r\niPad 10.2-inch / iPad mini 8.3-inch / iPad Pro 12.9-inch 4th / 3rd / 2nd / 1st generation; iPad Pro 11-inch 2nd / 1st generation; iPad Pro 10.5-inch; iPad Air 4th / 3rd generation; Pad 8th / 7th generation; iPad mini 5th generation; AirPods.\r\n\r\nGalaxy S10 / S10+ / S10e / S9 / S9+ / S8 / S8+; Note 9 / 8; Pixel 3a / 3XL / 3 / 2 XL / 2, and More\r\n\r\nSpecs\r\n\r\nTotal Wattage: 20W\r\n\r\nInput: 100-240V 0.7A 50-60Hz\r\n\r\nOutput: 5V=3A/9V=2.22A\r\n\r\nWeight: 45 g\r\n\r\nDimensions: 32.3 × 31.8 × 33 mm / 1.23 × 1.25 × 1.30 in (not including the charging prongs)','prod_6589ebfb5313d.jpeg',5,1,1,1,'2023-12-21 22:21:25','2023-12-25 20:54:19','Anker'),(8,'apple-studio-display-27-170328','Apple Studio Display 27&quot;',149900,'Immersive 27-inch 5K Retina display with 600 nits of brightness, support for one billion colors, and P3 wide color*\r\n\r\n12MP Ultra Wide camera with Center Stage for more engaging video calls\r\n\r\nStudio-quality three-mic array for crystal-clear calls and voice recordings\r\n\r\nSix-speaker sound system with Spatial Audio for an unbelievable listening experience\r\n\r\nOne Thunderbolt 3 port, three USB-C ports\r\n\r\n96W of power delivery to charge your Mac notebook\r\n\r\nNano-texture glass option\r\n\r\nConfigurable stand options','prod_6589eb6276327.jpeg',3,1,1,1,'2023-12-22 21:28:19','2023-12-25 20:51:46','Apple'),(10,'dell-27-monitor-170328','Dell 27&quot; Monitor',20999,'Extensive connectivity: Easily connect to a variety of devices with extensive connectivity ports, including DisplayPort, HDMI, VGA and 4x SuperSpeed USB 5Gbps.\r\n\r\nQuick-access ports: Easily share and deliver content via quick-access ports conveniently placed at the front of the display.\r\n\r\nExpand your efficiency: The three-sided ultrathin bezel design lets you enjoy an uninterrupted view of your content across multiple monitors. Furthermore, you can boost your productivity by 21% with a dual monitor setup.','prod_6589ea8ce5a35.jpeg',3,1,0,1,'2023-12-22 21:32:01','2023-12-25 20:48:12','Dell');
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT 0,
  `perm` int(4) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_username` (`username`),
  UNIQUE KEY `uq_email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'John','Doe','admin','$2y$10$WZrQgcRceAnL298aGjgaJuA4ib0TPr0WKpXMo8Luq52ZxSht6Xu4K','admin@v.je',1,15,'2023-12-09 23:50:15','2023-12-25 20:50:15'),(2,'Ayodeji','Osasona','trulyao','$2y$10$HXAyRrP5B/4AlLO6Mx8QZuFW0g53/BGNl8jLzqsFYRNe.ylr9Dvrm','ayodeji@trulyao.dev',0,0,'2023-12-25 20:58:15','2023-12-25 20:58:15');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'eds_electronics'
--
