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
require_once(LIMB_STATS_DIR . '/DAO/StatsReferersReportDAO.class.php');
require_once(LIMB_DIR . '/core/db/LimbDbPool.class.php');

class StatsReferersReportDAOTest extends LimbTestCase
{
  function StatsReferersReportDAOTest()
  {
    parent :: LimbTestCase('stats referers report DAO test');
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
    $this->db->delete('stats_referer_url');
  }

  function testFetch()
  {
    $time = mktime(6, 0, 0, 3, 6, 2005);
    $day = 24*60*60;

    $toolkit =& Limb :: toolkit();

    $db_table =& $toolkit->createDBTable('StatsRefererUrl');
    $db_table->insert(array('id' => $referer_id1 = 1,
                            'referer_url' => $uri1 = 'http://test.com'));

    $db_table->insert(array('id' => $referer_id2 = 2,
                            'referer_url' => $uri2 = 'http://test2.com'));

    $db_table =& $toolkit->createDBTable('StatsHit');
    $db_table->insert(array('stats_referer_id' => $referer_id1,
                            'session_id' => 'any',
                            'stats_uri_id' => 1,
                            'action' => 'any',
                            'time' => $time1 = $time,
                            'ip' => $ip = Ip :: encode('192.168.0.1')));

    $db_table->insert(array('stats_referer_id' => $referer_id1,
                            'session_id' => 'any',
                            'stats_uri_id' => 1,
                            'action' => 'any',
                            'time' => $time2 = $time + $day,
                            'ip' => $ip));

    $db_table->insert(array('stats_referer_id' => $referer_id1,
                            'session_id' => 'any',
                            'stats_uri_id' => 1,
                            'action' => 'any',
                            'time' => $time3 = $time + $day,
                            'ip' => $ip));

    $db_table->insert(array('stats_referer_id' => $referer_id2,
                            'session_id' => 'any',
                            'stats_uri_id' => 1,
                            'action' => 'any',
                            'time' => $time4 = $time + 2 * $day,
                            'ip' => $ip));

    $db_table->insert(array('stats_referer_id' => $referer_id1,
                            'session_id' => 'any',
                            'stats_uri_id' => 1,
                            'action' => 'any',
                            'time' => $time5 = $time + 3 * $day,
                            'ip' => $ip));

    $request =& $toolkit->getRequest();
    $request->set('start_date', date('Y-m-d', $time2));
    $request->set('finish_date', date('Y-m-d', $time5));
    // Must find three records only

    $dao = new StatsReferersReportDAO();
    $rs =& $dao->fetch();

    $this->assertTrue(is_a($rs, 'StatsPercentageRecordSet'));
    $this->assertEqual($rs->getRowCount(), 2);

    $rs->rewind();

    $record =& $rs->current();
    $this->assertEqual($record->get('referer_url'), $uri1);

    $rs->next();
    $record =& $rs->current();
    $this->assertEqual($record->get('referer_url'), $uri2);
  }
}

?>