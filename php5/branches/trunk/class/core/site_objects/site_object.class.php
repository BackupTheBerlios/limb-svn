<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/ 
require_once(LIMB_DIR . 'class/core/object.class.php');
require_once(LIMB_DIR . 'class/lib/error/error.inc.php');
require_once(LIMB_DIR . 'class/db_tables/db_table_factory.class.php');
require_once(LIMB_DIR . 'class/core/controllers/site_object_controller_factory.class.php');
require_once(LIMB_DIR . 'class/core/tree/tree.class.php');

class site_object extends object
{
  const STATUS_PUBLISHED = 1;
  const STATUS_RESTRICTED = 2;
  
	protected  $_attributes_definition = array();
	
	protected  $_class_properties = array();
	
	protected  $_class_id = null;

	function __construct()
	{
    $this->_class_properties = $this->_define_class_properties();
    
		$new_attributes_definition = $this->_define_attributes_definition();
		
    $this->_attributes_definition['id'] = array('type' => 'numeric');
    $this->_attributes_definition['version'] = array('type' => 'numeric');
    $this->_attributes_definition['object_id'] = array('type' => 'numeric');
    $this->_attributes_definition['parent_node_id'] = array('type' => 'numeric');
    $this->_attributes_definition['title'] = array('search' => true, 'search_weight' => 50);
    $this->_attributes_definition['identifier'] = array('search' => true, 'search_weight' => 50);
    
    $this->_attributes_definition = complex_array :: array_merge($this->_attributes_definition, $new_attributes_definition);
    
    parent :: __construct();
	}
	
	public function get_locale_by_id($id)
	{
	  $table = db_table_factory :: create('sys_site_object');
	  
	  if($row = $table->get_row_by_id($id))
	    return $row['locale_id'];
	  else
	    return false;
	}
		
	public function is_auto_identifier()
	{
		if(isset($this->_class_properties['auto_identifier']))
			return $this->_class_properties['auto_identifier'];
		else
			return false;		
	}
		
	protected function _define_class_properties()
	{
		return array(
			'class_ordr' => 1,
			'can_be_parent' => 1,
			'icon' => '/shared/images/generic.gif',
			'controller_class_name' => 'empty_controller'
		);
	}
	
	protected function _define_attributes_definition()
	{
		return array();
	}
			
	public function get_attributes_definition()
	{
		return $this->_attributes_definition;
	}

	public function get_definition($attribute_name)
	{
		$definition = $this->get_attributes_definition();
		
		if(isset($definition[$attribute_name]))
		{
			if($definition[$attribute_name] == '')
				return array();
			else 
				return $definition[$attribute_name];
		}
		return false;
	}
	
	public function fetch_accessible($params=array(), $sql_params=array(), $sort_ids = array())
	{
		$ids_sql_params = $sql_params;
		$ids_sql_params['tables'][] = ' , sys_object_access as soa';
		$ids_sql_params['conditions'][] = ' AND sso.id = soa.object_id AND soa.r = 1';
	
    $accessor_ids = implode(',', access_policy :: instance()->get_accessor_ids());
			
		$ids_sql_params['conditions'][] = " AND soa.accessor_id IN ({$accessor_ids})";

		$ids_sql_params['group'][] = ' GROUP BY sso.id';

		$ids = $this->fetch_ids($params, $ids_sql_params, $sort_ids);

		$ids_sql_params['conditions'] = array();
		
		$arr = array();
		if (!$ids)
			return $arr;
		
		if(isset($params['limit']))
		unset($params['limit']);

		if(isset($params['offset']))
		unset($params['offset']);
			
		return $this->fetch_by_ids($ids, $params, $sql_params);
	}
	
	public function fetch_ids($params=array(), $sql_params=array(), $sort_ids=array())
	{
		if (!isset($params['restrict_by_class']) ||
				(isset($params['restrict_by_class']) && (bool)$params['restrict_by_class']))
		{
			$sql_params['conditions'][] = (' AND sso.class_id = ' . $this->get_class_id());
		}	

		$sql = 
			sprintf( "SELECT sso.id 
								FROM 
								sys_site_object as sso, sys_site_object_tree as ssot
								%s
								WHERE sso.id=ssot.object_id 
								%s %s",
								$this->_add_sql($sql_params, 'tables'),
								$this->_add_sql($sql_params, 'conditions'),
								$this->_add_sql($sql_params, 'group')
							);

		$db = db_factory :: instance();

		$limit = isset($params['limit']) ? $params['limit'] : 0;
		$offset = isset($params['offset']) ? $params['offset'] : 0;

		$result = array();
		
		if(isset($params['order']))
		{
			$sql .= ' ORDER BY ' . $this->_build_order_sql($params['order']);
				
			$db->sql_exec($sql, $limit, $offset);

			while($row = $db->fetch_row())
				$result[] = $row['id'];
	
			return $result;
		}
		elseif(count($sort_ids))
		{
			$db->sql_exec($sql);
			
			if(!$arr = $db->get_array('id'))
				return $result;
			
			foreach($sort_ids as $key)
				if (isset($arr[$key]))
					$result[] = $key;
					
			if($limit)
				$result = array_splice($result, $offset, $limit);
			
			return $result;
		}
		else
		{
			$db->sql_exec($sql, $limit, $offset);

			while($row = $db->fetch_row())
				$result[] = $row['id'];
	
			return $result;
		}
	}
	
	public function fetch($params=array(), $sql_params=array())
	{
		if (!isset($params['restrict_by_class']) ||
				(isset($params['restrict_by_class']) && (bool)$params['restrict_by_class']))
		{
			$sql_params['conditions'][] = (' AND sso.class_id = ' . $this->get_class_id());
		}	

		$sql = 
			sprintf( "SELECT 
								sso.* 
								%s,
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
								sys_class.class_name as class_name,
								sys_class.icon as icon,
								sys_class.class_ordr as class_ordr,
								sys_class.can_be_parent as can_be_parent
								FROM
								sys_site_object as sso, sys_class, 
								sys_site_object_tree as ssot
								%s
								WHERE sys_class.id = sso.class_id
								AND ssot.object_id = sso.id
								%s %s",
								$this->_add_sql($sql_params, 'columns'),
								$this->_add_sql($sql_params, 'tables'),
								$this->_add_sql($sql_params, 'conditions'),
								$this->_add_sql($sql_params, 'group')
							);
		
		if(isset($params['order']))
			$sql .= ' ORDER BY ' . $this->_build_order_sql($params['order']);
		
		$db = db_factory :: instance();
		
		$limit = isset($params['limit']) ? $params['limit'] : 0;
		$offset = isset($params['offset']) ? $params['offset'] : 0;

		$db->sql_exec($sql, $limit, $offset);
		
		return $db->get_array('id');
	}
	
	public function fetch_by_ids($ids_array, $params=array(), $sql_params=array())
	{
		if (!count($ids_array))
			return array();
	
		$ids = '('. implode(' , ', $ids_array) . ')';
		
		if(isset($params['limit']))
		{
			$ids_sql_params = $sql_params;
			$ids_sql_params['conditions'][] =  " AND sso.id IN {$ids}";
			
			$ids_array = $this->fetch_ids($params, $ids_sql_params, $ids_array);
			
			if (!count($ids_array))
				return array();
			
		unset($params['limit']);

			if(isset($params['offset']))
			unset($params['offset']);

			$sql_params['conditions'] = array();
		}
		
		$ids = '('. implode(' , ', $ids_array) . ')';
		$sql_params['conditions'][] =  " AND sso.id IN {$ids}";

		return $this->fetch($params, $sql_params);
	}
	
	public function fetch_accessible_by_ids($ids_array, $params=array(), $sql_params=array())
	{
		if (!count($ids_array))
			return array();
			
		$ids = '('. implode(',', $ids_array) . ')';
		
		$sql_params['conditions'][] =  " AND sso.id IN {$ids}";
		
		return $this->fetch_accessible($params, $sql_params, $ids_array);
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
	
	public function fetch_accessible_count($params=array(), $sql_params=array())
	{
		$sql_params['tables'][] = ' INNER JOIN sys_object_access as soa ON soa.object_id = sso.id';
		$sql_params['conditions'][] = ' AND sso.id = soa.object_id AND soa.r = 1';
	
    $accessor_ids = implode(',', access_policy :: instance()->get_accessor_ids());
			
		$sql_params['conditions'][] = " AND soa.accessor_id IN ({$accessor_ids})";

		$sql_params['group'][] = ' GROUP BY sso.id';
		
		return $this->fetch_count($params, $sql_params);
	}
	
	public function fetch_accessible_by_ids_count($ids_array, $params=array(), $sql_params=array())
	{
		if (!count($ids_array))
			return array();
	
		$ids = '('. implode(',', $ids_array) . ')';
		
		$sql_params['conditions'][] =  " AND sso.id IN {$ids}";
		
		return $this->fetch_accessible_count($params, $sql_params);
	}

	public function fetch_count($params=array(), $sql_params=array())
	{
		if (!isset($params['restrict_by_class']) ||
				(isset($params['restrict_by_class']) && (bool)$params['restrict_by_class']))
		{
			$sql_params['conditions'][] = (' AND sso.class_id = ' . $this->get_class_id());
		}	
					
		$sql = sprintf("SELECT COUNT(sso.id) as count
										FROM sys_site_object as sso %s
										WHERE sso.id %s %s",
									$this->_add_sql($sql_params, 'tables'),
									$this->_add_sql($sql_params, 'conditions'),
									$this->_add_sql($sql_params, 'group')
								);
		
		$db = db_factory :: instance();
		
		$db->sql_exec($sql);

		if (!isset($sql_params['group']))		
		{
			$arr = $db->fetch_row();
			return (int)$arr['count'];
		}
		else
			return $db->count_selected_rows();
	}
	
	public function fetch_by_ids_count($ids_array, $params=array(), $sql_params=array())
	{
		if (!count($ids_array))
			return array();
	
		$ids = '('. implode(' , ', $ids_array) . ')';
		
		$sql_params['conditions'][] =  " AND sso.id IN {$ids}";
		
		return $this->fetch_count($params, $sql_params);
	}
				
	public function create($is_root = false)
	{
		if (!$class_id = $this->get_class_id())
		  throw new LimbException('class id is empty');
		
		if(!$this->is_auto_identifier())
		{
			if(!($identifier = $this->get_identifier()))
		    throw new LimbException('identifier is empty');
		}
		else
			$identifier = $this->_generate_auto_identifier();
		
		$id = $this->_create_site_object_record();

		$tree = tree :: instance();

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
	
	protected function _generate_auto_identifier()
	{
		$tree = tree :: instance();
		
		$identifier = $tree->get_max_child_identifier($this->get_parent_node_id());
		
		if($identifier === false)
   	  throw new LimbException('couldnt generate identifier');
			
		if(preg_match('/^(.*)(\d+)$/', $identifier, $matches))
			$new_identifier = $matches[1] . ($matches[2] + 1);
		else
			$new_identifier = $identifier . '1';
		
		$this->set_identifier($new_identifier);
		
		return $new_identifier;
	}
	
	protected function _can_add_node_to_parent($parent_node_id)
	{
		$tree = tree :: instance();
		
		if (!$tree->can_add_node($parent_node_id))
			return false;
			
		$sql = "SELECT sys_class.class_name
		FROM sys_site_object as sso, sys_class, sys_site_object_tree as ssot
		WHERE ssot.id={$parent_node_id} 
		AND sso.class_id=sys_class.id
		AND sso.id=ssot.object_id";
		
		$db = db_factory :: instance();
		
		$db->sql_exec($sql);
		
		$row = $db->fetch_row();
		
		if (!is_array($row) || !count($row))
			return false;
		
		$parent_object = site_object_factory :: create($row['class_name']);
		
		return $parent_object->can_be_parent();
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
		return $this->get('title');
	}

	public function set_title($title)
	{
		return $this->set('title', $title);
	}
	
	public function get_id()
	{
		return (int)$this->get('id');
	}
	
	public function get_version()
	{
		return (int)$this->get('version');
	}

	public function get_class_id()
	{
		if($this->_class_id)
			return $this->_class_id;
			
		$type_db_table = db_table_factory :: create('sys_class');	

		$class_name = get_class($this);
		
		$list = $type_db_table->get_list('class_name="'. $class_name. '"');
		
		if (count($list) == 1)
		{
			$this->_class_id = key($list);
			return $this->_class_id;
		}
		elseif(count($list) > 1)
		{
		  throw new LimbException('there are more than 1 type found', 
			  array('class_name' => $class_name));
		}			
		
		$insert_data = $this->get_class_properties();
		$insert_data['class_name'] = $class_name;
		
		if(!isset($insert_data['icon']) || !$insert_data['icon'])
			$insert_data['icon'] = '/shared/images/generic.gif';
		
		$type_db_table->insert($insert_data);
		
		$this->_class_id = (int)$type_db_table->get_last_insert_id();
		
		return $this->_class_id;
	}
			
	public function get_class_properties()
	{
		return $this->_class_properties;
	}

	public function can_be_parent()
	{
		if (isset($this->_class_properties['can_be_parent']))
			return $this->_class_properties['can_be_parent'];
		else
			return false;	
	}
	
	protected function _create_site_object_record()
	{
		$this->set('version', 1); 
		
		$user = user :: instance();

		$data['identifier'] = $this->get_identifier();
		$data['title'] = $this->get_title();
		$data['class_id'] = $this->get_class_id();
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
		
		$sys_site_object_db_table = db_table_factory :: create('sys_site_object');
		
		$sys_site_object_db_table->insert($data);
		
		return $sys_site_object_db_table->get_last_insert_id();
	}
	
	public function update($force_create_new_version = true)
	{
		if(!$object_id = $this->get_id())
		  throw new LimbException('object id not set');

		$this->_update_tree_node();
		
		$this->_update_site_object_record($force_create_new_version);
	}
	
	protected function _update_site_object_record($force_create_new_version = true)
	{
		$sys_site_object_db_table = db_table_factory :: create('sys_site_object');
		
		$row_data = $sys_site_object_db_table->get_row_by_id($this->get_id());

		if ($force_create_new_version)
			$this->set('version', $row_data['current_version'] + 1);
		else
			$this->set('version', $row_data['current_version']);
		
		$time = time();
		$data['current_version'] = $this->get_version();
		$data['modified_date'] = $time;
		$data['identifier'] = $this->get_identifier();
		$data['title'] = $this->get_title();
		$data['status'] = $this->get('status', 0);
		
		$sys_site_object_db_table->update_by_id($this->get_id(), $data);
	}
	
	protected function _delete_tree_node()
	{
		$data = $this->_attributes->export();

		$tree = tree :: instance();
		
		$tree->delete_node($data['node_id']);
	}
	
	protected function _update_tree_node()
	{
		$data = $this->_attributes->export();

		$tree = tree :: instance();
		
		$node = $tree->get_node($data['node_id']);
		if (isset($data['parent_node_id']) && isset($data['node_id']))
		{
			if ($node['parent_id'] != $data['parent_node_id'])
			{
				if (!$tree->move_tree($data['node_id'], $data['parent_node_id']))
				{
				 	throw new LimbException('could not move node',
	    			array(
	    				'node_id' => $data['node_id'],
	    				'target_id' => $data['parent_node_id'],
	  	  		)
		    	);
				}
			}	
		}	
		
		if (($identifier = $this->get('identifier')) && ($identifier != $node['identifier']))
		{
			if(!$tree->update_node($data['node_id'], array('identifier' => $identifier), true))
			{
			 	throw new LimbException('could not update node identifier',
    			array(
    				'node_id' => $data['node_id'],
    				'identifier' => $identifier,
  	  		)
	    	);
			}
		}	
	}
	
	public function delete()
	{
		if(!$object_id = $this->get_id())
		  throw new LimbException('object id is not set');
		
		if (!$this->can_delete())
		  return;

		$this->_delete_tree_node();
		
		$this->_delete_site_object_record();
	}

	protected function _delete_site_object_record()
	{
		$sys_site_object_db_table = db_table_factory :: create('sys_site_object');
		$sys_site_object_db_table->delete_by_id($this->get('id'));
	}

	public function can_delete()
	{
		$data = $this->_attributes->export();

		if(!$this->_can_delete_site_object($data['id']))
			return false;

		return $this->_can_delete_tree_node($data['node_id']);
	}
	
	protected function _can_delete_tree_node($node_id)
	{
		return tree :: instance()->can_delete_node($node_id);
	}
	
	protected function _can_delete_site_object($object_id)
	{
		return true;
	}
		
	public function get_controller()
	{
		return site_object_controller_factory :: create($this->_class_properties['controller_class_name']);
	}
	
	public function save_metadata()
	{
		if(!$id = $this->get_id())
		  throw new LimbException('object id is not set');
		
  	$sys_metadata_db_table = db_table_factory :: create('sys_metadata');
  	
  	$sys_metadata_db_table->delete('object_id=' . $id);
  	
  	$metadata = array();
  	$metadata['object_id'] = $id;
  	$metadata['keywords'] = $this->get('keywords');
  	$metadata['description'] = $this->get('description');
  	
  	$sys_metadata_db_table->insert($metadata);
  	return $sys_metadata_db_table->get_last_insert_id();
	}
	
	public function get_metadata()
	{
		if(!$id = $this->get_id())
			return false;
		
  	$sys_metadata_db_table = db_table_factory :: create('sys_metadata');
  	$arr = $sys_metadata_db_table->get_list('object_id=' . $id);
		
		if (!count($arr))
			return array();
			
		return current($arr);
	}
	
	protected function _get_parent_locale_id()
	{
		if (!$parent_node_id = $this->get_parent_node_id())
			return DEFAULT_CONTENT_LOCALE_ID;
		
		$sql = "SELECT sso.locale_id	
		FROM sys_site_object as sso, sys_site_object_tree as ssot
		WHERE ssot.id = {$parent_node_id} 
		AND sso.id = ssot.object_id";
		
		$db = db_factory :: instance();
		
		$db->sql_exec($sql);
		
		$parent_data = $db->fetch_row();
		
		if (isset($parent_data['locale_id']) && $parent_data['locale_id'])
			return $parent_data['locale_id'];
		else
			return DEFAULT_CONTENT_LOCALE_ID;
	}
	
	public function can_accept_child_class($class_name)
	{
		$class_properties = $this->get_class_properties();
		
		if (!isset($class_properties['acceptable_children']))
			return false;
		
		return in_array($class_name, $class_properties['acceptable_children']);
	}
}

?>
