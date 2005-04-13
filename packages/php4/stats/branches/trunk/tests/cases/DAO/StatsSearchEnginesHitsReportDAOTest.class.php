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
require_once(LIMB_STATS_DIR . '/DAO/StatsSearchEnginesHitsReportDAO.class.php');

class StatsSearchEnginesHitsReportDAOTest extends LimbTestCase
{
  function StatsSearchEnginesHitsReportDAOTest()
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
    $this->db->delete('stats_search_phrase');
  }

  function testFetch()
  {
    $time = mktime(6, 0, 0, 3, 6, 2005);
    $day = 24*60*60;

    $toolkit =& Limb :: toolkit();
    $db_table =& $toolkit->createDBTable('StatsSearchPhrase');
    $db_table =& $toolkit->createDBTable('StatsSearchPhrase');
    $db_table->insert(array('phrase' => $keyword1 = 'keyword1',
                            'engine' => $engine1 = 'google',
                            'time' => $time1 = $time));

    $db_table->insert(array('phrase' => $keyword1 = 'keyword1',
                            'engine' => $engine2 = 'yandex',
                            'time' => $time2 = $time + $day));

    $db_table->insert(array('phrase' => $keyword2 = 'keyword2',
                            'engine' => $engine2,
                            'time' => $time3 = $time + 2 * $day));

    $db_table->insert(array('phrase' => $keyword1,
                            'engine' => $engine2,
                            'time' => $time4 = $time + 3 * $day));

    $db_table->insert(array('phrase' => $keyword1 = 'keyword1',
                            'engine' => $engine1,
                            'time' => $time5 = $time + 4 * $day));

    $request =& $toolkit->getRequest();
    $request->set('start_date', date('Y-m-d', $time2));
    $request->set('finish_date', date('Y-m-d', $time5));
    // Must find three records only

    $dao = new StatsSearchEnginesHitsReportDAO();
    $rs =& $dao->fetch();

    $this->assertEqual($rs->getRowCount(), 4);
  }
}

?>