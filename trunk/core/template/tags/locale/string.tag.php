<?php

require_once(LIMB_DIR . '/core/template/compiler/compiler_directive_tag.class.php');

class locale_string_tag_info
{
	var $tag = 'locale:STRING';
	var $end_tag = ENDTAG_FORBIDDEN;
	var $tag_class = 'locale_string_tag';
} 

register_tag(new locale_string_tag_info());

class locale_string_tag extends compiler_directive_tag
{

	function generate_contents(&$code)
	{

		$file = 'common';
		
		if(isset($this->attributes['file']))
			$file = $this->attributes['file']; 
		
		if(isset($this->attributes['locale_type']))
		{
			if(strtolower($this->attributes['locale_type']) == 'content')
				$locale_constant = 'CONTENT_LOCALE_ID';	
			else
				$locale_constant = 'MANAGEMENT_LOCALE_ID';	
		}
		else
			$locale_constant = 'MANAGEMENT_LOCALE_ID';	

		if(isset($this->attributes['hash_id']))
		{
			$locale_tmp = '$' . $code->get_temp_variable();
			
			$code->write_php(
				"{$locale_tmp} = " . $this->get_dataspace_ref_code() . '->get("' . $this->attributes['hash_id'] . '");');

			$code->write_php("echo strings :: get({$locale_tmp}, '{$file}', constant('{$locale_constant}'));");
							
		}
		elseif(isset($this->attributes['name']))
		{			
			$code->write_php("echo strings :: get('{$this->attributes['name']}', '{$file}', constant('{$locale_constant}'));");
		}
		
				
		parent :: generate_contents($code);
	}
	

} 

?>