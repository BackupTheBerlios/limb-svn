<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: create_message_action.class.php 419 2004-02-09 15:12:03Z server $
*
***********************************************************************************/ 
require_once(LIMB_DIR . 'core/actions/form_create_site_object_action.class.php');

class create_message_action extends form_create_site_object_action
{
	function create_message_action()
	{
		$definition = array(
			'site_object' => 'message',
			'datamap' => array(
				'title' => 'title',
				'content' => 'content',
			)
		);
		
		parent :: form_create_site_object_action('create_message', $definition);
	}
	
	
	function _init_validator()
	{
		parent :: _init_validator();

		$this->validator->add_rule(new required_rule('title'));
		$this->validator->add_rule(new required_rule('content'));
	}
}

?>