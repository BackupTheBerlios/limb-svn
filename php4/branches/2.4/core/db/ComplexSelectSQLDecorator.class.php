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

class ComplexSelectSQLDecorator
{
  var $sql;

  function ComplexSelectSQLDecorator(&$sql)
  {
    $this->sql =& $sql;
  }

  function reset()
  {
    $this->sql->reset();
  }

  function addField($field)
  {
    $this->sql->addField($field);
  }

  function addTable($table)
  {
    $this->sql->addTable($table);
  }

  function addOrder($field, $type='ASC')
  {
    $this->sql->addOrder($field, $type);
  }

  function addGroupBy($group)
  {
    $this->sql->addGroupBy($group);
  }

  function addLeftJoin($table, $connect_by)
  {
    $this->sql->addLeftJoin($table, $connect_by);
  }

  function addCondition($condition)
  {
    $this->sql->addCondition($condition);
  }

  function toString()
  {
    return $this->sql->toString();
  }
}
?>
