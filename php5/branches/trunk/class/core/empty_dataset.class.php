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

class empty_dataset
{ 
	public function reset(){} 
	
	public function next()
	{
		return false;
	} 
	
	public function get($name)
	{
		return '';
	} 
	
	public function set($name, $value){} 
	
	public function append($name, $value){} 
	
	public function clear($name){} 
	
	public function import($valuelist){} 
	
	public function import_append($valuelist){} 
	
	public function export()
	{
		return array();
	} 
		
	public function get_total_row_count()
	{
		return 0;
	}
	
	public function counter()
	{
		return 0;
	}
} 

?>