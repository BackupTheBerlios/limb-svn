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
require_once(LIMB_DIR . '/class/lib/db/db_factory.class.php');
require_once(LIMB_DIR . '/class/core/site_objects/site_object_factory.class.php');

class site_objects_loader
{
  function & get_site_objects()
  {
    $site_objects = array();
    foreach($this->get_classes_list() as $class)
    {
      $site_objects[] = site_object_factory :: create($class);
    }

    return $site_objects;
  }

  function get_classes_list()
  {
    return array();
  }
}

?>