<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: delete_announce_folder_action.class.php 245 2004-03-05 12:11:42Z server $
*
***********************************************************************************/
require_once(LIMB_DIR . 'core/actions/form_delete_site_object_action.class.php');

class delete_announce_folder_action extends form_delete_site_object_action
{
	function delete_announce_folder_action($name='delete_form')
	{		
		parent :: form_delete_site_object_action($name, array('site_object' => 'announce_folder'));
	}
}

?>