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
require_once(LIMB_DIR . '/class/lib/util/complex_array.class.php');

/**
* The dataspace is a container for a set of named data values (variables).
*/
class dataspace
{
  public $vars = array();//public is done for speed in compiled template

  function __construct($vars = null)
  {
    if(is_array($vars))
      $this->import($vars);
  }

  public function get_hash()
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

  public function & get_reference($name, $default_value = '')
  {
    if (!isset($this->vars[$name]))
      $this->vars[$name] = $default_value;

    return $this->vars[$name];
  }

  protected function _process_index_string($index)
  {
    if(!preg_match('/^(\[\w+\]|\[\'\w+\'\]|\[\"\w+\"\])+$/', $index))
      return null;

    $index = str_replace(array('"', '\''), array('', ''), $index);
    $index = str_replace(array('[', ']'), array('["', '"]'), $index);
    return $index;
  }

  public function get_by_index_string($raw_index, $default_value = null)
  {
    if(!$index = $this->_process_index_string($raw_index))
      throw new Exception('invalid string index');

    eval('$res = isset($this->vars' . $index . ') ? $this->vars' . $index . ' : null;');

    if($res === null)
      return $default_value;

    return $res;
  }

  public function set_by_index_string($raw_index, $value)
  {
    if(!$index = $this->_process_index_string($raw_index))
      return null;

    eval('$this->vars' . $index . ' = "";$res =& $this->vars' . $index . ';');
    $res = $value;
  }

  public function get_size()
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

  public function import_append($valuelist)
  {
    foreach ($valuelist as $key => $value)
    {
      $this->set($key, $value);
    }
  }

  public function merge($valuelist)
  {
    if(is_array($valuelist) && sizeof($valuelist))
      $this->vars = complex_array :: array_merge($this->vars, $valuelist);

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

  public function is_empty()
  {
    return count($this->vars) ? false : true;
  }
}

?>