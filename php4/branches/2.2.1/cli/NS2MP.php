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
if(isset($argv[1]))  
	$project_dir = $argv[1];
else
	die('project dir required');

if(isset($argv[2]))  
	$ns_table = $argv[2];
else
	$ns_table = 'old_sys_site_object_tree';

if(isset($argv[3]))  
	$mp_table = $argv[3];
else
	$mp_table = 'sys_site_object_tree';
	
require_once($project_dir . '/setup.php'); 

require_once(LIMB_DIR . '/core/tree/drivers/nested_sets_driver.class.php');
require_once(LIMB_DIR . '/core/tree/drivers/materialized_path_driver.class.php');

$db = db_factory :: instance();
$db->sql_delete($mp_table);

$ns = new nested_sets_driver();
$ns->set_node_table($ns_table);
$mp = new materialized_path_driver();
$mp->set_node_table($mp_table);

$root_ns_nodes = $ns->get_root_nodes();
$mp->set_dumb_mode();

echo 'total roots: ' . sizeof($root_ns_nodes) . "\n";

foreach($root_ns_nodes as $root_node)
{
	unset($root_node['l']);
	unset($root_node['r']);
	unset($root_node['ordr']);
	$mp->create_root_node($root_node);
	
	$sub_branch = $ns->get_sub_branch($root_node['id'], -1);
	
	$branch_size = sizeof($sub_branch);
	echo 'total branches in current root: ' . $branch_size . "\n";
	
	$c = 0;
	foreach($sub_branch as $node)
	{
		echo ++$c . ' branch of ' . $branch_size . "\n";
		
		unset($node['l']);
		unset($node['r']);
		unset($node['ordr']);
		$mp->create_sub_node($node['parent_id'], $node);
	}
}


?>