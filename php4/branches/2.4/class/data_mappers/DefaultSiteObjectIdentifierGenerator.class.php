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

class DefaultSiteObjectIdentifierGenerator// implements SiteObjectIdentifierGenerator
{
  function generate(&$site_object)
  {
    return $site_object->getIdentifier();
  }
}

?>
