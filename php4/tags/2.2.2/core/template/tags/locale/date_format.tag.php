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
require_once(LIMB_DIR . '/core/template/compiler/server_component_tag.class.php');

class locale_date_format_tag_info
{
	var $tag = 'locale:DATE_FORMAT';
	var $end_tag = ENDTAG_FORBIDDEN;
	var $tag_class = 'locale_date_format_tag';
} 

register_tag(new locale_date_format_tag_info());

class locale_date_format_tag extends server_component_tag
{
	var $runtime_component_path = '/core/template/components/locale_date_format_component';
	
	function pre_generate(&$code)
	{
    $code->write_php($this->get_component_ref_code() . '->prepare();');
  }

	function generate_contents(&$code)
	{
		if(isset($this->attributes['hash_id']))
		{

			if(isset($this->attributes['locale_type']))
			{
				$code->write_php(
					$this->get_component_ref_code() . '->set_locale_type("' . $this->attributes['locale_type'] . '");');
			}
			
			if(isset($this->attributes['type']))
			{
				$code->write_php(
					$this->get_component_ref_code() . '->set_date_type("' . $this->attributes['type'] . '");');
			}
			
			$code->write_php(
				$this->get_component_ref_code() . '->set_date(' . $this->get_dataspace_ref_code() . '->get("' . $this->attributes['hash_id'] . '"));');
				
			if(isset($this->attributes['locale_format']))
			{
				$code->write_php(
					$this->get_component_ref_code() . '->set_locale_format_type("' . $this->attributes['locale_format'] . '");');
			}
			elseif(isset($this->attributes['format']))
			{
				$code->write_php(
					$this->get_component_ref_code() . '->set_format_string("' . $this->attributes['format'] . '");');
			}

			$code->write_php(
				$this->get_component_ref_code() . '->format();');
		}
		
		parent :: generate_contents($code);
	}
	

} 

?>