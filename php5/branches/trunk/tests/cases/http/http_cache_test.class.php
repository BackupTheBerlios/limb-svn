<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: limb@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . '/class/core/request/http_response.class.php');
require_once(LIMB_DIR . '/class/core/request/http_cache.class.php');

Mock::generate('http_response');

class http_cache_test extends LimbTestCase
{
  var $response;
  var $cache;
  var $server_vars;
  
  function setUp()
  {
    $this->response = new Mockhttp_response($this);
    $this->cache = new http_cache();
    $this->server_vars = $_SERVER;
  }
  
  function tearDown()
  {
    $this->response->tally();
    $_SERVER = $this->server_vars;
  } 
  
  function test_set_cache_settings()
  {
    $this->cache->set_last_modified_time($time = time());
    $this->assertEqual($this->cache->get_last_modified_time(), $time);
    $this->assertEqual($this->cache->format_last_modified_time(), gmdate('D, d M Y H:i:s \G\M\T', $time));

    $this->cache->set_etag($etag = md5(time()));
    $this->assertEqual($this->cache->get_etag(), $etag);
    
    $this->cache->set_cache_time(10);
    $this->assertEqual($this->cache->get_cache_time(), 10);
    
    $this->cache->set_cache_type('public');
    $this->assertEqual($this->cache->get_cache_type(), 'public');
  }
  
  function test_get_default_etag1()
  {
    $script = 'test';
    $query = 'query';
    
    $_SERVER['QUERY_STRING'] = $query;
    $_SERVER['SCRIPT_FILENAME'] = $script;
    
    $this->cache->set_last_modified_time($time = time());
    $etag = $this->cache->get_etag();
    
    $this->assertEqual($etag, '"' . md5($script . '?' . $query . '#' . $time ) . '"');
  }
  
  function test_get_default_etag2()
  {
    $script = 'test';
    $query = 'query';
    
    $_SERVER['QUERY_STRING'] = $query;
   unset($_SERVER['SCRIPT_FILENAME']);
    $_SERVER['PATH_TRANSLATED'] = $script;
    
    $this->cache->set_last_modified_time($time = time());
    $etag = $this->cache->get_etag();
    
    $this->assertEqual($etag, '"' . md5($script . '?' . $query . '#' . $time ) . '"');
  }  
  
  function test_get_default_etag3()
  {
    $script = 'test';
    
   unset($_SERVER['QUERY_STRING']);
    $_SERVER['SCRIPT_FILENAME'] = $script;
    
    $this->cache->set_last_modified_time($time = time());
    $etag = $this->cache->get_etag();
    
    $this->assertEqual($etag, '"' . md5($script . '#' . $time ) . '"');
  }   

  function test_is412_false()
  {
    $this->assertFalse($this->cache->is412());
  }  

  function test_is412_false_part_of_etag()
  {
    $_SERVER['HTTP_IF_MATCH'] = 'big_etag';
    
    $this->cache->set_etag('etag');
    
    $this->assertFalse($this->cache->is412());
  }  

  function test_is412_false_asteric()
  {
    $_SERVER['HTTP_IF_MATCH'] = '*';
    
    $this->cache->set_etag('etag');
    
    $this->assertFalse($this->cache->is412());
  }  
  
  function test_is412_etag()
  {
    $_SERVER['HTTP_IF_MATCH'] = 'wrong';
    
    $this->cache->set_etag('etag');
    
    $this->assertTrue($this->cache->is412());
  }
  
  function test_is412_unmodified_since()
  {
    $this->cache->set_last_modified_time($time = time());
    
    $_SERVER['HTTP_IF_UNMODIFIED_SINCE'] = gmdate('D, d M Y H:i:s \G\M\T', $time - 100);
    
    $this->assertTrue($this->cache->is412());
  }  
  
  function test_is304_false()
  {
    $this->assertFalse($this->cache->is304());
  }  
  
  function test_is304_last_modified_time()
  {
    $this->cache->set_last_modified_time($time = time());
    
    $_SERVER['HTTP_IF_MODIFIED_SINCE'] = $this->cache->format_last_modified_time();
    
    $this->assertTrue($this->cache->is304());
  }
  
  function test_is304_etag()
  {
    $etag = 'etag';
    
   unset($_SERVER['HTTP_IF_MODIFIED_SINCE']);
    $_SERVER['HTTP_IF_NONE_MATCH'] = $etag;
    
    $this->cache->set_last_modified_time($time = time());
    $this->cache->set_etag($etag);
        
    $this->assertTrue($this->cache->is304());
  }  

  function test_is304_etag_asteric()
  {
    $etag = 'etag';
    
   unset($_SERVER['HTTP_IF_MODIFIED_SINCE']);
    $_SERVER['HTTP_IF_NONE_MATCH'] = '*';
    
    $this->cache->set_last_modified_time($time = time());
    $this->cache->set_etag($etag);
        
    $this->assertTrue($this->cache->is304());
  }  
  
  function test_check_and_write_412()
  {
    $_SERVER['HTTP_IF_MATCH'] = 'wrong';
    
    $this->cache->set_etag('etag');
    
    $this->response->expectCallCount('header', 3);
    $this->response->expectArgumentsAt(0, 'header', array('HTTP/1.1 412 Precondition Failed'));
    $this->response->expectArgumentsAt(1, 'header', array('Cache-Control: protected, max-age=0, must-revalidate'));
    $this->response->expectArgumentsAt(2, 'header', array('Content-Type: text/plain'));
    
    $this->response->expectOnce('write', array(new WantedPatternExpectation("~^HTTP/1.1 Error 412~")));
    
    $this->assertTrue($this->cache->check_and_write($this->response));
  }
  
  function test_check_and_write_304()
  {
    $_SERVER['HTTP_IF_NONE_MATCH'] = 'etag';
    
    $this->cache->set_etag('etag');
    
    $this->response->expectCallCount('header', 6);
    $this->response->expectArgumentsAt(0, 'header', array('HTTP/1.0 304 Not Modified'));
    $this->response->expectArgumentsAt(1, 'header', array('Etag: etag'));
    $this->response->expectArgumentsAt(2, 'header', array('Pragma: '));
    $this->response->expectArgumentsAt(3, 'header', array('Cache-Control: '));
    $this->response->expectArgumentsAt(4, 'header', array('Last-Modified: '));
    $this->response->expectArgumentsAt(5, 'header', array('Expires: '));
    
    $this->assertTrue($this->cache->check_and_write($this->response));
  }
  
  function test_check_and_write_false_not_head()
  {
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $this->assertFalse($this->cache->check_and_write($this->response));
  }
  
  function test_check_and_write_no_cache_time()  
  {
    $_SERVER['REQUEST_METHOD'] = 'HEAD';
    
    $this->cache->set_last_modified_time($time = time());
    
    $this->response->expectCallCount('header', 5);
    $this->response->expectArgumentsAt(0, 'header', array('Cache-Control: protected, must-revalidate, max-age=0'));
    $this->response->expectArgumentsAt(1, 'header', array('Last-Modified: ' . $this->cache->format_last_modified_time()));
    $this->response->expectArgumentsAt(2, 'header', array('Etag: ' . $this->cache->get_etag()));
    $this->response->expectArgumentsAt(3, 'header', array('Pragma: '));
    $this->response->expectArgumentsAt(4, 'header', array('Expires: '));    
    
    $this->assertTrue($this->cache->check_and_write($this->response));
  }
  
  function test_check_and_write_with_cache_time()  
  {
    $_SERVER['REQUEST_METHOD'] = 'HEAD';
    
    $this->cache->set_last_modified_time($time = time());
    $this->cache->set_cache_time(100);
    
    $this->response->expectCallCount('header', 5);
    $this->response->expectArgumentsAt(0, 'header', array('Cache-Control: protected, max-age=100'));
    $this->response->expectArgumentsAt(1, 'header', array('Last-Modified: ' . $this->cache->format_last_modified_time()));
    $this->response->expectArgumentsAt(2, 'header', array('Etag: ' . $this->cache->get_etag()));
    $this->response->expectArgumentsAt(3, 'header', array('Pragma: '));
    $this->response->expectArgumentsAt(4, 'header', array('Expires: '));    
    
    $this->assertTrue($this->cache->check_and_write($this->response));
  }
  
  function test_check_and_write_with_privacy()  
  {
    $_SERVER['REQUEST_METHOD'] = 'HEAD';
    
    $this->cache->set_last_modified_time($time = time());
    $this->cache->set_cache_time(100);
    $this->cache->set_cache_type(http_cache :: TYPE_PUBLIC);
    
    $this->response->expectCallCount('header', 5);
    $this->response->expectArgumentsAt(0, 'header', array('Cache-Control: public, max-age=100'));
    $this->response->expectArgumentsAt(1, 'header', array('Last-Modified: ' . $this->cache->format_last_modified_time()));
    $this->response->expectArgumentsAt(2, 'header', array('Etag: ' . $this->cache->get_etag()));
    $this->response->expectArgumentsAt(3, 'header', array('Pragma: '));
    $this->response->expectArgumentsAt(4, 'header', array('Expires: '));        
    
    $this->assertTrue($this->cache->check_and_write($this->response));
  }  
  
}

?>