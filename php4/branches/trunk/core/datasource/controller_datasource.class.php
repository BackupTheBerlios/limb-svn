<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/datasource/datasource.class.php');

class controller_datasource extends datasource
{
  function & get_dataset(&$counter, $params=array())
  {
    $counter = 0;

    $request = request :: instance();

    if(!$controller_id = $request->get_attribute('controller_id'))
      return new array_dataset();

    $db_table =& db_table_factory :: instance('sys_controller');
    $controller_data = $db_table->get_row_by_id($controller_id);

    if ($controller_data)
    {
      $counter = 1;
      return new array_dataset(array(0 => $controller_data));
    }
    else
      return new array_dataset(array());
  }
}


?>
