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
require_once(LIMB_DIR . 'core/actions/form_action.class.php');
require_once(LIMB_DIR . 'core/model/response/redirect_response.class.php');

class vote_action extends form_action
{
	function vote_action($name='vote_action')
	{		
		parent :: form_action($name);
	}
	
	function _valid_perform()
	{
		$object =& site_object_factory :: create('poll_container');
		$data = $this->dataspace->export();
		
		if (!isset($data['answer']))
		{
			message_box :: write_notice(strings :: get('no_answer', 'poll'));
			return new failed_response();
		}
		
		if($object->register_answer($data['answer']))
			return new redirect_response(RESPONSE_STATUS_FORM_SUBMITTED, '/root/polls');
		else
			return new failed_response();
	}

}

?>