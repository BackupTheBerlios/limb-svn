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

class MaterializedPathTree// implements Tree
{
  var $_conn = null;
  var $_db = null;

  var $_node_table = 'sys_tree';

  var $_params = array(
    'id' => 'id',
    'root_id' => 'root_id',
    'identifier' => 'identifier',
    'path' => 'path',
    'level' => 'level',
    'parent_id' => 'parent_id',
    'children' => 'children'
  );

  var $_expanded_parents = array();

  var $_required_params = array('id', 'root_id', 'path', 'level', 'children');

  var $_dumb_mode = false;

  function MaterializedPathTree()
  {
    $toolkit =& Limb :: toolkit();
    $this->_conn =& $toolkit->getDbConnection();
    $this->_db =& new SimpleDB($this->_conn);
  }

  function setDumbMode($status=true)
  {
    $prev_mode = $this->_dumb_mode;
    $this->_dumb_mode = $status;
    return $prev_mode;
  }

  function setNodeTable($table_name)
  {
    $this->_node_table = $table_name;
  }

  function getNodeTable()
  {
    return $this->_node_table;
  }

  /**
  * Gets the select fields based on the params
  */
  function _getSelectFields()
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
  function _verifyUserValues(&$values)
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
  * Fetches the first level (the rootnodes)
  */
  function & getRootNodes()
  {
    $sql = "SELECT " . $this->_getSelectFields() . "
            FROM {$this->_node_table} WHERE parent_id=0";

    $stmt =& $this->_conn->newStatement($sql);
    return $stmt->getRecordSet();
  }

  /**
  * Fetch the parents of a node given by id
  */
  function & getParents($id)
  {
    if (!$child = $this->getNode($id))
      return null;

    $join_table = $this->_node_table . '2';
    $concat = $this->_dbConcat(array($this->_node_table . '.path', '"%"'));

    $sql = "SELECT " . $this->_getSelectFields() . "
            FROM {$this->_node_table}, {$this->_node_table} AS  {$join_table}
            WHERE
            {$join_table}.path LIKE {$concat} AND
            {$this->_node_table}.root_id = :root_id: AND
            {$this->_node_table}.level < :level: AND
            {$join_table}.id = :id:
            ORDER BY {$this->_node_table}.level ASC";

    $stmt =& $this->_conn->newStatement($sql);
    $stmt->setInteger('root_id', $child['root_id']);
    $stmt->setVarChar('level', $child['level']);
    $stmt->setVarChar('id', $child['id']);

    return $stmt->getRecordSet();
  }

  /**
  * Fetch the immediate parent of a node given by id
  */
  function getParent($id)
  {
    if (!$child = $this->getNode($id))
      return null;

    if ($child['id'] == $child['root_id'])
      return null;

    return $this->getNode($child['parent_id']);
  }

  /**
  * Fetch all siblings of the node given by id
  */
  function & getSiblings($id)
  {
    if (!($sibling = $this->getNode($id)))
      return null;

    $parent = $this->getParent($sibling['id']);
    return $this->getChildren($parent['id']);
  }

  /**
  * Fetch the children _one level_ after of a node given by id
  */
  function & getChildren($id)
  {
    if (!$parent = $this->getNode($id))
      return null;

    $sql = "SELECT " . $this->_getSelectFields() . "
            FROM {$this->_node_table}
            WHERE parent_id = :parent_id:";

    $stmt =& $this->_conn->newStatement($sql);
    $stmt->set('parent_id', $parent['id']);

    return $stmt->getRecordSet();
  }

  function countChildren($id)
  {
    if (!$parent = $this->getNode($id))
      return null;

    $sql = "SELECT count(id) as counter FROM {$this->_node_table}
            WHERE parent_id = :parent_id:";

    $stmt =& $this->_conn->newStatement($sql);
    $stmt->set('parent_id', $id);
    return $stmt->getOneValue();
  }

  /**
  * Fetch all the children of a node given by id
  */
  function & getSubBranch($id, $depth = -1, $include_parent = false, $check_expanded_parents = false)
  {
    if (!$parent_node = $this->getNode($id))
      return null;

    if ($depth != -1)
      $depth_condition = " AND level <=" . ($parent_node['level'] + $depth);
    else
      $depth_condition = '';

    if($include_parent)
      $include_parent_condition = '';
    else
      $include_parent_condition = " AND id!={$id}";

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
        $sql_path_condition .= ' ( '. implode(' OR ', $sql_for_expanded_parents) . ')';

      if($sql_path_condition && $sql_for_collapsed_parents)
        $sql_path_condition .= ' AND ';

      if($sql_for_collapsed_parents)
        $sql_path_condition .= implode(' AND ', $sql_for_collapsed_parents);

      $sql = "SELECT " . $this->_getSelectFields() . "
              FROM {$this->_node_table}
              WHERE
              {$sql_path_condition}
              {$depth_condition}
              {$include_parent_condition}
              ORDER BY path";

    }
    else
    {
      $sql = "SELECT " . $this->_getSelectFields() . "
              FROM {$this->_node_table}
              WHERE
              path LIKE '{$parent_node['path']}%%'
              {$depth_condition}
              {$include_parent_condition}
              ORDER BY path";
    }

    $stmt =& $this->_conn->newStatement($sql);
    return $stmt->getRecordSet();
  }

  function & getSubBranchByPath($path, $depth = -1, $include_parent = false, $check_expanded_parents = false)
  {
    if(!$parent_node = $this->getNodeByPath($path))
      return null;

    return $this->getSubBranch($parent_node['id'], $depth, $include_parent, $check_expanded_parents);
  }

  /**
  * Fetch the data of a node with the given id
  */
  function getNode($id)
  {
    $sql = "SELECT " . $this->_getSelectFields() . "
            FROM {$this->_node_table} WHERE id=:id:";

    $stmt =& $this->_conn->newStatement($sql);
    $stmt->setInteger('id', $id);

    if($r = $stmt->getOneRecord())
      return $r->export();

    return null;
  }

  function getNodeByPath($path, $delimiter='/')
  {
    $path_array = explode($delimiter, $path);

    array_shift($path_array);

    if(end($path_array) == '')
      array_pop($path_array);

    $level = sizeof($path_array);

    if(!count($path_array))
      return null;

    $in_condition = $this->_dbIn('identifier', array_unique($path_array));

    $sql = "SELECT " . $this->_getSelectFields() . "
            FROM {$this->_node_table}
            WHERE
            {$in_condition}
            AND level <= {$level}
            ORDER BY path";

    $stmt =& $this->_conn->newStatement($sql);
    $rs =& $stmt->getRecordSet();

    $curr_level = 0;
    $parent_id = 0;
    $path_to_node = '';

    for($rs->rewind();$rs->valid();$rs->next())
    {
      $record = $rs->current();
      $node = $record->export();

      if ($node['level'] < $curr_level)
        continue;

      if($node['identifier'] == $path_array[$curr_level] &&
         $node['parent_id'] == $parent_id)
      {
        $parent_id = $node['id'];

        $curr_level++;
        $path_to_node .= $delimiter . $node['identifier'];

        if ($curr_level == $level)
          return $node;
      }
    }

    return null;
  }

  function & getNodesByIds($ids)
  {
    $sql = "SELECT " . $this->_getSelectFields() . "
            FROM {$this->_node_table}
            WHERE " . $this->_dbIn('id', $ids) . "
            ORDER BY path";

    $stmt =& $this->_conn->newStatement($sql);
    return $stmt->getRecordSet();
  }

  function getMaxChildIdentifier($parent_id)
  {
    if (!($parent = $this->getNode($parent_id)))
      return false;

    $sql = "SELECT identifier FROM {$this->_node_table}
            WHERE
            root_id=:root_id: AND
            parent_id=:parent_id:";

    $stmt =& $this->_conn->newStatement($sql);
    $stmt->setInteger('root_id', $parent['root_id']);
    $stmt->setInteger('parent_id', $parent['id']);

    if($arr = $stmt->getOneColumnAsArray())
    {
      uasort($arr, 'strnatcmp');
      return end($arr);
    }
    else
      return 0;
  }

  function isNode($id)
  {
    return ($this->getNode($id) !== null);
  }

  function isNodeExpanded($id)
  {
    if(isset($this->_expanded_parents[$id]))
      return $this->_expanded_parents[$id]['status'];
    else
      return false;
  }

  /**
  * Changes the payload of a node
  */
  function updateNode($id, $values, $internal = false)
  {
    if(!$this->isNode($id))
      return false;

    if($internal === false)
      $this->_verifyUserValues($values);

    $this->_db->update($this->_node_table, $values, array('id' => $id));

    return true;//???
  }
  function setExpandedParents(& $expanded_parents)
  {
    $this->_expanded_parents =& $expanded_parents;

    $this->checkExpandedParents();
  }

  function checkExpandedParents()
  {
    if(!is_array($this->_expanded_parents) ||  sizeof($this->_expanded_parents) == 0)
    {
      $this->resetExpandedParents();
    }
    elseif(sizeof($this->_expanded_parents) > 0)
    {
      $this->updateExpandedParents();
    }
  }

  function toggleNode($id)
  {
    if(($node = $this->getNode($id)) === false)
      return false;

    $this->_setExpandedParentStatus($node, !$this->isNodeExpanded($id));

    return true;
  }

  function expandNode($id)
  {
    if(($node = $this->getNode($id)) === false)
      return false;

    $this->_setExpandedParentStatus($node, true);

    return true;
  }

  function collapseNode($id)
  {
    if(($node = $this->getNode($id)) === false)
      return false;

    $this->_setExpandedParentStatus($node, false);

    return true;
  }

  function updateExpandedParents()
  {
    $nodes_ids = array_keys($this->_expanded_parents);

    $nodes = $this->getNodesByIds($nodes_ids);

    foreach($nodes as $id => $node)
      $this->_setExpandedParentStatus($node, $this->isNodeExpanded($id));
  }

  function resetExpandedParents()
  {
    $this->_expanded_parents = array();

    $rs =& $this->getRootNodes();

    for($rs->rewind(); $rs->valid(); $rs->next())
    {
      $record = $rs->current();
      $branch_set =& $this->getSubBranch($record->get('id'), -1, true, false);

      for($branch_set->rewind(); $branch_set->valid(); $branch_set->next())
      {
        $record = $branch_set->current();
        $parent = $record->export();
        if($parent['parent_id'] == 0)
          $this->_setExpandedParentStatus($parent, true);
        else
          $this->_setExpandedParentStatus($parent, false);
      }
    }
  }

  function _setExpandedParentStatus($node, $status)
  {
    $id = (int)$node['id'];
    $this->_expanded_parents[$id]['path'] = $node['path'];
    $this->_expanded_parents[$id]['level'] = $node['level'];
    $this->_expanded_parents[$id]['status'] = $status;
  }

  function _getNextNodeInsertId()
  {
    $sql = 'SELECT MAX(id) as m FROM '. $this->_node_table;
    $stmt =& $this->_conn->newStatement($sql);
    $max = $stmt->getOneValue();

    return isset($max) ? $max + 1 : 1;
  }

  //this is very dirty hack, since this functionality MUST reside
  //somewhere in specific connection classes, furthermore it's tested with
  //MySQL only!!!
  function _dbConcat($values)
  {
    $str = implode(',' , $values);
    return " CONCAT({$str}) ";
  }

  //the same story...
  function _dbSubstr($string, $offset, $limit=null)
  {
    if ($limit === null)
      return " SUBSTRING({$string} FROM {$offset}) ";
    else
      return " SUBSTRING({$string} FROM {$offset} FOR {$limit}) ";
  }


  function _dbIn($column_name, $values)
  {
    $in_ids = implode('","', $values);

    return $column_name . ' IN ("' . $in_ids . '")';
  }

  function createRootNode($values)
  {
    $this->_verifyUserValues($values);

    if (!$this->_dumb_mode)
      $values['id'] = $node_id = $this->_getNextNodeInsertId();
    else
      $node_id = $values['id'];

    $values['root_id'] = $node_id;
    $values['path'] = '/' . $node_id . '/';
    $values['level'] = 1;
    $values['parent_id'] = 0;
    $values['children'] = 0;

    $this->_db->insert($this->_node_table, $values);

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
  function createSubNode($parent_id, $values)
  {
    if (!$parent_node = $this->getNode($parent_id))
      return false;

    $this->_verifyUserValues($values);

    if (!$this->_dumb_mode)
    {
      $node_id = $this->_getNextNodeInsertId();
      $values['id'] = $node_id;
    }
    else
      $node_id = $values['id'];

    $values['root_id'] = $parent_node['root_id'];
    $values['level'] = $parent_node['level'] + 1;
    $values['parent_id'] = $parent_id;
    $values['path'] = $parent_node['path'] . $node_id . '/';
    $values['children'] = 0;

    $this->_db->insert($this->_node_table, $values);

    $this->_db->update($this->_node_table,
                           array('children' => $parent_node['children'] + 1),
                           array('id' => $parent_id));

    return $node_id;
  }

  /**
  * Deletes a node
  */
  function deleteNode($id)
  {
    if (!$node = $this->getNode($id))
      return false;

    $stmt =& $this->_conn->newStatement("DELETE FROM {$this->_node_table}
                                        WHERE
                                        path LIKE :path: AND
                                        root_id = :root_id:");

    $stmt->setVarChar('path', $node['path'] . '%');
    $stmt->setInteger('root_id', $node['root_id']);

    $stmt->execute();

    $stmt =& $this->_conn->newStatement("UPDATE {$this->_node_table}
                                        SET children = children - 1
                                        WHERE
                                        id = :id:");

    $stmt->setInteger('id', $node['parent_id']);

    $stmt->execute();

    return true;
  }

  /**
  * Moves node
  */
  function moveTree($id, $target_id)
  {
    if ($id == $target_id)
      return false;

    if (!$source_node = $this->getNode($id))
      return false;

    if (!$target_node = $this->getNode($target_id))
      return false;

    if (strstr($target_node['path'], $source_node['path']) !== false)
      return false;

    $move_values = array('parent_id' => $target_id);
    $this->_db->update($this->_node_table, $move_values, array('id' => $id));

    $src_path_len = strlen($source_node['path']);
    $sub_string = $this->_dbSubstr('path', 1, $src_path_len);
    $sub_string2 = $this->_dbSubstr('path', $src_path_len);

    $path_set =
      $this->_dbConcat( array(
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
            path = '{$source_node['path']}'";

    $stmt =& $this->_conn->newStatement($sql);
    $stmt->execute();

    $sql = "UPDATE {$this->_node_table}
            SET children = children - 1
            WHERE
            id = {$source_node['parent_id']}";

    $stmt =& $this->_conn->newStatement($sql);
    $stmt->execute();

    $sql = "UPDATE {$this->_node_table}
            SET children = children + 1
            WHERE
            id = {$target_id}";

    $stmt =& $this->_conn->newStatement($sql);
    $stmt->execute();

    return true;
  }
}

?>