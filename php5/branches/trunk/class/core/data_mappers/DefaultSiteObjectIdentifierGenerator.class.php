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
require_once(dirname(__FILE__) . '/site_object_identifier_generator.interface.php');

class DefaultSiteObjectIdentifierGenerator implements SiteObjectIdentifierGenerator
{
  function generate($site_object)
  {
    return $site_object->get_identifier();
  }
}

?>
