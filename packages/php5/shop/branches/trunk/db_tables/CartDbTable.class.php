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
require_once(LIMB_DIR  . '/class/lib/db/DbTable.class.php');

class CartDbTable extends DbTable
{
  protected function _defineColumns()
  {
    return array(
      'id' => array('type' => 'numeric'),
      'user_id' => array('type' => 'numeric'),
      'cart_id' => '',
      'cart_items' => array('type' => 'clob'),
      'last_activity_time' => array('type' => 'numeric')
    );
  }
}

?>