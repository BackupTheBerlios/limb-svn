<?php

require_once(LIMB_DIR . 'core/controllers/site_object_controller.class.php');
	
class file_select_controller extends site_object_controller
{
	function file_select_controller()
	{
		$this->_actions = array(
				'display' => array(
						'permissions_required' => 'r',
						'template_path' => '/file_select/display.html',
						'popup' => true,
				),
		); 		

		parent :: site_object_controller();
	}
}

?>