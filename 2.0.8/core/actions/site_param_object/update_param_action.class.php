<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: set_metadata_action.class.php 38 2004-03-13 14:25:46Z server $
*
***********************************************************************************/ 
require_once(LIMB_DIR . 'core/actions/site_param_object/update_param_common_action.class.php');

class update_param_action extends update_param_common_action
{
	
	function update_action($name='site_param_form')
	{
		parent :: update_param_common_action($name);
	}
}
?>