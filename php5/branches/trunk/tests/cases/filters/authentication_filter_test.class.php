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
require_once(LIMB_DIR . '/class/core/filters/filter_chain.class.php');
require_once(LIMB_DIR . '/class/core/filters/authentication_filter.class.php');
require_once(LIMB_DIR . '/class/core/site_objects/site_object.class.php');
require_once(LIMB_DIR . '/class/core/site_objects/site_object_controller.class.php');
require_once(LIMB_DIR . '/class/core/request/request.class.php');
require_once(LIMB_DIR . '/class/core/datasources/requested_object_datasource.class.php');
require_once(LIMB_DIR . '/class/core/limb_toolkit.interface.php');
require_once(LIMB_DIR . '/class/core/behaviours/site_object_behaviour.class.php');
require_once(LIMB_DIR . '/class/core/request/http_response.class.php');
require_once(LIMB_DIR . '/class/core/permissions/user.class.php');
require_once(LIMB_DIR . '/class/core/permissions/authenticator.interface.php');
require_once(LIMB_DIR . '/class/lib/util/ini.class.php');

Mock :: generate('LimbToolkit');
Mock :: generate('filter_chain');
Mock :: generate('http_response');
Mock :: generate('requested_object_datasource');
Mock :: generate('request');
Mock :: generate('site_object_controller');
Mock :: generate('site_object_behaviour');
Mock :: generate('response');
Mock :: generate('user');
Mock :: generate('authenticator');
Mock :: generate('ini');

Mock :: generatePartial('authentication_filter',
                        'authentication_filter_test_version',
                        array('_get_controller',
                              'get_behaviour_by_object_id',
                              'initialize_user',
                              'process_404_error'));

class authentication_filter_test extends LimbTestCase
{
  var $filter_chain;
  var $filter;
  var $request;
  var $datasource;
  var $toolkit;
  var $response;
  var $ini;

  function setUp()
  {
    $this->filter = new authentication_filter_test_version($this);

    $this->toolkit = new MockLimbToolkit($this);
    $this->ini = new Mockini($this);

    $this->toolkit->setReturnValue('getINI', $this->ini);

    $this->datasource = new Mockrequested_object_datasource($this);
    $this->request = new Mockrequest($this);
    $this->filter_chain = new Mockfilter_chain($this);
    $this->response = new Mockhttp_response($this);

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

  function test_initialize_user_is_logged_in()
  {
    $user = new Mockuser($this);
    $authenticator = new Mockauthenticator($this);

    $this->toolkit->expectOnce('getUser');
    $this->toolkit->setReturnValue('getUser', $user);

    $user->expectOnce('is_logged_in');
    $user->setReturnValue('is_logged_in', true);
    $authenticator->expectNever('login', array(array('login' => '', 'password' => '')));

    $filter = new authentication_filter();

    $filter->initialize_user();

    $user->tally();
    $authenticator->tally();
  }

  function test_initialize_user_not_logged_in()
  {
    $user = new Mockuser($this);
    $authenticator = new Mockauthenticator($this);

    $this->toolkit->expectOnce('getUser');
    $this->toolkit->setReturnValue('getUser', $user);

    $user->expectOnce('is_logged_in');
    $user->setReturnValue('is_logged_in', false);

    $this->toolkit->expectOnce('getAuthenticator');
    $this->toolkit->setReturnValue('getAuthenticator', $authenticator);

    $authenticator->expectOnce('login', array(array('login' => '', 'password' => '')));

    $filter = new authentication_filter();

    $filter->initialize_user();

    $user->tally();
    $authenticator->tally();
  }

  function test_process_404_error_from_ini()
  {
    $this->ini->expectOnce('get_option', array('404', 'ERROR_DOCUMENTS'));
    $this->ini->setReturnValue('get_option', $error_path = '/root/404');

    $filter = new authentication_filter();

    $this->response->expectOnce('redirect', array($error_path));
    $this->response->expectNever('header');

    $filter->process_404_error($this->request, $this->response);
  }

  function test_process_404_error_not_found()
  {
    $filter = new authentication_filter();

    $this->response->expectNever('redirect');
    $this->response->expectOnce('header', array("HTTP/1.1 404 Not found"));

    $filter->process_404_error($this->request, $this->response);
  }

  function test_run_node_not_found()
  {
    $this->toolkit->setReturnValue('getDatasource',
                                   $this->datasource,
                                   array('requested_object_datasource'));

    $this->datasource->setReturnValue('map_request_to_node',
                                      null,
                                      array(new IsAExpectation('Mockrequest')));

    $this->filter->expectOnce('process_404_error');
    $this->filter_chain->expectOnce('next');

    $this->filter->run($this->filter_chain, $this->request, $this->response);
  }

  function test_run_no_such_action()
  {
    $this->toolkit->setReturnValue('getDatasource',
                                   $this->datasource,
                                   array('requested_object_datasource'));

    $this->datasource->setReturnValue('map_request_to_node',
                                      array('object_id' => $object_id = 100),
                                      array(new IsAExpectation('Mockrequest')));

    $controller = new Mocksite_object_controller($this);
    $behaviour = new Mocksite_object_behaviour($this);

    $this->filter->setReturnValue('get_behaviour_by_object_id', $behaviour, array($object_id));

    $this->filter->setReturnValue('_get_controller',
                                  $controller,
                                  array(new IsAExpectation('Mocksite_object_behaviour')));

    $controller->setReturnValue('get_requested_action', null);

    $this->filter->expectOnce('process_404_error');
    $this->filter_chain->expectOnce('next');

    $this->filter->run($this->filter_chain, $this->request, $this->response);
  }

  function test_run_object_is_not_accessible()
  {
    $this->toolkit->setReturnValue('getDatasource',
                                   $this->datasource,
                                   array('requested_object_datasource'));

    $this->datasource->setReturnValue('map_request_to_node',
                                      array('object_id' => $object_id = 100),
                                      array(new IsAExpectation('Mockrequest')));

    $controller = new Mocksite_object_controller($this);
    $behaviour = new Mocksite_object_behaviour($this);

    $this->filter->setReturnValue('get_behaviour_by_object_id', $behaviour, array($object_id));

    $this->filter->setReturnValue('_get_controller',
                                  $controller,
                                  array(new IsAExpectation('Mocksite_object_behaviour')));

    $controller->setReturnValue('get_requested_action', $action = 'some_actin');

    $this->datasource->expectOnce('set_permissions_action', array($action));
    $this->datasource->expectOnce('set_request', array(new IsAExpectation('Mockrequest')));

    $this->datasource->setReturnValue('fetch', null);

    $this->response->expectOnce('redirect');

    $this->filter_chain->expectNever('next');

    $this->filter->run($this->filter_chain, $this->request, $this->response);
  }

  function test_run_ok()
  {
    $this->toolkit->setReturnValue('getDatasource',
                                   $this->datasource,
                                   array('requested_object_datasource'));

    $this->datasource->setReturnValue('map_request_to_node',
                                      array('object_id' => $object_id = 100),
                                      array(new IsAExpectation('Mockrequest')));

    $controller = new Mocksite_object_controller($this);
    $behaviour = new Mocksite_object_behaviour($this);

    $this->filter->setReturnValue('get_behaviour_by_object_id', $behaviour, array($object_id));

    $this->filter->setReturnValue('_get_controller',
                                  $controller,
                                  array(new IsAExpectation('Mocksite_object_behaviour')));

    $controller->setReturnValue('get_requested_action', $action = 'some_actin');

    $this->datasource->expectOnce('set_permissions_action', array($action));
    $this->datasource->expectOnce('set_request', array(new IsAExpectation('Mockrequest')));

    $this->datasource->setReturnValue('fetch', $result = 'some_fetch_result');

    $this->response->expectNever('redirect');

    $this->filter_chain->expectOnce('next');

    $this->filter->run($this->filter_chain, $this->request, $this->response);
  }
}

?>