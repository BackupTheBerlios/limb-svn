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
  protected $dataspace;

  function __construct()
  {
    $this->dataspace = $this->_createDataspace();
  }

  protected function _createDataspace()
  {
    include_once(LIMB_DIR . '/class/core/Dataspace.class.php');
    return new Dataspace();
  }

  public function merge($values)
  {
    $this->dataspace->merge($values);
  }

  public function import($values)
  {
    $this->dataspace->import($values);
  }

  public function export()
  {
    return $this->dataspace->export();
  }

  public function hasAttribute($name)//rename later
  {
    return $this->dataspace->get($name) !== null;
  }

  public function get($name, $default_value=null)
  {
    return $this->dataspace->get($name, $default_value);
  }

  public function & getReference($name)
  {
    return $this->dataspace->getReference($name);
  }

  public function getByIndexString($raw_index, $default_value = null)
  {
    return $this->dataspace->getByIndexString($raw_index, $default_value);
  }

  public function set($name, $value)
  {
    $this->dataspace->set($name, $value);
  }

  public function setByIndexString($raw_index, $value)
  {
    $this->dataspace->setByIndexString($raw_index, $value);
  }

  public function destroy($name)
  {
    $this->dataspace->destroy($name);
  }

  public function reset()
  {
    $this->dataspace->reset();
  }

}

?>