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
require_once(dirname(__FILE__) . '/../../../stats_uri.class.php');

Mock :: generatePartial
(
  'stats_uri',
  'stats_uri_self_test_version',
  array(
  	'_get_http_uri'
  )
);

class stats_uri_test extends LimbTestCase 
{
  var $stats_uri = null;
  var $db = null;
  var $server = array();
	  
  function setUp()
  {
    $this->server = $_SERVER;    
    $_SERVER['HTTP_HOST'] = 'test';
  
  	$this->db =& db_factory :: instance();
  	
   	$this->stats_uri = new stats_uri_self_test_version($this);
   	$this->stats_uri->__construct();
   	
   	$this->_clean_up();
  }
  
  function tearDown()
  {
    $_SERVER = $this->server;
    
  	$this->stats_uri->tally();
  	
  	$this->_clean_up();
  }
  
  function _clean_up()
  {
  	$this->db->sql_delete('sys_stat_uri');
  }
    
  function test_new_inner_uri()
  {
  	$this->stats_uri->setReturnValue('_get_http_uri', 'http://' . $_SERVER['HTTP_HOST'] . '/test');
  	
  	$id = $this->stats_uri->get_uri_id();
  	
  	$this->db->sql_select('sys_stat_uri');
  	$arr = $this->db->get_array();
  	$record = current($arr);

  	$this->assertEqual(sizeof($arr), 1);
  	
		$this->assertEqual($record['id'], $id);
		$this->assertEqual($record['uri'], '/test');
  }
  
  function test_new_outer_uri()
  {
  	$this->stats_uri->setReturnValue('_get_http_uri', 'http://wow.com/test');
  	
  	$id = $this->stats_uri->get_uri_id();
  	
  	$this->db->sql_select('sys_stat_uri');
  	$arr = $this->db->get_array();
  	$record = current($arr);

  	$this->assertEqual(sizeof($arr), 1);
  	
		$this->assertEqual($record['id'], $id);
		$this->assertEqual($record['uri'], 'http://wow.com/test');
  }
  
  function test_same_uri()
  {
  	$this->test_new_outer_uri();
  	$this->test_new_outer_uri();  	  	
  }
              
  function test_clean_outer_uri()
  {
  	$this->assertEqual(
  		'http://wow.com.bit/some/path',
  		$this->stats_uri->clean_url('http://wow.com.bit/some/path/?PHPSESSID=8988190381803003109&yo=1&haba&haba#not'));
  }
  
  function test_clean_inner_uri()
  {
  	$this->assertEqual(
  		'/test',
  		$this->stats_uri->clean_url('http://' . $_SERVER['HTTP_HOST'] . '/test?PHPSESSID=8988190381803003109&yo=1&haba&haba#not'));
  }
 
}

?>