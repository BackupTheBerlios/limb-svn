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

class SysSiteObjectDbTable extends LimbDbTable
{
  function _defineDbTableName()
  {
    return 'sys_site_object';
  }

  function _defineColumns()
  {
    return array(
      'id' => array('type' => 'numeric'),
      'class_id' => array('type' => 'numeric'),
      'behaviour_id' => array('type' => 'numeric'),
      'title' => '',
      'creator_id' => array('type' => 'numeric'),
      'created_date' => array('type' => 'numeric'),
      'modified_date' => array('type' => 'numeric'),
      'locale_id' => '',
      'node_id' => array('type' => 'numeric'),
    );
  }
}

?>