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
class FakeShippingLocator
{
  function getShippingOptions($config)
  {
    $options = array(
        array(
          'id' => 1,
          'name' => '<a href="http://www.fedex.com/us/services/ground/intl/?link=4?">FedEx International Ground<SUP>&reg;</SUP></a>',
          'description' => "Delivery in&nbsp;\n5&nbsp;business days",
          'price' => 16.02,
        ),
        array(
          'id' => 2,
          'name' => '<a href="http://www.fedex.com/us/services/waystoship/intlexpress/economy.html?link=4">FedEx International Economy<SUP>&reg;</SUP></a>',
          'description' => 'Time definite delivery in 2 business days',
          'price' => 72.62,
        ),
        array(
          'id' => 3,
          'name' => '<a href="http://www.fedex.com/us/services/waystoship/intlexpress/priority.html?link=4">FedEx International Priority<SUP>&reg;</SUP></a>',
          'description' => 'Reach major business centers in 24 to 48 hours',
          'price' => 113.68,
        ),
        array(
          'id' => 4,
          'name' => '<a href="http://www.fedex.com/us/services/waystoship/intlexpress/first.html?link=4">FedEx International First<SUP>&reg;</SUP></a>',
          'description' => 'Overseas delivery by 8 a.m. to major cities',
          'price' => 169.31,
        ),
    );

    return $options;
  }
}

?>