<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: create_document_action.class.php 36 2004-02-29 18:57:15Z server $
*
***********************************************************************************/
require_once(LIMB_DIR . 'core/actions/form_create_site_object_action.class.php');

class create_document_action extends form_create_site_object_action
{
	function create_document_action()
	{
		$definition = array(
			'site_object' => 'document',
			'datamap' => array(
				'document_content' => 'content',
				'annotation' => 'annotation',
			)
		);
		
		parent :: form_create_site_object_action('documents_form', $definition);
	}
	
	function _init_validator()
	{
		parent :: _init_validator();

		$this->validator->add_rule(new required_rule('title'));
		$this->validator->add_rule(new required_rule('document_content'));
	}
}

?>