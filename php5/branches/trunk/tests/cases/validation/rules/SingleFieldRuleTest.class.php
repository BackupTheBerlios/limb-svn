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
require_once(dirname(__FILE__) . '/rule_test.class.php');
require_once(LIMB_DIR . '/class/validators/rules/single_field_rule.class.php');

class single_field_rule_test extends rule_test
{
  function test_init()
  {
    $r = new single_field_rule('test');
    $this->assertEqual($r->get_field_name(), 'test');
  }
}

?>