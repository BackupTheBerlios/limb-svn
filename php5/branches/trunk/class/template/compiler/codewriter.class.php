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
* Provides an API for generating the compiled template.
*/
class codewriter
{
  const CODE_WRITER_MODE_PHP = 1;
  const CODE_WRITER_MODE_HTML = 2;
  
	/**
	* String containing the compiled template
	*/
	protected $code = '';
	/**
	* The current state of the writer.
	*/
	protected $mode;
	/**
	* A prefix to add to the compiled template construct and render functions
	*/
	protected $function_prefix = '';
	/**
	* A suffix to add to the compiled template construct and render functions
	*/
	protected $function_suffix = 1;
	/**
	* List of files to write include statements for in the compiled template,
	* such as runtime component class files.
	* @access protected 
	*/
	protected $include_list = array();
	protected $temp_var_name = 1;
	
	function __construct()
	{
	  $this->mode = self :: CODE_WRITER_MODE_HTML;
	}

	/**
	* Puts the writer into PHP mode, writing an opening PHP processing
	* instruction to the template. Does nothing if writer is already
	* in PHP mode
	*/
	protected function switch_to_php()
	{
		if ($this->mode == self :: CODE_WRITER_MODE_HTML)
		{
			$this->mode = self :: CODE_WRITER_MODE_PHP;
			$this->code .= '<?php ';
		} 
	} 

	/**
	* Puts the writer into HTML mode, writing an closing PHP processing
	* instruction to the template. Does nothing if writer is already in
	* HTML mode
	*/
	protected function switch_to_html()
	{
		if ($this->mode == self :: CODE_WRITER_MODE_PHP)
		{
			$this->mode = self :: CODE_WRITER_MODE_HTML;
			$this->code .= ' ?>';
		} 
	} 

	/**
	* Writes some PHP to the compiled template
	*/
	public function write_php($text)
	{
		$this->switch_to_php();
		$this->code .= $text;
	} 

	/**
	* Writes some HTML to the compiled template
	*/
	public function write_html($text)
	{
		$this->switch_to_html();
		$this->code .= $text;
	} 

	/**
	* Returns the finished compiled template, adding the include directives
	* at the start of the template
	*/
	public function get_code()
	{
		$this->switch_to_html();
		$includecode = '';
		foreach($this->include_list as $includefile)
		{
			$includecode .= "require_once('$includefile');\n";
		} 

		if (!empty($includecode))
		{
			$pattern = '/' . preg_quote('<?php ', '/') . '/';
			if (preg_match($pattern, $this->code))
			{
				return preg_replace($pattern, '<?php ' . $includecode, $this->code, 1);
			} 
			else
			{
				return '<?php ' . $includecode . '?>';
			} 
		} 
		else
		{
			return $this->code;
		} 
	} 

	/**
	* Adds an include file (e.g a runtime component class file) to the
	* internal list. Checks that file has not already been included.
	* <br />Note that the path to the file to be included will need to
	* be in PHP's runtime include path.
	*/
	public function register_include($includefile)
	{
		if (!in_array($includefile, $this->include_list))
		{
			$this->include_list[] = $includefile;
		} 
	} 

	/**
	* Begins writing a PHP function to the compiled template, using the
	* function_prefix and the function_suffix, the latter being post incremented
	* by one.
	*/
	public function begin_function($ParamList)
	{
		$funcname = 'tpl' . $this->function_prefix . $this->function_suffix++;
		$this->write_php('function ' . $funcname . $ParamList . "\n{\n");
		return $funcname;
	} 

	/**
	* Finish writing a PHP function to the compiled template
	*/
	public function end_function()
	{
		$this->write_php("\n}\n");
	} 

	/**
	* Sets the function prefix
	*/
	public function set_function_prefix($prefix)
	{
		$this->function_prefix = $prefix;
	} 

	public function get_temp_variable()
	{
		$var = $this->temp_var_name++;
		if ($var > 675) 
		{
			return chr(65 + ($var/26)/26) . chr(65 + ($var/26)%26) . chr(65 + $var%26);
		} 
		elseif ($var > 26) 
		{
			return chr(65 + ($var/26)%26) . chr(65 + $var%26);
		} 
		else 
		{
			return chr(64 + $var);
		}
	} 
} 

?>