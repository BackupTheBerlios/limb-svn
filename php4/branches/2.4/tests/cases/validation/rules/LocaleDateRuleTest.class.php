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
require_once(LIMB_DIR . '/core/validators/rules/LocaleDateRule.class.php');
require_once(LIMB_DIR . '/core/db/LimbDbTable.class.php');

class LocaleDateRuleTest extends SingleFieldRuleTestCase
{
  function LocaleDateRuleTest()
  {
    parent :: SingleFieldRuleTestCase('locale date rule test');
  }

  function testLocaleDateRuleCorrect()
  {
    $this->validator->addRule(new LocaleDateRule('test', 'en'));

    $data = new Dataspace();
    $data->set('test', '02/28/2003');

    $this->ErrorList->expectNever('addError');

    $this->validator->validate($data);
    $this->assertTrue($this->validator->isValid());
  }

  function testLocaleDateRuleErrorLeapYear()
  {
    $this->validator->addRule(new LocaleDateRule('test', 'en'));

    $data = new Dataspace();
    $data->set('test', '02/29/2003');

    $this->ErrorList->expectOnce('addError', array('validation', 'INVALID_DATE', array('Field' => 'test'), NULL));

    $this->validator->validate($data);
    $this->assertFalse($this->validator->isValid());
  }

  function testErrorLocaleMonthPosition()
  {
    $this->validator->addRule(new LocaleDateRule('test', 'en'));

    $data = new Dataspace();
    $data->set('test', '28/12/2003');

    $this->ErrorList->expectOnce('addError', array('validation', 'INVALID_DATE', array('Field' => 'test'), NULL));

    $this->validator->validate($data);
    $this->assertFalse($this->validator->isValid());
  }

  function testLocaleDateRuleErrorFormat()
  {
    $this->validator->addRule(new LocaleDateRule('test', 'en'));

    $data = new Dataspace();
    $data->set('test', '02-29-2003');

    $this->ErrorList->expectOnce('addError', array('validation', 'INVALID_DATE', array('Field' => 'test'), NULL));

    $this->validator->validate($data);
    $this->assertFalse($this->validator->isValid());
  }

  function testLocaleDateRuleError()
  {
    $this->validator->addRule(new LocaleDateRule('test', 'en'));

    $data = new Dataspace();
    $data->set('test', '02jjklklak/sdsdskj34-sdsdsjkjkj78');

    $this->ErrorList->expectOnce('addError', array('validation', 'INVALID_DATE', array('Field' => 'test'), NULL));

    $this->validator->validate($data);
    $this->assertFalse($this->validator->isValid());
  }
}

?>