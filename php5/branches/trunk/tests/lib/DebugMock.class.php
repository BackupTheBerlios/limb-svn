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
require_once(LIMB_DIR . '/class/lib/error/Debug.class.php');

class DebugMock extends Debug
{
  protected $expected_data = array();
  protected $test = null;
  protected $mock = null;

  static public function init($test, $wildcard = MOCK_WILDCARD)
  {
    $debug = self :: instance();

    $debug->test = $test;
    $debug->mock = new SimpleMock($test, $wildcard, false);
  }

  static public function expectWriteException($e)
  {
    if($e instanceof LimbException)
      self :: _expectWrite(self :: LEVEL_ERROR, $e->getMessage(), $e->getAdditionalParams());
    else
      self :: _expectWrite(self :: LEVEL_ERROR, $e->getMessage());
  }

  static public function expectWriteError($message='', $params=array())
  {
    self :: _expectWrite(self :: LEVEL_ERROR, $message, $params);
  }

  static public function expectWriteWarning($message='', $params=array())
  {
    self :: _expectWrite(self :: LEVEL_WARNING, $message, $params);
  }

  static public function expectWriteNotice($message='', $params=array())
  {
    self :: _expectWrite(self :: LEVEL_NOTICE, $message, $params);
  }

  static protected function _expectWrite($verbosity_level, $message, $params)
  {
    $debug = self :: instance();

    $debug->expected_data[] = array(
      'level' => $verbosity_level,
      'message' => $message,
      'params' => $params);

    $debug->mock->expectArgumentsAt(sizeof($debug->expected_data)-1, 'write', array($verbosity_level, $message, $params));

    $debug->mock->expectCallCount('write', sizeof($debug->expected_data));
  }

  static public function tally()
  {
    $debug = self :: instance();

    $debug->mock->tally();
    $debug->expected_data = array();
  }

  protected function write($verbosity_level, $string, $code_line = '', $params = array())
  {
    if(!$this->mock)
    {
      if($verbosity_level != self :: TIMING_POINT)
        parent :: write($verbosity_level, $string, $code_line, $params);
      else
        parent :: write($verbosity_level, $string);

      return;
    }

    if($verbosity_level != self :: TIMING_POINT)
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