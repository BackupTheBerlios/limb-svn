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
require_once(dirname(__FILE__) . '/CartHandler.class.php');
require_once(LIMB_DIR . '/core/system/objects_support.inc.php');

class DbCartHandler extends CartHandler
{
  var $cart_db_table = null;

  function DbCartHandler($cart_id)
  {
    parent :: CartHandler($cart_id);

    $toolkit =& Limb :: toolkit();
    $this->cart_db_table =& $toolkit->createDBTable('Cart');

    register_shutdown_function(array($this, '_dbCartHandler'));
  }

  function reset()
  {
    $this->clearItems();

    $this->_loadItemsForVisitor();

    $user =& $this->_getUser();
    if($user->isLoggedIn())
    {
      $this->_loadItemsForUser();
    }
  }

  function _loadItemsForUser()
  {
    $user =& $this->_getUser();

    $conditions = 'user_id = ' . $user->getId() . ' AND cart_id <> "'. $this->_cart_id . '"';

    if (!$this->_loadItemsByConditions($conditions))
      return;

    $this->cart_db_table->delete($conditions);
  }

  function _loadItemsForVisitor()
  {
    $conditions = array(
      'cart_id' => $this->_cart_id
    );

    return $this->_loadItemsByConditions($conditions);
  }

  function _loadItemsByConditions($conditions)
  {
    if($arr = $this->cart_db_table->getList($conditions))
    {
      $record = reset($arr);
      $items = unserialize($record['cart_items']);

      foreach(array_keys($items) as $key)
        $this->addItem($items[$key]);

      return true;
    }

    return false;
  }

  function &_getUser()
  {
    $toolkit =& Limb :: toolkit();
    return $toolkit->getUser();
  }

  function _dbCartHandler()
  {
    $user =& $this->_getUser();

    $cart_data = array(
      'user_id' => $user->getId(),
      'last_activity_time' => time(),
      'cart_items' => serialize($this->getItems()),
      'cart_id' => $this->_cart_id,
    );

    $conditions['cart_id'] = $this->_cart_id;
    $records = $this->cart_db_table->getList($conditions);

    if (!count($records))
    {
      $this->cart_db_table->insert($cart_data);
    }
    else
    {
      $record = reset($records);
      $this->cart_db_table->updateById($record['id'], $cart_data);
    }
  }
}
?>