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
require_once(LIMB_DIR . '/class/core/site_objects/ContentObject.class.php');
require_once(dirname(__FILE__) . '/../SimpleAuthenticator.class.php');

class UserObject extends ContentObject
{
  public function create($is_root = false)
  {
    $crypted_password = SimpleAuthenticator :: getCryptedPassword($this->getIdentifier(), $this->get('password'));
    $this->set('password', $crypted_password);
    return parent :: create($is_root);
  }

  public function getMembership($user_id)
  {
    $db_table	= Limb :: toolkit()->createDBTable('UserInGroup');
    $groups = $db_table->getList('user_id='. $user_id, '', 'group_id');
    return $groups;
  }

  public function saveMembership($user_id, $membership)
  {
    $db_table	= Limb :: toolkit()->createDBTable('UserInGroup');
    $db_table->delete('user_id='. $user_id);

    foreach($membership as $group_id => $is_set)
    {
      if (!$is_set)
        continue;

      $data = array();
      $data['id'] = null;
      $data['user_id'] = (int)$user_id;
      $data['group_id'] = (int)$group_id;
      $db_table->insert($data);
    }
  }

  public function changePassword()
  {
    if(!$user_id = $this->getId())
      throw new LimbException('user id not set');

    if(!$identifier = $this->getIdentifier())
      throw new LimbException('user identifier not set');

    $this->set(
      'password',
      SimpleAuthenticator :: getCryptedPassword(
        $identifier,
        $this->get('password')
      )
    );

    $this->update(false);
  }

  public function validatePassword($password)
  {
    $user = Limb :: toolkit()->getUser();

    if(!$user->isLoggedIn() ||  !$node_id = $user->get('node_id'))
      return false;

    $password = SimpleAuthenticator :: getCryptedPassword($user->getLogin(), $password);

    if($user->get('password') !== $password)
      return false;
    else
      return true;
  }

  public function changeOwnPassword($password)
  {
    $user = Limb :: toolkit()->getUser();

    $node_id = $user->get('node_id');

    $data['password'] = SimpleAuthenticator :: getCryptedPassword($user->getLogin(),	$password);

    $user_db_table = Limb :: toolkit()->createDBTable('User');

    $this->set('password', $data['password']);

    $user_db_table->update($data, 'identifier="'. $user->getLogin() . '"');
    return $this->login($user->getLogin(), $password);
  }

  public function generatePassword($email, &$new_non_crypted_password)
  {
    if(!$user_data = $this->getUserByEmail($email))
      return false;

    $this->merge($user_data);

    $new_non_crypted_password = User :: generatePassword();
    $crypted_password = SimpleAuthenticator :: getCryptedPassword($user_data['identifier'], $new_non_crypted_password);
    $this->set('generated_password', $crypted_password);

    $this->update(false);
    return true;
  }

  public function activatePassword()
  {
    $request = Limb :: toolkit()->getRequest();

    if(!$email = $request->get('user'))
      return false;

    if(!$password = $request->get('id'))
      return false;

    $user_data = $this->getUserByEmail($email);
    if(($password != $user_data['password']) ||  empty($user_data['generated_password']))
      return false;

    $this->merge($user_data);
    $this->set('password', $user_data['generated_password']);
    $this->set('generated_password', '');

    $this->update(false);

    return true;
  }

  public function getUserByEmail($email)
  {
    $db = Limb :: toolkit()->getDB();

    $sql =
      'SELECT *, scot.id as node_id, sco.id as id FROM
      sys_site_object_tree as scot,
      sys_site_object as sco,
      user as tn
      WHERE tn.email="' . $db->escape($email) . '"
      AND scot.object_id=tn.object_id
      AND sco.id=tn.object_id
      AND sco.current_version=tn.version';

    $db->sqlExec($sql);

    return current($db->getArray());
  }
}

?>