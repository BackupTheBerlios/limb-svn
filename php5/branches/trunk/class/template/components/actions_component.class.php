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
require_once(LIMB_DIR . '/class/template/components/list_component.class.php');
require_once(LIMB_DIR . '/class/etc/limb_util.inc.php');

class actions_component extends list_component
{
	protected $all_actions = array();

	protected $node_id;
	
	public function set_actions($all_actions)
	{
		$this->all_actions = $all_actions;
	}	

	public function set_node_id($node_id)
	{
		$this->node_id = $node_id;
	}
		
	public function prepare()
	{
		$actions = $this->get_actions();

		if (count($actions))
			$this->register_dataset(new array_dataset($actions));
		
		return parent :: prepare();	
	} 
		
	public function get_actions()
	{
		if (!count($this->all_actions))
			return array();
		
		$actions = array();
		
		foreach($this->all_actions as $action => $params)
		{
			if (isset($params['extra']))
				$action_params = $params['extra'];
			else	
				$action_params = array();
			
			if(isset($params['popup']) && $params['popup'] === true)
					$action_params['popup'] = 1;

			if (isset($params['JIP']) && $params['JIP'] === true)
			{
				$actions[$action] = $params;
				$actions[$action]['action'] = $action;
				$action_params['action'] = $action;
				$action_params['node_id'] = $this->node_id;
								
				$actions[$action]['action_href'] = add_url_query_items('/root', $action_params);
			}
		}
		
		return $actions;
	}
} 

?>