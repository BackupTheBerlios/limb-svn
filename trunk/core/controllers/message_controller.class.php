<?php

require_once(LIMB_DIR . 'core/controllers/site_object_controller.class.php');
	
class message_controller extends site_object_controller
{
	function message_controller()
	{
		$this->_actions = array(
				'display' => array(
						'permissions_required' => 'r',
						'template_path' => '/message/display.html'
				),
				'create_message' => array(
						'permissions_required' => 'w',
						'template_path' => '/message/create.html',
						'action_path' => '/message/create_message_action',
						'JIP' => true,
						'popup' => true,
						'img_src' => '/shared/images/new.generic.gif',
						'action_name' => strings :: get('create_message','message'),
						'can_have_access_template' => true,
				),
				'edit' => array(
						'permissions_required' => 'w',
						'popup' => true,
						'JIP' => true,
						'action_name' => strings :: get('edit_message','message'),
						'action_path' => '/message/edit_message_action',
						'template_path' => '/message/edit.html',
						'img_src' => '/shared/images/edit.gif'
				),
				'delete' => array(
						'permissions_required' => 'w',
						'JIP' => true,
						'popup' => true,
						'action_name' => strings :: get('delete'),
						'action_path' => '/message/delete_message_action',
						'template_path' => '/site_object/delete.html',
						'img_src' => '/shared/images/rem.gif'
				),
		);
 		
		parent :: site_object_controller();
	}
}

?>