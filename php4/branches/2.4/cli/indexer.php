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
  $path = $argv[2];
else
  $path = '/root';

require_once($project_dir . '/setup.php');
require_once(LIMB_DIR . '/class/core/tree/MaterializedPathTree.class.php');
require_once(LIMB_DIR . '/class/core/site_objects/SiteObjectFactory.class.php');
require_once(LIMB_DIR . '/class/search/FullTextIndexer.class.php');
require_once(LIMB_DIR . '/class/lib/db/DbFactory.class.php');

set_time_limit(3000);

$tree = materializedPathTree();
$indexer = new FullTextIndexer();
$db = DbFactory::instance();

echo "getting nodes from {$path}...\n";

$nodes =& $tree->getSubBranchByPath($path);

echo "nodes retrieved...\n";

$total = sizeof($nodes);

echo 'total nodes count = '. $total . "...\n";

$counter = 0;

$missed_objects = array();

foreach($nodes as $node)
{
  $db->sqlExec(
    'SELECT sc.class_name FROM sys_site_object sso, sys_class sc
    WHERE sso.class_id=sc.id AND sso.id=' . $node['object_id']);

  if(!$row = $db->fetchRow())//???
  {
    $missed_objects[] = $node['object_id'];
    continue;
  }

  $site_object = Limb :: toolkit()->createSiteObject($row['ClassName']);

  $object_data = current($site_object->fetchByIds(array($node['object_id'])));

  $site_object->merge($object_data);

  $counter++;

  echo "indexing {$counter} of {$total}...\n";

  $indexer->add($site_object);
}

foreach($missed_objects as $id)
{
  echo "missed object_id: {$id}...\n";
}

echo 'done';

?>