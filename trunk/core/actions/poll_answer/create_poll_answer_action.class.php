<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: create_poll_answer_action.class.php 467 2004-02-18 10:16:31Z mike $
*
***********************************************************************************/ 
require_once(LIMB_DIR . 'core/actions/form_create_site_object_action.class.php');

class create_poll_answer_action extends form_create_site_object_action
{
	function create_poll_answer_action()
	{
		$definition = array(
			'site_object' => 'poll_answer'
		);
		
		parent :: form_create_site_object_action('create_poll_answer', $definition);
	}	
}

?>