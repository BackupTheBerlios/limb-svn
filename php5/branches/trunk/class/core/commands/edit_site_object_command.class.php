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
require_once(LIMB_DIR . 'class/core/commands/command.interface.php');

class edit_site_object_command implements Command
{
	public function perform()
	{
    $object = Limb :: toolkit()->createSiteObject($this->_define_site_object_class_name()); 
    
    $this->_fill_object($object);
    
    try
    {
      $this->_update_object_operation($object);
    }
    catch(LimbException $e)
    {
      return Limb :: STATUS_ERROR;
    }
    
    return Limb :: STATUS_OK;
  }
  
	protected function _update_object_operation($object)
	{
		$object->update($this->_define_increase_version_flag());
	}
  
  protected function _fill_object($object)
  {
    $dataspace = Limb :: toolkit()->getDataspace();

    $object->import($this->_load_object_data());
    
		complex_array :: map($this->_define_datamap(), $dataspace->export(), $data = array());
		
		$this->object->merge($data);
  }

	protected function _load_object_data()
	{
    $toolkit = Limb :: toolkit();
		return $toolkit->getFetcher()->fetch_requested_object($toolkit->getRequest());
	}

  function _define_increase_version_flag()
  {
    if (class_exists('content_object') && $this->object instanceof 'content_object'))
      return true;
    else 
      return false;
  }

  protected function _define_site_object_class_name()
	{
	  return 'site_object';
	}

	protected function _define_datamap()
	{
	  return array(
			'parent_node_id' => 'parent_node_id',
			'identifier' => 'identifier',
			'title' => 'title'
	  );
	}
}

?> 
