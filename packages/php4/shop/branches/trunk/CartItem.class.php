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
require_once(LIMB_DIR . '/class/Object.class.php');

class CartItem extends Object
{
  function CartItem($id)
  {
    parent :: Object();

    $this->_setId($id);

    //important!!!
    $this->__session_class_path = addslashes(__FILE__);
  }

  function _setId($id)
  {
    $this->set('id', $id);
  }

  function getId()
  {
    return (int)$this->get('id');
  }

  function getPrice()
  {
    return 1*$this->get('price', 0);
  }

  function setPrice($price)
  {
    $this->set('price', $price);
  }

  function getAmount()
  {
    return 1*$this->get('amount', 0);
  }

  function setAmount($amount)
  {
    $this->set('amount', $amount);
  }

  function getDescription()
  {
    return $this->get('description');
  }

  function setDescription($description)
  {
    $this->set('description', $description);
  }

  function getSumm()
  {
    return $this->getAmount() * $this->getPrice();
  }

  function summAmount($item)
  {
    $this->setAmount($this->getAmount() + $item->getAmount());
  }
}

?>