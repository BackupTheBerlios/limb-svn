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
require_once(LIMB_DIR . '/class/validators/rules/UsZipRule.class.php');

class UsZipRuleTest extends SingleFieldRuleTestCase
{
  function UsZipRuleTest()
  {
    parent :: SingleFieldRuleTestCase('usa zip rule test');
  }

  function testUsZipRuleValid()
  {
    $this->validator->addRule(new UsZipRule('test'));

    $data = new Dataspace();
    $data->set('test', '49007');

    $this->ErrorList->expectNever('addError');

    $this->validator->validate($data);
    $this->assertTrue($this->validator->isValid());
  }

  function testUsZipRuleValid2()
  {
    $this->validator->addRule(new UsZipRule('test'));

    $data = new Dataspace();
    $data->set('test', '49007 1234');

    $this->ErrorList->expectNever('addError');

    $this->validator->validate($data);
    $this->assertTrue($this->validator->isValid());
  }

  function testUsZipRuleInvalid1()
  {
    $this->validator->addRule(new UsZipRule('test'));

    $data = new Dataspace();
    $data->set('test', '490078');

    $this->ErrorList->expectOnce('addError', array('validation', 'ERROR_INVALID_ZIP_FORMAT', array('Field' => 'test'), NULL));

    $this->validator->validate($data);
    $this->assertFalse($this->validator->isValid());
  }

  function testUsZipRuleInvalid2()
  {
    $this->validator->addRule(new UsZipRule('test'));

    $data = new Dataspace();
    $data->set('test', '49007 23234');

    $this->ErrorList->expectOnce('addError', array('validation', 'ERROR_INVALID_ZIP_FORMAT', array('Field' => 'test'), NULL));

    $this->validator->validate($data);
    $this->assertFalse($this->validator->isValid());
  }

  function testUsZipRuleInvalid3()
  {
    $this->validator->addRule(new UsZipRule('test'));

    $data = new Dataspace();
    $data->set('test', '4t007 12d4');

    $this->ErrorList->expectOnce('addError', array('validation', 'ERROR_INVALID_ZIP_FORMAT', array('Field' => 'test'), NULL));

    $this->validator->validate($data);
    $this->assertFalse($this->validator->isValid());
  }

}

?>