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
class htmlspecialchars_tag_info
{
	public $tag = 'core:HTMLSPECIALCHARS';
	public $end_tag = ENDTAG_FORBIDDEN;
	public $tag_class = 'htmlspecialchars_tag';
} 

register_tag(new htmlspecialchars_tag_info());

class htmlspecialchars_tag extends compiler_directive_tag
{
	public function pre_parse()
	{
		if (! array_key_exists('hash_id', $this->attributes) ||
				empty($this->attributes['hash_id']))
		{
			error('MISSINGREQUIREATTRIBUTE', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, 
				array('tag' => $this->tag,
							'attribute' => 'hash_id',
							'file' => $this->source_file,
							'line' => $this->starting_line_no));
		} 
		return PARSER_FORBID_PARSING; 
	}  

	public function generate_contents($code)
	{
		if(isset($this->attributes['hash_id']))
		{
			$code->write_php(
				'echo htmlspecialchars(' . $this->get_dataspace_ref_code() . '->get("' . $this->attributes['hash_id'] . '"), ENT_QUOTES);');
		}
	} 
} 

?>