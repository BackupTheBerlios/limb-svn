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

class edit_document_action extends form_edit_site_object_action
{
	function edit_document_action($name = 'documents_form', $merge_definition = array())
	{
		$definition = array(
			'site_object' => 'document',
			'datamap' => array(
				'document_content' => 'content',
				'annotation' => 'annotation',
			)
		);

		parent :: form_edit_site_object_action(
					$name, 
					complex_array :: array_merge($definition, $merge_definition)
		);
	}
	
	function _init_validator()
	{
		parent :: _init_validator();
		
		$this->validator->add_rule(new required_rule('title'));
		$this->validator->add_rule(new required_rule('document_content'));
	}
}

?>