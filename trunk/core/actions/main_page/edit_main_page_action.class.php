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
require_once(LIMB_DIR . 'core/actions/document/edit_document_action.class.php');

class edit_main_page_action extends edit_document_action
{
	function edit_main_page_action()
	{
		$definition = array(
			'site_object' => 'main_page',
		);
		parent :: edit_document_action('documents_form', $definition);
	}
	
}

?>