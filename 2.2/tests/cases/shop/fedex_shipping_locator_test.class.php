<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/model/shop/shipping/fedex_shipping_locator.class.php'); 

Mock :: generatePartial(
  'fedex_shipping_locator',
  'special_fedex_shipping_locator',
  array(
    '_browse_to_the_home_page', 
    '_get_express_shipping_options_html', 
    '_get_ground_shipping_options_html')
);

class fedex_shipping_locator_test extends UnitTestCase
{
  var $mock_locator;
  var $locator;
    
  function setUp()
  {    
    $this->mock_locator = new special_fedex_shipping_locator($this);    
    $this->locator = new fedex_shipping_locator();
  }
  
  function tearDown()
  {
    $this->mock_locator->tally();
  }
  
  function test_get_shipping_options_mock_connect()
  {
    $orig_zip_code = 'L5V 1A7';
    $dest_zip_code = '02478';
    
    $orig_country_code = 'CA';
    $dest_country_code = 'US';
    
    $declared_value = 0;
    $weight = 10;
    $weight_unit = SHIPPING_FEDEX_WEIGHT_UNIT_LB;
    $is_residence = false;
    
    $this->mock_locator->setReturnValue('_get_express_shipping_options_html', file_get_contents(dirname(__FILE__) . '/fedex_express.html'));
    $this->mock_locator->setReturnValue('_get_ground_shipping_options_html', file_get_contents(dirname(__FILE__) . '/fedex_ground.html'));    
    
    $this->mock_locator->expectOnce('_get_express_shipping_options_html', 
      array(
        $orig_zip_code, 
        $dest_zip_code, 
        $orig_country_code, 
        $dest_country_code,
        $declared_value,
        $weight,
        $weight_unit,
        $is_residence)
    );

    $this->mock_locator->expectOnce('_get_ground_shipping_options_html', 
      array(
        $orig_zip_code, 
        $dest_zip_code, 
        $orig_country_code, 
        $dest_country_code,
        $declared_value,
        $weight,
        $weight_unit,
        $is_residence)
    );
    
    $options = $this->mock_locator->get_shipping_options(
      $orig_zip_code, 
      $dest_zip_code, 
      $orig_country_code, 
      $dest_country_code,
      $declared_value,
      $weight,
      $weight_unit,
      $is_residence
    );

    $this->assertEqual($options, 
      array(
        array(
          'name' => '<a href="http://www.fedex.com/us/services/ground/intl/?link=4?">FedEx International Ground<SUP>&reg;</SUP></a>',
          'description' => "Delivery in&nbsp;\n5&nbsp;business days",
          'price' => 16.02,          
        ),
        array(
          'name' => '<a href="http://www.fedex.com/us/services/waystoship/intlexpress/economy.html?link=4">FedEx International Economy<SUP>&reg;</SUP></a>',
          'description' => 'Time definite delivery in 2 business days',
          'price' => 72.62,          
        ),
        array(
          'name' => '<a href="http://www.fedex.com/us/services/waystoship/intlexpress/priority.html?link=4">FedEx International Priority<SUP>&reg;</SUP></a>',
          'description' => 'Reach major business centers in 24 to 48 hours',
          'price' => 113.68,  
        ),        
        array(
          'name' => '<a href="http://www.fedex.com/us/services/waystoship/intlexpress/first.html?link=4">FedEx International First<SUP>&reg;</SUP></a>',
          'description' => 'Overseas delivery by 8 a.m. to major cities',
          'price' => 169.31,
        ),
      )
    );    
  }
  
  function test_get_shipping_options_mock_connect_false()
  {
    $orig_zip_code = 'L5V 1A7';
    $dest_zip_code = '02478';
    
    $orig_country_code = 'CA';
    $dest_country_code = 'US';
    
    $declared_value = 0;
    $weight = 10;
    $weight_unit = SHIPPING_FEDEX_WEIGHT_UNIT_LB;
    $is_residence = false;
    
    $this->mock_locator->setReturnValue('_get_express_shipping_options_html', false);
    $this->mock_locator->setReturnValue('_get_ground_shipping_options_html', false);    
        
    $options = $this->mock_locator->get_shipping_options(
      $orig_zip_code, 
      $dest_zip_code, 
      $orig_country_code, 
      $dest_country_code,
      $declared_value,
      $weight,
      $weight_unit,
      $is_residence
    );

    $this->assertFalse($options);    
  
  }
  
//  function test_get_shipping_options_false()
//  {
//    $orig_zip_code = 'L5V 1A7';
//    $dest_zip_code = '02478';
//    
//    $orig_country_code = 'CA';
//    $dest_country_code = 'US';
//    
//    $declared_value = 0;
//    $weight = 0; //error
//    $weight_unit = SHIPPING_FEDEX_WEIGHT_UNIT_LB;
//    $is_residence = false;
//  
//    $options = $this->locator->get_shipping_options(
//      $orig_zip_code, 
//      $dest_zip_code, 
//      $orig_country_code, 
//      $dest_country_code,
//      $declared_value,
//      $weight,
//      $weight_unit,
//      $is_residence
//    );  
//    
//    $this->assertFalse($options);  
//  }
  
//  function test_get_shipping_options()
//  {
//    $orig_zip_code = 'L5V 1A7';
//    $dest_zip_code = '02478';
//    
//    $orig_country_code = 'CA';
//    $dest_country_code = 'US';
//    
//    $declared_value = 0;
//    $weight = 10;
//    $weight_unit = SHIPPING_FEDEX_WEIGHT_UNIT_LB;
//    $is_residence = false;
//    
//    $options = $this->locator->get_shipping_options(
//      $orig_zip_code, 
//      $dest_zip_code, 
//      $orig_country_code, 
//      $dest_country_code,
//      $declared_value,
//      $weight,
//      $weight_unit,
//      $is_residence
//    );
//
//    $this->assertEqual($options, 
//      array(
//        array(
//          'name' => '<a href="http://www.fedex.com/us/services/ground/intl/?link=4?">FedEx International Ground<SUP>&reg;</SUP></a>',
//          'description' => "Delivery in&nbsp;\n5&nbsp;business days",
//          'price' => 16.02,          
//        ),
//        array(
//          'name' => '<a href="http://www.fedex.com/us/services/waystoship/intlexpress/economy.html?link=4">FedEx International Economy<SUP>&reg;</SUP></a>',
//          'description' => 'Time definite delivery in 2 business days',
//          'price' => 72.62,          
//        ),
//        array(
//          'name' => '<a href="http://www.fedex.com/us/services/waystoship/intlexpress/priority.html?link=4">FedEx International Priority<SUP>&reg;</SUP></a>',
//          'description' => 'Reach major business centers in 24 to 48 hours',
//          'price' => 113.68,  
//        ),        
//        array(
//          'name' => '<a href="http://www.fedex.com/us/services/waystoship/intlexpress/first.html?link=4">FedEx International First<SUP>&reg;</SUP></a>',
//          'description' => 'Overseas delivery by 8 a.m. to major cities',
//          'price' => 169.31,
//        ),
//      )
//    );    
//  }
}

?>