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

class IniBasedRequestResolverMapper // implements RequestResolverMapper
{
  function & map(&$request)
  {
    $toolkit =& Limb :: toolkit();
    $uri =& $request->getUri();
    $path = $uri->getPath();

    $ini =& $toolkit->getIni('request_resolvers.ini');

    if (catch('LimbException', $e) || !is_object($ini))
      return null;

    $groups = $ini->getAll();

    foreach($groups as $key => $value)
    {
      if($key == 'default')
        continue;

      $regex = str_replace('\\*', '.*', preg_quote($value['path']));

      if(!preg_match('~^' . $regex . '$~', $path))
        continue;

      $handle = new LimbHandle($value['handle']);
      return Handle :: resolve($handle);
    }

    if($ini->hasOption('default_handle'))
    {
      $default = $ini->getOption('default_handle');
      $handle = new LimbHandle($default);
      return Handle :: resolve($handle);
    }
    else
      return null;
  }
}

?>