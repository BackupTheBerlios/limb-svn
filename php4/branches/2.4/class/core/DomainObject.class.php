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
  protected $clean_hash;

  function __construct()
  {
    parent :: __construct();

    $this->markClean();
  }

  public function getId()
  {
    return (int)$this->get('id');
  }

  public function setId($id)
  {
    $this->set('id', (int)$id);
  }

  public function isDirty()
  {
    return ($this->clean_hash != $this->dataspace->getHash());
  }

  public function markClean()
  {
    $this->clean_hash = $this->dataspace->getHash();
  }

  public function import($values)
  {
    parent :: import($values);

    $this->markClean();
  }
}

?>