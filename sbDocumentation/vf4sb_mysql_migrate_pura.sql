--
-- Table structure for table `pura_user`
--
use v3greentest;
DROP TABLE IF EXISTS `pura_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pura_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `edu_id` varchar(255) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `libraries` text NOT NULL,
  `pura_barcode` varchar(255),

  /*specific for a library, maybe move to another table*/
  `remarks` text,
  `library_system_number` varchar(255), /*for example aleph number */
  `request_access` BOOLEAN NOT NULL DEFAULT FALSE,
  `request_access_created` datetime DEFAULT NULL,
  `date_expiration` datetime DEFAULT NULL,
  `blocked` BOOLEAN NOT NULL DEFAULT FALSE,
  `blocked_created` datetime DEFAULT NULL,
  `created` datetime  DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`user_id`) REFERENCES user(`id`),
  UNIQUE `edu_id` (`edu_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

INSERT INTO `pura_user`
(  `id`, `edu_id`, `user_id`, `request_access`, `request_access_created`, `date_expiration`, `blocked`, `blocked_created`, `created`)
VALUES
  ('1', '3213213213@eduid.ch', '413', '0', NULL, '2018-06-09 00:00:00', '0', NULL, CURRENT_TIMESTAMP)
