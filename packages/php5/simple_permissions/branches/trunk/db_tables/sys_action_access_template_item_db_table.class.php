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
require_once(LIMB_DIR . 'class/lib/db/db_table.class.php');

class sys_action_access_template_item_db_table extends db_table
{
  protected function _define_columns()
  {
  	return array(
      'id' => array('type' => 'numeric'),
      'template_id' => array('type' => 'numeric'),
      'accessor_id' => array('type' => 'numeric'),
      'access' => array('type' => 'numeric'),
    );
  }
}

?>