<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: news_folder_controller.class.php 21 2004-03-05 11:43:13Z server $
*
***********************************************************************************/
require_once(LIMB_DIR . 'core/controllers/site_object_controller.class.php');
	
class simple_orders_folder_controller extends site_object_controller
{
	function simple_orders_folder_controller()
	{
		$this->_actions = array(
				'display' => array(
						'permissions_required' => 'r',
						'template_path' => '/simple_orders_folder/admin_display.html'
				),
		);
 		
		parent :: site_object_controller();
	}
}

?>