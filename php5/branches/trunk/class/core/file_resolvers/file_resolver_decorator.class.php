<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: limb@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . '/class/core/file_resolvers/file_resolver.interface.php');

class file_resolver_decorator implements file_resolver
{
  protected $_resolver = null;
  
  function __construct($resolver)
  {
    resolve_handle($resolver);
    
    if(!$resolver instanceof file_resolver && !$resolver instanceof SimpleMock)
      throw new Exception('invalid wrapped resolver');
    
    $this->_resolver = $resolver;    
  }
        
  public function resolve($file_path, $params = array())
  {    
    return $this->_resolver->resolve($file_path, $params);
  }  
}

?>