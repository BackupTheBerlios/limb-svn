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
require_once(LIMB_DIR . '/core/db/LimbDbTable.class.php');

class SysObject2ServiceDbTable extends LimbDbTable
{
  function _defineDbTableName()
  {
    return 'sys_object_to_service';
  }

  function _defineColumns()
  {
    return array(
      'id' => array('type' => 'numeric'),
      'oid' => array('type' => 'numeric'),
      'service_id' => array('type' => 'numeric'),
      'title' => '',
    );

  }
}

?>