<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: stats_ip.test.php 37 2004-03-13 10:36:02Z server $
*
***********************************************************************************/ 
require_once(LIMB_DIR . '/core/model/stats/stats_ip.class.php');
require_once(LIMB_DIR . '/core/lib/http/ip.class.php');

Mock::generatePartial
(
  'stats_ip',
  'stats_ip_self_test_version',
  array(
  	'get_client_ip'
  )
);

class test_stats_ip extends UnitTestCase 
{
  var $stats_ip = null;
  var $db = null;
	
  function test_stats_ip() 
  {
  	parent :: UnitTestCase();
  	
  	$this->db =& db_factory :: instance();
  }
  
  function setUp()
  {
   	$this->stats_ip = new stats_ip_self_test_version($this);
   	$this->stats_ip->stats_ip();
   	
   	$this->_clean_up();
  }
  
  function tearDown()
  {
  	$this->stats_ip->tally();
  	
  	$this->_clean_up();
  }
  
  function _clean_up()
  {
  	$this->db->sql_delete('sys_stat_ip');
  }
  
  function test_new_host() 
  {
  	$date = new date();
  	$ip = ip :: encode_ip('192.168.0.5');
  	$this->stats_ip->setReturnValue('get_client_ip', $ip);
  	
  	$this->assertTrue($this->stats_ip->is_new_host($date));
  	
  	$this->_check_stats_ip_record($total_records = 1, $ip, $date);
  }

  function test_second_new_host()
  {
  	$date = new date();
  	$ip = ip :: encode_ip('192.168.0.5');
  	$this->stats_ip->setReturnValue('get_client_ip', $ip);
  	
  	$date = new date();
  	$ip = ip :: encode_ip('192.168.0.6');
  	$this->stats_ip->setReturnValueAt(1, 'get_client_ip', $ip);

  	$this->assertTrue($this->stats_ip->is_new_host($date));
  }
  
  function test_same_host_new_day()
  {
  	$date = new date();
  	$ip = ip :: encode_ip('192.168.0.5');
  	$this->stats_ip->setReturnValue('get_client_ip', $ip);

  	$this->stats_ip->is_new_host($date);

  	$date = new date();
  	$date->set_by_days($date->date_to_days() + 1);
  	$this->stats_ip->setReturnValueAt(1, 'get_client_ip', $ip);
  	
  	$this->assertTrue($this->stats_ip->is_new_host($date));
  	
  	$this->_check_stats_ip_record($total_records = 1, $ip, $date);
  }
	
	function test_same_host_wrong_day()
	{
  	$date1 = new date();
  	$ip = ip :: encode_ip('192.168.0.5');
  	$this->stats_ip->setReturnValue('get_client_ip', $ip);

  	$this->stats_ip->is_new_host($date1);

  	$date2 = new date();
  	$date2->set_by_days($date1->date_to_days() - 2);
  	$this->stats_ip->setReturnValueAt(1, 'get_client_ip', $ip);
  	
  	$this->assertFalse($this->stats_ip->is_new_host($date2));
  	
  	$this->_check_stats_ip_record($total_records = 1, $ip, $date1);
	}

  function _check_stats_ip_record($total_records, $ip, $date)
  {
  	$this->db->sql_select('sys_stat_ip');
  	$arr = $this->db->get_array('id');

  	$this->assertTrue(sizeof($arr), $total_records, 'ip count is wrong');
  	$this->assertTrue(isset($arr[$ip]));
  	$this->assertEqual($arr[$ip]['time'], $date->get_stamp(), 'ip time is incorrect');
  }
}

?>