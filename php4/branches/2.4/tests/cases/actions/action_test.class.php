<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/actions/action.class.php');

class action_test extends LimbTestCase
{
  var $a = null;

  function setUp()
  {
    $this->a =& new action();
  }

  function tearDown()
  {
  }

  function test_init()
  {
    $this->assertNotNull($this->a);
  }
}

?>