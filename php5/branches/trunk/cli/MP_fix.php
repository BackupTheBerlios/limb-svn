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

require_once($project_dir . '/setup.php');
require_once(LIMB_DIR . '/class/core/tree/MaterializedPathTree.class.php');

$tree = new MaterializedPathTree();
$driver = $tree->getDriver();
$tree_table = $driver->getNodeTable();

$root_nodes = $tree->getRootNodes();

$db = DbFactory :: instance();

echo 'total roots: ' . sizeof($root_ns_nodes) . "\n";

foreach($root_nodes as $root_node)
{
  $sub_branch = $tree->getSubBranch($root_node['id'], -1);

  $branch_size = sizeof($sub_branch);
  echo 'total nodes in current root: ' . $branch_size . "\n";

  $c = 0;
  foreach($sub_branch as $node)
  {
    echo ++$c . ' branch of ' . $branch_size . ': ';

    $children = $tree->countChildren($node['id']);

    $db->sqlSelect($tree_table, 'children', array('id' => $node['id']));
    $row = $db->fetchRow();

    if($row['children'] != $children)
    {
      echo "expected {$children} found {$row['children']}...fixing...";
      $db->sqlUpdate($tree_table, array('children' => $children), array('id' => $node['id']));
      echo 'ok';
    }
    else
      echo 'ok';

    echo "\n";

  }
}


?>