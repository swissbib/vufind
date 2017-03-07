--
-- Table structure for table `swiss_national_licence`
--
use v3greentest;
DROP TABLE IF EXISTS `national_licence_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `national_licence_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `edu_id` varchar(255) NOT NULL,
  `persistent_id` varchar(255) NOT NULL,
  `user_id` int(11) DEFAULT NULL,

  `home_organization_type` varchar(255) DEFAULT NULL,
  `display_name` varchar(255) DEFAULT NULL,
  `mobile` VARCHAR(20) DEFAULT NULL,
  `home_postal_address` varchar(255) DEFAULT NULL,
  `affiliation` varchar(255) DEFAULT NULL,
  `swiss_library_person_residence` varchar(10) DEFAULT NULL,
  `assurance_level` varchar(255) DEFAULT NULL,

  `condition_accepted`  BOOLEAN DEFAULT FALSE,
  `request_temporary_access` BOOLEAN NOT NULL DEFAULT FALSE,
  `request_temporary_access_created` datetime DEFAULT NULL,
  `request_permanent_access` BOOLEAN NOT NULL DEFAULT FALSE,
  `request_permanent_access_created` datetime DEFAULT NULL,
  `date_expiration` datetime DEFAULT NULL,
  `blocked` BOOLEAN NOT NULL DEFAULT FALSE,
  `blocked_created` datetime DEFAULT NULL,
  `active_last_12_month` BOOLEAN NOT NULL DEFAULT FALSE,
  `last_account_extension_request` datetime DEFAULT NULL,
  `created` datetime  DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`user_id`) REFERENCES user(`id`),
  UNIQUE `edu_id` (`edu_id`),
  UNIQUE `persistent_id` (`persistent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;