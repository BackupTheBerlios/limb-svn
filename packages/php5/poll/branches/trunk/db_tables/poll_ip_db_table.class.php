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
require_once(LIMB_DIR . '/class/lib/db/db_table.class.php');

class poll_ip_db_table extends db_table
{
  protected function _define_columns()
  {
  	return array(
      'id' => array('type' => 'numeric'),
      'poll_id' => array('type' => 'numeric'),
      'ip' => '',
    );
  }
}

?>