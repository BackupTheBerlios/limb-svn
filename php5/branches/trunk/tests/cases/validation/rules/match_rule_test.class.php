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
require_once(LIMB_DIR . '/class/validators/rules/match_rule.class.php');

class match_rule_test extends single_field_rule_test
{
  function test_match_rule_true()
  {
    $this->validator->add_rule(new match_rule('testfield', 'testmatch'));

    $data = new dataspace();
    $data->set('testfield', 'peaches');
    $data->set('testmatch', 'peaches');

    $this->error_list->expectNever('add_error');

    $this->validator->validate($data);
    $this->assertTrue($this->validator->is_valid());
  }

  function test_match_rule_empty()
  {
    $this->validator->add_rule(new match_rule('testfield', 'testmatch'));

    $data = &new dataspace();

    $this->error_list->expectNever('add_error');

    $this->validator->validate($data);
    $this->assertTrue($this->validator->is_valid());
  }

  function test_match_rule_empty2()
  {
    $this->validator->add_rule(new match_rule('testfield', 'testmatch'));

    $data = &new dataspace();
    $data->set('testfield', 'peaches');

    $this->error_list->expectNever('add_error');

    $this->validator->validate($data);
    $this->assertTrue($this->validator->is_valid());
  }

  function test_match_rule_empty3()
  {
    $this->validator->add_rule(new match_rule('testfield', 'testmatch'));

    $data = &new dataspace();
    $data->set('testmatch', 'peaches');

    $this->error_list->expectNever('add_error');

    $this->validator->validate($data);
    $this->assertTrue($this->validator->is_valid());
  }

  function test_match_rule_failure()
  {
    $this->validator->add_rule(new match_rule('testfield', 'testmatch'));

    $data = new dataspace();
    $data->set('testfield', 'peaches');
    $data->set('testmatch', 'cream');

    $this->error_list->expectOnce('add_error', array('testfield', strings :: get('error_no_match', 'error'), array('match_field' => 'testmatch')));

    $this->validator->validate($data);
    $this->assertFalse($this->validator->is_valid());
  }
}

?>