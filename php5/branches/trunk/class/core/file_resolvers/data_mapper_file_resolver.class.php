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
require_once(LIMB_DIR . '/class/core/file_resolvers/file_resolver_decorator.class.php');

class data_mapper_file_resolver extends file_resolver_decorator
{
  public function resolve($class_path, $params = array())
  {
    if(file_exists(LIMB_DIR . '/class/core/data_mappers/' . $class_path . '.class.php'))
      return LIMB_DIR . '/class/core/data_mappers/' . $class_path . '.class.php';    
      
    return $this->_resolver->resolve('data_mappers/' . $class_path . '.class.php', $params);   
  }                                      
}

?>