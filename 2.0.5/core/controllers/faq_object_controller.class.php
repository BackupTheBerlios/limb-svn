<?php

require_once(LIMB_DIR . 'core/controllers/site_object_controller.class.php');
require_once(LIMB_DIR . 'core/lib/locale/strings.class.php');
	
class faq_object_controller extends site_object_controller
{
	function faq_object_controller()
	{
		$this->_actions = array(
				'display' => array(
						'permissions_required' => 'r',
						'template_path' => '/faq_object/display.html',
				),
				'edit' => array(
						'permissions_required' => 'w',
						'popup' => true,
						'JIP' => true,
						'action_name' => strings :: get('edit_faq_question', 'faq'),
						'action_path' => '/faq_object/edit_faq_object_action',
						'template_path' => '/faq_object/edit.html',
						'img_src' => '/shared/images/edit.gif'
				),
				'delete' => array(
						'permissions_required' => 'w',
						'JIP' => true,
						'popup' => true,
						'action_name' => strings :: get('delete_faq_question','faq'),
						'action_path' => '/faq_object/delete_faq_object_action',
						'template_path' => '/site_object/delete.html',
						'img_src' => '/shared/images/rem.gif'
				),
				'order' => array(
						'permissions_required' => 'r',
						'action_path' => 'tree_change_order_action', 
				),

		);
 		

		parent :: site_object_controller();
	}
}

?>