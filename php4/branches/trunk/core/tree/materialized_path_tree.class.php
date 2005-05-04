<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: nested_sets_imp.class.php 131 2004-04-09 14:11:45Z server $
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/model/access_policy.class.php');

define('TREE_ERROR_NODE_NOT_FOUND', 1);
define('TREE_ERROR_NODE_WRONG_PARAM', 2);
define('TREE_ERROR_RECURSION', 3);

class materialized_path_tree//implements tree
{
  var $_root_nodes_cache = null;

  var $_node_table = 'sys_site_object_tree';

  var $_db = null;

  var $_params = array(
    'id' => 'id',
    'root_id' => 'root_id',
    'identifier' => 'identifier',
    'object_id' => 'object_id',
    'path' => 'path',
    'level' => 'level',
    'parent_id' => 'parent_id',
    'children' => 'children'
  );

  var $_required_params = array('id', 'root_id', 'path', 'level', 'children');

  var $_expanded_parents = array();

  var $_dumb_mode = false;

  function materialized_path_tree()
  {
    $this->_db =& db_factory :: instance();
  }

  function set_dumb_mode($status=true)
  {
    $prev_mode = $this->_dumb_mode;
    $this->_dumb_mode = $status;
    return $prev_mode;
  }

  function set_node_table($table_name)
  {
    $this->_node_table = $table_name;
  }

  function get_node_table()
  {
    return $this->_node_table;
  }

  function & _get_result_set($sql)
  {
    $this->_db->sql_exec($sql);
    $nodes =& $this->_db->get_array('id');

    return $nodes;
  }

  function _assign_result_set(&$nodes, $sql)
  {
    $this->_sql = $sql;
    $this->_db->sql_exec($sql);
    $this->_db->assign_array($nodes, 'id');
  }

  /**
  * Changes the payload of a node
  */
  function update_node($node, $values)
  {
    if(!$node = $this->get_node($node))
      return false;

    $this->_verify_user_values($values);

    return $this->_db->sql_update($this->_node_table, $values, array('id' => $node['id']));
  }

  /**
  * Adds a specific type of SQL to a sql_exec string
  */
  function _add_sql($add_sql, $type)
  {
    if (!isset($add_sql[$type]))
      return '';

    return implode(' ', $add_sql[$type]);
  }

  function _is_table_joined($table_name, $add_sql)
  {
    if(!isset($add_sql['join']))
      return false;

    foreach($add_sql['join'] as $sql)
    {
      if(strpos($sql, $table_name) !== false)
        return true;
    }
    return false;
  }

  /**
  * Gets the select fields based on the params
  */
  function _get_select_fields()
  {
    $sql_exec_fields = array();
    foreach ($this->_params as $key => $val)
    {
      $sql_exec_fields[] = $this->_node_table . '.' . $key . ' AS ' . $val;
    }

    return implode(', ', $sql_exec_fields);
  }

  /**
  * Clean values from protected or unknown columns
  */
  function _verify_user_values(&$values)
  {
    if ($this->_dumb_mode)
      return true;

    foreach($values as $field => $value)
    {
      if (!isset($this->_params[$field]))
      {
        debug :: write_error(TREE_ERROR_NODE_WRONG_PARAM,
           __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__,
           array('param' => $field)
        );
        unset($values[$field]);
        continue;
      }

      if (in_array($this->_params[$field], $this->_required_params))
      {
        debug :: write_error(TREE_ERROR_NODE_WRONG_PARAM,
           __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__,
         array('value' => $field)
        );

        unset($values[$field]);
      }
    }
  }

  function set_expanded_parents(& $expanded_parents)
  {
    $this->_expanded_parents =& $expanded_parents;

    $this->check_expanded_parents();
  }

  function check_expanded_parents()
  {
    if(!is_array($this->_expanded_parents) || sizeof($this->_expanded_parents) == 0)
    {
      $this->reset_expanded_parents();
    }
    elseif(sizeof($this->_expanded_parents) > 0)
    {
      $this->update_expanded_parents();
    }
  }

  function toggle_node($node)
  {
    if(!$node = $this->get_node($node))
      return false;

    $this->_set_expanded_parent_status($node, !$this->is_node_expanded($id));

    return true;
  }

  function expand_node($node)
  {
    if(!$node = $this->get_node($node))
      return false;

    $this->_set_expanded_parent_status($node, true);

    return true;
  }

  function collapse_node($node)
  {
    if(!$node = $this->get_node($node))
      return false;

    $this->_set_expanded_parent_status($node, false);

    return true;
  }

  /**
  * Fetch the whole nested set
  */
  function & get_all_nodes($add_sql = array())
  {
    $node_set = array();
    $root_nodes = $this->get_root_nodes();
    foreach($root_nodes as $root_id => $root_node)
    {
      $node_set = $node_set + $this->get_sub_branch($root_node, -1, true, false, false, $add_sql);
    }
    return $node_set;
  }

  /**
  * Fetches the first level (the rootnodes)
  */
  function & get_root_nodes($add_sql = array())
  {
    if(isset($this->_root_nodes_cache))
      return $this->_root_nodes_cache;

    $sql = sprintf('SELECT %s %s FROM %s %s WHERE %s.parent_id=0 %s',
                    $this->_get_select_fields(),
                    $this->_add_sql($add_sql, 'columns'),
                    $this->_node_table,
                    $this->_add_sql($add_sql, 'join'),
                    $this->_node_table,
                    $this->_add_sql($add_sql, 'append'));

    $this->_root_nodes_cache =& $this->_get_result_set($sql);
    return $this->_root_nodes_cache;
  }

  /**
  * Fetch the parents of a node
  */
  function & get_parents($node, $add_sql = array())
  {
    if (!$node = $this->get_node($node))
      return false;

    $join_table = $this->_node_table . '2';

    $sql = sprintf("SELECT %s %s
                    FROM {$this->_node_table}, {$this->_node_table} AS  {$join_table} %s
                    WHERE
                    {$join_table}.path LIKE %s AND
                    {$this->_node_table}.root_id = {$node['root_id']} AND
                    {$this->_node_table}.level < {$node['level']} AND
                    {$join_table}.id = {$node['id']}
                    %s
                    ORDER BY {$this->_node_table}.level ASC",
                    $this->_get_select_fields(),
                    $this->_add_sql($add_sql, 'columns'),
                    $this->_add_sql($add_sql, 'join'),
                    $this->_db->concat(array($this->_node_table . '.path', '"%"')),
                    $this->_add_sql($add_sql, 'append')
                  );

    $node_set =& $this->_get_result_set($sql);

    return $node_set;
  }

  /**
  * Fetch the immediate parent of a node
  */
  function & get_parent($node, $add_sql = array())
  {
    if (!$node = $this->get_node($node))
      return false;

    if ($node['id'] == $node['root_id'])
      return false;

    return $this->get_node($node['parent_id'], $add_sql);
  }

  /**
  * Fetch all siblings of the node
  * Important: The node given by ID will also be returned
  * Do a unset($array[$id]) on the result if you don't want that
  */
  function & get_siblings($node, $add_sql = array())
  {
    if (!($sibling = $this->get_node($node)))
      return false;

    return $this->get_children($this->get_parent($sibling), $add_sql);
  }

  /**
  * Fetch the children one level after of a node given by id
  */
  function & get_children($node, $add_sql = array())
  {
    if (!$parent = $this->get_node($node))
      return false;

    $sql = sprintf('SELECT %s %s FROM %s %s
                    WHERE %s.parent_id=%s %s',
                    $this->_get_select_fields(),
                    $this->_add_sql($add_sql, 'columns'),
                    $this->_node_table,
                    $this->_add_sql($add_sql, 'join'),
                    $this->_node_table, $parent['id'],
                    $this->_add_sql($add_sql, 'append'));

    $node_set =& $this->_get_result_set($sql);

    return $node_set;
  }

  function count_children($node, $add_sql=array())
  {
    if (!$parent = $this->get_node($node))
      return false;

    $sql = sprintf('SELECT count(id) as counter FROM %s %s
                    WHERE %s.parent_id=%s %s',
                    $this->_node_table,
                    $this->_add_sql($add_sql, 'join'),
                    $this->_node_table, $parent['id'],
                    $this->_add_sql($add_sql, 'append'));

    $this->_db->sql_exec($sql);
    $dataset = $this->_db->fetch_row();

    return (int)$dataset['counter'];
  }

  /**
  * Fetch all the children of a node
  *
  * get_children only queries the immediate children
  * get_sub_branch returns all nodes below the given node
  */
  function & get_sub_branch($node, $depth = -1, $include_parent = false, $check_expanded_parents = false, $only_parents = false, $add_sql = array())
  {
    if (!$parent_node = $this->get_node($node))
      return false;

    if ($depth != -1)
      $add_sql['append'][] = " AND {$this->_node_table}.level <=" . ($parent_node['level'] + $depth);

    if($only_parents)
    {
      if(!$this->_is_table_joined('sys_class', $add_sql))
        $add_sql['join'][] = ', sys_class as sc';

      if(!$this->_is_table_joined('sys_site_object', $add_sql))
        $add_sql['join'][] = ', sys_site_object as sso';

      $add_sql['append'][] = " AND {$this->_node_table}.object_id = sso.id AND sc.id = sso.class_id AND sc.can_be_parent = 1";
    }

    if($check_expanded_parents)
    {
      $sql_path_condition = '';
      $sql_for_expanded_parents = array();
      $sql_for_collapsed_parents = array();

      foreach($this->_expanded_parents as $data)
      {
        if(substr($data['path'], 0, strlen($parent_node['path'])) != $parent_node['path'])
          continue;

        if($data['status'] == false)
          $sql_for_collapsed_parents[] =
            " {$this->_node_table}.path NOT LIKE '{$data['path']}%%/' ";
        else
          $sql_for_expanded_parents[] =
            " {$this->_node_table}.path LIKE '{$data['path']}%%' ";
      }

      if($sql_for_expanded_parents)
        $sql_path_condition .= ' AND ( '. implode(' OR ', $sql_for_expanded_parents) . ')';

      if($sql_for_collapsed_parents)
        $sql_path_condition .= ' AND ' . implode(' AND ', $sql_for_collapsed_parents);

      $sql = sprintf("SELECT %s %s
                      FROM {$this->_node_table} %s
                      WHERE 1=1
                      $sql_path_condition AND
                      {$this->_node_table}.id!={$parent_node['id']} %s ORDER BY {$this->_node_table}.path",
                      $this->_get_select_fields(),
                      $this->_add_sql($add_sql, 'columns'),
                      $this->_add_sql($add_sql, 'join'),
                      $this->_add_sql($add_sql, 'append'));


    }
    else
    {
      $sql = sprintf("SELECT %s %s
                      FROM {$this->_node_table} %s
                      WHERE
                      {$this->_node_table}.path LIKE '{$parent_node['path']}%%' AND
                      {$this->_node_table}.id!={$parent_node['id']} %s ORDER BY {$this->_node_table}.path",
                      $this->_get_select_fields(),
                      $this->_add_sql($add_sql, 'columns'),
                      $this->_add_sql($add_sql, 'join'),
                      $this->_add_sql($add_sql, 'append'));
    }

    $node_set = array();

    if($include_parent)
      $node_set[$parent_node['id']] = $parent_node;

    $this->_assign_result_set($node_set, $sql);

    return $node_set;
  }

  function & get_sub_branch_by_path($path, $depth = -1, $include_parent = false, $check_expanded_parents = false, $only_parents = false, $add_sql = array())
  {
    if(!$parent_node = $this->get_node_by_path($path))
      return false;

    $nodes =& $this->get_sub_branch($parent_node, $depth, $include_parent, $check_expanded_parents, $only_parents, $add_sql);

    return $nodes;
  }

  function & get_accessible_sub_branch_by_path($path, $depth = -1, $include_parent = false, $check_expanded_parents = false, $class_id = null, $only_parents = false)
  {
    $add_sql['columns'][] = ', soa.object_id';

    if(!$this->_is_table_joined('sys_site_object', $add_sql))
      $add_sql['join'][] = ', sys_site_object as sso ';

    if(!$this->_is_table_joined('sys_object_access', $add_sql))
      $add_sql['join'][] = ', sys_object_access as soa ';

    $add_sql['append'][] = ' AND sso.id = ' . $this->_node_table . '.object_id AND sso.id = soa.object_id AND soa.access = 1';

    $access_policy =& access_policy :: instance();
    $accessor_ids = implode(',', $access_policy->get_accessor_ids());

    if($class_id)
      $add_sql['append'][] = " AND sso.class_id = {$class_id}";

    if($accessor_ids)
      $add_sql['append'][] = " AND soa.accessor_id IN ({$accessor_ids})";

    $result =& $this->get_sub_branch_by_path($path, $depth, $include_parent, $check_expanded_parents, $only_parents, $add_sql);

    return $result;
  }

  function count_accessible_children($node, $add_sql=array())
  {
    if (!($parent = $this->get_node($node)))
      return false;

    if(!$this->_is_table_joined('sys_site_object', $add_sql))
      $add_sql['join'][] = ', sys_site_object as sso ';

    if(!$this->_is_table_joined('sys_object_access', $add_sql))
      $add_sql['join'][] = ', sys_object_access as soa ';

    $add_sql['append'][] = ' AND sso.id = ' . $this->_node_table . '.object_id AND sso.id = soa.object_id AND soa.access = 1';

    $access_policy =& access_policy :: instance();
    $accessor_ids = implode(',', $access_policy->get_accessor_ids());

    $add_sql['append'][] = " AND soa.accessor_id IN ({$accessor_ids})";
    $add_sql['group'][] = ' GROUP BY ' . $this->_node_table . '.id';

    $sql = sprintf('SELECT count(*) as counter FROM %s %s
                    WHERE %s.root_id=%s AND %s.parent_id=%s %s %s',
                    $this->_node_table,
                    $this->_add_sql($add_sql, 'join'),
                    $this->_node_table,
                    $parent['root_id'],
                    $this->_node_table,
                    $parent['id'],
                    $this->_add_sql($add_sql, 'append'),
                    $this->_add_sql($add_sql, 'group')
                  );

    $this->_db->sql_exec($sql);

    return count($this->_db->get_array());
  }

  /**
  * Fetch the data of a node with the given id
  */
  function & get_node($node, $add_sql = array())
  {
    if(is_array($node))
      return $node;
    else
      $id = $node;

    $sql = sprintf('SELECT %s %s FROM %s %s WHERE %s.id=%s %s',
                    $this->_get_select_fields(),
                    $this->_add_sql($add_sql, 'columns'),
                    $this->_node_table,
                    $this->_add_sql($add_sql, 'join'),
                    $this->_node_table, $id,
                    $this->_add_sql($add_sql, 'append')
                  );

    $node_set =& $this->_get_result_set($sql);

    return current($node_set);
  }

  function get_path_to_node($node, $delimeter = '/')
  {
    if (!$node = $this->get_node($node))
      return false;

    if(($parents = $this->get_parents($node)) === false)
      return false;

    $path = '';
    foreach($parents as $parent_data)
      $path .= $delimeter . $parent_data['identifier'];

    return $path .= $delimeter . $node['identifier'];
  }

  function & get_node_by_path($path, $delimiter='/')
  {
    $path_array = explode($delimiter, $path);

    array_shift($path_array);

    if(end($path_array) == '')
      array_pop($path_array);

    $level = sizeof($path_array);

    if(!count($path_array))
      return false;

    $nodes = $this->get_all_nodes(
      array(
        'append' =>
          array(" AND {$this->_node_table}.identifier IN('" . implode("' , '", array_unique($path_array)) . "') AND {$this->_node_table}.level <= " . $level)
      )
    );

    if(!$nodes)
      return false;

    $curr_level = 0;
    $result_node_id = -1;
    $parent_id = 0;
    $path_to_node = '';

    foreach($nodes as $node)
    {
      if ($node['level'] < $curr_level)
        continue;

      if($node['identifier'] == $path_array[$curr_level] && $node['parent_id'] == $parent_id)
      {
        $parent_id = $node['id'];

        $curr_level++;
        $result_node_id = $node['id'];
        $path_to_node .= $delimiter . $node['identifier'];
        if ($curr_level == $level)
          break;
      }
    }

    if ($curr_level == $level)
      return isset($nodes[$result_node_id]) ? $nodes[$result_node_id] : false;

    return false;
  }

  function & get_nodes_by_ids($ids)
  {
    $node_set = array();

    if(!sizeof($ids))
      return $node_set;

    $add_sql = array(
      'append' => array(' AND ' . sql_in('id', $ids))
    );

    $root_nodes = $this->get_root_nodes();
    foreach($root_nodes as $root_id => $root_node)
    {
      if(in_array($root_id, $ids))
        $include_parents = true;
      else
        $include_parents = false;
      $node_set = $node_set + $this->get_sub_branch($root_node, -1, $include_parents, false, false, $add_sql);
    }
    return $node_set;

  }

  function get_max_child_identifier($node)
  {
    if (!($parent = $this->get_node($node)))
      return false;

    $sql = sprintf('SELECT identifier FROM %s
                    WHERE root_id=%s AND parent_id=%s',
                    $this->_node_table,
                    $parent['root_id'],
                    $parent['id']);

    $this->_db->sql_exec($sql);
    if($arr = array_keys($this->_db->get_array('identifier')))
    {
      uasort($arr, 'strnatcmp');
      return end($arr);
    }
    else
      return 0;
  }

  function is_node($node)
  {
    return ($this->get_node($node) !== false);
  }

  function is_node_expanded($node)
  {
    $node = $this->get_node($node);

    if(isset($this->_expanded_parents[$node['id']]))
      return $this->_expanded_parents[$node['id']]['status'];
    else
      return false;
  }

  function initialize_expanded_parents()
  {
  }

  function update_expanded_parents()
  {
    $nodes_ids = array_keys($this->_expanded_parents);

    $nodes =& $this->get_nodes_by_ids($nodes_ids);

    foreach($nodes as $id => $node)
      $this->_set_expanded_parent_status($node, $this->is_node_expanded($node));
  }

  function reset_expanded_parents()
  {
    $this->_expanded_parents = array();

    $root_nodes = $this->get_root_nodes();

    foreach($root_nodes as $id => $node)
    {
      $parents = $this->get_sub_branch($node, -1, true, false, true);

      foreach($parents as $parent)
      {
        if($parent['parent_id'] == 0)
          $this->_set_expanded_parent_status($parent, true);
        else
          $this->_set_expanded_parent_status($parent, false);
      }
    }
  }

  function _set_expanded_parent_status($node, $status)
  {
    $node = $this->get_node($node);

    $id = $node['id'];
    $this->_expanded_parents[$id]['path'] = $node['path'];
    $this->_expanded_parents[$id]['level'] = $node['level'];
    $this->_expanded_parents[$id]['status'] = $status;
  }

  /**
  * Creates a new root node.  If no id is specified then it is either
  * added to the beginning/end of the tree based on the $pos.
  * Optionally it deletes the whole tree and creates one initial rootnode
  *
  * <pre>
  * +-- root1 [target]
  * |
  * +-- root2 [new]
  * |
  * +-- root3
  * </pre>
  */
  function create_root_node($values)
  {
    $this->_verify_user_values($values);

    if (!$this->_dumb_mode)
      $values['id'] = $node_id = $this->_db->get_max_column_value($this->_node_table, 'id') + 1;
    else
      $node_id = $values['id'];

    $values['root_id'] = $node_id;
    $values['path'] = '/' . $node_id . '/';
    $values['level'] = 1;
    $values['parent_id'] = 0;
    $values['children'] = 0;

    $this->_db->sql_insert($this->_node_table, $values);

    return $node_id;
  }

  /**
  * Creates a subnode
  *
  * <pre>
  * +-- root1
  * |
  * +-\ root2 [target]
  * | |
  * | |-- subnode1 [new]
  * |
  * +-- root3
  * </pre>
  */
  function create_sub_node($node, $values)
  {
    if (!$node = $this->get_node($node))
      return false;

    $this->_verify_user_values($values);

    if (!$this->_dumb_mode)
    {
      $node_id = $this->_db->get_max_column_value($this->_node_table, 'id') + 1;
      $values['id'] = $node_id;
    }
    else
      $node_id = $values['id'];

    $values['root_id'] = $node['root_id'];
    $values['level'] = $node['level'] + 1;
    $values['parent_id'] = $node['id'];
    $values['path'] = $node['path'] . $node_id . '/';
    $values['children'] = 0;

    $this->_db->sql_insert($this->_node_table, $values);

    $this->_db->sql_update($this->_node_table,
                           array('children' => $node['children'] + 1),
                           array('id' => $node['id']));

    return $node_id;
  }

  /**
  * Deletes a node
  */
  function delete_node($node)
  {
    if (!$node = $this->get_node($node))
      return false;

    $this->_db->sql_exec("DELETE FROM {$this->_node_table}
                          WHERE
                          path LIKE '{$node['path']}%' AND
                          root_id={$node['root_id']}");

    $this->_db->sql_exec("UPDATE {$this->_node_table}
                          SET children = children - 1
                          WHERE
                          id = {$node['parent_id']}");

    return true;
  }

  function can_add_node($node)
  {
    if (!$this->is_node($node))
      return false;
    else
      return true;
  }

  function can_delete_node($node)
  {
    $amount = $this->count_children($node);

    if ($amount === false || $amount == 0)
      return true;
    else
      return false;
  }

  function can_move_tree($source_node, $target_node)//should return error codes not simply false...
  {
    if ($source_node == $target_node)
      return false;

    if (!$source_node = $this->get_node($source_node))
      return false;

    if (!$target_node = $this->get_node($target_node))
      return false;

    if (strstr($target_node['path'], $source_node['path']) !== false)
      return false;

    return true;
  }

  /**
  * Wrapper for node moving and copying
  */
  function move_tree($source_node, $target_node)
  {
    if ($source_node == $target_node)
      return false;

    if (!$source_node = $this->get_node($source_node))
      return false;

    if (!$target_node = $this->get_node($target_node))
      return false;

    if (strstr($target_node['path'], $source_node['path']) !== false)
      return false;

    $id = $source_node['id'];
    $target_id = $target_node['id'];

    $move_values = array('parent_id' => $target_id);
    $this->_db->sql_update($this->_node_table, $move_values, array('id' => $id));

    $src_path_len = strlen($source_node['path']);
    $sub_string = $this->_db->substr( 'path', 1, $src_path_len );
    $sub_string2 = $this->_db->substr( 'path', $src_path_len);

    $path_set =
      $this->_db->concat( array(
        "'{$target_node['path']}'" ,
        "'{$id}'",
        $sub_string2)
      );

    $sql = "UPDATE {$this->_node_table}
            SET
            path = {$path_set},
            level = level + {$target_node['level']} - {$source_node['level']} + 1,
            root_id = {$target_node['root_id']}
            WHERE
            {$sub_string} = '{$source_node['path']}' OR
            path = '{$source_node['path']}' ";


    $this->_db->sql_exec($sql);

    $this->_db->sql_exec("UPDATE {$this->_node_table}
                          SET children = children - 1
                          WHERE
                          id = {$source_node['parent_id']}");

    $this->_db->sql_exec("UPDATE {$this->_node_table}
                          SET children = children + 1
                          WHERE
                          id = {$target_id}");

    return true;
  }
}
?>