<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: edit_files_folder_action.class.php 419 2004-02-09 15:12:03Z server $
*
***********************************************************************************/ 
require_once(LIMB_DIR . 'core/actions/form_edit_site_object_action.class.php');

class edit_files_folder_action extends form_edit_site_object_action
{
	function edit_files_folder_action()
	{
		$definition = array(
			'site_object' => 'files_folder',
			'datamap' => array(
				'title' => 'title',
			)
		);
		
		parent :: form_edit_site_object_action('edit_files_folder', $definition);
	}
	
	
	function _init_validator()
	{
		parent :: _init_validator();

		$this->validator->add_rule(new required_rule('title'));
	}
}

?>