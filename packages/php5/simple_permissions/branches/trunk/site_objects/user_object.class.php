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
require_once(LIMB_DIR . '/class/core/permissions/user.class.php');
require_once(LIMB_DIR . '/class/core/site_objects/content_object.class.php');
require_once(dirname(__FILE__) . '/../simple_authenticator.class.php');

class user_object extends content_object
{
	public function create($is_root = false)
	{
		$crypted_password = simple_authenticator :: get_crypted_password($this->get_identifier(), $this->get('password'));
		$this->set('password', $crypted_password);
		return parent :: create($is_root);
	}
	
	public function get_membership($user_id)
	{
		$db_table	= Limb :: toolkit()->createDBTable('user_in_group');
		$groups = $db_table->get_list('user_id='. $user_id, '', 'group_id');		
		return $groups;
	}
	
	public function save_membership($user_id, $membership)
	{
		$db_table	= Limb :: toolkit()->createDBTable('user_in_group');
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
	
	public function change_password()
	{
		if(!$user_id = $this->get_id())
   	  throw new LimbException('user id not set');

		if(!$identifier = $this->get_identifier())
   	  throw new LimbException('user identifier not set');
		
		$this->set(
			'password', 
			simple_authenticator :: get_crypted_password(
				$identifier, 
				$this->get('password')
			)
		);
		
		$this->update(false);
	}

	public function validate_password($password)
	{
		$user = Limb :: toolkit()->getUser();
		
		if(!$user->is_logged_in() || !$node_id = $user->get('node_id'))
		  return false;

		$password = simple_authenticator :: get_crypted_password($user->get_login(), $password);
		
		if($user->get('password') !== $password)
			return false;
		else 
			return true;
	}

	public function change_own_password($password)
	{
		$user = Limb :: toolkit()->getUser();
		
		$node_id = $user->get('node_id');

		$data['password'] = simple_authenticator :: get_crypted_password($user->get_login(),	$password);
		
		$user_db_table = Limb :: toolkit()->createDBTable('user');

		$this->set('password', $data['password']);
		
		$user_db_table->update($data, 'identifier="'. $user->get_login() . '"');
		return $this->login($user->get_login(), $password);
	}

	public function generate_password($email, &$new_non_crypted_password)
	{
		if(!$user_data = $this->get_user_by_email($email))
			return false;
		
		$this->merge($user_data);

		$new_non_crypted_password = user :: generate_password();
		$crypted_password = simple_authenticator :: get_crypted_password($user_data['identifier'], $new_non_crypted_password);
		$this->set('generated_password', $crypted_password);
		
		$this->update(false);
		return true;
	}

	public function activate_password()
	{
	  $request = Limb :: toolkit()->getRequest();
	  
	  if(!$email = $request->get('user'))
			return false;

	  if(!$password = $request->get('id'))
			return false;
		
		$user_data = $this->get_user_by_email($email);
		if(($password != $user_data['password']) || empty($user_data['generated_password']))
			return false;
		
		$this->merge($user_data);
		$this->set('password', $user_data['generated_password']);
		$this->set('generated_password', '');
		
    $this->update(false);
    
		return true;
	}

	public function get_user_by_email($email)
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
					
		$db->sql_exec($sql);
		
		return current($db->get_array());
	}
}

?>