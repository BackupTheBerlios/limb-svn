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

class save_new_object_access_command implements Command
{
	public function perform()
	{ 
    $toolkit = Limb :: toolkit();

    $dataspace = $toolkit->getDataspace();
    if (!$object = $dataspace->get('created_site_object'))
      throw new LimbException('can\'t find created site object in dataspace');

    $parent_id = $object->get_parent_node_id();

    $datasource = $toolkit->createDatasource('single_object_datasource');
    $datasource->set_node_id($parent_id);
    $parent_object = wrap_with_site_object($datasource->fetch());

    $action = $parent_object->get_controller()->get_action($toolkit->getRequest());

	  try
	  {
      $access_policy = $this->_get_access_policy();
	    $access_policy->save_new_object_access($object, $parent_object, $action);
	  }
	  catch(LimbException $e)
	  {
      return Limb :: STATUS_ERROR;
	  }

	  return Limb :: STATUS_OK;
	}
  
  // for mocking
  protected function _get_access_policy()
  {
    return new $access_policy;
  }
} 


?>