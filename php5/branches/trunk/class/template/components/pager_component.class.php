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
* Represents a page navigator at runtime.  The total number of items in the 
* list to be paged must be known before the navigator can be displayed.
*/
class pager_component extends component
{
	/**
	* Used while displaying a page number list to determine when a separator
	* should be shown between two page numbers
	*/
	public $show_separator;
	/**
	* Used while displaying a page number list to the page number being displayed
	*/
	public $page;	
	/**
	* The page number of the last page in the list.
	*/
	public $last_page;
	/**
	* The page number of the current page in the list.
	*/
	public $current_page = 0;

	public $current_section = 0;

	public $section = 0;

	public $pages_per_section = 10;
	
	public $section_has_changed = false;
	/**
	* number of items to display on each page of the list.
	* This is set via the items attribute of the pager:navigator tag.
	*/
	public $items = 20;
	/**
	* The total number of items in this list.
	*/
	public $total_items = 0;
	/**
	* The variable used to carry the current page in the URL.
	*/
	protected $pager_variable = 'page';
	/**
	* The Url used to display individual pages of the list.
	*/
	protected $base_url;
	/**
	* A paged dataset reference.  Used for determining the total number
	* of items the pager should navagate across.
	*/
	protected $paged_dataset;
	
	function __construct()
	{
		$this->base_url = $_SERVER['REQUEST_URI'];
		$pos = strpos($this->base_url, '?');
		if (is_integer($pos))
		{
			$this->base_url = substr($this->base_url, 0, $pos);
		} 
	} 
	
	public function set_total_items($items)
	{
		$this->total_items = $items;
	} 
	
	public function get_total_items()
	{
		return $this->total_items;
	} 

	public function has_more_than_one_page()
	{
		return $this->total_items > $this->items;
	} 

	public function set_items_per_page($items)
	{
		$this->items = $items;
	} 

	/**
	* Set the database which this pager controls.
	*/
	public function register_dataset($dataset)
	{
		$this->paged_dataset = $dataset;
	} 

	/**
	* Get the item number of the first item in the list.
	* Usually called by the paged_dataset to determine where to
	* begin query.
	*/
	public function get_starting_item()
	{
		return $this->items * ($this->current_page - 1);
	} 

	/**
	* Get the item number of the first item in the list.
	* Usually called by the paged_dataset to determine how many
	* items are on a page.
	*/
	public function get_items_per_page()
	{
		return $this->items;
	} 
	
	public function get_pages_count()
	{
	  return $this->last_page;
	}

	/**
	* Is the current page being displayed the first page in the page list?
	*/
	public function is_first()
	{
		return ($this->current_page == 1);
	} 

	/**
	* Is there a page available to display before the current page being displayed?
	*/
	public function has_prev()
	{
		return ($this->current_page > 1);
	} 

	/**
	* Is there a page available to display after the current page being displayed?
	*/
	public function has_next()
	{
		return ($this->current_page < $this->last_page);
	} 

	/**
	* Is the current page being displayed the last page in the page list?
	*/
	public function is_last()
	{
		return ($this->current_page == $this->last_page);
	} 

	/**
	* Initialize values used by this component.
	* This is called automatically from the compiled template.
	*/
	public function prepare()
	{
	  $request = Limb :: toolkit()->getRequest();
	  
		$this->current_page = $request->get($this->pager_variable .'_'. $this->get_server_id());
			
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
	*/
	public function next()
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
	*/
	public function get_page_number()
	{
		return $this->page;
	} 

	/**
	* Is the page number of the page being displayed in the page number list
	* the current page being displayed in the browser?
	* This is called automatically from the compiled template and should
	* not be called directly.
	*/
	public function is_current_page()
	{
		return $this->page == $this->current_page;
	} 

	public function is_display_page()
	{
		if ($this->section != $this->current_section)
			return false;
		else
			return true;	
	} 
	
	public function has_section_changed()
	{
		if($this->section_has_changed)
			$this->page += $this->pages_per_section - 1;
			
		return $this->section_has_changed;
	}
	
	public function get_current_section_begin_number()
	{
		return ($this->section - 1) * $this->pages_per_section + 1;
	}

	public function get_current_section_uri()
	{
		if ($this->section > $this->current_section)
			return $this->get_page_uri(($this->section - 1) * $this->pages_per_section + 1);
		else
			return $this->get_page_uri($this->section * $this->pages_per_section);
	}

	public function get_current_section_end_number()
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
	*/
	public function get_current_page_uri()
	{
		return $this->get_page_uri($this->page);
	} 

	/**
	* Return the URI to a specific page in the list.
	*/
	public function get_page_uri($page)
	{
		$params = complex_array :: array_merge($_GET, $_POST);
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
	*/
	public function get_first_page_uri()
	{
		return $this->get_page_uri(1);
	} 

	/**
	* Return the URI to the previous page in the list.
	*/
	public function get_prev_page_uri()
	{
		return $this->get_page_uri($this->current_page - 1);
	} 

	/**
	* Return the URI to the last page in the list.
	*/
	public function get_last_page_uri()
	{
		return $this->get_page_uri($this->last_page);
	} 

	/**
	* Return the URI to the next page in the list.
	*/
	public function get_next_page_uri()
	{
		return $this->get_page_uri($this->current_page + 1);
	} 
} 

?>