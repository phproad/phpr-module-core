CREATE TABLE `core_settings` (
  `id` int(11) NOT NULL auto_increment,
  `record_code` varchar(50) default NULL,
  `config_data` text,
  PRIMARY KEY  (`id`),
  KEY `record_code` (`record_code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

