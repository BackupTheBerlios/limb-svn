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
require_once(LIMB_DIR . '/core/util/ComplexArray.class.php');
require_once(WACT_ROOT . '/template/template.inc.php');

class LimbPagerComponent extends Component
{
  var $total_items = 0;

  var $total_page_count;

  var $page_counter;

  var $displayed_page;

  var $pages_per_section = 10;
  var $items_per_page = 20;

  var $pager_prefix = 'page';
  var $base_url;
  var $paged_dataset = null;

  function setPagerPrefix($prefix)
  {
    $this->pager_prefix = $prefix;
  }

  function setTotalItems($items)
  {
    $this->total_items = $items;
  }

  function prepare()
  {
    if ($this->paged_dataset)
      $this->setTotalItems($this->paged_dataset->getTotalRowCount());

    $this->_initBaseUrl();

    $this->total_page_count = ceil($this->total_items / $this->items_per_page);

    if ($this->total_page_count < 1)
      $this->total_page_count = 1;

    $toolkit =& Limb :: toolkit();
    $request =& $toolkit->getRequest();

    $this->displayed_page = $request->get($this->getPagerId());

    if (empty($this->displayed_page))
      $this->displayed_page = 1;

    if($this->displayed_page > $this->total_page_count)
      $this->displayed_page = $this->total_page_count;

    $this->page_counter = 1;
  }

  function setPagesPerSection($pages)
  {
    $this->pages_per_section = $pages;
  }

  function getPagesPerSection()
  {
    return $this->pages_per_section;
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

  //implementing WACT pager interface
  function getStartingItem()
  {
    return $this->getDisplayedPageBeginItem() - 1;
  }

  function setPagedDataSet(&$dataset)
  {
    $this->paged_dataset =& $dataset;

    $this->prepare();
  }

  function getDisplayedPageBeginItem()
  {
    if($this->total_items < 1)
      return 0;

    return $this->items_per_page * ($this->displayed_page - 1) + 1;
  }

  function getDisplayedPageEndItem()
  {
    $res = $this->items_per_page * $this->displayed_page;

    if($res > $this->total_items)
      return $this->total_items;
    else
      return $res;
  }

  function getItemsPerPage()
  {
    return $this->items_per_page;
  }

  function getTotalPages()
  {
    return $this->total_page_count;
  }

  function isFirst()
  {
    return ($this->displayed_page == 1);
  }

  function hasPrev()
  {
    return ($this->displayed_page > 1);
  }

  function hasNext()
  {
    return ($this->displayed_page < $this->total_page_count);
  }

  function isLast()
  {
    return ($this->displayed_page == $this->total_page_count);
  }

  function _initBaseUrl()
  {
    $toolkit =& Limb :: toolkit();
    $request =& $toolkit->getRequest();
    $uri =& $request->getUri();

    $uri->removeQueryItems();
    $this->base_url = $uri->toString();
  }

  function nextPage()
  {
    $this->page_counter++;

    return $this->isValid();
  }

  function isValid()
  {
    return ($this->page_counter <= $this->total_page_count);
  }

  function nextSection()
  {
    $this->page_counter += $this->pages_per_section;

    return $this->isValid();
  }

  function getPage()
  {
    return $this->page_counter;
  }

  function isDisplayedPage()
  {
    return $this->page_counter == $this->displayed_page;
  }

  function isDisplayedSection()
  {
    if($this->getSection() == $this->getDisplayedSection())
      return true;
    else
      return false;
  }

  function getSection()
  {
    return ceil($this->page_counter / $this->pages_per_section);
  }

  function getDisplayedSection()
  {
    return ceil($this->displayed_page / $this->pages_per_section);
  }

  function getSectionUri()
  {
    $section = $this->getSection();

    if ($section > $this->getDisplayedSection())
      return $this->getPageUri(($section - 1) * $this->pages_per_section + 1);
    else
      return $this->getPageUri($section  * $this->pages_per_section);
  }

  function getSectionBeginPage()
  {
    $result = ($this->getSection() - 1) * $this->pages_per_section + 1;

    if($result < 0)
      return 0;
    else
      return $result;
  }

  function getSectionEndPage()
  {
    $result = $this->getSection() * $this->pages_per_section;

    if ($result >= $this->total_page_count)
      $result = $this->total_page_count;

    return $result;
  }

  function getDisplayedPageUri()
  {
    return $this->getPageUri($this->displayed_page);
  }

  function getDisplayedPage()
  {
    return $this->displayed_page;
  }

  function getPagerId()
  {
    return $this->pager_prefix . '_' . $this->getServerId();
  }

  function getPageUri($page = null)
  {
    if ($page == null)
      $page = $this->page_counter;

    $toolkit =& Limb :: toolkit();
    $request =& $toolkit->getRequest();

    $params = $request->export();

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

  function getLastPageUri()
  {
    return $this->getPageUri($this->total_page_count);
  }
}

?>