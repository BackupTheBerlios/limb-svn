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
require_once(LIMB_DIR . '/class/lib/db/DbTable.class.php');

class SysBehaviourDbTable extends DbTable
{
  protected function _defineColumns()
  {
  	return array(
      'id' => array('type' => 'numeric'),
      'name' => '',
      'icon' => '',
      'sort_order' => array('type' => 'numeric'),
      'can_be_parent' => array('type' => 'numeric'),
    );
  }
}

?>