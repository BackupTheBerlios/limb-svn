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
require_once(LIMB_DIR . '/core/site_objects/SiteObject.class.php');

class VersionedSiteObject extends SiteObject
{
  var $new_version = false;

  function increaseVersion()
  {
    $this->new_version = true;
  }

  function isNewVersion()
  {
    return $this->new_version;
  }
}

?>
