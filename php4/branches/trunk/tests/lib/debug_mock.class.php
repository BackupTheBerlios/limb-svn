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

require_once(LIMB_DIR . '/core/lib/debug/debug.class.php');

class debug_mock extends debug
{
	var $expected_data = array();
	var $test = null;
	var $mock = null;
	
	function debug_mock()
	{
		parent :: debug();
	}
	
	function &instance()
	{
		$impl =& instantiate_object('debug_mock');
		return $impl;
	}
		
	function init(&$test, $wildcard = MOCK_WILDCARD)
	{
		$debug =& debug_mock :: instance();
		
		$debug->test =& $test;
		$debug->mock =& new SimpleMock($test, $wildcard, false);
	} 
	
  function expect_never_write() 
  {
  	$debug =& debug_mock :: instance();
  	
  	$debug->mock->expectNever('write');
  }
	
	function expect_write_error($message='', $params=array())
	{
		$debug =& debug_mock :: instance();
		
		$debug->_expect_write(DEBUG_LEVEL_ERROR, $message, $params);
	}

	function expect_write_warning($message='', $params=array())
	{
		$debug =& debug_mock :: instance();
		
		$debug->_expect_write(DEBUG_LEVEL_WARNING, $message, $params);
	}
	
	function expect_write_notice($message='', $params=array())
	{
		$debug =& debug_mock :: instance();
		
		$debug->_expect_write(DEBUG_LEVEL_NOTICE, $message, $params);
	}
	
	function _expect_write($verbosity_level, $message, $params)
	{
		$debug =& debug_mock :: instance();
		
		$debug->expected_data[] = array(
			'level' => $verbosity_level, 
			'message' => $message, 
			'params' => $params);
			
		$debug->mock->expectArgumentsAt(sizeof($this->expected_data)-1, 'write', array($verbosity_level, $message, $params));
		
		$debug->mock->expectCallCount('write', sizeof($this->expected_data));
	}

	function tally()
	{
		$debug =& debug_mock :: instance();
		
		$debug->mock->tally();
		$debug->expected_data = array();
	} 

	function &write()
	{
		$args = func_get_args();
					
		if(!$this->mock)
		{
			if($args[0] != DEBUG_TIMING_POINT)
				parent :: write($args[0], $args[1], $args[2], $args[3]);
			else
				parent :: write($args[0], $args[1]);
				
			return;
		}
		
		if($args[0] != DEBUG_TIMING_POINT)
		{	
			$this->mock->_invoke('write', array($args[0], $args[1], $args[3]));
			
			$call_parent = true;
			foreach($this->expected_data as $id => $data)
			{
				if(	$args[0] == $data['level'] && 
						$args[1] == $data['message'] &&
						$args[3] == $data['params'])
				{
					$call_parent = false;
					break;
				}
			}
			
			if($call_parent)
			{
				$this->test->fail('unexpected debug exception: [ ' . $args[1] . ' ]');
				
				parent :: write($args[0], $args[1], $args[2], $args[3]);
			}
		}
	} 	
} 

?>