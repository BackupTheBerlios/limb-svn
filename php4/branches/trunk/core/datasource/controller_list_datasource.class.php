<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/datasource/datasource.class.php');

class controller_list_datasource extends datasource
{
  function & get_dataset($params = array())
  {
    if(!fetch_requested_object())
      return new array_dataset();

    $db_table =& db_table_factory :: instance('sys_controller');
    $controllers = $db_table->get_list('', 'name');

    return new array_dataset($controllers);
  }
}


?>