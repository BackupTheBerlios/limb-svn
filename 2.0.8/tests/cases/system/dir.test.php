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
require_once(LIMB_DIR . '/core/lib/system/sys.class.php');
require_once(LIMB_DIR . '/core/lib/system/dir.class.php');

define('TEST_DIR_ABSOLUTE_PATH', PROJECT_DIR . '/var/');
define('TEST_DIR_RELATIVE_PATH', 'var');

class dir_walker
{
	function walk($dir, $file, $params, &$return_params){}
}

Mock::generate('dir_walker', 'mock_dir_walker');

class special_dir_walker extends mock_dir_walker
{
	function walk($dir, $file, $params, &$return_params)
	{
		static $counter = 0;
		
		$return_params[] = $counter++;
		
		parent :: walk($dir, $file, $params, &$return_params);
	}
}

class test_dir extends UnitTestCase 
{
  function test_dir() 
  {
  	parent :: UnitTestCase();
  }  
  
  function _create_file_system()
  {
  	dir :: mkdir(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/hey/');
  	
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
  	dir :: rm(TEST_DIR_ABSOLUTE_PATH . '/tmp/');
  }
  
  function test_remove_recursive()
  {
		$this->_create_file_system();
		  	
  	dir :: rm(TEST_DIR_ABSOLUTE_PATH . '/tmp/');
  	
  	$this->assertFalse(is_dir(TEST_DIR_ABSOLUTE_PATH . '/tmp/'));
  }
  
  function test_clean_path()
  {
  	$path = dir :: clean_path('/tmp\../tmp/wow////hey/');
  	$this->assertEqual($path, dir :: separator() . 'tmp' . dir :: separator() . 'wow' . dir :: separator() . 'hey' . dir :: separator());
  	
  	$path = dir :: clean_path('tmp\../tmp/wow////hey/');
  	$this->assertEqual($path, 'tmp' . dir :: separator() . 'wow' . dir :: separator() . 'hey' . dir :: separator());
  }

	function test_explode_absolute_path()
	{
		$path = dir :: explode_path('/tmp\../tmp/wow////hey/');
		
		$this->assertEqual(sizeof($path), 4);
		
		$this->assertEqual($path[0], '');
		$this->assertEqual($path[1], 'tmp');
		$this->assertEqual($path[2], 'wow');
		$this->assertEqual($path[3], 'hey');
		
		$path = dir :: explode_path('/tmp\../tmp/wow////hey'); // no trailing slash
		
		$this->assertEqual(sizeof($path), 4);
		
		$this->assertEqual($path[0], '');
		$this->assertEqual($path[1], 'tmp');
		$this->assertEqual($path[2], 'wow');
		$this->assertEqual($path[3], 'hey');		
	}
	
	function test_explode_relative_path()
	{
		$path = dir :: explode_path('tmp\../tmp/wow////hey/');
		
		$this->assertEqual(sizeof($path), 3);
		
		$this->assertEqual($path[0], 'tmp');
		$this->assertEqual($path[1], 'wow');
		$this->assertEqual($path[2], 'hey');
		
		$path = dir :: explode_path('tmp\../tmp/wow////hey'); // no trailing slash
		
		$this->assertEqual(sizeof($path), 3);
		
		$this->assertEqual($path[0], 'tmp');
		$this->assertEqual($path[1], 'wow');
		$this->assertEqual($path[2], 'hey');
	}
        
  function test_mkdir_absolute_path() 
  {
  	dir :: rm(TEST_DIR_ABSOLUTE_PATH . '/tmp/');
  	
  	$this->assertFalse(is_dir(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/hey/'));
  	  	
  	dir :: mkdir(TEST_DIR_ABSOLUTE_PATH . '/./tmp\../tmp/wow////hey/');
  	
  	$this->assertTrue(is_dir(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/hey/')); 	
  }    

  function test_mkdir_absolute_path_no_trailing_slash() 
  {  	
  	dir :: rm(TEST_DIR_ABSOLUTE_PATH . '/tmp/');
  	
  	$this->assertFalse(is_dir(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/hey/'));
  	
  	dir :: mkdir(TEST_DIR_ABSOLUTE_PATH . '/./tmp\../tmp/wow////hey');
  	
  	$this->assertTrue(is_dir(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/hey/'));
  }    
  
  function test_mkdir_relative_path()
  { 
  	dir :: rm(TEST_DIR_RELATIVE_PATH . '/tmp/');
  	
  	$this->assertFalse(is_dir(TEST_DIR_RELATIVE_PATH . '/tmp/wow/hey/'));
  	
  	dir :: mkdir(TEST_DIR_RELATIVE_PATH . '/./tmp\../tmp/wow////hey/');
  	
  	$this->assertTrue(is_dir(TEST_DIR_RELATIVE_PATH . '/tmp/wow/hey/'));
  }    

  function test_mkdir_relative_path_no_trailing_slash()
  { 
  	dir :: rm(TEST_DIR_RELATIVE_PATH . '/tmp/');
  	
  	$this->assertFalse(is_dir(TEST_DIR_RELATIVE_PATH . '/tmp/wow/hey/'));
  	
  	dir :: mkdir(TEST_DIR_RELATIVE_PATH . '/./tmp\../tmp/wow////hey');
  	
  	$this->assertTrue(is_dir(TEST_DIR_RELATIVE_PATH . '/tmp/wow/hey/'));
  }
  
  function test_dirpath()
  {
  	$this->assertEqual(dir :: dirpath('/wow/test.txt'), dir :: clean_path('/wow'));
  	$this->assertEqual(dir :: dirpath('wow/hey/test.txt'), dir :: clean_path('wow/hey'));
  	$this->assertEqual(dir :: dirpath('test.txt'), 'test.txt');
  	$this->assertEqual(dir :: dirpath('/'), '');
  }
  
  function test_ls()
  {
  	$this->_create_file_system();
  	
  	$this->assertEqual(array('test1_1', 'test1_2', 'test1_3', 'wow'), dir :: ls(TEST_DIR_ABSOLUTE_PATH . '/tmp/'));
  	$this->assertEqual(array('hey', 'test2_1', 'test2_2', 'test2_3'), dir :: ls(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow'));
  	
  	$this->_remove_file_system();
  }
  
  function test_path()
  {
  	$this->assertEqual(dir :: path(array('test')), 'test');
  	$this->assertEqual(dir :: path(array('test', 'wow')), 'test' . dir :: separator() . 'wow');
  	$this->assertEqual(dir :: path(array('test', 'wow/')), 'test' . dir :: separator() . 'wow');
  	
  	$this->assertEqual(dir :: path(array('test'), true), 'test' . dir :: separator());
  	$this->assertEqual(dir :: path(array('test', 'wow'), true), 'test' . dir :: separator() . 'wow' . dir :: separator());
  }
  
  function test_chop()
  {
  	$this->assertEqual(dir :: chop('test'), 'test');
  	$this->assertEqual(dir :: chop('test/'), 'test');
  }
  
  function test_walk_dir()
  {
  	$this->_create_file_system();
  	
  	$mock =& new special_dir_walker($this);
  	
  	$mock->expectCallCount('walk', 9+2);
  	
  	$mock->expectArgumentsAt(0, 'walk', array(dir :: clean_path(TEST_DIR_ABSOLUTE_PATH . '/tmp'), 'test1_1', array('test', 'separator' => dir :: separator()), array(0)));
  	$mock->expectArgumentsAt(1, 'walk', array(dir :: clean_path(TEST_DIR_ABSOLUTE_PATH . '/tmp'), 'test1_2', array('test', 'separator' => dir :: separator()), array(0, 1)));
  	$mock->expectArgumentsAt(2, 'walk', array(dir :: clean_path(TEST_DIR_ABSOLUTE_PATH . '/tmp'), 'test1_3', array('test', 'separator' => dir :: separator()), array(0, 1, 2)));
  	$mock->expectArgumentsAt(3, 'walk', array(dir :: clean_path(TEST_DIR_ABSOLUTE_PATH . '/tmp'), 'wow', array('test', 'separator' => dir :: separator()), array(0, 1, 2, 3)));
  	$mock->expectArgumentsAt(4, 'walk', array(dir :: clean_path(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow'), 'hey', array('test', 'separator' => dir :: separator()), array(0, 1, 2, 3, 4)));
  	$mock->expectArgumentsAt(5, 'walk', array(dir :: clean_path(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/hey'), 'test3_1', array('test', 'separator' => dir :: separator()), array(0, 1, 2, 3, 4, 5)));
  	$mock->expectArgumentsAt(6, 'walk', array(dir :: clean_path(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/hey'), 'test3_2', array('test', 'separator' => dir :: separator()), array(0, 1, 2, 3, 4, 5, 6)));
  	$mock->expectArgumentsAt(7, 'walk', array(dir :: clean_path(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/hey'), 'test3_3', array('test', 'separator' => dir :: separator()), array(0, 1, 2, 3, 4, 5, 6, 7)));
  	$mock->expectArgumentsAt(8, 'walk', array(dir :: clean_path(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow'), 'test2_1', array('test', 'separator' => dir :: separator()), array(0, 1, 2, 3, 4, 5, 6, 7, 8)));
  	$mock->expectArgumentsAt(9, 'walk', array(dir :: clean_path(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow'), 'test2_2', array('test', 'separator' => dir :: separator()), array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9)));
  	$mock->expectArgumentsAt(10, 'walk', array(dir :: clean_path(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow'), 'test2_3', array('test', 'separator' => dir :: separator()), array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10)));
  	
  	$this->assertEqual(
  		dir :: walk_dir(TEST_DIR_ABSOLUTE_PATH . '/tmp/', array(&$mock, 'walk'), array('test')),
  		array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10)
  	);
  	
  	$mock->tally();
  	
  	$this->_remove_file_system();
  }
  
  function test_cp()
  {
  	$this->_create_file_system();
  	
  	$res = dir :: cp(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow', TEST_DIR_ABSOLUTE_PATH . '/tmp/cp');
  	sort($res);
  	
  	$this->assertEqual(
  		$res,
  		array(
  		'hey',
  		dir :: clean_path('hey/test3_1'),
  		dir :: clean_path('hey/test3_2'),
  		dir :: clean_path('hey/test3_3'),
  		'test2_1',
  		'test2_2',
  		'test2_3',
  		)
		);
  	
  	$this->assertEqual(
  		dir :: ls(TEST_DIR_ABSOLUTE_PATH . '/tmp/cp'),
  		dir :: ls(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow'));

  	$this->assertEqual(
  		dir :: ls(TEST_DIR_ABSOLUTE_PATH . '/tmp/cp/hey'),
  		dir :: ls(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/hey'));
  	  	
  	$this->_remove_file_system();
  }
  
  function test_cp_as_shild()
  {
  	$this->_create_file_system();
  	
  	dir :: cp(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow', TEST_DIR_ABSOLUTE_PATH . '/tmp/cp', true);
		  	
  	$this->assertEqual(
  		dir :: ls(TEST_DIR_ABSOLUTE_PATH . '/tmp/cp/wow/'),
  		dir :: ls(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow'));

  	$this->assertEqual(
  		dir :: ls(TEST_DIR_ABSOLUTE_PATH . '/tmp/cp/wow/hey'),
  		dir :: ls(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/hey'));
  	  	
  	$this->_remove_file_system();
  }
  
  function test_cp_with_exclude()
  {
  	$this->_create_file_system();
  	
  	$res = dir :: cp(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow', TEST_DIR_ABSOLUTE_PATH . '/tmp/cp', false, '/hey/');
  	
  	$this->assertEqual(
  		$res,
  		array('test2_1', 'test2_2', 'test2_3')
  	);
  	
  	$this->assertEqual(
  		$res,
  		dir :: ls(TEST_DIR_ABSOLUTE_PATH . '/tmp/cp/')
  	);

  	$this->assertFalse(is_dir(TEST_DIR_ABSOLUTE_PATH . '/tmp/cp/hey'));
  	  	
  	$this->_remove_file_system();
  }
  
  function test_find_subitems()
  {
  	$this->_create_file_system();
  	
  	$res = dir :: find_subitems(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/hey');
  	
  	$this->assertEqual(
  		$res, 
  		array(
  			dir :: clean_path(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/hey/test3_1'),
  			dir :: clean_path(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/hey/test3_2'),
  			dir :: clean_path(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/hey/test3_3')
  		)
  	);

  	$res = dir :: find_subitems(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/', 'f', '/^test2_1$/');
  	
  	$this->assertEqual(
  		$res, 
  		array(
  			dir :: clean_path(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/test2_2'),
  			dir :: clean_path(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/test2_3'),
  		)
  	);
  	
  	$this->_remove_file_system();
  }
  
  function test_recursive_find()
  {
  	$this->_create_file_system();
  	 
  	$res = dir :: recursive_find(TEST_DIR_ABSOLUTE_PATH . '/tmp/', 'test\d_1');

  	$this->assertEqual(
  		$res, 
  		array(
  			dir :: clean_path(TEST_DIR_ABSOLUTE_PATH . '/tmp/test1_1'),
  			dir :: clean_path(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/hey/test3_1'),
  			dir :: clean_path(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/test2_1'),
  		)
  	);
  	
  	$this->_remove_file_system();
  }

}

?>