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
require_once(LIMB_DIR . '/core/lib/http/ip.class.php');

class test_ip extends UnitTestCase 
{
	var $ip = null;
	
  function test_ip() 
  {
  	parent :: UnitTestCase();
  }

  function setUp()
  {
  	$this->ip = new ip();
  }
        
  function test_encode_ip_range()
  {
  	$ip_list = $this->ip->encode_ip_range('192.168.0.1', '192.168.10.10');
  	
  	$this->assertNotIdentical(false, array_search($this->ip->encode_ip('192.168.0.1'), $ip_list));
  	$this->assertNotIdentical(false, array_search($this->ip->encode_ip('192.168.10.10'), $ip_list));
  }
}

?>