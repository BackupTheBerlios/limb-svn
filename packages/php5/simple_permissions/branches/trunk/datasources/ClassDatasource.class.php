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
  public function getDataset(&$counter, $params=array())
  {
    $counter = 0;

    if(!$class_id = Limb :: toolkit()->getRequest()->get('class_id'))
      return new ArrayDataset();

    $db_table = Limb :: toolkit()->createDBTable('SysClass');
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
