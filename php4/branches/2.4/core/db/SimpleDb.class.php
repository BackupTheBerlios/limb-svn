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

  function & select($table, $fields = array(), $conditions = array(), $order = '')
  {
    include_once(LIMB_DIR . '/core/db/SimpleSelectSQL.class.php');

    $sql = new SimpleSelectSQL($table);

    $this->_addFields($sql, $fields);

    $this->_addConditions($sql, $conditions);

    if($order)
      $sql->addOrder($order);

    $stmt =& $this->conn->newStatement($sql->toString());

    $this->_fillStatementVariables($stmt, $conditions);

    return new SimpleDbDataset($stmt->getRecordSet());
  }

  function insert($table, $values)
  {
    include_once(LIMB_DIR . '/core/db/SimpleInsertSQL.class.php');

    $sql = new SimpleInsertSQL($table);

    $this->_addFields($sql, $values);

    $stmt =& $this->conn->newStatement($sql->toString());

    $this->_fillStatementVariables($stmt, $values);
    return $stmt->insertId(null);//???
  }

  function update($table, $values, $conditions = array())
  {
    include_once(LIMB_DIR . '/core/db/SimpleUpdateSQL.class.php');

    $sql = new SimpleUpdateSQL($table);

    $prefixed_values = array();
    foreach($values as $key => $value)
    {
      $sql->addField($key . '=:_' . $key . ':');
      $prefixed_values['_' . $key] = $value;
    }

    $this->_addConditions($sql, $conditions);

    $stmt =& $this->conn->newStatement($sql->toString());

    $this->_fillStatementVariables($stmt, $prefixed_values);
    $this->_fillStatementVariables($stmt, $conditions);

    $stmt->execute();

    return $stmt->getAffectedRowCount();
  }

  function delete($table, $conditions = array())
  {
    include_once(LIMB_DIR . '/core/db/SimpleDeleteSQL.class.php');

    $sql = new SimpleDeleteSQL($table);

    $this->_addConditions($sql, $conditions);

    $stmt =& $this->conn->newStatement($sql->toString());
    $this->_fillStatementVariables($stmt, $conditions);

    $stmt->execute();
    return $stmt->getAffectedRowCount();
  }

  function _addFields(&$sql, $values)
  {
    foreach($values as $key => $value)
    {
      if(is_integer($key))
        $sql->addField($value);
      else
        $sql->addField($key , ':' . $key . ':');
    }
  }

  function _addConditions(&$sql, $conditions)
  {
    foreach($conditions as $key => $value)
    {
      if(is_integer($key))
        $sql->addCondition($value);
      else
        $sql->addCondition($key . '=:' . $key . ':');
    }
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
