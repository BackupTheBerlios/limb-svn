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
require_once(LIMB_DIR . '/class/template/components/datasource/DatasourceComponent.class.php');
require_once(LIMB_DIR . '/class/template/components/ListComponent.class.php');
require_once(LIMB_DIR . '/class/template/Component.class.php');
require_once(LIMB_DIR . '/class/template/components/PagerComponent.class.php');
require_once(LIMB_DIR . '/class/core/datasources/Datasource.interface.php');
require_once(LIMB_DIR . '/class/core/datasources/Countable.interface.php');
require_once(LIMB_DIR . '/class/core/request/Request.class.php');
require_once(LIMB_DIR . '/class/core/LimbToolkit.interface.php');

class DatasourceComponentTestVersion //implements Datasource, Countable
{
  function fetch(){}
  function countTotal(){}
  function setLimit($limit){}
  function setOffset($offset){}
  function setOrder($order){}
}

Mock :: generate('LimbToolkit');
Mock :: generate('Component');
Mock :: generate('ListComponent');
Mock :: generate('DatasourceComponentTestVersion');
Mock :: generate('PagerComponent');
Mock :: generate('Request');

Mock :: generatePartial('DatasourceComponent',
                        'DatasourceComponentSetupTargetsTestVersion',
                        array('getDataset'));

class DatasourceComponentTest extends LimbTestCase
{
  var $component;
  var $datasource;
  var $toolkit;
  var $parent;
  var $request;

  function setUp()
  {
    $this->toolkit = new MockLimbToolkit($this);

    $this->parent = new MockComponent($this);

    $this->component = new DatasourceComponent();
    $this->component->parent = $this->parent;

    $this->request = new MockRequest($this);

    $this->datasource = new MockDatasourceComponentTestVersion($this);

    $this->toolkit->setReturnReference('getRequest', $this->request);

    Limb :: registerToolkit($this->toolkit);
  }

  function tearDown()
  {
    $this->parent->tally();
    $this->request->tally();
    $this->datasource->tally();
    $this->toolkit->tally();

    Limb :: popToolkit();
  }

  function testSetGetParameter()
  {
    $this->component->setParameter('test', 'test parameter');
    $this->assertEqual($this->component->getParameter('test'), 'test parameter');
  }

  function testGetNonexistentParameter()
  {
    $this->assertNull($this->component->getParameter('test'));
  }

  function testSetOrderParameter1()
  {
    $this->component->setParameter('order', '');
    $this->assertNull($this->component->getParameter('order'));
  }

  function testSetOrderParameter2()
  {
    $this->component->setParameter('order', 'c1 =AsC, c2 = DeSC , c3=Junky');
    $this->assertEqual($this->component->getParameter('order'),
                       array('c1' => 'ASC', 'c2' => 'DESC', 'c3' => 'ASC'));
  }

  function testSetOrderParameter3()
  {
    $this->component->setParameter('order', 'c1, c2 = Rand() ');//!!!mysql only
    $this->assertEqual($this->component->getParameter('order'),
                       array('c1' => 'ASC', 'c2' => 'RAND()'));
  }

  function testLimitParameter1()
  {
    $this->component->setParameter('limit', '10');
    $this->assertEqual($this->component->getParameter('limit'), 10);
    $this->assertNull($this->component->getParameter('offset'));
  }

  function testLimitParameter2()
  {
    $this->component->setParameter('limit', '10, 20');
    $this->assertEqual($this->component->getParameter('limit'), 10);
    $this->assertEqual($this->component->getParameter('offset'), 20);
  }

  function testLimitParameter3()
  {
    $this->component->setParameter('limit', ',20');
    $this->assertNull($this->component->getParameter('limit'));
    $this->assertNull($this->component->getParameter('offset'));
  }

  function testSetupNavigatorNoNavigator()
  {
    $pager = new MockPagerComponent($this);

    $this->parent->expectOnce('findChild', array($pager_id = 'test-nav'));
    $this->parent->setReturnValue('findChild', null, array($pager_id));

    $this->request->expectNever('hasAttribute');

    $this->component->setupNavigator($pager_id);

    $this->assertNull($this->component->getParameter('limit'));
    $this->assertNull($this->component->getParameter('offset'));

    $pager->tally();
  }

  function testSetupNavigatorWithParamsInRequest()
  {
    $pager = new MockPagerComponent($this);

    $this->parent->expectOnce('findChild', array($pager_id = 'test-nav'));
    $this->parent->setReturnValue('findChild', $pager, array($pager_id));

    $pager->expectOnce('getItemsPerPage');
    $pager->setReturnValue('getItemsPerPage', 100);

    $pager->expectOnce('getServerId');
    $pager->setReturnValue('getServerId', $pager_id);

    $this->request->expectOnce('hasAttribute', array('page_' . $pager_id));
    $this->request->setReturnValue('hasAttribute', true, array('page_' . $pager_id));

    $this->request->expectOnce('get', array('page_' . $pager_id));
    $this->request->setReturnValue('get', 10, array('page_' . $pager_id));

    $this->component->setDatasourcePath('test-datasource');
    $this->toolkit->expectOnce('getDatasource', array('test-datasource'));
    $this->toolkit->setReturnReference('getDatasource', $this->datasource, array('test-datasource'));

    $this->datasource->expectOnce('countTotal');
    $this->datasource->setReturnValue('countTotal', $count = 13);
    $pager->expectOnce('setTotalItems', array($count));

    $this->component->setupNavigator($pager_id);

    $this->assertEqual($this->component->getParameter('limit'), 100);
    $this->assertEqual($this->component->getParameter('offset'), (10-1)*100);

    $pager->tally();
  }

  function testSetupNavigatorNoParamsInRequest()
  {
    $pager = new MockPagerComponent($this);

    $this->parent->expectOnce('findChild', array($pager_id = 'test-nav'));
    $this->parent->setReturnValue('findChild', $pager, array($pager_id));

    $pager->expectOnce('getItemsPerPage');
    $pager->expectOnce('getServerId');
    $pager->setReturnValue('getItemsPerPage', 100);
    $pager->setReturnValue('getServerId', $pager_id);

    $this->request->expectOnce('hasAttribute', array('page_' . $pager_id));
    $this->request->setReturnValue('hasAttribute', false, array('page_' . $pager_id));
    $this->request->expectNever('get');

    $this->component->setDatasourcePath('test-datasource');
    $this->toolkit->expectOnce('getDatasource', array('test-datasource'));
    $this->toolkit->setReturnReference('getDatasource', $this->datasource, array('test-datasource'));

    $this->datasource->expectOnce('countTotal');
    $this->datasource->setReturnValue('countTotal', $count = 13);
    $pager->expectOnce('setTotalItems', array($count));

    $this->component->setupNavigator($pager_id);

    $this->assertEqual($this->component->getParameter('limit'), 100);
    $this->assertNull($this->component->getParameter('offset'));

    $pager->tally();
  }

  function testGetDataset()
  {
    $this->component->setParameter('limit', '10, 2');
    $this->component->setParameter('order', 'col1=ASC');
    $this->component->setParameter('junky', 'trash');

    $this->component->setDatasourcePath('test-datasource');
    $this->toolkit->expectOnce('getDatasource', array('test-datasource'));
    $this->toolkit->setReturnReference('getDatasource', $this->datasource, array('test-datasource'));

    $this->datasource->expectOnce('setLimit', array(10));
    $this->datasource->expectOnce('setOffset', array(2));
    $this->datasource->expectOnce('setOrder', array(array('col1' => 'ASC')));

    $this->datasource->expectOnce('fetch');
    $this->datasource->setReturnValue('fetch', $result = array('whatever'));
    $this->assertEqual(new ArrayDataset($result), $this->component->getDataset());
  }

  function testSetupTargets()
  {
    $component = new DatasourceComponentSetupTargetsTestVersion($this);

    $component->parent = $this->parent;
    $this->parent->expectArgumentsAt(0, 'findChild', array('target1'));
    $this->parent->expectArgumentsAt(1, 'findChild', array('target2'));
    $this->parent->setReturnValueAt(0, 'findChild', $target1 = new MockListComponent($this));
    $this->parent->setReturnValueAt(1, 'findChild', $target2 = new MockListComponent($this));

    $component->expectOnce('getDataset');
    $dataset = new ArrayDataset(array('some_data'));
    $component->setReturnValue('getDataset', $dataset);

    $target1->expectOnce('registerDataset', array($dataset));
    $target2->expectOnce('registerDataset', array($dataset));
    $component->setupTargets('target1, target2');

    $component->tally();
    $target1->tally();
    $target2->tally();
  }

  function testSetupTargetsFailedNoSuchRuntimeTarget()
  {
    $component = new DatasourceComponentSetupTargetsTestVersion($this);

    $component->parent = $this->parent;
    $this->parent->expectArgumentsAt(0, 'findChild', array('target1'));
    $this->parent->setReturnValueAt(0, 'findChild', null);

    $component->expectOnce('getDataset');
    $dataset = new ArrayDataset(array('some_data'));
    $component->setReturnValue('getDataset', $dataset);

    $this->assertTrue(Limb :: isError($component->setupTargets('target1, target2')));
    $this->assertTrue(false);

    $component->tally();
  }

}

?>