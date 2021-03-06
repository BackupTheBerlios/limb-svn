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
require_once(LIMB_DIR . '/core/model/stats/stats_referers_report.class.php');

class stats_referers_report_test extends LimbTestCase
{
  var $report;
  var $db;

  function setUp()
  {
    $this->report = new stats_referers_report();
    $this->db =& db_factory :: instance();

    $this->_clean_up();
  }

  function tearDown()
  {
    $this->_clean_up();
  }

  function _clean_up()
  {
    $this->db->sql_delete('sys_stat_referer_url');
    $this->db->sql_delete('sys_stat_log');
  }

  function test_fetch_empty()
  {
    $this->assertEqual($this->report->fetch(), array());
  }

  function test_fetch()
  {
    $this->db->sql_insert('sys_stat_referer_url', array('id' => 1, 'referer_url' => 'http://host1'));
    $this->db->sql_insert('sys_stat_referer_url', array('id' => 2, 'referer_url' => 'http://host2'));

    $this->db->sql_insert('sys_stat_log', array('stat_referer_id' => 1));
    $this->db->sql_insert('sys_stat_log', array('stat_referer_id' => 2));
    $this->db->sql_insert('sys_stat_log', array('stat_referer_id' => 2));

    $expected = array(array('stat_referer_id' => 2, 'referer_url' => 'http://host2', 'hits' => 2),
                      array('stat_referer_id' => 1, 'referer_url' => 'http://host1', 'hits' => 1));

    $this->assertEqual($res = $this->report->fetch(),
                       $expected);

    $this->assertEqual($this->report->fetch_count(), 2);
    $this->assertEqual($this->report->fetch_total_hits(), 3);
  }

  function test_fetch_filtered()
  {
    $this->db->sql_insert('sys_stat_referer_url', array('id' => 1, 'referer_url' => 'http://host1'));
    $this->db->sql_insert('sys_stat_referer_url', array('id' => 2, 'referer_url' => 'http://host2'));

    $this->db->sql_insert('sys_stat_log', array('stat_referer_id' => 1, 'time' => 11));
    $this->db->sql_insert('sys_stat_log', array('stat_referer_id' => 2, 'time' => 99));
    $this->db->sql_insert('sys_stat_log', array('stat_referer_id' => 2, 'time' => 100));
    $this->db->sql_insert('sys_stat_log', array('stat_referer_id' => 2, 'time' => 1000));

    $expected = array(array('stat_referer_id' => 2, 'referer_url' => 'http://host2', 'hits' => 2),
                      array('stat_referer_id' => 1, 'referer_url' => 'http://host1', 'hits' => 1));

    $this->report->set_period_filter(new date(10), new date(101));
    $this->assertEqual($res = $this->report->fetch(),
                       $expected);

    $this->assertEqual($this->report->fetch_count(), 2);
    $this->assertEqual($this->report->fetch_total_hits(), 3);
  }

  function test_fetch_limited()
  {
    $this->db->sql_insert('sys_stat_referer_url', array('id' => 1, 'referer_url' => 'http://host1'));
    $this->db->sql_insert('sys_stat_referer_url', array('id' => 2, 'referer_url' => 'http://host2'));

    $this->db->sql_insert('sys_stat_log', array('stat_referer_id' => 1));
    $this->db->sql_insert('sys_stat_log', array('stat_referer_id' => 2));
    $this->db->sql_insert('sys_stat_log', array('stat_referer_id' => 2));

    $expected = array(array('stat_referer_id' => 1, 'referer_url' => 'http://host1', 'hits' => 1));

    $this->assertEqual($res = $this->report->fetch(1, 1),
                       $expected);

    $this->assertEqual($this->report->fetch_count(), 2);
    $this->assertEqual($this->report->fetch_total_hits(), 3);
  }

  function test_fetch_by_group()
  {
    $this->db->sql_insert('sys_stat_referer_url', array('id' => 1, 'referer_url' => 'http://host3'));
    $this->db->sql_insert('sys_stat_referer_url', array('id' => 2, 'referer_url' => 'http://host3/some/path'));
    $this->db->sql_insert('sys_stat_referer_url', array('id' => 3, 'referer_url' => 'http://host3?wow'));
    $this->db->sql_insert('sys_stat_referer_url', array('id' => 4, 'referer_url' => 'http://host4?cgi=1'));

    $this->db->sql_insert('sys_stat_log', array('stat_referer_id' => 1, 'time' => 5));
    $this->db->sql_insert('sys_stat_log', array('stat_referer_id' => 2, 'time' => 20));
    $this->db->sql_insert('sys_stat_log', array('stat_referer_id' => 2, 'time' => 21));
    $this->db->sql_insert('sys_stat_log', array('stat_referer_id' => 4, 'time' => 24));//not matches group
    $this->db->sql_insert('sys_stat_log', array('stat_referer_id' => 4, 'time' => 25));//not matches group
    $this->db->sql_insert('sys_stat_log', array('stat_referer_id' => 3, 'time' => 25));
    $this->db->sql_insert('sys_stat_log', array('stat_referer_id' => 3, 'time' => 26));
    $this->db->sql_insert('sys_stat_log', array('stat_referer_id' => 3, 'time' => 29));
    $this->db->sql_insert('sys_stat_log', array('stat_referer_id' => 3, 'time' => 30));
    $this->db->sql_insert('sys_stat_log', array('stat_referer_id' => 2, 'time' => 29));
    $this->db->sql_insert('sys_stat_log', array('stat_referer_id' => 3, 'time' => 31));//out of timespan

    $this->report->set_period_filter(new date(5), new date(30));

    $res = $this->report->fetch_by_group($group = '*host3*', $limit = 2, $offset = 1);

    $expected = array(array('stat_referer_id' => 2, 'referer_url' => 'http://host3/some/path', 'hits' => 3),
                      array('stat_referer_id' => 1, 'referer_url' => 'http://host3', 'hits' => 1));

    $this->assertEqual($res, $expected);

    $this->assertEqual($this->report->fetch_count_by_group($group), 3);
  }

  function test_fetch_summarized_by_groups()
  {
    $this->db->sql_insert('sys_stat_referer_url', array('id' => 1, 'referer_url' => 'http://host1'));
    $this->db->sql_insert('sys_stat_referer_url', array('id' => 2, 'referer_url' => 'http://host9'));
    $this->db->sql_insert('sys_stat_referer_url', array('id' => 3, 'referer_url' => 'http://host9/path'));
    $this->db->sql_insert('sys_stat_referer_url', array('id' => 4, 'referer_url' => 'http://host3/path/1'));

    $this->db->sql_insert('sys_stat_log', array('stat_referer_id' => 1));

    //*host9*
    $this->db->sql_insert('sys_stat_log', array('stat_referer_id' => 2));
    $this->db->sql_insert('sys_stat_log', array('stat_referer_id' => 2));
    $this->db->sql_insert('sys_stat_log', array('stat_referer_id' => 3));
    $this->db->sql_insert('sys_stat_log', array('stat_referer_id' => 3));
    $this->db->sql_insert('sys_stat_log', array('stat_referer_id' => 3));
    $this->db->sql_insert('sys_stat_log', array('stat_referer_id' => 3));
    $this->db->sql_insert('sys_stat_log', array('stat_referer_id' => 3));

    //*host3*
    $this->db->sql_insert('sys_stat_log', array('stat_referer_id' => 4));
    $this->db->sql_insert('sys_stat_log', array('stat_referer_id' => 4));
    $this->db->sql_insert('sys_stat_log', array('stat_referer_id' => 4));
    $this->db->sql_insert('sys_stat_log', array('stat_referer_id' => 4));
    $this->db->sql_insert('sys_stat_log', array('stat_referer_id' => 4));
    $this->db->sql_insert('sys_stat_log', array('stat_referer_id' => 4));

    $res = $this->report->fetch_summarized_by_groups($groups = array('*host9*', '*host3*'));

    $expected = array(array('referers_group' => '*host9*', 'hits' => 7),
                      array('referers_group' => '*host3*', 'hits' => 6));

    $this->assertEqual($res, $expected);

    $res = $this->report->fetch_except_groups($groups);

    $expected = array(array('stat_referer_id' => 1, 'referer_url' => 'http://host1', 'hits' => 1));

    $this->assertEqual($res, $expected);

    $this->assertEqual($this->report->fetch_total_hits(), 14);
  }

  function test_fetch_limited_except_groups()
  {
    $this->db->sql_insert('sys_stat_referer_url', array('id' => 1, 'referer_url' => 'http://host0'));
    $this->db->sql_insert('sys_stat_referer_url', array('id' => 2, 'referer_url' => 'http://host1'));
    $this->db->sql_insert('sys_stat_referer_url', array('id' => 3, 'referer_url' => 'http://host2'));
    $this->db->sql_insert('sys_stat_referer_url', array('id' => 4, 'referer_url' => 'http://host3'));

    //except *host3*
    $this->db->sql_insert('sys_stat_log', array('stat_referer_id' => 1));
    $this->db->sql_insert('sys_stat_log', array('stat_referer_id' => 2));
    $this->db->sql_insert('sys_stat_log', array('stat_referer_id' => 2));
    $this->db->sql_insert('sys_stat_log', array('stat_referer_id' => 2));
    $this->db->sql_insert('sys_stat_log', array('stat_referer_id' => 3));
    $this->db->sql_insert('sys_stat_log', array('stat_referer_id' => 3));
    $this->db->sql_insert('sys_stat_log', array('stat_referer_id' => 3));
    $this->db->sql_insert('sys_stat_log', array('stat_referer_id' => 3));

    //*host3*
    $this->db->sql_insert('sys_stat_log', array('stat_referer_id' => 4));
    $this->db->sql_insert('sys_stat_log', array('stat_referer_id' => 4));
    $this->db->sql_insert('sys_stat_log', array('stat_referer_id' => 4));
    $this->db->sql_insert('sys_stat_log', array('stat_referer_id' => 4));

    $res = $this->report->fetch_except_groups($groups = array('*host3*'), 2, 0);

    $expected = array(array('stat_referer_id' => 3, 'referer_url' => 'http://host2', 'hits' => 4),
                      array('stat_referer_id' => 2, 'referer_url' => 'http://host1', 'hits' => 3));

    $this->assertEqual($res, $expected);

    $this->assertEqual($this->report->fetch_count_except_groups($groups), 3);
  }
}

?>