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

class stats_search_engines_report_action extends form_action
{
	function _define_dataspace_name()
	{
	  return 'search_engines_form';
	}

	function _valid_perform()
	{
		$_REQUEST['stats_start_date'] = $this->dataspace->get('stats_start_date');
		$_REQUEST['stats_finish_date'] = $this->dataspace->get('stats_finish_date');
	
		return parent :: _valid_perform();
	}
	
}

?>