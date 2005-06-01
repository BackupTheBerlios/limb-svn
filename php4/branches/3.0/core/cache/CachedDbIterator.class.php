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
require_once(LIMB_DIR . '/core/db/IteratorDbDecorator.class.php');

define('RS_CACHE_COMMON_GROUP', 'rs');
define('RS_TOTAL_CACHE_COMMON_GROUP', 'rs_total');

class CachedDbIterator extends IteratorDbDecorator
{
  var $cached_rs = null;
  var $cached_total_row_count = null;
  var $is_cached_rs = false;
  var $is_cached_total = false;
  var $cache = null;
  var $cache_key_for_rs = null;
  var $cache_key_for_total = null;

  function CachedDbIterator(&$iterator)
  {
    parent :: IteratorDbDecorator($iterator);

    $toolkit =& Limb :: toolkit();
    $this->cache =& $toolkit->getCache();
    $this->cache_key_for_rs = $iterator;
    $this->cache_key_for_total = $iterator;
  }

  function paginate(&$pager)
  {
    parent :: paginate($pager);
    $this->cache_key_for_rs = $this->iterator;
  }

  function rewind()
  {
    $this->_checkRsCache();

    if($this->is_cached_rs)
    {
      $this->cached_rs->rewind();
      return;
    }

    $clean_iterator = $this->iterator;
    $tmp_cache = array();

    for($this->iterator->rewind();$this->iterator->valid();$this->iterator->next())
    {
      $record = $this->iterator->current();
      $tmp_cache[] = $record->export();
    }

    $this->is_cached_rs = true;
    $this->cached_rs = new PagedArrayDataSet($tmp_cache);
    $clean_cached_rs = $this->cached_rs;

    $this->cache->put($clean_iterator, $clean_cached_rs, RS_CACHE_COMMON_GROUP);

    $this->cached_rs->rewind();
  }

  function next()
  {
    if($this->is_cached_rs)
      return $this->cached_rs->next();
    else
      return parent :: next();
  }

  function valid()
  {
    if($this->is_cached_rs)
      return $this->cached_rs->valid();
    else
      return parent :: valid();
  }

  function current()
  {
    if($this->is_cached_rs)
      return $this->cached_rs->current();
    else
      return parent :: current();
  }

  function key()
  {
    if($this->is_cached_rs)
      return $this->cached_rs->key();
    else
      return parent :: key();
  }

  function getRowCount()
  {
    if($this->is_cached_rs)
      return $this->cached_rs->getRowCount();
    else
      return parent :: getRowCount();
  }

  function getTotalRowCount()
  {
    $this->_checkRsCacheTotal();

    if($this->is_cached_total)
      return $this->cached_total_row_count;

    $this->cached_total_row_count = parent :: getTotalRowCount();

    $this->cache->put($this->cache_key_for_rs,
                         $this->cached_total_row_count,
                         RS_TOTAL_CACHE_COMMON_GROUP);

    return $this->cached_total_row_count;
  }

  function _checkRsCache()
  {
    if($this->cache->assign($cached_rs, $this->cache_key_for_rs, RS_CACHE_COMMON_GROUP))
    {
      $this->cached_rs =& $cached_rs;
      $this->is_cached_rs = true;
    }
    else
    {
      $this->is_cached_rs = false;
    }
  }

  function _checkRsCacheTotal()
  {
    if($this->cache->assign($count, $this->cache_key_for_total, RS_TOTAL_CACHE_COMMON_GROUP))
    {
      $this->cached_total_row_count = $count;
      $this->is_cached_total = true;
    }
    else
    {
      $this->is_cached_total = false;
    }
  }
}

?>
