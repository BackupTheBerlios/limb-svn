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
require_once(LIMB_DIR . '/class/validators/rules/Rule.class.php');
require_once(LIMB_DIR . '/class/core/Dataspace.class.php');
require_once(LIMB_DIR . '/class/validators/rules/RequiredRule.class.php');

class RequiredRuleTest extends SingleFieldRuleTest
{
  function testRequiredRuleTrue()
  {
    $this->validator->addRule(new RequiredRule('testfield'));

    $data = &new Dataspace();
    $data->set('testfield', true);

    $this->error_list->expectNever('addError');

    $this->validator->validate($data);
    $this->assertTrue($this->validator->isValid());
  }

  function testRequiredRuleZero()
  {
    $this->validator->addRule(new RequiredRule('testfield'));

    $data = new Dataspace();
    $data->set('testfield', 0);

    $this->error_list->expectNever('addError');

    $this->validator->validate($data);
    $this->assertTrue($this->validator->isValid());
  }

  function testRequiredRuleZero2()
  {
    $this->validator->addRule(new RequiredRule('testfield'));

    $data = new Dataspace();
    $data->set('testfield', '0');

    $this->error_list->expectNever('addError');

    $this->validator->validate($data);
    $this->assertTrue($this->validator->isValid());
  }

  function testRequiredRuleFalse()
  {
    $this->validator->addRule(new RequiredRule('testfield'));

    $data = new Dataspace();
    $data->set('testfield', false);

    $this->error_list->expectNever('addError');

    $this->validator->validate($data);
    $this->assertTrue($this->validator->isValid());
  }

  function testRequiredRuleZeroLengthString()
  {
    $this->validator->addRule(new RequiredRule('testfield'));

    $data = &new Dataspace();
    $data->set('testfield', '');

    $this->error_list->expectOnce('addError', array('testfield', Strings :: get('error_required', 'error'), array()));

    $this->validator->validate($data);
    $this->assertFalse($this->validator->isValid());
  }

  function testRequiredRuleFailure()
  {
    $this->validator->addRule(new RequiredRule('testfield'));

    $data = &new Dataspace();

    $this->error_list->expectOnce('addError', array('testfield', Strings :: get('error_required', 'error'), array()));

    $this->validator->validate($data);
    $this->assertFalse($this->validator->isValid());
  }
}

?>