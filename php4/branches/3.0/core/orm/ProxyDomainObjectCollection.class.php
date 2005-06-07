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
require_once(WACT_ROOT . '/iterator/iterator.inc.php');

class ProxyDomainObjectCollection extends IteratorDecorator
{
  var $dao;
  var $mapper;
  var $class_handle;
  var $cached_array;

  function ProxyDomainObjectCollection(&$dao, &$mapper, $class_handle)
  {
    $this->dao = &$dao;
    $this->mapper = &$mapper;
    $this->class_handle = $class_handle;
    $this->cached_array = array();
  }

  function _ensureCachedArray()
  {
    if(!$this->cached_array)
    {
      $iterator =& $this->dao->fetch();
      for($iterator->rewind();$iterator->valid();$iterator->next())
      {
        $object =& LimbHandle :: resolve($this->class_handle);
        $record =& $iterator->current();
        $this->mapper->load($record, $object);

        $this->cached_array[] = $object;
      }
    }
  }

  function & current()
  {
    $record =& parent :: current();
    return $record->export();
  }

  function rewind()
  {
    $this->_ensureCachedArray();
    $this->iterator = new ArrayDataSet($this->cached_array);
    parent :: rewind();
  }

  function add(&$obj)
  {
    $this->_ensureCachedArray();
    $this->cached_array[] =& $obj;
  }
}

?>
