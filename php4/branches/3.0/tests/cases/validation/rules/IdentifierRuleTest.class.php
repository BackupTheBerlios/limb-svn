<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: CanadaZipRuleTest.class.php 1028 2005-01-18 11:06:55Z pachanga $
*
***********************************************************************************/
require_once(WACT_ROOT . '/../tests/cases/validation/rules/singlefield.inc.php');
require_once(WACT_ROOT . '/datasource/dataspace.inc.php');

require_once(LIMB_DIR . '/core/validators/rules/IdentifierRule.class.php');

class IdentifierRuleTest extends SingleFieldRuleTestCase
{
  function IdentifierRuleTest()
  {
    parent :: SingleFieldRuleTestCase('identifier rule test');
  }

  function testValid()
  {
    $this->validator->addRule(new IdentifierRule('test'));

    $data = new Dataspace();
    $data->set('test', 'test');

    $this->ErrorList->expectNever('addError');

    $this->validator->validate($data);
    $this->assertTrue($this->validator->isValid());
  }

  function testValid2()
  {
    $this->validator->addRule(new IdentifierRule('test'));

    $data = new Dataspace();
    $data->set('test', 'test456');

    $this->ErrorList->expectNever('addError');

    $this->validator->validate($data);
    $this->assertTrue($this->validator->isValid());
  }

  function testNotValidContainsSpace()
  {
    $this->validator->addRule(new IdentifierRule('test'));

    $data = new Dataspace();
    $data->set('test', 'test test');

    $this->ErrorList->expectOnce('addError', array('validation', 'INVALID', array('Field' => 'test'), NULL));

    $this->validator->validate($data);
    $this->assertFalse($this->validator->isValid());
  }

  function testNotValidContainsSlash()
  {
    $this->validator->addRule(new IdentifierRule('test'));

    $data = new Dataspace();
    $data->set('test', 'test/test');

    $this->ErrorList->expectOnce('addError', array('validation', 'INVALID', array('Field' => 'test'), NULL));

    $this->validator->validate($data);
    $this->assertFalse($this->validator->isValid());
  }
}

?>