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
require_once(LIMB_DIR . '/core/filters/FilterChain.class.php');
require_once(LIMB_DIR . '/core/filters/AuthenticationFilter.class.php');
require_once(LIMB_DIR . '/core/site_objects/SiteObject.class.php');
require_once(LIMB_DIR . '/core/site_objects/SiteObjectController.class.php');
require_once(LIMB_DIR . '/core/request/Request.class.php');
require_once(LIMB_DIR . '/core/daos/RequestedObjectDAO.class.php');
require_once(LIMB_DIR . '/core/LimbToolkit.interface.php');
require_once(LIMB_DIR . '/core/behaviours/SiteObjectBehaviour.class.php');
require_once(LIMB_DIR . '/core/request/HttpResponse.class.php');
require_once(LIMB_DIR . '/core/permissions/User.class.php');
require_once(LIMB_DIR . '/core/permissions/Authenticator.interface.php');
require_once(LIMB_DIR . '/core/util/Ini.class.php');

Mock :: generate('LimbToolkit');
Mock :: generate('FilterChain');
Mock :: generate('HttpResponse');
Mock :: generate('RequestedObjectDAO');
Mock :: generate('Request');
Mock :: generate('SiteObjectController');
Mock :: generate('SiteObjectBehaviour');
Mock :: generate('Response');
Mock :: generate('User');
Mock :: generate('Authenticator');
Mock :: generate('Ini');

Mock :: generatePartial('AuthenticationFilter',
                        'AuthenticationFilterTestVersion',
                        array('_getController',
                              'getBehaviourByObjectId',
                              'initializeUser',
                              'process404Error'));

class AuthenticationFilterTest extends LimbTestCase
{
  var $filter_chain;
  var $filter;
  var $request;
  var $dao;
  var $toolkit;
  var $response;
  var $ini;

  function setUp()
  {
    $this->filter = new AuthenticationFilterTestVersion($this);

    $this->toolkit = new MockLimbToolkit($this);
    $this->ini = new MockIni($this);

    $this->toolkit->setReturnReference('getINI', $this->ini);

    $this->dao = new MockRequestedObjectDAO($this);
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
    $this->ini->tally();
    $this->filter->tally();
    $this->filter_chain->tally();

    Limb :: popToolkit();
  }

  function testInitializeUserIsLoggedIn()
  {
    $user = new MockUser($this);
    $authenticator = new MockAuthenticator($this);

    $this->toolkit->expectOnce('getUser');
    $this->toolkit->setReturnReference('getUser', $user);

    $user->expectOnce('isLoggedIn');
    $user->setReturnValue('isLoggedIn', true);
    $authenticator->expectNever('login', array(array('login' => '', 'password' => '')));

    $filter = new AuthenticationFilter();

    $filter->initializeUser();

    $user->tally();
    $authenticator->tally();
  }

  function testInitializeUserNotLoggedIn()
  {
    $user = new MockUser($this);
    $authenticator = new MockAuthenticator($this);

    $this->toolkit->expectOnce('getUser');
    $this->toolkit->setReturnReference('getUser', $user);

    $user->expectOnce('isLoggedIn');
    $user->setReturnValue('isLoggedIn', false);

    $this->toolkit->expectOnce('getAuthenticator');
    $this->toolkit->setReturnReference('getAuthenticator', $authenticator);

    $authenticator->expectOnce('login', array(array('login' => '', 'password' => '')));

    $filter = new AuthenticationFilter();

    $filter->initializeUser();

    $user->tally();
    $authenticator->tally();
  }

  function testProcess404ErrorFromIni()
  {
    $this->ini->expectOnce('getOption', array('404', 'ERROR_DOCUMENTS'));
    $this->ini->setReturnValue('getOption', $error_path = '/root/404');

    $filter = new AuthenticationFilter();

    $this->response->expectOnce('redirect', array($error_path));
    $this->response->expectNever('header');

    $filter->process404Error($this->request, $this->response);
  }

  function testProcess404ErrorNotFound()
  {
    $filter = new AuthenticationFilter();

    $this->response->expectNever('redirect');
    $this->response->expectOnce('header', array("HTTP/1.1 404 Not found"));

    $filter->process404Error($this->request, $this->response);
  }

  function testRunNodeNotFound()
  {
    $this->toolkit->setReturnReference('createDAO',
                                   $this->dao,
                                   array('RequestedObjectDAO'));

    $this->dao->setReturnValue('mapRequestToNode',
                                      null,
                                      array(new IsAExpectation('MockRequest')));

    $this->filter->expectOnce('process404Error');
    $this->filter_chain->expectOnce('next');

    $this->filter->run($this->filter_chain, $this->request, $this->response);
  }

  function testRunNoSuchAction()
  {
    $this->toolkit->setReturnReference('createDAO',
                                   $this->dao,
                                   array('RequestedObjectDAO'));

    $this->dao->setReturnValue('mapRequestToNode',
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
  }

  function testRunObjectIsNotAccessible()
  {
    $this->toolkit->setReturnReference('createDAO',
                                   $this->dao,
                                   array('RequestedObjectDAO'));

    $this->dao->setReturnValue('mapRequestToNode',
                                      array('object_id' => $object_id = 100),
                                      array(new IsAExpectation('MockRequest')));

    $controller = new MockSiteObjectController($this);
    $behaviour = new MockSiteObjectBehaviour($this);

    $this->filter->setReturnReference('getBehaviourByObjectId', $behaviour, array($object_id));

    $this->filter->setReturnReference('_getController',
                                  $controller,
                                  array(new IsAExpectation('MockSiteObjectBehaviour')));

    $controller->setReturnValue('getRequestedAction', $action = 'someAction');

    $this->dao->expectOnce('setPermissionsAction', array($action));
    $this->dao->expectOnce('setRequest', array(new IsAExpectation('MockRequest')));

    $this->dao->setReturnValue('fetch', null);

    $this->response->expectOnce('redirect');

    $this->filter_chain->expectNever('next');

    $this->filter->run($this->filter_chain, $this->request, $this->response);
  }

  function testRunOk()
  {
    $this->toolkit->setReturnReference('createDAO',
                                   $this->dao,
                                   array('RequestedObjectDAO'));

    $this->dao->setReturnValue('mapRequestToNode',
                                      array('object_id' => $object_id = 100),
                                      array(new IsAExpectation('MockRequest')));

    $controller = new MockSiteObjectController($this);
    $behaviour = new MockSiteObjectBehaviour($this);

    $this->filter->setReturnReference('getBehaviourByObjectId', $behaviour, array($object_id));

    $this->filter->setReturnReference('_getController',
                                  $controller,
                                  array(new IsAExpectation('MockSiteObjectBehaviour')));

    $controller->setReturnValue('getRequestedAction', $action = 'someAction');

    $this->dao->expectOnce('setPermissionsAction', array($action));
    $this->dao->expectOnce('setRequest', array(new IsAExpectation('MockRequest')));

    $this->dao->setReturnValue('fetch', $result = 'someFetchResult');

    $this->response->expectNever('redirect');

    $this->filter_chain->expectOnce('next');

    $this->filter->run($this->filter_chain, $this->request, $this->response);
  }
}

?>