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
require_once(LIMB_DIR . '/core/system/Sys.class.php');
require_once(LIMB_DIR . '/core/system/Fs.class.php');

define('TEST_DIR_ABSOLUTE_PATH', LIMB_DIR . '/var/');
define('TEST_DIR_RELATIVE_PATH', 'var');

class SpecialDirWalker
{
  var $walked = array();

  function walk($dir, $file, $params, &$return_params)
  {
    static $counter = 0;

    $this->walked[] = Fs :: cleanPath($dir . $params['separator'] .  $file);

    $return_params[] = $counter++;
  }
}

class FsTest extends LimbTestCase
{
  function FsTest()
  {
    parent :: LimbTestCase('fs tests');
  }

  function _createFileSystem()
  {
    Fs :: mkdir(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/hey/');

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

  function _removeFileSystem()
  {
    Fs :: rm(TEST_DIR_ABSOLUTE_PATH . '/tmp/');
  }

  function testIsAbsoluteTrue()
  {
    $this->assertTrue(Fs :: isAbsolute('/test'));

    if(Sys :: osType() == 'win32')
      $this->assertTrue(Fs :: isAbsolute('c:/test'));
  }

  function testRemoveRecursive()
  {
    $this->_createFileSystem();

    Fs :: rm(TEST_DIR_ABSOLUTE_PATH . '/tmp/');

    $this->assertFalse(is_dir(TEST_DIR_ABSOLUTE_PATH . '/tmp/'));
  }

  function testIsPathAbsolute()
  {
    $this->assertTrue(Fs :: isPathAbsolute('c:/var/wow', 'win32'));
    $this->assertTrue(Fs :: isPathAbsolute('/var/wow', 'unix'));

    $this->assertFalse(Fs :: isPathAbsolute(':/var/wow', 'win32'));
    $this->assertFalse(Fs :: isPathAbsolute('/var/wow', 'win32'));
    $this->assertFalse(Fs :: isPathAbsolute('c:/var/wow', 'unix'));

    $this->assertFalse(Fs :: isPathAbsolute('var/wow'));
  }

  function testCleanPath1()
  {
    $path = Fs :: cleanPath('/tmp\../tmp/wow////hey/');
    $this->assertEqual($path, Fs :: separator() . 'tmp' . Fs :: separator() . 'wow' . Fs :: separator() . 'hey' . Fs :: separator());

    $path = Fs :: cleanPath('tmp\../tmp/wow////hey/');
    $this->assertEqual($path, 'tmp' . Fs :: separator() . 'wow' . Fs :: separator() . 'hey' . Fs :: separator());
  }

  function testCleanPath2()
  {
    $path = Fs :: cleanPath('c:\\var\\dev\\demo\\design\\templates\\test.html');

    $this->assertEqual($path,
      'c:' . Fs :: separator() .
      'var' . Fs :: separator() .
      'dev' . Fs :: separator() .
      'demo' . Fs :: separator() .
      'design' . Fs :: separator() .
      'templates' . Fs :: separator() .
      'test.html');
  }

  function testExplodeAbsolutePath()
  {
    $path = Fs :: explodePath('/tmp\../tmp/wow////hey/');

    $this->assertEqual(sizeof($path), 4);

    $this->assertEqual($path[0], '');
    $this->assertEqual($path[1], 'tmp');
    $this->assertEqual($path[2], 'wow');
    $this->assertEqual($path[3], 'hey');

    $path = Fs :: explodePath('/tmp\../tmp/wow////hey'); // no trailing slash

    $this->assertEqual(sizeof($path), 4);

    $this->assertEqual($path[0], '');
    $this->assertEqual($path[1], 'tmp');
    $this->assertEqual($path[2], 'wow');
    $this->assertEqual($path[3], 'hey');
  }

  function testExplodeRelativePath()
  {
    $path = Fs :: explodePath('tmp\../tmp/wow////hey/');

    $this->assertEqual(sizeof($path), 3);

    $this->assertEqual($path[0], 'tmp');
    $this->assertEqual($path[1], 'wow');
    $this->assertEqual($path[2], 'hey');

    $path = Fs :: explodePath('tmp\../tmp/wow////hey'); // no trailing slash

    $this->assertEqual(sizeof($path), 3);

    $this->assertEqual($path[0], 'tmp');
    $this->assertEqual($path[1], 'wow');
    $this->assertEqual($path[2], 'hey');
  }

  function testMkdirAbsolutePath()
  {
    Fs :: rm(TEST_DIR_ABSOLUTE_PATH . '/tmp/');

    $this->assertFalse(is_dir(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/hey/'));

    Fs :: mkdir(TEST_DIR_ABSOLUTE_PATH . '/./tmp\../tmp/wow////hey/');

    $this->assertTrue(is_dir(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/hey/'));
  }

  function testMkdirAbsolutePathNoTrailingSlash()
  {
    Fs :: rm(TEST_DIR_ABSOLUTE_PATH . '/tmp/');

    $this->assertFalse(is_dir(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/hey/'));

    Fs :: mkdir(TEST_DIR_ABSOLUTE_PATH . '/./tmp\../tmp/wow////hey');

    $this->assertTrue(is_dir(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/hey/'));
  }

  function testMkdirRelativePath()
  {
    Fs :: rm(TEST_DIR_RELATIVE_PATH . '/tmp/');

    $this->assertFalse(is_dir(TEST_DIR_RELATIVE_PATH . '/tmp/wow/hey/'));

    Fs :: mkdir(TEST_DIR_RELATIVE_PATH . '/./tmp\../tmp/wow////hey/');

    $this->assertTrue(is_dir(TEST_DIR_RELATIVE_PATH . '/tmp/wow/hey/'));
  }

  function testMkdirRelativePathNoTrailingSlash()
  {
    Fs :: rm(TEST_DIR_RELATIVE_PATH . '/tmp/');

    $this->assertFalse(is_dir(TEST_DIR_RELATIVE_PATH . '/tmp/wow/hey/'));

    Fs :: mkdir(TEST_DIR_RELATIVE_PATH . '/./tmp\../tmp/wow////hey');

    $this->assertTrue(is_dir(TEST_DIR_RELATIVE_PATH . '/tmp/wow/hey/'));
  }

  function testDirpath()
  {
    $this->assertEqual(Fs :: dirpath('/wow/test.txt'), Fs :: cleanPath('/wow'));
    $this->assertEqual(Fs :: dirpath('wow/hey/test.txt'), Fs :: cleanPath('wow/hey'));
    $this->assertEqual(Fs :: dirpath('test.txt'), 'test.txt');
    $this->assertEqual(Fs :: dirpath('/'), '');
  }

  function testLs()
  {
    $this->_createFileSystem();

    $a1 = array('test1_1', 'test1_2', 'test1_3', 'wow');
    sort($a1);
    $a2 =  Fs :: ls(TEST_DIR_ABSOLUTE_PATH . '/tmp/');
    sort($a2);

    $this->assertEqual($a1, $a2);
    $this->assertEqual(array('hey', 'test2_1', 'test2_2', 'test2_3'), Fs :: ls(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow'));

    $this->_removeFileSystem();
  }

  function testPath()
  {
    $this->assertEqual(Fs :: path(array('test')), 'test');
    $this->assertEqual(Fs :: path(array('test', 'wow')), 'test' . Fs :: separator() . 'wow');
    $this->assertEqual(Fs :: path(array('test', 'wow/')), 'test' . Fs :: separator() . 'wow');

    $this->assertEqual(Fs :: path(array('test'), true), 'test' . Fs :: separator());
    $this->assertEqual(Fs :: path(array('test', 'wow'), true), 'test' . Fs :: separator() . 'wow' . Fs :: separator());
  }

  function testChop()
  {
    $this->assertEqual(Fs :: chop('test'), 'test');
    $this->assertEqual(Fs :: chop('test/'), 'test');
  }

  function testWalkDir()
  {
    $this->_createFileSystem();

    $mock = new SpecialDirWalker();

    $this->assertEqual(
      Fs :: walkDir(TEST_DIR_ABSOLUTE_PATH . '/tmp/', array(&$mock, 'walk'), array('test')),
      array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10)
    );

    sort($mock->walked);

    $this->assertEqual(sizeof($mock->walked), 11);

    $this->assertEqual($mock->walked[0], Fs :: cleanPath(TEST_DIR_ABSOLUTE_PATH . '/tmp/test1_1'));
    $this->assertEqual($mock->walked[1], Fs :: cleanPath(TEST_DIR_ABSOLUTE_PATH . '/tmp/test1_2'));
    $this->assertEqual($mock->walked[2], Fs :: cleanPath(TEST_DIR_ABSOLUTE_PATH . '/tmp/test1_3'));
    $this->assertEqual($mock->walked[3], Fs :: cleanPath(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow'));
    $this->assertEqual($mock->walked[4], Fs :: cleanPath(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/hey'));
    $this->assertEqual($mock->walked[5], Fs :: cleanPath(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/hey/test3_1'));
    $this->assertEqual($mock->walked[6], Fs :: cleanPath(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/hey/test3_2'));
    $this->assertEqual($mock->walked[7], Fs :: cleanPath(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/hey/test3_3'));
    $this->assertEqual($mock->walked[8], Fs :: cleanPath(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/test2_1'));
    $this->assertEqual($mock->walked[9], Fs :: cleanPath(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/test2_2'));
    $this->assertEqual($mock->walked[10], Fs :: cleanPath(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/test2_3'));

    $this->_removeFileSystem();
  }

  function testCp()
  {
    $this->_createFileSystem();

    $res = Fs :: cp(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow', TEST_DIR_ABSOLUTE_PATH . '/tmp/cp');
    sort($res);

    $this->assertEqual(
      $res,
      array(
      'hey',
      Fs :: cleanPath('hey/test3_1'),
      Fs :: cleanPath('hey/test3_2'),
      Fs :: cleanPath('hey/test3_3'),
      'test2_1',
      'test2_2',
      'test2_3',
      )
    );

    $this->assertEqual(
      Fs :: ls(TEST_DIR_ABSOLUTE_PATH . '/tmp/cp'),
      Fs :: ls(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow'));

    $this->assertEqual(
      Fs :: ls(TEST_DIR_ABSOLUTE_PATH . '/tmp/cp/hey'),
      Fs :: ls(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/hey'));

    $this->_removeFileSystem();
  }

  function testCpAsShild()
  {
    $this->_createFileSystem();

    Fs :: cp(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow', TEST_DIR_ABSOLUTE_PATH . '/tmp/cp', true);

    $this->assertEqual(
      Fs :: ls(TEST_DIR_ABSOLUTE_PATH . '/tmp/cp/wow/'),
      Fs :: ls(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow'));

    $this->assertEqual(
      Fs :: ls(TEST_DIR_ABSOLUTE_PATH . '/tmp/cp/wow/hey'),
      Fs :: ls(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/hey'));

    $this->_removeFileSystem();
  }

  function testCpWithExclude()
  {
    $this->_createFileSystem();

    $res = Fs :: cp(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow', TEST_DIR_ABSOLUTE_PATH . '/tmp/cp', false, '/hey/');
    sort($res);

    $this->assertEqual(
      $res,
      array('test2_1', 'test2_2', 'test2_3')
    );

    $this->assertEqual(
      $res,
      Fs :: ls(TEST_DIR_ABSOLUTE_PATH . '/tmp/cp/')
    );

    $this->assertFalse(is_dir(TEST_DIR_ABSOLUTE_PATH . '/tmp/cp/hey'));

    $this->_removeFileSystem();
  }

  function testFindSubitems()
  {
    $this->_createFileSystem();

    $res = Fs :: findSubitems(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/hey');
  sort($res);

    $this->assertEqual(
      $res,
      array(
        Fs :: cleanPath(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/hey/test3_1'),
        Fs :: cleanPath(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/hey/test3_2'),
        Fs :: cleanPath(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/hey/test3_3')
      )
    );

    $res = Fs :: findSubitems(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/', 'f', '/^test2_1$/');
    sort($res);

    $this->assertEqual(
      $res,
      array(
        Fs :: cleanPath(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/test2_2'),
        Fs :: cleanPath(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/test2_3'),
      )
    );

    $this->_removeFileSystem();
  }

  function testRecursiveFind()
  {
    $this->_createFileSystem();

    $res = Fs :: recursiveFind(TEST_DIR_ABSOLUTE_PATH . '/tmp/', 'test\d_1');
  sort($res);

    $this->assertEqual(
      $res,
      array(
        Fs :: cleanPath(TEST_DIR_ABSOLUTE_PATH . '/tmp/test1_1'),
        Fs :: cleanPath(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/hey/test3_1'),
        Fs :: cleanPath(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/test2_1'),
      )
    );

    $this->_removeFileSystem();
  }
}

?>