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
require_once(LIMB_DIR . '/class/error/Debug.class.php');

class DebugMock extends Debug
{
  var $expected_data = array();
  var $test = null;
  var $mock = null;

  function init(&$test, $wildcard = MOCK_WILDCARD)
  {
    $debug =& DebugMock :: instance();

    $debug->test = $test;
    $debug->mock = new SimpleMock($test, $wildcard, false);
  }

  function expectWriteException($e)
  {
    if(is_a($e, 'LimbException'))
      DebugMock :: _expectWrite(DEBUG_LEVEL_ERROR,
                                $e->getMessage(),
                                $e->getAdditionalParams());
    else
      DebugMock :: _expectWrite(DEBUG_LEVEL_ERROR,
                                $e->getMessage());
  }

  function expectWriteError($message='', $params=array())
  {
    DebugMock :: _expectWrite(DEBUG_LEVEL_ERROR,
                              $message,
                              $params);
  }

  function expectWriteWarning($message='', $params=array())
  {
    DebugMock :: _expectWrite(DEBUG_LEVEL_WARNING,
                              $message,
                              $params);
  }

  function expectWriteNotice($message='', $params=array())
  {
    DebugMock :: _expectWrite(DEBUG_LEVEL_NOTICE,
                              $message,
                              $params);
  }

  function _expectWrite($verbosity_level, $message, $params)
  {
    $debug =& DebugMock :: instance();

    $debug->expected_data[] = array(
      'level' => $verbosity_level,
      'message' => $message,
      'params' => $params);

    $debug->mock->expectArgumentsAt(sizeof($debug->expected_data)-1, 'write', array($verbosity_level, $message, $params));

    $debug->mock->expectCallCount('write', sizeof($debug->expected_data));
  }

  function tally()
  {
    $debug =& DebugMock :: instance();

    $debug->mock->tally();
    $debug->expected_data = array();
  }

  function write($verbosity_level, $string, $code_line = '', $params = array())
  {
    if(!$this->mock)
    {
      if($verbosity_level != DEBUG_TIMING_POINT)
        parent :: write($verbosity_level, $string, $code_line, $params);
      else
        parent :: write($verbosity_level, $string);

      return;
    }

    if($verbosity_level != DEBUG_TIMING_POINT)
    {
      $this->mock->_invoke('write', array($verbosity_level, $string, $params));

      $call_parent = true;
      foreach($this->expected_data as $id => $data)
      {
        if(	$verbosity_level == $data['level'] &&
            $string == $data['message'] &&
            $params == $data['params'])
        {
          $call_parent = false;
          break;
        }
      }

      if($call_parent)
      {
        $this->test->fail('unexpected debug exception: [ ' . $string . ' ]');

        parent :: write($verbosity_level, $string, $code_line, $params);
      }
    }
  }
}

?>