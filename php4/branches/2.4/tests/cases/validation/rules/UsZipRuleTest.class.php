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
require_once(LIMB_DIR . '/class/validators/rules/UsZipRule.class.php');

class UsZipRuleTest extends SingleFieldRuleTest
{
  function testUsZipRuleValid()
  {
    $this->validator->addRule(new UsZipRule('test'));

    $data = new Dataspace();
    $data->set('test', '49007');

    $this->error_list->expectNever('addError');

    $this->validator->validate($data);
    $this->assertTrue($this->validator->isValid());
  }

  function testUsZipRuleValid2()
  {
    $this->validator->addRule(new UsZipRule('test'));

    $data = new Dataspace();
    $data->set('test', '49007 1234');

    $this->error_list->expectNever('addError');

    $this->validator->validate($data);
    $this->assertTrue($this->validator->isValid());
  }

  function testUsZipRuleInvalid1()
  {
    $this->validator->addRule(new UsZipRule('test'));

    $data = new Dataspace();
    $data->set('test', '490078');

    $this->error_list->expectOnce('addError', array('test', Strings :: get('error_invalid_zip_format', 'error'), array()));

    $this->validator->validate($data);
    $this->assertFalse($this->validator->isValid());
  }

  function testUsZipRuleInvalid2()
  {
    $this->validator->addRule(new UsZipRule('test'));

    $data = new Dataspace();
    $data->set('test', '49007 23234');

    $this->error_list->expectOnce('addError', array('test', Strings :: get('error_invalid_zip_format', 'error'), array()));

    $this->validator->validate($data);
    $this->assertFalse($this->validator->isValid());
  }

  function testUsZipRuleInvalid3()
  {
    $this->validator->addRule(new UsZipRule('test'));

    $data = new Dataspace();
    $data->set('test', '4t007 12d4');

    $this->error_list->expectOnce('addError', array('test', Strings :: get('error_invalid_zip_format', 'error'), array()));

    $this->validator->validate($data);
    $this->assertFalse($this->validator->isValid());
  }

}

?>