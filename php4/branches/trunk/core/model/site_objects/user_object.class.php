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
require_once(LIMB_DIR . '/core/model/site_objects/content_object.class.php');

class user_object extends content_object
{
  function _define_attributes_definition()
  {
    return complex_array :: array_merge(
        parent :: _define_attributes_definition(),
        array(
        'second_password' => '',
        ));
  }

  function _define_class_properties()
  {
    return array(
      'class_ordr' => 1,
      'db_table_name' => 'user',
      'can_be_parent' => 0,
    );
  }

  function create($is_root = false)
  {
    $crypted_password = user :: get_crypted_password($this->get_identifier(), $this->get_attribute('password'));
    $this->set_attribute('password', $crypted_password);
    return parent :: create($is_root);
  }

  function get_membership($user_id)
  {
    $db_table =& db_table_factory :: instance('user_in_group');
    $groups = $db_table->get_list('user_id='. $user_id, '', 'group_id');
    return $groups;
  }

  function save_membership($user_id, $membership)
  {
    $db_table =  & db_table_factory :: instance('user_in_group');
    $db_table->delete('user_id='. $user_id);

    foreach($membership as $group_id => $is_set)
    {
      if (!$is_set)
        continue;

      $data = array();
      $data['user_id'] = (int)$user_id;
      $data['group_id'] = (int)$group_id;
      $db_table->insert($data);
    }

    return true;
  }

  function change_password()
  {
    if(!$user_id = $this->get_id())
    {
      debug :: write_error('user id not set',
        __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
      return false;
    }

    if(!$identifier = $this->get_identifier())
    {
      debug :: write_error('user identifier not set',
        __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
      return false;
    }

    $user =& user :: instance();

    $this->set_attribute(
      'password',
      $user->get_crypted_password(
        $identifier,
        $this->get_attribute('password')
      )
    );

    if($user_id == $user->get_id())
    {
      $user->logout();
      message_box :: write_warning(strings :: get('need_relogin', 'user'));
    }
    else
      session :: destroy_user_session($user_id);

    return $this->update(false);
  }

  function validate_password($password)
  {
    $user =& user :: instance();

    if(!$user->is_logged_in() || !$node_id = $user->get_node_id())
    {
      debug :: write_error('user not logged in or node id is not set',
        __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
      return false;
    }

    $password = $user->get_crypted_password($user->get_login(), $password);

    if($user->get_password() !== $password)
      return false;
    else
      return true;
  }

  function change_own_password($password)
  {
    $user =& user :: instance();

    if(!$node_id = $user->get_node_id())
    {
      debug :: write_error('user not logged in - node id is not set',
        __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
      return false;
    }

    $data['password'] = $user->get_crypted_password($user->get_login(), $password);

    $user_db_table =& db_table_factory :: create('user');

    $this->set_attribute('password', $data['password']);

    if ($user_db_table->update($data, 'identifier="'. $user->get_login() . '"'))
      return $this->login($user->get_login(), $password);
    else
      return false;
  }

  function generate_password($email, &$new_non_crypted_password)
  {
    if(!$user_data = $this->get_user_by_email($email))
      return false;

    $this->merge_attributes($user_data);

    $new_non_crypted_password = user :: generate_password();
    $crypted_password = user :: get_crypted_password($user_data['identifier'], $new_non_crypted_password);
    $this->set_attribute('generated_password', $crypted_password);

    if($result = $this->update(false))
      $this->send_activate_password_email($user_data, $new_non_crypted_password);

    return $result;
  }

  function activate_password()
  {
    $request = request :: instance();

    if(!$email = $request->get_attribute('user'))
      return false;

    if(!$password = $request->get_attribute('id'))
      return false;

    $user_data = $this->get_user_by_email($email);
    if(($password != $user_data['password']) || empty($user_data['generated_password']))
      return false;

    $this->merge_attributes($user_data);
    $this->set_attribute('password', $user_data['generated_password']);
    $this->set_attribute('generated_password', '');

    return $this->update(false);
  }

  function get_user_by_email($email)
  {
    $db =& db_factory :: instance();

    $sql =
      'SELECT *, scot.id as node_id, sco.id as id FROM
      sys_site_object_tree as scot,
      sys_site_object as sco,
      user as tn
      WHERE tn.email="' . $db->escape($email) . '"
      AND scot.object_id=tn.object_id
      AND sco.id=tn.object_id
      AND sco.current_version=tn.version';

    $db->sql_exec($sql);

    return current($db->get_array());
  }

  function send_activate_password_email(&$user_data, $password)
  {
    include_once(LIMB_DIR . '/core/lib/mail/send_plain_mail.inc.php');
    global $_SERVER;
    $http_host = $_SERVER['HTTP_HOST'];

    $filename = LIMB_DIR . '/design/default/templates/user/generated_password_mail.html';

    if(!file_exists($filename))
      error('template file for password notification email not found!',
        __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, array('file_name' => $filename));

    $fd = fopen ($filename, "r");
    $contents = fread ($fd, filesize ($filename));
    fclose ($fd);

    $contents = str_replace('%website_name%', $http_host, $contents);
    $contents = str_replace('%user_name%', $user_data['name']. ' '. $user_data['lastname'], $contents);
    $contents = str_replace('%new_password%', $password, $contents);
    $contents = str_replace('%website_href%', $http_host, $contents);
    $contents = str_replace('%website_email%', ADMINISTRATOR_EMAIL, $contents);

    $activate_href = 'http://'. $http_host. '/root/activate_password?user='. $user_data['email'] .'&id='. $user_data['password'];
    $contents = str_replace('%activate_href%', $activate_href, $contents);

    if(!send_plain_mail(
                  array($user_data['email']),
                  ADMINISTRATOR_EMAIL,
                  strings :: get('generate_password_theme', 'user'),
                  $contents
                  )
      )
    {
      debug :: write_error('error while sending password notification email',
        __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
      return false;
    }
    else
      return true;
  }

  function login($login, $password, $locale_id = '')
  {
    $user =& user :: instance();
    return  $user->login($login, $password, $locale_id);
  }

  function logout()
  {
    $user =& user :: instance();
    return $user->logout();
  }
}

?>