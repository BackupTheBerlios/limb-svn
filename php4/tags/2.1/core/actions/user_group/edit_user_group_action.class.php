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
require_once(LIMB_DIR . 'core/actions/form_edit_site_object_action.class.php');

class edit_user_group_action extends form_edit_site_object_action
{
	function edit_user_group_action()
	{
		$definition = array(
			'site_object' => 'user_group',
		);
		
		parent :: form_edit_site_object_action('edit_user_group', $definition);
	}
}

?>