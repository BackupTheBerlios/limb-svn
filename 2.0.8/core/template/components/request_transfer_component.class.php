<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: dataspace_component.class.php 47 2004-03-19 15:59:48Z server $
*
***********************************************************************************/

require_once(LIMB_DIR . '/core/template/tag_component.class.php');

class request_transfer_component extends tag_component 
{
	var $attributes_string = '';
	
	function append_request_attributes(&$content)
	{
		$transfer_attributes = explode(',', $this->get_attribute('attributes'));
		
		$attributes_to_append = array();
		
		foreach($transfer_attributes as $attribute)
		{
			if(isset($_REQUEST[$attribute]))
				$attributes_to_append[] = $attribute . '=' . addslashes($_REQUEST[$attribute]);
		}
		if($this->attributes_string = implode('&', $attributes_to_append))
		{
			$callback = array(&$this,'_replace_callback'); 
			$content = preg_replace_callback("/(<(?:a|area|form|frame|input)[^>\\w]+(?:href|action|src)=)(?>(\"|'))?((?(2)[^\\2>]+?|[^\\s>]+))((?(2)\\2)[^>]*>)/", $callback, $content);
		}
	}
	
	function _replace_callback($matches)
	{
		if(strpos($matches[3], '?') === false)
			$matches[3] .= '?';
	
		$matches[3] .= '&' . $this->attributes_string;
			
		return $matches[1] . $matches[2] . $matches[3] . $matches[4];
	}
}

?>