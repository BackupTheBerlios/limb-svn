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
if(isset($argv[1]))  
	$project_dir = $argv[1];
else
	die('project dir required');
	
require_once($project_dir . '/setup.php'); 
require_once(LIMB_DIR . '/class/core/tree/tree.class.php');

$tree = tree :: instance();
$driver = $tree->get_driver();
$tree_table = $driver->get_node_table();

$root_nodes = $tree->get_root_nodes();

$db = db_factory :: instance();

echo 'total roots: ' . sizeof($root_ns_nodes) . "\n";

foreach($root_nodes as $root_node)
{
	$sub_branch = $tree->get_sub_branch($root_node['id'], -1);
	
	$branch_size = sizeof($sub_branch);
	echo 'total nodes in current root: ' . $branch_size . "\n";
	
	$c = 0;
	foreach($sub_branch as $node)
	{
		echo ++$c . ' branch of ' . $branch_size . ': ';
		
		$children = $tree->count_children($node['id']);
		
		$db->sql_select($tree_table, 'children', array('id' => $node['id']));
		$row = $db->fetch_row();
				
		if($row['children'] != $children)
		{
			echo "expected {$children} found {$row['children']}...fixing...";
			$db->sql_update($tree_table, array('children' => $children), array('id' => $node['id']));
			echo 'ok';
		}
		else
			echo 'ok';
		
		echo "\n";
		
	}
}


?>