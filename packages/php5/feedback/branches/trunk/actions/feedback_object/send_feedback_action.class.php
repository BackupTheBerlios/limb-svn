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
require_once(LIMB_DIR . 'class/core/actions/form_action.class.php');
require_once(LIMB_DIR . 'class/core/sys_param.class.php');

class send_feedback_action extends form_action
{
	protected function _define_dataspace_name()
	{
	  return 'feedback_form';
	}
	
	protected function _init_validator()
	{
	  parent :: _init_validator();
	  
    $this->validator->add_rule(array(LIMB_DIR . 'class/validators/rules/required_rule', 'subject'));
    $this->validator->add_rule(array(LIMB_DIR . 'class/validators/rules/required_rule', 'sender_email'));
    $this->validator->add_rule(array(LIMB_DIR . 'class/validators/rules/email_rule', 'sender_email'));
    $this->validator->add_rule(array(LIMB_DIR . 'class/validators/rules/required_rule', 'body'));
	}

	protected function _get_email()
	{
		if(!$email = sys_param :: instance()->get_param('contact_email', 'char'))
			$email = constant('ADMINISTRATOR_EMAIL');

		return $email;
	}

	protected function _get_mail_subject()
	{
		return sprintf(strings :: get('message_subject', 'feedback'), 
												$this->dataspace->get('subject'),
												$_SERVER['HTTP_HOST']);	
	}	

	protected function _valid_perform($request, $response)
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
		
		$mailer = $this->_get_mailer();
		
    $headers['From']    = $mail_data['sender_email'];
    $headers['To']      = $recipient_email;
    $headers['Subject'] = $subject;
    
		if(!$recipient_email ||
			 !$mailer->send($recipient_email, 
										$headers, 
										$body
										)
				)
		{
			message_box :: write_notice(strings :: get('mail_not_sent', 'feedback'));
			
			$request->set_status(request :: STATUS_FAILUER);
			return;
		}
		
		message_box :: write_notice(strings :: get('message_was_sent', 'feedback'));

		$request->set_status(request :: STATUS_FORM_SUBMITTED);
		$response->redirect($_SERVER['PHP_SELF']);
	}
	
	protected function _get_mailer()
	{
	  include_once('Mail.php');
	  
	  return Mail :: factory('mail');
	}
}
?>