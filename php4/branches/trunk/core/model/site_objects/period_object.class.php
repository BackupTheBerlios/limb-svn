<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . 'core/model/site_objects/content_object.class.php');

class period_object extends content_object
{
  function _define_class_properties()
  {
    return complex_array :: array_merge(
          parent :: _define_class_properties(),
          array(
            'abstract_class' => true,
            'db_table_name' => 'empty',
          )
    );
  }

  function & fetch_valid_period_by_ids($ids_array, $params=array(), $sql_params=array())
  {
    $current_date = date('Y-m-d', time());

    $sql_params['conditions'][] = " AND tn.start_date <= '{$current_date}' AND tn.finish_date >= '{$current_date}' ";

    $result =& parent :: fetch_by_ids($ids_array, $params, $sql_params);
    return $result;
  }

  function fetch_valid_period_by_ids_count($ids_array, $params=array(), $sql_params=array())
  {
    $current_date = date('Y-m-d', time());

    $sql_params['conditions'][] = " AND tn.start_date <= '{$current_date}' AND tn.finish_date >= '{$current_date}' ";
    $result = parent :: fetch_by_ids_count($ids_array, $params, $sql_params);
    return $result;
  }

  function & fetch_valid_period($params=array(), $sql_params=array())
  {
    $current_date = date('Y-m-d', time());

    $sql_params['conditions'][] = ' AND sso.locale_id = "'. CONTENT_LOCALE_ID . '"';
    $sql_params['conditions'][] = " AND tn.start_date <= '{$current_date}' AND tn.finish_date >= '{$current_date}' ";
    $result =& parent :: fetch_accessible($params, $sql_params);

    return $result;
  }

  function fetch_valid_period_count($params=array(), $sql_params=array())
  {
    $current_date = date('Y-m-d', time());

    $sql_params['conditions'][] = ' AND sso.locale_id = "'. CONTENT_LOCALE_ID . '"';
    $sql_params['conditions'][] = " AND tn.start_date <= '{$current_date}' AND tn.finish_date >= '{$current_date}' ";
    $result = parent :: fetch_accessible_count($params, $sql_params);
    return $result;
  }

  function & fetch_calendar_period($params=array(), $sql_params=array())
  {
    $start_date = $params['start_date'];
    $finish_date = $params['finish_date'];

    $sql_params['conditions'][] = ' AND sso.locale_id = "'. CONTENT_LOCALE_ID . '"';
    $sql_params['conditions'][] = " AND tn.start_date >= '{$start_date}'
                                  AND tn.start_date <= '{$finish_date}' ";

    $result =& parent :: fetch_accessible($params, $sql_params);
    return $result;
  }

  function fetch_calendar_period_count($params=array(), $sql_params=array())
  {
    $start_date = $params['start_date'];
    $finish_date = $params['finish_date'];

    $sql_params['conditions'][] = ' AND sso.locale_id = "'. CONTENT_LOCALE_ID . '"';
    $sql_params['conditions'][] = " AND sso.id=tn.object_id
                                  AND sso.current_version=tn.version
                                  AND tn.start_date >= '{$start_date}'
                                  AND tn.start_date <= '{$finish_date}' ";

    return parent :: fetch_accessible_count($params, $sql_params);
  }

  function & fetch_calendar_period_by_ids($ids_array, $params=array(), $sql_params=array())
  {
    $start_date = $params['start_date'];
    $finish_date = $params['finish_date'];

    $sql_params['conditions'][] = ' AND sso.locale_id = "'. CONTENT_LOCALE_ID . '"';
    $sql_params['conditions'][] = " AND tn.start_date >= '{$start_date}'
                                  AND tn.start_date <= '{$finish_date}' ";

    $result =& parent :: fetch_accessible_by_ids($ids_array, $params, $sql_params);
    return $result;
  }

  function fetch_calendar_period_by_ids_count($ids_array, $params=array(), $sql_params=array())
  {
    $start_date = $params['start_date'];
    $finish_date = $params['finish_date'];

    $sql_params['conditions'][] = ' AND sso.locale_id = "'. CONTENT_LOCALE_ID . '"';
    $sql_params['conditions'][] = " AND sso.id=tn.object_id
                                  AND sso.current_version=tn.version
                                  AND tn.start_date >= '{$start_date}'
                                  AND tn.start_date <= '{$finish_date}' ";

    return parent :: fetch_accessible_by_ids_count($ids_array, $params, $sql_params);
  }

  function & fetch_publish_period($params=array(), $sql_params=array())
  {
    $start_date = $params['start_date'];
    $finish_date = $params['finish_date'];

    $sql_params['conditions'][] =
      " AND ((tn.start_date >= '{$start_date}' AND tn.start_date <= '{$finish_date}')
          OR (tn.finish_date >= '{$start_date}' AND tn.finish_date <= '{$finish_date}')) ";

    $result =& parent :: fetch_accessible($params, $sql_params);
    return $result;
  }

  function fetch_publish_period_count($params=array(), $sql_params=array())
  {
    $start_date = $params['start_date'];
    $finish_date = $params['finish_date'];

    $sql_params['conditions'][] = ' AND sso.locale_id = "'. CONTENT_LOCALE_ID . '"';
    $sql_params['conditions'][] = " AND sso.id=tn.object_id AND sso.current_version=tn.version";

    $sql_params['conditions'][] =
      " AND ((tn.start_date >= '{$start_date}' AND tn.start_date <= '{$finish_date}')
          OR (tn.finish_date >= '{$start_date}' AND tn.finish_date <= '{$finish_date}')) ";

    return parent :: fetch_accessible_count($params, $sql_params);
  }
}

?>