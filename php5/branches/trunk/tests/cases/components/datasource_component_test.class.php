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
require_once(LIMB_DIR . '/class/template/components/datasource/datasource_component.class.php');
require_once(LIMB_DIR . '/class/template/components/list_component.class.php');
require_once(LIMB_DIR . '/class/template/component.class.php');
require_once(LIMB_DIR . '/class/template/components/pager_component.class.php');
require_once(LIMB_DIR . '/class/core/datasources/datasource.interface.php');
require_once(LIMB_DIR . '/class/core/datasources/countable.interface.php');
require_once(LIMB_DIR . '/class/core/request/request.class.php');
require_once(LIMB_DIR . '/class/core/limb_toolkit.interface.php');

class datasource_component_test_version implements datasource, countable
{
  public function fetch(){}
  public function count_total(){}
  public function set_limit($limit){}
  public function set_offset($offset){}
  public function set_order($order){}
}

Mock :: generate('LimbToolkit');
Mock :: generate('component');
Mock :: generate('list_component');
Mock :: generate('datasource_component_test_version');
Mock :: generate('pager_component');
Mock :: generate('request');

Mock :: generatePartial('datasource_component', 
                        'datasource_component_setup_targets_test_version',
                        array('get_dataset'));

class datasource_component_test extends LimbTestCase 
{
  var $component;
  var $datasource;
  var $toolkit;
  var $parent;
  var $request;
  
  function setUp()
  {
    $this->toolkit = new MockLimbToolkit($this);
    
    $this->parent = new Mockcomponent($this);
    
    $this->component = new datasource_component();
    $this->component->parent = $this->parent;

    $this->request = new Mockrequest($this);
    
    $this->datasource = new Mockdatasource_component_test_version($this);
    
    $this->toolkit->setReturnValue('getRequest', $this->request);

    Limb :: registerToolkit($this->toolkit);    
  }
  
  function tearDown()
  {
    $this->parent->tally();
    $this->request->tally();
    $this->datasource->tally();
    
    Limb :: popToolkit();
  }
  
  function test_set_get_parameter()
  {
    $this->component->set_parameter('test', 'test parameter');
    $this->assertEqual($this->component->get_parameter('test'), 'test parameter');
  }

  function test_get_nonexistent_parameter()
  {    
    $this->assertNull($this->component->get_parameter('test'));
  }

  function test_set_order_parameter1()
  {
    $this->component->set_parameter('order', '');  
    $this->assertNull($this->component->get_parameter('order'));
  }
  
  function test_set_order_parameter2()
  {
    $this->component->set_parameter('order', 'c1 =AsC, c2 = DeSC , c3=Junky'); 
    $this->assertEqual($this->component->get_parameter('order'), 
                       array('c1' => 'ASC', 'c2' => 'DESC', 'c3' => 'ASC'));
  }
  
  function test_set_order_parameter3()
  {
    $this->component->set_parameter('order', 'c1, c2 = Rand() ');//!!!mysql only 
    $this->assertEqual($this->component->get_parameter('order'), 
                       array('c1' => 'ASC', 'c2' => 'RAND()'));
  }
  
  function test_limit_parameter1()
  {
    $this->component->set_parameter('limit', '10');
    $this->assertEqual($this->component->get_parameter('limit'), 10);
    $this->assertNull($this->component->get_parameter('offset'));
  }

  function test_limit_parameter2()
  {
    $this->component->set_parameter('limit', '10, 20');
    $this->assertEqual($this->component->get_parameter('limit'), 10);
    $this->assertEqual($this->component->get_parameter('offset'), 20);
  }

  function test_limit_parameter3()
  { 
    $this->component->set_parameter('limit', ',20');
    $this->assertNull($this->component->get_parameter('limit'));
    $this->assertNull($this->component->get_parameter('offset'));
  }

  function test_setup_navigator_no_navigator()
  {
    $pager = new Mockpager_component($this);
    
    $this->parent->expectOnce('find_child', array($pager_id = 'test-nav'));
    $this->parent->setReturnValue('find_child', null, array($pager_id));
            
    $this->request->expectNever('has_attribute');    
    
    $this->component->setup_navigator($pager_id);
    
    $this->assertNull($this->component->get_parameter('limit'));
    $this->assertNull($this->component->get_parameter('offset'));
    
    $pager->tally();
  }
  
  function test_setup_navigator_with_params_in_request()
  {
    $pager = new Mockpager_component($this);
    
    $this->parent->expectOnce('find_child', array($pager_id = 'test-nav'));
    $this->parent->setReturnValue('find_child', $pager, array($pager_id));
    
    $pager->expectOnce('get_items_per_page');
    $pager->setReturnValue('get_items_per_page', 100);
    
    $pager->expectOnce('get_server_id');
    $pager->setReturnValue('get_server_id', $pager_id);
    
    $this->request->expectOnce('has_attribute', array('page_' . $pager_id));    
    $this->request->setReturnValue('has_attribute', true, array('page_' . $pager_id));
    
    $this->request->expectOnce('get', array('page_' . $pager_id));
    $this->request->setReturnValue('get', 10, array('page_' . $pager_id));

    $this->component->set_datasource_path('test-datasource');
    $this->toolkit->expectOnce('createDatasource', array('test-datasource'));
    $this->toolkit->setReturnValue('createDatasource', $this->datasource, array('test-datasource'));
    
    $this->datasource->expectOnce('count_total');
    $this->datasource->setReturnValue('count_total', $count = 13);
    $pager->expectOnce('set_total_items', array($count));
    
    $this->component->setup_navigator($pager_id);
    
    $this->assertEqual($this->component->get_parameter('limit'), 100);
    $this->assertEqual($this->component->get_parameter('offset'), (10-1)*100);
    
    $pager->tally();
  }

  function test_setup_navigator_no_params_in_request()
  {
    $pager = new Mockpager_component($this);
    
    $this->parent->expectOnce('find_child', array($pager_id = 'test-nav'));
    $this->parent->setReturnValue('find_child', $pager, array($pager_id));
    
    $pager->expectOnce('get_items_per_page');
    $pager->expectOnce('get_server_id');
    $pager->setReturnValue('get_items_per_page', 100);
    $pager->setReturnValue('get_server_id', $pager_id);
    
    $this->request->expectOnce('has_attribute', array('page_' . $pager_id));
    $this->request->setReturnValue('has_attribute', false, array('page_' . $pager_id));
    $this->request->expectNever('get');    

    $this->component->set_datasource_path('test-datasource');
    $this->toolkit->expectOnce('createDatasource', array('test-datasource'));
    $this->toolkit->setReturnValue('createDatasource', $this->datasource, array('test-datasource'));
    
    $this->datasource->expectOnce('count_total');
    $this->datasource->setReturnValue('count_total', $count = 13);
    $pager->expectOnce('set_total_items', array($count));
    
    $this->component->setup_navigator($pager_id);
    
    $this->assertEqual($this->component->get_parameter('limit'), 100);
    $this->assertNull($this->component->get_parameter('offset'));
    
    $pager->tally();
  }
  
  function test_get_dataset()
  {
    $this->component->set_parameter('limit', '10, 2');
    $this->component->set_parameter('order', 'col1=ASC');
    $this->component->set_parameter('junky', 'trash');
    
    $this->component->set_datasource_path('test-datasource');
    $this->toolkit->expectOnce('createDatasource', array('test-datasource'));
    $this->toolkit->setReturnValue('createDatasource', $this->datasource, array('test-datasource'));
    
    $this->datasource->expectOnce('set_limit', array(10));
    $this->datasource->expectOnce('set_offset', array(2));
    $this->datasource->expectOnce('set_order', array(array('col1' => 'ASC')));
    
    $this->datasource->expectOnce('fetch');
    $this->datasource->setReturnValue('fetch', $result = array('whatever'));
    $this->assertEqual(new array_dataset($result), $this->component->get_dataset());
  }
  
  function test_setup_targets()
  {
    $component = new datasource_component_setup_targets_test_version($this);

    $component->parent = $this->parent;
    $this->parent->expectArgumentsAt(0, 'find_child', array('target1'));
    $this->parent->expectArgumentsAt(1, 'find_child', array('target2'));
    $this->parent->setReturnValueAt(0, 'find_child', $target1 = new Mocklist_component($this));
    $this->parent->setReturnValueAt(1, 'find_child', $target2 = new Mocklist_component($this));
    
    $component->expectOnce('get_dataset');
    $dataset = new array_dataset(array('some_data'));
    $component->setReturnValue('get_dataset', $dataset);

    $target1->expectOnce('register_dataset', array($dataset));
    $target2->expectOnce('register_dataset', array($dataset));
    $component->setup_targets('target1, target2');
    
    $component->tally();
    $target1->tally();
    $target2->tally();
  }

  function test_setup_targets_failed_no_such_runtime_target()
  {
    $component = new datasource_component_setup_targets_test_version($this);

    $component->parent = $this->parent;
    $this->parent->expectArgumentsAt(0, 'find_child', array('target1'));
    $this->parent->setReturnValueAt(0, 'find_child', null);
    
    $component->expectOnce('get_dataset');
    $dataset = new array_dataset(array('some_data'));
    $component->setReturnValue('get_dataset', $dataset);
    
    try
    {
      $component->setup_targets('target1, target2');
      $this->assertTrue(false);
    }
    catch(WactException $e)
    {
    }
    
    $component->tally();
  }
  
}

?>