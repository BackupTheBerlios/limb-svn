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
require_once(LIMB_DIR . 'core/actions/form_action.class.php');

class save_priority_action extends form_action
{
	function _define_dataspace_name()
	{
	  return 'grid_form';
	}
	
	function _valid_perform(&$request, &$response)
	{
		$data = $this->dataspace->export();
		$object =& site_object_factory :: create('site_structure');
		
		if(isset($data['priority']))
			$object->save_priority($data['priority']);
    
    $request->set_status(REQUEST_STATUS_SUCCESS);
    
		if($request->has_attribute('popup'))
		  $response->write(close_popup_response($request));
	}
}

?>