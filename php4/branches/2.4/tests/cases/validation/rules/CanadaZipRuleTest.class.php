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

require_once(LIMB_DIR . '/class/validators/rules/CanadaZipRule.class.php');

class CanadaZipRuleTest extends SingleFieldRuleTestCase
{
  function CanadaZipRuleTest()
  {
    parent :: SingleFieldRuleTestCase('canada zip rule test');
  }

  function testCanadaZipRuleValid()
  {
    $this->validator->addRule(new CanadaZipRule('test'));

    $data = new Dataspace();
    $data->set('test', 'H2V 2K1');

    $this->ErrorList->expectNever('addError');

    $this->validator->validate($data);
    $this->assertTrue($this->validator->isValid());
  }

  function testCanadaZipRuleValid2()
  {
    $this->validator->addRule(new CanadaZipRule('test'));

    $data = new Dataspace();
    $data->set('test', 'h2v 2k1');

    $this->ErrorList->expectNever('addError');

    $this->validator->validate($data);
    $this->assertTrue($this->validator->isValid());
  }

  function testCanadaZipRuleInvalid1()
  {
    $this->validator->addRule(new CanadaZipRule('test'));

    $data = new Dataspace();
    $data->set('test', '490078');

    $this->ErrorList->expectOnce('addError', array('validation', 'ERROR_INVALID_ZIP_FORMAT', array('Field' => 'test'), NULL));

    $this->validator->validate($data);
    $this->assertFalse($this->validator->isValid());
  }

  function testCanadaZipRuleInvalid2()
  {
    $this->validator->addRule(new CanadaZipRule('test'));

    $data = new Dataspace();
    $data->set('test', '324 256');

    $this->ErrorList->expectOnce('addError', array('validation', 'ERROR_INVALID_ZIP_FORMAT', array('Field' => 'test'), NULL));

    $this->validator->validate($data);
    $this->assertFalse($this->validator->isValid());
  }

  function testCanadaZipRuleInvalid3()
  {
    $this->validator->addRule(new CanadaZipRule('test'));

    $data = new Dataspace();
    $data->set('test', 'H2V2K1');

    $this->ErrorList->expectOnce('addError', array('validation', 'ERROR_INVALID_ZIP_FORMAT', array('Field' => 'test'), NULL));

    $this->validator->validate($data);
    $this->assertFalse($this->validator->isValid());
  }
}

?>