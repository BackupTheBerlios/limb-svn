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
class core_parameter_tag_info
{
	public $tag = 'core:PARAMETER';
	public $end_tag = ENDTAG_FORBIDDEN;
	public $tag_class = 'core_parameter_tag';
} 

register_tag(new core_parameter_tag_info());

class core_parameter_tag extends compiler_directive_tag
{
	public function pre_parse()
	{
		if (!isset($this->attributes['name']))
		{
			error('MISSINGREQUIREATTRIBUTE', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, 
			array('tag' => $this->tag,
					'attribute' => 'name',
					'file' => $this->source_file,
					'line' => $this->starting_line_no));
		} 
		
		if (!isset($this->attributes['value']))
		{
			error('MISSINGREQUIREATTRIBUTE', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, 
			array('tag' => $this->tag,
					'attribute' => 'value',
					'file' => $this->source_file,
					'line' => $this->starting_line_no));
		} 

		return PARSER_FORBID_PARSING;
	} 
	
	public function check_nesting_level()
	{
		if (!is_subclass_of($this->parent, 'server_component_tag'))
		{
			error('WRONGPARENTCLASSNESTING', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, 
			array('tag' => $this->tag,
					'parent_class' => get_class($this->parent),
					'file' => $this->source_file,
					'line' => $this->starting_line_no));
		} 
	}
	
	public function pre_generate($code)
	{
		if(!isset($this->attributes['type']))
			$this->attributes['type'] = 'string';
		
		parent::pre_generate($code);
	} 
	
	public function generate_contents($code)
	{
		$value = $this->_typecast_value();
		
		$code->write_php($this->parent->get_component_ref_code() 
			. '->set_parameter("' . $this->attributes['name'] . '", ' 
			. var_export($value, true) . ')');
		
		parent::generate_contents($code);
	}
	
	private function _typecast_value()
	{
		$value = $this->attributes['value'];
    switch(strtolower($this->attributes['type'])) 
    {
    	case 'numeric':
    		return $value*1;
    	break;
	    case 'boolean':
	    	return (bool)$value;
	    break;
	    case 'string':
	    	return $value;
	    break;
	    default:
	    	return $value;
    }
	}
} 

?>