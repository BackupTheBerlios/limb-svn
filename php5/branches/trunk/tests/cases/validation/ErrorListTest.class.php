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
require_once(LIMB_DIR . '/class/validators/ErrorList.class.php');

class ErrorListTest extends LimbTestCase
{
  function errorListTest()
  {
    parent :: limbTestCase();
  }

  function setUp()
  {
    $e =& ErrorList :: instance();
    $e->reset();
  }

  function tearDown()
  {
    $e =& ErrorList :: instance();
    $e->reset();
  }

  function testInstance()
  {
    $e =& ErrorList :: instance();

    $this->assertNotNull($e);
    $this->assertIsA($e, 'ErrorList');

    $e2 =& ErrorList :: instance();

    $this->assertTrue($e === $e2);
  }

  function testAddError()
  {
    $e =& ErrorList :: instance();

    $e->addError('test', 'error');

    $errors = $e->getErrors('test');

    $this->assertEqual(sizeof($errors), 1);
    $this->assertEqual($errors[0]['error'], 'error');

    $e->addError('test', 'error2', array('param' => 1));

    $errors = $e->getErrors('test');

    $this->assertEqual(sizeof($errors), 2);
    $this->assertEqual($errors[1]['error'], 'error2');
    $this->assertEqual($errors[1]['params']['param'], 1);

    $errors = $e->getErrors('no_errors');
    $this->assertNull($errors);
  }
}

?>