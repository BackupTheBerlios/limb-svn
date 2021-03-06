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
class Object
{
  var $__class_name = 'Object';

  var $dataspace;

  function Object()
  {
    $this->dataspace =& $this->_createDataspace();
  }

  function & _createDataspace()
  {
    include_once(WACT_ROOT . '/datasource/dataspace.inc.php');
    return new Dataspace();
  }

  function merge($values)
  {
    $this->dataspace->merge($values);
  }

  function import($values)
  {
    $this->dataspace->import($values);
  }

  function export()
  {
    return $this->dataspace->export();
  }

  function hasAttribute($name)//rename later
  {
    return $this->dataspace->get($name) !== null;
  }

  function get($name, $default_value=null)
  {
    return $this->dataspace->get($name, $default_value);
  }

  function set($name, $value)
  {
    $this->dataspace->set($name, $value);
  }

  function remove($name)
  {
    $this->dataspace->remove($name);
  }

  function removeAll()
  {
    $this->dataspace->removeAll();
  }

}

?>