<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: create_catalog_object_action.class.php 786 2004-10-12 14:24:43Z pachanga $
*
***********************************************************************************/
require_once(LIMB_DIR . '/class/commands/CreateSiteObjectCommand.class.php');

class CreateCatalogObjectCommands extends CreateSiteObjectCommand
{
  function _defineSiteObjectClassName()
  {
    return 'catalog_object';
  }
}

?>