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
require_once(dirname(__FILE__) . '/../../shipping/fedex_shipping_locator.class.php');

Mock :: generatePartial(
  'fedex_shipping_locator',
  'special_fedex_shipping_locator',
  array(
    '_browse_to_home_page', 
    '_get_express_shipping_options_html', 
    '_get_ground_shipping_options_html')
);

class fedex_shipping_locator_test extends LimbTestCase
{
  var $mock_locator;
  var $locator;
  var $shipping_configuration;
    
  function setUp()
  {
    $this->mock_locator = new special_fedex_shipping_locator($this);
    $this->mock_locator->use_cache(false);
    
    $this->locator = new fedex_shipping_locator();
    $this->locator->use_cache(false);
    
    $this->shipping_configuration = new shipping_configuration();
    $this->shipping_configuration->set_zip_from('L5V 1A7');
    $this->shipping_configuration->set_zip_to('02478');
    $this->shipping_configuration->set_country_from('CA');
    $this->shipping_configuration->set_country_to('US');
    $this->shipping_configuration->set_declared_value(0);
    $this->shipping_configuration->set_weight(10);
    $this->shipping_configuration->set_weight_unit(SHIPPING_FEDEX_WEIGHT_UNIT_LB);
    $this->shipping_configuration->set_residence(false);
  }
  
  function tearDown()
  {
    $this->mock_locator->tally();
  }
  
  function test_get_shipping_options_mock_connect()
  {
    $this->mock_locator->setReturnValue('_get_express_shipping_options_html', file_get_contents(dirname(__FILE__) . '/fedex_express.html'));
    $this->mock_locator->setReturnValue('_get_ground_shipping_options_html', file_get_contents(dirname(__FILE__) . '/fedex_ground.html'));    
    
    $this->mock_locator->expectOnce('_get_express_shipping_options_html', 
      array($this->shipping_configuration)
    );

    $this->mock_locator->expectOnce('_get_ground_shipping_options_html', 
      array($this->shipping_configuration)
    );
    
    $options = $this->mock_locator->get_shipping_options($this->shipping_configuration);
    
    $id1 = md5('<a href="http://www.fedex.com/us/services/ground/intl/?link=4?">FedEx International Ground<SUP>&reg;</SUP></a>');
    $id2 = md5('<a href="http://www.fedex.com/us/services/waystoship/intlexpress/economy.html?link=4">FedEx International Economy<SUP>&reg;</SUP></a>');
    $id3 = md5('<a href="http://www.fedex.com/us/services/waystoship/intlexpress/priority.html?link=4">FedEx International Priority<SUP>&reg;</SUP></a>');
    $id4 = md5('<a href="http://www.fedex.com/us/services/waystoship/intlexpress/first.html?link=4">FedEx International First<SUP>&reg;</SUP></a>');
    
    $this->assertEqual($options, 
      array(
        $id1 => array(
          'name' => '<a href="http://www.fedex.com/us/services/ground/intl/?link=4?">FedEx International Ground<SUP>&reg;</SUP></a>',
          'description' => "Delivery in&nbsp;\n5&nbsp;business days",
          'price' => 16.02,
          'id' => $id1,
        ),
        $id2 => array(
          'name' => '<a href="http://www.fedex.com/us/services/waystoship/intlexpress/economy.html?link=4">FedEx International Economy<SUP>&reg;</SUP></a>',
          'description' => 'Time definite delivery in 2 business days',
          'price' => 72.62,          
          'id' => $id2,
        ),
        $id3 => array(
          'name' => '<a href="http://www.fedex.com/us/services/waystoship/intlexpress/priority.html?link=4">FedEx International Priority<SUP>&reg;</SUP></a>',
          'description' => 'Reach major business centers in 24 to 48 hours',
          'price' => 113.68,  
          'id' => $id3,
        ),        
        $id4 => array(
          'name' => '<a href="http://www.fedex.com/us/services/waystoship/intlexpress/first.html?link=4">FedEx International First<SUP>&reg;</SUP></a>',
          'description' => 'Overseas delivery by 8 a.m. to major cities',
          'price' => 169.31,
          'id' => $id4,
        ),
      )
    );    
  }
  
  function test_cache_shipping_options()
  { 
    $this->mock_locator->use_cache();
       
    $this->mock_locator->setReturnValue('_get_express_shipping_options_html', file_get_contents(dirname(__FILE__) . '/fedex_express.html'));
    $this->mock_locator->setReturnValue('_get_ground_shipping_options_html', file_get_contents(dirname(__FILE__) . '/fedex_ground.html'));
    
    $this->mock_locator->expectOnce('_get_express_shipping_options_html', 
      array($this->shipping_configuration)
    );

    $this->mock_locator->expectOnce('_get_ground_shipping_options_html', 
      array($this->shipping_configuration)
    );
    
    $options1 = $this->mock_locator->get_shipping_options($this->shipping_configuration);
    
    $cache = $this->mock_locator->get_cache();
    touch($cache->_file, time() + 1);//for sure
    clearstatcache();
    
    $options2 = $this->mock_locator->get_shipping_options($this->shipping_configuration);
    
    $this->assertEqual($options1, $options2);
    $this->mock_locator->flush_cache();  
  }

  function test_get_shipping_options_mock_connect_false()
  {    
    $this->mock_locator->setReturnValue('_get_express_shipping_options_html', false);
    $this->mock_locator->setReturnValue('_get_ground_shipping_options_html', false);    
        
    $options = $this->mock_locator->get_shipping_options($this->shipping_configuration);

    $this->assertFalse($options);    
  }
    
  function test_get_shipping_options_false()//integration test ???
  {
    return;
    
    $this->shipping_configuration->set_weight(0);//error
  
    $options = $this->locator->get_shipping_options($this->shipping_configuration);
    
    $this->assertFalse($options);  
  }
  
  function test_get_shipping_options()//integration test ???
  { 
    return;      
    
    $options = $this->locator->get_shipping_options($this->shipping_configuration);

    $id1 = md5('<a href="http://www.fedex.com/us/services/ground/intl/?link=4?">FedEx International Ground<SUP>&reg;</SUP></a>');
    $id2 = md5('<a href="http://www.fedex.com/us/services/waystoship/intlexpress/economy.html?link=4">FedEx International Economy<SUP>&reg;</SUP></a>');
    $id3 = md5('<a href="http://www.fedex.com/us/services/waystoship/intlexpress/priority.html?link=4">FedEx International Priority<SUP>&reg;</SUP></a>');
    $id4 = md5('<a href="http://www.fedex.com/us/services/waystoship/intlexpress/first.html?link=4">FedEx International First<SUP>&reg;</SUP></a>');
    
    $this->assertEqual($options, 
      array(
        $id1 => array(
          'name' => '<a href="http://www.fedex.com/us/services/ground/intl/?link=4?">FedEx International Ground<SUP>&reg;</SUP></a>',
          'description' => "Delivery in&nbsp;\n4&nbsp;business days",
          'price' => 16.02,
          'id' => $id1,
        ),
        $id2 => array(
          'name' => '<a href="http://www.fedex.com/us/services/waystoship/intlexpress/economy.html?link=4">FedEx International Economy<SUP>&reg;</SUP></a>',
          'description' => 'Time definite delivery in 2 business days',
          'price' => 72.62,
          'id' => $id2,
        ),
        $id3 => array(
          'name' => '<a href="http://www.fedex.com/us/services/waystoship/intlexpress/priority.html?link=4">FedEx International Priority<SUP>&reg;</SUP></a>',
          'description' => 'Reach major business centers in 24 to 48 hours',
          'price' => 113.68,  
          'id' => $id3,
        ),        
        $id4 => array(
          'name' => '<a href="http://www.fedex.com/us/services/waystoship/intlexpress/first.html?link=4">FedEx International First<SUP>&reg;</SUP></a>',
          'description' => 'Overseas delivery by 8 a.m. to major cities',
          'price' => 169.31,
          'id' => $id4,
        ),
      )
    );    
  }
}

?>