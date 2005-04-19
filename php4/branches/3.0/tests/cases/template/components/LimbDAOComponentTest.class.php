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
require_once(LIMB_DIR . '/core/template/components/dao/LimbDAOComponent.class.php');
require_once(WACT_ROOT . '/template/components/list/list.inc.php');
require_once(WACT_ROOT . '/iterator/pagedarraydataset.inc.php');
require_once(LIMB_DIR . '/core/template/components/LimbPagerComponent.class.php');
require_once(LIMB_DIR . '/core/request/Request.class.php');
require_once(LIMB_DIR . '/core/LimbBaseToolkit.class.php');

Mock :: generate('LimbBaseToolkit', 'MockLimbToolkit');
Mock :: generate('Component');
Mock :: generate('ListComponent');
Mock :: generate('LimbDAOComponentTestVersion');
Mock :: generate('LimbPagerComponent');
Mock :: generate('Request');
Mock :: generate('PagedArrayDataSet');

Mock :: generatePartial('LimbDAOComponent',
                        'LimbDAOComponentSetupTargetsTestVersion',
                        array('getDataset'));

class LimbDAOComponentTest extends LimbTestCase
{
  var $component;
  var $dao;
  var $toolkit;
  var $parent;
  var $request;

  function LimbDAOComponentTest()
  {
    parent :: LimbTestCase(__FILE__);
  }

  function setUp()
  {
    $this->parent = new MockComponent($this);

    $this->component = new LimbDAOComponent();
    $this->component->parent =& $this->parent;
    $this->component->setClassPath('test-dao');

    $this->request = new MockRequest($this);

    $this->toolkit = new MockLimbToolkit($this);

    $this->toolkit->setReturnReference('getRequest', $this->request);

    Limb :: registerToolkit($this->toolkit);
  }

  function tearDown()
  {
    $this->parent->tally();
    $this->request->tally();
    $this->toolkit->tally();

    Limb :: restoreToolkit();
  }

  function testProcessSeveralTargetsNoNavigator()
  {
    $component = new LimbDAOComponentSetupTargetsTestVersion($this);

    $component->parent =& $this->parent;
    $this->parent->expectArgumentsAt(0, 'findChild', array('target1'));
    $this->parent->expectArgumentsAt(1, 'findChild', array('target2'));
    $this->parent->setReturnReferenceAt(0, 'findChild', $target1 = new MockListComponent($this));
    $this->parent->setReturnReferenceAt(1, 'findChild', $target2 = new MockListComponent($this));

    $component->expectOnce('getDataset');
    $dataset = new PagedArrayDataset(array('some_data'));
    $component->setReturnReference('getDataset', $dataset);

    $target1->expectOnce('registerDataset', array($dataset));
    $target2->expectOnce('registerDataset', array($dataset));
    $component->setTargets(array('target1' , 'target2'));
    $component->process();

    $component->tally();
    $target1->tally();
    $target2->tally();
  }

  function testProcessSeveralTargetsWithNavigator()
  {
    $component = new LimbDAOComponentSetupTargetsTestVersion($this);

    $component->parent =& $this->parent;
    $this->parent->setReturnReference('findChild', $target1 = new MockListComponent($this), array('target1'));
    $this->parent->setReturnReference('findChild', $target2 = new MockListComponent($this), array('target2'));
    $this->parent->setReturnReference('findChild', $pager = new MockLimbPagerComponent($this), array('pager'));

    $component->expectOnce('getDataset');
    $rs = new MockPagedArrayDataSet($this);
    $rs->expectOnce('paginate', array(new IsAExpectation('MockLimbPagerComponent')));
    $component->setReturnReference('getDataset', $rs);

    $target1->expectOnce('registerDataset', array(new IsAExpectation('MockPagedArrayDataSet')));
    $target2->expectOnce('registerDataset', array(new IsAExpectation('MockPagedArrayDataSet')));
    $component->setTargets(array('target1', 'target2'));
    $component->setNavigator('pager');

    $component->process();

    $component->tally();
    $target1->tally();
    $target2->tally();
    $pager->tally();
    $rs->tally();
  }

  function testSetupTargetsFailedNoSuchRuntimeTarget()
  {
    $component = new LimbDAOComponentSetupTargetsTestVersion($this);

    $component->parent = $this->parent;
    $this->parent->expectArgumentsAt(0, 'findChild', array('target1'));
    $this->parent->setReturnValueAt(0, 'findChild', null);

    $component->expectOnce('getDataset');
    $dataset = new PagedArrayDataset(array('some_data'));
    $component->setReturnReference('getDataset', $dataset);

    $component->setTargets('target1, target2');
    $component->process();
    $this->assertTrue(catch_error('LimbException', $e));

    $component->tally();
  }
}

?>