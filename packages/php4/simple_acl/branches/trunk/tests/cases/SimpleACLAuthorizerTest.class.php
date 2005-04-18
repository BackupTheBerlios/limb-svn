<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: ImageObjectsDAOTest.class.php 1093 2005-02-07 15:17:20Z pachanga $
*
***********************************************************************************/
require_once(dirname(__FILE__) . '/../../SimpleACLAuthorizer.class.php');
require_once(LIMB_DIR . '/core/entity/Entity.class.php');

class SimpleACLAuthorizerTest extends LimbTestCase
{
  var $authorizer;

  function SimpleACLAuthorizerTest()
  {
    parent :: LimbTestCase(__FILE__);
  }

  function setUp()
  {
    $this->authorizer = new SimpleACLAuthorizer();

    registerTestingIni(
      'SimpleACLAuthorizerTestService.service.ini',
      '
      [read]
      access = 1
      [edit]
      access = 2
      [create]
      access = 4
      [delete]
      access = 128
      '
    );
  }

  function tearDown()
  {
    clearTestingIni();
  }

  function testAssignActionsWithPathExactMatchingAccess()
  {
    $toolkit =& Limb :: toolkit();
    $user =& $toolkit->getUser();
    $user->set('groups', array('visitors'));

    $this->authorizer->attachPolicy('/root', 'visitors', 1);
    $actions = $this->authorizer->getAccessibleActions($path = '/root',
                                                       $service_name = 'SimpleACLAuthorizerTestService');

    $this->assertEqual(sizeof($actions), 1);
    $this->assertEqual($actions['read']['access'], 1);
  }

  function testAssignActionsWithNearestParentAccess()
  {
    $toolkit =& Limb :: toolkit();
    $user =& $toolkit->getUser();
    $user->set('groups', array('visitors'));

    $this->authorizer->attachPolicy('/root/docs', 'visitors', 1);
    $this->authorizer->attachPolicy('/root', 'visitors', 128);
    $actions = $this->authorizer->getAccessibleActions($path = '/root/docs/some_doc',
                                                       $service_name = 'SimpleACLAuthorizerTestService');

    $this->assertEqual(sizeof($actions), 1);
    $this->assertEqual($actions['read']['access'], 1);
  }

  function testCanDo()
  {
    $toolkit =& Limb :: toolkit();
    $user =& $toolkit->getUser();
    $user->set('groups', array('visitors'));

    $this->authorizer->attachPolicy('/root/docs', 'visitors', 1);
    $this->authorizer->attachPolicy('/root', 'visitors', 128);
    $actions = $this->authorizer->getAccessibleActions($path = '/root/docs/some_doc',
                                                       $service_name = 'SimpleACLAuthorizerTestService');

    $this->assertTrue($this->authorizer->canDo('read', $path, $service_name));
    $this->assertFalse($this->authorizer->canDo('edit', $path, $service_name));
  }
}

?>