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
require_once(LIMB_DIR . '/class/datasources/Datasource.interface.php');

class ClassDatasource implements Datasource
{
  function getDataset(&$counter, $params=array())
  {
    $counter = 0;

    $toolkit =& Limb :: toolkit();
    $request =& $toolkit->getRequest();

    if(!$class_id = $request->get('class_id'))
      return new ArrayDataset();

    $db_table =& $toolkit->createDBTable('SysClass');
    $class_data = $db_table->getRowById($class_id);

    if ($class_data)
    {
      $counter = 1;
      return new ArrayDataset(array(0 => $class_data));
    }
    else
      return new ArrayDataset(array());
  }
}


?>
