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

class edit_user_group_action extends form_edit_site_object_action
{
	function _define_site_object_class_name()
	{
	  return 'user_group';
	}  
	  
 	function _define_dataspace_name()
	{
	  return 'edit_user_group';
	}
}

?>