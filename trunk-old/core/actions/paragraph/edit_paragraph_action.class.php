<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: edit_photo_action.class.php 21 2004-02-29 18:59:25Z server $
*
***********************************************************************************/
require_once(LIMB_DIR . 'core/actions/form_edit_site_object_action.class.php');

class edit_paragraph_action extends form_edit_site_object_action
{
	function edit_paragraph_action()
	{
		$definition = array(
			'site_object' => 'paragraph',
			'datamap' => array(
				'paragraph_content' => 'content',
			)
		);

		parent :: form_edit_site_object_action('paragraph_form', $definition);
	}

	function _init_validator()
	{
		parent :: _init_validator();
		
		$this->validator->add_rule(new required_rule('paragraph_content'));
	}
	
}

?>