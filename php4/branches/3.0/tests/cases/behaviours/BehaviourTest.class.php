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
require_once(LIMB_DIR . '/core/behaviours/Behaviour.class.php');

class BehaviourTest extends LimbTestCase
{
  function BehaviourTest()
  {
    parent :: LimbTestCase('site object behaviour tests');
  }

  function tearDown()
  {
    clearTestingIni();
  }

  function testGetActionsList()
  {
    registerTestingIni(
      'test.behaviour.ini',
      '
      [action1]
      some_properties
      [action2]
      some_properties
      '
    );

    $behaviour = new Behaviour('test');
    $this->assertEqual(array('action1', 'action2'),
                       $behaviour->getActionsList());
  }

  function testActionExists()
  {
    registerTestingIni(
      'test.behaviour.ini',
      '
      [action1]
      some_properties
      [action2]
      some_properties
      '
    );

    $behaviour = new Behaviour('test');
    $this->assertTrue($behaviour->actionExists('action1'));
    $this->assertFalse($behaviour->actionExists('no_such_action'));
  }

  function testCanBeParent()
  {
    registerTestingIni(
      'test.behaviour.ini',
      '
      can_be_parent = 1

      [action1]
      some_properties
      '
    );

    $behaviour = new Behaviour('test');
    $this->assertTrue($behaviour->canBeParent());
  }

  function testGetDefaultAction()
  {
    registerTestingIni(
      'test.behaviour.ini',
      '
      can_be_parent = 1
      default_action = admin_display

      [admin_display]
      some_properties
      '
    );

    $behaviour = new Behaviour('test');
    $this->assertTrue($behaviour->getDefaultAction(), 'admin_display');
  }
}

?>