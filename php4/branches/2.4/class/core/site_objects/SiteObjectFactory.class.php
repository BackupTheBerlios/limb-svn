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
require_once(LIMB_DIR . '/class/lib/system/objects_support.inc.php');

if(!isRegisteredResolver('site_object'))
{
  include_once(LIMB_DIR . '/class/core/file_resolvers/PackageFileResolver.class.php');
  include_once(LIMB_DIR . '/class/core/file_resolvers/SiteObjectFileResolver.class.php');
  registerFileResolver('site_object', new SiteObjectFileResolver(new PackageFileResolver()));
}

abstract class SiteObjectFactory
{
  static function create($class_name)
  {
    self :: _includeClassFile($class_name);

    return new $class_name();
  }

  static protected function _includeClassFile($class_name)
  {
    if(class_exists($class_name))
      return;

    resolveHandle($resolver =& getFileResolver('site_object'));

    $full_path = $resolver->resolve($class_name);

    include_once($full_path);
  }
}
?>