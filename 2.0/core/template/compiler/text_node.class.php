<?php

/**
* Used to write literal text from the source template to the compiled
* template
*/
class text_node extends compiler_directive_tag
{
	/**
	* A text string to write
	* 
	* @var string 
	* @access private 
	*/
	var $contents;

	/**
	* Constructs text_node
	* 
	* @param string $ contents of the text node
	* @access protected 
	*/
	function text_node($text)
	{
		$this->contents = $text;
	} 

	/**
	* Writes the contents of the text node to the compiled template
	* using the write_html method
	* 
	* @param code $ _writer
	* @return void 
	* @access protected 
	*/
	function generate(&$code)
	{
		$code->write_html($this->contents);
	} 
} 

?>