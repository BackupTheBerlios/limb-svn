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
require_once(LIMB_DIR . '/class/validators/rules/SizeRangeRule.class.php');

class SizeRangeRuleTest extends SingleFieldRuleTest
{
  function testSizeRangeRuleEmpty()
  {
    $this->validator->addRule(new SizeRangeRule('testfield', 10));

    $data = new Dataspace();

    $this->error_list->expectNever('addError');

    $this->validator->validate($data);
    $this->assertTrue($this->validator->isValid());
  }

  function testSizeRangeRuleBlank()
  {
    $this->validator->addRule(new SizeRangeRule('testfield', 5, 10));

    $data = new Dataspace();
    $data->set('testfield', '');

    $this->error_list->expectNever('addError');

    $this->validator->validate($data);
    $this->assertTrue($this->validator->isValid());
  }

  function testsizeRangeRuleZero()
  {
    $this->validator->addRule(new SizeRangeRule('testfield', 5, 10));

    $data = &new Dataspace();
    $data->set('testfield', '0');

    $this->error_list->expectOnce('addError', array('testfield', Strings :: get('size_too_small', 'error'), array()));

    $this->validator->validate($data);
    $this->assertFalse($this->validator->isValid());
  }

  function testsizeRangeRuleTooBig()
  {
    $this->validator->addRule(new SizeRangeRule('testfield', 10));

    $data = &new Dataspace();
    $data->set('testfield', '12345678901234567890');

    $this->error_list->expectOnce('addError', array('testfield', Strings :: get('size_too_big', 'error'), array()));

    $this->validator->validate($data);
    $this->assertFalse($this->validator->isValid());
  }

  function testsizeRangeRuleTooBig2()
  {
    $this->validator->addRule(new SizeRangeRule('testfield', 5, 10));

    $data = &new Dataspace();
    $data->set('testfield', '12345678901234567890');

    $this->error_list->expectOnce('addError', array('testfield', Strings :: get('size_too_big', 'error'), array()));

    $this->validator->validate($data);
    $this->assertFalse($this->validator->isValid());
  }

  function testsizeRangeRuleTooSmall()
  {
    $this->validator->addRule(new SizeRangeRule('testfield', 30, 100));

    $data = &new Dataspace();
    $data->set('testfield', '12345678901234567890');

    $this->error_list->expectOnce('addError', array('testfield', Strings :: get('size_too_small', 'error'), array()));

    $this->validator->validate($data);
    $this->assertFalse($this->validator->isValid());
  }
}

?>