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
require_once(LIMB_DIR . '/core/datasource/object_detail_info_datasource.class.php');

class object_versions_datasource extends object_detail_info_datasource
{
  function & get_dataset(&$counter, $params=array())
  {
    $object_data = $this->_fetch_object_data();

    if (!count($object_data))
      return new array_dataset(array());

    $db_table	=  & db_table_factory :: instance('sys_object_version');

    $arr = $db_table->get_list('object_id='. $object_data['id'], 'version DESC');

    $result = array();

    $users =& fetch_sub_branch('/root/admin/users', 'user_object', $counter);

    foreach($arr as $data)
    {
      $record = $data;
      $user = '';

      if (count($users))
        foreach($users as $user_data)
        {
          if ($user_data['id'] == $data['creator_id'])
          {
            $user = $user_data;
            break;
          }
        }

      if ($user)
      {
        $record['creator_identifier'] = $user['identifier'];
        $record['creator_email'] = $user['email'];
        $record['creator_name'] = $user['name'];
        $record['creator_lastname'] = isset($user['lastname']) ? $user['lastname'] : '';
      }
      $result[]	= $record;
    }

    return new array_dataset($result);
  }

}


?>