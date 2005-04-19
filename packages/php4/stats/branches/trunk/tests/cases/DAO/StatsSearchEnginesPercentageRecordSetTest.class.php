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
require_once(WACT_ROOT . '/iterator/pagedarraydataset.inc.php');
require_once(LIMB_STATS_DIR . '/dao/StatsSearchReportsPercentageRecordSet.class.php');
require_once(LIMB_STATS_DIR . '/dao/StatsSearchEnginesHitsReportDAO.class.php');
require_once(LIMB_DIR . '/core/LimbBaseToolkit.class.php');

Mock :: generatePartial('LimbBaseToolkit',
                        'MockLimbBaseToolkitStatsSearchReportsPercentageRecordsSetTestVestion',
                        array('createDAO'));

Mock :: generate('StatsSearchEnginesHitsReportDAO');
Mock :: generate('PagedArrayDataSet');

class StatsSearchEnginesPercentageRecordSetTest extends LimbTestCase
{
  var $tookit;
  var $hits_dao;

  function StatsSearchEnginesPercentageRecordSetTest()
  {
    parent :: LimbTestCase(__FILE__);
  }

  function setUp()
  {
    $this->hits_dao = new MockStatsSearchEnginesHitsReportDAO($this);

    $this->toolkit = new MockLimbBaseToolkitStatsSearchReportsPercentageRecordsSetTestVestion($this);
    $this->toolkit->setReturnReference('createDAO', $this->hits_dao, array('StatsSearchEnginesHitsReportDAO'));

    Limb :: registerToolkit($this->toolkit);
  }

  function tearDown()
  {
    $this->hits_dao->tally();
    $this->toolkit->tally();

    Limb :: restoreToolkit();
  }

  function testRecordSet()
  {
    $this->toolkit->expectOnce('createDAO', array('StatsSearchEnginesHitsReportDAO'));

    $hits_record_set = new MockPagedArrayDataSet($this);
    $this->hits_dao->expectOnce('fetch');
    $this->hits_dao->setReturnReference('fetch', $hits_record_set);

    $hits_record_set->setReturnValue('getTotalRowCount', 200);

    $records = array(array('hits' => 100),
                     array('hits' => 20),
                     array('hits' => 80));

    $base_record_set = new PagedArrayDataSet($records);

    $rs = new StatsSearchReportsPercentageRecordSet($base_record_set);
    $rs->rewind();

    $record =& $rs->current();
    $this->assertEqual($record->get('percentage'), 50);

    $rs->next();
    $record =& $rs->current();
    $this->assertEqual($record->get('percentage'), 10);

    $rs->next();
    $record =& $rs->current();
    $this->assertEqual($record->get('percentage'), 40);
  }

  function testRecordSetSomeProblemsWithHitsDAO()
  {
    $this->toolkit->expectOnce('createDAO', array('StatsSearchEnginesHitsReportDAO'));

    $hits_record_set = new MockPagedArrayDataSet($this);
    $this->hits_dao->expectOnce('fetch');
    $this->hits_dao->setReturnReference('fetch', $hits_record_set);

    $hits_record_set->setReturnValue('getTotalRowCount', 0);

    $records = array(array('hits' => 100),
                     array('hits' => 20),
                     array('hits' => 80));

    $base_record_set = new PagedArrayDataSet($records);

    $rs = new StatsSearchReportsPercentageRecordSet($base_record_set);
    $rs->rewind();

    $record =& $rs->current();
    $this->assertEqual($record->get('percentage'), 0);

    $rs->next();
    $record =& $rs->current();
    $this->assertEqual($record->get('percentage'), 0);

    $rs->next();
    $record =& $rs->current();
    $this->assertEqual($record->get('percentage'), 0);
  }
}

?>