<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: activate_password_action.class.php 401 2004-02-04 15:40:14Z server $
*
***********************************************************************************/ 
require_once(LIMB_DIR . 'core/lib/http/http_request.inc.php');
require_once(LIMB_DIR . 'core/actions/action.class.php');

class activate_password_action extends action
{
	function activate_password_action($name='')
	{
		parent :: action($name);
	}
	
	function perform()
	{
		$object =& site_object_factory :: create('user_object');
		if(!$object->activate_password())
		{
			message_box :: write_notice('Password activation failed!');
			reload('/');
		}
		
		return true;
	}
}

?>