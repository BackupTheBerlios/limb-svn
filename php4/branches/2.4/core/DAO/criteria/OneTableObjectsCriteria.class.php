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

class OneTableObjectsCriteria
{
  var $db_table_name;

  function OneTableObjectsCriteria($db_table_name)
  {
    $this->db_table_name = $db_table_name;
  }

  function setDBTableName($db_table_name)
  {
    $this->db_table_name = $db_table_name;
  }

  function process(&$sql)
  {
    $db_table =& $this->getDbTable($this->db_table_name);

    $sql->addField($db_table->getColumnsForSelectAsString('tn', array('id')));
    $sql->addTable($db_table->getTableName() . ' AS tn');
    $sql->addCondition('sso.id=tn.object_id');
  }

  function & getDbTable($db_table_name)
  {
    $toolkit =& Limb :: toolkit();
    return $toolkit->createDBTable($db_table_name);
  }
}

?>
