<?php

require_once(LIMB_DIR . 'core/controllers/site_object_controller.class.php');
	
class faq_folder_container_controller extends site_object_controller
{
	function faq_folder_container_controller()
	{
		$this->_actions = array(
				'display' => array(
						'permissions_required' => 'r',
						'template_path' => '/faq_folder_container/display.html'
				),
				'create_faq_folder' => array(
						'permissions_required' => 'w',
						'template_path' => '/faq_folder/create.html',
						'action_path' => '/faq_folder/create_faq_folder_action',
						'JIP' => true,
						'popup' => true,
						'img_src' => '/shared/images/new.folder.gif',
						'action_name' => strings :: get('create_faq_folder','faq'),

				),
		);
 		
		parent :: site_object_controller();
	}
}

?>