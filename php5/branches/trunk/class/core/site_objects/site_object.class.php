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
require_once(LIMB_DIR . '/class/core/object.class.php');
require_once(LIMB_DIR . '/class/db_tables/db_table_factory.class.php');
require_once(LIMB_DIR . '/class/core/tree/tree.class.php');
require_once(LIMB_DIR . '/class/core/permissions/user.class.php');

class site_object extends object
{
  const STATUS_PUBLISHED = 1;
  const STATUS_RESTRICTED = 2;

  protected  $_class_id = null;

  public function get_locale_by_id($id)
  {
    $table = Limb :: toolkit()->createDBTable('sys_site_object');

    if($row = $table->get_row_by_id($id))
      return $row['locale_id'];
    else
      return false;
  }

  protected function _get_identifier_generator()
  {
    include_once(LIMB_DIR . '/class/core/site_objects/default_site_object_identifier_generator.class.php');
    return new DefaultSiteObjectIdentifierGenerator();
  }

  public function fetch($params=array(), $sql_params=array())
  {
    $sql =
      sprintf( "SELECT
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
                %s %s",
                $this->_add_sql($sql_params, 'columns'),
                $this->_add_sql($sql_params, 'tables'),
                $this->_add_sql($sql_params, 'conditions'),
                $this->_add_sql($sql_params, 'group')
              );

    if(isset($params['order']))
      $sql .= ' ORDER BY ' . $this->_build_order_sql($params['order']);

    $db = Limb :: toolkit()->getDB();

    $limit = isset($params['limit']) ? $params['limit'] : 0;
    $offset = isset($params['offset']) ? $params['offset'] : 0;

    $db->sql_exec($sql, $limit, $offset);

    return $db->get_array('id');
  }

  protected function _add_sql($add_sql, $type)
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

  public function fetch_count($sql_params=array())
  {
    $sql = sprintf("SELECT COUNT(sso.id) as count
                    FROM sys_site_object as sso %s
                    WHERE sso.id %s %s",
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

  public function create($is_root = false)
  {
    if (!$class_id = $this->get_class_id())
      throw new LimbException('class id is empty');
     
    if(!$identifier = $this->_get_identifier_generator()->generate($this))
      throw new LimbException('identifier is empty');
    
    if (!$this->get_behaviour_id())
      throw new LimbException('behaviour_id is not set');

    $id = $this->_create_site_object_record();

    $tree = Limb :: toolkit()->getTree();

    $values['identifier'] = $identifier;
    $values['object_id'] = $id;

    if($is_root)
    {
      if (!$tree_node_id = $tree->create_root_node($values, false, true))
        throw new LimbException('could not create root tree node');
    }
    else
    {
      if(!($parent_node_id = $this->get_parent_node_id()))
        throw new LimbException('tree parent node is empty');

      if(!$this->_can_add_node_to_parent($parent_node_id))
        throw new LimbException('tree registering failed', array('parent_node_id' => $parent_node_id));

      if (!$tree_node_id = $tree->create_sub_node($parent_node_id, $values))
        throw new LimbException('could not create tree node');
    }

    $this->set('id', $id);
    $this->set('node_id', $tree_node_id);

    return $id;
  }

  protected function _can_add_node_to_parent($parent_node_id)
  {
    include_once(LIMB_DIR . '/class/core/behaviours/site_object_behaviour.class.php');
    return site_object_behaviour :: can_accept_children($parent_node_id);
  }

  public function get_parent_node_id()
  {
    return (int)$this->get('parent_node_id');
  }

  public function set_parent_node_id($parent_node_id)
  {
    $this->set('parent_node_id', (int)$parent_node_id);
  }

  public function get_node_id()
  {
    return (int)$this->get('node_id');
  }

  public function set_node_id($id)
  {
    return $this->set('node_id', $id);
  }
  
  public function get_identifier()
  {
    return $this->get('identifier');
  }

  public function set_identifier($identifier)
  {
    return $this->set('identifier', $identifier);
  }

  public function get_title()
  {
    return $this->get('title', '');
  }

  public function set_title($title)
  {
    return $this->set('title', $title);
  }

  public function set_id($id)
  {
    return $this->set('id', $id);
  }

  public function get_id()
  {
    return (int)$this->get('id');
  }
  
  public function set_behaviour_id($behaviour_id)
  {
    $this->set('behaviour_id', $behaviour_id);
  }
  
  public function get_behaviour_id()
  {
    return (int)$this->get('behaviour_id');
  }
  
  public function set_version($version)
  {
    $this->set('version', $version);
  }
  
  public function get_version()
  {
    return (int)$this->get('version');
  }

	static public function get_object_class_name_by_id($object_id)
	{
		$db = Limb :: toolkit()->getDB();

		$sql = "SELECT sc.class_name as class_name
			FROM sys_site_object as sso, sys_class as sc
			WHERE sso.class_id = sc.id
			AND sso.id={$object_id}";

		$db->sql_exec($sql);
		$row = $db->fetch_row();
		if (!isset($row['class_name']))
		{
			throw new LimbException('object class name not found',
    		array(
    			'object_id' => $object_id
    		)
    	);
		}
		else
			return $row['class_name'];
	}

  public function get_class_id()
  {
    if($this->_class_id)
      return $this->_class_id;

    $db_table = Limb :: toolkit()->createDBTable('sys_class');

    $class_name = get_class($this);

    $list = $db_table->get_list('name="'. $class_name. '"');

    if (count($list) == 1)
    {
      $this->_class_id = key($list);
      return $this->_class_id;
    }
    elseif(count($list) > 1)
    {
      throw new LimbException('there are more than 1 type found',
        array('name' => $class_name));
    }

    $insert_data['id'] = null;
    $insert_data['name'] = $class_name;

    $db_table->insert($insert_data);

    $this->_class_id = (int)$db_table->get_last_insert_id();

    return $this->_class_id;
  }

  public function get_class_properties()
  {
    return $this->_class_properties;
  }

  protected function _create_site_object_record()
  {
    $this->set('version', 1);

    $user = Limb :: toolkit()->getUser();

    $data['id'] = null;
    $data['identifier'] = $this->get_identifier();
    $data['title'] = $this->get_title();
    $data['class_id'] = $this->get_class_id();
    $data['behaviour_id'] = $this->get_behaviour_id();
    $data['current_version'] = $this->get_version();
    $data['creator_id'] = $user->get_id();
    $data['status'] = $this->get('status', 0);

    $created_date = $this->get('created_date', 0);
    $modified_date = $this->get('modified_date', 0);
    $time = time();

    if(!$created_date)
      $data['created_date'] = $time;
    else
      $data['created_date'] = $created_date;

    if(!$modified_date)
      $data['modified_date'] = $time;
    else
      $data['modified_date'] = $modified_date;

    if ($this->get('locale_id'))
      $data['locale_id'] = $this->get('locale_id');
    else
      $data['locale_id'] = $this->_get_parent_locale_id();

    $sys_site_object_db_table = Limb :: toolkit()->createDBTable('sys_site_object');

    $sys_site_object_db_table->insert($data);

    return $sys_site_object_db_table->get_last_insert_id();
  }

  public function update($force_create_new_version = true)
  {
    if(!$this->get_id())
      throw new LimbException('object id not set');
    
    if(!$this->get_node_id())
      throw new LimbException('node id not set');

    if(!$this->get_parent_node_id())
      throw new LimbException('parent node id not set');
    
    if (!$this->get_class_id())
      throw new LimbException('class id is empty'); 
    
    if (!$this->get_behaviour_id())
      throw new LimbException('behaviour id not set');    
    
    if(!$this->get_identifier())
      throw new LimbException('identifier is empty');    

    $this->_update_tree_node();

    $this->_update_site_object_record($force_create_new_version);
  }

  protected function _update_site_object_record($force_create_new_version = true)
  {
    $sys_site_object_db_table = Limb :: toolkit()->createDBTable('sys_site_object');

    $row_data = $sys_site_object_db_table->get_row_by_id($this->get_id());

    if ($force_create_new_version)
      $this->set_version($row_data['current_version'] + 1);
    else
      $this->set_version($row_data['current_version']);

    $time = time();
    $data['current_version'] = $this->get_version();
    $data['behaviour_id'] = $this->get_behaviour_id();
    $data['modified_date'] = $time;
    $data['identifier'] = $this->get_identifier();
    $data['title'] = $this->get_title();
    $data['status'] = $this->get('status', 0);

    $sys_site_object_db_table->update_by_id($this->get_id(), $data);
  }

  protected function _delete_tree_node()
  {
    Limb :: toolkit()->getTree()->delete_node($this->get_node_id());
  }
  
  protected function _is_object_moved_from_node($node)
  {
    return ($node['parent_id'] != $this->get_parent_node_id());
  }
  
  protected function _update_tree_node()
  {
    $node_id = $this->get_node_id();
    $parent_node_id = $this->get_parent_node_id();
    $identifier = $this->get_identifier();
      
    $tree = Limb :: toolkit()->getTree();
    
    $node = $tree->get_node($node_id);
    
    if ($this->_is_object_moved_from_node($node))
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

  public function delete()
  {
    if (!$this->can_delete())
      return;

    $this->_delete_tree_node();

    $this->_delete_site_object_record();
  }

  protected function _delete_site_object_record()
  {
    $sys_site_object_db_table = Limb :: toolkit()->createDBTable('sys_site_object');
    $sys_site_object_db_table->delete_by_id($this->get('id'));
  }

  public function can_delete()
  {
    if(!$this->get_id())
      throw new LimbException('object id not set');
    
    if(!$this->get_node_id())
      throw new LimbException('node id not set');
    
    if(!$this->_can_delete_site_object($this->get_id()))
      return false;

    return $this->_can_delete_tree_node($this->get_node_id());
  }

  protected function _can_delete_tree_node($node_id)
  {
    return Limb :: toolkit()->getTree()->can_delete_node($node_id);
  }

  protected function _can_delete_site_object($object_id)
  {
    return true;
  }

  public function get_behaviour()
  {    
    $id = $this->get_behaviour_id();
    
    $sql = "SELECT sys_behaviour.name as behaviour_name
            FROM sys_behaviour
            WHERE id={$id}";

    $db = Limb :: toolkit()->getDB();

    $db->sql_exec($sql);

    if($row = $db->fetch_row())
      return Limb :: toolkit()->createBehaviour($row['behaviour_name']);
    else
      return null;
  }

  protected function _get_parent_locale_id()
  {
    if (!$parent_node_id = $this->get_parent_node_id())
      return DEFAULT_CONTENT_LOCALE_ID;

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
      return DEFAULT_CONTENT_LOCALE_ID;
  }

}

?>
