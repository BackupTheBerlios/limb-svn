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
require_once(LIMB_DIR . '/class/core/Object.class.php');

class DomainObject extends Object
{
  var $clean_hash;

  function DomainObject()
  {
    parent :: Object();

    $this->markClean();
  }

  function getId()
  {
    return (int)$this->get('id');
  }

  function setId($id)
  {
    $this->set('id', (int)$id);
  }

  function isDirty()
  {
    return ($this->clean_hash != $this->dataspace->getHash());
  }

  function markClean()
  {
    $this->clean_hash = $this->dataspace->getHash();
  }

  function import($values)
  {
    parent :: import($values);

    $this->markClean();
  }
}

?>