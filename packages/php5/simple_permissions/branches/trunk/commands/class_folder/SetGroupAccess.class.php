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
require_once(LIMB_DIR . '/class/core/commans/FormCommand.class.php');
require_once(dirname(__FILE__) . '/../../AccessPolicy.class.php');

class SetGroupAccessCommand extends FormCommand
{
	protected function __construct()
	{
    parent :: __construct('set_group_access');
	}
   
	protected function _initDataspace()
	{
    $dataspace = Limb :: toolkit()->getDataspace();
    $request = Limb :: toolkit()->getRequest();

		if (!$class_id = $request->get('class_id'))
		  throw new LimbException('class_id not defined');

    $access_policy = $this->_getAccessPolicy();
		$policy = $access_policy->getActionsAccess($class_id, AccessPolicy :: ACCESSOR_TYPE_GROUP);

		$dataspace->set('policy', $policy);
	}
	
	protected function perform()
	{
    $request = Limb :: toolkit()->getRequest();
    $dataspace = Limb :: toolkit()->getDataspace();
    
		if (!$class_id = $request->get('class_id'))
		  throw new LimbException('class_id not defined');
		
    $access_policy = $this->_getAccessPolicy();
		$access_policy->saveActionsAccess($class_id,
                                        $dataspace->get('policy'),
                                        AccessPolicy :: ACCESSOR_TYPE_GROUP);

		return Limb :: STATUS_OK;
	}
  
  // for mocking
  protected function _getAccessPolicy()
  {
    return new AccessPolicy();
  }

}

?>