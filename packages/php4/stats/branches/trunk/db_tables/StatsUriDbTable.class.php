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

class StatsUriDbTable extends LimbDbTable
{
  function _defineDbTableName()
  {
    return 'stats_uri';
  }

  function _defineColumns()
  {
    return array(
      'id' => array('type' => 'numeric'),
      'uri' => '',
    );
  }
}

?>