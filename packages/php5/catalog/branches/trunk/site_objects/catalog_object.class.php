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
require_once(LIMB_DIR . '/class/core/site_objects/content_object.class.php');
require_once(LIMB_SHOP_DIR . 'cart_item.class.php');

class catalog_object extends content_object
{
	public function get_cart_item()
	{
		$cart_item = new cart_item($this->get_node_id());
		
		$cart_item->set_description($this->get_title());
		$cart_item->set_attribute('image_id', $this->get_attribute('image_id'));
		
		return $cart_item;
	}
}

?>