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

class PollIpDbTable extends LimbDbTable
{
  function _defineDbTableName()
  {
    return 'poll_ip';
  }

  function _defineColumns()
  {
    return array(
      'id' => array('type' => 'numeric'),
      'poll_id' => array('type' => 'numeric'),
      'ip' => '',
    );
  }
}

?>