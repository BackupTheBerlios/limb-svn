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
require_once(LIMB_DIR . 'core/lib/util/complex_array.class.php');
require_once(LIMB_DIR . 'core/actions/form_action.class.php');
require_once(LIMB_DIR . 'core/lib/validators/rules/required_rule.class.php');
require_once(LIMB_DIR . 'core/lib/validators/rules/email_rule.class.php');
require_once(LIMB_DIR . 'core/model/response/redirect_response.class.php');
require_once(LIMB_DIR . 'core/model/sys_param.class.php');

class send_feedback_action extends form_action
{
	function send_feedback_action($name='feedback_form', $merge_definition=array())
	{
		parent :: form_action($name);
	}
	
	function _init_validator()
	{
		$this->validator->add_rule(new required_rule('subject'));
		$this->validator->add_rule(new required_rule('sender_name'));
		$this->validator->add_rule(new required_rule('sender_email'));
		$this->validator->add_rule(new email_rule('sender_email'));
		$this->validator->add_rule(new required_rule('body'));
	}

	function _get_email()
	{
		$sys_param =& sys_param :: instance();

		if(!$email = $sys_param->get_param('contact_email', 'char'))
			$email = constant('ADMINISTRATOR_EMAIL');		

		return $email;
	}
	
	function _valid_perform()
	{
		$mail_data = $this->dataspace->export();
		$body = sprintf(strings :: get('body_template', 'feedback'),
										$mail_data['sender_name'], 
										$mail_data['sender_email'],
										$mail_data['body']);
										
		$body = str_replace('<br>', "\n", $body);

		$subject = sprintf(strings :: get('message_subject', 'feedback'), 
												$mail_data['subject'],
												$_SERVER['HTTP_HOST']);
		
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
			return new failed_response();
		}
		
		message_box :: write_error(strings :: get('message_was_sent', 'feedback'));
		return new redirect_response(RESPONSE_STATUS_FORM_SUBMITTED, PHP_SELF);
	}
}
?>