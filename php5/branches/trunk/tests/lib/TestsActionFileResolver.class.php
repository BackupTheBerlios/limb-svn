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
require_once(LIMB_DIR . '/class/core/file_resolvers/file_resolver.interface.php');

class tests_action_file_resolver implements file_resolver
{
  function resolve($class_path, $params = array())
  {
    if(file_exists(LIMB_DIR . '/class/core/actions/' . $class_path . '.class.php'))
      $full_path = LIMB_DIR . '/class/core/actions/' . $class_path . '.class.php';
    else
      throw new FileNotFoundException('action not found', $class_path);

    return $full_path;
  }
}

?>