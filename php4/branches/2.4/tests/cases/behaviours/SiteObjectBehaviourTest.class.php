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
require_once(LIMB_DIR . '/core/behaviours/SiteObjectBehaviour.class.php');

Mock :: generate('SiteObjectBehaviour');

class SiteObjectBehaviourTestVersion extends SiteObjectBehaviour
{
  function _defineProperties()
  {
    return array(
      'sort_order' => 3,
      'can_be_parent' => 1,
      'icon' => '/shared/images/folder.gif',
    );
  }

  function defineAction1(&$state_machine){}
  function defineAction2(&$state_machine){}
}

class SiteObjectBehaviourTest extends LimbTestCase
{
  var $behaviour;

  function SiteObjectBehaviourTest()
  {
    parent :: LimbTestCase('site object behaviour tests');
  }

  function setUp()
  {
    $this->behaviour = new SiteObjectBehaviourTestVersion();
  }

  function tearDown()
  {
  }

  function testGetActionsList()
  {
    $this->assertEqual(array('action1', 'action2'),
                       $this->behaviour->getActionsList());
  }

  function testActionExists()
  {
    $this->assertTrue($this->behaviour->actionExists('action1'));
    $this->assertFalse($this->behaviour->actionExists('no_such_action'));
  }

  function testCanBeParent()
  {
    $this->assertTrue($this->behaviour->canBeParent());
  }
}

?>