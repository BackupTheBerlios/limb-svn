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
/**
* Base class for compile time components. Compile time component methods are
* called by the template parser source_file_parser.<br />
* Note this in the comments for this class, parent and child refer to the XML
* heirarchy in the template, as opposed to the PHP class tree.
*/
abstract class compiler_component
{
	/**
	* XML attributes of the tag
	*/
	public $attributes = array();
	/**
	* child compile-time components
	*/
	public $children = array();
	public $vars = array();
	/**
	* Parent compile-time component
	*/
	public $parent = null;
	/**
	* Stores the identifying component ID
	*/
	public $server_id;
	/**
	* Name of the XML tag as it appears in the template. This would include
	* the namespace prefix, if applicable.
	*/
	public $tag;
	/**
	* Used to identify the source template file, when generating compile time
	* error messages.
	*/
	public $source_file;
	/**
	* Used to indentify the line number where a compile time error occurred.
	*/
	public $starting_line_no;
	/**
	* Instance of a CoreWraptag
	*/
	public $wrapping_component;
	/**
	* Defines whether the tag is allowed to have a closing tag
	*/
	public $has_closing_tag;
	
	/**
	* Sets the XML attributes for this component (as extracted from the
	* template)
	*/
	public function set_attributes($attrib)
	{
		$this->attributes = $attrib;
	} 
	
	public function set_source_file($source_file)
	{
	  $this->source_file = $source_file;
	}
	/**
	* Remove an attribute from the list
	* @param string name of attribute
	*/
	public function remove_attribute($attrib) 
	{
    unset($this->attributes[strtolower($attrib)]);
	}
	
	public function has_attribute($attrib) 
	{
		return isset($this->attributes[strtolower($attrib)]);
	}
	
	/**
	* Get the value of the XML id attribute
	*/
	public function get_client_id()
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
	*/
	public function get_server_id()
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
	*/
	public function add_child($child)
	{
		$child->parent = $this;
		$this->children[] = $child;
	} 

	/**
	* Removes a child component, given it's ServerID
	*/
	public function remove_child($server_id)
	{
		foreach(array_keys($this->children) as $key)
		{
			$child = $this->children[$key];
			if ($child->get_server_id() == $server_id)
			{
			unset($this->children[$key]);
				return $child;
			} 
		}
	} 

	/**
	* Returns a child component, given it's ServerID
	*/
	public function find_child($server_id)
	{
		foreach(array_keys($this->children) as $key)
		{
			if ($this->children[$key]->get_server_id() == $server_id)
				return $this->children[$key];
			else
			{
				if($result = $this->children[$key]->find_child($server_id))
					return $result;
			} 
		} 
		return false;
	} 

	/**
	* Returns a child component, given it's compile time component class
	*/
	public function find_child_by_class($class)
	{
		foreach(array_keys($this->children) as $key)
		{
			if ($this->children[$key] instanceof $class)
			{
				return $this->children[$key];
			} 
			else
			{
				$result = $this->children[$key]->find_child_by_class($class);
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
	*/
	public function find_immediate_child_by_class($class)
	{
		foreach(array_keys($this->children) as $key)
		{
			if ($this->children[$key] instanceof  $class)
			{
				return $this->children[$key];
			} 
		} 
		return false;
	} 

	/**
	* Returns a parent component, recursively searching parents by their
	* compile time component class name
	*/
	public function find_parent_by_class($class)
	{
		$parent = $this->parent;
		while ($parent && !($parent instanceof $class))
		{
			$parent = $parent->parent;
		} 
		return $parent;
	} 

	/**
	* Calls the prepare method for each child component, which will override
	* this method it it's concrete implementation. In the subclasses, prepare
	* will set up compile time variables. For example the CoreWraptag uses
	* the prepare method to assign itself as the wrapping component.
	*/
	public function prepare()
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
	*/
	public function check_nesting_level()
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
	*/
	public function pre_parse()
	{
		return PARSER_REQUIRE_PARSING;
	} 

	/**
	* If a parent compile time component exists, returns the value of the
	* parent's get_dataspace() method, which will be a concrete implementation
	*/
	public function get_dataspace()
	{
		if (isset($this->parent))
		{
			return $this->parent->get_dataspace();
		} 
	} 

	/**
	* Gets the parent in the dataspace, if one exists
	*/
	public function get_parent_dataspace()
	{
		$dataspace = $this->get_dataspace();
		if (isset($dataspace->parent))
		{
			return $dataspace->parent->get_dataspace();
		} 
	} 

	/**
	* Gets a root dataspace
	*/
	public function get_root_dataspace()
	{
		$root = $this;
		while ($root->parent != null)
		{
			$root = $root->parent;
		} 
		return $root;
	} 

	/**
	* Gets the dataspace reference code of the parent
	*/
	public function get_dataspace_ref_code()
	{
		return $this->parent->get_dataspace_ref_code();
	} 

	/**
	* Gets the component reference code of the parent. This is a PHP string
	* which is used in the compiled template to reference the component in
	* the hierarchy at runtime
	*/
	public function get_component_ref_code()
	{
		return $this->parent->get_component_ref_code();
	} 

	/**
	* Calls the generate_constructor() method of each child component
	*/
	public function generate_constructor($code)
	{
		foreach(array_keys($this->children) as $key)
		{
			$this->children[$key]->generate_constructor($code);
		} 
	} 
	

	/**
	* Calls the generate() method of each child component
	*/
	public function generate_contents($code)
	{
		foreach(array_keys($this->children) as $key)
		{
			$this->children[$key]->generate($code);
		} 
	} 

	/**
	* Pre generation method, calls the wrapping_component
	* generate_wrapper_prefix() method if the component exists
	*/
	public function pre_generate($code)
	{
		if (isset($this->wrapping_component))
		{
			if($this->is_debug_enabled())
			{				
				$code->write_html("<div style='border:dashed 1px green;padding: 10px 10px 10px 10px;'>");
				
				$this->_generate_debug_editor_link_html($code, $this->wrapping_component->resolved_source_file);
			}

			$this->wrapping_component->generate_wrapper_prefix($code);
		}
	} 
	
	protected function _generate_debug_editor_link_html($code, $file_path)
	{
		if(!defined('WS_SCRIPT_WRITTEN'))
		{

			$code->write_html('	<SCRIPT LANGUAGE="JScript">
													function run_template_editor(path)
													{
														WS = new ActiveXObject("WScript.shell");
														WS.exec("uedit32.exe " + path);
													}
													</SCRIPT>');
		
			define('WS_SCRIPT_WRITTEN', true);
		}
		
		if(substr($file_path, 1, 2) != fs :: separator())
		{
		  $items = fs :: explode_path($_SERVER['PATH_TRANSLATED']);
		  array_pop($items);
		  
		  $file_path = fs :: path($items) . fs :: separator() . $file_path;
		}		
		
		$file_path = addslashes(fs :: clean_path($file_path));
		$code->write_html("<a href='#'><img onclick='run_template_editor(\"{$file_path}\");' src='/shared/images/i.gif' alt='{$file_path}' title='{$file_path}' border='0'></a>");
	}

	/**
	* Post generation method, calls the wrapping_component
	* generate_wrapper_postfix() method if the component exists
	*/
	public function post_generate($code)
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
	*/
	public function generate($code)
	{
		$this->pre_generate($code);
		$this->generate_contents($code);
		$this->post_generate($code);
	} 
	
	public function is_debug_enabled()
	{
		return (defined('DEBUG_TEMPLATE_ENABLED') && constant('DEBUG_TEMPLATE_ENABLED'));
	}
} 

?>