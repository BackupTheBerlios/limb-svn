<?php

require_once(LIMB_DIR . 'core/lib/util/complex_array.class.php');
require_once(LIMB_DIR . 'core/actions/form_action.class.php');
require_once(LIMB_DIR . 'core/lib/validators/rules/required_rule.class.php');
require_once(LIMB_DIR . 'core/lib/validators/rules/email_rule.class.php');
require_once(LIMB_DIR . 'core/lib/mail/mime_mail.class.php');

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
		
		$mail = new mime_mail();
		$mail->set_body($body);
		$mail->build_message();
		
		$recipient_email = constant('ADMINISTRATOR_EMAIL');
		if(!$recipient_email || !$mail->send('administrator', 
										$recipient_email, 
										$mail_data['sender_name'], 
										$mail_data['sender_email'], 
										$subject))
		{
			message_box :: write_error(strings :: get('mail_not_sent', 'feedback'));
			return false;
		}

		return true;
	}
}
?>