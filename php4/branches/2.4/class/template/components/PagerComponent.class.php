<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
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
class PagerComponent extends Component
{
  /**
  * Used while displaying a page number list to determine when a separator
  * should be shown between two page numbers
  */
  var $show_separator;
  /**
  * Used while displaying a page number list to the page number being displayed
  */
  var $page;
  /**
  * The page number of the last page in the list.
  */
  var $last_page;
  /**
  * The page number of the current page in the list.
  */
  var $current_page = 0;

  var $current_section = 0;

  var $section = 0;

  var $pages_per_section = 10;

  var $section_has_changed = false;
  /**
  * number of items to display on each page of the list.
  * This is set via the items attribute of the pager:navigator tag.
  */
  var $items = 20;
  /**
  * The total number of items in this list.
  */
  var $total_items = 0;
  /**
  * The variable used to carry the current page in the URL.
  */
  var $pager_variable = 'page';
  /**
  * The Url used to display individual pages of the list.
  */
  var $base_url;
  /**
  * A paged dataset reference.  Used for determining the total number
  * of items the pager should navagate across.
  */
  var $paged_dataset;

  function PagerComponent()
  {
    $this->base_url = $_SERVER['REQUEST_URI'];
    $pos = strpos($this->base_url, '?');
    if (is_integer($pos))
    {
      $this->base_url = substr($this->base_url, 0, $pos);
    }
  }

  function setTotalItems($items)
  {
    $this->total_items = $items;
  }

  function getTotalItems()
  {
    return $this->total_items;
  }

  function hasMoreThanOnePage()
  {
    return $this->total_items > $this->items;
  }

  function setItemsPerPage($items)
  {
    $this->items = $items;
  }

  /**
  * Set the database which this pager controls.
  */
  function registerDataset($dataset)
  {
    $this->paged_dataset = $dataset;
  }

  /**
  * Get the item number of the first item in the list.
  * Usually called by the paged_dataset to determine where to
  * begin query.
  */
  function getStartingItem()
  {
    return $this->items * ($this->current_page - 1);
  }

  /**
  * Get the item number of the first item in the list.
  * Usually called by the paged_dataset to determine how many
  * items are on a page.
  */
  function getItemsPerPage()
  {
    return $this->items;
  }

  function getPagesCount()
  {
    return $this->last_page;
  }

  /**
  * Is the current page being displayed the first page in the page list?
  */
  function isFirst()
  {
    return ($this->current_page == 1);
  }

  /**
  * Is there a page available to display before the current page being displayed?
  */
  function hasPrev()
  {
    return ($this->current_page > 1);
  }

  /**
  * Is there a page available to display after the current page being displayed?
  */
  function hasNext()
  {
    return ($this->current_page < $this->last_page);
  }

  /**
  * Is the current page being displayed the last page in the page list?
  */
  function isLast()
  {
    return ($this->current_page == $this->last_page);
  }

  /**
  * Initialize values used by this component.
  * This is called automatically from the compiled template.
  */
  function prepare()
  {
    $toolkit =& Limb :: toolkit();
    $request =& $toolkit->getRequest();

    $this->current_page = $request->get($this->pager_variable .'_'. $this->getServerId());

    if (empty($this->current_page))
    {
      $this->current_page = 1;
    }

    if (isset($this->paged_dataset))
    {
      $this->setTotalItems($this->paged_dataset->getTotalRowCount());
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
  */
  function getPageNumber()
  {
    return $this->page;
  }

  /**
  * Is the page number of the page being displayed in the page number list
  * the current page being displayed in the browser?
  * This is called automatically from the compiled template and should
  * not be called directly.
  */
  function isCurrentPage()
  {
    return $this->page == $this->current_page;
  }

  function isDisplayPage()
  {
    if ($this->section != $this->current_section)
      return false;
    else
      return true;
  }

  function hasSectionChanged()
  {
    if($this->section_has_changed)
      $this->page += $this->pages_per_section - 1;

    return $this->section_has_changed;
  }

  function getCurrentSectionBeginNumber()
  {
    return ($this->section - 1) * $this->pages_per_section + 1;
  }

  function getCurrentSectionUri()
  {
    if ($this->section > $this->current_section)
      return $this->getPageUri(($this->section - 1) * $this->pages_per_section + 1);
    else
      return $this->getPageUri($this->section * $this->pages_per_section);
  }

  function getCurrentSectionEndNumber()
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
  function getCurrentPageUri()
  {
    return $this->getPageUri($this->page);
  }

  /**
  * Return the URI to a specific page in the list.
  */
  function getPageUri($page)
  {
    $params = ComplexArray :: array_merge($_GET, $_POST);
    if ($page <= 1)
    {
    unset($params[$this->pager_variable.'_'. $this->getServerId()]);
    }
    else
    {
      $params[$this->pager_variable .'_'. $this->getServerId()] = $page;
    }

    $sep = '';
    $query = '';

    $flat_params = array();
    ComplexArray :: toFlatArray($params, $flat_params);

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
  function getFirstPageUri()
  {
    return $this->getPageUri(1);
  }

  /**
  * Return the URI to the previous page in the list.
  */
  function getPrevPageUri()
  {
    return $this->getPageUri($this->current_page - 1);
  }

  /**
  * Return the URI to the last page in the list.
  */
  function getLastPageUri()
  {
    return $this->getPageUri($this->last_page);
  }

  /**
  * Return the URI to the next page in the list.
  */
  function getNextPageUri()
  {
    return $this->getPageUri($this->current_page + 1);
  }
}

?>