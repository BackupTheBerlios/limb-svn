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
class core_wrap_tag_info
{
	public $tag = 'core:WRAP';
	public $end_tag = ENDTAG_FORBIDDEN;
	public $tag_class = 'core_wrap_tag';
} 

register_tag(new core_wrap_tag_info());

/**
* Merges the current template with a wrapper template, the current
* template being inserted into the wrapper at the point where the
* wrap tag exists.
*/
class core_wrap_tag extends compiler_directive_tag
{
	protected $resolved_source_file;
	
	protected $keylist;

	public function check_nesting_level()
	{
		if ($this->find_parent_by_class('core_wrap_tag'))
		{
			throw new WactException('bad self nesting', 
					array('tag' => $this->tag,
					'file' => $this->source_file,
					'line' => $this->starting_line_no));
		} 
	} 
	
	public function pre_parse()
	{
		global $tag_dictionary;
		$file = $this->attributes['file'];
		if (!isset($this->attributes['file']) || !$this->attributes['file'])
		{
			throw new WactException('missing required attribute', 
					array('tag' => $this->tag,
					'attribute' => 'file',
					'file' => $this->source_file,
					'line' => $this->starting_line_no));
		} 
	
		if (!$this->resolved_source_file = resolve_template_source_file_name($file))
		{
			throw new WactException('missing file', 
					array('tag' => $this->tag,
					'srcfile' => $file,
					'file' => $this->source_file,
					'line' => $this->starting_line_no));
		} 
	
		$sfp = new source_file_parser($this->resolved_source_file, $tag_dictionary);
		$sfp->parse($this);
		return PARSER_FORBID_PARSING;
	} 
	
	public function prepare()
	{
		$this->parent->wrapping_component = $this;
		
		parent :: prepare();
	} 
	
	public function generate_wrapper_prefix($code)
	{
		$this->keylist = array_keys($this->children);
		$name = $this->attributes['placeholder'];
		reset($this->keylist);
		while (list(, $key) = each($this->keylist))
		{
			$child = $this->children[$key];
			if ($child->get_server_id() == $name)
			{
				break;
			} 
			$child->generate($code);
		} 
	} 
	
	public function generate_wrapper_postfix($code)
	{
		while (list(, $key) = each($this->keylist))
		{
			$this->children[$key]->generate($code);
		} 
	} 
	
	/**
	* By the time this is called we have already called generate
	* on all of our children, so does nothing
	*/
	public function generate($code)
	{ 
		// By the time this is called we have already called generate
		// on all of our children.
	} 
} 

?>