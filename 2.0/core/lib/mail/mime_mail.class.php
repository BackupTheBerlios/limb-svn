<?php

require_once(LIMB_DIR . 'core/lib/mail/mime_mail_part.class.php');

class mime_mail
{

	var $html;

	var $text;

	var $output;

	var $html_text;

	var $html_images;

	var $image_types;

	var $build_params;

	var $attachments;

	var $headers;

	var $is_built;

	var $return_path;

	var $smtp_params;

	function mime_mail()
	{

		$this->html_images = array();
		$this->headers = array();
		$this->is_built = false;

		$this->image_types = array(
			'gif' => 'image/gif',
			'jpg' => 'image/jpeg',
			'jpeg' => 'image/jpeg',
			'jpe' => 'image/jpeg',
			'bmp' => 'image/bmp',
			'png' => 'image/png',
			'tif' => 'image/tiff',
			'tiff' => 'image/tiff',
			'swf' => 'application/x-shockwave-flash'
			);

		$this->build_params['html_encoding'] = 'quoted-printable';
		$this->build_params['text_encoding'] = '7bit';
		$this->build_params['html_charset'] = 'ISO-8859-1';
		$this->build_params['text_charset'] = 'ISO-8859-1';
		$this->build_params['head_charset'] = 'ISO-8859-1';
		$this->build_params['text_wrap'] = 998;

		if (!empty($GLOBALS['HTTP_SERVER_VARS']['HTTP_HOST']))
		{
			$helo = $GLOBALS['HTTP_SERVER_VARS']['HTTP_HOST'];
		} elseif (!empty($GLOBALS['HTTP_SERVER_VARS']['SERVER_NAME']))
		{
			$helo = $GLOBALS['HTTP_SERVER_VARS']['SERVER_NAME'];
		} 
		else
		{
			$helo = 'localhost';
		} 

		$this->smtp_params['host'] = 'localhost';
		$this->smtp_params['port'] = 25;
		$this->smtp_params['helo'] = $helo;
		$this->smtp_params['auth'] = false;
		$this->smtp_params['user'] = '';
		$this->smtp_params['pass'] = '';

		$this->headers['MIME-Version'] = '1.0';
	} 

	function get_file($filename)
	{
		$return = '';
		if ($fp = fopen($filename, 'rb'))
		{
			while (!feof($fp))
			{
				$return .= fread($fp, 1024);
			} 
			fclose($fp);
			return $return;
		} 
		else
		{
			return false;
		} 
	} 

	function set_crlf($crlf = "\n")
	{
		if (!defined('CRLF'))
		{
			define('CRLF', $crlf, true);
		} 

		if (!defined('MAIL_MIMEPART_CRLF'))
		{
			define('MAIL_MIMEPART_CRLF', $crlf, true);
		} 
	} 

	function set_smtp_params($host = null, $port = null, $helo = null, $auth = null, $user = null, $pass = null)
	{
		if (!is_null($host)) $this->smtp_params['host'] = $host;
		if (!is_null($port)) $this->smtp_params['port'] = $port;
		if (!is_null($helo)) $this->smtp_params['helo'] = $helo;
		if (!is_null($auth)) $this->smtp_params['auth'] = $auth;
		if (!is_null($user)) $this->smtp_params['user'] = $user;
		if (!is_null($pass)) $this->smtp_params['pass'] = $pass;
	} 

	function set_text_encoding($encoding = '7bit')
	{
		$this->build_params['text_encoding'] = $encoding;
	} 

	function set_html_encoding($encoding = 'quoted-printable')
	{
		$this->build_params['html_encoding'] = $encoding;
	} 

	function set_text_charset($charset = 'ISO-8859-1')
	{
		$this->build_params['text_charset'] = $charset;
	} 

	function set_html_charset($charset = 'ISO-8859-1')
	{
		$this->build_params['html_charset'] = $charset;
	} 

	function set_head_charset($charset = 'ISO-8859-1')
	{
		$this->build_params['head_charset'] = $charset;
	} 

	function set_text_wrap($count = 998)
	{
		$this->build_params['text_wrap'] = $count;
	} 

	function set_header($name, $value)
	{
		$this->headers[$name] = $value;
	} 

	function set_subject($subject)
	{
		$this->headers['Subject'] = $subject;
	} 

	function set_from($from)
	{
		$this->headers['From'] = $from;
	} 

	function set_return_path($return_path)
	{
		$this->return_path = $return_path;
	} 

	function set_cc($cc)
	{
		$this->headers['Cc'] = $cc;
	} 

	function set_bcc($bcc)
	{
		$this->headers['Bcc'] = $bcc;
	} 

	function set_text($text = '')
	{
		$this->text = $text;
	} 

	function set_html($html, $text = null, $images_dir = null)
	{
		$this->html = $html;
		$this->html_text = $text;

		if (isset($images_dir))
		{
			$this->_find_html_images($images_dir);
		} 
	} 

	function _find_html_images($images_dir)
	{
		while (list($key,) = each($this->image_types))
		{
			$extensions[] = $key;
		} 

		preg_match_all('/(?:"|\')([^"\']+\.(' . implode('|', $extensions) . '))(?:"|\')/Ui', $this->html, $images);

		for ($i = 0; $i < count($images[1]); $i++)
		{
			if (file_exists($images_dir . $images[1][$i]))
			{
				$html_images[] = $images[1][$i];
				$this->html = str_replace($images[1][$i], basename($images[1][$i]), $this->html);
			} 
		} 

		if (!empty($html_images))
		{
			$html_images = array_unique($html_images);
			sort($html_images);

			for ($i = 0; $i < count($html_images); $i++)
			{
				if ($image = $this->get_file($images_dir . $html_images[$i]))
				{
					$ext = substr($html_images[$i], strrpos($html_images[$i], '.') + 1);
					$content_type = $this->image_types[strtolower($ext)];
					$this->add_html_image($image, basename($html_images[$i]), $content_type);
				} 
			} 
		} 
	} 

	function add_html_image($file, $name = '', $c_type = 'application/octet-stream')
	{
		$this->html_images[] = array(
			'body' => $file,
			'name' => $name,
			'c_type' => $c_type,
			'cid' => md5(uniqid(time()))
		);
	} 

	function add_attachment($file, $name = '', $c_type = 'application/octet-stream', $encoding = 'base64')
	{
		$this->attachments[] = array(
			'body' => $file,
			'name' => $name,
			'c_type' => $c_type,
			'encoding' => $encoding
		);
	} 

	function &_add_text_part(&$obj, $text)
	{
		$params['content_type'] = 'text/plain';
		$params['encoding'] = $this->build_params['text_encoding'];
		$params['charset'] = $this->build_params['text_charset'];
		if (is_object($obj))
		{
			return $obj->add_subpart($text, $params);
		} 
		else
		{
			return new mime_mail_part($text, $params);
		} 
	} 

	function &_add_html_part(&$obj)
	{
		$params['content_type'] = 'text/html';
		$params['encoding'] = $this->build_params['html_encoding'];
		$params['charset'] = $this->build_params['html_charset'];
		if (is_object($obj))
		{
			return $obj->add_subpart($this->html, $params);
		} 
		else
		{
			return new mime_mail_part($this->html, $params);
		} 
	} 

	function &_add_mixed_part()
	{
		$params['content_type'] = 'multipart/mixed';
		return new mime_mail_part('', $params);
	} 

	function &_add_alternative_part(&$obj)
	{
		$params['content_type'] = 'multipart/alternative';
		if (is_object($obj))
		{
			return $obj->add_subpart('', $params);
		} 
		else
		{
			return new mime_mail_part('', $params);
		} 
	} 

	function &_add_related_part(&$obj)
	{
		$params['content_type'] = 'multipart/related';
		if (is_object($obj))
		{
			return $obj->add_subpart('', $params);
		} 
		else
		{
			return new mime_mail_part('', $params);
		} 
	} 

	function &_add_html_image_part(&$obj, $value)
	{
		$params['content_type'] = $value['c_type'];
		$params['encoding'] = 'base64';
		$params['disposition'] = 'inline';
		$params['dfilename'] = $value['name'];
		$params['cid'] = $value['cid'];
		$obj->add_subpart($value['body'], $params);
	} 

	function &_add_attachment_part(&$obj, $value)
	{
		$params['content_type'] = $value['c_type'];
		$params['encoding'] = $value['encoding'];
		$params['disposition'] = 'attachment';
		$params['dfilename'] = $value['name'];
		$obj->add_subpart($value['body'], $params);
	} 

	function build_message($params = array())
	{
		if (!empty($params))
		{
			while (list($key, $value) = each($params))
			{
				$this->build_params[$key] = $value;
			} 
		} 

		if (!empty($this->html_images))
		{
			foreach ($this->html_images as $value)
			{
				$this->html = str_replace($value['name'], 'cid:' . $value['cid'], $this->html);
			} 
		} 

		$null = null;
		$attachments = !empty($this->attachments) ? true : false;
		$html_images = !empty($this->html_images) ? true : false;
		$html = !empty($this->html) ? true : false;
		$text = isset($this->text) ? true : false;

		switch (true)
		{
			case $text AND !$attachments:
				$message = &$this->_add_text_part($null, $this->text);
				break;

			case !$text AND $attachments AND !$html:
				$message = &$this->_add_mixed_part();

				for ($i = 0; $i < count($this->attachments); $i++)
				{
					$this->_add_attachment_part($message, $this->attachments[$i]);
				} 
				break;

			case $text AND $attachments:
				$message = &$this->_add_mixed_part();
				$this->_add_text_part($message, $this->text);

				for ($i = 0; $i < count($this->attachments); $i++)
				{
					$this->_add_attachment_part($message, $this->attachments[$i]);
				} 
				break;

			case $html AND !$attachments AND !$html_images:
				if (!is_null($this->html_text))
				{
					$message = &$this->_add_alternative_part($null);
					$this->_add_text_part($message, $this->html_text);
					$this->_add_html_part($message);
				} 
				else
				{
					$message = &$this->_add_html_part($null);
				} 
				break;

			case $html AND !$attachments AND $html_images:
				if (!is_null($this->html_text))
				{
					$message = &$this->_add_alternative_part($null);
					$this->_add_text_part($message, $this->html_text);
					$related = &$this->_add_related_part($message);
				} 
				else
				{
					$message = &$this->_add_related_part($null);
					$related = &$message;
				} 
				$this->_add_html_part($related);
				for ($i = 0; $i < count($this->html_images); $i++)
				{
					$this->_add_html_image_part($related, $this->html_images[$i]);
				} 
				break;

			case $html AND $attachments AND !$html_images:
				$message = &$this->_add_mixed_part();
				if (!is_null($this->html_text))
				{
					$alt = &$this->_add_alternative_part($message);
					$this->_add_text_part($alt, $this->html_text);
					$this->_add_html_part($alt);
				} 
				else
				{
					$this->_add_html_part($message);
				} 
				for ($i = 0; $i < count($this->attachments); $i++)
				{
					$this->_add_attachment_part($message, $this->attachments[$i]);
				} 
				break;

			case $html AND $attachments AND $html_images:
				$message = &$this->_add_mixed_part();
				if (!is_null($this->html_text))
				{
					$alt = &$this->_add_alternative_part($message);
					$this->_add_text_part($alt, $this->html_text);
					$rel = &$this->_add_related_part($alt);
				} 
				else
				{
					$rel = &$this->_add_related_part($message);
				} 
				$this->_add_html_part($rel);
				for ($i = 0; $i < count($this->html_images); $i++)
				{
					$this->_add_html_image_part($rel, $this->html_images[$i]);
				} 
				for ($i = 0; $i < count($this->attachments); $i++)
				{
					$this->_add_attachment_part($message, $this->attachments[$i]);
				} 
				break;
		} 

		if (isset($message))
		{
			$output = $message->encode();
			$this->output = $output['body'];
			$this->headers = array_merge($this->headers, $output['headers']);
			srand((double)microtime() * 10000000);
			$message_id = sprintf('<%s.%s@%s>', base_convert(time(), 10, 36), base_convert(rand(), 10, 36), !empty($GLOBALS['HTTP_SERVER_VARS']['HTTP_HOST']) ? $GLOBALS['HTTP_SERVER_VARS']['HTTP_HOST'] : $GLOBALS['HTTP_SERVER_VARS']['SERVER_NAME']);
			$this->headers['Message-ID'] = $message_id;

			$this->is_built = true;
			return true;
		} 
		else
		{
			return false;
		} 
	} 

	function _encode_header($input, $charset = 'ISO-8859-1')
	{

//		preg_match_all('/(\w*[\x80-\xFF]+\w*)/', $input, $matches);
//		foreach ($matches[1] as $value)
//		{
//			$replacement = preg_replace('/([\x80-\xFF])/e', '"=" . strtoupper(dechex(ord("\1")))', $value);
//			$input = str_replace($value, '=?' . $charset . '?Q?' . $replacement . '?=', $input);
//		} 

		return $input;
	} 

	function send($recipients, $type = 'mail')
	{
		if (!defined('CRLF'))
			$this->set_crlf($type == 'mail' ? "\n" : "\r\n");

		if (!$this->is_built)
			$this->build_message();

		switch ($type)
		{
			case 'smtp':
				return $this->_smtp_send($recipients);
				break;

			case 'mail':
			default:
				return $this->_mail_send($recipients);
				break;
			
		} 
	} 

	function _mail_send(&$recipients)
	{
		$subject = '';
		if (!empty($this->headers['Subject']))
		{
			$subject = $this->_encode_header($this->headers['Subject'], $this->build_params['head_charset']);
			unset($this->headers['Subject']);
		} 

		foreach ($this->headers as $name => $value)
			$headers[] = $name . ': ' . $this->_encode_header($value, $this->build_params['head_charset']);

		$to = $this->_encode_header(implode(', ', $recipients), $this->build_params['head_charset']);

		if (!empty($this->return_path))
			$result = mail($to, $subject, $this->output, implode(CRLF, $headers), '-f' . $this->return_path);
		else
			$result = mail($to, $subject, $this->output, implode(CRLF, $headers));

		if ($subject !== '')
			$this->headers['Subject'] = $subject;

		return $result;
	}
	
	function _smtp_send(&$recipients)
	{
		require_once(LIMB_DIR . 'core/lib/mail/smtp.class.php');
		require_once(LIMB_DIR . 'core/lib/mail/mail_rfc822.class.php');
trigger_error("Stop",E_USER_WARNING);	
		$smtp =& smtp :: connect($this->smtp_params);

		foreach ($recipients as $recipient)
		{
			$addresses = mail_rfc822 :: parse_address_list($recipient, $this->smtp_params['helo'], null, false);
			foreach ($addresses as $address)
				$smtp_recipients[] = sprintf('%s@%s', $address->mailbox, $address->host);

		} 
		unset($addresses); // These are reused
		unset($address); // These are reused
		
		foreach ($this->headers as $name => $value)
		{
			if ($name == 'Cc' OR $name == 'Bcc')
			{
				$addresses = mail_rfc822 :: parse_address_list($value, $this->smtp_params['helo'], null, false);
				foreach ($addresses as $address)
					$smtp_recipients[] = sprintf('%s@%s', $address->mailbox, $address->host);
			} 
			if ($name == 'Bcc')
				continue;

			$headers[] = $name . ': ' . $this->_encode_header($value, $this->build_params['head_charset']);
		} 

		$headers[] = 'To: ' . $this->_encode_header(implode(', ', $recipients), $this->build_params['head_charset']);
		$send_params['headers'] = $headers;
		$send_params['recipients'] = array_values(array_unique($smtp_recipients));
		$send_params['body'] = $this->output;

		if (isset($this->return_path))
			$send_params['from'] = $this->return_path;
		elseif (!empty($this->headers['From']))
		{
			$from = mail_rfc822 :: parse_address_list($this->headers['From']);
			$send_params['from'] = sprintf('%s@%s', $from[0]->mailbox, $from[0]->host);
		} 
		else
			$send_params['from'] = 'postmaster@' . $this->smtp_params['helo'];
trigger_error("Stop",E_USER_WARNING);	
		if (!$smtp->send($send_params))
		{
			$this->errors = $smtp->errors;
			return false;
		} 
		return true;
	}

	function get_rfc822($recipients)
	{
		$this->set_header('Date', date('D, d M y H:i:s O'));

		if (!defined('CRLF'))
			$this->set_crlf($type == 'mail' ? "\n" : "\r\n");


		if (!$this->is_built)
			$this->build_message();

		if (isset($this->return_path))
			$headers[] = 'Return-Path: ' . $this->return_path;

		foreach ($this->headers as $name => $value)
			$headers[] = $name . ': ' . $value;

		$headers[] = 'To: ' . implode(', ', $recipients);

		return implode(CRLF, $headers) . CRLF . CRLF . $this->output;
	} 
} // End of class.

?>
