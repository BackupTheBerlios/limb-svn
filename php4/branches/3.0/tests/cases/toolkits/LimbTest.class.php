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
require_once(LIMB_DIR . '/core/Limb.class.php');

class ImagineryToolkit
{
  var $reseted = false;
  var $foo_called = false;
  var $bar_called = false;

  function reset()
  {
    $this->reseted = true;
  }

  function foo()
  {
    $this->foo_called = true;
  }

  function bar()
  {
    $this->bar_called = true;
  }
}

class LimbTest extends LimbTestCase
{
  function LimbTest()
  {
    parent :: LimbTestCase(__FILE__);
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
    $this->assertEqual(Limb :: restoreToolkit(), $toolkit);
  }

  function testRegisterNamedToolkit()
  {
    $toolkit1 = 'test1';
    $toolkit2 = 'test2';

    Limb :: registerToolkit($toolkit1, 'named');
    Limb :: registerToolkit($toolkit2);

    $this->assertEqual(Limb :: toolkit('named'), $toolkit1);
    $this->assertEqual(Limb :: toolkit(), $toolkit2);

    $this->assertEqual(Limb :: restoreToolkit(), $toolkit2);

    $this->assertEqual(Limb :: toolkit('named'), $toolkit1);

    $this->assertEqual(Limb :: restoreToolkit('named'), $toolkit1);
  }

  function testNonexistingToolkit()
  {
    $this->assertFalse(Limb :: toolkit('no'));
    $this->assertFalse(Limb :: restoreToolkit('no'));
  }

  function testSaveToolkit()
  {
    $toolkit = new ImagineryToolkit();

    Limb :: registerToolkit($toolkit, 'test');

    $toolkit2 =& Limb :: saveToolkit('test');
    $this->assertTrue($toolkit2->reseted);

    $toolkit2->foo();

    //restoring saved toolkit
    Limb :: restoreToolkit('test');

    //should be a reference to $toolkit
    $toolkit3 =& Limb :: toolkit('test');
    $toolkit3->bar();

    $this->assertFalse($toolkit->foo_called);
    $this->assertTrue($toolkit->bar_called);

    Limb :: restoreToolkit('test');
  }

}

?>
