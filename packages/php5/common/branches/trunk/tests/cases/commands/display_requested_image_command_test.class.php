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
require_once(dirname(__FILE__) . '/../../../commands/images/display_requested_image_command.class.php');
require_once(LIMB_DIR . '/class/core/limb_toolkit.interface.php');
require_once(LIMB_DIR . '/class/core/request/http_response.class.php');
require_once(LIMB_DIR . '/class/core/request/request.class.php');
require_once(LIMB_DIR . '/class/core/request/http_cache.class.php');
require_once(LIMB_DIR . '/class/core/datasources/requested_object_datasource.class.php');
require_once(LIMB_DIR . '/class/lib/util/ini.class.php');

Mock :: generate('LimbToolkit');
Mock :: generate('http_response');
Mock :: generate('request');
Mock :: generate('http_cache');
Mock :: generate('ini');
Mock :: generate('requested_object_datasource');

Mock :: generatePartial('display_requested_image_command',
                        'display_image_command_test_version',
                        array('_get_http_cache'));

class display_requested_image_command_test extends LimbTestCase 
{
  var $command;
  var $toolkit;
  var $response;
  var $request;
  var $ini;
  var $datasource;
  var $cache;
        
  function setUp()
  {
    $this->command = new display_image_command_test_version($this);
        
    $this->toolkit = new MockLimbToolkit($this);
    $this->response = new Mockhttp_response($this);
    $this->cache = new Mockhttp_cache($this);
    $this->request = new Mockrequest($this);
    $this->ini = new Mockini($this);
    $this->datasource = new Mockrequested_object_datasource($this);
    
    $this->command->setReturnValue('_get_http_cache', $this->cache);
    
    $this->toolkit->setReturnValue('getResponse', $this->response);
    $this->toolkit->setReturnValue('getRequest', $this->request);
    $this->toolkit->setReturnValue('getDatasource', $this->datasource, array('requested_object_datasource'));
    $this->toolkit->setReturnValue('getINI', $this->ini, array('image_variations.ini'));
    
    $this->datasource->expectOnce('set_request', array(new IsAExpectation('Mockrequest')));
    $this->datasource->expectOnce('fetch');
    
    $this->ini->setReturnValue('get_all', array('original' => array(), 
                                                'thumbnail' => array(), 
                                                'icon' => array()));
     
    Limb :: registerToolkit($this->toolkit);
  }
  
  function tearDown()
  { 
    $this->toolkit->tally();
    $this->response->tally();
    $this->request->tally();
    $this->datasource->tally();
    $this->ini->tally();
    $this->cache->tally();
    
    Limb :: popToolkit();
  }
          
  function test_perform_object_not_fetched()
  {
    $this->datasource->setReturnValue('fetch', array());
    $this->ini->expectNever('get_all');
    
    $this->response->expectNever('commit');
            
    $this->assertEqual($this->command->perform(), Limb :: STATUS_ERROR);
  }  

  function test_perform_no_such_variation_original()
  {
    $this->datasource->setReturnValue('fetch', $object_data = array('id' => 100));
    
    $this->ini->expectOnce('get_all');
    
    $this->request->expectOnce('has_attribute', array('original'));
    $this->request->setReturnValue('has_attribute', true, array('original'));
    
    $this->response->expectNever('commit');
            
    $this->assertEqual($this->command->perform(), Limb :: STATUS_ERROR);
  }  

  function test_perform_no_such_variation_not_original()
  {
    $this->datasource->setReturnValue('fetch', $object_data = array('id' => 100));
    
    $this->ini->expectOnce('get_all');
      
    $this->request->setReturnValue('has_attribute', true, array('icon'));

    $this->response->expectOnce('header', array('Content-type: image/gif'));
    $this->response->expectOnce('readfile', array(HTTP_SHARED_DIR . 'images/1x1.gif'));
    $this->response->expectOnce('commit');
            
    $this->command->perform();
  }  

  function test_perform_no_media_file_original()
  {
    $object_data = array('id' => 100, 
                         'variations' => array('original' => array('media_id' => 'fxfxfxfx')));
    
    $this->datasource->setReturnValue('fetch', $object_data);
    
    $this->ini->expectOnce('get_all');
      
    $this->request->setReturnValue('has_attribute', true, array('original'));
    
    $this->response->expectNever('commit');
            
    $this->assertEqual($this->command->perform(), Limb :: STATUS_ERROR);
  }  

  function test_perform_no_media_file_not_original()
  {
    $object_data = array('id' => 100, 
                         'variations' => array('icon' => array('media_id' => 'fxfxfxfx')));
    
    $this->datasource->setReturnValue('fetch', $object_data);
    
    $this->ini->expectOnce('get_all');
      
    $this->request->setReturnValue('has_attribute', true, array('icon'));
    
    $this->response->expectOnce('header', array('HTTP/1.1 404 Not found'));
    $this->response->expectOnce('commit');
            
    $this->command->perform();
  }

  function test_perform_http_cache_hit_original()
  {
    $object_data = array('id' => 100, 
                         'modified_date' => $time = time(),
                         'variations' => array('original' => array('media_id' => $media_id = 'fxfxfxfx',
                                                                   'mime_type' => $mime_type = 'jpeg')));
    
    $this->datasource->setReturnValue('fetch', $object_data);
    
    $this->request->setReturnValue('has_attribute', true, array('original'));
    
    $this->cache->expectOnce('set_last_modified_time', array($time));
    $this->cache->expectOnce('set_cache_time', array(display_requested_image_command :: DAY_CACHE));
    $this->cache->expectOnce('check_and_write', array(new IsAExpectation('Mockhttp_response')));    
    $this->cache->setReturnValue('check_and_write', true);
    
    $this->response->expectNever('readfile');
    $this->response->expectOnce('header', array("Content-type: {$mime_type}"));
    $this->response->expectNever('commit');
    
    $this->_create_tmp_media($media_id);
    
    $this->assertEqual($this->command->perform(), Limb :: STATUS_OK);
    
    $this->_remove_tmp_media($media_id);
  }

  function test_perform_http_cache_hit_not_original()
  {
    $object_data = array('id' => 100, 
                         'modified_date' => $time = time(),
                         'variations' => array('icon' => array('media_id' => $media_id = 'fxfxfxfx',
                                                               'mime_type' => $mime_type = 'jpeg')));
    
    $this->datasource->setReturnValue('fetch', $object_data);
    
    $this->request->setReturnValue('has_attribute', true, array('icon'));
    
    $this->cache->expectOnce('set_last_modified_time', array($time));
    $this->cache->expectOnce('set_cache_time', array(display_requested_image_command :: DAY_CACHE));
    $this->cache->expectOnce('check_and_write', array(new IsAExpectation('Mockhttp_response')));    
    $this->cache->setReturnValue('check_and_write', true);
    
    $this->response->expectNever('readfile');
    $this->response->expectOnce('header', array("Content-type: {$mime_type}"));
    $this->response->expectOnce('commit');
    
    $this->_create_tmp_media($media_id);
    
    $this->command->perform();
    
    $this->_remove_tmp_media($media_id);
  }

  function test_perform_http_cache_miss()
  {
    $object_data = array('id' => 100, 
                         'modified_date' => $time = time(),
                         'variations' => array('icon' => array('media_id' => $media_id = 'fxfxfxfx',
                                                               'mime_type' => $mime_type = 'jpeg',
                                                               'file_name' => $file_name = 'test file')));
    
    $this->datasource->setReturnValue('fetch', $object_data);
    
    $this->request->setReturnValue('has_attribute', true, array('icon'));
    
    $this->cache->expectOnce('set_last_modified_time', array($time));
    $this->cache->expectOnce('set_cache_time', array(display_requested_image_command :: DAY_CACHE));
    $this->cache->expectOnce('check_and_write', array(new IsAExpectation('Mockhttp_response')));    
    $this->cache->setReturnValue('check_and_write', false);
    
    $this->response->expectOnce('readfile', array(MEDIA_DIR. $media_id .'.media'));
    $this->response->expectArgumentsAt(0, 'header', array("Content-Disposition: filename={$file_name}"));
    $this->response->expectArgumentsAt(1, 'header', array("Content-type: {$mime_type}"));
    $this->response->expectOnce('commit');
    
    $this->_create_tmp_media($media_id);
    
    $this->command->perform();
    
    $this->_remove_tmp_media($media_id);
  }
  
  function _create_tmp_media($media_id)
  {
    fs :: mkdir(MEDIA_DIR);
    touch(MEDIA_DIR. $media_id . '.media');
  }
  
  function _remove_tmp_media($media_id)
  {
    unlink(MEDIA_DIR. $media_id . '.media');
  }
}

?>