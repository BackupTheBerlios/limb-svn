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
require_once(LIMB_DIR . 'core/lib/locale/strings.class.php');
	
class site_structure_controller extends site_object_controller
{
	function site_structure_controller()
	{
		$this->_actions = array(
				'display' => array(
						'permissions_required' => 'r',
						'template_path' => '/admin/site_structure.html',
				),
				'toggle' => array(
						'permissions_required' => 'r',
						'template_path' => '/admin/site_structure.html',
						'action_path' => 'tree_toggle_action', 
						'display_in_breadcrumbs' => false,
				),
				'order' => array(
						'permissions_required' => 'r',
						'action_path' => 'tree_change_order_action', 
						'display_in_breadcrumbs' => false,
						'popup' => true,
				),
				'edit' => array(
						'permissions_required' => 'w',
						'popup' => true,
						'JIP' => true,
						'action_name' => strings :: get('edit'),
						'action_path' => '/site_object/edit_action',
						'template_path' => '/site_object/edit.html',
						'img_src' => '/shared/images/edit.gif'
				),
		);
 		

		parent :: site_object_controller();
	}
}

?>