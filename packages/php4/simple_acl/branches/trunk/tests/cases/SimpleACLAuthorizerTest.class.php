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
require_once(dirname(__FILE__) . '/SimpleACLAuthorizerTestBehaviour.class.php');
require_once(LIMB_DIR . '/core/Service.class.php');

class SimpleACLAuthorizerTest extends LimbTestCase
{
  var $authorizer;

  function SimpleACLAuthorizerTest()
  {
    parent :: LimbTestCase('Simple ACL Authorizer test');
  }

  function setUp()
  {
    $this->authorizer = new SimpleACLAuthorizer();
  }

  function testAssignActionsWithPathExactMatchingAccess()
  {
    $object = new Service();
    $object->set('path', $path = '/root');
    $object->attachBehaviour(new SimpleACLAuthorizerTestBehaviour());

    $toolkit =& Limb :: toolkit();
    $user =& $toolkit->getUser();
    $user->set('groups', array('visitors'));

    $this->authorizer->attachPolicy('/root', 'visitors', 1);
    $this->authorizer->assignActions($object);

    $actions = $object->get('actions');
    $this->assertEqual(sizeof($actions), 1);
    $this->assertEqual($actions['read']['access'], 1);
  }

  function testAssignActionsWithNearestParentAccess()
  {
    $object = new Service();
    $object->set('path', $path = '/root/docs/some_doc');
    $object->attachBehaviour(new SimpleACLAuthorizerTestBehaviour());

    $toolkit =& Limb :: toolkit();
    $user =& $toolkit->getUser();
    $user->set('groups', array('visitors'));

    $this->authorizer->attachPolicy('/root/docs', 'visitors', 1);
    $this->authorizer->attachPolicy('/root', 'visitors', 128);
    $this->authorizer->assignActions($object);

    $actions = $object->get('actions');
    $this->assertEqual(sizeof($actions), 1);
    $this->assertEqual($actions['read']['access'], 1);
  }

  function testCanDo()
  {
    $object = new Service();
    $object->set('path', $path = '/root/docs/some_doc');
    $object->attachBehaviour(new SimpleACLAuthorizerTestBehaviour());

    $toolkit =& Limb :: toolkit();
    $user =& $toolkit->getUser();
    $user->set('groups', array('visitors'));

    $this->authorizer->attachPolicy('/root/docs', 'visitors', 1);
    $this->authorizer->attachPolicy('/root', 'visitors', 128);
    $this->authorizer->assignActions($object);

    $this->assertTrue($this->authorizer->canDo('read', $object));
    $this->assertFalse($this->authorizer->canDo('edit', $object));
  }
}

?>