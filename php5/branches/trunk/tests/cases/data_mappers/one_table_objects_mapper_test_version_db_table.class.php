<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: limb@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . '/class/db_tables/one_table_object_db_table.class.php');

class one_table_objects_mapper_test_version_db_table extends one_table_object_db_table
{   
  function _define_db_table_name()
  {
    return 'test_one_table_object';
  }
  
  function _define_columns()
  {
    return complex_array :: array_merge(
      parent :: _define_columns(),
      array(
        'annotation' => '',
        'content' => '',
        'news_date' => array('type' => 'date'),
      )
    );
  }
}

?> 