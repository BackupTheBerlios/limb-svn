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
require_once(LIMB_STATS_DIR . '/DAO/StatsCountersReportDAO.class.php');

class StatsCountersReportDAOTest extends LimbTestCase
{
  function StatsCountersReportDAOTest()
  {
    parent :: LimbTestCase('stats counters report DAO test');
  }

  var $db = null;
  var $conn = null;

  function setUp()
  {
    $toolkit =& Limb :: toolkit();
    $this->conn =& $toolkit->getDbConnection();
    $this->db =& new SimpleDb($this->conn);

    $this->_cleanUp();
  }

  function tearDown()
  {
    $this->_cleanUp();
  }

  function _cleanUp()
  {
    $this->db->delete('stats_day_counters');
  }

  function testFetch()
  {
    $time = mktime(6, 0, 0, 3, 6, 2005);
    $day = 24*60*60;

    $toolkit =& Limb :: toolkit();
    $db_table =& $toolkit->createDBTable('StatsDayCounters');
    $db_table->insert(array('time' => $time1 = $time,
                             'hits' => $hits1 = 124,
                             'hosts' => $hosts1 = 100,
                             'home_hits' => $home_hits1 = 20,
                             'audience' => $audience1 = 34));

    $db_table->insert(array('time' => $time2 = $time + $day,
                             'hits' => $hits1 = 124,
                             'hosts' => $hosts1 = 100,
                             'home_hits' => $home_hits1 = 20,
                             'audience' => $audience1 = 34));

    $db_table->insert(array('time' => $time3 = $time + 2 * $day,
                             'hits' => $hits1 = 124,
                             'hosts' => $hosts1 = 100,
                             'home_hits' => $home_hits1 = 20,
                             'audience' => $audience1 = 34));

    $db_table->insert(array('time' => $time4 = $time + 3 * $day,
                             'hits' => $hits1 = 124,
                             'hosts' => $hosts1 = 100,
                             'home_hits' => $home_hits1 = 20,
                             'audience' => $audience1 = 34));

    $db_table->insert(array('time' => $time5 = $time + 4 * $day,
                             'hits' => $hits1 = 124,
                             'hosts' => $hosts1 = 100,
                             'home_hits' => $home_hits1 = 20,
                             'audience' => $audience1 = 34));

    $request =& $toolkit->getRequest();
    $request->set('start_date', date('Y-m-d', $time2));
    $request->set('finish_date', date('Y-m-d', $time4));
    // Must find three records only

    $dao = new StatsCountersReportDAO();
    $rs =& $dao->fetch();

    $this->assertTrue(is_a($rs, 'StatsCountersReportRecordSet'));
    $this->assertEqual($rs->getRowCount(), 3);
  }
}

?>