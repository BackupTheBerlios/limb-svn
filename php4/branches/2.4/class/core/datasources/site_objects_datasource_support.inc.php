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

function assignPathsToSiteObjects(&$objects_array, $append = '')
{
  $tree = Limb :: toolkit()->getTree();
  $parent_paths = array();

  foreach($objects_array as $key => $data)
  {
    $parent_node_id = $data['parent_node_id'];
    if (!isset($parent_paths[$parent_node_id]))
    {
      $parents = $tree->getParents($data['node_id']);
      $path = '';
      foreach($parents as $parent_data)
        $path .= '/' . $parent_data['identifier'];

      $parent_paths[$parent_node_id] = $path;
    }

    $objects_array[$key]['path'] = $parent_paths[$parent_node_id] . '/' . $data['identifier'] . $append;
  }
}

function wrapWithSiteObject($fetched_data)
{
  if(!$fetched_data)
    return false;

  if(!is_array($fetched_data))
    return false;

  if(isset($fetched_data['class_name']))
  {
    $site_object = Limb :: toolkit()->createSiteObject($fetched_data['class_name']);
    $site_object->merge($fetched_data);
    return $site_object;
  }

  $site_objects = array();
  foreach($fetched_data as $id => $data)
  {
    $site_object = Limb :: toolkit()->createSiteObject($data['class_name']);
    $site_object->merge($data);
    $site_objects[$id] = $site_object;
  }
  return $site_objects;
}

?>
