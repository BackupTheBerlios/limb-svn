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
require_once(LIMB_DIR . '/core/actions/document/edit_document_action.class.php');

class edit_main_page_action extends edit_document_action
{
	function _define_site_object_class_name()
	{
	  return 'main_page';
	}  
	  
	function _define_dataspace_name()
	{
	  return 'documents_form';
	}
}

?>