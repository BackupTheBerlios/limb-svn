<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: edit_poll_answer_action.class.php 467 2004-02-18 10:16:31Z mike $
*
***********************************************************************************/ 
require_once(LIMB_DIR . 'core/actions/form_edit_site_object_action.class.php');

class edit_poll_answer_action extends form_edit_site_object_action
{
	function edit_poll_answer_action()
	{
		$definition = array(
			'site_object' => 'poll_answer',
		);

		parent :: form_edit_site_object_action('edit_poll_answer', $definition);
	}
}

?>