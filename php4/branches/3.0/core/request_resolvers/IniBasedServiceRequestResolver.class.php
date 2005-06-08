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
require_once(LIMB_DIR . '/core/services/Service.class.php');

class IniBasedServiceRequestResolver
{
  function & resolve(&$request)
  {
    $toolkit =& Limb :: toolkit();
    $uri =& $request->getUri();
    $path = $uri->getPath();

    $ini =& $toolkit->getIni('services.ini');
    if (!is_object($ini))
      return null;

    $groups = $ini->getAll();

    foreach($groups as $key => $value)
    {
      if($key == 'default')
        continue;

      $regex = str_replace('\\*', '.*', preg_quote($value['path']));

      if(!preg_match('~^' . $regex . '$~', $path))
        continue;

      return new Service($value['service_name']);
    }

    return new Service('404');
  }
}

?>