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

class SessionCartHandler extends CartHandler
{
  function reset()
  {
    $toolkit =& Limb :: toolkit();
    $session =& $toolkit->getSession();

    $this->_items =& $session->getReference('session_cart_' . $this->_cart_id . '_items');

    if(!is_array($this->_items))
      $this->clearItems();
  }
}
?>