<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: limb@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/ 
class object
{
	protected $dataspace;
	
	function __construct()
	{
    $this->dataspace = $this->_create_dataspace();
	}
  
  protected function _create_dataspace()
  {
    include_once(LIMB_DIR . '/class/core/dataspace.class.php');
    return new dataspace();
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
	
	public function has_attribute($name)//rename later
	{
	  return $this->dataspace->get($name) !== null;
	}
  
	public function get($name, $default_value=null)
	{
		return $this->dataspace->get($name, $default_value);
	}

	public function & get_reference($name)
	{
		return $this->dataspace->get_reference($name);
	}
  
  public function get_by_index_string($raw_index, $default_value = null)
  {
    return $this->dataspace->get_by_index_string($raw_index, $default_value);
  }
	
	public function set($name, $value)
	{
		$this->dataspace->set($name, $value);
	}

  public function set_by_index_string($raw_index, $value)
  {
    $this->dataspace->set_by_index_string($raw_index, $value);
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