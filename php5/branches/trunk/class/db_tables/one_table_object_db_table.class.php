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
require_once(LIMB_DIR . '/class/lib/util/complex_array.class.php');

class one_table_object_db_table extends db_table
{
  protected function _define_columns() 
  {
    return array(
      'id' => array('type' => 'numeric'),
      'version' => array('type' => 'numeric'),
      'object_id' => array('type' => 'numeric'),
      'identifier' => '',
      'title' => '',
    );
    
  }
}

?>