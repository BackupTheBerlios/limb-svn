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

class StatsSearchPhraseDbTable extends LimbDbTable
{
  function _defineDbTableName()
  {
    return 'stats_search_phrase';
  }

  function _defineColumns()
  {
    return array(
      'id' => array('type' => 'numeric'),
      'phrase' => '',
      'engine' => '',
      'time' => array('type' => 'numeric'),
    );
  }
}

?>