<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: sys_param.tag.php 32 2004-03-11 18:00:48Z server $
*
***********************************************************************************/


require_once(LIMB_DIR . '/core/template/compiler/server_component_tag.class.php');

class sys_param_tag_info
{
	var $tag = 'core:SYS_PARAM';
	var $end_tag = ENDTAG_FORBIDDEN;
	var $tag_class = 'sys_param_tag';
} 

register_tag(new sys_param_tag_info());

class sys_param_tag extends server_component_tag
{
	var $runtime_component_path = '/core/template/components/sys_param_component';
	
	function generate_contents(&$code)
	{
		if(isset($this->attributes['name']) && isset($this->attributes['type']))
		{
			$code->write_php(
				$this->get_component_ref_code() . '->get_param("' . $this->attributes['name'] . '","' . $this->attributes['type'] . '");');
		}
		
		parent :: generate_contents($code);
	}
	

} 

?>