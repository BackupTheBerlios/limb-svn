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
require_once(LIMB_DIR . '/class/core/controllers/site_object_controller.class.php');
	
class navigation_item_controller extends site_object_controller
{
  protected function _define_default_action()
  {
		return 'admin_display';
	} 
	

	protected function _define_actions()
	{
		return array(
				'admin_display' => array(
						'template_path' => '/navigation_item/admin_display.html',
				),
				'create_navigation_item' => array(
						'template_path' => '/navigation_item/create.html',
						'action_path' => '/navigation_item/create_navigation_item_action',
						'JIP' => true,
						'popup' => true,
						'img_src' => '/shared/images/new.generic.gif',
						'action_name' => strings :: get('create_menu_item','navigation'),
						'can_have_access_template' => true,
				),
				'edit' => array(
						'popup' => true,
						'JIP' => true,
						'action_name' => strings :: get('edit_menu_item', 'navigation'),
						'action_path' => '/navigation_item/edit_navigation_item_action',
						'template_path' => '/navigation_item/edit.html',
						'img_src' => '/shared/images/edit.gif'
				),
				'publish' => array(
						'popup' => true,
						'JIP' => true,
						'action_name' => strings :: get('publish'),
						'action_path' => '/doc_flow_object/set_publish_status_action',
						'img_src' => '/shared/images/publish.gif',
						'can_have_access_template' => true,
				),
				'unpublish' => array(
						'popup' => true,
						'JIP' => true,
						'action_name' => strings :: get('unpublish'),
						'action_path' => '/doc_flow_object/set_publish_status_action',
						'img_src' => '/shared/images/unpublish.gif',
						'can_have_access_template' => true,
				),
				'delete' => array(
						'JIP' => true,
						'popup' => true,
						'action_name' => strings :: get('delete_menu_item', 'navigation'),
						'action_path' => 'form_delete_site_object_action',
						'template_path' => '/site_object/delete.html',
						'img_src' => '/shared/images/rem.gif'
				),
		);
	}
}

?>