/* 
SQLyog v3.63
Host - localhost : Database - temp_ru
**************************************************************
Server version 4.0.12-nt
*/

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
Table data for temp_ru.document
*/

INSERT INTO `document` VALUES (1,19,1,'аннотация','Text','root','Главная');

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
Table data for temp_ru.message
*/

INSERT INTO `message` VALUES (1,1,16,'Message',NULL,'messages');
INSERT INTO `message` VALUES (2,1,34,'Message',NULL,'messages');
INSERT INTO `message` VALUES (3,1,55,'Страница не найдена','Страница не найдена','404');

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
  UNIQUE KEY `object_id` (`object_id`,`version`)
) TYPE=InnoDB COMMENT='InnoDB free: 114688 kB; InnoDB free: 114688 kB; InnoDB free:';

/*
Table data for temp_ru.navigation_item
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
  UNIQUE KEY `as` (`class_id`,`action_name`,`accessor_id`),
  KEY `accessor_id` (`accessor_id`),
  KEY `accessor_type` (`accessor_type`),
  KEY `class_id` (`class_id`)
) TYPE=InnoDB COMMENT='InnoDB free: 114688 kB; InnoDB free: 114688 kB; InnoDB free:';

/*
Table data for temp_ru.sys_action_access
*/

INSERT INTO `sys_action_access` VALUES (237,15,'display',27,0);
INSERT INTO `sys_action_access` VALUES (238,15,'display',28,0);
INSERT INTO `sys_action_access` VALUES (239,15,'create_message',28,0);
INSERT INTO `sys_action_access` VALUES (240,15,'edit',28,0);
INSERT INTO `sys_action_access` VALUES (241,15,'delete',28,0);
INSERT INTO `sys_action_access` VALUES (319,11,'activate_password',27,0);
INSERT INTO `sys_action_access` VALUES (320,11,'activate_password',28,0);
INSERT INTO `sys_action_access` VALUES (321,12,'change_own_password',27,0);
INSERT INTO `sys_action_access` VALUES (322,12,'change_own_password',28,0);
INSERT INTO `sys_action_access` VALUES (323,13,'generate_password',27,0);
INSERT INTO `sys_action_access` VALUES (324,13,'generate_password',28,0);
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
INSERT INTO `sys_action_access` VALUES (345,7,'display',28,0);
INSERT INTO `sys_action_access` VALUES (346,7,'edit',28,0);
INSERT INTO `sys_action_access` VALUES (347,7,'set_membership',28,0);
INSERT INTO `sys_action_access` VALUES (348,7,'change_password',28,0);
INSERT INTO `sys_action_access` VALUES (349,7,'delete',28,0);
INSERT INTO `sys_action_access` VALUES (350,8,'display',28,0);
INSERT INTO `sys_action_access` VALUES (351,8,'create_user_group',28,0);
INSERT INTO `sys_action_access` VALUES (352,9,'display',28,0);
INSERT INTO `sys_action_access` VALUES (353,9,'edit',28,0);
INSERT INTO `sys_action_access` VALUES (354,9,'delete',28,0);
INSERT INTO `sys_action_access` VALUES (358,5,'display',28,0);
INSERT INTO `sys_action_access` VALUES (359,5,'set_group_access',28,0);
INSERT INTO `sys_action_access` VALUES (360,5,'toggle',28,0);
INSERT INTO `sys_action_access` VALUES (363,17,'display',28,0);
INSERT INTO `sys_action_access` VALUES (364,17,'create_file',28,0);
INSERT INTO `sys_action_access` VALUES (365,17,'create_files_folder',28,0);
INSERT INTO `sys_action_access` VALUES (366,17,'edit_files_folder',28,0);
INSERT INTO `sys_action_access` VALUES (367,17,'delete',28,0);
INSERT INTO `sys_action_access` VALUES (368,17,'file_select',28,0);
INSERT INTO `sys_action_access` VALUES (369,19,'display',27,0);
INSERT INTO `sys_action_access` VALUES (370,19,'display',28,0);
INSERT INTO `sys_action_access` VALUES (371,19,'edit',28,0);
INSERT INTO `sys_action_access` VALUES (372,19,'delete',28,0);
INSERT INTO `sys_action_access` VALUES (373,22,'display',28,0);
INSERT INTO `sys_action_access` VALUES (374,16,'display',28,0);
INSERT INTO `sys_action_access` VALUES (375,16,'create_image',28,0);
INSERT INTO `sys_action_access` VALUES (376,16,'create_images_folder',28,0);
INSERT INTO `sys_action_access` VALUES (377,16,'edit_images_folder',28,0);
INSERT INTO `sys_action_access` VALUES (378,16,'delete',28,0);
INSERT INTO `sys_action_access` VALUES (379,16,'image_select',28,0);
INSERT INTO `sys_action_access` VALUES (380,20,'display',27,0);
INSERT INTO `sys_action_access` VALUES (381,20,'display',28,0);
INSERT INTO `sys_action_access` VALUES (382,20,'edit',28,0);
INSERT INTO `sys_action_access` VALUES (383,20,'edit_variations',28,0);
INSERT INTO `sys_action_access` VALUES (384,20,'delete',28,0);
INSERT INTO `sys_action_access` VALUES (385,21,'display',28,0);
INSERT INTO `sys_action_access` VALUES (386,1,'display',28,0);
INSERT INTO `sys_action_access` VALUES (387,1,'admin_display',28,0);
INSERT INTO `sys_action_access` VALUES (388,1,'create_document',28,0);
INSERT INTO `sys_action_access` VALUES (389,1,'set_metadata',28,0);
INSERT INTO `sys_action_access` VALUES (390,1,'edit',28,0);
INSERT INTO `sys_action_access` VALUES (391,1,'display',27,0);
INSERT INTO `sys_action_access` VALUES (392,26,'display',28,0);
INSERT INTO `sys_action_access` VALUES (397,27,'display',28,0);
INSERT INTO `sys_action_access` VALUES (398,27,'edit',28,0);
INSERT INTO `sys_action_access` VALUES (399,27,'update',28,0);
INSERT INTO `sys_action_access` VALUES (400,3,'display',28,0);
INSERT INTO `sys_action_access` VALUES (401,3,'toggle',28,0);
INSERT INTO `sys_action_access` VALUES (402,3,'move',28,0);
INSERT INTO `sys_action_access` VALUES (403,3,'edit',28,0);
INSERT INTO `sys_action_access` VALUES (404,3,'node_select',28,0);
INSERT INTO `sys_action_access` VALUES (405,3,'save_priority',28,0);
INSERT INTO `sys_action_access` VALUES (406,3,'multi_delete',28,0);
INSERT INTO `sys_action_access` VALUES (407,3,'multi_toggle_publish_status',28,0);
INSERT INTO `sys_action_access` VALUES (408,10,'login',28,0);
INSERT INTO `sys_action_access` VALUES (409,10,'logout',28,0);
INSERT INTO `sys_action_access` VALUES (410,10,'edit',28,0);
INSERT INTO `sys_action_access` VALUES (411,10,'change_user_locale',28,0);
INSERT INTO `sys_action_access` VALUES (412,10,'login',27,0);
INSERT INTO `sys_action_access` VALUES (413,10,'logout',27,0);
INSERT INTO `sys_action_access` VALUES (414,2,'display',28,0);
INSERT INTO `sys_action_access` VALUES (415,2,'admin_display',28,0);
INSERT INTO `sys_action_access` VALUES (416,2,'register_new_object',28,0);
INSERT INTO `sys_action_access` VALUES (417,28,'display',28,0);
INSERT INTO `sys_action_access` VALUES (418,28,'edit',28,0);
INSERT INTO `sys_action_access` VALUES (419,28,'display',27,0);

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
  KEY `class` (`class_name`),
  KEY `can_be_parent` (`can_be_parent`)
) TYPE=InnoDB COMMENT='InnoDB free: 10240 kB; InnoDB free: 10240 kB; InnoDB free: 1';

/*
Table data for temp_ru.sys_class
*/

INSERT INTO `sys_class` VALUES (1,'main_page','/shared/images/folder.gif',0,1);
INSERT INTO `sys_class` VALUES (2,'admin_page','/shared/images/generic.gif',0,1);
INSERT INTO `sys_class` VALUES (3,'site_structure','/shared/images/generic.gif',1,1);
INSERT INTO `sys_class` VALUES (4,'class_folder','/shared/images/folder.gif',0,1);
INSERT INTO `sys_class` VALUES (5,'objects_access','/shared/images/generic.gif',0,0);
INSERT INTO `sys_class` VALUES (6,'users_folder','/shared/images/folder.gif',0,1);
INSERT INTO `sys_class` VALUES (7,'user_object','/shared/images/generic.gif',1,0);
INSERT INTO `sys_class` VALUES (8,'user_groups_folder','/shared/images/folder.gif',0,1);
INSERT INTO `sys_class` VALUES (9,'user_group','/shared/images/generic.gif',1,0);
INSERT INTO `sys_class` VALUES (10,'login_object','/shared/images/generic.gif',0,0);
INSERT INTO `sys_class` VALUES (11,'user_activate_password','/shared/images/generic.gif',0,0);
INSERT INTO `sys_class` VALUES (12,'user_change_password','/shared/images/generic.gif',0,0);
INSERT INTO `sys_class` VALUES (13,'user_generate_password','/shared/images/generic.gif',0,0);
INSERT INTO `sys_class` VALUES (14,'navigation_item','/shared/images/generic.gif',1,1);
INSERT INTO `sys_class` VALUES (15,'message','/shared/images/generic.gif',0,1);
INSERT INTO `sys_class` VALUES (16,'images_folder','/shared/images/folder.gif',0,1);
INSERT INTO `sys_class` VALUES (17,'files_folder','/shared/images/folder.gif',0,1);
INSERT INTO `sys_class` VALUES (19,'file_object','/shared/images/generic.gif',1,0);
INSERT INTO `sys_class` VALUES (20,'image_object','/shared/images/generic.gif',1,0);
INSERT INTO `sys_class` VALUES (21,'image_select','/shared/images/generic.gif',0,0);
INSERT INTO `sys_class` VALUES (22,'file_select','/shared/images/generic.gif',0,0);
INSERT INTO `sys_class` VALUES (26,'node_select','/shared/images/generic.gif',0,0);
INSERT INTO `sys_class` VALUES (27,'site_param_object','/shared/images/generic.gif',1,1);
INSERT INTO `sys_class` VALUES (28,'not_found_page','/shared/images/generic.gif',0,1);

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
  FULLTEXT KEY `body` (`body`)
) TYPE=MyISAM;

/*
Table data for temp_ru.sys_full_text_index
*/

INSERT INTO `sys_full_text_index` VALUES (1,'title',50,29,10,'авторизация');
INSERT INTO `sys_full_text_index` VALUES (2,'identifier',50,29,10,'login');
INSERT INTO `sys_full_text_index` VALUES (3,'title',50,30,11,'активировать пароль');
INSERT INTO `sys_full_text_index` VALUES (4,'identifier',50,30,11,'activate_password');
INSERT INTO `sys_full_text_index` VALUES (5,'title',50,31,12,'смена пароля');
INSERT INTO `sys_full_text_index` VALUES (6,'identifier',50,31,12,'change_password');
INSERT INTO `sys_full_text_index` VALUES (7,'title',50,32,13,'забыли пароль');
INSERT INTO `sys_full_text_index` VALUES (8,'identifier',50,32,13,'generate_password');
INSERT INTO `sys_full_text_index` VALUES (9,'title',1,33,14,'навигация');
INSERT INTO `sys_full_text_index` VALUES (10,'identifier',1,33,14,'navigation');
INSERT INTO `sys_full_text_index` VALUES (11,'title',1,37,14,'администрирование');
INSERT INTO `sys_full_text_index` VALUES (12,'identifier',1,37,14,'admin');
INSERT INTO `sys_full_text_index` VALUES (13,'title',1,38,14,'управление сайтом');
INSERT INTO `sys_full_text_index` VALUES (14,'identifier',1,38,14,'site_management');
INSERT INTO `sys_full_text_index` VALUES (15,'title',1,40,14,'навигация');
INSERT INTO `sys_full_text_index` VALUES (16,'identifier',1,40,14,'navigation');
INSERT INTO `sys_full_text_index` VALUES (17,'title',1,41,14,'структура сайта');
INSERT INTO `sys_full_text_index` VALUES (18,'identifier',1,41,14,'site_structure');
INSERT INTO `sys_full_text_index` VALUES (19,'title',1,42,14,'доступ к объектам');
INSERT INTO `sys_full_text_index` VALUES (20,'identifier',1,42,14,'objects_access');
INSERT INTO `sys_full_text_index` VALUES (21,'title',1,43,14,'типы объектов');
INSERT INTO `sys_full_text_index` VALUES (22,'identifier',1,43,14,'classes');
INSERT INTO `sys_full_text_index` VALUES (23,'title',1,44,14,'пользователи');
INSERT INTO `sys_full_text_index` VALUES (24,'identifier',1,44,14,'users');
INSERT INTO `sys_full_text_index` VALUES (25,'title',1,45,14,'группы пользователей');
INSERT INTO `sys_full_text_index` VALUES (26,'identifier',1,45,14,'user_groups');
INSERT INTO `sys_full_text_index` VALUES (27,'title',1,46,14,'служебные сообщения');
INSERT INTO `sys_full_text_index` VALUES (28,'identifier',1,46,14,'messages');
INSERT INTO `sys_full_text_index` VALUES (29,'title',1,47,14,'файлы');
INSERT INTO `sys_full_text_index` VALUES (30,'identifier',1,47,14,'files');
INSERT INTO `sys_full_text_index` VALUES (31,'title',1,48,14,'изображения');
INSERT INTO `sys_full_text_index` VALUES (32,'identifier',1,48,14,'images');
INSERT INTO `sys_full_text_index` VALUES (33,'title',1,39,14,'управление контентом');
INSERT INTO `sys_full_text_index` VALUES (34,'identifier',1,39,14,'content_management');
INSERT INTO `sys_full_text_index` VALUES (35,'title',1,49,14,'меню пользователя');
INSERT INTO `sys_full_text_index` VALUES (36,'identifier',1,49,14,'main');
INSERT INTO `sys_full_text_index` VALUES (37,'title',50,34,15,'сообщения');
INSERT INTO `sys_full_text_index` VALUES (38,'identifier',50,34,15,'messages');
INSERT INTO `sys_full_text_index` VALUES (39,'title',50,35,16,'изображения');
INSERT INTO `sys_full_text_index` VALUES (40,'identifier',50,35,16,'images_folder');
INSERT INTO `sys_full_text_index` VALUES (41,'title',50,36,17,'файлы');
INSERT INTO `sys_full_text_index` VALUES (42,'identifier',50,36,17,'files_folder');
INSERT INTO `sys_full_text_index` VALUES (43,'title',50,20,2,'администрирование');
INSERT INTO `sys_full_text_index` VALUES (44,'identifier',50,20,2,'admin');
INSERT INTO `sys_full_text_index` VALUES (45,'title',50,21,3,'структура сайта');
INSERT INTO `sys_full_text_index` VALUES (46,'identifier',50,21,3,'site_structure');
INSERT INTO `sys_full_text_index` VALUES (47,'title',50,53,27,'site params');
INSERT INTO `sys_full_text_index` VALUES (48,'identifier',50,53,27,'site_params');
INSERT INTO `sys_full_text_index` VALUES (49,'title',50,22,4,'классы объектов');
INSERT INTO `sys_full_text_index` VALUES (50,'identifier',50,22,4,'classes');
INSERT INTO `sys_full_text_index` VALUES (51,'title',50,23,5,'доступ к объектам');
INSERT INTO `sys_full_text_index` VALUES (52,'identifier',50,23,5,'objects_access');
INSERT INTO `sys_full_text_index` VALUES (53,'title',50,50,21,'выбор изображения');
INSERT INTO `sys_full_text_index` VALUES (54,'identifier',50,50,21,'image_select');
INSERT INTO `sys_full_text_index` VALUES (55,'title',50,51,22,'выбор файла');
INSERT INTO `sys_full_text_index` VALUES (56,'identifier',50,51,22,'file_select');
INSERT INTO `sys_full_text_index` VALUES (57,'title',50,52,26,'node select');
INSERT INTO `sys_full_text_index` VALUES (58,'identifier',50,52,26,'node_select');
INSERT INTO `sys_full_text_index` VALUES (59,'title',50,24,6,'пользователи');
INSERT INTO `sys_full_text_index` VALUES (60,'identifier',50,24,6,'users');
INSERT INTO `sys_full_text_index` VALUES (61,'title',50,25,7,'администрирование');
INSERT INTO `sys_full_text_index` VALUES (62,'identifier',50,25,7,'admin');
INSERT INTO `sys_full_text_index` VALUES (63,'title',50,26,8,'группы пользователей');
INSERT INTO `sys_full_text_index` VALUES (64,'identifier',50,26,8,'user_groups');
INSERT INTO `sys_full_text_index` VALUES (65,'title',50,28,9,'администраторы');
INSERT INTO `sys_full_text_index` VALUES (66,'identifier',50,28,9,'admins');
INSERT INTO `sys_full_text_index` VALUES (67,'title',50,27,9,'посетители');
INSERT INTO `sys_full_text_index` VALUES (68,'identifier',50,27,9,'visitors');
INSERT INTO `sys_full_text_index` VALUES (69,'identifier',50,55,15,'404');
INSERT INTO `sys_full_text_index` VALUES (70,'title',50,55,15,'страница не найдена');

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
Table data for temp_ru.sys_group_object_access_template
*/

INSERT INTO `sys_group_object_access_template` VALUES (3,17,'create_file');
INSERT INTO `sys_group_object_access_template` VALUES (4,17,'create_files_folder');
INSERT INTO `sys_group_object_access_template` VALUES (5,16,'create_image');
INSERT INTO `sys_group_object_access_template` VALUES (6,16,'create_images_folder');
INSERT INTO `sys_group_object_access_template` VALUES (7,6,'create_user');
INSERT INTO `sys_group_object_access_template` VALUES (8,1,'create_document');

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
Table data for temp_ru.sys_group_object_access_template_item
*/

INSERT INTO `sys_group_object_access_template_item` VALUES (5,3,27,1,0);
INSERT INTO `sys_group_object_access_template_item` VALUES (6,3,28,1,1);
INSERT INTO `sys_group_object_access_template_item` VALUES (7,4,27,1,0);
INSERT INTO `sys_group_object_access_template_item` VALUES (8,4,28,1,1);
INSERT INTO `sys_group_object_access_template_item` VALUES (9,5,27,1,0);
INSERT INTO `sys_group_object_access_template_item` VALUES (10,5,28,1,1);
INSERT INTO `sys_group_object_access_template_item` VALUES (11,6,27,1,0);
INSERT INTO `sys_group_object_access_template_item` VALUES (12,6,28,1,1);
INSERT INTO `sys_group_object_access_template_item` VALUES (13,7,28,1,1);
INSERT INTO `sys_group_object_access_template_item` VALUES (14,8,28,1,1);

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
Table data for temp_ru.sys_object_access
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
INSERT INTO `sys_object_access` VALUES (135,52,27,1,0,0);
INSERT INTO `sys_object_access` VALUES (136,52,28,1,1,0);
INSERT INTO `sys_object_access` VALUES (137,53,28,1,1,0);
INSERT INTO `sys_object_access` VALUES (138,54,27,1,0,0);
INSERT INTO `sys_object_access` VALUES (139,54,28,1,1,0);
INSERT INTO `sys_object_access` VALUES (140,55,27,1,0,0);
INSERT INTO `sys_object_access` VALUES (141,55,28,1,1,0);

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
  KEY `cd` (`created_date`)
) TYPE=InnoDB COMMENT='InnoDB free: 10240 kB; InnoDB free: 114688 kB; InnoDB free: ';

/*
Table data for temp_ru.sys_object_version
*/

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
INSERT INTO `sys_object_version` VALUES (27,19,25,1076762314,1076762314,1);
INSERT INTO `sys_object_version` VALUES (28,55,25,1086440706,1086440706,1);

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
Table data for temp_ru.sys_site_object
*/

INSERT INTO `sys_site_object` VALUES (19,1,1,1076768404,0,1076762314,0,'ru','Главная','root');
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
INSERT INTO `sys_site_object` VALUES (52,26,1,1084270888,0,1084270888,25,'ru','Node select','node_select');
INSERT INTO `sys_site_object` VALUES (53,27,1,1084270920,0,1084270920,25,'ru','Site params','site_params');
INSERT INTO `sys_site_object` VALUES (54,28,1,1086440668,0,1086440668,25,'ru','Страница не найдена','404');
INSERT INTO `sys_site_object` VALUES (55,15,1,1086440706,0,1086440706,25,'ru','Страница не найдена','404');

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
Table data for temp_ru.sys_site_object_tree
*/

INSERT INTO `sys_site_object_tree` VALUES (1,1,0,0,1,'root',19,'/1/',15);
INSERT INTO `sys_site_object_tree` VALUES (2,1,1,0,2,'admin',20,'/1/2/',4);
INSERT INTO `sys_site_object_tree` VALUES (3,1,2,0,3,'site_structure',21,'/1/2/3/',0);
INSERT INTO `sys_site_object_tree` VALUES (4,1,2,0,3,'classes',22,'/1/2/4/',0);
INSERT INTO `sys_site_object_tree` VALUES (5,1,2,0,3,'objects_access',23,'/1/2/5/',0);
INSERT INTO `sys_site_object_tree` VALUES (6,1,1,0,2,'users',24,'/1/6/',1);
INSERT INTO `sys_site_object_tree` VALUES (7,1,6,0,3,'admin',25,'/1/6/7/',0);
INSERT INTO `sys_site_object_tree` VALUES (8,1,1,0,2,'user_groups',26,'/1/8/',2);
INSERT INTO `sys_site_object_tree` VALUES (9,1,8,0,3,'visitors',27,'/1/8/9/',0);
INSERT INTO `sys_site_object_tree` VALUES (10,1,8,0,3,'admins',28,'/1/8/10/',0);
INSERT INTO `sys_site_object_tree` VALUES (11,1,1,0,2,'login',29,'/1/11/',0);
INSERT INTO `sys_site_object_tree` VALUES (12,1,1,0,2,'activate_password',30,'/1/12/',0);
INSERT INTO `sys_site_object_tree` VALUES (13,1,1,0,2,'change_password',31,'/1/13/',0);
INSERT INTO `sys_site_object_tree` VALUES (14,1,1,0,2,'generate_password',32,'/1/14/',0);
INSERT INTO `sys_site_object_tree` VALUES (15,1,1,0,2,'navigation',33,'/1/15/',2);
INSERT INTO `sys_site_object_tree` VALUES (16,1,1,0,2,'messages',34,'/1/16/',1);
INSERT INTO `sys_site_object_tree` VALUES (17,1,1,0,2,'images_folder',35,'/1/17/',0);
INSERT INTO `sys_site_object_tree` VALUES (18,1,1,0,2,'files_folder',36,'/1/18/',0);
INSERT INTO `sys_site_object_tree` VALUES (19,1,15,1,3,'admin',37,'/1/15/19/',2);
INSERT INTO `sys_site_object_tree` VALUES (20,1,19,2,4,'site_management',38,'/1/15/19/20/',9);
INSERT INTO `sys_site_object_tree` VALUES (21,1,19,1,4,'content_management',39,'/1/15/19/21/',0);
INSERT INTO `sys_site_object_tree` VALUES (22,1,20,6,5,'navigation',40,'/1/15/19/20/22/',0);
INSERT INTO `sys_site_object_tree` VALUES (23,1,20,1,5,'site_structure',41,'/1/15/19/20/23/',0);
INSERT INTO `sys_site_object_tree` VALUES (24,1,20,2,5,'objects_access',42,'/1/15/19/20/24/',0);
INSERT INTO `sys_site_object_tree` VALUES (25,1,20,3,5,'classes',43,'/1/15/19/20/25/',0);
INSERT INTO `sys_site_object_tree` VALUES (26,1,20,4,5,'users',44,'/1/15/19/20/26/',0);
INSERT INTO `sys_site_object_tree` VALUES (27,1,20,5,5,'user_groups',45,'/1/15/19/20/27/',0);
INSERT INTO `sys_site_object_tree` VALUES (28,1,20,9,5,'messages',46,'/1/15/19/20/28/',0);
INSERT INTO `sys_site_object_tree` VALUES (29,1,20,8,5,'files',47,'/1/15/19/20/29/',0);
INSERT INTO `sys_site_object_tree` VALUES (30,1,20,7,5,'images',48,'/1/15/19/20/30/',0);
INSERT INTO `sys_site_object_tree` VALUES (31,1,15,2,3,'main',49,'/1/15/31/',0);
INSERT INTO `sys_site_object_tree` VALUES (32,1,1,0,2,'image_select',50,'/1/32/',0);
INSERT INTO `sys_site_object_tree` VALUES (33,1,1,0,2,'file_select',51,'/1/33/',0);
INSERT INTO `sys_site_object_tree` VALUES (34,1,1,0,2,'node_select',52,'/1/34/',0);
INSERT INTO `sys_site_object_tree` VALUES (35,1,2,0,3,'site_params',53,'/1/2/35/',0);
INSERT INTO `sys_site_object_tree` VALUES (36,1,1,0,2,'404',54,'/1/36/',0);
INSERT INTO `sys_site_object_tree` VALUES (37,1,16,0,3,'404',55,'/1/16/37/',0);

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
Table data for temp_ru.sys_stat_counter
*/

INSERT INTO `sys_stat_counter` VALUES (1,8,238,1,50,1086440739);

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
  KEY `gpwd` (`generated_password`)
) TYPE=InnoDB COMMENT='InnoDB free: 10240 kB; InnoDB free: 114688 kB; InnoDB free: ';

/*
Table data for temp_ru.user
*/

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
  UNIQUE KEY `object_id` (`object_id`,`version`)
) TYPE=InnoDB COMMENT='InnoDB free: 10240 kB; InnoDB free: 114688 kB; InnoDB free: ';

/*
Table data for temp_ru.user_group
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
Table data for temp_ru.user_in_group
*/

INSERT INTO `user_in_group` VALUES (1,25,28);

