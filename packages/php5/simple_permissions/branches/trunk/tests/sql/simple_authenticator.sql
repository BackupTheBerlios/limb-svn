insert  into sys_site_object (id, class_id, current_version, modified_date, status, created_date, creator_id, locale_id, title, identifier) values (1, 1, 1, 0, 0, 0, 0, 'en', '', 'vasa')
insert  into sys_site_object (id, class_id, current_version, modified_date, status, created_date, creator_id, locale_id, title, identifier) values (3, 2, 1, 0, 0, 0, 0, 'en', '', 'visitors')
insert  into sys_site_object (id, class_id, current_version, modified_date, status, created_date, creator_id, locale_id, title, identifier) values (4, 2, 1, 0, 0, 0, 0, 'en', '', 'admins')

insert  into sys_site_object_tree (id, root_id, identifier, object_id) values (1, 1, 'root', 0)
insert  into sys_site_object_tree (id, root_id, identifier, object_id) values (2, 1, 'vasa', 1)
insert  into sys_site_object_tree (id, root_id, identifier, object_id) values (4, 1, 'visitors', 3)
insert  into sys_site_object_tree (id, root_id, identifier, object_id) values (5, 1, 'admins', 4)

insert  into user (id, version, object_id, name, lastname, password, email, generated_password, title ,identifier) values (10, 1, 1, 'Vasa', null, 'f35436994771530740cab9c2f738e57d', null, null, '', '')
insert  into user_group (id, version, object_id, title, identifier) values (12, 1, 3, 'Visitors', 'visitors')
insert  into user_group (id, version, object_id, title, identifier) values (13, 1, 4, 'Administrators', 'admins')
insert  into user_in_group (id, user_id, group_id) values (1, 1, 3)
insert  into user_in_group (id, user_id, group_id) values (2, 1, 4)