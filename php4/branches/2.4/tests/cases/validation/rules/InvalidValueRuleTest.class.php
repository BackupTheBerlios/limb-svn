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
require_once(WACT_ROOT . '/../tests/cases/validation/rules/singlefield.inc.php');
require_once(WACT_ROOT . '/datasource/dataspace.inc.php');

require_once(LIMB_DIR . '/class/validators/rules/InvalidValueRule.class.php');

class InvalidValueRuleTest extends SingleFieldRuleTestCase
{
  function InvalidValueRuleTest()
  {
    parent :: SingleFieldRuleTestCase('invalid value rule test');
  }

  function testInvalidValueRuleOkInt()
  {
    $this->validator->addRule(new InvalidValueRule('testfield', 0));

    $data = &new Dataspace();
    $data->set('testfield', 1);

    $this->ErrorList->expectNever('addError');

    $this->validator->validate($data);

    $this->assertTrue($this->validator->isValid());
  }

  function testInvalidValueRuleOkInt2()
  {
    $this->validator->addRule(new InvalidValueRule('testfield', 0));

    $data = &new Dataspace();
    $data->set('testfield', 'whatever');

    $this->ErrorList->expectNever('addError');

    $this->validator->validate($data);
    $this->assertTrue($this->validator->isValid());
  }


  function testInvalidValueRuleOkNull()
  {
    $this->validator->addRule(new InvalidValueRule('testfield', null));

    $data = &new Dataspace();
    $data->set('testfield', 'null');

    $this->ErrorList->expectNever('addError');

    $this->validator->validate($data);
    $this->assertTrue($this->validator->isValid());
  }

  function testInvalidValueRuleOkBool()
  {
    $this->validator->addRule(new InvalidValueRule('testfield', false));

    $data = &new Dataspace();
    $data->set('testfield', 'false');

    $this->ErrorList->expectNever('addError');

    $this->validator->validate($data);
    $this->assertTrue($this->validator->isValid());
  }

  function testInvalidValueRuleError()
  {
    $this->validator->addRule(new InvalidValueRule('testfield', 1));

    $data = &new Dataspace();
    $data->set('testfield', 1);

    $this->ErrorList->expectOnce('addError', array('validation', 'ERROR_INVALID_VALUE', array('Field' => 'testfield'), NULL));

    $this->validator->validate($data);
    $this->assertFalse($this->validator->isValid());
  }

  function testInvalidValueRuleError2()
  {
    $this->validator->addRule(new InvalidValueRule('testfield', 1));

    $data = &new Dataspace();
    $data->set('testfield', '1');

    $this->ErrorList->expectOnce('addError', array('validation', 'ERROR_INVALID_VALUE', array('Field' => 'testfield'), NULL));

    $this->validator->validate($data);
    $this->assertFalse($this->validator->isValid());
  }

}

?>