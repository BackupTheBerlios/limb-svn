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

class AppTestsIniFileResolver// implements FileResolver
{
  function resolve($file_name, $params = array())
  {
    if (file_exists(LIMB_APP_DIR . '/tests/settings/' . $file_name))
      $dir = LIMB_APP_DIR . '/tests/settings/';
    elseif(file_exists(LIMB_APP_DIR . '/settings/' . $file_name))
      $dir = LIMB_APP_DIR . '/settings/';
    elseif (file_exists(LIMB_DIR . '/tests/settings/' . $file_name))
      $dir = LIMB_DIR . '/tests/settings/';
    elseif (file_exists(LIMB_DIR . '/settings/' . $file_name))
      $dir = LIMB_DIR . '/settings/';
    else
      return throw_error(new FileNotFoundException('ini file not found', $file_name));

    return $dir . $file_name;
  }
}

?>