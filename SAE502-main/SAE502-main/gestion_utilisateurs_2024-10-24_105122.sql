/*!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19  Distrib 10.6.18-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: 127.0.0.1    Database: gestion_utilisateurs
-- ------------------------------------------------------
-- Server version	10.6.18-MariaDB-0ubuntu0.22.04.1

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
-- Table structure for table `tickets`
--

DROP TABLE IF EXISTS `tickets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tickets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date_creation` timestamp NOT NULL DEFAULT current_timestamp(),
  `numero` varchar(255) NOT NULL,
  `texte` text NOT NULL,
  `rapporteur` varchar(255) NOT NULL,
  `role` text NOT NULL,
  `urgence` enum('faible','moyen','eleve') NOT NULL,
  `nom_client` text NOT NULL,
  `nom_entreprise` text NOT NULL,
  `titre_ticket` text NOT NULL,
  `assigne_a` text DEFAULT NULL,
  `etat` text DEFAULT NULL,
  `date_assignation` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=69 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tickets`
--

/*!40000 ALTER TABLE `tickets` DISABLE KEYS */;
INSERT INTO `tickets` VALUES (30,'2024-09-18 08:06:56','INC-000001','gfbf','dev1','dev','moyen','fffff','ffffff','fffff','dev1','en_attente','2024-09-18 08:53:25'),(31,'2024-09-18 08:07:02','INC-000002','gfbf','dev1','dev','moyen','fffff','ffffff','fffff','dev1','termine','2024-09-18 08:22:57'),(32,'2024-09-18 08:08:03','INC-000003','gfbf','dev1','dev','moyen','fffff','ffffff','fffff','dev1','en_cours','2024-09-18 08:23:01'),(33,'2024-09-18 08:09:55','INC-000004','gfbf','dev1','dev','moyen','fffff','ffffff','fffff','dev1','en_cours','2024-09-18 08:23:04'),(34,'2024-09-18 08:10:34','INC-000005','gfbf','dev1','dev','moyen','fffff','ffffff','fffff','dev1','en_cours','2024-09-18 08:23:06'),(37,'2024-09-18 08:24:04','INC-000008','egqgdgsfdg','dev1','dev','faible','sdfh','sdfh','sfdhfshsf',NULL,NULL,'0000-00-00 00:00:00'),(38,'2024-09-18 08:24:09','INC-000009','egqgdgsfdg','dev1','dev','faible','sdfh','sdfh','sfdhfshsf',NULL,NULL,'0000-00-00 00:00:00'),(39,'2024-09-18 08:24:19','INC-000010','egqgdgsfdg','dev1','dev','faible','sdfh','sdfh','sfdhfshsf',NULL,NULL,'0000-00-00 00:00:00'),(40,'2024-09-18 08:24:44','INC-000011','egqgdgsfdg','dev1','dev','faible','sdfh','sdfh','sfdhfshsf',NULL,NULL,'0000-00-00 00:00:00'),(41,'2024-09-18 08:26:13','INC-000012','egqgdgsfdg','dev1','dev','faible','sdfh','sdfh','sfdhfshsf',NULL,NULL,'0000-00-00 00:00:00'),(42,'2024-09-18 08:26:28','INC-000013','egqgdgsfdg','dev1','dev','faible','sdfh','sdfh','sfdhfshsf',NULL,NULL,'0000-00-00 00:00:00'),(43,'2024-09-18 08:26:46','INC-000014','egqgdgsfdg','dev1','dev','faible','sdfh','sdfh','sfdhfshsf',NULL,NULL,'0000-00-00 00:00:00'),(44,'2024-09-18 08:26:57','INC-000015','egqgdgsfdg','dev1','dev','faible','sdfh','sdfh','sfdhfshsf',NULL,NULL,'0000-00-00 00:00:00'),(45,'2024-09-18 08:27:30','INC-000016','egqgdgsfdg','dev1','dev','faible','sdfh','sdfh','sfdhfshsf',NULL,NULL,'0000-00-00 00:00:00'),(46,'2024-09-18 08:29:46','INC-000017','egqgdgsfdg','dev1','dev','faible','sdfh','sdfh','sfdhfshsf',NULL,NULL,'0000-00-00 00:00:00'),(47,'2024-09-18 08:41:34','INC-000018','egqgdgsfdg','dev1','dev','faible','sdfh','sdfh','sfdhfshsf','dev1','en_cours','2024-09-18 09:00:54'),(48,'2024-09-18 08:53:12','INC-000019','egqgdgsfdg','dev1','dev','faible','sdfh','sdfh','sfdhfshsf',NULL,NULL,'0000-00-00 00:00:00'),(49,'2024-09-18 08:53:27','INC-000020','egqgdgsfdg','dev1','dev','faible','sdfh','sdfh','sfdhfshsf',NULL,NULL,'0000-00-00 00:00:00'),(50,'2024-09-18 08:53:54','INC-000021','egqgdgsfdg','dev1','dev','faible','sdfh','sdfh','sfdhfshsf','dev1','en_cours','2024-09-18 09:00:41'),(51,'2024-09-18 08:54:12','INC-000022','egqgdgsfdg','dev1','dev','faible','sdfh','sdfh','sfdhfshsf',NULL,NULL,'0000-00-00 00:00:00'),(52,'2024-09-18 09:00:29','INC-000023','egqgdgsfdg','dev1','dev','faible','sdfh','sdfh','sfdhfshsf',NULL,NULL,'0000-00-00 00:00:00'),(53,'2024-09-18 09:00:43','INC-000024','egqgdgsfdg','dev1','dev','faible','sdfh','sdfh','sfdhfshsf','dev1','en_cours','2024-09-18 09:17:08'),(54,'2024-09-18 09:00:50','INC-000025','egqgdgsfdg','dev1','dev','faible','sdfh','sdfh','sfdhfshsf','dev1','en_cours','2024-09-18 09:17:18'),(55,'2024-09-18 09:00:57','INC-000026','egqgdgsfdg','dev1','dev','faible','sdfh','sdfh','sfdhfshsf',NULL,NULL,'0000-00-00 00:00:00'),(56,'2024-09-18 09:01:05','INC-000027','egqgdgsfdg','dev1','dev','faible','sdfh','sdfh','sfdhfshsf',NULL,NULL,'0000-00-00 00:00:00'),(57,'2024-09-18 09:16:59','INC-000028','egqgdgsfdg','dev1','dev','faible','sdfh','sdfh','sfdhfshsf',NULL,NULL,'0000-00-00 00:00:00'),(58,'2024-09-18 09:17:10','INC-000029','egqgdgsfdg','dev1','dev','faible','sdfh','sdfh','sfdhfshsf',NULL,NULL,'0000-00-00 00:00:00'),(59,'2024-09-18 09:17:21','INC-000030','egqgdgsfdg','dev1','dev','faible','sdfh','sdfh','sfdhfshsf','dev1','en_cours','2024-09-18 09:18:20'),(60,'2024-09-18 09:18:23','INC-000031','egqgdgsfdg','dev1','dev','faible','sdfh','sdfh','sfdhfshsf',NULL,NULL,'0000-00-00 00:00:00'),(61,'2024-09-18 09:26:23','INC-000032','egqgdgsfdg','dev1','dev','faible','sdfh','sdfh','sfdhfshsf',NULL,NULL,'0000-00-00 00:00:00'),(62,'2024-09-18 09:26:33','INC-000033','egqgdgsfdg','dev1','dev','faible','sdfh','sdfh','sfdhfshsf',NULL,NULL,'0000-00-00 00:00:00'),(63,'2024-09-18 09:28:27','INC-000034','egqgdgsfdg','dev1','dev','faible','sdfh','sdfh','sfdhfshsf',NULL,NULL,'0000-00-00 00:00:00'),(64,'2024-09-18 09:28:37','INC-000035','egqgdgsfdg','dev1','dev','faible','sdfh','sdfh','sfdhfshsf',NULL,NULL,'0000-00-00 00:00:00'),(65,'2024-09-18 09:29:38','INC-000036','egqgdgsfdg','dev1','dev','faible','sdfh','sdfh','sfdhfshsf',NULL,NULL,'0000-00-00 00:00:00'),(66,'2024-09-18 09:36:56','INC-000037','egqgdgsfdg','dev1','dev','faible','sdfh','sdfh','sfdhfshsf',NULL,NULL,'0000-00-00 00:00:00'),(67,'2024-09-18 09:37:02','INC-000038','egqgdgsfdg','dev1','dev','faible','sdfh','sdfh','sfdhfshsf',NULL,NULL,'0000-00-00 00:00:00'),(68,'2024-09-18 09:37:31','INC-000039','egqgdgsfdg','dev1','dev','faible','sdfh','sdfh','sfdhfshsf',NULL,NULL,'0000-00-00 00:00:00');
/*!40000 ALTER TABLE `tickets` ENABLE KEYS */;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('rapporteur','dev','moderateur') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'rapporteur1','rapporteur1','rapporteur'),(2,'dev1','dev1','dev'),(3,'moderateur1','moderateur1','moderateur');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;

--
-- Dumping routines for database 'gestion_utilisateurs'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-10-24 10:51:42
