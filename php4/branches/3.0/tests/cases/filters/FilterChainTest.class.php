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
require_once(LIMB_DIR . '/core/request/Request.class.php');
require_once(LIMB_DIR . '/core/request/HttpResponse.class.php');

Mock :: generate('InterceptingFilter');
Mock :: generate('Request');
Mock :: generate('HttpResponse');

class InterceptingFilterStub
{
  var $captured = array();
  var $run = false;

  function run(&$fc, &$request, &$response)
  {
    $this->run = true;
    $this->captured['filter_chain'] =& $fc;
    $this->captured['request'] =& $request;
    $this->captured['response'] =& $response;
    $this->captured['context'] =& $context;

    $fc->next();
  }
}

class OutputFilter1
{
  function run(&$fc, &$request, &$response)
  {
    echo '<filter1>';
    $fc->next();
    echo '</filter1>';
  }
}

class OutputFilter2
{
  function run(&$fc, &$request, &$response)
  {
    echo '<filter2>';
    $fc->next();
    echo '</filter2>';
  }
}

class OutputFilter3
{
  function run(&$fc, &$request, &$response)
  {
    echo '<filter3>';
    $fc->next();
    echo '</filter3>';
  }
}

class FilterChainTest extends LimbTestCase
{
  var $fc;
  var $request;
  var $response;
  var $context;

  function FilterChainTest()
  {
    parent :: LimbTestCase(__FILE__);
  }

  function setUp()
  {
    $this->request = new MockRequest($this);
    $this->response = new MockHttpResponse($this);
    $this->fc = new FilterChain($this->request, $this->response);
  }

  function testRegisterFilter()
  {
    $ref = new LimbHandle('InterceptingFilterStub');
    $this->fc->registerFilter($ref);

    $this->assertTrue($this->fc->hasFilter('InterceptingFilterStub'));
    $this->assertFalse($this->fc->hasFilter('no_such_filter'));
  }

  function testProcess()
  {
    $mock_filter = new InterceptingFilterStub();

    $this->fc->registerFilter($mock_filter);

    $this->assertFalse($mock_filter->run);

    $this->fc->process();

    $this->assertTrue($mock_filter->run);

    $this->assertIsA($mock_filter->captured['filter_chain'], 'FilterChain');
    $this->assertIsA($mock_filter->captured['request'], 'MockRequest');
    $this->assertIsA($mock_filter->captured['response'], 'MockHttpResponse');
  }

  function testProcessProperNesting()
  {
    $f1 = new OutputFilter1();
    $f2 = new OutputFilter2();

    $this->fc->registerFilter($f1);
    $this->fc->registerFilter($f2);

    ob_start();

    $this->fc->process();

    $str = ob_get_contents();
    ob_end_clean();

    $this->assertEqual($str, '<filter1><filter2></filter2></filter1>');
  }
}

?>