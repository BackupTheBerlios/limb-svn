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
require_once(LIMB_DIR . '/core/system/objects_support.inc.php');

if(!isRegisteredResolver('object'))
{
  include_once(LIMB_DIR . '/core/file_resolvers/PackageFileResolver.class.php');
  include_once(LIMB_DIR . '/core/file_resolvers/ObjectFileResolver.class.php');
  registerFileResolver('object', new ObjectFileResolver(new PackageFileResolver()));
}

class ObjectFactory
{
  function create($class_name, $args = array())
  {
    ObjectFactory :: _includeClassFile($class_name);
    $handle = new Handle($class_name, $args);

    return Handle :: resolve($handle);
  }

  function _includeClassFile($class_name)
  {
    if(class_exists($class_name))
      return;

    $resolver =& Handle :: resolve(getFileResolver('object'));
    $full_path = $resolver->resolve($class_name);

    include_once($full_path);
  }
}
?>