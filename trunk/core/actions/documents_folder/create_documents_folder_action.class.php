<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: create_documents_folder_action.class.php 33 2004-03-10 16:05:12Z server $
*
***********************************************************************************/
require_once(LIMB_DIR . 'core/actions/form_create_site_object_action.class.php');

class create_documents_folder_action extends form_create_site_object_action
{
	function create_documents_folder_action()
	{
		$definition = array(
			'site_object' => 'documents_folder',
		);
		
		parent :: form_create_site_object_action('create_documents_folder', $definition);
	}
	
	function _init_validator()
	{
		parent :: _init_validator();

		$this->validator->add_rule(new required_rule('title'));
	}
}

?>