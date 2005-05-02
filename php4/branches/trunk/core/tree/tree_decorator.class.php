<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: tree.class.php 916 2004-11-23 09:14:28Z pachanga $
*
***********************************************************************************/

class tree_decorator
{
  var $tree_imp = null;

  function tree_decorator(&$imp)
  {
    $this->tree_imp =& $imp;
  }

  function initialize_expanded_parents()
  {
    $this->tree_imp->initialize_expanded_parents();
  }

  function is_node($node)
  {
    return $this->tree_imp->is_node($node);
  }

  function & get_node($node)
  {
    return $this->tree_imp->get_node($node);
  }

  function & get_parent($node)
  {
    return $this->tree_imp->get_parent($node);
  }

  function & get_parents($node)
  {
    return $this->tree_imp->get_parents($node);
  }

  function & get_siblings($node)
  {
    return $this->tree_imp->get_siblings($node);
  }

  function & get_children($node)
  {
    return $this->tree_imp->get_children($node);
  }

  function count_children($node)
  {
    return $this->tree_imp->count_children($node);
  }

  function create_root_node($values)
  {
    return $this->tree_imp->create_root_node($values);
  }

  function create_sub_node($node, $values)
  {
    return $this->tree_imp->create_sub_node($node, $values);
  }

  function delete_node($node)
  {
    return $this->tree_imp->delete_node($node);
  }

  function update_node($node, $values, $internal = false)
  {
    return $this->tree_imp->update_node($node, $values, $internal);
  }

  function can_move_tree($node, $target_id)
  {
    return $this->tree_imp->can_move_tree($node, $target_id);
  }

  function move_tree($node, $target_id)
  {
    return $this->tree_imp->move_tree($node, $target_id);
  }

  function set_dumb_mode($status=true)
  {
    $this->tree_imp->set_dumb_mode($status);
  }

  function & get_all_nodes()
  {
    return $this->tree_imp->get_all_nodes();
  }

  function & get_nodes_by_ids($nodes_array)
  {
    return $this->tree_imp->get_nodes_by_ids($nodes_array);
  }

  function get_path_to_node($node, $delimiter='/')
  {
    return $this->tree_imp->get_path_to_node($node, $delimiter);
  }

  function get_max_child_identifier($node)
  {
    return $this->tree_imp->get_max_child_identifier($node);
  }

  function & get_node_by_path($path, $delimiter='/')
  {
    return $this->tree_imp->get_node_by_path($path, $delimiter);
  }

  function & get_sub_branch($node, $depth = -1, $include_parent = false, $check_expanded_parents = false, $only_parents = false)
  {
    return $this->tree_imp->get_sub_branch($node, $depth, $include_parent, $check_expanded_parents, $only_parents);
  }

  function & get_sub_branch_by_path($path, $depth = -1, $include_parent = false, $check_expanded_parents = false, $only_parents = false)
  {
    return $this->tree_imp->get_sub_branch_by_path($path, $depth, $include_parent, $check_expanded_parents, $only_parents);
  }

  function & get_accessible_sub_branch_by_path($path, $depth = -1, $include_parent = false, $check_expanded_parents = false, $class_id = null, $only_parents = false)
  {
    return $this->tree_imp->get_accessible_sub_branch_by_path($path, $depth, $include_parent, $check_expanded_parents, $class_id, $only_parents);
  }

  function & get_root_nodes()
  {
    return $this->tree_imp->get_root_nodes();
  }

  function count_accessible_children($node)
  {
    return $this->tree_imp->count_accessible_children($node);
  }

  function is_node_expanded($node)
  {
    return $this->tree_imp->is_node_expanded($node);
  }

  function toggle_node($node)
  {
    return $this->tree_imp->toggle_node($node);
  }

  function expand_node($node)
  {
    return $this->tree_imp->expand_node($node);
  }

  function collapse_node($node)
  {
    return $this->tree_imp->collapse_node($node);
  }

  function can_add_node($node)
  {
    return $this->tree_imp->can_add_node($node);
  }

  function can_delete_node($node)
  {
    return $this->tree_imp->can_delete_node($node);
  }

  function check_expanded_parents()
  {
    $this->tree_imp->check_expanded_parents();
  }

  function update_expanded_parents()
  {
    $this->tree_imp->update_expanded_parents();
  }
}

?>