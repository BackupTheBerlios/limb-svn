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
    $this->assertEqual(get_class($this->toolkit->createDBTable('SysSiteObject')),
                       'sys_site_object_db_table');
  }

  function testGetDatasource()
  {
    $this->assertEqual(get_class($this->toolkit->getDatasource('SiteObjectsDatasource')),
                       'site_objects_datasource');
  }

  function testCreateSiteObject()
  {
    $this->assertEqual(get_class($this->toolkit->createSiteObject('SiteObject')),
                       'site_object');
  }

  function testCreateDataMapper()
  {
    $this->assertEqual(get_class($this->toolkit->createDataMapper('SiteObjectMapper')),
                       'site_object_mapper');
  }

  function testCreateBehaviour()
  {
    $this->assertEqual(get_class($this->toolkit->createBehaviour('SiteObjectBehaviour')),
                       'site_object_behaviour');
  }

  function testGetDb()
  {
    $this->assertTrue(is_a($this->toolkit->getDB(), 'DbModule'));
  }

  function testGetTree()
  {
    $this->assertEqual(get_class($this->toolkit->getTree()),
                       'tree_decorator');
  }

  function testGetUser()
  {
    $this->assertEqual(get_class($this->toolkit->getUser()), 'user');
  }

  function testGetConfig()
  {
    registerTestingIni('test-config.ini', 'test = 1');

    $conf = $this->toolkit->getINI('test-config.ini');
    $this->assertEqual($conf->getOption('test'), 1);
  }

  function testGetAuthenticator()
  {
    $this->assertEqual(get_class($this->toolkit->getAuthenticator()),
                       'simple_authenticator');
  }

  function testGetAuthorizer()
  {
    $this->assertEqual(get_class($this->toolkit->getAuthorizer()),
                       'simple_authorizer');
  }

  function testGetRequest()
  {
    $this->assertEqual(get_class($this->toolkit->getRequest()),
                       'request');
  }

  function testGetResponse()
  {
    $this->assertEqual(get_class($this->toolkit->getResponse()),
                       'http_response');
  }

  function testGetCache()
  {
    $this->assertEqual(get_class($this->toolkit->getCache()),
                       'CacheRegistry');
  }

  function testGetLocale()
  {
    $this->assertEqual(get_class($this->toolkit->getLocale()),
                       'locale');
  }

  function testGetSession()
  {
    $this->assertEqual(get_class($this->toolkit->getSession()),
                       'session');
  }

  function testGetDataspace()
  {
    $this->assertEqual(get_class($this->toolkit->getDataspace()),
                       'dataspace');
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

    $this->assertEqual(get_class($this->toolkit->getView()),
                       'view_test_version');
  }


}

?>
