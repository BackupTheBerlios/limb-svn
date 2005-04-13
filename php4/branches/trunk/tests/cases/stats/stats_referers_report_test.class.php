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

  function test_fetch_by_groups()
  {
    $this->db->sql_insert('sys_stat_referer_url', array('id' => 1, 'referer_url' => 'http://host1'));
    $this->db->sql_insert('sys_stat_referer_url', array('id' => 2, 'referer_url' => 'http://host2'));
    $this->db->sql_insert('sys_stat_referer_url', array('id' => 3, 'referer_url' => 'http://host2/path'));

    $this->db->sql_insert('sys_stat_log', array('stat_referer_id' => 1));
    $this->db->sql_insert('sys_stat_log', array('stat_referer_id' => 2));
    $this->db->sql_insert('sys_stat_log', array('stat_referer_id' => 2));
    $this->db->sql_insert('sys_stat_log', array('stat_referer_id' => 3));

    $res = $this->report->fetch_by_groups(array('*host2*'));
    $expected = array(array('referers_group' => '*host2*', 'hits' => 3));
    $this->assertEqual($res, $expected);

    $res = $this->report->fetch_except_groups(array('*host2*'));
    $expected = array(array('stat_referer_id' => 1, 'referer_url' => 'http://host1', 'hits' => 1));
    $this->assertEqual($res, $expected);

    $this->assertEqual($this->report->fetch_total_hits(), 4);
  }

  function test_fetch_limited_except_groups()
  {
    $this->db->sql_insert('sys_stat_referer_url', array('id' => 1, 'referer_url' => 'http://host0'));
    $this->db->sql_insert('sys_stat_referer_url', array('id' => 2, 'referer_url' => 'http://host1'));
    $this->db->sql_insert('sys_stat_referer_url', array('id' => 3, 'referer_url' => 'http://host2'));

    $this->db->sql_insert('sys_stat_log', array('stat_referer_id' => 1));
    $this->db->sql_insert('sys_stat_log', array('stat_referer_id' => 2));
    $this->db->sql_insert('sys_stat_log', array('stat_referer_id' => 2));
    $this->db->sql_insert('sys_stat_log', array('stat_referer_id' => 2));
    $this->db->sql_insert('sys_stat_log', array('stat_referer_id' => 3));

    $res = $this->report->fetch_except_groups(array('*host2*'), 1, 0);
    $expected = array(array('stat_referer_id' => '2', 'referer_url' => 'http://host1', 'hits' => '3'));
    $this->assertEqual($res, $expected);

    $this->assertEqual($this->report->fetch_count_except_groups(array('*host2*')), 2);
  }
}

?>