/* 
SQLyog v3.63
Host - localhost : Database - ptpa
**************************************************************
Server version 4.0.12-nt
*/

/*
Table struture for file_object
*/

drop table if exists `file_object`;
CREATE TABLE `file_object` (
  `id` bigint(20) NOT NULL auto_increment,
  `description` varchar(255) default NULL,
  `media_id` varchar(32) NOT NULL default '',
  `object_id` int(11) NOT NULL default '0',
  `version` int(11) NOT NULL default '0',
  `title` varchar(50) NOT NULL default '',
  `identifier` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `mid` (`media_id`),
  KEY `oid` (`object_id`),
  KEY `v` (`version`)
) TYPE=InnoDB COMMENT='InnoDB free: 7168 kB; InnoDB free: 114688 kB; InnoDB free: 1';


/*
Table struture for image_object
*/

drop table if exists `image_object`;
CREATE TABLE `image_object` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `description` text,
  `object_id` int(11) NOT NULL default '0',
  `version` int(11) NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `identifier` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `oid` (`object_id`),
  KEY `v` (`version`)
) TYPE=InnoDB COMMENT='InnoDB free: 9216 kB; InnoDB free: 114688 kB; InnoDB free: 1';


/*
Table struture for image_variation
*/

drop table if exists `image_variation`;
CREATE TABLE `image_variation` (
  `id` int(11) NOT NULL auto_increment,
  `image_id` int(11) unsigned NOT NULL default '0',
  `media_id` varchar(32) NOT NULL default '',
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
  `id` varchar(32) NOT NULL default '',
  `file_name` varchar(255) default NULL,
  `mime_type` varchar(100) NOT NULL default '',
  `size` int(10) unsigned default NULL,
  `etag` varchar(32) default NULL,
  PRIMARY KEY  (`id`),
  KEY `id` (`id`)
) TYPE=InnoDB COMMENT='InnoDB free: 9216 kB; InnoDB free: 114688 kB; InnoDB free: 1';


/*
Table struture for message
*/

drop table if exists `message`;
CREATE TABLE `message` (
  `id` int(11) NOT NULL auto_increment,
  `version` int(11) NOT NULL default '0',
  `object_id` int(11) NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `content` text,
  `identifier` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `v` (`version`),
  KEY `o` (`object_id`)
) TYPE=InnoDB COMMENT='InnoDB free: 114688 kB; InnoDB free: 114688 kB; InnoDB free:';

/*
Table data for ptpa.message
*/

INSERT INTO `message` VALUES (1,1,16,'Message',NULL,'messages');
INSERT INTO `message` VALUES (2,1,34,'Message',NULL,'messages');

/*
Table struture for navigation_item
*/

drop table if exists `navigation_item`;
CREATE TABLE `navigation_item` (
  `id` int(11) NOT NULL auto_increment,
  `version` int(11) NOT NULL default '0',
  `object_id` int(11) NOT NULL default '0',
  `title` varchar(100) NOT NULL default '',
  `url` varchar(255) NOT NULL default '',
  `identifier` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `v` (`version`),
  KEY `o` (`object_id`)
) TYPE=InnoDB COMMENT='InnoDB free: 114688 kB; InnoDB free: 114688 kB; InnoDB free:';

/*
Table data for ptpa.navigation_item
*/

INSERT INTO `navigation_item` VALUES (1,1,15,'Навигация','','navigation');
INSERT INTO `navigation_item` VALUES (2,1,33,'Навигация','','navigation');
INSERT INTO `navigation_item` VALUES (3,1,37,'Администрирование','/root/admin','admin');
INSERT INTO `navigation_item` VALUES (4,1,38,'Структура сайта','/root/admin/site_structure','site_structure');
INSERT INTO `navigation_item` VALUES (5,1,39,'Навигация','/root/navigation','navigation');
INSERT INTO `navigation_item` VALUES (6,2,33,'Навигация','/root/navigation','navigation');
INSERT INTO `navigation_item` VALUES (7,2,38,'Управление сайтом','/root/admin','site_management');
INSERT INTO `navigation_item` VALUES (8,2,39,'Управление контентом','/root/admin','content_management');
INSERT INTO `navigation_item` VALUES (9,1,40,'Навигация','/root/navigation','navigation');
INSERT INTO `navigation_item` VALUES (10,1,41,'Структура сайта','/root/admin/site_structure','site_structure');
INSERT INTO `navigation_item` VALUES (11,1,42,'Доступ к объектам','/root/admin/objects_access','objects_access');
INSERT INTO `navigation_item` VALUES (12,1,43,'Типы объектов','/root/admin/classes','classes');
INSERT INTO `navigation_item` VALUES (13,1,44,'Пользователи','/root/users','users');
INSERT INTO `navigation_item` VALUES (14,1,45,'Группы пользователей','/root/user_groups','user_groups');
INSERT INTO `navigation_item` VALUES (15,1,46,'Служебные сообщения','/root/messages','messages');
INSERT INTO `navigation_item` VALUES (16,1,47,'Файлы','/root/files_folder','files');
INSERT INTO `navigation_item` VALUES (17,1,48,'Изображения','/root/images_folder','images');
INSERT INTO `navigation_item` VALUES (18,1,49,'Меню пользователя','/root','main');

/*
Table struture for sys_action_access
*/

drop table if exists `sys_action_access`;
CREATE TABLE `sys_action_access` (
  `id` int(11) NOT NULL auto_increment,
  `class_id` int(11) NOT NULL default '0',
  `action_name` char(50) NOT NULL default '',
  `accessor_id` int(11) NOT NULL default '0',
  `accessor_type` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `accessor_id` (`accessor_id`),
  KEY `accessor_type` (`accessor_type`),
  KEY `class_id` (`class_id`)
) TYPE=InnoDB COMMENT='InnoDB free: 114688 kB; InnoDB free: 114688 kB; InnoDB free:';

/*
Table data for ptpa.sys_action_access
*/

INSERT INTO `sys_action_access` VALUES (216,17,'display',27,0);
INSERT INTO `sys_action_access` VALUES (217,17,'file_select',27,0);
INSERT INTO `sys_action_access` VALUES (218,17,'display',28,0);
INSERT INTO `sys_action_access` VALUES (219,17,'create_file',28,0);
INSERT INTO `sys_action_access` VALUES (220,17,'create_files_folder',28,0);
INSERT INTO `sys_action_access` VALUES (221,17,'edit_files_folder',28,0);
INSERT INTO `sys_action_access` VALUES (222,17,'delete',28,0);
INSERT INTO `sys_action_access` VALUES (223,17,'file_select',28,0);
INSERT INTO `sys_action_access` VALUES (224,16,'display',27,0);
INSERT INTO `sys_action_access` VALUES (225,16,'image_select',27,0);
INSERT INTO `sys_action_access` VALUES (226,16,'display',28,0);
INSERT INTO `sys_action_access` VALUES (227,16,'create_image',28,0);
INSERT INTO `sys_action_access` VALUES (228,16,'create_images_folder',28,0);
INSERT INTO `sys_action_access` VALUES (229,16,'edit_images_folder',28,0);
INSERT INTO `sys_action_access` VALUES (230,16,'delete',28,0);
INSERT INTO `sys_action_access` VALUES (231,16,'image_select',28,0);
INSERT INTO `sys_action_access` VALUES (232,1,'display',27,0);
INSERT INTO `sys_action_access` VALUES (233,1,'display',28,0);
INSERT INTO `sys_action_access` VALUES (234,1,'admin_display',28,0);
INSERT INTO `sys_action_access` VALUES (235,1,'set_metadata',28,0);
INSERT INTO `sys_action_access` VALUES (236,1,'edit',28,0);
INSERT INTO `sys_action_access` VALUES (237,15,'display',27,0);
INSERT INTO `sys_action_access` VALUES (238,15,'display',28,0);
INSERT INTO `sys_action_access` VALUES (239,15,'create_message',28,0);
INSERT INTO `sys_action_access` VALUES (240,15,'edit',28,0);
INSERT INTO `sys_action_access` VALUES (241,15,'delete',28,0);
INSERT INTO `sys_action_access` VALUES (259,9,'display',27,0);
INSERT INTO `sys_action_access` VALUES (260,9,'display',28,0);
INSERT INTO `sys_action_access` VALUES (261,9,'edit',28,0);
INSERT INTO `sys_action_access` VALUES (262,9,'delete',28,0);
INSERT INTO `sys_action_access` VALUES (266,7,'display',27,0);
INSERT INTO `sys_action_access` VALUES (267,7,'display',28,0);
INSERT INTO `sys_action_access` VALUES (268,7,'edit',28,0);
INSERT INTO `sys_action_access` VALUES (269,7,'set_membership',28,0);
INSERT INTO `sys_action_access` VALUES (270,7,'change_password',28,0);
INSERT INTO `sys_action_access` VALUES (271,7,'delete',28,0);
INSERT INTO `sys_action_access` VALUES (276,10,'login',27,0);
INSERT INTO `sys_action_access` VALUES (277,10,'logout',27,0);
INSERT INTO `sys_action_access` VALUES (278,10,'login',28,0);
INSERT INTO `sys_action_access` VALUES (279,10,'logout',28,0);
INSERT INTO `sys_action_access` VALUES (280,10,'edit',28,0);
INSERT INTO `sys_action_access` VALUES (308,3,'display',27,0);
INSERT INTO `sys_action_access` VALUES (309,3,'display',28,0);
INSERT INTO `sys_action_access` VALUES (310,3,'toggle',28,0);
INSERT INTO `sys_action_access` VALUES (311,3,'order',28,0);
INSERT INTO `sys_action_access` VALUES (312,5,'display',27,0);
INSERT INTO `sys_action_access` VALUES (313,5,'display',28,0);
INSERT INTO `sys_action_access` VALUES (314,5,'set_group_access',28,0);
INSERT INTO `sys_action_access` VALUES (315,5,'toggle',28,0);
INSERT INTO `sys_action_access` VALUES (319,11,'activate_password',27,0);
INSERT INTO `sys_action_access` VALUES (320,11,'activate_password',28,0);
INSERT INTO `sys_action_access` VALUES (321,12,'change_own_password',27,0);
INSERT INTO `sys_action_access` VALUES (322,12,'change_own_password',28,0);
INSERT INTO `sys_action_access` VALUES (323,13,'generate_password',27,0);
INSERT INTO `sys_action_access` VALUES (324,13,'generate_password',28,0);
INSERT INTO `sys_action_access` VALUES (325,8,'display',27,0);
INSERT INTO `sys_action_access` VALUES (326,8,'display',28,0);
INSERT INTO `sys_action_access` VALUES (327,8,'create_user_group',28,0);
INSERT INTO `sys_action_access` VALUES (331,2,'display',27,0);
INSERT INTO `sys_action_access` VALUES (332,2,'display',28,0);
INSERT INTO `sys_action_access` VALUES (333,2,'admin_display',28,0);
INSERT INTO `sys_action_access` VALUES (334,6,'display',28,0);
INSERT INTO `sys_action_access` VALUES (335,6,'create_user',28,0);
INSERT INTO `sys_action_access` VALUES (336,6,'edit',28,0);
INSERT INTO `sys_action_access` VALUES (337,14,'display',28,0);
INSERT INTO `sys_action_access` VALUES (338,14,'create_navigation_item',28,0);
INSERT INTO `sys_action_access` VALUES (339,14,'edit',28,0);
INSERT INTO `sys_action_access` VALUES (340,14,'delete',28,0);
INSERT INTO `sys_action_access` VALUES (341,14,'order',28,0);
INSERT INTO `sys_action_access` VALUES (342,4,'display',28,0);
INSERT INTO `sys_action_access` VALUES (343,4,'set_group_access',28,0);
INSERT INTO `sys_action_access` VALUES (344,4,'set_group_access_template',28,0);

/*
Table struture for sys_class
*/

drop table if exists `sys_class`;
CREATE TABLE `sys_class` (
  `id` int(11) NOT NULL auto_increment,
  `class_name` varchar(50) NOT NULL default '',
  `icon` varchar(30) NOT NULL default '',
  `class_ordr` smallint(6) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `class` (`class_name`)
) TYPE=InnoDB COMMENT='InnoDB free: 10240 kB; InnoDB free: 10240 kB; InnoDB free: 1';

/*
Table data for ptpa.sys_class
*/

INSERT INTO `sys_class` VALUES (1,'main_page','/shared/images/folder.gif',0);
INSERT INTO `sys_class` VALUES (2,'admin_page','',0);
INSERT INTO `sys_class` VALUES (3,'site_structure','',1);
INSERT INTO `sys_class` VALUES (4,'class_folder','/shared/images/folder.gif',0);
INSERT INTO `sys_class` VALUES (5,'objects_access','',0);
INSERT INTO `sys_class` VALUES (6,'users_folder','/shared/images/folder.gif',0);
INSERT INTO `sys_class` VALUES (7,'user_object','',1);
INSERT INTO `sys_class` VALUES (8,'user_groups_folder','/shared/images/folder.gif',0);
INSERT INTO `sys_class` VALUES (9,'user_group','',1);
INSERT INTO `sys_class` VALUES (10,'login_object','',0);
INSERT INTO `sys_class` VALUES (11,'user_activate_password','',0);
INSERT INTO `sys_class` VALUES (12,'user_change_password','',0);
INSERT INTO `sys_class` VALUES (13,'user_generate_password','',0);
INSERT INTO `sys_class` VALUES (14,'navigation_item','',1);
INSERT INTO `sys_class` VALUES (15,'message','',0);
INSERT INTO `sys_class` VALUES (16,'images_folder','/shared/images/folder.gif',0);
INSERT INTO `sys_class` VALUES (17,'files_folder','/shared/images/folder.gif',0);
INSERT INTO `sys_class` VALUES (19,'file_object','',1);
INSERT INTO `sys_class` VALUES (20,'image_object','',1);
INSERT INTO `sys_class` VALUES (21,'image_select','',0);
INSERT INTO `sys_class` VALUES (22,'file_select','',0);

/*
Table struture for sys_full_text_index
*/

drop table if exists `sys_full_text_index`;
CREATE TABLE `sys_full_text_index` (
  `id` int(11) NOT NULL auto_increment,
  `attribute` varchar(50) default NULL,
  `weight` tinyint(4) default '1',
  `object_id` int(11) default NULL,
  `class_id` int(11) default NULL,
  `body` text,
  PRIMARY KEY  (`id`),
  KEY `object_id` (`object_id`,`class_id`),
  KEY `body` (`body`(1))
) TYPE=MyISAM;


/*
Table struture for sys_group_object_access_template
*/

drop table if exists `sys_group_object_access_template`;
CREATE TABLE `sys_group_object_access_template` (
  `id` int(11) NOT NULL auto_increment,
  `class_id` int(11) NOT NULL default '0',
  `action_name` char(50) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `action_name` (`action_name`),
  KEY `class_id` (`class_id`)
) TYPE=InnoDB COMMENT='InnoDB free: 114688 kB; InnoDB free: 114688 kB; InnoDB free:';

/*
Table data for ptpa.sys_group_object_access_template
*/

INSERT INTO `sys_group_object_access_template` VALUES (2,6,'create_user');

/*
Table struture for sys_group_object_access_template_item
*/

drop table if exists `sys_group_object_access_template_item`;
CREATE TABLE `sys_group_object_access_template_item` (
  `id` int(11) NOT NULL auto_increment,
  `template_id` int(11) default NULL,
  `group_id` int(11) default NULL,
  `r` tinyint(4) default NULL,
  `w` tinyint(4) default NULL,
  PRIMARY KEY  (`id`),
  KEY `template_id` (`template_id`),
  KEY `group_id` (`group_id`)
) TYPE=InnoDB COMMENT='InnoDB free: 114688 kB; InnoDB free: 114688 kB; InnoDB free:';

/*
Table data for ptpa.sys_group_object_access_template_item
*/

INSERT INTO `sys_group_object_access_template_item` VALUES (3,2,27,1,0);
INSERT INTO `sys_group_object_access_template_item` VALUES (4,2,28,1,1);

/*
Table struture for sys_lock
*/

drop table if exists `sys_lock`;
CREATE TABLE `sys_lock` (
  `lock_id` char(32) NOT NULL default '',
  `lock_table` char(32) NOT NULL default '',
  `lock_stamp` int(11) NOT NULL default '0',
  PRIMARY KEY  (`lock_id`,`lock_table`)
) TYPE=InnoDB COMMENT='Table locks for NestedSet; InnoDB free: 114688 kB; InnoDB fr';


/*
Table struture for sys_metadata
*/

drop table if exists `sys_metadata`;
CREATE TABLE `sys_metadata` (
  `id` int(11) NOT NULL auto_increment,
  `object_id` int(11) NOT NULL default '0',
  `keywords` text,
  `description` text,
  PRIMARY KEY  (`id`),
  KEY `oid` (`object_id`)
) TYPE=InnoDB COMMENT='InnoDB free: 10240 kB; InnoDB free: 114688 kB; InnoDB free: ';


/*
Table struture for sys_object_access
*/

drop table if exists `sys_object_access`;
CREATE TABLE `sys_object_access` (
  `id` int(11) NOT NULL auto_increment,
  `object_id` int(11) NOT NULL default '0',
  `accessor_id` int(11) NOT NULL default '0',
  `r` tinyint(4) NOT NULL default '0',
  `w` tinyint(4) NOT NULL default '0',
  `accessor_type` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `accessor_id` (`accessor_id`),
  KEY `ora` (`object_id`,`r`,`accessor_id`),
  KEY `accessor_type` (`accessor_type`)
) TYPE=InnoDB COMMENT='InnoDB free: 114688 kB; InnoDB free: 114688 kB; InnoDB free:';

/*
Table data for ptpa.sys_object_access
*/

INSERT INTO `sys_object_access` VALUES (1,1,9,1,1,0);
INSERT INTO `sys_object_access` VALUES (2,1,10,1,1,0);
INSERT INTO `sys_object_access` VALUES (3,2,9,1,1,0);
INSERT INTO `sys_object_access` VALUES (4,2,10,1,1,0);
INSERT INTO `sys_object_access` VALUES (5,3,9,1,1,0);
INSERT INTO `sys_object_access` VALUES (6,3,10,1,1,0);
INSERT INTO `sys_object_access` VALUES (7,4,9,1,1,0);
INSERT INTO `sys_object_access` VALUES (8,4,10,1,1,0);
INSERT INTO `sys_object_access` VALUES (9,5,9,1,1,0);
INSERT INTO `sys_object_access` VALUES (10,5,10,1,1,0);
INSERT INTO `sys_object_access` VALUES (11,6,9,1,1,0);
INSERT INTO `sys_object_access` VALUES (12,6,10,1,1,0);
INSERT INTO `sys_object_access` VALUES (13,7,9,1,1,0);
INSERT INTO `sys_object_access` VALUES (14,7,10,1,1,0);
INSERT INTO `sys_object_access` VALUES (15,8,9,1,1,0);
INSERT INTO `sys_object_access` VALUES (16,8,10,1,1,0);
INSERT INTO `sys_object_access` VALUES (17,9,9,1,1,0);
INSERT INTO `sys_object_access` VALUES (18,9,10,1,1,0);
INSERT INTO `sys_object_access` VALUES (19,10,9,1,1,0);
INSERT INTO `sys_object_access` VALUES (20,10,10,1,1,0);
INSERT INTO `sys_object_access` VALUES (21,11,9,1,1,0);
INSERT INTO `sys_object_access` VALUES (22,11,10,1,1,0);
INSERT INTO `sys_object_access` VALUES (23,12,9,1,1,0);
INSERT INTO `sys_object_access` VALUES (24,12,10,1,1,0);
INSERT INTO `sys_object_access` VALUES (25,13,9,1,1,0);
INSERT INTO `sys_object_access` VALUES (26,13,10,1,1,0);
INSERT INTO `sys_object_access` VALUES (27,14,9,1,1,0);
INSERT INTO `sys_object_access` VALUES (28,14,10,1,1,0);
INSERT INTO `sys_object_access` VALUES (29,15,9,1,1,0);
INSERT INTO `sys_object_access` VALUES (30,15,10,1,1,0);
INSERT INTO `sys_object_access` VALUES (31,16,9,1,1,0);
INSERT INTO `sys_object_access` VALUES (32,16,10,1,1,0);
INSERT INTO `sys_object_access` VALUES (33,17,9,1,1,0);
INSERT INTO `sys_object_access` VALUES (34,17,10,1,1,0);
INSERT INTO `sys_object_access` VALUES (35,18,9,1,1,0);
INSERT INTO `sys_object_access` VALUES (36,18,10,1,1,0);
INSERT INTO `sys_object_access` VALUES (73,19,27,1,0,0);
INSERT INTO `sys_object_access` VALUES (74,19,28,1,1,0);
INSERT INTO `sys_object_access` VALUES (75,20,28,1,1,0);
INSERT INTO `sys_object_access` VALUES (76,21,28,1,1,0);
INSERT INTO `sys_object_access` VALUES (77,22,28,1,1,0);
INSERT INTO `sys_object_access` VALUES (78,23,28,1,1,0);
INSERT INTO `sys_object_access` VALUES (79,24,27,1,0,0);
INSERT INTO `sys_object_access` VALUES (80,24,28,1,1,0);
INSERT INTO `sys_object_access` VALUES (81,25,27,1,0,0);
INSERT INTO `sys_object_access` VALUES (82,25,28,1,1,0);
INSERT INTO `sys_object_access` VALUES (83,26,27,1,0,0);
INSERT INTO `sys_object_access` VALUES (84,26,28,1,1,0);
INSERT INTO `sys_object_access` VALUES (85,27,27,1,0,0);
INSERT INTO `sys_object_access` VALUES (86,27,28,1,1,0);
INSERT INTO `sys_object_access` VALUES (87,28,27,1,0,0);
INSERT INTO `sys_object_access` VALUES (88,28,28,1,1,0);
INSERT INTO `sys_object_access` VALUES (89,29,27,1,0,0);
INSERT INTO `sys_object_access` VALUES (90,29,28,1,1,0);
INSERT INTO `sys_object_access` VALUES (91,30,27,1,0,0);
INSERT INTO `sys_object_access` VALUES (92,30,28,1,1,0);
INSERT INTO `sys_object_access` VALUES (93,31,27,1,0,0);
INSERT INTO `sys_object_access` VALUES (94,31,28,1,1,0);
INSERT INTO `sys_object_access` VALUES (95,32,27,1,0,0);
INSERT INTO `sys_object_access` VALUES (96,32,28,1,1,0);
INSERT INTO `sys_object_access` VALUES (97,33,27,1,0,0);
INSERT INTO `sys_object_access` VALUES (98,33,28,1,1,0);
INSERT INTO `sys_object_access` VALUES (99,34,27,1,0,0);
INSERT INTO `sys_object_access` VALUES (100,34,28,1,1,0);
INSERT INTO `sys_object_access` VALUES (101,35,27,1,0,0);
INSERT INTO `sys_object_access` VALUES (102,35,28,1,1,0);
INSERT INTO `sys_object_access` VALUES (103,36,27,1,0,0);
INSERT INTO `sys_object_access` VALUES (104,36,28,1,1,0);
INSERT INTO `sys_object_access` VALUES (105,37,27,1,0,0);
INSERT INTO `sys_object_access` VALUES (106,37,28,1,1,0);
INSERT INTO `sys_object_access` VALUES (107,38,27,1,0,0);
INSERT INTO `sys_object_access` VALUES (108,38,28,1,1,0);
INSERT INTO `sys_object_access` VALUES (109,39,27,1,0,0);
INSERT INTO `sys_object_access` VALUES (110,39,28,1,1,0);
INSERT INTO `sys_object_access` VALUES (111,40,27,1,0,0);
INSERT INTO `sys_object_access` VALUES (112,40,28,1,1,0);
INSERT INTO `sys_object_access` VALUES (113,41,27,1,0,0);
INSERT INTO `sys_object_access` VALUES (114,41,28,1,1,0);
INSERT INTO `sys_object_access` VALUES (115,42,27,1,0,0);
INSERT INTO `sys_object_access` VALUES (116,42,28,1,1,0);
INSERT INTO `sys_object_access` VALUES (117,43,27,1,0,0);
INSERT INTO `sys_object_access` VALUES (118,43,28,1,1,0);
INSERT INTO `sys_object_access` VALUES (119,44,27,1,0,0);
INSERT INTO `sys_object_access` VALUES (120,44,28,1,1,0);
INSERT INTO `sys_object_access` VALUES (121,45,27,1,0,0);
INSERT INTO `sys_object_access` VALUES (122,45,28,1,1,0);
INSERT INTO `sys_object_access` VALUES (123,46,27,1,0,0);
INSERT INTO `sys_object_access` VALUES (124,46,28,1,1,0);
INSERT INTO `sys_object_access` VALUES (125,47,27,1,0,0);
INSERT INTO `sys_object_access` VALUES (126,47,28,1,1,0);
INSERT INTO `sys_object_access` VALUES (127,48,27,1,0,0);
INSERT INTO `sys_object_access` VALUES (128,48,28,1,1,0);
INSERT INTO `sys_object_access` VALUES (129,49,27,1,0,0);
INSERT INTO `sys_object_access` VALUES (130,49,28,1,1,0);
INSERT INTO `sys_object_access` VALUES (131,50,27,1,0,0);
INSERT INTO `sys_object_access` VALUES (132,50,28,1,1,0);
INSERT INTO `sys_object_access` VALUES (133,51,27,1,0,0);
INSERT INTO `sys_object_access` VALUES (134,51,28,1,1,0);

/*
Table struture for sys_object_version
*/

drop table if exists `sys_object_version`;
CREATE TABLE `sys_object_version` (
  `id` int(11) NOT NULL auto_increment,
  `object_id` int(11) NOT NULL default '0',
  `creator_id` int(11) NOT NULL default '0',
  `modified_date` int(11) NOT NULL default '0',
  `created_date` int(11) NOT NULL default '0',
  `version` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `oid` (`object_id`),
  KEY `cid` (`creator_id`),
  KEY `md` (`modified_date`),
  KEY `cd` (`created_date`),
  KEY `v` (`version`)
) TYPE=InnoDB COMMENT='InnoDB free: 10240 kB; InnoDB free: 114688 kB; InnoDB free: ';

/*
Table data for ptpa.sys_object_version
*/

INSERT INTO `sys_object_version` VALUES (1,7,0,1076755675,1076755675,1);
INSERT INTO `sys_object_version` VALUES (2,9,0,1076755676,1076755676,1);
INSERT INTO `sys_object_version` VALUES (3,10,0,1076755676,1076755676,1);
INSERT INTO `sys_object_version` VALUES (4,15,0,1076755676,1076755676,1);
INSERT INTO `sys_object_version` VALUES (5,16,0,1076755676,1076755676,1);
INSERT INTO `sys_object_version` VALUES (6,25,0,1076762314,1076762314,1);
INSERT INTO `sys_object_version` VALUES (7,27,0,1076762314,1076762314,1);
INSERT INTO `sys_object_version` VALUES (8,28,0,1076762314,1076762314,1);
INSERT INTO `sys_object_version` VALUES (9,33,0,1076762315,1076762315,1);
INSERT INTO `sys_object_version` VALUES (10,34,0,1076762315,1076762315,1);
INSERT INTO `sys_object_version` VALUES (11,37,25,1076770835,1076770835,1);
INSERT INTO `sys_object_version` VALUES (12,38,25,1076770879,1076770879,1);
INSERT INTO `sys_object_version` VALUES (13,39,25,1076771149,1076771149,1);
INSERT INTO `sys_object_version` VALUES (14,33,25,1076771224,1076771224,2);
INSERT INTO `sys_object_version` VALUES (15,38,25,1076771356,1076771356,2);
INSERT INTO `sys_object_version` VALUES (16,39,25,1076771416,1076771416,2);
INSERT INTO `sys_object_version` VALUES (17,40,25,1076771605,1076771605,1);
INSERT INTO `sys_object_version` VALUES (18,41,25,1076772382,1076772382,1);
INSERT INTO `sys_object_version` VALUES (19,42,25,1076772439,1076772439,1);
INSERT INTO `sys_object_version` VALUES (20,43,25,1076772480,1076772480,1);
INSERT INTO `sys_object_version` VALUES (21,44,25,1076772520,1076772520,1);
INSERT INTO `sys_object_version` VALUES (22,45,25,1076772541,1076772541,1);
INSERT INTO `sys_object_version` VALUES (23,46,25,1076772578,1076772578,1);
INSERT INTO `sys_object_version` VALUES (24,47,25,1076772601,1076772601,1);
INSERT INTO `sys_object_version` VALUES (25,48,25,1076772623,1076772623,1);
INSERT INTO `sys_object_version` VALUES (26,49,25,1076772668,1076772668,1);

/*
Table struture for sys_param
*/

drop table if exists `sys_param`;
CREATE TABLE `sys_param` (
  `id` int(20) unsigned NOT NULL auto_increment,
  `identifier` varchar(50) NOT NULL default '',
  `type` varchar(10) NOT NULL default '',
  `int_value` int(11) default NULL,
  `float_value` double(20,10) default NULL,
  `char_value` varchar(255) default NULL,
  `blob_value` longblob,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id_u` (`identifier`)
) TYPE=InnoDB COMMENT='InnoDB free: 114688 kB; InnoDB free: 114688 kB; InnoDB free:';


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
Table data for ptpa.sys_session
*/

INSERT INTO `sys_session` VALUES ('156661cce17d8630aeef1d1c1c42cf52','tree_expanded_parents|a:7:{i:1;a:4:{s:1:\"l\";i:1;s:1:\"r\";i:66;s:7:\"root_id\";i:1;s:6:\"status\";b:1;}i:8;a:4:{s:1:\"l\";i:2;s:1:\"r\";i:7;s:7:\"root_id\";i:1;s:6:\"status\";b:0;}i:6;a:4:{s:1:\"l\";i:8;s:1:\"r\";i:11;s:7:\"root_id\";i:1;s:6:\"status\";b:0;}i:2;a:4:{s:1:\"l\";i:16;s:1:\"r\";i:23;s:7:\"root_id\";i:1;s:6:\"status\";b:0;}i:15;a:4:{s:1:\"l\";i:28;s:1:\"r\";i:55;s:7:\"root_id\";i:1;s:6:\"status\";b:0;}i:19;a:4:{s:1:\"l\";i:29;s:1:\"r\";i:52;s:7:\"root_id\";i:1;s:6:\"status\";b:0;}i:20;a:4:{s:1:\"l\";i:30;s:1:\"r\";i:49;s:7:\"root_id\";i:1;s:6:\"status\";b:0;}}global_user|O:4:\"user\":10:{s:3:\"_id\";i:-1;s:8:\"_node_id\";i:-1;s:6:\"_login\";s:0:\"\";s:9:\"_password\";s:0:\"\";s:6:\"_email\";s:0:\"\";s:5:\"_name\";s:0:\"\";s:9:\"_lastname\";s:0:\"\";s:13:\"_is_logged_in\";b:0;s:7:\"_groups\";a:1:{i:27;s:8:\"visitors\";}s:11:\"_attributes\";N;}session_classes_paths|a:1:{i:0;s:56:\"c:/var/dev/limb2/trunk//core/lib/security/user.class.php\";}strings|s:0:\"\";',1080315187,-1);
INSERT INTO `sys_session` VALUES ('91ffbc7929e17bf4e83e05b9e8b93942','tree_expanded_parents|a:7:{i:1;a:4:{s:1:\"l\";i:1;s:1:\"r\";i:66;s:7:\"root_id\";i:1;s:6:\"status\";b:1;}i:8;a:4:{s:1:\"l\";i:2;s:1:\"r\";i:7;s:7:\"root_id\";i:1;s:6:\"status\";b:0;}i:6;a:4:{s:1:\"l\";i:8;s:1:\"r\";i:11;s:7:\"root_id\";i:1;s:6:\"status\";b:0;}i:2;a:4:{s:1:\"l\";i:16;s:1:\"r\";i:23;s:7:\"root_id\";i:1;s:6:\"status\";b:0;}i:15;a:4:{s:1:\"l\";i:28;s:1:\"r\";i:55;s:7:\"root_id\";i:1;s:6:\"status\";b:0;}i:19;a:4:{s:1:\"l\";i:29;s:1:\"r\";i:52;s:7:\"root_id\";i:1;s:6:\"status\";b:0;}i:20;a:4:{s:1:\"l\";i:30;s:1:\"r\";i:49;s:7:\"root_id\";i:1;s:6:\"status\";b:0;}}global_user|O:4:\"user\":10:{s:3:\"_id\";s:2:\"25\";s:8:\"_node_id\";s:1:\"7\";s:6:\"_login\";s:5:\"admin\";s:9:\"_password\";s:32:\"66d4aaa5ea177ac32c69946de3731ec0\";s:6:\"_email\";s:15:\"mike@office.bit\";s:5:\"_name\";s:5:\"admin\";s:9:\"_lastname\";s:5:\"super\";s:13:\"_is_logged_in\";b:1;s:7:\"_groups\";a:1:{i:28;s:6:\"admins\";}s:11:\"_attributes\";N;}session_classes_paths|a:2:{i:0;s:56:\"c:/var/dev/limb2/trunk//core/lib/security/user.class.php\";i:1;s:62:\"c:/var/dev/limb2/trunk//core/model/test_session_bean.class.php\";}strings|s:0:\"\";global_test_session_bean|O:17:\"test_session_bean\":1:{s:3:\"_id\";i:61;}',1080307894,25);
INSERT INTO `sys_session` VALUES ('d798af52726c1805810062c5b0dbec13','tree_expanded_parents|a:7:{i:1;a:4:{s:1:\"l\";i:1;s:1:\"r\";i:66;s:7:\"root_id\";i:1;s:6:\"status\";b:1;}i:8;a:4:{s:1:\"l\";i:2;s:1:\"r\";i:7;s:7:\"root_id\";i:1;s:6:\"status\";b:1;}i:6;a:4:{s:1:\"l\";i:8;s:1:\"r\";i:11;s:7:\"root_id\";i:1;s:6:\"status\";b:0;}i:2;a:4:{s:1:\"l\";i:16;s:1:\"r\";i:23;s:7:\"root_id\";i:1;s:6:\"status\";b:0;}i:15;a:4:{s:1:\"l\";i:28;s:1:\"r\";i:55;s:7:\"root_id\";i:1;s:6:\"status\";b:0;}i:19;a:4:{s:1:\"l\";i:29;s:1:\"r\";i:52;s:7:\"root_id\";i:1;s:6:\"status\";b:0;}i:20;a:4:{s:1:\"l\";i:30;s:1:\"r\";i:49;s:7:\"root_id\";i:1;s:6:\"status\";b:0;}}global_user|O:4:\"user\":10:{s:3:\"_id\";s:2:\"25\";s:8:\"_node_id\";s:1:\"7\";s:6:\"_login\";s:5:\"admin\";s:9:\"_password\";s:32:\"66d4aaa5ea177ac32c69946de3731ec0\";s:6:\"_email\";s:15:\"mike@office.bit\";s:5:\"_name\";s:5:\"admin\";s:9:\"_lastname\";s:5:\"super\";s:13:\"_is_logged_in\";b:1;s:7:\"_groups\";a:1:{i:28;s:6:\"admins\";}s:11:\"_attributes\";N;}session_classes_paths|a:2:{i:0;s:56:\"c:/var/dev/limb2/trunk//core/lib/security/user.class.php\";i:1;s:62:\"c:/var/dev/limb2/trunk//core/model/test_session_bean.class.php\";}strings|s:0:\"\";global_test_session_bean|O:17:\"test_session_bean\":1:{s:3:\"_id\";i:79;}',1080316190,25);

/*
Table struture for sys_site_object
*/

drop table if exists `sys_site_object`;
CREATE TABLE `sys_site_object` (
  `id` int(11) NOT NULL auto_increment,
  `class_id` int(11) NOT NULL default '0',
  `current_version` int(11) default NULL,
  `modified_date` int(11) NOT NULL default '0',
  `status` int(11) default '0',
  `created_date` int(11) NOT NULL default '0',
  `creator_id` int(11) NOT NULL default '0',
  `locale_id` char(2) NOT NULL default 'en',
  `title` varchar(255) NOT NULL default '',
  `identifier` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `idccv` (`id`,`locale_id`,`current_version`,`class_id`),
  KEY `md` (`modified_date`),
  KEY `cd` (`created_date`),
  KEY `cid` (`creator_id`),
  KEY `current_version` (`current_version`)
) TYPE=InnoDB COMMENT='InnoDB free: 10240 kB; InnoDB free: 10240 kB; InnoDB free: 1';

/*
Table data for ptpa.sys_site_object
*/

INSERT INTO `sys_site_object` VALUES (1,1,1,1076755675,0,1076755675,0,'ru','Главная','root');
INSERT INTO `sys_site_object` VALUES (2,2,1,1076755675,0,1076755675,0,'ru','Администрирование','admin');
INSERT INTO `sys_site_object` VALUES (3,3,1,1076755675,0,1076755675,0,'ru','Структура сайта','site_structure');
INSERT INTO `sys_site_object` VALUES (4,4,1,1076755675,0,1076755675,0,'ru','Типы объектов','classes');
INSERT INTO `sys_site_object` VALUES (5,5,1,1076755675,0,1076755675,0,'ru','Доступ к объектам','objects_access');
INSERT INTO `sys_site_object` VALUES (6,6,1,1076755675,0,1076755675,0,'ru','Пользователи','users');
INSERT INTO `sys_site_object` VALUES (7,7,1,1076755675,0,1076755675,0,'ru','Администрирование','admin');
INSERT INTO `sys_site_object` VALUES (8,8,1,1076755675,0,1076755675,0,'ru','Группы пользователей','user_groups');
INSERT INTO `sys_site_object` VALUES (11,10,1,1076755676,0,1076755676,0,'ru','Авторизация','login');
INSERT INTO `sys_site_object` VALUES (12,11,1,1076755676,0,1076755676,0,'ru','Активизация пароля','activate_password');
INSERT INTO `sys_site_object` VALUES (13,12,1,1076755676,0,1076755676,0,'ru','Смена пароля','change_password');
INSERT INTO `sys_site_object` VALUES (14,13,1,1076755676,0,1076755676,0,'ru','Генерация пароля','generate_password');
INSERT INTO `sys_site_object` VALUES (15,14,1,1076755676,0,1076755676,0,'ru','Навигация','navigation');
INSERT INTO `sys_site_object` VALUES (16,15,1,1076755676,0,1076755676,0,'ru','Сообщения','messages');
INSERT INTO `sys_site_object` VALUES (17,16,1,1076755676,0,1076755676,0,'ru','Изображения','images_folder');
INSERT INTO `sys_site_object` VALUES (18,17,1,1076755676,0,1076755676,0,'ru','Файлы','files_folder');
INSERT INTO `sys_site_object` VALUES (19,1,3,1076768404,0,1076762314,0,'ru','Главная','root');
INSERT INTO `sys_site_object` VALUES (20,2,1,1076762314,0,1076762314,0,'ru','Администрирование','admin');
INSERT INTO `sys_site_object` VALUES (21,3,2,1076769130,0,1076762314,0,'ru','Структура сайта','site_structure');
INSERT INTO `sys_site_object` VALUES (22,4,2,1076769267,0,1076762314,0,'ru','Классы объектов','classes');
INSERT INTO `sys_site_object` VALUES (23,5,2,1076769145,0,1076762314,0,'ru','Доступ к объектам','objects_access');
INSERT INTO `sys_site_object` VALUES (24,6,2,1076769160,0,1076762314,0,'ru','Пользователи','users');
INSERT INTO `sys_site_object` VALUES (25,7,1,1076762588,0,1076762314,0,'ru','Администрирование','admin');
INSERT INTO `sys_site_object` VALUES (26,8,2,1076769173,0,1076762314,0,'ru','Группы пользователей','user_groups');
INSERT INTO `sys_site_object` VALUES (27,9,1,1076762314,0,1076762314,0,'ru','Посетители','visitors');
INSERT INTO `sys_site_object` VALUES (28,9,1,1076762314,0,1076762314,0,'ru','Администраторы','admins');
INSERT INTO `sys_site_object` VALUES (29,10,2,1076769188,0,1076762314,0,'ru','Авторизация','login');
INSERT INTO `sys_site_object` VALUES (30,11,2,1076769202,0,1076762314,0,'ru','Активировать пароль','activate_password');
INSERT INTO `sys_site_object` VALUES (31,12,2,1076769224,0,1076762314,0,'ru','Смена пароля','change_password');
INSERT INTO `sys_site_object` VALUES (32,13,2,1076769246,0,1076762315,0,'ru','Забыли пароль?','generate_password');
INSERT INTO `sys_site_object` VALUES (33,14,2,1076771224,0,1076762315,0,'ru','Навигация','navigation');
INSERT INTO `sys_site_object` VALUES (34,15,1,1076762315,0,1076762315,0,'ru','Сообщения','messages');
INSERT INTO `sys_site_object` VALUES (35,16,1,1076762315,0,1076762315,0,'ru','Изображения','images_folder');
INSERT INTO `sys_site_object` VALUES (36,17,1,1076762315,0,1076762315,0,'ru','Файлы','files_folder');
INSERT INTO `sys_site_object` VALUES (37,14,1,1076770835,0,1076770835,25,'ru','Администрирование','admin');
INSERT INTO `sys_site_object` VALUES (38,14,2,1076771356,0,1076770879,25,'ru','Управление сайтом','site_management');
INSERT INTO `sys_site_object` VALUES (39,14,2,1076771416,0,1076771149,25,'ru','Управление контентом','content_management');
INSERT INTO `sys_site_object` VALUES (40,14,1,1076771604,0,1076771604,25,'ru','Навигация','navigation');
INSERT INTO `sys_site_object` VALUES (41,14,1,1076772382,0,1076772382,25,'ru','Структура сайта','site_structure');
INSERT INTO `sys_site_object` VALUES (42,14,1,1076772439,0,1076772439,25,'ru','Доступ к объектам','objects_access');
INSERT INTO `sys_site_object` VALUES (43,14,1,1076772480,0,1076772480,25,'ru','Типы объектов','classes');
INSERT INTO `sys_site_object` VALUES (44,14,1,1076772520,0,1076772520,25,'ru','Пользователи','users');
INSERT INTO `sys_site_object` VALUES (45,14,1,1076772540,0,1076772540,25,'ru','Группы пользователей','user_groups');
INSERT INTO `sys_site_object` VALUES (46,14,1,1076772578,0,1076772578,25,'ru','Служебные сообщения','messages');
INSERT INTO `sys_site_object` VALUES (47,14,1,1076772601,0,1076772601,25,'ru','Файлы','files');
INSERT INTO `sys_site_object` VALUES (48,14,1,1076772623,0,1076772623,25,'ru','Изображения','images');
INSERT INTO `sys_site_object` VALUES (49,14,1,1076772668,0,1076772668,25,'ru','Меню пользователя','main');
INSERT INTO `sys_site_object` VALUES (50,21,1,1079691597,0,1079691597,25,'ru','Выбор изображения','image_select');
INSERT INTO `sys_site_object` VALUES (51,22,1,1079691620,0,1079691620,25,'ru','Выбор файла','file_select');

/*
Table struture for sys_site_object_tree
*/

drop table if exists `sys_site_object_tree`;
CREATE TABLE `sys_site_object_tree` (
  `id` int(11) NOT NULL auto_increment,
  `root_id` int(11) NOT NULL default '0',
  `l` int(11) NOT NULL default '0',
  `r` int(11) NOT NULL default '0',
  `parent_id` int(11) NOT NULL default '0',
  `ordr` int(11) NOT NULL default '0',
  `level` int(11) NOT NULL default '0',
  `identifier` char(128) NOT NULL default '',
  `object_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `root_id` (`root_id`),
  KEY `identifier` (`identifier`),
  KEY `l` (`l`),
  KEY `r` (`r`),
  KEY `level` (`level`),
  KEY `rlr` (`root_id`,`l`,`r`),
  KEY `parent_id` (`parent_id`),
  KEY `object_id` (`object_id`)
) TYPE=InnoDB COMMENT='InnoDB free: 114688 kB; InnoDB free: 114688 kB; InnoDB free:';

/*
Table data for ptpa.sys_site_object_tree
*/

INSERT INTO `sys_site_object_tree` VALUES (1,1,1,66,0,1,1,'root',19);
INSERT INTO `sys_site_object_tree` VALUES (2,1,16,23,1,5,2,'admin',20);
INSERT INTO `sys_site_object_tree` VALUES (3,1,19,20,2,2,3,'site_structure',21);
INSERT INTO `sys_site_object_tree` VALUES (4,1,17,18,2,1,3,'classes',22);
INSERT INTO `sys_site_object_tree` VALUES (5,1,21,22,2,3,3,'objects_access',23);
INSERT INTO `sys_site_object_tree` VALUES (6,1,8,11,1,2,2,'users',24);
INSERT INTO `sys_site_object_tree` VALUES (7,1,9,10,6,1,3,'admin',25);
INSERT INTO `sys_site_object_tree` VALUES (8,1,2,7,1,1,2,'user_groups',26);
INSERT INTO `sys_site_object_tree` VALUES (9,1,3,4,8,1,3,'visitors',27);
INSERT INTO `sys_site_object_tree` VALUES (10,1,5,6,8,2,3,'admins',28);
INSERT INTO `sys_site_object_tree` VALUES (11,1,24,25,1,6,2,'login',29);
INSERT INTO `sys_site_object_tree` VALUES (12,1,26,27,1,7,2,'activate_password',30);
INSERT INTO `sys_site_object_tree` VALUES (13,1,56,57,1,9,2,'change_password',31);
INSERT INTO `sys_site_object_tree` VALUES (14,1,58,59,1,10,2,'generate_password',32);
INSERT INTO `sys_site_object_tree` VALUES (15,1,28,55,1,8,2,'navigation',33);
INSERT INTO `sys_site_object_tree` VALUES (16,1,60,61,1,11,2,'messages',34);
INSERT INTO `sys_site_object_tree` VALUES (17,1,12,13,1,3,2,'images_folder',35);
INSERT INTO `sys_site_object_tree` VALUES (18,1,14,15,1,4,2,'files_folder',36);
INSERT INTO `sys_site_object_tree` VALUES (19,1,29,52,15,1,3,'admin',37);
INSERT INTO `sys_site_object_tree` VALUES (20,1,30,49,19,1,4,'site_management',38);
INSERT INTO `sys_site_object_tree` VALUES (21,1,50,51,19,2,4,'content_management',39);
INSERT INTO `sys_site_object_tree` VALUES (22,1,31,32,20,1,5,'navigation',40);
INSERT INTO `sys_site_object_tree` VALUES (23,1,33,34,20,2,5,'site_structure',41);
INSERT INTO `sys_site_object_tree` VALUES (24,1,35,36,20,3,5,'objects_access',42);
INSERT INTO `sys_site_object_tree` VALUES (25,1,37,38,20,4,5,'classes',43);
INSERT INTO `sys_site_object_tree` VALUES (26,1,39,40,20,5,5,'users',44);
INSERT INTO `sys_site_object_tree` VALUES (27,1,41,42,20,6,5,'user_groups',45);
INSERT INTO `sys_site_object_tree` VALUES (28,1,43,44,20,7,5,'messages',46);
INSERT INTO `sys_site_object_tree` VALUES (29,1,45,46,20,8,5,'files',47);
INSERT INTO `sys_site_object_tree` VALUES (30,1,47,48,20,9,5,'images',48);
INSERT INTO `sys_site_object_tree` VALUES (31,1,53,54,15,2,3,'main',49);
INSERT INTO `sys_site_object_tree` VALUES (32,1,62,63,1,12,2,'image_select',50);
INSERT INTO `sys_site_object_tree` VALUES (33,1,64,65,1,13,2,'file_select',51);

/*
Table struture for sys_stat_counter
*/

drop table if exists `sys_stat_counter`;
CREATE TABLE `sys_stat_counter` (
  `id` int(11) NOT NULL auto_increment,
  `hosts_all` int(11) NOT NULL default '0',
  `hits_all` int(11) NOT NULL default '0',
  `hosts_today` int(11) NOT NULL default '0',
  `hits_today` int(11) NOT NULL default '0',
  `time` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=InnoDB;

/*
Table data for ptpa.sys_stat_counter
*/

INSERT INTO `sys_stat_counter` VALUES (1,5,89,1,78,1080316190);

/*
Table struture for sys_stat_day_counters
*/

drop table if exists `sys_stat_day_counters`;
CREATE TABLE `sys_stat_day_counters` (
  `id` int(11) NOT NULL auto_increment,
  `time` int(11) NOT NULL default '0',
  `hits` int(11) NOT NULL default '0',
  `hosts` int(11) NOT NULL default '0',
  `home_hits` int(11) NOT NULL default '0',
  `audience` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=InnoDB;

/*
Table data for ptpa.sys_stat_day_counters
*/

INSERT INTO `sys_stat_day_counters` VALUES (1,1079643600,1,1,1,1);
INSERT INTO `sys_stat_day_counters` VALUES (2,1079730000,1,1,1,1);
INSERT INTO `sys_stat_day_counters` VALUES (3,1079902800,8,1,8,1);
INSERT INTO `sys_stat_day_counters` VALUES (4,1080162000,1,1,1,1);
INSERT INTO `sys_stat_day_counters` VALUES (5,1080248400,78,1,44,1);

/*
Table struture for sys_stat_ip
*/

drop table if exists `sys_stat_ip`;
CREATE TABLE `sys_stat_ip` (
  `id` varchar(8) NOT NULL default '0',
  `time` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=InnoDB;

/*
Table data for ptpa.sys_stat_ip
*/

INSERT INTO `sys_stat_ip` VALUES ('c0a80006',1080295000);

/*
Table struture for sys_stat_log
*/

drop table if exists `sys_stat_log`;
CREATE TABLE `sys_stat_log` (
  `id` int(11) NOT NULL auto_increment,
  `node_id` int(11) NOT NULL default '0',
  `stat_referer_id` int(11) NOT NULL default '0',
  `time` int(11) NOT NULL default '0',
  `ip` varchar(8) NOT NULL default '0',
  `action` varchar(50) default NULL,
  `session_id` varchar(50) NOT NULL default '',
  `user_id` int(11) NOT NULL default '0',
  `status` int(11) default NULL,
  `stat_uri_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `complex` (`node_id`,`time`,`user_id`,`stat_uri_id`)
) TYPE=InnoDB ROW_FORMAT=DYNAMIC COMMENT='InnoDB free: 9216 kB';

/*
Table data for ptpa.sys_stat_log
*/

INSERT INTO `sys_stat_log` VALUES (1,1,-1,1080295000,'c0a80006','display','91ffbc7929e17bf4e83e05b9e8b93942',-1,1,1);
INSERT INTO `sys_stat_log` VALUES (2,1,-1,1080304018,'c0a80006','display','91ffbc7929e17bf4e83e05b9e8b93942',-1,1,1);
INSERT INTO `sys_stat_log` VALUES (3,1,-1,1080304024,'c0a80006','display','91ffbc7929e17bf4e83e05b9e8b93942',-1,1,1);
INSERT INTO `sys_stat_log` VALUES (4,1,-1,1080304091,'c0a80006','display','91ffbc7929e17bf4e83e05b9e8b93942',-1,1,1);
INSERT INTO `sys_stat_log` VALUES (5,1,-1,1080304109,'c0a80006','display','91ffbc7929e17bf4e83e05b9e8b93942',-1,1,1);
INSERT INTO `sys_stat_log` VALUES (6,1,-1,1080304113,'c0a80006','display','91ffbc7929e17bf4e83e05b9e8b93942',-1,1,1);
INSERT INTO `sys_stat_log` VALUES (7,1,-1,1080306259,'c0a80006','display','91ffbc7929e17bf4e83e05b9e8b93942',-1,1,1);
INSERT INTO `sys_stat_log` VALUES (8,1,-1,1080306260,'c0a80006','display','91ffbc7929e17bf4e83e05b9e8b93942',-1,1,1);
INSERT INTO `sys_stat_log` VALUES (9,1,-1,1080306261,'c0a80006','display','91ffbc7929e17bf4e83e05b9e8b93942',-1,1,1);
INSERT INTO `sys_stat_log` VALUES (10,1,-1,1080306262,'c0a80006','display','91ffbc7929e17bf4e83e05b9e8b93942',-1,1,1);
INSERT INTO `sys_stat_log` VALUES (11,1,-1,1080306603,'c0a80006','display','91ffbc7929e17bf4e83e05b9e8b93942',-1,1,1);
INSERT INTO `sys_stat_log` VALUES (12,1,-1,1080306616,'c0a80006','display','91ffbc7929e17bf4e83e05b9e8b93942',-1,1,1);
INSERT INTO `sys_stat_log` VALUES (13,1,-1,1080307092,'c0a80006','display','91ffbc7929e17bf4e83e05b9e8b93942',-1,1,1);
INSERT INTO `sys_stat_log` VALUES (14,1,-1,1080307098,'c0a80006','display','91ffbc7929e17bf4e83e05b9e8b93942',-1,1,1);
INSERT INTO `sys_stat_log` VALUES (15,1,-1,1080307100,'c0a80006','display','91ffbc7929e17bf4e83e05b9e8b93942',-1,1,1);
INSERT INTO `sys_stat_log` VALUES (16,1,-1,1080307123,'c0a80006','display','91ffbc7929e17bf4e83e05b9e8b93942',-1,1,1);
INSERT INTO `sys_stat_log` VALUES (17,1,-1,1080307136,'c0a80006','display','91ffbc7929e17bf4e83e05b9e8b93942',-1,1,1);
INSERT INTO `sys_stat_log` VALUES (18,1,-1,1080307183,'c0a80006','display','91ffbc7929e17bf4e83e05b9e8b93942',-1,1,1);
INSERT INTO `sys_stat_log` VALUES (19,1,-1,1080307207,'c0a80006','display','91ffbc7929e17bf4e83e05b9e8b93942',-1,1,1);
INSERT INTO `sys_stat_log` VALUES (20,1,-1,1080307221,'c0a80006','display','91ffbc7929e17bf4e83e05b9e8b93942',-1,1,1);
INSERT INTO `sys_stat_log` VALUES (21,1,-1,1080307318,'c0a80006','display','91ffbc7929e17bf4e83e05b9e8b93942',-1,1,1);
INSERT INTO `sys_stat_log` VALUES (22,1,-1,1080307320,'c0a80006','display','91ffbc7929e17bf4e83e05b9e8b93942',-1,1,1);
INSERT INTO `sys_stat_log` VALUES (23,11,-1,1080307367,'c0a80006','login','91ffbc7929e17bf4e83e05b9e8b93942',-1,4,3);
INSERT INTO `sys_stat_log` VALUES (24,11,-1,1080307371,'c0a80006','login','91ffbc7929e17bf4e83e05b9e8b93942',25,2,3);
INSERT INTO `sys_stat_log` VALUES (25,2,-1,1080307371,'c0a80006','display','91ffbc7929e17bf4e83e05b9e8b93942',25,1,4);
INSERT INTO `sys_stat_log` VALUES (26,2,-1,1080307373,'c0a80006','display','91ffbc7929e17bf4e83e05b9e8b93942',25,1,4);
INSERT INTO `sys_stat_log` VALUES (27,3,-1,1080307380,'c0a80006','display','91ffbc7929e17bf4e83e05b9e8b93942',25,1,5);
INSERT INTO `sys_stat_log` VALUES (28,3,-1,1080307441,'c0a80006','display','91ffbc7929e17bf4e83e05b9e8b93942',25,1,5);
INSERT INTO `sys_stat_log` VALUES (29,1,-1,1080307446,'c0a80006','display','d798af52726c1805810062c5b0dbec13',-1,1,1);
INSERT INTO `sys_stat_log` VALUES (30,1,-1,1080307453,'c0a80006','display','d798af52726c1805810062c5b0dbec13',-1,1,1);
INSERT INTO `sys_stat_log` VALUES (31,1,-1,1080307859,'c0a80006','display','91ffbc7929e17bf4e83e05b9e8b93942',25,1,1);
INSERT INTO `sys_stat_log` VALUES (32,1,-1,1080307861,'c0a80006','display','91ffbc7929e17bf4e83e05b9e8b93942',25,1,1);
INSERT INTO `sys_stat_log` VALUES (33,1,-1,1080307861,'c0a80006','display','91ffbc7929e17bf4e83e05b9e8b93942',25,1,1);
INSERT INTO `sys_stat_log` VALUES (34,1,-1,1080307862,'c0a80006','display','91ffbc7929e17bf4e83e05b9e8b93942',25,1,1);
INSERT INTO `sys_stat_log` VALUES (35,1,-1,1080307863,'c0a80006','display','91ffbc7929e17bf4e83e05b9e8b93942',25,1,1);
INSERT INTO `sys_stat_log` VALUES (36,1,-1,1080307864,'c0a80006','display','91ffbc7929e17bf4e83e05b9e8b93942',25,1,1);
INSERT INTO `sys_stat_log` VALUES (37,1,-1,1080307868,'c0a80006','display','91ffbc7929e17bf4e83e05b9e8b93942',25,1,1);
INSERT INTO `sys_stat_log` VALUES (38,1,-1,1080307889,'c0a80006','display','d798af52726c1805810062c5b0dbec13',-1,1,1);
INSERT INTO `sys_stat_log` VALUES (39,1,-1,1080307890,'c0a80006','display','d798af52726c1805810062c5b0dbec13',-1,1,1);
INSERT INTO `sys_stat_log` VALUES (40,1,-1,1080307891,'c0a80006','display','d798af52726c1805810062c5b0dbec13',-1,1,1);
INSERT INTO `sys_stat_log` VALUES (41,1,-1,1080307894,'c0a80006','display','91ffbc7929e17bf4e83e05b9e8b93942',25,1,1);
INSERT INTO `sys_stat_log` VALUES (42,1,-1,1080307896,'c0a80006','display','d798af52726c1805810062c5b0dbec13',-1,1,1);
INSERT INTO `sys_stat_log` VALUES (43,1,-1,1080308032,'c0a80006','display','d798af52726c1805810062c5b0dbec13',-1,1,1);
INSERT INTO `sys_stat_log` VALUES (44,1,-1,1080308034,'c0a80006','display','d798af52726c1805810062c5b0dbec13',-1,1,1);
INSERT INTO `sys_stat_log` VALUES (45,1,-1,1080308040,'c0a80006','display','d798af52726c1805810062c5b0dbec13',-1,1,1);
INSERT INTO `sys_stat_log` VALUES (46,1,-1,1080308064,'c0a80006','display','d798af52726c1805810062c5b0dbec13',-1,1,1);
INSERT INTO `sys_stat_log` VALUES (47,1,-1,1080308065,'c0a80006','display','d798af52726c1805810062c5b0dbec13',-1,1,1);
INSERT INTO `sys_stat_log` VALUES (48,1,-1,1080313733,'c0a80006','display','d798af52726c1805810062c5b0dbec13',-1,1,1);
INSERT INTO `sys_stat_log` VALUES (49,1,-1,1080313870,'c0a80006','display','d798af52726c1805810062c5b0dbec13',-1,1,1);
INSERT INTO `sys_stat_log` VALUES (50,11,-1,1080313895,'c0a80006','login','d798af52726c1805810062c5b0dbec13',-1,4,3);
INSERT INTO `sys_stat_log` VALUES (51,11,-1,1080313899,'c0a80006','login','d798af52726c1805810062c5b0dbec13',25,2,3);
INSERT INTO `sys_stat_log` VALUES (52,2,-1,1080313899,'c0a80006','display','d798af52726c1805810062c5b0dbec13',25,1,4);
INSERT INTO `sys_stat_log` VALUES (53,4,-1,1080313902,'c0a80006','display','d798af52726c1805810062c5b0dbec13',25,1,6);
INSERT INTO `sys_stat_log` VALUES (54,3,-1,1080313907,'c0a80006','display','d798af52726c1805810062c5b0dbec13',25,1,5);
INSERT INTO `sys_stat_log` VALUES (55,3,-1,1080313909,'c0a80006','toggle','d798af52726c1805810062c5b0dbec13',25,1,5);
INSERT INTO `sys_stat_log` VALUES (56,3,-1,1080313910,'c0a80006','toggle','d798af52726c1805810062c5b0dbec13',25,1,5);
INSERT INTO `sys_stat_log` VALUES (57,3,-1,1080314030,'c0a80006','toggle','d798af52726c1805810062c5b0dbec13',25,1,5);
INSERT INTO `sys_stat_log` VALUES (58,3,-1,1080314032,'c0a80006','toggle','d798af52726c1805810062c5b0dbec13',25,1,5);
INSERT INTO `sys_stat_log` VALUES (59,3,-1,1080314034,'c0a80006','toggle','d798af52726c1805810062c5b0dbec13',25,1,5);
INSERT INTO `sys_stat_log` VALUES (60,3,-1,1080314036,'c0a80006','toggle','d798af52726c1805810062c5b0dbec13',25,1,5);
INSERT INTO `sys_stat_log` VALUES (61,3,-1,1080314038,'c0a80006','toggle','d798af52726c1805810062c5b0dbec13',25,1,5);
INSERT INTO `sys_stat_log` VALUES (62,15,-1,1080314041,'c0a80006','display','d798af52726c1805810062c5b0dbec13',25,1,7);
INSERT INTO `sys_stat_log` VALUES (63,1,-1,1080315187,'c0a80006','display','156661cce17d8630aeef1d1c1c42cf52',-1,1,1);
INSERT INTO `sys_stat_log` VALUES (64,15,-1,1080315859,'c0a80006','display','d798af52726c1805810062c5b0dbec13',25,1,7);
INSERT INTO `sys_stat_log` VALUES (65,2,-1,1080315868,'c0a80006','display','d798af52726c1805810062c5b0dbec13',25,1,4);
INSERT INTO `sys_stat_log` VALUES (66,5,-1,1080315874,'c0a80006','display','d798af52726c1805810062c5b0dbec13',25,4,8);
INSERT INTO `sys_stat_log` VALUES (67,5,-1,1080315879,'c0a80006','toggle','d798af52726c1805810062c5b0dbec13',25,1,8);
INSERT INTO `sys_stat_log` VALUES (68,5,-1,1080316066,'c0a80006','toggle','d798af52726c1805810062c5b0dbec13',25,1,8);
INSERT INTO `sys_stat_log` VALUES (69,5,-1,1080316073,'c0a80006','toggle','d798af52726c1805810062c5b0dbec13',25,1,8);
INSERT INTO `sys_stat_log` VALUES (70,4,-1,1080316076,'c0a80006','display','d798af52726c1805810062c5b0dbec13',25,1,6);
INSERT INTO `sys_stat_log` VALUES (71,4,-1,1080316079,'c0a80006','set_group_access','d798af52726c1805810062c5b0dbec13',25,4,1);
INSERT INTO `sys_stat_log` VALUES (72,4,-1,1080316081,'c0a80006','set_group_access_template','d798af52726c1805810062c5b0dbec13',25,4,1);
INSERT INTO `sys_stat_log` VALUES (73,4,-1,1080316176,'c0a80006','set_group_access_template','d798af52726c1805810062c5b0dbec13',25,4,1);
INSERT INTO `sys_stat_log` VALUES (74,4,-1,1080316179,'c0a80006','set_group_access_template','d798af52726c1805810062c5b0dbec13',25,4,1);
INSERT INTO `sys_stat_log` VALUES (75,4,-1,1080316181,'c0a80006','set_group_access_template','d798af52726c1805810062c5b0dbec13',25,4,1);
INSERT INTO `sys_stat_log` VALUES (76,4,-1,1080316183,'c0a80006','set_group_access_template','d798af52726c1805810062c5b0dbec13',25,4,1);
INSERT INTO `sys_stat_log` VALUES (77,4,-1,1080316185,'c0a80006','set_group_access_template','d798af52726c1805810062c5b0dbec13',25,4,1);
INSERT INTO `sys_stat_log` VALUES (78,4,-1,1080316190,'c0a80006','set_group_access_template','d798af52726c1805810062c5b0dbec13',25,4,1);

/*
Table struture for sys_stat_referer_url
*/

drop table if exists `sys_stat_referer_url`;
CREATE TABLE `sys_stat_referer_url` (
  `id` int(11) NOT NULL auto_increment,
  `referer_url` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `url` (`referer_url`)
) TYPE=InnoDB;


/*
Table struture for sys_stat_search_phrase
*/

drop table if exists `sys_stat_search_phrase`;
CREATE TABLE `sys_stat_search_phrase` (
  `id` int(11) NOT NULL auto_increment,
  `phrase` varchar(255) NOT NULL default '',
  `engine` varchar(255) NOT NULL default '',
  `time` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;


/*
Table struture for sys_stat_uri
*/

drop table if exists `sys_stat_uri`;
CREATE TABLE `sys_stat_uri` (
  `id` int(11) NOT NULL auto_increment,
  `uri` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=InnoDB;

/*
Table data for ptpa.sys_stat_uri
*/

INSERT INTO `sys_stat_uri` VALUES (1,'/root');
INSERT INTO `sys_stat_uri` VALUES (2,'http://serega.ptpa.bit:81/root');
INSERT INTO `sys_stat_uri` VALUES (3,'/root/login');
INSERT INTO `sys_stat_uri` VALUES (4,'/root/admin');
INSERT INTO `sys_stat_uri` VALUES (5,'/root/admin/site_structure');
INSERT INTO `sys_stat_uri` VALUES (6,'/root/admin/classes');
INSERT INTO `sys_stat_uri` VALUES (7,'/root/navigation');
INSERT INTO `sys_stat_uri` VALUES (8,'/root/admin/objects_access');

/*
Table struture for sys_user_object_access_template
*/

drop table if exists `sys_user_object_access_template`;
CREATE TABLE `sys_user_object_access_template` (
  `id` int(11) NOT NULL auto_increment,
  `action_name` char(50) NOT NULL default '',
  `class_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `action_name` (`action_name`),
  KEY `class_id` (`class_id`)
) TYPE=InnoDB COMMENT='InnoDB free: 114688 kB; InnoDB free: 114688 kB; InnoDB free:';


/*
Table struture for sys_user_object_access_template_item
*/

drop table if exists `sys_user_object_access_template_item`;
CREATE TABLE `sys_user_object_access_template_item` (
  `id` int(11) NOT NULL auto_increment,
  `template_id` int(11) default NULL,
  `user_id` int(11) default NULL,
  `r` tinyint(4) default NULL,
  `w` tinyint(4) default NULL,
  PRIMARY KEY  (`id`),
  KEY `template_id` (`template_id`),
  KEY `user_id` (`user_id`)
) TYPE=InnoDB COMMENT='InnoDB free: 114688 kB; InnoDB free: 114688 kB; InnoDB free:';


/*
Table struture for user
*/

drop table if exists `user`;
CREATE TABLE `user` (
  `id` int(11) NOT NULL auto_increment,
  `version` int(11) NOT NULL default '0',
  `object_id` int(11) NOT NULL default '0',
  `name` varchar(100) default NULL,
  `lastname` varchar(100) default NULL,
  `password` varchar(50) NOT NULL default '',
  `email` varchar(50) default NULL,
  `generated_password` varchar(50) default NULL,
  `title` varchar(50) NOT NULL default '',
  `identifier` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `pwd` (`password`),
  KEY `gpwd` (`generated_password`),
  KEY `v` (`version`),
  KEY `oid` (`object_id`)
) TYPE=InnoDB COMMENT='InnoDB free: 10240 kB; InnoDB free: 114688 kB; InnoDB free: ';

/*
Table data for ptpa.user
*/

INSERT INTO `user` VALUES (1,1,7,NULL,'super','',NULL,NULL,'','admin');
INSERT INTO `user` VALUES (2,1,25,'admin','super','66d4aaa5ea177ac32c69946de3731ec0','mike@office.bit',NULL,'','admin');

/*
Table struture for user_group
*/

drop table if exists `user_group`;
CREATE TABLE `user_group` (
  `id` int(11) NOT NULL auto_increment,
  `version` int(11) NOT NULL default '0',
  `object_id` int(11) NOT NULL default '0',
  `title` varchar(50) NOT NULL default '',
  `identifier` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `v` (`version`),
  KEY `oid` (`object_id`)
) TYPE=InnoDB COMMENT='InnoDB free: 10240 kB; InnoDB free: 114688 kB; InnoDB free: ';

/*
Table data for ptpa.user_group
*/

INSERT INTO `user_group` VALUES (3,1,27,'Посетители','visitors');
INSERT INTO `user_group` VALUES (4,1,28,'Администраторы','admins');

/*
Table struture for user_in_group
*/

drop table if exists `user_in_group`;
CREATE TABLE `user_in_group` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL default '0',
  `group_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `group_id` (`group_id`),
  KEY `user_id` (`user_id`)
) TYPE=InnoDB COMMENT='InnoDB free: 10240 kB; InnoDB free: 114688 kB; InnoDB free: ';

/*
Table data for ptpa.user_in_group
*/

INSERT INTO `user_in_group` VALUES (1,25,28);

