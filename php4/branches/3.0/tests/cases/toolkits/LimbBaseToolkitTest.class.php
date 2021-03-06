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
require_once(LIMB_DIR . '/core/LimbBaseToolkit.class.php');

class ViewTestVersion{}

class LimbBaseToolkitTest extends LimbTestCase
{
  var $toolkit;

  function LimbBaseToolkitTest()
  {
    parent :: LimbTestCase(__FILE__);
  }

  function setUp()
  {
    $this->toolkit = new LimbBaseToolkit();
  }

  function testDefineConstant()
  {
    $const = md5(mt_rand());
    $this->toolkit->define($const, 'test-value');
    $this->assertEqual($this->toolkit->constant($const), 'test-value');
  }

  function testCreateDbTable()
  {
    $this->assertIsA($this->toolkit->createDBTable('SysObject'),
                     'SysObjectDbTable');
  }

  function testCreateDAO()
  {
    $this->assertIsA($this->toolkit->createDAO('DAO'),
                     'DAO');
  }

  function testCreateObject()
  {
    $this->assertIsA($this->toolkit->createObject('Object'),
                     'Object');
  }

  function testCreateDataMapper()
  {
    $this->assertIsA($this->toolkit->createDataMapper('AbstractDataMapper'),
                     'AbstractDataMapper');
  }

  function testGetDb()
  {
    $this->assertIsA($this->toolkit->getDbConnection(),
                     'MysqlConnection');//???
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
                     'CachePersisterKeyDecorator');
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

  function testGetUOW()
  {
    $this->assertIsA($this->toolkit->getUOW(),
                     'UnitOfWork');
  }

  function testSwitchDataspace()
  {
    $d1 =& $this->toolkit->getDataspace();
    $d2 =& $this->toolkit->switchDataspace('test-dataspace');

    $d1->set('test', 0);
    $d2->set('test', 1);

    $this->assertTrue($d1->get('test') != $d2->get('test'));

    $d3 =& $this->toolkit->switchDataspace('default');

    $this->assertReference($d1, $d3);
    $this->assertTrue($d1->get('test') == $d3->get('test'));
  }

  function testSetGetView()
  {
    $view = new ViewTestVersion();
    $this->toolkit->setView($view);

    $this->assertIsA($this->toolkit->getView(),
                     'ViewTestVersion');
  }

  function testSetGetService()
  {
    $service = new Object();
    $this->toolkit->setService($service);

    $this->assertReference($this->toolkit->getService(), $service);
  }

  function testGetPath2IdTranslator()
  {
    $this->assertIsA($this->toolkit->getPath2IdTranslator(),
                     'Path2IdTranslator');
  }

  function testGetNullRequestResolver()
  {
    $this->assertNull($this->toolkit->getRequestResolver('null'));
  }

  function testGetRequestResolver()
  {
    $this->toolkit->setRequestResolver('test', $o = new Object());
    $this->assertEqual($this->toolkit->getRequestResolver('test'), $o);
  }
}

?>
