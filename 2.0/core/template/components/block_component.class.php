<?php
/**
* The block tag can be used to show or hide the contents of the block.
* The block_component provides an API which allows the block to be shown
* or hidden at runtime.
*/
class block_component extends component
{
	/**
	* Whether the block is visible or not
	* 
	* @var boolean 
	* @access private 
	*/
	var $visible = true;
	/**
	* Called within the compiled template render function to determine
	* whether block should be displayed.
	* 
	* @return boolean current state of the block
	* @access protected 
	*/
	function is_visible()
	{
		return $this->visible;
	} 

	/**
	* Changes the block state to visible
	* 
	* @return void 
	* @access public 
	*/
	function show()
	{
		$this->visible = true;
	} 

	/**
	* Changes the block state to invisible
	* 
	* @return void 
	* @access public 
	*/
	function hide()
	{
		$this->visible = false;
	} 
} 

?>