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
require_once(LIMB_DIR . 'core/model/response/close_popup_response.class.php');

class save_priority_action extends form_action
{
	function _define_dataspace_name()
	{
	  return 'grid_form';
	}
	
	function _valid_perform()
	{
		$data = $this->dataspace->export();
		$object =& site_object_factory :: create('site_structure');
		
		if(isset($data['priority']))
			$object->save_priority($data['priority']);
				
		return new close_popup_response();
	}
}

?>