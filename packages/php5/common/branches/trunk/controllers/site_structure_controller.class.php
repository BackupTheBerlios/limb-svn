<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: limb@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . 'class/core/controllers/site_object_controller.class.php');
	
class site_structure_controller extends site_object_controller
{
  protected function _define_default_action()
  {
		return 'admin_display';
	} 
	
	protected function _define_actions()
	{
		return array(
				'admin_display' => array(
						'template_path' => '/site_structure/admin_display.html',
						'action_path' => '/site_structure/tree_display_action',
				),
				'toggle' => array(
						'template_path' => '/site_structure/admin_display.html',						
						'action_path' => '/site_structure/tree_toggle_action', 
				),
				'edit' => array(
						'popup' => true,
						'JIP' => true,
						'action_name' => strings :: get('edit'),
						'action_path' => '/site_object/edit_action',
						'template_path' => '/site_object/edit.html',
						'img_src' => '/shared/images/edit.gif'
				),
				'node_select' => array(
						'action_name' => strings :: get('select_node', 'site_structure'),
						'action_path' => '/site_structure/node_select_action',
						'template_path' => '/site_structure/node_select.html',
				),
				'save_priority' => array(
						'action_path' => '/site_structure/save_priority_action', 
						'popup' => true,
				),
				'multi_delete' => array(
						'action_path' => '/site_structure/multi_delete_action', 
						'template_path' => '/site_structure/multi_delete.html',
						'popup' => true,
				),
				'multi_toggle_publish_status' => array(
						'action_path' => '/site_structure/multi_toggle_publish_status_action', 
						'popup' => true,
				),
				'delete' => array(
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