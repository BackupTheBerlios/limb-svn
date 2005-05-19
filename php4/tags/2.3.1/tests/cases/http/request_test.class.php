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
require_once(LIMB_DIR . '/core/request/request.class.php');

class request_test extends LimbTestCase
{
  var $request;

  function setUp()
  {
    $this->request =& request :: instance();
  }

  function tearDown()
  {
  }

  function test_instance()
  {
    $this->assertReference($this->request, request :: instance());
  }

  function test_set_get_status()
  {
    $this->request->set_status(REQUEST_STATUS_SUCCESS);

    $this->assertEqual($this->request->get_status(), REQUEST_STATUS_SUCCESS);
  }

  function test_is_problem()
  {
    $this->request->set_status(REQUEST_STATUS_FAILURE | REQUEST_STATUS_FORM_NOT_VALID);
    $this->assertTrue($this->request->is_problem());
  }

  function test_is_success()
  {
    $this->request->set_status(REQUEST_STATUS_FORM_SUBMITTED | REQUEST_STATUS_FORM_DISPLAYED);
    $this->assertTrue($this->request->is_success());
  }
}

?>