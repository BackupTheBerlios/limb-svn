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
  function resolve($class_path, $params = array())
  {
    if(file_exists(LIMB_DIR . '/core/behaviours/' . $class_path . '.class.php'))
      return LIMB_DIR . '/core/behaviours/' . $class_path . '.class.php';

    return $this->_resolver->resolve('behaviours/' . $class_path . '.class.php', $params);
  }
}

?>