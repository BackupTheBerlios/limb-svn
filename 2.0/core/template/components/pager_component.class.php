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
* Represents a page navigator at runtime.  The total number of items in the 
* list to be paged must be known before the navigator can be displayed.
*/
class pager_component extends component
{
	/**
	* Used while displaying a page number list to determine when a separator
	* should be shown between two page numbers
	* 
	* @var boolean 
	* @access protected 
	*/
	var $show_separator;

	/**
	* Used while displaying a page number list to the page number being displayed
	* 
	* @var integer 
	* @access protected 
	*/
	var $page;


	/**
	* The page number of the last page in the list.
	* 
	* @var integer 
	* @access protected 
	*/
	var $last_page;

	/**
	* The page number of the current page in the list.
	* 
	* @var integer 
	* @access protected 
	*/
	var $current_page = 0;

	var $current_section = 0;

	var $section = 0;

	var $pages_per_section = 10;
	
	var $section_has_changed = false;
	/**
	* number of items to display on each page of the list.
	* This is set via the items attribute of the pager:navigator tag.
	* 
	* @var integer 
	* @access protected 
	*/
	var $items = 20;

	/**
	* The total number of items in this list.
	* 
	* @var integer 
	* @access protected 
	*/
	var $total_items = 0;

	/**
	* The variable used to carry the current page in the URL.
	* 
	* @access protected 
	*/
	var $pager_variable = 'page';

	/**
	* The Url used to display individual pages of the list.
	* 
	* @access protected 
	*/
	var $base_url;

	/**
	* A paged dataset reference.  Used for determining the total number
	* of items the pager should navagate across.
	* 
	* @access protected 
	*/
	var $paged_dataset;
	
	
	/**
	* Initialize this class
	* 
	* @access public 
	*/
	function pager_component()
	{
		$this->base_url = $_SERVER['REQUEST_URI'];
		$pos = strpos($this->base_url, '?');
		if (is_integer($pos))
		{
			$this->base_url = substr($this->base_url, 0, $pos);
		} 
	} 
	
	/**
	* Set the total number of items in the list.
	* 
	* @access protected 
	*/
	function set_total_items($items)
	{
		$this->total_items = $items;
	} 
	
	function get_total_items()
	{
		return $this->total_items;
	} 

	function set_items_per_page($items)
	{
		$this->items = $items;
	} 


	/**
	* Set the database which this pager controls.
	* 
	* @param object $ dataset
	* @access public 
	*/
	function register_dataset(&$dataset)
	{
		$this->paged_dataset = &$dataset;
	} 

	/**
	* Get the item number of the first item in the list.
	* Usually called by the paged_dataset to determine where to
	* begin query.
	* 
	* @return integer 
	* @access public 
	*/
	function get_starting_item()
	{
		return $this->items * ($this->current_page - 1);
	} 

	/**
	* Get the item number of the first item in the list.
	* Usually called by the paged_dataset to determine how many
	* items are on a page.
	* 
	* @return integer 
	* @access public 
	*/
	function get_items_per_page()
	{
		return $this->items;
	} 

	/**
	* Is the current page being displayed the first page in the page list?
	* 
	* @return boolean 
	* @access public 
	*/
	function is_first()
	{
		return ($this->current_page == 1);
	} 

	/**
	* Is there a page available to display before the current page being displayed?
	* 
	* @return boolean 
	* @access public 
	*/
	function has_prev()
	{
		return ($this->current_page > 1);
	} 

	/**
	* Is there a page available to display after the current page being displayed?
	* 
	* @return boolean 
	* @access public 
	*/
	function has_next()
	{
		return ($this->current_page < $this->last_page);
	} 

	/**
	* Is the current page being displayed the last page in the page list?
	* 
	* @return boolean 
	* @access public 
	*/
	function is_last()
	{
		return ($this->current_page == $this->last_page);
	} 

	/**
	* Initialize values used by this component.
	* This is called automatically from the compiled template.
	* 
	* @return void 
	* @access protected 
	*/
	function prepare()
	{
		$this->current_page = @$_REQUEST[$this->pager_variable .'_'. $this->get_server_id()];
		if (empty($this->current_page))
		{
			$this->current_page = 1;
		} 
	
		if (isset($this->paged_dataset))
		{
			$this->set_total_items($this->paged_dataset->get_total_row_count());
		} 

		$this->last_page = ceil($this->total_items / $this->items);
		if ($this->last_page < 1)
		{
			$this->last_page = 1;
		} 

		$this->show_separator = false;
		$this->page = 0;

		$this->current_section = ceil($this->current_page/$this->pages_per_section);
	} 

	/**
	* Advance the page list cursor to the next page.
	* This is called automatically from the compiled template and should
	* not be called directly.
	* 
	* @return boolean false if there are no more pages.
	* @access protected 
	*/
	function next()
	{
		$this->page++;

		if(ceil($this->page/$this->pages_per_section) != $this->section)
		{
			$this->section = ceil($this->page/$this->pages_per_section);
			$this->section_has_changed = true;
		}	
		else
		{
			$this->section_has_changed = false;
		}
		
		return ($this->page <= $this->last_page);
	} 

	/**
	* Get the page number of the page being displayed in the page number list.
	* This is called automatically from the compiled template and should
	* not be called directly.
	* 
	* @return integer 
	* @access protected 
	*/
	function get_page_number()
	{
		return $this->page;
	} 

	/**
	* Is the page number of the page being displayed in the page number list
	* the current page being displayed in the browser?
	* This is called automatically from the compiled template and should
	* not be called directly.
	* 
	* @return boolean 
	* @access protected 
	*/
	function is_current_page()
	{
		return $this->page == $this->current_page;
	} 

	function is_display_page()
	{
		if ($this->section != $this->current_section)
			return false;
		else
			return true;	
	} 
	
	function has_section_changed()
	{
		if($this->section_has_changed)
			$this->page += $this->pages_per_section - 1;
			
		return $this->section_has_changed;
	}
	
	function get_current_section_begin_number()
	{
		return ($this->section - 1) * $this->pages_per_section + 1;
	}

	function get_current_section_uri()
	{
		if ($this->section > $this->current_section)
			return $this->get_page_uri(($this->section - 1) * $this->pages_per_section + 1);
		else
			return $this->get_page_uri($this->section * $this->pages_per_section);
	}

	function get_current_section_end_number()
	{
		$result = $this->section * $this->pages_per_section;
		if ($result >= $this->last_page)
			$result = $this->last_page;
		
		return $result;	
	}
	
	/**
	* The URI of the page that is being displayed in the page number list
	* This is called automatically from the compiled template and should
	* not be called directly.
	* 
	* @return string 
	* @access protected 
	*/
	function get_current_page_uri()
	{
		return $this->get_page_uri($this->page);
	} 

	/**
	* Return the URI to a specific page in the list.
	* 
	* @return string 
	* @access public 
	*/
	function get_page_uri($page)
	{
		$params = $_REQUEST;
		if ($page <= 1)
		{
			unset($params[$this->pager_variable.'_'. $this->get_server_id()]);
		} 
		else
		{
			$params[$this->pager_variable .'_'. $this->get_server_id()] = $page;
		} 

		$sep = '';
		$query = '';
		
		$flat_params = array();
		complex_array :: to_flat_array($params, $flat_params);
		
		foreach ($flat_params as $key => $value)
		{
			$query .= $sep . $key . '=' . urlencode($value);
			$sep = '&';
		} 
		if (empty($query))
		{
			return $this->base_url;
		} 
		else
		{
			return $this->base_url . '?' . $query;
		} 
	} 

	/**
	* Return the URI to the first page in the list.
	* 
	* @return string 
	* @access public 
	*/
	function get_first_page_uri()
	{
		return $this->get_page_uri(1);
	} 

	/**
	* Return the URI to the previous page in the list.
	* 
	* @return string 
	* @access public 
	*/
	function get_prev_page_uri()
	{
		return $this->get_page_uri($this->current_page - 1);
	} 

	/**
	* Return the URI to the last page in the list.
	* 
	* @return string 
	* @access public 
	*/
	function get_last_page_uri()
	{
		return $this->get_page_uri($this->last_page);
	} 

	/**
	* Return the URI to the next page in the list.
	* 
	* @return string 
	* @access public 
	*/
	function get_next_page_uri()
	{
		return $this->get_page_uri($this->current_page + 1);
	} 
} 

?>