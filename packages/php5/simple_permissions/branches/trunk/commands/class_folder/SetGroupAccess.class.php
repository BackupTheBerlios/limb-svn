<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/ 
require_once(LIMB_DIR . '/class/core/commans/form_command.class.php');
require_once(dirname(__FILE__) . '/../../access_policy.class.php');

class set_group_access_command extends form_command
{
	protected function __construct()
	{
    parent :: __construct('set_group_access');
	}
   
	protected function _init_dataspace()
	{
    $dataspace = Limb :: toolkit()->getDataspace();
    $request = Limb :: toolkit()->getRequest();

		if (!$class_id = $request->get('class_id'))
		  throw new LimbException('class_id not defined');

    $access_policy = $this->_get_access_policy();
		$policy = $access_policy->get_actions_access($class_id, access_policy :: ACCESSOR_TYPE_GROUP);

		$dataspace->set('policy', $policy);
	}
	
	protected function perform()
	{
    $request = Limb :: toolkit()->getRequest();
    $dataspace = Limb :: toolkit()->getDataspace();
    
		if (!$class_id = $request->get('class_id'))
		  throw new LimbException('class_id not defined');
		
    $access_policy = $this->_get_access_policy();
		$access_policy->save_actions_access($class_id,
                                        $dataspace->get('policy'),
                                        access_policy :: ACCESSOR_TYPE_GROUP);

		return Limb :: STATUS_OK;
	}
  
  // for mocking
  protected function _get_access_policy()
  {
    return new access_policy();
  }

}

?>