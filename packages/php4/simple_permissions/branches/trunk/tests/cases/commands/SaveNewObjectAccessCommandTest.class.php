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
require_once(dirname(__FILE__) . '/../../../commands/SaveNewObjectAccessCommand.class.php');
require_once(dirname(__FILE__) . '/../../../AccessPolicy.class.php');
require_once(LIMB_DIR . '/class/request/Request.class.php');
require_once(LIMB_DIR . '/class/LimbToolkit.interface.php');
require_once(LIMB_DIR . '/class/site_objects/SiteObject.class.php');
require_once(LIMB_DIR . '/class/site_objects/SiteObjectController.class.php');
require_once(LIMB_DIR . '/class/Dataspace.class.php');
require_once(LIMB_DIR . '/class/datasources/SingleObjectDatasource.class.php');

Mock :: generate('LimbToolkit');
Mock :: generate('Request');
Mock :: generate('SingleObjectDatasource');
Mock :: generate('SiteObject');
Mock :: generate('SiteObjectController');
Mock :: generate('AccessPolicy');
Mock :: generate('Dataspace');

Mock :: generatePartial('SaveNewObjectAccessCommand',
                        'SaveNewObjectAccessCommandTestVersion',
                        array('_getAccessPolicy'));

class AccessPolicyForSaveNewObjectAccessCommand extends AccessPolicy
{
  function saveNewObjectAccess($object, $parent_object, $action)
  {
    return throw(new LimbException('catch me!'));
  }
}

class SaveNewObjectAccessCommandTest extends LimbTestCase
{
  var $command;
  var $request;
  var $dataspace;
  var $toolkit;
  var $datasource;
  var $site_object;
  var $parent_site_object;
  var $controller;
  var $access_policy;

  function setUp()
  {
    $this->request = new MockRequest($this);
    $this->dataspace = new MockDataspace($this);
    $this->datasource = new MockSingleObjectDatasource($this);
    $this->site_object = new MockSiteObject($this);
    $this->parent_site_object = new MockSiteObject($this);
    $this->controller = new MockSiteObjectController($this);
    $this->access_policy = new MockAccessPolicy($this);

    $this->toolkit = new MockLimbToolkit($this);
    $this->toolkit->setReturnReference('getDatasource', $this->datasource, array('SingleObjectDatasource'));
    $this->toolkit->setReturnReference('getRequest', $this->request);
    $this->toolkit->setReturnReference('createSiteObject', $this->parent_site_object, array('site_object'));
    $this->toolkit->setReturnReference('getDataspace', $this->dataspace);

    Limb :: registerToolkit($this->toolkit);

    $this->command = new SaveNewObjectAccessCommandTestVersion($this);
  }

  function tearDown()
  {
    Limb :: popToolkit();

    $this->request->tally();
    $this->dataspace->tally();
    $this->datasource->tally();
    $this->toolkit->tally();
    $this->site_object->tally();
    $this->parent_site_object->tally();
    $this->controller->tally();
    $this->access_policy->tally();
    $this->command->tally();
  }

  function testPerformOk()
  {
    $this->access_policy->expectOnce('saveNewObjectAccess',
                                     array(new IsAExpectation('MockSiteObject'),
                                           new IsAExpectation('MockSiteObject'),
                                           'someAction'));

    $this->dataspace->setReturnReference('get', $this->site_object, array('created_site_object'));

    $parent_object_data = array('class_name' => 'site_object');
    $this->datasource->expectOnce('setNodeId', array($parent_node_id = 100));
    $this->datasource->setReturnValue('fetch', $parent_object_data);

    $this->site_object->setReturnValue('getParentNodeId', $parent_node_id);

    $this->parent_site_object->setReturnReference('getController', $this->controller);

    $this->controller->setReturnValue('getRequestedAction',
                                      'someAction',
                                      array(new IsAExpectation('MockRequest')));

    $this->controller->expectOnce('getRequestedAction', array(new IsAExpectation('MockRequest')));

    $this->command->setReturnReference('_getAccessPolicy', $this->access_policy);

    $this->assertEqual(LIMB_STATUS_OK, $this->command->perform());
  }

  function testPerformFailureAccessPolicyFailed()
  {
    $this->dataspace->setReturnReference('get', $this->site_object, array('created_site_object'));

    $this->command->setReturnReference('_getAccessPolicy',
                                   new AccessPolicyForSaveNewObjectAccessCommand());

    $parent_object_data = array('class_name' => 'site_object');

    $this->datasource->expectOnce('setNodeId', array($parent_node_id = 100));
    $this->datasource->setReturnValue('fetch', $parent_object_data);

    $this->parent_site_object->setReturnReference('getController', $this->controller);

    $this->controller->setReturnValue('getRequestedAction',
                                      'someAction',
                                      array(new IsAExpectation('MockRequest')));

    $this->controller->expectOnce('getRequestedAction', array(new IsAExpectation('MockRequest')));

    $this->site_object->setReturnValue('getParentNodeId', $parent_node_id);

    $this->assertEqual(LIMB_STATUS_ERROR, $this->command->perform());
  }

  function testPerformFailureNoCreatedObjectData()
  {
    $this->command->setReturnReference('_getAccessPolicy',
                                   new AccessPolicyForSaveNewObjectAccessCommand());

    $this->dataspace->setReturnValue('get', null, array('created_site_object'));

    $this->command->perform();
    $this->assertTrue(catch('Exception', $e));
  }
}

?>