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
require_once(LIMB_DIR . '/class/core/controllers/site_object_controller.class.php');
	
class main_page_controller extends site_object_controller
{
	protected function _define_actions()
	{
		return array(
				'display' => array(
						'template_path' => '/main_page.html',
						'transaction' => false,
				),
				'admin_display' => array(
						'template_path' => '/document/admin_display.html',
						'transaction' => false,
				),
				'create_document' => array(
						'template_path' => '/document/create.html',
						'action_path' => '/document/create_document_action',
						'JIP' => true,
						'popup' => true,
						'action_name' => strings :: get('create_document', 'document'),						
						'img_src' => '/shared/images/new.generic.gif',
						'can_have_access_template' => true, 
				),
				'set_metadata' => array(
						'popup' => true,
						'JIP' => true,
						'action_name' => strings :: get('set_metadata'),
						'action_path' => '/site_object/set_metadata_action',
						'template_path' => '/site_object/set_metadata.html',
						'img_src' => '/shared/images/configure.gif'
				),
				'edit' => array(
						'popup' => true,
						'JIP' => true,
						'action_name' => strings :: get('edit'),
						'action_path' => '/main_page/edit_main_page_action',
						'template_path' => '/main_page/edit.html',
						'img_src' => '/shared/images/edit.gif'
				),
		);
	}
}

?>