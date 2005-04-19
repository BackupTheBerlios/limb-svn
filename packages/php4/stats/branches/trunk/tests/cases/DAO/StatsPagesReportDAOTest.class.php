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
require_once(LIMB_STATS_DIR . '/dao/StatsPagesReportDAO.class.php');

class StatsPagesReportDAOTest extends LimbTestCase
{
  function StatsPagesReportDAOTest()
  {
    parent :: LimbTestCase(__FILE__);
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
    $this->db->delete('stats_hit');
    $this->db->delete('stats_uri');
  }

  function testFetch()
  {
    $time = mktime(6, 0, 0, 3, 6, 2005);
    $day = 24*60*60;

    $toolkit =& Limb :: toolkit();

    $db_table =& $toolkit->createDBTable('StatsUri');
    $db_table->insert(array('id' => $uri_id1 = 1,
                            'uri' => $uri1 = '/news'));

    $db_table->insert(array('id' => $uri_id2 = 2,
                            'uri' => $uri2 = '/articles'));

    $db_table =& $toolkit->createDBTable('StatsHit');
    $db_table->insert(array('stats_referer_id' => 1,
                            'session_id' => 'any',
                            'stats_uri_id' => $uri_id1,
                            'action' => 'any',
                            'time' => $time1 = $time,
                            'ip' => $ip = Ip :: encode('192.168.0.1')));

    $db_table->insert(array('stats_referer_id' => 1,
                            'session_id' => 'any',
                            'stats_uri_id' => $uri_id1,
                            'action' => 'any',
                            'time' => $time2 = $time + $day,
                            'ip' => $ip));

    $db_table->insert(array('stats_referer_id' => 1,
                            'session_id' => 'any',
                            'stats_uri_id' => $uri_id1,
                            'action' => 'any',
                            'time' => $time3 = $time + $day,
                            'ip' => $ip));

    $db_table->insert(array('stats_referer_id' => 1,
                            'session_id' => 'any',
                            'stats_uri_id' => $uri_id2,
                            'action' => 'any',
                            'time' => $time4 = $time + 2 * $day,
                            'ip' => $ip));

    $db_table->insert(array('stats_referer_id' => 1,
                            'session_id' => 'any',
                            'stats_uri_id' => $uri_id1,
                            'action' => 'any',
                            'time' => $time5 = $time + 3 * $day,
                            'ip' => $ip));

    $request =& $toolkit->getRequest();
    $request->set('start_date', date('Y-m-d', $time2));
    $request->set('finish_date', date('Y-m-d', $time5));
    // Must find three records only

    $dao = new StatsPagesReportDAO();
    $rs =& $dao->fetch();

    $this->assertTrue(is_a($rs, 'StatsPercentageRecordSet'));
    $this->assertEqual($rs->getRowCount(), 2);

    $rs->rewind();

    $record =& $rs->current();
    $this->assertEqual($record->get('uri'), $uri1);

    $rs->next();
    $record =& $rs->current();
    $this->assertEqual($record->get('uri'), $uri2);
  }
}

?>