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
require_once(LIMB_DIR . '/class/lib/util/ComplexArray.class.php');

/**
* The dataspace is a container for a set of named data values (variables).
*/
class Dataspace
{
  public $vars = array();//public is done for speed in compiled template

  function __construct($vars = null)
  {
    if(is_array($vars))
      $this->import($vars);
  }

  public function getHash()
  {
    return md5(serialize($this->vars));
  }

  public function get($name, $default_value = null)
  {
    if (isset($this->vars[$name]))
      return $this->vars[$name];
    else
      return $default_value;
  }

  public function & getReference($name, $default_value = '')
  {
    if (!isset($this->vars[$name]))
      $this->vars[$name] = $default_value;

    return $this->vars[$name];
  }

  protected function _processIndexString($index)
  {
    if(!preg_match('/^(\[\w+\]|\[\'\w+\'\]|\[\"\w+\"\])+$/', $index))
      return null;

    $index = str_replace(array('"', '\''), array('', ''), $index);
    $index = str_replace(array('[', ']'), array('["', '"]'), $index);
    return $index;
  }

  public function getByIndexString($raw_index, $default_value = null)
  {
    if(!$index = $this->_processIndexString($raw_index))
      throw new Exception('invalid string index');

    eval('$res = isset($this->vars' . $index . ') ? $this->vars' . $index . ' : null;');

    if($res === null)
      return $default_value;

    return $res;
  }

  public function setByIndexString($raw_index, $value)
  {
    if(!$index = $this->_processIndexString($raw_index))
      return null;

    eval('$this->vars' . $index . ' = "";$res =& $this->vars' . $index . ';');
    $res = $value;
  }

  public function getSize()
  {
    return count($this->vars);
  }

  public function set($name, $value)
  {
    $this->vars[$name] = $value;
  }

  public function append($name, $value)
  {
    $this->vars[$name] .= $value;
  }

  public function import($valuelist)
  {
    $this->vars = $valuelist;
  }

  public function importAppend($valuelist)
  {
    foreach ($valuelist as $key => $value)
    {
      $this->set($key, $value);
    }
  }

  public function merge($valuelist)
  {
    if(is_array($valuelist) &&  sizeof($valuelist))
      $this->vars = ComplexArray :: array_merge($this->vars, $valuelist);

    if(!is_array($this->vars))
      $this->vars = array();
  }

  public function export()
  {
    return $this->vars;
  }

  public function destroy($name)
  {
    if (isset($this->vars[$name]))
      unset($this->vars[$name]);
  }

  public function reset()
  {
    $this->vars = array();
  }

  public function isEmpty()
  {
    return count($this->vars) ? false : true;
  }
}

?>