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
require_once('setup.php');
require_once(LIMB_DIR . 'core/lib/db/db_factory.class.php');

function get_counter_record()
{
	$db =& db_factory :: instance();
	$db->sql_select('sys_stat_counter', '*');
	return $db->fetch_row();
}
						
?>