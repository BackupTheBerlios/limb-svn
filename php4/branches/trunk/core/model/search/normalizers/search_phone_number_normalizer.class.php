<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/

class search_phone_number_normalizer
{
  function process($content)
  {
    $content = preg_replace("#[^\d\(\)\+]+#", '', $content);
    $content = preg_replace("#[\(\)\+]+#", ' ', $content);

    $pieces = explode(' ', trim($content));

    $numbers = array();
    for($i=0; $i<sizeof($pieces); $i++)
    {
      $number = '';
      for($j=$i; $j<sizeof($pieces); $j++)
        $number .= $pieces[$j];

      $numbers[] = $number;
    }

    return implode(' ', $numbers);
  }
}


?>