<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: control_flow.inc.php 533 2004-02-21 13:26:17Z mike $
*
***********************************************************************************/ 

function add_url_query_items($url, $items=array())
{
	$str_params = '';
	
	if(isset($_REQUEST['node_id']))
		$items['node_id'] = $_REQUEST['node_id'];

	if(strpos($url, '?') === false)
		$url .= '?';
	
	foreach($items as $key => $val)
	{
		$url = preg_replace("/&*{$key}=[^&]+/", '', $url);
		$str_params .= "&$key=$val";
	}
	
	$items = explode('#', $url);
	
	$url = $items[0];
	$fragment = isset($items[1]) ? '#' . $items[1] : '';
	
	return $url . $str_params . $fragment;
}

function reload($url = PHP_SELF)
{
	ob_end_clean();

	ob_start();
	
	echo "<html><head><meta http-equiv=refresh content='0;url=$url'></head><body bgcolor=white></body></html>";
	
	ob_end_flush();
	
	commit_user_transaction();
	
	exit();
}

function remember_url_history()
{
	if(!isset($_SESSION['URL_HISTORY']) || !is_array($_SESSION['URL_HISTORY']))
		$_SESSION['URL_HISTORY'] = array();

	$url =  $_SERVER['REQUEST_URI'];

	$index = sizeof($_SESSION['URL_HISTORY']) - 1;
	if( !isset($_SESSION['URL_HISTORY'][$index]) || $_SESSION['URL_HISTORY'][$index] != $url)
		array_push($_SESSION['URL_HISTORY'], $url);
}

function pop_url_history()
{
	if(sizeof($_SESSION['URL_HISTORY']))
	 return array_pop($_SESSION['URL_HISTORY']);
}

function close_popup_no_parent_reload()
{
	if(!isset($_REQUEST['popup']) || !$_REQUEST['popup'])
		return;
	
	ob_end_clean();

	ob_start();

	echo "<html><body><script>
					if(window.opener)
					{													
				 			window.opener.focus();
				 			window.close()
				 	};
				</script></body></html>"; 
	
	ob_end_flush();
	
	commit_user_transaction();
	
	exit();
}

function close_popup($parent_reload_url='', $search_for_node = false)
{
	if(!isset($_REQUEST['popup']) || !$_REQUEST['popup'])
		return;
	
	ob_end_clean();

	ob_start();

	echo "<html><body><script>
							if(window.opener)
							{";
							
							
	if($parent_reload_url)
		echo 			"	href = '{$parent_reload_url}';";
	else	
		echo 			"	href = window.opener.location.href;"; 

	if($search_for_node && !isset($_REQUEST['recursive_search_for_node']))
		echo 					_add_js_param_to_url('href', 'recursive_search_for_node', '1');
		
	echo 					_add_js_random_to_url('href');
	
	echo 				"	window.opener.location.href = href;";			
		
	echo 				" window.opener.focus();
								}
								window.close();
							</script></body></html>"; 
	
	ob_end_flush();
	
	commit_user_transaction();
	
	exit();
}

function _add_js_random_to_url($href)
{
	return _add_js_param_to_url($href, 'rn', 'Math.floor(Math.random()*10000)');
}

function _add_js_param_to_url($href, $param, $value)
{
	return "
		if({$href}.indexOf('?') == -1)
			{$href} = {$href} + '?';
		
		{$href} = {$href}.replace(/&*rn=[^&]+/g, '');
		
		items = {$href}.split('#');
		
		{$href} = items[0] + '&{$param}=' + {$value};
		
		if(items[1])
			{$href} = {$href} + '#' + items[1];";
	
}

function reload_popup($url=PHP_SELF)
{
	if(!isset($_REQUEST['popup']) || !$_REQUEST['popup'])
		return;
	
	ob_end_clean();

	ob_start();
	
	echo "<html><body><script>
							if(window.opener)
							{
								href = window.opener.location.href;";
								
	echo 					_add_js_random_to_url('href');
	
	echo 				" window.opener.location.href = href;";
								
	echo				"	
								window.opener.focus();
							}"; 

	echo "href = '{$url}';"; 
		
	echo _add_js_random_to_url('href');
							
	echo "window.location.href = href;";
							
	echo '</script>
					</body>
				</html>';
						
	ob_end_flush();
	
	commit_user_transaction();
	
	exit();
}

?>
