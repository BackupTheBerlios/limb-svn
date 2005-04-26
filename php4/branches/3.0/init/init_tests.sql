/* 
SQLyog v3.63
Host - 192.168.0.10 : Database - limb30_tests
**************************************************************
Server version 4.0.22
*/

create database if not exists `limb30_tests`;

use `limb30_tests`;

/*
Table struture for file_object
*/

drop table if exists `file_object`;
CREATE TABLE `file_object` (
  `id` bigint(20) NOT NULL auto_increment,
  `description` varchar(255) default NULL,
  `media_id` varchar(32) NOT NULL default '',
  `oid` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `mid` (`media_id`),
  KEY `oid` (`oid`,`media_id`,`id`)
) TYPE=InnoDB COMMENT='InnoDB free: 7168 kB; InnoDB free: 114688 kB; InnoDB free: 1';

/*
Table struture for image_object
*/

drop table if exists `image_object`;
CREATE TABLE `image_object` (
  `id` int(11) unsigned NOT NULL default '0',
  `description` text,
  `title` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=InnoDB COMMENT='InnoDB free: 9216 kB; InnoDB free: 114688 kB; InnoDB free: 1';

/*
Table struture for image_variation
*/

drop table if exists `image_variation`;
CREATE TABLE `image_variation` (
  `id` int(11) NOT NULL auto_increment,
  `image_id` int(11) unsigned NOT NULL default '0',
  `media_id` int(32) NOT NULL default '0',
  `width` int(11) unsigned NOT NULL default '0',
  `height` int(11) unsigned NOT NULL default '0',
  `variation` varchar(50) default NULL,
  PRIMARY KEY  (`id`),
  KEY `imid` (`image_id`),
  KEY `mid` (`media_id`),
  KEY `v` (`variation`)
) TYPE=InnoDB COMMENT='InnoDB free: 9216 kB; InnoDB free: 114688 kB; InnoDB free: 1';

/*
Table struture for media
*/

drop table if exists `media`;
CREATE TABLE `media` (
  `media_file_id` varchar(32) NOT NULL default '',
  `file_name` varchar(255) default NULL,
  `mime_type` varchar(100) NOT NULL default '',
  `size` int(10) unsigned default NULL,
  `etag` varchar(32) default NULL,
  `id` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`id`)
) TYPE=InnoDB COMMENT='InnoDB free: 9216 kB; InnoDB free: 114688 kB; InnoDB free: 1';

/*
Table struture for stats_counter
*/

drop table if exists `stats_counter`;
CREATE TABLE `stats_counter` (
  `id` int(11) NOT NULL auto_increment,
  `hosts_all` int(11) NOT NULL default '0',
  `hits_all` int(11) NOT NULL default '0',
  `hosts_today` int(11) NOT NULL default '0',
  `hits_today` int(11) NOT NULL default '0',
  `time` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=InnoDB;

/*
Table struture for stats_day_counters
*/

drop table if exists `stats_day_counters`;
CREATE TABLE `stats_day_counters` (
  `id` int(11) NOT NULL auto_increment,
  `time` int(11) NOT NULL default '0',
  `hits` int(11) NOT NULL default '0',
  `hosts` int(11) NOT NULL default '0',
  `home_hits` int(11) NOT NULL default '0',
  `audience` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=InnoDB;

/*
Table struture for stats_hit
*/

drop table if exists `stats_hit`;
CREATE TABLE `stats_hit` (
  `id` int(11) NOT NULL auto_increment,
  `stats_referer_id` int(11) NOT NULL default '0',
  `time` int(11) NOT NULL default '0',
  `ip` varchar(8) NOT NULL default '0',
  `action` varchar(50) default NULL,
  `session_id` varchar(50) NOT NULL default '',
  `stats_uri_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `complex` (`time`,`stats_uri_id`)
) TYPE=InnoDB ROW_FORMAT=DYNAMIC COMMENT='InnoDB free: 9216 kB';

/*
Table struture for stats_ip
*/

drop table if exists `stats_ip`;
CREATE TABLE `stats_ip` (
  `id` varchar(8) NOT NULL default '0',
  `time` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=InnoDB;

/*
Table struture for stats_referer_url
*/

drop table if exists `stats_referer_url`;
CREATE TABLE `stats_referer_url` (
  `id` int(11) NOT NULL auto_increment,
  `referer_url` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `url` (`referer_url`)
) TYPE=InnoDB;

/*
Table struture for stats_search_phrase
*/

drop table if exists `stats_search_phrase`;
CREATE TABLE `stats_search_phrase` (
  `id` int(11) NOT NULL auto_increment,
  `phrase` varchar(255) NOT NULL default '',
  `engine` varchar(255) NOT NULL default '',
  `time` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=InnoDB;

/*
Table struture for stats_uri
*/

drop table if exists `stats_uri`;
CREATE TABLE `stats_uri` (
  `id` int(11) NOT NULL auto_increment,
  `uri` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=InnoDB;

/*
Table struture for sys_behaviour
*/

drop table if exists `sys_behaviour`;
CREATE TABLE `sys_behaviour` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(100) NOT NULL default '',
  `icon` varchar(50) NOT NULL default '',
  `sort_order` smallint(6) NOT NULL default '0',
  `can_be_parent` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `class` (`name`)
) TYPE=InnoDB;

/*
Table struture for sys_class
*/

drop table if exists `sys_class`;
CREATE TABLE `sys_class` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `class` (`name`)
) TYPE=InnoDB COMMENT='InnoDB free: 10240 kB; InnoDB free: 10240 kB; InnoDB free: 1';

/*
Table struture for sys_current_version
*/

drop table if exists `sys_current_version`;
CREATE TABLE `sys_current_version` (
  `revision_object_id` int(11) NOT NULL default '0',
  `version_session_id` int(11) NOT NULL default '0',
  `id` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`id`),
  KEY `vo` (`revision_object_id`,`version_session_id`)
) TYPE=MyISAM;

/*
Table struture for sys_object
*/

drop table if exists `sys_object`;
CREATE TABLE `sys_object` (
  `oid` int(11) NOT NULL default '0',
  `class_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`oid`),
  KEY `class_id` (`class_id`)
) TYPE=MyISAM;

/*
Table struture for sys_object_metadata
*/

drop table if exists `sys_object_metadata`;
CREATE TABLE `sys_object_metadata` (
  `oid` bigint(20) NOT NULL default '0',
  `title` varchar(255) default NULL,
  `keywords` varchar(255) default NULL,
  `description` text,
  PRIMARY KEY  (`oid`)
) TYPE=MyISAM;

/*
Table struture for sys_object_to_node
*/

drop table if exists `sys_object_to_node`;
CREATE TABLE `sys_object_to_node` (
  `id` int(11) NOT NULL auto_increment,
  `oid` int(11) NOT NULL default '0',
  `node_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `uid` (`oid`,`node_id`)
) TYPE=MyISAM;

/*
Table struture for sys_object_to_service
*/

drop table if exists `sys_object_to_service`;
CREATE TABLE `sys_object_to_service` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `service_id` int(11) NOT NULL default '0',
  `oid` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `idccv` (`oid`,`id`,`service_id`)
) TYPE=InnoDB;

/*
Table struture for sys_service
*/

drop table if exists `sys_service`;
CREATE TABLE `sys_service` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `class` (`name`)
) TYPE=InnoDB;

/*
Table struture for sys_session
*/

drop table if exists `sys_session`;
CREATE TABLE `sys_session` (
  `session_id` varchar(50) NOT NULL default '',
  `session_data` blob NOT NULL,
  `last_activity_time` bigint(11) unsigned default NULL,
  `user_id` bigint(20) default NULL,
  PRIMARY KEY  (`session_id`),
  KEY `user_id` (`user_id`)
) TYPE=InnoDB COMMENT='InnoDB free: 10240 kB; InnoDB free: 114688 kB; InnoDB free: ';

/*
Table struture for sys_tree
*/

drop table if exists `sys_tree`;
CREATE TABLE `sys_tree` (
  `id` int(11) NOT NULL auto_increment,
  `root_id` int(11) NOT NULL default '0',
  `parent_id` int(11) NOT NULL default '0',
  `priority` int(11) NOT NULL default '0',
  `level` int(11) NOT NULL default '0',
  `identifier` varchar(128) NOT NULL default '',
  `path` varchar(255) NOT NULL default '',
  `children` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `root_id` (`root_id`),
  KEY `identifier` (`identifier`),
  KEY `level` (`level`),
  KEY `rlr` (`root_id`),
  KEY `parent_id` (`parent_id`)
) TYPE=InnoDB;

/*
Table struture for sys_uid
*/

drop table if exists `sys_uid`;
CREATE TABLE `sys_uid` (
  `id` bigint(20) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

/*
Table struture for sys_version_history
*/

drop table if exists `sys_version_history`;
CREATE TABLE `sys_version_history` (
  `id` int(11) NOT NULL auto_increment,
  `version_session_id` int(11) NOT NULL default '0',
  `revision_object_id` int(11) NOT NULL default '0',
  `creator_id` int(11) NOT NULL default '0',
  `created_date` int(11) NOT NULL default '0',
  `version` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `vo` (`version_session_id`,`revision_object_id`)
) TYPE=InnoDB COMMENT='InnoDB free: 10240 kB; InnoDB free: 114688 kB; InnoDB free: ';

/*
Table struture for test_cascade_master
*/

drop table if exists `test_cascade_master`;
CREATE TABLE `test_cascade_master` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `description` text,
  `title` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=InnoDB;

/*
Table struture for test_cascade_other
*/

drop table if exists `test_cascade_other`;
CREATE TABLE `test_cascade_other` (
  `id` char(32) NOT NULL default '',
  `file_name` char(255) default NULL,
  `mime_type` char(100) NOT NULL default '',
  `size` int(10) unsigned default NULL,
  `etag` char(32) default NULL,
  PRIMARY KEY  (`id`),
  KEY `id` (`id`)
) TYPE=InnoDB;

/*
Table struture for test_cascade_slave
*/

drop table if exists `test_cascade_slave`;
CREATE TABLE `test_cascade_slave` (
  `id` int(11) NOT NULL auto_increment,
  `image_id` int(11) unsigned NOT NULL default '0',
  `media_id` char(32) NOT NULL default '',
  `width` int(11) unsigned NOT NULL default '0',
  `height` int(11) unsigned NOT NULL default '0',
  `variation` char(50) default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

/*
Table struture for test_db_table
*/

drop table if exists `test_db_table`;
CREATE TABLE `test_db_table` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `description` text,
  `title` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=InnoDB;

/*
Table struture for test_materialized_path_tree
*/

drop table if exists `test_materialized_path_tree`;
CREATE TABLE `test_materialized_path_tree` (
  `id` int(11) NOT NULL auto_increment,
  `root_id` int(11) NOT NULL default '0',
  `parent_id` int(11) NOT NULL default '0',
  `level` int(11) NOT NULL default '0',
  `identifier` varchar(128) NOT NULL default '',
  `path` varchar(255) NOT NULL default '',
  `children` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `root_id` (`root_id`),
  KEY `identifier` (`identifier`),
  KEY `level` (`level`),
  KEY `parent_id` (`parent_id`),
  KEY `path` (`path`)
) TYPE=InnoDB;

/*
Table struture for test_nested_sets_tree
*/

drop table if exists `test_nested_sets_tree`;
CREATE TABLE `test_nested_sets_tree` (
  `id` int(11) NOT NULL auto_increment,
  `root_id` int(11) NOT NULL default '0',
  `l` int(11) NOT NULL default '0',
  `r` int(11) NOT NULL default '0',
  `parent_id` int(11) NOT NULL default '0',
  `ordr` int(11) NOT NULL default '0',
  `level` int(11) NOT NULL default '0',
  `identifier` char(128) NOT NULL default '',
  `children` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `root_id` (`root_id`),
  KEY `identifier` (`identifier`),
  KEY `l` (`l`),
  KEY `r` (`r`),
  KEY `level` (`level`),
  KEY `rlr` (`root_id`,`l`,`r`),
  KEY `parent_id` (`parent_id`)
) TYPE=InnoDB;

/*
Table struture for test_one_table_object
*/

drop table if exists `test_one_table_object`;
CREATE TABLE `test_one_table_object` (
  `id` int(11) NOT NULL default '0',
  `annotation` text,
  `content` text,
  `news_date` datetime default NULL,
  `oid` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=InnoDB;

/*
Table struture for user
*/

drop table if exists `user`;
CREATE TABLE `user` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(100) default NULL,
  `lastname` varchar(100) default NULL,
  `password` varchar(50) NOT NULL default '',
  `email` varchar(50) default NULL,
  `generated_password` varchar(50) default NULL,
  `login` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `pwd` (`password`),
  KEY `gpwd` (`generated_password`)
) TYPE=InnoDB;

