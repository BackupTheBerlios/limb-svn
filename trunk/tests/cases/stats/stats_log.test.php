<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: stats_referer.test.php 37 2004-03-13 10:36:02Z server $
*
***********************************************************************************/ 
require_once(LIMB_DIR . '/core/model/stats/stats_log.class.php');

class test_stats_log extends UnitTestCase 
{
  var $stats_log = null;
	
  function test_stats_log() 
  {
  	parent :: UnitTestCase();
  }
  
  function setUp()
  {
  	$this->_clean_up();
   	$this->stats_log = new stats_log();
  }
  
  function tearDown()
  {
    $this->_clean_up();
  }
          
  function test_clean_log()
  {
  	$this->stats_log->update(time(), ip :: encode_ip(sys :: client_ip()), 1, 'test', 0);
  	$this->stats_log->update(time() + 2*60*60*24, ip :: encode_ip(sys :: client_ip()), 1, 'test', 0);
  	$this->stats_log->update(time() + 3*60*60*24, ip :: encode_ip(sys :: client_ip()), 1, 'test', 0);
  	$this->stats_log->update(time() + 4*60*60*24, ip :: encode_ip(sys :: client_ip()), 1, 'test', 0);
  	$this->stats_log->update(time() + 5*60*60*24, ip :: encode_ip(sys :: client_ip()), 1, 'test', 0);
  	$this->stats_log->update(time() + 6*60*60*24, ip :: encode_ip(sys :: client_ip()), 1, 'test', 0);
  	
  	$date = new date();
  	$date->set_by_stamp(time() + 4*60*60*24 - 10);
  	$this->stats_log->clean_until($date);
  	
  	$this->assertEqual(3, $this->stats_log->count_log_records());
  }

  function _clean_up()
  {
   	$db = db_factory :: instance();
  	$db->sql_delete('sys_stat_log');
  }
 
}

?>