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

class apply_action_access_template_command implements Command
{
	public function perform()
	{ 
    $toolkit = Limb :: toolkit();
    $request = $toolkit->getRequest();

    $datasource = $toolkit->createDatasource('requested_object_datasource');
    $datasource->set_request($request);
    
    $object = wrap_with_site_object($datasource->fetch());
    
    $action = $object->get_controller()->get_action($request);
     
	  try
	  {
      $access_policy = $this->_get_access_policy();
	    $access_policy->apply_access_templates($object, $action);
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