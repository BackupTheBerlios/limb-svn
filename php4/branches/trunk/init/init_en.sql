/* 
SQLyog v3.63
Host - localhost : Database - init_en
**************************************************************
Server version 4.0.12-nt-log
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
) TYPE=InnoDB ROW_FORMAT=DYNAMIC;

/*
Table data for init_en.document
*/

INSERT INTO `document` VALUES (57,19,1,'Annotation','<p>content</p>','root','Main');

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
Table data for init_en.message
*/

INSERT INTO `message` VALUES (19,5,87,'Not found','<p>Page not found.</p>','404');

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
Table data for init_en.navigation_item
*/

INSERT INTO `navigation_item` VALUES (1,1,15,'Navigation','','navigation',0);
INSERT INTO `navigation_item` VALUES (3,1,37,'Admin navigation','/root/admin','navigation',0);
INSERT INTO `navigation_item` VALUES (6,2,33,'Navigation','/root/navigation','navigation',0);
INSERT INTO `navigation_item` VALUES (10,1,41,'Site structure','/root/admin/site_structure','site_structure',0);
INSERT INTO `navigation_item` VALUES (11,1,42,'Objects access','/root/admin/objects_access','objects_access',0);
INSERT INTO `navigation_item` VALUES (18,1,49,'User menu','/root','main',0);
INSERT INTO `navigation_item` VALUES (45,1,140,'JIP-mode','/root','jip',0);
INSERT INTO `navigation_item` VALUES (46,3,39,'Content management','/root?action=admin_display','content_management',0);
INSERT INTO `navigation_item` VALUES (51,3,38,'Site management','/root/admin?action=admin_display','site_management',0);
INSERT INTO `navigation_item` VALUES (53,3,47,'Files','/root/files?action=admin_display','files',0);
INSERT INTO `navigation_item` VALUES (54,3,46,'Messages','/root/messages?action=admin_display','messages',0);
INSERT INTO `navigation_item` VALUES (55,3,40,'Navigation','/root/navigation?action=admin_display','navigation',0);
INSERT INTO `navigation_item` VALUES (57,3,43,'Controllers','/root/admin/controllers','classes',0);
INSERT INTO `navigation_item` VALUES (58,2,44,'Users','/root/admin/users','users',0);
INSERT INTO `navigation_item` VALUES (59,2,45,'User groups','/root/admin/user_groups','user_groups',0);
INSERT INTO `navigation_item` VALUES (62,3,119,'System events','/root/admin/events','events',0);
INSERT INTO `navigation_item` VALUES (63,3,121,'Site statistics','/root/admin/stats','stats',0);
INSERT INTO `navigation_item` VALUES (64,1,143,'Common','/root','common',0);
INSERT INTO `navigation_item` VALUES (65,1,145,'Navigation(admin.)','/root/admin/navigation','navigation',0);
INSERT INTO `navigation_item` VALUES (66,1,146,'Security','/root','security',0);
INSERT INTO `navigation_item` VALUES (68,1,148,'Common','/root','common',0);
INSERT INTO `navigation_item` VALUES (69,1,149,'Media','/root','media',0);
INSERT INTO `navigation_item` VALUES (78,4,155,'Images','/root/images?action=admin_display','images',0);
INSERT INTO `navigation_item` VALUES (80,1,160,'Hits/hosts report','/root/admin/stats?action=hits_hosts_report','hits_report',0);
INSERT INTO `navigation_item` VALUES (81,1,161,'Popular pages report','/root/admin/stats?action=pages_report','pages_report',0);
INSERT INTO `navigation_item` VALUES (82,1,162,'Referers report','/root/admin/stats?action=referers_report','referers_report',0);
INSERT INTO `navigation_item` VALUES (83,1,163,'IPs report','/root/admin/stats?action=ips_report','ips_report',0);
INSERT INTO `navigation_item` VALUES (84,1,164,'Keywords report','/root/admin/stats?action=keywords_report','keywords_report',0);
INSERT INTO `navigation_item` VALUES (85,1,165,'Search engines report','/root/admin/stats?action=search_engines_report','search_engines_report',0);
INSERT INTO `navigation_item` VALUES (86,1,166,'Routes report','/root/admin/stats?action=routes_report','routes_report',0);

/*
Table struture for sys_access_template
*/

drop table if exists `sys_access_template`;
CREATE TABLE `sys_access_template` (
  `id` int(11) NOT NULL auto_increment,
  `controller_id` int(11) NOT NULL default '0',
  `action_name` char(50) NOT NULL default '',
  `accessor_type` tinyint(4) default NULL,
  PRIMARY KEY  (`id`),
  KEY `action_name` (`action_name`),
  KEY `controller_id` (`controller_id`,`accessor_type`)
) TYPE=InnoDB COMMENT='InnoDB free: 114688 kB; InnoDB free: 114688 kB; InnoDB free:';

/*
Table data for init_en.sys_access_template
*/

INSERT INTO `sys_access_template` VALUES (50,40,'publish',0);
INSERT INTO `sys_access_template` VALUES (51,40,'unpublish',0);
INSERT INTO `sys_access_template` VALUES (52,33,'create_article',0);
INSERT INTO `sys_access_template` VALUES (53,33,'create_articles_folder',0);
INSERT INTO `sys_access_template` VALUES (54,33,'publish',0);
INSERT INTO `sys_access_template` VALUES (55,33,'unpublish',0);
INSERT INTO `sys_access_template` VALUES (56,47,'create_catalog_folder',0);
INSERT INTO `sys_access_template` VALUES (57,47,'create_catalog_object',0);
INSERT INTO `sys_access_template` VALUES (58,47,'publish',0);
INSERT INTO `sys_access_template` VALUES (59,47,'unpublish',0);
INSERT INTO `sys_access_template` VALUES (60,48,'publish',0);
INSERT INTO `sys_access_template` VALUES (61,48,'unpublish',0);
INSERT INTO `sys_access_template` VALUES (65,41,'create_faq_object',0);
INSERT INTO `sys_access_template` VALUES (66,41,'publish',0);
INSERT INTO `sys_access_template` VALUES (67,41,'unpublish',0);
INSERT INTO `sys_access_template` VALUES (68,38,'create_document',0);
INSERT INTO `sys_access_template` VALUES (69,38,'publish',0);
INSERT INTO `sys_access_template` VALUES (70,38,'unpublish',0);
INSERT INTO `sys_access_template` VALUES (74,42,'publish',0);
INSERT INTO `sys_access_template` VALUES (75,42,'unpublish',0);
INSERT INTO `sys_access_template` VALUES (76,17,'create_file',0);
INSERT INTO `sys_access_template` VALUES (77,17,'create_files_folder',0);
INSERT INTO `sys_access_template` VALUES (80,44,'publish',0);
INSERT INTO `sys_access_template` VALUES (81,44,'unpublish',0);
INSERT INTO `sys_access_template` VALUES (82,16,'create_image',0);
INSERT INTO `sys_access_template` VALUES (83,16,'create_images_folder',0);
INSERT INTO `sys_access_template` VALUES (84,1,'create_document',0);
INSERT INTO `sys_access_template` VALUES (85,15,'create_message',0);
INSERT INTO `sys_access_template` VALUES (92,32,'create_poll',0);
INSERT INTO `sys_access_template` VALUES (93,6,'create_user',0);
INSERT INTO `sys_access_template` VALUES (94,36,'publish',0);
INSERT INTO `sys_access_template` VALUES (95,36,'unpublish',0);
INSERT INTO `sys_access_template` VALUES (108,43,'create_catalog_folder',0);
INSERT INTO `sys_access_template` VALUES (109,43,'create_catalog_object',0);
INSERT INTO `sys_access_template` VALUES (110,43,'publish',0);
INSERT INTO `sys_access_template` VALUES (111,43,'unpublish',0);
INSERT INTO `sys_access_template` VALUES (112,34,'create_document',0);
INSERT INTO `sys_access_template` VALUES (113,34,'publish',0);
INSERT INTO `sys_access_template` VALUES (114,34,'unpublish',0);
INSERT INTO `sys_access_template` VALUES (115,28,'create_faq_folder',0);
INSERT INTO `sys_access_template` VALUES (116,28,'publish',0);
INSERT INTO `sys_access_template` VALUES (117,28,'unpublish',0);
INSERT INTO `sys_access_template` VALUES (118,37,'create_faq_object',0);
INSERT INTO `sys_access_template` VALUES (119,37,'publish',0);
INSERT INTO `sys_access_template` VALUES (120,37,'unpublish',0);
INSERT INTO `sys_access_template` VALUES (121,31,'display',0);
INSERT INTO `sys_access_template` VALUES (122,31,'create_guestbook_message',0);
INSERT INTO `sys_access_template` VALUES (123,14,'create_navigation_item',0);
INSERT INTO `sys_access_template` VALUES (124,14,'publish',0);
INSERT INTO `sys_access_template` VALUES (125,14,'unpublish',0);
INSERT INTO `sys_access_template` VALUES (126,25,'create_news',0);
INSERT INTO `sys_access_template` VALUES (128,39,'create_answer',0);
INSERT INTO `sys_access_template` VALUES (129,39,'publish',0);
INSERT INTO `sys_access_template` VALUES (130,39,'unpublish',0);
INSERT INTO `sys_access_template` VALUES (131,29,'create_poll',0);
INSERT INTO `sys_access_template` VALUES (141,30,'create_article',0);
INSERT INTO `sys_access_template` VALUES (142,30,'create_articles_folder',0);
INSERT INTO `sys_access_template` VALUES (143,30,'publish',0);
INSERT INTO `sys_access_template` VALUES (144,30,'unpublish',0);
INSERT INTO `sys_access_template` VALUES (145,49,'create_navigation_item',0);
INSERT INTO `sys_access_template` VALUES (146,49,'publish',0);
INSERT INTO `sys_access_template` VALUES (147,49,'unpublish',0);

/*
Table struture for sys_access_template_item
*/

drop table if exists `sys_access_template_item`;
CREATE TABLE `sys_access_template_item` (
  `id` int(11) NOT NULL auto_increment,
  `template_id` int(11) default NULL,
  `accessor_id` int(11) default NULL,
  `access` tinyint(4) default NULL,
  PRIMARY KEY  (`id`),
  KEY `template_id` (`template_id`),
  KEY `acessor_id` (`accessor_id`)
) TYPE=InnoDB COMMENT='InnoDB free: 114688 kB; InnoDB free: 114688 kB; InnoDB free:';

/*
Table data for init_en.sys_access_template_item
*/

INSERT INTO `sys_access_template_item` VALUES (72,50,28,1);
INSERT INTO `sys_access_template_item` VALUES (74,50,27,1);
INSERT INTO `sys_access_template_item` VALUES (75,51,28,1);
INSERT INTO `sys_access_template_item` VALUES (77,52,28,1);
INSERT INTO `sys_access_template_item` VALUES (79,53,28,1);
INSERT INTO `sys_access_template_item` VALUES (81,54,28,1);
INSERT INTO `sys_access_template_item` VALUES (83,54,27,1);
INSERT INTO `sys_access_template_item` VALUES (84,55,28,1);
INSERT INTO `sys_access_template_item` VALUES (86,56,28,1);
INSERT INTO `sys_access_template_item` VALUES (88,57,28,1);
INSERT INTO `sys_access_template_item` VALUES (90,58,28,1);
INSERT INTO `sys_access_template_item` VALUES (92,58,27,1);
INSERT INTO `sys_access_template_item` VALUES (93,59,28,1);
INSERT INTO `sys_access_template_item` VALUES (95,60,28,1);
INSERT INTO `sys_access_template_item` VALUES (97,60,27,1);
INSERT INTO `sys_access_template_item` VALUES (98,61,28,1);
INSERT INTO `sys_access_template_item` VALUES (107,65,28,1);
INSERT INTO `sys_access_template_item` VALUES (109,66,28,1);
INSERT INTO `sys_access_template_item` VALUES (111,66,27,1);
INSERT INTO `sys_access_template_item` VALUES (112,67,28,1);
INSERT INTO `sys_access_template_item` VALUES (114,68,28,1);
INSERT INTO `sys_access_template_item` VALUES (116,69,28,1);
INSERT INTO `sys_access_template_item` VALUES (118,69,27,1);
INSERT INTO `sys_access_template_item` VALUES (119,70,28,1);
INSERT INTO `sys_access_template_item` VALUES (128,74,28,1);
INSERT INTO `sys_access_template_item` VALUES (130,74,27,1);
INSERT INTO `sys_access_template_item` VALUES (131,75,28,1);
INSERT INTO `sys_access_template_item` VALUES (133,76,28,1);
INSERT INTO `sys_access_template_item` VALUES (135,76,27,1);
INSERT INTO `sys_access_template_item` VALUES (136,77,28,1);
INSERT INTO `sys_access_template_item` VALUES (138,77,27,1);
INSERT INTO `sys_access_template_item` VALUES (145,80,28,1);
INSERT INTO `sys_access_template_item` VALUES (147,80,27,1);
INSERT INTO `sys_access_template_item` VALUES (148,81,28,1);
INSERT INTO `sys_access_template_item` VALUES (150,82,28,1);
INSERT INTO `sys_access_template_item` VALUES (152,82,27,1);
INSERT INTO `sys_access_template_item` VALUES (153,83,28,1);
INSERT INTO `sys_access_template_item` VALUES (155,83,27,1);
INSERT INTO `sys_access_template_item` VALUES (156,84,28,1);
INSERT INTO `sys_access_template_item` VALUES (158,85,28,1);
INSERT INTO `sys_access_template_item` VALUES (160,85,27,1);
INSERT INTO `sys_access_template_item` VALUES (176,92,28,1);
INSERT INTO `sys_access_template_item` VALUES (178,93,28,1);
INSERT INTO `sys_access_template_item` VALUES (180,94,28,1);
INSERT INTO `sys_access_template_item` VALUES (182,94,27,1);
INSERT INTO `sys_access_template_item` VALUES (183,95,28,1);
INSERT INTO `sys_access_template_item` VALUES (212,108,28,1);
INSERT INTO `sys_access_template_item` VALUES (214,109,28,1);
INSERT INTO `sys_access_template_item` VALUES (216,110,28,1);
INSERT INTO `sys_access_template_item` VALUES (218,110,27,1);
INSERT INTO `sys_access_template_item` VALUES (219,111,28,1);
INSERT INTO `sys_access_template_item` VALUES (221,112,28,1);
INSERT INTO `sys_access_template_item` VALUES (223,113,28,1);
INSERT INTO `sys_access_template_item` VALUES (225,113,27,1);
INSERT INTO `sys_access_template_item` VALUES (226,114,28,1);
INSERT INTO `sys_access_template_item` VALUES (228,115,28,1);
INSERT INTO `sys_access_template_item` VALUES (230,116,28,1);
INSERT INTO `sys_access_template_item` VALUES (232,116,27,1);
INSERT INTO `sys_access_template_item` VALUES (233,117,28,1);
INSERT INTO `sys_access_template_item` VALUES (235,118,28,1);
INSERT INTO `sys_access_template_item` VALUES (237,119,28,1);
INSERT INTO `sys_access_template_item` VALUES (239,119,27,1);
INSERT INTO `sys_access_template_item` VALUES (240,120,28,1);
INSERT INTO `sys_access_template_item` VALUES (242,121,28,1);
INSERT INTO `sys_access_template_item` VALUES (244,122,28,1);
INSERT INTO `sys_access_template_item` VALUES (246,123,28,1);
INSERT INTO `sys_access_template_item` VALUES (248,124,28,1);
INSERT INTO `sys_access_template_item` VALUES (250,124,27,1);
INSERT INTO `sys_access_template_item` VALUES (251,125,28,1);
INSERT INTO `sys_access_template_item` VALUES (253,126,28,1);
INSERT INTO `sys_access_template_item` VALUES (257,128,28,1);
INSERT INTO `sys_access_template_item` VALUES (259,129,28,1);
INSERT INTO `sys_access_template_item` VALUES (261,129,27,1);
INSERT INTO `sys_access_template_item` VALUES (262,130,28,1);
INSERT INTO `sys_access_template_item` VALUES (264,131,28,1);
INSERT INTO `sys_access_template_item` VALUES (266,131,27,1);
INSERT INTO `sys_access_template_item` VALUES (285,141,28,1);
INSERT INTO `sys_access_template_item` VALUES (287,142,28,1);
INSERT INTO `sys_access_template_item` VALUES (289,143,28,1);
INSERT INTO `sys_access_template_item` VALUES (291,143,27,1);
INSERT INTO `sys_access_template_item` VALUES (292,144,28,1);
INSERT INTO `sys_access_template_item` VALUES (294,145,28,1);
INSERT INTO `sys_access_template_item` VALUES (296,146,28,1);
INSERT INTO `sys_access_template_item` VALUES (298,147,28,1);

/*
Table struture for sys_action_access
*/

drop table if exists `sys_action_access`;
CREATE TABLE `sys_action_access` (
  `id` int(11) NOT NULL auto_increment,
  `controller_id` int(11) NOT NULL default '0',
  `action_name` char(50) NOT NULL default '',
  `accessor_id` int(11) NOT NULL default '0',
  `accessor_type` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `accessor_id` (`accessor_id`),
  KEY `accessor_type` (`accessor_type`),
  KEY `controller_id` (`controller_id`)
) TYPE=InnoDB COMMENT='InnoDB free: 114688 kB; InnoDB free: 114688 kB; InnoDB free:';

/*
Table data for init_en.sys_action_access
*/

INSERT INTO `sys_action_access` VALUES (577,33,'display',28,0);
INSERT INTO `sys_action_access` VALUES (578,33,'admin_display',28,0);
INSERT INTO `sys_action_access` VALUES (579,33,'set_metadata',28,0);
INSERT INTO `sys_action_access` VALUES (580,33,'create_article',28,0);
INSERT INTO `sys_action_access` VALUES (581,33,'create_articles_folder',28,0);
INSERT INTO `sys_action_access` VALUES (582,33,'edit',28,0);
INSERT INTO `sys_action_access` VALUES (583,33,'publish',28,0);
INSERT INTO `sys_action_access` VALUES (584,33,'unpublish',28,0);
INSERT INTO `sys_action_access` VALUES (585,33,'delete',28,0);
INSERT INTO `sys_action_access` VALUES (595,33,'display',27,0);
INSERT INTO `sys_action_access` VALUES (596,47,'display',28,0);
INSERT INTO `sys_action_access` VALUES (597,47,'admin_display',28,0);
INSERT INTO `sys_action_access` VALUES (598,47,'set_metadata',28,0);
INSERT INTO `sys_action_access` VALUES (599,47,'create_catalog_folder',28,0);
INSERT INTO `sys_action_access` VALUES (600,47,'create_catalog_object',28,0);
INSERT INTO `sys_action_access` VALUES (601,47,'edit',28,0);
INSERT INTO `sys_action_access` VALUES (602,47,'publish',28,0);
INSERT INTO `sys_action_access` VALUES (603,47,'unpublish',28,0);
INSERT INTO `sys_action_access` VALUES (604,47,'delete',28,0);
INSERT INTO `sys_action_access` VALUES (614,47,'display',27,0);
INSERT INTO `sys_action_access` VALUES (615,48,'display',28,0);
INSERT INTO `sys_action_access` VALUES (616,48,'admin_display',28,0);
INSERT INTO `sys_action_access` VALUES (617,48,'edit',28,0);
INSERT INTO `sys_action_access` VALUES (618,48,'publish',28,0);
INSERT INTO `sys_action_access` VALUES (619,48,'unpublish',28,0);
INSERT INTO `sys_action_access` VALUES (620,48,'delete',28,0);
INSERT INTO `sys_action_access` VALUES (627,48,'display',27,0);
INSERT INTO `sys_action_access` VALUES (632,38,'display',28,0);
INSERT INTO `sys_action_access` VALUES (633,38,'admin_display',28,0);
INSERT INTO `sys_action_access` VALUES (634,38,'set_metadata',28,0);
INSERT INTO `sys_action_access` VALUES (635,38,'admin_detail',28,0);
INSERT INTO `sys_action_access` VALUES (636,38,'create_document',28,0);
INSERT INTO `sys_action_access` VALUES (637,38,'print_version',28,0);
INSERT INTO `sys_action_access` VALUES (638,38,'edit',28,0);
INSERT INTO `sys_action_access` VALUES (639,38,'publish',28,0);
INSERT INTO `sys_action_access` VALUES (640,38,'unpublish',28,0);
INSERT INTO `sys_action_access` VALUES (641,38,'delete',28,0);
INSERT INTO `sys_action_access` VALUES (652,38,'display',27,0);
INSERT INTO `sys_action_access` VALUES (653,38,'print_version',27,0);
INSERT INTO `sys_action_access` VALUES (654,41,'display',28,0);
INSERT INTO `sys_action_access` VALUES (655,41,'admin_display',28,0);
INSERT INTO `sys_action_access` VALUES (656,41,'admin_detail',28,0);
INSERT INTO `sys_action_access` VALUES (657,41,'create_faq_object',28,0);
INSERT INTO `sys_action_access` VALUES (658,41,'edit',28,0);
INSERT INTO `sys_action_access` VALUES (659,41,'publish',28,0);
INSERT INTO `sys_action_access` VALUES (660,41,'unpublish',28,0);
INSERT INTO `sys_action_access` VALUES (661,41,'delete',28,0);
INSERT INTO `sys_action_access` VALUES (670,41,'display',27,0);
INSERT INTO `sys_action_access` VALUES (684,42,'display',28,0);
INSERT INTO `sys_action_access` VALUES (685,42,'admin_detail',28,0);
INSERT INTO `sys_action_access` VALUES (686,42,'edit',28,0);
INSERT INTO `sys_action_access` VALUES (687,42,'publish',28,0);
INSERT INTO `sys_action_access` VALUES (688,42,'unpublish',28,0);
INSERT INTO `sys_action_access` VALUES (689,42,'delete',28,0);
INSERT INTO `sys_action_access` VALUES (696,42,'display',27,0);
INSERT INTO `sys_action_access` VALUES (716,18,'display',28,0);
INSERT INTO `sys_action_access` VALUES (717,18,'edit',28,0);
INSERT INTO `sys_action_access` VALUES (718,18,'delete',28,0);
INSERT INTO `sys_action_access` VALUES (722,18,'display',27,0);
INSERT INTO `sys_action_access` VALUES (755,19,'display',28,0);
INSERT INTO `sys_action_access` VALUES (756,19,'edit',28,0);
INSERT INTO `sys_action_access` VALUES (757,19,'edit_variations',28,0);
INSERT INTO `sys_action_access` VALUES (758,19,'delete',28,0);
INSERT INTO `sys_action_access` VALUES (763,19,'display',27,0);
INSERT INTO `sys_action_access` VALUES (766,10,'login',28,0);
INSERT INTO `sys_action_access` VALUES (767,10,'logout',28,0);
INSERT INTO `sys_action_access` VALUES (768,10,'edit',28,0);
INSERT INTO `sys_action_access` VALUES (769,10,'change_user_locale',28,0);
INSERT INTO `sys_action_access` VALUES (774,10,'login',27,0);
INSERT INTO `sys_action_access` VALUES (775,10,'logout',27,0);
INSERT INTO `sys_action_access` VALUES (776,1,'display',28,0);
INSERT INTO `sys_action_access` VALUES (777,1,'admin_display',28,0);
INSERT INTO `sys_action_access` VALUES (778,1,'create_document',28,0);
INSERT INTO `sys_action_access` VALUES (779,1,'set_metadata',28,0);
INSERT INTO `sys_action_access` VALUES (780,1,'edit',28,0);
INSERT INTO `sys_action_access` VALUES (786,1,'display',27,0);
INSERT INTO `sys_action_access` VALUES (1014,59,'display',28,0);
INSERT INTO `sys_action_access` VALUES (1015,59,'edit',28,0);
INSERT INTO `sys_action_access` VALUES (1016,59,'delete',28,0);
INSERT INTO `sys_action_access` VALUES (1052,15,'admin_display',28,0);
INSERT INTO `sys_action_access` VALUES (1053,15,'create_message',28,0);
INSERT INTO `sys_action_access` VALUES (1054,15,'edit',28,0);
INSERT INTO `sys_action_access` VALUES (1055,15,'delete',28,0);
INSERT INTO `sys_action_access` VALUES (1097,9,'admin_display',28,0);
INSERT INTO `sys_action_access` VALUES (1098,9,'edit',28,0);
INSERT INTO `sys_action_access` VALUES (1099,9,'delete',28,0);
INSERT INTO `sys_action_access` VALUES (1107,7,'admin_display',28,0);
INSERT INTO `sys_action_access` VALUES (1108,7,'edit',28,0);
INSERT INTO `sys_action_access` VALUES (1109,7,'set_membership',28,0);
INSERT INTO `sys_action_access` VALUES (1110,7,'change_password',28,0);
INSERT INTO `sys_action_access` VALUES (1111,7,'delete',28,0);
INSERT INTO `sys_action_access` VALUES (1152,44,'display',28,0);
INSERT INTO `sys_action_access` VALUES (1153,44,'admin_display',28,0);
INSERT INTO `sys_action_access` VALUES (1154,44,'edit',28,0);
INSERT INTO `sys_action_access` VALUES (1155,44,'publish',28,0);
INSERT INTO `sys_action_access` VALUES (1156,44,'unpublish',28,0);
INSERT INTO `sys_action_access` VALUES (1157,44,'delete',28,0);
INSERT INTO `sys_action_access` VALUES (1164,44,'display',27,0);
INSERT INTO `sys_action_access` VALUES (1191,29,'display',28,0);
INSERT INTO `sys_action_access` VALUES (1192,29,'admin_display',28,0);
INSERT INTO `sys_action_access` VALUES (1193,29,'create_poll',28,0);
INSERT INTO `sys_action_access` VALUES (1194,29,'delete',28,0);
INSERT INTO `sys_action_access` VALUES (1195,29,'vote',28,0);
INSERT INTO `sys_action_access` VALUES (1201,29,'display',27,0);
INSERT INTO `sys_action_access` VALUES (1202,29,'vote',27,0);
INSERT INTO `sys_action_access` VALUES (1222,43,'display',28,0);
INSERT INTO `sys_action_access` VALUES (1223,43,'admin_display',28,0);
INSERT INTO `sys_action_access` VALUES (1224,43,'set_metadata',28,0);
INSERT INTO `sys_action_access` VALUES (1225,43,'create_catalog_folder',28,0);
INSERT INTO `sys_action_access` VALUES (1226,43,'create_catalog_object',28,0);
INSERT INTO `sys_action_access` VALUES (1227,43,'edit',28,0);
INSERT INTO `sys_action_access` VALUES (1228,43,'publish',28,0);
INSERT INTO `sys_action_access` VALUES (1229,43,'unpublish',28,0);
INSERT INTO `sys_action_access` VALUES (1230,43,'delete',28,0);
INSERT INTO `sys_action_access` VALUES (1240,43,'display',27,0);
INSERT INTO `sys_action_access` VALUES (1270,36,'display',28,0);
INSERT INTO `sys_action_access` VALUES (1271,36,'set_metadata',28,0);
INSERT INTO `sys_action_access` VALUES (1272,36,'admin_detail',28,0);
INSERT INTO `sys_action_access` VALUES (1273,36,'print_version',28,0);
INSERT INTO `sys_action_access` VALUES (1274,36,'edit',28,0);
INSERT INTO `sys_action_access` VALUES (1275,36,'publish',28,0);
INSERT INTO `sys_action_access` VALUES (1276,36,'unpublish',28,0);
INSERT INTO `sys_action_access` VALUES (1277,36,'delete',28,0);
INSERT INTO `sys_action_access` VALUES (1286,36,'display',27,0);
INSERT INTO `sys_action_access` VALUES (1293,30,'display',28,0);
INSERT INTO `sys_action_access` VALUES (1294,30,'admin_display',28,0);
INSERT INTO `sys_action_access` VALUES (1295,30,'set_metadata',28,0);
INSERT INTO `sys_action_access` VALUES (1296,30,'create_article',28,0);
INSERT INTO `sys_action_access` VALUES (1297,30,'create_articles_folder',28,0);
INSERT INTO `sys_action_access` VALUES (1298,30,'edit',28,0);
INSERT INTO `sys_action_access` VALUES (1299,30,'publish',28,0);
INSERT INTO `sys_action_access` VALUES (1300,30,'unpublish',28,0);
INSERT INTO `sys_action_access` VALUES (1301,30,'delete',28,0);
INSERT INTO `sys_action_access` VALUES (1311,30,'display',27,0);
INSERT INTO `sys_action_access` VALUES (1319,46,'display',28,0);
INSERT INTO `sys_action_access` VALUES (1320,46,'edit',28,0);
INSERT INTO `sys_action_access` VALUES (1321,46,'delete',28,0);
INSERT INTO `sys_action_access` VALUES (1324,34,'display',28,0);
INSERT INTO `sys_action_access` VALUES (1325,34,'admin_display',28,0);
INSERT INTO `sys_action_access` VALUES (1326,34,'set_metadata',28,0);
INSERT INTO `sys_action_access` VALUES (1327,34,'admin_detail',28,0);
INSERT INTO `sys_action_access` VALUES (1328,34,'create_document',28,0);
INSERT INTO `sys_action_access` VALUES (1329,34,'print_version',28,0);
INSERT INTO `sys_action_access` VALUES (1330,34,'edit',28,0);
INSERT INTO `sys_action_access` VALUES (1331,34,'publish',28,0);
INSERT INTO `sys_action_access` VALUES (1332,34,'unpublish',28,0);
INSERT INTO `sys_action_access` VALUES (1333,34,'delete',28,0);
INSERT INTO `sys_action_access` VALUES (1344,34,'display',27,0);
INSERT INTO `sys_action_access` VALUES (1345,28,'display',28,0);
INSERT INTO `sys_action_access` VALUES (1346,28,'admin_display',28,0);
INSERT INTO `sys_action_access` VALUES (1347,28,'create_faq_folder',28,0);
INSERT INTO `sys_action_access` VALUES (1348,28,'publish',28,0);
INSERT INTO `sys_action_access` VALUES (1349,28,'unpublish',28,0);
INSERT INTO `sys_action_access` VALUES (1350,28,'delete',28,0);
INSERT INTO `sys_action_access` VALUES (1356,28,'display',27,0);
INSERT INTO `sys_action_access` VALUES (1357,37,'display',28,0);
INSERT INTO `sys_action_access` VALUES (1358,37,'admin_display',28,0);
INSERT INTO `sys_action_access` VALUES (1359,37,'admin_detail',28,0);
INSERT INTO `sys_action_access` VALUES (1360,37,'create_faq_object',28,0);
INSERT INTO `sys_action_access` VALUES (1361,37,'edit',28,0);
INSERT INTO `sys_action_access` VALUES (1362,37,'publish',28,0);
INSERT INTO `sys_action_access` VALUES (1363,37,'unpublish',28,0);
INSERT INTO `sys_action_access` VALUES (1364,37,'delete',28,0);
INSERT INTO `sys_action_access` VALUES (1373,37,'display',27,0);
INSERT INTO `sys_action_access` VALUES (1374,32,'display',28,0);
INSERT INTO `sys_action_access` VALUES (1375,32,'admin_display',28,0);
INSERT INTO `sys_action_access` VALUES (1376,32,'edit',28,0);
INSERT INTO `sys_action_access` VALUES (1377,32,'delete',28,0);
INSERT INTO `sys_action_access` VALUES (1382,32,'display',27,0);
INSERT INTO `sys_action_access` VALUES (1383,17,'admin_display',28,0);
INSERT INTO `sys_action_access` VALUES (1384,17,'create_file',28,0);
INSERT INTO `sys_action_access` VALUES (1385,17,'create_files_folder',28,0);
INSERT INTO `sys_action_access` VALUES (1386,17,'edit',28,0);
INSERT INTO `sys_action_access` VALUES (1387,17,'delete',28,0);
INSERT INTO `sys_action_access` VALUES (1388,17,'file_select',28,0);
INSERT INTO `sys_action_access` VALUES (1395,21,'display',28,0);
INSERT INTO `sys_action_access` VALUES (1396,21,'edit',28,0);
INSERT INTO `sys_action_access` VALUES (1397,21,'delete',28,0);
INSERT INTO `sys_action_access` VALUES (1399,31,'display',28,0);
INSERT INTO `sys_action_access` VALUES (1400,31,'admin_display',28,0);
INSERT INTO `sys_action_access` VALUES (1401,31,'create_guestbook_message',28,0);
INSERT INTO `sys_action_access` VALUES (1402,31,'edit',28,0);
INSERT INTO `sys_action_access` VALUES (1403,31,'delete',28,0);
INSERT INTO `sys_action_access` VALUES (1408,31,'display',27,0);
INSERT INTO `sys_action_access` VALUES (1409,16,'admin_display',28,0);
INSERT INTO `sys_action_access` VALUES (1410,16,'create_image',28,0);
INSERT INTO `sys_action_access` VALUES (1411,16,'create_images_folder',28,0);
INSERT INTO `sys_action_access` VALUES (1412,16,'edit',28,0);
INSERT INTO `sys_action_access` VALUES (1413,16,'delete',28,0);
INSERT INTO `sys_action_access` VALUES (1414,16,'image_select',28,0);
INSERT INTO `sys_action_access` VALUES (1421,20,'display',28,0);
INSERT INTO `sys_action_access` VALUES (1422,20,'edit',28,0);
INSERT INTO `sys_action_access` VALUES (1423,20,'delete',28,0);
INSERT INTO `sys_action_access` VALUES (1425,14,'admin_display',28,0);
INSERT INTO `sys_action_access` VALUES (1426,14,'create_navigation_item',28,0);
INSERT INTO `sys_action_access` VALUES (1427,14,'edit',28,0);
INSERT INTO `sys_action_access` VALUES (1428,14,'publish',28,0);
INSERT INTO `sys_action_access` VALUES (1429,14,'unpublish',28,0);
INSERT INTO `sys_action_access` VALUES (1430,14,'delete',28,0);
INSERT INTO `sys_action_access` VALUES (1437,25,'display',28,0);
INSERT INTO `sys_action_access` VALUES (1438,25,'admin_display',28,0);
INSERT INTO `sys_action_access` VALUES (1439,25,'create_news',28,0);
INSERT INTO `sys_action_access` VALUES (1440,25,'edit',28,0);
INSERT INTO `sys_action_access` VALUES (1445,23,'display',28,0);
INSERT INTO `sys_action_access` VALUES (1446,23,'edit',28,0);
INSERT INTO `sys_action_access` VALUES (1447,23,'delete',28,0);
INSERT INTO `sys_action_access` VALUES (1449,35,'display',28,0);
INSERT INTO `sys_action_access` VALUES (1450,35,'edit',28,0);
INSERT INTO `sys_action_access` VALUES (1451,35,'delete',28,0);
INSERT INTO `sys_action_access` VALUES (1454,35,'display',27,0);
INSERT INTO `sys_action_access` VALUES (1455,5,'admin_display',28,0);
INSERT INTO `sys_action_access` VALUES (1456,5,'set_group_access',28,0);
INSERT INTO `sys_action_access` VALUES (1457,5,'toggle',28,0);
INSERT INTO `sys_action_access` VALUES (1458,5,'edit',28,0);
INSERT INTO `sys_action_access` VALUES (1461,39,'admin_display',28,0);
INSERT INTO `sys_action_access` VALUES (1462,39,'create_answer',28,0);
INSERT INTO `sys_action_access` VALUES (1463,39,'edit',28,0);
INSERT INTO `sys_action_access` VALUES (1464,39,'publish',28,0);
INSERT INTO `sys_action_access` VALUES (1465,39,'unpublish',28,0);
INSERT INTO `sys_action_access` VALUES (1466,39,'delete',28,0);
INSERT INTO `sys_action_access` VALUES (1473,22,'admin_display',28,0);
INSERT INTO `sys_action_access` VALUES (1474,22,'edit',28,0);
INSERT INTO `sys_action_access` VALUES (1475,22,'update',28,0);
INSERT INTO `sys_action_access` VALUES (1476,22,'delete',28,0);
INSERT INTO `sys_action_access` VALUES (1494,26,'events_list',28,0);
INSERT INTO `sys_action_access` VALUES (1495,26,'edit',28,0);
INSERT INTO `sys_action_access` VALUES (1496,26,'delete',28,0);
INSERT INTO `sys_action_access` VALUES (1499,27,'admin_display',28,0);
INSERT INTO `sys_action_access` VALUES (1500,27,'pages_report',28,0);
INSERT INTO `sys_action_access` VALUES (1501,27,'referers_report',28,0);
INSERT INTO `sys_action_access` VALUES (1502,27,'hits_hosts_report',28,0);
INSERT INTO `sys_action_access` VALUES (1503,27,'ips_report',28,0);
INSERT INTO `sys_action_access` VALUES (1504,27,'keywords_report',28,0);
INSERT INTO `sys_action_access` VALUES (1505,27,'search_engines_report',28,0);
INSERT INTO `sys_action_access` VALUES (1506,27,'routes_report',28,0);
INSERT INTO `sys_action_access` VALUES (1507,27,'edit',28,0);
INSERT INTO `sys_action_access` VALUES (1508,27,'delete',28,0);
INSERT INTO `sys_action_access` VALUES (1521,6,'admin_display',28,0);
INSERT INTO `sys_action_access` VALUES (1522,6,'create_user',28,0);
INSERT INTO `sys_action_access` VALUES (1523,6,'edit',28,0);
INSERT INTO `sys_action_access` VALUES (1526,11,'activate_password',28,0);
INSERT INTO `sys_action_access` VALUES (1527,11,'edit',28,0);
INSERT INTO `sys_action_access` VALUES (1529,11,'activate_password',27,0);
INSERT INTO `sys_action_access` VALUES (1530,12,'change_own_password',28,0);
INSERT INTO `sys_action_access` VALUES (1531,12,'edit',28,0);
INSERT INTO `sys_action_access` VALUES (1533,12,'change_own_password',27,0);
INSERT INTO `sys_action_access` VALUES (1534,13,'generate_password',28,0);
INSERT INTO `sys_action_access` VALUES (1535,13,'password_generated',28,0);
INSERT INTO `sys_action_access` VALUES (1536,13,'password_not_generated',28,0);
INSERT INTO `sys_action_access` VALUES (1537,13,'edit',28,0);
INSERT INTO `sys_action_access` VALUES (1538,13,'delete',28,0);
INSERT INTO `sys_action_access` VALUES (1542,13,'generate_password',27,0);
INSERT INTO `sys_action_access` VALUES (1543,13,'password_generated',27,0);
INSERT INTO `sys_action_access` VALUES (1544,13,'password_not_generated',27,0);
INSERT INTO `sys_action_access` VALUES (1545,8,'admin_display',28,0);
INSERT INTO `sys_action_access` VALUES (1546,8,'create_user_group',28,0);
INSERT INTO `sys_action_access` VALUES (1547,8,'edit',28,0);
INSERT INTO `sys_action_access` VALUES (1559,45,'display',28,0);
INSERT INTO `sys_action_access` VALUES (1560,45,'recover',28,0);
INSERT INTO `sys_action_access` VALUES (1561,45,'edit',28,0);
INSERT INTO `sys_action_access` VALUES (1562,45,'delete',28,0);
INSERT INTO `sys_action_access` VALUES (1567,45,'display',27,0);
INSERT INTO `sys_action_access` VALUES (1574,4,'display',28,0);
INSERT INTO `sys_action_access` VALUES (1575,4,'admin_display',28,0);
INSERT INTO `sys_action_access` VALUES (1576,4,'set_group_access',28,0);
INSERT INTO `sys_action_access` VALUES (1577,4,'set_group_access_template',28,0);
INSERT INTO `sys_action_access` VALUES (1578,4,'edit',28,0);
INSERT INTO `sys_action_access` VALUES (1620,49,'admin_display',28,0);
INSERT INTO `sys_action_access` VALUES (1621,49,'create_navigation_item',28,0);
INSERT INTO `sys_action_access` VALUES (1622,49,'edit',28,0);
INSERT INTO `sys_action_access` VALUES (1623,49,'publish',28,0);
INSERT INTO `sys_action_access` VALUES (1624,49,'unpublish',28,0);
INSERT INTO `sys_action_access` VALUES (1625,49,'delete',28,0);
INSERT INTO `sys_action_access` VALUES (1632,3,'admin_display',28,0);
INSERT INTO `sys_action_access` VALUES (1633,3,'change_controller',28,0);
INSERT INTO `sys_action_access` VALUES (1634,3,'toggle',28,0);
INSERT INTO `sys_action_access` VALUES (1635,3,'edit',28,0);
INSERT INTO `sys_action_access` VALUES (1636,3,'node_select',28,0);
INSERT INTO `sys_action_access` VALUES (1637,3,'save_priority',28,0);
INSERT INTO `sys_action_access` VALUES (1638,3,'multi_move',28,0);
INSERT INTO `sys_action_access` VALUES (1639,3,'multi_delete',28,0);
INSERT INTO `sys_action_access` VALUES (1640,3,'multi_toggle_publish_status',28,0);
INSERT INTO `sys_action_access` VALUES (1641,3,'delete',28,0);
INSERT INTO `sys_action_access` VALUES (1649,40,'display',28,0);
INSERT INTO `sys_action_access` VALUES (1650,40,'admin_display',28,0);
INSERT INTO `sys_action_access` VALUES (1651,40,'edit',28,0);
INSERT INTO `sys_action_access` VALUES (1652,40,'publish',28,0);
INSERT INTO `sys_action_access` VALUES (1653,40,'unpublish',28,0);
INSERT INTO `sys_action_access` VALUES (1654,40,'delete',28,0);
INSERT INTO `sys_action_access` VALUES (1661,40,'display',27,0);
INSERT INTO `sys_action_access` VALUES (1669,24,'display',28,0);
INSERT INTO `sys_action_access` VALUES (1670,24,'delete',28,0);
INSERT INTO `sys_action_access` VALUES (1672,24,'display',27,0);
INSERT INTO `sys_action_access` VALUES (1673,2,'display',28,0);
INSERT INTO `sys_action_access` VALUES (1674,2,'admin_display',28,0);
INSERT INTO `sys_action_access` VALUES (1675,2,'edit',28,0);
INSERT INTO `sys_action_access` VALUES (1676,2,'register_new_object',28,0);

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
Table data for init_en.sys_class
*/

INSERT INTO `sys_class` VALUES (1,'main_page','/shared/images/folder.gif',0,1);
INSERT INTO `sys_class` VALUES (3,'site_structure','/shared/images/generic.gif',1,1);
INSERT INTO `sys_class` VALUES (7,'user_object','/shared/images/generic.gif',1,0);
INSERT INTO `sys_class` VALUES (9,'user_group','/shared/images/generic.gif',1,0);
INSERT INTO `sys_class` VALUES (14,'navigation_item','/shared/images/generic.gif',1,1);
INSERT INTO `sys_class` VALUES (15,'message','/shared/images/generic.gif',0,1);
INSERT INTO `sys_class` VALUES (18,'file_object','/shared/images/generic.gif',1,0);
INSERT INTO `sys_class` VALUES (19,'image_object','/shared/images/generic.gif',1,0);
INSERT INTO `sys_class` VALUES (38,'document','/shared/images/generic.gif',1,1);
INSERT INTO `sys_class` VALUES (46,'site_object','/shared/images/generic.gif',1,1);
INSERT INTO `sys_class` VALUES (49,'poll','/shared/images/generic.gif',1,1);
INSERT INTO `sys_class` VALUES (50,'news_object','/shared/images/generic.gif',0,0);

/*
Table struture for sys_controller
*/

drop table if exists `sys_controller`;
CREATE TABLE `sys_controller` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=InnoDB;

/*
Table data for init_en.sys_controller
*/

INSERT INTO `sys_controller` VALUES (1,'main_page_controller');
INSERT INTO `sys_controller` VALUES (2,'admin_page_controller');
INSERT INTO `sys_controller` VALUES (3,'site_structure_controller');
INSERT INTO `sys_controller` VALUES (4,'controller_folder_controller');
INSERT INTO `sys_controller` VALUES (5,'objects_access_controller');
INSERT INTO `sys_controller` VALUES (6,'users_folder_controller');
INSERT INTO `sys_controller` VALUES (7,'user_controller');
INSERT INTO `sys_controller` VALUES (8,'user_groups_folder_controller');
INSERT INTO `sys_controller` VALUES (9,'user_group_controller');
INSERT INTO `sys_controller` VALUES (10,'login_object_controller');
INSERT INTO `sys_controller` VALUES (11,'user_activate_password_controller');
INSERT INTO `sys_controller` VALUES (12,'user_change_own_password_controller');
INSERT INTO `sys_controller` VALUES (13,'user_generate_password_controller');
INSERT INTO `sys_controller` VALUES (14,'navigation_item_controller');
INSERT INTO `sys_controller` VALUES (15,'message_controller');
INSERT INTO `sys_controller` VALUES (16,'images_folder_controller');
INSERT INTO `sys_controller` VALUES (17,'files_folder_controller');
INSERT INTO `sys_controller` VALUES (18,'file_object_controller');
INSERT INTO `sys_controller` VALUES (19,'image_object_controller');
INSERT INTO `sys_controller` VALUES (20,'image_select_controller');
INSERT INTO `sys_controller` VALUES (21,'file_select_controller');
INSERT INTO `sys_controller` VALUES (22,'site_param_object_controller');
INSERT INTO `sys_controller` VALUES (23,'node_select_controller');
INSERT INTO `sys_controller` VALUES (26,'stats_event_controller');
INSERT INTO `sys_controller` VALUES (27,'stats_report_controller');
INSERT INTO `sys_controller` VALUES (34,'document_controller');
INSERT INTO `sys_controller` VALUES (35,'not_found_page_controller');
INSERT INTO `sys_controller` VALUES (42,'site_object_controller');
INSERT INTO `sys_controller` VALUES (45,'version_controller');
INSERT INTO `sys_controller` VALUES (46,'control_panel_controller');
INSERT INTO `sys_controller` VALUES (47,'simple_folder_controller');
INSERT INTO `sys_controller` VALUES (48,'cache_manager_controller');
INSERT INTO `sys_controller` VALUES (49,'admin_navigation_item_controller');

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
  `title` varchar(255) NULL,
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
  `access` tinyint(4) NOT NULL default '0',
  `accessor_type` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `accessor_id` (`accessor_id`),
  KEY `ora` (`object_id`,`access`,`accessor_id`),
  KEY `accessor_type` (`accessor_type`)
) TYPE=InnoDB COMMENT='InnoDB free: 114688 kB; InnoDB free: 114688 kB; InnoDB free:';

/*
Table data for init_en.sys_object_access
*/

INSERT INTO `sys_object_access` VALUES (730,87,28,1,0);
INSERT INTO `sys_object_access` VALUES (732,87,27,1,0);
INSERT INTO `sys_object_access` VALUES (828,37,28,1,0);
INSERT INTO `sys_object_access` VALUES (830,37,27,1,0);
INSERT INTO `sys_object_access` VALUES (831,39,28,1,0);
INSERT INTO `sys_object_access` VALUES (833,39,27,1,0);
INSERT INTO `sys_object_access` VALUES (858,38,28,1,0);
INSERT INTO `sys_object_access` VALUES (860,38,27,1,0);
INSERT INTO `sys_object_access` VALUES (861,43,28,1,0);
INSERT INTO `sys_object_access` VALUES (863,43,27,1,0);
INSERT INTO `sys_object_access` VALUES (864,119,28,1,0);
INSERT INTO `sys_object_access` VALUES (866,119,27,1,0);
INSERT INTO `sys_object_access` VALUES (867,47,28,1,0);
INSERT INTO `sys_object_access` VALUES (869,47,27,1,0);
INSERT INTO `sys_object_access` VALUES (873,46,28,1,0);
INSERT INTO `sys_object_access` VALUES (875,46,27,1,0);
INSERT INTO `sys_object_access` VALUES (876,40,28,1,0);
INSERT INTO `sys_object_access` VALUES (878,40,27,1,0);
INSERT INTO `sys_object_access` VALUES (879,42,28,1,0);
INSERT INTO `sys_object_access` VALUES (881,42,27,1,0);
INSERT INTO `sys_object_access` VALUES (882,41,28,1,0);
INSERT INTO `sys_object_access` VALUES (884,41,27,1,0);
INSERT INTO `sys_object_access` VALUES (885,121,28,1,0);
INSERT INTO `sys_object_access` VALUES (887,121,27,1,0);
INSERT INTO `sys_object_access` VALUES (888,45,28,1,0);
INSERT INTO `sys_object_access` VALUES (890,45,27,1,0);
INSERT INTO `sys_object_access` VALUES (891,44,28,1,0);
INSERT INTO `sys_object_access` VALUES (893,44,27,1,0);
INSERT INTO `sys_object_access` VALUES (894,49,28,1,0);
INSERT INTO `sys_object_access` VALUES (896,49,27,1,0);
INSERT INTO `sys_object_access` VALUES (1024,22,28,1,0);
INSERT INTO `sys_object_access` VALUES (1026,118,28,1,0);
INSERT INTO `sys_object_access` VALUES (1028,23,28,1,0);
INSERT INTO `sys_object_access` VALUES (1030,120,28,1,0);
INSERT INTO `sys_object_access` VALUES (1032,52,28,1,0);
INSERT INTO `sys_object_access` VALUES (1034,21,28,1,0);
INSERT INTO `sys_object_access` VALUES (1268,28,28,1,0);
INSERT INTO `sys_object_access` VALUES (1270,28,27,1,0);
INSERT INTO `sys_object_access` VALUES (1274,27,28,1,0);
INSERT INTO `sys_object_access` VALUES (1276,27,27,1,0);
INSERT INTO `sys_object_access` VALUES (1280,25,28,1,0);
INSERT INTO `sys_object_access` VALUES (1282,25,27,1,0);
INSERT INTO `sys_object_access` VALUES (1492,63,28,1,0);
INSERT INTO `sys_object_access` VALUES (1494,63,27,1,0);
INSERT INTO `sys_object_access` VALUES (1726,53,28,1,0);
INSERT INTO `sys_object_access` VALUES (1727,53,27,1,0);
INSERT INTO `sys_object_access` VALUES (1756,140,27,1,0);
INSERT INTO `sys_object_access` VALUES (1757,140,28,1,0);
INSERT INTO `sys_object_access` VALUES (1782,143,28,1,0);
INSERT INTO `sys_object_access` VALUES (1784,144,28,1,0);
INSERT INTO `sys_object_access` VALUES (1786,145,28,1,0);
INSERT INTO `sys_object_access` VALUES (1788,146,28,1,0);
INSERT INTO `sys_object_access` VALUES (1954,19,27,1,0);
INSERT INTO `sys_object_access` VALUES (1958,34,27,1,0);
INSERT INTO `sys_object_access` VALUES (1962,77,27,1,0);
INSERT INTO `sys_object_access` VALUES (1966,30,27,1,0);
INSERT INTO `sys_object_access` VALUES (1973,31,27,1,0);
INSERT INTO `sys_object_access` VALUES (1975,139,27,1,0);
INSERT INTO `sys_object_access` VALUES (1979,51,27,1,0);
INSERT INTO `sys_object_access` VALUES (1981,36,27,1,0);
INSERT INTO `sys_object_access` VALUES (1983,32,27,1,0);
INSERT INTO `sys_object_access` VALUES (1987,50,27,1,0);
INSERT INTO `sys_object_access` VALUES (1989,35,27,1,0);
INSERT INTO `sys_object_access` VALUES (1991,29,27,1,0);
INSERT INTO `sys_object_access` VALUES (1993,33,27,1,0);
INSERT INTO `sys_object_access` VALUES (1999,142,27,1,0);
INSERT INTO `sys_object_access` VALUES (2001,26,27,1,0);
INSERT INTO `sys_object_access` VALUES (2003,24,27,1,0);
INSERT INTO `sys_object_access` VALUES (2005,125,27,1,0);
INSERT INTO `sys_object_access` VALUES (2060,19,28,1,0);
INSERT INTO `sys_object_access` VALUES (2064,34,28,1,0);
INSERT INTO `sys_object_access` VALUES (2068,77,28,1,0);
INSERT INTO `sys_object_access` VALUES (2072,30,28,1,0);
INSERT INTO `sys_object_access` VALUES (2074,20,28,1,0);
INSERT INTO `sys_object_access` VALUES (2080,31,28,1,0);
INSERT INTO `sys_object_access` VALUES (2082,139,28,1,0);
INSERT INTO `sys_object_access` VALUES (2086,51,28,1,0);
INSERT INTO `sys_object_access` VALUES (2088,36,28,1,0);
INSERT INTO `sys_object_access` VALUES (2090,32,28,1,0);
INSERT INTO `sys_object_access` VALUES (2094,50,28,1,0);
INSERT INTO `sys_object_access` VALUES (2096,35,28,1,0);
INSERT INTO `sys_object_access` VALUES (2098,29,28,1,0);
INSERT INTO `sys_object_access` VALUES (2100,33,28,1,0);
INSERT INTO `sys_object_access` VALUES (2106,142,28,1,0);
INSERT INTO `sys_object_access` VALUES (2108,26,28,1,0);
INSERT INTO `sys_object_access` VALUES (2110,24,28,1,0);
INSERT INTO `sys_object_access` VALUES (2112,125,28,1,0);
INSERT INTO `sys_object_access` VALUES (2140,148,28,1,0);
INSERT INTO `sys_object_access` VALUES (2142,149,28,1,0);
INSERT INTO `sys_object_access` VALUES (2154,155,28,1,0);
INSERT INTO `sys_object_access` VALUES (2167,160,28,1,0);
INSERT INTO `sys_object_access` VALUES (2169,161,28,1,0);
INSERT INTO `sys_object_access` VALUES (2171,162,28,1,0);
INSERT INTO `sys_object_access` VALUES (2173,163,28,1,0);
INSERT INTO `sys_object_access` VALUES (2175,164,28,1,0);
INSERT INTO `sys_object_access` VALUES (2177,165,28,1,0);
INSERT INTO `sys_object_access` VALUES (2179,166,28,1,0);

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
Table data for init_en.sys_object_version
*/

INSERT INTO `sys_object_version` VALUES (6,25,0,1076762314,1076762314,1);
INSERT INTO `sys_object_version` VALUES (7,27,0,1076762314,1076762314,1);
INSERT INTO `sys_object_version` VALUES (8,28,0,1076762314,1076762314,1);
INSERT INTO `sys_object_version` VALUES (10,34,0,1076762315,1076762315,1);
INSERT INTO `sys_object_version` VALUES (11,37,25,1076770835,1076770835,1);
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
INSERT INTO `sys_object_version` VALUES (26,49,25,1076772668,1076772668,1);
INSERT INTO `sys_object_version` VALUES (53,87,25,1084284790,1084284790,1);
INSERT INTO `sys_object_version` VALUES (140,119,25,1084436242,1084436242,1);
INSERT INTO `sys_object_version` VALUES (141,121,25,1084436412,1084436412,1);
INSERT INTO `sys_object_version` VALUES (201,140,25,1095492667,1095492667,1);
INSERT INTO `sys_object_version` VALUES (202,39,25,1095493592,1095493592,3);
INSERT INTO `sys_object_version` VALUES (203,40,25,1095494170,1095494170,2);
INSERT INTO `sys_object_version` VALUES (204,46,25,1095494218,1095494218,2);
INSERT INTO `sys_object_version` VALUES (206,47,25,1095495013,1095495013,2);
INSERT INTO `sys_object_version` VALUES (211,87,25,1095518793,1095518793,2);
INSERT INTO `sys_object_version` VALUES (212,87,25,1095518853,1095518853,3);
INSERT INTO `sys_object_version` VALUES (213,87,25,1095521497,1095521497,4);
INSERT INTO `sys_object_version` VALUES (214,87,25,1095521584,1095521584,5);
INSERT INTO `sys_object_version` VALUES (215,19,25,1095762404,1095762404,1);
INSERT INTO `sys_object_version` VALUES (216,38,124,1096456157,1096456157,3);
INSERT INTO `sys_object_version` VALUES (218,47,124,1096456856,1096456856,3);
INSERT INTO `sys_object_version` VALUES (219,46,124,1096456861,1096456861,3);
INSERT INTO `sys_object_version` VALUES (220,40,124,1096456867,1096456867,3);
INSERT INTO `sys_object_version` VALUES (221,43,124,1096458212,1096458212,2);
INSERT INTO `sys_object_version` VALUES (222,43,124,1096458374,1096458374,3);
INSERT INTO `sys_object_version` VALUES (223,44,124,1096458387,1096458387,2);
INSERT INTO `sys_object_version` VALUES (224,45,124,1096458449,1096458449,2);
INSERT INTO `sys_object_version` VALUES (225,119,124,1096458465,1096458465,2);
INSERT INTO `sys_object_version` VALUES (226,121,124,1096458475,1096458475,2);
INSERT INTO `sys_object_version` VALUES (227,119,124,1096458513,1096458513,3);
INSERT INTO `sys_object_version` VALUES (228,121,124,1096458518,1096458518,3);
INSERT INTO `sys_object_version` VALUES (229,143,124,1100013336,1100013336,1);
INSERT INTO `sys_object_version` VALUES (230,145,124,1100013547,1100013547,1);
INSERT INTO `sys_object_version` VALUES (231,146,124,1100014019,1100014019,1);
INSERT INTO `sys_object_version` VALUES (233,148,124,1100172147,1100172147,1);
INSERT INTO `sys_object_version` VALUES (234,149,124,1100172309,1100172309,1);
INSERT INTO `sys_object_version` VALUES (240,155,124,1100182180,1100182180,1);
INSERT INTO `sys_object_version` VALUES (241,155,124,1100182267,1100182267,2);
INSERT INTO `sys_object_version` VALUES (242,155,124,1100182287,1100182287,3);
INSERT INTO `sys_object_version` VALUES (243,155,124,1100182331,1100182331,4);
INSERT INTO `sys_object_version` VALUES (251,160,124,1100284559,1100284559,1);
INSERT INTO `sys_object_version` VALUES (252,161,124,1100284602,1100284602,1);
INSERT INTO `sys_object_version` VALUES (253,162,124,1100284636,1100284636,1);
INSERT INTO `sys_object_version` VALUES (254,163,124,1100284734,1100284734,1);
INSERT INTO `sys_object_version` VALUES (255,164,124,1100284756,1100284756,1);
INSERT INTO `sys_object_version` VALUES (256,165,124,1100284797,1100284797,1);
INSERT INTO `sys_object_version` VALUES (257,166,124,1100284815,1100284815,1);

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
  `controller_id` int(11) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `idccv` (`id`,`locale_id`,`current_version`,`class_id`,`controller_id`),
  KEY `md` (`modified_date`),
  KEY `cd` (`created_date`),
  KEY `cid` (`creator_id`),
  KEY `current_version` (`current_version`)
) TYPE=InnoDB COMMENT='InnoDB free: 10240 kB; InnoDB free: 10240 kB; InnoDB free: 1';

/*
Table data for init_en.sys_site_object
*/

INSERT INTO `sys_site_object` VALUES (19,1,1,1100287286,0,1076762314,0,'en','Main','root',1);
INSERT INTO `sys_site_object` VALUES (20,46,1,1100282879,0,1076762314,0,'en','Site management','admin',2);
INSERT INTO `sys_site_object` VALUES (21,46,2,1076769130,0,1076762314,0,'en','Site structure','site_structure',3);
INSERT INTO `sys_site_object` VALUES (22,46,2,1096458350,0,1076762314,0,'en','Controllers','controllers',4);
INSERT INTO `sys_site_object` VALUES (23,46,2,1076769145,0,1076762314,0,'en','Objects access','objects_access',5);
INSERT INTO `sys_site_object` VALUES (24,46,2,1100278924,0,1076762314,0,'en','Users','users',6);
INSERT INTO `sys_site_object` VALUES (25,7,1,1095423742,0,1076762314,0,'en','Management','admin',7);
INSERT INTO `sys_site_object` VALUES (26,46,2,1100278924,0,1076762314,0,'en','User groups','user_groups',8);
INSERT INTO `sys_site_object` VALUES (27,9,1,1076762314,0,1076762314,0,'en','Visitors','visitors',9);
INSERT INTO `sys_site_object` VALUES (28,9,1,1076762314,0,1076762314,0,'en','Admins','admins',9);
INSERT INTO `sys_site_object` VALUES (29,46,2,1076769188,0,1076762314,0,'en','Login','login',10);
INSERT INTO `sys_site_object` VALUES (30,46,2,1076769202,0,1076762314,0,'en','Activate password','activate_password',11);
INSERT INTO `sys_site_object` VALUES (31,46,2,1076769224,0,1076762314,0,'en','Change password','change_password',12);
INSERT INTO `sys_site_object` VALUES (32,46,2,1076769246,0,1076762315,0,'en','Forgot password?','generate_password',13);
INSERT INTO `sys_site_object` VALUES (33,14,2,1076771224,0,1076762315,0,'en','Navigation','navigation',14);
INSERT INTO `sys_site_object` VALUES (34,15,1,1076762315,0,1076762315,0,'en','Messages','messages',15);
INSERT INTO `sys_site_object` VALUES (35,46,1,1100283904,0,1076762315,0,'en','Images','images',16);
INSERT INTO `sys_site_object` VALUES (36,46,1,1100283889,0,1076762315,0,'en','Files','files',17);
INSERT INTO `sys_site_object` VALUES (37,14,1,1100176539,0,1076770835,25,'en','Admin navigation','navigation',49);
INSERT INTO `sys_site_object` VALUES (38,14,3,1100174979,0,1076770879,25,'en','Site management','site_management',49);
INSERT INTO `sys_site_object` VALUES (39,14,3,1100174799,0,1076771149,25,'en','Content management','content_management',49);
INSERT INTO `sys_site_object` VALUES (40,14,3,1100174896,0,1076771604,25,'en','Navigation','navigation',49);
INSERT INTO `sys_site_object` VALUES (41,14,1,1100175046,0,1076772382,25,'en','Site structure','site_structure',49);
INSERT INTO `sys_site_object` VALUES (42,14,1,1100186042,0,1076772439,25,'en','Objects access','objects_access',49);
INSERT INTO `sys_site_object` VALUES (43,14,3,1100175061,0,1076772480,25,'en','Controllers','classes',49);
INSERT INTO `sys_site_object` VALUES (44,14,2,1100278943,0,1076772520,25,'en','Users','users',49);
INSERT INTO `sys_site_object` VALUES (45,14,2,1100278951,0,1076772540,25,'en','User groups','user_groups',49);
INSERT INTO `sys_site_object` VALUES (46,14,3,1100174887,0,1076772578,25,'en','Messages','messages',49);
INSERT INTO `sys_site_object` VALUES (47,14,3,1100283962,0,1076772601,25,'en','Files','files',49);
INSERT INTO `sys_site_object` VALUES (49,14,1,1076772668,0,1076772668,25,'en','User menu','main',14);
INSERT INTO `sys_site_object` VALUES (50,46,1,1100283671,0,1084266500,25,'en','Image select','image_select',20);
INSERT INTO `sys_site_object` VALUES (51,46,1,1100283671,0,1084266511,25,'en','File select','file_select',21);
INSERT INTO `sys_site_object` VALUES (52,46,10,1084361850,0,1084266564,25,'en','Site params','site_params',22);
INSERT INTO `sys_site_object` VALUES (53,46,1,1084266606,0,1084266606,25,'en','Node select','node_select',23);
INSERT INTO `sys_site_object` VALUES (77,46,2,1084368911,0,1084283697,25,'en','Not found','404',35);
INSERT INTO `sys_site_object` VALUES (87,15,5,1095521584,0,1084284790,25,'en','Not found','404',15);
INSERT INTO `sys_site_object` VALUES (118,46,1,1084436155,0,1084436155,25,'en','Events','events',26);
INSERT INTO `sys_site_object` VALUES (119,14,3,1100284479,0,1084436242,25,'en','System events','events',49);
INSERT INTO `sys_site_object` VALUES (120,46,1,1084436311,0,1084436311,25,'en','Site statistics','stats',27);
INSERT INTO `sys_site_object` VALUES (121,14,3,1100175016,0,1084436412,25,'en','Site statistics','stats',49);
INSERT INTO `sys_site_object` VALUES (125,46,1,1086439596,0,1086439596,124,'en','Version control','version',45);
INSERT INTO `sys_site_object` VALUES (139,46,1,1095450568,0,1095450568,124,'en','Control panel','cp',46);
INSERT INTO `sys_site_object` VALUES (140,14,1,1100174966,0,1095492667,25,'en','JIP-mode','jip',49);
INSERT INTO `sys_site_object` VALUES (142,46,1,1100012496,0,1100012496,124,'en','Tools','tools',47);
INSERT INTO `sys_site_object` VALUES (143,14,1,1100175000,0,1100013336,124,'en','Common','common',49);
INSERT INTO `sys_site_object` VALUES (144,46,1,1100013507,0,1100013507,124,'en','Cache Manager','cache_manager',48);
INSERT INTO `sys_site_object` VALUES (145,14,1,1100175027,0,1100013547,124,'en','Navigation(admin.)','navigation',49);
INSERT INTO `sys_site_object` VALUES (146,14,1,1100175008,0,1100014019,124,'en','Security','security',49);
INSERT INTO `sys_site_object` VALUES (148,14,1,1100174816,0,1100172147,124,'en','Common','common',49);
INSERT INTO `sys_site_object` VALUES (149,14,1,1100174930,0,1100172309,124,'en','Media','media',49);
INSERT INTO `sys_site_object` VALUES (155,14,4,1100283949,0,1100182180,124,'en','Images','images',49);
INSERT INTO `sys_site_object` VALUES (160,14,1,1100284559,0,1100284559,124,'en','Hits/hosts report','hits_report',49);
INSERT INTO `sys_site_object` VALUES (161,14,1,1100284602,0,1100284602,124,'en','Popular pages report','pages_report',49);
INSERT INTO `sys_site_object` VALUES (162,14,1,1100284656,0,1100284636,124,'en','Referers report','referers_report',49);
INSERT INTO `sys_site_object` VALUES (163,14,1,1100284767,0,1100284734,124,'en','IPs report','ips_report',49);
INSERT INTO `sys_site_object` VALUES (164,14,1,1100284756,0,1100284756,124,'en','Keywords report','keywords_report',49);
INSERT INTO `sys_site_object` VALUES (165,14,1,1100284796,0,1100284796,124,'en','Search engines report','search_engines_report',49);
INSERT INTO `sys_site_object` VALUES (166,14,1,1100284815,0,1100284815,124,'en','Routes report','routes_report',49);

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
  KEY `root_id` (`root_id`),
  KEY `identifier` (`identifier`),
  KEY `level` (`level`),
  KEY `rlr` (`root_id`),
  KEY `parent_id` (`parent_id`),
  KEY `object_id` (`object_id`)
) TYPE=InnoDB;

/*
Table data for init_en.sys_site_object_tree
*/

INSERT INTO `sys_site_object_tree` VALUES (1,1,0,0,1,'root',19,'/1/',15);
INSERT INTO `sys_site_object_tree` VALUES (2,1,1,0,2,'admin',20,'/1/2/',10);
INSERT INTO `sys_site_object_tree` VALUES (3,1,2,1,3,'site_structure',21,'/1/2/3/',0);
INSERT INTO `sys_site_object_tree` VALUES (4,1,2,4,3,'controllers',22,'/1/2/4/',0);
INSERT INTO `sys_site_object_tree` VALUES (5,1,2,2,3,'objects_access',23,'/1/2/5/',0);
INSERT INTO `sys_site_object_tree` VALUES (6,1,2,0,3,'users',24,'/1/2/6/',1);
INSERT INTO `sys_site_object_tree` VALUES (7,1,6,0,4,'admin',25,'/1/2/6/7/',0);
INSERT INTO `sys_site_object_tree` VALUES (8,1,2,0,3,'user_groups',26,'/1/2/8/',2);
INSERT INTO `sys_site_object_tree` VALUES (9,1,8,30,4,'visitors',27,'/1/2/8/9/',0);
INSERT INTO `sys_site_object_tree` VALUES (10,1,8,20,4,'admins',28,'/1/2/8/10/',0);
INSERT INTO `sys_site_object_tree` VALUES (11,1,1,0,2,'login',29,'/1/11/',0);
INSERT INTO `sys_site_object_tree` VALUES (12,1,1,0,2,'activate_password',30,'/1/12/',0);
INSERT INTO `sys_site_object_tree` VALUES (13,1,1,0,2,'change_password',31,'/1/13/',0);
INSERT INTO `sys_site_object_tree` VALUES (14,1,1,0,2,'generate_password',32,'/1/14/',0);
INSERT INTO `sys_site_object_tree` VALUES (15,1,1,0,2,'navigation',33,'/1/15/',1);
INSERT INTO `sys_site_object_tree` VALUES (16,1,1,0,2,'messages',34,'/1/16/',1);
INSERT INTO `sys_site_object_tree` VALUES (17,1,1,0,2,'images',35,'/1/17/',0);
INSERT INTO `sys_site_object_tree` VALUES (18,1,1,0,2,'files',36,'/1/18/',0);
INSERT INTO `sys_site_object_tree` VALUES (19,1,2,0,3,'navigation',37,'/1/2/19/',3);
INSERT INTO `sys_site_object_tree` VALUES (20,1,19,30,4,'site_management',38,'/1/2/19/20/',3);
INSERT INTO `sys_site_object_tree` VALUES (21,1,19,20,4,'content_management',39,'/1/2/19/21/',2);
INSERT INTO `sys_site_object_tree` VALUES (22,1,116,12,6,'navigation',40,'/1/2/19/21/116/22/',0);
INSERT INTO `sys_site_object_tree` VALUES (23,1,111,1,6,'site_structure',41,'/1/2/19/20/111/23/',0);
INSERT INTO `sys_site_object_tree` VALUES (24,1,114,3,6,'objects_access',42,'/1/2/19/20/114/24/',0);
INSERT INTO `sys_site_object_tree` VALUES (25,1,114,4,6,'classes',43,'/1/2/19/20/114/25/',0);
INSERT INTO `sys_site_object_tree` VALUES (26,1,114,7,6,'users',44,'/1/2/19/20/114/26/',0);
INSERT INTO `sys_site_object_tree` VALUES (27,1,114,8,6,'user_groups',45,'/1/2/19/20/114/27/',0);
INSERT INTO `sys_site_object_tree` VALUES (28,1,116,11,6,'messages',46,'/1/2/19/21/116/28/',0);
INSERT INTO `sys_site_object_tree` VALUES (29,1,117,10,6,'files',47,'/1/2/19/21/117/29/',0);
INSERT INTO `sys_site_object_tree` VALUES (31,1,15,0,3,'main',49,'/1/15/31/',0);
INSERT INTO `sys_site_object_tree` VALUES (32,1,110,0,3,'image_select',50,'/1/110/32/',0);
INSERT INTO `sys_site_object_tree` VALUES (33,1,110,0,3,'file_select',51,'/1/110/33/',0);
INSERT INTO `sys_site_object_tree` VALUES (34,1,2,5,3,'site_params',52,'/1/2/34/',0);
INSERT INTO `sys_site_object_tree` VALUES (35,1,110,0,3,'node_select',53,'/1/110/35/',0);
INSERT INTO `sys_site_object_tree` VALUES (59,1,1,0,2,'404',77,'/1/59/',0);
INSERT INTO `sys_site_object_tree` VALUES (68,1,16,0,3,'404',87,'/1/16/68/',0);
INSERT INTO `sys_site_object_tree` VALUES (97,1,2,3,3,'events',118,'/1/2/97/',0);
INSERT INTO `sys_site_object_tree` VALUES (98,1,114,10,6,'events',119,'/1/2/19/20/114/98/',0);
INSERT INTO `sys_site_object_tree` VALUES (99,1,2,6,3,'stats',120,'/1/2/99/',0);
INSERT INTO `sys_site_object_tree` VALUES (100,1,20,11,5,'stats',121,'/1/2/19/20/100/',7);
INSERT INTO `sys_site_object_tree` VALUES (104,1,1,0,2,'version',125,'/1/104/',0);
INSERT INTO `sys_site_object_tree` VALUES (107,1,1,0,2,'cp',139,'/1/107/',0);
INSERT INTO `sys_site_object_tree` VALUES (108,1,19,10,4,'jip',140,'/1/2/19/108/',0);
INSERT INTO `sys_site_object_tree` VALUES (110,1,1,0,2,'tools',142,'/1/110/',2);
INSERT INTO `sys_site_object_tree` VALUES (111,1,20,0,5,'common',143,'/1/2/19/20/111/',2);
INSERT INTO `sys_site_object_tree` VALUES (112,1,2,0,3,'cache_manager',144,'/1/2/112/',0);
INSERT INTO `sys_site_object_tree` VALUES (113,1,111,0,6,'navigation',145,'/1/2/19/20/111/113/',0);
INSERT INTO `sys_site_object_tree` VALUES (114,1,20,0,5,'security',146,'/1/2/19/20/114/',5);
INSERT INTO `sys_site_object_tree` VALUES (116,1,21,10,5,'common',148,'/1/2/19/21/116/',2);
INSERT INTO `sys_site_object_tree` VALUES (117,1,21,20,5,'media',149,'/1/2/19/21/117/',2);
INSERT INTO `sys_site_object_tree` VALUES (118,1,117,0,6,'images',155,'/1/2/19/21/117/118/',0);
INSERT INTO `sys_site_object_tree` VALUES (120,1,100,10,6,'hits_report',160,'/1/2/19/20/100/120/',0);
INSERT INTO `sys_site_object_tree` VALUES (121,1,100,20,6,'pages_report',161,'/1/2/19/20/100/121/',0);
INSERT INTO `sys_site_object_tree` VALUES (122,1,100,30,6,'referers_report',162,'/1/2/19/20/100/122/',0);
INSERT INTO `sys_site_object_tree` VALUES (123,1,100,60,6,'ips_report',163,'/1/2/19/20/100/123/',0);
INSERT INTO `sys_site_object_tree` VALUES (124,1,100,40,6,'keywords_report',164,'/1/2/19/20/100/124/',0);
INSERT INTO `sys_site_object_tree` VALUES (125,1,100,50,6,'search_engines_report',165,'/1/2/19/20/100/125/',0);
INSERT INTO `sys_site_object_tree` VALUES (126,1,100,70,6,'routes_report',166,'/1/2/19/20/100/126/',0);

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
Table data for init_en.user
*/

INSERT INTO `user` VALUES (2,1,25,'admin','super','66d4aaa5ea177ac32c69946de3731ec0','yourmail@dot.com',NULL,'Management','admin');

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
Table data for init_en.user_group
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
Table data for init_en.user_in_group
*/

INSERT INTO `user_in_group` VALUES (9,25,28);

