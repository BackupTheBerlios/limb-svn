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
* Server component tags have a corresponding server component which represents
* an API which can be used to manipulate the marked up portion of the template.
*/
class server_component_tag extends compiler_component
{
  var $runtime_component_path = '';
  
  function server_component_tag()
  {
    $this->runtime_component_path = LIMB_DIR . '/class/template/component';//???
  }
  
	/**
	* Returns a string of PHP code identifying the component in the hierarchy.
	* 
	* @return string 
	* @access protected 
	*/
	function get_component_ref_code()
	{
		$path = $this->parent->get_component_ref_code();
		return $path . '->children[\'' . $this->get_server_id() . '\']';
	} 

	/**
	* Calls the parent get_component_ref_code() method and writes it to the
	* compiled template, appending an add_child() method used to create
	* this component at runtime
	* 
	* @param code $ _writer
	* @return string 
	* @access protected 
	*/
	function generate_constructor(&$code)
	{
		if (file_exists($this->runtime_component_path . '.class.php'))
			$code->register_include($this->runtime_component_path . '.class.php');
		else
			error('run time component file not found', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
		
		$component_class_name = end(explode('/', $this->runtime_component_path));
		
		if(!$component_class_name)
			error('empty component class name', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
		
		$code->write_php($this->parent->get_component_ref_code() . '->add_child(new ' . $component_class_name . '(), \'' . $this->get_server_id() . '\');' . "\n");
	
		parent::generate_constructor($code);
	} 
} 

?>