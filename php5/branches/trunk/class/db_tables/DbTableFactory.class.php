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

if(!isRegisteredResolver('db_table'))
{
  include_once(LIMB_DIR . '/class/core/file_resolvers/PackageFileResolver.class.php');
  include_once(LIMB_DIR . '/class/core/file_resolvers/DbTableFileResolver.class.php');
  registerFileResolver('db_table', new DbTableFileResolver(new PackageFileResolver()));
}

class DbTableFactory
{
  static protected $_tables;

  protected function __construct(){}

  static function create($db_table_name)
  {
    if(isset(self :: $_tables[$db_table_name]))
      return self :: $_tables[$db_table_name];

    self :: _includeClassFile($db_table_name);

    $class_name = $db_table_name . 'DbTable';

    $object = new $class_name();

    self :: $_tables[$db_table_name] = $object;

    return $object;
  }

  static protected function _includeClassFile($db_table_name)
  {
    if(class_exists($db_table_name . 'DbTable'))
      return;

    resolveHandle($resolver =& getFileResolver('db_table'));

    $full_path = $resolver->resolve($db_table_name);

    include_once($full_path);
  }
}
?>