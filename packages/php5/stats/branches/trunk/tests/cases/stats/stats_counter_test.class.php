<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: limb@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/ 
require_once(dirname(__FILE__) . '/../../../stats_register.class.php');
require_once(dirname(__FILE__) . '/../../../stats_counter.class.php');
require_once(LIMB_DIR . '/class/lib/db/db_factory.class.php');

Mock :: generatePartial
(
  'stats_counter',
  'stats_counter_test_version',
  array(
  	'_is_home_hit',
  	'_is_new_audience',
  )
);

class stats_counter_test extends LimbTestCase 
{
	var $db = null;
	
	var $stats_counter = null;
	  
  function setUp()
  {
  	$this->db = db_factory :: instance();
  	
   	$this->stats_counter = new stats_counter_test_version($this);
   	$this->stats_counter->__construct();

  	$this->stats_counter->setReturnValue('_is_home_hit', false);
  	$this->stats_counter->setReturnValue('_is_new_audience', false);
    	  	
  	$this->_clean_up();
  }
  
  function tearDown()
  {
		$this->stats_counter->tally();

  	$this->_clean_up();
  }
  
  function _clean_up()
  {
  	$this->db->sql_delete('sys_stat_counter');
  	$this->db->sql_delete('sys_stat_day_counters');
  }
      
  function test_new_host() 
  {
  	$date = new date();
  	
  	$this->stats_counter->set_new_host();
  	
  	$this->stats_counter->update($date);
  	
		$this->_check_stats_counter_record(
			$hits_all = 1,
			$hits_today = 1,
			$hosts_all = 1,
			$hosts_today = 1, 
			$date);

  	$this->_check_stats_day_counters_record(
  		$hits_today, 
  		$hosts_today, 
  		$home_hits = 0, 
  		$audience_host = 0, 
  		$date);
  		
  	$this->_check_counters_consistency($date);
  }
  
  function test_new_host_same_day()
  {
  	$this->test_new_host();
  	
  	$date = new date();
  	$this->stats_counter->set_new_host();
  	$this->stats_counter->update($date);
  			
		$this->_check_stats_counter_record(
			$hits_all = 2,
			$hits_today = 2,
			$hosts_all = 2,
			$hosts_today = 2, 
			$date);

  	$this->_check_stats_day_counters_record(
  		$hits_today, 
  		$hosts_today, 
  		$home_hits = 0, 
  		$audience_host = 0,
  		$date
  	);
  	
  	$this->_check_counters_consistency($date);
  }

  function test_new_host_new_day()
  {
  	$this->test_new_host();
  	
  	$date = new date();
  	$date->set_by_days($date->date_to_days() + 1);
  	$this->stats_counter->set_new_host();
  	$this->stats_counter->update($date);
  			
		$this->_check_stats_counter_record(
			$hits_all = 2,
			$hits_today = 1,
			$hosts_all = 2,
			$hosts_today = 1, 
			$date);

  	$this->_check_stats_day_counters_record(
  		$hits_today, 
  		$hosts_today, 
  		$home_hits = 0, 
  		$audience_host = 0,
  		$date
  	);
  	
  	$this->_check_counters_consistency($date);
  }
  
  function test_new_audience()
  {
  	$date = new date();
  	$this->stats_counter->set_new_host();

  	$this->stats_counter->setReturnValueAt(0, '_is_new_audience', true);

  	$this->stats_counter->update($date);
  	
  	$this->_check_stats_day_counters_record(
  		$hits_today = 1, 
  		$hosts_today = 1, 
  		$home_hits = 0, 
  		$audience_host = 1,
  		$date
  	);
  }
  
  function test_new_audience_same_host()
  {
  	$date = new date();

  	$this->stats_counter->setReturnValueAt(0, '_is_new_audience', true);

  	$this->stats_counter->update($date);
  	
  	$this->_check_stats_day_counters_record(
  		$hits_today = 1, 
  		$hosts_today = 0, 
  		$home_hits = 0, 
  		$audience_host = 0,
  		$date
  	);
  }
  
  function test_home_hit()
  {
  	$date = new date();
  	$this->stats_counter->setReturnValueAt(0, '_is_home_hit', true);

  	$this->stats_counter->update($date);
  	
  	$this->_check_stats_day_counters_record(
  		$hits_today = 1, 
  		$hosts_today = 0, 
  		$home_hits = 1, 
  		$audience_host = 0,
  		$date
  	);
  }
    
  function _check_stats_counter_record($hits_all, $hits_today, $hosts_all, $hosts_today, $date)
  {
  	$this->db->sql_select('sys_stat_counter');
  	$record = $this->db->fetch_row();
  	
  	$this->assertNotIdentical($record, false, 'counter record doesnt exist');
  	$this->assertEqual($record['hits_all'], $hits_all, 'all hits incorrect. Got ' . $record['hits_all'] . ', expected '. $hits_all);
  	$this->assertEqual($record['hits_today'], $hits_today, 'today hits incorrect. Got ' . $record['hits_today'] . ', expected '. $hits_today);
  	$this->assertEqual($record['hosts_all'], $hosts_all, 'all hosts incorrect. Got ' . $record['hosts_all'] . ', expected '. $hosts_all);
  	$this->assertEqual($record['hosts_today'], $hosts_today, 'today hosts incorrect. Got ' . $record['hosts_today'] . ', expected '. $hosts_today);
  	$this->assertEqual($record['time'], $date->get_stamp(), 'counter time is incorrect. Got ' . $record['time'] . ', expected '. $date->get_stamp());
  }
  
  function _check_stats_day_counters_record($hits, $hosts, $home_hits, $audience_hosts, $date)
  {
  	
  	$this->db->sql_select('sys_stat_day_counters', '*', array('time' => $this->stats_counter->make_day_stamp($date->get_stamp())));
  	$record = $this->db->fetch_row();
		
		$this->assertNotIdentical($record, false, 'day counters record doesnt exist');
  	$this->assertEqual($record['hits'], $hits, 'day hits incorrect. Got ' . $record['hits'] . ', expected '. $hits);
  	$this->assertEqual($record['hosts'], $hosts, 'day hits incorrect. Got ' . $record['hosts'] . ', expected '. $hosts);  	
  	$this->assertEqual($record['home_hits'], $home_hits, 'day home hits incorrect. Got ' . $record['home_hits'] . ', expected '. $home_hits);  	
  	$this->assertEqual($record['audience'], $audience_hosts, 'audience incorrect. Got ' . $record['audience'] . ', expected '. $audience_hosts);
  }
  
  function _check_counters_consistency($date)
  {
  	$time = $date->get_stamp();
  	
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
  	
  	$this->db->sql_select('sys_stat_day_counters', '*', array('time' => $this->stats_counter->make_day_stamp($time)));
  	$record3 = $this->db->fetch_row();

  	$this->assertEqual($record3['hits'], $record2['hits_today'], 'Counters day hits number inconsistent. ' . $record3['hits'] . ' not equal '. $record2['hits_today']);
  	$this->assertEqual($record3['hosts'], $record2['hosts_today'], 'Counters day hosts number inconsistent. ' . $record3['hosts'] . ' not equal '. $record2['hosts_today']);
  }
   
}

?>