<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: news_object_controller.class.php 21 2004-03-05 11:43:13Z server $
*
***********************************************************************************/

require_once(LIMB_DIR . 'core/controllers/site_object_controller.class.php');
require_once(LIMB_DIR . 'core/lib/locale/strings.class.php');
	
class simple_order_object_controller extends site_object_controller
{
	function simple_order_object_controller()
	{
		$this->_actions = array(
				'display' => array(
						'permissions_required' => 'r',
						'template_path' => '/simple_order_object/display.html',
				),
				'view' => array(
						'permissions_required' => 'r',
						'popup' => true,
						'JIP' => true,
						'action_name' => strings :: get('detail_info'),
						'template_path' => '/simple_order_object/view.html',
						'img_src' => '/shared/images/admin_detail.gif'
				),
				'delete' => array(
						'permissions_required' => 'w',
						'JIP' => true,
						'popup' => true,
						'action_name' => strings :: get('delete'),
						'action_path' => '/simple_order_object/delete_simple_order_object_action',
						'template_path' => '/site_object/delete.html',
						'img_src' => '/shared/images/rem.gif'
				),
		);
 		

		parent :: site_object_controller();
	}
}

?>