<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: cart_handler.class.php 566 2004-09-03 14:06:08Z pachanga $
*
***********************************************************************************/

class CartHandlerInterface
{
  function reset(){}

  function getCartId(){}

  function setCartId($cart_id){}

  function addItem($new_item){}

  function getItem($id){}

  function removeItem($item_id){}

  function removeItems($item_ids){}

  function getItems(){}

  function setItems($items){}

  function countItems(){}

  function clearItems(){}
}
?>