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
require_once(LIMB_DIR . 'core/datasource/datasource.class.php');
require_once(LIMB_DIR . 'core/model/links_manager.class.php');

class node_links_datasource extends datasource
{
	function & get_dataset(&$counter, $params=array())
	{
		$counter = 0;
		
		$mapped_node = map_current_request_to_node();
		
		$links_manager = new links_manager();
		
		$groups = array();
		
		if(isset($params['group_identifier']))
		{
		  if($group = $links_manager->fetch_group_by_identifier($params['group_identifier']))
		    $groups[$group['id']] = $group;
		}
		else
		  $groups = $links_manager->fetch_groups();

		if (!is_array($groups) || !count($groups))
		  return new empty_dataset();

		if(isset($params['back_links']) && $params['back_links'])
		  $links = $links_manager->fetch_back_links($mapped_node['id'], array_keys($groups));
		else
		  $links = $links_manager->fetch_target_links($mapped_node['id'], array_keys($groups));

		if (!is_array($links) || !count($links))
		  return new array_dataset($groups);
		
		$target_node_ids = complex_array :: get_column_values('target_node_id', $links);
				
		if (!is_array($target_node_ids) || !count($target_node_ids))
		  return new array_dataset($groups);
		
		$objects =& fetch_by_node_ids($target_node_ids, 'site_object', $counter, array(
		  'restrict_by_class' => false
		));
		
		$result = array();
		
		foreach($groups as $group_id => $group)
		{
      $groups[$group_id]['links'] = array();
      
		  foreach($links as $link_id => $link)
		  {
		    if ($link['group_id']!= $group_id)
		      continue;
        
        $link_data = array_merge($objects[$link['target_node_id']], $link);
        $groups[$group_id]['links'][$link_id] = $link_data;
		  }
		}
		
		return new array_dataset($groups);
	}		
}


?>
