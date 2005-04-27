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
class CompositeServiceRequestResolver
{
  var $resolvers;

  function & resolve(&$request)
  {
    foreach(array_keys($this->resolvers) as $key)
    {
      $resolver =& $this->resolvers[$key];
      $service =& $resolver->resolve($request);
      if(is_object($service) && ($service->getName() != '404'))
        return $service;
    }

    return new Service('404');
  }

  function addResolver(&$resolver)
  {
    $this->resolvers[] =& $resolver;
  }
}

?>