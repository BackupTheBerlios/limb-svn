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
require_once(LIMB_DIR . '/class/template/tag_component.class.php');

class request_transfer_component extends tag_component 
{
	private $attributes_string = '';
	
	public function append_request_attributes(&$content)
	{
		$transfer_attributes = explode(',', $this->get_attribute('attributes'));
		
		$attributes_to_append = array();
		
		$request = request :: instance();
		
		foreach($transfer_attributes as $attribute)
		{
			if($value = $request->get($attribute))
				$attributes_to_append[] = $attribute . '=' . addslashes($value);
		}
		if($this->attributes_string = implode('&', $attributes_to_append))
		{
			$callback = array(&$this,'_replace_callback'); 
			$content = preg_replace_callback("/(<(?:a|area|form|frame|input)[^>\\w]+(?:href|action|src)=)(?>(\"|'))?((?(2)[^\\2>]+?|[^\\s>]+))((?(2)\\2)[^>]*>)/", $callback, $content);
		}
	}
	
	private function _replace_callback($matches)
	{
		if(strpos($matches[3], '?') === false)
			$matches[3] .= '?';
	
		$matches[3] .= '&' . $this->attributes_string;
			
		return $matches[1] . $matches[2] . $matches[3] . $matches[4];
	}
}

?>