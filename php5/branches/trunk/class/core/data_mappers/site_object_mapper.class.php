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

class site_object_mapper
{
  const RAW_SELECT_STMT = 
    "SELECT
    sso.current_version as current_version,
    sso.modified_date as modified_date,
    sso.status as status,
    sso.created_date as created_date,
    sso.creator_id as creator_id,
    sso.locale_id as locale_id,
    %s
    sso.title as title,
    sso.identifier as identifier,
    sso.id as id,
    ssot.id as node_id,
    ssot.parent_id as parent_node_id,
    ssot.level as level,
    ssot.priority as priority,
    ssot.children as children,
    sso.current_version as version,
    sys_class.id as class_id,
    sys_class.name as class_name,
    sys_behaviour.id as behaviour_id,
    sys_behaviour.name as behaviour,
    sys_behaviour.icon as icon,
    sys_behaviour.sort_order as sort_order,
    sys_behaviour.can_be_parent as can_be_parent
    FROM
    sys_site_object as sso, sys_class, sys_behaviour,
    sys_site_object_tree as ssot
    %s
    WHERE sys_class.id = sso.class_id
    AND sys_behaviour.id = sso.behaviour_id
    AND ssot.object_id = sso.id
    %s %s";
    
  const RAW_COUNT_STMT = 
    "SELECT COUNT(sso.id) as count
     FROM sys_site_object as sso %s
     WHERE sso.id %s %s";
  
  public function save($site_object)
  {
    if($site_object->get_id())
      $this->update($site_object);
    else
      $this->insert($site_object);
  }
  
  public function insert($site_object)
  {
    $id = $this->_insert_site_object_record($site_object);

    $node_id = $this->_insert_tree_node($site_object);

    $site_object->set_id($id);
    $site_object->set_node_id($node_id);

    return $id;    
  }
  
  protected function _insert_tree_node($site_object)
  {
    if(!($parent_node_id = $site_object->get_parent_node_id()))
      throw new LimbException('tree parent node is empty');

    if(!$this->_can_add_node_to_parent($parent_node_id))
      throw new LimbException('tree registering failed', array('parent_node_id' => $parent_node_id));

    $tree = Limb :: toolkit()->getTree();

    $values['identifier'] = $site_object->get_identifier();
    $values['object_id'] = $site_object->get_id();

    if (!$node_id = $tree->create_sub_node($parent_node_id, $values))
      throw new LimbException('could not create tree node');
    
    return $node_id;
  }
  
  protected function _insert_site_object_record($site_object)
  {
    if(!$identifier = $this->_get_identifier_generator()->generate($site_object))
      throw new LimbException('identifier is empty');

    if (!$behaviour = $site_object->get_behaviour())
      throw new LimbException('behaviour is not attached');
    
    if (!$class_id = $this->get_class_id($site_object))
      throw new LimbException('class id is empty');
    
    if(!$created_date = $site_object->get_created_date())
      $site_object->set_created_date(time());
    
    if(!$modified_date = $site_object->get_modified_date())
      $site_object->set_modified_date(time());

    if (!$site_object->get_locale_id())
      $site_object->set_locale_id($this->get_parent_locale_id($site_object->get_parent_node_id()));

    $site_object->set_version(1);

    $user = Limb :: toolkit()->getUser();
    
    $behaviour_mapper = Limb :: toolkit()->createDataMapper('site_object_behaviour_mapper');
    $behaviour_mapper->save($site_object->get_behaviour());

    $site_object->set_creator_id($user->get_id());

    $data['id'] = null;
    $data['identifier'] = $site_object->get_identifier();
    $data['title'] = $site_object->get_title();
    $data['class_id'] = $this->get_class_id($site_object);
    $data['behaviour_id'] = $site_object->get_behaviour()->get_id();
    $data['current_version'] = $site_object->get_version();
    $data['creator_id'] = $site_object->get_creator_id();
    $data['status'] = $site_object->get_status();
    $data['created_date'] = $site_object->get_created_date();
    $data['modified_date'] = $site_object->get_modified_date();
    $data['locale_id'] = $site_object->get_locale_id();

    $sys_site_object_db_table = Limb :: toolkit()->createDBTable('sys_site_object');

    $sys_site_object_db_table->insert($data);

    return $sys_site_object_db_table->get_last_insert_id();
  }

  public function update($site_object)
  {
    $this->_update_tree_node($site_object);

    $this->_update_site_object_record($site_object);
  }

  protected function _update_site_object_record($site_object)
  {
    if(!$site_object->get_id())
      throw new LimbException('object id not set');

    if(!$site_object->get_identifier())
      throw new LimbException('identifier is empty');    

    if (!$site_object->get_behaviour())
      throw new LimbException('behaviour id not attached');   
    
    $behaviour_mapper = Limb :: toolkit()->createDataMapper('site_object_behaviour_mapper');
    $behaviour_mapper->save($site_object->get_behaviour());    
    
    $data['current_version'] = $site_object->get_version();
    $data['behaviour_id'] = $site_object->get_behaviour()->get_id();
    $data['locale_id'] = $site_object->get_locale_id();
    $data['modified_date'] = $site_object->get_modified_date();
    $data['identifier'] = $site_object->get_identifier();
    $data['title'] = $site_object->get_title();
    $data['status'] = $site_object->get_status();

    $sys_site_object_db_table = Limb :: toolkit()->createDBTable('sys_site_object');
    $sys_site_object_db_table->update_by_id($site_object->get_id(), $data);
  }

  protected function _update_tree_node($site_object)
  {
    if(!$site_object->get_node_id())
      throw new LimbException('node id not set');

    if(!$site_object->get_parent_node_id())
      throw new LimbException('parent node id not set');
    
    $node_id = $site_object->get_node_id();
    $parent_node_id = $site_object->get_parent_node_id();
    $identifier = $site_object->get_identifier();
    
    $tree = Limb :: toolkit()->getTree();
    $node = $tree->get_node($node_id);
    
    if ($this->_is_object_moved_from_node($parent_node_id, $node))
    {
      if(!$this->_can_add_node_to_parent($parent_node_id))
        throw new LimbException('new parent cant accept children', 
                                array('parent_node_id' => $parent_node_id));
      
      if (!$tree->move_tree($node_id, $parent_node_id))
      {
        throw new LimbException('could not move node',
          array(
            'node_id' => $node_id,
            'target_id' => $parent_node_id,
          )
        );
      }
    }

    if ($identifier != $node['identifier'])
      $tree->update_node($node_id, array('identifier' => $identifier), true);
  }

  protected function _get_identifier_generator()
  {
    include_once(LIMB_DIR . '/class/core/mappers/default_site_object_identifier_generator.class.php');
    return new DefaultSiteObjectIdentifierGenerator();
  }

  public function find_as_raw_array($params = array(), $sql_params=array())//refactor!!!
  {
    $sql = sprintf(self :: RAW_SELECT_STMT,
                  $this->_add_sql($sql_params, 'columns'),
                  $this->_add_sql($sql_params, 'tables'),
                  $this->_add_sql($sql_params, 'conditions'),
                  $this->_add_sql($sql_params, 'group'));

    if(isset($params['order']))
      $sql .= ' ORDER BY ' . $this->_build_order_sql($params['order']);

    $db = Limb :: toolkit()->getDB();

    $limit = isset($params['limit']) ? $params['limit'] : 0;
    $offset = isset($params['offset']) ? $params['offset'] : 0;

    $db->sql_exec($sql, $limit, $offset);

    return $db->get_array('id');
  }
  
  protected function _add_sql($add_sql, $type)//refactor!!!
  {
    if (isset($add_sql[$type]))
      return implode(' ', $add_sql[$type]);
    else
      return '';
  }  

  protected function _build_order_sql($order_array)
  {
    $columns = array();

    foreach($order_array as $column => $sort_type)
      $columns[] = $column . ' ' . $sort_type;

    return implode(', ', $columns);
  }

  public function count($sql_params=array())//refactor!!!
  {
    $sql = sprintf(self :: RAW_COUNT_STMT,
                  $this->_add_sql($sql_params, 'tables'),
                  $this->_add_sql($sql_params, 'conditions'),
                  $this->_add_sql($sql_params, 'group')
                );

    $db = Limb :: toolkit()->getDB();

    $db->sql_exec($sql);

    if (!isset($sql_params['group']))
    {
      $arr = $db->fetch_row();
      return (int)$arr['count'];
    }
    else
      return $db->count_selected_rows();
  }

  protected function _can_add_node_to_parent($parent_node_id)
  {
    $tree = Limb :: toolkit()->getTree();

    return $tree->can_add_node($node_id);
  }

  public function get_class_id($site_object)
  {
    $db_table = Limb :: toolkit()->createDBTable('sys_class');

    $class_name = get_class($site_object);

    $list = $db_table->get_list('name="'. $class_name. '"');

    if (count($list) == 1)
    {
      return key($list);
    }
    elseif(count($list) > 1)
    {
      throw new LimbException('there are more than 1 type found',
        array('name' => $class_name));
    }

    $insert_data['id'] = null;
    $insert_data['name'] = $class_name;

    $db_table->insert($insert_data);

    return $db_table->get_last_insert_id();
  }

  protected function _is_object_moved_from_node($parent_node_id, $node)
  {
    return ($node['parent_id'] != $parent_node_id);
  }

  public function delete($site_object)
  {
    if (!$this->can_delete($site_object))
      return;

    $this->_delete_tree_node($site_object);

    $this->_delete_site_object_record($site_object);
  }

  protected function _delete_tree_node($site_object)
  {
    Limb :: toolkit()->getTree()->delete_node($site_object->get_node_id());
  }
  
  protected function _delete_site_object_record($site_object)
  {
    $sys_site_object_db_table = Limb :: toolkit()->createDBTable('sys_site_object');
    $sys_site_object_db_table->delete_by_id($site_object->get_id());
  }

  public function can_delete($site_object)
  {
    if(!$this->_can_delete_site_object_record($site_object))
      return false;

    return $this->_can_delete_tree_node($site_object);
  }

  protected function _can_delete_tree_node($site_object)
  {
    if(!$site_object->get_node_id())
      throw new LimbException('node id not set');
    
    return Limb :: toolkit()->getTree()->can_delete_node($site_object->get_node_id());
  }

  protected function _can_delete_site_object_record($site_object)
  {
    if(!$site_object->get_id())
      throw new LimbException('object id not set');
    
    return true;
  }

  public function get_parent_locale_id($parent_node_id)
  {
    $sql = "SELECT sso.locale_id as locale_id
            FROM sys_site_object as sso, sys_site_object_tree as ssot
            WHERE ssot.id = {$parent_node_id}
            AND sso.id = ssot.object_id";

    $db = Limb :: toolkit()->getDB();

    $db->sql_exec($sql);

    $parent_data = $db->fetch_row();

    if (isset($parent_data['locale_id']) && $parent_data['locale_id'])
      return $parent_data['locale_id'];
    else
      return Limb :: toolkit()->constant('DEFAULT_CONTENT_LOCALE_ID');
  }

}

?>
