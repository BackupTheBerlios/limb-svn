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
require_once(LIMB_DIR . '/core/template/components/datasource/LimbDatasourceComponent.class.php');
require_once(WACT_ROOT . '/template/components/list/list.inc.php');
require_once(LIMB_DIR . '/core/template/components/LimbPagerComponent.class.php');
require_once(LIMB_DIR . '/core/datasources/Datasource.interface.php');
require_once(LIMB_DIR . '/core/datasources/Countable.interface.php');
require_once(LIMB_DIR . '/core/request/Request.class.php');
require_once(LIMB_DIR . '/core/LimbToolkit.interface.php');

class LimbDatasourceComponentTestVersion //implements Datasource, Countable
{
  function fetch(){}
  function countTotal(){}
  function setLimit($limit){}
  function setOffset($offset){}
  function setOrder($order){}
  function setBar($bar){}
}

Mock :: generate('LimbToolkit');
Mock :: generate('Component');
Mock :: generate('ListComponent');
Mock :: generate('LimbDatasourceComponentTestVersion');
Mock :: generate('LimbPagerComponent');
Mock :: generate('Request');

Mock :: generatePartial('LimbDatasourceComponent',
                        'LimbDatasourceComponentSetupTargetsTestVersion',
                        array('getDataset'));

class LimbDatasourceComponentTest extends LimbTestCase
{
  var $component;
  var $datasource;
  var $toolkit;
  var $parent;
  var $request;

  function LimbDatasourceComponentTest()
  {
    parent :: LimbTestCase('limb datasource component test');
  }

  function setUp()
  {
    $this->parent = new MockComponent($this);

    $this->component = new LimbDatasourceComponent();
    $this->component->parent =& $this->parent;
    $this->component->setClassPath('test-datasource');

    $this->request = new MockRequest($this);

    $this->datasource = new MockLimbDatasourceComponentTestVersion($this);

    $this->toolkit = new MockLimbToolkit($this);

    $this->toolkit->setReturnReference('getRequest', $this->request);

    $this->toolkit->setReturnReference('getDatasource', $this->datasource, array('test-datasource'));

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
    $this->component->setParameter('bar', 'test parameter');
    $this->datasource->expectOnce('setBar', array('test parameter'));

    //we can't check it...
    //$this->component->setParameter('foo', 'test parameter');
    //$this->datasource->expectNever('setFoo');
  }

  function testSetOrderParameter1()
  {
    $this->component->setParameter('order', '');
    $this->datasource->expectNever('setOrder');
  }

  function testSetOrderParameter2()
  {
    $this->component->setParameter('order', 'c1 =AsC, c2 = DeSC , c3=Junky');
    $this->datasource->expectOnce('setOrder',
                                  array(array('c1' => 'ASC', 'c2' => 'DESC', 'c3' => 'ASC')));
  }

  function testSetOrderParameter3()
  {
    $this->component->setParameter('order', 'c1, c2 = Rand() ');//!!!mysql only
    $this->datasource->expectOnce('setOrder',
                                  array(array('c1' => 'ASC', 'c2' => 'RAND()')));
  }

  function testLimitParameter1()
  {
    $this->component->setParameter('limit', '10');
    $this->datasource->expectOnce('setLimit', array(10));
    $this->datasource->expectNever('setOffset');
  }

  function testLimitParameter2()
  {
    $this->component->setParameter('limit', '10, 20');
    $this->datasource->expectOnce('setLimit', array(10));
    $this->datasource->expectOnce('setOffset', array(20));
  }

  function testLimitParameterError()
  {
    $this->component->setParameter('limit', ',20');
    $this->datasource->expectNever('setLimit');
    $this->datasource->expectNever('setOffset');
  }

  function testSetupNavigatorNoNavigator()
  {
    $this->parent->expectOnce('findChild', array($pager_id = 'test-nav'));
    $this->parent->setReturnValue('findChild', null, array($pager_id));

    $this->component->setupNavigator($pager_id);

    $this->datasource->expectNever('setLimit');
    $this->datasource->expectNever('setOffset');
  }

  function testSetupNavigatorWithParamsInRequest()
  {
    $pager = new MockLimbPagerComponent($this);

    $this->parent->expectOnce('findChild', array($pager_id = 'test-nav'));
    $this->parent->setReturnReference('findChild', $pager, array($pager_id));

    $pager->expectOnce('getItemsPerPage');
    $pager->setReturnValue('getItemsPerPage', $limit = 100);

    $pager->setReturnValue('getDisplayedPageBeginItem', $offset = 200);

    $this->component->setClassPath('test-datasource');
    $this->toolkit->expectOnce('getDatasource', array('test-datasource'));
    $this->toolkit->setReturnReference('getDatasource', $this->datasource, array('test-datasource'));

    $this->datasource->expectOnce('countTotal');
    $this->datasource->setReturnValue('countTotal', $count = 13);
    $pager->expectOnce('setTotalItems', array($count));
    $pager->expectOnce('reset');

    $this->component->setupNavigator($pager_id);

    $this->datasource->expectOnce('setLimit', array($limit));
    $this->datasource->expectOnce('setOffset', array($offset));

    $pager->tally();
  }

  function testSetupTargets()
  {
    $component = new LimbDatasourceComponentSetupTargetsTestVersion($this);

    $component->parent =& $this->parent;
    $this->parent->expectArgumentsAt(0, 'findChild', array('target1'));
    $this->parent->expectArgumentsAt(1, 'findChild', array('target2'));
    $this->parent->setReturnReferenceAt(0, 'findChild', $target1 = new MockListComponent($this));
    $this->parent->setReturnReferenceAt(1, 'findChild', $target2 = new MockListComponent($this));

    $component->expectOnce('getDataset');
    $dataset = new ArrayDataset(array('some_data'));
    $component->setReturnReference('getDataset', $dataset);

    $target1->expectOnce('registerDataset', array($dataset));
    $target2->expectOnce('registerDataset', array($dataset));
    $component->setupTargets('target1, target2');

    $component->tally();
    $target1->tally();
    $target2->tally();
  }

  function testSetupTargetsFailedNoSuchRuntimeTarget()
  {
    $component = new LimbDatasourceComponentSetupTargetsTestVersion($this);

    $component->parent = $this->parent;
    $this->parent->expectArgumentsAt(0, 'findChild', array('target1'));
    $this->parent->setReturnValueAt(0, 'findChild', null);

    $component->expectOnce('getDataset');
    $dataset = new ArrayDataset(array('some_data'));
    $component->setReturnReference('getDataset', $dataset);

    $component->setupTargets('target1, target2');
    $this->assertTrue(catch('Exception', $e));

    $component->tally();
  }

}

?>