<?php

require_once(LIMB_DIR . 'core/controllers/site_object_controller.class.php');
require_once(LIMB_DIR . 'core/lib/locale/strings.class.php');
	
class not_found_page_controller extends site_object_controller
{
	function not_found_page_controller()
	{
		$this->_actions = array(
				'display' => array(
						'permissions_required' => 'r',
						'template_path' => '/not_found/display.html',
						'transaction' => false,
				),
		);

		parent :: site_object_controller();
	}
}

?>