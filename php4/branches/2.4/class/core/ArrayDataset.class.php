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

class ArrayDataset
{
  var $data = array();

  var $record = array();

  var $first = true;

  var $counter = 0;

  function __construct($array = null)
  {
    if (is_array($array))
    {
      $this->addArray($array);
    }
  }

  function _setCurrentRecord()
  {
    if ($this->first)
      $this->next();
  }

  function _saveCurrentRecord($record)
  {
    $this->data[key($this->data)] = $record;
  }

  function reset()
  {
    $this->first = true;
    $this->counter = 0;
  }

  function next()
  {
    if ($this->first)
    {
      $record = reset($this->data);
      $this->first = false;
      $this->counter = 0;
    }
    else
    {
      $record = next($this->data);
      $this->counter++;
    }
    if (is_array($record))
    {
      $this->record = $record;
      return true;
    }
    else
    {
      $this->record = null;
      return false;
    }
  }

  function get($name)
  {
    $this->_setCurrentRecord();

    if (isset($this->record[$name]))
      return $this->record[$name];
  }

  function set($name, $value)
  {
    $this->_setCurrentRecord();
    $this->record[$name] = $value;
    $this->_saveCurrentRecord($this->record);
  }

  function append($name, $value)
  {
    $this->_setCurrentRecord();
    $this->record[$name] .= $value;
    $this->_saveCurrentRecord($this->record);
  }

  function import($valuelist)
  {
    $this->_setCurrentRecord();
    if (is_array($valuelist))
    {
      $this->record = null;
      $this->record = $valuelist;
    }
    $this->_saveCurrentRecord($this->record);
  }

  function importAppend($valuelist)
  {
    if (is_array($valuelist))
    {
      $this->_setCurrentRecord();
      foreach ($valuelist as $name => $value)
      {
        $this->set($name, $value);
      }
      $this->_saveCurrentRecord($this->record);
    }
  }

  function export()
  {
    $this->_setCurrentRecord();
    return $this->record;
  }

  function addArray($array)
  {
    foreach ($array as $value)
    {
      if (is_array($value))
      {
        $this->data[] = $value;
      }
    }
  }

  function getCounter()
  {
    return $this->counter;
  }

  function getTotalRowCount()
  {
    return sizeof($this->data);
  }

  function getByIndexString($index)
  {
    $this->_setCurrentRecord();

    if(!preg_match('/^(\[\w+\]|\[\'\w+\'\]|\[\"\w+\"\])+$/', $index))
      return '';

    $index = str_replace(array('"', '\''), array('', ''), $index);
    $index = str_replace(array('[', ']'), array('["', '"]'), $index);

    eval('$res = isset($this->record' . $index . ') ? $this->record' . $index . ' : "";');

    return $res;
  }
}

?>