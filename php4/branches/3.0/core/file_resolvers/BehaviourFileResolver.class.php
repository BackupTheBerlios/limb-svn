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
require_once(LIMB_DIR . '/core/file_resolvers/FileResolverDecorator.class.php');

class BehaviourFileResolver extends FileResolverDecorator
{
  function resolve($path, $params = array())
  {
    if(file_exists(LIMB_DIR . '/core/behaviours/' . $path))
      return LIMB_DIR . '/core/behaviours/' . $path;

    return $this->_resolver->resolve('behaviours/' . $path, $params);
  }
}

?>