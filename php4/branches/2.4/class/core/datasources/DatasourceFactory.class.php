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
require_once(LIMB_DIR . '/class/lib/error/Debug.class.php');

if(!isRegisteredResolver('datasource'))
{
  include_once(LIMB_DIR . '/class/core/file_resolvers/PackageFileResolver.class.php');
  include_once(LIMB_DIR . '/class/core/file_resolvers/DatasourceFileResolver.class.php');
  registerFileResolver('datasource', new DatasourceFileResolver(new PackageFileResolver()));
}

class DatasourceFactory
{
  static protected $datasources = array();

  static public function create($class_path)
  {
    $class_name = end(explode('/', $class_path));

    if(isset(self :: $datasources[$class_name]))
      return self :: $datasources[$class_name];

    if(!class_exists($class_name))
    {
      resolveHandle($resolver =& getFileResolver('datasource'));

      if(!$full_path = $resolver->resolve($class_path))
        return null;

      include_once($full_path);
    }

    $datasource = new $class_name();

    self :: $datasources[$class_name] = $datasource;

    return $datasource;
  }
}


?>