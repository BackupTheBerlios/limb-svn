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
require_once(LIMB_DIR . 'core/lib/i18n/strings.class.php');
	
class cache_manager_controller extends site_object_controller
{
  function _define_default_action()
  {
    return 'admin_display';
  } 

	function _define_actions()
	{
		return array(
				'admin_display' => array(
						'permissions_required' => 'r',
						'template_path' => '/cache_manager/admin_display.html',
						'action_path' => '/cache_manager/display_cache_manager_action', 
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
				'flush_full_page_cache' => array(
						'permissions_required' => 'r',
						'action_name' => strings :: get('flush_full_page_cache', 'cache_manager'),  
						'action_path' => '/cache_manager/flush_full_page_cache_action', 
						'popup' => true
				),
				'flush_partial_page_cache' => array(
						'permissions_required' => 'r',
						'action_name' => strings :: get('flush_partial_page_cache', 'cache_manager'),  
						'action_path' => '/cache_manager/flush_partial_page_cache_action', 
						'popup' => true
				),
				'flush_image_cache' => array(
						'permissions_required' => 'r',
						'action_path' => '/cache_manager/flush_image_cache_action', 
						'action_name' => strings :: get('flush_image_cache', 'cache_manager'),  
						'popup' => true
				),
				'flush_ini_cache' => array(
						'permissions_required' => 'r',
						'action_name' => strings :: get('flush_ini_cache', 'cache_manager'),  
						'action_path' => '/cache_manager/flush_ini_cache_action', 
						'popup' => true
				),
				'flush_template_cache' => array(
						'permissions_required' => 'r',
						'action_path' => '/cache_manager/flush_template_cache_action', 
						'action_name' => strings :: get('flush_template_cache', 'cache_manager'),  
						'popup' => true
				),
				'delete' => array(
						'permissions_required' => 'w',
						'JIP' => true,
						'popup' => true,
						'action_name' => strings :: get('delete'),
						'action_path' => 'form_delete_site_object_action',
						'template_path' => '/site_object/delete.html',
						'img_src' => '/shared/images/rem.gif'
				),
		);
	}
}

?>