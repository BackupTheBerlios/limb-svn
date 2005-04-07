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
require_once(LIMB_DIR . '/core/entity/Entity.class.php');

class EntityTest extends LimbTestCase
{
  function EntityTest()
  {
    parent :: LimbTestCase('entity test');
  }

  function setUp()
  {
  }

  function tearDown()
  {
  }

  function testGetNonExistingPart()
  {
    $e = new Entity();
    $this->assertNull($e->getPart('whatever'));
  }

  function testRegisterPart()
  {
    $e = new Entity();
    $e->registerPart('Object1', new Handle('Object'));
    $e->registerPart('Object2', new Handle('Object'));

    $part1 =& $e->getPart('Object1');
    $part2 =& $e->getPart('Object2');

    $this->assertIsA($part1, 'Object');
    $this->assertIsA($part2, 'Object');
  }

  function testGetParts()
  {
    $e = new Entity();
    $e->registerPart('Object1', $o1 = new Object());
    $e->registerPart('Object2', $o2 = new Object());
restore_error_handler();trigger_error('Stop', E_USER_WARNING);
    $this->assertEqual($e->getParts(),
                       array('Object1' => $o1,
                             'Object2' => $o2));
  }
}

?>
