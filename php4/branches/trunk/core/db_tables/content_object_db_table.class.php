<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/lib/db/db_table.class.php');

class content_object_db_table extends db_table
{
  function content_object_db_table()
  {
    parent :: db_table();

    $this->_columns['id'] = array('type' => 'numeric');
    $this->_columns['version'] = array('type' => 'numeric');
    $this->_columns['object_id'] = array('type' => 'numeric');
    $this->_columns['identifier'] = array();
    $this->_columns['title'] = array();
  }
}

?>