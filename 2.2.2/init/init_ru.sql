/* 
SQLyog v3.63
Host - 192.168.0.6 : Database - temp_ru
**************************************************************
Server version 4.0.12-nt
*/

/*
Table struture for cart
*/

drop table if exists `cart`;
CREATE TABLE `cart` (                                                                                                                                                                                                                                                                                                                       
  `id` int(11) NOT NULL auto_increment,                                                                                                                                                                                                                                                                                                     
  `cart_id` varchar(32) NOT NULL default '',                                                                                                                                                                                                                                                                                                
  `user_id` int(11) NOT NULL default '0',                                                                                                                                                                                                                                                                                                   
  `last_activity_time` int(11) NOT NULL default '0',                                                                                                                                                                                                                                                                                        
  `cart_items` blob NOT NULL,                                                                                                                                                                                                                                                                                                               
  PRIMARY KEY  (`id`),                                                                                                                                                                                                                                                                                                                      
  UNIQUE KEY `cart_id` (`cart_id`),                                                                                                                                                                                                                                                                                                         
  KEY `user_id` (`user_id`)                                                                                                                                                                                                                                                                                                                 
  ) TYPE=InnoDB; 

/*
Table struture for document
*/

drop table if exists `document`;
CREATE TABLE `document` (
  `id` int(11) NOT NULL auto_increment,
  `object_id` int(11) NOT NULL default '0',
  `version` int(11) NOT NULL default '0',
  `annotation` varchar(255) NOT NULL default '',
  `content` text NOT NULL,
  `identifier` varchar(50) NOT NULL default '',
  `title` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `ov` (`object_id`,`version`)
) TYPE=InnoDB;

/*
Table data for document
*/

INSERT INTO `document` VALUES 
(1,19,1,'аннотация','Text','root','Главная');

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
Table data for message
*/

INSERT INTO `message` VALUES 
(1,1,16,'Message',NULL,'messages'),
(2,1,34,'Message',NULL,'messages'),
(3,1,55,'Страница не найдена','Страница не найдена','404');

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
  `new_window` tinyint(4) default '0',
  PRIMARY KEY  (`id`),
  KEY `v` (`version`),
  KEY `o` (`object_id`)
) TYPE=InnoDB COMMENT='InnoDB free: 114688 kB; InnoDB free: 114688 kB; InnoDB free:';

/*
Table data for navigation_item
*/

INSERT INTO `navigation_item` VALUES 
(1,1,15,'Навигация','','navigation',0),
(2,1,33,'Навигация','','navigation',0),
(3,1,37,'Администрирование','/root/admin','admin',0),
(4,1,38,'Структура сайта','/root/admin/site_structure','site_structure',0),
(5,1,39,'Навигация','/root/navigation','navigation',0),
(6,2,33,'Навигация','/root/navigation','navigation',0),
(7,2,38,'Управление сайтом','/root/admin','site_management',0),
(8,2,39,'Управление контентом','/root/admin','content_management',0),
(9,1,40,'Навигация','/root/navigation','navigation',0),
(10,1,41,'Структура сайта','/root/admin/site_structure','site_structure',0),
(11,1,42,'Доступ к объектам','/root/admin/objects_access','objects_access',0),
(12,1,43,'Типы объектов','/root/admin/classes','classes',0),
(13,1,44,'Пользователи','/root/users','users',0),
(14,1,45,'Группы пользователей','/root/user_groups','user_groups',0),
(15,1,46,'Служебные сообщения','/root/messages','messages',0),
(16,1,47,'Файлы','/root/files_folder','files',0),
(17,1,48,'Изображения','/root/images_folder','images',0),
(18,1,49,'Меню пользователя','/root','main',0);

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
  UNIQUE KEY `as` (`class_id`,`action_name`,`accessor_id`),
  KEY `accessor_id` (`accessor_id`),
  KEY `accessor_type` (`accessor_type`),
  KEY `class_id` (`class_id`)
) TYPE=InnoDB COMMENT='InnoDB free: 114688 kB; InnoDB free: 114688 kB; InnoDB free:';

/*
Table data for sys_action_access
*/

INSERT INTO `sys_action_access` VALUES 
(237,15,'display',27,0),
(238,15,'display',28,0),
(239,15,'create_message',28,0),
(240,15,'edit',28,0),
(241,15,'delete',28,0),
(319,11,'activate_password',27,0),
(320,11,'activate_password',28,0),
(321,12,'change_own_password',27,0),
(322,12,'change_own_password',28,0),
(323,13,'generate_password',27,0),
(324,13,'generate_password',28,0),
(334,6,'display',28,0),
(335,6,'create_user',28,0),
(336,6,'edit',28,0),
(337,14,'display',28,0),
(338,14,'create_navigation_item',28,0),
(339,14,'edit',28,0),
(340,14,'delete',28,0),
(341,14,'order',28,0),
(342,4,'display',28,0),
(343,4,'set_group_access',28,0),
(344,4,'set_group_access_template',28,0),
(345,7,'display',28,0),
(346,7,'edit',28,0),
(347,7,'set_membership',28,0),
(348,7,'change_password',28,0),
(349,7,'delete',28,0),
(350,8,'display',28,0),
(351,8,'create_user_group',28,0),
(352,9,'display',28,0),
(353,9,'edit',28,0),
(354,9,'delete',28,0),
(358,5,'display',28,0),
(359,5,'set_group_access',28,0),
(360,5,'toggle',28,0),
(363,17,'display',28,0),
(364,17,'create_file',28,0),
(365,17,'create_files_folder',28,0),
(366,17,'edit_files_folder',28,0),
(367,17,'delete',28,0),
(368,17,'file_select',28,0),
(369,19,'display',27,0),
(370,19,'display',28,0),
(371,19,'edit',28,0),
(372,19,'delete',28,0),
(373,22,'display',28,0),
(374,16,'display',28,0),
(375,16,'create_image',28,0),
(376,16,'create_images_folder',28,0),
(377,16,'edit_images_folder',28,0),
(378,16,'delete',28,0),
(379,16,'image_select',28,0),
(380,20,'display',27,0),
(381,20,'display',28,0),
(382,20,'edit',28,0),
(383,20,'edit_variations',28,0),
(384,20,'delete',28,0),
(385,21,'display',28,0),
(386,1,'display',28,0),
(387,1,'admin_display',28,0),
(388,1,'create_document',28,0),
(389,1,'set_metadata',28,0),
(390,1,'edit',28,0),
(391,1,'display',27,0),
(392,26,'display',28,0),
(397,27,'display',28,0),
(398,27,'edit',28,0),
(399,27,'update',28,0),
(400,3,'display',28,0),
(401,3,'toggle',28,0),
(402,3,'move',28,0),
(403,3,'edit',28,0),
(404,3,'node_select',28,0),
(405,3,'save_priority',28,0),
(406,3,'multi_delete',28,0),
(407,3,'multi_toggle_publish_status',28,0),
(408,10,'login',28,0),
(409,10,'logout',28,0),
(410,10,'edit',28,0),
(411,10,'change_user_locale',28,0),
(412,10,'login',27,0),
(413,10,'logout',27,0),
(414,2,'display',28,0),
(415,2,'admin_display',28,0),
(416,2,'register_new_object',28,0),
(417,28,'display',28,0),
(418,28,'edit',28,0),
(419,28,'display',27,0);

/*
Table struture for sys_class
*/

drop table if exists `sys_class`;
CREATE TABLE `sys_class` (
  `id` int(11) NOT NULL auto_increment,
  `class_name` varchar(50) NOT NULL default '',
  `icon` varchar(30) NOT NULL default '',
  `class_ordr` smallint(6) NOT NULL default '0',
  `can_be_parent` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `class` (`class_name`)
) TYPE=InnoDB COMMENT='InnoDB free: 10240 kB; InnoDB free: 10240 kB; InnoDB free: 1';

/*
Table data for sys_class
*/

INSERT INTO `sys_class` VALUES 
(1,'main_page','/shared/images/folder.gif',0,1),
(2,'admin_page','/shared/images/generic.gif',0,1),
(3,'site_structure','/shared/images/generic.gif',1,1),
(4,'class_folder','/shared/images/folder.gif',0,1),
(5,'objects_access','/shared/images/generic.gif',0,0),
(6,'users_folder','/shared/images/folder.gif',0,1),
(7,'user_object','/shared/images/generic.gif',1,0),
(8,'user_groups_folder','/shared/images/folder.gif',0,1),
(9,'user_group','/shared/images/generic.gif',1,0),
(10,'login_object','/shared/images/generic.gif',0,0),
(11,'user_activate_password','/shared/images/generic.gif',0,0),
(12,'user_change_password','/shared/images/generic.gif',0,0),
(13,'user_generate_password','/shared/images/generic.gif',0,0),
(14,'navigation_item','/shared/images/generic.gif',1,1),
(15,'message','/shared/images/generic.gif',0,1),
(16,'images_folder','/shared/images/folder.gif',0,1),
(17,'files_folder','/shared/images/folder.gif',0,1),
(19,'file_object','/shared/images/generic.gif',1,0),
(20,'image_object','/shared/images/generic.gif',1,0),
(21,'image_select','/shared/images/generic.gif',0,0),
(22,'file_select','/shared/images/generic.gif',0,0),
(26,'node_select','/shared/images/generic.gif',0,0),
(27,'site_param_object','/shared/images/generic.gif',1,1),
(28,'not_found_page','/shared/images/generic.gif',0,1);

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
Table data for sys_full_text_index
*/

INSERT INTO `sys_full_text_index` VALUES 
(1,'title',50,29,10,'авторизация'),
(2,'identifier',50,29,10,'login'),
(3,'title',50,30,11,'активировать пароль'),
(4,'identifier',50,30,11,'activate_password'),
(5,'title',50,31,12,'смена пароля'),
(6,'identifier',50,31,12,'change_password'),
(7,'title',50,32,13,'забыли пароль'),
(8,'identifier',50,32,13,'generate_password'),
(9,'title',1,33,14,'навигация'),
(10,'identifier',1,33,14,'navigation'),
(11,'title',1,37,14,'администрирование'),
(12,'identifier',1,37,14,'admin'),
(13,'title',1,38,14,'управление сайтом'),
(14,'identifier',1,38,14,'site_management'),
(15,'title',1,40,14,'навигация'),
(16,'identifier',1,40,14,'navigation'),
(17,'title',1,41,14,'структура сайта'),
(18,'identifier',1,41,14,'site_structure'),
(19,'title',1,42,14,'доступ к объектам'),
(20,'identifier',1,42,14,'objects_access'),
(21,'title',1,43,14,'типы объектов'),
(22,'identifier',1,43,14,'classes'),
(23,'title',1,44,14,'пользователи'),
(24,'identifier',1,44,14,'users'),
(25,'title',1,45,14,'группы пользователей'),
(26,'identifier',1,45,14,'user_groups'),
(27,'title',1,46,14,'служебные сообщения'),
(28,'identifier',1,46,14,'messages'),
(29,'title',1,47,14,'файлы'),
(30,'identifier',1,47,14,'files'),
(31,'title',1,48,14,'изображения'),
(32,'identifier',1,48,14,'images'),
(33,'title',1,39,14,'управление контентом'),
(34,'identifier',1,39,14,'content_management'),
(35,'title',1,49,14,'меню пользователя'),
(36,'identifier',1,49,14,'main'),
(37,'title',50,34,15,'сообщения'),
(38,'identifier',50,34,15,'messages'),
(39,'title',50,35,16,'изображения'),
(40,'identifier',50,35,16,'images_folder'),
(41,'title',50,36,17,'файлы'),
(42,'identifier',50,36,17,'files_folder'),
(43,'title',50,20,2,'администрирование'),
(44,'identifier',50,20,2,'admin'),
(45,'title',50,21,3,'структура сайта'),
(46,'identifier',50,21,3,'site_structure'),
(47,'title',50,53,27,'site params'),
(48,'identifier',50,53,27,'site_params'),
(49,'title',50,22,4,'классы объектов'),
(50,'identifier',50,22,4,'classes'),
(51,'title',50,23,5,'доступ к объектам'),
(52,'identifier',50,23,5,'objects_access'),
(53,'title',50,50,21,'выбор изображения'),
(54,'identifier',50,50,21,'image_select'),
(55,'title',50,51,22,'выбор файла'),
(56,'identifier',50,51,22,'file_select'),
(57,'title',50,52,26,'node select'),
(58,'identifier',50,52,26,'node_select'),
(59,'title',50,24,6,'пользователи'),
(60,'identifier',50,24,6,'users'),
(61,'title',50,25,7,'администрирование'),
(62,'identifier',50,25,7,'admin'),
(63,'title',50,26,8,'группы пользователей'),
(64,'identifier',50,26,8,'user_groups'),
(65,'title',50,28,9,'администраторы'),
(66,'identifier',50,28,9,'admins'),
(67,'title',50,27,9,'посетители'),
(68,'identifier',50,27,9,'visitors'),
(69,'identifier',50,55,15,'404'),
(70,'title',50,55,15,'страница не найдена');

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
Table data for sys_group_object_access_template
*/

INSERT INTO `sys_group_object_access_template` VALUES 
(3,17,'create_file'),
(4,17,'create_files_folder'),
(5,16,'create_image'),
(6,16,'create_images_folder'),
(7,6,'create_user'),
(8,1,'create_document');

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
Table data for sys_group_object_access_template_item
*/

INSERT INTO `sys_group_object_access_template_item` VALUES 
(5,3,27,1,0),
(6,3,28,1,1),
(7,4,27,1,0),
(8,4,28,1,1),
(9,5,27,1,0),
(10,5,28,1,1),
(11,6,27,1,0),
(12,6,28,1,1),
(13,7,28,1,1),
(14,8,28,1,1);

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
Table struture for sys_node_link
*/

drop table if exists `sys_node_link`;
CREATE TABLE `sys_node_link` (
  `id` int(11) NOT NULL auto_increment,
  `linker_node_id` int(11) NOT NULL default '0',
  `target_node_id` int(11) NOT NULL default '0',
  `group_id` int(11) NOT NULL default '0',
  `priority` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `lg` (`linker_node_id`,`group_id`),
  KEY `tg` (`target_node_id`,`group_id`)
) TYPE=InnoDB;


/*
Table struture for sys_node_link_group
*/

drop table if exists `sys_node_link_group`;
CREATE TABLE `sys_node_link_group` (
  `id` int(11) NOT NULL auto_increment,
  `identifier` varchar(50) NOT NULL default '',
  `title` varchar(255) NOT NULL default '',
  `priority` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=InnoDB;

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
  UNIQUE KEY `ora` (`object_id`,`accessor_id`,`r`,`w`),
  KEY `accessor_id` (`accessor_id`),
  KEY `accessor_type` (`accessor_type`)
) TYPE=InnoDB COMMENT='InnoDB free: 114688 kB; InnoDB free: 114688 kB; InnoDB free:';

/*
Table data for sys_object_access
*/

INSERT INTO `sys_object_access` VALUES 
(1,1,9,1,1,0),
(2,1,10,1,1,0),
(3,2,9,1,1,0),
(4,2,10,1,1,0),
(5,3,9,1,1,0),
(6,3,10,1,1,0),
(7,4,9,1,1,0),
(8,4,10,1,1,0),
(9,5,9,1,1,0),
(10,5,10,1,1,0),
(11,6,9,1,1,0),
(12,6,10,1,1,0),
(13,7,9,1,1,0),
(14,7,10,1,1,0),
(15,8,9,1,1,0),
(16,8,10,1,1,0),
(17,9,9,1,1,0),
(18,9,10,1,1,0),
(19,10,9,1,1,0),
(20,10,10,1,1,0),
(21,11,9,1,1,0),
(22,11,10,1,1,0),
(23,12,9,1,1,0),
(24,12,10,1,1,0),
(25,13,9,1,1,0),
(26,13,10,1,1,0),
(27,14,9,1,1,0),
(28,14,10,1,1,0),
(29,15,9,1,1,0),
(30,15,10,1,1,0),
(31,16,9,1,1,0),
(32,16,10,1,1,0),
(33,17,9,1,1,0),
(34,17,10,1,1,0),
(35,18,9,1,1,0),
(36,18,10,1,1,0),
(73,19,27,1,0,0),
(74,19,28,1,1,0),
(75,20,28,1,1,0),
(76,21,28,1,1,0),
(77,22,28,1,1,0),
(78,23,28,1,1,0),
(79,24,27,1,0,0),
(80,24,28,1,1,0),
(81,25,27,1,0,0),
(82,25,28,1,1,0),
(83,26,27,1,0,0),
(84,26,28,1,1,0),
(85,27,27,1,0,0),
(86,27,28,1,1,0),
(87,28,27,1,0,0),
(88,28,28,1,1,0),
(89,29,27,1,0,0),
(90,29,28,1,1,0),
(91,30,27,1,0,0),
(92,30,28,1,1,0),
(93,31,27,1,0,0),
(94,31,28,1,1,0),
(95,32,27,1,0,0),
(96,32,28,1,1,0),
(97,33,27,1,0,0),
(98,33,28,1,1,0),
(99,34,27,1,0,0),
(100,34,28,1,1,0),
(101,35,27,1,0,0),
(102,35,28,1,1,0),
(103,36,27,1,0,0),
(104,36,28,1,1,0),
(105,37,27,1,0,0),
(106,37,28,1,1,0),
(107,38,27,1,0,0),
(108,38,28,1,1,0),
(109,39,27,1,0,0),
(110,39,28,1,1,0),
(111,40,27,1,0,0),
(112,40,28,1,1,0),
(113,41,27,1,0,0),
(114,41,28,1,1,0),
(115,42,27,1,0,0),
(116,42,28,1,1,0),
(117,43,27,1,0,0),
(118,43,28,1,1,0),
(119,44,27,1,0,0),
(120,44,28,1,1,0),
(121,45,27,1,0,0),
(122,45,28,1,1,0),
(123,46,27,1,0,0),
(124,46,28,1,1,0),
(125,47,27,1,0,0),
(126,47,28,1,1,0),
(127,48,27,1,0,0),
(128,48,28,1,1,0),
(129,49,27,1,0,0),
(130,49,28,1,1,0),
(131,50,27,1,0,0),
(132,50,28,1,1,0),
(133,51,27,1,0,0),
(134,51,28,1,1,0),
(135,52,27,1,0,0),
(136,52,28,1,1,0),
(137,53,28,1,1,0),
(138,54,27,1,0,0),
(139,54,28,1,1,0),
(140,55,27,1,0,0),
(141,55,28,1,1,0);

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
  UNIQUE KEY `object_id` (`object_id`,`version`),
  KEY `cid` (`creator_id`),
  KEY `md` (`modified_date`),
  KEY `cd` (`created_date`),
  KEY `v` (`version`)
) TYPE=InnoDB COMMENT='InnoDB free: 10240 kB; InnoDB free: 114688 kB; InnoDB free: ';

/*
Table data for sys_object_version
*/

INSERT INTO `sys_object_version` VALUES 
(6,25,0,1076762314,1076762314,1),
(7,27,0,1076762314,1076762314,1),
(8,28,0,1076762314,1076762314,1),
(9,33,0,1076762315,1076762315,1),
(10,34,0,1076762315,1076762315,1),
(11,37,25,1076770835,1076770835,1),
(12,38,25,1076770879,1076770879,1),
(13,39,25,1076771149,1076771149,1),
(14,33,25,1076771224,1076771224,2),
(15,38,25,1076771356,1076771356,2),
(16,39,25,1076771416,1076771416,2),
(17,40,25,1076771605,1076771605,1),
(18,41,25,1076772382,1076772382,1),
(19,42,25,1076772439,1076772439,1),
(20,43,25,1076772480,1076772480,1),
(21,44,25,1076772520,1076772520,1),
(22,45,25,1076772541,1076772541,1),
(23,46,25,1076772578,1076772578,1),
(24,47,25,1076772601,1076772601,1),
(25,48,25,1076772623,1076772623,1),
(26,49,25,1076772668,1076772668,1),
(27,19,25,1076762314,1076762314,1),
(28,55,25,1086440706,1086440706,1);

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
  UNIQUE KEY `idccv` (`class_id`,`id`,`current_version`,`locale_id`),
  KEY `md` (`modified_date`),
  KEY `cd` (`created_date`),
  KEY `cid` (`creator_id`),
  KEY `current_version` (`current_version`)
) TYPE=InnoDB COMMENT='InnoDB free: 10240 kB; InnoDB free: 10240 kB; InnoDB free: 1';

/*
Table data for sys_site_object
*/

INSERT INTO `sys_site_object` VALUES 
(19,1,1,1076768404,0,1076762314,0,'ru','Главная','root'),
(20,2,1,1076762314,0,1076762314,0,'ru','Администрирование','admin'),
(21,3,2,1076769130,0,1076762314,0,'ru','Структура сайта','site_structure'),
(22,4,2,1076769267,0,1076762314,0,'ru','Классы объектов','classes'),
(23,5,2,1076769145,0,1076762314,0,'ru','Доступ к объектам','objects_access'),
(24,6,2,1076769160,0,1076762314,0,'ru','Пользователи','users'),
(25,7,1,1076762588,0,1076762314,0,'ru','Администрирование','admin'),
(26,8,2,1076769173,0,1076762314,0,'ru','Группы пользователей','user_groups'),
(27,9,1,1076762314,0,1076762314,0,'ru','Посетители','visitors'),
(28,9,1,1076762314,0,1076762314,0,'ru','Администраторы','admins'),
(29,10,2,1076769188,0,1076762314,0,'ru','Авторизация','login'),
(30,11,2,1076769202,0,1076762314,0,'ru','Активировать пароль','activate_password'),
(31,12,2,1076769224,0,1076762314,0,'ru','Смена пароля','change_password'),
(32,13,2,1076769246,0,1076762315,0,'ru','Забыли пароль?','generate_password'),
(33,14,2,1076771224,0,1076762315,0,'ru','Навигация','navigation'),
(34,15,1,1076762315,0,1076762315,0,'ru','Сообщения','messages'),
(35,16,1,1076762315,0,1076762315,0,'ru','Изображения','images_folder'),
(36,17,1,1076762315,0,1076762315,0,'ru','Файлы','files_folder'),
(37,14,1,1076770835,0,1076770835,25,'ru','Администрирование','admin'),
(38,14,2,1076771356,0,1076770879,25,'ru','Управление сайтом','site_management'),
(39,14,2,1076771416,0,1076771149,25,'ru','Управление контентом','content_management'),
(40,14,1,1076771604,0,1076771604,25,'ru','Навигация','navigation'),
(41,14,1,1076772382,0,1076772382,25,'ru','Структура сайта','site_structure'),
(42,14,1,1076772439,0,1076772439,25,'ru','Доступ к объектам','objects_access'),
(43,14,1,1076772480,0,1076772480,25,'ru','Типы объектов','classes'),
(44,14,1,1076772520,0,1076772520,25,'ru','Пользователи','users'),
(45,14,1,1076772540,0,1076772540,25,'ru','Группы пользователей','user_groups'),
(46,14,1,1076772578,0,1076772578,25,'ru','Служебные сообщения','messages'),
(47,14,1,1076772601,0,1076772601,25,'ru','Файлы','files'),
(48,14,1,1076772623,0,1076772623,25,'ru','Изображения','images'),
(49,14,1,1076772668,0,1076772668,25,'ru','Меню пользователя','main'),
(50,21,1,1079691597,0,1079691597,25,'ru','Выбор изображения','image_select'),
(51,22,1,1079691620,0,1079691620,25,'ru','Выбор файла','file_select'),
(52,26,1,1084270888,0,1084270888,25,'ru','Node select','node_select'),
(53,27,1,1084270920,0,1084270920,25,'ru','Site params','site_params'),
(54,28,1,1086440668,0,1086440668,25,'ru','Страница не найдена','404'),
(55,15,1,1086440706,0,1086440706,25,'ru','Страница не найдена','404');

/*
Table struture for sys_site_object_tree
*/

drop table if exists `sys_site_object_tree`;
CREATE TABLE `sys_site_object_tree` (
  `id` int(11) NOT NULL auto_increment,
  `root_id` int(11) NOT NULL default '0',
  `parent_id` int(11) NOT NULL default '0',
  `priority` int(11) NOT NULL default '0',
  `level` int(11) NOT NULL default '0',
  `identifier` varchar(128) NOT NULL default '',
  `object_id` int(11) NOT NULL default '0',
  `path` varchar(255) NOT NULL default '',
  `children` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `op` (`object_id`,`id`,`path`),
  UNIQUE KEY `ipl` (`identifier`,`path`,`level`),
  UNIQUE KEY `path_level` (`path`,`level`),
  KEY `root_id` (`root_id`),
  KEY `identifier` (`identifier`),
  KEY `level` (`level`),
  KEY `rlr` (`root_id`),
  KEY `parent_id` (`parent_id`),
  KEY `object_id` (`object_id`)
) TYPE=InnoDB;

/*
Table data for sys_site_object_tree
*/

INSERT INTO `sys_site_object_tree` VALUES 
(1,1,0,0,1,'root',19,'/1/',15),
(2,1,1,0,2,'admin',20,'/1/2/',4),
(3,1,2,0,3,'site_structure',21,'/1/2/3/',0),
(4,1,2,0,3,'classes',22,'/1/2/4/',0),
(5,1,2,0,3,'objects_access',23,'/1/2/5/',0),
(6,1,1,0,2,'users',24,'/1/6/',1),
(7,1,6,0,3,'admin',25,'/1/6/7/',0),
(8,1,1,0,2,'user_groups',26,'/1/8/',2),
(9,1,8,0,3,'visitors',27,'/1/8/9/',0),
(10,1,8,0,3,'admins',28,'/1/8/10/',0),
(11,1,1,0,2,'login',29,'/1/11/',0),
(12,1,1,0,2,'activate_password',30,'/1/12/',0),
(13,1,1,0,2,'change_password',31,'/1/13/',0),
(14,1,1,0,2,'generate_password',32,'/1/14/',0),
(15,1,1,0,2,'navigation',33,'/1/15/',2),
(16,1,1,0,2,'messages',34,'/1/16/',1),
(17,1,1,0,2,'images_folder',35,'/1/17/',0),
(18,1,1,0,2,'files_folder',36,'/1/18/',0),
(19,1,15,1,3,'admin',37,'/1/15/19/',2),
(20,1,19,2,4,'site_management',38,'/1/15/19/20/',9),
(21,1,19,1,4,'content_management',39,'/1/15/19/21/',0),
(22,1,20,6,5,'navigation',40,'/1/15/19/20/22/',0),
(23,1,20,1,5,'site_structure',41,'/1/15/19/20/23/',0),
(24,1,20,2,5,'objects_access',42,'/1/15/19/20/24/',0),
(25,1,20,3,5,'classes',43,'/1/15/19/20/25/',0),
(26,1,20,4,5,'users',44,'/1/15/19/20/26/',0),
(27,1,20,5,5,'user_groups',45,'/1/15/19/20/27/',0),
(28,1,20,9,5,'messages',46,'/1/15/19/20/28/',0),
(29,1,20,8,5,'files',47,'/1/15/19/20/29/',0),
(30,1,20,7,5,'images',48,'/1/15/19/20/30/',0),
(31,1,15,2,3,'main',49,'/1/15/31/',0),
(32,1,1,0,2,'image_select',50,'/1/32/',0),
(33,1,1,0,2,'file_select',51,'/1/33/',0),
(34,1,1,0,2,'node_select',52,'/1/34/',0),
(35,1,2,0,3,'site_params',53,'/1/2/35/',0),
(36,1,1,0,2,'404',54,'/1/36/',0),
(37,1,16,0,3,'404',55,'/1/16/37/',0);

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
Table struture for sys_stat_ip
*/

drop table if exists `sys_stat_ip`;
CREATE TABLE `sys_stat_ip` (
  `id` varchar(8) NOT NULL default '0',
  `time` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=InnoDB;


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
) TYPE=InnoDB;


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
  UNIQUE KEY `object_id` (`object_id`,`version`),
  KEY `pwd` (`password`),
  KEY `gpwd` (`generated_password`),
  KEY `v` (`version`),
  KEY `oid` (`object_id`)
) TYPE=InnoDB COMMENT='InnoDB free: 10240 kB; InnoDB free: 114688 kB; InnoDB free: ';

/*
Table data for user
*/

INSERT INTO `user` VALUES 
(2,1,25,'admin','super','66d4aaa5ea177ac32c69946de3731ec0','mike@office.bit',NULL,'','admin');

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
Table data for user_group
*/

INSERT INTO `user_group` VALUES 
(3,1,27,'Посетители','visitors'),
(4,1,28,'Администраторы','admins');

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
Table data for user_in_group
*/

INSERT INTO `user_in_group` VALUES 
(1,25,28);