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

class SiteObjectBehaviourMapper extends AbstractDataMapper
{
  function & findById($id)
  {
    $toolkit =& Limb :: toolkit();
    $table =& $toolkit->createDBTable('SysBehaviour');

    if(!$record = $table->selectRecordById($id))
      return null;

    $behaviour =& $toolkit->createBehaviour($record->get('name'));
    $behaviour->setId($id);

    return $behaviour;
  }

  function insert(&$behaviour)
  {
    $toolkit =& Limb :: toolkit();
    $table =& $toolkit->createDBTable('SysBehaviour');

    $data['name'] = get_class($behaviour);

    $id = $table->insert($data);

    $behaviour->setId($id);

    return $id;
  }

  function update(&$behaviour)
  {
    if(!$id = $behaviour->getId())
      return throw(new LimbException('id is not set'));

    $toolkit =& Limb :: toolkit();
    $table =& $toolkit->createDBTable('SysBehaviour');

    $data['name'] = get_class($behaviour);

    return $table->updateById($id, $data);
  }

  function delete(&$behaviour)
  {
    if(!$id = $behaviour->getId())
      return throw(new LimbException('id is not set'));

    $toolkit =& Limb :: toolkit();
    $table =& $toolkit->createDBTable('SysBehaviour');

    return $table->deleteById($id);
  }

  function getIdsByNames($names)
  {
    $toolkit =& Limb :: toolkit();
    $db =& new SimpleDb($toolkit->getDbConnection());

    $rs =& $db->select('sys_behaviour', 'id', sqlIn('name', $names));

    $result = array();
    for($rs->rewind();$rs->valid();$rs->next())
    {
      $record = $rs->current();
      $result[] = $record->get('id');
    }

    return $result;
  }
}

?>
