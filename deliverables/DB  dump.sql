-- MySQL dump 10.13  Distrib 9.0.1, for macos15.0 (arm64)
--
-- Host: localhost    Database: Aragon
-- ------------------------------------------------------
-- Server version	9.1.0

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `carts`
--

DROP TABLE IF EXISTS `carts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `carts` (
  `cart_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `product_id` int NOT NULL,
  `cart_quantity` int NOT NULL,
  PRIMARY KEY (`cart_id`),
  KEY `carts_products_FK` (`product_id`),
  KEY `carts_users_FK` (`user_id`),
  CONSTRAINT `carts_products_FK` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`),
  CONSTRAINT `carts_users_FK` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `carts`
--

LOCK TABLES `carts` WRITE;
/*!40000 ALTER TABLE `carts` DISABLE KEYS */;
INSERT INTO `carts` VALUES (1,5,6,2),(3,5,7,2),(4,5,8,2),(5,5,9,2),(6,5,10,2),(7,5,11,2),(8,5,12,2),(9,5,13,2),(10,5,14,2),(11,5,15,2),(12,5,16,2),(13,5,17,2),(14,5,18,2),(15,5,19,2),(16,5,20,2),(17,5,21,2),(18,5,22,2),(19,5,23,2);
/*!40000 ALTER TABLE `carts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `genders`
--

DROP TABLE IF EXISTS `genders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `genders` (
  `gender_id` int NOT NULL AUTO_INCREMENT,
  `gender_name` varchar(100) NOT NULL,
  PRIMARY KEY (`gender_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `genders`
--

LOCK TABLES `genders` WRITE;
/*!40000 ALTER TABLE `genders` DISABLE KEYS */;
INSERT INTO `genders` VALUES (1,'Unisex'),(2,'Male'),(3,'Female');
/*!40000 ALTER TABLE `genders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `item_in_order`
--

DROP TABLE IF EXISTS `item_in_order`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `item_in_order` (
  `order_id` int NOT NULL,
  `product_id` int NOT NULL,
  `quantity` int NOT NULL,
  PRIMARY KEY (`order_id`,`product_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `item_in_order_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`),
  CONSTRAINT `item_in_order_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `item_in_order`
--

LOCK TABLES `item_in_order` WRITE;
/*!40000 ALTER TABLE `item_in_order` DISABLE KEYS */;
INSERT INTO `item_in_order` VALUES (4,22,2),(5,23,2);
/*!40000 ALTER TABLE `item_in_order` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notes`
--

DROP TABLE IF EXISTS `notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notes` (
  `note_name` varchar(100) NOT NULL,
  `note_id` int NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`note_id`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notes`
--

LOCK TABLES `notes` WRITE;
/*!40000 ALTER TABLE `notes` DISABLE KEYS */;
INSERT INTO `notes` VALUES ('Vanilla',1),('Citrus',2),('Wood',3),('Oud',4),('Leather',5),('Vanilla',6),('Vanilla',7),('Vanilla',8),('Vanilla',9),('Vanilla',10),('Vanilla',11),('Vanilla',12),('Vanilla',13),('Vanilla',14),('Vanilla',15),('Vanilla',16),('Vanilla',17),('Vanilla',18),('Vanilla',19),('Vanilla',20),('Vanilla',21),('Vanilla',22),('Vanilla',23),('Bergamot',25),('Bergamot',27),('Bergamot',29),('Bergamot',30),('Bergamot',32);
/*!40000 ALTER TABLE `notes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders` (
  `order_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `order_address` varchar(100) NOT NULL,
  `order_city` varchar(100) NOT NULL,
  `order_country` varchar(100) NOT NULL,
  `order_phone` int NOT NULL,
  `order_date` date NOT NULL,
  `status_id` int NOT NULL,
  `order_desc` varchar(100) DEFAULT NULL,
  `total_price` decimal(10,0) NOT NULL,
  PRIMARY KEY (`order_id`),
  KEY `orders_statuses_FK` (`status_id`),
  KEY `orders_users_FK` (`user_id`),
  CONSTRAINT `orders_statuses_FK` FOREIGN KEY (`status_id`) REFERENCES `statuses` (`status_id`),
  CONSTRAINT `orders_users_FK` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES (1,5,'123 Test Street, Scentville','Testville','Perfumeland',123456789,'2025-04-07',1,NULL,120),(2,5,'123 Test Street, Scentville','Testville','Perfumeland',123456789,'2025-04-07',1,NULL,120),(3,5,'123 Test Street, Scentville','Testville','Perfumeland',123456789,'2025-04-07',1,NULL,120),(4,5,'123 Test Street, Scentville','Testville','Perfumeland',123456789,'2025-04-07',1,NULL,120),(5,5,'123 Test Street, Scentville','Testville','Perfumeland',123456789,'2025-04-07',1,NULL,120),(8,22,'Payment Blvd 123','PayCity','Payland',999888777,'2025-04-07',1,NULL,150),(9,22,'Payment Blvd 123','PayCity','Payland',999888777,'2025-04-07',1,NULL,150),(10,22,'Payment Blvd 123','PayCity','Payland',999888777,'2025-04-07',1,NULL,150),(11,22,'Payment Blvd 123','PayCity','Payland',999888777,'2025-04-06',1,NULL,150),(12,22,'Payment Blvd 123','PayCity','Payland',999888777,'2025-04-06',1,NULL,150),(13,22,'Payment Blvd 123','PayCity','Payland',999888777,'2025-04-07',1,NULL,150),(14,22,'Payment Blvd 123','PayCity','Payland',999888777,'2025-04-07',1,NULL,150);
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payments` (
  `payment_id` int NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL,
  `payment_method` enum('card','cash') NOT NULL,
  `payment_status` varchar(50) DEFAULT 'pending',
  `payment_date` datetime DEFAULT NULL,
  PRIMARY KEY (`payment_id`),
  KEY `order_id` (`order_id`),
  CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_note`
--

DROP TABLE IF EXISTS `product_note`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_note` (
  `product_id` int NOT NULL,
  `note_id` int NOT NULL,
  PRIMARY KEY (`product_id`,`note_id`),
  KEY `note_id` (`note_id`),
  CONSTRAINT `product_note_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`),
  CONSTRAINT `product_note_ibfk_2` FOREIGN KEY (`note_id`) REFERENCES `notes` (`note_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_note`
--

LOCK TABLES `product_note` WRITE;
/*!40000 ALTER TABLE `product_note` DISABLE KEYS */;
INSERT INTO `product_note` VALUES (6,6),(7,7),(8,8),(9,9),(10,10),(11,11),(12,12),(13,13),(14,14),(15,15),(16,16),(17,17),(18,18),(19,19),(20,20),(21,21),(22,22),(23,23),(30,25),(31,27),(32,29);
/*!40000 ALTER TABLE `product_note` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `products` (
  `product_id` int NOT NULL AUTO_INCREMENT,
  `product_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `product_note` varchar(100) NOT NULL,
  `price` decimal(10,0) NOT NULL,
  `gender_id` int NOT NULL,
  `quantity` int NOT NULL,
  `description` text,
  `image_url` varchar(100) NOT NULL,
  `on_sale` tinyint(1) NOT NULL,
  PRIMARY KEY (`product_id`),
  KEY `products_genders_FK` (`gender_id`),
  CONSTRAINT `products_genders_FK` FOREIGN KEY (`gender_id`) REFERENCES `genders` (`gender_id`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (6,'Golden Scent','Vanilla',60,1,50,'Warm and luxurious.','golden.png',0),(7,'Golden Scent','Vanilla',60,1,50,'Warm and luxurious.','golden.png',0),(8,'Golden Scent','Vanilla',60,1,50,'Warm and luxurious.','golden.png',0),(9,'Golden Scent','Vanilla',60,1,50,'Warm and luxurious.','golden.png',0),(10,'Golden Scent','Vanilla',60,1,50,'Warm and luxurious.','golden.png',0),(11,'Golden Scent','Vanilla',60,1,50,'Warm and luxurious.','golden.png',0),(12,'Golden Scent','Vanilla',60,1,50,'Warm and luxurious.','golden.png',0),(13,'Golden Scent','Vanilla',60,1,50,'Warm and luxurious.','golden.png',0),(14,'Golden Scent','Vanilla',60,1,50,'Warm and luxurious.','golden.png',0),(15,'Golden Scent','Vanilla',60,1,50,'Warm and luxurious.','golden.png',0),(16,'Golden Scent','Vanilla',60,1,50,'Warm and luxurious.','golden.png',0),(17,'Golden Scent','Vanilla',60,1,50,'Warm and luxurious.','golden.png',0),(18,'Golden Scent','Vanilla',60,1,50,'Warm and luxurious.','golden.png',0),(19,'Golden Scent','Vanilla',60,1,50,'Warm and luxurious.','golden.png',0),(20,'Golden Scent','Vanilla',60,1,50,'Warm and luxurious.','golden.png',0),(21,'Golden Scent','Vanilla',60,1,50,'Warm and luxurious.','golden.png',0),(22,'Golden Scent','Vanilla',60,1,50,'Warm and luxurious.','golden.png',0),(23,'Golden Scent','Vanilla',60,1,50,'Warm and luxurious.','golden.png',0),(30,'Note Bound Perfume','Mixed',85,2,20,'A complex blend.','blend.png',0),(31,'Note Bound Perfume','Mixed',85,2,20,'A complex blend.','blend.png',0),(32,'Note Bound Perfume','Mixed',85,2,20,'A complex blend.','blend.png',0);
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `role_id` int NOT NULL AUTO_INCREMENT,
  `role_name` varchar(100) NOT NULL,
  PRIMARY KEY (`role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'admin'),(2,'user');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `statuses`
--

DROP TABLE IF EXISTS `statuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `statuses` (
  `status_id` int NOT NULL AUTO_INCREMENT,
  `status_name` varchar(100) NOT NULL,
  PRIMARY KEY (`status_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `statuses`
--

LOCK TABLES `statuses` WRITE;
/*!40000 ALTER TABLE `statuses` DISABLE KEYS */;
INSERT INTO `statuses` VALUES (1,'Pending'),(2,'Shipped'),(3,'Delivered');
/*!40000 ALTER TABLE `statuses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `user_id` int NOT NULL AUTO_INCREMENT,
  `user_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `image_url` varchar(100) DEFAULT NULL,
  `address` text,
  `role_id` int NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `users_unique` (`email`),
  KEY `users_roles_FK` (`role_id`),
  CONSTRAINT `users_roles_FK` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (5,'Test User','test@shop.com','test123','user.png','Test Address',2,'2025-04-07 01:37:51'),(9,'Test User','tesst@shop.com','test123','user.png','Test Address',2,'2025-04-07 01:37:51'),(16,'Order User','orderuser@example.com','orderpass','order.png','Order Street',2,'2025-04-07 01:37:51'),(22,'Payment User','paymentuser@example.com','paypass','pay.png','Pay Street',2,'2025-04-07 01:53:29');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'Aragon'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-04-07  2:32:09
