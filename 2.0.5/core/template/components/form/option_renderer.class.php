<?php

// --------------------------------------------------------------------------------
// Simple renderer for OPTIONs.  Does not support disabled and label attributes.
// Does not support OPTGROUP tags.
/**
* Deals with rendering option elements for HTML select tags
* 
*/
class option_renderer
{
	/**
	* Renders an option, sending directly to display. Called from a compiled
	* template render function.
	* 
	* @param string $ value to place within the option value attribute
	* @param string $ contents of the option tag
	* @param boolean $ whether the option is selected or not
	* @return void 
	* @access protected 
	*/
	function render_attribute($key, $contents, $selected)
	{
		echo '<option value="';
		echo htmlspecialchars($key, ENT_QUOTES);
		echo '"';
		if ($selected)
		{
			echo " selected";
		} 
		echo '>';
		if (empty($contents))
		{
			echo htmlspecialchars($key, ENT_QUOTES);
		} 
		else
		{
			echo $contents;
		} 
		echo '</option>';
	} 
}

?>