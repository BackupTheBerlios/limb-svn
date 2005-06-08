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
require_once(LIMB_DIR . '/core/orm/ObjectCollection.class.php');

class ProxyObjectCollection extends ObjectCollection
{
  var $dao;
  var $mapper;
  var $class_handle;

  function ProxyObjectCollection(&$dao, &$mapper, $class_handle)
  {
    $this->dao = &$dao;
    $this->mapper = &$mapper;
    $this->class_handle = $class_handle;
  }

  function _ensureCollection()
  {
    if(!$this->collection)
    {
      $iterator =& $this->dao->fetch();
      for($iterator->rewind();$iterator->valid();$iterator->next())
      {
        $object =& LimbHandle :: resolve($this->class_handle);
        $record =& $iterator->current();
        $this->mapper->load($record, $object);

        $this->collection[] =& $object;
      }
    }
  }


  function rewind()
  {
    $this->_ensureCollection();
    parent :: rewind();
  }

  function add(&$obj)
  {
    $this->_ensureCollection();
    parent :: add($obj);
  }
}

?>
