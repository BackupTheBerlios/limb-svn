<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/http/Ip.class.php');

class IpTest extends LimbTestCase
{
  var $ip = null;

  function ipTest()
  {
    parent :: LimbTestCase();
  }

  function setUp()
  {
    $this->ip = new Ip();
  }

  function testEncodeIpRange()
  {
    $ip_list = $this->ip->encodeIpRange('192.168.0.1', '192.168.10.10');

    $this->assertNotIdentical(false, array_search($this->ip->encode('192.168.0.1'), $ip_list));
    $this->assertNotIdentical(false, array_search($this->ip->encode('192.168.10.10'), $ip_list));
  }
}

?>