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

if(!isRegisteredResolver('mapper'))
{
  include_once(LIMB_DIR . '/class/core/file_resolvers/PackageFileResolver.class.php');
  include_once(LIMB_DIR . '/class/core/file_resolvers/DataMapperFileResolver.class.php');
  registerFileResolver('mapper', new DataMapperFileResolver(new PackageFileResolver()));
}

class DataMapperFactory
{
  function create($class_name)
  {
    DataMapperFactory :: _includeClassFile($class_name);

    return new $class_name();
  }

  function _includeClassFile($class_name)
  {
    if(class_exists($class_name))
      return;

    resolveHandle($resolver =& getFileResolver('mapper'));

    $full_path = $resolver->resolve($class_name);

    include_once($full_path);
  }
}
?>