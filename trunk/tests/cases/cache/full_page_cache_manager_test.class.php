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
require_once(LIMB_DIR . '/core/cache/full_page_cache_manager.class.php');
require_once(LIMB_DIR . '/core/request/request.class.php');
require_once(LIMB_DIR . '/core/lib/http/uri.class.php');
require_once(LIMB_DIR . '/core/lib/security/user.class.php');

Mock::generate('uri');
Mock::generate('user');

Mock::generatePartial(
  'full_page_cache_manager', 
  'full_page_cache_manager_test_version', 
  array('get_rules', '_set_matched_rule', '_get_matched_rule', '_get_user')
);

Mock::generatePartial(
  'full_page_cache_manager', 
  'full_page_cache_manager_test_version2', 
  array('is_cacheable', 'cache_exists', 'get_cache_id')
);

class full_page_cache_manager_test extends UnitTestCase
{
  var $cache_manager;
  var $uri;
  var $user;
  
  function setUp()
  {
    $this->uri =& new Mockuri($this);
    $this->user =& new Mockuser($this);
    
    $this->cache_manager =& new full_page_cache_manager_test_version($this);
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
    $cache_manager = new full_page_cache_manager();
    
    register_testing_ini(
      'full_page_cache.ini',
      ' 
      [rule1]
       path_regex = /root/test1
       optional[] = test1
       optional[] = test2
      [rule2]
       path_regex = /root/test2
       groups[] = members
       groups[] = visitors
      [not_valid_rule]
       path_regex = /root/test3
      '
    );

    $this->assertEqual($cache_manager->get_rules(), 
      array(
        array('path_regex' => '/root/test1', 'optional' => array('test1', 'test2')),
        array('path_regex' => '/root/test2', 'groups' => array('members', 'visitors'))
      )
    );
    
    clear_testing_ini();
  } 
  
  function test_is_not_cacheable_no_uri()
  {
    $cache_manager = new full_page_cache_manager();
    
    $this->assertFalse($cache_manager->is_cacheable());
  }

  function test_is_cacheable_required_attributes()
  {         
    $this->uri->setReturnValue('get_query_items', array('action' => 1, 'pager' => 1));
    $this->uri->setReturnValue('get_path', '/root/test');
    
    $rule = array(
               'path_regex' => '/^\/root\/test.*$/',
               'required' => array('action', 'pager'),
               'type' => 'allow'
              );

    $this->cache_manager->setReturnValue('get_rules',
      array($rule)    
    );
    
    $this->assertTrue($this->cache_manager->is_cacheable());
    $this->cache_manager->expectOnce('_set_matched_rule', array($rule));
  }
    
  function test_is_cacheable_optional_attributes()
  {         
    $this->uri->setReturnValue('get_query_items', array('action' => 1));
    $this->uri->setReturnValue('get_path', '/root/test');
    
    $rule = array(
               'path_regex' => '/^\/root\/test.*$/',
               'optional' => array('action', 'pager'),
               'type' => 'allow'
              );

    $this->cache_manager->setReturnValue('get_rules',
      array($rule)    
    );
    
    $this->assertTrue($this->cache_manager->is_cacheable());
    $this->cache_manager->expectOnce('_set_matched_rule', array($rule));
  }
  
  function test_is_cacheable_groups_match()
  {         
    $this->uri->setReturnValue('get_query_items', array('action' => 1, 'pager' => 1));
    $this->uri->setReturnValue('get_path', '/root/test');
    
    $this->user->setReturnValueAt(0, 'is_in_groups', true, array(array('members', 'visitors')));
    
    $rule = array(
               'path_regex' => '/^\/root\/test.*$/',
               'optional' => array('action', 'pager'),
               'type' => 'allow',
               'groups' => array('members', 'visitors')
              );

    $this->cache_manager->setReturnValue('get_rules',
      array($rule)    
    );
    
    $this->assertTrue($this->cache_manager->is_cacheable());
    $this->cache_manager->expectOnce('_set_matched_rule', array($rule));
  }  
  
  function test_is_not_cacheable_groups_dont_match()
  {         
    $this->uri->setReturnValue('get_query_items', array('action' => 1, 'pager' => 1));
    $this->uri->setReturnValue('get_path', '/root/test');
    
    $this->user->setReturnValueAt(0, 'is_in_groups', false, array(array('members')));
    
    $rule = array(
               'path_regex' => '/^\/root\/test.*$/',
               'optional' => array('action', 'pager'),
               'type' => 'allow',
               'groups' => array('members')
              );

    $this->cache_manager->setReturnValue('get_rules',
      array($rule)    
    );
    
    $this->assertFalse($this->cache_manager->is_cacheable());
    $this->cache_manager->expectNever('_set_matched_rule');
  }  

  function test_is_cacheable_allow_by_default()
  { 
    $rule = array(
               'path_regex' => '/^\/root\/test.*$/',
               'optional' => array('action', 'pager'),
              );

    $this->cache_manager->setReturnValue('get_rules',
      array($rule)    
    );
        
    $this->uri->setReturnValue('get_query_items', array('action' => 1, 'pager' => 1));
    $this->uri->setReturnValue('get_path', '/root/test');
    
    $this->assertTrue($this->cache_manager->is_cacheable());
    $this->cache_manager->expectOnce('_set_matched_rule', array($rule));
  }

  function test_is_not_cacheable_deny()
  { 
    $rule = array(
               'path_regex' => '/^\/root\/test.*$/',
               'optional' => array('action', 'pager'),
               'type' => 'deny'
              );

    $this->cache_manager->setReturnValue('get_rules',
      array($rule)    
    );
        
    $this->uri->setReturnValue('get_query_items', array('action' => 1, 'pager' => 1));
    $this->uri->setReturnValue('get_path', '/root/test');
    
    $this->assertFalse($this->cache_manager->is_cacheable()); 
    $this->cache_manager->expectNever('_set_matched_rule');
  }

  function test_is_cacheable_extra_query_items()
  {       
    $rule = array(
               'path_regex' => '/^\/root\/test.*$/',
               'required' => array('action', 'pager'),
               'type' => 'allow'
              );

    $this->cache_manager->setReturnValue('get_rules',
      array($rule)    
    );
      
    $this->uri->setReturnValue('get_query_items', array('action' => 1, 'pager' => 1, 'extra' => 1));
    $this->uri->setReturnValue('get_path', '/root/test');

    $this->assertTrue($this->cache_manager->is_cacheable());
    $this->cache_manager->expectOnce('_set_matched_rule', array($rule));
  }
  
  function test_is_not_cacheable_not_enough_attributes()
  {    
    $rule = array(
               'path_regex' => '/^\/root\/test.*$/',
               'required' => array('action', 'pager'),
               'type' => 'allow'
              );

    $this->cache_manager->setReturnValue('get_rules',
      array($rule)    
    );
    
    $this->uri->setReturnValue('get_query_items', array('action' => 1));
    $this->uri->setReturnValue('get_path', '/root/test');
    
    $this->assertFalse($this->cache_manager->is_cacheable());
    $this->cache_manager->expectNever('_set_matched_rule');
  }
  
  function test_is_not_cacheable_attributes_dont_match()
  {        
    $rule = array(
               'path_regex' => '/^\/root\/test.*$/',
               'required' => array('action', 'pager'),
               'type' => 'allow'
              );

    $this->cache_manager->setReturnValue('get_rules',
      array($rule)    
    );
    
    $this->uri->setReturnValue('get_query_items', array('action' => 1, 'pager1' => 1));
    $this->uri->setReturnValue('get_path', '/root/test');
    
    $this->assertFalse($this->cache_manager->is_cacheable());
    $this->cache_manager->expectNever('_set_matched_rule');
  }
  
  function test_is_not_cacheable_path_doesnt_match()
  {
    $rule = array(
               'path_regex' => '/^\/root\/test.*$/',
               'attributes' => array('action'),
               'type' => 'allow'
              );

    $this->cache_manager->setReturnValue('get_rules',
      array($rule)    
    );
          
    $this->uri->setReturnValue('get_query_items', array('action' => 1));
    $this->uri->setReturnValue('get_path', '/root/tesx');
    
    $this->assertFalse($this->cache_manager->is_cacheable());
    $this->cache_manager->expectNever('_set_matched_rule');
  }      

  function test_is_cacheable_second_rule_match()
  {    
    $rule1 = array(
           'path_regex' => '/^\/root\/test.*$/',
           'optional' => array('action', 'pager'),
           'type' => 'allow'
    );

    $rule2 = array(
           'path_regex' => '/^\/root\/test.*$/',
           'optional' => array('action'),
           'type' => 'allow'
    );

    $rule3 = array(
           'path_regex' => '/^\/root\/test.*$/',
           'type' => 'allow'
    );

    $this->cache_manager->setReturnValue('get_rules',
      array($rule1, $rule2, $rule3)    
    );

    $this->uri->setReturnValue('get_query_items', array('action' => 1));
    $this->uri->setReturnValue('get_path', '/root/test');
    
    $this->assertTrue($this->cache_manager->is_cacheable());
    $this->cache_manager->expectOnce('_set_matched_rule', array($rule2));
  }

  function test_is_not_cacheable_second_rule_deny()
  {    
    $rule1 = array(
           'path_regex' => '/^\/root\/test.*$/',
           'required' => array('action', 'pager'),
           'type' => 'allow'
    );

    $rule2 = array(
           'path_regex' => '/^\/root\/test.*$/',
           'required' => array('action'),
           'type' => 'deny'
    );

    $this->cache_manager->setReturnValue('get_rules',
      array($rule1, $rule2)    
    );
              
    $this->uri->setReturnValue('get_query_items', array('action' => 1));
    $this->uri->setReturnValue('get_path', '/root/test');
    
    $this->assertFalse($this->cache_manager->is_cacheable());
    $this->cache_manager->expectNever('_set_matched_rule');        
  }

  function test_is_cacheable_no_attributes()
  {    
    $rule = array(
           'path_regex' => '/^\/root\/test.*$/',
           'type' => 'allow'
    );

    $this->cache_manager->setReturnValue('get_rules',
      array($rule)    
    );
    
    $this->uri->setReturnValue('get_query_items', array('action' => 1));
    $this->uri->setReturnValue('get_path', '/root/test');
    
    $this->assertTrue($this->cache_manager->is_cacheable());
    $this->cache_manager->expectOnce('_set_matched_rule', array($rule)); 
  }
  
  function test_get_cache_id_no_matched_rule()
  {
    $this->cache_manager->setReturnValue('_get_matched_rule', null); 
    $this->assertNull($this->cache_manager->get_cache_id());  
  }
  
  function test_get_cache_id_optional_merged_with_required()
  {
    $rule = array('optional' => array('pager', 'prop'), 'required' => array('action'));
    
    $this->uri->setReturnValue('get_query_items', $query_items = array('action' => 1, 'pager' => 2));
    $this->uri->setReturnValue('get_path', '/root/test');

    $this->cache_manager->setReturnValue('_get_matched_rule', $rule); 

    $this->assertEqual(
      $this->cache_manager->get_cache_id(), 
      'f_' . md5('/root/test' . serialize($query_items))
    ); 
  }
  
  function test_get_cache_id_query_items_sorted()
  {
    $rule = array('optional' => array('pager', 'prop'), 'required' => array('action'));
    
    $this->uri->setReturnValue('get_query_items', $query_items = array('action' => 1, 'pager' => 2));

    $this->uri->setReturnValue('get_path', '/root/test');

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
  
  function test_get_cache_id_no_attributes()
  {
    $rule = array();
    
    $this->uri->setReturnValue('get_query_items', $query_items = array('action' => 1));
    $this->uri->setReturnValue('get_path', '/root/test');

    $this->cache_manager->setReturnValue('_get_matched_rule', $rule); 

    $this->assertEqual(
      $this->cache_manager->get_cache_id(), 
      'f_' . md5('/root/test' . serialize(array()))
    ); 
  }

  function test_get_cache_id_junky_attributes()
  {
    $rule = array('optional' => array('pager'), 'required' => array('action'));
    
    $this->uri->setReturnValue('get_query_items', $query_items = array('action' => 1, 'pager' => 1, 'extra' => 1));
    $this->uri->setReturnValue('get_path', '/root/test');

    $this->cache_manager->setReturnValue('_get_matched_rule', $rule); 

    $this->assertEqual(
      $this->cache_manager->get_cache_id(), 
      'f_' . md5('/root/test' . serialize(array('action' => 1, 'pager' => 1)))
    ); 
  }

  function test_cache_doesnt_exist_no_uri()
  {
    $cache_manager = new full_page_cache_manager();
    $this->assertFalse($cache_manager->cache_exists());
  }
  
  function test_cache_exists()
  {    
    $this->_write_cache($path = '/root/test', $attributes = array('action' => 1));
    
    $this->uri->setReturnValue('get_path', $path);
    $this->uri->setReturnValue('get_query_items', $attributes);
    $this->cache_manager->setReturnValue('_get_matched_rule', array('optional' => array('action')));
    
    $this->assertTrue($this->cache_manager->cache_exists());
    
    $this->_clean_cache($path, $attributes);
  }

  function test_cache_doesnt_exist_params_dont_match()
  { 
    $this->_write_cache($path = '/root/test', $attributes = array('action' => 2));
    
    $this->uri->setReturnValue('get_path', '/root/test');   
    $this->uri->setReturnValue('get_query_items', array('action' => 1));
            
    $this->cache_manager->setReturnValue('_get_matched_rule', array('optional' => array('action')));
    
    $this->assertFalse($this->cache_manager->cache_exists());
    
    $this->_clean_cache($path, $attributes);
  }

  function test_cache_doesnt_exist_params_dont_match2()
  { 
    $this->_write_cache($path = '/root/test', $attributes = array('action' => 1, 'page' => 1));
    
    $this->uri->setReturnValue('get_path', '/root/test');   
    $this->uri->setReturnValue('get_query_items', array('action' => 1));
            
    $this->cache_manager->setReturnValue('_get_matched_rule', array('optional' => array('action')));
    
    $this->assertFalse($this->cache_manager->cache_exists());
    
    $this->_clean_cache($path, $attributes);
  }
  
  function test_get_false_no_uri()
  {
    $cache_manager = new full_page_cache_manager();
    $this->assertFalse($cache_manager->get());    
  }

  function test_get()
  {
    $cache_manager =& new full_page_cache_manager_test_version2($this);
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
    $cache_manager = new full_page_cache_manager();
    $this->assertFalse($cache_manager->write($content = 'test'));      
  }

  function test_write()
  {
    $cache_manager =& new full_page_cache_manager_test_version2($this);
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
    
    $cache_manager =& new full_page_cache_manager_test_version2($this);
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
    $this->_write_simple_cache('f_test1', $content1 = 'test-content1');
    $this->_write_simple_cache('f_test2', $content2 ='test-content2');
    $this->_write_simple_cache('not_page_file', $content3 ='test-content3');
    
    $cache_manager =& new full_page_cache_manager();
    $cache_manager->flush();
    
    $files = fs :: find_subitems(PAGE_CACHE_DIR);
    
    $this->assertEqual(sizeof($files), 1);
    
    $file = reset($files);
    $this->assertEqual(fs :: clean_path($file), fs :: clean_path(PAGE_CACHE_DIR . fs :: separator() . 'not_page_file'));

    $this->_clean_simple_cache('not_page_file');
  }
      
  function _write_cache($path, $attributes, $contents='test')
  {
    $file_id = 'f_' . md5($path . serialize($attributes));
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
  
  function _clean_cache($path, $attributes)
  {
    $file_id = 'f_' . md5($path . serialize($attributes));
    $this->_clean_simple_cache($file_id);
  }
}

?>