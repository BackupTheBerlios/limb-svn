<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
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
require_once(LIMB_DIR . '/tests/lib/project_site_objects_loader.class.php');

$site_objects = array();

echo "loading site objects...\n";

$loader = new project_site_objects_loader();

if(!$site_objects = $loader->get_site_objects())
{
  die("no site objects loaded");
}

$class_db_table =& db_table_factory :: instance('sys_class');

foreach($site_objects as $object)
{
  $class_id = $object->get_class_id();

  $class_properties = $object->get_class_properties();

  echo "updating " . get_class($object)  . "...\n";

  if(!isset($class_properties['icon']) || !$class_properties['icon'])
    $class_properties['icon'] = '/shared/images/generic.gif';

  $class_db_table->update_by_id($class_id, $class_properties);
}

echo 'done';

?>