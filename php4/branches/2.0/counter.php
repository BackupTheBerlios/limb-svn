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
ob_start();
require_once('setup.php');
require_once(LIMB_DIR . 'core/lib/session/session.class.php');
require_once(LIMB_DIR . 'core/lib/util/client_info.php');
require_once(LIMB_DIR . 'core/lib/http/http_request.inc.php');
require_once(LIMB_DIR . 'core/lib/db/db_factory.class.php');

function _clean_url($raw_url)
{
	$url = trim($raw_url);
	$url = preg_replace('/(^' . preg_quote('http://' . $_SERVER['HTTP_HOST'], '/') . ')(.*)/', '\\2', $url);
	$url = preg_replace('/#[^\?]*/', '', $url);
			
	$pos = strpos($url, '?');
	if($pos !== false)
	{
		$url = preg_replace('/PHPSESSID=[^&]*/', '', $url);
					
		if($pos == (strlen($url)-1))
			$url = rtrim($url, '?');
	}
	$url = rtrim($url, '/');
	
	return $url;
}
		
$db =& db_factory :: instance();

$client_info = get_client_info();
$ip = $client_info['ip'];

//converting ip to integer		
$ip_pieces = explode('.', $ip);
$int_ip = 0;
for($i=3; $i>=0; $i--)
	$int_ip = $int_ip + ($ip_pieces[$i] << ($i*8)); 

$time = time();
$time_arr = getdate($time);

$page_url = $_REQUEST['pg'];
$page_url = _clean_url($page_url);

//cleaning referer
$page_referer = $_REQUEST['r'];
$page_referer = _clean_url($page_referer);

//collecting client specific stuff
$os = $client_info['os'] . ' ' . $client_info['os_ver'] . ' ' . $client_info['os_vendor'];
$browser = $client_info['browser'] . ' ' . $client_info['browser_ver'];
$script_language = isset($_REQUEST['sl']) ? $_REQUEST['sl'] : '?';
$cookie_enabled = isset($_REQUEST['c']) ? $_REQUEST['c'] : '?';
$java_enabled = isset($_REQUEST['j']) ? $_REQUEST['j'] : '?';
$screen_size = isset($_REQUEST['wh']) ? $_REQUEST['wh'] : '?';
$screen_depth = isset($_REQUEST['px']) ? $_REQUEST['px'] : '?';
$time_offset = isset($_REQUEST['t']) ? $_REQUEST['t'] : '?';
			
//checking new host today
$new_host_today = false;

$db->sql_select('sys_stat_ip', '*', "id=$int_ip");

$day_ip_arr = $db->get_array();	

if(!isset($day_ip_arr[0]['time']))
{
	$db->sql_insert('sys_stat_ip', array('id' => $int_ip, 'time' => $time));
	$new_host_today = true;
}
else
{
	$ip_time_arr = getdate($day_ip_arr[0]['time']);

	if( ($time_arr['year'] > $ip_time_arr['year']) ||
			($time_arr['year'] == $ip_time_arr['year']) && ($time_arr['yday'] > $ip_time_arr['yday']))
	{
		$db->sql_update('sys_stat_ip', array('time' => $time), "id=$int_ip");
		$new_host_today = true;
	}
	elseif(($time_arr['year'] > $ip_time_arr['year']) || 
				 ($time_arr['year'] == $ip_time_arr['year']) && ($time_arr['yday'] < $ip_time_arr['yday']))
	{
		//we need to be protected from fooling with time on server
		$time_arr = $ip_time_arr;
		$time = $day_ip_arr[0]['time'];		
	}
}

//checking page with specified url			

$db->sql_select('sys_stat_page_url', '*', "page_url='" . $db->escape($page_url) . "'");
$page_arr = $db->get_array();

if(!isset($page_arr[0]['id']))
{
	$db->sql_insert('sys_stat_page_url', array('page_url' => $page_url));
	$page_id = $db->get_sql_insert_id('sys_stat_page_url');		
}
else
	$page_id = $page_arr[0]['id'];
	
//checking referer url
$referer_id = -1;
if($page_referer)
{
	$db->sql_select('sys_stat_referer_url', '*', "referer_url='" . $db->escape($page_referer) . "'");
	$referer_arr = $db->get_array();
	
	if(!isset($referer_arr[0]['id']))
	{
		$db->sql_insert('sys_stat_referer_url', array('referer_url' => $page_referer));
		$referer_id = $db->get_sql_insert_id('sys_stat_referer_url');		
	}
	else
		$referer_id = $referer_arr[0]['id'];
}	

$db->sql_select('sys_stat_counter');
$counter_arr = $page_arr = $db->get_array();

$hosts_all = 0;
$hits_all = 0;
$hosts_today = 0;
$hits_today = 0;

if(isset($counter_arr[0]['id']))
{
	$hosts_all = $counter_arr[0]['hosts_all'];
	$hits_all = $counter_arr[0]['hits_all'];
	$hosts_today = $counter_arr[0]['hosts_today'];
	$hits_today = $counter_arr[0]['hits_today'];
	
	$counter_time_arr = getdate($counter_arr[0]['time']);
			
	if( ($time_arr['year'] > $counter_time_arr['year']) ||
			($time_arr['year'] == $counter_time_arr['year']) && ($time_arr['yday'] > $counter_time_arr['yday']))
	{
		$hosts_today = 0;
		$hits_today = 0;
	}
	
	$update_array['time'] = $time;
	$update_array['hits_all'] = ++$hits_all;
	$update_array['hits_today'] = ++$hits_today;

	if($new_host_today)
	{
		$update_array['hosts_all'] = ++$hosts_all;
		$update_array['hosts_today'] = ++$hosts_today;
	}
		
	$db->sql_update('sys_stat_counter', $update_array);
}
else
	$db->sql_insert('sys_stat_counter', 
		array(
			'hosts_all' => ++$hosts_all,
			'hits_all' => ++$hits_all,
			'hosts_today' => ++$hosts_today,
			'hits_today' => ++$hits_today,
			'time' => $time
		)
	);

//getting client specific stuff if neccessary
$db->sql_select('sys_stat_client_info', '*', 
	array(
			'java_enabled' => $java_enabled,
			'cookie_enabled' => $cookie_enabled,
			'script_language' => $script_language,
			'screen_size' => $screen_size,
			'screen_depth' => $screen_depth,
			'os' => $os,
			'browser' => $browser,
			'time_offset' => $time_offset));
$client_arr = $db->get_array();
			
if(!isset($client_arr[0]['id']))
{
	$db->sql_insert('sys_stat_client_info', 
		array(
			'java_enabled' => $java_enabled,
			'cookie_enabled' => $cookie_enabled,
			'script_language' => $script_language,
			'screen_size' => $screen_size,
			'screen_depth' => $screen_depth,
			'os' => $os,
			'browser' => $browser,
			'time_offset' => $time_offset,
		)
	);
	$client_info_id = $db->get_sql_insert_id('sys_stat_client_info');
}
else
	$client_info_id = $client_arr[0]['id'];

//writing log		
$db->sql_insert('sys_stat_log', 
	array(
		'ip' => $int_ip, 
		'time' => $time,
		'stat_page_id' => $page_id,
		'stat_referer_id' => $referer_id,
		'client_info_id' => $client_info_id
	)
);	
?>