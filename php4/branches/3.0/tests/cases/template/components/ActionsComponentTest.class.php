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
require_once(LIMB_DIR . '/core/template/components/ActionsComponent.class.php');
require_once(LIMB_DIR . '/core/i18n/Strings.class.php');

class ActionsComponentTest extends LimbTestCase
{
  function ActionsComponentTest()
  {
    parent :: LimbTestCase('actions component test');
  }

  function testGetActions()
  {
    $j = new ActionsComponent();

    $actions = array(
      'display' => array(),
      'edit' => array(
          'JIP' => false,
          'action_name' => Strings :: get('edit'),
          'img_src' => '/shared/images/edit.gif',
      ),
      'delete' => array(
          'JIP' => true,
          'action_name' => Strings :: get('delete'),
          'img_src' => '/shared/images/rem.gif',
      ),
    );

    $j->setActions($actions);
    $j->setNodeId(100);

    $actions = $j->getActions();

    $this->assertTrue(is_array($actions));
    $this->assertEqual(count($actions), 1);

    $action = reset($actions);
    $this->assertEqual($action['action'], 'delete');

    $this->assertWantedPattern('/^\/root\?.+$/', $action['action_href']);
    $this->assertWantedPattern('/&*action=delete/', $action['action_href']);
    $this->assertWantedPattern('/&*node_id=100/', $action['action_href']);
  }
}

?>