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
require_once(LIMB_DIR . '/class/template/compiler/server_component_tag.class.php');

class sys_param_tag_info
{
	public $tag = 'core:SYS_PARAM';
	public $end_tag = ENDTAG_FORBIDDEN;
	public $tag_class = 'sys_param_tag';
} 

register_tag(new sys_param_tag_info());

class sys_param_tag extends server_component_tag
{
  public function __construct()
  {
	  $this->runtime_component_path = dirname(__FILE__) . '/../../components/sys_param_component';
	}
	
	public function generate_contents($code)
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