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
require_once(LIMB_DIR . '/class/lib/util/ComplexArray.class.php');

class OneTableObjectDbTable extends DbTable
{
  protected function _defineColumns()
  {
    return array(
      'id' => array('type' => 'numeric'),
      'version' => array('type' => 'numeric'),
      'object_id' => array('type' => 'numeric'),
      'identifier' => '',
      'title' => '',
    );

  }
}

?>