<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: dir.test.php 2 2004-02-29 19:06:22Z server $
*
***********************************************************************************/ 
require_once(LIMB_DIR . '/core/model/stats/stats_log.class.php');

Mock::generatePartial
(
  'stats_log',
  'stats_log_test_version',
  array(
  	'_get_clean_referer_page',
  )
); 

class test_stats_register_log extends UnitTestCase 
{
	var $db = null;
	var $stats_log = null;
	
  function test_stats_register_log() 
  {
  	parent :: UnitTestCase();
  	
  	$this->db = db_factory :: instance();
  }
  
  function setUp()
  {
   	$this->stats_log = new stats_log_test_version($this);
   	$this->stats_log->stats_log();
  	$this->stats_log->setReturnValueAt(1, '_get_clean_referer_page', 'some.referer.com');

		$this->_login_user(10, array());
  	
  	$this->_clean_up();
  }
  
  function tearDown()
  {
  	$this->stats_log->tally();
  	$this->_clean_up();
  }
  
  function _clean_up()
  {
  	$this->db->sql_delete('sys_stat_log');
  	$this->db->sql_delete('sys_stat_ip');
  	$this->db->sql_delete('sys_stat_referer_url');
  	$this->db->sql_delete('sys_stat_counter');
  }
      
  function test_register_first_page() 
  {
  	$this->stats_log->register(2, 'display');

		$this->_check_stats_log_record();
		$this->_check_stats_referer_url_record();
		$this->_check_stats_ip_record();
		$this->_check_stats_counter_record();
  }
  
  function test_register_second_page()
  {
  	$this->test_register_first_page();
  	
  	$this->stats_log->setReturnValueAt(2, '_get_clean_referer_page', 'some.other-referer.com');
  	$this->stats_log->setReturnValueAt(3, '_get_clean_referer_page', 'some.other-referer.com');
  	$this->stats_log->register(4, 'edit');
		
		$this->_check_second_stats_log_record();
		$this->_check_second_stats_referer_url_record();
		$this->_check_second_stats_counter_record();
		$this->_check_stats_ip_record();
  }
  
	function _check_stats_log_record()
	{
  	$this->db->sql_select('sys_stat_log');
  	$record = $this->db->fetch_row();
  	
  	$this->assertEqual($record['user_id'], 10);
  	$this->assertEqual($record['node_id'], 2);
  	$this->assertEqual($record['action'], 'display');
  	$this->assertEqual($record['time'], $this->stats_log->reg_date->get_stamp());
  	$this->assertEqual($record['session_id'], session_id());
  }

	function _check_second_stats_log_record()
	{
  	$this->db->sql_select('sys_stat_log', '*', '', 'id');
  	$arr = $this->db->get_array();
  	$record = end($arr);
  	
  	$this->assertTrue(sizeof($arr), 2);
  	$this->assertEqual($record['user_id'], 10);
  	$this->assertEqual($record['node_id'], 4);
  	$this->assertEqual($record['action'], 'edit');
  	$this->assertEqual($record['time'], $this->stats_log->reg_date->get_stamp());
  	$this->assertEqual($record['session_id'], session_id());
  }
  
  function _check_stats_referer_url_record()
  {
  	$this->db->sql_select('sys_stat_referer_url');
  	$record = $this->db->fetch_row();
  	
  	$this->assertEqual($record['referer_url'], 'some.referer.com', 'referer url was parsed or written incorrectly');
  }

  function _check_second_stats_referer_url_record()
  {
  	$this->db->sql_select('sys_stat_referer_url', '*', '', 'id');
  	$arr = $this->db->get_array('id');
  	$record = end($arr);
  	
  	$this->assertTrue(sizeof($arr), 2);
  	$this->assertEqual($record['referer_url'], 'some.other-referer.com', 'referer url was parsed or written incorrectly');
  }
  
  function _check_stats_ip_record()
  {
  	$this->db->sql_select('sys_stat_ip');
  	$arr = $this->db->get_array('id');
  	$record = end($arr);
  	
  	$this->assertTrue(sizeof($arr), 1);
  	$this->assertEqual($record['id'], sys :: client_ip(true), 'client ip was written incorrectly');
  	$this->assertEqual($record['time'], $this->stats_log->reg_date->get_stamp());
  }
  
  function _check_stats_counter_record()
  {
  	$this->db->sql_select('sys_stat_counter');
  	$record = $this->db->fetch_row();
  	
  	$this->assertEqual($record['hits_all'], 1);
  	$this->assertEqual($record['hits_today'], 1);
  	$this->assertEqual($record['hosts_all'], 1);
  	$this->assertEqual($record['hosts_today'], 1);
  	$this->assertEqual($record['time'], $this->stats_log->reg_date->get_stamp());
  }

  function _check_second_stats_counter_record()
  {
  	$this->db->sql_select('sys_stat_counter');
  	$arr = $this->db->get_array();
  	$record = end($arr);
  	
  	$this->assertEqual($record['hits_all'], 2);
  	$this->assertEqual($record['hits_today'], 2);
  	$this->assertEqual($record['hosts_all'], 1);
  	$this->assertEqual($record['hosts_today'], 1);
  	$this->assertEqual($record['time'], $this->stats_log->reg_date->get_stamp());
  }
  
  function _login_user($id, $groups)
  {
		$_SESSION[user :: get_session_identifier()]['id'] = $id;
		$_SESSION[user :: get_session_identifier()]['groups'] = $groups;
  }
  
}

?>