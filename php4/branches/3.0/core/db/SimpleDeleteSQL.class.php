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

class SimpleDeleteSQL
{
  var $_table;
  var $_constraints;

  function SimpleDeleteSQL($table)
  {
    $this->_table = $table;

    $this->reset();
  }

  function reset()
  {
    $this->_constraints = array();
  }

  function addCondition($condition)
  {
    $this->_constraints[] = $condition;
  }

  function toString()
  {
    $clauses = array(
            $this->_createDeleteClause(),
            $this->_createWhereClause());
    return trim(implode(' ', $clauses));
  }

  function _createDeleteClause()
  {
    return "DELETE FROM {$this->_table}";
  }

  function _createWhereClause()
  {
    if (count($this->_constraints) == 0)
      return '';

    return 'WHERE (' . implode(') AND (', $this->_constraints) . ')';
  }
}
?>
