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

class create_navigation_item_action extends form_create_site_object_action
{
	function create_navigation_item_action()
	{
		$definition = array(
			'site_object' => 'navigation_item',
			'datamap' => array(
				'url' => 'url',
			)
		);
		
		parent :: form_create_site_object_action('create_navigation_item', $definition);
	}
	
	function _init_validator()
	{
		parent :: _init_validator();

		$this->validator->add_rule(new required_rule('title'));
		$this->validator->add_rule(new required_rule('url'));
	}
}

?>