<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: file_select_controller.class.php 21 2004-03-05 11:43:13Z server $
*
***********************************************************************************/

require_once(LIMB_DIR . 'core/controllers/site_object_controller.class.php');
	
class template_source_controller extends site_object_controller
{
	function template_source_controller()
	{
		$this->_actions = array(
				'display' => array(
						'permissions_required' => 'r',
						'action_path' => '/template_source/display_template_source_action',
						'template_path' => '/template_source/display.html',
				),
		); 		

		parent :: site_object_controller();
	}
}

?>