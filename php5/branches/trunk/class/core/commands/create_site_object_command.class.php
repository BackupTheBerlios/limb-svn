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
require_once(LIMB_DIR . '/class/core/commands/command.interface.php');

class create_site_object_command implements Command
{
	public function perform()
	{
    $object = Limb :: toolkit()->createSiteObject($this->_define_site_object_class_name()); 
    
    $this->_fill_object($object);
    
    try
    {
      $this->_create_object_operation($object);
    }
    catch(LimbException $e)
    {
      return Limb :: STATUS_ERROR;
    }
    
    return Limb :: STATUS_OK;
  }
  
	protected function _create_object_operation($object)
	{
		$object->create();
    
    Limb :: toolkit()->getDataspace()->set('created_site_object', $object);
	}
  
  protected function _fill_object($object)
  {
    $dataspace = Limb :: toolkit()->getDataspace();
    
    $object->merge($dataspace->export());
    
    if (!$dataspace->get('parent_node_id'))
    {
      $parent_object_data = $this->_load_parent_object_data();
      $object->set('parent_node_id', $parent_object_data['node_id']);
    }  
  }

	protected function _load_parent_object_data()
	{
    $toolkit = Limb :: toolkit();
    $datasource = $toolkit->createDatasource('requested_object_datasource');
    $datasource->set_request($toolkit->getRequest());
		return $datasource->fetch();
	}

  protected function _define_site_object_class_name()
	{
	  return 'site_object';
	}

}

?> 
