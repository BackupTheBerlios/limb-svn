<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: create_announce_action.class.php 245 2004-03-05 12:11:42Z server $
*
***********************************************************************************/
require_once(LIMB_DIR . 'core/actions/form_create_site_object_action.class.php');
require_once(LIMB_DIR . 'core/lib/validators/rules/locale_date_rule.class.php');

class create_announce_action extends form_create_site_object_action
{
	function create_announce_action()
	{
		$definition = array(
			'site_object' => 'announce_object',
			'datamap' => array(
				'annotation' => 'annotation',
				'image_id' => 'image_id',
				'url' => 'url',
				'start_date' => 'start_date',
				'finish_date' => 'finish_date',
			)
		);
		
		parent :: form_create_site_object_action('announce_form', $definition);
	}
	
	function _init_validator()
	{
		parent :: _init_validator();

		$this->validator->add_rule(new required_rule('annotation'));
		$this->validator->add_rule(new required_rule('start_date'));
		$this->validator->add_rule(new locale_date_rule('start_date'));
		$this->validator->add_rule(new required_rule('finish_date'));
		$this->validator->add_rule(new locale_date_rule('finish_date'));
	}
}

?>