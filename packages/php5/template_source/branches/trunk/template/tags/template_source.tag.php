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
class template_source_tag_info
{
	public $tag = 'dev:TEMPLATE_SOURCE';
	public $end_tag = ENDTAG_REQUIRED;
	public $tag_class = 'template_source_tag';
} 

register_tag(new template_source_tag_info());

class template_source_tag extends server_component_tag
{
  public function __construct()
  {
	  $this->runtime_component_path = dirname(__FILE__) . '/../components/template_source_component';
	}
	
	public function generate_contents($code)
	{
		if(isset($this->attributes['target']))
			$target = 'target=' . $this->attributes['target'];
		else
			$target = '';
		
		$code->write_php('echo "<a ' . $target . ' href=" . '  . $this->get_component_ref_code() . '->get_current_template_source_link() . ">"');
		
		parent :: generate_contents($code);
		
		$code->write_php('echo "</a>"');
	} 	
} 

?>