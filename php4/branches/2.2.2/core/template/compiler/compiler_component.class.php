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
* Base class for compile time components. Compile time component methods are
* called by the template parser source_file_parser.<br />
* Note this in the comments for this class, parent and child refer to the XML
* heirarchy in the template, as opposed to the PHP class tree.
*/
class compiler_component
{
	/**
	* XML attributes of the tag
	* 
	* @var array 
	* @access private 
	*/
	var $attributes = array();
	/**
	* child compile-time components
	* 
	* @var array of compile time component objects
	* @access private 
	*/
	var $children = array();
	/**
	* ???
	* 
	* @var array 
	* @access private 
	*/
	var $vars = array();
	/**
	* Parent compile-time component
	* 
	* @var object subclass of compiler_component
	* @access private 
	*/
	var $parent = null;
	/**
	* Stores the identifying component ID
	* 
	* @var string value of id attribute
	* @access private 
	*/
	var $server_id;
	/**
	* Name of the XML tag as it appears in the template. This would include
	* the namespace prefix, if applicable.
	* 
	* @var string tag name
	* @access private 
	*/
	var $tag;
	/**
	* Used to identify the source template file, when generating compile time
	* error messages.
	* 
	* @var string source template filename
	* @access private 
	*/
	var $source_file;
	/**
	* Used to indentify the line number where a compile time error occurred.
	* 
	* @var int line number
	* @access private 
	*/
	var $starting_line_no;
	/**
	* Instance of a CoreWraptag
	* 
	* @see CoreWraptag
	* @var CoreWraptag 
	* @access private 
	*/
	var $wrapping_component;
	/**
	* Defines whether the tag is allowed to have a closing tag
	* 
	* @var boolean 
	* @access private 
	*/
	var $has_closing_tag;

	/**
	* Sets the XML attributes for this component (as extracted from the
	* template)
	* 
	* @param array $ XML attributes as name/value pairs
	* @return void 
	* @access protected 
	*/
	function set_attributes($attrib)
	{
		$this->attributes = $attrib;
	} 
	
	/**
	* Remove an attribute from the list
	* @param string name of attribute
	* @return void
	* @access public
	*/
	function remove_attribute($attrib) 
	{
  	unset($this->attributes[strtolower($attrib)]);
	}
	
	function has_attribute($attrib) 
	{
		return isset($this->attributes[strtolower($attrib)]);
	}
	
	/**
	* Get the value of the XML id attribute
	* 
	* @return string value of id attribute
	* @access protected 
	*/
	function get_client_id()
	{
		if (isset($this->attributes['id']))
		{
			return $this->attributes['id'];
		} 
	} 

	/**
	* Returns the identifying server ID. It's value it determined in the
	* following order;
	* <ol>
	* <li>The XML id attribute in the template if it exists</li>
	* <li>The value of $this->server_id</li>
	* <li>An ID generated by the get_new_server_id() function</li>
	* </ol>
	* 
	* @see get_new_server_id
	* @return string value identifying this component
	* @access protected 
	*/
	function get_server_id()
	{
		if (!empty($this->attributes['id']))
		{
			return $this->attributes['id'];
		} 
		else if (!empty($this->server_id))
		{
			return $this->server_id;
		} 
		else
		{
			$this->server_id = get_new_server_id();
			return $this->server_id;
		} 
	} 

	/**
	* Adds a child component, by reference, to the array of children
	* 
	* @param object $ instance of a compile time component
	* @return void 
	* @access protected 
	*/
	function add_child(&$child)
	{
		$child->parent = &$this;
		$this->children[] = &$child;
	} 

	/**
	* Removes a child component, given it's ServerID
	* 
	* @param string $ server id
	* @return mixed if child is found, returns a reference to it or void
	* @access protected 
	*/
	function &remove_child($server_id)
	{
		foreach(array_keys($this->children) as $key)
		{
			$child = &$this->children[$key];
			if ($child->get_server_id() == $server_id)
			{
				unset($this->children[$key]);
				return $child;
			} 
		}
	} 

	/**
	* Returns a child component, given it's ServerID
	* 
	* @param string $ server id
	* @return mixed if child is found, returns a reference of false
	* @access protected 
	*/
	function &find_child($server_id)
	{
		foreach(array_keys($this->children) as $key)
		{
			if ($this->children[$key]->get_server_id() == $server_id)
				return $this->children[$key];
			else
			{
				if($result =& $this->children[$key]->find_child($server_id))
					return $result;
			} 
		} 
		return false;
	} 

	/**
	* Returns a child component, given it's compile time component class
	* 
	* @param string $ PHP class name
	* @return mixed if child is found, returns a reference of false
	* @access protected 
	*/
	function &find_child_by_class($class)
	{
		foreach(array_keys($this->children) as $key)
		{
			if (is_a($this->children[$key], $class))
			{
				return $this->children[$key];
			} 
			else
			{
				$result = &$this->children[$key]->find_child_by_class($class);
				if ($result)
				{
					return $result;
				} 
			} 
		} 
		return false;
	} 

	/**
	* Returns a child component, given it's compile time component class
	* 
	* @param string $ PHP class name
	* @return mixed if child is found, returns a reference of false
	* @access protected 
	*/
	function &find_immediate_child_by_class($class)
	{
		foreach(array_keys($this->children) as $key)
		{
			if (is_a($this->children[$key], $class))
			{
				return $this->children[$key];
			} 
		} 
		return false;
	} 

	/**
	* Returns a parent component, recursively searching parents by their
	* compile time component class name
	* 
	* @param string $ PHP class name
	* @return mixed if parent is found, returns a reference of void
	* @access protected 
	*/
	function & find_parent_by_class($class)
	{
		$parent =& $this->parent;
		while ($parent && !is_a($parent, $class))
		{
			$parent =& $parent->parent;
		} 
		return $parent;
	} 

	/**
	* Calls the prepare method for each child component, which will override
	* this method it it's concrete implementation. In the subclasses, prepare
	* will set up compile time variables. For example the CoreWraptag uses
	* the prepare method to assign itself as the wrapping component.
	* 
	* @return void 
	* @access protected 
	*/
	function prepare()
	{
		foreach($this->children as $key => $child)
		{
			$this->children[$key]->prepare();
		} 
	} 

	/**
	* Used to perform some error checking on the source template, such as
	* examining the tag hierarchy and triggering an error if a tag is
	* incorrectly nested. Concrete implementation is in subclasses
	* 
	* @return void 
	* @access protected 
	*/
	function check_nesting_level()
	{
	} 

	/**
	* Provides instruction to the template parser, while parsing is in
	* progress, telling it how it should handle the tag. Subclasses of
	* compiler_component will return different instructions.<br />
	* Available instructions are;
	* <ul>
	* <li>PARSER_REQUIRE_PARSING - default in this class. tag must be parsed</li>
	* <li>PARSER_FORBID_PARSING - tag may not be parsed</li>
	* <li>PARSER_ALLOW_PARSING - tag may can be parsed</li>
	* </ul>
	* In practice, the parser currently only pays attention to the 
	* PARSER_FORBID_PARSING instruction.<br />
	* Also used to perform error checking on template related to the syntax of
	* the concrete tag implementing this method.
	* 
	* @see source_file_parser
	* @return int PARSER_REQUIRE_PARSING
	* @access protected 
	*/
	function pre_parse()
	{
		return PARSER_REQUIRE_PARSING;
	} 

	/**
	* If a parent compile time component exists, returns the value of the
	* parent's get_dataspace() method, which will be a concrete implementation
	* 
	* @return mixed object compile time component if parent exists or void
	* @access protected 
	*/
	function &get_dataspace()
	{
		if (isset($this->parent))
		{
			return $this->parent->get_dataspace();
		} 
	} 

	/**
	* Gets the parent in the dataspace, if one exists
	* 
	* @return mixed object compile time data component if exists or void
	* @access protected 
	*/
	function &get_parent_dataspace()
	{
		$dataspace = &$this->get_dataspace();
		if (isset($dataspace->parent))
		{
			return $dataspace->parent->get_dataspace();
		} 
	} 

	/**
	* Gets a root dataspace
	* 
	* @return mixed object compile time data component if exists or void
	* @access protected 
	*/
	function &get_root_dataspace()
	{
		$root = &$this;
		while ($root->parent != null)
		{
			$root = &$root->parent;
		} 
		return $root;
	} 

	/**
	* Gets the dataspace reference code of the parent
	* 
	* @return string 
	* @access protected 
	*/
	function get_dataspace_ref_code()
	{
		return $this->parent->get_dataspace_ref_code();
	} 

	/**
	* Gets the component reference code of the parent. This is a PHP string
	* which is used in the compiled template to reference the component in
	* the hierarchy at runtime
	* 
	* @return string 
	* @access protected 
	*/
	function get_component_ref_code()
	{
		return $this->parent->get_component_ref_code();
	} 

	/**
	* Calls the generate_constructor() method of each child component
	* 
	* @param code $ _writer
	* @return void 
	* @access protected 
	*/
	function generate_constructor(&$code)
	{
		foreach(array_keys($this->children) as $key)
		{
			$this->children[$key]->generate_constructor($code);
		} 
	} 
	

	/**
	* Calls the generate() method of each child component
	* 
	* @param code $ _writer
	* @return void 
	* @access protected 
	*/
	function generate_contents(&$code)
	{
		foreach(array_keys($this->children) as $key)
		{
			$this->children[$key]->generate($code);
		} 
	} 

	/**
	* Pre generation method, calls the wrapping_component
	* generate_wrapper_prefix() method if the component exists
	* 
	* @see CoreWraptag
	* @param code $ _writer
	* @return void 
	* @access protected 
	*/
	function pre_generate(&$code)
	{
		if (isset($this->wrapping_component))
		{
			if($this->is_debug_enabled())
			{				
				$code->write_html("<div class='debug-tmpl-container'>");
				
				$this->_generate_debug_editor_link_html($code, $this->wrapping_component->resolved_source_file);
			}

			$this->wrapping_component->generate_wrapper_prefix($code);
		}
	} 
	
	function _generate_debug_editor_link_html(& $code, $file_path)
	{
//		if(!defined('WS_SCRIPT_WRITTEN'))
//		{
//
//			$code->write_html('	<SCRIPT LANGUAGE="JScript">
//													function run_template_editor(path)
//													{
//														WS = new ActiveXObject("WScript.shell");
//														WS.exec("uedit32.exe " + path);
//													}
//													</SCRIPT>');
//		
//			define('WS_SCRIPT_WRITTEN', true);
//		}
		
		$file_path = addslashes(fs :: clean_path($file_path));
		$code->write_html("<a href='#'><img class='debug-info-img' src='/shared/images/i.gif' alt='{$file_path}' title='{$file_path}' border='0'></a>");
	}

	/**
	* Post generation method, calls the wrapping_component
	* generate_wrapper_postfix() method if the component exists
	* 
	* @see CoreWraptag
	* @param code $ _writer
	* @return void 
	* @access protected 
	*/
	function post_generate(&$code)
	{
		if (isset($this->wrapping_component))
		{
			$this->wrapping_component->generate_wrapper_postfix($code);
			
			if($this->is_debug_enabled())
				$code->write_html('</div>');
		} 
	} 

	/**
	* Calls the local pre_generate(), generate_contents() and post_generate()
	* methods.
	* 
	* @param code $ _writer
	* @return void 
	* @access protected 
	*/
	function generate(&$code)
	{
		$this->pre_generate($code);
		$this->generate_contents($code);
		$this->post_generate($code);
	} 
	
	function is_debug_enabled()
	{
		return (defined('DEBUG_TEMPLATE_ENABLED') && constant('DEBUG_TEMPLATE_ENABLED'));
	}
} 

?>