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
require_once(LIMB_DIR . '/core/lib/system/sys.class.php');
require_once(LIMB_DIR . '/core/lib/system/fs.class.php');

define('TEST_DIR_ABSOLUTE_PATH', PROJECT_DIR . '/var/');
define('TEST_DIR_RELATIVE_PATH', 'var/');

class special_dir_walker
{
  var $walked = array();

  function walk($dir, $file, $params, &$return_params)
  {
    static $counter = 0;

    $this->walked[] = fs :: clean_path($dir . $params['separator'] .  $file);
    $return_params[] = $counter++;
  }
}

class fs_test extends LimbTestCase
{
  function _create_file_system()
  {
    fs :: mkdir(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/hey/');

    touch(TEST_DIR_ABSOLUTE_PATH . '/tmp/test1_1');
    touch(TEST_DIR_ABSOLUTE_PATH . '/tmp/test1_2');
    touch(TEST_DIR_ABSOLUTE_PATH . '/tmp/test1_3');

    touch(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/test2_1');
    touch(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/test2_2');
    touch(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/test2_3');

    touch(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/hey/test3_1');
    touch(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/hey/test3_2');
    touch(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/hey/test3_3');
  }

  function _remove_file_system()
  {
    fs :: rm(TEST_DIR_ABSOLUTE_PATH . '/tmp/');
  }

  function test_is_absolute_true()
  {
    $this->assertTrue(fs :: is_absolute('/test'));

    if(sys :: os_type() == 'win32')
      $this->assertTrue(fs :: is_absolute('c:/test'));
  }

  function test_remove_recursive()
  {
    $this->_create_file_system();

    fs :: rm(TEST_DIR_ABSOLUTE_PATH . '/tmp/');

    $this->assertFalse(is_dir(TEST_DIR_ABSOLUTE_PATH . '/tmp/'));
  }

  function test_clean_path()
  {
    $path = fs :: clean_path('/tmp\../tmp/wow////hey/');
    $this->assertEqual($path, fs :: separator() . 'tmp' . fs :: separator() . 'wow' . fs :: separator() . 'hey' . fs :: separator());

    $path = fs :: clean_path('tmp\../tmp/wow////hey/');
    $this->assertEqual($path, 'tmp' . fs :: separator() . 'wow' . fs :: separator() . 'hey' . fs :: separator());
  }

  function test_explode_absolute_path()
  {
    $path = fs :: explode_path('/tmp\../tmp/wow////hey/');

    $this->assertEqual(sizeof($path), 4);

    $this->assertEqual($path[0], '');
    $this->assertEqual($path[1], 'tmp');
    $this->assertEqual($path[2], 'wow');
    $this->assertEqual($path[3], 'hey');

    $path = fs :: explode_path('/tmp\../tmp/wow////hey'); // no trailing slash

    $this->assertEqual(sizeof($path), 4);

    $this->assertEqual($path[0], '');
    $this->assertEqual($path[1], 'tmp');
    $this->assertEqual($path[2], 'wow');
    $this->assertEqual($path[3], 'hey');
  }

  function test_explode_relative_path()
  {
    $path = fs :: explode_path('tmp\../tmp/wow////hey/');

    $this->assertEqual(sizeof($path), 3);

    $this->assertEqual($path[0], 'tmp');
    $this->assertEqual($path[1], 'wow');
    $this->assertEqual($path[2], 'hey');

    $path = fs :: explode_path('tmp\../tmp/wow////hey'); // no trailing slash

    $this->assertEqual(sizeof($path), 3);

    $this->assertEqual($path[0], 'tmp');
    $this->assertEqual($path[1], 'wow');
    $this->assertEqual($path[2], 'hey');
  }

  function test_mkdir_absolute_path()
  {
    fs :: rm(TEST_DIR_ABSOLUTE_PATH . '/tmp/');

    $this->assertFalse(is_dir(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/hey/'));

    fs :: mkdir(TEST_DIR_ABSOLUTE_PATH . '/./tmp\../tmp/wow////hey/');

    $this->assertTrue(is_dir(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/hey/'));
  }

  function test_mkdir_absolute_path_no_trailing_slash()
  {
    fs :: rm(TEST_DIR_ABSOLUTE_PATH . '/tmp/');

    $this->assertFalse(is_dir(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/hey/'));

    fs :: mkdir(TEST_DIR_ABSOLUTE_PATH . '/./tmp\../tmp/wow////hey');

    $this->assertTrue(is_dir(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/hey/'));
  }

  function test_mkdir_relative_path()
  {
    fs :: rm(TEST_DIR_RELATIVE_PATH . '/tmp/');

    $this->assertFalse(is_dir(TEST_DIR_RELATIVE_PATH . '/tmp/wow/hey/'));

    fs :: mkdir(TEST_DIR_RELATIVE_PATH . '/./tmp\../tmp/wow////hey/');

    $this->assertTrue(is_dir(TEST_DIR_RELATIVE_PATH . '/tmp/wow/hey/'));
  }

  function test_mkdir_relative_path_no_trailing_slash()
  {
    fs :: rm(TEST_DIR_RELATIVE_PATH . '/tmp/');

    $this->assertFalse(is_dir(TEST_DIR_RELATIVE_PATH . '/tmp/wow/hey/'));

    fs :: mkdir(TEST_DIR_RELATIVE_PATH . '/./tmp\../tmp/wow////hey');

    $this->assertTrue(is_dir(TEST_DIR_RELATIVE_PATH . '/tmp/wow/hey/'));
  }

  function test_dirpath()
  {
    $this->assertEqual(fs :: dirpath('/wow/test.txt'), fs :: clean_path('/wow'));
    $this->assertEqual(fs :: dirpath('wow/hey/test.txt'), fs :: clean_path('wow/hey'));
    $this->assertEqual(fs :: dirpath('test.txt'), 'test.txt');
    $this->assertEqual(fs :: dirpath('/'), '');
  }

  function test_ls()
  {
    $this->_create_file_system();

    $a1 = array('test1_1', 'test1_2', 'test1_3', 'wow');
    sort($a1);
    $a2 =  fs :: ls(TEST_DIR_ABSOLUTE_PATH . '/tmp/');

    $this->assertEqual($a1, $a2);
    $this->assertEqual(array('hey', 'test2_1', 'test2_2', 'test2_3'), fs :: ls(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow'));

    $this->_remove_file_system();
  }

  function test_path()
  {
    $this->assertEqual(fs :: path(array('test')), 'test');
    $this->assertEqual(fs :: path(array('test', 'wow')), 'test' . fs :: separator() . 'wow');
    $this->assertEqual(fs :: path(array('test', 'wow/')), 'test' . fs :: separator() . 'wow');

    $this->assertEqual(fs :: path(array('test'), true), 'test' . fs :: separator());
    $this->assertEqual(fs :: path(array('test', 'wow'), true), 'test' . fs :: separator() . 'wow' . fs :: separator());
  }

  function test_chop()
  {
    $this->assertEqual(fs :: chop('test'), 'test');
    $this->assertEqual(fs :: chop('test/'), 'test');
  }

  function test_walk_dir()
  {
    $this->_create_file_system();

    $mock = new special_dir_walker();

    $this->assertEqual(
      fs :: walk_dir(TEST_DIR_ABSOLUTE_PATH . '/tmp/', array(&$mock, 'walk'), array('test')),
      array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10)
    );

    sort($mock->walked);

    $this->assertEqual(sizeof($mock->walked), 11);

    $this->assertEqual($mock->walked[0], fs :: clean_path(TEST_DIR_ABSOLUTE_PATH . '/tmp/test1_1'));
    $this->assertEqual($mock->walked[1], fs :: clean_path(TEST_DIR_ABSOLUTE_PATH . '/tmp/test1_2'));
    $this->assertEqual($mock->walked[2], fs :: clean_path(TEST_DIR_ABSOLUTE_PATH . '/tmp/test1_3'));
    $this->assertEqual($mock->walked[3], fs :: clean_path(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow'));
    $this->assertEqual($mock->walked[4], fs :: clean_path(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/hey'));
    $this->assertEqual($mock->walked[5], fs :: clean_path(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/hey/test3_1'));
    $this->assertEqual($mock->walked[6], fs :: clean_path(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/hey/test3_2'));
    $this->assertEqual($mock->walked[7], fs :: clean_path(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/hey/test3_3'));
    $this->assertEqual($mock->walked[8], fs :: clean_path(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/test2_1'));
    $this->assertEqual($mock->walked[9], fs :: clean_path(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/test2_2'));
    $this->assertEqual($mock->walked[10], fs :: clean_path(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/test2_3'));

    $this->_remove_file_system();
  }

  function test_cp()
  {
    $this->_create_file_system();

    $res = fs :: cp(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow', TEST_DIR_ABSOLUTE_PATH . '/tmp/cp');

    $this->assertEqual(
      $res,
      array(
      'hey',
      fs :: clean_path('hey/test3_1'),
      fs :: clean_path('hey/test3_2'),
      fs :: clean_path('hey/test3_3'),
      'test2_1',
      'test2_2',
      'test2_3',
      )
    );

    $this->assertEqual(
      fs :: ls(TEST_DIR_ABSOLUTE_PATH . '/tmp/cp'),
      fs :: ls(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow'));

    $this->assertEqual(
      fs :: ls(TEST_DIR_ABSOLUTE_PATH . '/tmp/cp/hey'),
      fs :: ls(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/hey'));

    $this->_remove_file_system();
  }

  function test_cp_as_child()
  {
    $this->_create_file_system();

    fs :: cp(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow', TEST_DIR_ABSOLUTE_PATH . '/tmp/cp', true);

    $this->assertEqual(
      fs :: ls(TEST_DIR_ABSOLUTE_PATH . '/tmp/cp/wow/'),
      fs :: ls(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow'));

    $this->assertEqual(
      fs :: ls(TEST_DIR_ABSOLUTE_PATH . '/tmp/cp/wow/hey'),
      fs :: ls(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/hey'));

    $this->_remove_file_system();
  }

  function test_cp_with_exclude()
  {
    $this->_create_file_system();

    $res = fs :: cp(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow', TEST_DIR_ABSOLUTE_PATH . '/tmp/cp', false, null, '/hey/');

    $this->assertEqual(
      $res,
      array('test2_1', 'test2_2', 'test2_3')
    );

    $this->assertEqual(
      $res,
      fs :: ls(TEST_DIR_ABSOLUTE_PATH . '/tmp/cp/')
    );

    $this->assertFalse(is_dir(TEST_DIR_ABSOLUTE_PATH . '/tmp/cp/hey'));

    $this->_remove_file_system();
  }

  function test_cp_with_include()
  {
    $this->_create_file_system();

    $res = fs :: cp(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow', TEST_DIR_ABSOLUTE_PATH . '/tmp/cp', false, '/test2/');

    $this->assertEqual(
      $res,
      array('test2_1', 'test2_2', 'test2_3')
    );

    $this->assertEqual(
      $res,
      fs :: ls(TEST_DIR_ABSOLUTE_PATH . '/tmp/cp/')
    );

    $this->assertFalse(is_dir(TEST_DIR_ABSOLUTE_PATH . '/tmp/cp/hey'));

    $this->_remove_file_system();
  }

  function test_find()
  {
    $this->_create_file_system();

    $res = fs :: find(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/hey');

    $this->assertEqual(
      $res,
      array(
        fs :: clean_path(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/hey/test3_1'),
        fs :: clean_path(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/hey/test3_2'),
        fs :: clean_path(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/hey/test3_3')
      )
    );

    $res = fs :: find(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/', 'f', null, '/^test2_1$/');
    sort($res);

    $this->assertEqual(
      $res,
      array(
        fs :: clean_path(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/test2_2'),
        fs :: clean_path(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/test2_3'),
      )
    );

    $this->_remove_file_system();
  }

  function test_recursive_find()
  {
    $this->_create_file_system();

    $res = fs :: recursive_find(TEST_DIR_ABSOLUTE_PATH . '/tmp/', 'test\d_1');
  sort($res);

    $this->assertEqual(
      $res,
      array(
        fs :: clean_path(TEST_DIR_ABSOLUTE_PATH . '/tmp/test1_1'),
        fs :: clean_path(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/hey/test3_1'),
        fs :: clean_path(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/test2_1'),
      )
    );

    $this->_remove_file_system();
  }
}

?>