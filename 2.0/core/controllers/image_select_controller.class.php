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
	
class image_select_controller extends site_object_controller
{
	function image_select_controller()
	{
		$this->_actions = array(
				'display' => array(
						'permissions_required' => 'r',
						'template_path' => '/image_select/display.html',
						'popup' => true,
						'trasaction' => false
				),
		); 		

		parent :: site_object_controller();
	}
}

?>