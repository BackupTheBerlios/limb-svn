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
* A null implementation of the DataSpace and Iterator
*/
class empty_dataset
{ 
	// --------------------------------------------------------------------------------
	// Iterator implementation
	/**
	* Iterator Method
	* 
	* @return void 
	* @access public 
	*/
	function reset()
	{
	} 
	/**
	* Iterator Method
	* 
	* @return boolean FALSE
	* @access public 
	*/
	function next()
	{
		return false;
	} 
	// --------------------------------------------------------------------------------
	// DataStore implementation
	/**
	* DataSpace Method
	* 
	* @param string $ name of variable
	* @return string empty string
	* @access public 
	*/
	function &get($name)
	{
		return '';
	} 
	/**
	* DataSpace Method
	* 
	* @param string $ name of variable
	* @param string $ value of variable
	* @return void 
	* @access public 
	*/
	function set($name, $value)
	{
	} 
	/**
	* DataSpace Method
	* 
	* @param string $ name of variable
	* @param string $ value of variable
	* @return void 
	* @access public 
	*/
	function append($name, $value)
	{
	} 
	/**
	* DataSpace Method
	* 
	* @param string $ name of variable
	* @return void 
	* @access public 
	*/
	function clear($name)
	{
	} 
	/**
	* DataSpace Method
	* 
	* @param array $ associative array
	* @return void 
	* @access public 
	*/
	function import($valuelist)
	{
	} 
	/**
	* DataSpace Method
	* 
	* @param array $ associative array
	* @return void 
	* @access public 
	*/
	function import_append($valuelist)
	{
	} 
	/**
	* DataSpace Method
	* 
	* @return array empty array
	* @access public 
	*/
	function &export()
	{
		return array();
	} 
	/**
	* DataSpace Method
	* 
	* @param object $ instance of filter class containing a doFilter() method
	* @return void 
	* @access public 
	*/
	function register_filter(&$filter)
	{
	} 
	/**
	* DataSpace Method
	* 
	* @return void 
	* @access protected 
	*/
	function prepare()
	{
	} 
} 

?>