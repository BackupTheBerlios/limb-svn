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

class ip_test extends LimbTestCase
{
  var $ip = null;

  function ip_test()
  {
    parent :: LimbTestCase();
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