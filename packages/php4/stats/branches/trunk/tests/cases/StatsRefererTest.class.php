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
require_once(dirname(__FILE__) . '/../../StatsReferer.class.php');
require_once(LIMB_DIR . '/core/db/LimbDbPool.class.php');

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

    $this->conn =& LimbDbPool :: getConnection();
    $this->db =& new SimpleDb($this->conn);

    $this->stats_referer = new StatsRefererSelfTestVersion($this);
    $this->stats_referer->StatsReferer();

    $this->_cleanUp();
  }

  function tearDown()
  {
    $_SERVER = $this->server;

    $this->stats_referer->tally();

    $this->_cleanUp();
  }

  function _cleanUp()
  {
    $this->db->delete('stats_referer_url');
  }

  function testGetRefererPageIdNoReferer()
  {
    $this->stats_referer->setReturnValue('_getHttpReferer', '');

    $this->assertEqual(-1, $this->stats_referer->getRefererPageId());
  }

  function testGetRefererPageIdInnerReferer()
  {
    $this->stats_referer->setReturnValue('_getHttpReferer', 'http://' . $_SERVER['HTTP_HOST'] . '/test');

    $this->assertEqual(-1, $this->stats_referer->getRefererPageId());
  }

  function testGetRefererPageId()
  {
    $this->stats_referer->setReturnValue('_getHttpReferer', 'http://wow.com/test/referer');

    $id = $this->stats_referer->getRefererPageId();

    $rs = $this->db->select('stats_referer_url');
    $arr = $rs->getArray();
    $record = current($arr);

    $this->assertEqual(sizeof($arr), 1);

    $this->assertEqual($record['id'], $id);
  }

  function testGetRefererPageIdSameId()
  {
    $this->testGetRefererPageId();
    $this->testGetRefererPageId();
  }

  function testCleanUrl()
  {
    $this->assertEqual(
      'http://wow.com.bit/some/path/?haba&yo=1',
      $this->stats_referer->cleanUrl('http://wow.com.bit/some/path/?PHPSESSID=8988190381803003109&yo=1&haba&haba#not'));
  }

}

?>