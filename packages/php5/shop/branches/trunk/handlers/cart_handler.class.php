<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once dirname(__FILE__) . '/cart_handler_interface.interface.php';

class cart_handler implements cart_handler_interface
{
  protected $_cart_id = null;
  protected $_items = array();

  function __construct($cart_id)
  {
    $this->_cart_id = $cart_id;
  }

  public function reset()
  {
    $this->clear_items();
  }

  public function get_cart_id()
  {
    return $this->_cart_id;
  }

  public function set_cart_id($cart_id)
  {
    $this->_cart_id = $cart_id;
  }

  public function add_item($new_item)
  {
    $id = $new_item->get_id();

    if ($new_item->get_amount() <= 0)
      $new_item->set_amount(1);

    if (isset($this->_items[$id]))
      $new_item->summ_amount($this->_items[$id]);

    $this->_items[$id] = $new_item;
  }

  public function get_item($id)
  {
    if(isset($this->_items[$id]))
      return $this->_items[$id];
    else
      return false;
  }

  public function remove_item($item_id)
  {
    if (isset($this->_items[$item_id]))
      unset($this->_items[$item_id]);
  }

  public function remove_items($item_ids)
  {
    foreach($item_ids as $id)
      $this->remove_item($id);
  }

  public function get_items()
  {
    return $this->_items;
  }

  public function set_items($items)
  {
    $this->_items = $items;
  }

  public function count_items()
  {
    if (is_array($this->_items))
      return count($this->_items);
    else
      return 0;
  }

  public function clear_items()
  {
    $this->_items = array();
  }
}
?>