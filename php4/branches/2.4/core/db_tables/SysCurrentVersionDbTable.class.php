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
require_once(LIMB_DIR . '/core/db/LimbDbTable.class.php');

class SysCurrentVersionDbTable extends LimbDbTable
{
  function _defineDbTableName()
  {
    return 'sys_current_version';
  }

  function _defineColumns()
  {
    return array(
      'id' => array('type' => 'numeric'),
      'uid' => array('type' => 'numeric'),
      'version_uid' => array('type' => 'numeric'),
    );
  }
}

?>