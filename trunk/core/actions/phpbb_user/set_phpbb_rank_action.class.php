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
require_once(LIMB_DIR . 'core/actions/form_action.class.php');

class set_phpbb_rank_action extends form_action
{
	function _define_site_object_class_name()
	{
	  return 'user_object';
	}  
	  
	function _define_dataspace_name()
	{
	  return 'set_phpbb_rank';
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

		$this->dataspace->import($data);
	}
		
	function _valid_perform()
	{
		$object_data =& fetch_mapped_by_url();

		$data = $this->dataspace->export();

  	$phpbb_user_data['user_rank'] = $data['rank'];

		$db =& db_factory :: instance();
		
		if($db->sql_update('phpbb_users', $phpbb_user_data, array('user_id' => $object_data['id'])))
			return new response(RESPONSE_STATUS_FORM_SUBMITTED);
		else
			return new failed_response();
	}

}

?>