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
require_once(LIMB_DIR . 'class/validators/rules/single_field_rule.class.php');

class unique_user_email_rule extends single_field_rule
{
	protected $current_identifier = '';
	
	function __construct($field_name, $current_identifier='')
	{
		$this->current_identifier = $current_identifier;
		
		parent :: __construct($field_name);
	} 

	public function validate($dataspace)
	{
		if(!$value = $dataspace->get($this->field_name))
			return;
			
		if(	$this->current_identifier &&
				$this->current_identifier == $value)
			return;

		$db = db_factory :: instance();
		
		$sql = 'SELECT *
		FROM sys_site_object as sco, user as tn
		WHERE tn.email="' . $db->escape($value) . '"
		AND sco.id=tn.object_id 
		AND sco.current_version=tn.version';
					
		$db->sql_exec($sql);

		$arr = $db->get_array();

		if(is_array($arr) && count($arr))
			$this->error(strings :: get('error_duplicate_user', 'error'));
	} 
} 

?>