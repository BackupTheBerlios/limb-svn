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

class TestsActionFileResolver// implements FileResolver
{
  function resolve($class_path, $params = array())
  {
    if(file_exists(LIMB_DIR . '/core/actions/' . $class_path . '.class.php'))
      $full_path = LIMB_DIR . '/core/actions/' . $class_path . '.class.php';
    else
      return throw_error(new FileNotFoundException('action not found', $class_path));

    return $full_path;
  }
}

?>