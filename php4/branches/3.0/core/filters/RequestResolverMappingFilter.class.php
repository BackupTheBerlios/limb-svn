<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: InterceptingFilter.interface.php 981 2004-12-21 15:51:00Z pachanga $
*
***********************************************************************************/

class RequestResolverMappingFilter//implements InterceptingFilter
{
  var $mappers = array();

  function run(&$filter_chain, &$request, &$response)
  {
    $toolkit =& Limb :: toolkit();

    foreach(array_keys($this->mappers) as $key)
    {
      $mapper =& Handle :: resolve($this->mappers[$key]);
      if(is_object($resolver =& $mapper->map($request)))
        $toolkit->setRequestResolver($resolver);
    }

    if(!is_object($resolver =& $toolkit->getRequestResolver()))
    {
      include_once(LIMB_DIR . '/core/request_resolvers/NotFoundRequestResolver.class.php');
      $resolver404 = new NotFoundRequestResolver();
      $toolkit->setRequestResolver($resolver404);
    }

    $filter_chain->next();
  }

  function registerMapper(&$mapper)
  {
    $this->mappers[] =& $mapper;
  }
}

?>