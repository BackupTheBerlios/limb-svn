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

class edit_pricelist_object_action extends form_edit_site_object_action
{
	function edit_pricelist_object_action()
	{
		$definition = array(
			'site_object' => 'pricelist_object',
			'datamap' => array(
				'object_content' => 'content',
				'file_id' => 'file_id'
			)
		);

		parent :: form_edit_site_object_action('pricelist_object_form', $definition);
	}
}

?>