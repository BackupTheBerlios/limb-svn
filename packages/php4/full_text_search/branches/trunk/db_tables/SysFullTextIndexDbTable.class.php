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
require_once(LIMB_DIR . '/class/lib/db/DbTable.class.php');

class SysFullTextIndexDbTable extends DbTable
{
  function _defineColumns()
  {
    return array(
      'id' => array('type' => 'numeric'),
      'attribute' => '',
      'body' => '',
      'class_id' => array('type' => 'numeric'),
      'weight' => array('type' => 'numeric'),
      'object_id' => array('type' => 'numeric')
    );
  }
}

?>