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
require_once(LIMB_DIR . '/class/validators/rules/UrlRule.class.php');

class UrlRuleTest extends SingleFieldRuleTest
{
  function testUrlRuleValid()
  {
    $this->validator->addRule(new UrlRule('test'));

    $data = new Dataspace();
    $data->set('test', 'https://wow.com.dot:81/this/a/valid/url?hey=wow&test');

    $this->error_list->expectNever('addError');

    $this->validator->validate($data);
    $this->assertTrue($this->validator->isValid());
  }

  function testUrlRuleInvalid()
  {
    $this->validator->addRule(new UrlRule('testfield'));

    $data = new Dataspace();
    $data->set('testfield', '://not/a/valid/url');

    $this->error_list->expectOnce('addError', array('testfield', 'BAD_URL', array()));

    $this->validator->validate($data);
    $this->assertFalse($this->validator->isValid());
  }
}

?>