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
require_once(dirname(__FILE__). '\update_param_common_action.class.php');

class update_param_action extends update_param_common_action
{
	protected function _define_dataspace_name()
	{
	  return 'site_param_form';
	}
}
?>