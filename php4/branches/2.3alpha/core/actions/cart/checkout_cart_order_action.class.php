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
require_once(LIMB_DIR . '/core/actions/form_action.class.php');
require_once(LIMB_DIR . '/core/model/shop/cart.class.php');
require_once(LIMB_DIR . '/core/lib/i18n/locale.class.php');
require_once(LIMB_DIR . '/core/lib/date/date.class.php');
require_once(LIMB_DIR . '/core/lib/mail/send_html_mail.inc.php');
require_once(LIMB_DIR . '/core/template/template.class.php');

class checkout_cart_order_action extends form_action
{
	function _define_dataspace_name()
	{
	  return 'checkout_form';
	}

	function _init_validator()
	{
		parent :: _init_validator();

    $this->validator->add_rule($v1 = array(LIMB_DIR . '/core/lib/validators/rules/required_rule', 'name'));
    $this->validator->add_rule($v2 = array(LIMB_DIR . '/core/lib/validators/rules/required_rule', 'email'));
    $this->validator->add_rule($v3 = array(LIMB_DIR . '/core/lib/validators/rules/email_rule', 'email'));
	}

	function _init_dataspace(&$request)
	{
		$user =& user :: instance();
		
		if(!$user->is_logged_in())
			return;

		$data = array(
				'name' => $user->get_name() . ' ' . $user->get_lastname(),
				'email' => $user->get_email(),
		);
		$this->dataspace->import($data);
	}
	
	function _get_email()
	{
		$sys_param =& sys_param :: instance();

		if(!$email = $sys_param->get_param('contact_email', 'char'))
			$email = constant('ADMINISTRATOR_EMAIL');		

		return $email;
	}
	
	function _valid_perform(&$request, &$response)
	{
		//$html_body = $this->_get_mail_body('/cart/mail_template.html');
		$text_body = $this->_get_mail_body('/cart/mail_template.txt');

		$subject = sprintf(strings :: get('message_subject', 'cart'), $_SERVER['HTTP_HOST']);
		
		$recipient_email = $this->_get_email();
		if(!send_plain_mail(array($recipient_email), 
												$_SERVER['SERVER_ADMIN']. '<' . $_SERVER['HTTP_HOST'] . '> ', 
												$subject, 
												$text_body))
		{
			message_box :: write_error(strings :: get('mail_not_sent', 'cart'));

			$request->set_status(REQUEST_STATUS_FAILURE);
			
  		if($request->has_attribute('popup'))
  			$response->write(close_popup_response($request));
  			
  		return;
		}
		
		$this->_clear_cart();
		
		message_box :: write_error(strings :: get('message_was_sent', 'cart'));

		$request->set_status(REQUEST_STATUS_FORM_SUBMITTED);
		
		if($request->has_attribute('popup'))
			$response->write(close_popup_response($request));
	}
	
	function _clear_cart()
	{
		$cart =& cart :: instance();
		$cart->clear();
	}
		
	function _get_mail_body($template_path)
	{
		$template = new template($template_path);
		
		$locale =& locale :: instance();
		$date = new date();
		$template->set('date', $date->format($locale->get_short_date_format()));
		
		$cart =& cart :: instance();
		
		$list =& $template->find_child('cart_items');

		$list->register_dataset($cart->get_items_array_dataset());

		$template->set('name', $this->dataspace->get('name'));
		$template->set('notes', $this->dataspace->get('notes'));
		$template->set('phone', $this->dataspace->get('phone'));
		$template->set('address', $this->dataspace->get('address'));
		$template->set('email', $this->dataspace->get('email'));
		
		ob_start();
		$template->display();
		$content = ob_get_contents();
		ob_end_clean();
		
		return $content;
	}	
}

?>