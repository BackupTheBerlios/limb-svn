<?php

require_once(LIMB_DIR . 'core/actions/doc_flow_object/set_publish_status_action.class.php');

class unpublish_action extends set_publish_status_action
{
	function unpublish_action($name='')
	{
		parent :: set_publish_status_action($name);
	}
	
	function perform()
	{
		$result = $this->set_publish_status(get_ini_option('doc_flow.ini', 'default', 'unpublished'));
		if ($result !== false)
			close_popup();
		else
			return false;	

	}
	
}

?>