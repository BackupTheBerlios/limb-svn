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

class test_stats_log extends UnitTestCase 
{
	var $db = null;
	var $stats_log = null;
	
  function test_stats_log() 
  {
  	parent :: UnitTestCase();
  	
  	$this->db = db_factory :: instance();
  }
  
  function setUp()
  {
  	$this->stats_log = new stats_log();
		$this->_login_user(10, array());
  	
  	$this->_clean_up();
  }
  
  function tearDown()
  {
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

  	$this->db->sql_select('sys_stat_log');
  	$record = $this->db->fetch_row();
  	
  	$this->assertTrue($record['user_id'], 10);
  	$this->assertTrue($record['node_id'], 2);
  	$this->assertTrue($record['action'], 'display');
  	$this->assertTrue($record['time'], $this->stats_log->reg_date->get_stamp());
  }

  function _login_user($id, $groups)
  {
		$_SESSION[user :: get_session_identifier()]['id'] = $id;
		$_SESSION[user :: get_session_identifier()]['groups'] = $groups;
  }
  
}

?>