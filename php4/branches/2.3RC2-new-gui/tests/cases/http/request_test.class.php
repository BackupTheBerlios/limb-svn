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
require_once(LIMB_DIR . '/core/request/request.class.php');
require_once(LIMB_DIR . '/core/lib/http/uri.class.php');

class request_test extends LimbTestCase
{
  var $old_request_uri;
  var $old_get;
  var $old_post;
  var $old_files;

  function setUp()
  {
    if(sys :: exec_mode() != 'cli')
    {
      $this->old_request_uri = $_SERVER['REQUEST_URI'];
      $this->old_get = $_GET;
      $this->old_post = $_POST;
      $this->old_files = $_FILES;

      $_SERVER['REQUEST_URI'] = '';
      $_GET = array();
      $_POST = array();
      $_FILES = array();
    }
  }

  function tearDown()
  {
    if(sys :: exec_mode() != 'cli')
    {
      $_SERVER['REQUEST_URI'] = $this->old_request_uri;
      $_GET = $this->old_get;
      $_POST = $this->old_post;
      $_FILES = $this->old_files;
    }
  }

  function test_instance()
  {
    $this->assertReference(request :: instance(), request :: instance());
  }

  function test_get_uri_no_post_data()
  {
    $_SERVER['REQUEST_URI'] = 'http://test.com?test1=1';
    $_POST = array('test2' => 1);//it shouldn't be passed

    $request = new request();

    $this->assertEqual(new uri('http://test.com?test1=1'), $request->get_uri());
  }

  function test_to_string_empty_query()
  {
    $_SERVER['REQUEST_URI'] = 'http://test.com';

    $request = new request();

    $this->assertEqual($request->to_string(),
                       'http://test.com');
  }

  function test_to_string_file_data_not_passed()
  {
    $_SERVER['REQUEST_URI'] = 'http://test.com';
    $_FILES = array('file' => array('name' => 'test',
                                    'error' => 'error',
                                    'type' => 'type',
                                    'size' => 'size',
                                    'tmp_name' => 'tmp_name'));
    $request = new request();

    $this->assertEqual($request->to_string(),
                       'http://test.com');
  }

  function test_to_string_post_and_get()
  {
    $_SERVER['REQUEST_URI'] = 'http://test.com?test1=1';
    $_GET = array('test1' => 1);
    $_POST = array('test2' => array('id' => 3), 'test3' => 2);

    $request = new request();

    $this->assertEqual($request->to_string(),
                       'http://test.com?test1=1&test2[id]=3&test3=2');
  }

  function test_to_string_only_post()
  {
    $_SERVER['REQUEST_URI'] = 'http://test.com';
    $_POST = array('test2' => array('id' => 3), 'test3' => 2);

    $request = new request();

    $this->assertEqual($request->to_string(),
                       'http://test.com?test2[id]=3&test3=2');
  }

  function test_set_get_status()
  {
    $request = new request();
    $request->set_status(REQUEST_STATUS_SUCCESS);

    $this->assertEqual($request->get_status(), REQUEST_STATUS_SUCCESS);
  }

  function test_is_problem()
  {
    $request = new request();
    $request->set_status(REQUEST_STATUS_FAILURE | REQUEST_STATUS_FORM_NOT_VALID);
    $this->assertTrue($request->is_problem());
  }

  function test_is_success()
  {
    $request = new request();
    $request->set_status(REQUEST_STATUS_FORM_SUBMITTED | REQUEST_STATUS_FORM_DISPLAYED);
    $this->assertTrue($request->is_success());
  }

}

?>