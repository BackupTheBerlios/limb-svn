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
require_once(dirname(__FILE__) . '/../../shipping/FedexShippingLocator.class.php');

Mock :: generatePartial(
  'FedexShippingLocator',
  'SpecialFedexShippingLocator',
  array(
    '_browseToHomePage',
    '_getExpressShippingOptionsHtml',
    '_getGroundShippingOptionsHtml')
);

class FedexShippingLocatorTest extends LimbTestCase
{
  var $mock_locator;
  var $locator;
  var $shipping_configuration;

  function setUp()
  {
    $this->mock_locator = new SpecialFedexShippingLocator($this);
    $this->mock_locator->useCache(false);

    $this->locator = new FedexShippingLocator();
    $this->locator->useCache(false);

    $this->shipping_configuration = new ShippingConfiguration();
    $this->shipping_configuration->setZipFrom('L5V 1A7');
    $this->shipping_configuration->setZipTo('02478');
    $this->shipping_configuration->setCountryFrom('CA');
    $this->shipping_configuration->setCountryTo('US');
    $this->shipping_configuration->setDeclaredValue(0);
    $this->shipping_configuration->setWeight(10);
    $this->shipping_configuration->setWeightUnit(SHIPPING_FEDEX_WEIGHT_UNIT_LB);
    $this->shipping_configuration->setResidence(false);
  }

  function tearDown()
  {
    $this->mock_locator->tally();
  }

  function testGetShippingOptionsMockConnect()
  {
    $this->mock_locator->setReturnValue('_getExpressShippingOptionsHtml', file_get_contents(dirname(__FILE__) . '/fedex_express.html'));
    $this->mock_locator->setReturnValue('_getGroundShippingOptionsHtml', file_get_contents(dirname(__FILE__) . '/fedex_ground.html'));

    $this->mock_locator->expectOnce('_getExpressShippingOptionsHtml',
      array($this->shipping_configuration)
    );

    $this->mock_locator->expectOnce('_getGroundShippingOptionsHtml',
      array($this->shipping_configuration)
    );

    $options = $this->mock_locator->getShippingOptions($this->shipping_configuration);

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

  function testCacheShippingOptions()
  {
    $this->mock_locator->useCache();

    $this->mock_locator->setReturnValue('_getExpressShippingOptionsHtml', file_get_contents(dirname(__FILE__) . '/fedex_express.html'));
    $this->mock_locator->setReturnValue('_getGroundShippingOptionsHtml', file_get_contents(dirname(__FILE__) . '/fedex_ground.html'));

    $this->mock_locator->expectOnce('_getExpressShippingOptionsHtml',
      array($this->shipping_configuration)
    );

    $this->mock_locator->expectOnce('_getGroundShippingOptionsHtml',
      array($this->shipping_configuration)
    );

    $options1 = $this->mock_locator->getShippingOptions($this->shipping_configuration);

    $cache = $this->mock_locator->getCache();
    touch($cache->_file, time() + 1);//for sure
    clearstatcache();

    $options2 = $this->mock_locator->getShippingOptions($this->shipping_configuration);

    $this->assertEqual($options1, $options2);
    $this->mock_locator->flushCache();
  }

  function testGetShippingOptionsMockConnectFalse()
  {
    $this->mock_locator->setReturnValue('_getExpressShippingOptionsHtml', false);
    $this->mock_locator->setReturnValue('_getGroundShippingOptionsHtml', false);

    $options = $this->mock_locator->getShippingOptions($this->shipping_configuration);

    $this->assertFalse($options);
  }

  function testGetShippingOptionsFalse()//integration test ???
  {
    return;

    $this->shipping_configuration->setWeight(0);//error

    $options = $this->locator->getShippingOptions($this->shipping_configuration);

    $this->assertFalse($options);
  }

  function testGetShippingOptions()//integration test ???
  {
    return;

    $options = $this->locator->getShippingOptions($this->shipping_configuration);

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