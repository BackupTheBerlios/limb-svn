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
require_once(LIMB_DIR . '/tests/lib/site_objects_loader.class.php');
require_once(LIMB_DIR . '/core/lib/system/fs.class.php');

class all_site_objects_loader extends site_objects_loader
{
  function get_classes_list()
  {
    $contents = array_merge(
      fs :: ls(LIMB_DIR . '/core/model/site_objects/'),
      fs :: ls(PROJECT_DIR . '/core/model/site_objects/')
    );

    $classes_list = array();

    foreach($contents as $file_name)
    {
      if (substr($file_name, -10,  10) == '.class.php')
      {
        $classes_list[] = substr($file_name, 0, strpos($file_name, '.'));
      }
    }
    return $classes_list;
  }
}

?>