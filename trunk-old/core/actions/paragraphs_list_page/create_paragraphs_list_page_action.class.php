<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: create_photogallery_folder_action.class.php 21 2004-02-29 18:59:25Z server $
*
***********************************************************************************/
require_once(LIMB_DIR . 'core/actions/form_create_site_object_action.class.php');

class create_paragraphs_list_page_action extends form_create_site_object_action
{
	function create_paragraphs_list_page_action()
	{
		$definition = array(
			'site_object' => 'paragraphs_list_page',
		);
		
		parent :: form_create_site_object_action('paragraphs_list_page_form', $definition);
	}
	
	function _init_validator()
	{
		parent :: _init_validator();

		$this->validator->add_rule(new required_rule('title'));
	}
}

?>