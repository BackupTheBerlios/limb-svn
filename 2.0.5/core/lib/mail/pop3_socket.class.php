<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: pop3_socket.class.php 410 2004-02-06 10:46:51Z server $
*
***********************************************************************************/ 
/**
* POP3 Access Class
*  +----------------------------- IMPORTANT ------------------------------+
*  | Uses SOCKETS class							   																		|
*  +----------------------------------------------------------------------+
*/

require_once(LIMB_DIR . 'core/lib/mail/pop3.class.php');
require_once(LIMB_DIR . 'core/lib/util/socket.class.php');


class pop3_socket extends pop3
{
	
	function pop3_socket()
	{
		parent :: pop3();
	}
	
	
	function _connect()
	{
		if(!$this->_is_error)
			if(!$this->_connection = @imap_open ( "{". $this->_host . "/pop3:110}", $this->_user, $this->_password, CL_EXPUNGE) )
				$this->set_error("can't connect");
	}


	function _disconnect()
	{
		return imap_close($this->_connection);
	}


	function get_messages_count()
	{
		return imap_num_msg($this->_connection);
	}


	function get_headers($msg)
	{
		return imap_headerinfo($this->_connection, $msg);
	}


	function get_message($msg)
	{
		$header = imap_fetchheader($this->_connection, $msg);
		echo "<h1>header {$msg}:</h1><br>".$header;
		$message = imap_body($this->_connection, $msg);
		echo "<h1>message {$msg}:</h1><br>".$message;
		return $header.$message;
	}


	function delete($msg)
	{
		return @imap_delete($this->_connection, $msg);
	}

}
?>