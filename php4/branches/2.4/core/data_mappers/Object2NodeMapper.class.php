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
  function insert(&$object)
  {
    if (!$object->get('oid'))
      return throw(new LimbException('oid is not set'));

    if (!$object->get('node_id'))
      return throw(new LimbException('node id is not set'));

    $toolkit =& Limb :: toolkit();
    $db_table =& $toolkit->createDBTable('SysObject2Node');

    $row = array('oid' => $object->get('oid'),
                 'node_id' => $object->get('node_id'));

    $db_table->insert($row);
  }

  function update(&$object)
  {
    $toolkit =& Limb :: toolkit();
    $db_table =& $toolkit->createDBTable('SysObject2Node');

    $condition['oid'] = $object->get('oid');
    $rs = $db_table->select($condition);
    $rs->rewind();
    if($rs->valid())
    {
      $row['node_id'] = $object->get('node_id');
      $db_table->update($row, $condition);
    }
    else
    {
      $row['oid'] = $object->get('oid');
      $row['node_id'] = $object->get('node_id');
      $db_table->insert($row);
    }
  }

  function delete(&$object)
  {
    $toolkit =& Limb :: toolkit();
    $db_table =& $toolkit->createDBTable('SysObject2Node');
    $db_table->delete(array('oid' => $object->get('oid')));
  }
}

?>