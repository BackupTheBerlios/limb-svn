<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: StatsIpTest.class.php 1145 2005-03-05 13:09:48Z seregalimb $
*
***********************************************************************************/
require_once(LIMB_STATS_DIR . '/DAO/StatsIpsReportDAO.class.php');
require_once(LIMB_DIR . '/core/db/LimbDbPool.class.php');

class StatsIpsReportDAOTest extends LimbTestCase
{
  function StatsIpsReportDAOTest()
  {
    parent :: LimbTestCase('stats ips report DAO test');
  }

  var $db = null;
  var $conn = null;

  function setUp()
  {
    $this->conn =& LimbDbPool :: getConnection();
    $this->db =& new SimpleDb($this->conn);

    $this->_cleanUp();
  }

  function tearDown()
  {
    $this->_cleanUp();
  }

  function _cleanUp()
  {
    $this->db->delete('stats_hit');
  }

  function testFetch()
  {
    $time = mktime(6, 0, 0, 3, 6, 2005);
    $day = 24*60*60;

    $toolkit =& Limb :: toolkit();
    $db_table =& $toolkit->createDBTable('StatsHit');
    $db_table->insert(array('stats_referer_id' => 1,
                            'session_id' => 'any',
                            'stats_uri_id' => 1,
                            'action' => 'any',
                            'time' => $time1 = $time,
                            'ip' => $ip1 = Ip :: encode('192.168.0.1')));

    $db_table->insert(array('stats_referer_id' => 1,
                            'session_id' => 'any',
                            'stats_uri_id' => 1,
                            'action' => 'any',
                            'time' => $time2 = $time + $day,
                            'ip' => $ip1));

    $db_table->insert(array('stats_referer_id' => 1,
                            'session_id' => 'any',
                            'stats_uri_id' => 1,
                            'action' => 'any',
                            'time' => $time3 = $time + $day,
                            'ip' => $ip2 = Ip :: encode('192.168.0.2')));

    $db_table->insert(array('stats_referer_id' => 1,
                            'session_id' => 'any',
                            'stats_uri_id' => 1,
                            'action' => 'any',
                            'time' => $time4 = $time + 2 * $day,
                            'ip' => $ip1));

    $db_table->insert(array('stats_referer_id' => 1,
                            'session_id' => 'any',
                            'stats_uri_id' => 1,
                            'action' => 'any',
                            'time' => $time5 = $time + 3 * $day,
                            'ip' => $ip1));

    $request =& $toolkit->getRequest();
    $request->set('start_date', date('Y-m-d', $time2));
    $request->set('finish_date', date('Y-m-d', $time5));
    // Must find three records only

    $dao = new StatsIpsReportDAO();
    $rs =& $dao->fetch();

    $this->assertTrue(is_a($rs, 'StatsPercentageRecordSet'));
    $this->assertEqual($rs->getRowCount(), 2);

    $rs->rewind();

    $record =& $rs->current();
    $this->assertEqual($record->get('ip'), $ip1);

    $rs->next();
    $record =& $rs->current();
    $this->assertEqual($record->get('ip'), $ip2);
  }
}

?>