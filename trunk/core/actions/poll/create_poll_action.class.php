<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: create_poll_action.class.php 467 2004-02-18 10:16:31Z mike $
*
***********************************************************************************/ 
require_once(LIMB_DIR . 'core/actions/form_create_site_object_action.class.php');

class create_poll_action extends form_create_site_object_action
{
	function create_poll_action()
	{
		$definition = array(
			'site_object' => 'poll',
			'datamap' => array(
				'start_date' => 'start_date',
				'finish_date' => 'finish_date',
				'restriction' => 'restriction',
			)
		);
		
		parent :: form_create_site_object_action('create_poll', $definition);
	}
	
	
	function _init_validator()
	{
		parent :: _init_validator();

		$this->validator->add_rule(new required_rule('start_date'));
		$this->validator->add_rule(new required_rule('finish_date'));
		$this->validator->add_rule(new required_rule('restriction'));
	}
}

?>