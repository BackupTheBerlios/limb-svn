/* 
SQLyog v3.63
Host - localhost : Database - temp
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
Table data for temp.document
*/

INSERT INTO `document` VALUES (1,19,1,'Annotation','Text','root','main');

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
Table data for temp.message
*/

INSERT INTO `message` VALUES (1,1,16,'Message',NULL,'messages');
INSERT INTO `message` VALUES (2,1,34,'Message',NULL,'messages');
INSERT INTO `message` VALUES (3,1,55,'Not found','Page not found','404');

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
  UNIQUE KEY `ov` (`object_id`,`version`)
) TYPE=InnoDB COMMENT='InnoDB free: 114688 kB; InnoDB free: 114688 kB; InnoDB free:';

/*
Table data for temp.navigation_item
*/

INSERT INTO `navigation_item` VALUES (1,1,15,'navigation','','navigation');
INSERT INTO `navigation_item` VALUES (2,1,33,'navigation','','navigation');
INSERT INTO `navigation_item` VALUES (3,1,37,'Management','/root/admin','admin');
INSERT INTO `navigation_item` VALUES (4,1,38,'Site structure','/root/admin/site_structure','site_structure');
INSERT INTO `navigation_item` VALUES (5,1,39,'Navigation','/root/navigation','navigation');
INSERT INTO `navigation_item` VALUES (6,2,33,'Navigation','/root/navigation','navigation');
INSERT INTO `navigation_item` VALUES (7,2,38,'Site management','/root/admin','site_management');
INSERT INTO `navigation_item` VALUES (8,2,39,'Content management','/root/admin','content_management');
INSERT INTO `navigation_item` VALUES (9,1,40,'Navigation','/root/navigation','navigation');
INSERT INTO `navigation_item` VALUES (10,1,41,'Site structure','/root/admin/site_structure','site_structure');
INSERT INTO `navigation_item` VALUES (11,1,42,'Objects access','/root/admin/objects_access','objects_access');
INSERT INTO `navigation_item` VALUES (12,1,43,'Objects types','/root/admin/classes','classes');
INSERT INTO `navigation_item` VALUES (13,1,44,'Users','/root/users','users');
INSERT INTO `navigation_item` VALUES (14,1,45,'User groups','/root/user_groups','user_groups');
INSERT INTO `navigation_item` VALUES (15,1,46,'Messages','/root/messages','messages');
INSERT INTO `navigation_item` VALUES (16,1,47,'Files','/root/files_folder','files');
INSERT INTO `navigation_item` VALUES (17,1,48,'Images','/root/images_folder','images');
INSERT INTO `navigation_item` VALUES (18,1,49,'User menu','/root','main');

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
  KEY `accessor_type` (`accessor_type`),
  KEY `ac` (`class_id`,`action_name`,`accessor_id`)
) TYPE=InnoDB COMMENT='InnoDB free: 114688 kB; InnoDB free: 114688 kB; InnoDB free:';

/*
Table data for temp.sys_action_access
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
INSERT INTO `sys_action_access` VALUES (328,4,'display',28,0);
INSERT INTO `sys_action_access` VALUES (329,4,'set_group_access',28,0);
INSERT INTO `sys_action_access` VALUES (330,4,'set_group_access_template',28,0);
INSERT INTO `sys_action_access` VALUES (334,7,'display',28,0);
INSERT INTO `sys_action_access` VALUES (335,7,'edit',28,0);
INSERT INTO `sys_action_access` VALUES (336,7,'set_membership',28,0);
INSERT INTO `sys_action_access` VALUES (337,7,'change_password',28,0);
INSERT INTO `sys_action_access` VALUES (338,7,'delete',28,0);
INSERT INTO `sys_action_access` VALUES (339,8,'display',28,0);
INSERT INTO `sys_action_access` VALUES (340,8,'create_user_group',28,0);
INSERT INTO `sys_action_access` VALUES (341,9,'display',28,0);
INSERT INTO `sys_action_access` VALUES (342,9,'edit',28,0);
INSERT INTO `sys_action_access` VALUES (343,9,'delete',28,0);
INSERT INTO `sys_action_access` VALUES (344,6,'display',28,0);
INSERT INTO `sys_action_access` VALUES (345,6,'create_user',28,0);
INSERT INTO `sys_action_access` VALUES (346,5,'display',28,0);
INSERT INTO `sys_action_access` VALUES (347,5,'set_group_access',28,0);
INSERT INTO `sys_action_access` VALUES (348,5,'toggle',28,0);
INSERT INTO `sys_action_access` VALUES (349,14,'display',28,0);
INSERT INTO `sys_action_access` VALUES (350,14,'create_navigation_item',28,0);
INSERT INTO `sys_action_access` VALUES (351,14,'edit',28,0);
INSERT INTO `sys_action_access` VALUES (352,14,'delete',28,0);
INSERT INTO `sys_action_access` VALUES (353,14,'order',28,0);
INSERT INTO `sys_action_access` VALUES (354,17,'display',28,0);
INSERT INTO `sys_action_access` VALUES (355,17,'create_file',28,0);
INSERT INTO `sys_action_access` VALUES (356,17,'create_files_folder',28,0);
INSERT INTO `sys_action_access` VALUES (357,17,'edit_files_folder',28,0);
INSERT INTO `sys_action_access` VALUES (358,17,'delete',28,0);
INSERT INTO `sys_action_access` VALUES (359,17,'file_select',28,0);
INSERT INTO `sys_action_access` VALUES (360,16,'display',28,0);
INSERT INTO `sys_action_access` VALUES (361,16,'create_image',28,0);
INSERT INTO `sys_action_access` VALUES (362,16,'create_images_folder',28,0);
INSERT INTO `sys_action_access` VALUES (363,16,'edit_images_folder',28,0);
INSERT INTO `sys_action_access` VALUES (364,16,'delete',28,0);
INSERT INTO `sys_action_access` VALUES (365,16,'image_select',28,0);
INSERT INTO `sys_action_access` VALUES (366,19,'display',27,0);
INSERT INTO `sys_action_access` VALUES (367,19,'display',28,0);
INSERT INTO `sys_action_access` VALUES (368,19,'edit',28,0);
INSERT INTO `sys_action_access` VALUES (369,19,'edit_variations',28,0);
INSERT INTO `sys_action_access` VALUES (370,19,'delete',28,0);
INSERT INTO `sys_action_access` VALUES (371,18,'display',27,0);
INSERT INTO `sys_action_access` VALUES (372,18,'display',28,0);
INSERT INTO `sys_action_access` VALUES (373,18,'edit',28,0);
INSERT INTO `sys_action_access` VALUES (374,18,'delete',28,0);
INSERT INTO `sys_action_access` VALUES (375,24,'display',28,0);
INSERT INTO `sys_action_access` VALUES (376,23,'display',28,0);
INSERT INTO `sys_action_access` VALUES (377,10,'login',28,0);
INSERT INTO `sys_action_access` VALUES (378,10,'logout',28,0);
INSERT INTO `sys_action_access` VALUES (379,10,'edit',28,0);
INSERT INTO `sys_action_access` VALUES (380,10,'change_user_locale',28,0);
INSERT INTO `sys_action_access` VALUES (381,10,'login',27,0);
INSERT INTO `sys_action_access` VALUES (382,10,'logout',27,0);
INSERT INTO `sys_action_access` VALUES (383,1,'display',28,0);
INSERT INTO `sys_action_access` VALUES (384,1,'admin_display',28,0);
INSERT INTO `sys_action_access` VALUES (385,1,'create_document',28,0);
INSERT INTO `sys_action_access` VALUES (386,1,'set_metadata',28,0);
INSERT INTO `sys_action_access` VALUES (387,1,'edit',28,0);
INSERT INTO `sys_action_access` VALUES (388,1,'display',27,0);
INSERT INTO `sys_action_access` VALUES (389,3,'display',28,0);
INSERT INTO `sys_action_access` VALUES (390,3,'toggle',28,0);
INSERT INTO `sys_action_access` VALUES (391,3,'move',28,0);
INSERT INTO `sys_action_access` VALUES (392,3,'node_select',28,0);
INSERT INTO `sys_action_access` VALUES (393,3,'save_priority',28,0);
INSERT INTO `sys_action_access` VALUES (394,3,'multi_delete',28,0);
INSERT INTO `sys_action_access` VALUES (395,3,'multi_toggle_publish_status',28,0);
INSERT INTO `sys_action_access` VALUES (396,3,'display',27,0);
INSERT INTO `sys_action_access` VALUES (397,26,'display',28,0);
INSERT INTO `sys_action_access` VALUES (398,25,'display',28,0);
INSERT INTO `sys_action_access` VALUES (399,25,'edit',28,0);
INSERT INTO `sys_action_access` VALUES (400,25,'update',28,0);
INSERT INTO `sys_action_access` VALUES (401,25,'delete',28,0);
INSERT INTO `sys_action_access` VALUES (402,2,'display',28,0);
INSERT INTO `sys_action_access` VALUES (403,2,'admin_display',28,0);
INSERT INTO `sys_action_access` VALUES (404,2,'register_new_object',28,0);
INSERT INTO `sys_action_access` VALUES (405,2,'display',27,0);
INSERT INTO `sys_action_access` VALUES (406,27,'display',28,0);
INSERT INTO `sys_action_access` VALUES (407,27,'edit',28,0);
INSERT INTO `sys_action_access` VALUES (408,27,'display',27,0);

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
Table data for temp.sys_class
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
INSERT INTO `sys_class` VALUES (18,'file_object','/shared/images/generic.gif',1,0);
INSERT INTO `sys_class` VALUES (19,'image_object','/shared/images/generic.gif',1,0);
INSERT INTO `sys_class` VALUES (23,'image_select','/shared/images/generic.gif',0,0);
INSERT INTO `sys_class` VALUES (24,'file_select','/shared/images/generic.gif',0,0);
INSERT INTO `sys_class` VALUES (25,'site_param_object','/shared/images/generic.gif',1,1);
INSERT INTO `sys_class` VALUES (26,'node_select','/shared/images/generic.gif',0,0);
INSERT INTO `sys_class` VALUES (27,'not_found_page','/shared/images/generic.gif',0,1);

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
Table data for temp.sys_full_text_index
*/

INSERT INTO `sys_full_text_index` VALUES (1,'title',50,29,10,'login');
INSERT INTO `sys_full_text_index` VALUES (2,'identifier',50,29,10,'login');
INSERT INTO `sys_full_text_index` VALUES (3,'title',50,30,11,'activate password');
INSERT INTO `sys_full_text_index` VALUES (4,'identifier',50,30,11,'activate_password');
INSERT INTO `sys_full_text_index` VALUES (5,'title',50,31,12,'change password');
INSERT INTO `sys_full_text_index` VALUES (6,'identifier',50,31,12,'change_password');
INSERT INTO `sys_full_text_index` VALUES (7,'title',50,32,13,'forgot password');
INSERT INTO `sys_full_text_index` VALUES (8,'identifier',50,32,13,'generate_password');
INSERT INTO `sys_full_text_index` VALUES (9,'title',1,33,14,'navigation');
INSERT INTO `sys_full_text_index` VALUES (10,'identifier',1,33,14,'navigation');
INSERT INTO `sys_full_text_index` VALUES (11,'title',1,37,14,'management');
INSERT INTO `sys_full_text_index` VALUES (12,'identifier',1,37,14,'admin');
INSERT INTO `sys_full_text_index` VALUES (13,'title',1,38,14,'site management');
INSERT INTO `sys_full_text_index` VALUES (14,'identifier',1,38,14,'site_management');
INSERT INTO `sys_full_text_index` VALUES (15,'title',1,40,14,'navigation');
INSERT INTO `sys_full_text_index` VALUES (16,'identifier',1,40,14,'navigation');
INSERT INTO `sys_full_text_index` VALUES (17,'title',1,41,14,'site structure');
INSERT INTO `sys_full_text_index` VALUES (18,'identifier',1,41,14,'site_structure');
INSERT INTO `sys_full_text_index` VALUES (19,'title',1,42,14,'objects access');
INSERT INTO `sys_full_text_index` VALUES (20,'identifier',1,42,14,'objects_access');
INSERT INTO `sys_full_text_index` VALUES (21,'title',1,43,14,'object types');
INSERT INTO `sys_full_text_index` VALUES (22,'identifier',1,43,14,'classes');
INSERT INTO `sys_full_text_index` VALUES (23,'title',1,44,14,'users');
INSERT INTO `sys_full_text_index` VALUES (24,'identifier',1,44,14,'users');
INSERT INTO `sys_full_text_index` VALUES (25,'title',1,45,14,'user groups');
INSERT INTO `sys_full_text_index` VALUES (26,'identifier',1,45,14,'user_groups');
INSERT INTO `sys_full_text_index` VALUES (27,'title',1,46,14,'messages');
INSERT INTO `sys_full_text_index` VALUES (28,'identifier',1,46,14,'messages');
INSERT INTO `sys_full_text_index` VALUES (29,'title',1,47,14,'files');
INSERT INTO `sys_full_text_index` VALUES (30,'identifier',1,47,14,'files');
INSERT INTO `sys_full_text_index` VALUES (31,'title',1,48,14,'images');
INSERT INTO `sys_full_text_index` VALUES (32,'identifier',1,48,14,'images');
INSERT INTO `sys_full_text_index` VALUES (33,'title',1,39,14,'content management');
INSERT INTO `sys_full_text_index` VALUES (34,'identifier',1,39,14,'content_management');
INSERT INTO `sys_full_text_index` VALUES (35,'title',1,49,14,'user menu');
INSERT INTO `sys_full_text_index` VALUES (36,'identifier',1,49,14,'main');
INSERT INTO `sys_full_text_index` VALUES (37,'title',50,34,15,'messages');
INSERT INTO `sys_full_text_index` VALUES (38,'identifier',50,34,15,'messages');
INSERT INTO `sys_full_text_index` VALUES (39,'title',50,35,16,'images');
INSERT INTO `sys_full_text_index` VALUES (40,'identifier',50,35,16,'images_folder');
INSERT INTO `sys_full_text_index` VALUES (41,'title',50,36,17,'files');
INSERT INTO `sys_full_text_index` VALUES (42,'identifier',50,36,17,'files_folder');
INSERT INTO `sys_full_text_index` VALUES (43,'title',50,20,2,'management');
INSERT INTO `sys_full_text_index` VALUES (44,'identifier',50,20,2,'admin');
INSERT INTO `sys_full_text_index` VALUES (45,'title',50,21,3,'site structure');
INSERT INTO `sys_full_text_index` VALUES (46,'identifier',50,21,3,'site_structure');
INSERT INTO `sys_full_text_index` VALUES (47,'title',50,52,25,'site params');
INSERT INTO `sys_full_text_index` VALUES (48,'identifier',50,52,25,'site_params');
INSERT INTO `sys_full_text_index` VALUES (49,'title',50,22,4,'object types');
INSERT INTO `sys_full_text_index` VALUES (50,'identifier',50,22,4,'classes');
INSERT INTO `sys_full_text_index` VALUES (51,'title',50,23,5,'objects access');
INSERT INTO `sys_full_text_index` VALUES (52,'identifier',50,23,5,'objects_access');
INSERT INTO `sys_full_text_index` VALUES (53,'title',50,50,23,'image select');
INSERT INTO `sys_full_text_index` VALUES (54,'identifier',50,50,23,'image_select');
INSERT INTO `sys_full_text_index` VALUES (55,'title',50,51,24,'file select');
INSERT INTO `sys_full_text_index` VALUES (56,'identifier',50,51,24,'file_select');
INSERT INTO `sys_full_text_index` VALUES (57,'title',50,53,26,'node select');
INSERT INTO `sys_full_text_index` VALUES (58,'identifier',50,53,26,'node_select');
INSERT INTO `sys_full_text_index` VALUES (59,'title',50,24,6,'users');
INSERT INTO `sys_full_text_index` VALUES (60,'identifier',50,24,6,'users');
INSERT INTO `sys_full_text_index` VALUES (61,'title',50,25,7,'management');
INSERT INTO `sys_full_text_index` VALUES (62,'identifier',50,25,7,'admin');
INSERT INTO `sys_full_text_index` VALUES (63,'title',50,26,8,'user groups');
INSERT INTO `sys_full_text_index` VALUES (64,'identifier',50,26,8,'user_groups');
INSERT INTO `sys_full_text_index` VALUES (65,'title',50,28,9,'admins');
INSERT INTO `sys_full_text_index` VALUES (66,'identifier',50,28,9,'admins');
INSERT INTO `sys_full_text_index` VALUES (67,'title',50,27,9,'visitors');
INSERT INTO `sys_full_text_index` VALUES (68,'identifier',50,27,9,'visitors');
INSERT INTO `sys_full_text_index` VALUES (69,'identifier',50,55,15,'404');
INSERT INTO `sys_full_text_index` VALUES (70,'title',50,55,15,'not found');

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
Table data for temp.sys_group_object_access_template
*/

INSERT INTO `sys_group_object_access_template` VALUES (2,17,'create_file');
INSERT INTO `sys_group_object_access_template` VALUES (3,17,'create_files_folder');
INSERT INTO `sys_group_object_access_template` VALUES (4,16,'create_image');
INSERT INTO `sys_group_object_access_template` VALUES (5,16,'create_images_folder');
INSERT INTO `sys_group_object_access_template` VALUES (6,6,'create_user');
INSERT INTO `sys_group_object_access_template` VALUES (7,1,'create_document');
INSERT INTO `sys_group_object_access_template` VALUES (8,15,'create_message');

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
Table data for temp.sys_group_object_access_template_item
*/

INSERT INTO `sys_group_object_access_template_item` VALUES (3,2,27,1,0);
INSERT INTO `sys_group_object_access_template_item` VALUES (4,2,28,1,1);
INSERT INTO `sys_group_object_access_template_item` VALUES (5,3,27,1,0);
INSERT INTO `sys_group_object_access_template_item` VALUES (6,3,28,1,1);
INSERT INTO `sys_group_object_access_template_item` VALUES (7,4,27,1,0);
INSERT INTO `sys_group_object_access_template_item` VALUES (8,4,28,1,1);
INSERT INTO `sys_group_object_access_template_item` VALUES (9,5,27,1,0);
INSERT INTO `sys_group_object_access_template_item` VALUES (10,5,28,1,1);
INSERT INTO `sys_group_object_access_template_item` VALUES (11,6,28,1,1);
INSERT INTO `sys_group_object_access_template_item` VALUES (12,7,28,1,1);
INSERT INTO `sys_group_object_access_template_item` VALUES (13,8,28,1,1);
INSERT INTO `sys_group_object_access_template_item` VALUES (14,8,27,1,0);

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
Table data for temp.sys_object_access
*/

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
INSERT INTO `sys_object_access` VALUES (135,52,28,1,1,0);
INSERT INTO `sys_object_access` VALUES (136,53,27,1,0,0);
INSERT INTO `sys_object_access` VALUES (137,53,28,1,1,0);
INSERT INTO `sys_object_access` VALUES (138,54,27,1,0,0);
INSERT INTO `sys_object_access` VALUES (139,54,28,1,1,0);
INSERT INTO `sys_object_access` VALUES (140,55,28,1,1,0);
INSERT INTO `sys_object_access` VALUES (141,55,27,1,0,0);

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
  UNIQUE KEY `ov` (`object_id`,`version`),
  KEY `cid` (`creator_id`),
  KEY `md` (`modified_date`),
  KEY `cd` (`created_date`)
) TYPE=InnoDB COMMENT='InnoDB free: 10240 kB; InnoDB free: 114688 kB; InnoDB free: ';

/*
Table data for temp.sys_object_version
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
INSERT INTO `sys_object_version` VALUES (28,55,25,1086439395,1086439395,1);

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
Table data for temp.sys_session
*/

INSERT INTO `sys_session` VALUES ('b0d494dcb12d0c08de5c1bdb09baef71','global_user|O:4:\"user\":11:{s:3:\"_id\";s:2:\"25\";s:8:\"_node_id\";s:1:\"7\";s:6:\"_login\";s:5:\"admin\";s:9:\"_password\";s:32:\"66d4aaa5ea177ac32c69946de3731ec0\";s:6:\"_email\";s:15:\"mike@office.bit\";s:5:\"_name\";s:5:\"admin\";s:9:\"_lastname\";s:5:\"super\";s:10:\"_locale_id\";s:2:\"en\";s:13:\"_is_logged_in\";b:1;s:7:\"_groups\";a:1:{i:28;s:6:\"admins\";}s:11:\"_attributes\";N;}session_classes_paths|a:1:{i:0;s:54:\"c:/var/dev/limb2/2.2//core/lib/security/user.class.php\";}tree_expanded_parents|a:26:{i:1;a:3:{s:4:\"path\";s:3:\"/1/\";s:5:\"level\";s:1:\"1\";s:6:\"status\";b:1;}i:15;a:3:{s:4:\"path\";s:6:\"/1/15/\";s:5:\"level\";s:1:\"2\";s:6:\"status\";b:0;}i:19;a:3:{s:4:\"path\";s:9:\"/1/15/19/\";s:5:\"level\";s:1:\"3\";s:6:\"status\";b:0;}i:20;a:3:{s:4:\"path\";s:12:\"/1/15/19/20/\";s:5:\"level\";s:1:\"4\";s:6:\"status\";b:0;}i:22;a:3:{s:4:\"path\";s:15:\"/1/15/19/20/22/\";s:5:\"level\";s:1:\"5\";s:6:\"status\";b:0;}i:23;a:3:{s:4:\"path\";s:15:\"/1/15/19/20/23/\";s:5:\"level\";s:1:\"5\";s:6:\"status\";b:0;}i:24;a:3:{s:4:\"path\";s:15:\"/1/15/19/20/24/\";s:5:\"level\";s:1:\"5\";s:6:\"status\";b:0;}i:25;a:3:{s:4:\"path\";s:15:\"/1/15/19/20/25/\";s:5:\"level\";s:1:\"5\";s:6:\"status\";b:0;}i:26;a:3:{s:4:\"path\";s:15:\"/1/15/19/20/26/\";s:5:\"level\";s:1:\"5\";s:6:\"status\";b:0;}i:27;a:3:{s:4:\"path\";s:15:\"/1/15/19/20/27/\";s:5:\"level\";s:1:\"5\";s:6:\"status\";b:0;}i:28;a:3:{s:4:\"path\";s:15:\"/1/15/19/20/28/\";s:5:\"level\";s:1:\"5\";s:6:\"status\";b:0;}i:29;a:3:{s:4:\"path\";s:15:\"/1/15/19/20/29/\";s:5:\"level\";s:1:\"5\";s:6:\"status\";b:0;}i:30;a:3:{s:4:\"path\";s:15:\"/1/15/19/20/30/\";s:5:\"level\";s:1:\"5\";s:6:\"status\";b:0;}i:21;a:3:{s:4:\"path\";s:12:\"/1/15/19/21/\";s:5:\"level\";s:1:\"4\";s:6:\"status\";b:0;}i:31;a:3:{s:4:\"path\";s:9:\"/1/15/31/\";s:5:\"level\";s:1:\"3\";s:6:\"status\";b:0;}i:16;a:3:{s:4:\"path\";s:6:\"/1/16/\";s:5:\"level\";s:1:\"2\";s:6:\"status\";b:0;}i:37;a:3:{s:4:\"path\";s:9:\"/1/16/37/\";s:5:\"level\";s:1:\"3\";s:6:\"status\";b:0;}i:17;a:3:{s:4:\"path\";s:6:\"/1/17/\";s:5:\"level\";s:1:\"2\";s:6:\"status\";b:0;}i:18;a:3:{s:4:\"path\";s:6:\"/1/18/\";s:5:\"level\";s:1:\"2\";s:6:\"status\";b:0;}i:2;a:3:{s:4:\"path\";s:5:\"/1/2/\";s:5:\"level\";s:1:\"2\";s:6:\"status\";b:0;}i:3;a:3:{s:4:\"path\";s:7:\"/1/2/3/\";s:5:\"level\";s:1:\"3\";s:6:\"status\";b:0;}i:34;a:3:{s:4:\"path\";s:8:\"/1/2/34/\";s:5:\"level\";s:1:\"3\";s:6:\"status\";b:0;}i:4;a:3:{s:4:\"path\";s:7:\"/1/2/4/\";s:5:\"level\";s:1:\"3\";s:6:\"status\";b:0;}i:36;a:3:{s:4:\"path\";s:6:\"/1/36/\";s:5:\"level\";s:1:\"2\";s:6:\"status\";b:0;}i:6;a:3:{s:4:\"path\";s:5:\"/1/6/\";s:5:\"level\";s:1:\"2\";s:6:\"status\";b:0;}i:8;a:3:{s:4:\"path\";s:5:\"/1/8/\";s:5:\"level\";s:1:\"2\";s:6:\"status\";b:0;}}strings|s:0:\"\";',1088510121,25);

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
  KEY `current_version` (`current_version`),
  KEY `class_id` (`class_id`)
) TYPE=InnoDB COMMENT='InnoDB free: 10240 kB; InnoDB free: 10240 kB; InnoDB free: 1';

/*
Table data for temp.sys_site_object
*/

INSERT INTO `sys_site_object` VALUES (19,1,1,1076768404,0,1076762314,0,'en','Main','root');
INSERT INTO `sys_site_object` VALUES (20,2,1,1076762314,0,1076762314,0,'en','Management','admin');
INSERT INTO `sys_site_object` VALUES (21,3,2,1076769130,0,1076762314,0,'en','Site structure','site_structure');
INSERT INTO `sys_site_object` VALUES (22,4,2,1076769267,0,1076762314,0,'en','Object types','classes');
INSERT INTO `sys_site_object` VALUES (23,5,2,1076769145,0,1076762314,0,'en','Objects access','objects_access');
INSERT INTO `sys_site_object` VALUES (24,6,2,1076769160,0,1076762314,0,'en','Users','users');
INSERT INTO `sys_site_object` VALUES (25,7,1,1076762588,0,1076762314,0,'en','Management','admin');
INSERT INTO `sys_site_object` VALUES (26,8,2,1076769173,0,1076762314,0,'en','User groups','user_groups');
INSERT INTO `sys_site_object` VALUES (27,9,1,1076762314,0,1076762314,0,'en','Visitors','visitors');
INSERT INTO `sys_site_object` VALUES (28,9,1,1076762314,0,1076762314,0,'en','Admins','admins');
INSERT INTO `sys_site_object` VALUES (29,10,2,1076769188,0,1076762314,0,'en','Login','login');
INSERT INTO `sys_site_object` VALUES (30,11,2,1076769202,0,1076762314,0,'en','Activate password','activate_password');
INSERT INTO `sys_site_object` VALUES (31,12,2,1076769224,0,1076762314,0,'en','Change password','change_password');
INSERT INTO `sys_site_object` VALUES (32,13,2,1076769246,0,1076762315,0,'en','Forgot password?','generate_password');
INSERT INTO `sys_site_object` VALUES (33,14,2,1076771224,0,1076762315,0,'en','Navigation','navigation');
INSERT INTO `sys_site_object` VALUES (34,15,1,1076762315,0,1076762315,0,'en','Messages','messages');
INSERT INTO `sys_site_object` VALUES (35,16,1,1076762315,0,1076762315,0,'en','Images','images_folder');
INSERT INTO `sys_site_object` VALUES (36,17,1,1076762315,0,1076762315,0,'en','Files','files_folder');
INSERT INTO `sys_site_object` VALUES (37,14,1,1076770835,0,1076770835,25,'en','Management','admin');
INSERT INTO `sys_site_object` VALUES (38,14,2,1076771356,0,1076770879,25,'en','Site management','site_management');
INSERT INTO `sys_site_object` VALUES (39,14,2,1076771416,0,1076771149,25,'en','Content management','content_management');
INSERT INTO `sys_site_object` VALUES (40,14,1,1076771604,0,1076771604,25,'en','Navigation','navigation');
INSERT INTO `sys_site_object` VALUES (41,14,1,1076772382,0,1076772382,25,'en','Site structure','site_structure');
INSERT INTO `sys_site_object` VALUES (42,14,1,1076772439,0,1076772439,25,'en','Objects access','objects_access');
INSERT INTO `sys_site_object` VALUES (43,14,1,1076772480,0,1076772480,25,'en','Object types','classes');
INSERT INTO `sys_site_object` VALUES (44,14,1,1076772520,0,1076772520,25,'en','Users','users');
INSERT INTO `sys_site_object` VALUES (45,14,1,1076772540,0,1076772540,25,'en','User groups','user_groups');
INSERT INTO `sys_site_object` VALUES (46,14,1,1076772578,0,1076772578,25,'en','Messages','messages');
INSERT INTO `sys_site_object` VALUES (47,14,1,1076772601,0,1076772601,25,'en','Files','files');
INSERT INTO `sys_site_object` VALUES (48,14,1,1076772623,0,1076772623,25,'en','Images','images');
INSERT INTO `sys_site_object` VALUES (49,14,1,1076772668,0,1076772668,25,'en','User menu','main');
INSERT INTO `sys_site_object` VALUES (50,23,1,1084266500,0,1084266500,25,'en','Image select','image_select');
INSERT INTO `sys_site_object` VALUES (51,24,1,1084266511,0,1084266511,25,'en','File select','file_select');
INSERT INTO `sys_site_object` VALUES (52,25,1,1084266564,0,1084266564,25,'en','Site params','site_params');
INSERT INTO `sys_site_object` VALUES (53,26,1,1084266606,0,1084266606,25,'en','Node select','node_select');
INSERT INTO `sys_site_object` VALUES (54,27,1,1086439381,0,1086439381,25,'en','Not found','404');
INSERT INTO `sys_site_object` VALUES (55,15,1,1086439395,0,1086439395,25,'en','Not found','404');

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
  UNIQUE KEY `path` (`path`,`level`),
  KEY `root_id` (`root_id`),
  KEY `level` (`level`),
  KEY `rlr` (`root_id`),
  KEY `parent_id` (`parent_id`),
  KEY `object_id` (`object_id`)
) TYPE=InnoDB;

/*
Table data for temp.sys_site_object_tree
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
INSERT INTO `sys_site_object_tree` VALUES (22,1,20,1,5,'navigation',40,'/1/15/19/20/22/',0);
INSERT INTO `sys_site_object_tree` VALUES (23,1,20,2,5,'site_structure',41,'/1/15/19/20/23/',0);
INSERT INTO `sys_site_object_tree` VALUES (24,1,20,3,5,'objects_access',42,'/1/15/19/20/24/',0);
INSERT INTO `sys_site_object_tree` VALUES (25,1,20,4,5,'classes',43,'/1/15/19/20/25/',0);
INSERT INTO `sys_site_object_tree` VALUES (26,1,20,9,5,'users',44,'/1/15/19/20/26/',0);
INSERT INTO `sys_site_object_tree` VALUES (27,1,20,8,5,'user_groups',45,'/1/15/19/20/27/',0);
INSERT INTO `sys_site_object_tree` VALUES (28,1,20,7,5,'messages',46,'/1/15/19/20/28/',0);
INSERT INTO `sys_site_object_tree` VALUES (29,1,20,6,5,'files',47,'/1/15/19/20/29/',0);
INSERT INTO `sys_site_object_tree` VALUES (30,1,20,5,5,'images',48,'/1/15/19/20/30/',0);
INSERT INTO `sys_site_object_tree` VALUES (31,1,15,2,3,'main',49,'/1/15/31/',0);
INSERT INTO `sys_site_object_tree` VALUES (32,1,1,0,2,'image_select',50,'/1/32/',0);
INSERT INTO `sys_site_object_tree` VALUES (33,1,1,0,2,'file_select',51,'/1/33/',0);
INSERT INTO `sys_site_object_tree` VALUES (34,1,2,0,3,'site_params',52,'/1/2/34/',0);
INSERT INTO `sys_site_object_tree` VALUES (35,1,1,0,2,'node_select',53,'/1/35/',0);
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
Table data for temp.sys_stat_counter
*/

INSERT INTO `sys_stat_counter` VALUES (1,4,308,1,58,1088510121);

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
Table data for temp.sys_stat_day_counters
*/

INSERT INTO `sys_stat_day_counters` VALUES (1,1088452800,58,1,3,0);

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
Table data for temp.sys_stat_ip
*/

INSERT INTO `sys_stat_ip` VALUES ('c0a80001',1088498799);

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
Table data for temp.sys_stat_uri
*/

INSERT INTO `sys_stat_uri` VALUES (1,'/root');
INSERT INTO `sys_stat_uri` VALUES (2,'/shared/js/common.js');
INSERT INTO `sys_stat_uri` VALUES (3,'/design/main/styles/main.css');
INSERT INTO `sys_stat_uri` VALUES (4,'/design/main/js/form_errors.js');
INSERT INTO `sys_stat_uri` VALUES (5,'/root/login');
INSERT INTO `sys_stat_uri` VALUES (6,'/shared/styles/admin.css');
INSERT INTO `sys_stat_uri` VALUES (7,'/shared/images/logo.gif');
INSERT INTO `sys_stat_uri` VALUES (8,'/root/admin');
INSERT INTO `sys_stat_uri` VALUES (9,'/var/debug.html');

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
  UNIQUE KEY `ov` (`object_id`,`version`),
  KEY `pwd` (`password`),
  KEY `gpwd` (`generated_password`)
) TYPE=InnoDB COMMENT='InnoDB free: 10240 kB; InnoDB free: 114688 kB; InnoDB free: ';

/*
Table data for temp.user
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
  UNIQUE KEY `ov` (`object_id`,`version`)
) TYPE=InnoDB COMMENT='InnoDB free: 10240 kB; InnoDB free: 114688 kB; InnoDB free: ';

/*
Table data for temp.user_group
*/

INSERT INTO `user_group` VALUES (3,1,27,'Visitors','visitors');
INSERT INTO `user_group` VALUES (4,1,28,'Admins','admins');

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
Table data for temp.user_in_group
*/

INSERT INTO `user_in_group` VALUES (1,25,28);

