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
require_once(LIMB_DIR . '/core/model/stats/stats_register.class.php');
require_once(LIMB_DIR . '/core/model/response/response.class.php');

Mock::generatePartial
(
  'stats_register',
  'stats_register_test_version',
  array(
  	'_get_ip_register',
  	'_get_log_register',
  	'_get_counter_register',
  )
);

Mock::generatePartial
(
  'stats_counter',
  'stats_counter_test_version',
  array(
  	'_is_home_hit',
  )
);

Mock::generatePartial
(
  'stats_log',
  'stats_log_test_version',
  array(
  	'_get_referer_register',
  )
); 

Mock::generatePartial
(
  'stats_ip',
  'stats_ip_test_version',
  array(
  	'get_client_ip',
  )
); 

Mock::generatePartial
(
  'stats_referer',
  'stats_referer_test_version',
  array(
  	'_get_clean_referer_page'
  )
); 

class test_stats_register extends UnitTestCase 
{
	var $db = null;

  var	$stats_ip1 = null;
  var $stats_ip2 = null;

  var $stats_referer1 = null;
  var $stats_referer2 = null;
	
	var $stats_log1 = null;
	var $stats_log2 = null;

	var $stats_register1 = null;
	var $stats_register2 = null;
	
	var $stats_counter = null;
	
  function test_stats_register() 
  {
  	parent :: UnitTestCase();
  	
  	$this->db = db_factory :: instance();
  }
  
  function setUp()
  {
   	$this->stats_counter = new stats_counter_test_version($this);
   	$this->stats_counter->stats_counter();
  
   	$this->stats_ip1 = new stats_ip_test_version($this);
   	$this->stats_ip1->stats_ip();

   	$this->stats_ip2 = new stats_ip_test_version($this);
   	$this->stats_ip2->stats_ip();

   	$this->stats_referer1 = new stats_referer_test_version($this);
   	$this->stats_referer1->stats_referer();

   	$this->stats_referer2 = new stats_referer_test_version($this);
   	$this->stats_referer2->stats_referer();

   	$this->stats_log1 = new stats_log_test_version($this);
  	$this->stats_log1->setReturnReference('_get_referer_register', $this->stats_referer1);
   	$this->stats_log1->stats_log();

   	$this->stats_log2 = new stats_log_test_version($this);
  	$this->stats_log2->setReturnReference('_get_referer_register', $this->stats_referer2);
   	$this->stats_log2->stats_log();
   	
   	$this->stats_register1 = new stats_register_test_version($this);
   	$this->stats_register1->stats_register();
  	$this->stats_register1->setReturnReference('_get_log_register', $this->stats_log1);
  	$this->stats_register1->setReturnReference('_get_ip_register', $this->stats_ip1);
  	$this->stats_register1->setReturnReference('_get_counter_register', $this->stats_counter);
   	
   	$this->stats_register2 = new stats_register_test_version($this);
   	$this->stats_register2->stats_register();
  	$this->stats_register2->setReturnReference('_get_log_register', $this->stats_log2);
  	$this->stats_register2->setReturnReference('_get_ip_register', $this->stats_ip2);
  	$this->stats_register2->setReturnReference('_get_counter_register', $this->stats_counter);
  	
		$this->_login_user(10, array());
  	
  	$this->_clean_up();
  }
  
  function tearDown()
  {
		$this->stats_counter->tally();

  	$this->stats_ip1->tally();
  	$this->stats_ip2->tally();

  	$this->stats_referer1->tally();
  	$this->stats_referer2->tally();

  	$this->stats_log1->tally();
  	$this->stats_log2->tally();

		$this->stats_register1->tally();
		$this->stats_register2->tally();
		
  	$this->_clean_up();
  }
  
  function _clean_up()
  {
  	$this->db->sql_delete('sys_stat_log');
  	$this->db->sql_delete('sys_stat_ip');
  	$this->db->sql_delete('sys_stat_referer_url');
  	$this->db->sql_delete('sys_stat_counter');
  	$this->db->sql_delete('sys_stat_day_counters');
  }
      
  function test_new_host() 
  {
  	$ip = sprintf('%02x%02x%02x%02x', 192, 168, 0, 5);
  	$this->stats_ip1->setReturnValue('get_client_ip', $ip);
  	$this->stats_referer1->setReturnValue('_get_clean_referer_page', 'some.referer.com');

  	$this->stats_counter->setReturnValueAt(0, '_is_home_hit', true);
  	
		$this->stats_register1->set_register_time(time());
  	$this->stats_register1->register(2, 'display', RESPONSE_STATUS_SUCCESS);

		$time = $this->stats_register1->get_register_time_stamp();
		$this->_check_stats_log_record(1, 1, 10, 2, 'display', RESPONSE_STATUS_SUCCESS, $time);
		$this->_check_stats_referer_url_record(1, 1, 'some.referer.com');
		$this->_check_stats_ip_record(1, $ip, $time);
		$this->_check_stats_counter_record(
			$hits_all = 1,
			$hits_today = 1,
			$hosts_all = 1,
			$hosts_today = 1, 
			$time);

  	$this->_check_stats_day_counters_record($hits_today, $hosts_today, $home_hits = 1, $time);
  }
  
  function test_same_host_and_new_referer()
  {
  	$this->test_new_host();
  	
  	$ip = sprintf('%02x%02x%02x%02x', 192, 168, 0, 5);
  	$this->stats_ip2->setReturnValue('get_client_ip', $ip);
  	$this->stats_referer2->setReturnValue('_get_clean_referer_page', 'some.other-referer.com');

  	$this->stats_counter->setReturnValueAt(1, '_is_home_hit', true);
  	
		$this->stats_register2->set_register_time(time()+1);
  	$this->stats_register2->register(4, 'edit', RESPONSE_STATUS_SUCCESS);
		
		$time = $this->stats_register2->get_register_time_stamp();
		$this->_check_stats_log_record(2, 2, 10, 4, 'edit', RESPONSE_STATUS_SUCCESS, $time);
		$this->_check_stats_referer_url_record(2, 2, 'some.other-referer.com');
		$this->_check_stats_ip_record(1, $ip, $this->stats_register1->get_register_time_stamp());

		$this->_check_stats_counter_record(
			$hits_all = 2,
			$hits_today = 2,
			$hosts_all = 1,
			$hosts_today = 1, 
			$time);

  	$this->_check_stats_day_counters_record($hits_today, $hosts_today, $home_hits = 2, $time);
  }
  
  function test_second_new_host()
  {
  	$this->test_new_host();
  	
  	$ip = sprintf('%02x%02x%02x%02x', 192, 168, 0, 6);
  	$this->stats_ip2->setReturnValue('get_client_ip', $ip);
  	$this->stats_referer2->setReturnValue('_get_clean_referer_page', 'some.referer.com');

  	$this->stats_counter->setReturnValueAt(1, '_is_home_hit', false);
  	
		$this->stats_register2->set_register_time(time()+1);
  	$this->stats_register2->register(4, 'edit', RESPONSE_STATUS_FAILURE);
		
		$time = $this->stats_register2->get_register_time_stamp();
		$this->_check_stats_log_record(2, 2, 10, 4, 'edit', RESPONSE_STATUS_FAILURE, $time);
		$this->_check_stats_referer_url_record(1, 1, 'some.referer.com');
		$this->_check_stats_ip_record(2, $ip, $time);

		$this->_check_stats_counter_record(
			$hits_all = 2,
			$hits_today = 2,
			$hosts_all = 2,
			$hosts_today = 2, 
			$time);

  	$this->_check_stats_day_counters_record($hits_today, $hosts_today, $home_hits = 1, $time);
  }
  
  function test_second_new_host_new_day()
  {
  	$this->test_new_host();
  	
  	$ip = sprintf('%02x%02x%02x%02x', 192, 168, 0, 6);
  	$this->stats_referer2->setReturnValue('_get_clean_referer_page', 'some.referer.com');
  	$this->stats_ip2->setReturnValue('get_client_ip', $ip);

  	$this->stats_counter->setReturnValueAt(1, '_is_home_hit', true);
  	
		$this->stats_register2->set_register_time(time()+ 60*60*24 + 1);
  	$this->stats_register2->register(4, 'edit', RESPONSE_STATUS_FORM_NOT_VALID);
		
		$time = $this->stats_register2->get_register_time_stamp();
		$this->_check_stats_log_record(2, 2, 10, 4, 'edit', RESPONSE_STATUS_FORM_NOT_VALID, $time);
		$this->_check_stats_referer_url_record(1, 1, 'some.referer.com');
		$this->_check_stats_ip_record(1, $ip, $time);

		$this->_check_stats_counter_record(
			$hits_all = 2,
			$hits_today = 1,
			$hosts_all = 2,
			$hosts_today = 1, 
			$time);

  	$this->_check_stats_day_counters_record($hits_today, $hosts_today, $home_hits = 1, $time);
  }
  
  function test_existing_host_new_day()
  {
  	$this->test_new_host();
  	
  	$ip = sprintf('%02x%02x%02x%02x', 192, 168, 0, 5);
  	$this->stats_referer2->setReturnValue('_get_clean_referer_page', 'some.referer.com');
  	$this->stats_ip2->setReturnValue('get_client_ip', $ip);

  	$this->stats_counter->setReturnValueAt(1, '_is_home_hit', false);
  	
		$this->stats_register2->set_register_time(time()+ 60*60*24 + 1);
  	$this->stats_register2->register(4, 'edit', RESPONSE_STATUS_SUCCESS);
		
		$time = $this->stats_register2->get_register_time_stamp();
		$this->_check_stats_log_record(2, 2, 10, 4, 'edit', RESPONSE_STATUS_SUCCESS, $time);
		$this->_check_stats_referer_url_record(1, 1, 'some.referer.com');
		$this->_check_stats_ip_record(1, $ip, $time);

		$this->_check_stats_counter_record(
			$hits_all = 2,
			$hits_today = 1,
			$hosts_all = 2,
			$hosts_today = 1, 
			$time);

  	$this->_check_stats_day_counters_record($hits_today, $hosts_today, $home_hits = 0, $time);
  }
  
  function test_second_new_host_wrong_day()
  {
  	$this->test_new_host();
  	
  	$stamp = $this->stats_register2->get_register_time_stamp();
  	
  	$ip = sprintf('%02x%02x%02x%02x', 192, 168, 0, 5);
  	$this->stats_referer2->setReturnValue('_get_clean_referer_page', 'some.referer.com');
  	$this->stats_ip2->setReturnValue('get_client_ip', $ip);

  	$this->stats_counter->setReturnValueAt(1, '_is_home_hit', false);
  	
		$this->stats_register2->set_register_time(time() - 2*60*60*24);
  	$this->stats_register2->register(4, 'edit', RESPONSE_STATUS_SUCCESS);
		
		$this->_check_stats_log_record(2, 2, 10, 4, 'edit', RESPONSE_STATUS_SUCCESS, $this->stats_register2->get_register_time_stamp());
		$this->_check_stats_referer_url_record(1, 1, 'some.referer.com');
		$this->_check_stats_ip_record(1, $ip, $stamp);

		$this->_check_stats_counter_record(
			$hits_all = 1,
			$hits_today = 1,
			$hosts_all = 1,
			$hosts_today = 1, 
			$stamp);

  	$this->_check_stats_day_counters_record($hits_today, $hosts_today, $home_hits = 1, $stamp);
		
  }
  
	function _check_stats_log_record($total_records, $current_record, $user_id, $node_id, $action, $status, $time)
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
  	$this->assertEqual($record['status'], $status);
  	$this->assertEqual($record['time'], $time, 'log time is incorrect');
  	$this->assertEqual($record['session_id'], session_id());
  }

  function _check_stats_referer_url_record($total_records, $current_record, $referer)
  {
  	$this->db->sql_select('sys_stat_referer_url', '*', '', 'id');
  	$arr = $this->db->get_array('id');

  	$this->assertTrue(sizeof($arr), $total_records, 'referers count is wrong');
  	reset($arr);
  	
  	for($i = 1; $i <= $current_record; $i++)
  	{
  	 	$record = current($arr);
  	 	next($arr);
  	}
  	
  	$this->assertEqual($record['referer_url'], $referer, 'referer url was parsed or written incorrectly');
  }
  
  function _check_stats_ip_record($total_records, $ip, $time)
  {
  	$this->db->sql_select('sys_stat_ip');
  	$arr = $this->db->get_array('id');

  	$this->assertTrue(sizeof($arr), $total_records, 'ip count is wrong');
  	$this->assertTrue(isset($arr[$ip]));
  	$this->assertEqual($arr[$ip]['time'], $time, 'ip time is incorrect');
  }
  
  function _check_stats_counter_record($hits_all, $hits_today, $hosts_all, $hosts_today, $time)
  {
  	$this->db->sql_select('sys_stat_counter');
  	$record = $this->db->fetch_row();
  	
  	$this->assertNotIdentical($record, false, 'counter record doesnt exist');
  	$this->assertEqual($record['hits_all'], $hits_all, 'all hits incorrect. Got ' . $record['hits_all'] . ', expected '. $hits_all);
  	$this->assertEqual($record['hits_today'], $hits_today, 'today hits incorrect. Got ' . $record['hits_today'] . ', expected '. $hits_today);
  	$this->assertEqual($record['hosts_all'], $hosts_all, 'all hosts incorrect. Got ' . $record['hosts_all'] . ', expected '. $hosts_all);
  	$this->assertEqual($record['hosts_today'], $hosts_today, 'today hosts incorrect. Got ' . $record['hosts_today'] . ', expected '. $hosts_today);
  	$this->assertEqual($record['time'], $time, 'counter time is incorrect. Got ' . $record['time'] . ', expected '. $time);  	
  	
  	$this->_check_counters_consistency($time);
  }
  
  function _check_stats_day_counters_record($hits, $hosts, $home_hits, $time)
  {
  	$this->db->sql_select('sys_stat_day_counters', '*', array('time' => $this->stats_counter->_make_day_stamp($time)));
  	$record = $this->db->fetch_row();
		
		$this->assertNotIdentical($record, false, 'day counters record doesnt exist');
  	$this->assertEqual($record['hits'], $hits, 'day hits incorrect. Got ' . $record['hits'] . ', expected '. $hits);
  	$this->assertEqual($record['hosts'], $hosts, 'day hits incorrect. Got ' . $record['hosts'] . ', expected '. $hosts);  	
  	$this->assertEqual($record['home_hits'], $home_hits, 'day home hits incorrect. Got ' . $record['home_hits'] . ', expected '. $home_hits);  	
  }
  
  function _check_counters_consistency($time)
  {
  	$this->db->sql_exec('	SELECT 
  												SUM(ssdc.hits) as hits_all,  
  												SUM(ssdc.hosts) as hosts_all
  												FROM
  												sys_stat_day_counters as ssdc');
  	$record1 = $this->db->fetch_row();

  	$this->db->sql_select('sys_stat_counter');
  	$record2 = $this->db->fetch_row();
  	
  	$this->assertEqual($record1['hits_all'], $record2['hits_all'], 'Counters all hits number inconsistent. ' . $record1['hits_all'] . ' not equal '. $record2['hits_all']);
  	$this->assertEqual($record1['hosts_all'], $record2['hosts_all'], 'Counters all hosts number inconsistent. ' . $record1['hosts_all'] . ' not equal '. $record2['hosts_all']);
  	
  	$this->db->sql_select('sys_stat_day_counters', '*', array('time' => $this->stats_counter->_make_day_stamp($time)));
  	$record3 = $this->db->fetch_row();

  	$this->assertEqual($record3['hits'], $record2['hits_today'], 'Counters day hits number inconsistent. ' . $record3['hits'] . ' not equal '. $record2['hits_today']);
  	$this->assertEqual($record3['hosts'], $record2['hosts_today'], 'Counters day hosts number inconsistent. ' . $record3['hosts'] . ' not equal '. $record2['hosts_today']);
  }
  
  function _login_user($id, $groups)
  {
		$_SESSION[user :: get_session_identifier()]['id'] = $id;
		$_SESSION[user :: get_session_identifier()]['groups'] = $groups;
  }
  
}

?>