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
require_once(LIMB_DIR . '/class/core/site_objects/site_object.class.php');

class versioned_site_object extends site_object
{
  protected $new_version = false;

  public function increase_version()
  {
    $this->new_version = true;
  }

  public function is_new_version()
  {
    return $this->new_version;
  }
}

?>
