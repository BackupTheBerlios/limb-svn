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

class SimpleInsertSQL
{
  var $_table;
  var $_fields;

  function SimpleInsertSQL($table)
  {
    $this->_table = $table;

    $this->reset();
  }

  function reset()
  {
    $this->_fields = array();
  }

  function addField($field, $value)
  {
    $this->_fields[$field] = $value;
  }

  function toString()
  {
    return $this->_createInsertClause();
  }

  function _createInsertClause()
  {
    $str_names = '(' . implode(',', array_keys($this->_fields)) . ')';
    $str_values = '(' . implode(',', $this->_fields) . ')';

    return "INSERT INTO {$this->_table} {$str_names} VALUES {$str_values}";
  }
}
?>
