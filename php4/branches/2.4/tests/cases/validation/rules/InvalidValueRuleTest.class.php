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
require_once(dirname(__FILE__) . '/SingleFieldRuleTest.class.php');
require_once(LIMB_DIR . '/class/core/Dataspace.class.php');
require_once(LIMB_DIR . '/class/validators/rules/InvalidValueRule.class.php');

class InvalidValueRuleTest extends SingleFieldRuleTest
{
  function testInvalidValueRuleOkInt()
  {
    $this->validator->addRule(new InvalidValueRule('testfield', 0));

    $data = &new Dataspace();
    $data->set('testfield', 1);

    $this->error_list->expectNever('addError');

    $this->validator->validate($data);
    $this->assertTrue($this->validator->isValid());
  }

  function testInvalidValueRuleOkInt2()
  {
    $this->validator->addRule(new InvalidValueRule('testfield', 0));

    $data = &new Dataspace();
    $data->set('testfield', 'whatever');

    $this->error_list->expectNever('addError');

    $this->validator->validate($data);
    $this->assertTrue($this->validator->isValid());
  }


  function testInvalidValueRuleOkNull()
  {
    $this->validator->addRule(new InvalidValueRule('testfield', null));

    $data = &new Dataspace();
    $data->set('testfield', 'null');

    $this->error_list->expectNever('addError');

    $this->validator->validate($data);
    $this->assertTrue($this->validator->isValid());
  }

  function testInvalidValueRuleOkBool()
  {
    $this->validator->addRule(new InvalidValueRule('testfield', false));

    $data = &new Dataspace();
    $data->set('testfield', 'false');

    $this->error_list->expectNever('addError');

    $this->validator->validate($data);
    $this->assertTrue($this->validator->isValid());
  }

  function testInavlidValueRuleError()
  {
    $this->validator->addRule(new InvalidValueRule('testfield', 1));

    $data = &new Dataspace();
    $data->set('testfield', 1);

    $this->error_list->expectOnce('addError', array('testfield', Strings :: get('error_invalid_value', 'error'), array()));

    $this->validator->validate($data);
    $this->assertFalse($this->validator->isValid());
  }

  function testInavlidValueRuleError2()
  {
    $this->validator->addRule(new InvalidValueRule('testfield', 1));

    $data = &new Dataspace();
    $data->set('testfield', '1');

    $this->error_list->expectOnce('addError', array('testfield', Strings :: get('error_invalid_value', 'error'), array()));

    $this->validator->validate($data);
    $this->assertFalse($this->validator->isValid());
  }

}

?>