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
require_once(LIMB_DIR . '/class/core/BaseLimbToolkit.class.php');

class ViewTestVersion{}

class BaseLimbToolkitTest extends LimbTestCase
{
  var $toolkit;

  function setUp()
  {
    $this->toolkit = new BaseLimbToolkit();
  }

  function testDefineConstant()
  {
    $const = md5(mt_rand());
    $this->toolkit->define($const, 'test-value');
    $this->assertEqual($this->toolkit->constant($const), 'test-value');
  }

  function testCreateDbTable()
  {
    $this->assertIsA($this->toolkit->createDBTable('SysSiteObject'),
                     'SysSiteObjectDbTable');
  }

  function testGetDatasource()
  {
    $this->assertIsA($this->toolkit->getDatasource('SiteObjectsDatasource'),
                     'SiteObjectsDatasource');
  }

  function testCreateSiteObject()
  {
    $this->assertIsA($this->toolkit->createSiteObject('SiteObject'),
                     'SiteObject');
  }

  function testCreateDataMapper()
  {
    $this->assertIsA($this->toolkit->createDataMapper('SiteObjectMapper'),
                     'SiteObjectMapper');
  }

  function testCreateBehaviour()
  {
    $this->assertIsA($this->toolkit->createBehaviour('SiteObjectBehaviour'),
                     'SiteObjectBehaviour');
  }

  function testGetDb()
  {
    $this->assertIsA($this->toolkit->getDB(),
                     'DbModule');
  }

  function testGetTree()
  {
    $this->assertIsA($this->toolkit->getTree(),
                     'TreeDecorator');
  }

  function testGetUser()
  {
    $this->assertIsA($this->toolkit->getUser(),
                     'User');
  }

  function testGetConfig()
  {
    registerTestingIni('test-config.ini', 'test = 1');

    $conf = $this->toolkit->getINI('test-config.ini');
    $this->assertEqual($conf->getOption('test'), 1);
  }

  function testGetAuthenticator()
  {
    $this->assertIsA($this->toolkit->getAuthenticator(),
                     'SimpleAuthenticator');
  }

  function testGetAuthorizer()
  {
    $this->assertIsA($this->toolkit->getAuthorizer(),
                     'SimpleAuthorizer');
  }

  function testGetRequest()
  {
    $this->assertIsA($this->toolkit->getRequest(),
                     'Request');
  }

  function testGetResponse()
  {
    $this->assertIsA($this->toolkit->getResponse(),
                     'HttpResponse');
  }

  function testGetCache()
  {
    $this->assertIsA($this->toolkit->getCache(),
                     'CacheRegistry');
  }

  function testGetLocale()
  {
    $this->assertIsA($this->toolkit->getLocale(),
                     'Locale');
  }

  function testGetSession()
  {
    $this->assertIsA($this->toolkit->getSession(),
                     'Session');
  }

  function testGetDataspace()
  {
    $this->assertIsA($this->toolkit->getDataspace(),
                     'Dataspace');
  }

  function testSwitchDataspace()
  {
    $d1 = $this->toolkit->getDataspace();
    $d2 = $this->toolkit->switchDataspace('test-dataspace');

    $this->assertTrue($d1 !== $d2);

    $d3 = $this->toolkit->switchDataspace('default');

    $this->assertTrue($d1 === $d3);
  }

  function testSetGetView()
  {
    $view = new ViewTestVersion();
    $this->toolkit->setView($view);

    $this->assertIsA($this->toolkit->getView(),
                     'ViewTestVersion');
  }


}

?>
