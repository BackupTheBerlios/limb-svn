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
require_once(LIMB_DIR . '/core/model/stats/stats_referer.class.php');

class test_stats_referer extends UnitTestCase 
{
  var $stats_referer = null;
	
  function test_stats_referer() 
  {
  	parent :: UnitTestCase();
  }
  
  function setUp()
  {
   	$this->stats_referer = new stats_referer();
  }
          
  function test_clean_url()
  {
  	$this->assertEqual(
  		'http://wow.com.bit/some/path?yo=1&haba',
  		$this->stats_referer->clean_url('http://wow.com.bit/some/path/?PHPSESSID=8988190381803003109&yo=1&haba&haba#not'));
  }
 
}

?>