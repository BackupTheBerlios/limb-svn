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
	
class poll_container_controller extends site_object_controller
{
	protected function _define_actions()
	{
		return array(
				'display' => array(
						'template_path' => '/poll_container/display.html'
				),
				'admin_display' => array(
						'template_path' => '/poll_container/admin_display.html'
				),
				'create_poll' => array(
						'template_path' => '/poll/create.html',
						'action_path' => '/poll/create_poll_action',
						'JIP' => true,
						'popup' => true,
						'img_src' => '/shared/images/new.generic.gif',
						'action_name' => strings :: get('create_poll_question','poll'),
						'can_have_access_template' => true,
				),
				'delete' => array(
						'JIP' => true,
						'popup' => true,
						'action_name' => strings :: get('delete'),
						'action_path' => 'form_delete_site_object_action',
						'template_path' => '/site_object/delete.html',
						'img_src' => '/shared/images/rem.gif'
				),
				'vote' => array(
						'action_path' => '/poll_container/vote_action',
						'template_path' => '/poll_container/display.html'
				),
		);
	}
}

?>