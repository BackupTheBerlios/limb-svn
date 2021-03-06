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

class SysParamDbTable extends DbTable
{
  protected function _defineColumns()
  {
    return  array(
      'id' => array('type' => 'numeric'),
      'identifier' => '',
      'type' => '',
      'int_value' => array('type' => 'int'),
      'float_value' => array('type' => 'float'),
      'char_value' => '',
      'blob_value' => array('type' => 'blob'),
    );
  }

}
?>