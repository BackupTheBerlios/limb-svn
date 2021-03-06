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

if(!isRegisteredResolver('finder'))
{
  include_once(LIMB_DIR . '/class/core/file_resolvers/PackageFileResolver.class.php');
  include_once(LIMB_DIR . '/class/core/file_resolvers/FinderFileResolver.class.php');
  registerFileResolver('finder', new FinderFileResolver(new PackageFileResolver()));
}

abstract class FinderFactory
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

    resolveHandle($resolver =& getFileResolver('finder'));

    $full_path = $resolver->resolve($class_name);

    include_once($full_path);
  }
}
?>