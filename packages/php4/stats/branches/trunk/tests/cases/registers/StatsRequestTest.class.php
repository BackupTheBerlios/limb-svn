<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: StatsRegisterTest.class.php 1135 2005-03-03 10:25:19Z seregalimb $
*
***********************************************************************************/
require_once(LIMB_STATS_DIR . '/registers/StatsRequest.class.php');
require_once(LIMB_DIR . '/core/http/Uri.class.php');

class StatsRequestTest extends LimbTestCase
{
  function StatsRequestTest()
  {
    parent :: LimbTestCase('stats request test');
  }

  function setUp()
  {
  }

  function tearDown()
  {
  }

  function testIsHomeHit()
  {
    $stats_request = new StatsRequest();

    $stats_request->setUri(new Uri('http://test.com/root'));
    $stats_request->setBaseUri(new Uri('http://test.com/root'));

    $this->assertTrue($stats_request->isHomeHit());
  }

  function testIsNotHomeHit()
  {
    $stats_request = new StatsRequest();

    $stats_request->setUri(new Uri('http://test.com/root/path'));
    $stats_request->setBaseUri(new Uri('http://test.com/root'));

    $this->assertFalse($stats_request->isHomeHit());
  }

  function testIsAudienceHit()
  {
    $stats_request = new StatsRequest();

    $this->assertTrue($stats_request->isAudienceHit());
  }

  function testIsNotAudienceHit()
  {
    $stats_request = new StatsRequest();

    $stats_request->setRefererUri(new Uri('http://test.com/root'));

    $this->assertFalse($stats_request->isAudienceHit());
  }
}

?>