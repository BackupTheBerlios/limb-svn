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
require_once(LIMB_DIR . '/class/lib/db/DbModule.class.php');

class DbNull extends DbModule
{
  function __construct()
  {
    $this->_db_connection = -1;
    $this->_sql_result = null;
  }

  public function connectDb($db_params)
  {
  }

  public function selectDb($db_name)
  {
  }

  public function disconnectDb($db_params)
  {
  }

  public function freeResult()
  {
  }

  protected function _sqlExecOperation($sql, $count=0, $start=0)
  {
    return false;
  }

  public function getAffectedRows()
  {
    return 0;
  }

  public function getSqlInsertId()
  {
    return false;
  }

  public function getLastError()
  {
    return '';
  }

  public function parseBatchSql(&$ret, $sql, $release)
  {
    return false;
  }

  protected function _fetchAssocResultRow()
  {
    return false;
  }

  protected function _resultNumFields()
  {
    return false;
  }

  protected function _processDefaultValue($value)
  {
    return false;
  }

  public function escape($sql)
  {
    return false;
  }

  public function concat($values)
  {
    return false;
  }

  public function substr($string, $offset, $limit=null)
  {
    return false;
  }

  public function countSelectedRows()
  {
    return false;
  }

  protected function _beginOperation()
  {
    return false;
  }

  protected function _commitOperation()
  {
  }

  protected function _rollbackOperation()
  {
  }
}
?>