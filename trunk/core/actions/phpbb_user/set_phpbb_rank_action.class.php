<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: set_membership.class.php 2 2004-02-29 19:06:22Z server $
*
***********************************************************************************/ 
require_once(LIMB_DIR . 'core/actions/form_action.class.php');

class set_phpbb_rank_action extends form_action
{
	function set_phpbb_rank_action($name = 'set_phpbb_rank', $merge_definition=array())
	{		
		$definition = array(
			'site_object' => 'user_object'
		);
		
		parent :: form_action($name, complex_array :: array_merge($definition, $merge_definition));
	}
	
	function _init_dataspace()
	{
		$object_data =& fetch_mapped_by_url();
		
		$phpbb_users_db_table =& db_table_factory :: create('phpbb_users');
		$conditions['user_id'] = $object_data['id'];
		$list = $phpbb_users_db_table->get_list($conditions);
		if (!count($list))
			return ;
		
		$phpbb_user_data = current($list);
		$data['rank'] = $phpbb_user_data['user_rank'];

		$this->_import($data);
	}
		
	function _valid_perform()
	{
		$object_data =& fetch_mapped_by_url();

		$data = $this->_export();

  	$phpbb_user_data['user_rank'] = $data['rank'];

		$db =& db_factory :: instance();
		return $db->sql_update('phpbb_users', $phpbb_user_data, array('user_id' => $object_data['id']));
	}

}

?>