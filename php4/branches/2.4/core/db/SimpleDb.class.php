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
require_once(LIMB_DIR . '/core/db/SimpleQueryBuilder.class.php');
require_once(LIMB_DIR . '/core/db/SimpleDbDataset.class.php');

class SimpleDb
{
  var $conn;

  function SimpleDb(&$conn)
  {
    $this->conn =& $conn;
  }

  function & getConnection()
  {
    return $this->conn;
  }

  function & select($table, $fields = '*', $conditions = array(), $order = '')
  {
    $stmt =& $this->conn->newStatement(SimpleQueryBuilder :: buildSelectSQL($table,
                                                                            $fields,
                                                                            $conditions,
                                                                            $order));
    $this->_fillStatementVariables($stmt, $conditions);
    return new SimpleDbDataset($stmt->getRecordSet());
  }

  function insert($table, $values)
  {
    $stmt =& $this->conn->newStatement(SimpleQueryBuilder :: buildInsertSQL($table,
                                                                            array_keys($values)));
    $this->_fillStatementVariables($stmt, $values);
    return $stmt->insertId(null);//???
  }

  function update($table, $values, $conditions = array())
  {
    $stmt =& $this->conn->newStatement(SimpleQueryBuilder :: buildUpdateSQL($table,
                                                                            array_keys($values),
                                                                            $conditions));
    $prefix = SimpleQueryBuilder :: getUpdatePrefix();
    foreach($values as $key => $value)
      $prefixed_values[$prefix . $key] = $value;

    $this->_fillStatementVariables($stmt, $prefixed_values);
    $this->_fillStatementVariables($stmt, $conditions);
    $stmt->execute();
    return $stmt->getAffectedRowCount();
  }

  function delete($table, $conditions = array())
  {
    $stmt =& $this->conn->newStatement(SimpleQueryBuilder :: buildDeleteSQL($table,
                                                                            $conditions));
    $this->_fillStatementVariables($stmt, $conditions);
    $stmt->execute();
    return $stmt->getAffectedRowCount();
  }


  function _fillStatementVariables(&$stmt, $values)
  {
    if(!is_array($values))
      return;

    foreach($values as $key => $value)
      $stmt->set($key, $value);
  }
}

?>
