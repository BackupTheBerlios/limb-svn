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
      
  function test_register_new_host() 
  {
  	$this->stats_log->register(2, 'display');

		$this->_check_stats_log_record(1, 1, 10, 2, 'display', $this->stats_log->get_register_time_stamp());
		$this->_check_stats_referer_url_record(1, 1, 'some.referer.com');
		$this->_check_stats_ip_record();
		$this->_check_stats_counter_record(1, 1, 1, 1, $this->stats_log->get_register_time_stamp());
  }
  
  function test_register_same_host_and_new_referer()
  {
  	$this->test_register_new_host();
  	
  	$this->stats_log->setReturnValueAt(2, '_get_clean_referer_page', 'some.other-referer.com');
  	$this->stats_log->setReturnValueAt(3, '_get_clean_referer_page', 'some.other-referer.com');
  	$this->stats_log->register(4, 'edit');
		
		$this->_check_stats_log_record(2, 2, 10, 4, 'edit', $this->stats_log->get_register_time_stamp());
		$this->_check_stats_referer_url_record(2, 2, 'some.other-referer.com');
		$this->_check_stats_counter_record(2, 2, 1, 1, $this->stats_log->get_register_time_stamp());
		$this->_check_stats_ip_record();
  }
  
  function test_register_same_host()
  {
  }
  
	function _check_stats_log_record($total_records, $current_record, $user_id, $node_id, $action, $time)
	{
  	$this->db->sql_select('sys_stat_log', '*', '', 'id');
  	$arr = $this->db->get_array();

  	$this->assertTrue(sizeof($arr), $total_records);
  	reset($arr);
  	
  	for($i = 1; $i <= $current_record; $i++)
  	{
  	 	$record = current($arr);
  	 	next($arr);
  	}
  	
  	$this->assertEqual($record['user_id'], $user_id);
  	$this->assertEqual($record['node_id'], $node_id);
  	$this->assertEqual($record['action'], $action);
  	$this->assertEqual($record['time'], $time);
  	$this->assertEqual($record['session_id'], session_id());
  }

  function _check_stats_referer_url_record($total_records, $current_record, $referer)
  {
  	$this->db->sql_select('sys_stat_referer_url', '*', '', 'id');
  	$arr = $this->db->get_array('id');

  	$this->assertTrue(sizeof($arr), $total_records);
  	reset($arr);
  	
  	for($i = 1; $i <= $current_record; $i++)
  	{
  	 	$record = current($arr);
  	 	next($arr);
  	}
  	
  	$this->assertEqual($record['referer_url'], $referer, 'referer url was parsed or written incorrectly');
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
  
  function _check_stats_counter_record($hits_all, $hits_today, $hosts_all, $hosts_today, $time)
  {
  	$this->db->sql_select('sys_stat_counter');
  	$record = $this->db->fetch_row();
  	
  	$this->assertEqual($record['hits_all'], $hits_all);
  	$this->assertEqual($record['hits_today'], $hits_today);
  	$this->assertEqual($record['hosts_all'], $hosts_all);
  	$this->assertEqual($record['hosts_today'], $hosts_today);
  	$this->assertEqual($record['time'], $time);
  }
  
  function _login_user($id, $groups)
  {
		$_SESSION[user :: get_session_identifier()]['id'] = $id;
		$_SESSION[user :: get_session_identifier()]['groups'] = $groups;
  }
  
}

?>