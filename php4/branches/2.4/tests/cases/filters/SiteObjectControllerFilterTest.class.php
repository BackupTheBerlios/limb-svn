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
require_once(LIMB_DIR . '/core/filters/SiteObjectControllerFilter.class.php');
require_once(LIMB_DIR . '/core/site_objects/SiteObject.class.php');
require_once(LIMB_DIR . '/core/site_objects/SiteObjectController.class.php');
require_once(LIMB_DIR . '/core/request/Request.class.php');
require_once(LIMB_DIR . '/core/daos/RequestedObjectDAO.class.php');
require_once(LIMB_DIR . '/core/LimbToolkit.interface.php');
require_once(LIMB_DIR . '/core/behaviours/SiteObjectBehaviour.class.php');
require_once(LIMB_DIR . '/core/request/Response.interface.php');

Mock :: generate('LimbToolkit');
Mock :: generate('FilterChain');
Mock :: generate('Request');
Mock :: generate('RequestedObjectDAO');
Mock :: generate('SiteObject');
Mock :: generate('SiteObjectController');
Mock :: generate('SiteObjectBehaviour');
Mock :: generate('Response');

Mock :: generatePartial('SiteObjectControllerFilter',
                        'SiteObjectControllerFilterTestVersion',
                        array('_getController'));

class SiteObjectControllerFilterTest extends LimbTestCase
{
  var $filter_chain;
  var $filter;
  var $request;
  var $dao;
  var $toolkit;
  var $site_object;
  var $controller;
  var $behaviour;
  var $response;

  function setUp()
  {
    $this->filter = new SiteObjectControllerFilterTestVersion($this);

    $this->toolkit = new MockLimbToolkit($this);
    $this->site_object = new MockSiteObject($this);
    $this->request = new MockRequest($this);
    $this->filter_chain = new MockFilterChain($this);
    $this->dao = new MockRequestedObjectDAO($this);
    $this->controller = new MockSiteObjectController($this);
    $this->behaviour = new MockSiteObjectBehaviour($this);
    $this->response = new MockResponse($this);

    $this->dao->expectOnce('setRequest', array(new IsAExpectation('MockRequest')));
    $this->dao->expectOnce('fetch');

    $this->toolkit->setReturnReference('createDAO',
                                   $this->dao,
                                   array('RequestedObjectDAO'));

    $this->toolkit->setReturnReference('createSiteObject', $this->site_object, array('SiteObject'));

    $this->filter_chain->expectOnce('next');

    $this->site_object->setReturnReference('getController', $this->controller);

    $this->controller->expectOnce('process', array(new IsAExpectation('MockRequest')));

    Limb :: registerToolkit($this->toolkit);
  }

  function tearDown()
  {
    $this->request->tally();
    $this->filter_chain->tally();
    $this->dao->tally();
    $this->site_object->tally();
    $this->filter->tally();
    $this->behaviour->tally();
    $this->response->tally();

    Limb :: popToolkit();
  }

  function testRun()
  {
    $this->dao->setReturnValue('fetch', array('class_name' => 'SiteObject'));

    $this->filter->run($this->filter_chain, $this->request, $this->response);
  }
}

?>