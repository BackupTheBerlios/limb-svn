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
require_once(LIMB_DIR . '/class/util/ComplexArray.class.php');

class OneTableObjectDbTable extends LimbDbTable
{
  function _defineColumns()
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