<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
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
require_once(LIMB_DIR . '/class/db_tables/DbTableFactory.class.php');
require_once(LIMB_DIR . '/tests/lib/ProjectSiteObjectsLoader.class.php');

$site_objects = array();

echo "loading site objects...\n";

$loader = new ProjectSiteObjectsLoader();

if(!$site_objects = $loader->getSiteObjects())
{
  die("no site objects loaded");
}

$class_db_table = DbTableFactory :: create('SysClass');

foreach($site_objects as $object)
{
  $class_id = $object->getClassId();

  $class_properties = $object->getClassProperties();

  echo "updating " . get_class($object)  . "...\n";

  if(!isset($class_properties['icon']) ||  !$class_properties['icon'])
    $class_properties['icon'] = '/shared/images/generic.gif';

  $class_db_table->updateById($class_id, $class_properties);
}

echo 'done';

?>