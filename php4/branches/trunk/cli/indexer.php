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
  $path = $argv[2];
else
  $path = '/root';

require_once($project_dir . '/setup.php');
require_once(LIMB_DIR . '/core/tree/tree.class.php');
require_once(LIMB_DIR . '/core/model/site_object_factory.class.php');
require_once(LIMB_DIR . '/core/model/search/full_text_indexer.class.php');
require_once(LIMB_DIR . '/core/lib/db/db_factory.class.php');

set_time_limit(3000);

$tree =& tree :: instance();
$indexer =& new full_text_indexer();
$db =& db_factory::instance();

echo "getting nodes from {$path}...\n";

$nodes =& $tree->get_sub_branch_by_path($path);

echo "nodes retrieved...\n";

$total = sizeof($nodes);

echo 'total nodes count = '. $total . "...\n";

$counter = 0;

$missed_objects = array();

foreach($nodes as $node)
{
  $db->sql_exec(
    'SELECT sc.class_name FROM sys_site_object sso, sys_class sc
    WHERE sso.class_id=sc.id AND sso.id=' . $node['object_id']);

  if(!$row = $db->fetch_row())//???
  {
    $missed_objects[] = $node['object_id'];
    continue;
  }

  $site_object =& site_object_factory :: create($row['class_name']);

  $object_data = current($site_object->fetch_by_ids(array($node['object_id'])));

  $site_object->merge_attributes($object_data);

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