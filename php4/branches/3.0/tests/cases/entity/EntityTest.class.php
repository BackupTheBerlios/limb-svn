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
    parent :: LimbTestCase(__FILE__);
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

    $this->assertEqual($e->getParts(),
                       array('Object1' => $o1,
                             'Object2' => $o2));
  }

  function testExport()
  {
    $e = new Entity();
    $e->set('oid', $oid = 10);
    $e->set('prop1', 'prop1');
    $e->set('prop2', 'prop2');

    $o1 = new Object();
    $o1->set('prop1', 'prop11');

    $o2 = new Object();
    $o2->set('prop2', 'prop22');

    $e->registerPart('part1', $o1);
    $e->registerPart('part2', $o2);

    $this->assertEqual($e->export(),
                       array('oid' => $oid,
                             'prop1' => 'prop1',
                             'prop2' => 'prop2',
                             '_part1_prop1' => 'prop11',
                             '_part2_prop2' => 'prop22'));

  }
}

?>
