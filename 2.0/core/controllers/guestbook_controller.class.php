<?php

require_once(LIMB_DIR . 'core/controllers/site_object_controller.class.php');
	
class guestbook_controller extends site_object_controller
{
	function guestbook_controller()
	{
		$this->_actions = array(
				'display' => array(
						'permissions_required' => 'r',
						'action_path' => '/guestbook_message/front_create_guestbook_message_action',
						'template_path' => '/guestbook/display.html'
				),
				'create_guestbook_message' => array(
						'permissions_required' => 'w',
						'template_path' => '/guestbook_message/create.html',
						'action_path' => '/guestbook_message/create_guestbook_message_action',
						'JIP' => true,
						'popup' => true,
						'img_src' => '/shared/images/new.generic.gif',
						'action_name' => strings :: get('create_message', 'guestbook'),
						'can_have_access_template' => true,
				),
		);
 		
		parent :: site_object_controller();
	}
}

?>