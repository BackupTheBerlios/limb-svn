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
require_once('../setup.php');
require_once('chat_setup.php');
require_once('smiles.php');

require_once(LIMB_DIR . 'core/lib/session/session.class.php');

start_user_session();

$template_vars = array();

$view = file_get_contents(DESIGN_DIR . 'chat/chat.html');

if (user :: get_id())
{
	$user_data = fetch_one_by_node_id(user :: get_node_id());
	chat_login($user_data['identifier'], $user_data['chat_color']);
}

if(!session :: get('chat_user_id'))	
{
	$template_vars['enter_div_display'] = 'none';
	$template_vars['message_div_display'] = 'block';
}
else
{
	$template_vars['enter_div_display'] = 'block';
	$template_vars['message_div_display'] = 'none';
}

foreach($template_vars as $key => $value)
	$view = str_replace("<!--<<{$key}>>-->", $value, $view);

$view = str_replace("<!--<<smiles>>-->", get_smiles_htm_table(), $view);

echo $view;

ob_end_flush();
?>