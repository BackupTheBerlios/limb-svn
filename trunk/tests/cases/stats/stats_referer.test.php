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
require_once(LIMB_DIR . '/core/model/stats/stats_referer.class.php');

Mock::generatePartial
(
  'stats_referer',
  'stats_referer_self_test_version',
  array(
  	'_get_http_referer'
  )
);

class test_stats_referer extends UnitTestCase 
{
  var $stats_referer = null;
  var $connection = null;
	
  function test_stats_referer() 
  {
  	parent :: UnitTestCase();
  	
  	$this->connection=& db_factory :: get_connection();
  }
  
  function setUp()
  {
   	$this->stats_referer = new stats_referer_self_test_version($this);
   	$this->stats_referer->stats_referer();
   	
   	$this->_clean_up();
  }
  
  function tearDown()
  {
  	$this->stats_referer->tally();
  	
  	$this->_clean_up();
  }
  
  function _clean_up()
  {
  	$this->connection->sql_delete('sys_stat_referer_url');
  }
  
  function test_get_referer_page_id_no_referer()
  {
  	$this->stats_referer->setReturnValue('_get_http_referer', '');
  	
  	$this->assertEqual(-1, $this->stats_referer->get_referer_page_id());
  }
  
  function test_get_referer_page_id_inner_referer()
  {
  	$this->stats_referer->setReturnValue('_get_http_referer', 'http://' . $_SERVER['HTTP_HOST'] . '/test');
  	
  	$this->assertEqual(-1, $this->stats_referer->get_referer_page_id());
  }
  
  function test_get_referer_page_id()
  {
  	$this->stats_referer->setReturnValue('_get_http_referer', 'http://wow.com/test/referer');
  	
  	$id = $this->stats_referer->get_referer_page_id();
  	
  	$this->connection->sql_select('sys_stat_referer_url');
  	$arr = $this->connection->get_array();
  	$record = current($arr);
  	
  	$this->assertEqual(sizeof($arr), 1);
  	
		$this->assertEqual($record['id'], $id);
  }
  
  function test_get_referer_page_id_same_id()
  {
  	$this->test_get_referer_page_id();
  	$this->test_get_referer_page_id();
  }
          
  function test_clean_url()
  {
  	$this->assertEqual(
  		'http://wow.com.bit/some/path?yo=1&haba',
  		$this->stats_referer->clean_url('http://wow.com.bit/some/path/?PHPSESSID=8988190381803003109&yo=1&haba&haba#not'));
  }
 
}

?>