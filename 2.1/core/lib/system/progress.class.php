<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: progress.class.php 410 2004-02-06 10:46:51Z server $
*
***********************************************************************************/ 
define( 'PROGRESS_MAX_MESSAGE_LIFETIME', 60*60*24);

define( 'PROCESS_LEVEL_NOTICE', 1 );
define( 'PROCESS_LEVEL_WARNING', 2 );
define( 'PROCESS_LEVEL_ERROR', 3 );
define( 'PROCESS_LEVEL', 4 );
define( 'PROCESS_STARTED', -1 );
define( 'PROCESS_END', 0 );

require_once(LIMB_DIR . 'core/lib/system/sys.class.php');
require_once(LIMB_DIR . 'core/lib/db/db_factory.class.php');

class progress
{
	var $processes_stack = array();
	var $current_process = null;
	
	var $db = null;
	var $session_id = null;
	
  function progress( )
  {
  	$this->db =& db_factory :: instance();
  	$this->session_id = sys :: client_ip();
  }
  
  function &instance( )
  {
    $impl =& $GLOBALS['global_progress_instance'];

    $class =& get_class( $impl );
    if ( $class != 'progress' )
    	$impl = new progress();
    	
    return $impl;
  }
  
  function process_start($process_name)
  {
    $this =& progress::instance();    
    
    $this->current_process = $process_name;
   	array_push($this->processes_stack, $process_name);
    
    $this->write('started', PROCESS_STARTED);    
  }
  
  function process_end($process_name)
  {
    $this =& progress::instance();
    
    $this->current_process = array_pop($this->processes_stack);
    
    $this->write('finished', PROCESS_END);
  }
  
  function write_notice($string)
  {
    $this =& progress::instance();
    $this->write($string, PROCESS_LEVEL_NOTICE);
  }

  function write_warning($string)
  {
    $this =& progress::instance();
    $this->write($string, PROCESS_LEVEL_WARNING);
  }

  function write_error($string)
  {
    $this =& progress::instance();
    $this->write($string, PROCESS_LEVEL_ERROR);
  }

  function write($string='', $verbosity_status = PROCESS_LEVEL_NOTICE)
  {
  	if(!connection_aborted() && $this->current_process) 
  	{
	  	$values['session_id'] = $this->session_id;
	  	$values['status'] = $verbosity_status;
	  	$values['name'] = $this->current_process;
	  	$values['message'] = $string;
	  	$values['time'] = time();
	  	
	  	$this->db->sql_insert('sys_progress', $values);
	  }
  }
        
  function get_messages_since($message_id)
  {
  	$this =& progress::instance();

  	$message_id = $this->db->escape($message_id);
  	  	  	  	  	
		$this->db->sql_select('sys_progress', '*', "id>{$message_id} AND session_id='{$this->session_id}'", 'id');
		
		$messages = $this->db->get_array();
				
		if($messages)
			$this->db->sql_delete('sys_progress', "id>{$message_id} AND session_id='{$this->session_id}'");
										
		return $messages;
  }
  
  function cleanup()
  {
  	$this =& progress::instance();
  	$this->db->sql_delete('sys_progress', "session_id='{$this->session_id}' OR (" . time(). "-time) > " . PROGRESS_MAX_MESSAGE_LIFETIME);
  }
}

if(strstr(REQUEST_URI, 'progress=1'))
	progress :: cleanup();
	
?>