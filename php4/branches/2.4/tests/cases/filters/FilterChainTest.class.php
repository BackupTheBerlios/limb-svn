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
require_once(LIMB_DIR . '/core/filters/FilterChain.class.php');
require_once(LIMB_DIR . '/core/filters/InterceptingFilter.interface.php');
require_once(LIMB_DIR . '/core/request/Request.class.php');
require_once(LIMB_DIR . '/core/request/HttpResponse.class.php');

Mock :: generate('InterceptingFilter');
Mock :: generate('Request');
Mock :: generate('HttpResponse');

class SpecialInterceptingFilter extends MockInterceptingFilter
{
  var $captured = array();

  function specialInterceptingFilter(&$test)
  {
    parent :: mockinterceptingFilter($test);
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

class OutputFilter1 extends MockInterceptingFilter
{
  function outputFilter1($test)
  {
    parent :: mockinterceptingFilter($test);
  }

  function run(&$fc, &$request, &$response)
  {
    echo '<filter1>';

    $fc->next();

    echo '</filter1>';

    return parent::run($fc, $request, $response);
  }
}

class OutputFilter2 extends MockInterceptingFilter
{
  function outputFilter2($test)
  {
    parent :: mockinterceptingFilter($test);
  }

  function run(&$fc, &$request, &$response)
  {
    echo '<filter2>';

    $fc->next();

    echo '</filter2>';

    return parent::run($fc, $request, $response);
  }
}

class OutputFilter3 extends MockInterceptingFilter
{
  function outputFilter3(&$test)
  {
    parent :: mockinterceptingFilter($test);
  }

  function run(&$fc, &$request, &$response)
  {
    echo '<filter3>';

    $fc->next();

    echo '</filter3>';

    return parent::run($fc, $request, $response);
  }
}

class FilterChainTest extends LimbTestCase
{
  var $fc;
  var $request;
  var $response;

  function setUp()
  {
    $this->request = new MockRequest($this);
    $this->response = new MockHttpResponse($this);
    $this->fc = new FilterChain($this->request, $this->response);
  }

  function tearDown()
  {
  }

  function testRegisterFilter()
  {
    $ref = array('MockInterceptingFilter', $this);
    $this->fc->registerFilter($ref);

    $this->assertTrue($this->fc->hasFilter('MockInterceptingFilter'));
    $this->assertFalse($this->fc->hasFilter('no_such_filter'));
  }

  function testProcess()
  {
    $mock_filter = new SpecialInterceptingFilter($this);

    $this->fc->registerFilter($mock_filter);

    $mock_filter->expectOnce('run');

    $this->fc->process();

    $this->assertIsA($mock_filter->captured['filter_chain'], 'FilterChain');
    $this->assertIsA($mock_filter->captured['request'], 'MockRequest');
    $this->assertIsA($mock_filter->captured['response'], 'MockHttpResponse');

    $mock_filter->tally();
  }

  function testProcessProperNesting()
  {
    $f1 = new OutputFilter1($this);
    $f2 = new OutputFilter2($this);

    $this->fc->registerFilter($f1);
    $this->fc->registerFilter($f2);

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