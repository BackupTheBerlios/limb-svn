<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: limb@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . '/class/db_tables/db_table_factory.class.php');
require_once(LIMB_DIR . '/class/lib/util/complex_array.class.php');

class links_manager
{  
  public function create_links_group($identifier, $title)
  {
    $group_db_table = Limb :: toolkit()->createDBTable('sys_node_link_group');
    
    $conditions = array(
      'identifier' => $identifier
    );
    
    if($arr = $group_db_table->get_list($conditions))
      return false;
    
    $data = array(
      'id' => null,
      'identifier' => $identifier,
      'title' => $title,
      'priority' => 0,
    );
    
    if($group_db_table->insert($data))
      return $group_db_table->get_last_insert_id();
    else
      return false;
  }
  
  public function update_links_group($group_id, $identifier, $title)
  {
    $group_db_table = Limb :: toolkit()->createDBTable('sys_node_link_group');
    
    $group_db_table->update_by_id($group_id, 
      array('identifier' => $identifier, 
            'title' => $title)
    );
  }
  
  public function delete_links_group($group_id)
  {
    Limb :: toolkit()->createDBTable('sys_node_link_group')->delete_by_id($group_id);
  }
  
  public function set_groups_priority($priority_info)
  {
    $group_db_table = Limb :: toolkit()->createDBTable('sys_node_link_group');
    
    foreach($priority_info as $group_id => $priority_value)
    {
      $group_db_table->update_by_id($group_id, array('priority' => (int)$priority_value));
    }
  }
  
  public function fetch_groups()
  {
    return Limb :: toolkit()->createDBTable('sys_node_link_group')->get_list('', 'priority ASC');
  }

  public function fetch_group_by_identifier($identifier)
  {
    $group_db_table = Limb :: toolkit()->createDBTable('sys_node_link_group');
    
    if($arr = $group_db_table->get_list(array('identifier' => $identifier)))
      return current($arr);
    else
      return false;
  }
  
  public function fetch_group($group_id)
  {
    return Limb :: toolkit()->createDBTable('sys_node_link_group')->get_row_by_id($group_id);
  }
  
  public function create_link($group_id, $linker_object_id, $target_object_id)
  {
    if ($this->fetch_group($group_id) === false)
      return false;
    
    $link_db_table = Limb :: toolkit()->createDBTable('sys_node_link');

    $data = array(
      'linker_node_id' => $linker_object_id,
      'target_node_id' => $target_object_id,
      'group_id' => $group_id,
    );

    if($arr = $link_db_table->get_list($data))
      return false;
     
    $data['priority'] = 0;
    
    if($link_db_table->insert($data))
      return $link_db_table->get_last_insert_id();
    else
      return false;
  }
  
  public function delete_link($link_id)
  {
    Limb :: toolkit()->createDBTable('sys_node_link')->delete_by_id($link_id);
  }
  
  public function fetch_target_links_node_ids($linker_node_id, $groups_ids = array())
  {
    $links = $this->fetch_target_links($linker_node_id, $groups_ids);
    
    return complex_array :: get_column_values('target_node_id', $links);
  }
  
  public function fetch_target_links($linker_node_id, $groups_ids = array())
  {
    $link_db_table = Limb :: toolkit()->createDBTable('sys_node_link');
    
    $conditions = "linker_node_id = {$linker_node_id}";
    
    if (is_array($groups_ids) && count($groups_ids))
      $conditions .= ' AND ' . sql_in('group_id', $groups_ids);
     
    return $link_db_table->get_list($conditions, 'priority ASC');
  }

  public function fetch_back_links_node_ids($target_node_id, $groups_ids = array())
  {
    $links = $this->fetch_back_links($target_node_id, $groups_ids);
    
    return complex_array :: get_column_values('linker_node_id', $links);
  }

  public function fetch_back_links($target_node_id, $groups_ids = array())
  {
    $link_db_table = Limb :: toolkit()->createDBTable('sys_node_link');

    $conditions = "target_node_id = {$target_node_id}";
    
    if (is_array($groups_ids) && count($groups_ids))
      $conditions .= ' AND ' . sql_in('group_id', $groups_ids);
    
    return $link_db_table->get_list($conditions, 'priority ASC');
  }

  public function set_links_priority($priority_info)
  {
    $link_db_table = Limb :: toolkit()->createDBTable('sys_node_link');
    
    foreach($priority_info as $link_id => $priority_value)
    {
      $link_db_table->update_by_id($link_id, array('priority' => (int)$priority_value));
    }
  }
  
}

?>