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
require_once(dirname(__FILE__) . '/single_field_rule_test.class.php');
require_once(LIMB_DIR . '/class/core/dataspace.class.php');
require_once(LIMB_DIR . '/class/validators/rules/invalid_value_rule.class.php');

class invalid_value_rule_test extends single_field_rule_test
{
  function test_invalid_value_rule_ok_int()
  {
    $this->validator->add_rule(new invalid_value_rule('testfield', 0));

    $data = &new dataspace();
    $data->set('testfield', 1);

    $this->error_list->expectNever('add_error');

    $this->validator->validate($data);
    $this->assertTrue($this->validator->is_valid());
  }

  function test_invalid_value_rule_ok_int2()
  {
    $this->validator->add_rule(new invalid_value_rule('testfield', 0));

    $data = &new dataspace();
    $data->set('testfield', 'whatever');

    $this->error_list->expectNever('add_error');

    $this->validator->validate($data);
    $this->assertTrue($this->validator->is_valid());
  }


  function test_invalid_value_rule_ok_null()
  {
    $this->validator->add_rule(new invalid_value_rule('testfield', null));

    $data = &new dataspace();
    $data->set('testfield', 'null');

    $this->error_list->expectNever('add_error');

    $this->validator->validate($data);
    $this->assertTrue($this->validator->is_valid());
  }

  function test_invalid_value_rule_ok_bool()
  {
    $this->validator->add_rule(new invalid_value_rule('testfield', false));

    $data = &new dataspace();
    $data->set('testfield', 'false');

    $this->error_list->expectNever('add_error');

    $this->validator->validate($data);
    $this->assertTrue($this->validator->is_valid());
  }

  function test_inavlid_value_rule_error()
  {
    $this->validator->add_rule(new invalid_value_rule('testfield', 1));

    $data = &new dataspace();
    $data->set('testfield', 1);

    $this->error_list->expectOnce('add_error', array('testfield', strings :: get('error_invalid_value', 'error'), array()));

    $this->validator->validate($data);
    $this->assertFalse($this->validator->is_valid());
  }

  function test_inavlid_value_rule_error2()
  {
    $this->validator->add_rule(new invalid_value_rule('testfield', 1));

    $data = &new dataspace();
    $data->set('testfield', '1');

    $this->error_list->expectOnce('add_error', array('testfield', strings :: get('error_invalid_value', 'error'), array()));

    $this->validator->validate($data);
    $this->assertFalse($this->validator->is_valid());
  }

}

?>