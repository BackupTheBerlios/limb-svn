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
require_once(LIMB_DIR . '/core/db/ComplexSelectSQLDecorator.class.php');

class OneTableObjectsSQL extends ComplexSelectSQLDecorator
{
  function OneTableObjectsSQL(&$sql, $db_table_name)
  {
    parent :: ComplexSelectSQLDecorator($sql);

    $db_table =& $this->getDbTable($db_table_name);

    $this->addField($db_table->getColumnsForSelectAsString('tn', array('id')));
    $this->addTable($db_table->getTableName() . ' AS tn');
    $this->addCondition('sso.id=tn.object_id');
  }

  function & getDbTable($db_table_name)
  {
    $toolkit =& Limb :: toolkit();
    return $toolkit->createDBTable($db_table_name);
  }
}

?>
