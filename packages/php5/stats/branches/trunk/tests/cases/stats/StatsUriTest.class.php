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
require_once(dirname(__FILE__) . '/../../../StatsUri.class.php');
require_once(LIMB_DIR . '/class/lib/db/DbFactory.class.php');

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
  var $server = array();

  function setUp()
  {
    $this->server = $_SERVER;
    $_SERVER['HTTP_HOST'] = 'test';

    $this->db = DbFactory :: instance();

    $this->stats_uri = new StatsUriSelfTestVersion($this);
    $this->stats_uri->__construct();

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
    $this->db->sqlDelete('sys_stat_uri');
  }

  function testNewInnerUri()
  {
    $this->stats_uri->setReturnValue('_getHttpUri', 'http://' . $_SERVER['HTTP_HOST'] . '/test');

    $id = $this->stats_uri->getUriId();

    $this->db->sqlSelect('sys_stat_uri');
    $arr = $this->db->getArray();
    $record = current($arr);

    $this->assertEqual(sizeof($arr), 1);

    $this->assertEqual($record['id'], $id);
    $this->assertEqual($record['uri'], '/test');
  }

  function testNewOuterUri()
  {
    $this->stats_uri->setReturnValue('_getHttpUri', 'http://wow.com/test');

    $id = $this->stats_uri->getUriId();

    $this->db->sqlSelect('sys_stat_uri');
    $arr = $this->db->getArray();
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
      'http://wow.com.bit/some/path',
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