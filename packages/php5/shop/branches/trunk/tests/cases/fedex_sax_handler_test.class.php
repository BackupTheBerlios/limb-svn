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
include_once(LIMB_COMMON_DIR . '/setup_HTMLSax.inc.php');
require_once(dirname(__FILE__) . '/../../shipping/fedex_sax_handler.class.php');

class fedex_sax_handler_test extends LimbTestCase
{
  var $handler;
  var $parser;
  
  function setUp()
  {
    $this->parser =& new XML_HTMLSax3();
    $this->handler =& new fedex_sax_handler();
    
    $this->parser->set_object($this->handler);
    
		$this->parser->set_element_handler('open_handler','close_handler');
		$this->parser->set_data_handler('data_handler');
		$this->parser->set_escape_handler('escape_handler');    
  }
  
  function test_get_options_failed()
  {
    $this->parser->parse('');
		
		$options = $this->handler->get_options();
		
		$this->assertEqual(sizeof($options), 0);
  }
    
  function test_get_options()
  {
    $html = file_get_contents(dirname(__FILE__) . '/fedex_express.html');
    $this->parser->parse($html);
		
		$options = $this->handler->get_options();
		
    $this->assertEqual($options, 
      array(
        1 => array(
          'name' => '<a href="http://www.fedex.com/us/services/waystoship/intlexpress/first.html?link=4">FedEx International First<SUP>&reg;</SUP></a>',
          'description' => 'Overseas delivery by 8 a.m. to major cities',
          'price' => '169.31&nbsp;',
        ),
        2 => array(
          'name' => '<a href="http://www.fedex.com/us/services/waystoship/intlexpress/priority.html?link=4">FedEx International Priority<SUP>&reg;</SUP></a>',
          'description' => 'Reach major business centers in 24 to 48 hours',
          'price' => '113.68&nbsp;',          
        ),
        3 => array(
          'name' => '<a href="http://www.fedex.com/us/services/waystoship/intlexpress/economy.html?link=4">FedEx International Economy<SUP>&reg;</SUP></a>',
          'description' => 'Time definite delivery in 2 business days',
          'price' => '72.62&nbsp;',          
        ),
        4 => array(
          'name' => '<a href="http://www.fedex.com/us/services/express/intl/nextflight.html?link=4">FedEx International Next Flight<SUP>&reg;</SUP></a>',
          'description' => 'In the shortest time possible. Call 1&middot;800&middot;Go&middot;FedEx for availability and rate.',
          'price' => '',          
        )        
      )
    );
  }  
}

?>