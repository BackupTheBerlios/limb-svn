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

if(!is_registered_resolver('db_table'))
{
  include_once(LIMB_DIR . '/class/core/file_resolvers/package_file_resolver.class.php');
  include_once(LIMB_DIR . '/class/core/file_resolvers/db_table_file_resolver.class.php');
  register_file_resolver('db_table', new db_table_file_resolver(new package_file_resolver()));
}

class db_table_factory
{
  static protected $_tables;

  protected function __construct(){}

  static function create($db_table_name)
  {
    if(isset(self :: $_tables[$db_table_name]))
      return self :: $_tables[$db_table_name];

    self :: _include_class_file($db_table_name);

    $class_name = $db_table_name . '_db_table';

    $object = new $class_name();

    self :: $_tables[$db_table_name] = $object;

    return $object;
  }

  static protected function _include_class_file($db_table_name)
  {
    if(class_exists($db_table_name . '_db_table'))
      return;

    resolve_handle($resolver =& get_file_resolver('db_table'));

    $full_path = $resolver->resolve($db_table_name);

    include_once($full_path);
  }
}
?>