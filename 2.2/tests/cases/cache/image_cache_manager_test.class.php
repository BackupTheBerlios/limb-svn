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
require_once(LIMB_DIR . '/core/cache/image_cache_manager.class.php');
require_once(LIMB_DIR . '/core/request/request.class.php');
require_once(LIMB_DIR . '/core/lib/http/uri.class.php');
require_once(LIMB_DIR . '/core/lib/security/user.class.php');
require_once(LIMB_DIR . '/core/fetcher.class.php');

Mock::generate('uri');
Mock::generate('user');
Mock::generate('fetcher');

Mock::generatePartial(
  'image_cache_manager', 
  'image_cache_manager_test_version', 
  array('get_rules', '_set_matched_rule', '_get_matched_rule', '_get_user')
);

Mock::generatePartial(
  'image_cache_manager', 
  'image_cache_manager_test_version2', 
  array('is_cacheable', '_get_fetcher', '_cache_media_file')
);

Mock::generatePartial(
  'image_cache_manager', 
  'image_cache_manager_test_version3', 
  array('is_cacheable', '_get_fetcher')
);

class image_cache_manager_test extends UnitTestCase
{
  var $cache_manager;
  var $cache_manager2;
  var $uri;
  var $user;
  var $fetcher;
  
  function setUp()
  {
    $this->uri =& new Mockuri($this);
    $this->user =& new Mockuser($this);
    $this->fetcher =& new Mockfetcher($this);
    
    $this->cache_manager =& new image_cache_manager_test_version($this);
    $this->cache_manager->set_uri($this->uri);    
    $this->cache_manager->setReturnReference('_get_user', $this->user);
    
    $this->cache_manager2 =& new image_cache_manager_test_version2($this);
    $this->cache_manager2->setReturnValue('is_cacheable', true);
    $this->cache_manager2->setReturnReference('_get_fetcher', $this->fetcher);
  }
  
  function tearDown()
  {
    $this->cache_manager->tally();
    $this->cache_manager2->tally();
    $this->uri->tally();
    $this->user->tally();
    $this->fetcher->tally();
  }
  
  function test_get_rules_from_ini()
  {
    $cache_manager = new image_cache_manager();
    
    register_testing_ini(
      'image_cache.ini',
      ' 
      [rule1]
       path_regex = /root/test1
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
        array('path_regex' => '/root/test1'),
        array('path_regex' => '/root/test2', 'groups' => array('members', 'visitors'))
      )
    );
    
    clear_testing_ini();
  } 
  
  function test_is_not_cacheable_no_uri()
  {
    $cache_manager = new image_cache_manager();
    
    $this->assertFalse($cache_manager->is_cacheable());
  }
  
  function test_is_cacheable_groups_match()
  {         
    $this->uri->setReturnValue('get_path', '/root/test');
    
    $this->user->setReturnValueAt(0, 'is_in_groups', true, array(array('members', 'visitors')));
    
    $rule = array(
               'path_regex' => '/^\/root\/test.*$/',
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
    $this->uri->setReturnValue('get_path', '/root/test');
    
    $this->user->setReturnValueAt(0, 'is_in_groups', false, array(array('members')));
    
    $rule = array(
               'path_regex' => '/^\/root\/test.*$/',
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
              );

    $this->cache_manager->setReturnValue('get_rules',
      array($rule)    
    );
        
    $this->uri->setReturnValue('get_path', '/root/test');
    
    $this->assertTrue($this->cache_manager->is_cacheable());
    $this->cache_manager->expectOnce('_set_matched_rule', array($rule));
  }

  function test_is_not_cacheable_deny()
  { 
    $rule = array(
               'path_regex' => '/^\/root\/test.*$/',
               'type' => 'deny'
              );

    $this->cache_manager->setReturnValue('get_rules',
      array($rule)    
    );
        
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
    
  function test_is_not_cacheable_path_doesnt_match()
  {
    $rule = array(
               'path_regex' => '/^\/root\/test.*$/',
               'type' => 'allow'
              );

    $this->cache_manager->setReturnValue('get_rules',
      array($rule)    
    );
          
    $this->uri->setReturnValue('get_path', '/root/tesx');
    
    $this->assertFalse($this->cache_manager->is_cacheable());
    $this->cache_manager->expectNever('_set_matched_rule');
  }      

  function test_is_cacheable_second_rule_match()
  {    
    $rule1 = array(
           'path_regex' => '/^\/root\/test1.*$/',
           'type' => 'allow'
    );

    $rule2 = array(
           'path_regex' => '/^\/root\/test2.*$/',
           'type' => 'allow'
    );

    $rule3 = array(
           'path_regex' => '/^\/root\/tes3.*$/',
           'type' => 'allow'
    );

    $this->cache_manager->setReturnValue('get_rules',
      array($rule1, $rule2, $rule3)    
    );

    $this->uri->setReturnValue('get_path', '/root/test2');
    
    $this->assertTrue($this->cache_manager->is_cacheable());
    $this->cache_manager->expectOnce('_set_matched_rule', array($rule2));
  }

  function test_is_not_cacheable_second_rule_deny()
  {    
    $rule1 = array(
           'path_regex' => '/^\/root\/test1.*$/',
           'type' => 'allow'
    );

    $rule2 = array(
           'path_regex' => '/^\/root\/test2.*$/',
           'type' => 'deny'
    );

    $this->cache_manager->setReturnValue('get_rules',
      array($rule1, $rule2)    
    );
              
    $this->uri->setReturnValue('get_path', '/root/test2');
    
    $this->assertFalse($this->cache_manager->is_cacheable());
    $this->cache_manager->expectNever('_set_matched_rule');        
  }
  
  function test_process_empty_content()
  {        
    $this->cache_manager2->process_content($c = '');
    $this->assertIdentical($c, '');
    $this->cache_manager2->expectNever('_get_fetcher');
  }

  function test_process_img_tag()
  {        
    $this->fetcher->expectOnce('fetch_one_by_node_id', array(1));
    $this->fetcher->setReturnValue('fetch_one_by_node_id',     
      array(
        'identifier' => 'test_image', 
        'variations' => array(
          'thumbnail' => array(
            'media_id' => 200,
            'mime_type' => 'image/jpeg'
          ),
        )
      )
    );
    
    $c = '<p><img alt="test" src="/root?node_id=1" border="0"></p>';
    
    $this->cache_manager2->expectOnce('_cache_media_file', array(200, '.jpg'));
    $this->cache_manager2->process_content($c);
    
    $this->assertEqual($c, "<p><img alt=\"test\" src='/var/images/200.jpg' border=\"0\"></p>");
  }

  function test_process_content_background_attribute()
  {        
    $this->fetcher->expectOnce('fetch_one_by_node_id', array(1));
    $this->fetcher->setReturnValue('fetch_one_by_node_id',     
      array(
        'identifier' => 'test_image', 
        'variations' => array(
          'thumbnail' => array(
            'media_id' => 200,
            'mime_type' => 'image/jpeg'
          ),
        )
      )
    );
    
    $c = '<td width="1" background="/root?node_id=1" border="0"></td>';
    
    $this->cache_manager2->expectOnce('_cache_media_file', array(200, '.jpg'));
    $this->cache_manager2->process_content($c);
    
    $this->assertEqual($c, "<td width=\"1\" background='/var/images/200.jpg' border=\"0\"></td>");
  }
    
  function test_process_content_quotes_proper_handling()
  {        
    $this->fetcher->expectOnce('fetch_one_by_node_id', array(1));
    $this->fetcher->setReturnValue('fetch_one_by_node_id',     
      array(
        'identifier' => 'test_image', 
        'variations' => array(
          'thumbnail' => array(
            'media_id' => 200,
            'mime_type' => 'image/jpeg'
          ),
        )
      )
    );
    
    $c = '<p><img alt="test" src=\'/root?node_id=1" border="0"></p>';
    
    $this->cache_manager2->expectOnce('_cache_media_file', array(200, '.jpg'));
    $this->cache_manager2->process_content($c);
    
    $this->assertEqual($c, "<p><img alt=\"test\" src='/var/images/200.jpg' border=\"0\"></p>");
  }  
  
  function test_process_content_original_variation()
  {        
    $this->fetcher->expectOnce('fetch_one_by_node_id', array(1));
    $this->fetcher->setReturnValue('fetch_one_by_node_id',     
      array(
        'identifier' => 'test_image', 
        'variations' => array(
          'original' => array(
            'media_id' => 100,
            'mime_type' => 'image/jpeg'
          ),
        )
      )
    );
    
    $c = '<p><img alt="test" src="/root?node_id=1&original" border="0"></p>';
    
    $this->cache_manager2->expectOnce('_cache_media_file', array(100, '.jpg'));
    $this->cache_manager2->process_content($c);
    
    $this->assertEqual($c, "<p><img alt=\"test\" src='/var/images/100.jpg' border=\"0\"></p>");
  }  

  function test_process_content_thumbnail_variation()
  {        
    $this->fetcher->expectOnce('fetch_one_by_node_id', array(1));
    $this->fetcher->setReturnValue('fetch_one_by_node_id',     
      array(
        'identifier' => 'test_image', 
        'variations' => array(
          'thumbnail' => array(
            'media_id' => 100,
            'mime_type' => 'image/jpeg'
          ),
        )
      )
    );
    
    $c = '<p><img alt="test" src="/root?node_id=1&thumbnail" border="0"></p>';
    
    $this->cache_manager2->expectOnce('_cache_media_file', array(100, '.jpg'));
    $this->cache_manager2->process_content($c);
    
    $this->assertEqual($c, "<p><img alt=\"test\" src='/var/images/100.jpg' border=\"0\"></p>");
  }  

  function test_process_content_icon_variation()
  {        
    $this->fetcher->expectOnce('fetch_one_by_node_id', array(1));
    $this->fetcher->setReturnValue('fetch_one_by_node_id',     
      array(
        'identifier' => 'test_image', 
        'variations' => array(
          'icon' => array(
            'media_id' => 300,
            'mime_type' => 'image/jpeg'
          ),
        )
      )
    );
    
    $c = '<p><img alt="test" src="/root?node_id=1&icon" border="0"></p>';
    
    $this->cache_manager2->expectOnce('_cache_media_file', array(300, '.jpg'));
    $this->cache_manager2->process_content($c);
    
    $this->assertEqual($c, "<p><img alt=\"test\" src='/var/images/300.jpg' border=\"0\"></p>");
  }  
  
  function test_process_content_img_and_background()
  {        
    $this->fetcher->expectArgumentsAt(0, 'fetch_one_by_node_id', array(1));
    $this->fetcher->expectArgumentsAt(1, 'fetch_one_by_node_id', array(1));
    
    $this->fetcher->setReturnValueAt(0, 'fetch_one_by_node_id',     
      array(
        'identifier' => 'test_image', 
        'variations' => array(
          'thumbnail' => array(
            'media_id' => 200,
            'mime_type' => 'image/jpeg'
          ),
        )
      )
    );

    $this->fetcher->setReturnValueAt(1, 'fetch_one_by_node_id',     
      array(
        'identifier' => 'test_image', 
        'variations' => array(
          'icon' => array(
            'media_id' => 300,
            'mime_type' => 'image/jpeg'
          ),
        )
      )
    );
    
    $c = '<img src="/root?node_id=1"><br><td width="1" background="/root?node_id=1&icon" border="0"></td>';
    
    $this->cache_manager2->expectArgumentsAt(0, '_cache_media_file', array(200, '.jpg'));
    $this->cache_manager2->expectArgumentsAt(1, '_cache_media_file', array(300, '.jpg'));
    $this->cache_manager2->process_content($c);
    
    $this->assertEqual($c, "<img src='/var/images/200.jpg'><br><td width=\"1\" background='/var/images/300.jpg' border=\"0\"></td>");
  }          
  
  function test_cached_write_file()
  {
    $cache_manager =& new image_cache_manager_test_version3($this);
    $cache_manager->setReturnValue('is_cacheable', true);
    $cache_manager->setReturnReference('_get_fetcher', $this->fetcher);
    
    $this->fetcher->expectOnce('fetch_one_by_node_id', array(1));
    $this->fetcher->setReturnValue('fetch_one_by_node_id',     
      array(
        'identifier' => 'test_image', 
        'variations' => array(
          'icon' => array(
            'media_id' => 300,
            'mime_type' => 'image/jpeg'
          ),
        )
      )
    );
    
    $c = '<p><img alt="test" src="/root?node_id=1&icon" border="0"></p>';
    $this->_write_media_file(300);
    $cache_manager->process_content($c);
    
    $this->assertTrue(file_exists(IMAGE_CACHE_DIR . '300.jpg'));
    
    $this->_clean_up();
  }

  function test_cached_write_file_no_media_file()
  {
    $cache_manager =& new image_cache_manager_test_version3($this);
    $cache_manager->setReturnValue('is_cacheable', true);
    $cache_manager->setReturnReference('_get_fetcher', $this->fetcher);
    
    $this->fetcher->expectOnce('fetch_one_by_node_id', array(1));
    $this->fetcher->setReturnValue('fetch_one_by_node_id',     
      array(
        'identifier' => 'test_image', 
        'variations' => array(
          'icon' => array(
            'media_id' => 300,
            'mime_type' => 'image/jpeg'
          ),
        )
      )
    );
    
    $c = '<p><img alt="test" src="/root?node_id=1&icon" border="0"></p>';
  
    $cache_manager->process_content($c);
    
    $this->assertFalse(file_exists(IMAGE_CACHE_DIR . '300.jpg'));
    
    $this->_clean_up();
  }

  function _write_media_file($media_id)
  {
    fs :: mkdir(MEDIA_DIR);
    
    $f = fopen(MEDIA_DIR . $media_id . '.media', 'w');
    fwrite($f, 'test');
    fclose($f);
  }
  
  function _clean_up()
  {
    fs :: rm(IMAGE_CACHE_DIR);
    fs :: rm(MEDIA_DIR);
  }
}

?>