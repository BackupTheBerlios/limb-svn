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
require_once(LIMB_DIR . '/class/core/finders/SiteObjectsRawFinder.class.php');

abstract class OneTableObjectsRawFinder extends SiteObjectsRawFinder
{
  protected  $_db_table = null;

  public function getDbTable()
  {
    if(!$this->_db_table)
    {
      $db_table_name = $this->_defineDbTableName();

      $this->_db_table = Limb :: toolkit()->createDBTable($db_table_name);
    }

    return $this->_db_table;
  }

  abstract protected function _defineDbTableName();

  public function find($params=array(), $sql_params=array())
  {
    $db_table = $this->getDbTable();

    $sql_params['columns'][] = ' ' . $db_table->getColumnsForSelect('tn', array('id')) . ',';

    $table_name = $db_table->getTableName();
    $sql_params['tables'][] = ",{$table_name} as tn";

    $sql_params['conditions'][] = 'AND sso.id=tn.object_id';

    return $this->_doParentFind($params, $sql_params);
  }

  public function findById($id)
  {
    return $this->find(array(), array('conditions' => array(' AND sso.id='. $id)));
  }

  //for mocking
  protected function _doParentFind($params, $sql_params)
  {
    return parent :: find($params, $sql_params);
  }

  protected function _doParentFindCount($sql_params)
  {
    return parent :: findCount($sql_params);
  }

  public function findCount($sql_params=array())
  {
    $db_table = $this->getDbTable();
    $table_name = $db_table->getTableName();
    $sql_params['tables'][] = ",{$table_name} as tn";

    $sql_params['conditions'][] = 'AND sso.id=tn.object_id';

    return $this->_doParentFindCount($sql_params);
  }
}

?>