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

class phpbb_ranks_options_datasource extends datasource
{
	function phpbb_ranks_options_datasource()
	{
		parent :: datasource();
	}
	
	function get_default_option()
	{
		return 0;
	}
	
	function get_options_array()
	{
		$connection = & db_factory :: get_connection();
		$connection->sql_select('phpbb_ranks');
		
		$rows = $connection->get_array('rank_id');
		
		$result = array(0 => '----');
		foreach($rows as $rank_id => $data)
			$result[$rank_id] = $data['rank_title'];
			
		return $result;
	}
}
?>