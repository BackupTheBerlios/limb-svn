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

class create_poll_answer_action extends form_create_site_object_action
{
	function _define_site_object_class_name()
	{
	  return 'poll_answer';
	}  
	  
	function _define_dataspace_name()
	{
	  return 'create_poll_answer';
	}
}

?>