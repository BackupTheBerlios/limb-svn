<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: subscribe_controller.class.php 245 2004-03-05 12:11:42Z server $
*
***********************************************************************************/
require_once(LIMB_DIR . 'core/controllers/site_object_controller.class.php');
	
class subscribe_controller extends site_object_controller
{
	function subscribe_controller()
	{
		$this->_actions = array(
				'display' => array(
						'permissions_required' => 'r',
						'template_path' => '/subscribe/display.html'
				),
				'create_subscribe_theme' => array(
						'permissions_required' => 'w',
						'template_path' => '/subscribe_theme/create.html',
						'action_path' => '/subscribe_theme/create_subscribe_theme_action',
						'JIP' => true,
						'popup' => true,
						'img_src' => '/shared/images/new.folder.gif',
						'action_name' => strings :: get('create_subscribe_theme', 'subscribe'),
						'can_have_access_template' => true,
				),
		);
 		
		parent :: site_object_controller();
	}
}

?>