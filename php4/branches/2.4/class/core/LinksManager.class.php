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
require_once(LIMB_DIR . '/class/lib/util/ComplexArray.class.php');

class LinksManager
{
  function createLinksGroup($identifier, $title)
  {
    $toolkit =& Limb :: toolkit();
    $group_db_table =& $toolkit->createDBTable('SysNodeLinkGroup');

    $conditions = array(
      'identifier' => $identifier
    );

    if($arr = $group_db_table->getList($conditions))
      return false;

    $data = array(
      'id' => null,
      'identifier' => $identifier,
      'title' => $title,
      'priority' => 0,
    );

    if($group_db_table->insert($data))
      return $group_db_table->getLastInsertId();
    else
      return false;
  }

  function updateLinksGroup($group_id, $identifier, $title)
  {
    $toolkit =& Limb :: toolkit();
    $group_db_table =& $toolkit->createDBTable('SysNodeLinkGroup');

    $group_db_table->updateById($group_id,
      array('identifier' => $identifier,
            'title' => $title)
    );
  }

  function deleteLinksGroup($group_id)
  {
    $toolkit =& Limb :: toolkit();
    $table =& $toolkit->createDBTable('SysNodeLinkGroup');
    $table->deleteById($group_id);
  }

  function setGroupsPriority($priority_info)
  {
    $toolkit =& Limb :: toolkit();
    $group_db_table =& $toolkit->createDBTable('SysNodeLinkGroup');

    foreach($priority_info as $group_id => $priority_value)
    {
      $group_db_table->updateById($group_id, array('priority' => (int)$priority_value));
    }
  }

  function fetchGroups()
  {
    $toolkit =& Limb :: toolkit();
    $table =& $toolkit->createDBTable('SysNodeLinkGroup');
    return $table->getList('', 'priority ASC');
  }

  function fetchGroupByIdentifier($identifier)
  {
    $toolkit =& Limb :: toolkit();
    $group_db_table =& $toolkit->createDBTable('SysNodeLinkGroup');

    if($arr = $group_db_table->getList(array('identifier' => $identifier)))
      return current($arr);
    else
      return false;
  }

  function fetchGroup($group_id)
  {
    $toolkit =& Limb :: toolkit();
    $table =& $toolkit->createDBTable('SysNodeLinkGroup');
    return $table->getRowById($group_id);
  }

  function createLink($group_id, $linker_object_id, $target_object_id)
  {
    if ($this->fetchGroup($group_id) === false)
      return false;

    $toolkit =& Limb :: toolkit();
    $link_db_table =& $toolkit->createDBTable('SysNodeLink');

    $data = array(
      'linker_node_id' => $linker_object_id,
      'target_node_id' => $target_object_id,
      'group_id' => $group_id,
    );

    if($arr = $link_db_table->getList($data))
      return false;

    $data['priority'] = 0;

    if($link_db_table->insert($data))
      return $link_db_table->getLastInsertId();
    else
      return false;
  }

  function deleteLink($link_id)
  {
    $toolkit =& Limb :: toolkit();
    $table =& $toolkit->createDBTable('SysNodeLink');
    $table->deleteById($link_id);
  }

  function fetchTargetLinksNodeIds($linker_node_id, $groups_ids = array())
  {
    $links = $this->fetchTargetLinks($linker_node_id, $groups_ids);

    return ComplexArray :: getColumnValues('target_node_id', $links);
  }

  function fetchTargetLinks($linker_node_id, $groups_ids = array())
  {
    $toolkit =& Limb :: toolkit();
    $link_db_table =& $toolkit->createDBTable('SysNodeLink');

    $conditions = "linker_node_id = {$linker_node_id}";

    if (is_array($groups_ids) &&  count($groups_ids))
      $conditions .= ' AND ' . sqlIn('group_id', $groups_ids);

    return $link_db_table->getList($conditions, 'priority ASC');
  }

  function fetchBackLinksNodeIds($target_node_id, $groups_ids = array())
  {
    $links = $this->fetchBackLinks($target_node_id, $groups_ids);

    return ComplexArray :: getColumnValues('linker_node_id', $links);
  }

  function fetchBackLinks($target_node_id, $groups_ids = array())
  {
    $toolkit =& Limb :: toolkit();
    $link_db_table =& $toolkit->createDBTable('SysNodeLink');

    $conditions = "target_node_id = {$target_node_id}";

    if (is_array($groups_ids) &&  count($groups_ids))
      $conditions .= ' AND ' . sqlIn('group_id', $groups_ids);

    return $link_db_table->getList($conditions, 'priority ASC');
  }

  function setLinksPriority($priority_info)
  {
    $toolkit =& Limb :: toolkit();
    $link_db_table =& $toolkit->createDBTable('SysNodeLink');

    foreach($priority_info as $link_id => $priority_value)
    {
      $link_db_table->updateById($link_id, array('priority' => (int)$priority_value));
    }
  }

}

?>