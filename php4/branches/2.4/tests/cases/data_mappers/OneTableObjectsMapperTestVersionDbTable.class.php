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
require_once(LIMB_DIR . '/class/db_tables/OneTableObjectDbTable.class.php');

class OneTableObjectsMapperTestVersionDbTable extends OneTableObjectDbTable
{
  function _defineDbTableName()
  {
    return 'test_one_table_object';
  }

  function _defineColumns()
  {
    return ComplexArray :: array_merge(
      parent :: _defineColumns(),
      array(
        'annotation' => '',
        'content' => '',
        'news_date' => array('type' => 'date'),
      )
    );
  }
}

?>
