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
require_once(LIMB_DIR . '/class/lib/system/objects_support.inc.php');

class FileResolverDecorator// implements FileResolver
{
  var $_resolver = null;

  function FileResolverDecorator($resolver)
  {
    resolveHandle($resolver);

    if(!is_a($resolver, 'FileResolver') &&  is_a($resolver, 'SimpleMock'))
      die('invalid wrapped resolver! ' . __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);

    $this->_resolver = $resolver;
  }

  function resolve($file_path, $params = array())
  {
    return $this->_resolver->resolve($file_path, $params);
  }
}

?>