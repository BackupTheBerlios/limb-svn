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
require_once(LIMB_DIR . '/core/actions/action_factory.class.php');

class action_factory_test extends LimbTestCase
{
  function setUp()
  {
    debug_mock :: init($this);
  }

  function tearDown()
  {
    debug_mock :: tally();
  }

  function test_create_ok()
  {
    $c =& action_factory :: create('action');

    $this->assertIsA($c, 'action');
  }

  function test_create_no_such_action()
  {
    debug_mock :: expect_write_error('action not found', array('class_path' => 'no_such_action'));

    $c =& action_factory :: create('no_such_action');

    $this->assertIsA($c, 'empty_action');
  }
}

?>