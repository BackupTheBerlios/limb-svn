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
require_once(LIMB_DIR . '/class/core/controllers/SiteObjectController.class.php');
	
class ControlPanelController extends SiteObjectController
{
	protected function _defineActions()
	{
		return array(
				'display' => array(
						'template_path' => '/control_panel/display.html',
						'transaction' => false,
				),
				'edit' => array(
						'popup' => true,
						'JIP' => true,
						'action_name' => Strings :: get('edit'),
						'action_path' => '/site_object/edit_action',
						'template_path' => '/site_object/edit.html',
						'img_src' => '/shared/images/edit.gif'
				),
				'delete' => array(
						'JIP' => true,
						'popup' => true,
						'action_name' => Strings :: get('delete'),
						'action_path' => 'form_delete_site_object_action',
						'template_path' => '/site_object/delete.html',
						'img_src' => '/shared/images/rem.gif'
				),
		);
	}
}

?>