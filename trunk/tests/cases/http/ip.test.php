<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: log.test.php 2 2004-02-29 19:06:22Z server $
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
    
  function test_process_simple_ips_list()
  {
  	$ip_list = $this->ip->process_ip_range('192.168.0.1, 192.168.0.2, 192.168.0.3');
  	
  	$this->assertEqual($ip_list[0], ip :: encode_ip('192.168.0.1'));
  	$this->assertEqual($ip_list[1], ip :: encode_ip('192.168.0.2'));
  	$this->assertEqual($ip_list[2], ip :: encode_ip('192.168.0.3'));
  }
  
  function test_process_templated_ip()
  {
  	$ip = $this->_make_random_templated_ip();
  	
  	$ip_list = $this->ip->process_ip_range($ip);
  	
  	$this->assertEqual(sizeof($ip_list), 1);
  	$this->assertEqual(str_replace('*', '255', $ip), $this->ip->decode_ip($ip_list[0]));
  }
  
  function test_process_ranged_ip()
  {
  	$ip_list = $this->ip->process_ip_range('192.168.0.1 - 192.168.10.10');
  	
  	$this->assertNotIdentical(false, array_search($this->ip->encode_ip('192.168.0.1'), $ip_list));
  	$this->assertNotIdentical(false, array_search($this->ip->encode_ip('192.168.10.10'), $ip_list));
  }
    
  function test_process_templated_ips_list()
  {
  	$ip1 = $this->_make_random_templated_ip();
  	$ip2 = $this->_make_random_templated_ip();
  	$ip3 = $this->_make_random_templated_ip();
  	
  	$ip_list1 = $this->ip->process_ip_range($ip1);
  	$ip_list2 = $this->ip->process_ip_range($ip2);
  	$ip_list3 = $this->ip->process_ip_range($ip3);
  	
		$ip_list = $this->ip->process_ip_range($ip1 . ',' . $ip2 . ',' . $ip3);
		
		$this->assertEqual(sizeof($ip_list), 3);
		$this->assertEqual($ip_list, array_merge($ip_list1, $ip_list2, $ip_list3));
  }  
  
  function _make_random_templated_ip()
  {
  	$number = array();
		for($i=0; $i<4; $i++)
		{
			$number[$i] = mt_rand(0, 1) ? '*' : (mt_rand(1, 255));
		}
		
		return implode('.', $number);
  }
}

?>