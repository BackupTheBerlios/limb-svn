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
require_once(LIMB_DIR . '/class/core/file_resolvers/FileResolverDecorator.class.php');

class DbTableFileResolver extends FileResolverDecorator
{
  public function resolve($class_path, $params = array())
  {
    if(file_exists(LIMB_DIR . '/class/db_tables/' . $class_path . 'DbTable.class.php'))
      return LIMB_DIR . '/class/db_tables/' . $class_path . 'DbTable.class.php';

    return $this->_resolver->resolve('db_tables/' . $class_path . 'DbTable.class.php', $params);
  }
}

?>