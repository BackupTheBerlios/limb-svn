<?php

require_once(LIMB_DIR . 'core/controllers/site_object_controller.class.php');
require_once(LIMB_DIR . 'core/lib/locale/strings.class.php');
	
class poll_answer_controller extends site_object_controller
{
	function poll_answer_controller()
	{
		$this->_actions = array(
				'display' => array(
						'permissions_required' => 'r',
						'template_path' => '/poll_answer/display.html',
				),
				'edit' => array(
						'permissions_required' => 'w',
						'popup' => true,
						'JIP' => true,
						'action_name' => strings :: get('edit_poll_answer','poll'),
						'action_path' => '/poll_answer/edit_poll_answer_action',
						'template_path' => '/poll_answer/edit.html',
						'img_src' => '/shared/images/edit.gif'
				),
				'delete' => array(
						'permissions_required' => 'w',
						'JIP' => true,
						'popup' => true,
						'action_name' => strings :: get('delete_poll_answer','poll'),
						'action_path' => '/poll_answer/delete_poll_answer_action',
						'template_path' => '/site_object/delete.html',
						'img_src' => '/shared/images/rem.gif'
				),
		);
 		

		parent :: site_object_controller();
	}
}

?>