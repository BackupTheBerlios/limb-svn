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
setlocale(LC_ALL, 'ru');//temporary

require_once(LIMB_DIR . 'core/process_output.php');
require_once(LIMB_DIR . 'core/lib/debug/debug.class.php');

ob_start('process_output');

debug :: add_timing_point('start');

require_once(LIMB_DIR . 'core/lib/http/http_request.inc.php');

require_once(LIMB_DIR . 'core/lib/session/session.class.php');
require_once(LIMB_DIR . 'core/lib/system/message_box.class.php');
require_once(LIMB_DIR . 'core/lib/db/db_table_factory.class.php');
require_once(LIMB_DIR . 'core/lib/error/error.inc.php');
require_once(LIMB_DIR . 'core/lib/locale/strings.class.php');
require_once(LIMB_DIR . 'core/lib/http/control_flow.inc.php');
require_once(LIMB_DIR . 'core/tree/tree.class.php');
require_once(LIMB_DIR . 'core/fetcher.class.php');
require_once(LIMB_DIR . 'core/model/stats/stats_register.class.php');
require_once(LIMB_DIR . 'core/model/response/response.class.php');

start_user_session();

$stats_register = new stats_register();

debug :: add_timing_point('require_done');

$recursive = false;
if (isset($_REQUEST['recursive_search_for_node']) && $_REQUEST['recursive_search_for_node'])
	$recursive = true;

$node =& map_current_request_to_node($recursive);

if(!$node)
{
	debug :: write_error('node not found', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	ob_end_clean();
	if (debug :: is_console_enabled())
		echo debug :: parse_html_console();

	if(defined("ERROR_DOCUMENT_404"))
		reload(ERROR_DOCUMENT_404);
	else
		header("HTTP/1.1 404 Not found");

	exit;
}

if(isset($node['only_parent_found']) && $node['only_parent_found'])
{
	if(isset($_REQUEST['action'])) //only action significant when reload to found parent
		$params = '?action='. $_REQUEST['action'];
	
	$tree = tree :: instance();
	reload($tree->get_path_to_node($node). $params);
	exit;
}

$user =& user :: instance();

if(($object_data =& fetch_one_by_node_id($node['id'], false)) === false)
{
	if (!$user->is_logged_in())
	{
		$tree = tree :: instance();
		
		$response = new response();
		$stats_register->register(-1, '', $response->get_status());
		
		$response = new response();
		$stats_register->register(-1, 'redirect', $response->get_status());
		reload('/root/login?redirect='. $tree->get_path_to_node($node));
		exit;
	}	
	else
	{
		debug :: write_error('content object not allowed or retrieved', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
		ob_end_clean();
		if (debug :: is_console_enabled())
			echo debug :: parse_html_console();
	
		if(defined("ERROR_DOCUMENT_403"))
			reload(ERROR_DOCUMENT_403);
		else
			header("HTTP/1.1 403 Access denied");
			
		exit;
	}	
}

if(isset($object_data['locale_id']) && $object_data['locale_id'])
	define('CONTENT_LOCALE_ID', $object_data['locale_id']);
else
	define('CONTENT_LOCALE_ID', DEFAULT_CONTENT_LOCALE_ID);

if($locale_id = $user->get_locale_id())
	define('MANAGEMENT_LOCALE_ID', $locale_id);
else
	define('MANAGEMENT_LOCALE_ID', CONTENT_LOCALE_ID);

$site_object =& site_object_factory :: instance($object_data['class_name']);

debug :: add_timing_point('object fetched');

$site_object_controller =& $site_object->get_controller();

if(($action = $site_object_controller->determine_action()) === false)
{
	debug :: write_error('"'. $action . '" action not found', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	ob_end_clean();
	if (debug :: is_console_enabled())
		echo debug :: parse_html_console();

	if(defined("ERROR_DOCUMENT_404"))
		reload(ERROR_DOCUMENT_404);
	else
		header("HTTP/1.1 404 Not found");
	
	exit;
}

$access_policy = access_policy :: instance();
$access_policy->assign_actions_to_objects($object_data);

$actions = $object_data['actions'];

if(!isset($actions[$action]))
{
	debug :: write_error('"'. $action . '" action is not accessible', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	ob_end_clean();
	if (debug :: is_console_enabled())
		echo debug :: parse_html_console();

	if(defined("ERROR_DOCUMENT_403"))
		reload(ERROR_DOCUMENT_403);
	else
		header("HTTP/1.1 403 Access denied");

	exit;
}

$response = $site_object_controller->process();

$stats_register->register($node['id'], $action, $response->get_status());

$response->perform();

$site_object_controller->display_view();

echo message_box :: parse();

if (debug :: is_console_enabled())
	echo debug :: parse_html_console();

ob_end_flush();
?>
