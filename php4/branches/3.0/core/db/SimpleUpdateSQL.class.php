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

//inspired by Marcus Baker's Changes :)

class SimpleUpdateSQL
{
  var $_table;
  var $_fields;
  var $_constraints;

  function SimpleUpdateSQL($table)
  {
    $this->_table = $table;

    $this->reset();
  }

  function reset()
  {
    $this->_fields = array();
    $this->_constraints = array();
  }

  function addField($field)
  {
    $this->_fields[] = $field;
  }

  function addCondition($condition)
  {
    $this->_constraints[] = $condition;
  }

  function toString()
  {
    $clauses = array(
            $this->_createUpdateClause(),
            $this->_createWhereClause());
    return trim(implode(' ', $clauses));
  }

  function _createUpdateClause()
  {
    return "UPDATE {$this->_table} SET " . implode(',', $this->_fields);
  }

  function _createWhereClause()
  {
    if (count($this->_constraints) == 0)
      return '';

    return 'WHERE (' . implode(') AND (', $this->_constraints) . ')';
  }
}
?>
