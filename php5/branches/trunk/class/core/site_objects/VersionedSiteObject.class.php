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
require_once(LIMB_DIR . '/class/core/site_objects/SiteObject.class.php');

class VersionedSiteObject extends SiteObject
{
  protected $new_version = false;

  public function increaseVersion()
  {
    $this->new_version = true;
  }

  public function isNewVersion()
  {
    return $this->new_version;
  }
}

?>
