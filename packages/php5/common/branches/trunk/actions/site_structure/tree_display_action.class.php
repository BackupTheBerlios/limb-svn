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
require_once(LIMB_DIR . '/class/core/actions/action.class.php');

class tree_display_action extends action
{
	public function perform($request, $response)
	{
		$parents =& Limb :: toolkit()->getSession()->get_reference('tree_expanded_parents');
		Limb :: toolkit()->getTree()->set_expanded_parents($parents);
	}
}

?>