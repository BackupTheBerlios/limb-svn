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
require_once(LIMB_STATS_DIR . '/DAO/StatsCountersReportRecordSet.class.php');

Mock :: generatePartial('PagedArrayDataSet',
                        'MockPagedArrayDataSetStatsCountersReportRecordSetTestVersion',
                        array('getTotalRowCount'));

class StatsCountersReportRecordSetTest extends LimbTestCase
{
  function StatsCountersReportRecordSetTest()
  {
    parent :: LimbTestCase('stats counters report record set test');
  }

  function testRecordSet()
  {
    $base_record_set = new MockPagedArrayDataSetStatsCountersReportRecordSetTestVersion($this);
    $base_record_set->setReturnValue('getTotalRowCount', $total = 300);

    $time = mktime(6, 0, 0, 3, 6, 2005);
    $sunday_time = mktime(6, 0, 0, 3, 6, 2005);

    $records = array(array('hosts' => 1,   'hits' => 1000,'home_hits' => 1,   'audience' => 1, 'time' => $time),
                     array('hosts' => 1,   'hits' => 1,   'home_hits' => 1000,'audience' => 1, 'time' => $time + 2),
                     array('hosts' => 100, 'hits' => 1,   'home_hits' => 1,   'audience' => 1, 'time' => $sunday_time),
                     array('hosts' => 1,   'hits' => 1,   'home_hits' => 1,   'audience' => 100, 'time' => $time + 4));

    $base_record_set->PagedArrayDataSet($records);

    $rs = new StatsCountersReportRecordSet($base_record_set);
    $this->assertEqual($rs->getTotalRowCount(), $total);

    $rs->rewind();

    $record =& $rs->current();
    $this->assertEqual($record->get('max_hits'), 1);

    $rs->next();
    $record =& $rs->current();
    $this->assertEqual($record->get('max_home_hits'), 1);

    $rs->next();
    $record =& $rs->current();
    $this->assertEqual($record->get('max_hosts'), 1);
    $this->assertEqual($record->get('new_week'), 1);

    $rs->next();
    $record =& $rs->current();
    $this->assertEqual($record->get('max_audience'), 1);
  }
}

?>