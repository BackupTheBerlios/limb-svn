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
class core_request_transfer_tag_info
{
	public $tag = 'core:REQUEST_TRANSFER';
	public $end_tag = ENDTAG_REQUIRED;
	public $tag_class = 'core_request_transfer_tag';
} 

register_tag(new core_request_transfer_tag_info());

class core_request_transfer_tag extends server_tag_component_tag
{
  public function __construct()
  {
	  $this->runtime_component_path = dirname(__FILE__) . '/../../components/request_transfer_component';
	}
	
	public function pre_parse()
	{
		if (! array_key_exists('attributes', $this->attributes) || empty($this->attributes['attributes'])) 
			throw new WactException('missing required attribute', 
					array('tag' => $this->tag,
					'attribute' => 'attributes',
					'file' => $this->source_file,
					'line' => $this->starting_line_no));
									
		return PARSER_REQUIRE_PARSING;
	}
	
	public function pre_generate()
	{
		//we override parent behavior
	}

	public function post_generate()
	{
		//we override parent behavior
	}
	
	public function generate_contents($code)
	{
		$content = '$' . $code->get_temp_variable();
		
		$code->write_php('ob_start();');
		
		parent :: generate_contents($code);
		
		$code->write_php("{$content} = ob_get_contents();ob_end_clean();");
		
		$code->write_php($this->get_component_ref_code() . "->append_request_attributes({$content});");
		
		$code->write_php("echo {$content};");
	}
} 

?>