<?php

require_once(LIMB_DIR . 'core/controllers/site_object_controller.class.php');
	
class poll_controller extends site_object_controller
{
	function poll_controller()
	{
		$this->_actions = array(
				'display' => array(
						'permissions_required' => 'r',
						'template_path' => '/poll/display.html'
				),
				'create_answer' => array(
						'permissions_required' => 'w',
						'template_path' => '/poll_answer/create.html',
						'action_path' => '/poll_answer/create_poll_answer_action',
						'JIP' => true,
						'popup' => true,
						'img_src' => '/shared/images/new.generic.gif',
						'action_name' => strings :: get('create_poll_answer','poll'),
						'can_have_access_template' => true, 
				),
				'edit' => array(
						'permissions_required' => 'w',
						'popup' => true,
						'JIP' => true,
						'action_name' => strings :: get('edit_poll_question','poll'),
						'action_path' => '/poll/edit_poll_action',
						'template_path' => '/poll/edit.html',
						'img_src' => '/shared/images/edit.gif'
				),
				'delete' => array(
						'permissions_required' => 'w',
						'JIP' => true,
						'popup' => true,
						'action_name' => strings :: get('delete_poll_question','poll'),
						'action_path' => '/poll/delete_poll_action',
						'template_path' => '/site_object/delete.html',
						'img_src' => '/shared/images/rem.gif'
				),
		);
 		
		parent :: site_object_controller();
	}
}

?>