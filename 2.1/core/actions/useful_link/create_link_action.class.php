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
require_once(LIMB_DIR . 'core/lib/validators/rules/url_rule.class.php');

class create_link_action extends form_create_site_object_action
{
	function create_link_action()
	{
		$definition = array(
			'site_object' => 'useful_link',
			'datamap' => array(
				'annotation' => 'annotation',
				'image_id' => 'image_id',
				'uri' => 'uri',
			)
		);
		
		parent :: form_create_site_object_action('useful_link_form', $definition);
	}
	
	function _init_validator()
	{
		parent :: _init_validator();

		$this->validator->add_rule(new required_rule('annotation'));
		$this->validator->add_rule(new required_rule('uri'));
		$this->validator->add_rule(new required_rule('title'));
		$this->validator->add_rule(new url_rule('uri'));
	}
}

?>