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
require_once(LIMB_DIR . '/class/core/permissions/authenticator.interface.php');

class simple_authenticator implements authenticator
{
  const DEFAULT_USER_GROUP = 'visitors';

  public function login($params = array())
  {
    if(!isset($params['login']))
      throw new LimbException('login attribute required!');

    if(!isset($params['password']))
      throw new LimbException('password attribute required!');

    $user = user :: instance();
		$user->logout();
		
		if($record = $this->_get_identity_record($params['login'], $params['password']))
		{
  		$user->login();
  		
  		$record['login'] = $params['login'];
  		$user->import($record);
		}
		
		$this->_determine_groups();		

		if (isset($params['locale_id']) && locale::is_valid_locale_id($params['locale_id']))
		  $user->set('locale_id', $params['locale_id']);
  }
  
	protected function _determine_groups()
	{
	  $user = user :: instance();
	  
		if ($user->is_logged_in())
			$groups_arr = $this->_get_db_groups();
		else
			$groups_arr = $this->_get_default_db_groups();
		
		if(!$groups_arr)
			return;
					
		$result = array();
		foreach($groups_arr as $group_data)
			$result[$group_data['object_id']] = $group_data['identifier'];
		
		$user->set('groups', $result);
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
	

	protected function _get_db_groups()
	{
	  $user = user :: instance();
	  
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
      			AND u_i_g.user_id=". $user->get_id() . "
      			AND u_i_g.group_id=sso.id";
					
		$db->sql_exec($sql);	
		
		return $db->get_array();
	}

	protected function _get_identity_record($login, $password)
	{
		$crypted_password = self :: get_crypted_password($login, $password);

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

	static public function get_crypted_password($login, $none_crypt_password)
	{	
		return md5($login.$none_crypt_password);
	}

  public function logout($params = array())
  {
    user :: instance()->logout();
    
    $this->_determine_groups();
  }

	static public function is_user_in_groups($groups_to_check)
	{
		if (!is_array($groups_to_check))
		{
			$groups_to_check = explode(',', $groups_to_check);
			if(!is_array($groups_to_check))
				return false;
		}	
			
		foreach	(user :: instance()->get('groups', array()) as $group_name)
			if (in_array($group_name, $groups_to_check))
				return true; 
		
		return false;		
	}

	static public function generate_password()
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