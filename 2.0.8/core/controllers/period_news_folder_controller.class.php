<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: school_news_folder_controller.class.php 33 2004-03-10 16:05:12Z server $
*
***********************************************************************************/
require_once(LIMB_DIR . 'core/controllers/site_object_controller.class.php');
	
class period_news_folder_controller extends site_object_controller
{
	function period_news_folder_controller()
	{
		$this->_actions = array(
				'display' => array(
						'permissions_required' => 'r',
						'template_path' => '/period_news_folder/display.html'
				),
				'admin_display' => array(
						'permissions_required' => 'rw',
						'template_path' => '/period_news_folder/admin_display.html'
				),
				'create_news' => array(
						'permissions_required' => 'w',
						'template_path' => '/period_news_object/create.html',
						'action_path' => '/period_news/create_period_news_action',
						'JIP' => true,
						'popup' => true,
						'img_src' => '/shared/images/new.generic.gif',
						'action_name' => strings :: get('create_newsline', 'newsline'),
						'can_have_access_template' => true,
				),
				'edit' => array(
						'permissions_required' => 'w',
						'popup' => true,
						'JIP' => true,
						'action_name' => strings :: get('edit'),
						'action_path' => '/site_object/edit_action',
						'template_path' => '/site_object/edit.html',
						'img_src' => '/shared/images/edit.gif'
				),
		);
 		
		parent :: site_object_controller();
	}
}

?>