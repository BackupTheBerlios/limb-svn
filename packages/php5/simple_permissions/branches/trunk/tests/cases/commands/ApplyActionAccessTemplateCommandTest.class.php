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
require_once(dirname(__FILE__) . '/../../../commands/ApplyActionAccessTemplateCommand.class.php');
require_once(dirname(__FILE__) . '/../../../AccessPolicy.class.php');
require_once(LIMB_DIR . '/class/core/request/Request.class.php');
require_once(LIMB_DIR . '/class/core/datasources/RequestedObjectDatasource.class.php');
require_once(LIMB_DIR . '/class/core/LimbToolkit.interface.php');
require_once(LIMB_DIR . '/class/core/site_objects/SiteObject.class.php');
require_once(LIMB_DIR . '/class/core/site_objects/SiteObjectController.class.php');

Mock :: generate('LimbToolkit');
Mock :: generate('Request');
Mock :: generate('RequestedObjectDatasource');
Mock :: generate('SiteObject');
Mock :: generate('SiteObjectController');
Mock :: generate('AccessPolicy');

Mock :: generatePartial(
                      'ApplyActionAccessTemplateCommand',
                      'ApplyActionAccessTemplateCommandTestVersion',
                      array('_getAccessPolicy'));

class AccessPolicyForApplyActionAccessTemplateCommand extends AccessPolicy
{
  public function applyAccessTemplates($object, $action)
  {
    throw new LimbException('catch me!');
  }
}

class ApplyActionAccessTemplateCommandTest extends LimbTestCase
{
  var $command;
  var $request;
  var $toolkit;
  var $datasource;
  var $site_object;
  var $controller;
  var $access_policy;

  function setUp()
  {
    $this->request = new MockRequest($this);
    $this->datasource = new MockRequestedObjectDatasource($this);
    $this->site_object = new MockSiteObject($this);
    $this->controller = new MockSiteObjectController($this);
    $this->access_policy = new MockAccessPolicy($this);

    $this->toolkit = new MockLimbToolkit($this);
    $this->toolkit->setReturnValue('getDatasource', $this->datasource, array('requested_object_datasource'));
    $this->toolkit->setReturnValue('getRequest', $this->request);
    $this->toolkit->setReturnValue('createSiteObject', $this->site_object);

    $this->site_object->setReturnValue('getController', $this->controller);

    Limb :: registerToolkit($this->toolkit);

    $this->command = new ApplyActionAccessTemplateCommandTestVersion($this);
  }

  function tearDown()
  {
    Limb :: popToolkit();

    $this->request->tally();
    $this->datasource->tally();
    $this->toolkit->tally();
    $this->site_object->tally();
    $this->controller->tally();
    $this->access_policy->tally();
    $this->command->tally();
  }

  function testPerformOk()
  {
    $object_data = array('class_name' => 'site_object');

    $this->controller->expectOnce('getRequestedAction', array(new IsAExpectation('MockRequest')));
    $this->controller->setReturnValue('getRequestedAction', $action = 'someAction');

    $this->datasource->expectOnce('setRequest', array(new IsAExpectation('MockRequest')));
    $this->datasource->expectOnce('fetch');
    $this->datasource->setReturnValue('fetch', $object_data);

    $this->access_policy->expectOnce('applyAccessTemplates',
                                     array(new IsAExpectation('MockSiteObject'), $action));

    $this->command->setReturnValue('_getAccessPolicy', $this->access_policy);

    $this->assertEqual(Limb :: STATUS_OK, $this->command->perform());
  }

  function testPerformFailure()
  {
    $object_data = array('class_name' => 'site_object');

    $this->datasource->setReturnValue('fetch', $object_data);

    $this->command->setReturnValue('_getAccessPolicy',
                                   new AccessPolicyForApplyActionAccessTemplateCommand());

    $this->assertEqual(Limb :: STATUS_ERROR, $this->command->perform());
  }
}

?>