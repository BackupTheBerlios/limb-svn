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
require_once(dirname(__FILE__) . '/../../simple_authenticator.class.php');

class simple_authenticator_component extends component
{
	public function is_user_in_groups($groups)
	{
	  if ((Limb :: toolkit()->getUser()->is_logged_in()) && simple_authenticator :: is_user_in_groups($groups))
      return true;
	}
	
} 

?>