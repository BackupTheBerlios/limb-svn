<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/lib/util/dataspace.class.php');
require_once(LIMB_DIR . '/core/lib/validators/rules/canada_zip_rule.class.php');
require_once(LIMB_DIR . '/tests/cases/validation/rules/single_field_rule_test.class.php');

class canada_zip_rule_test extends single_field_rule_test
{
  function test_canada_zip_rule_valid()
  {
    $this->validator->add_rule(new canada_zip_rule('test'));

    $data =& new dataspace();
    $data->set('test', 'H2V 2K1');

    $this->error_list->expectNever('add_error');

    $this->validator->validate($data);
    $this->assertTrue($this->validator->is_valid());
  }

  function test_canada_zip_rule_valid2()
  {
    $this->validator->add_rule(new canada_zip_rule('test'));

    $data =& new dataspace();
    $data->set('test', 'h2v 2k1');

    $this->error_list->expectNever('add_error');

    $this->validator->validate($data);
    $this->assertTrue($this->validator->is_valid());
  }

  function test_canada_zip_rule_valid3()
  {
    $this->validator->add_rule(new canada_zip_rule('test'));

    $data =& new dataspace();
    $data->set('test', 'h2v2k1');

    $this->error_list->expectNever('add_error');

    $this->validator->validate($data);
    $this->assertTrue($this->validator->is_valid());
  }

  function test_canada_zip_rule_invalid1()
  {
    $this->validator->add_rule(new canada_zip_rule('test'));

    $data =& new dataspace();
    $data->set('test', '490078');

    $this->error_list->expectOnce('add_error', array('test', strings :: get('error_invalid_zip_format', 'error'), array()));

    $this->validator->validate($data);
    $this->assertFalse($this->validator->is_valid());
  }

  function test_canada_zip_rule_invalid2()
  {
    $this->validator->add_rule(new canada_zip_rule('test'));

    $data =& new dataspace();
    $data->set('test', '324 256');

    $this->error_list->expectOnce('add_error', array('test', strings :: get('error_invalid_zip_format', 'error'), array()));

    $this->validator->validate($data);
    $this->assertFalse($this->validator->is_valid());
  }
}

?>