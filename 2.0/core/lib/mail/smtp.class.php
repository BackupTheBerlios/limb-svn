<?php

class smtp_class
{
	var $host_name = "";
	var $host_port = 25;
	var $localhost = "";
	var $timeout = 0;
	var $error = "";
	var $debug = 0;
	var $esmtp = 1;
	var $esmtp_host = "";
	var $esmtp_extensions = array();
	var $maximum_piped_recipients = 100;

	var $state = "Disconnected";
	var $connection = 0;
	var $pending_recipients = 0;

	function output_debug($message)
	{
		echo $message, "\n";
	} 

	function get_line()
	{
		for($line = "";;)
		{
			if (feof($this->connection))
			{
				$this->error = "reached the end of stream while reading from socket";
				return(0);
			} 
			if (($data = fgets($this->connection, 100)) == false)
			{
				$this->error = "it was not possible to read line from socket";
				return(0);
			} 
			$line .= $data;
			$length = strlen($line);
			if ($length >= 2 && substr($line, $length-2, 2) == "\r\n")
			{
				$line = substr($line, 0, $length-2);
				if ($this->debug)
					$this->output_debug("< $line");
				return($line);
			} 
		} 
	} 

	function put_line($line)
	{
		if ($this->debug)
			$this->output_debug("> $line");
		if (!fputs($this->connection, "$line\r\n"))
		{
			$this->error = "it was not possible to write line to socket";
			return(0);
		} 
		return(1);
	} 

	function put_data($data)
	{
		if (strlen($data))
		{
			if ($this->debug)
				$this->output_debug("> $data");
			if (!fputs($this->connection, $data))
			{
				$this->error = "it was not possible to write data to socket";
				return(0);
			} 
		} 
		return(1);
	} 

	function verify_result_lines($code, $responses = "")
	{
		if (gettype($responses) != "array")
			$responses = array();
		unset($match_code);
		while (($line = $this->get_line($this->connection)))
		{
			if (isset($match_code))
			{
				if (strcmp(strtok($line, " -"), $match_code))
				{
					$this->error = $line;
					return(0);
				} 
			} 
			else
			{
				$match_code = strtok($line, " -");
				if (gettype($code) == "array")
				{
					for($codes = 0;$codes < count($code) && strcmp($match_code, $code[$codes]);$codes++);
					if ($codes >= count($code))
					{
						$this->error = $line;
						return(0);
					} 
				} 
				else
				{
					if (strcmp($match_code, $code))
					{
						$this->error = $line;
						return(0);
					} 
				} 
			} 
			$responses[] = strtok("");
			if (!strcmp($match_code, strtok($line, " ")))
				return(1);
		} 
		return(-1);
	} 

	function flush_recipients()
	{
		if ($this->pending_sender)
		{
			if ($this->verify_result_lines("250") <= 0)
				return(0);
			$this->pending_sender = 0;
		} 
		for(;$this->pending_recipients;$this->pending_recipients--)
		{
			if ($this->verify_result_lines(array("250", "251")) <= 0)
				return(0);
		} 
		return(1);
	} 

	function connect()
	{
		$this->error = $error = "";
		$this->esmtp_host = "";
		$this->esmtp_extensions = array();
		if (!($this->connection = ($this->timeout ? fsockopen($this->host_name, $this->host_port, &$errno, &$error, $this->timeout) : fsockopen($this->host_name, $this->host_port))))
		{
			switch ($error)
			{
				case -3:
					$this->error = "-3 socket could not be created";
					return(0);
				case -4:
					$this->error = "-4 dns lookup on hostname \"" . $host_name . "\" failed";
					return(0);
				case -5:
					$this->error = "-5 connection refused or timed out";
					return(0);
				case -6:
					$this->error = "-6 fdopen() call failed";
					return(0);
				case -7:
					$this->error = "-7 setvbuf() call failed";
					return(0);
				default:
					$this->error = $error . " could not connect to the host \"" . $this->host_name . "\"";
					return(0);
			} 
		} 
		else
		{
			if (!strcmp($localhost = $this->localhost, "") && !strcmp($localhost = getenv("SERVER_NAME"), "") && !strcmp($localhost = getenv("HOST"), ""))
				$localhost = "localhost";
			$success = 0;
			if ($this->verify_result_lines("220") > 0)
			{
				if ($this->esmtp)
				{
					$responses = array();
					if ($this->put_line("EHLO $localhost") && $this->verify_result_lines("250", &$responses) > 0)
					{
						$this->esmtp_host = strtok($responses[0], " ");
						for($response = 1;$response < count($responses);$response++)
						{
							$extension = strtoupper(strtok($responses[$response], " "));
							$this->esmtp_extensions[$extension] = strtok("");
						} 
						$success = 1;
					} 
				} 
				if (!$success && $this->put_line("HELO $localhost") && $this->verify_result_lines("250") > 0)
					$success = 1;
			} 
			if ($success)
			{
				$this->state = "Connected";
				return(1);
			} 
			else
			{
				fclose($this->connection);
				$this->connection = 0;
				$this->state = "Disconnected";
				return(0);
			} 
		} 
	} 

	function mail_from($sender)
	{
		if (strcmp($this->state, "Connected"))
		{
			$this->error = "connection is not in the initial state";
			return(0);
		} 
		$this->error = "";
		if (!$this->put_line("MAIL FROM:<$sender>"))
			return(0);
		if (!IsSet($this->esmtp_extensions["PIPELINING"]) && $this->verify_result_lines("250") <= 0)
			return(0);
		$this->state = "SenderSet";
		if (IsSet($this->esmtp_extensions["PIPELINING"]))
			$this->pending_sender = 1;
		$this->pending_recipients = 0;
		return(1);
	} 

	function set_recipient($recipient)
	{
		switch ($this->state)
		{
			case "SenderSet":
			case "RecipientSet":
				break;
			default:
				$this->error = "connection is not in the recipient setting state";
				return(0);
		} 
		$this->error = "";
		if (!$this->put_line("RCPT TO:<$recipient>"))
			return(0);
		if (IsSet($this->esmtp_extensions["PIPELINING"]))
		{
			$this->pending_recipients++;
			if ($this->pending_recipients >= $this->maximum_piped_recipients)
			{
				if (!$this->flush_recipients())
					return(0);
			} 
		} 
		else
		{
			if ($this->verify_result_lines(array("250", "251")) <= 0)
				return(0);
		} 
		$this->state = "RecipientSet";
		return(1);
	} 

	function start_data()
	{
		if (strcmp($this->state, "RecipientSet"))
		{
			$this->error = "connection is not in the start sending data state";
			return(0);
		} 
		$this->error = "";
		if (!$this->put_line("DATA"))
			return(0);
		if ($this->pending_recipients)
		{
			if (!$this->flush_recipients())
				return(0);
		} 
		if ($this->verify_result_lines("354") <= 0)
			return(0);
		$this->state = "SendingData";
		return(1);
	} 

	function prepare_data($data, &$output)
	{
		$length = strlen(&$data);
		for($output = "", $position = 0;$position < $length;)
		{
			$next_position = $length;
			for($current = $position;$current < $length;$current++)
			{
				switch ($data[$current])
				{
					case "\n":
						$next_position = $current + 1;
						break 2;
					case "\r":
						$next_position = $current + 1;
						if ($data[$next_position] == "\n")
							$next_position++;
						break 2;
				} 
			} 
			if ($data[$position] == ".")
				$output .= ".";
			$output .= substr(&$data, $position, $current - $position) . "\r\n";
			$position = $next_position;
		} 
	} 

	function send_data($data)
	{
		if (strcmp($this->state, "SendingData"))
		{
			$this->error = "connection is not in the sending data state";
			return(0);
		} 
		$this->error = "";
		return($this->put_data(&$data));
	} 

	function end_sending_data()
	{
		if (strcmp($this->state, "SendingData"))
		{
			$this->error = "connection is not in the sending data state";
			return(0);
		} 
		$this->error = "";
		if (!$this->put_line("\r\n.") || $this->verify_result_lines("250") <= 0)
			return(0);
		$this->state = "Connected";
		return(1);
	} 

	function reset_connection()
	{
		switch ($this->state)
		{
			case "Connected":
				return(1);
			case "SendingData":
				$this->error = "can not reset the connection while sending data";
				return(0);
			case "Disconnected":
				$this->error = "can not reset the connection before it is established";
				return(0);
		} 
		$this->error = "";
		if (!$this->put_line("RSET") || $this->verify_result_lines("250") <= 0)
			return(0);
		$this->state = "Connected";
		return(1);
	} 

	function disconnect($quit = 1)
	{
		if (!strcmp($this->state, "Disconnected"))
		{
			$this->error = "it was not previously established a SMTP connection";
			return(0);
		} 
		$this->error = "";
		if (!strcmp($this->state, "Connected") && $quit && (!$this->put_line("QUIT") || $this->verify_result_lines("221") <= 0))
			return(0);
		fclose($this->connection);
		$this->connection = 0;
		$this->state = "Disconnected";
		return(1);
	} 

	function send_message($sender, $recipients, $headers, $body)
	{
		if (!is_array($recipients) || !($success = $this->connect()))
			return($success);

		if (($success = $this->mail_from($sender)))
		{

			foreach($recipients as $recipient)
				if (!($success = $this->set_recipient($recipient)))
					break;

			if ($success && ($success = $this->start_data()))
			{
				for($header_data = "", $header = 0;$header < count($headers);$header++)
					$header_data .= $headers[$header] . "\r\n";
	
				if (($success = $this->send_data($header_data . "\r\n")))
				{
					$this->prepare_data($body, &$body_data);
					$success = $this->send_data($body_data);
				} 
	
				if ($success)
					$success = $this->end_sending_data();
			} 
		} 

		$error = $this->error;
		$disconnect_success = $this->disconnect($success);

		if ($success)
			$success = $disconnect_success;
		else
			$this->error = $error;

		return($success);
	} 
} ;

?>

