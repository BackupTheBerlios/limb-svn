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
require_once(LIMB_DIR . '/core/datasource/stats_referers_except_groups_list_datasource.class.php');
require_once(LIMB_DIR . '/core/model/stats/stats_referers_report.class.php');

Mock :: generatePartial('stats_referers_except_groups_list_datasource',
                        'stats_referers_except_groups_list_datasource_test_version',
                        array('_create_referers_report'));

Mock :: generate('stats_referers_report');

class stats_referers_except_groups_list_datasource_test extends LimbTestCase
{
  var $ds;
  var $report;
  var $locale;

  function setUp()
  {
    $this->report = new Mockstats_referers_report($this);

    $this->ds = new stats_referers_except_groups_list_datasource_test_version($this);
    $this->ds->setReturnReference('_create_referers_report', $this->report);
    $this->ds->stats_referers_list_datasource();

    $this->locale =& locale :: instance();
  }

  function tearDown()
  {
    $this->report->tally();
  }

  function test_get_dataset()
  {
    register_testing_ini('referers_groups.ini',
                         'groups[] = group1
                          groups[] = group2
                         '
                         );

    $this->report->expectOnce('fetch_count_except_groups', array(array('group1', 'group2')));
    $this->report->setReturnValue('fetch_count_except_groups', 3);

    $this->report->expectOnce('fetch_except_groups', array(array('group1', 'group2'), $limit = 0, $offset = 0));
    $this->report->setReturnValue('fetch_except_groups', array(array('hits' => 5),
                                                               array('hits' => 15),
                                                               array('hits' => 11)));


    $this->report->expectOnce('fetch_total_hits');
    $this->report->setReturnValue('fetch_total_hits', 1000);

    $this->assertEqual($this->ds->get_dataset($counter, array()),
                       new array_dataset(array(array('hits' => 5, 'percentage' => '0.5'),
                                               array('hits' => 15, 'percentage' => '1.5'),
                                               array('hits' => 11, 'percentage' => '1.1'))));

    $this->assertEqual($counter, 3);
  }
}

?>