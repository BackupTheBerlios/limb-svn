<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
if(isset($argv[1]))
	$project_dir = $argv[1];
else
	die('project dir required');

require_once($project_dir . '/setup.php');
require_once(LIMB_DIR . '/core/lib/db/db_table_factory.class.php');
require_once(LIMB_DIR . '/core/controllers/site_object_controller.class.php');

$site_object_db_table = db_table_factory :: create('sys_site_object');
$class_db_table = db_table_factory :: create('sys_class');
$action_access_db_table = db_table_factory :: create('sys_action_access');
$group_object_access_template_db_table = db_table_factory :: create('sys_group_object_access_template');
$user_object_access_template_db_table = db_table_factory :: create('sys_user_object_access_template');

$classes = $class_db_table->get_list();

foreach($classes as $class)
{
	$class_id = $class['id'];

	$controller_name = $class['class_name'] . '_controller';

	$controller_id = site_object_controller :: get_id($controller_name);

	$conditions['class_id'] = $class_id;
	$row['controller_id'] = $controller_id;

	$site_object_db_table->update($row, $conditions);
	$action_access_db_table->update($row, $conditions);
	$group_object_access_template_db_table->update($row, $conditions);
	$user_object_access_template_db_table->update($row, $conditions);

	echo $class['class_name'] . " objects updated...\n";
}

echo 'done';

?>