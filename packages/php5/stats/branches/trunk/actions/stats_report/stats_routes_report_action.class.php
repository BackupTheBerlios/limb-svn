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

class stats_routes_report_action extends form_action
{
	protected function _define_dataspace_name()
	{
	  return 'routes_form';
	}
	
	protected function _valid_perform($request, $response)
	{
	  $request->import($this->dataspace->export());
	
		parent :: _valid_perform($request, $response);
	}
	
}

?>