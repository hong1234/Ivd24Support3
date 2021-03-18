-- MySQL dump 10.13  Distrib 5.7.17, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: db001_portal
-- ------------------------------------------------------
-- Server version	5.5.5-10.4.14-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `roles` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`roles`)),
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_8D93D649E7927C74` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1,'vuanhde@yahoo.de','[\"ROLE_ADMIN\"]','$argon2id$v=19$m=65536,t=4,p=1$a3kzY2RjQ3IySXpPZW80Lg$W/oNbKLn0TCHVbUd/LOOQp6aT3AuFT/VPaKyxa62HWw',0),(2,'hong2@yahoo.de','[\"ROLE_SUPPORT\"]','$argon2id$v=19$m=65536,t=4,p=1$T0g2SHQxVnZEd0hOTm9vbQ$ZKMDWKOMarufgjUZHZHJHXJUsjURdWdqxsajtAyd+1U',0),(3,'hong3@yahoo.de','[\"ROLE_SUPPORT\"]','$argon2id$v=19$m=65536,t=4,p=1$WFZRRS9UY3ZEY21TQnFxcg$52gIPsgUQxJ5e7nUcwy9/qXqNhjqqP0WQhdh6eXKE/o',109360),(4,'hong4@yahoo.de','[\"ROLE_STATISTIC\"]','$argon2id$v=19$m=65536,t=4,p=1$cUh0M1hZSHdaRjV1MlJ3eA$4KIor17RLozFyvndi/WcdOn8hufiT01krWCM0E+RWO0',109361),(5,'hong5@yahoo.de','[\"ROLE_STATISTIC\"]','$argon2id$v=19$m=65536,t=4,p=1$T2dQcFZ6MnJpamJJUFNxRA$NFByH9fu3eXPPk+MgU1qIMp2FvnOgD9ktQLnc8Ok+dA',109362);
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'db001_portal'
--

--
-- Dumping routines for database 'db001_portal'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2021-03-14 14:07:33
