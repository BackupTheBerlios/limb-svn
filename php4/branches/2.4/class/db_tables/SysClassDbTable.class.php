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
require_once(LIMB_DIR . '/class/db/LimbDbTable.class.php');

class SysClassDbTable extends LimbDbTable
{
  function _defineDbTableName()
  {
    return 'sys_class';
  }

  function _defineColumns()
  {
    return array(
      'id' => array('type' => 'numeric'),
      'name' => '',
    );
  }
}

?>