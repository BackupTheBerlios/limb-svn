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
require_once(LIMB_DIR . '/class/validators/rules/EmailRule.class.php');

class EmailRuleTest extends SingleFieldRuleTest
{
  function testEmailRuleValid()
  {
    $this->validator->addRule(new EmailRule('test'));

    $data = new Dataspace();
    $data->set('test', 'billgates@microsoft.com');

    $this->error_list->expectNever('addError');

    $this->validator->validate($data);
    $this->assertTrue($this->validator->isValid());
  }

  function testEmailRuleValid2()
  {
    $this->validator->addRule(new EmailRule('test'));

    $data = new Dataspace();
    $data->set('test', 'billgates_@microsoft.com');

    $this->error_list->expectNever('addError');

    $this->validator->validate($data);
    $this->assertTrue($this->validator->isValid());
  }

  function testEmailRuleValid3()
  {
    $this->validator->addRule(new EmailRule('test'));

    $data = new Dataspace();
    $data->set('test', 'bill_gates_@microsoft.com');

    $this->error_list->expectNever('addError');

    $this->validator->validate($data);
    $this->assertTrue($this->validator->isValid());
  }

  function testEmailRuleValid4()
  {
    $this->validator->addRule(new EmailRule('test'));

    $data = new Dataspace();
    $data->set('test', 'bill-gates@microsoft.com');

    $this->error_list->expectNever('addError');

    $this->validator->validate($data);
    $this->assertTrue($this->validator->isValid());
  }

  function testEmailRuleInvalidUser()
  {
    $this->validator->addRule(new EmailRule('testfield'));

    $data = new Dataspace();
    $data->set('testfield', 'bill(y!)gates@microsoft.com');

    $this->error_list->expectOnce('addError', array('testfield', Strings :: get('invalid_email', 'error'), array()));

    $this->validator->validate($data);
    $this->assertFalse($this->validator->isValid());
  }

  function testEmailRuleInvalidUser2()
  {
    $this->validator->addRule(new EmailRule('testfield'));

    $data = new Dataspace();
    $data->set('testfield', '_bill.gates@microsoft.com');

    $this->error_list->expectOnce('addError', array('testfield', Strings :: get('invalid_email', 'error'), array()));

    $this->validator->validate($data);
    $this->assertFalse($this->validator->isValid());
  }

  function testEmailUserInvalidDomain()
  {
    $this->validator->addRule(new EmailRule('testfield'));

    $data = new Dataspace();
    $data->set('testfield', 'billgates@micro$oft.com');

    $this->error_list->expectOnce('addError', array('testfield', Strings :: get('bad_domain_characters', 'error'), array()));

    $this->validator->validate($data);
    $this->assertFalse($this->validator->isValid());
  }
}

?>