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
require_once(LIMB_DIR . '/class/core/site_objects/SiteObject.class.php');

class SiteStructure extends SiteObject
{
  function savePriority($params)
  {
    if(!count($params))
      return true;

    $toolkit =& Limb :: toolkit();
    $db_table = $toolkit->createDBTable('SysSiteObjectTree');

    foreach($params as $node_id => $value)
    {
      $data = array();
      $data['priority'] = (int)$value;
      $db_table->updateById($node_id, $data);
    }

    return true;
  }
}

?>