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
require_once(LIMB_DIR . '/class/core/file_resolvers/FileResolver.interface.php');

class FileResolverDecorator implements FileResolver
{
  protected $_resolver = null;

  function __construct($resolver)
  {
    resolveHandle($resolver);

    if(!$resolver instanceof FileResolver &&  !$resolver instanceof SimpleMock)
      throw new Exception('invalid wrapped resolver');

    $this->_resolver = $resolver;
  }

  public function resolve($file_path, $params = array())
  {
    return $this->_resolver->resolve($file_path, $params);
  }
}

?>