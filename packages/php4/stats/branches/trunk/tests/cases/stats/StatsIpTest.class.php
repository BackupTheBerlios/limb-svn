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
require_once(dirname(__FILE__) . '/../../../StatsIp.class.php');
require_once(LIMB_DIR . '/core/http/Ip.class.php');
require_once(LIMB_DIR . '/core/db/LimbDbPool.class.php');

Mock :: generatePartial
(
  'StatsIp',
  'StatsIpSelfTestVersion',
  array(
    'getClientIp'
  )
);

class StatsIpTest extends LimbTestCase
{
  var $stats_ip = null;
  var $db = null;

  function statsIpTest()
  {
    parent :: LimbTestCase('stats ip test');

    $this->db =& LimbDbPool :: getConnection();
  }

  function setUp()
  {
    $this->stats_ip = new StatsIpSelfTestVersion($this);
    $this->stats_ip->StatsIp();

    $this->_cleanUp();
  }

  function tearDown()
  {
    $this->stats_ip->tally();

    $this->_cleanUp();
  }

  function _cleanUp()
  {
    $this->db->sqlDelete('sys_stat_ip');
  }

  function testNewHost()
  {
    $date = new Date();
    $ip = Ip :: encodeIp('192.168.0.5');
    $this->stats_ip->setReturnValue('getClientIp', $ip);

    $this->assertTrue($this->stats_ip->isNewHost($date));

    $this->_checkStatsIpRecord($total_records = 1, $ip, $date);
  }

  function testSecondNewHost()
  {
    $date = new Date();
    $ip = Ip :: encodeIp('192.168.0.5');
    $this->stats_ip->setReturnValue('getClientIp', $ip);

    $date = new Date();
    $ip = Ip :: encodeIp('192.168.0.6');
    $this->stats_ip->setReturnValueAt(1, 'getClientIp', $ip);

    $this->assertTrue($this->stats_ip->isNewHost($date));
  }

  function testSameHostNewDay()
  {
    $date = new Date();
    $ip = Ip :: encodeIp('192.168.0.5');
    $this->stats_ip->setReturnValue('getClientIp', $ip);

    $this->stats_ip->isNewHost($date);

    $date = new Date();
    $date->setByDays($date->dateToDays() + 1);
    $this->stats_ip->setReturnValueAt(1, 'getClientIp', $ip);

    $this->assertTrue($this->stats_ip->isNewHost($date));

    $this->_checkStatsIpRecord($total_records = 1, $ip, $date);
  }

  function testSameHostWrongDay()
  {
    $date1 = new Date();
    $ip = Ip :: encodeIp('192.168.0.5');
    $this->stats_ip->setReturnValue('getClientIp', $ip);

    $this->stats_ip->isNewHost($date1);

    $date2 = new Date();
    $date2->setByDays($date1->dateToDays() - 2);
    $this->stats_ip->setReturnValueAt(1, 'getClientIp', $ip);

    $this->assertFalse($this->stats_ip->isNewHost($date2));

    $this->_checkStatsIpRecord($total_records = 1, $ip, $date1);
  }

  function _checkStatsIpRecord($total_records, $ip, $date)
  {
    $this->db->sqlSelect('sys_stat_ip');
    $arr = $this->db->getArray('id');

    $this->assertTrue(sizeof($arr), $total_records, 'ip count is wrong');
    $this->assertTrue(isset($arr[$ip]));
    $this->assertEqual($arr[$ip]['time'], $date->getStamp(), 'ip time is incorrect');
  }
}

?>