/*
Mascon Dump
Source Host:           192.168.0.6
Source Server Version: 4.0.12-nt-log
Source Database:       school
Date:                  2004.02.19 14:43:33
*/

#----------------------------
# Table structure for file_object
#----------------------------
drop table if exists file_object;
create table file_object (
   id bigint(20) not null auto_increment,
   description varchar(255),
   media_id varchar(32) not null,
   object_id int(11) not null default '0',
   `version` int(11) not null default '0',
   title varchar(50) not null,
   identifier varchar(50) not null,
   primary key (id),
   index `mid` (media_id),
   index oid (object_id),
   index v (`version`))
   type=InnoDB comment="InnoDB free: 7168 kB; InnoDB free: 114688 kB; InnoDB free: 1; InnoDB free: 1034240 kB";

#----------------------------
# No records for table file_object
#----------------------------

#----------------------------
# Table structure for image_object
#----------------------------
drop table if exists image_object;
create table image_object (
   id int(11) unsigned not null auto_increment,
   description text,
   object_id int(11) not null default '0',
   `version` int(11) not null default '0',
   title varchar(255) not null,
   identifier varchar(50) not null,
   primary key (id),
   index oid (object_id),
   index v (`version`))
   type=InnoDB comment="InnoDB free: 9216 kB; InnoDB free: 114688 kB; InnoDB free: 1; InnoDB free: 1034240 kB";

#----------------------------
# No records for table image_object
#----------------------------

#----------------------------
# Table structure for image_variation
#----------------------------
drop table if exists image_variation;
create table image_variation (
   id int(11) not null auto_increment,
   image_id int(11) unsigned not null default '0',
   media_id varchar(32) not null,
   width int(11) unsigned not null default '0',
   height int(11) unsigned not null default '0',
   variation varchar(50),
   primary key (id),
   index imid (image_id),
   index `mid` (media_id),
   index v (variation))
   type=InnoDB comment="InnoDB free: 9216 kB; InnoDB free: 114688 kB; InnoDB free: 1; InnoDB free: 1034240 kB";

#----------------------------
# No records for table image_variation
#----------------------------

#----------------------------
# Table structure for media
#----------------------------
drop table if exists media;
create table media (
   id varchar(32) not null,
   file_name varchar(255),
   mime_type varchar(100) not null,
   size int(10) unsigned,
   etag varchar(32),
   primary key (id),
   index id (id))
   type=InnoDB comment="InnoDB free: 9216 kB; InnoDB free: 114688 kB; InnoDB free: 1; InnoDB free: 1034240 kB";

#----------------------------
# No records for table media
#----------------------------

#----------------------------
# Table structure for message
#----------------------------
drop table if exists message;
create table message (
   id int(11) not null auto_increment,
   `version` int(11) not null default '0',
   object_id int(11) not null default '0',
   title varchar(255) not null,
   content text,
   identifier varchar(50) not null,
   primary key (id),
   index v (`version`),
   index o (object_id))
   type=InnoDB comment="InnoDB free: 114688 kB; InnoDB free: 114688 kB; InnoDB free:; InnoDB free: 1034240 kB";

#----------------------------
# Records for table message
#----------------------------


insert  into message values (1, 1, 16, 'Message', null, 'messages') ;
insert  into message values (2, 1, 34, 'Message', null, 'messages') ;
#----------------------------
# Table structure for navigation_item
#----------------------------
drop table if exists navigation_item;
create table navigation_item (
   id int(11) not null auto_increment,
   `version` int(11) not null default '0',
   object_id int(11) not null default '0',
   title varchar(100) not null,
   url varchar(255) not null,
   identifier varchar(50) not null,
   primary key (id),
   index v (`version`),
   index o (object_id))
   type=InnoDB comment="InnoDB free: 114688 kB; InnoDB free: 114688 kB; InnoDB free:; InnoDB free: 1034240 kB";

#----------------------------
# Records for table navigation_item
#----------------------------


insert  into navigation_item values (1, 1, 15, 'Навигация', '', 'navigation') ;
insert  into navigation_item values (2, 1, 33, 'Навигация', '', 'navigation') ;
insert  into navigation_item values (3, 1, 37, 'Администрирование', '/root/admin', 'admin') ;
insert  into navigation_item values (4, 1, 38, 'Структура сайта', '/root/admin/site_structure', 'site_structure') ;
insert  into navigation_item values (5, 1, 39, 'Навигация', '/root/navigation', 'navigation') ;
insert  into navigation_item values (6, 2, 33, 'Навигация', '/root/navigation', 'navigation') ;
insert  into navigation_item values (7, 2, 38, 'Управление сайтом', '/root/admin', 'site_management') ;
insert  into navigation_item values (8, 2, 39, 'Управление контентом', '/root/admin', 'content_management') ;
insert  into navigation_item values (9, 1, 40, 'Навигация', '/root/navigation', 'navigation') ;
insert  into navigation_item values (10, 1, 41, 'Структура сайта', '/root/admin/site_structure', 'site_structure') ;
insert  into navigation_item values (11, 1, 42, 'Доступ к объектам', '/root/admin/objects_access', 'objects_access') ;
insert  into navigation_item values (12, 1, 43, 'Типы объектов', '/root/admin/classes', 'classes') ;
insert  into navigation_item values (13, 1, 44, 'Пользователи', '/root/users', 'users') ;
insert  into navigation_item values (14, 1, 45, 'Группы пользователей', '/root/user_groups', 'user_groups') ;
insert  into navigation_item values (15, 1, 46, 'Служебные сообщения', '/root/messages', 'messages') ;
insert  into navigation_item values (16, 1, 47, 'Файлы', '/root/files_folder', 'files') ;
insert  into navigation_item values (17, 1, 48, 'Изображения', '/root/images_folder', 'images') ;
insert  into navigation_item values (18, 1, 49, 'Меню пользователя', '/root', 'main') ;
#----------------------------
# Table structure for sys_action_access
#----------------------------
drop table if exists sys_action_access;
create table sys_action_access (
   id int(11) not null auto_increment,
   class_id int(11) not null default '0',
   action_name char(50) not null,
   accessor_id int(11) not null default '0',
   accessor_type tinyint(4) not null default '0',
   primary key (id),
   index accessor_id (accessor_id),
   index accessor_type (accessor_type),
   index class_id (class_id))
   type=InnoDB comment="InnoDB free: 114688 kB; InnoDB free: 114688 kB; InnoDB free:; InnoDB free: 1034240 kB";

#----------------------------
# Records for table sys_action_access
#----------------------------


insert  into sys_action_access values (216, 17, 'display', 27, 0) ;
insert  into sys_action_access values (217, 17, 'file_select', 27, 0) ;
insert  into sys_action_access values (218, 17, 'display', 28, 0) ;
insert  into sys_action_access values (219, 17, 'create_file', 28, 0) ;
insert  into sys_action_access values (220, 17, 'create_files_folder', 28, 0) ;
insert  into sys_action_access values (221, 17, 'edit_files_folder', 28, 0) ;
insert  into sys_action_access values (222, 17, 'delete', 28, 0) ;
insert  into sys_action_access values (223, 17, 'file_select', 28, 0) ;
insert  into sys_action_access values (224, 16, 'display', 27, 0) ;
insert  into sys_action_access values (225, 16, 'image_select', 27, 0) ;
insert  into sys_action_access values (226, 16, 'display', 28, 0) ;
insert  into sys_action_access values (227, 16, 'create_image', 28, 0) ;
insert  into sys_action_access values (228, 16, 'create_images_folder', 28, 0) ;
insert  into sys_action_access values (229, 16, 'edit_images_folder', 28, 0) ;
insert  into sys_action_access values (230, 16, 'delete', 28, 0) ;
insert  into sys_action_access values (231, 16, 'image_select', 28, 0) ;
insert  into sys_action_access values (232, 1, 'display', 27, 0) ;
insert  into sys_action_access values (233, 1, 'display', 28, 0) ;
insert  into sys_action_access values (234, 1, 'admin_display', 28, 0) ;
insert  into sys_action_access values (235, 1, 'set_metadata', 28, 0) ;
insert  into sys_action_access values (236, 1, 'edit', 28, 0) ;
insert  into sys_action_access values (237, 15, 'display', 27, 0) ;
insert  into sys_action_access values (238, 15, 'display', 28, 0) ;
insert  into sys_action_access values (239, 15, 'create_message', 28, 0) ;
insert  into sys_action_access values (240, 15, 'edit', 28, 0) ;
insert  into sys_action_access values (241, 15, 'delete', 28, 0) ;
insert  into sys_action_access values (242, 14, 'display', 27, 0) ;
insert  into sys_action_access values (243, 14, 'display', 28, 0) ;
insert  into sys_action_access values (244, 14, 'create_navigation_item', 28, 0) ;
insert  into sys_action_access values (245, 14, 'edit', 28, 0) ;
insert  into sys_action_access values (246, 14, 'delete', 28, 0) ;
insert  into sys_action_access values (247, 14, 'order', 28, 0) ;
insert  into sys_action_access values (259, 9, 'display', 27, 0) ;
insert  into sys_action_access values (260, 9, 'display', 28, 0) ;
insert  into sys_action_access values (261, 9, 'edit', 28, 0) ;
insert  into sys_action_access values (262, 9, 'delete', 28, 0) ;
insert  into sys_action_access values (266, 7, 'display', 27, 0) ;
insert  into sys_action_access values (267, 7, 'display', 28, 0) ;
insert  into sys_action_access values (268, 7, 'edit', 28, 0) ;
insert  into sys_action_access values (269, 7, 'set_membership', 28, 0) ;
insert  into sys_action_access values (270, 7, 'change_password', 28, 0) ;
insert  into sys_action_access values (271, 7, 'delete', 28, 0) ;
insert  into sys_action_access values (276, 10, 'display', 27, 0) ;
insert  into sys_action_access values (277, 10, 'logout', 27, 0) ;
insert  into sys_action_access values (278, 10, 'display', 28, 0) ;
insert  into sys_action_access values (279, 10, 'logout', 28, 0) ;
insert  into sys_action_access values (280, 10, 'edit', 28, 0) ;
insert  into sys_action_access values (308, 3, 'display', 27, 0) ;
insert  into sys_action_access values (309, 3, 'display', 28, 0) ;
insert  into sys_action_access values (310, 3, 'toggle', 28, 0) ;
insert  into sys_action_access values (311, 3, 'order', 28, 0) ;
insert  into sys_action_access values (312, 5, 'display', 27, 0) ;
insert  into sys_action_access values (313, 5, 'display', 28, 0) ;
insert  into sys_action_access values (314, 5, 'set_group_access', 28, 0) ;
insert  into sys_action_access values (315, 5, 'toggle', 28, 0) ;
insert  into sys_action_access values (316, 6, 'display', 27, 0) ;
insert  into sys_action_access values (317, 6, 'display', 28, 0) ;
insert  into sys_action_access values (318, 6, 'create_user', 28, 0) ;
insert  into sys_action_access values (319, 11, 'activate_password', 27, 0) ;
insert  into sys_action_access values (320, 11, 'activate_password', 28, 0) ;
insert  into sys_action_access values (321, 12, 'change_own_password', 27, 0) ;
insert  into sys_action_access values (322, 12, 'change_own_password', 28, 0) ;
insert  into sys_action_access values (323, 13, 'generate_password', 27, 0) ;
insert  into sys_action_access values (324, 13, 'generate_password', 28, 0) ;
insert  into sys_action_access values (325, 8, 'display', 27, 0) ;
insert  into sys_action_access values (326, 8, 'display', 28, 0) ;
insert  into sys_action_access values (327, 8, 'create_user_group', 28, 0) ;
insert  into sys_action_access values (328, 4, 'display', 28, 0) ;
insert  into sys_action_access values (329, 4, 'set_group_access', 28, 0) ;
insert  into sys_action_access values (330, 4, 'set_group_access_template', 28, 0) ;
insert  into sys_action_access values (331, 2, 'display', 27, 0) ;
insert  into sys_action_access values (332, 2, 'display', 28, 0) ;
insert  into sys_action_access values (333, 2, 'admin_display', 28, 0) ;
#----------------------------
# Table structure for sys_class
#----------------------------
drop table if exists sys_class;
create table sys_class (
   id int(11) not null auto_increment,
   class_name varchar(50) not null,
   icon varchar(30) not null,
   class_ordr smallint(6) not null default '0',
   primary key (id),
   index class (class_name))
   type=InnoDB comment="InnoDB free: 10240 kB; InnoDB free: 10240 kB; InnoDB free: 1; InnoDB free: 1034240 kB";

#----------------------------
# Records for table sys_class
#----------------------------


insert  into sys_class values (1, 'main_page', '/shared/images/folder.gif', 0) ;
insert  into sys_class values (2, 'admin_page', '', 0) ;
insert  into sys_class values (3, 'site_structure', '', 1) ;
insert  into sys_class values (4, 'class_folder', '/shared/images/folder.gif', 0) ;
insert  into sys_class values (5, 'objects_access', '', 0) ;
insert  into sys_class values (6, 'users_folder', '/shared/images/folder.gif', 0) ;
insert  into sys_class values (7, 'user_object', '', 1) ;
insert  into sys_class values (8, 'user_groups_folder', '/shared/images/folder.gif', 0) ;
insert  into sys_class values (9, 'user_group', '', 1) ;
insert  into sys_class values (10, 'login_object', '', 0) ;
insert  into sys_class values (11, 'user_activate_password', '', 0) ;
insert  into sys_class values (12, 'user_change_password', '', 0) ;
insert  into sys_class values (13, 'user_generate_password', '', 0) ;
insert  into sys_class values (14, 'navigation_item', '', 1) ;
insert  into sys_class values (15, 'message', '', 0) ;
insert  into sys_class values (16, 'images_folder', '/shared/images/folder.gif', 0) ;
insert  into sys_class values (17, 'files_folder', '/shared/images/folder.gif', 0) ;
insert  into sys_class values (18, 'site_object', '/shared/images/generic.gif', 1) ;
#----------------------------
# Table structure for sys_full_text_index
#----------------------------
drop table if exists sys_full_text_index;
create table sys_full_text_index (
   id int(11) not null auto_increment,
   attribute varchar(50),
   weight tinyint(4) default '1',
   object_id int(11),
   class_id int(11),
   body text,
   primary key (id),
   index object_id (object_id, class_id),
   index body (body(1)))
   type=MyISAM;

#----------------------------
# No records for table sys_full_text_index
#----------------------------

#----------------------------
# Table structure for sys_group_object_access_template
#----------------------------
drop table if exists sys_group_object_access_template;
create table sys_group_object_access_template (
   id int(11) not null auto_increment,
   class_id int(11) not null default '0',
   action_name char(50) not null,
   primary key (id),
   index action_name (action_name),
   index class_id (class_id))
   type=InnoDB comment="InnoDB free: 114688 kB; InnoDB free: 114688 kB; InnoDB free:; InnoDB free: 1034240 kB";

#----------------------------
# Records for table sys_group_object_access_template
#----------------------------


insert  into sys_group_object_access_template values (1, 6, 'create_user') ;
#----------------------------
# Table structure for sys_group_object_access_template_item
#----------------------------
drop table if exists sys_group_object_access_template_item;
create table sys_group_object_access_template_item (
   id int(11) not null auto_increment,
   template_id int(11),
   group_id int(11),
   r tinyint(4),
   w tinyint(4),
   primary key (id),
   index template_id (template_id),
   index group_id (group_id))
   type=InnoDB comment="InnoDB free: 114688 kB; InnoDB free: 114688 kB; InnoDB free:; InnoDB free: 1034240 kB";

#----------------------------
# Records for table sys_group_object_access_template_item
#----------------------------


insert  into sys_group_object_access_template_item values (1, 1, 27, 1, 1) ;
insert  into sys_group_object_access_template_item values (2, 1, 28, 0, 1) ;
#----------------------------
# Table structure for sys_lock
#----------------------------
drop table if exists sys_lock;
create table sys_lock (
   lock_id char(32) not null,
   lock_table char(32) not null,
   lock_stamp int(11) not null default '0',
   primary key (lock_id, lock_table))
   type=InnoDB comment="Table locks for NestedSet; InnoDB free: 114688 kB; InnoDB fr; InnoDB free: 1034240 kB";

#----------------------------
# No records for table sys_lock
#----------------------------

#----------------------------
# Table structure for sys_metadata
#----------------------------
drop table if exists sys_metadata;
create table sys_metadata (
   id int(11) not null auto_increment,
   object_id int(11) not null default '0',
   keywords text,
   description text,
   primary key (id),
   index oid (object_id))
   type=InnoDB comment="InnoDB free: 10240 kB; InnoDB free: 114688 kB; InnoDB free: ; InnoDB free: 1034240 kB";

#----------------------------
# No records for table sys_metadata
#----------------------------

#----------------------------
# Table structure for sys_object_access
#----------------------------
drop table if exists sys_object_access;
create table sys_object_access (
   id int(11) not null auto_increment,
   object_id int(11) not null default '0',
   accessor_id int(11) not null default '0',
   r tinyint(4) not null default '0',
   w tinyint(4) not null default '0',
   accessor_type tinyint(4) not null default '0',
   primary key (id),
   index accessor_id (accessor_id),
   index ora (object_id, r, accessor_id),
   index accessor_type (accessor_type))
   type=InnoDB comment="InnoDB free: 114688 kB; InnoDB free: 114688 kB; InnoDB free:; InnoDB free: 1034240 kB";

#----------------------------
# Records for table sys_object_access
#----------------------------


insert  into sys_object_access values (1, 1, 9, 1, 1, 0) ;
insert  into sys_object_access values (2, 1, 10, 1, 1, 0) ;
insert  into sys_object_access values (3, 2, 9, 1, 1, 0) ;
insert  into sys_object_access values (4, 2, 10, 1, 1, 0) ;
insert  into sys_object_access values (5, 3, 9, 1, 1, 0) ;
insert  into sys_object_access values (6, 3, 10, 1, 1, 0) ;
insert  into sys_object_access values (7, 4, 9, 1, 1, 0) ;
insert  into sys_object_access values (8, 4, 10, 1, 1, 0) ;
insert  into sys_object_access values (9, 5, 9, 1, 1, 0) ;
insert  into sys_object_access values (10, 5, 10, 1, 1, 0) ;
insert  into sys_object_access values (11, 6, 9, 1, 1, 0) ;
insert  into sys_object_access values (12, 6, 10, 1, 1, 0) ;
insert  into sys_object_access values (13, 7, 9, 1, 1, 0) ;
insert  into sys_object_access values (14, 7, 10, 1, 1, 0) ;
insert  into sys_object_access values (15, 8, 9, 1, 1, 0) ;
insert  into sys_object_access values (16, 8, 10, 1, 1, 0) ;
insert  into sys_object_access values (17, 9, 9, 1, 1, 0) ;
insert  into sys_object_access values (18, 9, 10, 1, 1, 0) ;
insert  into sys_object_access values (19, 10, 9, 1, 1, 0) ;
insert  into sys_object_access values (20, 10, 10, 1, 1, 0) ;
insert  into sys_object_access values (21, 11, 9, 1, 1, 0) ;
insert  into sys_object_access values (22, 11, 10, 1, 1, 0) ;
insert  into sys_object_access values (23, 12, 9, 1, 1, 0) ;
insert  into sys_object_access values (24, 12, 10, 1, 1, 0) ;
insert  into sys_object_access values (25, 13, 9, 1, 1, 0) ;
insert  into sys_object_access values (26, 13, 10, 1, 1, 0) ;
insert  into sys_object_access values (27, 14, 9, 1, 1, 0) ;
insert  into sys_object_access values (28, 14, 10, 1, 1, 0) ;
insert  into sys_object_access values (29, 15, 9, 1, 1, 0) ;
insert  into sys_object_access values (30, 15, 10, 1, 1, 0) ;
insert  into sys_object_access values (31, 16, 9, 1, 1, 0) ;
insert  into sys_object_access values (32, 16, 10, 1, 1, 0) ;
insert  into sys_object_access values (33, 17, 9, 1, 1, 0) ;
insert  into sys_object_access values (34, 17, 10, 1, 1, 0) ;
insert  into sys_object_access values (35, 18, 9, 1, 1, 0) ;
insert  into sys_object_access values (36, 18, 10, 1, 1, 0) ;
insert  into sys_object_access values (73, 19, 27, 1, 0, 0) ;
insert  into sys_object_access values (74, 19, 28, 1, 1, 0) ;
insert  into sys_object_access values (75, 20, 28, 1, 1, 0) ;
insert  into sys_object_access values (76, 21, 28, 1, 1, 0) ;
insert  into sys_object_access values (77, 22, 28, 1, 1, 0) ;
insert  into sys_object_access values (78, 23, 28, 1, 1, 0) ;
insert  into sys_object_access values (79, 24, 27, 1, 0, 0) ;
insert  into sys_object_access values (80, 24, 28, 1, 1, 0) ;
insert  into sys_object_access values (81, 25, 27, 1, 0, 0) ;
insert  into sys_object_access values (82, 25, 28, 1, 1, 0) ;
insert  into sys_object_access values (83, 26, 27, 1, 0, 0) ;
insert  into sys_object_access values (84, 26, 28, 1, 1, 0) ;
insert  into sys_object_access values (85, 27, 27, 1, 0, 0) ;
insert  into sys_object_access values (86, 27, 28, 1, 1, 0) ;
insert  into sys_object_access values (87, 28, 27, 1, 0, 0) ;
insert  into sys_object_access values (88, 28, 28, 1, 1, 0) ;
insert  into sys_object_access values (89, 29, 27, 1, 0, 0) ;
insert  into sys_object_access values (90, 29, 28, 1, 1, 0) ;
insert  into sys_object_access values (91, 30, 27, 1, 0, 0) ;
insert  into sys_object_access values (92, 30, 28, 1, 1, 0) ;
insert  into sys_object_access values (93, 31, 27, 1, 0, 0) ;
insert  into sys_object_access values (94, 31, 28, 1, 1, 0) ;
insert  into sys_object_access values (95, 32, 27, 1, 0, 0) ;
insert  into sys_object_access values (96, 32, 28, 1, 1, 0) ;
insert  into sys_object_access values (97, 33, 27, 1, 0, 0) ;
insert  into sys_object_access values (98, 33, 28, 1, 1, 0) ;
insert  into sys_object_access values (99, 34, 27, 1, 0, 0) ;
insert  into sys_object_access values (100, 34, 28, 1, 1, 0) ;
insert  into sys_object_access values (101, 35, 27, 1, 0, 0) ;
insert  into sys_object_access values (102, 35, 28, 1, 1, 0) ;
insert  into sys_object_access values (103, 36, 27, 1, 0, 0) ;
insert  into sys_object_access values (104, 36, 28, 1, 1, 0) ;
insert  into sys_object_access values (105, 37, 27, 1, 0, 0) ;
insert  into sys_object_access values (106, 37, 28, 1, 1, 0) ;
insert  into sys_object_access values (107, 38, 27, 1, 0, 0) ;
insert  into sys_object_access values (108, 38, 28, 1, 1, 0) ;
insert  into sys_object_access values (109, 39, 27, 1, 0, 0) ;
insert  into sys_object_access values (110, 39, 28, 1, 1, 0) ;
insert  into sys_object_access values (111, 40, 27, 1, 0, 0) ;
insert  into sys_object_access values (112, 40, 28, 1, 1, 0) ;
insert  into sys_object_access values (113, 41, 27, 1, 0, 0) ;
insert  into sys_object_access values (114, 41, 28, 1, 1, 0) ;
insert  into sys_object_access values (115, 42, 27, 1, 0, 0) ;
insert  into sys_object_access values (116, 42, 28, 1, 1, 0) ;
insert  into sys_object_access values (117, 43, 27, 1, 0, 0) ;
insert  into sys_object_access values (118, 43, 28, 1, 1, 0) ;
insert  into sys_object_access values (119, 44, 27, 1, 0, 0) ;
insert  into sys_object_access values (120, 44, 28, 1, 1, 0) ;
insert  into sys_object_access values (121, 45, 27, 1, 0, 0) ;
insert  into sys_object_access values (122, 45, 28, 1, 1, 0) ;
insert  into sys_object_access values (123, 46, 27, 1, 0, 0) ;
insert  into sys_object_access values (124, 46, 28, 1, 1, 0) ;
insert  into sys_object_access values (125, 47, 27, 1, 0, 0) ;
insert  into sys_object_access values (126, 47, 28, 1, 1, 0) ;
insert  into sys_object_access values (127, 48, 27, 1, 0, 0) ;
insert  into sys_object_access values (128, 48, 28, 1, 1, 0) ;
insert  into sys_object_access values (129, 49, 27, 1, 0, 0) ;
insert  into sys_object_access values (130, 49, 28, 1, 1, 0) ;
#----------------------------
# Table structure for sys_object_version
#----------------------------
drop table if exists sys_object_version;
create table sys_object_version (
   id int(11) not null auto_increment,
   object_id int(11) not null default '0',
   creator_id int(11) not null default '0',
   modified_date int(11) not null default '0',
   created_date int(11) not null default '0',
   `version` int(11) not null default '0',
   primary key (id),
   index oid (object_id),
   index cid (creator_id),
   index `md` (modified_date),
   index cd (created_date),
   index v (`version`))
   type=InnoDB comment="InnoDB free: 10240 kB; InnoDB free: 114688 kB; InnoDB free: ; InnoDB free: 1034240 kB";

#----------------------------
# Records for table sys_object_version
#----------------------------


insert  into sys_object_version values (1, 7, 0, 1076755675, 1076755675, 1) ;
insert  into sys_object_version values (2, 9, 0, 1076755676, 1076755676, 1) ;
insert  into sys_object_version values (3, 10, 0, 1076755676, 1076755676, 1) ;
insert  into sys_object_version values (4, 15, 0, 1076755676, 1076755676, 1) ;
insert  into sys_object_version values (5, 16, 0, 1076755676, 1076755676, 1) ;
insert  into sys_object_version values (6, 25, 0, 1076762314, 1076762314, 1) ;
insert  into sys_object_version values (7, 27, 0, 1076762314, 1076762314, 1) ;
insert  into sys_object_version values (8, 28, 0, 1076762314, 1076762314, 1) ;
insert  into sys_object_version values (9, 33, 0, 1076762315, 1076762315, 1) ;
insert  into sys_object_version values (10, 34, 0, 1076762315, 1076762315, 1) ;
insert  into sys_object_version values (11, 37, 25, 1076770835, 1076770835, 1) ;
insert  into sys_object_version values (12, 38, 25, 1076770879, 1076770879, 1) ;
insert  into sys_object_version values (13, 39, 25, 1076771149, 1076771149, 1) ;
insert  into sys_object_version values (14, 33, 25, 1076771224, 1076771224, 2) ;
insert  into sys_object_version values (15, 38, 25, 1076771356, 1076771356, 2) ;
insert  into sys_object_version values (16, 39, 25, 1076771416, 1076771416, 2) ;
insert  into sys_object_version values (17, 40, 25, 1076771605, 1076771605, 1) ;
insert  into sys_object_version values (18, 41, 25, 1076772382, 1076772382, 1) ;
insert  into sys_object_version values (19, 42, 25, 1076772439, 1076772439, 1) ;
insert  into sys_object_version values (20, 43, 25, 1076772480, 1076772480, 1) ;
insert  into sys_object_version values (21, 44, 25, 1076772520, 1076772520, 1) ;
insert  into sys_object_version values (22, 45, 25, 1076772541, 1076772541, 1) ;
insert  into sys_object_version values (23, 46, 25, 1076772578, 1076772578, 1) ;
insert  into sys_object_version values (24, 47, 25, 1076772601, 1076772601, 1) ;
insert  into sys_object_version values (25, 48, 25, 1076772623, 1076772623, 1) ;
insert  into sys_object_version values (26, 49, 25, 1076772668, 1076772668, 1) ;
#----------------------------
# Table structure for sys_param
#----------------------------
drop table if exists sys_param;
create table sys_param (
   id int(20) unsigned not null auto_increment,
   identifier varchar(50) not null,
   `type` varchar(10) not null,
   int_value int(11),
   float_value double(20,10),
   char_value varchar(255),
   blob_value longblob,
   primary key (id),
   unique id_u (identifier))
   type=InnoDB comment="InnoDB free: 114688 kB; InnoDB free: 114688 kB; InnoDB free:; InnoDB free: 1034240 kB";

#----------------------------
# No records for table sys_param
#----------------------------

#----------------------------
# Table structure for sys_session
#----------------------------
drop table if exists sys_session;
create table sys_session (
   session_id varchar(50) not null,
   session_data blob not null,
   last_activity_time bigint(11) unsigned,
   user_id bigint(20),
   primary key (session_id),
   index user_id (user_id))
   type=InnoDB comment="InnoDB free: 10240 kB; InnoDB free: 114688 kB; InnoDB free: ; InnoDB free: 1034240 kB";

#----------------------------
# Records for table sys_session
#----------------------------


insert  into sys_session values ('8478ecc9584bf2eb4f0ee0c42d308a42', 0x747265655F657870616E6465645F706172656E74737C613A373A7B693A313B613A343A7B733A313A226C223B693A313B733A313A2272223B693A36323B733A373A22726F6F745F6964223B693A313B733A363A22737461747573223B623A313B7D693A323B613A343A7B733A313A226C223B693A323B733A313A2272223B693A393B733A373A22726F6F745F6964223B693A313B733A363A22737461747573223B623A303B7D693A383B613A343A7B733A313A226C223B693A31303B733A313A2272223B693A31353B733A373A22726F6F745F6964223B693A313B733A363A22737461747573223B623A303B7D693A363B613A343A7B733A313A226C223B693A31363B733A313A2272223B693A31393B733A373A22726F6F745F6964223B693A313B733A363A22737461747573223B623A303B7D693A31353B613A343A7B733A313A226C223B693A32383B733A313A2272223B693A35353B733A373A22726F6F745F6964223B693A313B733A363A22737461747573223B623A303B7D693A31393B613A343A7B733A313A226C223B693A32393B733A313A2272223B693A35323B733A373A22726F6F745F6964223B693A313B733A363A22737461747573223B623A303B7D693A32303B613A343A7B733A313A226C223B693A33303B733A313A2272223B693A34393B733A373A22726F6F745F6964223B693A313B733A363A22737461747573223B623A303B7D7D6C6F676765645F696E5F757365725F646174617C613A393A7B733A31323A2269735F6C6F676765645F696E223B623A313B733A323A226964223B733A323A223235223B733A373A226E6F64655F6964223B733A313A2237223B733A353A226C6F67696E223B733A353A2261646D696E223B733A353A22656D61696C223B733A31353A226D696B65406F66666963652E626974223B733A343A226E616D65223B733A353A2261646D696E223B733A383A226C6173746E616D65223B733A353A227375706572223B733A383A2270617373776F7264223B733A33323A223636643461616135656131373761633332633639393436646533373331656330223B733A363A2267726F757073223B613A313A7B693A32383B733A363A2261646D696E73223B7D7D737472696E67737C733A303A22223B, 1077190936, 25) ;
#----------------------------
# Table structure for sys_site_object
#----------------------------
drop table if exists sys_site_object;
create table sys_site_object (
   id int(11) not null auto_increment,
   class_id int(11) not null default '0',
   current_version int(11),
   modified_date int(11) not null default '0',
   `status` int(11) default '0',
   created_date int(11) not null default '0',
   creator_id int(11) not null default '0',
   locale_id char(2) not null default 'en',
   title varchar(255) not null,
   identifier varchar(255) not null,
   primary key (id),
   unique idccv (id, locale_id, current_version, class_id),
   index `md` (modified_date),
   index cd (created_date),
   index cid (creator_id),
   index current_version (current_version))
   type=InnoDB comment="InnoDB free: 10240 kB; InnoDB free: 10240 kB; InnoDB free: 1; InnoDB free: 1034240 kB";

#----------------------------
# Records for table sys_site_object
#----------------------------


insert  into sys_site_object values (1, 1, 1, 1076755675, 0, 1076755675, 0, 'ru', 'Главная', 'root') ;
insert  into sys_site_object values (2, 2, 1, 1076755675, 0, 1076755675, 0, 'ru', 'Администрирование', 'admin') ;
insert  into sys_site_object values (3, 3, 1, 1076755675, 0, 1076755675, 0, 'ru', 'Структура сайта', 'site_structure') ;
insert  into sys_site_object values (4, 4, 1, 1076755675, 0, 1076755675, 0, 'ru', 'Типы объектов', 'classes') ;
insert  into sys_site_object values (5, 5, 1, 1076755675, 0, 1076755675, 0, 'ru', 'Доступ к объектам', 'objects_access') ;
insert  into sys_site_object values (6, 6, 1, 1076755675, 0, 1076755675, 0, 'ru', 'Пользователи', 'users') ;
insert  into sys_site_object values (7, 7, 1, 1076755675, 0, 1076755675, 0, 'ru', 'Администрирование', 'admin') ;
insert  into sys_site_object values (8, 8, 1, 1076755675, 0, 1076755675, 0, 'ru', 'Группы пользователей', 'user_groups') ;
insert  into sys_site_object values (11, 10, 1, 1076755676, 0, 1076755676, 0, 'ru', 'Авторизация', 'login') ;
insert  into sys_site_object values (12, 11, 1, 1076755676, 0, 1076755676, 0, 'ru', 'Активизация пароля', 'activate_password') ;
insert  into sys_site_object values (13, 12, 1, 1076755676, 0, 1076755676, 0, 'ru', 'Смена пароля', 'change_password') ;
insert  into sys_site_object values (14, 13, 1, 1076755676, 0, 1076755676, 0, 'ru', 'Генерация пароля', 'generate_password') ;
insert  into sys_site_object values (15, 14, 1, 1076755676, 0, 1076755676, 0, 'ru', 'Навигация', 'navigation') ;
insert  into sys_site_object values (16, 15, 1, 1076755676, 0, 1076755676, 0, 'ru', 'Сообщения', 'messages') ;
insert  into sys_site_object values (17, 16, 1, 1076755676, 0, 1076755676, 0, 'ru', 'Изображения', 'images_folder') ;
insert  into sys_site_object values (18, 17, 1, 1076755676, 0, 1076755676, 0, 'ru', 'Файлы', 'files_folder') ;
insert  into sys_site_object values (19, 1, 3, 1076768404, 0, 1076762314, 0, 'ru', 'Главная', 'root') ;
insert  into sys_site_object values (20, 2, 1, 1076762314, 0, 1076762314, 0, 'ru', 'Администрирование', 'admin') ;
insert  into sys_site_object values (21, 3, 2, 1076769130, 0, 1076762314, 0, 'ru', 'Структура сайта', 'site_structure') ;
insert  into sys_site_object values (22, 4, 2, 1076769267, 0, 1076762314, 0, 'ru', 'Классы объектов', 'classes') ;
insert  into sys_site_object values (23, 5, 2, 1076769145, 0, 1076762314, 0, 'ru', 'Доступ к объектам', 'objects_access') ;
insert  into sys_site_object values (24, 6, 2, 1076769160, 0, 1076762314, 0, 'ru', 'Пользователи', 'users') ;
insert  into sys_site_object values (25, 7, 1, 1076762588, 0, 1076762314, 0, 'ru', 'Администрирование', 'admin') ;
insert  into sys_site_object values (26, 8, 2, 1076769173, 0, 1076762314, 0, 'ru', 'Группы пользователей', 'user_groups') ;
insert  into sys_site_object values (27, 9, 1, 1076762314, 0, 1076762314, 0, 'ru', 'Посетители', 'visitors') ;
insert  into sys_site_object values (28, 9, 1, 1076762314, 0, 1076762314, 0, 'ru', 'Администраторы', 'admins') ;
insert  into sys_site_object values (29, 10, 2, 1076769188, 0, 1076762314, 0, 'ru', 'Авторизация', 'login') ;
insert  into sys_site_object values (30, 11, 2, 1076769202, 0, 1076762314, 0, 'ru', 'Активировать пароль', 'activate_password') ;
insert  into sys_site_object values (31, 12, 2, 1076769224, 0, 1076762314, 0, 'ru', 'Смена пароля', 'change_password') ;
insert  into sys_site_object values (32, 13, 2, 1076769246, 0, 1076762315, 0, 'ru', 'Забыли пароль?', 'generate_password') ;
insert  into sys_site_object values (33, 14, 2, 1076771224, 0, 1076762315, 0, 'ru', 'Навигация', 'navigation') ;
insert  into sys_site_object values (34, 15, 1, 1076762315, 0, 1076762315, 0, 'ru', 'Сообщения', 'messages') ;
insert  into sys_site_object values (35, 16, 1, 1076762315, 0, 1076762315, 0, 'ru', 'Изображения', 'images_folder') ;
insert  into sys_site_object values (36, 17, 1, 1076762315, 0, 1076762315, 0, 'ru', 'Файлы', 'files_folder') ;
insert  into sys_site_object values (37, 14, 1, 1076770835, 0, 1076770835, 25, 'ru', 'Администрирование', 'admin') ;
insert  into sys_site_object values (38, 14, 2, 1076771356, 0, 1076770879, 25, 'ru', 'Управление сайтом', 'site_management') ;
insert  into sys_site_object values (39, 14, 2, 1076771416, 0, 1076771149, 25, 'ru', 'Управление контентом', 'content_management') ;
insert  into sys_site_object values (40, 14, 1, 1076771604, 0, 1076771604, 25, 'ru', 'Навигация', 'navigation') ;
insert  into sys_site_object values (41, 14, 1, 1076772382, 0, 1076772382, 25, 'ru', 'Структура сайта', 'site_structure') ;
insert  into sys_site_object values (42, 14, 1, 1076772439, 0, 1076772439, 25, 'ru', 'Доступ к объектам', 'objects_access') ;
insert  into sys_site_object values (43, 14, 1, 1076772480, 0, 1076772480, 25, 'ru', 'Типы объектов', 'classes') ;
insert  into sys_site_object values (44, 14, 1, 1076772520, 0, 1076772520, 25, 'ru', 'Пользователи', 'users') ;
insert  into sys_site_object values (45, 14, 1, 1076772540, 0, 1076772540, 25, 'ru', 'Группы пользователей', 'user_groups') ;
insert  into sys_site_object values (46, 14, 1, 1076772578, 0, 1076772578, 25, 'ru', 'Служебные сообщения', 'messages') ;
insert  into sys_site_object values (47, 14, 1, 1076772601, 0, 1076772601, 25, 'ru', 'Файлы', 'files') ;
insert  into sys_site_object values (48, 14, 1, 1076772623, 0, 1076772623, 25, 'ru', 'Изображения', 'images') ;
insert  into sys_site_object values (49, 14, 1, 1076772668, 0, 1076772668, 25, 'ru', 'Меню пользователя', 'main') ;
#----------------------------
# Table structure for sys_site_object_tree
#----------------------------
drop table if exists sys_site_object_tree;
create table sys_site_object_tree (
   id int(11) not null auto_increment,
   root_id int(11) not null default '0',
   l int(11) not null default '0',
   r int(11) not null default '0',
   parent_id int(11) not null default '0',
   ordr int(11) not null default '0',
   level int(11) not null default '0',
   identifier char(128) not null,
   object_id int(11) not null default '0',
   primary key (id),
   index root_id (root_id),
   index identifier (identifier),
   index l (l),
   index r (r),
   index level (level),
   index rlr (root_id, l, r),
   index parent_id (parent_id),
   index object_id (object_id))
   type=InnoDB comment="InnoDB free: 114688 kB; InnoDB free: 114688 kB; InnoDB free:; InnoDB free: 1034240 kB";

#----------------------------
# Records for table sys_site_object_tree
#----------------------------


insert  into sys_site_object_tree values (1, 1, 1, 62, 0, 1, 1, 'root', 19) ;
insert  into sys_site_object_tree values (2, 1, 2, 9, 1, 1, 2, 'admin', 20) ;
insert  into sys_site_object_tree values (3, 1, 3, 4, 2, 1, 3, 'site_structure', 21) ;
insert  into sys_site_object_tree values (4, 1, 5, 6, 2, 2, 3, 'classes', 22) ;
insert  into sys_site_object_tree values (5, 1, 7, 8, 2, 3, 3, 'objects_access', 23) ;
insert  into sys_site_object_tree values (6, 1, 16, 19, 1, 3, 2, 'users', 24) ;
insert  into sys_site_object_tree values (7, 1, 17, 18, 6, 1, 3, 'admin', 25) ;
insert  into sys_site_object_tree values (8, 1, 10, 15, 1, 2, 2, 'user_groups', 26) ;
insert  into sys_site_object_tree values (9, 1, 11, 12, 8, 1, 3, 'visitors', 27) ;
insert  into sys_site_object_tree values (10, 1, 13, 14, 8, 2, 3, 'admins', 28) ;
insert  into sys_site_object_tree values (11, 1, 20, 21, 1, 4, 2, 'login', 29) ;
insert  into sys_site_object_tree values (12, 1, 22, 23, 1, 5, 2, 'activate_password', 30) ;
insert  into sys_site_object_tree values (13, 1, 24, 25, 1, 6, 2, 'change_password', 31) ;
insert  into sys_site_object_tree values (14, 1, 26, 27, 1, 7, 2, 'generate_password', 32) ;
insert  into sys_site_object_tree values (15, 1, 28, 55, 1, 8, 2, 'navigation', 33) ;
insert  into sys_site_object_tree values (16, 1, 56, 57, 1, 9, 2, 'messages', 34) ;
insert  into sys_site_object_tree values (17, 1, 58, 59, 1, 10, 2, 'images_folder', 35) ;
insert  into sys_site_object_tree values (18, 1, 60, 61, 1, 11, 2, 'files_folder', 36) ;
insert  into sys_site_object_tree values (19, 1, 29, 52, 15, 1, 3, 'admin', 37) ;
insert  into sys_site_object_tree values (20, 1, 30, 49, 19, 1, 4, 'site_management', 38) ;
insert  into sys_site_object_tree values (21, 1, 50, 51, 19, 2, 4, 'content_management', 39) ;
insert  into sys_site_object_tree values (22, 1, 31, 32, 20, 1, 5, 'navigation', 40) ;
insert  into sys_site_object_tree values (23, 1, 33, 34, 20, 2, 5, 'site_structure', 41) ;
insert  into sys_site_object_tree values (24, 1, 35, 36, 20, 3, 5, 'objects_access', 42) ;
insert  into sys_site_object_tree values (25, 1, 37, 38, 20, 4, 5, 'classes', 43) ;
insert  into sys_site_object_tree values (26, 1, 39, 40, 20, 5, 5, 'users', 44) ;
insert  into sys_site_object_tree values (27, 1, 41, 42, 20, 6, 5, 'user_groups', 45) ;
insert  into sys_site_object_tree values (28, 1, 43, 44, 20, 7, 5, 'messages', 46) ;
insert  into sys_site_object_tree values (29, 1, 45, 46, 20, 8, 5, 'files', 47) ;
insert  into sys_site_object_tree values (30, 1, 47, 48, 20, 9, 5, 'images', 48) ;
insert  into sys_site_object_tree values (31, 1, 53, 54, 15, 2, 3, 'main', 49) ;
#----------------------------
# Table structure for sys_user_object_access_template
#----------------------------
drop table if exists sys_user_object_access_template;
create table sys_user_object_access_template (
   id int(11) not null auto_increment,
   action_name char(50) not null,
   class_id int(11) not null default '0',
   primary key (id),
   index action_name (action_name),
   index class_id (class_id))
   type=InnoDB comment="InnoDB free: 114688 kB; InnoDB free: 114688 kB; InnoDB free:; InnoDB free: 1034240 kB";

#----------------------------
# No records for table sys_user_object_access_template
#----------------------------

#----------------------------
# Table structure for sys_user_object_access_template_item
#----------------------------
drop table if exists sys_user_object_access_template_item;
create table sys_user_object_access_template_item (
   id int(11) not null auto_increment,
   template_id int(11),
   user_id int(11),
   r tinyint(4),
   w tinyint(4),
   primary key (id),
   index template_id (template_id),
   index user_id (user_id))
   type=InnoDB comment="InnoDB free: 114688 kB; InnoDB free: 114688 kB; InnoDB free:; InnoDB free: 1034240 kB";

#----------------------------
# No records for table sys_user_object_access_template_item
#----------------------------

#----------------------------
# Table structure for user
#----------------------------
drop table if exists `user`;
create table `user` (
   id int(11) not null auto_increment,
   `version` int(11) not null default '0',
   object_id int(11) not null default '0',
   name varchar(100),
   lastname varchar(100),
   `password` varchar(50) not null,
   email varchar(50),
   generated_password varchar(50),
   title varchar(50) not null,
   identifier varchar(50) not null,
   primary key (id),
   index pwd (`password`),
   index gpwd (generated_password),
   index v (`version`),
   index oid (object_id))
   type=InnoDB comment="InnoDB free: 10240 kB; InnoDB free: 114688 kB; InnoDB free: ; InnoDB free: 1034240 kB";

#----------------------------
# Records for table user
#----------------------------


insert  into `user` values (1, 1, 7, null, 'super', '', null, null, '', 'admin') ;
insert  into `user` values (2, 1, 25, 'admin', 'super', '66d4aaa5ea177ac32c69946de3731ec0', 'mike@office.bit', null, '', 'admin') ;
#----------------------------
# Table structure for user_group
#----------------------------
drop table if exists user_group;
create table user_group (
   id int(11) not null auto_increment,
   `version` int(11) not null default '0',
   object_id int(11) not null default '0',
   title varchar(50) not null,
   identifier varchar(50) not null,
   primary key (id),
   index v (`version`),
   index oid (object_id))
   type=InnoDB comment="InnoDB free: 10240 kB; InnoDB free: 114688 kB; InnoDB free: ; InnoDB free: 1034240 kB";

#----------------------------
# Records for table user_group
#----------------------------


insert  into user_group values (3, 1, 27, 'Посетители', 'visitors') ;
insert  into user_group values (4, 1, 28, 'Администраторы', 'admins') ;
#----------------------------
# Table structure for user_in_group
#----------------------------
drop table if exists user_in_group;
create table user_in_group (
   id int(11) not null auto_increment,
   user_id int(11) not null default '0',
   group_id int(11) not null default '0',
   primary key (id),
   index group_id (group_id),
   index user_id (user_id))
   type=InnoDB comment="InnoDB free: 10240 kB; InnoDB free: 114688 kB; InnoDB free: ; InnoDB free: 1034240 kB";

#----------------------------
# Records for table user_in_group
#----------------------------


insert  into user_in_group values (1, 25, 28) ;
