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
require_once(LIMB_DIR . 'class/core/request/request.class.php');

function add_url_query_items($url, $items=array())
{
	$str_params = '';

  $request = LimbToolsBox :: getToolkit()->getRequest();
  
  if (($node_id = $request->get('node_id')) && !isset($items['node_id']))
		$items['node_id'] = $node_id;
	
	if(strpos($url, '?') === false)
		$url .= '?';
	
	foreach($items as $key => $val)
	{
		$url = preg_replace("/&*{$key}=[^&]*/", '', $url);
		$str_params .= "&$key=$val";
	}
	
	$items = explode('#', $url);
	
	$url = $items[0];
	$fragment = isset($items[1]) ? '#' . $items[1] : '';
	
	return $url . $str_params . $fragment;
}


?>
