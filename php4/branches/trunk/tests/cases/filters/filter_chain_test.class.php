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
require_once(LIMB_DIR . '/core/filters/filter_chain.class.php');
require_once(LIMB_DIR . '/core/filters/intercepting_filter.class.php');
require_once(LIMB_DIR . '/core/request/request.class.php');
require_once(LIMB_DIR . '/core/request/response.class.php');

Mock :: generate('intercepting_filter');
Mock :: generate('request');
Mock :: generate('response');

class special_intercepting_filter extends Mockintercepting_filter
{
  var $captured = array();

  function special_intercepting_filter(&$test)
  {
    parent :: Mockintercepting_filter($test);
  }

  function run(&$fc, &$request, &$response)
  {
    $this->captured['filter_chain'] =& $fc;
    $this->captured['request'] =& $request;
    $this->captured['response'] =& $response;

    $fc->next();

    return parent::run($fc, $request, $response);
  }
}

class output_filter1 extends Mockintercepting_filter
{
  function output_filter1(&$test)
  {
    parent :: Mockintercepting_filter($test);
  }

  function run(&$fc, &$request, &$response)
  {
    echo '<filter1>';

    $fc->next();

    echo '</filter1>';

    return parent::run($fc, $request, $response);
  }
}

class output_filter2 extends Mockintercepting_filter
{
  function output_filter2(&$test)
  {
    parent :: Mockintercepting_filter($test);
  }

  function run(&$fc, &$request, &$response)
  {
    echo '<filter2>';

    $fc->next();

    echo '</filter2>';

    return parent::run($fc, $request, $response);
  }
}

class output_filter3 extends Mockintercepting_filter
{
  function output_filter3(&$test)
  {
    parent :: Mockintercepting_filter($test);
  }

  function run(&$fc, &$request, &$response)
  {
    echo '<filter3>';

    $fc->next();

    echo '</filter3>';

    return parent::run($fc, $request, $response);
  }
}

class filter_chain_test extends LimbTestCase
{
  var $fc;
  var $request;
  var $response;

  function setUp()
  {
    $this->request =& new Mockrequest($this);
    $this->response =& new Mockresponse($this);
    $this->fc =& new filter_chain($this->request, $this->response);
  }

  function tearDown()
  {
  }

  function test_register_filter()
  {
    $ref = array('Mockintercepting_filter', $this);
    $this->fc->register_filter($ref);

    $this->assertTrue($this->fc->has_filter('Mockintercepting_filter'));
    $this->assertFalse($this->fc->has_filter('no_such_filter'));
  }

  function test_process()
  {
    $mock_filter =& new special_intercepting_filter($this);

    $this->fc->register_filter($mock_filter);

    $mock_filter->expectOnce('run');

    $this->fc->process();

    $this->assertReference($mock_filter->captured['filter_chain'], $this->fc);
    $this->assertReference($mock_filter->captured['request'], $this->request);
    $this->assertReference($mock_filter->captured['response'], $this->response);

    $mock_filter->tally();
  }

  function test_process_proper_nesting()
  {
    $f1 =& new output_filter1($this);
    $f2 =& new output_filter2($this);

    $this->fc->register_filter($f1);
    $this->fc->register_filter($f2);

    $f1->expectOnce('run');
    $f2->expectOnce('run');

    ob_start();

    $this->fc->process();

    $str = ob_get_contents();

    ob_end_clean();

    $this->assertEqual($str, '<filter1><filter2></filter2></filter1>');

    $f1->tally();
    $f2->tally();
  }
}

?>