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
require_once(LIMB_DIR . '/tests/lib/SiteObjectsLoader.class.php');
require_once(LIMB_DIR . '/class/lib/system/Fs.class.php');

class AllSiteObjectsLoader extends SiteObjectsLoader
{
  function getClassesList()
  {
    $contents = array_merge(
      Fs :: ls(LIMB_DIR . '/class/core/site_objects/'),
      Fs :: ls(LIMB_APP_DIR . '/class/core/site_objects/')
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