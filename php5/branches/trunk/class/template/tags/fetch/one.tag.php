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
class fetch_one_tag_info
{
	public $tag = 'fetch:ONE';
	public $end_tag = ENDTAG_REQUIRED;
	public $tag_class = 'fetch_one_tag';
} 

register_tag(new fetch_one_tag_info());

class fetch_one_tag extends server_component_tag
{
  public function __construct()
  {
	  $this->runtime_component_path = dirname(__FILE__) . '/../../components/fetch_component';
	}
		
	public function generate_contents($code)
	{		
		$code->write_php($this->get_component_ref_code() . '->fetch("' . $this->attributes['path'] . '");');
		
		parent :: generate_contents($code);
	}
	
	public function get_dataspace()
	{
		return $this;
	} 

	public function get_dataspace_ref_code()
	{
		return $this->get_component_ref_code();
	} 
} 

?>