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

require_once(LIMB_DIR . '/class/core/tree/drivers/NestedSetsTree.class.php');
require_once(LIMB_DIR . '/class/core/tree/MaterializedPathTree.class.php');

$db = DbFactory :: instance();
$db->sqlDelete($mp_table);

$ns = new NestedSetsTree();
$ns->setNodeTable($ns_table);
$mp = new MaterializedPathTree();
$mp->setNodeTable($mp_table);

$root_ns_nodes = $ns->getRootNodes();
$mp->setDumbMode();

echo 'total roots: ' . sizeof($root_ns_nodes) . "\n";

foreach($root_ns_nodes as $root_node)
{
  unset($root_node['l']);
  unset($root_node['r']);
  unset($root_node['ordr']);
  $mp->createRootNode($root_node);

  $sub_branch = $ns->getSubBranch($root_node['id'], -1);

  $branch_size = sizeof($sub_branch);
  echo 'total branches in current root: ' . $branch_size . "\n";

  $c = 0;
  foreach($sub_branch as $node)
  {
    echo ++$c . ' branch of ' . $branch_size . "\n";

    unset($node['l']);
    unset($node['r']);
    unset($node['ordr']);
    $mp->createSubNode($node['parent_id'], $node);
  }
}


?>