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

class SysDomainObject2NodeDbTable extends LimbDbTable
{
  function _defineDbTableName()
  {
    return 'sys_domain_object_to_node';
  }

  function _defineColumns()
  {
    return array(
      'id' => array('type' => 'numeric'),
      'uid' => array('type' => 'numeric'),
      'site_object_id' => array('type' => 'numeric'),
    );
  }
}

?>