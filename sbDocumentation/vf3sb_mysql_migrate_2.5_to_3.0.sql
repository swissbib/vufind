-- ---------------------------------------------------------------------------
-- this script migrates the sb-mysql-db (prod green) to VF3.0 mysql-schema --
-- ---------------------------------------------------------------------------

alter table `comments`
modify `created` datetime NOT NULL DEFAULT '2000-00-00 00:00:00';

alter table `oai_resumption`
modify `expires` datetime NOT NULL DEFAULT '2000-00-00 00:00:00';

alter table `resource`
modify `record_id` varchar(255) NOT NULL DEFAULT '',
modify `source` varchar(50) NOT NULL DEFAULT 'Solr';

alter table `search`
modify `created` datetime NOT NULL DEFAULT '2000-00-00 00:00:00',
add `checksum` int(11) default NULL after `search_object`;

alter table `session`
modify `created` datetime NOT NULL DEFAULT '2000-00-00 00:00:00';

alter table `user`
modify `username` varchar(255) NOT NULL DEFAULT '',
modify `email` varchar(255) NOT NULL DEFAULT '',
modify `cat_password` varchar(70) DEFAULT NULL,
modify `cat_pass_enc` varchar(170) DEFAULT NULL,
modify `created` datetime NOT NULL DEFAULT '2000-00-00 00:00:00',
drop `sb_nickname`;

alter table `user_list`
add `expires` datetime NOT NULL DEFAULT '2000-00-00 00:00:00';


drop table `user_localdata`;

--
-- Table structure for table `user_card`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_card` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `card_name` varchar(255) NOT NULL DEFAULT '',
  `cat_username` varchar(50) NOT NULL DEFAULT '',
  `cat_password` varchar(50) DEFAULT NULL,
  `cat_pass_enc` varchar(110) DEFAULT NULL,
  `home_library` varchar(100) NOT NULL DEFAULT '',
  `created` datetime NOT NULL DEFAULT '2000-01-01 00:00:00',
  `saved` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `user_card_cat_username` (`cat_username`),
  CONSTRAINT `user_card_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;




--
-- Table structure for table `record`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `record` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `record_id` varchar(255) DEFAULT NULL,
  `source` varchar(50) DEFAULT NULL,
  `version` varchar(20) NOT NULL,
  `data` longtext DEFAULT NULL,
  `updated` datetime NOT NULL DEFAULT '2000-01-01 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `record_id_source` (`record_id`, `source`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
