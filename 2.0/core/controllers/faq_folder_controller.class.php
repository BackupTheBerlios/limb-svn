<?php

require_once(LIMB_DIR . 'core/controllers/site_object_controller.class.php');
	
class faq_folder_controller extends site_object_controller
{
	function faq_folder_controller()
	{
		$this->_actions = array(
				'display' => array(
						'permissions_required' => 'r',
						'template_path' => '/faq_folder/display.html'
				),
				'create_faq_object' => array(
						'permissions_required' => 'w',
						'template_path' => '/faq_object/create.html',
						'action_path' => '/faq_object/create_faq_object_action',
						'JIP' => true,
						'popup' => true,
						'img_src' => '/shared/images/new.generic.gif',
						'action_name' => strings :: get('create_faq_question','faq'),

				),
				'edit' => array(
						'permissions_required' => 'w',
						'popup' => true,
						'JIP' => true,
						'action_name' => strings :: get('edit_faq_section','faq'),
						'action_path' => '/faq_folder/edit_faq_folder_action',
						'template_path' => '/faq_folder/edit.html',
						'img_src' => '/shared/images/edit.gif'
				),
				'order' => array(
						'permissions_required' => 'r',
						'action_path' => 'tree_change_order_action', 
				),
				'delete' => array(
						'permissions_required' => 'w',
						'JIP' => true,
						'popup' => true,
						'action_name' => strings :: get('delete_faq_section','faq'),
						'action_path' => '/faq_folder/delete_faq_folder_action',
						'template_path' => '/site_object/delete.html',
						'img_src' => '/shared/images/rem.gif'
				),
		);
 		
		parent :: site_object_controller();
	}
}

?>