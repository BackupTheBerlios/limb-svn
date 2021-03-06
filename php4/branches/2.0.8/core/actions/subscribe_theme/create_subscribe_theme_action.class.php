<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: create_subscribe_theme_action.class.php 245 2004-03-05 12:11:42Z server $
*
***********************************************************************************/
require_once(LIMB_DIR . 'core/actions/form_create_site_object_action.class.php');

class create_subscribe_theme_action extends form_create_site_object_action
{
	function create_subscribe_theme_action()
	{
		$definition = array(
			'site_object' => 'subscribe_theme',
			'datamap' => array(
				'title' => 'title',
				'mail_template' => 'mail_template',
			)
		);
		
		parent :: form_create_site_object_action('create_subscribe_theme', $definition);
	}
	
	function _init_validator()
	{
		parent :: _init_validator();

		$this->validator->add_rule(new required_rule('title'));
		$this->validator->add_rule(new required_rule('mail_template'));
	}
}

?>