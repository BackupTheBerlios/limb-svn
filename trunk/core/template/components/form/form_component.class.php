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
require_once(LIMB_DIR . 'core/lib/util/array_dataset.class.php');
require_once(LIMB_DIR . 'core/template/tag_component.class.php');
require_once(LIMB_DIR . 'core/validators/error_list.class.php');
require_once(LIMB_DIR . 'core/lib/util/dataspace_registry.class.php');

/**
* The form_component provide a runtime API for control the behavior of a form
*/
class form_component extends tag_component
{
	/**
	* Switch to identify whether the form has errors or not
	* 
	* @var boolean TRUE means no errors
	* @access private 
	*/
	var $is_valid = true;
	/**
	* An indexed array of variable names used to build hidden form fields which
	* are passed on in the next POST request
	* 
	* @var array 
	* @access private 
	*/
	var $state_vars = array();

	/**
	* Determined whether the form has errors.
	* 
	* @return boolean TRUE if the form has errros
	* @access public 
	*/
	function is_valid()
	{
		return $this->is_valid;
	} 
	
	function set_valid_status($status)
	{
		$this->is_valid = $status;
	}

	/**
	* Returns the error_list if it exists or an empty_error_list if not
	* 
	* @return object 
	* @access public 
	*/
	function &get_error_dataset()
	{
		$error_list =& error_list :: instance();
		
		$errors = $error_list->export();
		
		if (!sizeof($errors))
			return new empty_dataset();
		
		$array = array();
		foreach($errors as $field_name => $errors_array)
		{
			foreach($errors_array as $error)
			{
				if($child =& $this->find_child($field_name))
				{
					if(!$label = $child->get_attribute('label'))
						$label = $child->get_server_id();
						
					$array[] = array('label' => $label, 'error_message' => $error['error']);
				}
			}
		}
		
		return new array_dataset($array);
	} 

	/**
	* Identify a variable stored in the dataspace of the component, which
	* should be passed as a hidden form field in the form post.
	* 
	* @param string $ name of variable
	* @return void 
	* @access public 
	*/
	function preserve_state($variable, $value=null)
	{
		$this->state_vars[$variable] = $value;
	} 
	
	function is_first_time()
	{
		if(isset($this->attributes['name']))
		{
			$dataspace =& dataspace_registry :: get($this->attributes['name']);
			
			return $dataspace->get('submitted') ? false : true;
		}	
		else
		{
		  $request = request :: instance();
		  return $request->has_attribute('submitted');
		}
	} 

	/**
	* Renders the hidden fields for variables which should be preserved
	* 
	* @return void 
	* @access public 
	*/
	function render_state()
	{		
		foreach ($this->state_vars as $var => $value)
		{
			echo '<input type="hidden" name="';
			echo $this->attributes['name'] . '[' . $var . ']';
			echo '" value="';
			
			if(!$value)
				echo htmlspecialchars($this->get($var), ENT_QUOTES);
			else
				echo htmlspecialchars($value, ENT_QUOTES);
				
			echo '">';
		} 
	} 
	
	function render_attributes()
	{
		if(!isset($this->attributes['action']))
		{
			$this->attributes['action'] = $_SERVER['PHP_SELF'];
			
			$request = request :: instance();
			if($request->has_attribute('popup'))
				$this->attributes['action'] .= '?popup=1';
		}
			
		parent :: render_attributes();
	}

} 

?>