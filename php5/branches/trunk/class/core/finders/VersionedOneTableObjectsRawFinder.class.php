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
require_once(LIMB_DIR . '/class/core/finders/one_table_objects_raw_finder.class.php');

abstract class versioned_one_table_objects_raw_finder extends one_table_objects_raw_finder
{
  public function find($params=array(), $sql_params=array())
  {
    $sql_params['conditions'][] = ' AND sso.current_version=tn.version';

    return $this->_do_parent_find($params, $sql_params);
  }

  public function find_by_version($object_id, $version)
  {
    $sql_params = array();
    $sql_params['conditions'][] = ' AND sso.id=' . $object_id;
    $sql_params['conditions'][] = ' AND tn.version=' . $version;

    return $this->_do_parent_find(array(), $sql_params);
  }

  //for mocking
  protected function _do_parent_find($params, $sql_params)
  {
    return parent :: find($params, $sql_params);
  }

  protected function _do_parent_find_count($sql_params)
  {
    return parent :: find_count($sql_params);
  }

  public function find_count($sql_params=array())
  {
    $sql_params['conditions'][] = ' AND sso.current_version=tn.version';

    return $this->_do_parent_find_count($sql_params);
  }
}

?>