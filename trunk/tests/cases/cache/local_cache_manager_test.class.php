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
require_once(LIMB_DIR . '/core/cache/local_cache_manager.class.php');
require_once(LIMB_DIR . '/core/request/request.class.php');
require_once(LIMB_DIR . '/core/lib/http/uri.class.php');
require_once(LIMB_DIR . '/core/lib/security/user.class.php');

Mock::generate('uri');
Mock::generate('user');

Mock::generatePartial(
  'local_cache_manager', 
  'local_cache_manager_test_version', 
  array('get_rules', '_set_matched_rule', '_get_matched_rule', '_get_user') 
);

class local_cache_manager_test extends UnitTestCase
{
  var $cache_manager;
  var $uri;
  var $user;
  
  function setUp()
  {
    $this->uri =& new Mockuri($this);
    $this->user =& new Mockuser($this);
    
    $this->cache_manager =& new local_cache_manager_test_version($this);
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
    $cache_manager = new local_cache_manager();
    
    register_testing_ini(
      'local_page_cache.ini',
      ' 
      [rule1]
       server_id = test1
       attributes[] = test1
       attributes[] = test2
      [rule2]
       server_id = wow
       groups[] = members
       groups[] = visitors
      [not_valid_rule]
       path_regex = /root/test3
      '
    );

    $this->assertEqual($cache_manager->get_rules(), 
      array(
        array('server_id' => 'test1', 'attributes' => array('test1', 'test2')),
        array('server_id' => 'wow', 'groups' => array('members', 'visitors'))
      )
    );
    
    clear_testing_ini();
  }
  
  function test_is_not_cacheable_no_uri()
  {
    $cache_manager = new local_cache_manager();
    
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
               'attributes' => array('action', 'pager'),
               'type' => 'allow'
              );
    $rule2 = array(
               'server_id' => 'last_docs',
               'attributes' => array('action', 'pager'),
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
               'attributes' => array('action', 'pager'),
              );

    $this->cache_manager->setReturnValue('get_rules',
      array($rule)    
    );
        
    $this->uri->setReturnValue('get_query_items', array('action' => 1, 'pager' => 1));

    $this->cache_manager->set_server_id('last_news');
    
    $this->assertTrue($this->cache_manager->is_cacheable());
    $this->cache_manager->expectOnce('_set_matched_rule', array($rule));
  }

  function test_is_cacheable()
  { 
    $rule = array(
               'server_id' => 'last_news',
               'attributes' => array('action', 'pager'),
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
               'attributes' => array('action', 'pager'),
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
               'attributes' => array('action', 'pager'),
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
               'attributes' => array('action', 'pager'),
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
               'attributes' => array('action', 'pager'),
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
               'attributes' => array('action', 'pager'),
              );

    $this->cache_manager->setReturnValue('get_rules',
      array($rule)    
    );
        
    $this->uri->setReturnValue('get_query_items', array('action' => 1));

    $this->cache_manager->set_server_id('last_news');
    
    $this->assertFalse($this->cache_manager->is_cacheable());
    $this->cache_manager->expectNever('_set_matched_rule');
  }

}

?>