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
require_once(dirname(__FILE__) . '/RuleTest.class.php');
require_once(LIMB_DIR . '/class/validators/rules/SingleFieldRule.class.php');

class SingleFieldRuleTest extends RuleTest
{
  function testInit()
  {
    $r = new SingleFieldRule('test');
    $this->assertEqual($r->getFieldName(), 'test');
  }
}

?>