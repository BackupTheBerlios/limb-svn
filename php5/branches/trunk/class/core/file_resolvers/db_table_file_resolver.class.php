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
require_once(LIMB_DIR . '/class/core/file_resolvers/file_resolver_decorator.class.php');

class db_table_file_resolver extends file_resolver_decorator
{
  public function resolve($class_path, $params = array())
  {
    if(file_exists(LIMB_DIR . '/class/db_tables/' . $class_path . '_db_table.class.php'))
      return LIMB_DIR . '/class/db_tables/' . $class_path . '_db_table.class.php';

    return $this->_resolver->resolve('db_tables/' . $class_path . '_db_table.class.php', $params);
  }
}

?>