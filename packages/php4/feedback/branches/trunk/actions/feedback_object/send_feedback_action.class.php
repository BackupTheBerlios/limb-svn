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
require_once(LIMB_DIR . 'class/lib/util/complex_array.class.php');
require_once(LIMB_DIR . 'class/lib/mail/send_plain_mail.inc.php');
require_once(LIMB_DIR . 'class/core/actions/form_action.class.php');
require_once(LIMB_DIR . 'class/core/sys_param.class.php');

class send_feedback_action extends form_action
{
	function _define_dataspace_name()
	{
	  return 'feedback_form';
	}
	
	function _init_validator()
	{
	  parent :: _init_validator();
	  
    $this->validator->add_rule($v1 = array(LIMB_DIR . 'class/validators/rules/required_rule', 'subject'));
    $this->validator->add_rule($v2 = array(LIMB_DIR . 'class/validators/rules/required_rule', 'sender_email'));
    $this->validator->add_rule($v3 = array(LIMB_DIR . 'class/validators/rules/email_rule', 'sender_email'));
    $this->validator->add_rule($v4 = array(LIMB_DIR . 'class/validators/rules/required_rule', 'body'));
	}

	function _get_email()
	{
		$sys_param =& sys_param :: instance();

		if(!$email = $sys_param->get_param('contact_email', 'char'))
			$email = constant('ADMINISTRATOR_EMAIL');		

		return $email;
	}

	function _get_mail_subject()
	{
		return sprintf(strings :: get('message_subject', 'feedback'), 
												$this->dataspace->get('subject'),
												$_SERVER['HTTP_HOST']);	
	}	

	function _valid_perform(&$request, &$response)
	{
		$mail_data = $this->dataspace->export();

		if(isset($mail_data['sender_name']) )
			$sender_name = $mail_data['sender_name'];
		else
			$sender_name = $mail_data['sender_firstname'] . ' ' . $mail_data['sender_lastname'];	
		
		$body = sprintf(strings :: get('body_template', 'feedback'),
										$sender_name, 
										$mail_data['sender_email'],
										$mail_data['body']);
										
		$body = str_replace('<br>', "\n", $body);

		$subject = $this->_get_mail_subject();
		
		$recipient_email = $this->_get_email();
		
		if(!$recipient_email ||
			 !send_plain_mail(array($recipient_email), 
										$mail_data['sender_email'], 
										$subject,
										$body
										)
				)
		{
			message_box :: write_error(strings :: get('mail_not_sent', 'feedback'));
			
			$request->set_status(REQUEST_STATUS_FAILUER);
			return;
		}
		
		message_box :: write_error(strings :: get('message_was_sent', 'feedback'));

		$request->set_status(REQUEST_STATUS_FORM_SUBMITTED);
		$response->redirect($_SERVER['PHP_SELF']);
	}
}
?>