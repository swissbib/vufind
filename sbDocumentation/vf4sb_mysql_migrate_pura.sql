--
-- Table structure for table `pura_user`
--
use v4greentest;
DROP TABLE IF EXISTS `pura_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pura_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `edu_id` varchar(255) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `barcode` varchar(255),
  `library_code` varchar(255),
  `remarks` text,
  `library_system_number` varchar(255), /*for example aleph number */
  `has_access` BOOLEAN NOT NULL DEFAULT FALSE,
  `access_created` datetime DEFAULT NULL,
  `date_expiration` datetime DEFAULT NULL,
  `blocked` BOOLEAN NOT NULL DEFAULT FALSE,
  `blocked_created` datetime DEFAULT NULL,
  `last_account_extension_request` datetime DEFAULT NULL,
  `created` datetime  DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `barcode` (`barcode`),
  FOREIGN KEY (`user_id`) REFERENCES user(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
GRANT INSERT, SELECT, UPDATE, DELETE ON pura_user to 'pura-back-end'@'%';
GRANT SELECT ON user to 'pura-back-end'@'%';