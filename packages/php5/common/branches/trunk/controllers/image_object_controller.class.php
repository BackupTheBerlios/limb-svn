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
require_once(LIMB_DIR . 'class/core/controllers/site_object_controller.class.php');
	
class image_object_controller extends site_object_controller
{
	protected function _define_actions()
	{
		return array(
				'display' => array(
						'permissions_required' => 'r',
						'action_path' => '/images/display_image_action',
				),
				'edit' => array(
						'permissions_required' => 'w',
						'popup' => true,
						'JIP' => true,
						'action_name' => strings :: get('edit'),
						'action_path' => '/images/edit_image_action',
						'template_path' => '/image/edit.html',
						'img_src' => '/shared/images/edit.gif'
				),
				'edit_variations' => array(
						'permissions_required' => 'w',
						'popup' => true,
						'JIP' => true,
						'action_name' => strings :: get('edit_variations', 'image'),
						'action_path' => '/images/edit_variations_action',
						'template_path' => '/image/edit_variations.html',
						'img_src' => '/shared/images/look_group.gif'
				),
				'delete' => array(
						'permissions_required' => 'w',
						'JIP' => true,
						'popup' => true,
						'img_src' => '/shared/images/rem.gif',
						'action_name' => strings :: get('delete'),
						'action_path' => 'form_delete_site_object_action',
						'template_path' => '/site_object/delete.html',
				),
		);
	}
}

?>