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

require_once(LIMB_DIR . 'core/lib/mail/send_plain_mail.php');
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


start_user_session();
debug :: add_timing_point('require_done');

$node =& map_url_to_node();
  	
if(!$node)
{
	debug :: write_error('node not found', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	ob_end_clean();
	if (debug :: is_console_enabled())
		echo debug :: parse_html_console();

	if(defined("ERROR_DOCUMENT_404") && !isset($_GET['node_id']))
		reload(ERROR_DOCUMENT_404);
	else
		header("HTTP/1.1 404 Not found");
	exit;
}

if(!$object_data =& fetch_one_by_node_id($node['id']))
{
	debug :: write_error('content object not allowed or retrieved', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	ob_end_clean();
	if (debug :: is_console_enabled())
		echo debug :: parse_html_console();
	
	if(defined("ERROR_DOCUMENT_403") && !isset($_GET['node_id']))
		reload(ERROR_DOCUMENT_403);
	else
		header("HTTP/1.1 403 Access denied");

	exit;  
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
	
	if(defined("ERROR_DOCUMENT_404"))
		reload(ERROR_DOCUMENT_404);
	else
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

	if(defined("ERROR_DOCUMENT_403"))
		reload(ERROR_DOCUMENT_403);
	else
		header("HTTP/1.1 403 Access denied");

	exit;
}

if(!$site_object_controller->process())
	debug :: write_error('action failed', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);

debug :: add_timing_point('action processed');
	
if(!$view =& $site_object_controller->get_view())
	error('template is null', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);

$view->display();

debug :: add_timing_point('template executed');

echo message_box :: parse();

if (debug :: is_console_enabled())
	echo debug :: parse_html_console();

ob_end_flush();

?>
