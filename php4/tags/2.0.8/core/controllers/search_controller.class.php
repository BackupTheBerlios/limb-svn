<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: search_controller.class.php 245 2004-03-05 12:11:42Z server $
*
***********************************************************************************/
require_once(LIMB_DIR . 'core/controllers/site_object_controller.class.php');
require_once(LIMB_DIR . 'core/lib/locale/strings.class.php');
	
class search_controller extends site_object_controller
{
	function search_controller()
	{
		$this->_actions = array(
				'display' => array(
						'permissions_required' => 'r',
						'template_path' => '/search/display.html',
						'transaction' => false,
				),
				'fulltext_search' => array(
						'permissions_required' => 'r',
						'template_path' => '/search/fulltext_search.html',
						'transaction' => false,
						'action_path' => '/search_action'
				),
		);
 		

		parent :: site_object_controller();
	}
}

?>