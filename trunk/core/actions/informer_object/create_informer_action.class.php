<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: create_informer_action.class.php 59 2004-03-22 13:54:41Z server $
*
***********************************************************************************/
require_once(LIMB_DIR . 'core/actions/form_create_site_object_action.class.php');
require_once(LIMB_DIR . 'core/lib/validators/rules/locale_date_rule.class.php');

class create_informer_action extends form_create_site_object_action
{
	function create_informer_action()
	{
		$definition = array(
			'site_object' => 'informer_object',
			'datamap' => array(
				'code' => 'code',
			)
		);
		
		parent :: form_create_site_object_action('informer_form', $definition);
	}
	
	function _init_validator()
	{
		parent :: _init_validator();

		$this->validator->add_rule(new required_rule('code'));
	}
}

?>