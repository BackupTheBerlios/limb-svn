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
require_once(LIMB_DIR . 'core/actions/form_create_site_object_action.class.php');

class create_user_group_action extends form_create_site_object_action
{
	function create_user_group_action()
	{
		$definition = array(
			'site_object' => 'user_group',
			'datamap' => array(
				'title' => 'title',
			)
		);

		parent :: form_create_site_object_action('create_user_group', $definition);
	}
	
	function _init_validator()
	{
		parent :: _init_validator();
		
		$this->validator->add_rule(new required_rule('title'));
	}
}

?>