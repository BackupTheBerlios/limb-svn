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
require_once(dirname(__FILE__) . '/../../StatsUri.class.php');
require_once(LIMB_DIR . '/core/db/LimbDbPool.class.php');

Mock :: generatePartial
(
  'StatsUri',
  'StatsUriSelfTestVersion',
  array(
    '_getHttpUri'
  )
);

class StatsUriTest extends LimbTestCase
{
  var $stats_uri = null;
  var $db = null;
  var $conn = null;
  var $server = array();

  function StatsUriTest()
  {
    parent :: LimbTestCase('stats uri test');
  }

  function setUp()
  {
    $this->server = $_SERVER;
    $_SERVER['HTTP_HOST'] = 'test';

    $this->conn =& LimbDbPool :: getConnection();
    $this->db =& new SimpleDb($this->conn);

    $this->stats_uri = new StatsUriSelfTestVersion($this);
    $this->stats_uri->StatsUri();

    $this->_cleanUp();
  }

  function tearDown()
  {
    $_SERVER = $this->server;

    $this->stats_uri->tally();

    $this->_cleanUp();
  }

  function _cleanUp()
  {
    $this->db->delete('stats_uri');
  }

  function testNewInnerUri()
  {
    $this->stats_uri->setReturnValue('_getHttpUri', 'http://' . $_SERVER['HTTP_HOST'] . '/test');

    $id = $this->stats_uri->getUriId();

    $rs =& $this->db->select('stats_uri');
    $arr = $rs->getArray();
    $record = current($arr);

    $this->assertEqual(sizeof($arr), 1);

    $this->assertEqual($record['id'], $id);
    $this->assertEqual($record['uri'], '/test');
  }

  function testNewOuterUri()
  {
    $this->stats_uri->setReturnValue('_getHttpUri', 'http://wow.com/test');

    $id = $this->stats_uri->getUriId();

    $rs =& $this->db->select('stats_uri');
    $arr = $rs->getArray();
    $record = current($arr);

    $this->assertEqual(sizeof($arr), 1);

    $this->assertEqual($record['id'], $id);
    $this->assertEqual($record['uri'], 'http://wow.com/test');
  }

  function testSameUri()
  {
    $this->testNewOuterUri();
    $this->testNewOuterUri();
  }

  function testCleanOuterUri()
  {
    $this->assertEqual(
      'http://wow.com.bit/some/path/',
      $this->stats_uri->cleanUrl('http://wow.com.bit/some/path/?PHPSESSID=8988190381803003109&yo=1&haba&haba#not'));
  }

  function testCleanInnerUri()
  {
    $this->assertEqual(
      '/test',
      $this->stats_uri->cleanUrl('http://' . $_SERVER['HTTP_HOST'] . '/test?PHPSESSID=8988190381803003109&yo=1&haba&haba#not'));
  }

}

?>