<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: authentication_filter_test.class.php 814 2004-10-21 12:46:23Z pachanga $
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/filters/FilterChain.class.php');
require_once(LIMB_DIR . '/core/site_objects/SiteObject.class.php');
require_once(LIMB_DIR . '/core/site_objects/SiteObjectController.class.php');
require_once(LIMB_DIR . '/core/request/Request.class.php');
require_once(LIMB_DIR . '/core/datasources/RequestedObjectDatasource.class.php');
require_once(LIMB_DIR . '/core/LimbToolkit.interface.php');
require_once(LIMB_DIR . '/core/behaviours/SiteObjectBehaviour.class.php');
require_once(LIMB_DIR . '/core/request/HttpResponse.class.php');
require_once(LIMB_DIR . '/core/permissions/User.class.php');
require_once(LIMB_DIR . '/core/permissions/Authenticator.interface.php');
require_once(LIMB_DIR . '/core/permissions/Authorizer.interface.php');

require_once(dirname(__FILE__) . '/../../../filters/SimplePermissionsAuthenticationFilter.class.php');

Mock :: generate('LimbToolkit');
Mock :: generate('FilterChain');
Mock :: generate('HttpResponse');
Mock :: generate('RequestedObjectDatasource');
Mock :: generate('Request');
Mock :: generate('SiteObjectController');
Mock :: generate('SiteObjectBehaviour');
Mock :: generate('Response');
Mock :: generate('User');
Mock :: generate('Authenticator');
Mock :: generate('Authorizer');

Mock :: generatePartial('SimplePermissionsAuthenticationFilter',
                        'SimplePermissionsAuthenticationFilterTestVersion',
                        array('_getController',
                              'getBehaviourByObjectId',
                              'initializeUser',
                              'process404Error'));

class SimplePermissionsAuthenticationFilterTest extends LimbTestCase
{
  var $filter_chain;
  var $filter;
  var $request;
  var $datasource;
  var $toolkit;
  var $response;

  function setUp()
  {
    $this->filter = new SimplePermissionsAuthenticationFilterTestVersion($this);

    $this->toolkit = new MockLimbToolkit($this);
    $this->datasource = new MockRequestedObjectDatasource($this);
    $this->request = new MockRequest($this);
    $this->filter_chain = new MockFilterChain($this);
    $this->response = new MockHttpResponse($this);

    Limb :: registerToolkit($this->toolkit);
  }

  function tearDown()
  {
    $this->request->tally();
    $this->response->tally();

    $this->toolkit->tally();

    Limb :: popToolkit();
  }

  function testRunNodeNotFound()
  {
    $this->toolkit->setReturnReference('getDatasource',
                                   $this->datasource,
                                   array('RequestedObjectDatasource'));

    $this->datasource->setReturnValue('mapRequestToNode',
                                      null,
                                      array(new IsAExpectation('MockRequest')));

    $this->filter->expectOnce('process404Error');
    $this->filter_chain->expectOnce('next');

    $this->filter->run($this->filter_chain, $this->request, $this->response);

    $this->filter->tally();
    $this->filter_chain->tally();
  }

  function testRunNoSuchAction()
  {
    $this->toolkit->setReturnReference('getDatasource',
                                   $this->datasource,
                                   array('RequestedObjectDatasource'));

    $this->datasource->setReturnValue('mapRequestToNode',
                                      array('object_id' => $object_id = 100),
                                      array(new IsAExpectation('MockRequest')));

    $controller = new MockSiteObjectController($this);
    $behaviour = new MockSiteObjectBehaviour($this);

    $this->filter->setReturnReference('getBehaviourByObjectId', $behaviour, array($object_id));

    $this->filter->setReturnReference('_getController',
                                  $controller,
                                  array(new IsAExpectation('MockSiteObjectBehaviour')));

    $controller->setReturnValue('getRequestedAction', null);

    $this->filter->expectOnce('process404Error');
    $this->filter_chain->expectOnce('next');

    $this->filter->run($this->filter_chain, $this->request, $this->response);

    $this->filter->tally();
    $this->filter_chain->tally();
  }

  function testRunObjectIsNotAccessible()
  {
    $this->toolkit->setReturnReference('getDatasource',
                                   $this->datasource,
                                   array('RequestedObjectDatasource'));

    $this->datasource->setReturnValue('mapRequestToNode',
                                      array('object_id' => $object_id = 100),
                                      array(new IsAExpectation('MockRequest')));

    $controller = new MockSiteObjectController($this);
    $behaviour = new MockSiteObjectBehaviour($this);

    $this->filter->setReturnReference('getBehaviourByObjectId', $behaviour, array($object_id));

    $this->filter->setReturnReference('_getController',
                                  $controller,
                                  array(new IsAExpectation('MockSiteObjectBehaviour')));

    $controller->setReturnValue('getRequestedAction', $action = 'some action');

    $this->datasource->expectOnce('setRequest', array(new IsAExpectation('MockRequest')));

    $this->datasource->setReturnValue('fetch', null);

    $this->response->expectOnce('redirect');

    $this->filter_chain->expectNever('next');

    $this->filter->run($this->filter_chain, $this->request, $this->response);

    $this->filter->tally();
    $this->filter_chain->tally();
  }

  function testRunActionIsNotAccessible()
  {
    $this->toolkit->setReturnReference('getDatasource',
                                   $this->datasource,
                                   array('RequestedObjectDatasource'));

    $this->datasource->setReturnValue('mapRequestToNode',
                                      array('object_id' => $object_id = 100),
                                      array(new IsAExpectation('MockRequest')));

    $controller = new MockSiteObjectController($this);
    $behaviour = new MockSiteObjectBehaviour($this);

    $this->filter->setReturnReference('getBehaviourByObjectId', $behaviour, array($object_id));

    $this->filter->setReturnReference('_getController',
                                  $controller,
                                  array(new IsAExpectation('MockSiteObjectBehaviour')));

    $controller->setReturnValue('getRequestedAction', $action = 'some action');

    $this->datasource->expectOnce('setRequest', array(new IsAExpectation('MockRequest')));

    $object_data = array('actions' => array(), 'behaviour' => 'some behaviour');
    $this->datasource->setReturnValue('fetch', $object_data);

    $authorizer = new MockAuthorizer($this);
    $authorizer->expectOnce('assignActionsToObjects', array($object_data));

    $this->toolkit->setReturnReference('getAuthorizer', $authorizer);

    $this->response->expectOnce('redirect');

    $this->filter_chain->expectNever('next');

    $this->filter->run($this->filter_chain, $this->request, $this->response);

    $this->filter->tally();
    $this->filter_chain->tally();
  }

  function testRunOk()
  {
    $this->toolkit->setReturnReference('getDatasource',
                                   $this->datasource,
                                   array('RequestedObjectDatasource'));

    $this->datasource->setReturnValue('mapRequestToNode',
                                      array('object_id' => $object_id = 100),
                                      array(new IsAExpectation('MockRequest')));

    $controller = new MockSiteObjectController($this);
    $behaviour = new MockSiteObjectBehaviour($this);

    $this->filter->setReturnReference('getBehaviourByObjectId', $behaviour, array($object_id));

    $this->filter->setReturnReference('_getController',
                                  $controller,
                                  array(new IsAExpectation('MockSiteObjectBehaviour')));

    $controller->setReturnValue('getRequestedAction', $action = 'some_action');

    $this->datasource->expectOnce('setRequest', array(new IsAExpectation('MockRequest')));

    $object_data = array('actions' => array($action => array()), 'behaviour' => 'some_behaviour');
    $this->datasource->setReturnValue('fetch', $object_data);

    $authorizer = new MockAuthorizer($this);
    $authorizer->expectOnce('assignActionsToObjects', array($object_data));

    $this->toolkit->setReturnValue('getAuthorizer', $authorizer);

    $this->response->expectNever('redirect');

    $this->filter_chain->expectOnce('next');

    $this->filter->run($this->filter_chain, $this->request, $this->response);

    $this->filter->tally();
    $this->filter_chain->tally();
  }
}

?>