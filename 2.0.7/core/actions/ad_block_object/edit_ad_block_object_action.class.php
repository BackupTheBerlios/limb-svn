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
require_once(LIMB_DIR . 'core/actions/form_edit_site_object_action.class.php');

class edit_ad_block_object_action extends form_edit_site_object_action
{
	function edit_ad_block_object_action()
	{
		$definition = array(
			'site_object' => 'ad_block_object',
			'datamap' => array(
				'image_id' => 'image_id',
			)
		);

		parent :: form_edit_site_object_action('ad_block_form', $definition);
	}
	
	function _init_validator()
	{
		parent :: _init_validator();
		
		$this->validator->add_rule(new required_rule('title'));
	}
}

?>