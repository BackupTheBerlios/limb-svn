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
	
class informer_object_controller extends site_object_controller
{
	function informer_object_controller()
	{
		$this->_actions = array(
				'display' => array(
						'permissions_required' => 'r',
						'template_path' => '/informer_object/display.html',
				),
				'admin_detail' => array(
						'permissions_required' => 'r',
						'template_path' => '/admin/object_detail_info.html',
						'popup' => true,
						'JIP' => true,
						'img_src' => '/shared/images/admin_detail.gif',
						'action_name' => strings :: get('detail_info'),
				),
				'edit' => array(
						'permissions_required' => 'w',
						'popup' => true,
						'JIP' => true,
						'action_name' => strings :: get('edit_informer', 'informer'),
						'action_path' => '/informer_object/edit_informer_action',
						'template_path' => '/informer_object/edit.html',
						'img_src' => '/shared/images/edit.gif'
				),
				'publish' => array(
						'permissions_required' => 'w',
						'popup' => true,
						'JIP' => true,
						'action_name' => strings :: get('publish'),
						'action_path' => '/doc_flow_object/set_publish_status_action',
						'img_src' => '/shared/images/publish.gif',
						'can_have_access_template' => true,
				),
				'unpublish' => array(
						'permissions_required' => 'w',
						'popup' => true,
						'JIP' => true,
						'action_name' => strings :: get('unpublish'),
						'action_path' => '/doc_flow_object/set_publish_status_action',
						'img_src' => '/shared/images/unpublish.gif',
						'can_have_access_template' => true,
				),
				'delete' => array(
						'permissions_required' => 'w',
						'JIP' => true,
						'popup' => true,
						'action_name' => strings :: get('delete_informer', 'informer'),
						'action_path' => '/informer_object/delete_informer_action',
						'template_path' => '/site_object/delete.html',
						'img_src' => '/shared/images/rem.gif'
				),
		);
 		

		parent :: site_object_controller();
	}
}

?>