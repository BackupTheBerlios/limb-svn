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
require_once(LIMB_DIR . '/class/core/Object.class.php');

class CartItem extends Object
{
  public function __construct($id)
  {
    parent :: __construct();

    $this->_setId($id);

    //important!!!
    $this->__session_class_path = addslashes(__FILE__);
  }

  protected function _setId($id)
  {
    $this->set('id', $id);
  }

  public function getId()
  {
    return (int)$this->get('id');
  }

  public function getPrice()
  {
    return 1*$this->get('price', 0);
  }

  public function setPrice($price)
  {
    $this->set('price', $price);
  }

  public function getAmount()
  {
    return 1*$this->get('amount', 0);
  }

  public function setAmount($amount)
  {
    $this->set('amount', $amount);
  }

  public function getDescription()
  {
    return $this->get('description');
  }

  public function setDescription($description)
  {
    $this->set('description', $description);
  }

  public function getSumm()
  {
    return $this->getAmount() * $this->getPrice();
  }

  public function summAmount($item)
  {
    $this->setAmount($this->getAmount() + $item->getAmount());
  }
}

?>