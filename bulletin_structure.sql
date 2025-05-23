-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: esbtp_new
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

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
-- Table structure for table `esbtp_bulletins`
--

DROP TABLE IF EXISTS `esbtp_bulletins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `esbtp_bulletins` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `etudiant_id` bigint(20) unsigned NOT NULL,
  `classe_id` bigint(20) unsigned NOT NULL,
  `annee_universitaire_id` bigint(20) unsigned NOT NULL,
  `periode` enum('semestre1','semestre2','annuel') NOT NULL DEFAULT 'semestre1',
  `moyenne_generale` decimal(5,2) DEFAULT NULL,
  `rang` int(11) DEFAULT NULL,
  `effectif_classe` int(11) DEFAULT NULL,
  `appreciation_generale` text DEFAULT NULL,
  `config_matieres` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`config_matieres`)),
  `professeurs` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`professeurs`)),
  `decision_conseil` varchar(191) DEFAULT NULL,
  `mention` varchar(191) DEFAULT NULL,
  `signature_directeur` tinyint(1) NOT NULL DEFAULT 0,
  `signature_responsable` tinyint(1) NOT NULL DEFAULT 0,
  `signature_parent` tinyint(1) NOT NULL DEFAULT 0,
  `date_signature_directeur` timestamp NULL DEFAULT NULL,
  `date_signature_responsable` timestamp NULL DEFAULT NULL,
  `date_signature_parent` timestamp NULL DEFAULT NULL,
  `is_published` tinyint(1) NOT NULL DEFAULT 0,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `bulletin_unique` (`etudiant_id`,`classe_id`,`annee_universitaire_id`,`periode`),
  KEY `esbtp_bulletins_classe_id_foreign` (`classe_id`),
  KEY `esbtp_bulletins_annee_universitaire_id_foreign` (`annee_universitaire_id`),
  KEY `esbtp_bulletins_user_id_foreign` (`user_id`),
  KEY `esbtp_bulletins_created_by_foreign` (`created_by`),
  KEY `esbtp_bulletins_updated_by_foreign` (`updated_by`),
  CONSTRAINT `esbtp_bulletins_annee_universitaire_id_foreign` FOREIGN KEY (`annee_universitaire_id`) REFERENCES `esbtp_annee_universitaires` (`id`) ON DELETE CASCADE,
  CONSTRAINT `esbtp_bulletins_classe_id_foreign` FOREIGN KEY (`classe_id`) REFERENCES `esbtp_classes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `esbtp_bulletins_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  CONSTRAINT `esbtp_bulletins_etudiant_id_foreign` FOREIGN KEY (`etudiant_id`) REFERENCES `esbtp_etudiants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `esbtp_bulletins_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`),
  CONSTRAINT `esbtp_bulletins_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `esbtp_bulletins`
--

LOCK TABLES `esbtp_bulletins` WRITE;
/*!40000 ALTER TABLE `esbtp_bulletins` DISABLE KEYS */;
INSERT INTO `esbtp_bulletins` VALUES (1,2,1,6,'semestre1',NULL,NULL,NULL,NULL,'\"{\\\"techniques\\\":[2],\\\"generales\\\":[]}\"',NULL,NULL,NULL,0,0,0,NULL,NULL,NULL,0,NULL,1,1,'2025-04-02 10:26:37','2025-04-02 19:44:50',NULL);
/*!40000 ALTER TABLE `esbtp_bulletins` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-04-02 19:54:35
