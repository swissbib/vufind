-- -------------------------------------
--
-- modifies swissbib DB from vf3 to vf4
--
-- -------------------------------------


--
-- set utf8 as default character set
--
/*!40101 SET NAMES utf8 */;



--
-- drop unused tables:
--
drop table user_stats;
drop table user_stats_fields;



--
-- create new table `external_session`
--
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `external_session` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `session_id` varchar(128) NOT NULL,
  `external_session_id` varchar(255) NOT NULL,
  `created` datetime NOT NULL DEFAULT '2000-01-01 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `session_id` (`session_id`),
  KEY `external_session_id` (`external_session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;



--
-- alter columns
--
ALTER TABLE `comments` MODIFY `created` datetime NOT NULL DEFAULT '2000-01-01 00:00:00';
ALTER TABLE `oai_resumption` MODIFY `expires` datetime NOT NULL DEFAULT '2000-01-01 00:00:00';
ALTER TABLE `record` MODIFY `data` longtext DEFAULT NULL;
ALTER TABLE `resource` MODIFY `title` varchar(255) NOT NULL DEFAULT '';
ALTER TABLE `resource` MODIFY `author` varchar(255) DEFAULT NULL;
ALTER TABLE `search` MODIFY `created` datetime NOT NULL DEFAULT '2000-01-01 00:00:00';
ALTER TABLE `session` MODIFY `created` datetime NOT NULL DEFAULT '2000-01-01 00:00:00';
ALTER TABLE `user` MODIFY `created` datetime NOT NULL DEFAULT '2000-01-01 00:00:00';
ALTER TABLE `user_list` MODIFY `created` datetime NOT NULL DEFAULT '2000-01-01 00:00:00';



--
-- add columns
--
ALTER TABLE `user` ADD COLUMN `cat_id` varchar(255) DEFAULT NULL;



--
-- modify tables
--
ALTER TABLE `user` ADD CONSTRAINT `cat_id` UNIQUE (`cat_id`);
