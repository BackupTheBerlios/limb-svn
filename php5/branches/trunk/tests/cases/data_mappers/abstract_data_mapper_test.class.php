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
require_once(LIMB_DIR . '/class/core/data_mappers/abstract_data_mapper.class.php');
require_once(LIMB_DIR . '/class/core/domain_object.class.php');
require_once(LIMB_DIR . '/class/core/finders/data_finder.interface.php');

Mock :: generate('domain_object');
Mock :: generate('data_finder');

class abstract_data_mapper_test_version extends abstract_data_mapper{}

Mock :: generatePartial('abstract_data_mapper_test_version',
                        'abstract_data_mapper_mock',
                        array('insert', 
                              'update',
                              '_create_domain_object',
                              '_do_load',
                              '_get_finder'));

class abstract_data_mapper_test extends LimbTestCase 
{ 
  var $object;
  var $finder;
  
  function setUp()
  {
    $this->object = new Mockdomain_object($this);
    $this->finder = new Mockdata_finder($this);
  }
  
  function tearDown()
  { 
    $this->object->tally();
    $this->finder->tally();
  }
  
  function test_find_by_id_null()
  {
    $mapper = new abstract_data_mapper_mock($this);
    $mapper->setReturnValue('_get_finder', $this->finder);
    
    $this->finder->expectOnce('find_by_id', array($id = 100));
    $this->finder->setReturnValue('find_by_id', array(), array($id = 100));
    
    $mapper->expectNever('_create_domain_object');
    $mapper->expectNever('_do_load');
    
    $this->assertNull($mapper->find_by_id($id));
    
    $mapper->tally();
  }

  function test_find_by_id()
  {
    $mapper = new abstract_data_mapper_mock($this);
    $mapper->setReturnValue('_get_finder', $this->finder);
    
    $this->finder->expectOnce('find_by_id', array($id = 100));
    $this->finder->setReturnValue('find_by_id', $result_set = array('whatever'), array($id = 100));
    
    $mapper->expectOnce('_create_domain_object');
    $mapper->setReturnValue('_create_domain_object', $object = new domain_object());
    
    $mapper->expectOnce('_do_load', array($result_set, $object));
    
    $this->assertTrue($mapper->find_by_id($id) === $object);
    
    $mapper->tally();
  }
  
  function test_save_insert()
  {
    $mapper = new abstract_data_mapper_mock($this);
    
    $mapper->expectOnce('insert', array(new IsAExpectation('Mockdomain_object')));
    
    $this->object->expectOnce('get_id');
    
    $mapper->save($this->object);
    
    $mapper->tally();
  }

  function test_save_update()
  {
    $mapper = new abstract_data_mapper_mock($this);
    
    $mapper->expectOnce('update', array(new IsAExpectation('Mockdomain_object')));
    
    $this->object->expectOnce('get_id');
    $this->object->setReturnValue('get_id', 10);
    
    $mapper->save($this->object);
    
    $mapper->tally();
  }  
}

?>