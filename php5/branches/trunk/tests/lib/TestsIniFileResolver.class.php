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

class tests_ini_file_resolver implements file_resolver
{
  function resolve($file_name, $params = array())
  {
    if (file_exists(LIMB_DIR . 'tests/settings/' . $file_name))
      $dir = LIMB_DIR . 'tests/settings/';
    elseif (file_exists(LIMB_DIR . '/settings/' . $file_name))
      $dir = LIMB_DIR . '/settings/';
    else
      throw new FileNotFoundException('ini file not found', $file_name);

    return $dir . $file_name;
  }
}

?>