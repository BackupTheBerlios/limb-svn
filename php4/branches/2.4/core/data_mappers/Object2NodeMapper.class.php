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
require_once(LIMB_DIR . '/core/data_mappers/AbstractDataMapper.class.php');

class Object2NodeMapper extends AbstractDataMapper
{
  function insert(&$site_object)
  {
    if (!$site_object->getId())
      return throw(new LimbException('uid is not set'));

    if (!$site_object->getSiteObjectId())
      return throw(new LimbException('site object id is not set'));

    $toolkit =& Limb :: toolkit();
    $db_table =& $toolkit->createDBTable('SysDomainObject2Node');

    $row = array('uid' => $site_object->getId(),
                 'site_object_id' => $site_object->getSiteObjectId());

    $db_table->insert($row);
  }

  function update(&$object)
  {
    $toolkit =& Limb :: toolkit();
    $db_table =& $toolkit->createDBTable('SysDomainObject2Node');

    $condition['site_object_id'] = $object->getSiteObjectId();
    $rs = $db_table->select($condition);
    $rs->rewind();
    if($rs->valid())
    {
      $row['uid'] = $object->getId();
      $db_table->update($row, $condition);
    }
    else
    {
      $row['uid'] = $object->getId();
      $row['site_object_id'] = $object->getSiteObjectId();
      $db_table->insert($row);
    }
  }

  function delete(&$site_object)
  {
    $toolkit =& Limb :: toolkit();
    $db_table =& $toolkit->createDBTable('SysDomainObject2Node');
    $db_table->delete(array('uid' => $site_object->getId()));
  }
}

?>