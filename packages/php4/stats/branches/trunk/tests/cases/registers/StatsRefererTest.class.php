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
require_once(LIMB_STATS_DIR . '/registers/StatsReferer.class.php');

Mock :: generatePartial
(
  'StatsReferer',
  'StatsRefererSelfTestVersion',
  array(
    '_getHttpReferer'
  )
);

class StatsRefererTest extends LimbTestCase
{
  var $stats_referer = null;
  var $db = null;
  var $conn = null;
  var $server = array();

  function StatsRefererTest()
  {
    parent :: LimbTestCase('stats referer test');
  }

  function setUp()
  {
    $this->server = $_SERVER;
    $_SERVER['HTTP_HOST'] = 'test';

    $toolkit =& Limb :: toolkit();
    $this->conn =& $toolkit->getDbConnection();
    $this->db =& new SimpleDb($this->conn);

    $this->_cleanUp();
  }

  function tearDown()
  {
    $_SERVER = $this->server;

    $this->_cleanUp();
  }

  function _cleanUp()
  {
    $this->db->delete('stats_referer_url');
  }

  function testIsRefererToOtherDomain()
  {
    $uri = new Uri('http://test1.com/');
    $stats_referer = new StatsReferer();

    $uri2 = new Uri('http://test2.com/');
    $this->assertTrue($stats_referer->isRefererTo($uri, $uri2));
  }

  function testNotIsRefererToSameDomain()
  {
    $uri = new Uri('http://test.com/');
    $stats_referer = new StatsReferer();

    $uri2 = new Uri('http://test.com/');
    $this->assertFalse($stats_referer->isRefererTo($uri, $uri2));
  }

  function testGetIdNoReferer()
  {
    $stats_referer = new StatsReferer();
    $this->assertEqual(-1, $stats_referer->getId(new Uri()));
  }

  function testGetId()
  {
    $stats_referer = new StatsReferer();

    $id = $stats_referer->getId(new Uri('http://wow.com/test/referer'));

    $rs = $this->db->select('stats_referer_url');
    $arr = $rs->getArray();
    $record = current($arr);

    $this->assertEqual(sizeof($arr), 1);

    $this->assertEqual($record['id'], $id);
  }

  function testGetRefererPageIdSameId()
  {
    $uri = new Uri('http://wow.com/test/referer');
    $stats_referer = new StatsReferer();

    $id1 = $stats_referer->getId($uri);
    $id2 = $stats_referer->getId($uri);

    $this->assertEqual($id1, $id2);
    $rs = $this->db->select('stats_referer_url');
    $this->assertEqual($rs->getRowCount(), 1);
  }

  function testCleanUrl()
  {
    $stats_referer = new StatsReferer();

    $this->assertEqual(
      'http://wow.com.bit/some/path/?haba&yo=1',
      $stats_referer->cleanUrl('http://wow.com.bit/some/path/?PHPSESSID=8988190381803003109&yo=1&haba&haba#not'));
  }

}

?>