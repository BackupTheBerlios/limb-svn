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
require_once(WACT_ROOT . '/template/template.inc.php');

class LimbPagerComponent extends Component
{
  var $total_items = 0;

  var $total_page_count;

  var $page_counter;
  var $section_counter;

  var $current_page;
  var $current_section;

  var $section_has_changed = false;

  var $pages_per_section = 10;
  var $items_per_page = 20;

  var $pager_prefix = 'page';
  var $base_url;
  var $request;

  function setPagerPrefix($prefix)
  {
    $this->pager_prefix = $prefix;
  }

  function setTotalItems($items)
  {
    $this->total_items = $items;
  }

  function setPagesPerSection($pages)
  {
    $this->pages_per_section = $pages;
  }

  function getTotalItems()
  {
    return $this->total_items;
  }

  function hasMoreThanOnePage()
  {
    return $this->total_items > $this->items_per_page;
  }

  function setItemsPerPage($items)
  {
    $this->items_per_page = $items;
  }

  function getCurrentPageBeginItemNumber()
  {
    if($this->total_items < 1)
      return 0;

    return $this->items_per_page * ($this->current_page - 1) + 1;
  }

  function getCurrentPageEndItemNumber()
  {
    $res = $this->items_per_page * $this->current_page;

    if($res > $this->total_items)
      return $this->total_items;
    else
      return $res;
  }

  function getItemsPerPage()
  {
    return $this->items_per_page;
  }

  function getPagesCount()
  {
    return $this->total_page_count;
  }

  function isFirst()
  {
    return ($this->current_page == 1);
  }

  function hasPrev()
  {
    return ($this->current_page > 1);
  }

  function hasNext()
  {
    return ($this->current_page < $this->total_page_count);
  }

  function isLast()
  {
    return ($this->current_page == $this->total_page_count);
  }

  function prepare()
  {
    $this->_initBaseUrl();

    $this->total_page_count = ceil($this->total_items / $this->items_per_page);
    if ($this->total_page_count < 1)
    {
      $this->total_page_count = 1;
    }

    $toolkit =& Limb :: toolkit();
    $request =& $toolkit->getRequest();

    $this->current_page = $request->get($this->getPagerId());

    if (empty($this->current_page))
    {
      $this->current_page = 1;
    }

    if($this->current_page > $this->total_page_count)
      $this->current_page = $this->total_page_count;

    $this->page_counter = 0;
    $this->section_counter = 1;

    $this->current_section = ceil($this->current_page / $this->pages_per_section);
  }

  function _initBaseUrl()
  {
    $toolkit =& Limb :: toolkit();
    $this->request =& $toolkit->getRequest();
    $uri =& $this->request->getUri();

    $uri->removeQueryItems();
    $this->base_url = $uri->toString();
  }

  function next()
  {
    $this->page_counter++;

    if(ceil($this->page_counter / $this->pages_per_section) != $this->section_counter)
    {
      $this->section_counter = ceil($this->page_counter/$this->pages_per_section);
      $this->section_has_changed = true;
    }
    else
    {
      $this->section_has_changed = false;
    }

    return ($this->page_counter <= $this->total_page_count);
  }

  function getPageCounter()
  {
    return $this->page_counter;
  }

  function getSectionCounter()
  {
    return $this->section_counter;
  }

  function isCurrentPage()
  {
    return $this->page_counter == $this->current_page;
  }

  function isDisplayPage()
  {
    if ($this->section_counter != $this->current_section)
      return false;
    else
      return true;
  }

  function hasSectionChanged()
  {
    if($this->section_has_changed)
      $this->page_counter += $this->pages_per_section - 1;

    return $this->section_has_changed;
  }

  function getSectionUri()
  {
    if ($this->section_counter > $this->current_section)
      return $this->getPageUri(($this->section_counter - 1) * $this->pages_per_section + 1);
    else
      return $this->getPageUri($this->section_counter * $this->pages_per_section);
  }

  function getSectionBeginPageNumber()
  {
    return ($this->section_counter - 1) * $this->pages_per_section + 1;
  }

  function getSectionEndPageNumber()
  {
    $result = $this->section_counter * $this->pages_per_section;
    if ($result >= $this->total_page_count)
      $result = $this->total_page_count;

    return $result;
  }

  function getCurrentPageUri()
  {
    return $this->getPageUri($this->page_counter);
  }

  function getCurrentPage()
  {
    return $this->current_page;
  }

  function getPagerId()
  {
    return $this->pager_prefix . '_' . $this->getServerId();
  }

  function getPageUri($page)
  {
    $params = $this->request->export();

    if ($page <= 1)
      unset($params[$this->getPagerId()]);
    else
      $params[$this->getPagerId()] = $page;

    ComplexArray :: toFlatArray($params, $flat_params = array());

    $query_items = array();
    foreach ($flat_params as $key => $value)
      $query_items[] = $key . '=' . urlencode($value);

    $query = implode('&', $query_items);

    if (empty($query))
      return $this->base_url;
    else
      return $this->base_url . '?' . $query;
  }

  function getFirstPageUri()
  {
    return $this->getPageUri(1);
  }

  function getPrevPageUri()
  {
    return $this->getPageUri($this->current_page - 1);
  }

  function getLastPageUri()
  {
    return $this->getPageUri($this->total_page_count);
  }

  function getNextPageUri()
  {
    return $this->getPageUri($this->current_page + 1);
  }
}

?>