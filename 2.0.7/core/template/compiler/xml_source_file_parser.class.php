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

require_once (LIMB_DIR . 'core/template/compiler/xml_pull.inc.php');

/**
* The source template parser which uses the xml_pull parser
* 
* @todo This is not in use yet. Planned to replace the existing regex based parser
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
	* Instance of xml_pull
	* 
	* @var xml _pull
	* @access private 
	*/
	var $parser;
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
	var $cur_byte_index;
	/**
	* Lists of tags obtained from tag_dictionary
	* 
	* @var array 
	* @access private 
	*/
	var $tag_list;
	/**
	* Regex pattern to match the contents of a tag.
	* 
	* @var string 
	* @access private 
	*/
	var $variable_reference_pattern; 
	// ----------------------------------------------------------------------------
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
		$this->parser = &new xml_pull($this->rawtext);
		$this->cur_byte_index = 0;
		$this->text = '';
		$this->get_tags();
		$this->initialize_variable_reference_pattern();
	} 
	// ----------------------------------------------------------------------------
	/**
	* Builds the tag_list, converting tag names to lower case
	* 
	* @return void 
	* @access private 
	*/
	function get_tags()
	{
		$this->tag_list = array_map('strtolower', $this->tag_dictionary->gettag_list());
	} 
	// ----------------------------------------------------------------------------
	/**
	* Builds the regex for fetching contents of tags
	* 
	* @return void 
	* @access private 
	*/
	function initialize_variable_reference_pattern()
	{
		$this->variable_reference_pattern = '/^(.*){(' . preg_quote('$', '/') . '|' . preg_quote('#', '/') . '|' . preg_quote('^', '/') . ')(\w+)}(.*)$/Usi';
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
			// Set up the Sourcefile and line number for errors
			$component->source_file = $this->source_file;
			$component->starting_line_no = $this->get_line_number();

			$parent_component->add_child($component);
			$text = $match[4];
		} 
		if (strlen($text) > 0)
		{
			$component = &$this->get_text_node($text);
			$parent_component->add_child($component);
		} 
	} 
	// ----------------------------------------------------------------------------
	/**
	* Make sure we never have a duplicate Server Id in the component tree we build.
	* 
	* @param object $ parent component
	* @param object $ current component
	* @return void 
	* @access private 
	*/
	function check_server_id(&$parent_component, &$component)
	{ 
		// Move up to the root
		$root = &$parent_component;
		while (!is_null($root->parent))
		{
			$root = &$root->parent;
		} 

		$server_id = $component->get_server_id();
		if ($root->find_child($server_id))
		{
			error('DUPLICATEID', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, array('server_id' => $server_id,
					'tag' => $component->tag,
					'file' => $component->source_file,
					'line' => $component->starting_line_no));
		} 
	} 
	// ----------------------------------------------------------------------------
	/**
	* Used to parse the source template.
	* Initially invoked by the Compiletemplate function,
	* the first component argument being a root_compiler_component.
	* Uses the tag_dictionary to spot compiler components
	* 
	* @see Compiletemplate
	* @see root_compiler_component
	* @see tag_dictionary
	* @param object $ compile time component
	* @return void 
	* @access protected 
	*/
	function parse(&$component_root)
	{
		$tag_info = null;
		$parent_component = null;
		$component = &$component_root; # Set the starting component
		$component->contents = ''; 
		// Monitor the state for literal components
		$literal_tag = '';
		$forbid_parsing = false;

		while ($event = $this->parser->parse())
		{
			$this->cur_byte_index = $event->position(); # Keep track of location
			
			switch ($event->type())
			{ 
				// ----------------------------------------------------------------
				case XML_OPEN: // XML opening tags
					// Make sure lower case - could be moved to xml_pull?
					$tag = strtolower($event->tag());

					$attrs = $event->attributes(); 
					// Make sure array keys are lower case for core:include
					// This could be moved to xml_pull
					foreach ($attrs as $a_key => $a_value)
					{
						$attrs[strtolower($a_key)] = $a_value;
					} 
					// Unless parsing is forbidden, components are dealt with here
					if (in_array($tag, $this->tag_list) && !$forbid_parsing)
					{ 
						// Get tag info
						$tag_info = &$this->tag_dictionary->gettag_info($tag);
						$tag_class = $tag_info->tag_class; 
						// Assign current component to parent reference
						$parent_component = &$component; 
						// Create the new component
						$component = &new $tag_class();
						$component->tag = $tag; 
						// Set up the Sourcefile and line number for errors
						$component->source_file = $this->source_file;
						$component->starting_line_no = $this->get_line_number(); 
						// Assign attributes to new component
						$component->set_attributes($attrs); 
						// Check for duplicate IDs
						$this->check_server_id($parent_component, $component); 
						// Add child to parent
						$parent_component->add_child($component); 
						// Call the component self validation check
						$component->check_nesting_level(); 
						// Switch parser state to Forbid parsing if necessary
						if ($component->pre_parse() == PARSER_FORBID_PARSING)
						{
							$literal_tag = $tag;
							$forbid_parsing = true;
						} 
						// Cleanup for components that have no closing tag
						if ($tag_info->end_tag == ENDTAG_FORBIDDEN)
						{
							$component->has_closing_tag = false;
							$component = &$parent_component;
							$parent_component = &$component->parent;
							$literal_tag = '';
							$forbid_parsing = false;
						} 
					} 
					else
					{ 
						// Deal with normal HTML here
						$component->add_child($this->get_text_node('<' . $tag));
						foreach ($attrs as $key => $value)
						{
							if (is_null ($value))
							{
								$component->add_child($this->get_text_node(' ' . $key));
							} 
							else
							{
								$component->add_child($this->get_text_node(' ' . $key . '="'));
								if (!$forbid_parsing)
								{
									$this->parse_text($component, $value);
								} 
								else
								{
									$component->add_child($this->get_text_node($value));
								} 
								$component->add_child($this->get_text_node('"'));
							} 
						} 
						$component->add_child($this->get_text_node('>'));
					} 

					break; 
				// ----------------------------------------------------------------
				case XML_CLOSE: // XML opening tags
					// Make sure lower case - could be moved to xml_pull?
					$tag = strtolower($event->tag()); 
					// Cleanup if this is the closing tag of a Literal
					if ($forbid_parsing && $literal_tag == $tag)
					{
						$literal_tag = '';
						$forbid_parsing = false;
						$component->has_closing_tag = true;
						$component = &$parent_component;
						$parent_component = &$component->parent; 
						// Handle closing of components
					} 
					else if (in_array($tag, $this->tag_list) && !$forbid_parsing)
					{ 
						// Check for unexpected closing tags
						if ($component->tag != $tag)
						{ 
							// If there's a concrete parent, say what was expected
							if (isset($component->tag))
							{
								error('UNEXPECTEDCLOSE', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, array('tag' => $tag,
										'expect_tag' => $component->tag,
										'file' => $this->source_file,
										'line' => $this->get_line_number())); 
								// No concrete parent? (e.g. root_compiler_component)
							} 
							else
							{
								error('UNEXPECTEDCLOSE2', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, array('tag' => $tag,
										'file' => $this->source_file,
										'line' => $this->get_line_number()));
							} 
						} 
						else
						{
							$tag_info = &$this->tag_dictionary->gettag_info($tag); 
							// Check for tags which shouldn't be closed
							if ($tag_info->end_tag == ENDTAG_FORBIDDEN)
							{
								error('UNEXPECTEDCLOSE2', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, array('tag' => $tag,
										'file' => $this->source_file,
										'line' => $this->get_line_number()));
							} 
							$component->has_closing_tag = true;
							$component = &$parent_component;
							$parent_component = &$component->parent;
						} 
						// Handle normal HTML
					} 
					else
					{
						$component->add_child($this->get_text_node('</' . $tag . '>'));
					} 
					break; 
				// ----------------------------------------------------------------
				case XML_TEXT: // Handle the body of a tag
					// If parsing not forbidden, allow literals
					if (!$forbid_parsing)
						$this->parse_text($component, $event->text()); 
					// Else dump the text straight through
					else
						$component->add_child($this->get_text_node($event->text()));
					break;
			} 
		} 
		// Check for closing tag - the component should be back to where we started
		if (get_class($component) != get_class($component_root))
		{
			error('MISSINGCLOSE', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, array('tag' => $component->tag,
					'file' => $this->source_file,
					'line' => $this->get_line_number()));
		} 
	} 
	// ----------------------------------------------------------------------------
	/*
	* Calculates the line number from the byte index
	* @return int the current line number
	* @access private
	*/
	function get_line_number()
	{
		static $lineno = 0;
		static $index = 0;
		static $text = null;
		static $lines;
		if (is_null($text))
		{
			$text = explode("\n", $this->rawtext);
			$lines = count ($text);
		} 

		for (;$lineno < $lines;$lineno++)
		{
			$index += strlen($text[$lineno]);
			if ($index > $this->cur_byte_index)
				return ($lineno + 1);
		} 
		return $lineno;
	} 
	// ----------------------------------------------------------------------------
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
	// ----------------------------------------------------------------------------
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
	// ----------------------------------------------------------------------------
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