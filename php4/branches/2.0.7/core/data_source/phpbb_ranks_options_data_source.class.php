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
require_once(LIMB_DIR . 'core/data_source/data_source.class.php');

class phpbb_ranks_options_data_source extends data_source
{
	function phpbb_ranks_options_data_source()
	{
		parent :: data_source();
	}
	
	function get_default_option()
	{
		return 0;
	}
	
	function get_options_array()
	{
		$db =& db_factory :: instance();
		$db->sql_select('phpbb_ranks');
		
		$rows = $db->get_array('rank_id');
		
		$result = array(0 => '----');
		foreach($rows as $rank_id => $data)
			$result[$rank_id] = $data['rank_title'];
			
		return $result;
	}
}
?>