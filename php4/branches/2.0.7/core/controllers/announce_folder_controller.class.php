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
require_once(LIMB_DIR . 'core/controllers/site_object_controller.class.php');
	
class announce_folder_controller extends site_object_controller
{
	function announce_folder_controller()
	{
		$this->_actions = array(
				'display' => array(
						'permissions_required' => 'r',
						'template_path' => '/announce_folder/display.html'
				),
				'admin_display' => array(
						'permissions_required' => 'r',
						'template_path' => '/announce_folder/admin_display.html'
				),
				'create_announce' => array(
						'permissions_required' => 'w',
						'template_path' => '/announce_object/create.html',
						'action_path' => '/announce_object/create_announce_action',
						'JIP' => true,
						'popup' => true,
						'img_src' => '/shared/images/new.generic.gif',
						'action_name' => strings :: get('create_announce', 'announce'),
						'can_have_access_template' => true,
				),
		);
 		
		parent :: site_object_controller();
	}
}

?>