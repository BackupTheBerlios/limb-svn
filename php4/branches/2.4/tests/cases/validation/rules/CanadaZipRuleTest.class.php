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
require_once(LIMB_DIR . '/class/validators/rules/CanadaZipRule.class.php');

class CanadaZipRuleTest extends SingleFieldRuleTest
{
  function testCanadaZipRuleValid()
  {
    $this->validator->addRule(new CanadaZipRule('test'));

    $data = new Dataspace();
    $data->set('test', 'H2V 2K1');

    $this->error_list->expectNever('addError');

    $this->validator->validate($data);
    $this->assertTrue($this->validator->isValid());
  }

  function testCanadaZipRuleValid2()
  {
    $this->validator->addRule(new CanadaZipRule('test'));

    $data = new Dataspace();
    $data->set('test', 'h2v 2k1');

    $this->error_list->expectNever('addError');

    $this->validator->validate($data);
    $this->assertTrue($this->validator->isValid());
  }

  function testCanadaZipRuleInvalid1()
  {
    $this->validator->addRule(new CanadaZipRule('test'));

    $data = new Dataspace();
    $data->set('test', '490078');

    $this->error_list->expectOnce('addError', array('test', Strings :: get('error_invalid_zip_format', 'error'), array()));

    $this->validator->validate($data);
    $this->assertFalse($this->validator->isValid());
  }

  function testCanadaZipRuleInvalid2()
  {
    $this->validator->addRule(new CanadaZipRule('test'));

    $data = new Dataspace();
    $data->set('test', '324 256');

    $this->error_list->expectOnce('addError', array('test', Strings :: get('error_invalid_zip_format', 'error'), array()));

    $this->validator->validate($data);
    $this->assertFalse($this->validator->isValid());
  }

  function testCanadaZipRuleInvalid3()
  {
    $this->validator->addRule(new CanadaZipRule('test'));

    $data = new Dataspace();
    $data->set('test', 'H2V2K1');

    $this->error_list->expectOnce('addError', array('test', Strings :: get('error_invalid_zip_format', 'error'), array()));

    $this->validator->validate($data);
    $this->assertFalse($this->validator->isValid());
  }
}

?>