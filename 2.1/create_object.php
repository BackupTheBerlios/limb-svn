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

$params = $_REQUEST['params'];
$params_set = false;
if(is_array($params) &&
		!empty($params['class']) &&		!empty($params['parent_path']) &&
		!empty($params['identifier']) &&		!empty($params['title']))
		$params_set= true;

if($_REQUEST['action'] == 'create_object' && $params_set)
{
	require_once('setup.php');
	
	require_once(LIMB_DIR . 'core/process_output.php');

  require_once(LIMB_DIR . 'core/lib/http/http_request.inc.php');
  require_once(LIMB_DIR . 'core/lib/session/session.class.php');
  require_once(LIMB_DIR . 'core/lib/debug/debug.class.php');
  require_once(LIMB_DIR . 'core/lib/system/message_box.class.php');
  require_once(LIMB_DIR . 'core/lib/db/db_table_factory.class.php');
   
	debug :: add_timing_point('start');
	
	require_once(LIMB_DIR . 'core/lib/error/error.inc.php');
	require_once(LIMB_DIR . 'core/lib/security/user.class.php');
	require_once(LIMB_DIR . 'core/lib/locale/strings.class.php');
  require_once(LIMB_DIR . 'core/lib/http/control_flow.inc.php');
  require_once(LIMB_DIR . 'core/fetcher.class.php');
  require_once(LIMB_DIR . 'core/model/site_object_factory.class.php');
  
  start_user_session();

	$user =& user :: instance();
  $user->login('admin', 'test');

	debug :: add_timing_point('start');

  $db =& db_factory :: instance();
  
  $db->begin();
  
	define('CONTENT_LOCALE_ID', 'ru');
	
 	define('MANAGEMENT_LOCALE_ID', 'ru');

  $tree =& tree :: instance();
	
	$object =& site_object_factory :: create($params['class']);
	
	$is_root = false;
	if(!$parent_data = fetch_one_by_path($params['parent_path']))
	{
		if ($params['parent_path'] == '/')
			$is_root = true;
		else
		{	
			echo('parent object not found');
			exit();
		}	
	}	
	
	if (!$is_root)
		$params['parent_node_id'] = $parent_data['node_id'];
	else	
		$params['parent_node_id'] = 0;
		
	$object->import_attributes($params);

	if($object->create($is_root))
		echo $params['parent_path'].'/'. $params['identifier'].' created <br/>';
	else
	{
		echo('object was not created');
		exit();
	}	
	
	if (!$is_root)
	{
		$parent_object =& site_object_factory :: instance($parent_data['class_name']);
		$parent_object->import_attributes($parent_data);
	
		$access_policy =& access_policy :: instance();
		$access_policy->save_object_access($object, $parent_object);
	}	

  $db->commit();
  debug :: add_timing_point('finish');
	if (debug :: is_console_enabled())
		echo debug :: parse_html_console();
}
?>
<html>

<head>
	<title>Class creation</title>
	<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
	<link href="/shared/styles/admin.css" rel="stylesheet" type="text/css">
	<script language="javascript" src="/shared/js/common.js"></script>
	<script language="javascript" src="/design/main/js/form_errors.js"></script>
	<metadata:METADATA>
	<meta name="description" content="{$description}">
	<meta name="keywords" content="{$keywords}">
	</metadata:METADATA>
</head>

<body>
<h1>Class creation</h1>
<table colsapn=2 rowspan=5>
<form action='' method='post'>
<input type='hidden' name='action' value='create_object'>
<tr>
	<td><span class=req>*</span>Object class name</td>
	<td><input type='text' name='params[class]' class='input'></td>
</tr>
<tr>
	<td><span class=req>*</span>Object identifier</td>
	<td><input type='text' name='params[identifier]' class='input'></td>
</tr>
<tr>
	<td><span class=req>*</span>Object title</td>
	<td><input type='text' name='params[title]' class='input'></td>
</tr>
<tr>
	<td><span class=req>*</span>Parent path</td>
	<td><input type='text' name='params[parent_path]' class='input'></td>
</tr>
<tr>
	<td colspan=2 align=center><input type='submit' value='create' class=button></td>
</tr>

</form>
</table>
</body>
</html>