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
require_once(LIMB_DIR . '/tests/lib/site_objects_loader.class.php');
require_once(LIMB_DIR . '/class/lib/system/fs.class.php');

class all_site_objects_loader extends site_objects_loader
{	
	function get_classes_list()
	{
	  $contents = array_merge(
  	  fs :: ls(LIMB_DIR . '/class/model/site_objects/'),
  	  fs :: ls(LIMB_APP_DIR . '/class/model/site_objects/')
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