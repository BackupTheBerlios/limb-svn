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
require_once(LIMB_DIR . '/core/model/shop/handlers/cart_handler.class.php');
require_once(LIMB_DIR . '/core/lib/system/objects_support.inc.php');
require_once(LIMB_DIR . '/core/lib/session/session.class.php');

class session_cart_handler extends cart_handler
{			
	function session_cart_handler($cart_id)
	{
	  parent :: cart_handler($cart_id);		
	}
	
	function reset()
	{
		$this->_items =& session :: get('session_cart_' . $this->_cart_id . '_items');
		
		if(!is_array($this->_items))
		  $this->_items = array();	
	}
}
?>