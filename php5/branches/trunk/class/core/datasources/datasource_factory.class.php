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
require_once(LIMB_DIR . '/class/lib/error/debug.class.php');

if(!is_registered_resolver('datasource'))
{
  include_once(LIMB_DIR . '/class/core/file_resolvers/package_file_resolver.class.php');
  include_once(LIMB_DIR . '/class/core/file_resolvers/datasource_file_resolver.class.php');
  register_file_resolver('datasource', new datasource_file_resolver(new package_file_resolver()));
}

class datasource_factory
{
  static protected $datasources = array();

  static public function create($class_path)
  {
    $class_name = end(explode('/', $class_path));

    if(isset(self :: $datasources[$class_name]))
      return self :: $datasources[$class_name];

    if(!class_exists($class_name))
    {
      resolve_handle($resolver =& get_file_resolver('datasource'));

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