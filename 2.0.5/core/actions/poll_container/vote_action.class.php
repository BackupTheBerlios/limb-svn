<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: vote_action.class.php 401 2004-02-04 15:40:14Z server $
*
***********************************************************************************/ 
require_once(LIMB_DIR . 'core/actions/form_action.class.php');

class vote_action extends form_action
{
	function vote_action($name='vote_action')
	{		
		parent :: form_action($name);
	}
	
	function _valid_perform()
	{
		$object =& site_object_factory :: create('poll_container');
		
		$data = $this->_export();
		
		if (!isset($data['answer']))
		{
			message_box :: write_notice(strings :: get('no_answer', 'poll'));
			return false;
		}
		
		return $object->register_answer($data['answer']);
	}

}

?>