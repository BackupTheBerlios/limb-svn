<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: limb@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . '/class/core/filters/filter_chain.class.php');
require_once(LIMB_DIR . '/class/core/filters/locale_definition_filter.class.php');
require_once(LIMB_DIR . '/class/core/request/request.class.php');
require_once(LIMB_DIR . '/class/core/request/response.interface.php');
require_once(LIMB_DIR . '/class/core/datasources/requested_object_datasource.class.php');
require_once(LIMB_DIR . '/class/core/limb_toolkit.interface.php');
require_once(LIMB_DIR . '/class/i18n/locale.class.php');
require_once(LIMB_DIR . '/class/core/permissions/user.class.php');

Mock :: generate('LimbToolkit');
Mock :: generate('filter_chain');
Mock :: generate('request');
Mock :: generate('locale');
Mock :: generate('response');
Mock :: generate('requested_object_datasource');
Mock :: generate('user');

Mock :: generatePartial('locale_definition_filter',
                        'locale_definition_filter_test_version',
                        array('_find_site_object_locale_id')); 

class locale_definition_filter_test extends LimbTestCase
{
  var $filter_chain;
  var $filter;
  var $request;
  var $locale;
  var $response;
  var $datasource;
  var $toolkit;
  var $user;
  
  function setUp()
  {
    $this->filter = new locale_definition_filter_test_version($this);
    
    $this->toolkit = new MockLimbToolkit($this);
    $this->request = new Mockrequest($this);
    $this->response = new Mockresponse($this);
    $this->locale = new Mocklocale($this);
    $this->filter_chain = new Mockfilter_chain($this);
    $this->datasource = new Mockrequested_object_datasource($this);
    $this->user = new Mockuser($this);
    
    $this->toolkit->setReturnValue('getDatasource', 
                                   $this->datasource, 
                                   array('requested_object_datasource'));
    
    $this->toolkit->setReturnValue('getUser', $this->user);
    $this->toolkit->setReturnValue('getLocale', $this->locale);
    $this->locale->expectOnce('setlocale');
    
    $this->filter_chain->expectOnce('next');
    
    Limb :: registerToolkit($this->toolkit);
  }
  
  function tearDown()
  {
    $this->request->tally();
    $this->response->tally();  
    $this->filter_chain->tally();
    $this->datasource->tally();
    $this->locale->tally();
    $this->user->tally();

    Limb :: popToolkit();    
  }
  
  function test_run_node_not_found()
  {
    $this->datasource->expectOnce('map_request_to_node', array(new isAExpectation('Mockrequest')));
    $this->datasource->setReturnValue('map_request_to_node', false);
    
    $this->toolkit->expectArgumentsAt(0, 'define', array('CONTENT_LOCALE_ID', DEFAULT_CONTENT_LOCALE_ID));
    $this->toolkit->expectArgumentsAt(1, 'define', array('MANAGEMENT_LOCALE_ID', DEFAULT_CONTENT_LOCALE_ID));
    
    $this->filter->run($this->filter_chain, $this->request, $this->response);
  }

  function test_run_node_found_1()
  {
    $this->datasource->expectOnce('map_request_to_node', array(new isAExpectation('Mockrequest')));
    $this->datasource->setReturnValue('map_request_to_node', $node = array('object_id' => $object_id = 100));
    
    $this->filter->expectOnce('_find_site_object_locale_id', array($object_id));
    $this->filter->setReturnValue('_find_site_object_locale_id', $locale_id = 'fr');
    
    $this->toolkit->expectOnce('getUser');
    
    $this->user->expectOnce('get', array('locale_id'));
    $this->user->setReturnValue('get', null);

    $this->toolkit->expectArgumentsAt(0, 'define', array('CONTENT_LOCALE_ID', $locale_id));
    $this->toolkit->expectOnce('constant', array('CONTENT_LOCALE_ID'));
    $this->toolkit->setReturnValue('constant', $locale_id);
    $this->toolkit->expectArgumentsAt(1, 'define', array('MANAGEMENT_LOCALE_ID', $locale_id));

    $this->filter->run($this->filter_chain, $this->request, $this->response);
  }

  function test_run_node_found_2()
  {
    $this->datasource->expectOnce('map_request_to_node', array(new isAExpectation('Mockrequest')));
    $this->datasource->setReturnValue('map_request_to_node', $node = array('object_id' => $object_id = 100));
    
    $this->filter->expectOnce('_find_site_object_locale_id', array($object_id));
    $this->filter->setReturnValue('_find_site_object_locale_id', $locale_id = 'fr');
    
    $this->toolkit->expectOnce('getUser');
    
    $this->user->expectOnce('get', array('locale_id'));
    $this->user->setReturnValue('get', $user_locale_id = 'de');

    $this->toolkit->expectArgumentsAt(0, 'define', array('CONTENT_LOCALE_ID', $locale_id));
    $this->toolkit->expectArgumentsAt(1, 'define', array('MANAGEMENT_LOCALE_ID', $user_locale_id));
    $this->filter->run($this->filter_chain, $this->request, $this->response);
  }

  function test_run_node_found_3()
  {
    $this->datasource->expectOnce('map_request_to_node', array(new isAExpectation('Mockrequest')));
    $this->datasource->setReturnValue('map_request_to_node', $node = array('object_id' => $object_id = 100));
    
    $this->filter->expectOnce('_find_site_object_locale_id', array($object_id));
    $this->filter->setReturnValue('_find_site_object_locale_id', null);
    
    $this->toolkit->expectOnce('getUser');
    
    $this->user->expectOnce('get', array('locale_id'));
    $this->user->setReturnValue('get', $user_locale_id = 'de');

    $this->toolkit->expectArgumentsAt(0, 'define', array('CONTENT_LOCALE_ID', DEFAULT_CONTENT_LOCALE_ID));
    $this->toolkit->expectArgumentsAt(1, 'define', array('MANAGEMENT_LOCALE_ID', $user_locale_id));

    $this->filter->run($this->filter_chain, $this->request, $this->response);
  }

  function test_run_node_found_4()
  {
    $this->datasource->expectOnce('map_request_to_node', array(new isAExpectation('Mockrequest')));
    $this->datasource->setReturnValue('map_request_to_node', $node = array('object_id' => $object_id = 100));
    
    $this->filter->expectOnce('_find_site_object_locale_id', array($object_id));
    $this->filter->setReturnValue('_find_site_object_locale_id', null);
    
    $this->toolkit->expectOnce('getUser');
    
    $this->user->expectOnce('get', array('locale_id'));
    $this->user->setReturnValue('get', null);

    $this->toolkit->expectArgumentsAt(0, 'define', array('CONTENT_LOCALE_ID', DEFAULT_CONTENT_LOCALE_ID));
    $this->toolkit->expectOnce('constant', array('CONTENT_LOCALE_ID'));
    $this->toolkit->setReturnValue('constant', DEFAULT_CONTENT_LOCALE_ID);
    $this->toolkit->expectArgumentsAt(1, 'define', array('MANAGEMENT_LOCALE_ID', DEFAULT_CONTENT_LOCALE_ID));

    $this->filter->run($this->filter_chain, $this->request, $this->response);
  }
  
}

?>