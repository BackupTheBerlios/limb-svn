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
require_once(LIMB_DIR . '/class/core/object.class.php');

class domain_object extends object
{
  protected $clean_hash;

  function __construct()
  {
    parent :: __construct();

    $this->mark_clean();
  }

  public function get_id()
  {
    return (int)$this->get('id');
  }

  public function set_id($id)
  {
    $this->set('id', (int)$id);
  }

  public function is_dirty()
  {
    return ($this->clean_hash != $this->dataspace->get_hash());
  }

  public function mark_clean()
  {
    $this->clean_hash = $this->dataspace->get_hash();
  }

  public function import($values)
  {
    parent :: import($values);

    $this->mark_clean();
  }
}

?>