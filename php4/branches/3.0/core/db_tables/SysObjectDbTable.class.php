<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: SysClassDbTable.class.php 1028 2005-01-18 11:06:55Z pachanga $
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/db/LimbDbTable.class.php');

class SysObjectDbTable extends LimbDbTable
{
  function _definePrimaryKeyName()
  {
    return 'oid';
  }

  function _defineDbTableName()
  {
    return 'sys_object';
  }

  function _defineColumns()
  {
    return array(
      'oid' => array('type' => 'numeric'),
      'class_id' => array('type' => 'numeric'),
    );
  }
}

?>