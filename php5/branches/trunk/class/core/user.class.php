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
require_once(LIMB_DIR . 'class/lib/db/db_factory.class.php');
require_once(LIMB_DIR . 'class/lib/system/objects_support.inc.php');
require_once(LIMB_DIR . 'class/core/object.class.php');

//This class requires serious refactoring since it "knows" too much - it shouldn't make login 
// and logout operations, it should be just a container!

class user extends object
{
  const DEFAULT_USER_ID = -1;
  const DEFAULT_USER_GROUP = 'visitors';
  
  protected static $_instance = null;
  
  protected
	  $_id,
	  $_node_id = -1,
	  $_login = '',
	  $_password = '',
	  $_email = '',
	  $_name = '',
	  $_lastname = '',
	  $_locale_id = '',
    $_is_logged_in = false,	
	  $_groups = array(),
	  $__session_class_path;
	
	function __construct()
	{
	  $this->_id = self :: DEFAULT_USER_ID;
	  
	  //important!!!
	  $this->__session_class_path = addslashes(__FILE__);
	  
	  parent :: __construct();
	}
	
	static public function instance()
	{
    if (!self :: $_instance)
      self :: $_instance = instantiate_session_object('user');

    return self :: $_instance;
	}
	
	//this should be fixed, since it's made public for testing only
	public function _set_groups($groups)
	{
		$this->_groups = $groups;
	}
	
	protected function _determine_groups()
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
	
	protected function _get_db_groups()
	{
		$db = db_factory :: instance();
		
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
      			AND u_i_g.user_id=". $this->get_id() . "
      			AND u_i_g.group_id=sso.id";
					
		$db->sql_exec($sql);	
		
		return $db->get_array();
	}
	
	protected function _get_default_db_groups()
	{
		$db = db_factory :: instance();
	
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
					
		$db->sql_exec($sql);	
		
		return $db->get_array();
	}
		
	public function login($login, $password, $locale_id = '')
	{				
		$this->logout();
		
		if(!$record = $this->_get_identity_record($login, $password))
			return false;
		
		$this->_set_is_logged_in();
		
		$this->_set_id($record['id']);
		$this->_set_node_id($record['node_id']);
		$this->_set_login($login);
		$this->_set_password($record['password']);
		
		$this->_set_email($record['email']);
		$this->_set_name($record['name']);
		$this->_set_lastname($record['lastname']);
		
		$this->_determine_groups();		

		if ($locale_id && locale::is_valid_locale_id($locale_id))
			$this->set_locale_id($locale_id);

		return true;
	}
	
	protected function _get_identity_record($login, $password)
	{
		$crypted_password = $this->get_crypted_password($login, $password);

		$db = db_factory :: instance();
		
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
					
		$db->sql_exec($sql);
		
		return $db->fetch_row();
	}
		
	public function logout()
	{	
		$this->_set_id(self :: DEFAULT_USER_ID);
		$this->_set_is_logged_in(false);
		$this->_set_groups(array());
	}
	
	public function get_crypted_password($login, $none_crypt_password)
	{	
		return md5($login.$none_crypt_password);
	}
	
	public function is_logged_in()
	{
		return $this->_is_logged_in;
	}
	
	protected function _set_is_logged_in($status = true)
	{
		$this->_is_logged_in = $status;
	}
	
	public function get_id()
	{
		return $this->_id;
	}
	
	//this should be fixed, since it's made public for testing only
	public function _set_id($id)
	{
		$this->_id = $id;
	}
	
	public function get_node_id()
	{
		return $this->_node_id;
	}

	protected function _set_node_id($node_id)
	{
		$this->_node_id = $node_id;
	}
	
	public function get_login()
	{
		return $this->_login;
	}

	protected function _set_login($login)
	{
		$this->_login = $login;
	}

	public function get_email()
	{
		return $this->_email;
	}

	protected function _set_email($email)
	{
		$this->_email = $email;
	}

	public function get_name()
	{
		return $this->_name;
	}

	protected function _set_name($name)
	{
		$this->_name = $name;
	}
	
	public function get_locale_id()
	{
		return $this->_locale_id;
	}
	
	public function set_locale_id($locale_id)
	{
		$this->_locale_id = $locale_id;
	}
	
	public function get_password()
	{
		return $this->_password;
	}

	protected function _set_password($password)
	{
		$this->_password = $password;
	}
	
	public function get_lastname()
	{
		return $this->_lastname;
	}
	
	protected function _set_lastname($lastname)
	{
		$this->_lastname = $lastname;
	}

	public function get_groups()
	{
		if(!$this->_groups)
			$this->_determine_groups();
		
		return $this->_groups;
	}
		
	public function generate_password()
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
		
	public function is_in_groups($groups_to_check)
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