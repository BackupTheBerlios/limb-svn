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

/**
* POP3 Access Class
*  +----------------------------- IMPORTANT ------------------------------+
*  | Uses native php extension IMAP   																		|
*  +----------------------------------------------------------------------+
*/

require_once(LIMB_DIR . 'core/lib/mail/pop3.class.php');

class pop3_imap_ext extends pop3
{
	function pop3_imap_ext()
	{
		parent :: pop3();
	}
	
	
	function _connect()
	{
		if($this->_is_error)
			return false;

		if(!$this->_connection = @imap_open ( "{". $this->_host . "/pop3:110}", $this->_user, $this->_password, CL_EXPUNGE) )
		{
			$this->set_error("can't connect");
			return false;
		}

		return true;
	}

	function _disconnect()
	{
		@imap_expunge($this->_connection);
		return @imap_close($this->_connection);
	}

	function get_messages_count()
	{
		return imap_num_msg($this->_connection);
	}

	function get_headers($msg)
	{
		$header = imap_headerinfo($this->_connection, $msg);
		
		$result['from'] = $header->fromaddress; 
		$result['to'] = $header->toaddress; 
		$result['recipient'] = $header->toaddress; 
		$result['sender'] = $header->senderaddress; 
		$result['reply_to'] = $header->reply_toaddress; 
		$result['size'] = $header->Size; 
		$result['subject'] = $header->subject; 
		$result['date_sent_str'] = $header->MailDate; 
		$result['udate_sent'] = $header->udate; 
		$result['date_sent'] = date('Y-m-d H:i:s',$header->udate);
		return $result;
	}

	function get_message($msg)
	{
		$header = imap_fetchheader($this->_connection, $msg);
		$message = imap_body($this->_connection, $msg);
		return $header.$message;
	}


	function delete($msg)
	{
		return @imap_delete($this->_connection, $msg);
	}


	function _parse_message_into_parts($message_parts)
	{
		$message_type = array("text", "multipart", "message", "application", "audio", "image", "video", "other");
		$message_encoding = array("7bit", "8bit", "binary", "base64", "quoted-printable", "other");
		
		for($i=0; $i<sizeof($message_parts); $i++)
		{
			$obj = $message_parts[$i];

			if (empty($obj->type)) $obj->type = 0;
			if (empty($obj->encoding))	$obj->encoding = 0;

			$result[$i]["part_type"] = $type[$obj->type] . "/" . strtolower($obj->subtype);	
			$result[$i]["encoding"] = $encoding[$obj>encoding];	
			$result[$i]["size"] = strtolower($obj->bytes);	
			$result[$i]["disposition"] = strtolower($obj->disposition);	
			
			if (!strtolower($obj->disposition) == "attachment")
				continue;

			foreach ($obj->dparameters as $param)
			{
				if($param->attribute == "FILENAME")
				{
					$result[$i]["file_name"] = $param->value;	
					break;			
				}
			}

		}
	
		return $result;
	}
	
	function _get_attachments_from_message_parts($message_parts)
	{
		for($i=0; $i<sizeof($message_parts); $i++)
			if($message_parts[$i]["disposition"] == "attachment")
				$result[] = $message_parts[$i];

		return $result;
	}

}
?>