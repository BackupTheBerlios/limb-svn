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
  public function createLinksGroup($identifier, $title)
  {
    $group_db_table = Limb :: toolkit()->createDBTable('SysNodeLinkGroup');

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

  public function updateLinksGroup($group_id, $identifier, $title)
  {
    $group_db_table = Limb :: toolkit()->createDBTable('SysNodeLinkGroup');

    $group_db_table->updateById($group_id,
      array('identifier' => $identifier,
            'title' => $title)
    );
  }

  public function deleteLinksGroup($group_id)
  {
    Limb :: toolkit()->createDBTable('SysNodeLinkGroup')->deleteById($group_id);
  }

  public function setGroupsPriority($priority_info)
  {
    $group_db_table = Limb :: toolkit()->createDBTable('SysNodeLinkGroup');

    foreach($priority_info as $group_id => $priority_value)
    {
      $group_db_table->updateById($group_id, array('priority' => (int)$priority_value));
    }
  }

  public function fetchGroups()
  {
    return Limb :: toolkit()->createDBTable('SysNodeLinkGroup')->getList('', 'priority ASC');
  }

  public function fetchGroupByIdentifier($identifier)
  {
    $group_db_table = Limb :: toolkit()->createDBTable('SysNodeLinkGroup');

    if($arr = $group_db_table->getList(array('identifier' => $identifier)))
      return current($arr);
    else
      return false;
  }

  public function fetchGroup($group_id)
  {
    return Limb :: toolkit()->createDBTable('SysNodeLinkGroup')->getRowById($group_id);
  }

  public function createLink($group_id, $linker_object_id, $target_object_id)
  {
    if ($this->fetchGroup($group_id) === false)
      return false;

    $link_db_table = Limb :: toolkit()->createDBTable('SysNodeLink');

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

  public function deleteLink($link_id)
  {
    Limb :: toolkit()->createDBTable('SysNodeLink')->deleteById($link_id);
  }

  public function fetchTargetLinksNodeIds($linker_node_id, $groups_ids = array())
  {
    $links = $this->fetchTargetLinks($linker_node_id, $groups_ids);

    return ComplexArray :: getColumnValues('target_node_id', $links);
  }

  public function fetchTargetLinks($linker_node_id, $groups_ids = array())
  {
    $link_db_table = Limb :: toolkit()->createDBTable('SysNodeLink');

    $conditions = "linker_node_id = {$linker_node_id}";

    if (is_array($groups_ids) &&  count($groups_ids))
      $conditions .= ' AND ' . sqlIn('group_id', $groups_ids);

    return $link_db_table->getList($conditions, 'priority ASC');
  }

  public function fetchBackLinksNodeIds($target_node_id, $groups_ids = array())
  {
    $links = $this->fetchBackLinks($target_node_id, $groups_ids);

    return ComplexArray :: getColumnValues('linker_node_id', $links);
  }

  public function fetchBackLinks($target_node_id, $groups_ids = array())
  {
    $link_db_table = Limb :: toolkit()->createDBTable('SysNodeLink');

    $conditions = "target_node_id = {$target_node_id}";

    if (is_array($groups_ids) &&  count($groups_ids))
      $conditions .= ' AND ' . sqlIn('group_id', $groups_ids);

    return $link_db_table->getList($conditions, 'priority ASC');
  }

  public function setLinksPriority($priority_info)
  {
    $link_db_table = Limb :: toolkit()->createDBTable('SysNodeLink');

    foreach($priority_info as $link_id => $priority_value)
    {
      $link_db_table->updateById($link_id, array('priority' => (int)$priority_value));
    }
  }

}

?>