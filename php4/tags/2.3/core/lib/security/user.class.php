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
define('DEFAULT_USER_ID', -1);
define('DEFAULT_USER_GROUP', 'visitors');

require_once(LIMB_DIR . '/core/lib/db/db_factory.class.php');
require_once(LIMB_DIR . '/core/lib/system/objects_support.inc.php');
require_once(LIMB_DIR . '/core/model/object.class.php');

class user extends object
{
  var $_id = DEFAULT_USER_ID;
  var $_node_id = -1;
  var $_login = '';
  var $_password = '';
  var $_email = '';
  var $_name = '';
  var $_lastname = '';
  var $_locale_id = '';

  var $_is_logged_in = false;

  var $_groups = array();

  function user()
  {
    parent :: object();

    //IMPORTANT!!!
    $this->__session_class_path = LIMB_DIR . '/core/lib/security/user.class.php';
  }

  function & instance()
  {
    $obj =& instantiate_session_object('user');
    return $obj;
  }

  function _set_groups($groups)
  {
    $this->_groups = $groups;
  }

  function _determine_groups()
  {
    if ($this->is_logged_in())
      $groups_arr = $this->_get_db_groups();
    else
      $groups_arr = $this->_get_default_db_groups();

    if(!$groups_arr)
      return;

    $result = array();
    foreach($groups_arr as $group_data)
      $result[$group_data['object_id']] = $group_data['identifier'];

    $this->_set_groups($result);
  }

  function _get_db_groups()
  {
    $db =& db_factory :: instance();

    $sql =
      'SELECT sso.*, tn.*
      FROM sys_site_object as sso, user_group as tn, user_in_group as u_i_g
      WHERE sso.id=tn.object_id
      AND sso.current_version=tn.version
      AND u_i_g.user_id='. $this->get_id() . '
      AND u_i_g.group_id=sso.id';

    $db->sql_exec($sql);

    return $db->get_array();
  }

  function _get_default_db_groups()
  {
    $db =& db_factory :: instance();

    $sql =
      'SELECT sso.*, tn.*
      FROM sys_site_object as sso, user_group as tn
      WHERE sso.identifier="' . DEFAULT_USER_GROUP . '"
      AND sso.id=tn.object_id
      AND sso.current_version=tn.version';

    $db->sql_exec($sql);

    return $db->get_array();
  }

  function _set_logged_user_data(&$user_data, $locale_id = '')
  {
    $this->_set_is_logged_in();

    $this->_set_id($user_data['id']);
    $this->_set_node_id($user_data['node_id']);
    $this->_set_login($user_data['identifier']);
    $this->_set_password($user_data['password']);

    $this->_set_email($user_data['email']);
    $this->_set_name($user_data['name']);
    $this->_set_lastname($user_data['lastname']);

    $this->_determine_groups();

    if ($locale_id && locale::is_valid_locale_id($locale_id))
      $this->set_locale_id($locale_id);

  }

  function login($login, $password, $locale_id = '')
  {
    $this->logout();

    if(!$record = $this->_get_identity_record($login, $password))
      return false;

    $this->_set_logged_user_data($record, $locale_id);

    return true;
  }

  function try_autologin()
  {
    if($this->is_logged_in())
      return false;

    $cookie = $_COOKIE;
    if(empty($cookie['user_id']) || empty($cookie['password']))
      return false;

    if(!$record = $this->_get_identity_record_by_id($cookie['user_id'], $cookie['password']))
      return false;

    $this->_set_logged_user_data($record, $locale_id);

    return true;
  }

  function configure_autologin($time = null)
  {
    if(!$this->is_logged_in())
      return false;
    $one_week = 7 * 24 * 60 * 60;
    if($time === null)
      $time = $one_week;

    setcookie('user_id', $this->get_id(), time() + $time, '/');
    setcookie('password', $this->get_password(), time() + $time, '/');
  }

  function &_get_identity_record($login, $password)
  {
    $crypted_password = $this->get_crypted_password($login, $password);

    $db =& db_factory :: instance();

    $sql =
      'SELECT *, ssot.id as node_id, sso.id as id FROM
      sys_site_object_tree as ssot,
      sys_site_object as sso,
      user as tn
      WHERE sso.identifier="' . $db->escape($login) . '"
      AND tn.password="' . $db->escape($crypted_password) . '"
      AND ssot.object_id=tn.object_id
      AND sso.id=tn.object_id
      AND sso.current_version=tn.version';

    $db->sql_exec($sql);

    return $db->fetch_row();
  }

  function &_get_identity_record_by_id($user_id, $crypted_password)
  {
    $db =& db_factory :: instance();

    $sql =
      'SELECT *, ssot.id as node_id, sso.id as id FROM
      sys_site_object_tree as ssot,
      sys_site_object as sso,
      user as tn
      WHERE tn.object_id="' . $user_id . '"
      AND tn.password="' . $db->escape($crypted_password) . '"
      AND ssot.object_id=tn.object_id
      AND sso.id=tn.object_id
      AND sso.current_version=tn.version';

    $db->sql_exec($sql);

    return $db->fetch_row();
  }

  function logout()
  {
    $this->_set_id(DEFAULT_USER_ID);
    $this->_set_is_logged_in(false);
    $this->_set_groups(array());

    $cookie = $_COOKIE;
    if(empty($cookie['user_id']) || empty($cookie['password']))
      return;

    setcookie('user_id', 0, time() - 1, '/');
    setcookie('password', 0, time() - 1, '/');

  }

  function get_crypted_password($login, $none_crypt_password)
  {
    return md5($login.$none_crypt_password);
  }

  function is_logged_in()
  {
    return $this->_is_logged_in;
  }

  function _set_is_logged_in($status = true)
  {
    $this->_is_logged_in = $status;
  }

  function get_id()
  {
    return $this->_id;
  }

  function _set_id($id)
  {
    $this->_id = $id;
  }

  function get_node_id()
  {
    return $this->_node_id;
  }

  function _set_node_id($node_id)
  {
    $this->_node_id = $node_id;
  }

  function get_login()
  {
    return $this->_login;
  }

  function _set_login($login)
  {
    $this->_login = $login;
  }

  function get_email()
  {
    return $this->_email;
  }

  function _set_email($email)
  {
    $this->_email = $email;
  }

  function get_name()
  {
    return $this->_name;
  }

  function _set_name($name)
  {
    $this->_name = $name;
  }

  function get_locale_id()
  {
    return $this->_locale_id;
  }

  function set_locale_id($locale_id)
  {
    $this->_locale_id = $locale_id;
  }

  function get_password()
  {
    return $this->_password;
  }

  function _set_password($password)
  {
    $this->_password = $password;
  }

  function get_lastname()
  {
    return $this->_lastname;
  }

  function _set_lastname($lastname)
  {
    $this->_lastname = $lastname;
  }

  function get_groups()
  {
    if(!$this->_groups)
      $this->_determine_groups();

    return $this->_groups;
  }

  function generate_password()
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

  function is_in_groups($groups_to_check)
  {
    if (!is_array($groups_to_check))
    {
      $groups_to_check = explode(',', $groups_to_check);
      if(!is_array($groups_to_check))
        return false;
    }

    foreach	($this->get_groups() as $group_name)
      if (in_array($group_name, $groups_to_check))
        return true;

    return false;
  }
}
?>