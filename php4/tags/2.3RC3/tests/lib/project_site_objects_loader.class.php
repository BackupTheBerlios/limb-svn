<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . '/tests/lib/site_objects_loader.class.php');

class project_site_objects_loader extends site_objects_loader
{
  function get_classes_list()
  {
    $project_db = str_replace('_tests', '', DB_NAME);

    $db =& db_factory :: instance();

    $db->select_db($project_db);

    $db->sql_select('sys_class', '*', 'class_name != "site_object"');

    $list = $db->get_array();

    $db->select_db(DB_NAME);

    return complex_array :: get_column_values('class_name', $list);
  }
}

?>