-- MySQL dump 10.13  Distrib 5.7.17, for osx10.12 (x86_64)
--
-- Host: localhost    Database: images
-- ------------------------------------------------------
-- Server version	5.7.17

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
-- Table structure for table `ims_games`
--

DROP TABLE IF EXISTS `ims_games`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_games` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` date DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `type` char(10) NOT NULL,
  `remote_game_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nba_match_id_uindex` (`id`),
  UNIQUE KEY `ims_games_remote_game_id_uindex` (`remote_game_id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8 COMMENT='比赛';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ims_games_labels`
--

DROP TABLE IF EXISTS `ims_games_labels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_games_labels` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `game_id` int(10) unsigned DEFAULT NULL,
  `label_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ims_games_labels_id_uindex` (`id`),
  KEY `ims_games_labels_ims_games_id_fk` (`game_id`),
  KEY `ims_games_labels_ims_labels_id_fk` (`label_id`),
  CONSTRAINT `ims_games_labels_ims_games_id_fk` FOREIGN KEY (`game_id`) REFERENCES `ims_games` (`id`),
  CONSTRAINT `ims_games_labels_ims_labels_id_fk` FOREIGN KEY (`label_id`) REFERENCES `ims_labels` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='games与labels关联的中间表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ims_games_tags`
--

DROP TABLE IF EXISTS `ims_games_tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_games_tags` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `game_id` int(10) unsigned DEFAULT NULL,
  `tag_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ims_games_tags_id_uindex` (`id`),
  KEY `ims_games_tags_ims_games_id_fk` (`game_id`),
  KEY `ims_games_tags_ims_tags_id_fk` (`tag_id`),
  CONSTRAINT `ims_games_tags_ims_games_id_fk` FOREIGN KEY (`game_id`) REFERENCES `ims_games` (`id`),
  CONSTRAINT `ims_games_tags_ims_tags_id_fk` FOREIGN KEY (`tag_id`) REFERENCES `ims_tags` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='game的默认标签';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ims_images`
--

DROP TABLE IF EXISTS `ims_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_images` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL COMMENT '图片名称',
  `thumb` varchar(1024) DEFAULT NULL COMMENT '缩略图',
  `url` varchar(1024) DEFAULT NULL COMMENT '图片地址',
  `game_id` int(10) unsigned NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nba_images_id_uindex` (`id`),
  KEY `nba_images_nba_match_id_fk` (`game_id`),
  KEY `images_created_at_index` (`created_at`),
  KEY `images_updated_at_index` (`updated_at`),
  CONSTRAINT `nba_images_nba_match_id_fk` FOREIGN KEY (`game_id`) REFERENCES `ims_games` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=744 DEFAULT CHARSET=utf8 COMMENT='NBA图片资源';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ims_images_tags`
--

DROP TABLE IF EXISTS `ims_images_tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_images_tags` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `image_id` int(10) unsigned NOT NULL,
  `tag_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nba_images_tags_id_uindex` (`id`),
  KEY `nba_images_tags_nba_images_id_fk` (`image_id`),
  KEY `nba_images_tags_nba_tags_id_fk` (`tag_id`),
  CONSTRAINT `nba_images_tags_nba_images_id_fk` FOREIGN KEY (`image_id`) REFERENCES `ims_images` (`id`),
  CONSTRAINT `nba_images_tags_nba_tags_id_fk` FOREIGN KEY (`tag_id`) REFERENCES `ims_tags` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=305 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ims_labels`
--

DROP TABLE IF EXISTS `ims_labels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_labels` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ims_labels_id_uindex` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COMMENT='赛程标签';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ims_tags`
--

DROP TABLE IF EXISTS `ims_tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_tags` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `type` char(10) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nba_tags_id_uindex` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1085 DEFAULT CHARSET=utf8 COMMENT='NBA标签';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-04-24  9:35:55
