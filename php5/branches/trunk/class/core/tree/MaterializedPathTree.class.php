<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: nested_sets_tree.class.php 131 2004-04-09 14:11:45Z server $
*
***********************************************************************************/
require_once(LIMB_DIR . '/class/core/tree/tree.interface.php');

class materialized_path_tree implements tree
{
  protected $_db = null;

  protected $_node_table = 'sys_site_object_tree';

  protected $_params = array(
    'id' => 'id',
    'root_id' => 'root_id',
    'identifier' => 'identifier',
    'object_id' => 'object_id',
    'path' => 'path',
    'level' => 'level',
    'parent_id' => 'parent_id',
    'children' => 'children'
  );

  protected $_expanded_parents = array();

  protected $_required_params = array('id', 'root_id', 'path', 'level', 'children');

  protected $_dumb_mode = false;

  function __construct()
  {
    $this->_db = Limb :: toolkit()->getDB();
  }

  public function set_dumb_mode($status=true)
  {
    $prev_mode = $this->_dumb_mode;
    $this->_dumb_mode = $status;
    return $prev_mode;
  }

  public function set_node_table($table_name)
  {
    $this->_node_table = $table_name;
  }

  public function get_node_table()
  {
    return $this->_node_table;
  }

  /**
  * Gets the select fields based on the params
  */
  protected function _get_select_fields()
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
  protected function _verify_user_values(&$values)
  {
    if ($this->_dumb_mode)
      return true;

    foreach($values as $field => $value)
    {
      if (!isset($this->_params[$field]))
      {
        unset($values[$field]);
        continue;
      }

      if (in_array($this->_params[$field], $this->_required_params))
      {
        unset($values[$field]);
      }
    }
  }

  /**
  * Fetch the whole nested set
  */
  public function get_all_nodes()
  {
    $node_set = array();
    $root_nodes = $this->get_root_nodes();

    foreach($root_nodes as $root_id => $rootnode)
    {
      $node_set = $node_set + $this->get_sub_branch($root_id, -1, true, false);
    }
    return $node_set;
  }

  /**
  * Fetches the first level (the rootnodes)
  */
  public function get_root_nodes()
  {
    $sql = "SELECT " . $this->_get_select_fields() . "
            FROM {$this->_node_table} WHERE parent_id=0";

    $this->_db->sql_exec($sql);
    return $this->_db->get_array('id');
  }

  /**
  * Fetch the parents of a node given by id
  */
  public function get_parents($id)
  {
    if (!$child = $this->get_node($id))
      return false;

    $join_table = $this->_node_table . '2';
    $concat = $this->_db->concat(array($this->_node_table . '.path', '"%"'));

    $sql = "SELECT " . $this->_get_select_fields() . "
            FROM {$this->_node_table}, {$this->_node_table} AS  {$join_table}
            WHERE
            {$join_table}.path LIKE $concat AND
            {$this->_node_table}.root_id = {$child['root_id']} AND
            {$this->_node_table}.level < {$child['level']} AND
            {$join_table}.id = {$child['id']}
            ORDER BY {$this->_node_table}.level ASC";

    $this->_db->sql_exec($sql);
    return $this->_db->get_array('id');
  }

  /**
  * Fetch the immediate parent of a node given by id
  */
  public function get_parent($id)
  {
    if (!$child = $this->get_node($id))
      return false;

    if ($child['id'] == $child['root_id'])
      return false;

    return $this->get_node($child['parent_id']);
  }

  /**
  * Fetch all siblings of the node given by id
  * Important: The node given by ID will also be returned
  * Do aunset($array[$id]) on the result if you don't want that
  */
  public function get_siblings($id)
  {
    if (!($sibling = $this->get_node($id)))
      return false;

    $parent = $this->get_parent($sibling['id']);
    return $this->get_children($parent['id']);
  }

  /**
  * Fetch the children _one level_ after of a node given by id
  */
  public function get_children($id)
  {
    if (!$parent = $this->get_node($id))
      return false;

    $sql = "SELECT " . $this->_get_select_fields() . "
            FROM {$this->_node_table}
            WHERE parent_id={$parent['id']}";

    $this->_db->sql_exec($sql);
    return $this->_db->get_array('id');
  }

  public function count_children($id)
  {
    if (!$parent = $this->get_node($id))
      return false;

    $sql = "SELECT count(id) as counter FROM {$this->_node_table}
            WHERE parent_id={$id}";

    $this->_db->sql_exec($sql);
    $dataset = $this->_db->fetch_row();

    return (int)$dataset['counter'];
  }

  /**
  * Fetch all the children of a node given by id
  * get_children only queries the immediate children
  * get_sub_branch returns all nodes below the given node
  */
  public function get_sub_branch($id, $depth = -1, $include_parent = false, $check_expanded_parents = false)
  {
    if (!$parent_node = $this->get_node($id))
      return false;

    if ($depth != -1)
      $depth_condition = " AND level <=" . ($parent_node['level'] + $depth);
    else
      $depth_condition = '';

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
            " path NOT LIKE '{$data['path']}%%/' ";
        else
          $sql_for_expanded_parents[] =
            " path LIKE '{$data['path']}%%' ";
      }

      if($sql_for_expanded_parents)
        $sql_path_condition .= ' AND ( '. implode(' OR ', $sql_for_expanded_parents) . ')';

      if($sql_for_collapsed_parents)
        $sql_path_condition .= ' AND ' . implode(' AND ', $sql_for_collapsed_parents);

      $sql = "SELECT " . $this->_get_select_fields() . "
              FROM {$this->_node_table}
              WHERE
              id!={$id}
              {$sql_path_condition}
              {$depth_condition}
              ORDER BY path";

    }
    else
    {
      $sql = "SELECT " . $this->_get_select_fields() . "
              FROM {$this->_node_table}
              WHERE
              path LIKE '{$parent_node['path']}%%' AND
              id!={$id}
              {$depth_condition}
              ORDER BY path";
    }

    $node_set = array();

    if($include_parent)
      $node_set[$id] = $parent_node;

    $this->_db->sql_exec($sql);
    $this->_db->assign_array($node_set, 'id');

    return $node_set;
  }

  public function get_sub_branch_by_path($path, $depth = -1, $include_parent = false, $check_expanded_parents = false)
  {
    if(!$parent_node = $this->get_node_by_path($path))
      return false;

    return $this->get_sub_branch($parent_node['id'], $depth, $include_parent, $check_expanded_parents);
  }

  /**
  * Fetch the data of a node with the given id
  */
  public function get_node($id)
  {
    $sql = "SELECT " . $this->_get_select_fields() . "
            FROM {$this->_node_table} WHERE id={$id}";

    $this->_db->sql_exec($sql);
    return current($this->_db->get_array('id'));
  }

  public function get_node_by_path($path, $delimiter='/')
  {
    $path_array = explode($delimiter, $path);

    array_shift($path_array);

    if(end($path_array) == '')
      array_pop($path_array);

    $level = sizeof($path_array);

    if(!count($path_array))
      return false;

    $in_condition = $this->_db->sql_in('identifier', array_unique($path_array));

    $sql = "SELECT " . $this->_get_select_fields() . "
            FROM {$this->_node_table}
            WHERE
            {$in_condition}
            AND level <= {$level}
            ORDER BY path";

    $this->_db->sql_exec($sql);

    if(!$nodes = $this->_db->get_array('id'))
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

  public function get_nodes_by_ids($ids)
  {
    if(!$ids)
      return array();

    $sql = "SELECT " . $this->_get_select_fields() . "
            FROM {$this->_node_table}
            WHERE " . $this->_db->sql_in('id', $ids) . "
            ORDER BY path";

    $this->_db->sql_exec($sql);
    return $this->_db->get_array('id');
  }

  public function get_max_child_identifier($parent_id)
  {
    if (!($parent = $this->get_node($parent_id)))
      return false;

    $sql = "SELECT identifier FROM {$this->_node_table}
            WHERE
            root_id={$parent['root_id']} AND
            parent_id={$parent['id']}";

    $this->_db->sql_exec($sql);
    if($arr = array_keys($this->_db->get_array('identifier')))
    {
      uasort($arr, 'strnatcmp');
      return end($arr);
    }
    else
      return 0;
  }

  public function is_node($id)
  {
    return ($this->get_node($id) !== false);
  }

  public function is_node_expanded($id)
  {
    if(isset($this->_expanded_parents[$id]))
      return $this->_expanded_parents[$id]['status'];
    else
      return false;
  }

  /**
  * Changes the payload of a node
  */
  public function update_node($id, $values, $internal = false)
  {
    if(!$this->is_node($id))
      return false;

    if($internal === false)
      $this->_verify_user_values($values);

    return $this->_db->sql_update($this->_node_table, $values, array('id' => $id));
  }
  public function set_expanded_parents(& $expanded_parents)
  {
    $this->_expanded_parents =& $expanded_parents;

    $this->check_expanded_parents();
  }

  public function check_expanded_parents()
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

  public function toggle_node($id)
  {
    if(($node = $this->get_node($id)) === false)
      return false;

    $this->_set_expanded_parent_status($node, !$this->is_node_expanded($id));

    return true;
  }

  public function expand_node($id)
  {
    if(($node = $this->get_node($id)) === false)
      return false;

    $this->_set_expanded_parent_status($node, true);

    return true;
  }

  public function collapse_node($id)
  {
    if(($node = $this->get_node($id)) === false)
      return false;

    $this->_set_expanded_parent_status($node, false);

    return true;
  }

  public function update_expanded_parents()
  {
    $nodes_ids = array_keys($this->_expanded_parents);

    $nodes = $this->get_nodes_by_ids($nodes_ids);

    foreach($nodes as $id => $node)
      $this->_set_expanded_parent_status($node, $this->is_node_expanded($id));
  }

  public function reset_expanded_parents()
  {
    $this->_expanded_parents = array();

    $root_nodes = $this->get_root_nodes();

    foreach(array_keys($root_nodes) as $id)
    {
      $parents = $this->get_sub_branch($id, -1, true, false);

      foreach($parents as $parent)
      {
        if($parent['parent_id'] == 0)
          $this->_set_expanded_parent_status($parent, true);
        else
          $this->_set_expanded_parent_status($parent, false);
      }
    }
  }

  protected function _set_expanded_parent_status($node, $status)
  {
    $id = (int)$node['id'];
    $this->_expanded_parents[$id]['path'] = $node['path'];
    $this->_expanded_parents[$id]['level'] = $node['level'];
    $this->_expanded_parents[$id]['status'] = $status;
  }

  public function create_root_node($values)
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
  *
  */
  public function create_sub_node($parent_id, $values)
  {
    if (!$parent_node = $this->get_node($parent_id))
      return false;

    $this->_verify_user_values($values);

    if (!$this->_dumb_mode)
    {
      $node_id = $this->_db->get_max_column_value($this->_node_table, 'id') + 1;
      $values['id'] = $node_id;
    }
    else
      $node_id = $values['id'];

    $values['root_id'] = $parent_node['root_id'];
    $values['level'] = $parent_node['level'] + 1;
    $values['parent_id'] = $parent_id;
    $values['path'] = $parent_node['path'] . $node_id . '/';
    $values['children'] = 0;

    $this->_db->sql_insert($this->_node_table, $values);

    $this->_db->sql_update($this->_node_table,
                           array('children' => $parent_node['children'] + 1),
                           array('id' => $parent_id));

    return $node_id;
  }

  /**
  * Deletes a node
  */
  public function delete_node($id)
  {
    if (!$node = $this->get_node($id))
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

  /**
  * Moves node
  */
  public function move_tree($id, $target_id)
  {
    if ($id == $target_id)
      return false;

    if (!$source_node = $this->get_node($id))
      return false;

    if (!$target_node = $this->get_node($target_id))
      return false;

    if (strstr($target_node['path'], $source_node['path']) !== false)
      return false;

    $move_values = array('parent_id' => $target_id);
    $this->_db->sql_update($this->_node_table, $move_values, array('id' => $id));

    $src_path_len = strlen($source_node['path']);
    $sub_string = $this->_db->substr('path', 1, $src_path_len);
    $sub_string2 = $this->_db->substr('path', $src_path_len);

    $path_set =
      $this->_db->concat( array(
        "'{$target_node['path']}'" ,
        "'{$id}'",
        $sub_string2)
      );

    $this->_db->sql_exec("UPDATE {$this->_node_table}
                          SET
                          path = {$path_set},
                          level = level + {$target_node['level']} - {$source_node['level']} + 1,
                          root_id = {$target_node['root_id']}
                          WHERE
                          {$sub_string} = '{$source_node['path']}' OR
                          path = '{$source_node['path']}'");

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