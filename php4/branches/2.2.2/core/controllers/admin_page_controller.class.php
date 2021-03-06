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
require_once(LIMB_DIR . 'core/lib/i18n/strings.class.php');
	
class admin_page_controller extends site_object_controller
{
	function _define_actions()
	{
		return array(
				'display' => array(
						'permissions_required' => 'r',
						'template_path' => '/admin/admin_page.html',
						'transaction' => false,
				),
				'admin_display' => array(
						'permissions_required' => 'r',
						'template_path' => '/admin/admin_page.html',
						'transaction' => false,
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
				'register_new_object' => array(
						'permissions_required' => 'w',
						'popup' => true,
						'JIP' => true,
						'action_name' => strings :: get('register_new_object'),
						'action_path' => '/site_object/register_new_object_action',
						'template_path' => '/site_object/register_new_object.html',
						'img_src' => '/shared/images/activate.gif'				
				)
		);
	}
}

?>