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
require_once(LIMB_DIR . '/class/core/filters/FilterChain.class.php');
require_once(LIMB_DIR . '/class/core/filters/LocaleDefinitionFilter.class.php');
require_once(LIMB_DIR . '/class/core/request/Request.class.php');
require_once(LIMB_DIR . '/class/core/request/Response.interface.php');
require_once(LIMB_DIR . '/class/core/datasources/RequestedObjectDatasource.class.php');
require_once(LIMB_DIR . '/class/core/LimbToolkit.interface.php');
require_once(LIMB_DIR . '/class/i18n/Locale.class.php');
require_once(LIMB_DIR . '/class/core/permissions/User.class.php');

Mock :: generate('LimbToolkit');
Mock :: generate('FilterChain');
Mock :: generate('Request');
Mock :: generate('Locale');
Mock :: generate('Response');
Mock :: generate('RequestedObjectDatasource');
Mock :: generate('User');

Mock :: generatePartial('LocaleDefinitionFilter',
                        'LocaleDefinitionFilterTestVersion',
                        array('_findSiteObjectLocaleId'));

class LocaleDefinitionFilterTest extends LimbTestCase
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
    $this->filter = new LocaleDefinitionFilterTestVersion($this);

    $this->toolkit = new MockLimbToolkit($this);
    $this->request = new MockRequest($this);
    $this->response = new MockResponse($this);
    $this->locale = new MockLocale($this);
    $this->filter_chain = new MockFilterChain($this);
    $this->datasource = new MockRequestedObjectDatasource($this);
    $this->user = new MockUser($this);

    $this->toolkit->setReturnReference('getDatasource',
                                   $this->datasource,
                                   array('RequestedObjectDatasource'));

    $this->toolkit->setReturnReference('getUser', $this->user);
    $this->toolkit->setReturnReference('getLocale', $this->locale);
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

  function testRunNodeNotFound()
  {
    $this->datasource->expectOnce('mapRequestToNode', array(new IsAExpectation('MockRequest')));
    $this->datasource->setReturnValue('mapRequestToNode', false);

    $this->toolkit->expectArgumentsAt(0, 'define', array('CONTENT_LOCALE_ID', DEFAULT_CONTENT_LOCALE_ID));
    $this->toolkit->expectArgumentsAt(1, 'define', array('MANAGEMENT_LOCALE_ID', DEFAULT_CONTENT_LOCALE_ID));

    $this->filter->run($this->filter_chain, $this->request, $this->response);
  }

  function testRunNodeFound1()
  {
    $this->datasource->expectOnce('mapRequestToNode', array(new IsAExpectation('MockRequest')));
    $this->datasource->setReturnValue('mapRequestToNode', $node = array('object_id' => $object_id = 100));

    $this->filter->expectOnce('_findSiteObjectLocaleId', array($object_id));
    $this->filter->setReturnValue('_findSiteObjectLocaleId', $locale_id = 'fr');

    $this->toolkit->expectOnce('getUser');

    $this->user->expectOnce('get', array('locale_id'));
    $this->user->setReturnValue('get', null);

    $this->toolkit->expectArgumentsAt(0, 'define', array('CONTENT_LOCALE_ID', $locale_id));
    $this->toolkit->expectOnce('constant', array('CONTENT_LOCALE_ID'));
    $this->toolkit->setReturnValue('constant', $locale_id);
    $this->toolkit->expectArgumentsAt(1, 'define', array('MANAGEMENT_LOCALE_ID', $locale_id));

    $this->filter->run($this->filter_chain, $this->request, $this->response);
  }

  function testRunNodeFound2()
  {
    $this->datasource->expectOnce('mapRequestToNode', array(new IsAExpectation('MockRequest')));
    $this->datasource->setReturnValue('mapRequestToNode', $node = array('object_id' => $object_id = 100));

    $this->filter->expectOnce('_findSiteObjectLocaleId', array($object_id));
    $this->filter->setReturnValue('_findSiteObjectLocaleId', $locale_id = 'fr');

    $this->toolkit->expectOnce('getUser');

    $this->user->expectOnce('get', array('locale_id'));
    $this->user->setReturnValue('get', $user_locale_id = 'de');

    $this->toolkit->expectArgumentsAt(0, 'define', array('CONTENT_LOCALE_ID', $locale_id));
    $this->toolkit->expectArgumentsAt(1, 'define', array('MANAGEMENT_LOCALE_ID', $user_locale_id));
    $this->filter->run($this->filter_chain, $this->request, $this->response);
  }

  function testRunNodeFound3()
  {
    $this->datasource->expectOnce('mapRequestToNode', array(new IsAExpectation('MockRequest')));
    $this->datasource->setReturnValue('mapRequestToNode', $node = array('object_id' => $object_id = 100));

    $this->filter->expectOnce('_findSiteObjectLocaleId', array($object_id));
    $this->filter->setReturnValue('_findSiteObjectLocaleId', null);

    $this->toolkit->expectOnce('getUser');

    $this->user->expectOnce('get', array('locale_id'));
    $this->user->setReturnValue('get', $user_locale_id = 'de');

    $this->toolkit->expectArgumentsAt(0, 'define', array('CONTENT_LOCALE_ID', DEFAULT_CONTENT_LOCALE_ID));
    $this->toolkit->expectArgumentsAt(1, 'define', array('MANAGEMENT_LOCALE_ID', $user_locale_id));

    $this->filter->run($this->filter_chain, $this->request, $this->response);
  }

  function testRunNodeFound4()
  {
    $this->datasource->expectOnce('mapRequestToNode', array(new IsAExpectation('MockRequest')));
    $this->datasource->setReturnValue('mapRequestToNode', $node = array('object_id' => $object_id = 100));

    $this->filter->expectOnce('_findSiteObjectLocaleId', array($object_id));
    $this->filter->setReturnValue('_findSiteObjectLocaleId', null);

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