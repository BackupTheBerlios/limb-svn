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

class FileResolverDecorator// implements FileResolver
{
  var $_resolver = null;

  function FileResolverDecorator($resolver)
  {
    resolveHandle($resolver);

    if(!is_a($resolver, 'FileResolver') &&  is_a($resolver, 'SimpleMock'))
      throw new Exception('invalid wrapped resolver');

    $this->_resolver = $resolver;
  }

  function resolve($file_path, $params = array())
  {
    return $this->_resolver->resolve($file_path, $params);
  }
}

?>