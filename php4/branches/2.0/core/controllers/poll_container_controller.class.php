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
	
class poll_container_controller extends site_object_controller
{
	function poll_container_controller()
	{
		$this->_actions = array(
				'display' => array(
						'permissions_required' => 'r',
						'template_path' => '/poll_container/display.html'
				),
				'create_poll' => array(
						'permissions_required' => 'w',
						'template_path' => '/poll/create.html',
						'action_path' => '/poll/create_poll_action',
						'JIP' => true,
						'popup' => true,
						'img_src' => '/shared/images/new.generic.gif',
						'action_name' => strings :: get('create_poll_question','poll'),
				),
				'vote' => array(
						'permissions_required' => 'r',
						'template_path' => '/poll_container/display_active_poll.html',
						'action_path' => '/poll_container/vote_action',
				),
		);
 		
		parent :: site_object_controller();
	}
}

?>