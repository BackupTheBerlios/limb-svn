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
  function DbNull()
  {
    $this->_db_connection = -1;
    $this->_sql_result = null;
  }

  function connectDb($db_params)
  {
  }

  function selectDb($db_name)
  {
  }

  function disconnectDb($db_params)
  {
  }

  function freeResult()
  {
  }

  function _sqlExecOperation($sql, $count=0, $start=0)
  {
    return false;
  }

  function getAffectedRows()
  {
    return 0;
  }

  function getSqlInsertId()
  {
    return false;
  }

  function getLastError()
  {
    return '';
  }

  function parseBatchSql(&$ret, $sql, $release)
  {
    return false;
  }

  function _fetchAssocResultRow()
  {
    return false;
  }

  function _resultNumFields()
  {
    return false;
  }

  function _processDefaultValue($value)
  {
    return false;
  }

  function escape($sql)
  {
    return false;
  }

  function concat($values)
  {
    return false;
  }

  function substr($string, $offset, $limit=null)
  {
    return false;
  }

  function countSelectedRows()
  {
    return false;
  }

  function _beginOperation()
  {
    return false;
  }

  function _commitOperation()
  {
  }

  function _rollbackOperation()
  {
  }
}
?>