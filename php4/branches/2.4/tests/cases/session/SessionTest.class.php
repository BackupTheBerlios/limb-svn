<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . '/class/session/Session.class.php');
require_once(LIMB_DIR . '/class/session/SessionDriver.interface.php');

Mock :: generate('SessionDriver');

class SessionTest extends LimbTestCase
{
  var $session;
  var $session_driver;

  function setUp()
  {
    $this->session_driver = new MockSessionDriver($this);
    $this->session = new Session($this->session_driver);
  }

  function tearDown()
  {
    $this->session_driver->tally();
  }

  function testStorageOpen()
  {
    $this->session_driver->expectOnce('storageOpen');
    $this->session->storageOpen();
  }

  function testCloseSession()
  {
    $this->session_driver->expectOnce('storageClose');
    $this->session->storageClose();
  }

  function testReadSession()
  {
    $session_include_path1 = dirname(__FILE__) . '/session_test_include_file1.php';
    $session_include_path2 = dirname(__FILE__) . '/session_test_include_file2.php';

    $raw_session_data = 'global_user|O:4:"user":12:{s:3:"_id";i:-1;s:8:"_node_id";i:-1;s:6:"_login";s:0:"";s:9:"_password";s:0:"";s:6:"_email";s:0:"";s:5:"_name";s:0:"";s:9:"_lastname";s:0:"";s:10:"_locale_id";s:0:"";s:13:"_is_logged_in";b:0;s:7:"_groups";a:1:{i:27;s:8:"visitors";}s:11:"_attributes";O:9:"dataspace":2:{s:4:"vars";a:0:{}s:6:"filter";N;}' .
                        's:20:"__session_class_path";s:56:"' . $session_include_path1 . '";}' .
                        'session_history|a:1:{s:3:"tab";a:3:{i:0;a:2:{s:5:"title";s:7:"Yo-yo";s:4:"href";' .
                        's:20:"__session_class_path";s:56:"' . $session_include_path2 .'";}s:38:"http://dbrain.bit-creative.bit/root/ru";}i:1;a:2:{s:5:"title";s:5:"?????";s:4:"href";s:57:"http://dbrain.bit-creative.bit/root/ru/portfolio/websites";}i:2;a:2:{s:5:"title";s:5:"Bla-bla";s:4:"href";s:84:"http://dbrain.bit-creative.bit/root/ru/portfolio/websites?id=273&action=presentation";}}}strings|s:0:"";';

    $this->session_driver->expectOnce('storageRead', array($id = 100));
    $this->session_driver->setReturnValue('storageRead', $raw_session_data);
    $this->session->storageRead($id);

    $this->assertEqual($GLOBALS['session_read_include_file_test_value1'], 'whatever');
    $this->assertEqual($GLOBALS['session_read_include_file_test_value2'], 'nevermind');
  }

  function testStorageWrite()
  {
    $this->session_driver->expectOnce('storageWrite', array($id = 20, $value = 'something' ));
    $this->session->storageWrite($id, $value);
  }

  function testStorageDestroy()
  {
    $this->session_driver->expectOnce('storageDestroy', array($id = 20));
    $this->session->storageDestroy($id);
  }

  function testStorageDestroyUser()
  {
    $this->session_driver->expectOnce('storageDestroyUser', array($user_id = 20));
    $this->session->storageDestroyUser($user_id);
  }

  function testStorageGc()
  {
    $this->session_driver->expectOnce('storageGc', array($time = 200));
    $this->session->storageGc($time);
  }

  function testGet()
  {
    $key = md5(mt_rand());

    $this->assertNull($this->session->get($key));

    $_SESSION[$key] = 'test';

    $this->assertEqual($this->session->get($key), 'test');

    unset($_SESSION[$key]);
  }

  function testGetReference()
  {
    $key = md5(mt_rand());

    $ref =& $this->session->getReference($key);

    $ref = 'ref test';

    $this->assertEqual($this->session->get($key), 'ref test');

    unset($_SESSION[$key]);
  }

  function testExists()
  {
    $key = md5(mt_rand());

    $this->assertFalse($this->session->exists($key));

    $_SESSION[$key] = 'test';

    $this->assertTrue($this->session->exists($key));

    unset($_SESSION[$key]);
  }

  function testDestroy()
  {
    $key = md5(mt_rand());

    $_SESSION[$key] = 'test';

    $this->session->destroy($key);
    $this->assertFalse($this->session->exists($key));
  }

}

?>
