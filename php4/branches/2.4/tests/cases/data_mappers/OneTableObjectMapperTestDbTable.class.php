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

class OneTableObjectMapperTestDbTable extends LimbDbTable
{
  function _defineDbTableName()
  {
    return 'test_one_table_object';
  }

  function _defineColumns()
  {
     return array(
          'id' => array('type' => 'numeric'),
          'annotation' => '',
          'content' => '',
          'news_date' => array('type' => 'date'),
        );
  }
}

?>
