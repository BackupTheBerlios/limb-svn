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
require_once(dirname(__FILE__) . '/../../../commands/files/display_requested_file_command.class.php');
require_once(LIMB_DIR . '/class/core/limb_toolkit.interface.php');
require_once(LIMB_DIR . '/class/core/request/http_response.class.php');
require_once(LIMB_DIR . '/class/core/request/request.class.php');
require_once(LIMB_DIR . '/class/core/datasources/requested_object_datasource.class.php');
include_once(LIMB_DIR . '/class/lib/util/mime_type.class.php');

Mock :: generate('LimbToolkit');
Mock :: generate('http_response');
Mock :: generate('request');
Mock :: generate('mime_type');
Mock :: generate('requested_object_datasource');

Mock :: generatePartial('display_requested_file_command',
                        'display_file_command_test_version',
                        array('_get_mime_type'));

class display_requested_file_command_test extends LimbTestCase 
{
  var $command;
  var $toolkit;
  var $response;
  var $request;
  var $datasource;
  var $mime;
        
  function setUp()
  {
    $this->command = new display_file_command_test_version($this);
        
    $this->toolkit = new MockLimbToolkit($this);
    $this->response = new Mockhttp_response($this);
    $this->request = new Mockrequest($this);
    $this->datasource = new Mockrequested_object_datasource($this);
    $this->mime = new Mockmime_type($this);
    
    $this->command->setReturnValue('_get_mime_type', $this->mime);
    
    $this->toolkit->setReturnValue('getResponse', $this->response);
    $this->toolkit->setReturnValue('getRequest', $this->request);
    $this->toolkit->setReturnValue('getDatasource', $this->datasource, array('requested_object_datasource'));
    
    $this->datasource->expectOnce('set_request', array(new IsAExpectation('Mockrequest')));
    $this->datasource->expectOnce('fetch');
    
    Limb :: registerToolkit($this->toolkit);
  }
  
  function tearDown()
  { 
    $this->toolkit->tally();
    $this->response->tally();
    $this->request->tally();
    $this->datasource->tally();
    $this->mime->tally();
    
    Limb :: popToolkit();
  }
          
  function test_perform_object_not_fetched()
  {
    $this->datasource->setReturnValue('fetch', array());
    
    $this->response->expectNever('commit');
            
    $this->assertEqual($this->command->perform(), Limb :: STATUS_ERROR);
  }  

  function test_perform_no_media_file_not_icon()
  {
    $this->datasource->setReturnValue('fetch', $object_data = array('id' => 100,
                                                                    'media_id' => 'yoyoyo'));
        
    $this->response->expectOnce('header', array('HTTP/1.1 404 Not found'));
    $this->response->expectNever('commit');
    
    $this->request->expectOnce('has_attribute', array('icon'));
    $this->request->setReturnValue('has_attribute', false, array('icon'));    
             
    $this->assertEqual($this->command->perform(), Limb :: STATUS_ERROR);
  }  

  function test_perform_no_media_file_is_icon()
  {
    $this->datasource->setReturnValue('fetch', $object_data = array('id' => 100,
                                                                    'media_id' => 'yoyoyo'));
        
    $this->response->expectOnce('header', array('HTTP/1.1 404 Not found'));
    $this->response->expectOnce('commit');
    
    $this->request->expectOnce('has_attribute', array('icon'));
    $this->request->setReturnValue('has_attribute', true, array('icon'));    
             
    $this->command->perform();
  } 
  
  function test_perform_show_default_sized_icon()
  {
    $this->datasource->setReturnValue('fetch', $object_data = array('id' => 100,
                                                                    'media_id' => $media_id = 'yoyoyo',
                                                                    'mime_type' => $mime_type = 'file',
                                                                    'file_name' => $file_name = 'test file'));
       
    $this->request->expectOnce('has_attribute', array('icon'));
    $this->request->setReturnValue('has_attribute', true, array('icon'));
    $this->request->setReturnValue('get', '', array('icon'));
    
    $this->mime->expectOnce('get_type_icon', array($mime_type));
    $this->mime->setReturnValue('get_type_icon', $icon = 'application_doc', array($mime_type));
    
    $this->response->expectOnce('header', array('Content-type: image/gif'));
    $this->response->expectOnce('readfile', array(HTTP_MIME_ICONS_DIR . 
                                                  $icon . '.' . 
                                                  display_requested_file_command :: DEFAULT_ICON_SIZE . 
                                                  '.gif'));
    $this->response->expectOnce('commit');    
    
    $this->_create_tmp_media($media_id);
    
    $this->command->perform();
    
    $this->_remove_tmp_media($media_id);
  }    

  function test_perform_show_requested_size_icon()
  {
    $this->datasource->setReturnValue('fetch', $object_data = array('id' => 100,
                                                                    'media_id' => $media_id = 'yoyoyo',
                                                                    'mime_type' => $mime_type = 'file',
                                                                    'file_name' => $file_name = 'test file'));
       
    $this->request->expectOnce('has_attribute', array('icon'));
    $this->request->setReturnValue('has_attribute', true, array('icon'));
    $this->request->setReturnValue('get', $size = 32, array('icon'));
    
    $this->mime->expectOnce('get_type_icon', array($mime_type));
    $this->mime->setReturnValue('get_type_icon', $icon = 'application_doc', array($mime_type));
    
    $this->response->expectOnce('header', array('Content-type: image/gif'));
    $this->response->expectOnce('readfile', array(HTTP_MIME_ICONS_DIR . 
                                                  $icon . '.' . 
                                                  $size . 
                                                  '.gif'));
    $this->response->expectOnce('commit');    
    
    $this->_create_tmp_media($media_id);
    
    $this->command->perform();
    
    $this->_remove_tmp_media($media_id);
  }      

  function test_perform_show_requested_icon_no_mime_file()
  {
    $this->datasource->setReturnValue('fetch', $object_data = array('id' => 100,
                                                                    'media_id' => $media_id = 'yoyoyo',
                                                                    'mime_type' => $mime_type = 'file',
                                                                    'file_name' => $file_name = 'test file'));
       
    $this->request->expectOnce('has_attribute', array('icon'));
    $this->request->setReturnValue('has_attribute', true, array('icon'));
    $this->request->setReturnValue('get', $size = 32, array('icon'));
    
    $this->mime->expectOnce('get_type_icon', array($mime_type));
    $this->mime->setReturnValue('get_type_icon', $icon = 'no-such-icon', array($mime_type));
    
    $this->response->expectOnce('header', array('Content-type: image/gif'));
    $this->response->expectOnce('readfile', array(HTTP_MIME_ICONS_DIR . 
                                                  'file.' . 
                                                  $size . 
                                                  '.gif'));
    $this->response->expectOnce('commit');    
    
    $this->_create_tmp_media($media_id);
     
    $this->command->perform();
    
    $this->_remove_tmp_media($media_id);
  }      
  
  function test_perform_readfile()
  {
    $this->datasource->setReturnValue('fetch', $object_data = array('id' => 100,
                                                                    'media_id' => $media_id = 'yoyoyo',
                                                                    'mime_type' => $mime_type = 'file',
                                                                    'file_name' => $file_name = 'test file'));
       
    $this->request->expectOnce('has_attribute', array('icon'));
    $this->request->setReturnValue('has_attribute', false, array('icon'));

    $this->response->expectArgumentsAt(0, 'header', array("Content-type: {$mime_type}"));
    $this->response->expectArgumentsAt(1, 'header', array("Content-Disposition: attachment; filename=\"{$file_name}\""));
    $this->response->expectOnce('readfile', array(MEDIA_DIR . $media_id . '.media'));
    
    $this->_create_tmp_media($media_id);
    
    $this->assertEqual($this->command->perform(), Limb :: STATUS_OK);
    
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