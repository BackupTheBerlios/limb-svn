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
require_once(LIMB_DIR . '/class/core/ArrayDataset.class.php');
require_once(LIMB_DIR . '/class/validators/Validator.class.php');
require_once(LIMB_DIR . '/class/validators/rules/SizeRangeRule.class.php');
require_once(LIMB_DIR . '/class/validators/rules/RequiredRule.class.php');

Mock :: generate('ErrorList');

Mock :: generatePartial(
    'Validator',
    'ValidatorTestVersion2',
    array('_getErrorList'));

Mock :: generate('Rule');

class ValidatorTest extends LimbTestCase
{
  var $error_list = null;
  var $validator = null;

  function setUp()
  {
   $this->error_list = new MockErrorList($this);
   $this->validator = new ValidatorTestVersion2($this);
   $this->validator->setReturnValue('_getErrorList', $this->error_list);
  }

  function testValidateNoRules()
  {
    $this->assertTrue($this->validator->validate(new ArrayDataset()));
  }

  function testValidateTrue()
  {
    $r1 = new MockRule($this);

    $r1->expectOnce('validate');
    $r1->expectOnce('isValid');
    $r1->setReturnValue('isValid', true);

    $this->validator->addRule($r1);

    $this->validator->validate(new ArrayDataset());

    $this->assertTrue($this->validator->isValid());

    $r1->tally();
  }

  function testValidateFalse()
  {
    $r1 = new MockRule($this);
    $r2 = new MockRule($this);

    $r1->setReturnValue('isValid', true);
    $r2->setReturnValue('isValid', false);

    $this->validator->addRule($r1);
    $this->validator->addRule($r2);

    $this->validator->validate(new ArrayDataset());

    $this->assertFalse($this->validator->isValid());
  }

  function testAddError()
  {
    $this->validator->addError('test', 'error', array('1' => 'error'));
    $this->error_list->expectOnce('addError', array('test', 'error', array('1' => 'error')));
  }
}

?>