/* 
SQLyog v3.63
Host - 192.168.0.6 : Database - mebelgid
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
Table data for mebelgid.document
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
Table data for mebelgid.message
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
Table data for mebelgid.navigation_item
*/

INSERT INTO `navigation_item` VALUES 
(1,1,15,'Навигация','','navigation',0),
(3,1,37,'Администрирование','/root/admin','admin',0),
(4,1,38,'Структура сайта','/root/admin/site_structure','site_structure',0),
(5,1,39,'Навигация','/root/navigation','navigation',0),
(7,2,38,'Управление сайтом','/root/admin','site_management',0),
(8,2,39,'Управление контентом','/root/admin','content_management',0),
(19,2,37,'Администрирование','/root/admin','navigation',0),
(20,3,37,'Навигция администрирования','/root/admin','navigation',0),
(21,4,37,'Навигация администрирования','/root/admin','navigation',0),
(22,1,59,'Безопасность','/root/admin','security',0),
(23,2,45,'Группы пользователей','/root/user_groups','user_groups',0),
(24,2,44,'Пользователи','/root/users','users',0),
(26,2,42,'Доступ к объектам','/root/admin/objects_access','objects_access',0),
(27,1,60,'Общие','/root/admin','common',0),
(28,2,41,'Структура сайта','/root/admin/site_structure','site_structure',0),
(29,2,40,'Навигация(админ)','/root/admin/navigation','navigation',0),
(30,1,61,'Общие','/root/admin','common',0),
(31,1,62,'Медиа','/root/media','media',0),
(32,2,48,'Изображения','/root/media/images_folder','images',0),
(33,2,47,'Файлы','/root/media/files_folder','files',0),
(34,2,46,'Служебные сообщения','/root/messages','messages',0),
(35,3,43,'Типы объектов','/root/admin/controllers','classes',0),
(38,1,65,'JIP-управление','/root','jip',0),
(39,3,39,'Управление контентом','/root?action=admin_display','content_management',0),
(40,1,66,'Навигация','/root/navigation','navi',0),
(41,1,67,'Навигация','','navigation',0),
(43,1,70,'Управление кэшем','/root/admin/cache_manager','cache',0),
(44,1,71,'Конфигурация сайта','/root/admin/site_params','site_params',0);

/*
Table struture for sys_action_access
*/

drop table if exists `sys_action_access`;
CREATE TABLE `sys_action_access` (
  `id` int(11) NOT NULL auto_increment,
  `action_name` char(50) NOT NULL default '',
  `accessor_id` int(11) NOT NULL default '0',
  `accessor_type` tinyint(4) NOT NULL default '0',
  `controller_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `as` (`action_name`,`accessor_id`,`controller_id`),
  KEY `accessor_id` (`accessor_id`),
  KEY `accessor_type` (`accessor_type`)
) TYPE=InnoDB COMMENT='InnoDB free: 114688 kB; InnoDB free: 114688 kB; InnoDB free:';

/*
Table data for mebelgid.sys_action_access
*/

INSERT INTO `sys_action_access` VALUES 
(319,'activate_password',27,0,11),
(320,'activate_password',28,0,11),
(321,'change_own_password',27,0,12),
(322,'change_own_password',28,0,12),
(323,'generate_password',27,0,13),
(324,'generate_password',28,0,13),
(358,'admin_display',28,0,5),
(359,'set_group_access',28,0,5),
(360,'toggle',28,0,5),
(369,'display',27,0,18),
(370,'display',28,0,18),
(371,'edit',28,0,18),
(372,'delete',28,0,18),
(380,'display',27,0,19),
(381,'display',28,0,19),
(382,'edit',28,0,19),
(383,'edit_variations',28,0,19),
(384,'delete',28,0,19),
(386,'display',28,0,1),
(387,'admin_display',28,0,1),
(388,'create_document',28,0,1),
(389,'set_metadata',28,0,1),
(390,'edit',28,0,1),
(391,'display',27,0,1),
(408,'login',28,0,10),
(409,'logout',28,0,10),
(410,'edit',28,0,10),
(411,'change_user_locale',28,0,10),
(412,'login',27,0,10),
(413,'logout',27,0,10),
(417,'display',28,0,24),
(418,'edit',28,0,24),
(419,'display',27,0,24),
(420,'display',28,0,2),
(421,'admin_display',28,0,2),
(422,'register_new_object',28,0,2),
(427,'admin_display',28,0,17),
(428,'create_file',28,0,17),
(429,'create_files_folder',28,0,17),
(430,'edit',28,0,17),
(431,'delete',28,0,17),
(432,'file_select',28,0,17),
(433,'display',28,0,21),
(434,'edit',28,0,21),
(435,'admin_display',28,0,16),
(436,'create_image',28,0,16),
(437,'create_images_folder',28,0,16),
(438,'edit',28,0,16),
(439,'delete',28,0,16),
(440,'image_select',28,0,16),
(441,'display',28,0,20),
(442,'edit',28,0,20),
(443,'admin_display',28,0,15),
(444,'create_message',28,0,15),
(445,'edit',28,0,15),
(446,'delete',28,0,15),
(447,'admin_display',28,0,14),
(448,'create_navigation_item',28,0,14),
(449,'edit',28,0,14),
(450,'publish',28,0,14),
(451,'unpublish',28,0,14),
(452,'delete',28,0,14),
(453,'admin_display',28,0,23),
(454,'edit',28,0,23),
(455,'update',28,0,23),
(456,'admin_display',28,0,3),
(457,'toggle',28,0,3),
(458,'edit',28,0,3),
(459,'node_select',28,0,3),
(460,'save_priority',28,0,3),
(461,'multi_delete',28,0,3),
(462,'multi_toggle_publish_status',28,0,3),
(463,'admin_display',28,0,6),
(464,'create_user',28,0,6),
(465,'edit',28,0,6),
(466,'admin_display',28,0,8),
(467,'create_user_group',28,0,8),
(468,'admin_display',28,0,9),
(469,'edit',28,0,9),
(470,'delete',28,0,9),
(471,'admin_display',28,0,7),
(472,'edit',28,0,7),
(473,'set_membership',28,0,7),
(474,'change_password',28,0,7),
(475,'delete',28,0,7),
(481,'display',28,0,4),
(482,'admin_display',28,0,4),
(483,'set_group_access',28,0,4),
(484,'set_group_access_template',28,0,4),
(485,'display',28,0,25),
(486,'edit',28,0,25),
(487,'delete',28,0,25),
(488,'admin_display',28,0,26),
(489,'edit',28,0,26),
(490,'admin_display',28,0,27),
(491,'edit',28,0,27),
(492,'delete',28,0,27),
(495,'display',28,0,22),
(496,'edit',28,0,22),
(497,'delete',28,0,22),
(498,'display',28,0,28),
(499,'recover',28,0,28),
(500,'edit',28,0,28),
(501,'delete',28,0,28),
(508,'admin_display',28,0,29),
(509,'create_navigation_item',28,0,29),
(510,'edit',28,0,29),
(511,'publish',28,0,29),
(512,'unpublish',28,0,29),
(513,'delete',28,0,29);

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
Table data for mebelgid.sys_class
*/

INSERT INTO `sys_class` VALUES 
(1,'main_page','/shared/images/folder.gif',0,1),
(7,'user_object','/shared/images/generic.gif',1,0),
(9,'user_group','/shared/images/generic.gif',1,0),
(14,'navigation_item','/shared/images/generic.gif',1,1),
(15,'message','/shared/images/generic.gif',0,1),
(19,'file_object','/shared/images/generic.gif',1,0),
(20,'image_object','/shared/images/generic.gif',1,0),
(21,'site_object','/shared/images/generic.gif',1,1);

/*
Table struture for sys_controller
*/

drop table if exists `sys_controller`;
CREATE TABLE `sys_controller` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `name` (`name`)
) TYPE=InnoDB;

/*
Table data for mebelgid.sys_controller
*/

INSERT INTO `sys_controller` VALUES 
(14,'admin_navigation_item_controller'),
(2,'admin_page_controller'),
(31,'cache_manager_controller'),
(4,'controller_folder_controller'),
(25,'control_panel_controller'),
(17,'files_folder_controller'),
(18,'file_object_controller'),
(21,'file_select_controller'),
(16,'images_folder_controller'),
(19,'image_object_controller'),
(20,'image_select_controller'),
(10,'login_object_controller'),
(1,'main_page_controller'),
(27,'media_page_controller'),
(15,'message_controller'),
(29,'navigation_item_controller'),
(22,'node_select_controller'),
(24,'not_found_page_controller'),
(5,'objects_access_controller'),
(26,'simple_folder_controller'),
(23,'site_param_object_controller'),
(3,'site_structure_controller'),
(6,'users_folder_controller'),
(11,'user_activate_password_controller'),
(12,'user_change_own_password_controller'),
(7,'user_controller'),
(13,'user_generate_password_controller'),
(8,'user_groups_folder_controller'),
(9,'user_group_controller'),
(28,'version_controller');

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
Table data for mebelgid.sys_full_text_index
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
(129,'identifier',1,70,14,'cache'),
(78,'title',1,37,14,'навигация администрирования'),
(13,'title',1,38,14,'управление сайтом'),
(14,'identifier',1,38,14,'site_management'),
(94,'title',1,40,14,'навигация админ'),
(93,'identifier',1,40,14,'navigation'),
(92,'title',1,41,14,'структура сайта'),
(91,'identifier',1,41,14,'site_structure'),
(88,'title',1,42,14,'доступ к объектам'),
(87,'identifier',1,42,14,'objects_access'),
(106,'title',1,43,14,'типы объектов'),
(105,'identifier',1,43,14,'classes'),
(84,'title',1,44,14,'пользователи'),
(83,'identifier',1,44,14,'users'),
(82,'title',1,45,14,'группы пользователей'),
(81,'identifier',1,45,14,'user_groups'),
(104,'title',1,46,14,'служебные сообщения'),
(103,'identifier',1,46,14,'messages'),
(102,'title',1,47,14,'файлы'),
(101,'identifier',1,47,14,'files'),
(100,'title',1,48,14,'изображения'),
(99,'identifier',1,48,14,'images'),
(122,'title',1,39,14,'управление контентом'),
(121,'identifier',1,39,14,'content_management'),
(130,'title',1,70,14,'управление кэшем'),
(37,'title',50,34,15,'сообщения'),
(38,'identifier',50,34,15,'messages'),
(118,'title',50,35,21,'изображения'),
(117,'identifier',50,35,21,'images_folder'),
(114,'title',50,36,21,'файлы'),
(113,'identifier',50,36,21,'files_folder'),
(43,'title',50,20,2,'администрирование'),
(44,'identifier',50,20,2,'admin'),
(45,'title',50,21,3,'структура сайта'),
(46,'identifier',50,21,3,'site_structure'),
(128,'title',50,53,21,'конфигурирование сайта'),
(127,'identifier',50,53,21,'site_params'),
(71,'identifier',50,22,21,'controllers'),
(51,'title',50,23,5,'доступ к объектам'),
(52,'identifier',50,23,5,'objects_access'),
(116,'title',50,50,21,'выбор изображения'),
(115,'identifier',50,50,21,'image_select'),
(112,'title',50,51,21,'выбор файла'),
(111,'identifier',50,51,21,'file_select'),
(119,'identifier',1,65,14,'jip'),
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
(70,'title',50,55,15,'страница не найдена'),
(72,'title',50,22,21,'типы контроллеров'),
(77,'identifier',1,37,14,'navigation'),
(79,'identifier',1,59,14,'security'),
(80,'title',1,59,14,'безопасность'),
(89,'identifier',1,60,14,'common'),
(90,'title',1,60,14,'общие'),
(95,'identifier',1,61,14,'common'),
(96,'title',1,61,14,'общие'),
(97,'identifier',1,62,14,'media'),
(98,'title',1,62,14,'медиа'),
(120,'title',1,65,14,'jip управление'),
(123,'identifier',1,66,14,'navi'),
(124,'title',1,66,14,'навигация'),
(131,'identifier',1,71,14,'site_params'),
(132,'title',1,71,14,'конфигурация сайта');

/*
Table struture for sys_group_object_access_template
*/

drop table if exists `sys_group_object_access_template`;
CREATE TABLE `sys_group_object_access_template` (
  `id` int(11) NOT NULL auto_increment,
  `action_name` char(50) NOT NULL default '',
  `controller_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `action_name` (`action_name`),
  KEY `controller_id` (`controller_id`)
) TYPE=InnoDB COMMENT='InnoDB free: 114688 kB; InnoDB free: 114688 kB; InnoDB free:';

/*
Table data for mebelgid.sys_group_object_access_template
*/

INSERT INTO `sys_group_object_access_template` VALUES 
(3,'create_file',17),
(4,'create_files_folder',17),
(5,'create_image',16),
(6,'create_images_folder',16),
(7,'create_user',6),
(8,'create_document',1),
(9,'create_message',15),
(10,'create_navigation_item',14),
(11,'publish',14),
(12,'unpublish',14),
(16,'create_navigation_item',29),
(17,'publish',29),
(18,'unpublish',29);

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
Table data for mebelgid.sys_group_object_access_template_item
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
(14,8,28,1,1),
(15,9,28,1,1),
(16,9,27,1,0),
(17,10,28,1,1),
(18,11,28,1,1),
(19,11,27,1,0),
(20,12,28,1,1),
(26,16,28,1,1),
(27,17,28,1,1),
(28,17,27,1,0),
(29,18,28,1,1);

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
Table data for mebelgid.sys_object_access
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
(81,25,27,1,0,0),
(82,25,28,1,1,0),
(85,27,27,1,0,0),
(86,27,28,1,1,0),
(87,28,27,1,0,0),
(88,28,28,1,1,0),
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
(140,55,27,1,0,0),
(141,55,28,1,1,0),
(148,59,28,1,1,0),
(149,60,28,1,1,0),
(150,61,28,1,1,0),
(151,62,28,1,1,0),
(152,19,27,1,0,0),
(153,19,28,1,1,0),
(154,34,27,1,0,0),
(155,34,28,1,1,0),
(156,54,27,1,0,0),
(157,54,28,1,1,0),
(158,30,27,1,0,0),
(159,30,28,1,1,0),
(160,20,28,1,1,0),
(161,22,28,1,1,0),
(162,37,28,1,1,0),
(163,39,28,1,1,0),
(164,38,28,1,1,0),
(165,23,28,1,1,0),
(166,53,28,1,1,0),
(167,21,28,1,1,0),
(168,31,27,1,0,0),
(169,31,28,1,1,0),
(170,56,27,1,0,0),
(171,56,28,1,1,0),
(172,51,27,1,0,0),
(173,51,28,1,1,0),
(174,36,27,1,0,0),
(175,36,28,1,1,0),
(176,32,27,1,0,0),
(177,32,28,1,1,0),
(178,50,27,1,0,0),
(179,50,28,1,1,0),
(180,35,27,1,0,0),
(181,35,28,1,1,0),
(182,29,27,1,0,0),
(183,29,28,1,1,0),
(188,57,28,1,1,0),
(189,58,28,1,1,0),
(190,26,27,1,0,0),
(191,26,28,1,1,0),
(192,24,27,1,0,0),
(193,24,28,1,1,0),
(194,63,27,1,0,0),
(195,63,28,1,1,0),
(196,64,28,1,1,0),
(197,65,28,1,1,0),
(198,66,28,1,1,0),
(199,67,27,1,0,0),
(200,67,28,1,1,0),
(202,69,28,1,1,0),
(203,70,28,1,1,0),
(204,71,28,1,1,0);

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
Table data for mebelgid.sys_object_version
*/

INSERT INTO `sys_object_version` VALUES 
(6,25,0,1076762314,1076762314,1),
(7,27,0,1076762314,1076762314,1),
(8,28,0,1076762314,1076762314,1),
(10,34,0,1076762315,1076762315,1),
(11,37,25,1076770835,1076770835,1),
(12,38,25,1076770879,1076770879,1),
(13,39,25,1076771149,1076771149,1),
(15,38,25,1076771356,1076771356,2),
(16,39,25,1076771416,1076771416,2),
(27,19,25,1076762314,1076762314,1),
(28,55,25,1086440706,1086440706,1),
(29,37,25,1096891196,1096891196,2),
(30,37,25,1096891245,1096891245,3),
(31,37,25,1096891316,1096891316,4),
(32,59,25,1096891430,1096891430,1),
(33,45,25,1096891459,1096891459,2),
(34,44,25,1096891474,1096891474,2),
(36,42,25,1096891505,1096891505,2),
(37,60,25,1096891551,1096891551,1),
(38,41,25,1096891579,1096891579,2),
(39,40,25,1096891613,1096891613,2),
(40,61,25,1096891649,1096891649,1),
(41,62,25,1096891675,1096891675,1),
(42,48,25,1096891704,1096891704,2),
(43,47,25,1096891734,1096891734,2),
(44,46,25,1096891753,1096891753,2),
(45,43,25,1096891933,1096891933,3),
(48,65,25,1096892379,1096892379,1),
(49,39,25,1096892398,1096892398,3),
(50,66,25,1096892737,1096892737,1),
(51,67,25,1096893354,1096893354,1),
(53,70,25,1096893652,1096893652,1),
(54,71,25,1096893683,1096893683,1);

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
  `controller_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `idccv` (`class_id`,`id`,`current_version`,`locale_id`,`controller_id`),
  KEY `md` (`modified_date`),
  KEY `cd` (`created_date`),
  KEY `cid` (`creator_id`),
  KEY `current_version` (`current_version`)
) TYPE=InnoDB COMMENT='InnoDB free: 10240 kB; InnoDB free: 10240 kB; InnoDB free: 1';

/*
Table data for mebelgid.sys_site_object
*/

INSERT INTO `sys_site_object` VALUES 
(19,1,1,1076768404,0,1076762314,0,'ru','Главная','root',1),
(20,21,1,1076762314,0,1076762314,0,'ru','Администрирование','admin',2),
(21,21,2,1076769130,0,1076762314,0,'ru','Структура сайта','site_structure',3),
(22,21,2,1096890786,0,1076762314,0,'ru','Типы контроллеров','controllers',4),
(23,21,2,1076769145,0,1076762314,0,'ru','Доступ к объектам','objects_access',5),
(24,21,2,1076769160,0,1076762314,0,'ru','Пользователи','users',6),
(25,7,1,1076762588,0,1076762314,0,'ru','Администрирование','admin',7),
(26,21,2,1076769173,0,1076762314,0,'ru','Группы пользователей','user_groups',8),
(27,9,1,1076762314,0,1076762314,0,'ru','Посетители','visitors',9),
(28,9,1,1076762314,0,1076762314,0,'ru','Администраторы','admins',9),
(29,21,2,1076769188,0,1076762314,0,'ru','Авторизация','login',10),
(30,21,2,1076769202,0,1076762314,0,'ru','Активировать пароль','activate_password',11),
(31,21,2,1076769224,0,1076762314,0,'ru','Смена пароля','change_password',12),
(32,21,2,1076769246,0,1076762315,0,'ru','Забыли пароль?','generate_password',13),
(34,15,1,1076762315,0,1076762315,0,'ru','Сообщения','messages',15),
(35,21,1,1096892215,0,1076762315,0,'ru','Изображения','images_folder',16),
(36,21,1,1096892176,0,1076762315,0,'ru','Файлы','files_folder',17),
(37,14,4,1096891316,0,1076770835,25,'ru','Навигация администрирования','navigation',14),
(38,14,2,1076771356,0,1076770879,25,'ru','Управление сайтом','site_management',14),
(39,14,3,1096892398,0,1076771149,25,'ru','Управление контентом','content_management',14),
(40,14,2,1096891613,0,1076771604,25,'ru','Навигация(админ)','navigation',14),
(41,14,2,1096891579,0,1076772382,25,'ru','Структура сайта','site_structure',14),
(42,14,2,1096891505,0,1076772439,25,'ru','Доступ к объектам','objects_access',14),
(43,14,3,1096891933,0,1076772480,25,'ru','Типы объектов','classes',14),
(44,14,2,1096891474,0,1076772520,25,'ru','Пользователи','users',14),
(45,14,2,1096891459,0,1076772540,25,'ru','Группы пользователей','user_groups',14),
(46,14,2,1096891753,0,1076772578,25,'ru','Служебные сообщения','messages',14),
(47,14,2,1096891734,0,1076772601,25,'ru','Файлы','files',14),
(48,14,2,1096891704,0,1076772623,25,'ru','Изображения','images',14),
(50,21,1,1096892196,0,1079691597,25,'ru','Выбор изображения','image_select',20),
(51,21,1,1096892157,0,1079691620,25,'ru','Выбор файла','file_select',21),
(53,21,1,1096893619,0,1084270920,25,'ru','Конфигурирование сайта','site_params',23),
(54,21,1,1086440668,0,1086440668,25,'ru','Страница не найдена','404',24),
(55,15,1,1086440706,0,1086440706,25,'ru','Страница не найдена','404',15),
(56,21,1,1096890583,0,1096890583,25,'ru','Панель управления','cp',25),
(57,21,1,1096891063,0,1096891063,25,'ru','Инструменты','tools',26),
(58,21,1,1096891135,0,1096891135,25,'ru','Выбор элемента','node_select',22),
(59,14,1,1096891430,0,1096891430,25,'ru','Безопасность','security',14),
(60,14,1,1096891551,0,1096891551,25,'ru','Общие','common',14),
(61,14,1,1096891649,0,1096891649,25,'ru','Общие','common',14),
(62,14,1,1096891675,0,1096891675,25,'ru','Медиа','media',14),
(63,21,1,1096891901,0,1096891901,25,'ru','Медиа','media',27),
(64,21,1,1096892304,0,1096892304,25,'ru','Контроль версий','version',28),
(65,14,1,1096892379,0,1096892379,25,'ru','JIP-управление','jip',14),
(66,14,1,1096892737,0,1096892737,25,'ru','Навигация','navi',14),
(67,14,1,1096893354,0,1096893354,25,'ru','Навигация','navigation',29),
(69,21,1,1096893581,0,1096893581,25,'ru','Управление кэшем','cache_manager',31),
(70,14,1,1096893652,0,1096893652,25,'ru','Управление кэшем','cache',14),
(71,14,1,1096893683,0,1096893683,25,'ru','Конфигурация сайта','site_params',14);

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
Table data for mebelgid.sys_site_object_tree
*/

INSERT INTO `sys_site_object_tree` VALUES 
(1,1,0,0,1,'root',19,'/1/',13),
(2,1,1,0,2,'admin',20,'/1/2/',6),
(3,1,2,0,3,'site_structure',21,'/1/2/3/',0),
(4,1,2,0,3,'controllers',22,'/1/2/4/',0),
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
(16,1,1,0,2,'messages',34,'/1/16/',1),
(17,1,45,0,3,'images_folder',35,'/1/45/17/',0),
(18,1,45,0,3,'files_folder',36,'/1/45/18/',0),
(19,1,2,1,3,'navigation',37,'/1/2/19/',3),
(20,1,19,2,4,'site_management',38,'/1/2/19/20/',2),
(21,1,19,1,4,'content_management',39,'/1/2/19/21/',2),
(22,1,42,3,6,'navigation',40,'/1/2/19/20/42/22/',0),
(23,1,42,1,6,'site_structure',41,'/1/2/19/20/42/23/',0),
(24,1,41,2,6,'objects_access',42,'/1/2/19/20/41/24/',0),
(25,1,41,3,6,'classes',43,'/1/2/19/20/41/25/',0),
(26,1,41,4,6,'users',44,'/1/2/19/20/41/26/',0),
(27,1,41,5,6,'user_groups',45,'/1/2/19/20/41/27/',0),
(28,1,43,2,6,'messages',46,'/1/2/19/21/43/28/',0),
(29,1,44,8,6,'files',47,'/1/2/19/21/44/29/',0),
(30,1,44,7,6,'images',48,'/1/2/19/21/44/30/',0),
(32,1,45,0,3,'image_select',50,'/1/45/32/',0),
(33,1,45,0,3,'file_select',51,'/1/45/33/',0),
(35,1,2,0,3,'site_params',53,'/1/2/35/',0),
(36,1,1,0,2,'404',54,'/1/36/',0),
(37,1,16,0,3,'404',55,'/1/16/37/',0),
(38,1,1,0,2,'cp',56,'/1/38/',0),
(39,1,1,0,2,'tools',57,'/1/39/',2),
(40,1,39,0,3,'node_select',58,'/1/39/40/',0),
(41,1,20,2,5,'security',59,'/1/2/19/20/41/',4),
(42,1,20,1,5,'common',60,'/1/2/19/20/42/',4),
(43,1,21,0,5,'common',61,'/1/2/19/21/43/',2),
(44,1,21,0,5,'media',62,'/1/2/19/21/44/',2),
(45,1,1,0,2,'media',63,'/1/45/',4),
(46,1,39,0,3,'version',64,'/1/39/46/',0),
(47,1,19,0,4,'jip',65,'/1/2/19/47/',0),
(48,1,43,1,6,'navi',66,'/1/2/19/21/43/48/',0),
(49,1,1,0,2,'navigation',67,'/1/49/',0),
(50,1,2,0,3,'cache_manager',69,'/1/2/50/',0),
(51,1,42,2,6,'cache',70,'/1/2/19/20/42/51/',0),
(52,1,42,4,6,'site_params',71,'/1/2/19/20/42/52/',0);

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
Table data for mebelgid.sys_stat_counter
*/

INSERT INTO `sys_stat_counter` VALUES 
(1,1,394,1,394,1096894454);

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
Table data for mebelgid.sys_stat_day_counters
*/

INSERT INTO `sys_stat_day_counters` VALUES 
(1,1096833600,394,1,8,1);

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
Table data for mebelgid.sys_stat_ip
*/

INSERT INTO `sys_stat_ip` VALUES 
('c0a80005',1096888225);

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
Table data for mebelgid.sys_stat_uri
*/

INSERT INTO `sys_stat_uri` VALUES 
(1,'/root'),
(2,'/design/main/styles/main.css'),
(3,'/design/main/js/form_errors.js'),
(4,'/root/login'),
(5,'/root/admin/site_structure'),
(6,'/root/admin'),
(7,'/root/404'),
(8,'/root/admin/objects_access'),
(9,'/root/admin/classes'),
(10,'/root/admin/controllers'),
(11,'/root/navigation'),
(12,'/root/navigation/admin'),
(13,'/root/tools/node_select'),
(14,'/root/navigation/main'),
(15,'/root/cp'),
(16,'/root/admin/navigation'),
(17,'/root/admin/navigation/site_management'),
(18,'/root/admin/navigation/content_management'),
(19,'/root/admin/navigation/site_management/security'),
(20,'/root/media/files_folder'),
(21,'/root/media/images_folder'),
(22,'/root/messages'),
(23,'/root/admin/navigation/content_management/common'),
(24,'/root/admin/navigation/123'),
(25,'/root/admin/navigation/site_management/common');

/*
Table struture for sys_user_object_access_template
*/

drop table if exists `sys_user_object_access_template`;
CREATE TABLE `sys_user_object_access_template` (
  `id` int(11) NOT NULL auto_increment,
  `action_name` char(50) NOT NULL default '',
  `class_id` int(11) NOT NULL default '0',
  `controller_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `action_name` (`action_name`),
  KEY `controller_id` (`controller_id`)
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
Table data for mebelgid.user
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
Table data for mebelgid.user_group
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
Table data for mebelgid.user_in_group
*/

INSERT INTO `user_in_group` VALUES 
(1,25,28);

