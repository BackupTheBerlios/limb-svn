<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: LimbBaseToolkitTest.class.php 1105 2005-02-15 13:46:50Z pachanga $
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/Limb.class.php');

class LimbTest extends LimbTestCase
{
  function LimbTest()
  {
    parent :: LimbTestCase('limb container tests');
  }

  function testDefaultToolkit()
  {
    $this->assertIsA(Limb :: toolkit(), 'LimbBaseToolkit');
  }

  function testRegisterUnnamedToolkit()
  {
    $toolkit = 'test';
    Limb :: registerToolkit($toolkit);
    $this->assertEqual(Limb :: toolkit(), $toolkit);
    $this->assertEqual(Limb :: popToolkit(), $toolkit);
  }

  function testRegisterNamedToolkit()
  {
    $toolkit1 = 'test1';
    $toolkit2 = 'test2';

    Limb :: registerToolkit($toolkit1, 'named');
    Limb :: registerToolkit($toolkit2);

    $this->assertEqual(Limb :: toolkit('named'), $toolkit1);
    $this->assertEqual(Limb :: toolkit(), $toolkit2);

    $this->assertEqual(Limb :: popToolkit(), $toolkit2);

    $this->assertEqual(Limb :: toolkit('named'), $toolkit1);

    $this->assertEqual(Limb :: popToolkit('named'), $toolkit1);
  }

  function testNonexistingToolkit()
  {
    $this->assertFalse(Limb :: toolkit('no'));
    $this->assertFalse(Limb :: popToolkit('no'));
  }

}

?>
