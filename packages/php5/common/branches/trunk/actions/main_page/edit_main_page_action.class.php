<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: limb@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(dirname(__FILE__) . '/../document/edit_document_action.class.php');

class edit_main_page_action extends edit_document_action
{
	protected function _define_site_object_class_name()
	{
	  return 'main_page';
	}  
	  
	protected function _define_dataspace_name()
	{
	  return 'documents_form';
	}
}

?>