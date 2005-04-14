<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: Service.class.php 1191 2005-03-25 14:04:13Z seregalimb $
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/services/Service.class.php');
require_once(LIMB_DIR . '/core/entity/Entity.class.php');

class IniBasedRequestResolver // implements RequestResolver
{
  function & getRequestedService(&$request)
  {
    $toolkit =& Limb :: toolkit();
    $uri =& $request->getUri();
    $path = $uri->getPath();

    $ini =& $toolkit->getIni('services.ini');
    if (catch('LimbException', $e) || !is_object($ini))
      return new Service('404');

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

  function getRequestedAction(&$request)
  {
    if($action = $request->get('action'))
      return $action;

    return false;
  }

  function & getRequestedEntity(&$request)
  {
    return new Entity();
  }
}

?>