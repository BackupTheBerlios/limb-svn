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
require_once(LIMB_STATS_DIR . '/registers/StatsIp.class.php');

class StatsIpTest extends LimbTestCase
{
  var $stats_ip = null;
  var $db = null;
  var $conn = null;
  var $sys = null;
  var $toolkit = null;

  function statsIpTest()
  {
    parent :: LimbTestCase('stats ip test');

    $toolkit =& Limb :: toolkit();
    $this->conn =& $toolkit->getDbConnection();
    $this->db =& new SimpleDb($this->conn);
  }

  function setUp()
  {
    $this->stats_ip = new StatsIp();

    $this->_cleanUp();
  }

  function tearDown()
  {
    $this->_cleanUp();
  }

  function _cleanUp()
  {
    $this->db->delete('stats_ip');
  }

  function testNewHost()
  {
    $this->assertTrue($this->stats_ip->isNewToday($ip = '192.168.0.5', $time = time()));

    $this->_checkStatsIpRecord($total_records = 1, $ip, $time);
  }

  function testSecondNewHost()
  {
    $this->assertTrue($this->stats_ip->isNewToday($ip = '192.168.0.5', $time = time()));

    $this->assertTrue($this->stats_ip->isNewToday($ip2 = '192.168.0.6', $time));
  }

  function testSameHostNewDay()
  {
    $this->stats_ip->isNewToday($ip = '192.168.0.5', $time = time());

    $one_day = 24*60*60;
    $this->assertTrue($this->stats_ip->isNewToday($ip, $time + $one_day));

    $this->_checkStatsIpRecord($total_records = 1, $ip, $time + $one_day);
  }

  function testSameHostWrongDay()
  {
    $this->stats_ip->isNewToday($ip = '192.168.0.5', $time = time());

    $two_days = 2*24*60*60;
    $this->assertFalse($this->stats_ip->isNewToday($ip, $time - $two_days));

    $this->_checkStatsIpRecord($total_records = 1, $ip, $time);
  }

  function _checkStatsIpRecord($total_records, $ip, $time)
  {
    $encoded_ip = Ip :: encode($ip);

    $rs =& $this->db->select('stats_ip');
    $arr = $rs->getArray('id');

    $this->assertTrue(sizeof($arr), $total_records, 'ip count is wrong');
    $this->assertTrue(isset($arr[$encoded_ip]));
    $this->assertEqual($arr[$encoded_ip]['time'], $time, 'ip time is incorrect');
  }
}

?>