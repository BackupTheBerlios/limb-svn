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
require_once(LIMB_STATS_DIR . '/registers/StatsHitRegister.class.php');
require_once(LIMB_STATS_DIR . '/registers/StatsRequest.class.php');
require_once(LIMB_STATS_DIR . '/registers/StatsReferer.class.php');
require_once(LIMB_STATS_DIR . '/registers/StatsUri.class.php');
require_once(LIMB_DIR . '/core/request/Request.class.php');
require_once(LIMB_DIR . '/core/db/LimbDbPool.class.php');

Mock :: generate('StatsReferer');
Mock :: generate('StatsUri');

class StatsHitRegisterTest extends LimbTestCase
{
  var $db;
  var $conn;

  var $stats_referer;
  var $stats_uri;

  var $register;

  function StatsHitRegisterTest()
  {
    parent :: LimbTestCase('stats hit register test');

    $this->conn =& LimbDbPool :: getConnection();
    $this->db =& new SimpleDb($this->conn);
  }

  function setUp()
  {
    $this->register = new StatsHitRegister();

    $this->stats_referer = new MockStatsReferer($this);
    $this->stats_uri = new MockStatsUri($this);

    $this->register->setStatsReferer($this->stats_referer);
    $this->register->setStatsUri($this->stats_uri);

    $this->_cleanUp();
  }

  function tearDown()
  {
    $this->stats_referer->tally();
    $this->stats_uri->tally();

    $this->_cleanUp();
  }

  function _cleanUp()
  {
    $this->db->delete('stats_hit');
  }

  function testRegister()
  {
    $stats_request = new StatsRequest();
    $stats_request->setTime($time = time());
    $stats_request->setAction($action = 'test');
    $stats_request->setClientIp('127.0.0.1');

    $referer_uri = new Uri('http://example.com');
    $uri = new Uri('http://test.com');

    $stats_request->setRefererUri($referer_uri);
    $stats_request->setUri($uri);

    $this->stats_referer->expectOnce('getId', array($referer_uri));
    $this->stats_uri->expectOnce('getId', array($uri));

    $this->stats_referer->setReturnValue('getId', $refered_id = 1);
    $this->stats_uri->setReturnValue('getId', $uri_id = 2);

    $this->register->register($stats_request);

    $rs =& $this->db->select('stats_hit');
    $record = $rs->getRow();

    $this->assertEqual($record['action'], $action);
    $this->assertEqual($record['time'], $time, 'log time is incorrect');
    $this->assertEqual($record['session_id'], session_id());
    $this->assertEqual($record['ip'], Ip :: decode('127.0.0.1'));
    $this->assertEqual($record['stats_referer_id'], 1);
    $this->assertEqual($record['stats_uri_id'], $uri_id);
  }

  function testCleanLog()
  {
    $stats_request = new StatsRequest();
    $stats_request->setTime($time = time());
    $stats_request->setAction($action = 'test');
    $stats_request->setClientIp('127.0.0.1');

    $referer_uri = new Uri('http://example.com');
    $uri = new Uri('http://test.com');

    $stats_request->setRefererUri($referer_uri);
    $stats_request->setUri($uri);

    $this->stats_referer->setReturnValue('getId', $refered_id = 1);
    $this->stats_uri->setReturnValue('getId', $uri_id = 2);

    $this->register->register($stats_request);

    $stats_request->setTime(time() + 2*60*60*24);
    $this->register->register($stats_request);

    $stats_request->setTime(time() + 3*60*60*24);
    $this->register->register($stats_request);

    $stats_request->setTime(time() + 4*60*60*24);
    $this->register->register($stats_request);

    $stats_request->setTime(time() + 5*60*60*24);
    $this->register->register($stats_request);

    $stats_request->setTime(time() + 6*60*60*24);
    $this->register->register($stats_request);

    $this->register->cleanUntil(time() + 4*60*60*24 - 10);

    $rs =& $this->db->select('stats_hit');
    $this->assertEqual(3, $rs->getTotalRowCount());
  }
}

?>