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
require_once(dirname(__FILE__) . '/search_normalizer.interface.php');

class search_phone_number_normalizer implements search_normalizer
{	
	public function process($content)
	{
    $content = preg_replace("#[^\d\(\)\+]+#", '', $content);
    $content = preg_replace("#[\(\)\+]+#", ' ', $content);
		
		$pieces = explode(' ', trim($content));
		
		$numbers = array();
		for($i = 0; $i < sizeof($pieces); $i++)
		{
			$number = '';
			for($j = $i; $j < sizeof($pieces); $j++)
				$number .= $pieces[$j];
				
			$numbers[] = $number;
		}
		
    return implode(' ', $numbers);
	}
}


?>