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
require_once(LIMB_DIR . '/class/core/tree/tree.interface.php');

class tree_decorator implements tree
{
  protected $_tree = null;

  function __construct($tree)
  {
    $this->_tree = $tree;
  }

  public function is_node($id)
  {
    return $this->_tree->is_node($id);
  }

  public function get_node($id)
  {
    return $this->_tree->get_node($id);
  }

  public function get_parent($id)
  {
    return $this->_tree->get_parent($id);
  }

  public function get_parents($id)
  {
    return $this->_tree->get_parents($id);
  }

  public function get_siblings($id)
  {
    return $this->_tree->get_siblings($id);
  }

  public function get_children($id)
  {
    return $this->_tree->get_children($id);
  }

  public function count_children($id)
  {
    return $this->_tree->count_children($id);
  }

  public function create_root_node($values)
  {
    return $this->_tree->create_root_node($values);
  }

  public function create_sub_node($id, $values)
  {
    return $this->_tree->create_sub_node($id, $values);
  }

  public function delete_node($id)
  {
    return $this->_tree->delete_node($id);
  }

  public function update_node($id, $values, $internal = false)
  {
    return $this->_tree->update_node($id, $values, $internal);
  }

  public function move_tree($id, $target_id)
  {
    return $this->_tree->move_tree($id, $target_id);
  }

  public function set_dumb_mode($status=true)
  {
    $this->_tree->set_dumb_mode($status);
  }

  public function get_all_nodes()
  {
    return $this->_tree->get_all_nodes();
  }

  public function get_nodes_by_ids($ids_array)
  {
    return $this->_tree->get_nodes_by_ids($ids_array);
  }

  public function get_path_to_node($node, $delimeter = '/')
  {
    if(($parents = $this->_tree->get_parents($node['id'])) === false)
      return false;

    $path = '';
    foreach($parents as $parent_data)
      $path .= $delimeter . $parent_data['identifier'];

    return $path .= $delimeter . $node['identifier'];
  }

  public function get_max_child_identifier($id)
  {
    return $this->_tree->get_max_child_identifier($id);
  }

  public function get_node_by_path($path, $delimiter='/')
  {
    return $this->_tree->get_node_by_path($path, $delimiter);
  }

  public function get_sub_branch($id, $depth = -1, $include_parent = false, $check_expanded_parents = false)
  {
    return $this->_tree->get_sub_branch($id, $depth, $include_parent, $check_expanded_parents);
  }

  public function get_sub_branch_by_path($path, $depth = -1, $include_parent = false, $check_expanded_parents = false)
  {
    return $this->_tree->get_sub_branch_by_path($path, $depth, $include_parent, $check_expanded_parents);
  }

  public function get_root_nodes()
  {
    return $this->_tree->get_root_nodes();
  }

  public function is_node_expanded($id)
  {
    return $this->_tree->is_node_expanded($id);
  }

  public function toggle_node($id)
  {
    return $this->_tree->toggle_node($id);
  }

  public function expand_node($id)
  {
    return $this->_tree->expand_node($id);
  }

  public function collapse_node($id)
  {
    return $this->_tree->collapse_node($id);
  }

  public function can_add_node($id)
  {
    if (!$this->is_node($id))
      return false;
    else
      return true;
  }

  public function can_delete_node($id)
  {
    $amount = $this->count_children($id);

    if ($amount === false || $amount == 0)
      return true;
    else
      return false;
  }
}

?>