<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/cache/partial_page_cache_manager.class.php');
require_once(LIMB_DIR . '/core/request/request.class.php');
require_once(LIMB_DIR . '/core/lib/http/uri.class.php');
require_once(LIMB_DIR . '/core/lib/security/user.class.php');

Mock::generate('uri');
Mock::generate('user');

Mock::generatePartial(
  'partial_page_cache_manager',
  'partial_page_cache_manager_test_version',
  array('get_rules', '_set_matched_rule', '_get_matched_rule', '_get_user')
);

Mock::generatePartial(
  'partial_page_cache_manager',
  'partial_page_cache_manager_test_version2',
  array('is_cacheable', 'cache_exists', 'get_cache_id')
);


class partial_page_cache_manager_test extends LimbTestCase
{
  var $cache_manager;
  var $uri;
  var $user;

  function setUp()
  {
    $this->uri =& new Mockuri($this);
    $this->user =& new Mockuser($this);

    $this->cache_manager =& new partial_page_cache_manager_test_version($this);
    $this->cache_manager->set_uri($this->uri);

    $this->cache_manager->setReturnReference('_get_user', $this->user);
  }

  function tearDown()
  {
    $this->cache_manager->tally();
    $this->uri->tally();
    $this->user->tally();
  }

  function test_get_rules_from_ini()
  {
    $cache_manager = new partial_page_cache_manager();

    register_testing_ini(
      'partial_page_cache.ini',
      '
      [rule1]
       server_id = test1
       optional[] = test1
       required[] = test2
      [rule2]
       server_id = wow
       groups[] = members
       groups[] = visitors
      [not_valid_rule]
       bla-bla = bla-bla
      '
    );

    $this->assertEqual($cache_manager->get_rules(),
      array(
        array('server_id' => 'test1', 'optional' => array('test1'), 'required' => array('test2')),
        array('server_id' => 'wow', 'groups' => array('members', 'visitors'))
      )
    );

    clear_testing_ini();
  }

  function test_is_not_cacheable_no_uri()
  {
    $cache_manager = new partial_page_cache_manager();

    $this->assertFalse($cache_manager->is_cacheable());
  }

  function test_is_not_cacheable_if_no_rules()
  {
    $this->uri->setReturnValue('get_query_items', array('pager' => 1));

    $this->cache_manager->setReturnValue('get_rules',  array());
    $this->assertFalse($this->cache_manager->is_cacheable());
  }

  function test_is_not_cacheable_no_rule_for_server_id()
  {
    $this->uri->setReturnValue('get_query_items', array('action' => 1, 'pager' => 1));

    $rule1 = array(
               'server_id' => 'last_news',
               'optional' => array('action', 'pager'),
               'type' => 'allow'
              );
    $rule2 = array(
               'server_id' => 'last_docs',
               'optional' => array('action', 'pager'),
               'type' => 'allow'
              );

    $this->cache_manager->setReturnValue('get_rules',
      array($rule1, $rule2)
    );

    $this->cache_manager->set_server_id('no_such_server_id');

    $this->assertFalse($this->cache_manager->is_cacheable());
    $this->cache_manager->expectNever('_set_matched_rule');
  }

  function test_is_cacheable_allow_by_default()
  {
    $rule = array(
               'server_id' => 'last_news',
               'optional' => array('action', 'pager'),
              );

    $this->cache_manager->setReturnValue('get_rules',
      array($rule)
    );

    $this->uri->setReturnValue('get_query_items', array('action' => 1, 'pager' => 1));

    $this->cache_manager->set_server_id('last_news');

    $this->assertTrue($this->cache_manager->is_cacheable());
    $this->cache_manager->expectOnce('_set_matched_rule', array($rule));
  }

  function test_is_cacheable_optional_attributes()
  {
    $rule = array(
               'server_id' => 'last_news',
               'optional' => array('action', 'pager'),
               'type' => 'allow'
              );

    $this->cache_manager->setReturnValue('get_rules',
      array($rule)
    );

    $this->uri->setReturnValue('get_query_items', array('action' => 1, 'pager' => 1));

    $this->cache_manager->set_server_id('last_news');

    $this->assertTrue($this->cache_manager->is_cacheable());
    $this->cache_manager->expectOnce('_set_matched_rule', array($rule));
  }

  function test_is_cacheable_required_attributes()
  {
    $rule = array(
               'server_id' => 'last_news',
               'required' => array('action', 'pager'),
               'type' => 'allow'
              );

    $this->cache_manager->setReturnValue('get_rules',
      array($rule)
    );

    $this->uri->setReturnValue('get_query_items', array('action' => 1, 'pager' => 1));

    $this->cache_manager->set_server_id('last_news');

    $this->assertTrue($this->cache_manager->is_cacheable());
    $this->cache_manager->expectOnce('_set_matched_rule', array($rule));
  }

  function test_is_cacheable_groups_match()
  {
    $this->uri->setReturnValue('get_query_items', array('action' => 1, 'pager' => 1));
    $this->uri->setReturnValue('get_path', '/root/test');

    $this->user->setReturnValueAt(0, 'is_in_groups', true, array(array('members', 'visitors')));

    $rule = array(
               'server_id' => 'last_news',
               'optional' => array('action', 'pager'),
               'type' => 'allow',
               'groups' => array('members', 'visitors')
              );

    $this->cache_manager->setReturnValue('get_rules',
      array($rule)
    );

    $this->cache_manager->set_server_id('last_news');

    $this->assertTrue($this->cache_manager->is_cacheable());
    $this->cache_manager->expectOnce('_set_matched_rule', array($rule));
  }

  function test_is_not_cacheable_groups_dont_match()
  {
    $this->uri->setReturnValue('get_query_items', array('action' => 1, 'pager' => 1));
    $this->uri->setReturnValue('get_path', '/root/test');

    $this->user->setReturnValueAt(0, 'is_in_groups', false, array(array('members', 'visitors')));

    $rule = array(
               'server_id' => 'last_news',
               'optional' => array('action', 'pager'),
               'type' => 'allow',
               'groups' => array('members', 'visitors')
              );

    $this->cache_manager->setReturnValue('get_rules',
      array($rule)
    );

    $this->cache_manager->set_server_id('last_news');

    $this->assertFalse($this->cache_manager->is_cacheable());
    $this->cache_manager->expectNever('_set_matched_rule');
  }

  function test_is_cacheable_extra_attributes()
  {
    $rule = array(
               'server_id' => 'last_news',
               'optional' => array('action', 'pager'),
              );

    $this->cache_manager->setReturnValue('get_rules',
      array($rule)
    );

    $this->uri->setReturnValue('get_query_items', array('action' => 1, 'pager' => 1, 'extra' => 1));

    $this->cache_manager->set_server_id('last_news');

    $this->assertTrue($this->cache_manager->is_cacheable());
    $this->cache_manager->expectOnce('_set_matched_rule', array($rule));
  }

  function test_is_not_cacheable_deny()
  {
    $rule = array(
               'server_id' => 'last_news',
               'optional' => array('action', 'pager'),
               'type' => 'deny'
              );

    $this->cache_manager->setReturnValue('get_rules',
      array($rule)
    );

    $this->uri->setReturnValue('get_query_items', array('action' => 1, 'pager' => 1));

    $this->cache_manager->set_server_id('last_news');

    $this->assertFalse($this->cache_manager->is_cacheable());
    $this->cache_manager->expectNever('_set_matched_rule');
  }

  function test_is_not_cacheable_attributes_dont_match()
  {
    $rule = array(
               'server_id' => 'last_news',
               'required' => array('action', 'pager'),
              );

    $this->cache_manager->setReturnValue('get_rules',
      array($rule)
    );

    $this->uri->setReturnValue('get_query_items', array('action' => 1));

    $this->cache_manager->set_server_id('last_news');

    $this->assertFalse($this->cache_manager->is_cacheable());
    $this->cache_manager->expectNever('_set_matched_rule');
  }

  function test_get_cache_id()
  {
    $rule = array('optional' => array('action'), 'server_id' => 'last_news');

    $this->uri->setReturnValue('get_query_items', $query_items = array('action' => 1));

    $this->cache_manager->setReturnValue('_get_matched_rule', $rule);

    $this->assertEqual(
      $this->cache_manager->get_cache_id(),
      'p_' . md5( 'last_news' . serialize($query_items))
    );
  }

  function test_get_cache_id_query_items_sorted()
  {
    $rule = array('server_id' => 'test', 'optional' => array('pager', 'prop'), 'required' => array('action'));

    $this->uri->setReturnValue('get_query_items', $query_items = array('action' => 1, 'pager' => 2));

    $this->cache_manager->setReturnValue('_get_matched_rule', $rule);

    $cache_id = $this->cache_manager->get_cache_id();

    $this->uri->setReturnValue('get_query_items', $query_items = array('junky' => 1, 'pager' => 2, 'action' => 1));
    $this->uri->setReturnValue('get_path', '/root/test');

    $this->cache_manager->setReturnValue('_get_matched_rule', $rule);

    $this->assertEqual(
      $this->cache_manager->get_cache_id(),
      $cache_id
    );
  }

  function test_get_cache_id_use_path()
  {
    $rule = array('optional' => array('action'), 'server_id' => 'last_news', 'use_path' => true);

    $this->uri->setReturnValue('get_query_items', $query_items = array('action' => 1));
    $this->uri->setReturnValue('get_path', '/root/test');

    $this->cache_manager->setReturnValue('_get_matched_rule', $rule);

    $this->assertEqual(
      $this->cache_manager->get_cache_id(),
      'p_' . md5('last_news' . '/root/test' . serialize($query_items))
    );
  }

  function test_get_cache_id_extra_attributes()
  {
    $rule = array('optional' => array('action'), 'server_id' => 'last_news');

    $this->uri->setReturnValue('get_query_items', $query_items = array('action' => 1, 'extra' => 1));

    $this->cache_manager->setReturnValue('_get_matched_rule', $rule);

    $this->assertEqual(
      $this->cache_manager->get_cache_id(),
      'p_' . md5( 'last_news' . serialize(array('action' => 1)))
    );
  }

  function test_get_cache_id_no_attributes()
  {
    $rule = array('server_id' => 'last_news');

    $this->uri->setReturnValue('get_query_items', $query_items = array('action' => 1));

    $this->cache_manager->setReturnValue('_get_matched_rule', $rule);

    $this->assertEqual(
      $this->cache_manager->get_cache_id(),
      'p_' . md5('last_news' . serialize(array()))
    );
  }

  function test_cache_doesnt_exist_no_uri()
  {
    $cache_manager = new partial_page_cache_manager();
    $this->assertFalse($cache_manager->cache_exists());
  }

  function test_cache_exists()
  {
    $this->_write_cache($server_id = 'last_news', $attributes = array('action' => 1));

    $this->uri->setReturnValue('get_query_items', $attributes);
    $this->cache_manager->setReturnValue('_get_matched_rule', array('server_id' => $server_id, 'optional' => array('action')));

    $this->assertTrue($this->cache_manager->cache_exists());

    $this->_clean_cache($server_id, $attributes);
  }

  function test_cache_exists_use_path()
  {
    $this->_write_cache($server_id = 'last_news', $attributes = array('action' => 1), 'test', $path = '/root/test');

    $this->uri->setReturnValue('get_query_items', $attributes);
    $this->uri->setReturnValue('get_path', '/root/test');
    $this->cache_manager->setReturnValue('_get_matched_rule', array('server_id' => $server_id, 'use_path' => true, 'optional' => array('action')));

    $this->assertTrue($this->cache_manager->cache_exists());

    $this->_clean_cache($server_id, $attributes, $path);
  }

  function test_get()
  {
    $cache_manager =& new partial_page_cache_manager_test_version2($this);
    $cache_manager->set_uri($this->uri);

    $this->_write_simple_cache($cache_id = 1, $contents = 'test-test');

    $cache_manager->setReturnValue('is_cacheable', true);
    $cache_manager->setReturnValue('cache_exists', true);
    $cache_manager->setReturnValue('get_cache_id', $cache_id);

    $this->assertEqual($cache_manager->get(), $contents);

    $cache_manager->tally();

    $this->_clean_simple_cache($cache_id);
  }

  function test_write_false_no_uri()
  {
    $cache_manager = new partial_page_cache_manager();
    $this->assertFalse($cache_manager->write($content = 'test'));
  }

  function test_write()
  {
    $cache_manager =& new partial_page_cache_manager_test_version2($this);
    $cache_manager->set_uri($this->uri);

    $contents = 'test-test';

    $cache_manager->setReturnValue('get_cache_id', $cache_id = 1);

    $this->assertTrue($cache_manager->write($contents));
    $this->assertEqual($this->_read_simple_cache($cache_id), $contents);

    $cache_manager->tally();

    $this->_clean_simple_cache($cache_id);
  }

  function test_overwrite()
  {
    $this->_write_simple_cache($cache_id = 1, $contents = 'test-overwrite');

    $cache_manager =& new partial_page_cache_manager_test_version2($this);
    $cache_manager->set_uri($this->uri);

    $contents = 'test-test';

    $cache_manager->setReturnValue('get_cache_id', $cache_id);

    $this->assertTrue($cache_manager->write($contents));
    $this->assertEqual($this->_read_simple_cache($cache_id), $contents);

    $this->_clean_simple_cache($cache_id);

    $cache_manager->tally();
  }

  function test_flush()
  {
    $this->_write_simple_cache('p_test1', $content1 = 'test-content1');
    $this->_write_simple_cache('p_test2', $content2 ='test-content2');
    $this->_write_simple_cache('not_page_file', $content3 ='test-content3');

    $cache_manager =& new partial_page_cache_manager();
    $cache_manager->flush();

    $files = fs :: find(PAGE_CACHE_DIR);

    $this->assertEqual(sizeof($files), 1);

    $file = reset($files);
    $this->assertEqual(fs :: clean_path($file), fs :: clean_path(PAGE_CACHE_DIR . fs :: separator() . 'not_page_file'));

    $this->_clean_simple_cache('not_page_file');
  }

  function _write_cache($server_id, $attributes, $contents='test', $path = '')
  {
    if ($path)
      $file_id = 'p_' . md5($server_id . $path . serialize($attributes));
    else
      $file_id = 'p_' . md5($server_id . serialize($attributes));

    $this->_write_simple_cache($file_id, $contents);
  }

  function _write_simple_cache($file_id, $contents)
  {
    fs :: mkdir(PAGE_CACHE_DIR);
    $f = fopen(PAGE_CACHE_DIR . $file_id, 'w');
    fwrite($f, $contents);
    fclose($f);
  }

  function _read_simple_cache($file_id)
  {
    return file_get_contents(PAGE_CACHE_DIR . $file_id);
  }

  function _clean_simple_cache($file_id)
  {
    if(file_exists(PAGE_CACHE_DIR . $file_id))
      unlink(PAGE_CACHE_DIR . $file_id);
  }

  function _clean_cache($server_id, $attributes, $path = '')
  {
    if ($path)
      $file_id = 'p_' . md5($server_id . $path . serialize($attributes));
    else
      $file_id = 'p_' . md5($server_id . serialize($attributes));

    $this->_clean_simple_cache($file_id);
  }
}

?>