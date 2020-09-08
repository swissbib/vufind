--
-- Modifications to table `user`
--

ALTER TABLE user
  ADD COLUMN pending_email varchar(255) NOT NULL DEFAULT '';

ALTER TABLE user
  ADD COLUMN user_provided_email boolean NOT NULL DEFAULT '0';

ALTER TABLE user
  ADD COLUMN last_language varchar(30) NOT NULL DEFAULT '';

--
-- Modifications to table `search`
--

ALTER TABLE search
  modify id bigint;

--
-- Modifications to table `session`
--

ALTER TABLE session
  modify id bigint;

--
-- Modifications to table `external_session`
--

ALTER TABLE external_session
  modify id bigint;


--
-- Modifications to table `search`
--

ALTER TABLE search
  ADD COLUMN notification_frequency int(11) NOT NULL DEFAULT '0',
  ADD COLUMN last_notification_sent datetime NOT NULL DEFAULT '2000-01-01 00:00:00',
  ADD COLUMN notification_base_url varchar(255) NOT NULL DEFAULT '';

CREATE INDEX notification_frequency_idx ON search (notification_frequency);
CREATE INDEX notification_base_url_idx ON search (notification_base_url);


--
-- Table structure for table `auth_hash`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `auth_hash` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `session_id` varchar(128) DEFAULT NULL,
  `hash` varchar(255) NOT NULL DEFAULT '',
  `type` varchar(50) DEFAULT NULL,
  `data` mediumtext,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `session_id` (`session_id`),
  UNIQUE KEY `hash_type` (`hash`, `type`),
  KEY `created` (`created`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;


ALTER TABLE shortlinks
    ADD COLUMN hash varchar(32);
CREATE UNIQUE INDEX shortlinks_hash_idx ON shortlinks (hash);