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
/**
* Define compile component states which determine parse behaviour
*/
define('PARSER_REQUIRE_PARSING', true);
define('PARSER_FORBID_PARSING', false);
define('PARSER_ALLOW_PARSING', null);

/**
* The source template parser which uses a regular expression engine
*/
class source_file_parser
{
	/**
	* The contents of the source template as a string
	* 
	* @var string 
	* @access private 
	*/
	var $rawtext;
	/**
	* path and filename of source template
	* 
	* @var string 
	* @access private 
	*/
	var $source_file;
	/**
	* Reference to the global instance of the tag_dictionary
	* 
	* @var tag _dictionary
	* @access private 
	*/
	var $tag_dictionary;
	/**
	* Current line number of parser cursor within the raw text
	* 
	* @var int 
	* @access private 
	*/
	var $cur_line_no;
	/**
	* Regex pattern to match an opening tags which are components,
	* based on the contents of the tag dictionary.
	* 
	* @var string 
	* @access private 
	*/
	var $tag_starting_pattern;
	/**
	* Regex pattern to match opening tag attributes
	* 
	* @var string 
	* @access private 
	*/
	var $attribute_pattern;
	/**
	* Regex pattern to match the contents of a tag.
	* 
	* @var string 
	* @access private 
	*/
	var $variable_reference_pattern; 
	// --------------------------------------------------------------------------------
	/**
	* Constructs SourecFileParser. Uses read_template_file() to get the contents
	* of the template.
	* 
	* @see read_template_file
	* @param string $ path and filename of source template
	* @access protected 
	*/
	function source_file_parser($sourcefile, &$tagdictionary)
	{
		$this->source_file = $sourcefile;
		$this->tag_dictionary = &$tagdictionary;
		$this->rawtext = $this->read_template_file($sourcefile);
		$this->cur_line_no = 1;
		$this->text = '';

		$this->initializetag_starting_pattern();
		$this->initialize_attribute_pattern();
		$this->initialize_variable_reference_pattern();
	} 
	// --------------------------------------------------------------------------------
	/**
	* Builds the tag starting regex pattern, which "spots" all tags registered
	* in the  $tag_dictionary
	* 
	* @see tag_dictionary
	* @return void 
	* @access private 
	*/
	function initializetag_starting_pattern()
	{
		$tag_list = $this->tag_dictionary->gettag_list();

		$tag_starting_pattern = '/';
		$tag_starting_pattern .= '^(.*)';
		$tag_starting_pattern .= preg_quote('<', '/');
		$tag_starting_pattern .= '(' . preg_quote('/', '/') . ')?';
		$tag_starting_pattern .= '(';
		$sep = '';

		foreach ($tag_list as $tag)
		{
			$tag_starting_pattern .= $sep;
			$tag_starting_pattern .= preg_quote($tag, '/');
			$sep = '|';
		} 
		$tag_starting_pattern .= ')';
		$tag_starting_pattern .= '(\s+|\/?' . preg_quote('>', '/') . ')';

		$tag_starting_pattern .= '/Usi';

		$this->tag_starting_pattern = $tag_starting_pattern;
	} 
	// --------------------------------------------------------------------------------
	/**
	* Builds the regex for fetching contents of tags
	* 
	* @return void 
	* @access private 
	*/
	function initialize_variable_reference_pattern()
	{
		$this->variable_reference_pattern = '/^(.*){(\$|\#|\^)([\w\[\]\'\"]+)}(.*)$/Usi';
	} 
	// --------------------------------------------------------------------------------
	/**
	* Builds the attribute spotting regular expression
	* 
	* @return void 
	* @access private 
	*/
	function initialize_attribute_pattern()
	{
		$this->attribute_pattern = "/^(\\w+)\\s*(=\\s*(\"|')?((?(3)[^\\3]*?|[^\\s]*))(?(3)\\3))?\\s*/";
	} 
	// --------------------------------------------------------------------------------
	/**
	* Used to find tag components in the template
	* 
	* @param string $ regex pattern
	* @param array $ "callback" array of matches
	* @return boolean TRUE on success
	* @access private 
	*/
	function match_text($pattern, &$match)
	{
		if (preg_match($pattern, $this->rawtext, $match))
		{
			$this->rawtext = substr($this->rawtext, strlen($match[0]));
			$this->cur_line_no += preg_match_all("/\r\n|\n|\r/", $match[0], $discarded);
			return true;
		} 
		else
		{
			return false;
		} 
	} 
	// --------------------------------------------------------------------------------
	/**
	* Used to parse the attributes of a component tag
	* 
	* @param object $ compile time component
	* @return void 
	* @access private 
	*/
	function parse_attributes(&$component)
	{
		$attributes = array();
		$use_php_decode = (version_compare(phpversion(), '4.3.1', '>')) ? true : false;
		
		while ($this->match_text($this->attribute_pattern, $attribute_match))
		{
			$attrib_name = strtolower($attribute_match[1]);
			if (!empty($attribute_match[2]))
			{
				if($use_php_decode)
					$attributes[$attrib_name] = html_entity_decode($attribute_match[4]);
				else
					$attributes[$attrib_name] = 
						strtr($attribute_match[4], array_flip(get_html_translation_table(HTML_ENTITIES)));
			} 
			else
			{
				$attributes[$attrib_name] = null;
			} 
		} 

		$component->set_attributes($attributes);
	} 
		
	// --------------------------------------------------------------------------------
	// This does not correctly determine the line number of the variable reference.
	// The preg_match in this method should be rolled up and included in the main
	// loop of the parse() method.
	// This will require a seriously nasty regular expression.
	/**
	* Used to parse the contents of a component
	* 
	* @param object $ compile time component
	* @param string $ contents of a component
	* @return void 
	* @access private 
	*/
	function parse_text(&$parent_component, $text)
	{
		while (preg_match($this->variable_reference_pattern, $text, $match))
		{
			if (strlen($match[1]) > 0)
			{
				$component = &$this->get_text_node($match[1]);
				$parent_component->add_child($component);
			} 
			$component = &$this->get_variable_reference();
			$component->reference = $match[3];
			$component->scope = $match[2];
			$component->source_file = $this->source_file;
			$component->starting_line_no = $this->cur_line_no;
			$parent_component->add_child($component);
			$text = $match[4];
		} 
		if (strlen($text) > 0)
		{
			$component = &$this->get_text_node($text);
			$parent_component->add_child($component);
		} 
	} 

	function check_server_id(&$parent_component, &$component)
	{
		$tree =& $parent_component;
		if (is_a($component, 'server_tag_component_tag'))
		{ 
			// Move up to the root
			while (!is_null($tree->parent))
			{
				$tree =& $tree->parent;
			} 
		}
		elseif($tree->parent)
			 $tree =& $tree->parent;

		$server_id = $component->get_server_id();
		
		if ($tree->find_child($server_id))
		{
			error('DUPLICATEID', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, array('server_id' => $server_id,
					'tag' => $component->tag,
					'file' => $component->source_file,
					'line' => $component->starting_line_no));
		} 
	} 
	// --------------------------------------------------------------------------------
	/**
	* Used to parse (recursively) parse the source template. It is initially
	* invoked by the Compiletemplate function, the first component argument
	* being a root_compiler_component. Accesses the $tag_dictionary
	* 
	* @see Compiletemplate
	* @see root_compiler_component
	* @see tag_dictionary
	* @param object $ compile time component
	* @return void 
	* @access protected 
	*/
	function parse(&$parent_component)
	{
		$tag_info = null;
		$parent_component->contents = '';

		while ($this->match_text($this->tag_starting_pattern, $start_maches))
		{
			$tag = $start_maches[3];
			$this->parse_text($parent_component, $start_maches[1]);
			if ($start_maches[2] == '/')
			{
				if (isset($parent_component->tag))
				{
					if ($tag != $parent_component->tag)
					{
						error('UNEXPECTEDCLOSE', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, array('tag' => $tag,
								'expect_tag' => $parent_component->tag,
								'file' => $this->source_file,
								'line' => $this->cur_line_no));
					} 
					else
					{
						return;
					} 
				} 
				else
				{
					error('UNEXPECTEDCLOSE2', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, array('tag' => $tag,
							'file' => $this->source_file,
							'line' => $this->cur_line_no));
				} 
			} 
			else
			{
				$tag_info = &$this->tag_dictionary->gettag_info($tag);
				$tag_class = $tag_info->tag_class;

				$component =& new $tag_class();
				$component->tag = $tag;
				$component->source_file = $this->source_file;
				$component->starting_line_no = $this->cur_line_no;

				if ($start_maches[4] != '>')
				{
					$this->parse_attributes($component);
						
					if (!$this->match_text('/^\/?>/', $start_maches))
					{
						error('EXPECTING_>', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, 
							array('file' => $this->source_file,
										'line' => $this->cur_line_no,
										'tag' => $component->tag));
					} 
				} 

				$this->check_server_id($parent_component, $component);
				$parent_component->add_child($component);
				$component->check_nesting_level();

				$parsing_policy = $component->pre_parse();
				if ($tag_info->end_tag == ENDTAG_REQUIRED)
				{
					if ($parsing_policy == PARSER_FORBID_PARSING)
					{
						if ($this->match_text('/^(.*)' . preg_quote('</' . $component->tag . '>', '/') . '/Usi', $literal_match))
						{
							$literal_component = &$this->get_text_node($literal_match[1]);
							$component->add_child($literal_component);
						} 
						else
						{
							error('MISSINGCLOSE', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, array('tag' => $component->tag,
									'file' => $this->source_file,
									'line' => $this->cur_line_no));
						} 
					} 
					else
					{
						$this->parse($component);
					} 
					$component->has_closing_tag = true;
				} 
				else
				{
					$component->has_closing_tag = false;
				} 
			} 
		} 

		if (isset($parent_component->tag))
		{
			$parenttag_info =& $this->tag_dictionary->gettag_info($parent_component->tag);
			if ($parenttag_info->end_tag != ENDTAG_REQUIRED)
			{
				$this->parse_text($parent_component, $this->rawtext);
			} 
			else
			{
				error('MISSINGCLOSE', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, array('tag' => $parent_component->tag,
						'file' => $this->source_file,
						'line' => $this->cur_line_no));
			} 
		} 
		else
		{
			$this->parse_text($parent_component, $this->rawtext);
		} 
	} 
	/**
	* Provide local method of same name to help with Unit testing
	* 
	* @param string $ path and file of source template
	* @return string raw text from template file
	* @access private 
	*/
	function read_template_file($sourcefile)
	{
		return read_template_file($sourcefile);
	} 
	/**
	* Returns an instance of text_node
	* 
	* @see text_node
	* @param string $ text from template
	* @return text _node
	* @access private 
	*/
	function &get_text_node($text)
	{
		return new text_node($text);
	} 
	/*
	* Returns an instance of variable_reference
	* @see variable_reference
	* @return variable_reference
	* @access private
	*/
	function &get_variable_reference()
	{
		return new variable_reference();
	} 
} 

?>