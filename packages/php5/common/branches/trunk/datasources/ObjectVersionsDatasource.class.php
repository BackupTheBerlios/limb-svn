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
require_once(LIMB_DIR . '/class/datasources/Datasource.interface.php');

class ObjectVersionsDatasource implements Datasource
{
  public function getDataset(&$counter, $params=array())
  {
    $request = Limb :: toolkit()->getRequest();

    $datasource = Limb :: toolkit()->getDatasource('RequestedObjectDatasource');
    $datasource->setRequest($request);

    $object_data = $datasource->fetch();

    if (!count($object_data))
      return new ArrayDataset(array());

    $db_table	= Limb :: toolkit()->createDBTable('SysObjectVersion');

    $arr = $db_table->getList('object_id='. $object_data['id'], 'version DESC');

    $result = array();

    $users = $this->_getUsers();

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

    return new ArrayDataset($result);
  }

  protected function _getUsers()
  {
    $datasource = Limb :: toolkit()->getDatasource('SiteObjectsBranchDatasource');
    $datasource->setPath('/root/users');
    $datasource->setSiteObjectClassName('user_object');
    $datasource->setRestrictByClass();

    return $datasource->fetch();
  }
}


?>