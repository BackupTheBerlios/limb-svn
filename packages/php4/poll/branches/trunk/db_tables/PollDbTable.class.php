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
require_once(LIMB_DIR . '/core/db_tables/OneTableObjectDbTable.class.php');

class PollDbTable extends OneTableObjectDbTable
{
  function _defineColumns()
  {
    return ComplexArray :: array_merge(
      parent :: _defineColumns(),
      array(
        'restriction' => array('type' => 'numeric'),
        'start_date' => array('type' => 'date'),
        'finish_date' => array('type' => 'date')
      )
    );
  }

  function _defineConstraints()
  {
    return array(
      'id' =>	array(
        0 => array(
          'table_name' => 'poll_ip',
          'field' => 'poll_id',
        ),
      ),
    );
  }
}

?>