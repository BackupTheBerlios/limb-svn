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

interface tree
{
	public function is_node($id);

	public function get_node($id);

	public function get_parent($id);

	public function get_parents($id);

	public function get_siblings($id);

	public function get_children($id);

	public function count_children($id);

	public function create_root_node($values);

	public function create_sub_node($id, $values);

	public function delete_node($id);

	public function update_node($id, $values, $internal = false);

	public function move_tree($id, $target_id);

	public function set_dumb_mode($status=true);

	public function get_all_nodes();

	public function get_nodes_by_ids($ids_array);

	public function get_max_child_identifier($id);

	public function get_node_by_path($path, $delimiter='/');

	public function get_sub_branch($id, $depth = -1, $include_parent = false, $check_expanded_parents = false);

	public function get_sub_branch_by_path($path, $depth = -1, $include_parent = false, $check_expanded_parents = false);

	public function get_root_nodes();

  public function is_node_expanded($id);

  public function toggle_node($id);

  public function expand_node($id);

  public function collapse_node($id);
} 

?>