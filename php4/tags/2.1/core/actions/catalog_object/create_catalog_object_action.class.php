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

class create_catalog_object_action extends form_create_site_object_action
{
	function create_catalog_object_action()
	{
		$definition = array(
			'site_object' => 'catalog_object',
			'datamap' => array(
				'annotation' => 'annotation',
				'object_content' => 'content',
				'image_id' => 'image_id'
			)
		);
		
		parent :: form_create_site_object_action('catalog_object_form', $definition);
	}
	
	function _init_validator()
	{
		parent :: _init_validator();

		$this->validator->add_rule(new required_rule('annotation'));
		$this->validator->add_rule(new required_rule('object_content'));
	}
}

?>