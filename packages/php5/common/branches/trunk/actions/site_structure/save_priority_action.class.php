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
require_once(LIMB_DIR . 'class/core/actions/form_action.class.php');

class save_priority_action extends form_action
{
	protected function _define_dataspace_name()
	{
	  return 'grid_form';
	}
	
	protected function _valid_perform($request, $response)
	{
		$data = $this->dataspace->export();
		$object = Limb :: toolkit()->createSiteObject('site_structure');
		
		if(isset($data['priority']))
			$object->save_priority($data['priority']);
    
    $request->set_status(request :: STATUS_SUCCESS);
    
		if($request->has_attribute('popup'))
		  $response->write(close_popup_response($request));
	}
}

?>