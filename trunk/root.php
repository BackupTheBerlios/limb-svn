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
require_once(LIMB_DIR . 'core/lib/security/user.class.php');
require_once(LIMB_DIR . 'core/lib/locale/strings.class.php');
require_once(LIMB_DIR . 'core/lib/http/control_flow.inc.php');
require_once(LIMB_DIR . 'core/tree/limb_tree.class.php');
require_once(LIMB_DIR . 'core/fetcher.class.php');
require_once(LIMB_DIR . 'core/model/stats/stats_register.class.php');
require_once(LIMB_DIR . 'core/model/shop/cart.class.php');

start_user_session();
debug :: add_timing_point('require_done');

$recursive = false;
if (isset($_REQUEST['recursive_search_for_node']) && $_REQUEST['recursive_search_for_node'])
	$recursive = true;

$node =& map_url_to_node('', $recursive);

if(!$node)
{
	debug :: write_error('node not found', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	ob_end_clean();
	if (debug :: is_console_enabled())
		echo debug :: parse_html_console();

	header("HTTP/1.1 404 Not found");
	exit;
}

if(isset($node['only_parent_found']) && $node['only_parent_found'])
{
	if(isset($_REQUEST['action'])) //only action significant when reload to found parent
		$params = '?action='. $_REQUEST['action'];
	
	$tree = limb_tree :: instance();
	reload($tree->get_path_to_node($node). $params);
	exit;
}

if(($object_data =& fetch_one_by_node_id($node['id'])) === false)
{
	if (!user :: is_logged_in())
	{
		$tree = limb_tree :: instance();
		reload('/root/login?redirect='. $tree->get_path_to_node($node));
		exit;
	}	
	else
	{
		debug :: write_error('content object not allowed or retrieved', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
		ob_end_clean();
		if (debug :: is_console_enabled())
			echo debug :: parse_html_console();
	
		header("HTTP/1.1 403 Access denied");
		exit;
	}	
}

if(isset($object_data['locale_id']) && $object_data['locale_id'])
	define('CONTENT_LOCALE_ID', $object_data['locale_id']);
else
	define('CONTENT_LOCALE_ID', DEFAULT_CONTENT_LOCALE_ID);

define('MANAGEMENT_LOCALE_ID', user :: get_management_locale_id());

$site_object =& site_object_factory :: instance($object_data['class_name']);

debug :: add_timing_point('object fetched');

$site_object_controller =& $site_object->get_controller();

if(($action = $site_object_controller->determine_action()) === false)
{
	debug :: write_error('"'. $action . '" action not found', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	ob_end_clean();
	if (debug :: is_console_enabled())
		echo debug :: parse_html_console();

	header("HTTP/1.1 404 Not found");
	exit;
}

$actions = $object_data['actions'];

if(!isset($actions[$action]))
{
	debug :: write_error('"'. $action . '" action is not accessible', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	ob_end_clean();
	if (debug :: is_console_enabled())
		echo debug :: parse_html_console();

	header("HTTP/1.1 403 Access denied");
	exit;
}

$response = $site_object_controller->process();

$stats_register = new stats_register();

$stats_register->register($node['id'], $action, $response->get_status());

$response->perform();

$site_object_controller->display_view();

echo message_box :: parse();

if (debug :: is_console_enabled())
	echo debug :: parse_html_console();

ob_end_flush();
?>
