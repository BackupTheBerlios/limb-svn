<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/model/stats/stats_register.class.php');
require_once(LIMB_DIR . '/core/request/request.class.php');

Mock::generatePartial
(
  'stats_register',
  'stats_register_test_version',
  array(
    '_get_ip_register',
    '_get_counter_register',
    '_get_referer_register',
    '_get_search_phrase_register',
  )
);

Mock::generatePartial
(
  'stats_counter',
  'stats_counter_test_version2',
  array(
    'set_new_host',
    'update'
  )
);

Mock::generatePartial
(
  'stats_ip',
  'stats_ip_test_version',
  array(
    'get_client_ip',
    'is_new_host',
  )
);

Mock::generatePartial
(
  'stats_referer',
  'stats_referer_test_version',
  array(
    'get_referer_page_id'
  )
);

Mock::generatePartial
(
  'stats_search_phrase',
  'stats_search_phrase_test_version',
  array(
    'register'
  )
);

class stats_register_test extends LimbTestCase
{
  var $db = null;

  var $stats_ip = null;

  var $stats_referer = null;

  var $stats_register = null;

  function stats_register_test()
  {
    parent :: LimbTestCase();

    $this->db = db_factory :: instance();
  }

  function setUp()
  {
    $this->stats_counter = new stats_counter();

    $this->stats_ip = new stats_ip_test_version($this);
    $this->stats_ip->stats_ip();
    $this->stats_ip->setReturnValue('get_client_ip', ip :: encode_ip('127.0.0.1'));

    $this->stats_counter = new stats_counter_test_version2($this);
    $this->stats_counter->stats_counter();

    $this->stats_referer = new stats_referer_test_version($this);
    $this->stats_referer->stats_referer();
    $this->stats_referer->setReturnValue('get_referer_page_id', 10);

    $this->stats_search_phrase = new stats_search_phrase_test_version($this);
    $this->stats_search_phrase->stats_search_phrase();
    $this->stats_search_phrase->setReturnValue('register', true);

    $this->stats_register = new stats_register_test_version($this);
    $this->stats_register->stats_register();
    $this->stats_register->setReturnReference('_get_ip_register', $this->stats_ip);
    $this->stats_register->setReturnReference('_get_counter_register', $this->stats_counter);
    $this->stats_register->setReturnReference('_get_referer_register', $this->stats_referer);
    $this->stats_register->setReturnReference('_get_search_phrase_register', $this->stats_search_phrase);

    $this->_login_user(10, array());

    $this->_clean_up();
  }

  function tearDown()
  {
    $this->stats_ip->tally();

    $this->stats_referer->tally();

    $this->stats_search_phrase->tally();

    $this->stats_register->tally();

    $this->_clean_up();
  }

  function _clean_up()
  {
    $this->db->sql_delete('sys_stat_log');
    $this->db->sql_delete('sys_stat_ip');
    $this->db->sql_delete('sys_stat_referer_url');
    $this->db->sql_delete('sys_stat_counter');
    $this->db->sql_delete('sys_stat_day_counters');
  }

  function test_register_dont_track()
  {
    $this->stats_ip->expectNever('is_new_host');
    $this->stats_ip->expectNever('get_client_ip');

    $this->stats_counter->expectNever('set_new_host');
    $this->stats_counter->expectNever('update');

    $this->stats_referer->expectNever('get_referer_page_id');

    $this->stats_search_phrase->expectNever('register');

    $this->stats_register->register(0, '', REQUEST_STATUS_DONT_TRACK);
  }

  function test_register()
  {
    $date = new date();

    $this->stats_ip->expectOnce('is_new_host');
    $this->stats_ip->expectOnce('get_client_ip');

    $this->stats_counter->expectOnce('set_new_host');
    $this->stats_counter->expectOnce('update', array($date));

    $this->stats_referer->expectOnce('get_referer_page_id');

    $this->stats_search_phrase->expectOnce('register', array($date));

    $this->stats_register->set_register_time($date->get_stamp());

    $this->stats_register->register($node_id = 1, 'test', REQUEST_STATUS_SUCCESS);

    $this->_check_stats_register_record(
      $total_records = 1,
      $current_record = 1,
      $user_id = 10,
      $node_id,
      'test',
      REQUEST_STATUS_SUCCESS,
      $this->stats_register->get_register_time_stamp());
  }

  function test_clean_log()
  {
    $this->stats_register->set_register_time(time());
    $this->stats_register->register($node_id = 1, 'test', REQUEST_STATUS_SUCCESS);

    $this->stats_register->set_register_time(time() + 2*60*60*24);
    $this->stats_register->register($node_id = 1, 'test', REQUEST_STATUS_SUCCESS);

    $this->stats_register->set_register_time(time() + 3*60*60*24);
    $this->stats_register->register($node_id = 1, 'test', REQUEST_STATUS_SUCCESS);

    $this->stats_register->set_register_time(time() + 4*60*60*24);
    $this->stats_register->register($node_id = 1, 'test', REQUEST_STATUS_SUCCESS);

    $this->stats_register->set_register_time(time() + 5*60*60*24);
    $this->stats_register->register($node_id = 1, 'test', REQUEST_STATUS_SUCCESS);

    $this->stats_register->set_register_time(time() + 6*60*60*24);
    $this->stats_register->register($node_id = 1, 'test', REQUEST_STATUS_SUCCESS);

    $date = new date();
    $date->set_by_stamp(time() + 4*60*60*24 - 10);
    $this->stats_register->clean_until($date);

    $this->assertEqual(3, $this->stats_register->count_log_records());
  }

  function _check_stats_register_record($total_records, $current_record, $user_id, $node_id, $action, $status, $time)
  {
    $this->db->sql_select('sys_stat_log', '*', '', 'id');
    $arr = $this->db->get_array();

    $this->assertTrue(sizeof($arr), $total_records);
    reset($arr);

    for($i = 1; $i <= $current_record; $i++)
    {
      $record = current($arr);
      next($arr);
    }

    $this->assertEqual($record['user_id'], $user_id);
    $this->assertEqual($record['node_id'], $node_id);
    $this->assertEqual($record['action'], $action);
    $this->assertEqual($record['status'], $status);
    $this->assertEqual($record['time'], $time, 'log time is incorrect');
    $this->assertEqual($record['session_id'], session_id());
  }

  function _login_user($id, $groups)
  {
    $user =& user :: instance();

    $user->_set_id($id);
    $user->_set_groups($groups);
  }
}

?>