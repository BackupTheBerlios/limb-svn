<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: site_structure_controller.class.php 21 2004-03-05 11:43:13Z server $
*
***********************************************************************************/
require_once(LIMB_DIR . 'core/controllers/site_object_controller.class.php');
require_once(LIMB_DIR . 'core/lib/locale/strings.class.php');
	
class version_controller extends site_object_controller
{
	function version_controller()
	{
		$this->_actions = array(
				'display' => array(
						'permissions_required' => 'r',
						'template_path' => '/version/display.html',
				),
				'recover' => array(
						'permissions_required' => 'r',
						'action_path' => '/version/recover_version_action', 
						'popup' => true
				)
		);
 		

		parent :: site_object_controller();
	}
}

?>