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
require_once(LIMB_DIR . '/class/core/permissions/Authenticator.interface.php');

class SimpleAuthenticator implements Authenticator
{
  const DEFAULT_USER_GROUP = 'visitors';

  public function login($params = array())
  {
    if(!isset($params['login']))
      throw new LimbException('login attribute required!');

    if(!isset($params['password']))
      throw new LimbException('password attribute required!');

    $user = Limb :: toolkit()->getUser();
    $user->logout();

    if($record = $this->_getIdentityRecord($params['login'], $params['password']))
    {
      $user->login();

      $record['login'] = $params['login'];
      $user->import($record);
    }

    $this->_determineGroups();

    if (isset($params['locale_id']) &&  Limb :: toolkit()->getLocale()->isValidLocaleId($params['locale_id']))
      $user->set('locale_id', $params['locale_id']);
  }

  protected function _determineGroups()
  {
    $user = Limb :: toolkit()->getUser();

    if ($user->isLoggedIn())
      $groups_arr = $this->_getDbGroups();
    else
      $groups_arr = $this->_getDefaultDbGroups();

    if(!$groups_arr)
      return;

    $result = array();
    foreach($groups_arr as $group_data)
      $result[$group_data['object_id']] = $group_data['identifier'];

    $user->set('groups', $result);
  }

  protected function _getDefaultDbGroups()
  {
    $db = Limb :: toolkit()->getDB();

    $sql = "SELECT
            sso.id as id,
            sso.class_id as class_id,
            sso.current_version as current_version,
            sso.modified_date as modified_date,
            sso.status as status,
            sso.created_date as created_date,
            sso.creator_id as creator_id,
            sso.locale_id as locale_id,
            tn.title as title,
            tn.identifier as identifier,
            tn.version as version,
            tn.object_id as object_id
            FROM sys_site_object as sso, user_group as tn
            WHERE sso.identifier='" . self :: DEFAULT_USER_GROUP . "'
            AND sso.id=tn.object_id
            AND sso.current_version=tn.version";

    $db->sqlExec($sql);

    return $db->getArray();
  }

  protected function _getDbGroups()
  {
    $user = Limb :: toolkit()->getUser();

    $db = Limb :: toolkit()->getDB();

    $sql = "SELECT
            sso.id as id,
            sso.class_id as class_id,
            sso.current_version as current_version,
            sso.modified_date as modified_date,
            sso.status as status,
            sso.created_date as created_date,
            sso.creator_id as creator_id,
            sso.locale_id as locale_id,
            tn.title as title,
            tn.identifier as identifier,
            tn.version as version,
            tn.object_id as object_id
            FROM sys_site_object as sso, user_group as tn, user_in_group as u_i_g
            WHERE sso.id=tn.object_id
            AND sso.current_version=tn.version
            AND u_i_g.user_id=". $user->getId() . "
            AND u_i_g.group_id=sso.id";

    $db->sqlExec($sql);

    return $db->getArray();
  }

  protected function _getIdentityRecord($login, $password)
  {
    $crypted_password = self :: getCryptedPassword($login, $password);

    $db = Limb :: toolkit()->getDB();

    $sql = "SELECT
            sso.id as id,
            sso.class_id as class_id,
            sso.current_version as current_version,
            sso.modified_date as modified_date,
            sso.status as status,
            sso.created_date as created_date,
            sso.creator_id as creator_id,
            sso.locale_id as locale_id,
            ssot.id as node_id,
            tn.version as version,
            tn.object_id as object_id,
            tn.name as name,
            tn.lastname as lastname,
            tn.password as password,
            tn.email as email,
            tn.generated_password as generated_password
            FROM
            sys_site_object_tree as ssot,
            sys_site_object as sso,
            user as tn
            WHERE sso.identifier='" . $db->escape($login) . "'
            AND tn.password='" . $db->escape($crypted_password) . "'
            AND ssot.object_id=tn.object_id
            AND sso.id=tn.object_id
            AND sso.current_version=tn.version";

    $db->sqlExec($sql);

    return $db->fetchRow();
  }

  static public function getCryptedPassword($login, $none_crypt_password)
  {
    return md5($login.$none_crypt_password);
  }

  public function logout($params = array())
  {
    Limb :: toolkit()->getUser()->logout();

    $this->_determineGroups();
  }

  static public function isUserInGroups($groups_to_check)
  {
    if (!is_array($groups_to_check))
    {
      $groups_to_check = explode(',', $groups_to_check);
      if(!is_array($groups_to_check))
        return false;
    }

    foreach	(Limb :: toolkit()->getUser()->get('groups', array()) as $group_name)
      if (in_array($group_name, $groups_to_check))
        return true;

    return false;
  }

  static public function generatePassword()
  {
    $alphabet = array(
        array('b','c','d','f','g','h','g','k','l','m','n','p','q','r','s','t','v','w','x','z',
              'B','C','D','F','G','H','G','K','L','M','N','P','Q','R','S','T','V','W','X','Z'),
        array('a','e','i','o','u','y','A','E','I','O','U','Y'),
    );

    $new_password = '';
    for($i = 0; $i < 9 ;$i++)
    {
      $j = $i%2;
      $min_value = 0;
      $max_value = count($alphabet[$j]) - 1;
      $key = rand($min_value, $max_value);
      $new_password .= $alphabet[$j][$key];
    }

    return $new_password;
  }
}

?>