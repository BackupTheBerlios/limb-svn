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
require_once(LIMB_DIR . '/class/validators/rules/LocaleDateRule.class.php');
require_once(LIMB_DIR . '/class/lib/db/DbTable.class.php');

class LocaleDateRuleTest extends SingleFieldRuleTest
{
  function testLocaleDateRuleCorrect()
  {
    $this->validator->addRule(new LocaleDateRule('test', 'en'));

    $data = new Dataspace();
    $data->set('test', '02/28/2003');

    $this->error_list->expectNever('addError');

    $this->validator->validate($data);
    $this->assertTrue($this->validator->isValid());
  }

  function testLocaleDateRuleErrorLeapYear()
  {
    $this->validator->addRule(new LocaleDateRule('test', 'en'));

    $data = new Dataspace();
    $data->set('test', '02/29/2003');

    $this->error_list->expectOnce('addError', array('test', 'INVALID_DATE', array()));

    $this->validator->validate($data);
    $this->assertFalse($this->validator->isValid());
  }

  function testErrorLocaleMonthPosition()
  {
    $this->validator->addRule(new LocaleDateRule('test', 'en'));

    $data = new Dataspace();
    $data->set('test', '28/12/2003');

    $this->error_list->expectOnce('addError', array('test', 'INVALID_DATE', array()));

    $this->validator->validate($data);
    $this->assertFalse($this->validator->isValid());
  }

  function testLocaleDateRuleErrorFormat()
  {
    $this->validator->addRule(new LocaleDateRule('test', 'en'));

    $data = new Dataspace();
    $data->set('test', '02-29-2003');

    $this->error_list->expectOnce('addError', array('test', 'INVALID_DATE', array()));

    $this->validator->validate($data);
    $this->assertFalse($this->validator->isValid());
  }

  function testLocaleDateRuleError()
  {
    $this->validator->addRule(new LocaleDateRule('test', 'en'));

    $data = new Dataspace();
    $data->set('test', '02jjklklak/sdsdskj34-sdsdsjkjkj78');

    $this->error_list->expectOnce('addError', array('test', 'INVALID_DATE', array()));

    $this->validator->validate($data);
    $this->assertFalse($this->validator->isValid());
  }
}

?>