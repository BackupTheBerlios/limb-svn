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
require_once(LIMB_DIR . '/class/validators/rules/MatchRule.class.php');

class MatchRuleTest extends SingleFieldRuleTest
{
  function testMatchRuleTrue()
  {
    $this->validator->addRule(new MatchRule('testfield', 'testmatch'));

    $data = new Dataspace();
    $data->set('testfield', 'peaches');
    $data->set('testmatch', 'peaches');

    $this->error_list->expectNever('addError');

    $this->validator->validate($data);
    $this->assertTrue($this->validator->isValid());
  }

  function testMatchRuleEmpty()
  {
    $this->validator->addRule(new MatchRule('testfield', 'testmatch'));

    $data = &new Dataspace();

    $this->error_list->expectNever('addError');

    $this->validator->validate($data);
    $this->assertTrue($this->validator->isValid());
  }

  function testMatchRuleEmpty2()
  {
    $this->validator->addRule(new MatchRule('testfield', 'testmatch'));

    $data = &new Dataspace();
    $data->set('testfield', 'peaches');

    $this->error_list->expectNever('addError');

    $this->validator->validate($data);
    $this->assertTrue($this->validator->isValid());
  }

  function testMatchRuleEmpty3()
  {
    $this->validator->addRule(new MatchRule('testfield', 'testmatch'));

    $data = &new Dataspace();
    $data->set('testmatch', 'peaches');

    $this->error_list->expectNever('addError');

    $this->validator->validate($data);
    $this->assertTrue($this->validator->isValid());
  }

  function testMatchRuleFailure()
  {
    $this->validator->addRule(new MatchRule('testfield', 'testmatch'));

    $data = new Dataspace();
    $data->set('testfield', 'peaches');
    $data->set('testmatch', 'cream');

    $this->error_list->expectOnce('addError', array('testfield', Strings :: get('error_no_match', 'error'), array('match_field' => 'testmatch')));

    $this->validator->validate($data);
    $this->assertFalse($this->validator->isValid());
  }
}

?>