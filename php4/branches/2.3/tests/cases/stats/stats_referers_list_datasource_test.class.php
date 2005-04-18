<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: stats_referer_test.class.php 950 2004-12-10 10:34:26Z pachanga $
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/datasource/stats_referers_list_datasource.class.php');
require_once(LIMB_DIR . '/core/model/stats/stats_referers_report.class.php');

Mock :: generatePartial('stats_referers_list_datasource',
                        'stats_referers_list_datasource_test_version',
                        array('_create_referers_report'));

Mock :: generate('stats_referers_report');

class stats_referers_list_datasource_test extends LimbTestCase
{
  var $ds;
  var $report;
  var $locale;

  function setUp()
  {
    $this->report = new Mockstats_referers_report($this);

    $this->ds = new stats_referers_list_datasource_test_version($this);
    $this->ds->setReturnReference('_create_referers_report', $this->report);

    $this->ds->stats_referers_list_datasource();

    $this->locale =& locale :: instance();
  }

  function tearDown()
  {
    $this->report->tally();
  }

  function test_get_empty_dataset_default_period()
  {
    $start_date = $this->_create_start_date();
    $finish_date = $this->_create_finish_date();

    $this->report->expectOnce('set_period_filter', array($start_date, $finish_date));

    $this->report->expectOnce('fetch_count');
    $this->report->setReturnValue('fetch_count', 0);

    $this->report->expectOnce('fetch', array($limit = 0, $offset = 0));
    $this->report->setReturnValue('fetch', array());

    $this->report->expectNever('fetch_total_hits');

    $this->assertEqual($this->ds->get_dataset($counter, array()),
                       new array_dataset(array()));

    $this->assertEqual($counter, 0);
  }

  function test_get_empty_dataset_for_period()
  {
    $this->_setup_request_period($start = '23/10/2003', $finish = '25/11/2005');

    $start_date = $this->_create_start_date($start);
    $finish_date = $this->_create_finish_date($finish);

    $this->report->expectOnce('set_period_filter', array($start_date, $finish_date));

    $this->report->expectOnce('fetch_count');
    $this->report->setReturnValue('fetch_count', 0);

    $this->report->expectOnce('fetch', array($limit = 0, $offset = 0));
    $this->report->setReturnValue('fetch', array());

    $this->report->expectNever('fetch_total_hits');

    $this->assertEqual($this->ds->get_dataset($counter, array()),
                       new array_dataset(array()));

    $this->assertEqual($counter, 0);
  }

  function test_get_dataset_for_period()
  {
    $this->_setup_request_period($start = '23/10/2003', $finish = '25/11/2005');

    $start_date = $this->_create_start_date($start);
    $finish_date = $this->_create_finish_date($finish);

    $this->report->expectOnce('set_period_filter', array($start_date, $finish_date));

    $this->report->expectOnce('fetch_count');
    $this->report->setReturnValue('fetch_count', 3);

    $this->report->expectOnce('fetch', array($limit = 1, $offset = 1));
    $this->report->setReturnValue('fetch', array(array('hits' => 5),
                                                 array('hits' => 15),
                                                 array('hits' => 11)));


    $this->report->expectOnce('fetch_total_hits');
    $this->report->setReturnValue('fetch_total_hits', 1000);

    $this->assertEqual($this->ds->get_dataset($counter, array('limit' => $limit, 'offset' => $offset)),
                       new array_dataset(array(array('hits' => 5, 'percentage' => '0.5'),
                                               array('hits' => 15, 'percentage' => '1.5'),
                                               array('hits' => 11, 'percentage' => '1.1'))));

    $this->assertEqual($counter, 3);
  }

  function _create_start_date($time_string = '')
  {
    $start_date = new date();
    $start_date->set_hour(0);
    $start_date->set_minute(0);
    $start_date->set_second(0);

    if($time_string)
      $start_date->set_by_string($time_string, $this->locale->get_short_date_time_format());

    return $start_date;
  }

  function _create_finish_date($time_string = '')
  {
    $finish_date = new date();
    if($time_string)
      $finish_date->set_by_string($time_string, $this->locale->get_short_date_time_format());

    $finish_date->set_hour(23);
    $finish_date->set_minute(59);
    $finish_date->set_second(59);

    return $finish_date;
  }

  function _setup_request_period($start_time, $finish_time)
  {
    $request = new request();
    $request->set_attribute('stats_start_date', $start_time);
    $request->set_attribute('stats_finish_date', $finish_time);

    $this->ds->set_request($request);
  }
}

?>