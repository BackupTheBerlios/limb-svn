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
require_once(LIMB_DIR . 'core/lib/http/http_request.inc.php');
require_once(LIMB_DIR . 'core/actions/action.class.php');

class logout_action extends action
{
	var $_site_object_name = 'user_object';
	
	function logout_action($name='')
	{
		parent :: action($name);
	}
	
	function perform()
	{
		$user_object =& site_object_factory :: create($this->_site_object_name);
		$user_object->logout();
		reload('/');
	}
}

?>